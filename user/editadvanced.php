<?php // $Id$

    require_once('../config.php');
    require_once($CFG->libdir.'/gdlib.php');
    require_once($CFG->libdir.'/adminlib.php');
    require_once($CFG->dirroot.'/user/editadvanced_form.php');
    require_once($CFG->dirroot.'/user/editlib.php');
    require_once($CFG->dirroot.'/user/profile/lib.php');
    require_once($CFG->libdir .'/messagelib.php');      // Messagelib functions

    httpsrequired();

    $id     = optional_param('id', $USER->id, PARAM_INT);    // user id; -1 if creating new user
    $course = optional_param('course', SITEID, PARAM_INT);   // course id (defaults to Site)

    if (!$course = $DB->get_record('course', array('id'=>$course))) {
        print_error('invalidcourseid');
    }
    if (!empty($USER->newadminuser)) {
        $PAGE->set_course($SITE);
        $PAGE->set_generaltype('maintenance');
    } else {
        require_login($course);
    }

    if ($course->id == SITEID) {
        $coursecontext = get_context_instance(CONTEXT_SYSTEM);   // SYSTEM context
    } else {
        $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);   // Course context
    }
    $systemcontext = get_context_instance(CONTEXT_SYSTEM);

    if ($id == -1) {
        // creating new user
        require_capability('moodle/user:create', $systemcontext);
        $user = new object();
        $user->id = -1;
        $user->auth = 'manual';
        $user->confirmed = 1;
        $user->deleted = 0;
    } else {
        // editing existing user
        require_capability('moodle/user:update', $systemcontext);
        if (!$user = $DB->get_record('user', array('id'=>$id))) {
            print_error('invaliduserid');
        }
    }

    // remote users cannot be edited
    if ($user->id != -1 and is_mnet_remote_user($user)) {
        redirect($CFG->wwwroot . "/user/view.php?id=$id&course={$course->id}");
    }

    if ($user->id != $USER->id and is_primary_admin($user->id)) {  // Can't edit primary admin
        print_error('adminprimarynoedit');
    }

    if (isguestuser($user->id)) { // the real guest user can not be edited
        print_error('guestnoeditprofileother');
    }

    if ($user->deleted) {
        print_header();
        echo $OUTPUT->heading(get_string('userdeleted'));
        echo $OUTPUT->footer();
        die;
    }

    if ($user->id == -1) {
        admin_externalpage_setup('addnewuser', '', array('id' => -1));
    } else if ($user->id != $USER->id) {
        admin_externalpage_setup('editusers', '', array('id' => $user->id, 'course' => SITEID), $CFG->wwwroot . '/user/editadvanced.php');
    }

    //load user preferences
    useredit_load_preferences($user);

    //Load custom profile fields data
    profile_load_data($user);

    //User interests
    if (!empty($CFG->usetags)) {
        require_once($CFG->dirroot.'/tag/lib.php');
        $user->interests = tag_get_tags_array('user', $id);
    }

    //create form
    $userform = new user_editadvanced_form();
    $userform->set_data($user);

    if ($usernew = $userform->get_data()) {
        add_to_log($course->id, 'user', 'update', "view.php?id=$user->id&course=$course->id", '');

        if (empty($usernew->auth)) {
            //user editing self
            $authplugin = get_auth_plugin($user->auth);
            unset($usernew->auth); //can not change/remove
        } else {
            $authplugin = get_auth_plugin($usernew->auth);
        }

        $usernew->username     = trim($usernew->username);
        $usernew->timemodified = time();

        if ($usernew->id == -1) {
            //TODO check out if it makes sense to create account with this auth plugin and what to do with the password
            unset($usernew->id);
            $usernew->mnethostid = $CFG->mnet_localhost_id; // always local user
            $usernew->confirmed  = 1;
            $usernew->password = hash_internal_user_password($usernew->newpassword);
            $usernew->id = $DB->insert_record('user', $usernew);
            $usercreated = true;
            
        } else {
            $DB->update_record('user', $usernew);
            // pass a true $userold here
            if (! $authplugin->user_update($user, $userform->get_data())) {
                // auth update failed, rollback for moodle
                $DB->update_record('user', $user);
                print_error('cannotupdateuseronexauth', '', '', $user->auth);
            }

            //set new password if specified
            if (!empty($usernew->newpassword)) {
                if ($authplugin->can_change_password()) {
                    if (!$authplugin->user_update_password($usernew, $usernew->newpassword)){
                        print_error('cannotupdatepasswordonextauth', '', '', $usernew->auth);
                    }
                }
            }
            $usercreated = false;
        }

        $usercontext = get_context_instance(CONTEXT_USER, $usernew->id);

        //update preferences
        useredit_update_user_preference($usernew);

        // update tags
        if (!empty($CFG->usetags)) {
            useredit_update_interests($usernew, $usernew->interests);
        }

        //update user picture
        if (!empty($CFG->gdversion)) {
            useredit_update_picture($usernew, $userform);
        }

        // update mail bounces
        useredit_update_bounces($user, $usernew);

        // update forum track preference
        useredit_update_trackforums($user, $usernew);

        // save custom profile fields data
        profile_save_data($usernew);

        // reload from db
        $usernew = $DB->get_record('user', array('id'=>$usernew->id));
        
        // trigger events
        if ($usercreated) {
            //set default message preferences
            if (!message_set_default_message_preferences( $usernew )){
                print_error('cannotsavemessageprefs', 'message');
            }
            events_trigger('user_created', $usernew);
        } else {
            events_trigger('user_updated', $usernew);
        }

        if ($user->id == $USER->id) {
            // Override old $USER session variable
            foreach ((array)$usernew as $variable => $value) {
                $USER->$variable = $value;
            }
            if (!empty($USER->newadminuser)) {
                unset($USER->newadminuser);
                // apply defaults again - some of them might depend on admin user info, backup, roles, etc.
                admin_apply_default_settings(NULL , false);
                // redirect to admin/ to continue with installation
                redirect("$CFG->wwwroot/$CFG->admin/");
            } else {
                redirect("$CFG->wwwroot/user/view.php?id=$USER->id&course=$course->id");
            }            
        } else {
            session_gc(); // remove stale sessions
            redirect("$CFG->wwwroot/$CFG->admin/user.php");
        }
        //never reached
    }


