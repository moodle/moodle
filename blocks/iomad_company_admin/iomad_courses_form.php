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

require_once('../../config.php');
require_once(dirname(__FILE__) . '/../../config.php'); // Creates $PAGE.
require_once(dirname('__FILE__').'/lib.php');
require_once(dirname('__FILE__').'/iomad_courses_table.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/user/filters/lib.php');
require_once($CFG->dirroot.'/blocks/iomad_company_admin/lib.php');

$companyid    = optional_param('companyid', 0, PARAM_CLEAN);
$coursesearch      = optional_param('coursesearch', '', PARAM_CLEAN);// Search string.
$courseid = optional_param('courseid', 0, PARAM_INTEGER);
$update = optional_param('update', null, PARAM_ALPHA);
$license = optional_param('license', 0, PARAM_INTEGER);
$shared = optional_param('shared', 0, PARAM_INTEGER);
$validfor = optional_param('validfor', 0, PARAM_INTEGER);
$warnnotstarted = optional_param('warnnotstarted', 0, PARAM_INTEGER);
$warnexpire = optional_param('warnexpire', 0, PARAM_INTEGER);
$warncompletion = optional_param('warncompletion', 0, PARAM_INTEGER);
$notifyperiod = optional_param('notifyperiod', 0, PARAM_INTEGER);
$expireafter = optional_param('expireafter', 0, PARAM_INTEGER);
$hasgrade = optional_param('hasgrade', 1, PARAM_INTEGER);
$deleteid = optional_param('deleteid', 0, PARAM_INT);
$confirm = optional_param('confirm', null, PARAM_ALPHANUM);

$params = array();

$params['companyid'] = $companyid;
$params['coursesearch'] = $coursesearch;
if ($courseid) {
    $params['courseid'] = $courseid;
}

$systemcontext = context_system::instance();
require_login();
iomad::require_capability('block/iomad_company_admin:viewcourses', $systemcontext);

if (iomad::has_capability('block/iomad_company_admin:managecourses', $systemcontext)
    || iomad::has_capability('block/iomad_company_admin:manageallcourses', $systemcontext)) {
    $canedit = true;
} else {
    $canedit = false;
}

if (iomad::has_capability('block/iomad_company_admin:manageallcourses', $systemcontext)) {
    $caneditall = true;
} else {
    $caneditall = false;
}

// Set the url.
$linkurl = new moodle_url('/blocks/iomad_company_admin/iomad_courses_form.php');
$linktext = get_string('iomad_courses_title', 'block_iomad_company_admin');

// Print the page header.
$PAGE->set_context($systemcontext);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($linktext);

// Set the page heading.
$PAGE->set_heading(get_string('myhome') . " - $linktext");
if (empty($CFG->defaulthomepage)) {
    $PAGE->navbar->add(get_string('dashboard', 'block_iomad_company_admin'), new moodle_url($CFG->wwwroot . '/my'));
}
$PAGE->navbar->add($linktext, $linkurl);

// Set the companyid
$mycompanyid = iomad::get_my_companyid($systemcontext, false);

// Is the users company set and no other company selected?
if (empty($companyid) && !empty($mycompanyid)) {
    $companyid = $mycompanyid;
    $params['companyid'] = $mycompanyid;
}

