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
 * @subpackage radiobutton
 * @copyright 2011 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class dataformfield_radiobutton_radiobutton extends dataformfield_select_select {

    /**
     * Returns list of seprators.
     *
     * @return array Array of arrays (name => string, chr => string)
     */
    public function get_separator_types() {
        return array(
            array('name' => get_string('newline', 'dataformfield_selectmulti'), 'chr' => '<br />'),
            array('name' => get_string('space', 'dataformfield_selectmulti'), 'chr' => '&#32;'),
            array('name' => get_string('comma', 'dataformfield_selectmulti'), 'chr' => '&#44;'),
            array('name' => get_string('commaandspace', 'dataformfield_selectmulti'), 'chr' => '&#44;&#32;')
        );
    }

    /**
     * Returns the field configured separator.
     *
     * @return string
     */
    public function get_separator() {
        $separatortypes = $this->separator_types;

        $selected = (int) $this->param3;
        return $separatortypes[$selected]['chr'];
    }

}
