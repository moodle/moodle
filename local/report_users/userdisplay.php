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
 * @package   local_report_users
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__).'/../../config.php');
require_once($CFG->libdir.'/completionlib.php');
require_once($CFG->dirroot.'/blocks/iomad_company_admin/lib.php');
require_once($CFG->dirroot.'/local/iomad_track/lib.php');
require_once($CFG->dirroot.'/local/iomad_track/db/install.php');

// Params.
$courseid = optional_param('courseid', 0, PARAM_INT);
$userid = required_param('userid', PARAM_INT);
$page = optional_param('page', 0, PARAM_INT);
$download = optional_param('download', 0, PARAM_CLEAN);
$delete = optional_param('delete', 0, PARAM_INT);
$rowid = optional_param('rowid', 0, PARAM_INT);
$redocertificate = optional_param('redocertificate', 0, PARAM_INT);
$action = optional_param('action', '', PARAM_CLEAN);
$confirm = optional_param('confirm', 0, PARAM_INT);
$validonly = optional_param('validonly', $CFG->iomad_hidevalidcourses, PARAM_BOOL);
$edit = optional_param('edit', -1, PARAM_BOOL);

if (!empty($USER->editing)) {
    $download = 0;
}

$params = array();
$params['userid'] = $userid;
$params['validonly'] = $validonly;

// Check permissions.
require_login();
$context = context_system::instance();
iomad::require_capability('local/report_users:view', $context);

// Deal with edit buttons.
if ($edit != -1) {
    $USER->editing = $edit;
}
if (!iomad::has_capability('local/report_users:redocertificates', $context) ||
    !iomad::has_capability('local/report_users:deleteentriesfull', $context) ||
    !iomad::has_capability('local/report_users:updateentries', $context)) {
    $USER->editing = false;
}

// Set the companyid
$companyid = iomad::get_my_companyid($context);
$company = new company($companyid);
$userinfo = $DB->get_record('user', array('id' => $userid));

$linktext = get_string('user_detail_title', 'local_report_users');

// Set the url.
$reporturl = new moodle_url('/local/report_users/index.php');
$baseurl = new moodle_url($CFG->wwwroot . '/local/report_users/userdisplay.php', $params);
$returnurl = $baseurl;

// Print the page header.
$PAGE->set_context($context);
$PAGE->set_url($baseurl);
$PAGE->set_pagelayout('report');
$PAGE->set_title($linktext);
$PAGE->requires->jquery();
$PAGE->set_other_editing_capability('local/report_users:redocertificates');
$PAGE->set_other_editing_capability('local/report_users:deleteentriesfull');
$PAGE->set_other_editing_capability('local/report_users:updateentries');

// Set the page heading.
$PAGE->set_heading(get_string('userdetails', 'local_report_users').
          $userinfo->firstname." ".
          $userinfo->lastname. " (".$userinfo->email.")");
if (iomad::has_capability('local/report_completion:view', $context)) {
    $buttoncaption = get_string('pluginname', 'local_report_completion');
    $buttonlink = new moodle_url($CFG->wwwroot . "/local/report_completion/index.php");
    $buttons = $OUTPUT->single_button($buttonlink, $buttoncaption, 'get');
    // Non boost theme edit buttons.
    if ($PAGE->user_allowed_editing()) {
        $buttons .= "&nbsp" . $OUTPUT->edit_button($PAGE->url);
    }
    $PAGE->set_button($buttons);
}