if (!empty($update)) {
    // Need to change something.
    if (!$coursedetails = (array) $DB->get_record('iomad_courses', array('courseid' => $courseid))) {
        print_error(get_string('invaliddetails', 'block_iomad_company_admin'));
    } else {
        // Keep the original details for the event.
        $originaldetails = $coursedetails;
        if ('license' == $update) {
            if ($license == 3) {
                $coursedetails['licensed'] = 0;
            } else {
                $coursedetails['licensed'] = $license;
            }
            $DB->update_record('iomad_courses', $coursedetails);
            if (empty($license) || $license == 3) {
                // Changing to manual enrolment type only.
                if ($instances = $DB->get_records('enrol', array('courseid' => $courseid))) {
                    foreach ($instances as $instance) {
                        $updateinstance = (array) $instance;
                        if ($license == 0) {
                            if ($instance->enrol != 'manual') {
                                $updateinstance['status'] = 1;
                            } else {
                                $updateinstance['status'] = 0;
                            }
                        } else if ($license == 3) {
                            if ($instance->enrol == 'manual' || $instance->enrol == 'self') {
                                $updateinstance['status'] = 0;
                            } else {
                                $updateinstance['status'] = 1;
                            }
                        }
                        $DB->update_record('enrol', $updateinstance);
                    }
                }
            } else {
                // Changing to license enrolment type only.
                if ($instances = $DB->get_records('enrol', array('courseid' => $courseid))) {
                    $gotlicense = false;
                    foreach ($instances as $instance) {
                        $updateinstance = (array) $instance;
                        if ($instance->enrol != 'license') {
                            $updateinstance['status'] = 1;
                        } else {
                            $updateinstance['status'] = 0;
                            $gotlicense = true;
                        }
                        $DB->update_record('enrol', $updateinstance);
                    }
                    if (!$gotlicense) {
                        $courserecord = $DB->get_record('course', array('id' => $courseid));
                        $plugin = enrol_get_plugin('license');
                        $plugin->add_instance($courserecord, array('status' => 0,
                                                                   'name' => '',
                                                                   'password' => null,
                                                                   'customint1' => 0,
                                                                   'customint2' => 0,
                        'customint3' => 0, 'customint4' => 0, 'customtext1' => '',
                        'roleid' => 5, 'enrolperiod' => 0, 'enrolstartdate' => 0, 'enrolenddate' => 0));
                    }
                }
            }
        } else if ('shared' == $update) {
            $previousshared = $coursedetails['shared'];
            // Check if we are sharing a course for the first time.
            if ($previousshared == 0 && $shared != 0) { // Turning sharing on.
                $courseinfo = $DB->get_record('course', array('id' => $courseid));
                // Set the shared options on.
                $courseinfo->groupmode = 1;
                $courseinfo->groupmodeforce = 1;
                $DB->update_record('course', $courseinfo);
                $coursedetails['shared'] = $shared;
                $DB->update_record('iomad_courses', $coursedetails);
                // Deal with any current enrolments.
                if ($companycourse = $DB->get_record('company_course', array('courseid' => $courseid))) {
                    if ($shared == 2) {
                        $sharingrecord = new stdclass();
                        $sharingrecord->courseid = $courseid;
                        $sharingrecord->companyid = $companycourse->companyid;
                        $DB->insert_record('company_shared_courses', $sharingrecord);
                    }
                    company::company_users_to_company_course_group($companycourse->companyid, $courseid);
                }
            } else if ($shared == 0 and $previousshared != 0) { // Turning sharing off.
                $courseinfo = $DB->get_record('course', array('id' => $courseid));
                // Set the shared options on.
                $courseinfo->groupmode = 0;
                $courseinfo->groupmodeforce = 0;
                $DB->update_record('course', $courseinfo);
                $coursedetails['shared'] = $shared;
                $DB->update_record('iomad_courses', $coursedetails);
                // Deal with enrolments.
                if ($companygroups = $DB->get_records('company_course_groups', array('courseid' => $courseid))) {
                    // Got companies using it.
                    $count = 1;
                    // Skip the first company, it was the one who had it before anyone else so is
                    // assumed to be the owning company.
                    foreach ($companygroups as $companygroup) {
                        if ($count == 1) {
                            continue;
                        }
                        $count ++;
                        company::unenrol_company_from_course($companygroup->companyid, $courseid);
                    }
                }
            } else {  // Changing from open sharing to closed sharing.
                $coursedetails['shared'] = $shared;
                $DB->update_record('iomad_courses', $coursedetails);
                if ($companygroups = $DB->get_records('company_course_groups', array('courseid' => $courseid))) {
                    // Got companies using it.
                    foreach ($companygroups as $companygroup) {
                        $sharingrecord = new stdclass();
                        $sharingrecord->courseid = $courseid;
                        $sharingrecord->companyid = $companygroup->companyid;
                        $DB->insert_record('company_shared_courses', $sharingrecord);
                    }
                }
            }

        } else if ('validfor' == $update) {
            // Work out the time in seconds....
            if ($validfor < 0) {
                $validfor = 0;
            }
            $coursedetails['validlength'] = $validfor;
            $DB->update_record('iomad_courses', $coursedetails);
        } else if ('expireafter' == $update) {
            // Work out the time in seconds....
            if ($expireafter < 0) {
                $expireafter = 0;
            }
            $coursedetails['expireafter'] = $expireafter;
            $DB->update_record('iomad_courses', $coursedetails);
        } else if ('warnexpire' == $update) {
            // Work out the time in seconds....
            if ($warnexpire < 0) {
                $warnexpire = 0;
            }
            $coursedetails['warnexpire'] = $warnexpire;
            $DB->update_record('iomad_courses', $coursedetails);
        } else if ('warnnotstarted' == $update) {
            // Work out the time in seconds....
            if ($warnnotstarted < 0) {
                $warnnotstarted = 0;
            }
            $coursedetails['warnnotstarted'] = $warnnotstarted;
            $DB->update_record('iomad_courses', $coursedetails);
        } else if ('warncompletion' == $update) {
            // Work out the time in seconds....
            if ($warncompletion < 0) {
                $warncompletion = 0;
            }
            $coursedetails['warncompletion'] = $warncompletion;
            $DB->update_record('iomad_courses', $coursedetails);
        } else if ('notifyperiod' == $update) {
            // Work out the time in seconds....
            if ($notifyperiod < 0) {
                $notifyperiod = 0;
            }
            $coursedetails['notifyperiod'] = $notifyperiod;
            $DB->update_record('iomad_courses', $coursedetails);
        } else if ('hasgrade' == $update) {
            $coursedetails['hasgrade'] = $hasgrade;
            $DB->update_record('iomad_courses', $coursedetails);
        }
        // Fire an event for this.
        $eventother = array('iomadcourse' => $originaldetails);
        $event = \block_iomad_company_admin\event\company_course_updated::create(array('context' => context_system::instance(),
                                                                                       'objectid' => $courseid,
                                                                                       'userid' => $USER->id,
                                                                                       'other' => $eventother));
        $event->trigger();
    }
}

