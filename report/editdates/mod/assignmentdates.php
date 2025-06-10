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

require_once($CFG->dirroot.'/mod/assignment/lib.php');


class report_editdates_mod_assignment_date_extractor
extends report_editdates_mod_date_extractor {

    public function __construct($course) {
        parent::__construct($course, 'assignment');
        parent::load_data();
    }

    public function get_settings(cm_info $cm) {
        $ass = $this->mods[$cm->instance];
        // Availability and due date settings for an assignment.
        return array(
            'timeavailable' => new report_editdates_date_setting(
                    get_string('availabledate', 'assignment'),
                    $ass->timeavailable, self::DATETIME, true),
            'timedue' => new report_editdates_date_setting(
                    get_string('duedate', 'assignment'),
                    $ass->timedue, self::DATETIME, true)
        );
    }

    public function validate_dates(cm_info $cm, array $dates) {
        $errors = array();
        if ($dates['timeavailable'] != 0 && $dates['timedue'] != 0 && $dates['timedue'] < $dates['timeavailable']) {
            $errors['timedue'] = get_string('timedue', 'report_editdates');
        }
        return $errors;
    }

    public function save_dates(cm_info $cm, array $dates) {

        // Fetch module instance from $mods array.
        $assignment = $this->mods[$cm->instance];

        $assignment->instance = $cm->instance;
        $assignment->coursemodule = $cm->id;
        $assignment->cmidnumber = $cm->id;

        // Updating date values.
        foreach ($dates as $datetype => $datevalue) {
            $assignment->$datetype = $datevalue;
        }

        // Method name to udpate the instance and associated events.
        $methodname = $cm->modname.'_update_instance';
        // Calling the method.
        $methodname($assignment);
    }
}