// Deal with the adhoc form.
$data = data_submitted();
if (!empty($data)) {
    if (!empty($data->redo_selected_certificates) && !empty($data->redo_certificates)) {
        if (!empty($confirm) && confirm_sesskey()) {
            iomad::require_capability('local/report_users:redocertificates', $context);
            echo $OUTPUT->header();
            foreach($data->redo_certificates as $redocertificate) {
                if ($trackrec = $DB->get_record('local_iomad_track', array('id' => $redocertificate))) {
                    echo html_writer::start_tag('p');
                    local_iomad_track_delete_entry($redocertificate);
                    xmldb_local_iomad_track_record_certificates($trackrec->courseid, $trackrec->userid, $trackrec->id);
                    echo html_writer::end_tag('p');
                }
            }
            echo $OUTPUT->single_button(new moodle_url('/local/report_users/userdisplay.php',
                                     array('userid' => $userid)), get_string('continue'));
            echo $OUTPUT->footer();
            die;
        } else {
            iomad::require_capability('local/report_users:redocertificates', $context);
            $param_array = array('userid' => $userid,
                                 'confirm' => true,
                                 'redo_selected_certificates' => $data->redo_selected_certificates,
                                 'sesskey' => sesskey()
                                 );
            foreach ($data->redo_certificates as $key => $redocertificate) {
                $param_array["redo_certificates[$key]"] = $redocertificate;
            }
            $confirmurl = new moodle_url('/local/report_users/userdisplay.php', $param_array);

            $cancel = new moodle_url('/local/report_users/userdisplay.php',
                                     array('userid' => $userid));
            echo $OUTPUT->header();
            echo $OUTPUT->confirm(get_string('redoselectedcertificatesconfirm', 'block_iomad_company_admin'), $confirmurl, $cancel);
            echo $OUTPUT->footer();
            die;

        }
    } else if (!empty($data->purge_selected_entries) && !empty($data->purge_entries)) {
        if (!empty($confirm) && confirm_sesskey()) {
            iomad::require_capability('local/report_users:deleteentriesfull', $context);
            echo $OUTPUT->header();
            foreach($data->purge_entries as $rowid) {
                local_iomad_track_delete_entry($rowid, true);
                echo html_writer::tag('p', get_string('deletedtrackentry', 'block_iomad_company_admin', $rowid));
            }
            echo $OUTPUT->single_button(new moodle_url('/local/report_users/userdisplay.php',
                                     array('userid' => $userid)), get_string('continue'));
            echo $OUTPUT->footer();
            die;
        } else {
            iomad::require_capability('local/report_users:deleteentriesfull', $context);
            $param_array = array('userid' => $userid,
                                 'confirm' => true,
                                 'purge_selected_entries' => $data->purge_selected_entries,
                                 'sesskey' => sesskey()
                                 );
            foreach ($data->purge_entries as $key => $purgeentry) {
                $param_array["purge_entries[$key]"] = $purgeentry;
            }
            $confirmurl = new moodle_url('/local/report_users/userdisplay.php', $param_array);
            $cancel = new moodle_url('/local/report_users/userdisplay.php',
                                     array('userid' => $userid));
            echo $OUTPUT->header();
            echo $OUTPUT->confirm(get_string('purgeselectedentriesconfirm', 'block_iomad_company_admin'), $confirmurl, $cancel);
            echo $OUTPUT->footer();
            die;
        }
    } else {
        iomad::require_capability('local/report_users:updateentries', $context);
        if (!empty($data->licenseallocated)) {
            $data->licenseallocated = clean_param_array($data->licenseallocated, PARAM_INT, true);
        }
        if (!empty($data->timeenrolled)) {
            $data->timeenrolled = clean_param_array($data->timeenrolled, PARAM_INT, true);
        }
        if (!empty($data->timecompleted)) {
            $data->timecompleted = clean_param_array($data->timecompleted, PARAM_INT, true);
        }
        if (!empty($data->origlicenseallocated)) {
            $data->origlicenseallocated = clean_param_array($data->origlicenseallocated, PARAM_INT);
        }
        if (!empty($data->origtimeenrolled)) {
            $data->origtimeenrolled = clean_param_array($data->origtimeenrolled, PARAM_INT);
        }
        if (!empty($data->origtimecompleted)) {
            $data->origtimecompleted = clean_param_array($data->origtimecompleted, PARAM_INT);
        }
        if (!empty($data->finalscore)) {
            $data->finalscore = clean_param_array($data->finalscore, PARAM_INT);
        }
        if (!empty($data->origfinalscore)) {
            $data->origfinalscore = clean_param_array($data->origfinalscore, PARAM_INT);
        }

        // Update any data sent from the form.
        if (!empty($data->finalscore)) {
            foreach ($data->finalscore as $key => $value) {
                if ($data->origfinalscore[$key] != $value && confirm_sesskey()) {
                    $DB->set_field('local_iomad_track', 'finalscore', $value, array('id' => $key));
                    $DB->set_field('local_iomad_track', 'modifiedtime', time(), array('id' => $key));

                    // Re-generate the certificate.
                    if ($trackrec = $DB->get_record('local_iomad_track', array('id' => $key))) {
                        local_iomad_track_delete_entry($key);
                        xmldb_local_iomad_track_record_certificates($trackrec->courseid, $trackrec->userid, $trackrec->id, false);
                    }
                }
            }
        }
        if (!empty($data->licenseallocated)) {
            foreach ($data->licenseallocated as $key => $value) {
                $testtime = strtotime("0:00", $data->origlicenseallocated[$key]);
                $senttime = strtotime($value['year'] . "-" . $value['month'] . "-" . $value['day']);

                if ($testtime != $senttime && confirm_sesskey()) {
                    $DB->set_field('local_iomad_track', 'licenseallocated', $senttime, array('id' => $key));
                    $DB->set_field('local_iomad_track', 'modifiedtime', time(), array('id' => $key));
                }
            }
        }
        if (!empty($data->timeenrolled)) {
            foreach ($data->timeenrolled as $key => $value) {
                $testtime = strtotime("0:00", $data->origtimeenrolled[$key]);
                $senttime = strtotime($value['year'] . "-" . $value['month'] . "-" . $value['day']);

                if ($testtime != $senttime && confirm_sesskey()) {
                    $DB->set_field('local_iomad_track', 'timeenrolled', $senttime, array('id' => $key));
                    $DB->set_field('local_iomad_track', 'modifiedtime', time(), array('id' => $key));
                }
            }
        }
        if (!empty($data->timecompleted)) {
            foreach ($data->timecompleted as $key => $value) {
                if ($trackrec = $DB->get_record('local_iomad_track', array('id' => $key))) {
                    $testtime = strtotime("0:00", $data->origtimecompleted[$key]);
                    $senttime = strtotime($value['year'] . "-" . $value['month'] . "-" . $value['day']);

                    if ($testtime != $senttime && confirm_sesskey()) {
                        $DB->set_field('local_iomad_track', 'timecompleted', $senttime, array('id' => $key));
                        $DB->set_field('local_iomad_track', 'modifiedtime', time(), array('id' => $key));
                        if ($iomadcourseinfo = $DB->get_record('iomad_courses', array('courseid' => $trackrec->courseid))) {
                            if (!empty($iomadcourseinfo->validlength)) {
                                $DB->set_field('local_iomad_track', 'timeexpires', $senttime + ($iomadcourseinfo->validlength * 24 * 60 * 60), array('id' => $key));
                            }
                        }

                        // Re-generate the certificate.
                        local_iomad_track_delete_entry($key);
                        xmldb_local_iomad_track_record_certificates($trackrec->courseid, $trackrec->userid, $trackrec->id, false);
                    }
                }
            }
        }
    }
}

