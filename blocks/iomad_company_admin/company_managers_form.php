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

$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);
$departmentid = optional_param('deptid', 0, PARAM_INTEGER);
$roleid = optional_param('managertype', 0, PARAM_INTEGER);
$showothermanagers = optional_param('showothermanagers', 0, PARAM_BOOL);

// If we are not handling company manager role types we are not picking other company managers.
if ($roleid != 1) {
    $showothermanagers = false;
}

$context = context_system::instance();
require_login();
iomad::require_capability('block/iomad_company_admin:company_manager', $context);

// Correct the navbar.
// Set the name for the page.
$linktext = get_string('assignmanagers', 'block_iomad_company_admin');
// Set the url.
$linkurl = new moodle_url('/blocks/iomad_company_admin/company_managers_form.php');

$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('base');
$PAGE->set_title($linktext);

// Set the page heading.
$PAGE->set_heading($linktext);

// Set the companyid
$companyid = iomad::get_my_companyid($context);
$company = new company($companyid);

// Set up the departments stuffs.
$parentlevel = company::get_company_parentnode($company->id);
if (iomad::has_capability('block/iomad_company_admin:edit_all_departments', context_system::instance())) {
    $userhierarchylevel = $parentlevel->id;
} else {
    $userlevel = $company->get_userlevel($USER);
    $userhierarchylevel = key($userlevel);
}
if ($departmentid == 0) {
    $departmentid = $userhierarchylevel;
}

$PAGE->set_context($context);

// get output renderer
$output = $PAGE->get_renderer('block_iomad_company_admin');

// Javascript for fancy select.
// Parameter is name of proper select form element followed by 1=submit its form
$PAGE->requires->js_call_amd('block_iomad_company_admin/department_select', 'init', array('deptid', 1, optional_param('deptid', 0, PARAM_INT)));

$urlparams = array('deptid' => $departmentid,
                   'managertype' => $roleid,
                   'showothermanagers' => $showothermanagers);
if ($returnurl) {
    $urlparams['returnurl'] = $returnurl;
}

// Get the manager types.
$managertypes = $company->get_managertypes();
if (empty($departmentid)) {
    $departmentid = $parentlevel->id;
} else if ($departmentid != $parentlevel->id) {
    unset($managertypes[1]);
    if ($roleid ==1) {
        $urlparams['managertype'] = '';
        $urlparams['deptid'] = $departmentid;
        redirect(new moodle_url($linkurl, $urlparams));
    }
}
$managerselect = new single_select(
    new moodle_url($linkurl, $urlparams),
    'managertype',
    $managertypes,
    $roleid,
    array('' => 'choosedots'),
    null,
    ['label' => get_string('managertype', 'block_iomad_company_admin')]
);

$othersselect = new single_select(new moodle_url($linkurl, $urlparams), 'showothermanagers',
                array(get_string('no'), get_string('yes')), $showothermanagers);
$othersselect->label = get_string('showothermanagers', 'block_iomad_company_admin') .
                       $output->help_icon('showothermanagers', 'block_iomad_company_admin') . '&nbsp';

// Set up the allocation form.
$managersform = new block_iomad_company_admin\forms\company_managers_form($PAGE->url, $context, $companyid, $departmentid, $roleid, $showothermanagers);

// Change the department for the form.
if ($departmentid != 0) {
    $managersform->set_data(array('deptid' => $departmentid));
}
// Change the user type of the form.
if ($roleid != 0) {
    $managersform->set_data(array('managertype' => $roleid));
}


if ($managersform->is_cancelled()) {
    if ($returnurl) {
        redirect($returnurl);
    } else {
        redirect(new moodle_url('/my'));
    }
} else {
    $managersform->process($departmentid, $roleid);

    echo $output->header();

    // Check the department is valid.
    if (!empty($departmentid) && !company::check_valid_department($companyid, $departmentid)) {
        print_error('invaliddepartment', 'block_iomad_company_admin');
    }

    // Display the department tree.
    echo $output->display_tree_selector($company, $parentlevel, $linkurl, $urlparams, $departmentid);

    echo html_writer::start_tag('div', array('class' => 'iomadclear'));
    echo html_writer::start_tag('div', array('class' => 'fitem'));
    echo $output->render($managerselect);
    echo html_writer::end_tag('div');
    echo html_writer::end_tag('div');

    if (iomad::has_capability('block/iomad_company_admin:company_add', context_system::instance()) &&
        $roleid == 1) {
        echo html_writer::start_tag('div', array('class' => 'iomadclear'));
        echo html_writer::start_tag('div', array('class' => 'fitem'));
        echo $output->render($othersselect);
        echo html_writer::end_tag('div');
        echo html_writer::end_tag('div');
    }

    echo $managersform->display();

    echo $output->footer();
}
