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
 * @package   local_report_completion
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__).'/../../config.php');
require_once($CFG->libdir.'/completionlib.php');
require_once($CFG->dirroot.'/local/iomad_track/lib.php');
require_once($CFG->dirroot.'/local/iomad_track/db/install.php');
require_once($CFG->dirroot.'/blocks/iomad_company_admin/lib.php');

// Params.
$courseid = optional_param('courseid', 0, PARAM_INT);
$participant = optional_param('participant', 0, PARAM_INT);
$download = optional_param('download', 0, PARAM_CLEAN);
$firstname       = optional_param('firstname', 0, PARAM_CLEAN);
$lastname      = optional_param('lastname', '', PARAM_CLEAN);
$showsuspended = optional_param('showsuspended', 0, PARAM_INT);
$showhistoric = optional_param('showhistoric', 1, PARAM_BOOL);
$email  = optional_param('email', 0, PARAM_CLEAN);
$timecreated  = optional_param('timecreated', 0, PARAM_CLEAN);
$sort         = optional_param('sort', '', PARAM_ALPHA);
$dir          = optional_param('dir', 'ASC', PARAM_ALPHA);
$page         = optional_param('page', 0, PARAM_INT);
$perpage      = optional_param('perpage', $CFG->iomad_max_list_users, PARAM_INT);        // How many per page.
$acl          = optional_param('acl', '0', PARAM_INT);           // Id of user to tweak mnet ACL (requires $access).
$coursesearch = optional_param('coursesearch', '', PARAM_CLEAN);// Search string.
$departmentid = optional_param('deptid', 0, PARAM_INTEGER);
$completiontype = optional_param('completiontype', 0, PARAM_INT);
$charttype = optional_param('charttype', '', PARAM_CLEAN);
$showcharts = optional_param('showcharts', $CFG->iomad_showcharts, PARAM_BOOL);
$confirm = optional_param('confirm', 0, PARAM_INT);
$fromraw = optional_param_array('compfromraw', null, PARAM_INT);
$toraw = optional_param_array('comptoraw', null, PARAM_INT);
$yearfrom = optional_param_array('fromarray', null, PARAM_INT);
$yearto = optional_param_array('toarray', null, PARAM_INT);
$showpercentage = optional_param('showpercentage', 0, PARAM_INT);
$submitbutton = optional_param('submitbutton', '', PARAM_CLEAN);
$validonly = optional_param('validonly', 0, PARAM_BOOL);
$edit = optional_param('edit', -1, PARAM_BOOL);
$action = optional_param('action', '', PARAM_CLEAN);
$confirm = optional_param('confirm', 0, PARAM_INT);
$rowid = optional_param('rowid', 0, PARAM_INT);
$redocertificate = optional_param('redocertificate', 0, PARAM_INT);
$userid = optional_param('userid', 0, PARAM_INT);
$delete = optional_param('delete', 0, PARAM_INT);
$viewchildren = optional_param('viewchildren', true, PARAM_BOOL);
$showsummary = optional_param('showsummary', true, PARAM_BOOL);

require_login();

$systemcontext = context_system::instance();

// Set the companyid
$companyid = iomad::get_my_companyid($systemcontext);
$companycontext = \core\context\company::instance($companyid);
$company = new company($companyid);

// We need to unset the companyid as we could be looking elsewhere.
$companyid = optional_param('companyid', $companyid, PARAM_INT);

iomad::require_capability('local/report_completion:view', $companycontext);

$canseechildren = iomad::has_capability('block/iomad_company_admin:canviewchildren', $companycontext);

$params['courseid'] = $courseid;
if ($firstname) {
    $params['firstname'] = $firstname;
}
if ($lastname) {
    $params['lastname'] = $lastname;
}
if ($email) {
    $params['email'] = $email;
}
if ($sort) {
    $params['sort'] = $sort;
}
if ($dir) {
    $params['dir'] = $dir;
}
if ($page) {
    $params['page'] = $page;
}
if ($perpage) {
    $params['perpage'] = $perpage;
}
if ($coursesearch) {
    $params['coursesearch'] = $coursesearch;
}
if ($courseid) {
    $params['courseid'] = $courseid;
}
if ($departmentid) {
    $params['deptid'] = $departmentid;
}
if ($departmentid) {
    $params['departmentid'] = $departmentid;
}
if ($showsuspended) {
    $params['showsuspended'] = $showsuspended;
}
if ($completiontype) {
    $params['completiontype'] = $completiontype;
}
$params['viewchildren'] = $viewchildren;
$params['showsummary'] = $showsummary;
$params['showcharts'] = $showcharts;

if ($fromraw) {
    if (is_array($fromraw)) {
        $from = mktime(0, 0, 0, $fromraw['month'], $fromraw['day'], $fromraw['year']);
    } else {
        $from = $fromraw;
    }
    $params['from'] = $from;
    $params['fromraw[day]'] = $fromraw['day'];
    $params['fromraw[month]'] = $fromraw['month'];
    $params['fromraw[year]'] = $fromraw['year'];
    $params['fromraw[enabled]'] = $fromraw['enabled'];
} else {
    $from = null;
}

