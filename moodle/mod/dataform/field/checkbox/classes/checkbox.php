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
 * @package dataformfield_checkbox
 * @copyright 2014 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class dataformfield_checkbox_checkbox extends dataformfield_selectmulti_selectmulti {

    /**
     *
     */
    public function content_names() {
        $optioncount = count(explode("\n", $this->param1));
        $contentnames = array('newvalue');
        foreach (range(1, $optioncount) as $key) {
            $contentnames[] = "selected_$key";
        }
        // Add contentname selected for import.
        $contentnames[] = 'selected';

        return $contentnames;
    }

    /**
     *
     */
    protected function format_content($entry, array $values = null) {
        $fieldid = $this->id;
        $entryid = $entry->id;

        // When called by import values are already collated in selected.
        if (!empty($values['selected'])) {
            return parent::format_content($entry, $values);
        }

        // When called by form submission collate the selected to one array.
        $selected = array();
        if (!empty($values)) {
            $optioncount = count(explode("\n", $this->param1));
            foreach (range(1, $optioncount) as $key) {
                if (!empty($values["selected_$key"])) {
                    $selected[] = $key;
                }
            }
        }
        $values['selected'] = $selected;

        return parent::format_content($entry, $values);
    }

}
