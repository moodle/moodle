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

define('CLI_SCRIPT', true);

require(__DIR__.'/../../../../config.php');
require_once($CFG->libdir.'/clilib.php');
require_once($CFG->libdir.'/adminlib.php');
require($CFG->dirroot . '/local/iomad_track/db/install.php');

$help =
    "Recreate stored certificates.

Options:
--userid=INT                    String to search for.
--courseid=INT                  String to redocerts with.
--companyid=INT                 Shorten result if necessary.
--idnumber=INT                  Perform the redocertsment without confirming.
--fromtimestamp=unixtimestamp   Perform the redocertsment without confirming.
--totimestamp=unixtimestamp     Perform the redocertsment without confirming.
-h, --help                      Print out this help.

Example:
\$ sudo -u www-data /usr/bin/php admin/tool/redocerts/cli/redocerts.php --userid=5 --courseid=3 --fromtimestamp=1541077082
";

list($options, $unrecognized) = cli_get_params(
    array(
        'userid'  => 0,
        'courseid' => 0,
        'companyid' => 0,
        'idnumber' => 0,
        'fromtimestamp' => null,
        'totimestamp' => null,
        'help'    => false,
    ),
    array(
        'h' => 'help',
    )
);

if ($options['help']) {
    echo $help;
    exit(0);
}

foreach ($options as $key => $value) {
    $$key = $value;
}

// Build the SQL.
$usersql = array();
if (!empty($userid)) {
    $usersql[] = " lit.userid = $userid ";
}
if (!empty($courseid)) {
    $usersql[] = " lit.courseid = $courseid ";
}
if (!empty($companyid)) {
    $usersql[] = " lit.userid IN (SELECT userid FROM {company_users} WHERE companyid = $companyid) ";
}
if (!empty($idnumber)) {
    $usersql[] = " lit.id > $idnumber ";
}
if ($fromtimestamp != null) {
    $usersql[] = " lit.timecompleted > $fromtimestamp ";
}
if ($totimestamp != null) {
    $usersql[] = " lit.timecompleted < $totimestamp ";
}
if (!empty($usersql)) {
    $extrasql = " WHERE " . implode("AND", $usersql);
} else {
    $extrasql = "";
}
// delete the initial records
$oldrecords = $DB->get_records_sql("SELECT lit.* from {local_iomad_track} lit JOIN {course} c ON (c.id = lit.courseid) join {user} u on (lit.userid = u.id and u.deleted = 0 )$extrasql order by lit.id asc");

$total = count($oldrecords);
$count = 1;
foreach ($oldrecords as $track) {
mtrace ("clearing id $track->id - " . $count . " of " . $total);
    if ($cert = $DB->get_record('local_iomad_track_certs', array('trackid' => $track->id))) {
mtrace("deleting track record id $track->id");
        $DB->delete_records('local_iomad_track_certs', array('id' => $cert->id));
    }
    if ($file = $DB->get_record_sql("SELECT * FROM {files} WHERE component= :component and itemid = :itemid and filename != '.'", array('component' => 'local_iomad_track', 'itemid' => $track->id))) {
        $filedir1 = substr($file->contenthash,0,2);
        $filedir2 = substr($file->contenthash,2,2);
        $filepath = $CFG->dataroot . '/filedir/' . $filedir1 . '/' . $filedir2 . '/' . $file->contenthash;
mtrace("removing filename $filepath");
        unlink($filepath);
    }
    $DB->delete_records('files', array('itemid' => $track->id, 'component' => 'local_iomad_track'));
mtrace ("adding Certificate");
    xmldb_local_iomad_track_record_certificates($track->courseid, $track->userid, $track->id);

$count++;
}

