<?php  
// $Id$
// forgot password routine. 
// find the user and call the appropriate routine for their authentication
// type.

require_once('../config.php');
httpsrequired();


//******************************
// GET PARAMS AND STRINGS
//******************************

// parameters from form
$param = new StdClass;
$param->action = optional_param( 'action','',PARAM_ALPHA );
$param->email = optional_param( 'email','',PARAM_CLEAN );
$param->p = optional_param( 'p','',PARAM_CLEAN );
$param->s = optional_param( 's','',PARAM_CLEAN );
$param->username = optional_param( 'username','',PARAM_CLEAN );

// setup text strings
$txt = new StdClass;
$txt->cancel = get_string('cancel');
$txt->confirmednot = get_string('confirmednot');
$txt->email = get_string('email');
$txt->emailnotfound = get_string('emailnotfound');
$txt->forgotten = get_string('passwordforgotten');
$txt->forgottenduplicate = get_string('forgottenduplicate','moodle',get_admin() );
$txt->forgotteninstructions = get_string('passwordforgotteninstructions');
$txt->invalidemail = get_string('invalidemail');
$txt->login = get_string('login');
$txt->loginalready = get_string('loginalready');
$txt->ok = get_string('ok');
$txt->passwordextlink = get_string('passwordextlink');
$txt->passwordnohelp = get_string('passwordnohelp');
$txt->senddetails = get_string('senddetails');
$txt->username = get_string('username');
$txt->usernameemailmatch = get_string('usernameemailmatch');
$txt->usernamenotfound = get_string('usernamenotfound');

$sesskey = sesskey();
$errors = array();
$page = ''; // page to display


//******************************
// PROCESS ACTIONS
//******************************

// if you are logged in then you shouldn't be here!
if (isloggedin()) {
    redirect( $CFG->wwwroot, $txt->loginalready, 5 );
}

// changepassword link replaced by individual auth setting
$auth = $CFG->auth; // the 'default' authentication method
if (!empty($CFG->changepassword)) {
    if (empty($CFG->{'auth_'.$auth.'_changepasswordurl'})) {
       set_config('auth_'.$auth.'_changepasswordurl',$CFG->changepassword );
    }
    set_config('changepassword','');
}        
 
// ACTION = FIND
if ($param->action=='find' and confirm_sesskey()) {
    // find the user in the database

    // first try the username
    if (!empty($param->username)) {
        if (!$user=get_complete_user_data('username',$param->username)) {
            $errors[] = $txt->usernamenotfound;
        }
    }

    // now try email
    if (!empty($param->email)) {
        // validate email address 1st
        if (!validate_email( $param->email )) {
            $errors[] = $txt->invalidemail;
        }
        elseif (count_records('user','email',$param->email) > 1) {
            // (if there is more than one instance of the email then we
            // cannot complete automated recovery)
            $page = 'duplicateemail';

            // just clear everything - we drop through to message page
            unset( $user );
            unset( $email );
            $errors = array();
        }
        elseif (!$mailuser = get_complete_user_data('email',$param->email)) {
            $errors[] = $txt->emailnotfound;
        }

        // just in case they did specify both...
        // if $user exists then check they actually match (then just use $user)
        if (!empty($user) and !empty($mailuser)) {
            if ($user->id != $mailuser->id) {
                $errors[] = $txt->usernameemailmatch;
            }
        $user = $mailuser;
        }

        // use email user if username not used or located
        if (!empty($mailuser) and empty($user)) {
            $user = $mailuser;
        }
    }

    // if user located (and no errors) take the appropriate action
    if (!empty($user) and (count($errors)==0)) {
        // check this user isn't 'unconfirmed'
        if (empty($user->confirmed)) {
            $errors[] = $txt->confirmednot;
        }
        else {
            // what to do depends on the authentication method
            $authmethod = $user->auth;
            if (is_internal_auth( $authmethod ) or !empty($CFG->{'auth_'.$authmethod.'_stdchangepassword'})) {
                // handle internal authentication
                
                // set 'secret' string
                $user->secret = random_string( 15 );
                if (!set_field('user','secret',$user->secret,'id',$user->id)) {
                    error( 'error setting user secret string' );
                }

                // send email (make sure mail block is off)
                $user->mailstop = 0;
                if (!send_password_change_confirmation_email($user)) {
                    error( 'error sending password change confirmation email' );
                }
 
                // display confirm message
                $page = 'emailconfirm';
            }
            else {
                // handle some 'external' authentication
                // if help text defined then we are going to display another page
                $txt->extmessage = '';
                $continue = false;
                if (!empty( $CFG->{'auth_'.$authmethod.'_changepasswordhelp'} )) {
                    $txt->extmessage = $CFG->{'auth_'.$authmethod.'_changepasswordhelp'}.'<br /><br />';
                }
                // if url defined then add that to the message (with a standard message)
                if (!empty( $CFG->{'auth_'.$authmethod.'_changepasswordurl'} )) {
                    $txt->extmessage .= $txt->passwordextlink . '<br /><br />';
                    $link = $CFG->{'auth_'.$authmethod.'_changepasswordurl'};
                    $txt->extmessage .= "<a href=\"$link\">$link</a>";
                }
                // if nothing to display, just do message that we can't help
                if (empty($txt->extmessage)) {
                    $txt->extmessage = $txt->passwordextlink;
                    $continue = true;
                }
                $page = 'external';
            }
        }
    }

    // nothing supplied - error
    if (empty($param->username) and empty($param->email)) {
        $errors[] = 'no email or username';
    }
}

