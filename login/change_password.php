<?PHP // $Id$

	include("../config.php");


	if (match_referer() && isset($HTTP_POST_VARS)) {

		$frm = (object) $HTTP_POST_VARS;

		validate_form($frm, $err);

		update_login_count();

		if (!count((array)$err)) {
			$username = $frm->username;
			$password = md5($frm->newpassword1);

			$user = get_user_info_from_db("username", $username);

            if (isguest($user->id)) {
                error("Can't change guest password!");
            }
			
			if (set_field("user", "password", $password, "username", $username)) {
                $user->password = $password;
            } else {
				error("Could not set the new password");
            }

			$USER = $user;
			$USER->loggedin = true;
            $USER->site = $CFG->wwwroot;   // for added security
            save_session("USER");

			set_moodle_cookie($USER->username);

			reset_login_count();

            $passwordchanged = get_string("passwordchanged");
			print_header($passwordchanged, $passwordchanged, $passwordchanged, "");
			notice($passwordchanged, "$CFG->wwwroot/course/");
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

    $changepassword = get_string("changepassword");
	print_header($changepassword, $changepassword, $changepassword, $focus);
    print_simple_box_start("center", "", $THEME->cellheading);
	include("change_password_form.html");
    print_simple_box_end();
	print_footer();




/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/
function validate_form($frm, &$err) {

    if (empty($frm->username))
        $err->username = get_string("missingusername");

    else if (empty($frm->password))
        $err->password = get_string("missingpassword");

    else if (!verify_login($frm->username, $frm->password))
		$err->password = get_string("wrongpassword");

    if (empty($frm->newpassword1))
        $err->newpassword1 = get_string("missingnewpassword");

    if (empty($frm->newpassword2))
        $err->newpassword2 = get_string("missingnewpassword");

    else if ($frm->newpassword1 <> $frm->newpassword2)
        $err->newpassword2 = get_string("passwordsdiffer");

    return;
}

?>