// Get the renderer.
$output = $PAGE->get_renderer('block_iomad_company_admin');

// Check the userid is valid.
if (!company::check_valid_user($companyid, $userid)) {
    print_error('invaliduser', 'block_iomad_company_management');
}

// Check for user/course delete?
if (!empty($action)) {
    if (!empty($confirm) && confirm_sesskey()) {
        if ($action == 'redocert' && !empty($redocertificate)) {
            if ($trackrec = $DB->get_record('local_iomad_track', array('id' => $redocertificate))) {
                local_iomad_track_delete_entry($redocertificate);
                if (xmldb_local_iomad_track_record_certificates($trackrec->courseid, $trackrec->userid, $trackrec->id, false)) {
                    redirect(new moodle_url('/local/report_users/userdisplay.php', array('userid' => $userid)),
                             get_string($action . "_successful", 'local_report_users'),
                             null,
                             \core\output\notification::NOTIFY_SUCCESS);
                } else {
                    redirect(new moodle_url('/local/report_users/userdisplay.php', array('userid' => $userid)),
                             get_string($action . "_failed", 'local_report_users'),
                             null,
                             \core\output\notification::NOTIFY_ERROR);
                }
            }
        } else if ($action != 'trackonly') {
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
        if ($action != 'redocert') {
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
            } else if ($action == 'revoke') {
                echo $OUTPUT->confirm(get_string('revokeconfirm', 'local_report_users'), $confirmurl, $cancel);
            } else if ($action == 'clear') {
                if (empty($CFG->iomad_autoreallocate_licenses)) {
                    echo $OUTPUT->confirm(get_string('clearconfirm', 'local_report_users'), $confirmurl, $cancel);
                } else {
                    echo $OUTPUT->confirm(get_string('clearreallocateconfirm', 'local_report_users'), $confirmurl, $cancel);
                }
            } else if ($action == 'trackonly') {
                // We are only removing the saved record for this.
                echo $OUTPUT->confirm(get_string('purgerecordconfirm', 'local_report_users'), $confirmurl, $cancel);
            }
        } else {
            $confirmurl = new moodle_url('/local/report_users/userdisplay.php',
                                         array('userid' => $userid,
                                         'rowid' => $rowid,
                                         'confirm' => $redocertificate,
                                         'redocertificate' => $redocertificate,
                                         'courseid' => $courseid,
                                         'action' => $action,
                                         'sesskey' => sesskey()
                                         ));
            $cancel = new moodle_url('/local/report_users/userdisplay.php',
                                     array('userid' => $userid));
            echo $OUTPUT->confirm(get_string('redocertificateconfirm', 'local_report_users'), $confirmurl, $cancel);
        }

        echo $OUTPUT->footer();
        die;
    }
}

