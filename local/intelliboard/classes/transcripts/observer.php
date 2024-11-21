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

defined('MOODLE_INTERNAL') || die();

use local_intelliboard\helpers\DBHelper;
use local_intelliboard\repositories\transcripts_repository;
use local_intelliboard\transcripts\transcripts_courses;
use local_intelliboard\transcripts\transcripts_modules;

/**
 * Event observer for transcripts.
 */
class observer {

    /**
     * Triggered when 'user_enrolment_created' event happens.
     *
     * @param \core\event\user_enrolment_created $event event generated when user profile is updated.
     */
    public static function transcripts_user_enrolment_created(\core\event\user_enrolment_created $event) {
        global $DB;

        if (!get_config('local_intelliboard', 'enable_transcripts')) {
            return;
        }

        $course = $DB->get_record('course', array('id' => $event->contextinstanceid));
        $user = $DB->get_record('user', array('id' => $event->relateduserid));
        $enrolid = $event->objectid;

        // Get user enrolment details, roles, groups.
        $enrolment = transcripts_repository::get_transcripts_enrolments(
            ['userid' => $user->id, 'courseid' => $course->id, 'ueid' => $enrolid],
            true
        );

        if (!$enrolment) {
            return;
        }

        $transcript = new transcripts_courses();
        $record = $transcript->record;
        $record->userid         = $user->id;
        $record->useremail      = $user->email;
        $record->firstname      = $user->firstname;
        $record->lastname       = $user->lastname;
        $record->userenrolid    = $enrolment->id;
        $record->enrolid        = $enrolment->enrolid;
        $record->enroltype      = $enrolment->enrol;
        $record->courseid       = $course->id;
        $record->coursename     = $course->fullname;
        $record->enroldate      = ($enrolment->timestart) ? $enrolment->timestart : $enrolment->timecreated;
        $record->status         = $transcript::STATUS_INPROGRESS;
        $record->rolesids       = $enrolment->rolesids;
        $record->groupsids      = $enrolment->groupsids;
        $record->timecreated    = time();
        $record->timemodified   = time();

        // Get user grade.
        $record = transcripts_repository::get_transcripts_course_grades(
            ['userid' => $user->id, 'courseid' => $course->id], $record
        );

        $transcript->set_record($record);
        $record->id = $transcript->insert();
    }

    /**
     * Triggered when 'user_enrolment_deleted' event happens.
     *
     * @param \core\event\user_enrolment_deleted $event event generated when user profile is updated.
     */
    public static function transcripts_user_enrolment_deleted(\core\event\user_enrolment_deleted $event) {
        global $DB, $CFG;

        if (!get_config('local_intelliboard', 'enable_transcripts')) {
            return;
        }

        require_once($CFG->libdir . '/completionlib.php');

        $course = $DB->get_record('course', array('id' => $event->contextinstanceid));
        $user = $DB->get_record('user', array('id' => $event->relateduserid));
        $enrol = $DB->get_record('enrol', ['id' => $event->other['userenrolment']['enrolid']]);
        $userenrolid = $event->other['userenrolment']['id'];

        $transcript             = new transcripts_courses();
        $record                 = $transcript->record;
        $record->userid         = $user->id;
        $record->useremail      = $user->email;
        $record->firstname      = $user->firstname;
        $record->lastname       = $user->lastname;
        $record->userenrolid    = $userenrolid;
        $record->enrolid        = $enrol->id;
        $record->enroltype      = $enrol->enrol;
        $record->courseid       = $course->id;
        $record->coursename     = $course->fullname;
        $record->status         = $transcript::STATUS_CLOSED;
        $record->enroldate      = ($event->other['userenrolment']['timestart']) ?
                                    $event->other['userenrolment']['timestart'] :
                                    $event->other['userenrolment']['timecreated'];
        $record->unenroldate    = time();
        $record->timemodified   = time();

        // Get user grade.
        $record = transcripts_repository::get_transcripts_course_grades(
            ['userid' => $user->id, 'courseid' => $course->id], $record
        );

        $recordexists = $transcript->get_record(['userenrolid' => $userenrolid, 'userid' => $user->id]);
        if ($recordexists) {
            $record->id = $recordexists->id;

            $transcript->set_record($record);
            $transcript->update();
        } else {
            $record->timecreated = time();

            $transcript->set_record($record);
            $transcript->insert();
        }

        // Update modules transcripts status.
        $transcriptsmodules    = new transcripts_modules();
        $recordsmodules        = $transcriptsmodules->get_records([
            'userid' => $user->id,
            'userenrolid' => $userenrolid,
        ]);

        foreach ($recordsmodules as $recordmodule) {
            $record                 = $recordmodule;
            $record->timemodified   = time();
            $record->status         = $transcriptsmodules::STATUS_CLOSED;

            $transcriptsmodules->set_record($record);
            $transcriptsmodules->update();
        }
    }

