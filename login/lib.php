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
 *
 * Login library file of login/password related Moodle functions.
 *
 * @package    core
 * @subpackage lib
 * @copyright  Catalyst IT
 * @copyright  Peter Bulmer
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define('PWRESET_STATUS_NOEMAILSENT', 1);
define('PWRESET_STATUS_TOKENSENT', 2);
define('PWRESET_STATUS_OTHEREMAILSENT', 3);
define('PWRESET_STATUS_ALREADYSENT', 4);

/**
 *  Processes a user's request to set a new password in the event they forgot the old one.
 *  If no user identifier has been supplied, it displays a form where they can submit their identifier.
 *  Where they have supplied identifier, the function will check their status, and send email as appropriate.
 */
function core_login_process_password_reset_request() {
    global $OUTPUT, $PAGE;
    $mform = new login_forgot_password_form();

    if ($mform->is_cancelled()) {
        redirect(get_login_url());

    } else if ($data = $mform->get_data()) {

        $username = $email = '';
        if (!empty($data->username)) {
            $username = $data->username;
        } else {
            $email = $data->email;
        }
        list($status, $notice, $url) = core_login_process_password_reset($username, $email);

        // Any email has now been sent.
        // Next display results to requesting user if settings permit.
        echo $OUTPUT->header();
        notice($notice, $url);
        die; // Never reached.
    }

    // DISPLAY FORM.

    echo $OUTPUT->header();
    echo $OUTPUT->box(get_string('passwordforgotteninstructions2'), 'generalbox boxwidthnormal boxaligncenter');
    $mform->display();

    echo $OUTPUT->footer();
}

/**
 * Process the password reset for the given user (via username or email).
 *
 * @param  string $username the user name
 * @param  string $email    the user email
 * @return array an array containing fields indicating the reset status, a info notice and redirect URL.
 * @since  Moodle 3.4
 */
function core_login_process_password_reset($username, $email) {
    global $CFG, $DB;

    if (empty($username) && empty($email)) {
        print_error('cannotmailconfirm');
    }

    // Next find the user account in the database which the requesting user claims to own.
    if (!empty($username)) {
        // Username has been specified - load the user record based on that.
        $username = core_text::strtolower($username); // Mimic the login page process.
        $userparams = array('username' => $username, 'mnethostid' => $CFG->mnet_localhost_id, 'deleted' => 0, 'suspended' => 0);
        $user = $DB->get_record('user', $userparams);
    } else {
        // Try to load the user record based on email address.
        // this is tricky because
        // 1/ the email is not guaranteed to be unique - TODO: send email with all usernames to select the account for pw reset
        // 2/ mailbox may be case sensitive, the email domain is case insensitive - let's pretend it is all case-insensitive.

        $select = $DB->sql_like('email', ':email', false, true, false, '|') .
                " AND mnethostid = :mnethostid AND deleted=0 AND suspended=0";
        $params = array('email' => $DB->sql_like_escape($email, '|'), 'mnethostid' => $CFG->mnet_localhost_id);
        $user = $DB->get_record_select('user', $select, $params, '*', IGNORE_MULTIPLE);
    }

    // Target user details have now been identified, or we know that there is no such account.
    // Send email address to account's email address if appropriate.
    $pwresetstatus = PWRESET_STATUS_NOEMAILSENT;
    if ($user and !empty($user->confirmed)) {
        $systemcontext = context_system::instance();

        $userauth = get_auth_plugin($user->auth);
        if (!$userauth->can_reset_password() or !is_enabled_auth($user->auth)
          or !has_capability('moodle/user:changeownpassword', $systemcontext, $user->id)) {
            if (send_password_change_info($user)) {
                $pwresetstatus = PWRESET_STATUS_OTHEREMAILSENT;
            } else {
                print_error('cannotmailconfirm');
            }
        } else {
            // The account the requesting user claims to be is entitled to change their password.
            // Next, check if they have an existing password reset in progress.
            $resetinprogress = $DB->get_record('user_password_resets', array('userid' => $user->id));
            if (empty($resetinprogress)) {
                // Completely new reset request - common case.
                $resetrecord = core_login_generate_password_reset($user);
                $sendemail = true;
            } else if ($resetinprogress->timerequested < (time() - $CFG->pwresettime)) {
                // Preexisting, but expired request - delete old record & create new one.
                // Uncommon case - expired requests are cleaned up by cron.
                $DB->delete_records('user_password_resets', array('id' => $resetinprogress->id));
                $resetrecord = core_login_generate_password_reset($user);
                $sendemail = true;
            } else if (empty($resetinprogress->timererequested)) {
                // Preexisting, valid request. This is the first time user has re-requested the reset.
                // Re-sending the same email once can actually help in certain circumstances
                // eg by reducing the delay caused by greylisting.
                $resetinprogress->timererequested = time();
                $DB->update_record('user_password_resets', $resetinprogress);
                $resetrecord = $resetinprogress;
                $sendemail = true;
            } else {
                // Preexisting, valid request. User has already re-requested email.
                $pwresetstatus = PWRESET_STATUS_ALREADYSENT;
                $sendemail = false;
            }

            if ($sendemail) {
                $sendresult = send_password_change_confirmation_email($user, $resetrecord);
                if ($sendresult) {
                    $pwresetstatus = PWRESET_STATUS_TOKENSENT;
                } else {
                    print_error('cannotmailconfirm');
                }
            }
        }
    }

    $url = $CFG->wwwroot.'/index.php';
    if (!empty($CFG->protectusernames)) {
        // Neither confirm, nor deny existance of any username or email address in database.
        // Print general (non-commital) message.
        $status = 'emailpasswordconfirmmaybesent';
        $notice = get_string($status);
    } else if (empty($user)) {
        // Protect usernames is off, and we couldn't find the user with details specified.
        // Print failure advice.
        $status = 'emailpasswordconfirmnotsent';
        $notice = get_string($status);
        $url = $CFG->wwwroot.'/forgot_password.php';
    } else if (empty($user->email)) {
        // User doesn't have an email set - can't send a password change confimation email.
        $status = 'emailpasswordconfirmnoemail';
        $notice = get_string($status);
    } else if ($pwresetstatus == PWRESET_STATUS_ALREADYSENT) {
        // User found, protectusernames is off, but user has already (re) requested a reset.
        // Don't send a 3rd reset email.
        $status = 'emailalreadysent';
        $notice = get_string($status);
    } else if ($pwresetstatus == PWRESET_STATUS_NOEMAILSENT) {
        // User found, protectusernames is off, but user is not confirmed.
        // Pretend we sent them an email.
        // This is a big usability problem - need to tell users why we didn't send them an email.
        // Obfuscate email address to protect privacy.
        $protectedemail = preg_replace('/([^@]*)@(.*)/', '******@$2', $user->email);
        $status = 'emailpasswordconfirmsent';
        $notice = get_string($status, '', $protectedemail);
    } else {
        // Confirm email sent. (Obfuscate email address to protect privacy).
        $protectedemail = preg_replace('/([^@]*)@(.*)/', '******@$2', $user->email);
        // This is a small usability problem - may be obfuscating the email address which the user has just supplied.
        $status = 'emailresetconfirmsent';
        $notice = get_string($status, '', $protectedemail);
    }
    return array($status, $notice, $url);
}

