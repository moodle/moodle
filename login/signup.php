<?PHP // $Id$

	require_once("../config.php");
	require_once("../lib/countries.php");

	if ($user = data_submitted()) {

		validate_form($user, $err);

		if (count((array)$err) == 0) {

            $user->password = md5($user->password);
            $user->confirmed = 0;
            $user->lang = $CFG->lang;
            $user->firstaccess = time();
            $user->secret = random_string(15);

			if (! ($user->id = insert_record("user", $user)) ) {
                error("Could not add your record to the database!");
            }

            if (! send_confirmation_email($user)) {
                error("Tried to send you an email but failed!");
            }
	
            $emailconfirm = get_string("emailconfirm");
	        print_header($emailconfirm, $emailconfirm, $emailconfirm);
            echo "<CENTER>";
            print_string("emailconfirmsent", "", $user->email);
            echo "</CENTER>";
            print_footer();
			exit;
		}
	}

    if ($err) {
        $focus = 'form.' . array_shift(array_flip(get_object_vars($err)));
    }

    if (empty($user->country) and !empty($CFG->country)) {
        $user->country = $CFG->country;
    }

    $newaccount = get_string("newaccount");
    $login = get_string("login");

	print_header($newaccount, $newaccount, "<A HREF=\".\">$login</A> -> $newaccount", $focus);
	include("signup_form.php");
    print_footer();



/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/

function validate_form($user, &$err) {

	if (empty($user->username))
		$err->username = get_string("missingusername");

    else if (record_exists("user", "username", $user->username))
        $err->username = get_string("usernameexists");

    else {
        $string = eregi_replace("[^([:alnum:])]", "", $user->username);
        if (strcmp($user->username, $string)) 
            $err->username = get_string("alphanumerical");
    }


	if (empty($user->password)) 
		$err->password = get_string("missingpassword");

    if (empty($user->firstname))
        $err->firstname = get_string("missingfirstname");
        
    if (empty($user->lastname))
        $err->lastname = get_string("missinglastname");
        

    if (empty($user->email))
        $err->email = get_string("missingemail");
        
    else if (! validate_email($user->email))
        $err->email = get_string("invalidemail");
	
    else if (record_exists("user", "email", $user->email)) 
		$err->email = get_string("emailexists")." <A HREF=forgot_password.php>".get_string("newpassword")."?</A>";


	if (empty($user->city)) 
		$err->city = get_string("missingcity");

	if (empty($user->country)) 
		$err->country = get_string("missingcountry");

    return;
}


?>
