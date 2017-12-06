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

class dataformfield_duration_duration extends mod_dataform\pluginbase\dataformfield {
    public $width;
    public $widthunit;

    protected $_units = null;

    /**
     *
     */
    public function __construct($field) {
        parent::__construct($field);

        $this->width = $this->param2;
        $this->widthunit = $this->param3;
    }

    /**
     * Returns time associative array of unit length.
     *
     * @return array unit length in seconds => string unit name.
     */
    public function get_units() {
        if (is_null($this->_units)) {
            $this->_units = array(
                604800 => get_string('weeks'),
                86400 => get_string('days'),
                3600 => get_string('hours'),
                60 => get_string('minutes'),
                1 => get_string('seconds'),
            );
        }
        return $this->_units;
    }

    /**
     * Converts seconds to the best possible time unit. for example
     * 1800 -> array(30, 60) = 30 minutes.
     *
     * @param int $seconds an amout of time in seconds.
     * @return array associative array ($number => $unit)
     */
    public function seconds_to_unit($seconds) {
        if ($seconds == 0) {
            return array(0, $this->_options['defaultunit']);
        }
        foreach ($this->get_units() as $unit => $notused) {
            if (fmod($seconds, $unit) == 0) {
                return array($seconds / $unit, $unit);
            }
        }
        return array($seconds, 1);
    }

    /**
     *
     */
    protected function get_sql_compare_text($column = 'content') {
        global $DB;
        return $DB->sql_cast_char2int("c{$this->id}.$column", true);
    }

}
