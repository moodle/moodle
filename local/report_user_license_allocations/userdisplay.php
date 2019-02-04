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
require_once($CFG->libdir.'/completionlib.php');
require_once($CFG->dirroot.'/blocks/iomad_company_admin/lib.php');
require_once(dirname(__FILE__).'/lib.php');

// Params.
$courseid = optional_param('courseid', 0, PARAM_INT);
$userid = required_param('userid', PARAM_INT);
$page = optional_param('page', 0, PARAM_INT);
$dodownload = optional_param('dodownload', 0, PARAM_INT);

// Check permissions.
require_login();
$context = context_system::instance();
iomad::require_capability('local/report_user_license_allocations:view', $context);

// Set the companyid
$companyid = iomad::get_my_companyid($context);

$linktext = get_string('user_detail_title', 'local_report_user_license_allocations');
// Set the url.
$linkurl = new moodle_url('/local/report_user_license_allocations/index.php');

// Print the page header.
$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('report');
$PAGE->set_title($linktext);

// Set the page heading.
$PAGE->set_heading(get_string('pluginname', 'block_iomad_reports') . " - $linktext");
$PAGE->navbar->add(get_string('dashboard', 'block_iomad_company_admin'));
$PAGE->navbar->add($linktext, $linkurl);

$baseurl = new moodle_url(basename(__FILE__));
$returnurl = $baseurl;
if (empty($dodownload)) {
    echo $OUTPUT->header();

    // Check the userid is valid.
    if (!company::check_valid_user($companyid, $userid)) {
        print_error('invaliduser', 'block_iomad_company_management');
    }
} else {
    // Check the userid is valid.
    if (!company::check_valid_user($companyid, $userid)) {
        print_error('invaliduser', 'block_iomad_company_management');
    }
}


// Get this list of license the user has been allocated.
$userlicenses = $DB->get_records_sql("SELECT DISTINCT objectid FROM {logstore_standard_log}
                                      WHERE userid = :userid
                                      AND " . $DB->sql_like('eventname', ':eventname', false, false),
                                      array('userid' => $userid,
                                            'eventname' => '%user_license%'));
// Get the Users details.
$userinfo = $DB->get_record('user', array('id' => $userid));

if (!empty($dodownload)) {
    // Set up the Excel workbook.
    header("Content-Type: application/download\n");
    header("Content-Disposition: attachment; filename=\"userlicallocreport.csv\"");
    header("Expires: 0");
    header("Cache-Control: must-revalidate,post-check=0,pre-check=0");
    header("Pragma: public");
}

if (empty($dodownload)) {
    echo "<h2>".get_string('userdetails', 'local_report_user_license_allocations').
          $userinfo->firstname." ".
          $userinfo->lastname. " (".$userinfo->email.")";
          if (!empty($userinfo->suspended)) {
              echo " - Suspended</h2>";
          } else {
              echo "</h2>";
          }
    if (!empty($licenseid)) {
        // Navigation and header.
        echo $OUTPUT->single_button(new moodle_url('userdisplay.php', array('licensid' => $licenseid,
                                                                            'userid' => $userid,
                                                                            'dodownload' => '1')),
                                    get_string("downloadcsv", 'local_report_completion'));
    }
}
// Table for results.
$compusertable = new html_table();
$compusertable->head = array(get_string('course', 'local_report_completion'),
                             get_string('status', 'local_report_completion'),
                             get_string('dateenrolled', 'local_report_completion'),
                             get_string('datestarted', 'local_report_completion'),
                             get_string('datecompleted', 'local_report_completion'),
                             get_string('finalscore', 'local_report_completion'));
$compusertable->align = array('left', 'center', 'center', 'center', 'center', 'center');
$compusertable->width = '95%';

$compusertable->head[] = get_string('actions', 'local_report_user_license_allocations');
$compusertable->align[] = 'center';

// Set that there is nothing found here first.
$results = false;

