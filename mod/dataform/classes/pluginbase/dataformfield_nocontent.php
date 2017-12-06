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
 * @copyright 2012 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_dataform\pluginbase;

/**
 * Base class for Dataform field types that require no content
 */
abstract class dataformfield_nocontent extends dataformfield {
    /**
     *
     */
    public function get_content_parts() {
        return array();
    }

    public function update_content($entry, array $values = null, $savenew = false) {
        return true;
    }

    public function delete_content($entryid = 0) {
        return true;
    }

    public function transfer_content($tofieldid) {
        return true;
    }

    /**
     * Returns an array of distinct content of the field.
     *
     * @param string $element
     * @param int $sortdir Sort direction 0|1 ASC|DESC
     * @return array
     */
    public function get_distinct_content($element, $sortdir = 0) {
        return array();
    }

    public function get_select_sql() {
        return '';
    }

    /**
     *
     */
    public function is_dataform_content() {
        return false;
    }
}