if ($toraw) {
    if (is_array($toraw)) {
        $to = mktime(0, 0, 0, $toraw['month'], $toraw['day'], $toraw['year']);
    } else {
        $to = $toraw;
    }
    $params['to'] = $to;
    $params['toraw[day]'] = $toraw['day'];
    $params['toraw[month]'] = $toraw['month'];
    $params['toraw[year]'] = $toraw['year'];
    $params['toraw[enabled]'] = $toraw['enabled'];
} else {
    if (!empty($from)) {
        $to = time();
        $params['to'] = $to;
    } else {
        $to = null;
    }
}
$params['showpercentage'] = $showpercentage;
$params['validonly'] = $validonly;
$params['userid'] = $userid;

// Deal with edit buttons.
if ($edit != -1) {
    $USER->editing = $edit;
}
if (!iomad::has_capability('local/report_users:redocertificates', $companycontext) ||
    !iomad::has_capability('local/report_users:deleteentriesfull', $companycontext) ||
    !iomad::has_capability('local/report_users:updateentries', $companycontext)) {
    $USER->editing = false;
}

$buttons = "";

// Url stuff.
$url = new moodle_url('/local/report_completion/index.php', array('validonly' => $validonly));

// Page stuff:.
$strcompletion = get_string('pluginname', 'local_report_completion');
$PAGE->set_context($companycontext);
$PAGE->set_url($url, $params);
$PAGE->set_pagelayout('report');
$PAGE->set_title($strcompletion);
$PAGE->requires->css("/local/report_completion/styles.css");
$PAGE->requires->jquery();
$PAGE->set_other_editing_capability('local/report_users:redocertificates');
$PAGE->set_other_editing_capability('local/report_users:deleteentriesfull');
$PAGE->set_other_editing_capability('local/report_users:updateentries');


// Javascript for fancy select.
// Parameter is name of proper select form element followed by 1=submit its form
$PAGE->requires->js_call_amd('block_iomad_company_admin/department_select', 'init', array('deptid', 1, optional_param('deptid', 0, PARAM_INT)));

// Set the page heading.
if (empty($courseid)) {
    $PAGE->set_heading($strcompletion);
} else {
    $course = $DB->get_record('course', ['id' => $courseid]);
    $PAGE->set_heading(get_string('completion_course_title', 'local_report_completion', format_string($course->fullname)));
}

if (!empty($courseid)) {
    $buttoncaption = get_string('pluginname', 'local_report_completion');
    $buttonparams = $params;
    unset($buttonparams['courseid']);
    $buttonlink = new moodle_url($CFG->wwwroot . "/local/report_completion/index.php", $buttonparams);
    $buttons .= $OUTPUT->single_button($buttonlink, $buttoncaption, 'get');
    // Non boost theme edit buttons.
    if ($PAGE->user_allowed_editing()) {
        $buttons .=  "&nbsp" . $OUTPUT->edit_button($PAGE->url);
    }
    $PAGE->set_button($buttons);
}

// Deal with the adhoc form.
$data = data_submitted();
if (!empty($data)) {
    if (!empty($data->redo_selected_certificates) && !empty($data->redo_certificates)) {
        if (!empty($confirm) && confirm_sesskey()) {
            iomad::require_capability('local/report_users:redocertificates', $companycontext);
            echo $OUTPUT->header();
            foreach($data->redo_certificates as $redocertificate) {
                if ($trackrec = $DB->get_record('local_iomad_track', array('id' => $redocertificate))) {
                    echo html_writer::start_tag('p');
                    local_iomad_track_delete_entry($redocertificate);
                    xmldb_local_iomad_track_record_certificates($trackrec->courseid, $trackrec->userid, $trackrec->id, true, false);
                    echo html_writer::end_tag('p');
                }
            }
            echo $OUTPUT->single_button(new moodle_url('/local/report_completion/index.php',
                                     $params), get_string('continue'));
            echo $OUTPUT->footer();
            die;
        } else {
            iomad::require_capability('local/report_users:redocertificates', $companycontext);
            $param_array = array('courseid' => $courseid,
                                 'confirm' => true,
                                 'redo_selected_certificates' => $data->redo_selected_certificates,
                                 'sesskey' => sesskey()
                                 );
            foreach ($data->redo_certificates as $key => $redocertificate) {
                $param_array["redo_certificates[$key]"] = $redocertificate;
            }
            $confirmurl = new moodle_url('/local/report_completion/index.php', $param_array + $params);

            $cancel = new moodle_url('/local/report_completion/index.php', $params);
            echo $OUTPUT->header();
            echo $OUTPUT->confirm(get_string('redoselectedcertificatesconfirm', 'block_iomad_company_admin'), $confirmurl, $cancel);
            echo $OUTPUT->footer();
            die;

        }
    } else if (!empty($data->purge_selected_entries) && !empty($data->purge_entries)) {
        if (!empty($confirm) && confirm_sesskey()) {
            iomad::require_capability('local/report_users:deleteentriesfull', $companycontext);
            echo $OUTPUT->header();
            foreach($data->purge_entries as $rowid) {
                local_iomad_track_delete_entry($rowid, true);
                echo html_writer::tag('p', get_string('deletedtrackentry', 'block_iomad_company_admin', $rowid));
            }
            echo $OUTPUT->single_button(new moodle_url('/local/report_completion/index.php',
                                     $params + array('userid' => $userid)), get_string('continue'));
            echo $OUTPUT->footer();
            die;
        } else {
            iomad::require_capability('local/report_users:deleteentriesfull', $companycontext);
            $param_array = $params +
                           array('userid' => $userid,
                                 'confirm' => true,
                                 'purge_selected_entries' => $data->purge_selected_entries,
                                 'sesskey' => sesskey()
                                 );
            foreach ($data->purge_entries as $key => $purgeentry) {
                $param_array["purge_entries[$key]"] = $purgeentry;
            }
            $confirmurl = new moodle_url('/local/report_completion/index.php', $param_array);
            $cancel = new moodle_url('/local/report_completion/index.php',
                                     $params +
                                     array('userid' => $userid));
            echo $OUTPUT->header();
            echo $OUTPUT->confirm(get_string('purgeselectedcourseentriesconfirm', 'block_iomad_company_admin'), $confirmurl, $cancel);
            echo $OUTPUT->footer();
            die;
        }
    } else if (!empty($data->origlicenseallocated) ||
               !empty($data->origtimeenrolled) ||
               !empty($data->origtimecompleted) ||
               !empty($data->origfinalscore)) {
        iomad::require_capability('local/report_users:updateentries', $companycontext);
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
                        xmldb_local_iomad_track_record_certificates($trackrec->courseid, $trackrec->userid, $trackrec->id, false, false);
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
                        xmldb_local_iomad_track_record_certificates($trackrec->courseid, $trackrec->userid, $trackrec->id, false, false);
                    }
                }
            }
        }
    }
}

