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

use local_intelliboard\helpers\DBHelper;
use local_intelliboard\repositories\transcripts_repository;
use local_intelliboard\transcripts\transcripts_courses;
use local_intelliboard\transcripts\transcripts_modules;

class transcripts_processor {

    public static function process($params) {
        global $CFG, $DB;

        require_once($CFG->libdir . '/clilib.php');
        require_once($CFG->libdir . '/completionlib.php');
        require_once($CFG->dirroot . '/grade/querylib.php');
        require_once($CFG->libdir . '/gradelib.php');

        $rolessql = DBHelper::get_group_concat('roleid', ',');
        $groupssql = DBHelper::get_group_concat('g.id', ',');
        $sqlparams = ['contextlevel' => CONTEXT_COURSE];

        $sql = "SELECT ue.*, ue.id as ueid, e.enrol, e.courseid, c.fullname, cc.timecompleted, ra.rolesids, gr.groupsids,
                u.email, u.firstname, u.lastname
            FROM {user_enrolments} ue
       LEFT JOIN {user} u ON u.id = ue.userid
       LEFT JOIN {enrol} e ON e.id = ue.enrolid
       LEFT JOIN {course} c ON c.id = e.courseid
       LEFT JOIN {course_completions} cc ON cc.course = c.id AND cc.userid = ue.userid
       LEFT JOIN {context} ctx ON ctx.instanceid = e.courseid AND ctx.contextlevel = :contextlevel
       LEFT JOIN (
                    SELECT contextid, userid, $rolessql as rolesids
                      FROM {role_assignments}
                  GROUP BY userid, contextid
                  ) ra ON ra.contextid = ctx.id AND ra.userid = ue.userid
      LEFT JOIN (
                    SELECT g.courseid, gm.userid, $groupssql as groupsids
                      FROM {groups_members} gm
                      JOIN {groups} g ON gm.groupid = g.id
                  GROUP BY g.courseid, gm.userid
                ) gr ON gr.courseid = e.courseid AND gr.userid = ue.userid
           WHERE u.deleted = 0 AND u.id > 2 AND c.id > 1";

        if (isset($params['ueid']) and $params['ueid'] > 0) {
            $sql .= " AND ue.id > :ueid";
            $sqlparams['ueid'] = $params['ueid'];
        }

        $orderby = " ORDER BY ue.id";
        $userenrolments_rs = $DB->get_recordset_sql($sql . $orderby, $sqlparams, $params['start'], $params['limit']);

        $courserecordscount = 0;
        $modulesrecordscount = 0;

            $coursetranscript = new transcripts_courses();
            $recordcourse = $coursetranscript->record;

            $moduletranscript = new transcripts_modules();
            $recordmodule = $moduletranscript->record;

            foreach ($userenrolments_rs as $enrolment) {

                if (!$coursetranscript->get_record(['userenrolid' => $enrolment->ueid])) {
                    $recordcourse->userid       = $enrolment->userid;
                    $recordcourse->useremail    = $enrolment->email;
                    $recordcourse->firstname    = $enrolment->firstname;
                    $recordcourse->lastname     = $enrolment->lastname;
                    $recordcourse->userenrolid  = $enrolment->ueid;
                    $recordcourse->enrolid      = $enrolment->enrolid;
                    $recordcourse->enroltype    = $enrolment->enrol;
                    $recordcourse->courseid     = $enrolment->courseid;
                    $recordcourse->coursename   = $enrolment->fullname;
                    $recordcourse->enroldate    = ($enrolment->timestart) ? $enrolment->timestart : $enrolment->timecreated;
                    $recordcourse->status       = $coursetranscript::STATUS_INPROGRESS;
                    $recordcourse->rolesids     = $enrolment->rolesids;
                    $recordcourse->groupsids    = $enrolment->groupsids;
                    $recordcourse->timecreated  = time();
                    $recordcourse->timemodified = time();
                    $recordcourse->completeddate = 0;

                    // Set completion status.
                    if ($enrolment->timecompleted) {
                        $recordcourse->status           = $coursetranscript::STATUS_COMPLETED;
                        $recordcourse->completeddate    = ($enrolment->timecompleted > 1) ? $enrolment->timecompleted : 0;
                    }

                    // Set user grades.
                    $recordcourse->formattedgrade    = '';
                    $recordcourse->finalgrade        = 0;
                    $recordcourse->grademax          = 0;
                    $recordcourse->grademin          = 0;
                    $recordcourse->gradeid           = 0;
                    $recordcourse->gradeitemid       = 0;
                    $recordcourse = transcripts_repository::get_transcripts_course_grades(
                        ['userid' => $enrolment->userid, 'courseid' => $enrolment->courseid], $recordcourse
                    );

                    $coursetranscript->set_record($recordcourse);
                    $id = $coursetranscript->insert();
                    mtrace("Inserted course record courseid: $enrolment->courseid, userid: $enrolment->userid");
                    $courserecordscount++;
                }

                // Insert activities records.
                $course = $DB->get_record('course', ['id' => $enrolment->courseid]);
                $modinfo = get_fast_modinfo($course);
                if (!empty($modinfo->get_cms())) {

                    foreach ($modinfo->get_cms() as $cm) {

                        $cmcompletion = $DB->get_record('course_modules_completion',
                            ['coursemoduleid' => $cm->id, 'userid' => $enrolment->userid]
                        );

                        if ($cmcompletion and
                            !$moduletranscript->get_record(
                                ['cmid' => $cm->id, 'userid' => $enrolment->userid, 'userenrolid' => $enrolment->ueid]
                            )
                        ) {

                            $recordmodule->userenrolid  = $enrolment->ueid;
                            $recordmodule->userid       = $enrolment->userid;
                            $recordmodule->courseid     = $course->id;
                            $recordmodule->cmid         = $cm->id;
                            $recordmodule->moduleid     = $cm->instance;
                            $recordmodule->modulename   = $cm->name;
                            $recordmodule->moduletype   = $cm->modname;
                            $recordmodule->startdate    = ($enrolment->timestart) ? $enrolment->timestart : $enrolment->timecreated;
                            $recordmodule->completeddate = 0;
                            $recordmodule->status       = $moduletranscript::STATUS_INPROGRESS;
                            $recordmodule->timecreated  = time();
                            $recordmodule->timemodified = time();

                            // Set module status.
                            switch ($cmcompletion->completionstate) {
                                case COMPLETION_INCOMPLETE:
                                    $recordmodule->completeddate = 0;
                                    $recordmodule->status = $moduletranscript::STATUS_INPROGRESS;
                                case COMPLETION_COMPLETE:
                                    $recordmodule->completeddate = $cmcompletion->timemodified;
                                    $recordmodule->status = $moduletranscript::STATUS_COMPLETED;
                                    break;
                                case COMPLETION_COMPLETE_PASS:
                                    $recordmodule->completeddate = $cmcompletion->timemodified;
                                    $recordmodule->status = $moduletranscript::STATUS_PASSED;
                                    break;
                                case COMPLETION_COMPLETE_FAIL:
                                    $recordmodule->completeddate = $cmcompletion->timemodified;
                                    $recordmodule->status = $moduletranscript::STATUS_FAILED;
                                    break;
                            }

                            // Set user grades.
                            $recordmodule->formattedgrade    = '';
                            $recordmodule->finalgrade        = 0;
                            $recordmodule->grademax          = 0;
                            $recordmodule->grademin          = 0;
                            $recordmodule->gradeid           = 0;
                            $recordmodule->gradeitemid       = 0;
                            $recordmodule = transcripts_repository::get_transcripts_module_grades(
                                ['userid' => $enrolment->userid, 'courseid' => $course->id, 'modname' => $cm->modname, 'instance' => $cm->instance],
                                $recordmodule
                            );

                            $moduletranscript->set_record($recordmodule);
                            $id = $moduletranscript->insert();
                            mtrace("Inserted module record moduleid: $cm->id, userid: $enrolment->userid");
                            $modulesrecordscount++;
                        }
                    }
                }

                set_config('lasttranscriptsrecordid', $enrolment->ueid, 'local_intelliboard');
                mtrace("-------------------------------------");
                mtrace("Created $courserecordscount courses records and $modulesrecordscount modules records");
            }

        $userenrolments_rs->close();


    }
}