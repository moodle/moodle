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
 * @package dataformfield
 * @subpackage duration
 * @copyright 2013 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') or die;

/**
 *
 */
class dataformfield_duration_renderer extends mod_dataform\pluginbase\dataformfieldrenderer {

    /**
     *
     */
    protected function replacements(array $patterns, $entry, array $options = null) {
        $field = $this->_field;
        $fieldname = $field->name;
        $edit = !empty($options['edit']);

        $replacements = array();

        if ($edit) {
            $firstinput = false;
            foreach ($patterns as $pattern => $cleanpattern) {
                $noedit = $this->is_noedit($pattern);
                if (!$firstinput and !$noedit and $cleanpattern != "[[$fieldname:unit]]") {
                    $required = $this->is_required($pattern);
                    $replacements[$pattern] = array(array($this, 'display_edit'), array($entry, array('required' => $required)));
                    $firstinput = true;
                } else {
                    $replacements[$pattern] = '';
                }
            }
            return $replacements;
        }

        // Browse mode.
        foreach ($patterns as $pattern => $cleanpattern) {
            switch ($cleanpattern) {
                case "[[$fieldname]]":
                    $replacements[$pattern] = $this->display_browse($entry);
                    break;
                case "[[$fieldname:unit]]":
                    $replacements[$pattern] = $this->display_browse($entry, array('format' => 'unit'));
                    break;
                case "[[$fieldname:value]]":
                    $replacements[$pattern] = $this->display_browse($entry, array('format' => 'value'));
                    break;
                case "[[$fieldname:seconds]]":
                    $replacements[$pattern] = $this->display_browse($entry, array('format' => 'seconds'));
                    break;
                case "[[$fieldname:interval]]":
                    $replacements[$pattern] = $this->display_browse($entry, array('format' => 'interval'));
                    break;
                default:
                    $replacements[$pattern] = '';
            }
        }

        return $replacements;
    }

    /**
     *
     */
    public function display_edit(&$mform, $entry, array $options = null) {
        global $PAGE;

        $field = $this->_field;
        $fieldid = $field->id;
        $entryid = $entry->id;
        $fieldname = "field_{$fieldid}_{$entryid}";

        $number = '';
        if ($entryid > 0 and isset($entry->{"c{$fieldid}_content"})) {
            $number = $entry->{"c{$fieldid}_content"};
        }

        // Field width.
        $fieldattr = array();
        if ($field->param2) {
            $fieldattr['style'] = 'width:'. s($field->param2). s($field->param3). ';';
        }

        // Optional and units.
        $elemattr = array();
        $elemattr['optional'] = null;
        if ($field->param4) {
            $elemattr['units'] = explode(',', $field->param4);
        }
        $elem = &$mform->addElement('duration', $fieldname, null, $elemattr, $fieldattr);
        $mform->setDefault($fieldname, $number);
        $required = !empty($options['required']);
        if ($required) {
            $mform->addRule($fieldname, null, 'required', null, 'client');
            // JS Error message.
            $options = array(
                'fieldname' => $fieldname,
                'message' => get_string('err_required', 'form'),
            );

            $module = array(
                'name' => 'M.dataformfield_duration_required',
                'fullpath' => '/mod/dataform/field/duration/duration.js',
                'requires' => array('base', 'node')
            );

            $PAGE->requires->js_init_call('M.dataformfield_duration_required.init', array($options), false, $module);
        }
    }

    /**
     *
     */
    public function display_browse($entry, $params = null) {
        $field = $this->_field;
        $fieldid = $field->id;
        if (isset($entry->{"c{$fieldid}_content"})) {
            $duration = (int) $entry->{"c{$fieldid}_content"};
        } else {
            $duration = '';
        }

        $format = !empty($params['format']) ? $params['format'] : '';
        if ($duration) {
            list($value, $unit) = $field->seconds_to_unit($duration);
            $units = $field->get_units();
            switch ($format) {
                case 'unit':
                    return $units[$unit];
                    break;

                case 'value':
                    return $value;
                    break;

                case 'seconds':
                    return $duration;
                    break;

                case 'interval':
                    return format_time($duration);
                    break;

                default:
                    return $value. ' '. $units[$unit];
                    break;
            }
        }
        return '';
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

        // Only [[$fieldname]] is editable so check if exists.
        if (array_key_exists("[[*$fieldname]]", $patterns) and isset($data->$formfieldname)) {
            if (!$content = clean_param($data->$formfieldname, PARAM_INT)) {
                return array($fieldname, get_string('fieldrequired', 'dataform'));
            }
        }
        return null;
    }

    /**
     * Array of patterns this field supports
     */
    protected function patterns() {
        $fieldname = $this->_field->name;

        $patterns = parent::patterns();
        $patterns["[[$fieldname]]"] = array(true, $fieldname);
        $patterns["[[$fieldname:unit]]"] = array(false, $fieldname);
        $patterns["[[$fieldname:value]]"] = array(false, $fieldname);
        $patterns["[[$fieldname:seconds]]"] = array(false, $fieldname);
        $patterns["[[$fieldname:interval]]"] = array(false, $fieldname);

        return $patterns;
    }
}