// Check for user/course delete?
if (!empty($action)) {
    if (!empty($confirm) && confirm_sesskey()) {
        if ($action == 'redocert' && !empty($redocertificate)) {
            if ($trackrec = $DB->get_record('local_iomad_track', array('id' => $redocertificate))) {
                local_iomad_track_delete_entry($redocertificate);
                if (xmldb_local_iomad_track_record_certificates($trackrec->courseid, $trackrec->userid, $trackrec->id, false, false)) {
                    redirect(new moodle_url('/local/report_completion/index.php', $params),
                             get_string($action . "_successful", 'local_report_users'),
                             null,
                             \core\output\notification::NOTIFY_SUCCESS);
                } else {
                    redirect(new moodle_url('/local/report_completion/index.php', $params),
                             get_string($action . "_failed", 'local_report_users'),
                             null,
                             \core\output\notification::NOTIFY_ERROR);
                }
            }
        } else if ($action != 'trackonly') {
            company_user::delete_user_course($userid, $courseid, $action);
            redirect(new moodle_url('/local/report_completion/index.php', $params),
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
            $confirmurl = new moodle_url('/local/report_completion/index.php',
                                         $params + array('userid' => $userid,
                                         'rowid' => $rowid,
                                         'confirm' => $delete,
                                         'courseid' => $courseid,
                                         'action' => $action,
                                         'sesskey' => sesskey()
                                         ));
            $cancel = new moodle_url('/local/report_completion/index.php',
                                     $params);
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
            die;
        } else {
            $confirmurl = new moodle_url('/local/report_completion/index.php',
                                         $params +
                                         array('userid' => $userid,
                                         'rowid' => $rowid,
                                         'confirm' => $redocertificate,
                                         'redocertificate' => $redocertificate,
                                         'courseid' => $courseid,
                                         'action' => $action,
                                         'sesskey' => sesskey()
                                         ));
            $cancel = new moodle_url('/local/report_completion/index.php', $params);
            echo $OUTPUT->confirm(get_string('redocertificateconfirm', 'local_report_users'), $confirmurl, $cancel);
        }
    }
}

// get output renderer
$output = $PAGE->get_renderer('block_iomad_company_admin');

// Set the companyid
if ($viewchildren && $canseechildren && !empty($departmentid) && company::can_manage_department($departmentid)) {
    $departmentrec = $DB->get_record('department', ['id' => $departmentid]);
    $realcompanyid = $companyid;
    $companyid = $departmentrec->company;
    $realcompany = $company;
    $selectedcompany = new company($companyid);
} else {
    $realcompanyid = $companyid;
    $realcompany = $company;
}

$haschildren = false;
if ($childcompanies = $realcompany->get_child_companies_recursive()) {
    $childcompanies[$realcompany->id] = (array) $realcompany;
    $haschildren = true;
} else {
    $showsummary = false;
}

// Work out department level.
$company = new company($companyid);
if ($viewchildren && $canseechildren) {
    $parentlevel = company::get_company_parentnode($realcompany->id);
} else {
    $parentlevel = company::get_company_parentnode($company->id);
}
$companydepartment = $parentlevel->id;

// Work out where the user sits in the company department tree.
if (\iomad::has_capability('block/iomad_company_admin:edit_all_departments', $companycontext)) {
    $userlevels = array($parentlevel->id => $parentlevel->id);
} else {
    $userlevels = $company->get_userlevel($USER);
}

$userhierarchylevel = key($userlevels);
if ($departmentid == 0 ) {
    $departmentid = $userhierarchylevel;
}

// Get the company additional optional user parameter names.
$foundobj = iomad::add_user_filter_params($params, $companyid);
$idlist = $foundobj->idlist;
$foundfields = $foundobj->foundfields;

$url = new moodle_url('/local/report_completion/index.php', $params);
$selectparams = $params;
$selecturl = new moodle_url('/local/report_completion/index.php', $selectparams);

// Set up the user search parameters.
if ($courseid == 1) {
    $searchinfo = iomad::get_user_sqlsearch($params, $idlist, $sort, $dir, $departmentid, true, true);
} else {
    $searchinfo = iomad::get_user_sqlsearch($params, $idlist, $sort, $dir, $departmentid, false, false);
}

// Create data for filter form.
$customdata = null;

// Check the department is valid.
if (!empty($departmentid) && !company::check_valid_department($companyid, $departmentid)) {
    print_error('invaliddepartment', 'block_iomad_company_admin');
}

// Are we showing the overview table?
if (empty($courseid)) {
    // Set up the course display table.
    $coursetable = new \local_report_completion\tables\course_table('local_report_completion_course_table');
    $coursetable->is_downloading($download, format_string($company->get('name')) . ' course completion report all courses', 'local_report_coursecompletion_course123');

    if (!$coursetable->is_downloading()) {
        // Display the control buttons.
        $buttons = "";
        $alluserslink = new moodle_url($url, array(
            'courseid' => 1,
            'departmentid' => $departmentid,
        ));
        $buttons = $output->single_button($alluserslink, get_string("allusers", 'local_report_completion'));

        // Also for suspended user controls.
        $showsuspendedparams = $params;
        if (!$showsuspended) {
            $showsuspendedparams['showsuspended'] = 1;
            $suspendeduserslink = new moodle_url($url, $showsuspendedparams);
            $buttons = $buttons ."&nbsp" . $output->single_button($suspendeduserslink, get_string("showsuspendedusers", 'local_report_completion'));
        } else {
            $showsuspendedparams['showsuspended'] = 0;
            $suspendeduserslink = new moodle_url($url, $showsuspendedparams);
            $buttons = $buttons ."&nbsp" . $output->single_button($suspendeduserslink, get_string("hidesuspendedusers", 'local_report_completion'));
        }

        // Also for percentage of user controls.
        $showpercentageoptions= [get_string("hidepercentageusers", 'block_iomad_company_admin'),
                                 get_string("showpercentageusers", 'block_iomad_company_admin'),
                                 get_string("showpercentagecourseusers", 'block_iomad_company_admin')];
        $percentageuserslink = new moodle_url($url, $params);
        $percentageselect = new single_select($percentageuserslink, 'showpercentage', $showpercentageoptions, $showpercentage);

        $buttons = $buttons ."&nbsp" . $output->render($percentageselect);

        // Also for validonly courses user controls.
        $validonlyparams = $params;
        $validonlyparams['validonly'] = !$validonly;
        if (!$validonly) {
            $validonlystring = get_string('hidevalidcourses', 'block_iomad_company_admin');
        } else {
            $validonlystring = get_string('showvalidcourses', 'block_iomad_company_admin');
        }
        $validonlylink = new moodle_url($url, $validonlyparams);
        $buttons = $buttons ."&nbsp" . $output->single_button($validonlylink, $validonlystring);

        // Also for Summary courses user controls.
        if ($viewchildren && $canseechildren) {
            $showsummaryparams = $params;
            $showsummaryparams['showsummary'] = !$showsummary;
            if ($showsummary) {
                $showsummarystring = get_string('showcompanydetail', 'block_iomad_company_admin');
            } else {
                $showsummarystring = get_string('showcompanysummary', 'block_iomad_company_admin');
            }
            $showsummarylink = new moodle_url($url, $showsummaryparams);
        $buttons = $buttons ."&nbsp" . $output->single_button($showsummarylink, $showsummarystring);
        }

        // Also for validonly courses user controls.
        $showchartsparams = $params;
        $showchartsparams['showcharts'] = !$showcharts;
        if (!$showcharts) {
            $showchartsstring = get_string('showcharts', 'block_iomad_company_admin');
        } else {
            $showchartsstring = get_string('showdata', 'block_iomad_company_admin');
        }
        $showchartslink = new moodle_url($url, $showchartsparams);
        $buttons = $buttons ."&nbsp" . $output->single_button($showchartslink, $showchartsstring);

        $mform = new iomad_course_search_form($url, $params);
        $mform->set_data($params);

        // Set up the date filter form.
        $datemform = new iomad_date_filter_form($url, $params);
        $datemform->set_data(array('departmentid' => $departmentid));
        $options = $params;
        $options['compfromraw'] = $from;
        $options['comptoraw'] = $to;
        $datemform->set_data($options);
        $datemform->get_data();


        // Display the controls.
        $PAGE->set_button($buttons);

        // Display the header.
        echo $output->header();

        // Display the department selector.
        $selectorparams['showsummary'] = false;
        echo $output->display_tree_selector($company, $parentlevel, $selecturl, $selectparams, $departmentid, $viewchildren);
        echo html_writer::start_tag('div', array('class' => 'reporttablecontrols', 'style' => 'padding-left: 15px'));
        echo html_writer::start_tag('div', array('class' => 'reporttablecontrolscontrol'));
        $mform->display();
        echo html_writer::end_tag('div');
        echo html_writer::start_tag('div', array('class' => 'reporttablecontrolscontrol', 'style' => 'padding-left: 30px'));
        $datemform->display();
        echo html_writer::end_tag('div');
        echo html_writer::end_tag('div');
    }

    // Deal with any course searches.
    $searchparams = array();
    if (!empty($coursesearch)) {
        $coursesearchsql = " AND courseid IN (" . join(',', array_keys($company->get_menu_courses(true))) . ") AND " . $DB->sql_like('coursename', ':coursename', false, false);
        $searchparams['coursename'] = "%" . $coursesearch . "%";
    } else {
        $coursesearchsql = " AND courseid IN (" . join(',', array_keys($company->get_menu_courses(true))) . ") ";
    }

    // Set up the SQL for the table.
    $selectsql = "courseid as id, coursename, $departmentid AS departmentid, $showsuspended AS showsuspended, companyid";
    $fromsql = "{local_iomad_track}";
    $sqlparams = array('companyid' => $companyid) + $searchparams;

    $wheresql = "companyid = :companyid $coursesearchsql group by courseid, coursename, companyid";

    // Set up the headers.
    $courseheaders = [get_string('coursename', 'local_report_completion')];
    $coursecolumns = ['coursename'];

    // Set up the rest of the headers for the table.
    $haslicenses = !empty($DB->count_records_sql("SELECT COUNT(id)
                                                  FROM {local_iomad_track}
                                                  WHERE courseid IN (
                                                     SELECT courseid FROM {iomad_courses}
                                                     WHERE licensed = 1)
                                                  $coursesearchsql",
                                                  $sqlparams));
    if (iomad::has_capability('block/iomad_company_admin:licensemanagement_view', $companycontext) &&
        $haslicenses) {
        if ($showcharts) {
            $courseheaders[] = get_string('licenseallocated', 'local_report_user_license_allocations');
            $courseheaders[] = get_string('usersummary', 'local_report_completion');
            $coursecolumns[] = 'licenseallocated';
            $coursecolumns[] = 'usersummary';
        } else {
            $courseheaders[] = get_string('licenseallocated', 'local_report_user_license_allocations');
            $courseheaders[] = get_string('licenseused', 'block_iomad_company_admin');
            $coursecolumns[] = 'licenseuserallocated';
            $coursecolumns[] = 'licenseuserused';
        }
    } else {
        if ($showcharts) {
            $courseheaders[] = get_string('usersummary', 'local_report_completion');
            $coursecolumns[] = 'usersummary';
        } else {
            $courseheaders[] = get_string('started', 'question');
            $courseheaders[] = get_string('inprogressusers', 'local_report_completion');
            $courseheaders[] = get_string('completedusers', 'local_report_completion');
            $coursecolumns[] = 'userstarted';
            $coursecolumns[] = 'userinprogress';
            $coursecolumns[] = 'usercompleted';
        }
    }

    $coursetable->set_sql($selectsql, $fromsql, $wheresql, $sqlparams);
    $coursetable->define_baseurl($url);
    $coursetable->define_columns($coursecolumns);
    $coursetable->define_headers($courseheaders);
    $coursetable->no_sorting('licenseallocated');
    $coursetable->no_sorting('usersummary');
    $coursetable->sort_default_column = 'coursename';
    $coursetable->out($CFG->iomad_max_list_users, true);

    if (!$coursetable->is_downloading()) {
        echo $output->footer();
    }
} else {
    // Do we have any additional reporting fields?
    $extrafields = array();
    if (!empty($CFG->iomad_report_fields)) {
        $companyrec = $DB->get_record('company', array('id' => $companyid));
        foreach (explode(',', $CFG->iomad_report_fields) as $extrafield) {
            $extrafields[$extrafield] = new stdclass();
            $extrafields[$extrafield]->name = $extrafield;
            if (strpos($extrafield, 'profile_field') !== false) {
                // Its an optional profile field.
                $profilefield = $DB->get_record('user_info_field', array('shortname' => str_replace('profile_field_', '', $extrafield)));
                if ($profilefield->categoryid == $companyrec->profileid ||
                    !$DB->get_record('company', array('profileid' => $profilefield->categoryid))) {
                    $extrafields[$extrafield]->title = $profilefield->name;
                    $extrafields[$extrafield]->fieldid = $profilefield->id;
                } else {
                    unset($extrafields[$extrafield]);
                }
            } else {
                $extrafields[$extrafield]->title = get_string($extrafield);
            }
        }
    }

    // Set up the display table.
    $table = new \local_report_completion\tables\user_table('local_report_course_completion_user_table');
    if ($courseid == 1) {
        $table->is_downloading($download, format_string($company->get('name')) . ' course completion report ' . format_string($SITE->fullname), 'local_report_coursecompletion_course123');
    } else {
        $table->is_downloading($download, format_string($company->get('name')) . ' course completion report ' . format_string($course->fullname), 'local_report_coursecompletion_course123');
    }

    // Deal with sort by course for all courses if sort is empty.
    if (empty($sort) && $courseid == 1) {
        $table->sort_default_column = 'coursename';
    }

    // Set defaults for extra columns/headers.
    $completionheaders = [];
    $completioncolumns = [];
    $gradeheaders = [];
    $gradecolumns = [];
    $completionids = [];
    $completionsqlfrom = "";
    $completionsqlselect = "";

    // Get the completion information if we need it.
    if ($table->is_downloading() && $courseid != 1) {
        // Get the course completion criteria.
        $info = new completion_info(get_course($courseid));
        $coursecompletioncrits = $info->get_criteria(null);

        // Set up the additional columns.
        if (!empty($coursecompletioncrits)) {
            foreach ($coursecompletioncrits as $completioncrit) {
                $modinfo = get_coursemodule_from_id('', $completioncrit->moduleinstance);
                
                $completionheaders[$completioncrit->id] = format_string($completioncrit->get_title() . " " . $modinfo->name);
                $gradeheaders[$completioncrit->id] = format_string(get_string('grade') . " " . $modinfo->name);
                $completioncolumns[$completioncrit->id] = "criteria_" . $completioncrit->id;
                $gradecolumns[$completioncrit->id] = "grade_" . $completioncrit->id;
                $completionids[] = $completioncrit->id;
            }
        }
    }

    $sqlparams = array('companyid' => $companyid, 'courseid' => $courseid);

    // Deal with where we are on the department tree.
    $currentdepartment = company::get_departmentbyid($departmentid);
    $showdepartments = company::get_subdepartments_list($currentdepartment);
    $showdepartments[$departmentid] = $departmentid;
    $departmentsql = " AND d.id IN (" . implode(',', array_keys($showdepartments)) . ")";

    // all companies?
    if (!$viewchildren && !$canseechildren && $parentslist = $company->get_parent_companies_recursive()) {
        $companysql = " AND u.id NOT IN (
                        SELECT userid FROM {company_users}
                        WHERE companyid IN (" . implode(',', array_keys($parentslist)) ."))";
    } else if ($showsummary) {
        // Deal with the company list..
        $companysql = " AND lit.companyid IN (" . implode(',', array_keys($childcompanies)) . ")";
    } else {
        $companysql = " AND lit.companyid = :companyid";
    }

    // All courses or just the one?
    if ($courseid != 1) {
        $coursesql = " AND lit.courseid = :courseid ";
    } else {
        $coursesql = " AND lit.courseid IN (" . join(',', array_keys($company->get_menu_courses(true))) . ") ";
    }

    // Deal with any search dates.
    $datesql = "";
    if (!empty($params['from'])) {
        $datesql = " AND (lit.timeenrolled > :enrolledfrom OR lit.timecompleted > :completedfrom ) ";
        $sqlparams['enrolledfrom'] = $params['from'];
        $sqlparams['completedfrom'] = $params['from'];
    }
    if (!empty($params['to'])) {
        $datesql .= " AND (lit.timeenrolled < :enrolledto OR lit.timecompleted < :completedto) ";
        $sqlparams['enrolledto'] = $params['to'];
        $sqlparams['completedto'] = $params['to'];
    }

    // Just valid courses?
    if ($validonly) {
        $validsql = " AND (lit.timeexpires > :runtime or (lit.timecompleted IS NULL) or (lit.timecompleted > 0 AND lit.timeexpires IS NULL))";
        $sqlparams['runtime'] = time();
    } else {
        $validsql = "";
    }

    // Set up the initial SQL for the form.
    $userfields = \core_user\fields::for_name()->with_identity($systemcontext)->excluding('id', 'deleted');
    $fieldsql = $userfields->get_sql('u');
    $selectsql = "DISTINCT lit.id,
                  u.id as userid,
                  u.email,
                  lit.id as certsource,
                  lit.courseid,
                  lit.coursename,
                  lit.timecompleted,
                  lit.timeenrolled,
                  lit.timestarted,
                  lit.timeexpires,
                  lit.finalscore,
                  lit.licenseid,
                  lit.licensename,
                  lit.licenseallocated,
                  lit.companyid,
                  lit.coursecleared,
                  lit.modifiedtime
                  {$fieldsql->selects} $completionsqlselect";
    $fromsql = "{user} u JOIN {local_iomad_track} lit ON (u.id = lit.userid) JOIN {company_users} cu ON (u.id = cu.userid AND lit.userid = cu.userid AND lit.companyid = cu.companyid) JOIN {department} d ON (cu.departmentid = d.id) $completionsqlfrom";
    $wheresql = $searchinfo->sqlsearch . " AND 1=1 $departmentsql $companysql $datesql $coursesql $validsql";
    $sqlparams = $sqlparams + $searchinfo->searchparams;

    // Are we showing this rolled up?
    if ($haschildren && $showsummary) {
        $headers = [get_string('company', 'block_iomad_company_admin')];
        $columns = ['company'];
    } else {
        $headers = [];
        $columns = [];
    }

    // Set up the headers for the form.
    $headers[] = get_string('fullname');
    $headers[] = get_string('department', 'block_iomad_company_admin');
    $headers[] = get_string('email');

    $columns[] = 'fullname';
    $columns[] = 'department';
    $columns[] = 'email';

    // Are we showing this rolled up?
    if ($haschildren && $showsummary) {
        $headers = [get_string('company', 'block_iomad_company_admin')] + $headers;
        $columns = ['company'] + $columns;
    }

    if (empty($USER->editing)) {
        // Deal with optional report fields.
        if (!empty($extrafields)) {
            foreach ($extrafields as $extrafield) {
                $headers[] = $extrafield->title;
                $columns[] = $extrafield->name;
                if (!empty($extrafield->fieldid)) {
                    // Its a profile field.
                    // Skip it this time as these may not have data.
                } else {
                    $selectsql .= ", u." . $extrafield->name;
                }
            }
            foreach ($extrafields as $extrafield) {
                if (!empty($extrafield->fieldid)) {
                    // Its a profile field.
                    $selectsql .= ", P" . $extrafield->fieldid . ".data AS " . $extrafield->name;
                    $fromsql .= " LEFT JOIN {user_info_data} P" . $extrafield->fieldid . " ON (u.id = P" . $extrafield->fieldid . ".userid AND P".$extrafield->fieldid . ".fieldid = :p" . $extrafield->fieldid . "fieldid )";
                    $sqlparams["p".$extrafield->fieldid."fieldid"] = $extrafield->fieldid;
                }
            }
        }
    }

    // Are we showing all courses?
    if ($courseid == 1) {
        $headers[] = get_string('course');
        $columns[] = 'coursename';
    }

    // Status column.
    $headers[] =  get_string('status');
    $columns[] = 'status';

    // Is this licensed?
    if ($courseid == 1 ||
        $DB->get_record('iomad_courses', array('courseid' => $courseid, 'licensed' => 1)) ||
        $DB->count_records_sql("SELECT count(id) FROM {local_iomad_track}
                                WHERE courseid = :courseid
                                AND licensename IS NOT NULL",
                                array('courseid' => $courseid)) > 0) {
        // Need to add the license columns
        if (empty($USER->editing)) {
            $headers[] = get_string('licensename', 'block_iomad_company_admin');
            $columns[] = 'licensename';
        }
        $headers[] = get_string('licensedateallocated', 'block_iomad_company_admin');
        $columns[] = 'licenseallocated';
    }

    // And enrolment columns.
    $headers[] = get_string('timestarted', 'local_report_completion');
    $headers[] = get_string('timecompleted', 'local_report_completion');
    $columns[] = 'timeenrolled';
    $columns[] = 'timecompleted';

    // Does this course have an expiry time?
    if (($courseid == 1 && $DB->get_records_sql("SELECT id FROM {iomad_courses} WHERE courseid IN (SELECT courseid FROM {local_iomad_track} WHERE companyid = :companyid) AND expireafter != 0", array('companyid' => $company->id))) ||
        $DB->get_record_sql("SELECT id FROM {iomad_courses} WHERE courseid = :courseid AND validlength > 0", array('courseid' => $courseid))) {
        $columns[] = 'timeexpires';
        $headers[] = get_string('timeexpires', 'local_report_completion');
    }

    // Does this course have an visible grade?
    if (($courseid == 1 && $DB->get_records_sql("SELECT id FROM {iomad_courses} WHERE courseid IN (SELECT courseid FROM {local_iomad_track} WHERE companyid = :companyid) AND hasgrade = 1", array('companyid' => $company->id))) ||
        $DB->get_record_sql("SELECT id FROM {iomad_courses} WHERE courseid = :courseid AND hasgrade = 1", array('courseid' => $courseid))) {
        $columns[] = 'finalscore';
        $headers[] = get_string('grade', 'grades');
    }

    // And finally the last of the columns.
    if (!$table->is_downloading()) {
        $headers[] = get_string('certificate', 'local_report_completion');
        $columns[] = 'certificate';
        $headers[] = get_string('actions');
        $columns[] = 'actions';
    } else if ($courseid != 1) {
        foreach ($completionids as $completionid) {
            $headers[] = $completionheaders[$completionid];
            $columns[] = $completioncolumns[$completionid];
            $headers[] = $gradeheaders[$completionid];
            $columns[] = $gradecolumns[$completionid];
        }
        $headers[] = get_string('lastmodified');
        $columns[] = 'modifiedtime';
    }

    // Also for Summary courses user controls.
    if ($viewchildren && $canseechildren) {
        $showsummaryparams = $params;
        $showsummaryparams['showsummary'] = !$showsummary;
        if ($showsummary) {
            $showsummarystring = get_string('showcompanydetail', 'block_iomad_company_admin');
        } else {
            $showsummarystring = get_string('showcompanysummary', 'block_iomad_company_admin');
        }
        $showsummarylink = new moodle_url($url, $showsummaryparams);
        $buttons = $output->single_button($showsummarylink, $showsummarystring) . "&nbsp" .$buttons;
    }

    // Also for percentage of user controls.
    $showpercentageoptions= [get_string("hidepercentageusers", 'block_iomad_company_admin'),
                             get_string("showpercentageusers", 'block_iomad_company_admin'),
                             get_string("showpercentagecourseusers", 'block_iomad_company_admin')];
    $percentageuserslink = new moodle_url($url, $params);
    $percentageselect = new single_select($percentageuserslink, 'showpercentage', $showpercentageoptions, $showpercentage);

    $buttons = $output->render($percentageselect) . "&nbsp" .$buttons;;

    $total = $DB->count_records_sql("SELECT count(DISTINCT lit.id) FROM $fromsql WHERE $wheresql", $sqlparams);
    $totalcompleted = $DB->count_records_sql("SELECT count(DISTINCT lit.id) FROM $fromsql WHERE lit.timecompleted > 0 AND $wheresql", $sqlparams);
    $totalstarted = $DB->count_records_sql("SELECT count(DISTINCT lit.id) FROM $fromsql WHERE lit.timecompleted > 0 AND $wheresql", $sqlparams);

    if ($showpercentage == 2) {
        if (!empty($total)) {
            $totalstarted = get_string('percents','moodle', number_format($totalstarted * 100 / $total,2 )); 
            $totalcompleted = get_string('percents', 'moodle', number_format($totalcompleted * 100 / $total, 2));
        } 
    } else if ($showpercentage == 1) {
        $totalcompanyusers = $DB->count_records_sql("SELECT count(DISTINCT lit.userid) FROM {company_users} lit
                                                     JOIN {user} u ON (lit.userid = u.id)
                                                     JOIN {department} d ON (lit.departmentid = d.id)
                                                     WHERE 1=1 $companysql $departmentsql",
                                                     $sqlparams);
        if (!empty($total)) {
            $total = get_string('percents','moodle', number_format($total * 100 / $totalcompanyusers, 2)); 
            $totalstarted = get_string('percents','moodle', number_format($totalstarted * 100 / $totalcompanyusers, 2)); 
            $totalcompleted = get_string('percents', 'moodle', number_format($totalcompleted * 100 / $totalcompanyusers, 2));
        }
        
    }
    $summarystring = get_string('usercoursetotal', 'block_iomad_company_admin', (object) ['total' => $total, 'totalstarted' => $totalstarted, 'totalcompleted' => $totalcompleted]);
    $buttons = $summarystring . "&nbsp $buttons";
    $PAGE->set_button($buttons);

    if (!$table->is_downloading()) {
        echo $output->header();

        // Display the search form and department picker.
        if (!empty($companyid)) {
            if (empty($table->is_downloading())) {
                echo $output->display_tree_selector($company, $parentlevel, $selecturl, $selectparams, $departmentid);

                // Set up the filter form.
                $options = $params;
                $options['companyid'] = $companyid;
                $options['addfrom'] = 'compfromraw';
                $options['addto'] = 'comptoraw';
                $options['adddodownload'] = false;
                $options['compfromraw'] = $from;
                $options['comptoraw'] = $to;
                $options['addvalidonly'] = true;
                $mform = new iomad_user_filter_form(null, $options);
                $mform->set_data(array('departmentid' => $departmentid, 'validonly' => $validonly));
                $mform->set_data($options);
                $mform->get_data();

                // Display the user filter form.
                $mform->display();
            }
        }
    }

    // Set up the form.
    if (!empty($USER->editing) && !$table->is_downloading()) {
        echo html_writer::start_tag('form', array('action' => $url,
                                                  'enctype' => 'application/x-www-form-urlencoded',
                                                  'method' => 'post',
                                                  'name' => 'iomad_report_user_userdisplay_values',
                                                  'id' => 'iomad_report_user_userdisplay_values'));
        echo "<input type='hidden' name='sesskey' value=" . sesskey() .">";
        echo "<input type='hidden' name='download' value=''>";
        echo html_writer::start_tag('div', array('class' => 'iomadclear'));
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
        echo html_writer::end_tag('div');
        echo html_writer::end_tag('div');
        echo html_writer::start_tag('div', array('class' => 'iomadclear'));

    }  

    // Set up the table and display it.
    $table->set_sql($selectsql, $fromsql, $wheresql, $sqlparams);
    $countsql = "SELECT count(DISTINCT lit.id) FROM $fromsql WHERE $wheresql";
    $table->set_count_sql($countsql, $sqlparams);
    $table->define_baseurl($url);
    $table->define_columns($columns);
    $table->define_headers($headers);
    $table->no_sorting('status');
    $table->no_sorting('certificate');
    $table->sort_default_column = 'lastname';
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
            echo "<input type = 'submit' id='purge_all_selected' name='purge_selected_entries' value = '" . get_string('purgeselectedentries', 'block_iomad_company_admin') . "' class='btn btn-secondary'>";
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
}