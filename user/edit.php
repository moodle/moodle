<?PHP // $Id$

    require_once("../config.php");
    require_once("$CFG->libdir/gdlib.php");

    require_variable($id);       // user id
    require_variable($course);   // course id

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


/// If data submitted, then process and store.

    if ($usernew = data_submitted()) {

        if (isset($USER->username)) {
            check_for_restricted_user($USER->username, "$CFG->wwwroot/course/view.php?id=$course->id");
        }

        foreach ($usernew as $key => $data) {
            $usernew->$key = clean_text($usernew->$key, FORMAT_MOODLE);
        }

        $usernew->firstname = trim(strip_tags($usernew->firstname));
        $usernew->lastname  = trim(strip_tags($usernew->lastname));

        if (isset($usernew->username)) {
            $usernew->username = trim(moodle_strtolower($usernew->username));
        }

        if (empty($_FILES['imagefile'])) {
            $_FILES['imagefile'] = NULL;    // To avoid using uninitialised variable later
        }

        if (find_form_errors($user, $usernew, $err)) {
            if ($filename = valid_uploaded_file($_FILES['imagefile'])) { 
                $usernew->picture = save_profile_image($user->id, $filename);
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

            if ($filename = valid_uploaded_file($_FILES['imagefile'])) { 
                $usernew->picture = save_profile_image($user->id, $filename);
            } else {
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
                        "<A HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</A> 
                        -> <A HREF=\"index.php?id=$course->id\">$strparticipants</A>
                        -> <A HREF=\"view.php?id=$user->id&course=$course->id\">$userfullname</A> 
                        -> $streditmyprofile", "");
        } else {
            if (isset($USER->newadminuser)) {
                print_header();
            } else {
                print_header("$course->shortname: $streditmyprofile", "$course->fullname",
                             "<A HREF=\"view.php?id=$user->id&course=$course->id\">$userfullname</A> 
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
       echo "<CENTER>";
       notify(get_string("someerrorswerefound"));
       echo "</CENTER>";
    }
    include("edit.html");
    print_simple_box_end();

    if (!isset($USER->newadminuser)) {
        print_footer($course);
    }

    exit;



/// FUNCTIONS ////////////////////

function find_form_errors(&$user, &$usernew, &$err) {
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

    if (empty($usernew->description))
        $err["description"] = get_string("missingdescription");

    if (empty($usernew->city))
        $err["city"] = get_string("missingcity");

    if (empty($usernew->firstname))
        $err["firstname"] = get_string("missingfirstname");

    if (empty($usernew->lastname))
        $err["lastname"] = get_string("missinglastname");

    if (empty($usernew->country))
        $err["country"] = get_string("missingcountry");

    if (! validate_email($usernew->email))
        $err["email"] = get_string("invalidemail");

    else if ($otheruser = get_record("user", "email", $usernew->email)) {
        if ($otheruser->id <> $user->id) {
            $err["email"] = get_string("emailexists");
        }
    }

    $user->email = $usernew->email;

    return count($err);
}


?>