    /**
     * Triggered when 'course_completed' event is triggered.
     *
     * @param \core\event\course_completed $event
     */
    public static function transcripts_course_completed(\core\event\course_completed $event) {
        global $DB;

        if (!get_config('local_intelliboard', 'enable_transcripts')) {
            return;
        }

        $course = $DB->get_record('course', array('id' => $event->courseid));
        $user = $DB->get_record('user', array('id' => $event->relateduserid));

        // Update course transcript.
        $transcriptscourses = new transcripts_courses();
        $transcriptscourses->update_transcripts([
                'user'          => $user,
                'course'        => $course,
                'status'        => $transcriptscourses::STATUS_COMPLETED,
                'completeddate' => time()
        ]);
    }

    /**
     * Triggered when 'course_module_completion_updated' event is triggered.
     *
     * @param \core\event\course_module_completion_updated $event
     */
    public static function transcripts_course_module_completion_updated(\core\event\course_module_completion_updated $event) {
        global $DB, $CFG;

        if (!get_config('local_intelliboard', 'enable_transcripts')) {
            return;
        }

        require_once($CFG->libdir . '/gradelib.php');

        $eventdata = $event->get_record_snapshot('course_modules_completion', $event->objectid);
        $modinfo = get_fast_modinfo($event->courseid);
        $cm = $modinfo->get_cm($event->contextinstanceid);
        if (!$cm) {
            return;
        }

        $course = $DB->get_record('course', array('id' => $event->courseid));
        $userid = ($event->relateduserid) ? $event->relateduserid : $event->userid;
        $user = $DB->get_record('user', array('id' => $userid));

        // Update course transcript.
        $transcriptscourses = new transcripts_courses();
        $recordscourses = $transcriptscourses->update_transcripts(['user' => $user, 'course' => $course]);

        // Update cm completion.
        $transcript = new transcripts_modules();
        $record                 = $transcript->record;
        $record->userid         = $user->id;
        $record->courseid       = $course->id;
        $record->cmid           = $cm->id;
        $record->moduleid       = $cm->instance;
        $record->modulename     = $cm->name;
        $record->moduletype     = $cm->modname;
        $record->timemodified   = time();
        $record->completeddate  = 0;
        $record->status         = $transcript::STATUS_INPROGRESS;

        if ($eventdata->completionstate == COMPLETION_COMPLETE) {
            $record->completeddate = time();
            $record->status = $transcript::STATUS_COMPLETED;
        } else if ($eventdata->completionstate == COMPLETION_COMPLETE_PASS) {
            $record->completeddate = time();
            $record->status = $transcript::STATUS_PASSED;
        } else if ($eventdata->completionstate == COMPLETION_COMPLETE_FAIL) {
            $record->completeddate = time();
            $record->status = $transcript::STATUS_FAILED;
        }

        // Get user grade.
        $record = transcripts_repository::get_transcripts_module_grades(
            ['userid' => $user->id, 'courseid' => $course->id, 'modname' => $cm->modname, 'instance' => $cm->instance], $record
        );

        if (count($recordscourses)) {
            foreach ($recordscourses as $recordcourse) {

                $record->userenrolid = $recordcourse->userenrolid;
                $transcriptsmodules = $transcript->get_records(
                    ['userid' => $user->id, 'userenrolid' => $recordcourse->userenrolid, 'cmid' => $cm->id]
                );
                if (count($transcriptsmodules)) {
                    foreach ($transcriptsmodules as $recordmodule) {
                        $record->id = $recordmodule->id;

                        $transcript->set_record($record);
                        $transcript->update();
                    }
                } else {
                    $record->timecreated = time();

                    $transcript->set_record($record);
                    $transcript->insert();
                }

            }
        }
    }

