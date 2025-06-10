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

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot.'/mod/assign/locallib.php');


class report_editdates_mod_assign_date_extractor
extends report_editdates_mod_date_extractor {

    public function __construct($course) {
        parent::__construct($course, 'assign');
        parent::load_data();
    }

    public function get_settings(cm_info $cm) {
        $assign = $this->mods[$cm->instance];

        return array(
                'allowsubmissionsfromdate' => new report_editdates_date_setting(
                        get_string('allowsubmissionsfromdate', 'assign'),
                        $assign->allowsubmissionsfromdate,
                        self::DATETIME, true),
                'duedate' => new report_editdates_date_setting(
                        get_string('duedate', 'assign'),
                        $assign->duedate,
                        self::DATETIME, true),
                'cutoffdate' => new report_editdates_date_setting(
                        get_string('cutoffdate', 'assign'),
                        $assign->cutoffdate,
                        self::DATETIME, true),
                'gradingduedate' => new report_editdates_date_setting(
                        get_string('gradingduedate', 'assign'),
                        $assign->gradingduedate,
                        self::DATETIME, true),
                );
    }

    public function validate_dates(cm_info $cm, array $dates) {
        $errors = array();
        if ($dates['allowsubmissionsfromdate'] && $dates['duedate']
                && $dates['duedate'] < $dates['allowsubmissionsfromdate']) {
            $errors['duedate'] = get_string('duedatevalidation', 'assign');
        }

        if ($dates['duedate'] && $dates['cutoffdate'] && $dates['duedate'] > $dates['cutoffdate']) {
            $errors['cutoffdate'] = get_string('cutoffdatevalidation', 'assign');
        }

        if ($dates['duedate'] && $dates['gradingduedate'] && $dates['duedate'] > $dates['gradingduedate']) {
            $errors['gradingduedate'] = get_string('gradingdueduedatevalidation', 'assign');
        }

        if ($dates['allowsubmissionsfromdate'] && $dates['gradingduedate'] &&
            $dates['allowsubmissionsfromdate'] > $dates['gradingduedate']) {
            $errors['gradingduedate'] = get_string('gradingduefromdatevalidation', 'assign');
        }
        return $errors;
    }

    public function save_dates(cm_info $cm, array $dates) {
        global $DB, $COURSE;

        $update = new stdClass();
        $update->id = $cm->instance;
        $update->duedate = $dates['duedate'];
        $update->allowsubmissionsfromdate = $dates['allowsubmissionsfromdate'];
        $update->cutoffdate = $dates['cutoffdate'];
        $update->gradingduedate = $dates['gradingduedate'];

        $result = $DB->update_record('assign', $update);

        $module = new assign(context_module::instance($cm->id), null, null);

        // Update the calendar and grades.
        $module->update_calendar($cm->id);

        $module->update_gradebook(false, $cm->id);
    }
}