// Delete any valid departments.
if (!empty($deleteid)) {
    if (!$course = $DB->get_record('course', array('id' => $deleteid))) {
        print_error('invalidcourse');
    }
    if (confirm_sesskey() && $confirm == md5($deleteid)) {
        $destroy = optional_param('destroy', 0, PARAM_INT);
        // delete the course and all of the data.
        if (company::delete_course($companyid, $deleteid, $destroy)) {
            redirect($linkurl,
                get_string("deletecourse_successful", 'block_iomad_company_admin'),
                null,
                \core\output\notification::NOTIFY_SUCCESS);

        }
        die;
    } else {
        echo $OUTPUT->header();
        $confirmurl = new moodle_url('iomad_courses_form.php',
                                     array('confirm' => md5($deleteid),
                                           'deleteid' => $deleteid,
                                           'sesskey' => sesskey()
                                           ));
        $continue = new single_button($confirmurl, get_string('continue'), 'post', true);
        $destroyurl = new moodle_url('iomad_courses_form.php',
                                     array('confirm' => md5($deleteid),
                                           'deleteid' => $deleteid,
                                           'destroy' => true,
                                           'sesskey' => sesskey()
                                           ));
        $destroy = new single_button($destroyurl, get_string('destroy', 'block_iomad_company_admin'), 'post', true);
        $cancel = new single_button($linkurl, get_string('cancel'), 'post', true);

        $attributes = [
            'role'=>'alertdialog',
            'aria-labelledby'=>'modal-header',
            'aria-describedby'=>'modal-body',
            'aria-modal'=>'true'
        ];

        // Which message are we showing?
        if (iomad::has_capability('block/iomad_company_admin:destroycourses', $systemcontext)) {
            $message = get_string('deleteanddestroycoursesfull', 'block_iomad_company_admin', $course->fullname);
        } else {
            $message = get_string('deleteacoursesfull', 'block_iomad_company_admin', $course->fullname);
        }
        $confirmhtml = $OUTPUT->box_start('generalbox modal modal-dialog modal-in-page show', 'notice', $attributes);
        $confirmhtml .= $OUTPUT->box_start('modal-content', 'modal-content');
        $confirmhtml .= $OUTPUT->box_start('modal-header p-x-1', 'modal-header');
        $confirmhtml .= html_writer::tag('h4', get_string('confirm'));
        $confirmhtml .= $OUTPUT->box_end();
        $attributes = [
            'role'=>'alert',
            'data-aria-autofocus'=>'true'
        ];
        $confirmhtml .= $OUTPUT->box_start('modal-body', 'modal-body', $attributes);
        $confirmhtml .= html_writer::tag('p', $message);
        $confirmhtml .= $OUTPUT->box_end();
        $confirmhtml .= $OUTPUT->box_start('modal-footer', 'modal-footer');
        if (iomad::has_capability('block/iomad_company_admin:destroycourses', $systemcontext)) {
            $confirmhtml .= html_writer::tag('div', $OUTPUT->render($continue) . $OUTPUT->render($destroy) . $OUTPUT->render($cancel), array('class' => 'buttons'));
        } else {
            $confirmhtml .= html_writer::tag('div', $OUTPUT->render($continue) . $OUTPUT->render($cancel), array('class' => 'buttons'));
        }
        $confirmhtml .= $OUTPUT->box_end();
        $confirmhtml .= $OUTPUT->box_end();
        $confirmhtml .= $OUTPUT->box_end();

        echo $confirmhtml;
        echo $OUTPUT->footer();
        die;
    }
}
$baseurl = new moodle_url(basename(__FILE__), $params);
$returnurl = $baseurl;

