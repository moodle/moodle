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

/**
 * Script to let a user company users to a company course group.
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir . '/formslib.php');
require_once('lib.php');
require_once(dirname(__FILE__) . '/../../course/lib.php');

$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);
$courseid = optional_param('courseid', 0, PARAM_INT);
$deleteids = optional_param_array('courseids', null, PARAM_INT);
$createnew = optional_param('createnew', 0, PARAM_INT);
$selectedcourse = optional_param('selectedcourse', 0, PARAM_INTEGER);
$selectedgroup = optional_param('selectedgroup', 0, PARAM_INTEGER);
$groupids = optional_param_array('groupids', 0, PARAM_INTEGER);
$departmentid = optional_param_array('deparmentid', 0, PARAM_INTEGER);

if (!empty($groupids)) {
    $groupid = $groupids[0];
} else {
    $groupid = 0;
}

$context = context_system::instance();
require_login();

iomad::require_capability('block/iomad_company_admin:assign_groups', $context);

$urlparams = array();
if ($returnurl) {
    $urlparams['returnurl'] = $returnurl;
}
$companylist = new moodle_url('/my', $urlparams);

$linktext = get_string('assigncoursegroups', 'block_iomad_company_admin');

// Set the url.
$linkurl = new moodle_url('/blocks/iomad_company_admin/company_groups_users_form.php');

$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('base');
$PAGE->set_title($linktext);

// get output renderer
$output = $PAGE->get_renderer('block_iomad_company_admin');

// Set the page heading.
$PAGE->set_heading($linktext);

// Set the companyid
$companyid = iomad::get_my_companyid($context);

// Javascript for fancy select.
// Parameter is name of proper select form element.
$PAGE->requires->js_call_amd('block_iomad_company_admin/department_select', 'init', array('deptid'));

$courseform = new \block_iomad_company_admin\forms\company_gu_courses_form($PAGE->url, $context, $companyid, $selectedcourse);
$mform = new \block_iomad_company_admin\forms\course_group_user_display_form($PAGE->url, $companyid, $selectedcourse, $output);
if (!empty($selectedcourse) && !empty($selectedgroup)) {
    $groupform = new \block_iomad_company_admin\forms\course_group_users_form($PAGE->url, $context, $companyid, $departmentid, $selectedcourse, $selectedgroup);
}
$courseform->set_data(array('selectedcourse' => $selectedcourse));
$mform->set_data(array('selectedgroup' => $selectedgroup));

if (!empty($groupform) && $groupform->is_cancelled()) {
    redirect($companylist);

} else {

    echo $output->header();

    // Check the department is valid.
    if (!empty($departmentid) && !company::check_valid_department($companyid, $departmentid)) {
        print_error('invaliddepartment', 'block_iomad_company_admin');
    }

    $courseform->display();
    if (!empty($selectedcourse)) {
        $mform->display();
    }
    if (!empty($selectedgroup)) {
        $groupform->process();
        $groupform->set_data(array());
        $groupform->display();
    }

    echo $output->footer();
}