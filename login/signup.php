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
 * user signup page.
 *
 * @package    core
 * @subpackage auth
 * @copyright  1999 onwards Martin Dougiamas  http://dougiamas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../config.php');
require_once($CFG->dirroot . '/user/editlib.php');
require_once($CFG->libdir . '/authlib.php');
require_once('lib.php');

if (!$authplugin = signup_is_enabled()) {
    print_error('notlocalisederrormessage', 'error', '', 'Sorry, you may not use this page.');
}

// IOMAD - Deal with any passed company information from parameters or from SESSION.
if (empty($SESSION->company)) {
    $wantedcompanyid = optional_param('id', 0, PARAM_INT);
    if (!empty($wantedcompanyid)) {
        $wantedcompanyshort = required_param('code', PARAM_CLEAN);
    } else {
        $wantedcompanyshort = '';
    }
} else {
    $wantedcompanyid = $SESSION->company->id;
    $wantedcompanyshort = $SESSION->company->shortname;
}
$wanteddepartment = optional_param('dept', '', PARAM_CLEAN);

$PAGE->set_url('/login/signup.php');
$PAGE->set_context(context_system::instance());

// If wantsurl is empty or /login/signup.php, override wanted URL.
// We do not want to end up here again if user clicks "Login".
if (empty($SESSION->wantsurl)) {
    $SESSION->wantsurl = $CFG->wwwroot . '/';
} else {
    $wantsurl = new moodle_url($SESSION->wantsurl);
    if ($PAGE->url->compare($wantsurl, URL_MATCH_BASE)) {
        $SESSION->wantsurl = $CFG->wwwroot . '/';
    }
}

// Check if the company being passed is valid.
if (!empty($wantedcompanyid) &&!$company = $DB->get_record('company', array('id'=> $wantedcompanyid, 'shortname'=>$wantedcompanyshort))) {
    print_error(get_string('unknown_company', 'local_iomad_signup'));
}
// Check if the company can have more users?.
if (!empty($wantedcompanyid) && $company->maxusers > 0) {
    $currentusers = $DB->count_records('company_users', array('companyid' => $wantedcompanyid));
    if ($currentusers >= $company->maxusers) {
        print_error(get_string('maxuserswarning', 'local_iomad_signup', $company->maxusers));
    }
}
if (!empty($wantedcompanyid)) {
    $company->deptid = 0;
    $SESSION->company->deptid = 0;
    if (!empty($wanteddepartment)) {
        if ($department=$DB->get_record('department', array('company' => $company->id, 'shortname' => urldecode($wanteddepartment)))) {
            $company->deptid = $department->id;
            $SESSION->company->deptid = $department->id;
        }
    }
    // Set the page theme.
    $SESSION->theme = $company->theme;
    $SESSION->currenteditingcompany = $company->id;
}

if (isloggedin() and !isguestuser()) {
    // Prevent signing up when already logged in.
    echo $OUTPUT->header();
    echo $OUTPUT->box_start();
    $logout = new single_button(new moodle_url('/login/logout.php',
        array('sesskey' => sesskey(), 'loginpage' => 1)), get_string('logout'), 'post');
    $continue = new single_button(new moodle_url('/'), get_string('cancel'), 'get');
    echo $OUTPUT->confirm(get_string('cannotsignup', 'error', fullname($USER)), $logout, $continue);
    echo $OUTPUT->box_end();
    echo $OUTPUT->footer();
    exit;
}

// If verification of age and location (digital minor check) is enabled.
if (\core_auth\digital_consent::is_age_digital_consent_verification_enabled()) {
    $cache = cache::make('core', 'presignup');
    $isminor = $cache->get('isminor');
    if ($isminor === false) {
        // The verification of age and location (minor) has not been done.
        redirect(new moodle_url('/login/verify_age_location.php'));
    } else if ($isminor === 'yes') {
        // The user that attempts to sign up is a digital minor.
        redirect(new moodle_url('/login/digital_minor.php'));
    }
}

// Plugins can create pre sign up requests.
// Can be used to force additional actions before sign up such as acceptance of policies, validations, etc.
core_login_pre_signup_requests();

$mform_signup = $authplugin->signup_form();

if ($mform_signup->is_cancelled()) {
    // IOMAD - We want the company/theme to persist.
    $redirect = get_login_url();
    if (!empty($SESSION->company)) {
        $redirect .= "?id=" . $SESSION->company->id . "&code=" . $SESSION->company->shortname;
    }

    redirect($redirect);

} else if ($user = $mform_signup->get_data()) {
    if (!empty($SESSION->company) && $CFG->local_iomad_signup_useemail) {
        $user->username = $user->email;
    }

    // Add missing required fields.
    $user = signup_setup_new_user($user);

    // Plugins can perform post sign up actions once data has been validated.
    core_login_post_signup_requests($user);

    $authplugin->user_signup($user, true); // prints notice and link to login/index.php
    exit; //never reached
}


$newaccount = get_string('newaccount');
$login      = get_string('login');

$PAGE->navbar->add($login);
$PAGE->navbar->add($newaccount);

$PAGE->set_pagelayout('login');
$PAGE->set_title($newaccount);
$PAGE->set_heading($SITE->fullname);

echo $OUTPUT->header();

if ($mform_signup instanceof renderable) {
    // Try and use the renderer from the auth plugin if it exists.
    try {
        $renderer = $PAGE->get_renderer('auth_' . $authplugin->authtype);
    } catch (coding_exception $ce) {
        // Fall back on the general renderer.
        $renderer = $OUTPUT;
    }
    echo $renderer->render($mform_signup);
} else {
    // Fall back for auth plugins not using renderables.
    $mform_signup->display();
}
echo $OUTPUT->footer();
