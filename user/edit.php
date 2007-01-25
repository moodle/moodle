<?php // $Id$

    require_once('../config.php');
    require_once($CFG->libdir.'/gdlib.php');
    require_once($CFG->dirroot.'/user/edit_form.php');
    require_once($CFG->dirroot.'/user/profile/lib.php');

    $course = optional_param('course', SITEID, PARAM_INT);   // course id (defaults to Site)

    if (!$course = get_record('course', 'id', $course)) {
        error('Course ID was incorrect');
    }

    require_login($course->id);

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

    //load preferences
    if (!empty($user->id) and $preferences = get_user_preferences(null, null, $user->id)) {
        foreach($preferences as $name=>$value) {
            $user->{'preference_'.$name} = $value;
        }
    }
    //TODO: Load the custom profile fields

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
        $ua = (array)$usernew;
        foreach($ua as $key=>$value) {
            if (strpos($key, 'preference_') === 0) {
                $name = substr($key, strlen('preference_'));
                set_user_preference($name, $value, $usernew->id);
            }
        }

        if (!empty($CFG->gdversion) and empty($CFG->disableuserimages)) {
             //update user picture
            if ($usernew->deletepicture) {
                //TODO - delete the files
                set_field('user', 'picture', 0, 'id', $usernew->id);
            } else if ($usernew->picture = save_profile_image($usernew->id, $userform->get_um(), 'users')) {
                set_field('user', 'picture', 1, 'id', $usernew->id);
            }
        }

        // update mail bounces
        if ($user->email !== $usernew->email) {
            set_bounce_count($usernew,true);
            set_send_count($usernew,true);
        }

        /// Update forum track preference.
        if (($usernew->trackforums != $user->trackforums) and !$usernew->trackforums) {
            require_once($CFG->dirroot.'/mod/forum/lib.php');
            forum_tp_delete_read_records($usernew->id);
        }

        //TODO: Save the custom profile fields

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