    /**
     * Triggered when 'course_module_viewed' event is triggered.
     *
     * @param \core\event\course_module_viewed $event
     */
    public static function transcripts_course_module_viewed(\core\event\course_module_viewed $event) {
        global $DB;

        if (!get_config('local_intelliboard', 'enable_transcripts')) {
            return;
        }

        $course = $DB->get_record('course', ['id' => $event->courseid]);
        $modinfo = get_fast_modinfo($event->courseid);
        $cm = $modinfo->get_cm($event->contextinstanceid);
        if (!$cm) {
            return;
        }

        $userid = ($event->relateduserid) ? $event->relateduserid : $event->userid;
        $user = $DB->get_record('user', array('id' => $userid));

        // Update course transcript.
        $transcriptscourses    = new transcripts_courses();
        $recordscourses        = $transcriptscourses->update_transcripts([
            'user'   => $user,
            'course' => $course
        ]);

        if (count($recordscourses)) {

            $transcript             = new transcripts_modules();
            $record                 = $transcript->record;
            $record->userid         = $user->id;
            $record->courseid       = $course->id;
            $record->cmid           = $cm->id;
            $record->moduleid       = $cm->instance;
            $record->modulename     = $cm->name;
            $record->moduletype     = $cm->modname;
            $record->startdate      = time();
            $record->timemodified   = time();

            foreach ($recordscourses as $recordcourse) {

                $record->userenrolid = $recordcourse->userenrolid;
                $transcriptsmodules = $transcript->get_records([
                    'userid' => $user->id,
                    'userenrolid' => $recordcourse->userenrolid,
                    'cmid' => $cm->id
                ]);

                if (count($transcriptsmodules)) {
                    foreach ($transcriptsmodules as $recordmodule) {
                        $record->id = $recordmodule->id;

                        $transcript->set_record($record);
                        $transcript->update();
                    }
                } else {
                    $record->timecreated    = time();
                    $record->status         = $transcript::STATUS_INPROGRESS;

                    $transcript->set_record($record);
                    $transcript->insert();
                }

            }
        }
    }

    /**
     * Triggered when 'user_graded' event is triggered.
     *
     * @param \core\event\user_graded $event
     */
    public static function transcripts_user_graded(\core\event\user_graded $event) {
        global $DB;

        if (!get_config('local_intelliboard', 'enable_transcripts')) {
            return;
        }

        if (!$item = $DB->get_record('grade_items', ['id' => $event->other['itemid']])) {
            return;
        }
        if (!$grade = $DB->get_record('grade_grades', ['id' => $event->objectid])) {
            return;
        }

        $course     = $DB->get_record('course', ['id' => $event->courseid]);
        $userid     = ($event->relateduserid) ? $event->relateduserid : $event->userid;
        $user       = $DB->get_record('user', array('id' => $userid));
        $finalgrade = $event->other['finalgrade'];

        if ($item->itemtype == 'course') {

            // Update course transcript.
            $transcriptscourses = new transcripts_courses();
            $recordscourses = $transcriptscourses->update_transcripts(['user' => $user, 'course' => $course]);

        } else if ($item->itemtype == 'mod') {

            $cmid = get_coursemodule_from_instance($item->itemmodule, $item->iteminstance, $course->id)->id;
            $modinfo = get_fast_modinfo($course->id);
            $cm = $modinfo->get_cm($cmid);

            if ($cm) {

                // Update course transcript.
                $transcriptscourses    = new transcripts_courses();
                $recordscourses        = $transcriptscourses->update_transcripts(['user' => $user, 'course' => $course]);

                if (count($recordscourses)) {

                    // Update cm transcript.
                    $transcript             = new transcripts_modules();
                    $record                 = $transcript->record;
                    $record->userid         = $user->id;
                    $record->courseid       = $course->id;
                    $record->cmid           = $cm->id;
                    $record->moduleid       = $cm->instance;
                    $record->modulename     = $cm->name;
                    $record->moduletype     = $cm->modname;
                    $record->timemodified   = time();

                    // Get user grade.
                    $record = transcripts_repository::get_transcripts_module_grades(
                        ['userid' => $user->id, 'courseid' => $course->id, 'modname' => $cm->modname, 'instance' => $cm->instance],
                        $record
                    );

                    $record->finalgrade     = $finalgrade;
                    $record->gradeitemid    = $item->id;
                    $record->gradeid        = $grade->id;

                    foreach ($recordscourses as $recordcourse) {

                        $record->userenrolid        = $recordcourse->userenrolid;
                        $transcriptsmodules        = $transcript->get_records(
                            ['userid' => $user->id, 'userenrolid' => $recordcourse->userenrolid, 'cmid' => $cm->id]
                        );
                        if (count($transcriptsmodules)) {
                            foreach ($transcriptsmodules as $recordmodule) {
                                $record->id         = $recordmodule->id;

                                $transcript->set_record($record);
                                $transcript->update();
                            }
                        } else {
                            $record->status         = $transcript::STATUS_INPROGRESS;
                            $record->timecreated    = time();

                            $transcript->set_record($record);
                            $transcript->insert();
                        }

                    }
                }

            }
        }

    }

