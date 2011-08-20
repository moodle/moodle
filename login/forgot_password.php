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
 * @package    core
 * @subpackage auth
 * @copyright  1999 onwards Martin Dougiamas  http://dougiamas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../config.php');
require_once('forgot_password_form.php');

$p_secret   = optional_param('p', false, PARAM_RAW);
$p_username = optional_param('s', false, PARAM_RAW);

//HTTPS is required in this page when $CFG->loginhttps enabled
$PAGE->https_required();

$PAGE->set_url('/login/forgot_password.php');
$systemcontext = get_context_instance(CONTEXT_SYSTEM);
$PAGE->set_context($systemcontext);

// setup text strings
$strforgotten = get_string('passwordforgotten');
$strlogin     = get_string('login');

$PAGE->navbar->add($strlogin, get_login_url());
$PAGE->navbar->add($strforgotten);
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

if ($p_secret !== false) {
///=====================
/// user clicked on link in email message
///=====================

    update_login_count();

    $user = $DB->get_record('user', array('username'=>$p_username, 'mnethostid'=>$CFG->mnet_localhost_id, 'deleted'=>0, 'suspended'=>0));

    if ($user and ($user->auth === 'nologin' or !is_enabled_auth($user->auth))) {
        // bad luck - user is not able to login, do not let them reset password
        $user = false;
    }

    if (!empty($user) and $user->secret === '') {
        echo $OUTPUT->header();
        print_error('secretalreadyused');
    } else if (!empty($user) and $user->secret == $p_secret) {
        // make sure that url relates to a valid user

        // check this isn't guest user
        if (isguestuser($user)) {
            print_error('cannotresetguestpwd');
        }

        // make sure user is allowed to change password
        require_capability('moodle/user:changeownpassword', $systemcontext, $user->id);

        if (!reset_password_and_mail($user)) {
            print_error('cannotresetmail');
        }

        // Clear secret so that it can not be used again
        $user->secret = '';
        $DB->set_field('user', 'secret', $user->secret, array('id'=>$user->id));

        reset_login_count();

        $changepasswordurl = "{$CFG->httpswwwroot}/login/change_password.php";
        $a = new stdClass();
        $a->email = $user->email;
        $a->link = $changepasswordurl;

        echo $OUTPUT->header();
        notice(get_string('emailpasswordsent', '', $a), $changepasswordurl);

    } else {
        if (!empty($user) and strlen($p_secret) === 15) {
            // somebody probably tries to hack in by guessing secret - stop them!
            $DB->set_field('user', 'secret', '', array('id'=>$user->id));
        }
        echo $OUTPUT->header();
        print_error('forgotteninvalidurl');
    }

    die; //never reached
}

$mform = new login_forgot_password_form();

if ($mform->is_cancelled()) {
    redirect(get_login_url());

} else if ($data = $mform->get_data()) {
/// find the user in the database and mail info

    // first try the username
    if (!empty($data->username)) {
        $username = textlib_get_instance()->strtolower($data->username); // mimic the login page process, if they forget username they need to use email for reset
        $user = $DB->get_record('user', array('username'=>$username, 'mnethostid'=>$CFG->mnet_localhost_id, 'deleted'=>0, 'suspended'=>0));

    } else {
        // this is tricky because
        // 1/ the email is not guaranteed to be unique - TODO: send email with all usernames to select the correct account for pw reset
        // 2/ mailbox may be case sensitive, the email domain is case insensitive - let's pretend it is all case-insensitive

        $select = $DB->sql_like('email', ':email', false, true, false, '|'). " AND mnethostid = :mnethostid AND deleted=0 AND suspended=0";
        $params = array('email'=>$DB->sql_like_escape($data->email, '|'), 'mnethostid'=>$CFG->mnet_localhost_id);
        $user = $DB->get_record_select('user', $select, $params, '*', IGNORE_MULTIPLE);
    }

    if ($user and !empty($user->confirmed)) {

        $userauth = get_auth_plugin($user->auth);
        if (has_capability('moodle/user:changeownpassword', $systemcontext, $user->id)) {
            // send email
        }

        if ($userauth->can_reset_password() and is_enabled_auth($user->auth)
          and has_capability('moodle/user:changeownpassword', $systemcontext, $user->id)) {
            // send reset password confirmation

            // set 'secret' string
            $user->secret = random_string(15);
            $DB->set_field('user', 'secret', $user->secret, array('id'=>$user->id));

            if (!send_password_change_confirmation_email($user)) {
                print_error('cannotmailconfirm');
            }

        } else {
            if (!send_password_change_info($user)) {
                print_error('cannotmailconfirm');
            }
        }
    }

    echo $OUTPUT->header();

    if (empty($user->email) or !empty($CFG->protectusernames)) {
        // Print general confirmation message
        notice(get_string('emailpasswordconfirmmaybesent'), $CFG->wwwroot.'/index.php');

    } else {
        // Confirm email sent
        $protectedemail = preg_replace('/([^@]*)@(.*)/', '******@$2', $user->email); // obfuscate the email address to protect privacy
        $stremailpasswordconfirmsent = get_string('emailpasswordconfirmsent', '', $protectedemail);
        notice($stremailpasswordconfirmsent, $CFG->wwwroot.'/index.php');
    }

    die; // never reached
}

// make sure we really are on the https page when https login required
$PAGE->verify_https_required();


/// DISPLAY FORM

echo $OUTPUT->header();
echo $OUTPUT->box(get_string('passwordforgotteninstructions2'), 'generalbox boxwidthnormal boxaligncenter');
$mform->display();

echo $OUTPUT->footer();
