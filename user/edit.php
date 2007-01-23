<?php // $Id$

    require_once("../config.php");
    require_once("$CFG->libdir/gdlib.php");
    require_once("$CFG->dirroot/user/edit_form.php");

    $id     = optional_param('id',     0,      PARAM_INT);   // user id
    $course = optional_param('course', SITEID, PARAM_INT);   // course id (defaults to Site)

    if (empty($id)) {         // See your own profile by default
        require_login();
        $id = $USER->id;
    }

    httpsrequired(); // HTTPS is potentially required in this page

    if (! $user = get_record("user", "id", $id)) {
        error("User ID was incorrect");
    }

    if (! $course = get_record("course", "id", $course)) {
        error("Course ID was incorrect");
    }

    if ($user->confirmed and user_not_fully_set_up($user)) {
        // Special case which can only occur when a new account
        // has just been created by EXTERNAL authentication
        // This is the only page in Moodle that has the exception
        // so that users can set up their accounts
        $newaccount  = true;

        if (empty($USER->id)) {
            error("Sessions don't seem to be working on this server!");
        }

    } else {
        $newaccount  = false;
        require_login($course->id);
    }

    // remote users cannot be edited
    if (is_mnet_remote_user($user)) {
        redirect($CFG->wwwroot . "/user/view.php?id=$id&course={$course->id}");
    }

    if ($USER->id <> $user->id) {    // Current user editing someone else's profile
        if (has_capability('moodle/user:update', get_context_instance(CONTEXT_SYSTEM, SITEID))) { // Current user can update user profiles
            if ($mainadmin = get_admin()) {
                if ($user->id == $mainadmin->id) {  // Can't edit primary admin
                    print_error('adminprimarynoedit');
                }
            }
        } else {
            print_error('onlyeditown');
        }
    }

    if (isguest()) {
        print_error('guestnoeditprofile');
    }

    if (isguest($user->id)) {
        print_error('guestnoeditprofileother');
    }


    // load the relevant auth libraries
    if (!empty($user->auth)) {
        $auth = $user->auth;
        // TODO: spit dummy if $auth doesn't exist
        if (! exists_auth_plugin($auth)) {
            trigger_error("Can't find auth module '$auth', default to internal.");
            $auth = "manual";
        }
        $authplugin = get_auth_plugin($auth);
    } else {
        $authplugin = get_auth_plugin($CFG->auth);
    }


    $userform = new user_edit_form(null, compact('user','course','authplugin'));
    if ($user->username == 'changeme') {
        $changeme = new object();
        $changeme->id = $user->id;
        $changeme->auth = $user->auth;
        $userform->set_data($changeme);
    } else {
        $userform->set_data($user);
    }

