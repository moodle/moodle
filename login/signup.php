<?PHP // $Id$

	require("../config.php");
	require("countries.php");

	if (match_referer() && isset($HTTP_POST_VARS)) {
		$user = (object) $HTTP_POST_VARS;

		validate_form($user, $err);

		if (count((array)$err) == 0) {

            $user->password = md5($user->password);
            $user->confirmed = 0;
            $user->firstaccess = time();

			if (! ($user->id = insert_record("user", $user)) ) {
                error("Could not add your record to the database!");
            }

            if (! send_confirmation_email($user)) {
                error("Tried to send you an email but failed!");
            }
	
	        print_header("Check your email", "Check your email", "Confirm", "");
			include("signup_confirm.html");
			exit;
		}
	}

	if ($err) {
		foreach ((array)$err as $key => $value) {
			$focus = "form.$key";
		}
	}

	print_header("New account", "New account", 
                 "<A HREF=\".\">Login</A> -> New Account", $focus);

	include("signup_form.php");



/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/

function validate_form($user, &$err) {

	if (empty($user->username))
		$err->username = "Missing username";

    else if (record_exists("user", "username", $user->username))
        $err->username = "This username already exists, choose another";

    else {
        $string = eregi_replace("[^([:alnum:])]", "", $user->username);
        if (strcmp($user->username, $string)) 
            $err->username = "Must only contain alphabetical characters";
    }


	if (empty($user->password)) 
		$err->password = "Missing password";

    if (empty($user->firstname))
        $err->firstname = "Missing first name";
        
    if (empty($user->lastname))
        $err->lastname = "Missing last name";
        

    if (empty($user->email))
        $err->email = "Missing email address";
        
    else if (! validate_email($user->email))
        $err->email = "Invalid email address, check carefully";
	
    else if (record_exists("user", "email", $user->email)) 
		$err->email = "Email address already registered. <A HREF=forgot_password.php>New password?</A>";


	if (empty($user->phone)) 
		$err->phone = "Missing phone number";

	if (empty($user->city)) 
		$err->city = "Missing city";

	if (empty($user->country)) 
		$err->country = "Missing country";

    return;
}


function send_confirmation_email($user) {

    global $CFG;

    $site  = get_site();
    $from = get_admin();

    $message  = "Hi $user->firstname,\n\n";

    $message .= "A new account has been requested at '$site->fullname'\n";
    $message .= "using your email address.\n\n";

    $message .= "To confirm your new account, please go to the \n";
    $message .= "following web address:\n\n";

    $message .= "$CFG->wwwroot/login/confirm.php?x=$user->id&s=$user->username\n\n";

    $message .= "In most mail programs, this should appear as a blue link\n";
    $message .= "which you can just click on.  If that doesn't work, \n";
    $message .= "then cut and paste the address into the address\n";
    $message .= "line at the top of your web browser window.\n\n";

    $message .= "Cheers from the '$site->fullname' administrator,\n";
    $message .= "$from->firstname $from->lastname ($from->email)\n";

    $subject  = "$site->fullname account confirmation";

    return email_to_user($user, $from, $subject, $message);

}



?>
