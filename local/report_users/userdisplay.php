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
require_once(dirname(__FILE__).'/report_user_completion_table.php');

// Params.
$courseid = optional_param('courseid', 0, PARAM_INT);
$userid = required_param('userid', PARAM_INT);
$page = optional_param('page', 0, PARAM_INT);
$download = optional_param('download', 0, PARAM_INT);
$delete = optional_param('delete', 0, PARAM_INT);
$action = optional_param('action', '', PARAM_CLEAN);
$confirm = optional_param('confirm', 0, PARAM_INT);

// Check permissions.
require_login();
$context = context_system::instance();
iomad::require_capability('local/report_users:view', $context);

// Set the companyid
$companyid = iomad::get_my_companyid($context);

$linktext = get_string('user_detail_title', 'local_report_users');

// Set the url.
$linkurl = new moodle_url('/local/report_users/index.php');

// Print the page header.
$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('report');
$PAGE->set_title($linktext);

// Set the page heading.
$PAGE->set_heading(get_string('pluginname', 'block_iomad_reports') . " - $linktext");
$PAGE->navbar->add(get_string('dashboard', 'block_iomad_company_admin'));
$PAGE->navbar->add($linktext, $linkurl);

// Get the renderer.
$output = $PAGE->get_renderer('block_iomad_company_admin');

$baseurl = new moodle_url(basename(__FILE__));
$returnurl = $baseurl;

// Check the userid is valid.
if (!company::check_valid_user($companyid, $userid)) {
    print_error('invaliduser', 'block_iomad_company_management');
}

// Get this list of courses the user is a member of.
// Check for confirmed delete?
if ($confirm) {
   company_user::delete_user_course($userid, $courseid, $action);
   redirect(new moodle_url('/local/report_users/userdisplay.php', array(
        'userid' => $userid)));
}

// Set up the table.
$table = new local_report_user_completion_table('user_report_completion');
//$table->is_downloading($download, 'user_report_completion', 'user_report_completion123');

if (!$table->is_downloading()) {
    echo $output->header();
    $userinfo = $DB->get_record('user', array('id' => $userid));

    echo "<h2>".get_string('userdetails', 'local_report_users').
          $userinfo->firstname." ".
          $userinfo->lastname. " (".$userinfo->email.")";
    if (!empty($userinfo->suspended)) {
        echo " - Suspended</h2>";
    } else {
        echo "</h2>";
    }
}

// Set up the initial SQL for the form.
$selectsql = "lit.id,lit.userid,lit.courseid,lit.coursename,lit.licenseid,lit.licensename,lit.licenseallocated,lit.timeenrolled,lit.timestarted,lit.timecompleted,lit.finalscore,lit.id as certsource, cc.timecompleted AS action";
$fromsql = "{local_iomad_track} lit LEFT JOIN {course_completions} cc ON (lit.courseid = cc.course AND lit.userid = cc.userid AND lit.timecompleted = cc.timecompleted AND lit.timecompleted IS NOT NULL)";
$wheresql = " lit.userid = :userid";
$sqlparams = array('userid' => $userid);

// Set up the headers for the form.
$headers = array(get_string('course', 'local_report_completion'),
                 get_string('status', 'local_report_completion'),
                 get_string('licensedateallocated', 'block_iomad_company_admin'),
                 get_string('dateenrolled', 'local_report_completion'),
                 get_string('datecompleted', 'local_report_completion'),
                 get_string('timeexpires', 'local_report_completion'),
                 get_string('finalscore', 'local_report_completion'),
                 get_string('certificate', 'local_report_completion'),
                 get_string('actions', 'local_report_users'));

$columns = array('coursename',
                 'status',
                 'licenseallocated',
                 'timeenrolled',
                 'timecompleted',
                 'timeexpires',
                 'finalscore',
                 'certificate',
                 'actions');

$table->set_sql($selectsql, $fromsql, $wheresql, $sqlparams);
$table->define_baseurl($baseurl);
$table->define_columns($columns);
$table->define_headers($headers);
$table->no_sorting('status');
$table->no_sorting('certificate');
$table->out($CFG->iomad_max_list_courses, true);

echo $output->footer();