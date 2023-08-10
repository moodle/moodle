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
 * Confirm self oauth2 user.
 *
 * @package    auth_oauth2
 * @copyright  2017 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once($CFG->libdir . '/authlib.php');

$usersecret = required_param('token', PARAM_RAW);
$username = required_param('username', PARAM_USERNAME);
$redirect = optional_param('redirect', '', PARAM_LOCALURL);    // Where to redirect the browser once the user has been confirmed.

$PAGE->set_url('/auth/oauth2/confirm-account.php');
$PAGE->set_context(context_system::instance());

$auth = get_auth_plugin('oauth2');

if (!\auth_oauth2\api::is_enabled()) {
    throw new \moodle_exception('notenabled', 'auth_oauth2');
}

$confirmed = $auth->user_confirm($username, $usersecret);

if ($confirmed == AUTH_CONFIRM_ALREADY && !isloggedin()) {
    $user = get_complete_user_data('username', $username);
    $PAGE->navbar->add(get_string("alreadyconfirmed"));
    $PAGE->set_title(get_string("alreadyconfirmed"));
    $PAGE->set_heading($COURSE->fullname);
    echo $OUTPUT->header();
    echo $OUTPUT->box_start('generalbox centerpara boxwidthnormal boxaligncenter');
    echo "<p>".get_string("alreadyconfirmed")."</p>\n";
    echo $OUTPUT->single_button("$CFG->wwwroot/course/", get_string('courses'));
    echo $OUTPUT->box_end();
    echo $OUTPUT->footer();
    exit;

} else if ($confirmed == AUTH_CONFIRM_OK) {

    // The user has confirmed successfully, let's log them in.

    if (!$user = get_complete_user_data('username', $username)) {
        print_error('cannotfinduser', '', '', s($username));
    }

    if ($user->id == $USER->id) {
        // Check where to go, $redirect has a higher preference.
        if (empty($redirect) and !empty($SESSION->wantsurl) ) {
            $redirect = $SESSION->wantsurl;
            unset($SESSION->wantsurl);
        }

        if (!empty($redirect)) {
            redirect($redirect);
        }
    }

    $PAGE->navbar->add(get_string("confirmed"));
    $PAGE->set_title(get_string("confirmed"));
    $PAGE->set_heading($COURSE->fullname);
    echo $OUTPUT->header();
    echo $OUTPUT->box_start('generalbox centerpara boxwidthnormal boxaligncenter');
    echo "<h3>".get_string("thanks").", ". fullname($user) . "</h3>\n";
    echo "<p>".get_string("confirmed")."</p>\n";
    if (!isloggedin() || isguestuser()) {
        echo $OUTPUT->single_button(get_login_url(), get_string('login'));
    } else {
        echo $OUTPUT->single_button("$CFG->wwwroot/login/logout.php", get_string('logout'));
    }
    echo $OUTPUT->box_end();
    echo $OUTPUT->footer();
    exit;
} else {
    if (!isloggedin()) {
        \core\notification::error(get_string('confirmationinvalid', 'auth_oauth2'));
    }
}

redirect("$CFG->wwwroot/");