    /**
     * Observer for role_assigned event.
     *
     * @param \core\event\role_assigned $event
     * @return void
     */
    public static function transcripts_role_assigned(\core\event\role_assigned $event) {
        global $DB;

        if (!get_config('local_intelliboard', 'enable_transcripts')) {
            return;
        }

        $context = \context::instance_by_id($event->contextid, MUST_EXIST);

        if ($context->contextlevel != CONTEXT_COURSE) {
            return;
        }

        $course = $DB->get_record('course', array('id' => $context->instanceid));
        $user   = $DB->get_record('user', array('id' => $event->relateduserid));

        $rolessql = DBHelper::get_group_concat('roleid', ',');
        $roles  = $DB->get_record_sql(
        "SELECT $rolessql as rolesids
               FROM {role_assignments}
              WHERE contextid = :contextid
                AND userid = :userid",
            ['contextid' => $context->id, 'userid' => $user->id]
        );

        // Update course transcript.
        $transcriptscourses = new transcripts_courses();
        $transcriptscourses->update_transcripts([
            'user'          => $user,
            'course'        => $course,
            'rolesids'      => (!empty($roles->rolesids)) ? $roles->rolesids : ''
        ]);

    }

    /**
     * Observer for group_member_added event.
     *
     * @param \core\event\group_member_added $event
     * @return void
     */
    public static function transcripts_group_member_added(\core\event\group_member_added $event) {
        global $DB;

        if (!get_config('local_intelliboard', 'enable_transcripts')) {
            return;
        }

        $context = \context::instance_by_id($event->contextid, MUST_EXIST);

        if ($context->contextlevel != CONTEXT_COURSE) {
            return;
        }

        $course = $DB->get_record('course', array('id' => $context->instanceid));
        $user = $DB->get_record('user', array('id' => $event->relateduserid));

        $groupssql = DBHelper::get_group_concat('g.id', ',');
        $groups     = $DB->get_record_sql(" SELECT $groupssql as groupsids
                                                  FROM {groups_members} gm
                                                  JOIN {groups} g ON gm.groupid = g.id
                                                 WHERE g.courseid = :courseid
                                                   AND gm.userid = :userid",
            ['courseid' => $course->id, 'userid' => $user->id]
        );

        // Update course transcript.
        $transcriptscourses = new transcripts_courses();
        $transcriptscourses->update_transcripts([
            'user'          => $user,
            'course'        => $course,
            'groupsids'     => (!empty($groups->groupsids)) ? $groups->groupsids : ''
        ]);

    }

}
