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

        // Define field firstname to be added to local_iomad_track.
        $table = new xmldb_table('local_iomad_track');
        $field = new xmldb_field('firstname', XMLDB_TYPE_CHAR, '100', null, null, null, null, 'userid');

        // Conditionally launch add field firstname.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        // Define field lastname to be added to local_iomad_track.
        $table = new xmldb_table('local_iomad_track');
        $field = new xmldb_field('lastname', XMLDB_TYPE_CHAR, '100', null, null, null, null, 'firstname');

        // Conditionally launch add field lastname.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Save the user names.
        $litusers = $DB->get_records_sql("SELECT distinct userid from {local_iomad_track}");
        foreach ($litusers as $lituser) {
            if ($user = $DB->get_record('user', array('id' => $lituser->userid))) {
                $DB->set_field('local_iomad_track', 'firstname', $user->firstname, array('userid' => $lituser->userid));
                $DB->set_field('local_iomad_track', 'lastname', $user->lastname, array('userid' => $lituser->userid));
            }
        }


        // Define field companyid to be added to local_iomad_track.
        $table = new xmldb_table('local_iomad_track');
        $field = new xmldb_field('companyid', XMLDB_TYPE_INTEGER, '20', null, null, null, null, 'finalscore');

        // Conditionally launch add field companyid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field companyname to be added to local_iomad_track.
        $table = new xmldb_table('local_iomad_track');
        $field = new xmldb_field('companyname', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'companyid');

        // Conditionally launch add field companyname.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field departmentid to be added to local_iomad_track.
        $table = new xmldb_table('local_iomad_track');
        $field = new xmldb_field('departmentid', XMLDB_TYPE_INTEGER, '20', null, null, null, null, 'companyname');

        // Conditionally launch add field departmentid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field departmentname to be added to local_iomad_track.
        $table = new xmldb_table('local_iomad_track');
        $field = new xmldb_field('departmentname', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'departmentid');

        // Conditionally launch add field departmentname.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Get the user company and departments.
        $users = $DB->get_records_sql("SELECT DISTINCT userid FROM {local_iomad_track}");
        foreach ($users as $user) {
            if ($usercompany = company::by_userid($user->userid)) {
                if ($usercompanyrec = $DB->get_record('company', array('id' => $usercompany->id))) {
                    $department = $DB->get_record_sql("SELECT d.* from {department} d JOIN {company_users} cu ON (d.id = cu.departmentid) WHERE cu.companyid = :companyid AND cu.userid = :userid", array('companyid' => $usercompanyrec->id, 'userid' => $user->userid));
                    $DB->set_field('local_iomad_track', 'companyid', $usercompanyrec->id, array('userid' => $user->userid));
                    $DB->set_field('local_iomad_track', 'companyname', $usercompanyrec->name, array('userid' => $user->userid));
                    $DB->set_field('local_iomad_track', 'departmentid', $department->id, array('userid' => $user->userid));
                    $DB->set_field('local_iomad_track', 'departmentname', $department->name, array('userid' => $user->userid));
                }
            }
        }

        // Define field licenseid to be added to local_iomad_track.
        $table = new xmldb_table('local_iomad_track');
        $field = new xmldb_field('licenseid', XMLDB_TYPE_INTEGER, '20', null, null, null, '0', 'departmentname');

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

        // Deal with licenses.
        $liccourses = $DB->get_records('iomad_courses', array('licensed' => 1));
        foreach ($liccourses as $liccourse) {
            $lictracks = $DB->get_records('local_iomad_track', array('courseid' => $liccourse->courseid));
            foreach ($lictracks as $lictrack) {
                $licenserecs = $DB->get_records_sql("SELECT * FROM {companylicense_users}
                                                     WHERE userid = :userid AND licensecourseid = :licensecourseid AND issuedate < :issuedate
                                                     AND licensid IN (SELECT id from {companylicense} WHERE companyid = :companyid)
                                                     ORDER BY issuedate DESC",
                                                     array('licensecourseid' => $lictrack->courseid, 'userid' => $lictrack->userid, 'companyid' => $usercompanyrec->id, 'issuedate' => $lictrack->timecompleted),
                                                     0,1);
                $licenserec = array_pop($licenserecs);
                if ($license = $DB->get_record('companylicense', array('id' => $licenserec->licenseid))) {
                    $lictrack->licenseid = $license->id;
                    $lictrack->licensename = $license->name;
                    $DB->update_record('local_iomad_track', $lictrack);
                }
            }
        }

        // Iomad_track savepoint reached.
        upgrade_plugin_savepoint(true, 2018081900, 'local', 'iomad_track');
    }

    return $result;
}
