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
 * @subpackage select
 * @copyright 2011 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') or die;

/**
 *
 */
class dataformfield_select_renderer extends mod_dataform\pluginbase\dataformfieldrenderer {

    protected $_cats = array();

    /**
     *
     */
    protected function replacements(array $patterns, $entry, array $options = null) {
        $field = $this->_field;
        $fieldname = $field->name;
        $edit = !empty($options['edit']);

        $replacements = array_fill_keys(array_keys($patterns), '');

        $editdisplayed = false;
        foreach ($patterns as $pattern => $cleanpattern) {
            // Edit.
            if ($edit and !$editdisplayed and !$this->is_noedit($pattern)) {
                $params = array('required' => $this->is_required($pattern));
                if ($cleanpattern == "[[$fieldname:addnew]]") {
                    $params['addnew'] = true;
                }
                $replacements[$pattern] = array(array($this, 'display_edit'), array($entry, $params));
                $editdisplayed = true;
                continue;
            }

            // Browse.
            if ($cleanpattern == "[[$fieldname:options]]") {
                $replacements[$pattern] = $this->display_browse($entry, array('options' => true));
            } else if ($cleanpattern == "[[$fieldname:key]]") {
                $replacements[$pattern] = $this->display_browse($entry, array('key' => true));
            } else if ($cleanpattern == "[[$fieldname:cat]]") {
                $replacements[$pattern] = $this->display_category($entry);
            } else {
                $replacements[$pattern] = $this->display_browse($entry);
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
        $menuoptions = $field->options_menu();
        $fieldname = "field_{$fieldid}_$entryid";
        $required = !empty($options['required']);
        $selected = !empty($entry->{"c{$fieldid}_content"}) ? (int) $entry->{"c{$fieldid}_content"} : 0;

        // Check for default value.
        if (!$selected) {
            $defaultcontent = $field->defaultcontent;
            if (!empty($defaultcontent)) {
                $selected = $defaultcontent;
            }
        }

        // Add element only if there are options.
        if ($menuoptions) {
            list($elem, $separators) = $this->render($mform, "{$fieldname}_selected", $menuoptions, $selected, $required);
            // Add group or element.
            if (is_array($elem)) {
                $mform->addGroup($elem, $fieldname, null, $separators, false);
            } else {
                $mform->addElement($elem);
            }

            // Required.
            if ($required) {
                $this->set_required($mform, $fieldname, $selected);
            }
        }

        // Input field for adding a new option.
        if (!empty($options['addnew'])) {
            if ($field->param4 or has_capability('mod/dataform:managetemplates', $field->get_df()->context)) {
                $mform->addElement('text', "{$fieldname}_newvalue", get_string('newvalue', 'dataform'));
                $mform->setType("{$fieldname}_newvalue", PARAM_TEXT);
                $mform->disabledIf("{$fieldname}_newvalue", "{$fieldname}_selected", 'neq', '');
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
            $selected = (int) $entry->{"c{$fieldid}_content"};
            $options = $field->options_menu();

            if (!empty($params['options'])) {
                $str = array();
                foreach ($options as $key => $option) {
                    $isselected = (int) ($key == $selected);
                    $str[] = "$isselected $option";
                }
                $str = implode(',', $str);
                return $str;
            }

            if (!empty($params['key'])) {
                if ($selected) {
                    return $selected;
                } else {
                    return '0';
                }
            }

            if ($selected and isset($options[$selected])) {
                return (string) $options[$selected];
            }
        }

        return '';
    }

    /**
     * Overriding {@link dataformfieldrenderer::get_pattern_import_settings()}
     * to allow only the base pattern and add a setting for 'allow add option'.
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
    protected function display_category($entry, $params = null) {
        $field = $this->_field;
        $fieldid = $field->id;
        if (!isset($this->_cats[$fieldid])) {
            $this->_cats[$fieldid] = null;
        }

        $str = '';
        if (isset($entry->{"c{$fieldid}_content"})) {
            $selected = (int) $entry->{"c{$fieldid}_content"};

            $options = $field->options_menu();
            if ($selected and $selected <= count($options) and $selected != $this->_cats[$fieldid]) {
                $this->_cats[$fieldid] = $selected;
                $str = $options[$selected];
            }
        }

        return $str;
    }

    /**
     *
     */
    protected function render(&$mform, $fieldname, $options, $selected, $required = false) {
        $select = &$mform->createElement('select', $fieldname, null, array('' => get_string('choosedots')) + $options);
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
     * Array of patterns this field supports
     */
    protected function patterns() {
        $fieldname = $this->_field->name;

        $patterns = parent::patterns();
        $patterns["[[$fieldname]]"] = array(true, $fieldname);
        $patterns["[[$fieldname:addnew]]"] = array(true, $fieldname);
        $patterns["[[$fieldname:options]]"] = array(false);
        $patterns["[[$fieldname:cat]]"] = array(false);
        $patterns["[[$fieldname:key]]"] = array(false);

        return $patterns;
    }
}