/// If data submitted, then process and store.
    if ($usernew = $userform->get_data()) {

        $context = get_context_instance(CONTEXT_SYSTEM, SITEID);
        // if userid = x and name = changeme then we are adding 1
        // else we are editting one
        $dummyuser = get_record('user','id', $id);

        if ($dummyuser->username == 'changeme') {                                            // check for add user
            require_capability('moodle/user:create', $context);
        } else {
            if ($USER->id <> $usernew->id and !has_capability('moodle/user:update', $context)) { // check for edit
                print_error('onlyeditown');
            }
        }

        if (isset($usernew->password)) {
            unset($usernew->password);
        }

        if ($CFG->allowusermailcharset) {
            $usernew->mailcharset = clean_param($usernew->mailcharset, PARAM_CLEAN);
            if (!empty($usernew->mailcharset)) {
                set_user_preference('mailcharset', $usernew->mailcharset, $user->id);
            } else {
                 unset_user_preference('mailcharset', $user->id);
            }
        } else {
            unset_user_preference('mailcharset', $user->id);
        }

        foreach ($usernew as $key => $data) {
            $usernew->$key = addslashes(clean_text(stripslashes(trim($usernew->$key)), FORMAT_MOODLE));
        }

        if (isset($usernew->username)) {
            $usernew->username = moodle_strtolower($usernew->username);
        }

        if (!empty($_FILES) and !(empty($CFG->disableuserimages) or has_capability('moodle/user:update', get_context_instance(CONTEXT_SYSTEM, SITEID)))) {
            error('Users can not update profile images!');
        }

        // override locked values
        if (!has_capability('moodle/user:update', get_context_instance(CONTEXT_SYSTEM, SITEID))) {
            $fields = get_user_fieldnames();
            foreach ($fields as $field) {
                $configvariable = 'field_lock_' . $field;
                if ( empty($authplugin->config->{$configvariable}) ) {
                    continue; //no locking set
                }
                if ( $authplugin->config->{$configvariable} === 'locked'
                     or ($authplugin->config->{$configvariable} === 'unlockedifempty' and !empty($user->$field))) {
                    if (!empty( $user->$field)) {
                        $usernew->$field = addslashes($user->$field);
                    }
                }
            }
            unset($fields);
            unset($field);
            unset($configvariable);
        }

        if (!$usernew->picture = save_profile_image($user->id, $userform->_upload_manager, 'users')) {
            if (!empty($usernew->deletepicture)) {
                set_field('user', 'picture', 0, 'id', $user->id);  /// Delete picture
                $usernew->picture = 0;
            } else {
                $usernew->picture = $user->picture;
            }
        }

        $timenow = time();

        $usernew->timemodified = time();

        if (has_capability('moodle/user:update', get_context_instance(CONTEXT_SYSTEM, SITEID))) {
            if (!empty($usernew->newpassword)) {
                $usernew->password = hash_internal_user_password($usernew->newpassword);
                // update external passwords
                // TODO: this was using $user->auth possibly overriding $authplugin above. Can we guarantee $user->auth being something valid?
                if ($authplugin->can_change_password()) {
                    if (method_exists($authplugin, 'user_update_password')){
                        if (!$authplugin->user_update_password($user->username, $usernew->newpassword)){
                            error('Failed to update password on external auth: ' . $user->auth .
                                    '. See the server logs for more details.');
                        }
                    } else {
                        error('Your external authentication module is misconfigued!');
                    }
                }
            }
            // store forcepasswordchange in user's preferences
            if (!empty($usernew->forcepasswordchange)){
                set_user_preference('auth_forcepasswordchange', 1, $user->id);
            } else {
                unset_user_preference('auth_forcepasswordchange', $user->id);
            }
        } else {
            if (isset($usernew->newpassword)) {
                error("You can not change the password like that");
            }
        }

        $userold = get_record('user','id',$usernew->id);
        if (update_record("user", $usernew)) {
            if (method_exists($authplugin, "user_update")){
                // pass a true $userold here
                if (! $authplugin->user_update($userold, $usernew)) {
                    // auth update failed, rollback for moodle
                    update_record("user", $userold);
                    error('Failed to update user data on external auth: '.$user->auth.
                            '. See the server logs for more details.');
                }
            };

            if ($userold->email != $usernew->email) {
                set_bounce_count($usernew,true);
                set_send_count($usernew,true);
            }

            /// Update forum track preference.
            if (($usernew->trackforums != $userold->trackforums) && !$usernew->trackforums) {
                require_once($CFG->dirroot.'/mod/forum/lib.php');
                forum_tp_delete_read_records($usernew->id);
            }

            /// Update the custom user fields
            if ($categories = get_records_select('user_info_category', '', 'sortorder ASC')) {
                foreach ($categories as $category) {

                    if ($fields = get_records_select('user_info_field', "categoryid=$category->id", 'sortorder ASC')) {
                        foreach ($fields as $field) {

                            require_once($CFG->dirroot.'/user/profile/field/'.$field->datatype.'/field.class.php');
                            $newfield = 'profile_field_'.$field->datatype;
                            $formfield = new $newfield($field->id,$user->id);
                            if (isset($usernew->{$formfield->fieldname})) {
                                $formfield->save_data($usernew->{$formfield->fieldname});
                            }

                            unset($formfield);

                        }
                    } /// End of $fields if
                } /// End of $categories foreach
            } /// End of $categories if



            add_to_log($course->id, "user", "update", "view.php?id=$user->id&course=$course->id", "");

            if ($user->id == $USER->id) {
                // Copy data into $USER session variable
                $usernew = (array)$usernew;
                foreach ($usernew as $variable => $value) {
                    $USER->$variable = stripslashes($value);
                }
                if (isset($USER->newadminuser)) {
                    unset($USER->newadminuser);
                    // redirect to admin/ to continue with installation
                    redirect("$CFG->wwwroot/$CFG->admin/");
                }
                if (!empty($SESSION->wantsurl)) {  // User may have been forced to edit account, so let's
                    // send them to where they wanted to go originally
                    $wantsurl = $SESSION->wantsurl;
                    $SESSION->wantsurl = '';       // In case unset doesn't work as expected
                    unset($SESSION->wantsurl);
                    redirect($wantsurl);
                } else {
                    redirect("$CFG->wwwroot/user/view.php?id=$user->id&course=$course->id");
                }
            } else {
                redirect("$CFG->wwwroot/$CFG->admin/user.php");
            }
        } else {
            error("Could not update the user record ($user->id)");
        }
    }


