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

class userlicallocrep {
    // Find completion data. $courseid=0 means all courses
    // for that company.
    public static function get_completion( $userid, $courseid, $showhistoric=false ) {
        global $DB;

        // Going to build an array for the data.
        $data = array();

        // Count the three statii for the graph.
        $notstarted = 0;
        $inprogress = 0;
        $completed = 0;

        // Get completion data for course.
        // Get course object.
        if (!$course = $DB->get_record('course', array('id' => $courseid))) {
            error( 'unable to find course record' );
        }
        $datum = new stdclass();
        $datum->coursename = $course->fullname;

        // Instantiate completion info thingy.
        $info = new completion_info( $course );

        // Set up the temporary table for all the completion information to go into.
        $tempcomptablename = 'tmp_ccomp_comp_'.uniqid();

        // Populate the temporary completion table.
        list($compdbman, $comptable) = self::populate_temporary_completion($tempcomptablename, $userid, $courseid, $showhistoric);

        // Get gradebook details.
        $gbsql = "select gg.finalgrade as result from {grade_grades} gg, {grade_items} gi
                  WHERE gi.courseid=$courseid AND gi.itemtype='course' AND gg.userid=$userid
                  AND gi.id=gg.itemid";
        if (!$gradeinfo = $DB->get_record_sql($gbsql)) {
            $gradeinfo = new stdclass();
            $gradeinfo->result = null;
        }

        // If completion is not enabled on the course
        // there's no point carrying on.
        if (!$info->is_enabled()) {
            $datum->enabled = false;
            $data[ $courseid ] = $datum;
            // Drop the temp table.
            $compdbman->drop_table($comptable);
            return false;
        } else {
            $datum->enabled = true;
        }

        // Get criteria for coursed.
        // This is an array of tracked activities (only tracked ones).
        $criteria = $info->get_criteria();

        // Number of tracked activities to complete.
        $trackedcount = count( $criteria );
        $datum->trackedcount = $trackedcount;
        $datum->completion = new stdclass();

        $u = new stdclass();
        $data[$courseid] = new stdclass();

        // Iterate over users to get info.
        // Find user's completion info for this course.
        if ($completionsinfo = $DB->get_records_sql("SELECT DISTINCT id as uniqueid, userid,courseid,timeenrolled,timestarted,timecompleted,finalscore
                                                     FROM {".$tempcomptablename."}
                                                     ORDER BY timeenrolled DESC")) {

            foreach ($completionsinfo as $testcompletioninfo) {
                $u = new stdclass();
                // get the first occurrance of this info.
                if ($completionsinfo = $DB->get_records_sql("SELECT * FROM {".$tempcomptablename."}
                                                            WHERE userid = :userid
                                                            AND courseid = :courseid
                                                            AND timeenrolled = :timeenrolled
                                                            AND timestarted = :timestarted
                                                            AND timecompleted = :timecompleted",
                                                            (array) $testcompletioninfo, 0, 1)) {
                    $completioninfo = array_shift($completionsinfo);

                    $u->certsource = null;
                    if (!empty($completioninfo->timeenrolled)) {
                        $u->timeenrolled = $completioninfo->timeenrolled;
                    } else {
                        $u->timeenrolled = '';
                    }
                    if (!empty($completioninfo->timestarted)) {
                        $u->timestarted = $completioninfo->timestarted;
                        if (!empty($completioninfo->timecompleted)) {
                            $u->timecompleted = $completioninfo->timecompleted;
                            $u->status = 'completed';
                            $u->certsource = $completioninfo->certsource;
                            ++$completed;
                        } else {
                            $u->timecompleted = 0;
                            $u->status = 'inprogress';
                            ++$inprogress;
                        }

                    } else {
                        $u->timestarted = 0;
                        $u->status = 'notstarted';
                        ++$notstarted;
                    }
                    if (!empty($completioninfo->finalscore)) {
                        $u->result = round($completioninfo->finalscore, 0);
                    } else {
                        $u->result = '';
                    }
                    $datum->completion->{$completioninfo->id} = $u;

                    $data[$courseid] = $datum;
                } else {
                    // Does the user have a license for this course?
                    if ($DB->get_record('companylicense_users', array('licensecourseid' => $courseid, 'userid' => $userid, 'isusing' => 0))) {
                        $u->timeenrolled = 0;
                        $u->timecompleted = 0;
                        $u->timestarted = 0;
                        $u->status = 'notstarted';
                        $u->certsource = null;
                        ++$notstarted;
                        $datum->completion->$courseid = $u;
                    }
                }
            }
            $data[$courseid] = $datum;
        } else {
            // Does the user have a license for this course?
            if ($DB->get_record('companylicense_users', array('licensecourseid' => $courseid, 'userid' => $userid, 'isusing' => 0))) {
                $u->timeenrolled = 0;
                $u->timecompleted = 0;
                $u->timestarted = 0;
                $u->status = 'notstarted';
                $u->certsource = null;
                ++$notstarted;
                $datum->completion->$courseid = $u;
                $data[$courseid] = $datum;
            }
            // user is in the course and hasn't accessed it yet.
            if ($enrolinfo = $DB->get_record_sql("SELECT ue.* FROM {user_enrolments} ue JOIN {enrol} e ON (ue.enrolid = e.id)
                                                   WHERE e.status = 0
                                                   AND e.courseid = :courseid
                                                   AND ue.userid = :userid",
                                                   array('courseid' => $courseid, 'userid' => $userid))) {
                $u->timeenrolled = $enrolinfo->timestart;
                $u->timecompleted = 0;
                $u->timestarted = 0;
                $u->status = 'notstarted';
                $u->certsource = null;
                ++$notstarted;
                $datum->completion->$courseid = $u;
                $data[$courseid] = $datum;
            }
        }
        // Make return object.
        $returnobj = new stdclass();
        $returnobj->data = $data;
        $returnobj->criteria = $criteria;

        // Drop the temp table.
        $compdbman->drop_table($comptable);

        return $returnobj;
    }

    /**
     * Get users into temporary table
     */
    private static function populate_temporary_completion($tempcomptablename, $userid, $courseid=0, $showhistoric=false) {
        global $DB;


        // Create a temporary table to hold the userids.
        $dbman = $DB->get_manager();

        // Define table user to be created.
        $table = new xmldb_table($tempcomptablename);
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->add_field('timeenrolled', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->add_field('timestarted', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->add_field('timecompleted', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->add_field('finalscore', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->add_field('certsource', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->add_field('trackid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        $dbman->create_temp_table($table);

        // Populate it.
        $tempcreatesql = "INSERT INTO {".$tempcomptablename."} (userid, courseid, timeenrolled, timestarted, timecompleted, finalscore, certsource)
                          SELECT cc.userid, cc.course, ue.timestart, cc.timestarted, cc.timecompleted, gg.finalgrade, 0
                          FROM {course_completions} cc LEFT JOIN {grade_grades} gg ON (gg.userid = cc.userid)
                          JOIN {grade_items} gi
                          ON (cc.course = gi.courseid
                          AND gg.itemid = gi.id
                          AND gi.itemtype = 'course')
                          JOIN {user_enrolments} ue ON (cc.userid = ue.userid)
                          JOIN {enrol} e ON (cc.course = e.courseid AND e.id = ue.enrolid)
                          WHERE cc.userid = :userid ";
        if (!empty($courseid)) {
            $tempcreatesql .= " AND cc.course = ".$courseid;
        }
        $DB->execute($tempcreatesql, array('userid' => $userid, 'courseid' => $courseid));

        // Are we also adding in historic data?
        if ($showhistoric) {
        // Populate it.
            $tempcreatesql = "INSERT INTO {".$tempcomptablename."} (userid, courseid, timeenrolled, timestarted, timecompleted, finalscore, certsource)
                              SELECT it.userid, it.courseid, it.timeenrolled, it.timestarted, it.timecompleted, it.finalscore, it.id
                              FROM {local_iomad_track} it
                              WHERE it.userid = :userid";
        if (!empty($courseid)) {
            $tempcreatesql .= " AND it.courseid = :courseid";
        }
            $DB->execute($tempcreatesql, array('userid' => $userid, 'courseid' => $courseid));
        }

        // deal with NULLs as it breaks the code.
        $DB->execute("UPDATE {".$tempcomptablename."} SET timecompleted = 0 WHERE timecompleted is NULL");

        return array($dbman, $table);
    }



    /**
     * 'Delete' user from course
     * @param int userid
     * @param int courseid
     */
    public static function delete_user($userid, $courseid, $action = '') {
        global $DB, $CFG;

        // Remove enrolments
        $plugins = enrol_get_plugins(true);
        $instances = enrol_get_instances($courseid, true);
        foreach ($instances as $instance) {
            $plugin = $plugins[$instance->enrol];
            $plugin->unenrol_user($instance, $userid);
        }

        // Remove completions
        $DB->delete_records('course_completions', array('userid' => $userid, 'course' => $courseid));
        if ($compitems = $DB->get_records('course_completion_criteria', array('course' => $courseid))) {
            foreach ($compitems as $compitem) {
                $DB->delete_records('course_completion_crit_compl', array('userid' => $userid,
                                                                          'criteriaid' => $compitem->id));
            }
        }
        if ($modules = $DB->get_records_sql("SELECT id FROM {course_modules} WHERE course = :course AND completion != 0", array('course' => $courseid))) {
            foreach ($modules as $module) {
                $DB->delete_records('course_modules_completion', array('userid' => $userid, 'coursemoduleid' => $module->id));
            }
        }

        // Remove grades
        if ($items = $DB->get_records('grade_items', array('courseid' => $courseid))) {
            foreach ($items as $item) {
                $DB->delete_records('grade_grades', array('userid' => $userid, 'itemid' => $item->id));
            }
        }

        // Remove quiz entries.
        if ($quizzes = $DB->get_records('quiz', array('course' => $courseid))) {
            // We have quiz(zes) so clear them down.
            foreach ($quizzes as $quiz) {
                $DB->execute("DELETE FROM {quiz_attempts} WHERE quiz=:quiz AND userid = :userid", array('quiz' => $quiz->id, 'userid' => $userid));
                $DB->execute("DELETE FROM {quiz_grades} WHERE quiz=:quiz AND userid = :userid", array('quiz' => $quiz->id, 'userid' => $userid));
                $DB->execute("DELETE FROM {quiz_overrides} WHERE quiz=:quiz AND userid = :userid", array('quiz' => $quiz->id, 'userid' => $userid));
            }
        }

        // Remove certificate info.
        if ($certificates = $DB->get_records('iomadcertificate', array('course' => $courseid))) {
            foreach ($certificates as $certificate) {
                $DB->execute("DELETE FROM {iomadcertificate_issues} WHERE iomadcertificateid = :certid AND userid = :userid", array('certid' => $certificate->id, 'userid' => $userid));
            }
        }

        // Remove feedback info.
        if ($feedbacks = $DB->get_records('feedback', array('course' => $courseid))) {
            foreach ($feedbacks as $feedback) {
                $DB->execute("DELETE FROM {feedback_completed} WHERE feedback = :feedbackid AND userid = :userid", array('feedbackid' => $feedback->id, 'userid' => $userid));
                $DB->execute("DELETE FROM {feedback_completedtmp} WHERE feedback = :feedbackid AND userid = :userid", array('feedbackid' => $feedback->id, 'userid' => $userid));
                $DB->execute("DELETE FROM {feedback_tracking} WHERE feedback = :feedbackid AND userid = :userid", array('feedbackid' => $feedback->id, 'userid' => $userid));
            }
        }

        // Remove lesson info.
        if ($lessons = $DB->get_records('lesson', array('course' => $courseid))) {
            foreach ($lessons as $lesson) {
                $DB->execute("DELETE FROM {lesson_attempts} WHERE lessonid = :lessonid AND userid = :userid", array('lessonid' => $lesson->id, 'userid' => $userid));
                $DB->execute("DELETE FROM {lesson_grades} WHERE lessonid = :lessonid AND userid = :userid", array('lessonid' => $lesson->id, 'userid' => $userid));
                $DB->execute("DELETE FROM {lesson_branch} WHERE lessonid = :lessonid AND userid = :userid", array('lessonid' => $lesson->id, 'userid' => $userid));
                $DB->execute("DELETE FROM {lesson_timer} WHERE lessonid = :lessonid AND userid = :userid", array('lessonid' => $lesson->id, 'userid' => $userid));
            }
        }

        // Fix company licenses
        if ($licenses = $DB->get_records('companylicense_users', array('licensecourseid' => $courseid, 'userid' =>$userid, 'isusing' => 1))) {
            $license = array_pop($licenses);
            if ($action == 'delete') {
                $DB->delete_records('companylicense_users', array('id' => $license->id));
                // Fix the usagecount.
                $licenserecord = $DB->get_record('companylicense', array('id' => $license->licenseid));
                $licenserecord->used = $DB->count_records('companylicense_users', array('licenseid' => $license->licenseid));
                $DB->update_record('companylicense', $licenserecord);
            } else if ($action == 'clear') {
                $newlicense = $license;
                $license->timecompleted = time();
                $DB->update_record('companylicense_users', $license);
                $newlicense->isusing = 0;
                $newlicense->issuedate = time();
                $newlicense->timecompleted = null;
                $licenserecord = $DB->get_record('companylicense', array('id' => $license->licenseid));
                if ($licenserecord->used < $licenserecord->allocation) {
                    $DB->insert_record('companylicense_users', (array) $newlicense);
                    $licenserecord->used = $DB->count_records('companylicense_users', array('licenseid' => $license->licenseid));
                    $DB->update_record('companylicense', $licenserecord);
               }
            }
        }
    }
}
