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
require_once($CFG->dirroot.'/local/iomad_track/lib.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/report_user_completion_table.php');

// Params.
$courseid = optional_param('courseid', 0, PARAM_INT);
$userid = required_param('userid', PARAM_INT);
$page = optional_param('page', 0, PARAM_INT);
$download = optional_param('download', 0, PARAM_CLEAN);
$delete = optional_param('delete', 0, PARAM_INT);
$rowid = optional_param('rowid', 0, PARAM_INT);
$action = optional_param('action', '', PARAM_CLEAN);
$confirm = optional_param('confirm', 0, PARAM_INT);
$validonly = optional_param('validonly', $CFG->iomad_hidevalidcourses, PARAM_BOOL);
$revoke = false;

if ($action == 'revoke') {
    $revoke = true;
    $action = 'clear';
}
$params = array();
$params['userid'] = $userid;
$params['validonly'] = $validonly;

// Check permissions.
require_login();
$context = context_system::instance();
iomad::require_capability('local/report_users:view', $context);

// Set the companyid
$companyid = iomad::get_my_companyid($context);
$company = new company($companyid);

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
if (empty($CFG->defaulthomepage)) {
    $PAGE->navbar->add(get_string('dashboard', 'block_iomad_company_admin'), new moodle_url($CFG->wwwroot . '/my'));
}
if (iomad::has_capability('local/report_completion:view', $context)) {
    $PAGE->navbar->add(get_string('pluginname', 'local_report_completion'),
                       new moodle_url($CFG->wwwroot . "/local/report_completion/index.php", array('validonly' => $validonly)));
}
$PAGE->navbar->add($linktext, $linkurl);

// Get the renderer.
$output = $PAGE->get_renderer('block_iomad_company_admin');

$baseurl = new moodle_url(basename(__FILE__), $params);
$returnurl = $baseurl;

// Check the userid is valid.
if (!company::check_valid_user($companyid, $userid)) {
    print_error('invaliduser', 'block_iomad_company_management');
}

// Check for user/course delete?
if (!empty($action)) {
    if (!empty($confirm) && confirm_sesskey()) {
        if ($action != 'trackonly') {
            company_user::delete_user_course($userid, $courseid, $action);
            redirect(new moodle_url('/local/report_users/userdisplay.php', array('userid' => $userid)),
                     get_string($action . "_successful", 'local_report_users'),
                     null,
                     \core\output\notification::NOTIFY_SUCCESS);
            die;
        } else {
            local_iomad_track_delete_entry($rowid, true);
        }
    } else {
        echo $OUTPUT->header();
        $confirmurl = new moodle_url('/local/report_users/userdisplay.php',
                                     array('userid' => $userid,
                                     'rowid' => $rowid,
                                     'confirm' => $delete,
                                     'courseid' => $courseid,
                                     'action' => $action,
                                     'sesskey' => sesskey()
                                     ));
        $cancel = new moodle_url('/local/report_users/userdisplay.php',
                                 array('userid' => $userid));
        if ($action == 'delete') {
            echo $OUTPUT->confirm(get_string('resetcourseconfirm', 'local_report_users'), $confirmurl, $cancel);
        } else if ($action == 'clear') {
            if ($revoke) {
                echo $OUTPUT->confirm(get_string('revokeconfirm', 'local_report_users'), $confirmurl, $cancel);
            } else {
                if (empty($CFG->iomad_autoreallocate_licenses)) {
                    echo $OUTPUT->confirm(get_string('clearconfirm', 'local_report_users'), $confirmurl, $cancel);
                } else {
                    echo $OUTPUT->confirm(get_string('clearreallocateconfirm', 'local_report_users'), $confirmurl, $cancel);
                }
            }
        } else if ($action == 'trackonly') {
            // We are only removing the saved record for this.
            echo $OUTPUT->confirm(get_string('purgerecordconfirm', 'local_report_users'), $confirmurl, $cancel);
        }
        echo $OUTPUT->footer();
        die;
    }
}

// Get this list of courses the user is a member of.
// Check for confirmed delete?
if ($confirm) {
}

// Set up the table.
$table = new local_report_user_completion_table('user_report_completion');
$table->is_downloading($download, 'user_report_completion', 'user_report_completion123');

