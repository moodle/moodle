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
 * @package   local_iomad_signup
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once($CFG->dirroot . '/user/editlib.php');
require_once('signup_form.php');
$wantedcompanyid = required_param('id', PARAM_INT);
$wantedcompanyshort = required_param('code', PARAM_CLEAN);
$wanteddepartment = optional_param('dept', '', PARAM_CLEAN);

// Try to prevent searching for sites that allow sign-up.
if (!isset($CFG->additionalhtmlhead)) {
    $CFG->additionalhtmlhead = '';
}
$CFG->additionalhtmlhead .= '<meta name="robots" content="noindex" />';

if (empty($CFG->registerauth) && !$CFG->local_iomad_signup_enable) {
    print_error('notlocalisederrormessage', 'error', '', 'Sorry, you may not use this page.');
}
$authplugin = get_auth_plugin($CFG->registerauth);

if (!$authplugin->can_signup()) {
    print_error('notlocalisederrormessage', 'error', '', 'Sorry, you may not use this page.');
}

$PAGE->set_url('/login/signup.php');
$PAGE->set_context(context_system::instance());

// Check if the company being passed is valid.
if (!$company = $DB->get_record('company', array('id'=> $wantedcompanyid, 'shortname'=>$wantedcompanyshort))) {
    print_error(get_string('unknown_company', 'local_iomad_signup'));
}

$company->deptid = 0;
if (!empty($wanteddepartment)) {
    if ($department=$DB->get_record('department', array('company' => $company->id, 'shortname' => urldecode($wanteddepartment)))) {
        $company->deptid = $department->id;
    }
}

// Override wanted URL, we do not want to end up here again if user clicks "Login".
$SESSION->wantsurl = $CFG->wwwroot . '/';

// Set the page theme.
$SESSION->theme = $company->theme;

if (isloggedin() and !isguestuser()) {
    // Prevent signing up when already logged in.
    echo $OUTPUT->header();
    echo $OUTPUT->box_start();
    $logout = new single_button(new moodle_url($CFG->httpswwwroot . '/login/logout.php',
        array('sesskey' => sesskey(), 'loginpage' => 1)), get_string('logout'), 'post');
    $continue = new single_button(new moodle_url('/'), get_string('cancel'), 'get');
    echo $OUTPUT->confirm(get_string('cannotsignup', 'error', fullname($USER)), $logout, $continue);
    echo $OUTPUT->box_end();
    echo $OUTPUT->footer();
    exit;
}

$mform_signup = new iomad_signup_form(null, (array) $company);

if ($mform_signup->is_cancelled()) {
    redirect(get_login_url());

} else if ($user = $mform_signup->get_data()) {
    if ($CFG->local_iomad_signup_useemail) {
       $user->username = $user->email;
    }
    $user->confirmed   = 0;
    $user->lang        = current_language();
    $user->firstaccess = time();
    $user->timecreated = time();
    $user->mnethostid  = $CFG->mnet_localhost_id;
    $user->secret      = random_string(15);
    $user->auth        = $CFG->registerauth;
    $user->companyid     = $company->id;
    // Initialize alternate name fields to empty strings.
    $namefields = array_diff(get_all_user_name_fields(), useredit_get_required_name_fields());
    foreach ($namefields as $namefield) {
        $user->$namefield = '';
    }

    $authplugin->user_signup($user, true); // prints notice and link to login/index.php
    exit; //never reached
}

$newaccount = get_string('newaccount');
$login      = get_string('login');

$PAGE->navbar->add($login);
$PAGE->navbar->add($newaccount);

$PAGE->set_title($newaccount);
$PAGE->set_heading($SITE->fullname);

echo $OUTPUT->header();
echo $OUTPUT->heading($company->name . ' - ' . $newaccount);
if (!empty($CFG->auth_instructions) && !empty($CFG->local_iomad_signup_showinstructions)) {
    echo format_text($CFG->auth_instructions);
} else {
    print_string("logininfo", "local_iomad_signup");
}
$mform_signup->display();
echo $OUTPUT->footer();