// Set up the table.
$table = new \local_report_users\tables\completion_table('user_report_completion');
$table->is_downloading($download, format_string($company->get('name')) . ' course completion report ' . fullname($userinfo), 'user_report_completion123');

if (!$table->is_downloading()) {
    $mainadmin = get_admin();

    echo $output->header();

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
            $url = new moodle_url('/blocks/iomad_company_admin/company_users_course_form.php',
                                  array('userid' => $userid));
            echo html_writer::start_tag('div', array('class' => 'reporttablecontrolscontrol'));
            echo $output->single_button($url, get_string('userenrolments', 'block_iomad_company_admin'));
            echo html_writer::end_tag('div');
        }

        if ((iomad::has_capability('block/iomad_company_admin:company_license_users', $context)
             or iomad::has_capability('block/iomad_company_admin:editallusers', $context))
             and ($userid == $USER->id or $userid != $mainadmin->id)
             and !is_mnet_remote_user($userinfo)) {
            $url = new moodle_url($CFG->wwwroot . '/blocks/iomad_company_admin/company_users_licenses_form.php',
                                   array('userid' => $userid));
            echo html_writer::start_tag('div', array('class' => 'reporttablecontrolscontrol'));
            echo $output->single_button($url, get_string('userlicenses', 'block_iomad_company_admin'));
            echo html_writer::end_tag('div');
        }
        $url = new moodle_url($CFG->wwwroot . '/local/report_users/userdisplay.php',
                              array('userid' => $userid, 'validonly' => !$validonly));
        if (!$validonly) {
            $validstring = get_string('hidevalidcourses', 'block_iomad_company_admin');
        } else {
            $validstring = get_string('showvalidcourses', 'block_iomad_company_admin');
        }
        echo html_writer::start_tag('div', array('class' => 'reporttablecontrolscontrol'));
        echo $output->single_button($url, $validstring);
        echo html_writer::end_tag('div');
        if (!empty($USER->editing)) {
            echo html_writer::start_tag('div', array('class' => 'reporttablecontrolscontrol'));
            $url = new moodle_url($CFG->wwwroot . '/local/report_users/newentry.php',
                                  array('userid' => $userid,
                                        'returnurl' => $baseurl->out()));
            echo $output->single_button($url, get_string('addnewentry', 'blog'));
            echo html_writer::end_tag('div');
        }
        echo html_writer::end_tag('div');
        echo html_writer::start_tag('div', array('class' => 'iomadclear'));
}