$mform = new iomad_course_search_form($baseurl, $params);
$mform->set_data($params);

echo $OUTPUT->header();

// Get the list of companies and display it as a drop down select..
$companyids = company::get_companies_select(false);
if ($caneditall) {
    $companyids = [
            'none' => get_string('nocompany', 'block_iomad_company_admin'),
            'all' => get_string('allcourses', 'block_iomad_company_admin')
    ] + $companyids;
}

$companyselect = new single_select($linkurl, 'companyid', $companyids, $companyid);
$companyselect->label = get_string('filtercompany', 'block_iomad_company_admin');
    echo html_writer::start_tag('div', array('class' => 'reporttablecontrolscontrol'));
if ($canedit) {
    echo html_writer::tag('div', $OUTPUT->render($companyselect), array('id' => 'iomad_company_selector')).'</br>';
}
$mform->display();
echo html_writer::end_tag('div');
echo html_writer::start_tag('div', array('class' => 'iomadclear'));

$table = new iomad_courses_table('iomad_courses_table');

if ($companyid == 'all') {
    $companyid = 0;
}

$companysql = " 1 = 1";
$searchsql = "";
if (!empty($companyid)) {
    if ($companyid == "none") {
        $companysql = " c.id NOT IN (SELECT courseid FROM {company_course}) ";
    } else {
        $companysql = " (c.id IN (
                          SELECT courseid FROM {company_course}
                          WHERE companyid = :companyid)
                         OR ic.shared = 1) ";
    }
}

if (!empty($coursesearch)) {
    if (!empty($companysql)) {
        $searchsql = " AND ";
    }
    $searchsql .= $DB->sql_like('c.fullname', ':coursesearch', false, false);
    $params['coursesearch'] = "%" . $params['coursesearch'] ."%";
    $params['coursesearchtext'] = $coursesearch;
}

// Set up the SQL for the table.
$selectsql = "ic.id, c.id AS courseid, c.fullname AS coursename, ic.licensed, ic.shared, ic.validlength, ic.warnexpire, ic.warncompletion, ic.notifyperiod, ic.expireafter, ic.warnnotstarted, ic.hasgrade, '$companyid' AS companyid";
$fromsql = "{iomad_courses} ic JOIN {course} c ON (ic.courseid = c.id)";
$wheresql = "$companysql $searchsql";
$sqlparams = $params;

