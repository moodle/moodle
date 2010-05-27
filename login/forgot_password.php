<?php

// forgot password routine.
// find the user and call the appropriate routine for their authentication
// type.

require_once('../config.php');
require_once('forgot_password_form.php');

$p_secret   = optional_param('p', false, PARAM_RAW);
$p_username = optional_param('s', false, PARAM_RAW);

httpsrequired();

$systemcontext = get_context_instance(CONTEXT_SYSTEM);

// setup text strings
$strforgotten = get_string('passwordforgotten');
$strlogin     = get_string('login');

$PAGE->set_url('/login/forgot_password.php');
$PAGE->navbar->add($strlogin, get_login_url());
$PAGE->navbar->add($strforgotten);

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

    $PAGE->set_title($strforgotten);
    $PAGE->set_heading($COURSE->fullname);

    $user = get_complete_user_data('username', $p_username);
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

        // override email stop and mail new password
        $user->emailstop = 0;
        if (!reset_password_and_mail($user)) {
            print_error('cannotresetmail');
        }

        // Clear secret so that it can not be used again
        $user->secret = '';
        $DB->set_field('user', 'secret', $user->secret, array('id'=>$user->id));

        reset_login_count();

        $changepasswordurl = "{$CFG->httpswwwroot}/login/change_password.php";
        $a = new object();
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
        $user = get_complete_user_data('username', $data->username);
    } else {

        $user = get_complete_user_data('email', $data->email);
    }

    if ($user and !empty($user->confirmed)) {

        $userauth = get_auth_plugin($user->auth);
        if (has_capability('moodle/user:changeownpassword', $systemcontext, $user->id)) {
            // send email (make sure mail block is off)
            $user->emailstop = 0;
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

    $PAGE->set_title($strforgotten);
    $PAGE->set_heading($COURSE->fullname);
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


/// DISPLAY FORM
$PAGE->set_title($strforgotten);
$PAGE->set_heading($COURSE->fullname);
$PAGE->set_focuscontrol('id_email');

echo $OUTPUT->header();
echo $OUTPUT->box(get_string('passwordforgotteninstructions2'), 'generalbox boxwidthnormal boxaligncenter');
$mform->display();

echo $OUTPUT->footer();
