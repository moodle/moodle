<?PHP // $Id$

	require("../config.php");
	require("../user/lib.php");

    optional_variable($id);       // course id

    if (! $site = get_site()) {
        redirect("$CFG->wwwroot/admin/");
    }

    require_login();

    if (!isadmin()) {
        error("You must be an administrator to edit users this way.");
    }

    if (!$id) {
        $courses = get_records_sql("SELECT * from course WHERE category > 0 ORDER BY fullname");

	    print_header("Add teachers to a course", "Add teachers to a course", "<A HREF=\"$CFG->wwwroot/admin\">Admin</A> -> Add teachers", "");
        print_heading("Choose a course to add teachers to");
        print_simple_box_start("CENTER");
        foreach ($courses as $course) {
            echo "<A HREF=\"teacher.php?id=$course->id\">$course->fullname</A><BR>";
        }
        print_simple_box_end();
        print_footer();
        exit;
    }

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID was incorrect (can't find it)");
    }


/// If data submitted, then process and store.

	if (match_referer() && isset($HTTP_POST_VARS)) {

        $usernew = (object)$HTTP_POST_VARS;

        if (find_form_errors($user, $usernew, $err) ) {
            $user = $usernew;

        } else {

            $usernew->timemodified = time();

            if (update_record("user", $usernew)) {
		        redirect("index.php", "Changes saved");
            } else {
                error("Could not update the user record ($user->id)");
            }
	    }
    }
    
/// Otherwise fill and print the form.

    XXXXXXX

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