/// Otherwise fill and print the form.

    $streditmyprofile = get_string("editmyprofile");
    $strparticipants = get_string("participants");
    $strnewuser = get_string("newuser");

    if (over_bounce_threshold($user) && empty($err['email'])) {
        $err['email'] = get_string('toomanybounces');
    }

    if (($user->firstname and $user->lastname) or $newaccount) {
        if ($newaccount) {
            $userfullname = $strnewuser;
        } else {
            $userfullname = fullname($user, has_capability('moodle/site:viewfullnames', get_context_instance(CONTEXT_COURSE, $course->id)));
        }
        if ($course->id != SITEID) {
            print_header("$course->shortname: $streditmyprofile", "$course->fullname: $streditmyprofile",
                        "<a href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</a>
                        -> <a href=\"index.php?id=$course->id\">$strparticipants</a>
                        -> <a href=\"view.php?id=$user->id&amp;course=$course->id\">$userfullname</a>
                        -> $streditmyprofile", "");
        } else {
            if (isset($USER->newadminuser)) {
                print_header();
            } else {
                print_header("$course->shortname: $streditmyprofile", "$course->fullname",
                             "<a href=\"view.php?id=$user->id&amp;course=$course->id\">$userfullname</a>
                              -> $streditmyprofile", "");
            }
        }
    } else {
        $userfullname = $strnewuser;
        $straddnewuser = get_string("addnewuser");

        $stradministration = get_string("administration");
        print_header("$course->shortname: $streditmyprofile", "$course->fullname",
                     "<a href=\"$CFG->wwwroot/$CFG->admin/\">$stradministration</a> -> $straddnewuser", "");
    }


    if (isset($USER->newadminuser)) {
        print_simple_box(get_string('configintroadmin', 'admin'), 'center', '50%');
        echo '<br />';
    } else {
        /// Print tabs at top
        /// This same call is made in:
        ///     /user/view.php
        ///     /user/edit.php
        ///     /course/user.php
        $showroles = 1;
        $currenttab = 'editprofile';
        include('tabs.php');
    }

    if (!empty($err)) {
        echo "<center>";
        notify(get_string("someerrorswerefound"));
        echo "</center>";
    }

    $teacher = $course->teacher;
    if (!has_capability('moodle/user:update', get_context_instance(CONTEXT_SYSTEM, SITEID))) {
        $teacheronly = '('.get_string('teacheronly', '', $teacher).')';
    } else {
        $teacheronly = '';
    }

    $userform->display();

    if (!isset($USER->newadminuser)) {
        print_footer($course);
    } else {
        print_footer();
    }

?>
