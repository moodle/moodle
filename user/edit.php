<?php // $Id$

    require_once('../config.php');
    require_once($CFG->libdir.'/gdlib.php');
    require_once($CFG->dirroot.'/user/edit_form.php');
    require_once($CFG->dirroot.'/user/editlib.php');
    require_once($CFG->dirroot.'/user/profile/lib.php');

    httpsrequired();

    $course = optional_param('course', SITEID, PARAM_INT);   // course id (defaults to Site)

    if (!$course = get_record('course', 'id', $course)) {
        error('Course ID was incorrect');
    }

    if ($course->id != SITEID) {
        require_login($course);
    } else if (!isloggedin()) {
        if (empty($SESSION->wantsurl)) {
            $SESSION->wantsurl = $CFG->httpswwwroot.'/edit/user.php';
        }
        redirect($CFG->httpswwwroot.'/login/index.php');
    }

    if (isguest()) { //TODO: add proper capability to edit own profile and change password too
        print_error('guestnoeditprofile');
    }
    if (!$user = get_record('user', 'id', $USER->id)) {
        error('User ID was incorrect');
    }

    // remote users cannot be edited
    if (is_mnet_remote_user($user)) {
        redirect($CFG->wwwroot . "/user/view.php?course={$course->id}");
    }

    //load user preferences
    useredit_load_preferences($user);

    //Load custom profile fields data
    profile_load_data($user);

    //create form
    $userform = new user_edit_form();
    $userform->set_data($user);

    if ($usernew = $userform->get_data()) {
        add_to_log($course->id, 'user', 'update', "view.php?id=$user->id&course=$course->id", '');

        $authplugin = get_auth_plugin($user->auth);

        $usernew->timemodified = time();

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

        //update preferences
        useredit_update_user_preference($usernew);

        //update user picture
        if (!empty($CFG->gdversion) and empty($CFG->disableuserimages)) {
            useredit_update_picture($usernew, $userform);
        }

        // update mail bounces
        useredit_update_bounces($user, $usernew);

        /// update forum track preference
        useredit_update_trackforums($user, $usernew);

        // save custom profile fields data
        profile_save_data($usernew);

        // Override old $USER session variable
        $usernew = (array)get_record('user', 'id', $usernew->id); // reload from db
        foreach ($usernew as $variable => $value) {
            $USER->$variable = $value;
        }

        redirect("$CFG->wwwroot/user/view.php?id=$USER->id&course=$course->id");
    }


/// Display page header
    $streditmyprofile = get_string('editmyprofile');
    $strparticipants  = get_string('participants');
    $userfullname     = fullname($user, true);
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

/// Finally display THE form
    $userform->display();

/// and proper footer
    print_footer($course);

?>
