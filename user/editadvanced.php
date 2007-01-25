<?php // $Id$

    require_once('../config.php');
    require_once($CFG->libdir.'/gdlib.php');
    require_once($CFG->libdir.'/adminlib.php');
    require_once($CFG->dirroot.'/user/editadvanced_form.php');

    $id     = optional_param('id', $USER->id, PARAM_INT);    // user id; -1 if creating new user
    $course = optional_param('course', SITEID, PARAM_INT);   // course id (defaults to Site)

    if (!$course = get_record('course', 'id', $course)) {
        error('Course ID was incorrect');
    }
    require_login($course->id);
    httpsrequired(); // HTTPS is potentially required in this page because there are passwords

    if ($id == -1) {
        // creating new user
        require_capability('moodle/user:create', get_context_instance(CONTEXT_SYSTEM, SITEID));
        $user = new object();
        $user->id = -1;
        $user->auth = 'manual';
        $user->confirmed = 1;
    } else {
        // editing existing user
        require_capability('moodle/user:update', get_context_instance(CONTEXT_SYSTEM, SITEID));
        if (!$user = get_record('user', 'id', $id)) {
            error('User ID was incorrect');
        }
    }

    // remote users cannot be edited
    if ($user->id != -1 and is_mnet_remote_user($user)) {
        redirect($CFG->wwwroot . "/user/view.php?id=$id&course={$course->id}");
    }

    $mainadmin = get_admin();
    if ($user->id != $USER->id and $user->id == $mainadmin->id) {  // Can't edit primary admin
        print_error('adminprimarynoedit');
    }

    if (isguestuser($user->id)) { // the real guest user can not be edited
        print_error('guestnoeditprofileother');
    }

    //load preferences
    if (!empty($user->id) and $preferences = get_user_preferences(null, null, $user->id)) {
        foreach($preferences as $name=>$value) {
            $user->{'preference_'.$name} = $value;
        }
    }
    //TODO: Load the custom profile fields

    //create form
    $userform = new user_editadvanced_form(null, $course);
    $userform->set_data($user);

    if ($usernew = $userform->get_data()) {
        add_to_log($course->id, 'user', 'update', "view.php?id=$user->id&course=$course->id", '');

        $authplugin = get_auth_plugin($CFG->auth);

        $usernew->timemodified = time();

        if ($usernew->id == -1) {
            unset($usernew->id);
            $usernew->mnethostid = $CFG->mnet_localhost_id; // always local user
            $usernew->confirmed = 1;
            if (!$usernew->id = insert_record('user', $usernew)) {
                error('Error creating user record');
            }
        } else {
            if (update_record('user', $usernew)) {
                if (method_exists($authplugin, 'user_update')){
                    // pass a true $userold here
                    if (! $authplugin->user_update($user, $userform->get_data(false))) {
                        // auth update failed, rollback for moodle
                        update_record('user', addslashes_object($user));
                        error('Failed to update user data on external auth: '.$usernew->auth.
                                '. See the server logs for more details.');
                    }
                };
            } else {
                error('Error updating user record');
            }
        }

        //set new password if specified
        if (!empty($usernew->newpassword)) {
            if ($authplugin->can_change_password()) {
                if (method_exists($authplugin, 'user_update_password')){
                    if (!$authplugin->user_update_password($user->username, $usernew->newpassword)){
                        error('Failed to update password on external auth: ' . $usernew->auth .
                                '. See the server logs for more details.');
                    }
                } else {
                    error('Your external authentication module is misconfigued!');
                }
            }
        }

        //update preferences
        $ua = (array)$usernew;
        foreach($ua as $key=>$value) {
            if (strpos($key, 'preference_') === 0) {
                $name = substr($key, strlen('preference_'));
                set_user_preference($name, $value, $usernew->id);
            }
        }

        //update user picture
        if ($usernew->deletepicture) {
            //TODO - delete the files
            set_field('user', 'picture', 0, 'id', $usernew->id);
        } else if ($usernew->picture = save_profile_image($usernew->id, $userform->get_um(), 'users')) {
            set_field('user', 'picture', 1, 'id', $usernew->id);
        }

        // update mail bounces
        if ($user->email != $usernew->email) {
            set_bounce_count($usernew,true);
            set_send_count($usernew,true);
        }

        /// Update forum track preference.
        if (($usernew->trackforums != $user->trackforums) && !$usernew->trackforums) {
            require_once($CFG->dirroot.'/mod/forum/lib.php');
            forum_tp_delete_read_records($usernew->id);
        }

        //TODO: Save the custom profile fields

        if ($user->id == $USER->id) {
            // Override old $USER session variable
            $usernew = (array)get_record('user', 'id', $newuser->id); // reload from db
            foreach ($usernew as $variable => $value) {
                $USER->$variable = $value;
            }
            if (!empty($USER->newadminuser)) {
                unset($USER->newadminuser);
                // redirect to admin/ to continue with installation
                redirect("$CFG->wwwroot/$CFG->admin/");
            } else { 
                redirect("$CFG->wwwroot/user/view.php?id=$USER->id&course=$course->id");
            }
        } else {
            redirect("$CFG->wwwroot/$CFG->admin/user.php");
        }
        //never reached
    }


/// Display page header
    if ($user->id == -1 or ($user->id != $USER->id)) {
        $adminroot = admin_get_root();
        if ($user->id == -1) {
            admin_externalpage_setup('addnewuser', $adminroot);
            admin_externalpage_print_header($adminroot);
        } else {
            admin_externalpage_setup('editusers', $adminroot);
            admin_externalpage_print_header($adminroot);
            $userfullname = fullname($user, true);
            print_heading($userfullname);
        }
    } else if (!empty($USER->newadminuser)) {
        print_header();
        print_simple_box(get_string('configintroadmin', 'admin'), 'center', '50%');
        echo '<br />';
    } else {
        $streditmyprofile = get_string('editmyprofile');
        $strparticipants = get_string('participants');
        $strnewuser = get_string('newuser');
        $userfullname = fullname($user, true);
        if ($course->id != SITEID) {
            print_header("$course->shortname: $streditmyprofile", "$course->fullname: $streditmyprofile",
                         "<a href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</a>
                          -> <a href=\"index.php?id=$course->id\">$strparticipants</a>
                          -> <a href=\"view.php?id=$user->id&amp;course=$course->id\">$userfullname</a>
                          -> $streditmyprofile", "");
        } else {
            print_header("$course->shortname: $streditmyprofile", "$course->fullname",
                         "<a href=\"view.php?id=$user->id&amp;course=$course->id\">$userfullname</a>
                          -> $streditmyprofile", "");
        }
        /// Print tabs at the top
        $showroles = 1;
        $currenttab = 'editprofile';
        require('tabs.php');
    }

/// Finally display THE form
    $userform->display();

/// and proper footer
    if ($user->id == -1 or ($user->id != $USER->id)) {
        admin_externalpage_print_footer($adminroot);
    } else if (!empty($USER->newadminuser)) {
        print_footer('none');
    } else {
        print_footer($course);
    }

?>
