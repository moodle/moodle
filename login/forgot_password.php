<?PHP // $Id$

	require_once("../config.php");

    optional_variable($p, "");
    optional_variable($s, "");

    if (!empty($p) and !empty($s)) {  // User trying to authenticate change password routine

		update_login_count();

        $user = get_user_info_from_db("username", "$s");

        if (!empty($user)) {
            if ($user->secret == $p) {   // They have provided the secret key to get in

                if (isguest($user->id)) {
                    error("Can't change guest password!");
                }
    
			    if (! reset_password_and_mail($user)) {
                    error("Could not reset password and mail the new one to you");
                }
    
			    reset_login_count();

	            print_header(get_string("passwordsent"), get_string("passwordsent"), get_string("passwordsent"));

                $a->email = $user->email;
                $a->link = "$CFG->wwwroot/login/change_password.php";
                notice(get_string("emailpasswordsent", "", $a), $a->link);
            }
        }
        error(get_string("error"));
    }

	if ($frm = data_submitted()) {    // Initial request for new password

		validate_form($frm, $err);

		if (count((array)$err) == 0) {

			if (!$user = get_user_info_from_db("email", $frm->email)) {
                error("No such user with this address:  $frm->email");
            }

            if (empty($user->confirmed)) {
                error(get_string("confirmednot"));
            }
            
            $user->secret = random_string(15);
            
			if (!set_field("user", "secret", $user->secret, "id", $user->id)) {
                error("Could not set user secret string!");
            }

            if (! send_password_change_confirmation_email($user)) {
                error("Could not send you an email to confirm the password change");
            }

	        print_header(get_string("passwordconfirmchange"), get_string("passwordconfirmchange"));
            
            notice(get_string('emailpasswordconfirmsent', '', $user->email), "$CFG->wwwroot/");
        }
	}

	if (empty($frm->email)) {
		if ($username = get_moodle_cookie() ) {
			$frm->email = get_field("user", "email", "username", "$username");
		}
	}

	print_header(get_string("senddetails"), get_string("senddetails"), 
                 "<A HREF=\"$CFG->wwwroot/login/index.php\">".get_string("login")."</A> -> ".get_string("senddetails"), 
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
