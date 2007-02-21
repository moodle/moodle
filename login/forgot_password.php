<?php
// $Id$
// forgot password routine.
// find the user and call the appropriate routine for their authentication
// type.

require_once('../config.php');
require_once('forgot_password_form.php');

$action     = optional_param('action', '', PARAM_ALPHA);
$p_secret   = optional_param('p', false, PARAM_RAW);
$p_username = optional_param('s', false, PARAM_RAW);

httpsrequired();

$sitecontext = get_context_instance(CONTEXT_SYSTEM, SITEID);

// setup text strings
$strcancel             = get_string('cancel');
$strconfirmednot       = get_string('confirmednot');
$stremail              = get_string('email');
$stremailnotfound      = get_string('emailnotfound');
$strerror              = get_string('error');
$strforgotten          = get_string('passwordforgotten');
$strforgottenduplicate = get_string('forgottenduplicate', 'moodle', get_admin()); // does not exist in lang file??
$strforgotteninstruct  = get_string('passwordforgotteninstructions');
$strinvalidemail       = get_string('invalidemail');
$strinvalidurl         = get_string('forgotteninvalidurl');
$strlogin              = get_string('login');
$strloginalready       = get_string('loginalready');
$strok                 = get_string('ok');
$strpasswordnohelp     = get_string('passwordnohelp');
$strsecretalreadyused  = get_string('secretalreadyused');
$strsenddetails        = get_string('senddetails');
$strusername           = get_string('username');
$strusernameemailmatch = get_string('usernameemailmatch');
$strusernamenotfound   = get_string('usernamenotfound');

$errors = array();
$page = ''; // page to display


// if you are logged in then you shouldn't be here!
if (isloggedin() && !isguestuser()) {
    redirect($CFG->wwwroot.'/index.php', $strloginalready, 5);
}

$mform = new login_forgot_password_form();

if ($mform->is_cancelled()) {
    redirect($CFG->httpswwwroot.'/login/index.php');
}

