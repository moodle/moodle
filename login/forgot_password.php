<?PHP // $Id$

	require_once("../config.php");

	if ($frm = data_submitted()) {

		validate_form($frm, $err);

		update_login_count();

		if (count((array)$err) == 0) {

			if (!$user = get_user_info_from_db("email", $frm->email)) {
                error("No such user with this address:  $frm->email");
            }

            if (isguest($user->id)) {
                error("Can't change guest password!");
            }

			if (! reset_password_and_mail($user)) {
                error("Could not reset password and mail the new one to you");
            }

			reset_login_count();
	        print_header(get_string("passwordsent"), get_string("passwordsent"), get_string("passwordsent"));
            echo "<CENTER>";
            $a->email = $frm->email;
            $a->link = "$CFG->wwwroot/login/change_password.php";
	        print_string("passwordsenttext", "", $a);
            echo "</CENTER>";
            print_footer();
			exit;
		}
	}

	if (empty($frm->email)) {
		if ($username = get_moodle_cookie() ) {
			$frm->email = get_field("user", "email", "username", "$username");
		}
	}

	print_header(get_string("senddetails"), get_string("senddetails"), 
                 "<A HREF=\"$CFG->wwwroot/login\">".get_string("login")."</A> -> ".get_string("senddetails"), 
                 "form.email");
	include("forgot_password_form.html");
    print_footer();


/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/

function validate_form($frm, &$err) {

    if (empty($frm->email))
        $err->email = get_string("missingemail");

    else if (! validate_email($frm->email))
        $err->email = get_string("invalidemail");

    else if (! record_exists("user", "email", $frm->email))
        $err->email = get_string("nosuchemail");

}


?>
