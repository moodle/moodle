<?php // $Id$

    require_once("../config.php");
    require_once("$CFG->libdir/gdlib.php");

    $id     = optional_param('id',     0,      PARAM_INT);   // user id
    $course = optional_param('course', SITEID, PARAM_INT);   // course id (defaults to Site)

    if (empty($id)) {         // See your own profile by default
        require_login();
        $id = $USER->id;
    }

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
        if (isadmin()) {             // Current user is an admin
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
        error("The guest user cannot edit their profile.");
    }

    if (isguest($user->id)) {
        error("Sorry, the guest user cannot be edited.");
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

        if (($USER->id <> $usernew->id) && !isadmin()) {
            error("You can only edit your own information");
        }

        if (isset($USER->username)) {
            check_for_restricted_user($USER->username, "$CFG->wwwroot/course/view.php?id=$course->id");
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
                $usernew->timezone = str_replace(';', '',  $usernew->timezone);
                $usernew->timezone = str_replace('\'', '', $usernew->timezone);
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


        require_once($CFG->dirroot.'/lib/uploadlib.php');
        $um = new upload_manager('imagefile',false,false,null,false,0,true,true);

        // override locked values
        if (!isadmin()) {      
            $fields = get_user_fieldnames();
            $authconfig = get_config( 'auth/' . $user->auth );
            foreach ($fields as $field) {
                $configvariable = 'field_lock_' . $field;  
                if ( $authconfig->{$configvariable} === 'locked'
                     || ($authconfig->{$configvariable} === 'unlockedifempty' && !empty($user->$field)) ) {
                    if (!empty( $user->$field)) {
                        $usernew->$field = $user->$field;
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

            if (isadmin()) {
                if (!empty($usernew->newpassword)) {
                    $usernew->password = md5($usernew->newpassword);
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
                    auth_user_update($userold, $usernew);
                };

                 if ($userold->email != $usernew->email) {
                    set_bounce_count($usernew,true);
                    set_send_count($usernew,true);
                }

                /// Update forum track preference.
                if (($usernew->trackforums != $USER->trackforums) && !$usernew->trackforums) {
                    require_once($CFG->dirroot.'/mod/forum/lib.php');
                    forum_tp_delete_read_records($USER->id);
                }

                add_to_log($course->id, "user", "update", "view.php?id=$user->id&course=$course->id", "");

                if ($user->id == $USER->id) {
                    // Copy data into $USER session variable
                    $usernew = (array)$usernew;
                    foreach ($usernew as $variable => $value) {
                        $USER->$variable = stripslashes($value);
                    }
                    if (isset($USER->newadminuser)) {
                        unset($USER->newadminuser);
                        redirect("$CFG->wwwroot/", get_string('changessaved'));
                    }
                    if (!empty($SESSION->wantsurl)) {  // User may have been forced to edit account, so let's 
                                                       // send them to where they wanted to go originally
                        $wantsurl = $SESSION->wantsurl;
                        $SESSION->wantsurl = '';       // In case unset doesn't work as expected
                        unset($SESSION->wantsurl);
                        redirect($wantsurl, get_string('changessaved'));
                    } else {
                        redirect("$CFG->wwwroot/user/view.php?id=$user->id&course=$course->id", 
                                  get_string("changessaved"));
                    }
                } else {
                    redirect("$CFG->wwwroot/$CFG->admin/user.php", get_string("changessaved"));
                }
            } else {
                error("Could not update the user record ($user->id)");
            }
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
            $userfullname = fullname($user, isteacher($course->id));
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
                     "<a href=\"$CFG->wwwroot/$CFG->admin/\">$stradministration</a> -> ".
                     "<a href=\"$CFG->wwwroot/$CFG->admin/users.php\">$strusers</a> -> $straddnewuser", "");
    }


/// Print tabs at top
/// This same call is made in:
///     /user/view.php
///     /user/edit.php
///     /course/user.php
    $currenttab = 'editprofile';
    include('tabs.php');

    

    $teacher = strtolower($course->teacher);
    if (!isadmin()) {
        $teacheronly = "(".get_string("teacheronly", "", $teacher).")";
    } else {
        $teacheronly = "";
    }

    if (isset($USER->newadminuser)) {
        print_simple_box(get_string("configintroadmin", 'admin'), "center", "50%");
        echo "<br />";
    }

    print_simple_box_start("center");

    if (!empty($err)) {
        echo "<center>";
        notify(get_string("someerrorswerefound"));
        echo "</center>";
    }

    include("edit.html");

    if (!isadmin()) {      /// Lock all the locked fields using Javascript
        $fields = get_user_fieldnames();

        echo '<script type="text/javascript">'."\n";
        echo '<!--'."\n";

        $authconfig = get_config( 'auth/' . $user->auth );
        foreach ($fields as $field) {            
            $configvariable = 'field_lock_' . $field;
            if ( $authconfig->{$configvariable} === 'locked'
                 || ($authconfig->{$configvariable} === 'unlockedifempty' && !empty($user->$field)) ) {
                echo "eval('document.form.$field.disabled=true');\n";
            }
        }

        echo '-->'."\n";
        echo '</script>'."\n";
    }

    print_simple_box_end();

    if (!isset($USER->newadminuser)) {
        print_footer($course);
    }

    exit;



/// FUNCTIONS ////////////////////

function find_form_errors(&$user, &$usernew, &$err, &$um) {
    global $CFG;

    if (isadmin()) {
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

    if (empty($usernew->description) and !isadmin())
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

    if (empty($err["email"]) and !isadmin()) {
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
