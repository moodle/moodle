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
$strpasswordextlink    = get_string('passwordextlink');
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
        // check this user isn't 'unconfirmed'
        if (empty($user->confirmed)) {
            $errors[] = $strconfirmednot;
        } else {
            // what to do depends on the authentication method
            $userauth = get_auth_plugin($user->auth);
            if ($userauth->is_internal() or $userauth->can_change_password()) {
                // handle internal authentication

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
                // handle some 'external' authentication
                // if help text defined then we are going to display another page
                $strextmessage = '';
                $continue = false;
                if (!empty($userauth->config->changepasswordhelp)) {
                    $txt->extmessage = $userauth->config->changepasswordhelp .'<br /><br />';
                }
                // if url defined then add that to the message (with a standard message)
                if (method_exists($userauth, 'change_password_url') and $userauth->change_password_url()) {
                    $strextmessage .= $strpasswordextlink . '<br /><br />';
                    $strextmessage .= '<a href="' . $userauth->change_password_url() . '">' . $userauth->change_password_url() . '</a>';
                }
                // if nothing to display, just do message that we can't help
                if (empty($strextmessage)) {
                    $strextmessage = $strpasswordextlink;
                    $continue = true;
                }
                $page = 'external';
            }
        }
    }

    if ($page != 'external' and !empty($CFG->protectusernames)) {
        // do not give any hints about usernames or email!
        $errors = array();
        $page = 'emailmaybeconfirmed';
    }

    // nothing supplied - show error in any case
    if (empty($param->username) and empty($param->email)) {
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
        if ($user->username == 'guest' or has_capability('moodle/legacy:guest', $sitecontext, $user->id, false)) {
            error('You cannot change the guest password');
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

} else if ($page == 'external') {
    // display change password help text
    print_simple_box($strextmessage, 'center', '50%', '', '20', 'noticebox');

    // only print continue button if it makes sense
    if ($continue) {
        print_continue($CFG->wwwroot.'/index.php');
    }

} else if ($page == 'emailsent') {
    // mail sent with new password
    notice($stremailpasswordsent, $changepasswordurl);

} else if ($page == 'duplicateemail') {
    // email address appears more than once
    notice($strforgottenduplicate, $CFG->wwwroot.'/index.php');

} else {
    echo '<br />';
    print_simple_box_start('center', '50%', '', '20');

    // display any errors
    if (!empty($errors)) {
        $s = $strerror;
        $s .= '<ul class="errors">';
        foreach ($errors as $error) {
            $s .= '<li>'.$error.'</li>';
        }
        $s .= '</ul>';
        notify($s, 'notifyproblem');
    }

}

if(!$mform->get_data()) {
    echo $strforgotteninstruct;
    $mform->display();
}
print_simple_box_end();

print_footer();

?>