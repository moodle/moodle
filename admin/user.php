<?PHP // $Id$

	require("../config.php");
	require("../user/lib.php");

    optional_variable($id);       // user id

    if (! record_exists_sql("SELECT * FROM user_admins")) {
        $user->firstname = "Admin";
        $user->lastname  = "User";
        $user->username  = "admin";
        $user->password  = "";
        $user->email     = "root@localhost";
        $user->confirmed = 1;
        $user->timemodified = time();

        if (! $id = insert_record("user", $user)) {
            error("Could not create admin user record !!!");
        }

        $admin->user = $id;

        if (! insert_record("user_admins", $admin)) {
            error("Could not make user $id an admin !!!");
        }

        if (! $user = get_record("user", "id", $id)) {
            error("User ID was incorrect (can't find it)");
        }

        if (! $course = get_site()) {
            error("Could not find site-level course");
        }

        $teacher->user = $user->id;
        $teacher->course = $course->id;
        $teacher->authority = 1;
        if (! insert_record("user_teachers", $teacher)) {
            error("Could not make user $id a teacher of site-level course !!!");
        }

        $USER = $user;
        $USER->loggedin = true;
        $USER->admin = true;
        $USER->teacher["$course->id"] = true;

    }

    require_login();

    if (!isadmin()) {
        error("You must be an administrator to edit users this way.");
    }

    if (!$id) {
        $users = get_records_sql("SELECT * from user ORDER BY firstname");

	    print_header("Edit users", "Edit users", "<A HREF=\"$CFG->wwwroot/admin\">Admin</A> -> Edit users", "");
        echo "<CENTER>";
        foreach ($users as $user) {
            echo "<A HREF=\"user.php?id=$user->id\">$user->firstname $user->lastname</A><BR>";
        }
        echo "</CENTER>";
        print_footer();
        exit;
    }

    if (! $user = get_record("user", "id", $id)) {
        error("User ID was incorrect (can't find it)");
    }


/// If data submitted, then process and store.

	if (match_referer() && isset($HTTP_POST_VARS)) {

        $usernew = (object)$HTTP_POST_VARS;

        if (find_form_errors($user, $usernew, $err) ) {
            $user = $usernew;

        } else {

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
                if (function_exists("ImageCreateTrueColor")) {
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
                    mkdir("$CFG->dataroot/users", 0777);
                }
                if (!file_exists("$CFG->dataroot/users/$USER->id")) {
                    mkdir("$CFG->dataroot/users/$USER->id", 0777);
                }
                
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
            } else {
                $usernew->picture = $user->picture;
            }
    
            if ($usernew->password) {
                $usernew->password = md5($usernew->password);
            } else {
                unset($usernew->password);
            }

            $usernew->timemodified = time();

            if (update_record("user", $usernew)) {
		        redirect("index.php", "Changes saved");
            } else {
                error("Could not update the user record ($user->id)");
            }
	    }
    }
    
/// Otherwise fill and print the form.

    if (!$usernew) {
        $usernew = $user;
        $usernew->password = "";
    }

	print_header("Edit user profile", "Edit user profile", "<A HREF=\"$CFG->wwwroot/admin\">Admin</A> -> Edit user", "");

    print_simple_box_start("center", "", "$THEME->cellheading");
    echo "<H2>User profile for $usernew->firstname $usernew->lastname</H2>";
	include("user.html");
    print_simple_box_end();

    print_footer();




/// FUNCTIONS ////////////////////

function find_form_errors(&$user, &$usernew, &$err) {

    if (empty($usernew->email))
        $err["email"] = "Missing email address";

    else if (! validate_email($usernew->email))
        $err["email"] = "Invalid email address, check carefully";

    else if ($otheruser = get_record("user", "email", $usernew->email)) {
        if ($otheruser->id <> $user->id) {
            $err["email"] = "Email address already in use by someone else.";
        }
    }
    $user->email = $usernew->email;

    if (empty($user->password) && empty($usernew->password)) {
        $err["password"] = "Must have a password";
    }

    if (empty($usernew->username))
        $err["username"] = "Must have a username";

    if (empty($usernew->firstname))
        $err["firstname"] = "Must enter your first name";

    if (empty($usernew->lastname))
        $err["lastname"] = "Must enter your last name";

    return count($err);
}


?>
