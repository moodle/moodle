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
 * @copyright  2020 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    http://intelliboard.net/
 */

namespace local_intelliboard\transcripts;

use local_intelliboard\repositories\transcripts_repository;

class transcripts_courses extends \local_intelliboard\transcripts\transcripts {

    const STATUS_INPROGRESS     = 0;
    const STATUS_COMPLETED      = 1;
    const STATUS_CLOSED         = 2;

    public $table   = 'local_intelliboard_trns_c';
    public $record  = null;

    public function update_transcripts($params = null) {
        global $CFG;

        if (!count($params)) {
            return;
        }

        require_once($CFG->libdir . '/completionlib.php');

        $user   = $params['user'];
        $course = $params['course'];

        $record                 = $this->record;
        $record->userid         = $user->id;
        $record->useremail      = $user->email;
        $record->firstname      = $user->firstname;
        $record->lastname       = $user->lastname;
        $record->courseid       = $course->id;
        $record->coursename     = $course->fullname;
        $record->timemodified   = time();

        // Get user grade.
        $record = \local_intelliboard\repositories\transcripts_repository::get_transcripts_course_grades(
            ['userid' => $user->id, 'courseid' => $course->id], $record
        );

        if (isset($params['status'])) {
            $record->status = $params['status'];
        } else {
            $ccompletion = new \completion_completion([
                'userid'  => $user->id,
                'course'  => $course->id
            ]);

            if ($ccompletion->is_complete()) {
                $record->status = self::STATUS_COMPLETED;
                $record->completeddate = $ccompletion->timecompleted;
            }
        }
        if (isset($params['completeddate'])) {
            $record->completeddate = $params['completeddate'];
        }
        if (isset($params['rolesids'])) {
            $record->rolesids = $params['rolesids'];
        }
        if (isset($params['groupsids'])) {
            $record->groupsids = $params['groupsids'];
        }

        $transcripts = $this->get_records(['userid' => $user->id, 'courseid' => $course->id, 'unenroldate' => 0]);
        if (count($transcripts)) {
            foreach ($transcripts as $recordexists) {
                $record->id = $recordexists->id;

                $this->set_record($record);
                $this->update();
            }
        } else {

            // Get user enrolment details, roles, groups.
            $enrolments = transcripts_repository::get_transcripts_enrolments(
                ['userid' => $user->id, 'courseid' => $course->id]
            );

            if (count($enrolments)) {
                foreach ($enrolments as $enrolment) {

                    $record->userenrolid    = $enrolment->id;
                    $record->enrolid        = $enrolment->enrolid;
                    $record->enroltype      = $enrolment->enrol;
                    $record->enroldate      = ($enrolment->timestart) ? $enrolment->timestart : $enrolment->timecreated;
                    $record->rolesids       = $enrolment->rolesids;
                    $record->groupsids      = $enrolment->groupsids;
                    $record->timecreated    = time();

                    $this->set_record($record);
                    $this->insert();
                }
            }
        }

        return $this->get_records(['userid' => $user->id, 'courseid' => $course->id, 'unenroldate' => 0]);
    }

    public function get_statuses() {
        return [
            self::STATUS_INPROGRESS     => get_string('inprogress', 'local_intelliboard'),
            self::STATUS_COMPLETED      => get_string('completed', 'local_intelliboard'),
            self::STATUS_CLOSED         => get_string('closed', 'local_intelliboard'),
        ];
    }

}
