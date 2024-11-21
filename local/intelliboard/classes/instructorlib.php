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
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 * @package    local_intelliboard
 * @copyright  2019 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    http://intelliboard.net/
 */

use local_intelliboard\repositories\user_settings;

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/externallib.php");

class local_intelliboard_instructorlib extends external_api {

    /**
     * Save instructor courses
     */
    public static function save_instructor_courses_parameters() {
        return new external_function_parameters([
            'data' => new external_value(PARAM_RAW)
        ]);
    }

    public static function save_instructor_courses($data) {
        global $DB, $USER, $CFG;

        require_once($CFG->dirroot .'/local/intelliboard/locallib.php');
        require_once($CFG->dirroot . '/local/intelliboard/instructor/lib.php');

        $params = self::validate_parameters(
            self::save_instructor_courses_parameters(), ['data' => $data]
        );

        self::validate_context(context_system::instance());
        $availablecourses = intelliboard_instructor_getcourses('', true, '', false, false);
        $availablecourses = is_array($availablecourses) ? array_keys($availablecourses) : [];

        try {
            $transaction = $DB->start_delegated_transaction();

            $DB->delete_records(
                'local_intelliboard_assign',
                ['rel' => 'instructordashboard', 'type' => 'courses', 'userid' => $USER->id]
            );

            $courses = json_decode($params['data'], true)['courses'];

            foreach ($courses as $course) {
                if(in_array($course, $availablecourses)) {
                    $row = (object) [
                        'rel' => 'instructordashboard',
                        'type' => 'courses',
                        'userid' => $USER->id,
                        'instance' => $course,
                        'timecreated' => time(),
                    ];

                    $DB->insert_record('local_intelliboard_assign', $row);
                }
            }

            $transaction->allow_commit();

            return ['status' => 200];
        } catch(Exception $e) {
            $transaction->rollback($e);

            return ['status' => 500];
        }
    }

    public static function save_instructor_courses_returns() {
        new external_single_structure([
            'status' => new external_value(PARAM_INT, 'Operation status'),
        ]);
    }
}
