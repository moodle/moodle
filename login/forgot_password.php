<?PHP // $Id$

	include("../config.php");

	if (match_referer() && isset($HTTP_POST_VARS)) {

		$frm = (object)$HTTP_POST_VARS;

		validate_form($frm, $err);

		update_login_count();

		if (count((array)$err) == 0) {

			if (!$user = get_user_info_from_db("email", $frm->email)) {
                error("No such user with this address:  $frm->email");
            }

			if (! reset_password_and_mail($user)) {
                error("Could not reset password and mail the new one to you");
            }

			reset_login_count();
	        print_header("Password has been sent", "Password has been sent", "Password Sent", "");
	        include("forgot_password_change.html");
			exit;
		}
	}

	if ( empty($frm->email) ) {
		if ( $username = get_moodle_cookie() ) {
			$frm->email = get_field("user", "email", "username", "$username");
		}
	}

	print_header("Forgot password?", "Have a new password sent to you", "", "form.email");

	include("forgot_password_form.html");


/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/

function validate_form($frm, &$err) {

    if (empty($frm->email))
        $err->email = "Missing email address";

    else if (! validate_email($frm->email))
        $err->email = "Invalid email address";

    else if (! record_exists("user", "email", $frm->email))
        $err->email = "No such email address on file";

}


function reset_password_and_mail($user) {

    global $CFG;

    $site  = get_site();
    $from = get_admin();

    $newpassword = generate_password();

    if (! set_field("user", "password", md5($newpassword), "id", $user->id) ) {
        error("Could not set user password!");
    }

    $message  = "Hi $user->firstname,\n\n";

    $message .= "Your account password at '$site->fullname' has been reset\n";
    $message .= "and you have been issued with a new temporary password.\n\n";

    $message .= "Your current login information is now:\n\n";

    $message .= "   username: $user->username\n";
    $message .= "   password: $newpassword\n\n";

    $message .= "Please go to this page to change your password:\n\n";

    $message .= "$CFG->wwwroot/login/change_password.php\n\n";

    $message .= "In most mail programs, this should appear as a blue link\n";
    $message .= "which you can just click on.  If that doesn't work, \n";
    $message .= "then cut and paste the address into the address\n";
    $message .= "line at the top of your web browser window.\n\n";

    $message .= "Cheers from the '$site->fullname' administrator,\n";
    $message .= "$from->firstname $from->lastname ($from->email)\n";

    $subject  = "$site->fullname: Changed password";

    return email_to_user($user, $from, $subject, $message);

}



?>