/**
 * This function processes a user's submitted token to validate the request to set a new password.
 * If the user's token is validated, they are prompted to set a new password.
 * @param string $token the one-use identifier which should verify the password reset request as being valid.
 * @return void
 */
function core_login_process_password_set($token) {
    global $DB, $CFG, $OUTPUT, $PAGE, $SESSION;
    require_once($CFG->dirroot.'/user/lib.php');

    $pwresettime = isset($CFG->pwresettime) ? $CFG->pwresettime : 1800;
    $sql = "SELECT u.*, upr.token, upr.timerequested, upr.id as tokenid
              FROM {user} u
              JOIN {user_password_resets} upr ON upr.userid = u.id
             WHERE upr.token = ?";
    $user = $DB->get_record_sql($sql, array($token));

    $forgotpasswordurl = "{$CFG->wwwroot}/login/forgot_password.php";
    if (empty($user) or ($user->timerequested < (time() - $pwresettime - DAYSECS))) {
        // There is no valid reset request record - not even a recently expired one.
        // (suspicious)
        // Direct the user to the forgot password page to request a password reset.
        echo $OUTPUT->header();
        notice(get_string('noresetrecord'), $forgotpasswordurl);
        die; // Never reached.
    }
    if ($user->timerequested < (time() - $pwresettime)) {
        // There is a reset record, but it's expired.
        // Direct the user to the forgot password page to request a password reset.
        $pwresetmins = floor($pwresettime / MINSECS);
        echo $OUTPUT->header();
        notice(get_string('resetrecordexpired', '', $pwresetmins), $forgotpasswordurl);
        die; // Never reached.
    }

    if ($user->auth === 'nologin' or !is_enabled_auth($user->auth)) {
        // Bad luck - user is not able to login, do not let them set password.
        echo $OUTPUT->header();
        print_error('forgotteninvalidurl');
        die; // Never reached.
    }

    // Check this isn't guest user.
    if (isguestuser($user)) {
        print_error('cannotresetguestpwd');
    }

    // Token is correct, and unexpired.
    $mform = new login_set_password_form(null, $user);
    $data = $mform->get_data();
    if (empty($data)) {
        // User hasn't submitted form, they got here directly from email link.
        // Next, display the form.
        $setdata = new stdClass();
        $setdata->username = $user->username;
        $setdata->username2 = $user->username;
        $setdata->token = $user->token;
        $mform->set_data($setdata);
        echo $OUTPUT->header();
        echo $OUTPUT->box(get_string('setpasswordinstructions'), 'generalbox boxwidthnormal boxaligncenter');
        $mform->display();
        echo $OUTPUT->footer();
        return;
    } else {
        // User has submitted form.
        // Delete this token so it can't be used again.
        $DB->delete_records('user_password_resets', array('id' => $user->tokenid));
        $userauth = get_auth_plugin($user->auth);
        if (!$userauth->user_update_password($user, $data->password)) {
            print_error('errorpasswordupdate', 'auth');
        }
        user_add_password_history($user->id, $data->password);
        if (!empty($CFG->passwordchangelogout)) {
            \core\session\manager::kill_user_sessions($user->id, session_id());
        }
        // Reset login lockout (if present) before a new password is set.
        login_unlock_account($user);
        // Clear any requirement to change passwords.
        unset_user_preference('auth_forcepasswordchange', $user);
        unset_user_preference('create_password', $user);

        if (!empty($user->lang)) {
            // Unset previous session language - use user preference instead.
            unset($SESSION->lang);
        }
        complete_user_login($user); // Triggers the login event.

        \core\session\manager::apply_concurrent_login_limit($user->id, session_id());

        $urltogo = core_login_get_return_url();
        unset($SESSION->wantsurl);
        redirect($urltogo, get_string('passwordset'), 1);
    }
}

