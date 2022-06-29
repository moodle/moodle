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
 * Forgot password routine.
 *
 * Finds the user and calls the appropriate routine for their authentication type.
 *
 * There are several pathways to/through this page, summarised below:
 * 1. User clicks the 'forgotten your username or password?' link on the login page.
 *  - No token is received, render the username/email search form.
 * 2. User clicks the link in the forgot password email
 *  - Token received as GET param, store the token in session, redirect to self
 * 3. Redirected from (2)
 *  - Fetch token from session, and continue to run the reset routine defined in 'core_login_process_password_set()'.
 *
 * @package    core
 * @subpackage auth
 * @copyright  1999 onwards Martin Dougiamas  http://dougiamas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../config.php');
require_once($CFG->libdir.'/authlib.php');
require_once(__DIR__ . '/lib.php');
require_once('forgot_password_form.php');
require_once('set_password_form.php');

$token = optional_param('token', false, PARAM_ALPHANUM);

$PAGE->set_url('/login/forgot_password.php');
$systemcontext = context_system::instance();
$PAGE->set_context($systemcontext);

// setup text strings
$strforgotten = get_string('passwordforgotten');

$PAGE->set_pagelayout('login');
$PAGE->set_title($strforgotten);
$PAGE->set_heading($COURSE->fullname);

// if alternatepasswordurl is defined, then we'll just head there
if (!empty($CFG->forgottenpasswordurl)) {
    redirect($CFG->forgottenpasswordurl);
}

// if you are logged in then you shouldn't be here!
if (isloggedin() and !isguestuser()) {
    redirect($CFG->wwwroot.'/index.php', get_string('loginalready'), 5);
}

// Fetch the token from the session, if present, and unset the session var immediately.
$tokeninsession = false;
if (!empty($SESSION->password_reset_token)) {
    $token = $SESSION->password_reset_token;
    unset($SESSION->password_reset_token);
    $tokeninsession = true;
}

if (empty($token)) {
    // This is a new password reset request.
    // Process the request; identify the user & send confirmation email.
    core_login_process_password_reset_request();
} else {
    // A token has been found, but not in the session, and not from a form post.
    // This must be the user following the original rest link, so store the reset token in the session and redirect to self.
    // The session var is intentionally used only during the lifespan of one request (the redirect) and is unset above.
    if (!$tokeninsession && $_SERVER['REQUEST_METHOD'] === 'GET') {
        $SESSION->password_reset_token = $token;
        redirect($CFG->wwwroot . '/login/forgot_password.php');
    } else {
        // Continue with the password reset process.
        core_login_process_password_set($token);
    }
}
