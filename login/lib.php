<?php
define('PWRESET_STATUS_NOEMAILSENT', 1);
define('PWRESET_STATUS_TOKENSENT', 2);
define('PWRESET_STATUS_OTHEREMAILSENT', 3);

/*  This function processes a user's request to set a new password in the event they forgot the old one.
    If no user identifier has been supplied, it displays a form where they can submit their identifier.
    Where they have supplied identifier, the function will check their status, and send email as appropriate.
*/
function forgotpw_process_request() {
    global $DB, $OUTPUT, $CFG, $PAGE;
    $systemcontext = context_system::instance();
    $mform = new login_forgot_password_form();

    if ($mform->is_cancelled()) {
        redirect(get_login_url());

    } else if ($data = $mform->get_data()) {
        // Requesting user has submitted form data.
        // Find the user account in the database which the requesting user claims to own:
        if (!empty($data->username)) {
            // Username has been specified - load the user record based on that.
            $username = core_text::strtolower($data->username); // mimic the login page process, if they forget username they need to use email for reset
            $user = $DB->get_record('user', array('username'=>$username, 'mnethostid'=>$CFG->mnet_localhost_id, 'deleted'=>0, 'suspended'=>0));

        } else {
            // Try to load the user record based on email address:
            // this is tricky because
            // 1/ the email is not guaranteed to be unique - TODO: send email with all usernames to select the correct account for pw reset
            // 2/ mailbox may be case sensitive, the email domain is case insensitive - let's pretend it is all case-insensitive

            $select = $DB->sql_like('email', ':email', false, true, false, '|'). " AND mnethostid = :mnethostid AND deleted=0 AND suspended=0";
            $params = array('email'=>$DB->sql_like_escape($data->email, '|'), 'mnethostid'=>$CFG->mnet_localhost_id);
            $user = $DB->get_record_select('user', $select, $params, '*', IGNORE_MULTIPLE);
        }

        // Target user details have now been identified, or we know that there is no such account.
        // Send email address to account's email address if appropriate:
        $pwresetstatus = PWRESET_STATUS_NOEMAILSENT;
        if ($user and !empty($user->confirmed)) {
            $userauth = get_auth_plugin($user->auth);
            if ($userauth->can_reset_password() and is_enabled_auth($user->auth)
              and has_capability('moodle/user:changeownpassword', $systemcontext, $user->id)) {
                // send reset password confirmation

                // set 'secret' string
                $user->secret = random_string(15);
                $DB->set_field('user', 'secret', $user->secret, array('id'=>$user->id));

                if (send_password_change_confirmation_email($user)) {
                    $pwresetstatus = PWRESET_STATUS_TOKENSENT;
                } else {
                    print_error('cannotmailconfirm');
                }

            } else {
                if (send_password_change_info($user)) {
                    $pwresetstatus = PWRESET_STATUS_OTHEREMAILSENT;
                } else {
                    print_error('cannotmailconfirm');
                }
            }
        }

        // Any email has now been sent.
        // Next display results to requesting user if settings permit:
        echo $OUTPUT->header();

        if (!empty($CFG->protectusernames)) {
            // Neither confirm, nor deny existance of any username or email address in database.
            // Print general (non-commital) message
            notice(get_string('emailpasswordconfirmmaybesent'), $CFG->wwwroot.'/index.php');
            die; // never reached
        } elseif (empty($user)) {
            // Protect usernames is off, and we couldn't find the user with details specified.
            // Print failure advice:
            notice(get_string('emailpasswordconfirmnotsent'), $CFG->wwwroot.'/forgot_password.php');
            die; // never reached
        } elseif (empty($user->email)) {
            // User doesn't have an email set - can't send a password change confimation email.
            notice(get_string('emailpasswordconfirmnoemail'), $CFG->wwwroot.'/index.php');
            die; // never reached
        } elseif ($pwresetstatus == PWRESET_STATUS_NOEMAILSENT) {
            // User found, protectusernames is off, but user is not confirmed
            // Pretend we sent them an email
            // This is a big usability problem - need to tell users why we didn't send them an email
            $protectedemail = preg_replace('/([^@]*)@(.*)/', '******@$2', $user->email); // obfuscate the email address to protect privacy
            $stremailpasswordconfirmsent = get_string('emailpasswordconfirmsent', '', $protectedemail);
            notice($stremailpasswordconfirmsent, $CFG->wwwroot.'/index.php');
            die; // never reached
        } else {
            // Confirm email sent
            $protectedemail = preg_replace('/([^@]*)@(.*)/', '******@$2', $user->email); // obfuscate the email address to protect privacy
            // This is a small usability problem - may be obfuscating the email address which the user has just supplied.
            $stremailpasswordconfirmsent = get_string('emailpasswordconfirmsent', '', $protectedemail);
            notice($stremailpasswordconfirmsent, $CFG->wwwroot.'/index.php');
            die; // never reached
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
}

/*  This function processes a user's submitted token to validate the request to set a new password
    If the user's token is validated, they are emailed with a new password.
*/
function forgotpw_process_pwset($token, $username) {
    global $DB, $CFG, $OUTPUT;
    $systemcontext = context_system::instance();
    $user = $DB->get_record('user', array('username'=>$username, 'mnethostid'=>$CFG->mnet_localhost_id, 'deleted'=>0, 'suspended'=>0));

    if ($user and ($user->auth === 'nologin' or !is_enabled_auth($user->auth))) {
        // bad luck - user is not able to login, do not let them reset password
        $user = false;
    }

    if (!empty($user) and $user->secret === '') {
        echo $OUTPUT->header();
        print_error('secretalreadyused');
    } else if (!empty($user) and $user->secret == $token) {
        // make sure that url relates to a valid user

        // check this isn't guest user
        if (isguestuser($user)) {
            print_error('cannotresetguestpwd');
        }

        // Reset login lockout even of the password reset fails.
        login_unlock_account($user);

        // make sure user is allowed to change password
        require_capability('moodle/user:changeownpassword', $systemcontext, $user->id);

        if (!reset_password_and_mail($user)) {
            print_error('cannotresetmail');
        }

        // Clear secret so that it can not be used again
        $user->secret = '';
        $DB->set_field('user', 'secret', $user->secret, array('id'=>$user->id));

        $changepasswordurl = "{$CFG->httpswwwroot}/login/change_password.php";
        $a = new stdClass();
        $a->email = $user->email;
        $a->link = $changepasswordurl;

        echo $OUTPUT->header();
        notice(get_string('emailpasswordsent', '', $a), $changepasswordurl);

    } else {
        if (!empty($user) and strlen($token) === 15) {
            // somebody probably tries to hack in by guessing secret - stop them!
            $DB->set_field('user', 'secret', '', array('id'=>$user->id));
        }
        echo $OUTPUT->header();
        print_error('forgotteninvalidurl');
    }

    die; //never reached
}
?>