// Set up the initial SQL for the form.
$selectsql = "lit.id,
              lit.userid,
              lit.courseid,
              lit.coursename,
              lit.licenseid,
              lit.licensename,
              lit.licenseallocated,
              lit.timeenrolled,
              lit.timestarted,
              lit.timecompleted,
              lit.timeexpires,
              lit.finalscore,
              lit.id AS certsource,
              lit.coursecleared,1 AS actions,
              lit.modifiedtime";
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
if (empty($USER->editing) && 
    $DB->get_records_sql("SELECT lit.id FROM {iomad_courses} ic
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
    $headers[] = get_string('grade', 'grades');
}

if (!$table->is_downloading()){
    $headers[] = get_string('certificate', 'local_report_completion');
    $columns[] = 'certificate';
    $headers[] = get_string('actions');
    $columns[] = 'actions';

    // Set up the form.
    if (!empty($USER->editing) && !$table->is_downloading()) {
        echo html_writer::start_tag('form', array('action' => $baseurl,
                                                  'enctype' => 'application/x-www-form-urlencoded',
                                                  'method' => 'post',
                                                  'name' => 'iomad_report_user_userdisplay_values',
                                                  'id' => 'iomad_report_user_userdisplay_values'));
        echo "<input type='hidden' name='sesskey' value=" . sesskey() .">";
        echo "<input type='hidden' name='download' value=''>";
        echo html_writer::start_tag('div', array('class' => 'reporttablecontrolscontrol'));
        echo html_writer::start_tag('div', array('class' => 'singlebutton'));
        echo "<input type = 'submit' id='redo_all_certs' name='redo_selected_certificates' value = '" . get_string('redoselectedcertificates', 'block_iomad_company_admin') . "' class='btn btn-secondary'>";
        echo html_writer::end_tag('div');
        echo html_writer::end_tag('div');
        echo html_writer::start_tag('div', array('class' => 'reporttablecontrolscontrol'));
        echo html_writer::start_tag('div', array('class' => 'singlebutton'));
        echo "<input type = 'submit' id='purge_all_selected' name='purge_selected_entries' value = '" . get_string('purgeselectedentries', 'block_iomad_company_admin') . "' class='btn btn-secondary'>";
        echo html_writer::end_tag('div');
        echo html_writer::end_tag('div');
        echo html_writer::start_tag('div', array('class' => 'iomadclear'));

    }
}

$table->set_sql($selectsql, $fromsql, $wheresql, $sqlparams);
$table->define_baseurl($baseurl);
$table->define_columns($columns);
$table->define_headers($headers);
$table->no_sorting('status');
$table->no_sorting('certificate');
$table->no_sorting('actions');
$table->sort_default_column='coursename';

if (!empty($USER->editing)) {
    $table->downloadable = false;
}

if (!$table->is_downloading()) {
        echo html_writer::start_tag('div', array('class' => 'tablecontainer'));
}
$table->out($CFG->iomad_max_list_courses, true);

if (!$table->is_downloading()) {
    if (!empty($USER->editing)) {
        // Set up the form.
        echo html_writer::end_tag('div');
        echo html_writer::start_tag('div', array('class' => 'iomadclear'));
        echo html_writer::start_tag('div', array('class' => 'reporttablecontrolscontrol'));
        echo html_writer::start_tag('div', array('class' => 'singlebutton'));
        echo "<input type = 'submit' id='redo_all_certs_bottom' name='redo_selected_certificates' value = '" . get_string('redoselectedcertificates', 'block_iomad_company_admin') . "' class='btn btn-secondary'>";
        echo html_writer::end_tag('div');
        echo html_writer::end_tag('div');
        echo html_writer::start_tag('div', array('class' => 'reporttablecontrolscontrol'));
        echo html_writer::start_tag('div', array('class' => 'singlebutton'));
        echo "<input type = 'submit' id='purge_all_selected_bottom' name='purge_selected_entries' value = '" . get_string('purgeselectedentries', 'block_iomad_company_admin') . "' class='btn btn-secondary'>";
        echo html_writer::end_tag('div');
        echo html_writer::end_tag('div');
        echo html_writer::end_tag('div');
        echo html_writer::end_tag('form');
        echo html_writer::end_tag('div');
        form_init_date_js();
    }
    echo html_writer::end_tag('div');
    echo html_writer::end_tag('div');
?>
<script>
$(".checkbox").change(function() {
    var inputElems = document.getElementsByTagName("input")
    var matched = this.value;
    if(this.checked) {
        if(this.classList.contains("enableallcertificates")) {
            $(".enablecertificates").prop("checked", this.checked);
        }
        if(this.classList.contains("enableallentries")) {
            $(".enableentries").prop("checked", this.checked);
        }
    } else {
        if(this.classList.contains("enableallcertificates")) {
            $(".enablecertificates").prop("checked", '');
        }
        if(this.classList.contains("enableallentries")) {
            $(".enableentries").prop("checked", '');
        }
    }
});
</script>
<?php
    echo $output->footer();
}
