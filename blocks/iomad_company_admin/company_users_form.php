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
$context = context_system::instance();

require_login();
iomad::require_capability('block/iomad_company_admin:company_user', $context);

$urlparams = array();
if ($returnurl) {
    $urlparams['returnurl'] = $returnurl;
}

// Correct the navbar.
// Set the name for the page.
$linktext = get_string('assignusers', 'block_iomad_company_admin');
// Set the url..
$linkurl = new moodle_url('/blocks/iomad_company_admin/company_users_form.php');

$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('base');
$PAGE->set_title($linktext);

// Set the companyid
$companyid = iomad::get_my_companyid($context);
$company = new company($companyid);
$PAGE->set_heading(get_string('company_users_for', 'block_iomad_company_admin', $company->get_name()));

$usersform = new \block_iomad_company_admin\forms\company_users_form($PAGE->url, $context, $companyid);

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
