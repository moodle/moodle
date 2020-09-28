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

defined('MOODLE_INTERNAL') || die();

function xmldb_local_iomad_track_upgrade($oldversion) {
    global $CFG, $DB;

    $result = true;
    $dbman = $DB->get_manager();

    require_once($CFG->dirroot.'/local/iomad_track/lib.php');

    if ($oldversion < 2017080800) {

        // Changing type of field finalscore on table local_iomad_track to number.
        $table = new xmldb_table('local_iomad_track');
        $field = new xmldb_field('finalscore', XMLDB_TYPE_NUMBER, '10, 5', null, XMLDB_NOTNULL, null, '0', 'timestarted');

        // Launch change of type for field finalscore.
        $dbman->change_field_type($table, $field);

        // Iomad_track savepoint reached.
        upgrade_plugin_savepoint(true, 2017080800, 'local', 'iomad_track');
    }

    if ($oldversion < 2018081900) {
mtrace("starting " . time());
        // Define field coursename to be added to local_iomad_track.
        $table = new xmldb_table('local_iomad_track');
        $field = new xmldb_field('coursename', XMLDB_TYPE_CHAR, '254', null, null, null, null, 'courseid');

        // Conditionally launch add field coursename.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Save the coursename.
        $litcourses = $DB->get_records_sql("SELECT distinct courseid from {local_iomad_track}");
        foreach ($litcourses as $litcourse) {
            if ($course = $DB->get_record('course', array('id' => $litcourse->courseid))) {
                $DB->set_field('local_iomad_track', 'coursename', $course->fullname, array('courseid' => $litcourse->courseid));
            }
        }
mtrace("coursename end " . time());

        // Define field companyid to be added to local_iomad_track.
        $table = new xmldb_table('local_iomad_track');
        $field = new xmldb_field('companyid', XMLDB_TYPE_INTEGER, '20', null, null, null, null, 'finalscore');

        // Conditionally launch add field companyid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

mtrace("companies/departments start " . time());

        // Get the user company and departments.
        $users = $DB->get_records_sql("SELECT DISTINCT userid FROM {local_iomad_track}");
        foreach ($users as $user) {
            if ($usercompany = company::by_userid($user->userid)) {
                if ($usercompanyrec = $DB->get_record('company', array('id' => $usercompany->id))) {
                    $DB->set_field('local_iomad_track', 'companyid', $usercompanyrec->id, array('userid' => $user->userid));
                }
            }
        }

mtrace("companies/departments end " . time());

        // Define field licenseid to be added to local_iomad_track.
        $table = new xmldb_table('local_iomad_track');
        $field = new xmldb_field('licenseid', XMLDB_TYPE_INTEGER, '20', null, null, null, '0', 'companyid');

        // Conditionally launch add field licenseid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field licensename to be added to local_iomad_track.
        $table = new xmldb_table('local_iomad_track');
        $field = new xmldb_field('licensename', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'licenseid');

        // Conditionally launch add field licensename.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

mtrace("licenses start " . time());

        // Deal with licenses.
        $liccourses = $DB->get_records('iomad_courses', array('licensed' => 1));
        foreach ($liccourses as $liccourse) {
            $lictracks = $DB->get_records('local_iomad_track', array('courseid' => $liccourse->courseid));
            foreach ($lictracks as $lictrack) {
                $licenserecs = $DB->get_records_sql("SELECT clu.*,cl.name FROM {companylicense_users} clu JOIN {companylicense} cl ON (clu.licenseid = cl.id)
                                                     WHERE clu.userid = :userid AND clu.licensecourseid = :licensecourseid AND clu.issuedate < :issuedate
                                                     ORDER BY clu.issuedate DESC",
                                                     array('licensecourseid' => $lictrack->courseid, 'userid' => $lictrack->userid, 'issuedate' => $lictrack->timecompleted),
                                                     0,1);
                $licenserec = array_pop($licenserecs);
                if (!empty($licenserec->licenseid)) {
                    $lictrack->licenseid = $licenserec->licenseid;
                    $lictrack->licensename = $licenserec->name;
                } else {
                    $lictrack->licenseid = 0;
                    $lictrack->licensename = 'HISTORIC';
                }
                $DB->update_record('local_iomad_track', $lictrack);
            }
        }

mtrace("licenses end " . time());

        // Iomad_track savepoint reached.
        upgrade_plugin_savepoint(true, 2018081900, 'local', 'iomad_track');
    }

    if ($oldversion < 2019012100) {

mtrace("starting " . time());
        // Define field userid to be dropped from local_iomad_track.
        $table = new xmldb_table('local_iomad_track');
        $field = new xmldb_field('firstname');

        // Conditionally launch drop field userid.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        $field = new xmldb_field('lastname');

        // Conditionally launch drop field lastname.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        $field = new xmldb_field('companyname');

        // Conditionally launch drop field companyname.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        $field = new xmldb_field('departmentname');

        // Conditionally launch drop field departmentname.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        $field = new xmldb_field('departmentid');

        // Conditionally launch drop field departmentid.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define field companyid to be added to local_iomad_track.
        $field = new xmldb_field('companyid', XMLDB_TYPE_INTEGER, '20', null, null, null, null, 'finalscore');

        // Conditionally launch add field companyid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('licensename', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'licenseid');

        // Conditionally launch add field licensename.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('licenseallocated', XMLDB_TYPE_INTEGER, '20', null, null, null, null, 'licensename');

        // Conditionally launch add field licenseallocated.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field modifiedtime to be added to local_iomad_track.
        $table = new xmldb_table('local_iomad_track');
        $field = new xmldb_field('modifiedtime', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, '0', 'licenseallocated');

        // Conditionally launch add field modifiedtime.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Changing nullability of field timecompleted on table local_iomad_track to null.
        $table = new xmldb_table('local_iomad_track');
        $field = new xmldb_field('timecompleted', XMLDB_TYPE_INTEGER, '11', null, null, null, null, 'userid');

        // Launch change of nullability for field timecompleted.
        $dbman->change_field_notnull($table, $field);

        // Changing nullability of field timeenrolled on table local_iomad_track to null.
        $table = new xmldb_table('local_iomad_track');
        $field = new xmldb_field('timeenrolled', XMLDB_TYPE_INTEGER, '11', null, null, null, null, 'timecompleted');

        // Launch change of nullability for field timeenrolled.
        $dbman->change_field_notnull($table, $field);

        // Changing nullability of field timestarted on table local_iomad_track to null.
        $table = new xmldb_table('local_iomad_track');
        $field = new xmldb_field('timestarted', XMLDB_TYPE_INTEGER, '11', null, null, null, null, 'timeenrolled');

        // Launch change of nullability for field timestarted.
        $dbman->change_field_notnull($table, $field);

        // Define index companycourse (not unique) to be added to local_iomad_track.
        $table = new xmldb_table('local_iomad_track');
        $index = new xmldb_index('companycourse', XMLDB_INDEX_NOTUNIQUE, ['companyid', 'courseid']);

        // Conditionally launch add index companycourse.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Define index usercourseenrol (not unique) to be added to local_iomad_track.
        $table = new xmldb_table('local_iomad_track');
        $index = new xmldb_index('usercourseenrol', XMLDB_INDEX_NOTUNIQUE, ['userid', 'courseid', 'timeenrolled']);

        // Conditionally launch add index usercourseenrol.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Define index usercourselicense (not unique) to be added to local_iomad_track.
        $table = new xmldb_table('local_iomad_track');
        $index = new xmldb_index('usercourselicense', XMLDB_INDEX_NOTUNIQUE, ['userid', 'courseid', 'licenseid', 'licenseallocated']);

        // Conditionally launch add index usercourselicense.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

mtrace("current start " . time());
        // Get the timestamp for the license which have been allocated for already tracked courses.
        $current = $DB->get_records_sql("SELECT clu.id, lit.id AS litid,clu.issuedate
                                         FROM {local_iomad_track} lit
                                         JOIN {companylicense_users} clu ON (lit.courseid = clu.licensecourseid AND lit.userid = clu.userid AND lit.licenseid = clu.licenseid)
                                         WHERE lit.timecompleted IS NOT NULL
                                         AND lit.licenseid != 0
                                         AND lit.timeenrolled > clu.issuedate
                                         GROUP BY clu.id");
        foreach ($current as $cur) {
            $DB->set_field('local_iomad_track', 'licenseallocated', $cur->issuedate, array('id' => $cur->litid));
            $DB->set_field('local_iomad_track', 'modifiedtime', time(), array('id' => $cur->litid));
        }

mtrace("current end " . time());

        // Get the timestamp historic values.
        if ($nolics = $DB->get_records('local_iomad_track', array('licenseid' => 0))) {
            foreach ($nolics as $nolic) {
                $DB->set_field('local_iomad_track', 'licenseallocated', $nolic->timeenrolled, array('id' => $nolic->id));
            $DB->set_field('local_iomad_track', 'modifiedtime', time(), array('id' => $nolic->id));
            }
        }

mtrace("rest start " . time());

        $rest = $DB->get_records_sql("SELECT clu.*,cl.name AS licensename, cl.companyid, c.fullname AS coursename FROM {companylicense_users} clu
                                      JOIN {companylicense} cl ON (clu.licenseid = cl.id)
                                      JOIN {course} c ON (clu.licensecourseid = c.id)
                                      LEFT JOIN {local_iomad_track} lit ON (clu.licensecourseid = lit.courseid AND clu.userid = lit.userid AND clu.issuedate = lit.licenseallocated)
                                      WHERE lit.id IS NULL");
        foreach ($rest as $rec) {
            if ($completionrec = $DB->get_record('course_completions', array('course' => $rec->licensecourseid, 'userid' => $rec->userid))) {
                $timeenrolled = $completionrec->timeenrolled;
                $timestarted = $completionrec->timestarted;
                $timecompleted = $completionrec->timecompleted;
            } else {
                $timeenrolled = null;
                $timestarted = null;
                $timecompleted = null;
            }

            // Get the final grade for the course.
            $finalgrade = 0;
            if ($graderec = $DB->get_record_sql("SELECT gg.* FROM {grade_grades} gg
                                             JOIN {grade_items} gi ON (gg.itemid = gi.id
                                                                       AND gi.itemtype = 'course'
                                                                       AND gi.courseid = :courseid)
                                             WHERE gg.userid = :userid", array('courseid' => $rec->licensecourseid,
                                                                               'userid' => $rec->userid))) {
                if (!empty($graderec->finalgrade)) {
                    $finalgrade = $graderec->finalgrade;
                }
            }

            $trackrecord = array('courseid' => $rec->licensecourseid,
                                 'userid' => $rec->userid,
                                 'coursename' => $rec->coursename,
                                 'timeenrolled' => $timeenrolled,
                                 'timestarted' => $timestarted,
                                 'timecompleted' => $timecompleted,
                                 'companyid' => $rec->companyid,
                                 'licenseid' => $rec->licenseid,
                                 'licensename' => $rec->licensename,
                                 'licenseallocated' => $rec->issuedate,
                                 'finalscore' => $finalgrade,
                                 'modifiedtime' => time());
            $DB->insert_record('local_iomad_track', $trackrecord);
        }

mtrace("reset end " . time());

        // Deal with enrolments.
        $enrolments = $DB->get_records_sql("SELECT cc.*,c.fullname AS coursename FROM {course_completions} cc
                                            JOIN {course} c ON (cc.course = c.id)
                                            LEFT JOIN {local_iomad_track} lit ON (cc.course = lit.courseid AND cc.userid = lit.userid)
                                            WHERE lit.id IS NULL");
        foreach ($enrolments as $rec) {
            // Get the user's company.
            if ($companies = $DB->get_records_sql("SELECT cu.* FROM {company_users} cu
                                                  JOIN {company_course} cc on (cu.companyid = cc.companyid)
                                                  WHERE cu.userid = :userid
                                                  AND cc.courseid = :courseid
                                                  ORDER BY cu.id DESC",
                                                  array('userid' => $rec->userid,
                                                        'courseid' => $rec->course))) {

                $company = array_shift($companies);
                $entry = array('userid' => $rec->userid,
                               'courseid' => $rec->course,
                               'coursename' => $rec->coursename,
                               'companyid' => $company->companyid,
                               'timeenrolled' => $rec->timeenrolled,
                               'timestarted' => $rec->timestarted,
                               'modifiedtime' => time()
                               );
                $DB->insert_record('local_iomad_track', $entry);
            }
        }
mtrace("enrol end " . time());

        // Iomad_track savepoint reached.
        upgrade_plugin_savepoint(true, 2019012100, 'local', 'iomad_track');
    }

    if ($oldversion < 2019022700) {

        $noscores = $DB->get_records_sql("SELECT * from {local_iomad_track}
                                          WHERE finalscore = 0 
                                          AND timeenrolled IS NOT NULL");
        foreach ($noscores as $rec) {
            // Get the final grade for the course.
            if ($graderec = $DB->get_record_sql("SELECT gg.* FROM {grade_grades} gg
                                             JOIN {grade_items} gi ON (gg.itemid = gi.id
                                                                       AND gi.itemtype = 'course'
                                                                       AND gi.courseid = :courseid)
                                             WHERE gg.userid = :userid", array('courseid' => $rec->courseid,
                                                                               'userid' => $rec->userid))) {

                if (!empty($graderec->finalgrade)) {
                    $DB->set_field('local_iomad_track', 'finalscore', $graderec->finalgrade, array('id' => $rec->id));
                    $DB->set_field('local_iomad_track', 'modifiedtime', time(), array('id' => $rec->id));
                }
            } else {
                $finalgrade = 0;
            }
        }

        // Iomad_track savepoint reached.
        upgrade_plugin_savepoint(true, 2019022700, 'local', 'iomad_track');
    }

    if ($oldversion < 2019070200) {

        // Define field expirysent to be added to local_iomad_track.
        $table = new xmldb_table('local_iomad_track');
        $field = new xmldb_field('expirysent', XMLDB_TYPE_INTEGER, '20', null, null, null, null, 'licenseallocated');

        // Conditionally launch add field expirysent.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add any which have been sent.
        $expirycourses = $DB->get_records_sql("SELECT * FROM {iomad_courses}
                                               WHERE validlength > 0");
        $timenow = time();
        foreach ($expirycourses as $expirycourse) {
            $expiryemails = $DB->get_records('email', array('templatename' => 'expiry_warn_user','courseid' => $expirycourse->courseid));
            foreach ($expiryemails as $expiryemail) {
                // Get the track records
                $trackrecords = $DB->get_records_sql("SELECT * FROM {local_iomad_track}
                                                      WHERE userid = :userid
                                                      AND courseid = :courseid
                                                      AND timecompleted IS NOT NULL
                                                      AND timecompleted > :modifiedtime
                                                      AND expirysent IS NULL",
                                                      array('userid' => $expiryemail->userid,
                                                            'courseid' => $expiryemail->courseid,
                                                            'modifiedtime' => $expiryemail - 3600));
                foreach ($trackrecords as $trackrecord) {
                    if ($trackrecord->timecompleted + ($expirycourse->validlength - $expirycourse->warnafter) * 24 * 60 * 60 < $timenow) {
                        // continue as we havent reached the date to send yet.
                        continue;
                    } else {
                        $trackrecord->expirysent = $expiryemail->sent;
                        $trackrecord->modifiedtime = $timenow;
                        $DB->update_record('local_iomad_track', $trackrecord);
                    }
                }
            }
        }

        // Iomad_track savepoint reached.
        upgrade_plugin_savepoint(true, 2019070200, 'local', 'iomad_track');
    }

    if ($oldversion < 2019090800) {

        // Define field notstartedstop to be added to local_iomad_track.
        $table = new xmldb_table('local_iomad_track');
        $field = new xmldb_field('notstartedstop', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'expirysent');

        // Conditionally launch add field notstartedstop.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field completedstop to be added to local_iomad_track.
        $table = new xmldb_table('local_iomad_track');
        $field = new xmldb_field('completedstop', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'notstartedstop');

        // Conditionally launch add field completedstop.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field expiredstop to be added to local_iomad_track.
        $table = new xmldb_table('local_iomad_track');
        $field = new xmldb_field('expiredstop', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'completedstop');

        // Conditionally launch add field expiredstop.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Iomad_track savepoint reached.
        upgrade_plugin_savepoint(true, 2019090800, 'local', 'iomad_track');
    }

    if ($oldversion < 2020010200) {

        // Get any license allocationw which have an issuedate of null.
        $brokenrecs = $DB->get_records_sql("SELECT * FROM {local_iomad_track}
                                            WHERE licensename IS NOT NULL
                                            AND licenseallocated IS NULL");
        foreach ($brokenrecs as $brokenrec) {
            // This is fromt the external functions so can set it to the enrolment date.
            $brokenrec->licenseallocated = $brokenrec->timeenrolled;
            $DB->update_record('local_iomad_track', $brokenrec);
        }

        // Iomad_track savepoint reached.
        upgrade_plugin_savepoint(true, 2020010200, 'local', 'iomad_track');
    }

    if ($oldversion < 2020010201) {

        require_once(dirname(__FILE__) . '/../classes/task/fixtracklicensetask.php');

        // Fire off the adhoc task to fix the license records.
        $task = new local_iomad_track\task\fixtracklicensetask();
        \core\task\manager::queue_adhoc_task($task, true);

        // Iomad_track savepoint reached.
        upgrade_plugin_savepoint(true, 2020010201, 'local', 'iomad_track');
    }

    if ($oldversion < 2020042400) {

        // Define field timeexpires to be added to local_iomad_track.
        $table = new xmldb_table('local_iomad_track');
        $field = new xmldb_field('timeexpires', XMLDB_TYPE_INTEGER, '11', null, null, null, null, 'timestarted');

        // Conditionally launch add field timeexpires.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define index usercourseexpire (not unique) to be added to local_iomad_track.
        $table = new xmldb_table('local_iomad_track');
        $index = new xmldb_index('usercourseexpire', XMLDB_INDEX_NOTUNIQUE, ['userid', 'courseid', 'timeexpires']);

        // Conditionally launch add index usercourseexpire.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Define index usercoursecomplete (not unique) to be added to local_iomad_track.
        $table = new xmldb_table('local_iomad_track');
        $index = new xmldb_index('usercoursecomplete', XMLDB_INDEX_NOTUNIQUE, ['userid', 'courseid', 'timecompleted']);

        // Conditionally launch add index usercoursecomplete.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }


        // Calculate the timeexpired for all users.
        // Get the courses where there is a expired value.
        $expirycourses = $DB->get_records_sql("SELECT courseid,validlength FROM {iomad_courses}
                                               WHERE validlength > 0");
        foreach ($expirycourses as $expirycourse) {
            $offset = $expirycourse->validlength * 24 * 60 * 60;
            $DB->execute("UPDATE {local_iomad_track}
                          SET timeexpires = timecompleted + :offset
                          WHERE courseid = :courseid
                          AND timecompleted > 0",
                          array('courseid' => $expirycourse->courseid,
                                'offset' => $offset));
        }

        // Iomad_track savepoint reached.
        upgrade_plugin_savepoint(true, 2020042400, 'local', 'iomad_track');
    }

    if ($oldversion < 2020042900) {

        require_once(dirname(__FILE__) . '/../lib.php');
        require_once(dirname(__FILE__) . '/install.php');

        // Need to fix records which are broken due to error in enrol/license code.
        $records = $DB->get_records_sql("SELECT lit.*,clu.licenseid AS reallicenseid, cc.timeenrolled AS cctimeentolled, cc.timestarted AS cctimestarted, cc.timecompleted AS cctimecompleted
                                         FROM {local_iomad_track} lit
                                         JOIN {companylicense_users} clu
                                         ON (lit.userid = clu.userid AND lit.courseid = clu.licensecourseid AND lit.licenseallocated = clu.issuedate)
                                         JOIN {course_completions} cc
                                         ON (lit.courseid = cc.course AND lit.userid = cc.userid)
                                         WHERE
                                         lit.timecompleted IS NULL
                                         and cc.timecompleted > 0
                                         and clu.isusing = 1");
        foreach ($records as $record) {
            if (empty($record->timeenrolled)) {
                if (!empty($record->cctimeenrolled)) {
                    $record->timeenrolled = $record->cctimeenrolled;
                } else {
                    // Need to get it from the enrolment record.
                    $enrolrec = $DB->get_record_sql("SELECT ue.* FROM {user_enrolments} ue
                                                     JOIN {enrol} e ON (ue.enrolid = e.id)
                                                     WHERE ue.userid = :userid
                                                     AND e.courseid = :courseid
                                                     AND e.status = 0",
                                                     array('userid' => $record->userid,
                                                           'courseid' => $record->courseid));
                    $record->timeenrolled = $enrolrec->starttime;
                }
            }
            $record->timestarted = $record->cctimestarted;
            if (empty($record->timecompleted) && !empty($record->cctimecompleted)) {
                $record->timecompleted = $record->cctimecompleted;
                $completed = true;
            } else {
                $completed = false;
            }
            if (empty($record->licensename) || $record->licenseid != $record->reallicenseid) {
                $record->licenseid = $record->reallicenseid;
                if ($licenserec = $DB->get_record('companylicense', array('id' => $record->reallicenseid))) {
                    $record->licensename = $licenserec->name;
                }
            }
            $DB->update_record('local_iomad_track', $record);
            if ($complete) {
                // Generate the certificates.
                local_iomad_track_delete_entry($record->id, false);
                xmldb_local_iomad_track_record_certificates($record->courseid, $record->userid, $record->id);
            }
        }

        // Iomad_track savepoint reached.
        upgrade_plugin_savepoint(true, 2020042900, 'local', 'iomad_track');
    }

    if ($oldversion < 2020051900) {

        // Calculate the timeexpired for all users.
        // Get the courses where there is a expired value.
        $expirycourses = $DB->get_records_sql("SELECT courseid,validlength FROM {iomad_courses}
                                               WHERE validlength > 0");
        foreach ($expirycourses as $expirycourse) {
            $offset = $expirycourse->validlength * 24 * 60 * 60;
            $DB->execute("UPDATE {local_iomad_track}
                          SET timeexpires = timecompleted + :offset
                          WHERE courseid = :courseid
                          AND timecompleted > 0",
                          array('courseid' => $expirycourse->courseid,
                                'offset' => $offset));
        }

        // Iomad_track savepoint reached.
        upgrade_plugin_savepoint(true, 2020051900, 'local', 'iomad_track');
    }

    if ($oldversion < 2020062900) {

        // Remove the certificates which have been recorded erroneously / with no timecompleted
        $brokentracks = $DB->get_records_sql("SELECT lit.* FROM {local_iomad_track} lit
                                              JOIN {local_iomad_track_certs} litc
                                              ON (lit.id = litc.trackid)
                                              WHERE lit.timecompleted IS NULL");
        foreach ($brokentracks as $brokentrack) {
            // Remove the file
            local_iomad_track_delete_entry($brokentrack->id);
        }

        // Iomad_track savepoint reached.
        upgrade_plugin_savepoint(true, 2020062900, 'local', 'iomad_track');
    }

    if ($oldversion < 2020092800) {

        // Define field coursecleared to be added to local_iomad_track.
        $table = new xmldb_table('local_iomad_track');
        $field = new xmldb_field('coursecleared', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'expiredstop');

        // Conditionally launch add field coursecleared.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Deal with all of the previous data.
        require_once(dirname(__FILE__) . '/../classes/task/fixcourseclearedtask.php');

        // Fire off the adhoc task to populate this new field correctly.
        $task = new local_iomad_track\task\fixcourseclearedtask();
        \core\task\manager::queue_adhoc_task($task, true);

        // Iomad_track savepoint reached.
        upgrade_plugin_savepoint(true, 2020092800, 'local', 'iomad_track');
    }

   return $result;
}