if ($action == 'find' and $param = $mform->get_data()) {
///=====================
/// find the user in the database and mail info
///=====================

    // first try the username
    if (!empty($param->username)) {
        if (!$user = get_complete_user_data('username', $param->username)) {
            $errors[] = $strusernamenotfound;
        }
    } else {
        $user = false;
    }

    // now try email
    if (!empty($param->email)) {
        // validate email address 1st
        if (!validate_email($param->email)) {
            $errors[] = $strinvalidemail;

        } else if (count_records('user', 'email', $param->email) > 1) {
            // (if there is more than one instance of the email then we
            // cannot complete automated recovery)
            $page = 'duplicateemail';
            $errors[] = $strforgottenduplicate;

        } else if (!$mailuser = get_complete_user_data('email', $param->email)) {
            $errors[] = $stremailnotfound;
        }

        // just in case they did specify both...
        // if $user exists then check they actually match (then just use $user)
        if (!empty($user) and !empty($mailuser)) {
            if ($user->id != $mailuser->id) {
                $errors[] = $strusernameemailmatch;
            }
            $user = $mailuser;
        }

        // use email user if username not used or located
        if (!empty($mailuser) and empty($user)) {
            $user = $mailuser;
        }
    }

    // if user located (and no errors) take the appropriate action
    if (empty($errors) and !empty($user)) {

         $userauth = get_auth_plugin($user->auth);

        // check this user isn't 'unconfirmed'
        if (empty($user->confirmed)) {
            $errors[] = $strconfirmednot;

        } else {
            if (method_exists($userauth, 'can_reset_password') and $userauth->can_reset_password()) {
                // reset internal password and notify user

                // set 'secret' string
                $user->secret = random_string(15);
                if (!set_field('user', 'secret', $user->secret, 'id', $user->id)) {
                    error('error setting user secret string');
                }

                // send email (make sure mail block is off)
                $user->mailstop = 0;
                if (!send_password_change_confirmation_email($user)) {
                    error('error sending password change confirmation email');
                }

                // display confirm message
                $page = 'emailconfirm';

            } else {
                // send email (make sure mail block is off)
                $user->mailstop = 0;
                if (!send_password_change_info($user)) {
                    error('error sending password change confirmation email');
                }

                // display confirm message
                $page = 'emailconfirm';
            }
        }
    }

    if (!empty($CFG->protectusernames)) {
        // do not give any hints about usernames or email!
        $errors = array();
        $page = 'emailmaybeconfirmed';
    }

    if (empty($param->username) and empty($param->email)) {
        // nothing supplied - show error in any case
        $errors[] = 'no email or username';
        $page = '';
    }


} else if ($p_secret !== false) {
///=====================
/// user clicked on link in email message
///=====================

    update_login_count();

    $user = get_complete_user_data('username', $p_username);

    if (!empty($user) and $user->secret === '') {
        $errors[] = $strsecretalreadyused;

    } else if (!empty($user) and $user->secret == stripslashes($p_secret)) {
        // make sure that url relates to a valid user

        // check this isn't guest user
        // TODO: add change password capability so that we can prevent participants to change password
        if (isguestuser($user) or has_capability('moodle/legacy:guest', $sitecontext, $user->id, false)) {
            error('You cannot reset the guest password');
        }

        // override email stop and mail new password
        $user->emailstop = 0;
        if (!reset_password_and_mail($user)) {
            error('Error resetting password and mailing you');
        }

        // Clear secret so that it can not be used again
        $user->secret = '';
        if (!set_field('user', 'secret', $user->secret, 'id', $user->id)) {
            error('Error resetting user secret string');
        }

        reset_login_count();
        $page = 'emailsent';

        $changepasswordurl = "{$CFG->httpswwwroot}/login/change_password.php";
        $a = new object();
        $a->email = $user->email;
        $a->link = $changepasswordurl;
        $stremailpasswordsent = get_string('emailpasswordsent', '', $a);
    } else {
       $errors[] = $strinvalidurl;
    }

}


//******************************
// DISPLAY PART
//******************************

print_header($strforgotten, $strforgotten,
    "<a href=\"{$CFG->wwwroot}/login/index.php\">{$strlogin}</a>->{$strforgotten}",
    'form.email');

if ($page == 'emailmaybeconfirmed') {
    // Print general confirmation message
    notice(get_string('emailpasswordconfirmmaybesent'), $CFG->wwwroot.'/index.php');
}


/// ---------------------------------------------
/// check $page for appropriate page to display
if ($page == 'emailconfirm') {
    // Confirm (internal method) email sent
    $protectedemail = preg_replace('/([^@]*)@(.*)/', '******@$2', $user->email); // obfuscate the email address to protect privacy
    $stremailpasswordconfirmsent = get_string('emailpasswordconfirmsent', '', $protectedemail);
    notice($stremailpasswordconfirmsent, $CFG->wwwroot.'/index.php');

} else if ($page == 'emailsent') {
    // mail sent with new password
    notice($stremailpasswordsent, $changepasswordurl);

} else if ($page == 'duplicateemail') {
    // email address appears more than once
    notice($strforgottenduplicate, $CFG->wwwroot.'/index.php');

} else {
    // display any errors
    if (!empty($errors)) {
        print_box_start('generalbox boxwidthnormal boxaligncenter');
        $s = $strerror;
        $s .= '<ul class="errors">';
        foreach ($errors as $error) {
            $s .= '<li>'.$error.'</li>';
        }
        $s .= '</ul>';
        notify($s, 'notifyproblem');
        print_box_end();
    }
}

if(!$mform->get_data() or !empty($errors)) {
    print_box_start('generalbox boxwidthnormal boxaligncenter');
    echo $strforgotteninstruct;
    print_box_end();
    $mform->display();
}

print_footer();

?>