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

$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);
$companyid = optional_param('companyid', 0, PARAM_INTEGER);
$departmentid = optional_param('deptid', 0, PARAM_INTEGER);

$context = context_system::instance();
require_login();
iomad::require_capability('block/iomad_company_admin:company_course', $context);

$urlparams = array('companyid' => $companyid);
if ($returnurl) {
    $urlparams['returnurl'] = $returnurl;
}

// Correct the navbar.
// Set the name for the page.
$linktext = get_string('assigncourses', 'block_iomad_company_admin');
// Set the url.
$linkurl = new moodle_url('/blocks/iomad_company_admin/company_courses_form.php');

// Print the page header.
$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('base');
$PAGE->set_title($linktext);

// Set the companyid
$companyid = iomad::get_my_companyid($context);
$parentlevel = company::get_company_parentnode($companyid);
$companydepartment = $parentlevel->id;
$syscontext = context_system::instance();
$company = new company($companyid);

// Set the page heading.
$PAGE->set_heading(get_string('company_courses_for', 'block_iomad_company_admin', $company->get_name()));

if (iomad::has_capability('block/iomad_company_admin:edit_all_departments', $syscontext)) {
    $userhierarchylevel = $parentlevel->id;
} else {
    $userlevel = $company->get_userlevel($USER);
    $userhierarchylevel = key($userlevel);
}

$subhierarchieslist = company::get_all_subdepartments($userhierarchylevel);
if (empty($departmentid)) {
    $departmentid = $userhierarchylevel;
}
$departmentselect = new single_select(new moodle_url($linkurl, $urlparams), 'deptid', $subhierarchieslist, $departmentid);
$departmentselect->label = get_string('department', 'block_iomad_company_admin') .
                           $OUTPUT->help_icon('department', 'block_iomad_company_admin') . '&nbsp';

$mform = new \block_iomad_company_admin\forms\company_courses_form($PAGE->url, $context, $companyid, $departmentid, $parentlevel);

if ($mform->is_cancelled()) {
    if ($returnurl) {
        redirect($returnurl);
    } else {
        redirect(new moodle_url('/my'));
    }
} else {
    $mform->process();

    echo $OUTPUT->header();

    // Check the department is valid.
    if (!empty($departmentid) && !company::check_valid_department($companyid, $departmentid)) {
        print_error('invaliddepartment', 'block_iomad_company_admin');
    }

    $mform->display();

    echo $OUTPUT->footer();
}
