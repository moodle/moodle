<?PHP // $Id$

	require("../config.php");
	require("../lib/countries.php");

	if (match_referer() && isset($HTTP_POST_VARS)) {
		$user = (object) $HTTP_POST_VARS;

		validate_form($user, $err);

		if (count((array)$err) == 0) {

            $user->password = md5($user->password);
            $user->confirmed = 0;
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
		foreach ((array)$err as $key => $value) {
			$focus = "form.$key";
		}
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


function random_string ($length=15) {
    $pool  = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $pool .= "abcdefghijklmnopqrstuvwxyz";
    $pool .= "0123456789";
    $poollen = strlen($pool);
    mt_srand ((double) microtime() * 1000000);
    $string = "";
    for ($i = 0; $i < $length; $i++) {
        $string .= substr($pool, (mt_rand()%($poollen)), 1);
    }
    return $string;
}


function send_confirmation_email($user) {

    global $CFG;

    $site = get_site();
    $from = get_admin();

    $data->firstname = $user->firstname;
    $data->sitename = $site->fullname;
    $data->link = "$CFG->wwwroot/login/confirm.php?p=$user->secret&s=$user->username";
    $data->admin = "$from->firstname $from->lastname ($from->email)";

    $message = get_string("emailconfirmation", "", $data);
    $subject = "$site->fullname account confirmation";

    return email_to_user($user, $from, $subject, $message);

}



?>
