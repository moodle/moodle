<?PHP // $Id$

	require("../config.php");
	require("../lib/countries.php");
	require("lib.php");

    require_variable($id);       // user id
    require_variable($course);   // course id

    if (! $user = get_record("user", "id", $id)) {
        error("User ID was incorrect");
    }

    if (! $course = get_record("course", "id", $course)) {
        error("User ID was incorrect");
    }

	require_login($course->id);

    if ($USER->id <> $user->id) {
        error("You can only edit your own information");
    }

    if (isguest()) {
        error("The guest user cannot edit their profile.");
    }


/// If data submitted, then process and store.

	if (match_referer() && isset($HTTP_POST_VARS)) {

        $usernew = (object)$HTTP_POST_VARS;

        if (!find_form_errors($user, $usernew, $err) ) {

		    $timenow = time();

            if ($imagefile && $imagefile!="none") { 
                $imageinfo = GetImageSize($imagefile);
                $image->width  = $imageinfo[0];
                $image->height = $imageinfo[1];
                $image->type   = $imageinfo[2];
    
                switch ($image->type) {
                    case 2: $im = ImageCreateFromJPEG($imagefile); break;
                    case 3: $im = ImageCreateFromPNG($imagefile); break;
                    default: error("Image must be in JPG or PNG format");
                }
                if (function_exists("ImageCreateTrueColor") and $CFG->gdversion >= 2) {
                    $im1 = ImageCreateTrueColor(100,100);
                    $im2 = ImageCreateTrueColor(35,35);
                } else {
                    $im1 = ImageCreate(100,100);
                    $im2 = ImageCreate(35,35);
                }
                
                $cx = $image->width / 2;
                $cy = $image->height / 2;
    
                if ($image->width < $image->height) {
                    $half = floor($image->width / 2.0);
                } else {
                    $half = floor($image->height / 2.0);
                }
    
                if (!file_exists("$CFG->dataroot/users")) {
                    if (! mkdir("$CFG->dataroot/users", 0777)) {
                        $badpermissions = true;
                    }
                }
                if (!file_exists("$CFG->dataroot/users/$USER->id")) {
                    if (! mkdir("$CFG->dataroot/users/$USER->id", 0777)) {
                        $badpermissions = true;
                    }
                }
                
                if ($badpermissions) {
                    $usernew->picture = "0";

                } else {
                    ImageCopyBicubic($im1, $im, 0, 0, $cx-$half, $cy-$half, 100, 100, $half*2, $half*2);
                    ImageCopyBicubic($im2, $im, 0, 0, $cx-$half, $cy-$half, 35, 35, $half*2, $half*2);
    
                    // Draw borders over the top.
                    $black1 = ImageColorAllocate ($im1, 0, 0, 0);
                    $black2 = ImageColorAllocate ($im2, 0, 0, 0);
                    ImageLine ($im1, 0, 0, 0, 99, $black1);
                    ImageLine ($im1, 0, 99, 99, 99, $black1);
                    ImageLine ($im1, 99, 99, 99, 0, $black1);
                    ImageLine ($im1, 99, 0, 0, 0, $black1);
                    ImageLine ($im2, 0, 0, 0, 34, $black2);
                    ImageLine ($im2, 0, 34, 34, 34, $black2);
                    ImageLine ($im2, 34, 34, 34, 0, $black2);
                    ImageLine ($im2, 34, 0, 0, 0, $black2);
                
                    ImageJpeg($im1, "$CFG->dataroot/users/$USER->id/f1.jpg", 90);
                    ImageJpeg($im2, "$CFG->dataroot/users/$USER->id/f2.jpg", 95);
                    $usernew->picture = "1";
                }
            } else {
                $usernew->picture = $user->picture;
            }
    
            $usernew->timemodified = time();



            if (update_record("user", $usernew)) {
                add_to_log($course->id, "user", "update", "view.php?id=$user->id&course=$course->id", "");

                // Copy data into $USER session variable
                $usernew = (array)$usernew;
                foreach ($usernew as $variable => $value) {
                    $USER->$variable = $value;
                }
		        redirect("view.php?id=$user->id&course=$course->id", "Changes saved");
            } else {
                error("Could not update the user record ($user->id)");
            }
	    }
    }
    
/// Otherwise fill and print the form.

    $editmyprofile = get_string("editmyprofile");
    $participants = get_string("participants");

    if ($course->category) {
	    print_header($editmyprofile, $editmyprofile, 
                    "<A HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</A> 
                    -> <A HREF=\"index.php?id=$course->id\">$participants</A>
                    -> <A HREF=\"view.php?id=$USER->id&course=$course->id\">$USER->firstname $USER->lastname</A> 
                    -> $editmyprofile", "");
    } else {
	    print_header($editmyprofile, $editmyprofile,
                     "<A HREF=\"view.php?id=$USER->id&course=$course->id\">$USER->firstname $USER->lastname</A> 
                      -> $editmyprofile", "");
    }

    $teacher = strtolower($course->teacher);
    $teacheronly = "(".get_string("teacheronly", "", $teacher).")";

    print_simple_box_start("center", "", "$THEME->cellheading");
    print_heading( get_string("userprofilefor", "", "$user->firstname $user->lastname") );
	include("edit.html");
    print_simple_box_end();
    print_footer($course);




/// FUNCTIONS ////////////////////

function find_form_errors(&$user, &$usernew, &$err) {

    if (empty($usernew->email))
        $err["email"] = get_string("missingemail");

    if (empty($usernew->city))
        $err["city"] = get_string("missingcity");

    if (empty($usernew->firstname))
        $err["firstname"] = get_string("missingfirstname");

    if (empty($usernew->lastname))
        $err["lastname"] = get_string("missinglastname");

    if (empty($usernew->country))
        $err["country"] = get_string("missingcountry");

    else if (! validate_email($usernew->email))
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