/// Display page header
    if ($user->id == -1 or ($user->id != $USER->id)) {
        if ($user->id == -1) {
            admin_externalpage_print_header();
        } else {
            admin_externalpage_print_header();
            $userfullname = fullname($user, true);
            echo $OUTPUT->heading($userfullname);
        }
    } else if (!empty($USER->newadminuser)) {
        $strinstallation = get_string('installation', 'install');
        $strprimaryadminsetup = get_string('primaryadminsetup');
        $navigation = build_navigation(array(array('name'=>$strprimaryadminsetup, 'link'=>null, 'type'=>'misc')));
        print_header($strinstallation, $strinstallation, $navigation, "", "", false, "&nbsp;", "&nbsp;");
        print_box(get_string('configintroadmin', 'admin'), 'generalbox boxwidthnormal boxaligncenter');
        echo '<br />';
    } else {
        $streditmyprofile = get_string('editmyprofile');
        $strparticipants  = get_string('participants');
        $strnewuser       = get_string('newuser');
        $userfullname     = fullname($user, true);

        $navlinks = array();
        if (has_capability('moodle/course:viewparticipants', $coursecontext) || has_capability('moodle/site:viewparticipants', $systemcontext)) {
            $navlinks[] = array('name' => $strparticipants, 'link' => "index.php?id=$course->id", 'type' => 'misc');
        }
        $navlinks[] = array('name' => $userfullname,
                            'link' => "view.php?id=$user->id&amp;course=$course->id",
                            'type' => 'misc');
        $navlinks[] = array('name' => $streditmyprofile, 'link' => null, 'type' => 'misc');
        $navigation = build_navigation($navlinks);
        print_header("$course->shortname: $streditmyprofile", $course->fullname, $navigation, "");

        /// Print tabs at the top
        $showroles = 1;
        $currenttab = 'editprofile';
        require('tabs.php');
    }

/// Finally display THE form
    $userform->display();

/// and proper footer
    echo $OUTPUT->footer();

?>