// ACTION = AUTHENTICATE
if (!empty($param->p) and !empty($param->s)) {

    update_login_count();
    $user = get_complete_user_data('username',$s);

    // make sure that url relates to a valid user
    if (!empty($user)) {
        // check this isn't guest user
        if (isguest( $user->id )) {
            error('You cannot change the guest password');
        }

        // override email stop and mail new password
        $user->emailstop = 0;
        if (!reset_password_and_mail($user)) {
            error( 'Error resetting password and mailing you' );
        }

        reset_login_count();
        $page = 'emailsent';
       
        $changepasswordurl = "{$CFG->httpswwwroot}/login/change_password.php?action=forgot";
        $a->email = $user->email;
        $a->link = $changepasswordurl;
        $txt->emailpasswordsent = get_string( 'emailpasswordsent', '', $a );
    }

}


//******************************
// DISPLAY PART
//******************************
 
print_header( $txt->forgotten, $txt->forgotten,
    "<a href=\"{$CFG->wwwroot}/login/index.php\">{$txt->login}</a>->{$txt->forgotten}",
    'form.email' );
print_simple_box_start('center');

// display any errors
if (count($errors)) {
    echo "<ul class=\"errors\">\n";
    foreach ($errors as $error) {
        echo "    <li>$error</li>\n";
    }
    echo "</ul>\n";
}

// check $page for appropriate page to display
if ($page=='emailconfirm') {
    // Confirm (internal method) email sent
    $txt->emailpasswordconfirmsent = get_string( 'emailpasswordconfirmsent','',$user->email );
    notice( $txt->emailpasswordconfirmsent,"$CFG->wwwroot/" ); 
}

elseif ($page=='external') { 
    // display change password help text
    print_simple_box( $txt->extmessage, 'center', '50%','','20','noticebox' );

    // only print continue button if it makes sense
    if ($continue) {
        print_continue( "{$CFG->wwwroot}/" );
    }
}

elseif ($page=='emailsent') {
    // mail sent with new password
    notice( $txt->emailpasswordsent, $changepasswordurl );
}

elseif ($page=='duplicateemail') {
    // email address appears more than once
    notice( $txt->forgottenduplicate, "{$CFG->wwwroot}/" );
}

else {
?>

<p><?php echo $txt->forgotteninstructions; ?></p>

<form action="forgot_password.php" method="post">
    <input type="hidden" name="sesskey" value="<?php echo $sesskey; ?>" />
    <input type="hidden" name="action" value="find" />
    <table id="forgottenpassword">
        <tr>
            <td><?php echo $txt->username; ?></td>
            <td><input type="text" name="username" size="25" /></td>
        </tr>
        <tr>
            <td><?php echo $txt->email; ?></td>
            <td><input type="text" name="email" size="25" /></td>
        </tr>
        <tr>
             <td>&nbsp;</td>
             <td><input type="submit" value="<?php echo $txt->ok; ?>" />
                 <input type="button" value="<?php echo $txt->cancel; ?>" 
                 onclick="javascript: history.go(-1)" /></td>
        </tr>
    </table>   
    

</form>

<?php
}

print_simple_box_end();
print_footer();
?>



