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
$allusers = optional_param('allusers', false, PARAM_BOOL);

require_login();

$systemcontext = context_system::instance();

// Set the companyid
$companyid = iomad::get_my_companyid($systemcontext);
$companycontext = \core\context\company::instance($companyid);
$company = new company($companyid);

iomad::require_capability('block/iomad_company_admin:company_user', $companycontext);

$urlparams = array();
if ($returnurl) {
    $urlparams['returnurl'] = $returnurl;
}

// Correct the navbar.
// Set the name for the page.
$linktext = get_string('assignusers', 'block_iomad_company_admin');
// Set the url..
$linkurl = new moodle_url('/blocks/iomad_company_admin/company_users_form.php', ['allusers' => $allusers]);

$PAGE->set_context($companycontext);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('base');
$PAGE->set_title($linktext);
$PAGE->set_heading(get_string('company_users_for', 'block_iomad_company_admin', $company->get_name()));

// Deal with the link back to the user edit page.
$buttons = "";
if (has_capability('block/iomad_company_admin:company_add', $systemcontext)) {
    if ($allusers) {
        $buttoncaption = get_string('potentialdepartmentusers', 'block_iomad_company_admin');
    } else {
        $buttoncaption = get_string('show_all_company_users', 'block_iomad_company_admin');
    }
    $buttonlink = new moodle_url('/blocks/iomad_company_admin/company_users_form.php', ['allusers' => !$allusers]);
    $buttons = $OUTPUT->single_button($buttonlink, $buttoncaption, 'get');
}
$PAGE->set_button($buttons);

$usersform = new \block_iomad_company_admin\forms\company_users_form($PAGE->url, $companycontext, $companyid, $allusers);

if ($usersform->is_cancelled()) {
    if ($returnurl) {
        redirect($returnurl);
    } else {
        redirect(new moodle_url('/blocks/iomad_company_admin/index.php'));
    }
} else {
    $usersform->process();

    echo $OUTPUT->header();

    echo $usersform->display();

    echo $OUTPUT->footer();
}