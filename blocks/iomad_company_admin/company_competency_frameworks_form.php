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

$context = context_system::instance();
require_login();
iomad::require_capability('block/iomad_company_admin:company_framework', $context);

$urlparams = array('companyid' => $companyid);
if ($returnurl) {
    $urlparams['returnurl'] = $returnurl;
}

// Correct the navbar.
// Set the url.
$linkurl = new moodle_url('/blocks/iomad_company_admin/company_competency_frameworks_form.php');

// Print the page header.
$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('base');

// Set the companyid
$companyid = iomad::get_my_companyid($context);
$company = new company($companyid);

// Set the name for the page.
$linktext = get_string('company_frameworks_for', 'block_iomad_company_admin', $company->get_name());

// Set the page heading.
$PAGE->set_title($linktext);
$PAGE->set_heading($linktext);

$mform = new \block_iomad_company_admin\forms\company_frameworks_form($PAGE->url, $context, $companyid);

if ($mform->is_cancelled()) {
    if ($returnurl) {
        redirect($returnurl);
    } else {
        redirect(new moodle_url('/my'));
    }
} else {
    $mform->process();

    echo $OUTPUT->header();

    $mform->display();

    echo $OUTPUT->footer();
}
