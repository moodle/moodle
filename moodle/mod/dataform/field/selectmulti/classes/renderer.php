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
 * @package dataformfield_selectmulti
 * @copyright 2014 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') or die();

/**
 *
 */
class dataformfield_selectmulti_renderer extends mod_dataform\pluginbase\dataformfieldrenderer {

    /**
     *
     */
    protected function replacements(array $patterns, $entry, array $options = null) {
        $field = $this->_field;
        $fieldname = $field->name;
        $edit = !empty($options['edit']);

        $replacements = array_fill_keys(array_keys($patterns), '');

        $editonce = false;
        foreach ($patterns as $pattern => $cleanpattern) {
            $noedit = $this->is_noedit($pattern);
            if (!$editonce and $edit and !$noedit) {
                $params = array('required' => $this->is_required($pattern));
                if ($cleanpattern == "[[$fieldname:addnew]]") {
                    $params['addnew'] = true;
                }
                $replacements[$pattern] = array(array($this , 'display_edit'), array($entry, $params));
                $editonce = true;
            } else {
                if ($cleanpattern == "[[$fieldname:options]]") {
                    $replacements[$pattern] = $this->display_options($entry);
                } else {
                    $replacements[$pattern] = $this->display_browse($entry);
                }
            }
        }

        return $replacements;
    }

    /**
     *
     */
    public function display_edit(&$mform, $entry, array $options = null) {
        $field = $this->_field;
        $fieldid = $field->id;
        $entryid = $entry->id;
        $fieldname = "field_{$fieldid}_$entryid";
        $menuoptions = $field->options_menu();
        $required = !empty($options['required']);

        $content = !empty($entry->{"c{$fieldid}_content"}) ? $entry->{"c{$fieldid}_content"} : null;

        if ($entryid > 0 and $content) {
            $selected = explode('#', trim($content, '#'));
        } else {
            $selected = array();
        }

        // Check for default values.
        if (!$selected and $defaultcontent = $field->default_content) {
            $selected = $defaultcontent;
        }

        // Add selector only if there are menu options.
        if ($menuoptions) {
            list($elem, $separators) = $this->render($mform, "{$fieldname}_selected", $menuoptions, $selected, $required);
            // Add group or element.
            if (is_array($elem)) {
                $mform->addGroup($elem, $fieldname, null, $separators, false);
            } else {
                $mform->addElement($elem);
            }

            // Required rule.
            if ($required) {
                $this->set_required($mform, $fieldname, $selected);
            }
        }

        // Input field for adding a new option.
        if (!empty($options['addnew'])) {
            if ($field->param4 or has_capability('mod/dataform:managetemplates', $field->get_df()->context)) {
                $mform->addElement('text', "{$fieldname}_newvalue", get_string('newvalue', 'dataform'));
                $mform->setType("{$fieldname}_newvalue", PARAM_TEXT);
                $mform->disabledIf("{$fieldname}_newvalue", "{$fieldname}_selected", 'neq', 0);
            }
            return;
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

            $options = $field->options_menu();
            $optionscount = count($options);
            $showalloptions = !empty($params['options']);

            $contents = explode('#', trim($content, '#'));

            $str = array();
            foreach ($options as $key => $option) {
                $selected = (int) in_array($key, $contents);
                if ($showalloptions) {
                    $str[] = "$selected $option";
                } else if ($selected) {
                    $str[] = $option;
                }
            }
            $str = implode($field->separator, $str);
        } else {
            $str = '';
        }

        return $str;
    }

    /**
     *
     */
    public function display_options($entry) {
        $field = $this->_field;
        $fieldid = $field->id;

        if (!isset($entry->{"c{$fieldid}_content"})) {
            return '';
        }

        $content = $entry->{"c{$fieldid}_content"};
        $contents = explode('#', trim($content, '#'));

        $str = array();
        $options = $field->options_menu();
        foreach ($options as $key => $option) {
            $selected = (int) in_array($key, $contents);
            $str[] = "$selected $option";
        }

        return implode($field->separator, $str);
    }

    /**
     * Overriding {@link dataformfieldrenderer::get_pattern_import_settings()}
     * to add a setting for 'allow add option'.
     */
    public function get_pattern_import_settings(&$mform, $patternname, $header) {
        $field = $this->_field;
        $fieldid = $field->id;
        $fieldname = $field->name;

        // Only [[fieldname]] can be imported.
        if ($patternname != $fieldname) {
            return array(array(), array());
        }

        $name = "f_{$fieldid}_";

        list($grp, $labels) = parent::get_pattern_import_settings($mform, $patternname, $header);

        $grp[] = &$mform->createElement('selectyesno', "{$name}_allownew");
        $labels = array_merge($labels, array(' '. get_string('allowaddoption', 'dataformfield_selectmulti'). ': '));

        return array($grp, $labels);
    }

    /**
     *
     */
    protected function render(&$mform, $fieldname, $options, $selected, $required = false) {
        $select = &$mform->createElement('select', $fieldname, null, $options);
        $select->setMultiple(true);
        $select->setSelected($selected);
        return array($select, null);
    }

    /**
     *
     */
    protected function set_required(&$mform, $fieldname, $selected) {
        $mform->addRule("{$fieldname}_selected", null, 'required', null, 'client');
    }

    /**
     * Array of patterns this field supports.
     */
    protected function patterns() {
        $fieldname = $this->_field->name;

        $patterns = parent::patterns();
        $patterns["[[$fieldname]]"] = array(true, $fieldname);
        $patterns["[[$fieldname:addnew]]"] = array(true, $fieldname);
        $patterns["[[$fieldname:options]]"] = array(false);

        return $patterns;
    }
}