if (!$table->is_downloading()) {
    $mainadmin = get_admin();

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

        echo html_writer::start_tag('div', array('class' => 'iomadclear'));
        if ((iomad::has_capability('block/iomad_company_admin:company_course_users', $context)
             or iomad::has_capability('block/iomad_company_admin:editallusers', $context))
             and ($userid == $USER->id or $userid != $mainadmin->id)
             and !is_mnet_remote_user($userinfo)) {
            $url = new moodle_url('/blocks/iomad_company_admin/company_users_course_form.php', array(
                'userid' => $userid,
            ));
            echo html_writer::start_tag('div', array('class' => 'reporttablecontrolscontrol'));
            echo $output->single_button($url, get_string('userenrolments', 'block_iomad_company_admin'));
            echo html_writer::end_tag('div');
        }

        if ((iomad::has_capability('block/iomad_company_admin:company_license_users', $context)
             or iomad::has_capability('block/iomad_company_admin:editallusers', $context))
             and ($userid == $USER->id or $userid != $mainadmin->id)
             and !is_mnet_remote_user($userinfo)) {
            $url = new moodle_url('/blocks/iomad_company_admin/company_users_licenses_form.php', array(
                'userid' => $userid,
            ));
            echo html_writer::start_tag('div', array('class' => 'reporttablecontrolscontrol'));
            echo $output->single_button($url, get_string('userlicenses', 'block_iomad_company_admin'));
            echo html_writer::end_tag('div');
        }
        $url = new moodle_url(basename(__FILE__), array('userid' => $userid, 'validonly' => !$validonly));
        if (!$validonly) {
            $validstring = get_string('hidevalidcourses', 'block_iomad_company_admin');
        } else {
            $validstring = get_string('showvalidcourses', 'block_iomad_company_admin');
        }
        echo html_writer::start_tag('div', array('class' => 'reporttablecontrolscontrol'));
        echo $output->single_button($url, $validstring);
        echo html_writer::end_tag('div');
        echo html_writer::end_tag('div');
        echo html_writer::start_tag('div', array('class' => 'iomadclear'));
}

// Set up the initial SQL for the form.
$selectsql = "lit.id,lit.userid,lit.courseid,lit.coursename,lit.licenseid,lit.licensename,lit.licenseallocated,lit.timeenrolled,lit.timestarted,lit.timecompleted,lit.timeexpires,lit.finalscore,lit.id as certsource,lit.coursecleared";
$fromsql = "{local_iomad_track} lit ";
$sqlparams = array('userid' => $userid, 'companyid' => $companyid);

// Just valid courses?
if ($validonly) {
    $validsql = " AND (lit.timeexpires > :runtime or (lit.timecompleted IS NULL) or (lit.timecompleted > 0 AND lit.timeexpires IS NULL))";
    $sqlparams['runtime'] = time();
} else {
    $validsql = "";
}

$wheresql = " lit.userid = :userid AND lit.companyid = :companyid AND lit.courseid IN (" . join(',', array_keys($company->get_menu_courses(true))) .") $validsql";

// Set up the headers for the form.
$headers = array(get_string('course', 'local_report_completion'),
                 get_string('status'),
                 get_string('licensedateallocated', 'block_iomad_company_admin'),
                 get_string('datestarted', 'local_report_completion'),
                 get_string('datecompleted', 'local_report_completion'));

$columns = array('coursename',
                 'status',
                 'licenseallocated',
                 'timeenrolled',
                 'timecompleted');

// Do we show the time expires column?
if ($DB->get_records_sql("SELECT lit.id FROM {iomad_courses} ic
                          JOIN {local_iomad_track} lit
                          ON ic.courseid = lit.courseid
                          WHERE ic.validlength > 0
                          AND lit.userid = :userid",
                          array('userid' => $userid))) {
    $columns[] = 'timeexpires';
    $headers[] = get_string('timeexpires', 'local_report_completion');
}

// Do we show the grade column?
if ($DB->get_records_sql("SELECT lit.id FROM {iomad_courses} ic
                          JOIN {local_iomad_track} lit
                          ON ic.courseid = lit.courseid
                          WHERE ic.hasgrade = 1
                          AND lit.userid = :userid",
                          array('userid' => $userid))) {
    $columns[] = 'finalscore';
    $headers[] = get_string('grade');
}

if (!$table->is_downloading()){
    $headers[] = get_string('certificate', 'local_report_completion');
    $columns[] = 'certificate';
    $headers[] = get_string('actions');
    $columns[] = 'actions';
}

$table->set_sql($selectsql, $fromsql, $wheresql, $sqlparams);
$table->define_baseurl($baseurl);
$table->define_columns($columns);
$table->define_headers($headers);
$table->no_sorting('status');
$table->no_sorting('certificate');
$table->sort_default_column='coursename';
$table->out($CFG->iomad_max_list_courses, true);

if (!$table->is_downloading()) {
    echo html_writer::end_tag('div');
    echo $output->footer();
}
