<?PHP // $Id$

	include("../config.php");


	if (match_referer() && isset($HTTP_POST_VARS)) {

		$frm = (object) $HTTP_POST_VARS;

		validate_form($frm, $err);

		update_login_count();

		if (!count((array)$err)) {
			$username = $frm->username;
			$password = $frm->newpassword1;
			
			if (! set_field("user", "password", md5($frm->newpassword1), "username", $frm->username)) {
				error("Could not set the new password");
            }

			unset($USER);

			$USER = get_user_info_from_db("username", $username);
			$USER->loggedin = true;

			set_moodle_cookie($USER->username);

            add_to_log("Changed password");
			reset_login_count();

			print_header("Changed password", "Password changed successfully", "Changed Password", "");
			notice("Password changed successfully", "$CFG->wwwroot/course/");
			print_footer();
			exit;
		}
	}



	if (!$frm->username)
    	$frm->username = get_moodle_cookie();

	if ($frm->username) {
    	$focus = "form.password";
	} else {
    	$focus = "form.username";
	}


	print_header("Change password", "Change Password", "Change Password", "$focus");
    print_simple_box_start("center", "", $THEME->cellheading);
	include("change_password_form.html");
    print_simple_box_end();
	print_footer();




/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/
function validate_form($frm, &$err) {

    if (empty($frm->username))
        $err->username = "Missing username";

    else if (empty($frm->password))
        $err->password = "Missing password";

    else if (!verify_login($frm->username, $frm->password))
		$err->password = "Incorrect password for this username";

    if (empty($frm->newpassword1))
        $err->newpassword1 = "Missing new password";

    if (empty($frm->newpassword2))
        $err->newpassword2 = "Missing new password";

    else if ($frm->newpassword1 <> $frm->newpassword2)
        $err->newpassword2 = "Passwords not the same";

    return;
}

?>
