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

// Let codechecker ignore the sniff for this file for nullable types since the super method of
// create_instance is not yet rewritten and mod_attendance_generator::create_instance must have an identical signature.
// phpcs:disable PHPCompatibility.FunctionDeclarations.RemovedImplicitlyNullableParam.Deprecated

/**
 * mod_attendance data generator
 *
 * @package    mod_attendance
 * @category   test
 * @copyright  2013 Davo Smith, Synergy Learning
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * mod_attendance data generator
 *
 * @package    mod_attendance
 * @category   test
 * @copyright  2013 Davo Smith, Synergy Learning
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_attendance_generator extends testing_module_generator {

    /**
     * Create new attendance module instance
     *
     * @param array|stdClass $record
     * @param null|array $options
     * @return stdClass mod_attendance_structure
     */
    public function create_instance($record = null, array $options = null) {
        global $CFG, $DB;
        require_once($CFG->dirroot.'/mod/attendance/lib.php');

        $this->instancecount++;
        $i = $this->instancecount;

        $record = (object)(array)$record;
        $options = (array)$options;

        if (empty($record->course)) {
            throw new coding_exception('module generator requires $record->course');
        }
        if (!isset($record->name)) {
            $record->name = get_string('pluginname', 'attendance').' '.$i;
        }
        if (!isset($record->grade)) {
            $record->grade = 100;
        }

        return parent::create_instance($record, (array)$options);
    }
}