// Can we manage the courses or just see them?
if ($canedit) {
    // Set up the headers for the table.
    $tableheaders = array(
        get_string('company', 'block_iomad_company_admin'),
        get_string('course'),
        get_string('licensed', 'block_iomad_company_admin') . $OUTPUT->help_icon('licensed', 'block_iomad_company_admin'),
        get_string('shared', 'block_iomad_company_admin')  . $OUTPUT->help_icon('shared', 'block_iomad_company_admin'),
        get_string('validfor', 'block_iomad_company_admin') . $OUTPUT->help_icon('validfor', 'block_iomad_company_admin'),
        get_string('expireafter', 'block_iomad_company_admin') . $OUTPUT->help_icon('expireafter', 'block_iomad_company_admin'),
        get_string('warnexpire', 'block_iomad_company_admin') . $OUTPUT->help_icon('warnexpire', 'block_iomad_company_admin'),
        get_string('warnnotstarted', 'block_iomad_company_admin') . $OUTPUT->help_icon('warnnotstarted', 'block_iomad_company_admin'),
        get_string('warncompletion', 'block_iomad_company_admin') . $OUTPUT->help_icon('warncompletion', 'block_iomad_company_admin'),
        get_string('notifyperiod', 'block_iomad_company_admin') . $OUTPUT->help_icon('notifyperiod', 'block_iomad_company_admin'),
        get_string('hasgrade', 'block_iomad_company_admin') . $OUTPUT->help_icon('hasgrade', 'block_iomad_company_admin'),
        get_string('actions'));
    $tablecolumns = array('company',
                          'coursename',
                          'licensed',
                          'shared',
                          'validlength',
                          'expireafter',
                          'warnexpire',
                          'warnnotstarted',
                          'warncompletion',
                          'notifyperiod',
                          'hasgrade',
                          'actions');
} else {
// Set up the headers for the table.
$tableheaders = array(
    get_string('course'),
    get_string('licensed', 'block_iomad_company_admin') . $OUTPUT->help_icon('licensed', 'block_iomad_company_admin'),
    get_string('validfor', 'block_iomad_company_admin') . $OUTPUT->help_icon('validfor', 'block_iomad_company_admin'),
    get_string('expireafter', 'block_iomad_company_admin') . $OUTPUT->help_icon('expireafter', 'block_iomad_company_admin'),
    get_string('warnexpire', 'block_iomad_company_admin') . $OUTPUT->help_icon('warnexpire', 'block_iomad_company_admin'),
    get_string('warnnotstarted', 'block_iomad_company_admin') . $OUTPUT->help_icon('warnnotstarted', 'block_iomad_company_admin'),
    get_string('warncompletion', 'block_iomad_company_admin') . $OUTPUT->help_icon('warncompletion', 'block_iomad_company_admin'),
    get_string('notifyperiod', 'block_iomad_company_admin') . $OUTPUT->help_icon('notifyperiod', 'block_iomad_company_admin'),
    get_string('hasgrade', 'block_iomad_company_admin') . $OUTPUT->help_icon('hasgrade', 'block_iomad_company_admin'),
        get_string('actions'));
$tablecolumns = array('coursename',
                      'licensed',
                      'validlength',
                      'expireafter',
                      'warnexpire',
                      'warnnotstarted',
                      'warncompletion',
                      'notifyperiod',
                      'hasgrade',
                      'actions');
}
$table->set_sql($selectsql, $fromsql, $wheresql, $sqlparams);
$table->define_baseurl($baseurl);
$table->define_columns($tablecolumns);
$table->define_headers($tableheaders);
$table->sort_default_column = 'coursename';
$table->no_sorting('company');
$table->out($CFG->iomad_max_list_courses, true);

echo html_writer::end_tag('div');

echo $OUTPUT->footer();