$userresults = array();
foreach ($userlicenses as $userlicense) {
    // Get the license info.
    $licenseid = $userlicense->objectid;
    if ($license = $DB->get_record('companylicense', array('id' => $licenseid))) {
        if (empty($license->program)) {
            $allocations = $DB->get_records_sql("SELECT * FROM {logstore_standard_log}
                                                 WHERE eventname = :eventname
                                                 AND objectid = :licenseid
                                                 AND userid = :userid",
                                                 array('licenseid' => $licenseid,
                                                       'userid' => $userid,
                                                       'eventname' => '\block_iomad_company_admin\event\user_license_assigned'));
            $unallocations = $DB->get_records_sql("SELECT * FROM {logstore_standard_log}
                                                   WHERE eventname = :eventname
                                                   AND objectid = :licenseid
                                                   AND userid = :userid",
                                                   array('licenseid' => $licenseid,
                                                         'userid' => $userid,
                                                         'eventname' => '\block_iomad_company_admin\event\user_license_unassigned'));
            $allocationinfo = array();
            $allocationinfo = $allocations + $unallocations;
            ksort($allocationinfo);
        } else {
            // need to do a bit of munging.
            $allocations = $DB->get_records_sql("SELECT * FROM {logstore_standard_log}
                                                 WHERE eventname = :eventname
                                                 AND objectid = :licenseid
                                                 AND userid = :userid",
                                                 array('licenseid' => $licenseid,
                                                       'userid' => $userid,
                                                       'eventname' => '\block_iomad_company_admin\event\user_license_assigned'));
            $tempallocs = array();
            foreach ($allocations as $allocation) {
                $tempallocs[$allocation->other. '-' . round($allocation->timecreated, -2)] = $allocation;
            }
            $allocations = array();
            foreach ($tempallocs as $tempalloc) {
                $allocations[$tempalloc->id] = $tempalloc;
            }
            $unallocations = $DB->get_records_sql("SELECT * FROM {logstore_standard_log}
                                                   WHERE eventname = :eventname
                                                   AND objectid = :licenseid
                                                   AND userid = :userid",
                                                   array('licenseid' => $licenseid,
                                                         'userid' => $userid,
                                                         'eventname' => '\block_iomad_company_admin\event\user_license_unassigned'));
            $tempallocs = array();
            foreach ($unallocations as $unallocation) {
                $tempallocs[$unallocation->other. '-' . round($unallocation->timecreated, -2)] = $unallocation;
            }
            $unallocations = array();
            foreach ($tempallocs as $tempalloc) {
                $unallocations[$tempalloc->id] = $tempalloc;
            }
            $allocationinfo = array();
            $allocationinfo = $allocations + $unallocations;
            ksort($allocationinfo);
        }
        $userresults[$licenseid] = $allocationinfo;
    }
}

$userlicensetable = new html_table();
$userlicensetable->head = array(get_string('licensename', 'block_iomad_company_admin'), get_string('detail', 'local_report_user_license_allocations'));
foreach ($userresults as $licenseid => $detail) {
    $license = $DB->get_record('companylicense', array('id' => $licenseid));
    $allocationinfo = "";
    foreach ($detail as $allocation) {
        if ($allocation->eventname == '\block_iomad_company_admin\event\user_license_assigned') {
            $allocationinfo .= get_string('allocated', 'local_report_user_license_allocations', date($CFG->iomad_date_format, $allocation->timecreated)) . "</br>";
        } else if ($allocation->eventname == '\block_iomad_company_admin\event\user_license_unassigned') {
            $allocationinfo .= get_string('unallocated', 'local_report_user_license_allocations', date($CFG->iomad_date_format, $allocation->timecreated)) . "</br>";
        }
    }
    $licenseurl = "<a href='" . new moodle_url('/local/report_user_license_allocations/index.php', array('licenseid' => $licenseid)) ."'>" .
                   $license->name . "</a>";
    $userlicensetable->data[] = array($licenseurl, $allocationinfo);
}

if (!empty($dodownload)) {
    exit;
}

echo html_writer::table($userlicensetable);

echo $OUTPUT->footer();
