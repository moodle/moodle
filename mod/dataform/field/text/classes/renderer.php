<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package dataformfield_text
 * @copyright 2014 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') or die;

/**
 *
 */
class dataformfield_text_renderer extends \mod_dataform\pluginbase\dataformfieldrenderer {

    /**
     *
     */
    protected function replacements(array $patterns, $entry, array $options = null) {
        $field = $this->_field;
        $fieldname = $field->name;
        $edit = !empty($options['edit']);

        $replacements = array();

        // Edit mode: display edit the first editable pattern and display browse the rest.
        if ($edit) {
            $firstinput = false;
            foreach ($patterns as $pattern => $cleanpattern) {
                if (!$firstinput and !$noedit = $this->is_noedit($pattern)) {
                    $required = $this->is_required($pattern);
                    $replacements[$pattern] = array(array($this, 'display_edit'), array($entry, array('required' => $required)));
                    $firstinput = true;
                } else {
                    $replacements[$pattern] = $this->display_browse($entry, array('pattern' => $cleanpattern));
                }
            }
            return $replacements;
        }

        // Browse mode.
        foreach ($patterns as $pattern => $cleanpattern) {
             $replacements[$pattern] = $this->display_browse($entry, array('pattern' => $cleanpattern));
        }
        return $replacements;
    }

    /**
     *
     */
    public function validate_data($entryid, $patterns, $data) {
        $field = $this->_field;
        $fieldid = $field->id;
        $fieldname = $field->name;

        $formfieldname = "field_{$fieldid}_{$entryid}";
        $patterns = $this->add_clean_pattern_keys($patterns);

        // Only [[$fieldname]] is editable so check it if exists.
        if (array_key_exists("[[*$fieldname]]", $patterns) and isset($data->$formfieldname)) {
            if (!$content = clean_param($data->$formfieldname, PARAM_NOTAGS)) {
                return array($formfieldname, get_string('fieldrequired', 'dataform'));
            }
        }
        return null;
    }

    /**
     *
     */
    public function display_edit(&$mform, $entry, array $options = null) {
        $field = $this->_field;
        $fieldid = $field->id;
        $entryid = $entry->id;

        $content = '';
        if ($entryid > 0 and isset($entry->{"c{$fieldid}_content"})) {
            $content = $entry->{"c{$fieldid}_content"};
        } else {
            // Default content.
            $content = $field->defaultcontent;
        }

        $fieldattr = array();

        // Width.
        if ($field->param2) {
            $fieldattr['style'] = 'width:'. s($field->param2). s($field->param3). ';';
        }

        // Class.
        $classes = $field->name_normalized;
        if ($field->param4) {
            $classes .= ' '. s($field->param4);
        }
        $fieldattr['class'] = $classes;

        $fieldname = "field_{$fieldid}_{$entryid}";
        $mform->addElement('text', $fieldname, null, $fieldattr);
        $mform->setType($fieldname, PARAM_TEXT);
        $mform->setDefault($fieldname, $content);
        $required = !empty($options['required']);
        if ($required) {
            $mform->addRule($fieldname, null, 'required', null, 'client');
        }
        // Format rule.
        if ($format = $field->param4) {
            $mform->addRule($fieldname, null, $format, null, 'client');
            // Adjust type.
            switch($format) {
                case 'alphanumeric':
                    $mform->setType($fieldname, PARAM_ALPHANUM);
                    break;
                case 'lettersonly':
                    $mform->setType($fieldname, PARAM_ALPHA);
                    break;
                case 'numeric':
                    $mform->setType($fieldname, PARAM_TEXT);
                    break;
                case 'email':
                    $mform->setType($fieldname, PARAM_EMAIL);
                    break;
            }
        }
        // Length rule.
        if ($length = $field->param5) {
            ($min = $field->param6) or ($min = 0);
            ($max = $field->param7) or ($max = 64);

            switch ($length) {
                case 'minlength':
                    $val = $min;
                    break;
                case 'maxlength':
                    $val = $max;
                    break;
                case 'rangelength':
                    $val = array($min, $max);
                    break;
            }
            $mform->addRule($fieldname, null, $length, $val, 'client');
        }
    }

    /**
     *
     */
    public function display_browse($entry, $params = null) {
        $field = $this->_field;
        $fieldid = $field->id;

        if (isset($entry->{"c{$fieldid}_content"})) {
            $content = $entry->{"c{$fieldid}_content"};

            $options = new stdClass;
            $options->para = false;

            $format = FORMAT_PLAIN;
            $str = format_text($content, $format, $options);
        } else {
            $str = '';
        }

        return $str;
    }

    /**
     * Overriding {@link dataformfieldrenderer::get_pattern_import_settings()}
     * to allow only the base pattern.
     */
    public function get_pattern_import_settings(&$mform, $patternname, $header) {
        // Only [[fieldname]] can be imported.
        if ($patternname != $this->_field->name) {
            return array(array(), array());
        }

        return parent::get_pattern_import_settings($mform, $patternname, $header);
    }

    /**
     * Array of patterns this field supports
     */
    protected function patterns() {
        $fieldname = $this->_field->name;

        $patterns = parent::patterns();
        $patterns["[[$fieldname]]"] = array(true, $fieldname);

        return $patterns;
    }
}
