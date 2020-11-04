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

require_once(dirname(__FILE__).'/../../config.php');
require_once($CFG->dirroot.'/local/iomad_track/lib.php');
require_once($CFG->dirroot.'/local/iomad_track/db/install.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/report_user_completion_table.php');

// Params.
$userid = required_param('userid', PARAM_INT);
$returnurl = required_param('returnurl', PARAM_RAW);


// Check permissions.
require_login();
$context = context_system::instance();
iomad::require_capability('local/report_users:addentry', $context);

// Set the companyid
$companyid = iomad::get_my_companyid($context);
$company = new company($companyid);

$linktext = get_string('user_detail_title', 'local_report_users');

// Set the url.
$reporturl = new moodle_url('/local/report_users/index.php');
$baseurl = new moodle_url('/local/report_users/newentry.php', array('userid' => $userid, 'returnurl' => $returnurl));

// Print the page header.
$PAGE->set_context($context);
$PAGE->set_url($baseurl);
$PAGE->set_pagelayout('report');
$PAGE->set_title($linktext);

// Set the page heading.
$PAGE->set_heading(get_string('pluginname', 'block_iomad_reports') . " - $linktext");
$PAGE->navbar->add(get_string('dashboard', 'block_iomad_company_admin'));
if (iomad::has_capability('local/report_completion:view', $context)) {
    $PAGE->navbar->add(get_string('pluginname', 'local_report_completion'),
                       new moodle_url($CFG->wwwroot . "/local/report_completion/index.php"));
}
$PAGE->navbar->add($linktext, $reporturl);

// Get the renderer.
$output = $PAGE->get_renderer('block_iomad_company_admin');

// Check the userid is valid.
if (!company::check_valid_user($companyid, $userid)) {
    print_error('invaliduser', 'block_iomad_company_management');
}

$mform = new local_report_users\forms\add_entry_form($PAGE->url);

if ($mform->is_cancelled()) {
    redirect($returnurl);
    die;
}

if ($data = $mform->get_data()) {
    // Process it.
    $newentry = new stdclass();
    $newentry->userid = $userid;
    $newentry->courseid = $data->courseid;
    $newentry->timeenrolled = $data->timeenrolled;
    $newentry->timestarted = $data->timeenrolled;
    $newentry->timecompleted = $data->timecompleted;
    $newentry->finalscore = $data->finalscore;
    $newentry->companyid = $companyid;
    if (!empty($data->licenseallocated)) {
        $newentry->licenseallocated = $data->licenseallocated;
        $newentry->licenseid = 0;
        $newentry->licensename = $data->licensename;
    } else {
        $newentry->licenseallocated = null;
    }
    $newentry->modifiedtime = time();
    if ($iomadcourse = $DB->get_record_sql("SELECT * FROM {iomad_courses}
                                            WHERE courseid = :courseid
                                            AND validlength > 0",
                                            array('courseid' => $data->courseid))) {
        $newentry->timeexpires = $data->timecompleted + (24*60*60 * $iomadcourse->validlength);
    } else {
        $newentry->timeexpires = null;
    }
    $courserec = $DB->get_record('course', array('id' => $data->courseid));
    $newentry->coursename = $courserec->fullname;
    $newentry->coursecleared = 1;
    $trackid = $DB->insert_record('local_iomad_track', $newentry);

    // Create a certificate, if required.
    xmldb_local_iomad_track_record_certificates($newentry->courseid, $newentry->userid, $trackid, false);

    // Return success.
    redirect($returnurl,
             get_string("newentry_successful", 'local_report_users'),
             null,
             core\output\notification::NOTIFY_SUCCESS);
    die;
}
// Display the page.
echo $output->header();
$mform->display();
echo $output->footer();
