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
 * This file is part of the login section Moodle
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package login
 */

require_once('../config.php');

/**
 * Returns whether or not the captcha element is enabled, and the admin settings fulfil its requirements.
 * @return bool
 */
function signup_captcha_enabled() {
    global $CFG;
    return !empty($CFG->recaptchapublickey) && !empty($CFG->recaptchaprivatekey) && get_config('auth/email', 'recaptcha');
}

require_once('signup_form.php');


if (empty($CFG->registerauth)) {
    print_error("Sorry, you may not use this page.");
}
$authplugin = get_auth_plugin($CFG->registerauth);

if (!$authplugin->can_signup()) {
    print_error("Sorry, you may not use this page.");
}

//HTTPS is potentially required in this page
httpsrequired();
$PAGE->set_url('/login/signup.php');
$mform_signup = new login_signup_form();

if ($mform_signup->is_cancelled()) {
    redirect(get_login_url());

} else if ($user = $mform_signup->get_data()) {
    $user->confirmed   = 0;
    $user->lang        = current_language();
    $user->firstaccess = time();
    $user->timecreated = time();
    $user->mnethostid  = $CFG->mnet_localhost_id;
    $user->secret      = random_string(15);
    $user->auth        = $CFG->registerauth;
    
    $authplugin->user_signup($user, true); // prints notice and link to login/index.php  
    exit; //never reached
}

$newaccount = get_string('newaccount');
$login      = get_string('login');

$PAGE->navbar->add($login);
$PAGE->navbar->add($newaccount);

$PAGE->set_title($newaccount);
$PAGE->set_heading($SITE->fullname);
$PAGE->set_focuscontrol($mform_signup->focus());

echo $OUTPUT->header();
$mform_signup->display();
echo $OUTPUT->footer();
