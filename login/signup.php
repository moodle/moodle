<?PHP // $Id$

    require_once("../config.php");
    require_once("../auth/$CFG->auth/lib.php");

    if ($CFG->auth != 'email' and (empty($CFG->auth_user_create) or !(function_exists('auth_user_create'))) ) {
        error("Sorry, you may not use this page.");
    }

    if ($user = data_submitted()) {

        $user->firstname = strip_tags($user->firstname);
        $user->lastname = strip_tags($user->lastname);
        $user->email = strip_tags($user->email);

        validate_form($user, $err);
        $user->username= trim(moodle_strtolower($user->username));

        if (count((array)$err) == 0) {
            $plainpass = $user->password;
            $user->password = md5($user->password);
            $user->confirmed = 0;
            $user->lang = current_language();
            $user->firstaccess = time();
            $user->secret = random_string(15);
            if (!empty($CFG->auth_user_create) and function_exists('auth_user_create') ){
                if (! auth_user_exists($user->username)) {
                    if (! auth_user_create($user,$plainpass)) {
                        error("Could not add user to authentication module!");
                    }
                } else {
                    error("User already exists on authentication database.");
                }
            }

            if (! ($user->id = insert_record("user", $user)) ) {
                error("Could not add your record to the database!");
            }

            if (! send_confirmation_email($user)) {
                error("Tried to send you an email but failed!");
            }
    
            $emailconfirm = get_string("emailconfirm");
            print_header($emailconfirm, $emailconfirm, $emailconfirm);
            notice(get_string("emailconfirmsent", "", $user->email), "$CFG->wwwroot/");
            exit;
        }
    }

    if (!empty($err)) {
        $focus = "form.".array_shift(array_flip(get_object_vars($err)));
    } else {
        $focus = "";
    }

    if (empty($user->country) and !empty($CFG->country)) {
        $user->country = $CFG->country;
    }

    $newaccount = get_string("newaccount");
    $login = get_string("login");

    if (empty($CFG->langmenu)) {
        $langmenu = "";
    } else {
        $currlang = current_language();
        $langs    = get_list_of_languages();
        $langmenu = popup_form ("$CFG->wwwroot/login/signup.php?lang=", $langs, "chooselang", $currlang, "", "", "", true);
    }

    print_header($newaccount, $newaccount, "<A HREF=\"index.php\">$login</A> -> $newaccount", $focus, "", true, "<div align=right>$langmenu</div>");
    include("signup_form.html");
    print_footer();



/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/

function validate_form($user, &$err) {
    global $CFG;

    if (empty($user->username)){
        $err->username = get_string("missingusername");
    } else{
        $user->username = trim(moodle_strtolower($user->username));
        if (record_exists("user", "username", $user->username)){
            $err->username = get_string("usernameexists");
        } else {
            if (empty($CFG->extendedusernamechars)) {
                $string = eregi_replace("[^(-\.[:alnum:])]", "", $user->username);
                if (strcmp($user->username, $string)) {
                    $err->username = get_string("alphanumerical");
                }
            }
        }
    }

    if (isset($CFG->auth_user_create) and $CFG->auth_user_create==1 and function_exists('auth_user_exists') ){
        if (auth_user_exists($user->username)) {
            $err->username = get_string("usernameexists");
        }
    }         


    if (empty($user->password)) {
        $err->password = get_string("missingpassword");
    }

    if (empty($user->firstname)) {
        $err->firstname = get_string("missingfirstname");
    }
        
    if (empty($user->lastname)) {
        $err->lastname = get_string("missinglastname");
    }
        

    if (empty($user->email)) {
        $err->email = get_string("missingemail");
        
    } else if (! validate_email($user->email)) {
        $err->email = get_string("invalidemail");
    
    } else if (record_exists("user", "email", $user->email)) {
        $err->email = get_string("emailexists")." <A HREF=forgot_password.php>".get_string("newpassword")."?</A>";
    }
    

    if (empty($user->email2)) {
        $err->email2 = get_string("missingemail");

    } else if ($user->email2 != $user->email) {
        $err->email2 = get_string("invalidemail");
    }


    if (empty($user->city)) {
        $err->city = get_string("missingcity");
    }

    if (empty($user->country)) {
        $err->country = get_string("missingcountry");
    }

    return;
}


?>
