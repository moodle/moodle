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
 * Steps definitions related to mod_attendance
 *
 * @package   mod_attendance
 * @copyright 2021 Dan Marsden
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');

use Behat\Gherkin\Node\TableNode as TableNode;

use Behat\Mink\Exception\ExpectationException as ExpectationException;

/**
 * Steps definitions related to mod_attendance.
 *
 * @copyright 2021 Dan Marsden
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_mod_attendance extends behat_question_base {
    /**
     * Convert page names to URLs for steps like 'When I am on the "[identifier]" "[page type]" page'.
     *
     * Recognised page names are:
     * | pagetype          | name meaning                                | description                                  |
     * | View              | attendance name                             | The attendance info page (view.php)          |
     *
     * @param string $type identifies which type of page this is, e.g. 'Attempt review'.
     * @param string $identifier identifies the particular page, e.g. 'Test attendance > student > Attempt 1'.
     * @return moodle_url the corresponding URL.
     * @throws Exception with a meaningful error message if the specified page cannot be found.
     */
    protected function resolve_page_instance_url(string $type, string $identifier): moodle_url {
        switch (strtolower($type)) {
            case 'view':
                return new moodle_url('/mod/attendance/view.php',
                        ['id' => $this->get_cm_by_attendance_name($identifier)->id]);
            case 'report':
                return new moodle_url('/mod/attendance/report.php',
                       ['id' => $this->get_cm_by_attendance_name($identifier)->id]);
            default:
                throw new Exception('Unrecognised attendance page type "' . $type . '."');
        }
    }

    /**
     * Get a attendance by name.
     *
     * @param string $name attendance name.
     * @return stdClass the corresponding DB row.
     */
    protected function get_attendance_by_name(string $name): stdClass {
        global $DB;
        return $DB->get_record('attendance', array('name' => $name), '*', MUST_EXIST);
    }

    /**
     * Get a quiz cmid from the quiz name.
     *
     * @param string $name quiz name.
     * @return stdClass cm from get_coursemodule_from_instance.
     */
    protected function get_cm_by_attendance_name(string $name): stdClass {
        $attendance = $this->get_attendance_by_name($name);
        return get_coursemodule_from_instance('attendance', $attendance->id, $attendance->course);
    }
}
