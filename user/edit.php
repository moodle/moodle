<?PHP // $Id$

    require_once("../config.php");
    require_once("$CFG->libdir/gdlib.php");

    optional_variable($id);       // user id
    optional_variable($course);   // course id

    if (empty($id)) {         // See your own profile by default
        require_login();
        $id = $USER->id;
    }

    if (empty($course)) {     // See it at site level by default
        $course = SITEID;
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

        if (empty($USER)) {
            error("Sessions don't seem to be working on this server!");
        }

    } else {
        $newaccount  = false;
        require_login($course->id);
    }

    if ($USER->id <> $user->id and !isadmin()) {
        error("You can only edit your own information");
    }

    if (isguest()) {
        error("The guest user cannot edit their profile.");
    }

    if (isguest($user->id)) {
        error("Sorry, the guest user cannot be edited.");
    }

    // load the relevant auth libraries
    if ($user->auth) { 
        $auth = $user->auth;
        if (!file_exists("$CFG->dirroot/auth/$auth/lib.php")) {
            $auth = "manual";    // Can't find auth module, default to internal
        }
        require_once("$CFG->dirroot/auth/$auth/lib.php");
    }

    
/// If data submitted, then process and store.

    if ($usernew = data_submitted()) {

        if (isset($USER->username)) {
            check_for_restricted_user($USER->username, "$CFG->wwwroot/course/view.php?id=$course->id");
        }

        foreach ($usernew as $key => $data) {
            $usernew->$key = addslashes(clean_text(stripslashes($usernew->$key), FORMAT_MOODLE));
        }

        $usernew->firstname = trim(strip_tags($usernew->firstname));
        $usernew->lastname  = trim(strip_tags($usernew->lastname));

        if (isset($usernew->username)) {
            $usernew->username = trim(moodle_strtolower($usernew->username));
        }


        require_once($CFG->dirroot.'/lib/uploadlib.php');
        $um = new upload_manager('imagefile',false,false,null,false,0,true,true);

        if (find_form_errors($user, $usernew, $err, $um)) {
            if (empty($err['imagefile']) && $usernew->picture = save_profile_image($user->id, $um,'users')) {
                set_field('user', 'picture', $usernew->picture, 'id', $user->id);  /// Note picture in DB
            } else {
                if (!empty($usernew->deletepicture)) {
                    set_field('user', 'picture', 0, 'id', $user->id);  /// Delete picture
                    $usernew->picture = 0;
                }
            }

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

            if (update_record("user", $usernew)) {
                if (function_exists("auth_user_update")){ 
                    auth_user_update($user, $usernew);
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
                        redirect("$CFG->wwwroot/", get_string("changessaved"));
                    }
                    redirect("$CFG->wwwroot/user/view.php?id=$user->id&course=$course->id", get_string("changessaved"));
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

    $teacher = strtolower($course->teacher);
    if (!isadmin()) {
        $teacheronly = "(".get_string("teacheronly", "", $teacher).")";
    } else {
        $teacheronly = "";
    }

    print_heading( get_string("userprofilefor", "", "$userfullname") );

    if (isset($USER->newadminuser)) {
        print_simple_box(get_string("configintroadmin"), "center", "50%");
        echo "<br />";
    }

    print_simple_box_start("center", "", "$THEME->cellheading");

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

        foreach ($fields as $field) {
            $configvariable = 'auth_user_'.$field.'_editlock';
            if (!empty($CFG->$configvariable)) {
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

    if (!isadmin()) {      /// Make sure that locked fields are not being edited
        $fields = get_user_fieldnames();

        foreach ($fields as $field) {
            $configvariable = 'auth_user_'.$field.'_editlock';
            if (!empty($CFG->$configvariable)) {
                if ($user->$field !== $usernew->$field) {
                    $err[$field] = get_string("editlock");
                }
            }
        }
    }

    $user->email = $usernew->email;

    return count($err);
}


?>
