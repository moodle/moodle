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
 * @package   block_iomad_company_admin
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php'); // Creates $PAGE.
require_once('lib.php');
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot.'/local/email/lib.php');

$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);
$companyid = optional_param('companyid', 0, PARAM_INTEGER);
$courses = optional_param_array('courses', array(), PARAM_INTEGER);
$departmentid = optional_param('deptid', 0, PARAM_INTEGER);
$selectedcourses = optional_param_array('selectedcourses', array('-1'), PARAM_INTEGER);
$groupid = optional_param('groupid', 0, PARAM_INTEGER);

if (empty($courses) && !empty($selectedcourses)) {
    $courses = $selectedcourses;
}

$context = context_system::instance();
require_login();

$params = array('companyid' => $companyid,
                'courses' => $courses,
                'deptid' => $departmentid,
                'selectedcourses' => $selectedcourses,
                'groupid' => $groupid);

$urlparams = array('companyid' => $companyid);
if ($returnurl) {
    $urlparams['returnurl'] = $returnurl;
}
if (!empty($courses)) {
    foreach ($courses as $a => $b)
    $urlparams["courses[$a]"] = $b;
}
if (!empty($selectedcourses)) {
    foreach ($selectedcourses as $a => $b)
    $urlparams["selectedcourses[$a]"] = $b;
}
// Correct the navbar.
// Set the name for the page.
$linktext = get_string('company_course_users_title', 'block_iomad_company_admin');
// Set the url.
$linkurl = new moodle_url('/blocks/iomad_company_admin/company_course_users_form.php');

// Print the page header.
$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('base');
$PAGE->set_title($linktext);
// Set the page heading.
$PAGE->set_heading($linktext);

// get output renderer
$output = $PAGE->get_renderer('block_iomad_company_admin');

// Javascript for fancy select.
// Parameter is name of proper select form element followed by 1=submit its form
$PAGE->requires->js_call_amd('block_iomad_company_admin/department_select', 'init', array('deptid', 1, optional_param('deptid', 0, PARAM_INT)));
$PAGE->navbar->add($linktext, $linkurl);

require_login(null, false); // Adds to $PAGE, creates $output.
iomad::require_capability('block/iomad_company_admin:company_course_users', $context);

// Set the companyid
$companyid = iomad::get_my_companyid($context);
$parentlevel = company::get_company_parentnode($companyid);
$companydepartment = $parentlevel->id;
$syscontext = context_system::instance();
$company = new company($companyid);

$coursesform = new \block_iomad_company_admin\forms\company_ccu_courses_form($PAGE->url, $context, $companyid, $departmentid, $selectedcourses, $parentlevel);
$coursesform->set_data(array('selectedcourses' => $selectedcourses, 'courses' => $courses));
$usersform = new \block_iomad_company_admin\forms\company_course_users_form($PAGE->url, $context, $companyid, $departmentid, $selectedcourses);

// Check the department is valid.
if (!empty($departmentid) && !company::check_valid_department($companyid, $departmentid)) {
    print_error('invaliddepartment', 'block_iomad_company_admin');
}

if ($coursesform->is_cancelled() || $usersform->is_cancelled() ||
     optional_param('cancel', false, PARAM_BOOL) ) {
    if ($returnurl) {
        redirect($returnurl);
    } else {
        redirect(new moodle_url('/my'));
    }
} else {
    echo $output->header();

    // Display the department selector.
    echo $output->display_tree_selector($company, $parentlevel, $linkurl, $urlparams, $departmentid);

    echo html_writer::start_tag('div', array('class' => 'iomadclear'));
    if ($companyid > 0) {
        $coursesform->set_data($params);
        echo $coursesform->display();
        if (!in_array('-1', $selectedcourses, true)) {
            if ($data = $coursesform->get_data() || empty($selectedcourses)) {
                 if (count($courses) > 0) {
                    $usersform->set_course(array($courses));
                    $usersform->process();
                    $usersform = new \block_iomad_company_admin\forms\company_course_users_form($PAGE->url, $context, $companyid, $departmentid, $selectedcourses);
                    $usersform->set_course(array($courses));
                    $usersform->set_data(array('groupid' => $groupid));
                } else if (!empty($selectedcourses)) {
                    $usersform->set_course($selectedcourses);
                }
                echo $usersform->display();
            } else if (count($courses) > 0) {
                $usersform->set_course(array($courses));
                $usersform->process();
                $usersform = new \block_iomad_company_admin\forms\company_course_users_form($PAGE->url, $context, $companyid, $departmentid, $selectedcourses);
                $usersform->set_course(array($courses));
                $usersform->set_data(array('groupid' => $groupid));
                echo $usersform->display();
            }
        }
    }
    echo html_writer::end_tag('div');

    echo $output->footer();
}
