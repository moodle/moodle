<?PHP // $Id$

    require_once('../config.php');

    $id = optional_param('id', SITEID);

    //HTTPS is potentially required in this page
    httpsrequired();

    if (!$course = get_record('course', 'id', $id)) {
        error('No such course!');
    }

    if (empty($USER->preference['auth_forcepasswordchange'])) {  // Don't redirect if they just got sent here
        require_login($id);
    }
    
    if ($frm = data_submitted()) {

        validate_form($frm, $err);

        check_for_restricted_user($frm->username);

        update_login_count();

        if (!count((array)$err)) {
            $username = $frm->username;
            $password = md5($frm->newpassword1);

            $user = get_complete_user_data('username', $username);

            if (isguest($user->id)) {
                error('Can\'t change guest password!');
            }
            
            if (is_internal_auth($user->auth)){
                if (set_field('user', 'password', $password, 'username', $username)) {
                    $user->password = $password;
                } else {
                    error('Could not set the new password');
                }
            } else { // external users
                // the relevant auth libs should be loaded already 
                // as validate_form() calls authenticate_user_login()
                // check that we allow changes through moodle
                if (!empty($CFG->{'auth_'. $user->auth.'_stdchangepassword'})) {
                    if (function_exists('auth_user_update_password')){
                        // note that we pass cleartext password 
                        if (auth_user_update_password($user->username, $frm->newpassword1)){
                            $user->password = $password;
                        } else {
                            error('Could not set the new password');
                        }
                    } else {
                        error('The authentication module is misconfigured (missing auth_user_update_password)'); 
                    } 
                } else {
                    error('You cannot change your password this way.');
                }
            }
            
            /// Are we admin logged in as someone else? If yes then we need to retain our real identity.
            if (!empty($USER->realuser)) {
                $realuser = $USER->realuser;
            }
            
            $USER = clone($user); // Get a fresh copy

            if (!empty($realuser)) {
                $USER->realuser = $realuser;
            }

            // register success changing password
            unset_user_preference('auth_forcepasswordchange', $user);

            set_moodle_cookie($USER->username);

            reset_login_count();

            $strpasswordchanged = get_string('passwordchanged');

            add_to_log($course->id, 'user', 'change password', "view.php?id=$user->id&amp;course=$course->id", "$user->id");

            $fullname = fullname($USER, true);

            if ($course->id != SITEID) {
                $navstr = "<a href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</a> -> ";
            } else {
                $navstr = '';
            }
            $navstr .= "<a href=\"$CFG->wwwroot/user/index.php?id=$course->id\">".get_string("participants")."</a> -> <a href=\"$CFG->wwwroot/user/view.php?id=$USER->id&amp;course=$course->id\">$fullname</a> -> $strpasswordchanged";

            print_header($strpasswordchanged, $strpasswordchanged, $navstr);

            notice($strpasswordchanged, "$CFG->wwwroot/user/view.php?id=$USER->id&amp;course=$id");

            print_footer();
            exit;
        }
    }

    // We NEED to set this, because the form assumes it has a value!
    $frm->id = empty($course->id) ? 0 : $course->id;

    if (empty($frm->username)) {
        $frm->username = $USER->username;
    }

    if (!empty($frm->username)) {
        $focus = 'form.password';
    } else {
        $focus = 'form.username';
    }

    $strchangepassword = get_string('changepassword');

    $fullname = fullname($USER, true);

    if ($course->id != SITEID) {
        $navstr = "<a href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</a> -> ";
    } else {
        $navstr = '';
    }
    $navstr .= "<a href=\"$CFG->wwwroot/user/index.php?id=$course->id\">".get_string('participants')."</a> -> <a href=\"$CFG->wwwroot/user/view.php?id=$USER->id&amp;course=$course->id\">$fullname</a> -> $strchangepassword";

    print_header($strchangepassword, $strchangepassword, $navstr, $focus);

    print_simple_box_start('center');
    include('change_password_form.html');
    print_simple_box_end();
    print_footer();




/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/
function validate_form($frm, &$err) {

    if (empty($frm->username)){
        $err->username = get_string('missingusername');
    } else {
        if (!isadmin() and empty($frm->password)){
            $err->password = get_string('missingpassword');
        } else {  
            //require non adminusers to give valid password
            if (!isadmin() && !authenticate_user_login($frm->username, $frm->password)){
                $err->password = get_string('wrongpassword');
            }
        }
    }

    if (empty($frm->newpassword1)){
        $err->newpassword1 = get_string('missingnewpassword');
    }

    if (empty($frm->newpassword2)){
        $err->newpassword2 = get_string('missingnewpassword');
    } else {
        if ($frm->newpassword1 <> $frm->newpassword2) {
            $err->newpassword2 = get_string('passwordsdiffer');
        } else {
            if(!isadmin() and ($frm->password === $frm->newpassword1)){
                $err->newpassword1 = get_string('mustchangepassword');
            }
        }
    }
    
    return;
}

?>
