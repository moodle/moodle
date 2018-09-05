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
require_once(dirname('__FILE__').'/lib.php');
require_once(dirname(__FILE__) . '/../../config.php'); // Creates $PAGE.
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/user/filters/lib.php');
require_once($CFG->dirroot.'/blocks/iomad_company_admin/lib.php');

$company       = optional_param('company', 0, PARAM_CLEAN);
$sort         = optional_param('sort', 'name', PARAM_ALPHA);
$dir          = optional_param('dir', 'ASC', PARAM_ALPHA);
$page         = optional_param('page', 0, PARAM_INT);
$perpage      = optional_param('perpage', 30, PARAM_INT);        // How many per page.
$acl          = optional_param('acl', '0', PARAM_INT);           // Id of user to tweak mnet ACL (requires $access).
$search      = optional_param('search', '', PARAM_CLEAN);// Search string.
$courseid = optional_param('courseid', 0, PARAM_INTEGER);
$update = optional_param('update', null, PARAM_ALPHA);
$license = optional_param('license', 0, PARAM_INTEGER);
$shared = optional_param('shared', 0, PARAM_INTEGER);
$validfor = optional_param('validfor', 0, PARAM_INTEGER);
$warnexpire = optional_param('warnexpire', 0, PARAM_INTEGER);
$warncompletion = optional_param('warncompletion', 0, PARAM_INTEGER);
$notifyperiod = optional_param('notifyperiod', 0, PARAM_INTEGER);

$params = array();

if ($company) {
    $params['company'] = $company;
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
if ($search) {
    $params['search'] = $search;
}
if ($courseid) {
    $params['courseid'] = $courseid;
}

$systemcontext = context_system::instance();
require_login();
iomad::require_capability('block/iomad_company_admin:managecourses', $systemcontext);

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

// Build the nav bar.
company_admin_fix_breadcrumb($PAGE, $linktext, $linkurl);

// Set the companyid
$companyid = iomad::get_my_companyid($systemcontext, false);

// Is the users company set and no other company selected?
if (empty($company) && !empty($companyid)) {
    $company = $companyid;
    $params['company'] = $company;
}


if (!empty($update)) {
    // Need to change something.
    if (!$coursedetails = (array) $DB->get_record('iomad_courses', array('courseid' => $courseid))) {
        print_error(get_string('invaliddetails', 'block_iomad_company_admin'));
    } else {
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
        } else if ('warnexpire' == $update) {
            // Work out the time in seconds....
            if ($warnexpire < 0) {
                $warnexpire = 0;
            }
            $coursedetails['warnexpire'] = $warnexpire;
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
        }
    }
}

$baseurl = new moodle_url(basename(__FILE__), $params);
$returnurl = $baseurl;

echo $OUTPUT->header();

// Get the list of companies and display it as a drop down select..

$companyids = $DB->get_records_menu('company', array(), 'id, name');
$companyids['none'] = get_string('nocompany', 'block_iomad_company_admin');
$companyids['all'] = get_string('allcourses', 'block_iomad_company_admin');
ksort($companyids);
$companyselect = new single_select($linkurl, 'company', $companyids, $company);
$companyselect->label = get_string('filtercompany', 'block_iomad_company_admin');
$companyselect->formid = 'choosecompany';
echo html_writer::tag('div', $OUTPUT->render($companyselect), array('id' => 'iomad_company_selector')).'</br>';

// Need a name search in here too.

// Set default courses.
$courses = array();

if (!empty($company)) {
    if ($company == 'none') {
        // Get all courses which are not assigned to any company.
        if (!empty($search)) {
            $select = "fullname like '%$search%' AND id!=1 AND";
        } else {
            $select = "id != 1 AND";
        }
        $sql = "SELECT * from {course} WHERE $select
                id not in (select courseid from {company_course})";
        $courses = $DB->get_records_sql($sql);
    } else  if ($company == 'all') {
        // Get every course.
        if (!empty($search)) {
            $select = "fullname like '%$search%' AND id!=1";
        } else {
            $select = "id != 1";
        }
        $courses = $DB->get_records_select('course', $select);
    } else {
        // Get the courses belonging to that company only.
        if (!empty($search)) {
            $select = "AND c.fullname like '%$search%'";
        } else {
            $select = "";
        }
        $sql = "SELECT c.* from {course} c, {company_course} cc WHERE
                cc.companyid=$company AND cc.courseid = c.id $select";
        $courses = $DB->get_records_sql($sql);
    }
}



// Display the table.
$table = new html_table();
$table->head = array (
    get_string('company', 'block_iomad_company_admin'),
    get_string('course'),
    get_string('licensed', 'block_iomad_company_admin') . $OUTPUT->help_icon('licensed', 'block_iomad_company_admin'),
    get_string('shared', 'block_iomad_company_admin')  . $OUTPUT->help_icon('shared', 'block_iomad_company_admin'),
    get_string('validfor', 'block_iomad_company_admin') . $OUTPUT->help_icon('validfor', 'block_iomad_company_admin'),
    get_string('warnexpire', 'block_iomad_company_admin') . $OUTPUT->help_icon('warnexpire', 'block_iomad_company_admin'),
    get_string('warncompletion', 'block_iomad_company_admin') . $OUTPUT->help_icon('warncompletion', 'block_iomad_company_admin'),
    get_string('notifyperiod', 'block_iomad_company_admin') . $OUTPUT->help_icon('notifyperiod', 'block_iomad_company_admin')
);
$table->align = array ("left", "center", "center", "center", "center", "center", "center", "center");
$table->width = "95%";
$selectbutton = array('0' => get_string('no'), '1' => get_string('yes'));
$licenseselectbutton = array('0' => get_string('no'), '1' => get_string('yes'), '3' => get_string('pluginname', 'enrol_self'));
$sharedselectbutton = array('0' => get_string('no'),
                            '1' => get_string('open', 'block_iomad_company_admin'),
                            '2' => get_string('closed', 'block_iomad_company_admin'));


