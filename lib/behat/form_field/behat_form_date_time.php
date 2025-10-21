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

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__  . '/behat_form_date.php');

/**
 * Date time form field.
 *
 * This class will be refactored in case we are interested in
 * creating more complex formats to fill date-time fields.
 *
 * @package    core_form
 * @category   test
 * @copyright  2013 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_form_date_time extends behat_form_date {
    /**
     * Returns the current value of the field
     *
     * @return string
     */
    public function get_value() {
        return make_timestamp(
            $this->get_child_field_value('year'),
            $this->get_child_field_value('month'),
            $this->get_child_field_value('day'),
            $this->get_child_field_value('hour'),
            $this->get_child_field_value('minute'),
        );
    }

    /**
     * Returns the date field identifiers and the values that should be assigned to them.
     *
     * @param int $timestamp The UNIX timestamp
     * @return array
     */
    protected function get_mapped_fields(int $timestamp): array {
        return array_merge(parent::get_mapped_fields($timestamp), [
            'hour' => date('G', $timestamp),
            'minute' => (int) date('i', $timestamp)
        ]);
    }
}
