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
 * Front-end class.
 *
 * @package availability_profile
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace availability_profile;

defined('MOODLE_INTERNAL') || die();

/**
 * Front-end class.
 *
 * @package availability_profile
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class frontend extends \core_availability\frontend {
    protected function get_javascript_strings() {
        return array('op_contains', 'op_doesnotcontain', 'op_endswith', 'op_isempty',
                'op_isequalto', 'op_isnotempty', 'op_startswith', 'conditiontitle',
                'label_operator', 'label_value');
    }

    protected function get_javascript_init_params($course, \cm_info $cm = null,
            \section_info $section = null) {
        // Standard user fields.
        $standardfields = array(
            'firstname' => \core_user\fields::get_display_name('firstname'),
            'lastname' => \core_user\fields::get_display_name('lastname'),
            'email' => \core_user\fields::get_display_name('email'),
            'city' => \core_user\fields::get_display_name('city'),
            'country' => \core_user\fields::get_display_name('country'),
            'idnumber' => \core_user\fields::get_display_name('idnumber'),
            'institution' => \core_user\fields::get_display_name('institution'),
            'department' => \core_user\fields::get_display_name('department'),
            'phone1' => \core_user\fields::get_display_name('phone1'),
            'phone2' => \core_user\fields::get_display_name('phone2'),
            'address' => \core_user\fields::get_display_name('address')
        );
        \core_collator::asort($standardfields);

        // Custom fields.
        $customfields = array();
        $options = array('context' => \context_course::instance($course->id));
        foreach (condition::get_custom_profile_fields() as $field) {
            $customfields[$field->shortname] = format_string($field->name, true, $options);
        }
        \core_collator::asort($customfields);

        // Make arrays into JavaScript format (non-associative, ordered) and return.
        return array(self::convert_associative_array_for_js($standardfields, 'field', 'display'),
                self::convert_associative_array_for_js($customfields, 'field', 'display'));
    }
}
