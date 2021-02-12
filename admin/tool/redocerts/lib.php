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
 * @package   tool_redocerts
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;


/**
 * Gets whether database transactions are allowed.
 * @global moodle_database $DB
 * @return bool true if transactions are allowed. false otherwise.
 */
function do_redocerts($user = 0, $course = 0, $company = 0, $idnumber = 0, $fromdate = null, $todate = null, $userid = 0, $courseid = 0, $companyid = 0) {
    global $DB, $CFG;

    require_once($CFG->dirroot.'/local/iomad_track/lib.php');

    // Build the SQL.
    $usersql = array();
    if (!empty($user)) {
        $usersql[] = " lit.userid = $user ";
    }
    if (!empty($course)) {
        $usersql[] = " lit.courseid = $course ";
    }
    if (!empty($userid)) {
        $usersql[] = " lit.userid = $userid ";
    }
    if (!empty($courseid)) {
        $usersql[] = " lit.courseid = $courseid ";
    }
    if (!empty($company)) {
        $usersql[] = " lit.userid IN (SELECT userid FROM {company_users} WHERE companyid = $company) ";
    }
    if (!empty($companyid)) {
        $usersql[] = " lit.userid IN (SELECT userid FROM {company_users} WHERE companyid = $companyid) ";
    }
    if (!empty($idnumber)) {
        $usersql[] = " lit.id > $idnumber ";
    }
    if ($fromdate != null) {
        $usersql[] = " lit.timecompleted > $fromdate ";
    }
    if ($todate != null) {
        $usersql[] = " lit.timecompleted < $todate ";
    }
    if (!empty($usersql)) {
        $extrasql = " WHERE lit.timecompleted > 0 AND " . implode("AND", $usersql);
    } else {
        $extrasql = " WHERE lit.timecompleted > 0 ";
    }
    // delete the initial records
    $oldrecords = $DB->get_records_sql("SELECT lit.* from {local_iomad_track} lit JOIN {course} c ON (c.id = lit.courseid) join {user} u on (lit.userid = u.id and u.deleted = 0 ) $extrasql order by lit.id asc");

    $total = count($oldrecords);
    $count = 1;
    $sumstring = new stdclass();
    $sumstring->total = $total;
    foreach ($oldrecords as $track) {
        $sumstring->count = $count;
        $sumstring->id = $track->id;
        echo html_writer::start_tag('p');
        echo format_string('clearingcertificate', 'tool_redocerts', $sumstring);
        echo html_writer::end_tag('br');
        local_iomad_track_delete_entry($track->id);
        echo format_string('recreatingcertificate', 'tool_redocerts');
        if (xmldb_local_iomad_track_record_certificates($track->courseid, $track->userid, $track->id)) {
            echo format_string('success');
        } else {
            echo format_string('falied', 'scorm');
        }
        echo html_writer::end_tag('p');

        $count++;
    }
}