foreach ($courses as $course) {
    if (!$iomaddetails = $DB->get_record('iomad_courses', array('courseid' => $course->id))) {
        $iomadrecord = array('courseid' => $course->id, 'licensed' => 0, 'shared' => 0);
        $iomadrecord['id'] = $DB->insert_record('iomad_courses', $iomadrecord);
        $iomaddetails = (object) $iomadrecord;
    }
    $linkparams = $params;
    $linkparams['courseid'] = $course->id;
    $linkparams['update'] = 'license';
    $licenseurl = new moodle_url($baseurl, $linkparams);
    $licenseselect = new single_select($licenseurl, 'license', $licenseselectbutton, $iomaddetails->licensed);
    $licenseselect->label = '';
    $licenseselect->formid = 'licenseselect'.$course->id;
    $licenseselectoutput = html_writer::tag('div', $OUTPUT->render($licenseselect), array('id' => 'license_selector'.$course->id));
    $linkparams['update'] = 'shared';
    $sharedurl = new moodle_url($baseurl, $linkparams);
    $sharedselect = new single_select($sharedurl, 'shared', $sharedselectbutton, $iomaddetails->shared);
    $sharedselect->label = '';
    $sharedselect->formid = 'sharedselect'.$course->id;
    $sharedselectoutput = html_writer::tag('div', $OUTPUT->render($sharedselect), array('id' => 'shared_selector'.$course->id));
    if ($tablecompany = $DB->get_records_sql("select c.shortname from {company} c, {company_course} cc WHERE
                                                      cc.courseid = $course->id and cc.companyid = c.id")) {
        $companyname = "";
        foreach ($tablecompany as $tcompany) {
            if ($companyname == "") {
                $companyname = $tcompany->shortname;
            } else {
                $companyname .= ", " . $tcompany->shortname;
            }
        }
    } else {
        $companyname = "";
    }
    if (empty($iomaddetails->validlength)) {
        $duration = 0;
    } else {
        $duration = $iomaddetails->validlength;
    }
    if (empty($iomaddetails->warnexpire)) {
        $warnexpire = 0;
    } else {
        $warnexpire = $iomaddetails->warnexpire;
    }
    if (empty($iomaddetails->warncompletion)) {
        $warncompletion = 0;
    } else {
        $warncompletion = $iomaddetails->warncompletion;
    }
    if (empty($iomaddetails->notifyperiod)) {
        $notifyperiod = 0;
    } else {
        $notifyperiod = $iomaddetails->notifyperiod;
    }
    $formhtml = '<form action="iomad_courses_form.php" method="get">
                 <input type="hidden" name="courseid" value="' . $course->id . '" />
                 <input type="hidden" name="company" value="'.$company.'" />
                 <input type="hidden" name="update" value="validfor" />
                 <input type="text" name="validfor" id="id_validfor" value="'.$duration.'" size="10"/>
                 <input type="submit" value="' . get_string('submit') . '" />
                 </form>';
    $expirehtml = '<form action="iomad_courses_form.php" method="get">
                 <input type="hidden" name="courseid" value="' . $course->id . '" />
                 <input type="hidden" name="company" value="'.$company.'" />
                 <input type="hidden" name="update" value="warnexpire" />
                 <input type="text" name="warnexpire" id="id_warnexpire" value="'.$warnexpire.'" size="10"/>
                 <input type="submit" value="' . get_string('submit') . '" />
                 </form>';
    $warnhtml = '<form action="iomad_courses_form.php" method="get">
                 <input type="hidden" name="courseid" value="' . $course->id . '" />
                 <input type="hidden" name="company" value="'.$company.'" />
                 <input type="hidden" name="update" value="warncompletion" />
                 <input type="text" name="warncompletion" id="id_warncompletion" value="'.$warncompletion.'" size="10"/>
                 <input type="submit" value="' . get_string('submit') . '" />
                 </form>';
    $notifyhtml = '<form action="iomad_courses_form.php" method="get">
                 <input type="hidden" name="courseid" value="' . $course->id . '" />
                 <input type="hidden" name="company" value="'.$company.'" />
                 <input type="hidden" name="update" value="notifyperiod" />
                 <input type="text" name="notifyperiod" id="id_notifyperiod" value="'.$notifyperiod.'" size="10"/>
                 <input type="submit" value="' . get_string('submit') . '" />
                 </form>';
    $courselink = new moodle_url('/course/view.php', array('id'=>$course->id));
    $table->data[] = array ($companyname,
                            "<a href='$courselink'>$course->fullname</a>",
                            $licenseselectoutput,
                            $sharedselectoutput, $formhtml, $expirehtml, $warnhtml, $notifyhtml);
}

if (!empty($courses)) {
    echo html_writer::table($table);
} else {
    echo '<div class="alert alert-warning">' . get_string('nocourses', 'block_iomad_company_admin') . '</div>';
}

// exit button
$link = new moodle_url('/my');
echo '<a class="btn btn-primary" href="' . $link . '">' . get_string('todashboard', 'block_iomad_company_admin') . '</a>';

echo $OUTPUT->footer();
