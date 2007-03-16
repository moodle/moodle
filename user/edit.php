<?php // $Id$

    require_once("../config.php");
    require_once("$CFG->libdir/gdlib.php");
    
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

    if ($USER->id <> $user->id) {    // Current user editing someone else's profile
        if (has_capability('moodle/user:update', get_context_instance(CONTEXT_SYSTEM, SITEID)) || has_capability('moodle/user:editprofile', get_context_instance(CONTEXT_USER, $user->id))) { // Current user can update user profiles
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
        if (!file_exists("$CFG->dirroot/auth/$auth/lib.php")) {
            trigger_error("Can't find auth module $auth , default to internal.");
            $auth = "manual";    // Can't find auth module, default to internal
        }
        require_once("$CFG->dirroot/auth/$auth/lib.php");
    }

    
/// If data submitted, then process and store.

    if ($usernew = data_submitted()) {
        
        $context = get_context_instance(CONTEXT_SYSTEM, SITEID);
        // if userid = x and name = changeme then we are adding 1
        // else we are editting one
        $dummyuser = get_record('user','id', $id);
    
        if ($dummyuser->username == 'changeme') {                                            // check for add user
            require_capability('moodle/user:create', $context);
        } else {
            if ($USER->id <> $usernew->id and !has_capability('moodle/user:update', $context) and !has_capability('moodle/user:editprofile', get_context_instance(CONTEXT_USER, $usernew->id))) { // check for edit  
                print_error('onlyeditown');
            }   
        }   

        if (isset($usernew->password)) {
            unset($usernew->password);
        }

        // data cleanup 
        // username is validated in find_form_errors
        $usernew->country = clean_param($usernew->country, PARAM_ALPHA);
        $usernew->lang    = clean_param($usernew->lang,    PARAM_FILE);
        $usernew->url     = clean_param($usernew->url,     PARAM_URL);
        $usernew->icq     = clean_param($usernew->icq,     PARAM_INT);
        if (!$usernew->icq) {
            $usernew->icq = '';
        }
        $usernew->skype   = clean_param($usernew->skype,   PARAM_CLEAN);
        $usernew->yahoo   = clean_param($usernew->yahoo,   PARAM_CLEAN);
        $usernew->aim   = clean_param($usernew->aim,   PARAM_CLEAN);
        $usernew->msn   = clean_param($usernew->msn,   PARAM_CLEAN);
        
        $usernew->maildisplay   = clean_param($usernew->maildisplay,   PARAM_INT);
        $usernew->mailformat    = clean_param($usernew->mailformat,    PARAM_INT);
        if (!empty($CFG->unicodedb) && $CFG->allowusermailcharset) {
            $usernew->mailcharset = clean_param($usernew->mailcharset, PARAM_CLEAN);
            if (!empty($usernew->mailcharset)) {
                set_user_preference('mailcharset', $usernew->mailcharset, $user->id);
            } else {
                 unset_user_preference('mailcharset', $user->id);
            }
        } else {
            unset_user_preference('mailcharset', $user->id);
        }
        if (empty($CFG->enableajax)) {
            unset($usernew->ajax);
        }

        $usernew->maildigest    = clean_param($usernew->maildigest,    PARAM_INT);
        $usernew->autosubscribe = clean_param($usernew->autosubscribe, PARAM_INT);
        if (!empty($CFG->htmleditor)) {
            $usernew->htmleditor    = clean_param($usernew->htmleditor,    PARAM_INT);
        }
        else {
            unset( $usernew->htmleditor );
        }
        $usernew->emailstop     = clean_param($usernew->emailstop,     PARAM_INT);

        if (isset($usernew->timezone)) {
            if ($CFG->forcetimezone != 99) { // Don't allow changing this in any way
                unset($usernew->timezone);
            } else { // Clean up the data a bit, just in case of injections
                $usernew->timezone = clean_param($usernew->timezone, PARAM_PATH); //not a path, but it looks like it anyway
            }
        }

        foreach ($usernew as $key => $data) {
            $usernew->$key = addslashes(clean_text(stripslashes(trim($usernew->$key)), FORMAT_MOODLE));
        }

        $usernew->firstname = strip_tags($usernew->firstname);
        $usernew->lastname  = strip_tags($usernew->lastname);

        if (isset($usernew->username)) {
            $usernew->username = moodle_strtolower($usernew->username);
        }

        if (!empty($_FILES) and !(empty($CFG->disableuserimages) or has_capability('moodle/user:update', get_context_instance(CONTEXT_SYSTEM, SITEID)))) {
            error('Users can not update profile images!');
        }

        require_once($CFG->dirroot.'/lib/uploadlib.php');
        $um = new upload_manager('imagefile',false,false,null,false,0,true,true);

        // override locked values
        if (!has_capability('moodle/user:update', get_context_instance(CONTEXT_SYSTEM, SITEID))) {      
            $fields = get_user_fieldnames();
            $authconfig = get_config( 'auth/' . $user->auth );
            foreach ($fields as $field) {
                $configvariable = 'field_lock_' . $field;
                if ( empty($authconfig->{$configvariable}) ) {
                    continue; //no locking set
                }
                if ( $authconfig->{$configvariable} === 'locked'
                     || ($authconfig->{$configvariable} === 'unlockedifempty' && !empty($user->$field)) ) {
                    if (!empty( $user->$field)) {
                        $usernew->$field = addslashes($user->$field);
                    }
                }
            }
            unset($fields);
            unset($field);
            unset($configvariable);
        }
        if (find_form_errors($user, $usernew, $err, $um)) {
            if (empty($err['imagefile']) && $usernew->picture = save_profile_image($user->id, $um,'users')) {
                set_field('user', 'picture', $usernew->picture, 'id', $user->id);  /// Note picture in DB
            } else {
                if (!empty($usernew->deletepicture)) {
                    set_field('user', 'picture', 0, 'id', $user->id);  /// Delete picture
                    $usernew->picture = 0;
                }
            }

            $usernew->auth = $user->auth;
            $user = $usernew;

        } else {
            $timenow = time();
            
            if (!$usernew->picture = save_profile_image($user->id,$um,'users')) {
                if (!empty($usernew->deletepicture)) {
                    set_field('user', 'picture', 0, 'id', $user->id);  /// Delete picture
                    $usernew->picture = 0;
                } else {
                    $usernew->picture = $user->picture;
                }
            }

            $usernew->timemodified = time();

            if (has_capability('moodle/user:update', get_context_instance(CONTEXT_SYSTEM, SITEID))) {
                if (!empty($usernew->newpassword)) {
                    $usernew->password = hash_internal_user_password($usernew->newpassword);
                    // update external passwords
                    if (!empty($CFG->{'auth_'. $user->auth.'_stdchangepassword'})) {
                        if (function_exists('auth_user_update_password')){
                            if (!auth_user_update_password($user->username, $usernew->newpassword)){
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
            if ($usernew->url and !(substr($usernew->url, 0, 4) == "http")) {
                $usernew->url = "http://".$usernew->url;
            }

            $userold = get_record('user','id',$usernew->id);
            if (update_record("user", $usernew)) {
                if (function_exists("auth_user_update")){
                    // pass a true $userold here 
                    if (!auth_user_update($userold, $usernew)) {
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

                add_to_log($course->id, "user", "update", "view.php?id=$user->id&course=$course->id", "");

                if ($user->id == $USER->id || !has_capability('moodle/user:update', get_context_instance(CONTEXT_SYSTEM, SITEID))) { // non admin redirect
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
    }

/// Otherwise fill and print the form.

    $usehtmleditor = can_use_html_editor();

    //temporary hack to disable htmleditor in IE when loginhttps on and wwwroot starts with http://
    //see bug #5534
    if (!empty($CFG->loginhttps) and check_browser_version('MSIE', 5.5) and (strpos($CFG->wwwroot, 'http://') === 0)) {
        $usehtmleditor = false;
    }

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
        if ($course->category) {
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

    print_simple_box_start("center");

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

    include("edit.html");

    if (!has_capability('moodle/user:update', get_context_instance(CONTEXT_SYSTEM, SITEID))) {      /// Lock all the locked fields using Javascript
        $fields = get_user_fieldnames();

        echo '<script type="text/javascript">'."\n";
        echo '<!--'."\n";

        $authconfig = get_config( 'auth/' . $user->auth );
        
        foreach ($fields as $field) {
            $configvariable = 'field_lock_' . $field;
            if (isset($authconfig->{$configvariable})) {
                if ( $authconfig->{$configvariable} === 'locked'
                    || ($authconfig->{$configvariable} === 'unlockedifempty' && !empty($user->$field)) ) {
                   echo "eval('document.form.$field.disabled=true');\n";
                }
            }
        }

        echo '-->'."\n";
        echo '</script>'."\n";
    }

    print_simple_box_end();

    if ($usehtmleditor) {
        use_html_editor("description");
    }

    if (!isset($USER->newadminuser)) {
        print_footer($course);
    }

    exit;



/// FUNCTIONS ////////////////////

function find_form_errors(&$user, &$usernew, &$err, &$um) {
    global $CFG;

    if (has_capability('moodle/user:update', get_context_instance(CONTEXT_SYSTEM, SITEID))) {
        if (empty($usernew->username)) {
            $err["username"] = get_string("missingusername");

        } else if (record_exists("user", "username", $usernew->username) and $user->username == "changeme") {
            $err["username"] = get_string("usernameexists");

        } else {
            if (empty($CFG->extendedusernamechars)) {
                $string = eregi_replace("[^(-\.[:alnum:])]", "", $usernew->username);
                if (strcmp($usernew->username, $string)) {
                    $err["username"] = get_string("alphanumerical");
                }
            }
        }

        if (empty($usernew->newpassword) and empty($user->password) and is_internal_auth() )
            $err["newpassword"] = get_string("missingpassword");

        if (($usernew->newpassword == "admin") or ($user->password == md5("admin") and empty($usernew->newpassword)) ) {
            $err["newpassword"] = get_string("unsafepassword");
        }
    }

    if (empty($usernew->email))
        $err["email"] = get_string("missingemail");

    if (over_bounce_threshold($user) && $user->email == $usernew->email) 
        $err['email'] = get_string('toomanybounces');

    if (empty($usernew->description) and !has_capability('moodle/user:update', get_context_instance(CONTEXT_SYSTEM, SITEID)))
        $err["description"] = get_string("missingdescription");

    if (empty($usernew->city))
        $err["city"] = get_string("missingcity");

    if (empty($usernew->firstname))
        $err["firstname"] = get_string("missingfirstname");

    if (empty($usernew->lastname))
        $err["lastname"] = get_string("missinglastname");

    if (empty($usernew->country))
        $err["country"] = get_string("missingcountry");

    if (! validate_email($usernew->email)) {
        $err["email"] = get_string("invalidemail");

    } else if ($otheruser = get_record("user", "email", $usernew->email)) {
        if ($otheruser->id <> $user->id) {
            $err["email"] = get_string("emailexists");
        }
    }

    if (empty($err["email"]) and !has_capability('moodle/user:update', get_context_instance(CONTEXT_SYSTEM, SITEID))) {
        if ($error = email_is_not_allowed($usernew->email)) {
            $err["email"] = $error;
        }
    }

    if (!$um->preprocess_files()) {
        $err['imagefile'] = $um->notify;
    }

    $user->email = $usernew->email;

    return count($err);
}


?>
