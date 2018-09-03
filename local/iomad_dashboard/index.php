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

// Display iomad_dashboard.

require_once( '../../config.php');

// We always require users to be logged in for this page.
require_login();

// Get parameters.
$edit = optional_param( 'edit', null, PARAM_BOOL );
$company = optional_param('company', 0, PARAM_INT);
$showsuspendedcompanies = optional_param('showsuspendedcompanies', false, PARAM_BOOL);
$noticeok = optional_param('noticeok', '', PARAM_CLEAN);
$noticefail = optional_param('noticefail', '', PARAM_CLEAN);

// Check we are allowed to view this page.
$systemcontext = context_system::instance();
iomad::require_capability( 'local/iomad_dashboard:view', $systemcontext );

// Set the session to a user if they are editing a company other than their own.
$SESSION->showsuspendedcompanies = $showsuspendedcompanies;

// Set the session to a user if they are editing a company other than their own.
if (!empty($company) && ( iomad::has_capability('block/iomad_company_admin:company_add', $systemcontext) 
                          || $DB->get_record('company_users', array('managertype' => 1, 'companyid' => $company, 'userid' => $USER->id)))) {
    $SESSION->currenteditingcompany = $company;
}

// Check if there are any companies.
if (!$companycount = $DB->count_records('company')) {

    // If not redirect to create form.
    // But first clear any existing notifications. 
    \core\notification::fetch();
    redirect(new moodle_url('/blocks/iomad_company_admin/company_edit_form.php',
                             array('createnew' => 1)));
}

// If there is only one company, make that the current one
if ($companycount == 1) {
     $companies = $DB->get_records('company');
     $firstcompany = reset($companies);
     $SESSION->currenteditingcompany = $firstcompany->id;
     $company = $firstcompany->id;
}

// Set the url.
$linkurl = new moodle_url('/local/iomad_dashboard/index.php');
$linktext = get_string('name', 'local_iomad_dashboard');
// Page setup stuff.
// The page layout for my moodle does the job here
// as it allows blocks in the centre column.
// Print the page header.
$PAGE->set_context($systemcontext);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($linktext);
$PAGE->set_heading($linktext);
$PAGE->requires->js_init_call( 'M.local_iomad_dashboard.init');
$PAGE->blocks->add_region('content');

// Set tye pagetype correctly.
$PAGE->set_pagetype('local-iomad-dashboard-index');
$PAGE->set_pagelayout('mydashboard');

// Now we can display the page.

echo $OUTPUT->header();

// Deal with any notices.
if (!empty($noticeok)) {
    echo html_writer::start_tag('div', array('class' => "alert alert-success"));
    echo $noticeok;
    echo "</div>";
} 
if (!empty($noticefail)) {
    echo html_writer::start_tag('div', array('class' => "alert alert-warning"));
    echo $noticefail;
    echo "</div>";
} 

echo $OUTPUT->blocks_for_region('content');
echo $OUTPUT->footer();
