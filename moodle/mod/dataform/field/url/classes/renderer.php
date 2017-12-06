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
 * @subpackage url
 * @copyright 2012 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') or die;

/**
 *
 */
class dataformfield_url_renderer extends mod_dataform\pluginbase\dataformfieldrenderer {

    /**
     *
     */
    protected function replacements(array $patterns, $entry, array $options = null) {
        $field = $this->_field;
        $fieldname = $field->name;
        $edit = !empty($options['edit']);

        $replacements = array_fill_keys(array_keys($patterns), '');

        if ($edit) {
            foreach ($patterns as $pattern => $cleanpattern) {
                if ($noedit = $this->is_noedit($pattern)) {
                    continue;
                }
                $params = array('required' => $this->is_required($pattern));
                $replacements[$pattern] = array(array($this, 'display_edit'), array($entry, $params));
            }
            return $replacements;
        }

        // Browse mode.
        foreach ($patterns as $pattern => $cleanpattern) {
            $parts = explode(':', trim($cleanpattern, '[]'));
            if (!empty($parts[1])) {
                $type = $parts[1];
            } else {
                $type = '';
            }
            $replacements[$pattern] = $this->display_browse($entry, $type);
        }

        return $replacements;
    }

    /**
     *
     */
    public function display_edit(&$mform, $entry, array $options = null) {
        global $CFG, $PAGE;

        $field = $this->_field;
        $fieldid = $field->id;
        $entryid = $entry->id;
        $fieldname = "field_{$fieldid}_{$entryid}";

        $contentid = isset($entry->{"c{$fieldid}_id"}) ? $entry->{"c{$fieldid}_id"} : null;
        $url = isset($entry->{"c{$fieldid}_content"}) ? $entry->{"c{$fieldid}_content"} : null;
        $alt = isset($entry->{"c{$fieldid}_content1"}) ? $entry->{"c{$fieldid}_content1"} : null;

        $url = empty($url) ? 'http://' : $url;
        $usepicker = !$field->param1 ? false : true;
        $foptions = array(
            'title' => s($field->description),
            'size' => 64
        );
        $mform->addElement('url', $fieldname, null, $foptions, array('usefilepicker' => $usepicker));
        $mform->setType($fieldname, PARAM_URL);
        $mform->setDefault($fieldname, s($url));
        $required = !empty($options['required']);
        if ($required) {
            $mform->addRule($fieldname, null, 'required', null, 'client');
        }

        // Add alt name if not forcing name.
        if (!$field->param2) {
            $mform->addElement('text', "{$fieldname}_alt", get_string('alttext', 'dataformfield_url'));
            $mform->setType("{$fieldname}_alt", PARAM_TEXT);
            $mform->setDefault("{$fieldname}_alt", s($alt));
        }
    }

    /**
     *
     */
    public function display_browse($entry, $type = '') {
        global $CFG;

        $field = $this->_field;
        $fieldid = $field->id;

        if (isset($entry->{"c{$fieldid}_content"})) {
            $url = $entry->{"c{$fieldid}_content"};
            if (empty($url) or ($url == 'http://')) {
                return '';
            }

            // Simple url text.
            if (empty($type)) {
                return $url;
            }

            // Param2 forces the text to something.
            if ($field->param2) {
                $alttext = s($field->param2);
            } else {
                $alttext = empty($entry->{"c{$fieldid}_content1"}) ? $url : $entry->{"c{$fieldid}_content1"};
            }

            // Linking.
            if ($type == 'link') {
                return html_writer::link($url, $alttext);
            }

            // Image.
            if ($type == 'image') {
                return html_writer::empty_tag('img', array('src' => $url));
            }

            // Image flexible.
            if ($type == 'imageflex') {
                return html_writer::empty_tag('img', array('src' => $url, 'style' => 'width:100%'));
            }

            // Media.
            if ($type == 'media') {
                require_once("$CFG->dirroot/filter/mediaplugin/filter.php");
                $mpfilter = new filter_mediaplugin($field->get_df()->context, array());
                return $mpfilter->filter(html_writer::link($url, ''));
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

        // Only [[$fieldname]] is editable so check it if exists.
        if (array_key_exists("[[*$fieldname]]", $patterns) and isset($data->$formfieldname)) {
            // Remove http:.
            $content = str_replace('http://', '', $data->$formfieldname);
            if (!$content = clean_param($content, PARAM_URL)) {
                return array($formfieldname, get_string('fieldrequired', 'dataform'));
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
        $patterns["[[$fieldname:link]]"] = array(true, $fieldname);
        $patterns["[[$fieldname:image]]"] = array(false);
        $patterns["[[$fieldname:imageflex]]"] = array(false);
        $patterns["[[$fieldname:media]]"] = array(false);

        return $patterns;
    }
}
