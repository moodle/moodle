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

    if ($oldversion < 2019912100) {

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

    return $result;
}