/** Create a new record in the database to track a new password set request for user.
 * @param object $user the user record, the requester would like a new password set for.
 * @return record created.
 */
function core_login_generate_password_reset ($user) {
    global $DB;
    $resetrecord = new stdClass();
    $resetrecord->timerequested = time();
    $resetrecord->userid = $user->id;
    $resetrecord->token = random_string(32);
    $resetrecord->id = $DB->insert_record('user_password_resets', $resetrecord);
    return $resetrecord;
}

/**  Determine where a user should be redirected after they have been logged in.
 * @return string url the user should be redirected to.
 */
function core_login_get_return_url() {
    global $CFG, $SESSION, $USER;
    // Prepare redirection.
    if (user_not_fully_set_up($USER, true)) {
        $urltogo = $CFG->wwwroot.'/user/edit.php';
        // We don't delete $SESSION->wantsurl yet, so we get there later.

    } else if (isset($SESSION->wantsurl) and (strpos($SESSION->wantsurl, $CFG->wwwroot) === 0
            or strpos($SESSION->wantsurl, str_replace('http://', 'https://', $CFG->wwwroot)) === 0)) {
        $urltogo = $SESSION->wantsurl;    // Because it's an address in this site.
        unset($SESSION->wantsurl);
    } else {
        // No wantsurl stored or external - go to homepage.
        $urltogo = $CFG->wwwroot.'/';
        unset($SESSION->wantsurl);
    }

    // If the url to go to is the same as the site page, check for default homepage.
    if ($urltogo == ($CFG->wwwroot . '/')) {
        $homepage = get_home_page();
        // Go to my-moodle page instead of site homepage if defaulthomepage set to homepage_my.
        if ($homepage == HOMEPAGE_MY && !is_siteadmin() && !isguestuser()) {
            if ($urltogo == $CFG->wwwroot or $urltogo == $CFG->wwwroot.'/' or $urltogo == $CFG->wwwroot.'/index.php') {
                $urltogo = $CFG->wwwroot.'/my/';
            }
        }
    }
    return $urltogo;
}

/**
 * Validates the forgot password form data.
 *
 * This is used by the forgot_password_form and by the core_auth_request_password_rest WS.
 * @param  array $data array containing the data to be validated (email and username)
 * @return array array of errors compatible with mform
 * @since  Moodle 3.4
 */
function core_login_validate_forgot_password_data($data) {
    global $CFG, $DB;

    $errors = array();

    if ((!empty($data['username']) and !empty($data['email'])) or (empty($data['username']) and empty($data['email']))) {
        $errors['username'] = get_string('usernameoremail');
        $errors['email']    = get_string('usernameoremail');

    } else if (!empty($data['email'])) {
        if (!validate_email($data['email'])) {
            $errors['email'] = get_string('invalidemail');

        } else if ($DB->count_records('user', array('email' => $data['email'])) > 1) {
            $errors['email'] = get_string('forgottenduplicate');

        } else {
            if ($user = get_complete_user_data('email', $data['email'])) {
                if (empty($user->confirmed)) {
                    send_confirmation_email($user);
                    $errors['email'] = get_string('confirmednot');
                }
            }
            if (!$user and empty($CFG->protectusernames)) {
                $errors['email'] = get_string('emailnotfound');
            }
        }

    } else {
        if ($user = get_complete_user_data('username', $data['username'])) {
            if (empty($user->confirmed)) {
                send_confirmation_email($user);
                $errors['email'] = get_string('confirmednot');
            }
        }
        if (!$user and empty($CFG->protectusernames)) {
            $errors['username'] = get_string('usernamenotfound');
        }
    }

    return $errors;
}

/**
 * Plugins can create pre sign up requests.
 */
function core_login_pre_signup_requests() {
    $callbacks = get_plugins_with_function('pre_signup_requests');
    foreach ($callbacks as $type => $plugins) {
        foreach ($plugins as $plugin => $pluginfunction) {
            $pluginfunction();
        }
    }
}
