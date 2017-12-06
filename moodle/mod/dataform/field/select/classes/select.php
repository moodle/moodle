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

class dataformfield_select_select extends mod_dataform\pluginbase\dataformfield {
    protected $_options = array();

    /**
     * Update a field in the database
     */
    public function update($data) {
        global $DB;

        // Before we update get the current options.
        $oldoptions = $this->options_menu();
        // Update.
        parent::update($data);

        // Adjust content if necessary.
        $adjustments = array();
        // Get updated options.
        $newoptions = $this->options_menu(true);
        foreach ($newoptions as $newkey => $value) {
            if (!isset($oldoptions[$newkey]) or $value != $oldoptions[$newkey]) {
                if ($key = array_search($value, $oldoptions) or $key !== false) {
                    $adjustments[$key] = $newkey;
                }
            }
        }

        if (!empty($adjustments)) {
            // Fetch all contents of the field whose content in keys.
            list($incontent, $params) = $DB->get_in_or_equal(array_keys($adjustments));
            array_unshift($params, $this->id);
            $contents = $DB->get_records_select_menu('dataform_contents',
                                        " fieldid = ? AND content $incontent ",
                                        $params,
                                        '',
                                        'id,content');
            if ($contents) {
                if (count($contents) == 1) {
                    list($id, $content) = each($contents);
                    $DB->set_field('dataform_contents', 'content', $adjustments[$content], array('id' => $id));
                } else {
                    $params = array();
                    $sql = "UPDATE {dataform_contents} SET content = CASE id ";
                    foreach ($contents as $id => $content) {
                        $newcontent = $adjustments[$content];
                        $sql .= " WHEN ? THEN ? ";
                        $params[] = $id;
                        $params[] = $newcontent;
                    }
                    list($inids, $paramids) = $DB->get_in_or_equal(array_keys($contents));
                    $sql .= " END WHERE id $inids ";
                    $params = array_merge($params, $paramids);
                    $DB->execute($sql, $params);
                }
            }
        }
        return true;
    }

    /**
     *
     */
    public function content_names() {
        return array('selected', 'newvalue');
    }

    /**
     *
     */
    protected function format_content($entry, array $values = null) {
        $fieldid = $this->id;
        // Old contents.
        $oldcontents = array();
        if (isset($entry->{"c{$fieldid}_content"})) {
            $oldcontents[] = $entry->{"c{$fieldid}_content"};
        }
        // New contents.
        $contents = array();

        $selected = $newvalue = null;
        if (!empty($values)) {
            foreach ($values as $name => $value) {
                $value = (string) $value;
                if (!empty($name) and !empty($value)) {
                    ${$name} = $value;
                }
            }
        }
        // Update new value in the field type.
        if ($newvalue = s($newvalue)) {
            $options = $this->options_menu();
            if (!$selected = (int) array_search($newvalue, $options)) {
                $selected = count($options) + 1;
                $this->param1 = trim($this->param1). "\n$newvalue";
                $this->update($this->data);
            }
        }
        // Add the content.
        if (!is_null($selected)) {
            $contents[] = $selected;
        }

        return array($contents, $oldcontents);
    }

    /**
     *
     */
    protected function get_sql_compare_text($column = 'content') {
        global $DB;

        $alias = $this->get_sql_alias();
        return $DB->sql_compare_text("$alias.$column", 255);
    }

    /**
     * Returns the index of the sepcified value in the field's options.
     * If not found returns '#'. This assumes that the option indices are
     * always numeric. The comparison of the search value with the options
     * is case insensitive.
     *
     * @param string $value
     * @return int
     */
    public function get_search_value($value) {
        $options = $this->options_menu();
        if ($key = array_search(strtolower($value), array_map('strtolower', $options))) {
            return $key;
        } else {
            return '#';
        }
    }

    /**
     *
     */
    public function get_search_sql($search) {
        if (!$search) {
            return null;
        }

        // Convert the search value to option index.
        $search[3] = $this->get_search_value($search[3]);

        return parent::get_search_sql($search);
    }

    /**
     *
     */
    public function options_menu($forceget = false) {
        if (!$this->_options or $forceget) {
            if ($this->param1) {
                $rawoptions = explode("\n", $this->param1);
                foreach ($rawoptions as $key => $option) {
                    $option = trim($option);
                    if ($option != '') {
                        $this->_options[$key + 1] = $option;
                    }
                }
            }
        }
        return $this->_options;
    }

    // IMPORT EXPORT.
    /**
     *
     */
    public function prepare_import_content($data, $importsettings, $csvrecord = null, $entryid = null) {
        // Import only from csv.
        if (!$csvrecord) {
            return $data;
        }

        // There is only one import pattern for this field.
        $importsetting = reset($importsettings);

        $fieldid = $this->id;
        $csvname = $importsetting['name'];
        $allownew = !empty($importsetting['allownew']);
        $label = !empty($csvrecord[$csvname]) ? $csvrecord[$csvname] : null;

        if ($label) {
            $options = $this->options_menu();
            if ($optionkey = array_search($label, $options)) {
                $data->{"field_{$fieldid}_{$entryid}_selected"} = $optionkey;
            } else if ($allownew) {
                $data->{"field_{$fieldid}_{$entryid}_newvalue"} = $label;
            }
        }

        return $data;
    }

}
