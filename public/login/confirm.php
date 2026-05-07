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
 * Confirm self registered user.
 *
 * @package    core
 * @subpackage auth
 * @copyright  1999 Martin Dougiamas  http://dougiamas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../config.php');
require_once(__DIR__ . '/lib.php');
require_once($CFG->libdir . '/authlib.php');

$data = optional_param('data', '', PARAM_RAW);  // Formatted as:  secret/username

$p = optional_param('p', '', PARAM_ALPHANUM);   // Old parameter:  secret
$s = optional_param('s', '', PARAM_RAW);        // Old parameter:  username
$redirect = optional_param('redirect', '', PARAM_LOCALURL);    // Where to redirect the browser once the user has been confirmed.

$PAGE->set_url('/login/confirm.php');
$PAGE->set_context(context_system::instance());

if (!$authplugin = signup_get_user_confirmation_authplugin()) {
    throw new moodle_exception('confirmationnotenabled');
}

if (!empty($data) || (!empty($p) && !empty($s))) {

    if (!empty($data)) {
        $dataelements = explode('/', $data, 2); // Stop after 1st slash. Rest is username. MDL-7647
        $usersecret = $dataelements[0];
        $username   = $dataelements[1];
    } else {
        $usersecret = $p;
        $username   = $s;
    }

    // Read auth_email_wantsurl before user_confirm() cleans it up, so we can
    // restore it into the session after complete_user_login() regenerates the session.
    $earlyuser = get_complete_user_data('username', $username);
    $emailwantsurl = $earlyuser ? get_user_preferences('auth_email_wantsurl', false, $earlyuser) : false;

    $confirmed = $authplugin->user_confirm($username, $usersecret);

    if ($confirmed == AUTH_CONFIRM_ALREADY) {
        $user = get_complete_user_data('username', $username);
        $PAGE->navbar->add(get_string("alreadyconfirmed"));
        $PAGE->set_title(get_string("alreadyconfirmed"));
        $PAGE->set_heading($COURSE->fullname);
        echo $OUTPUT->header();
        echo $OUTPUT->box_start('generalbox centerpara boxwidthnormal boxaligncenter');
        echo "<p>".get_string("alreadyconfirmed")."</p>\n";
        echo $OUTPUT->single_button(core_login_get_return_url(), get_string('courses'));
        echo $OUTPUT->box_end();
        echo $OUTPUT->footer();
        exit;

    } else if ($confirmed == AUTH_CONFIRM_OK) {

        // The user has confirmed successfully, let's log them in

        if (!$user = get_complete_user_data('username', $username)) {
            throw new \moodle_exception('cannotfinduser', '', '', s($username));
        }

        if (!$user->suspended) {
            complete_user_login($user);

            \core\session\manager::apply_concurrent_login_limit($user->id, session_id());

            // Restore the originally requested URL saved at signup time.
            // This must happen after complete_user_login() because session regeneration during login
            // destroys any $SESSION data set before that point. The preference was already cleaned
            // up by user_confirm(), so we use the value read before calling it.
            if ($emailwantsurl) {
                $SESSION->wantsurl = $emailwantsurl;
            }

            // Check where to go, $redirect has a higher preference.
            if (!empty($redirect)) {
                if (!empty($SESSION->wantsurl)) {
                    unset($SESSION->wantsurl);
                }
                redirect($redirect);
            }
        }

        $PAGE->navbar->add(get_string("confirmed"));
        $PAGE->set_title(get_string("confirmed"));
        $PAGE->set_heading($COURSE->fullname);
        echo $OUTPUT->header();
        echo $OUTPUT->box_start('generalbox centerpara boxwidthnormal boxaligncenter');
        echo "<h3>".get_string("thanks").", ". fullname($USER) . "</h3>\n";
        echo "<p>".get_string("confirmed")."</p>\n";
        // Calling core_login_get_return_url() consumes $SESSION->wantsurl; restore it so
        // that MFA intercepting the next page load can still redirect the user correctly.
        // Skip restore when profile completion is required — core_login_get_return_url()
        // intentionally preserves $SESSION->wantsurl in that case so it survives past user/edit.php.
        $returnurl = core_login_get_return_url();
        if (!user_not_fully_set_up($USER, true)) {
            $SESSION->wantsurl = $returnurl;
        }
        echo $OUTPUT->single_button($returnurl, get_string('continue'));
        echo $OUTPUT->box_end();
        echo $OUTPUT->footer();
        exit;
    } else {
        throw new \moodle_exception('invalidconfirmdata');
    }
} else {
    throw new \moodle_exception("errorwhenconfirming");
}

redirect("$CFG->wwwroot/");
