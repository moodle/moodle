<?PHP // $Id$
    require("../config.php");


    // Check if the guest user exists.  If not, create one.
    if (! record_exists("user", "username", "guest")) {
        $guest->username    = "guest"; 
        $guest->password    = md5("guest");
        $guest->firstname   = "Guest";
        $guest->lastname    = "User";
        $guest->email       = "root@localhost";
        $guest->description = "This user is a special user that allows read-only access to some courses.";
        $guest->confirmed   = 1;
        $guest->timemodified= time();

        if (! $guest->id = insert_record("user", $guest)) {
            notify("Could not create guest user record !!!");
        }
    }


    if (match_referer() && isset($HTTP_POST_VARS)) {    // form submitted

        $frm = (object)$HTTP_POST_VARS;
        $user = verify_login($frm->username, $frm->password);

	    update_login_count();

        if ($user) {
            if (! $user->confirmed ) {       // they never confirmed via email 
                print_header(get_string("mustconfirm"), get_string("mustconfirm") ); 
                include("index_confirm.html");
                print_footer();
                die;
            }

            $USER = $user;
            $USER->loggedin = true;
            $USER->site = $CFG->wwwroot;   // for added security
            save_session("USER");
    
            if (!update_user_in_db()) {
                error("Weird error: User not found");
            }

            if (!update_user_login_times()) {
                error("Wierd error: could not update login records");
            }

		    set_moodle_cookie($USER->username);

    
		    if (empty($SESSION->wantsurl)) {
        	    header("Location: $CFG->wwwroot");
		    } else {
        	    header("Location: $SESSION->wantsurl");
			    unset($SESSION->wantsurl);
                save_session("SESSION");
		    }
    
		    reset_login_count();

            die;
    
        } else {
            $errormsg = get_string("invalidlogin");
        }
    }

    
    if (empty($SESSION->wantsurl)) {
	    $SESSION->wantsurl = $HTTP_REFERER;
        save_session("SESSION");
    }
    
    if (!$frm->username) 
        $frm->username = get_moodle_cookie();
    
    if ($frm->username) {
        $focus = "form.password";
    } else {
        $focus = "form.username";
    }
    
    $loginsite = get_string("loginsite");

    print_header($loginsite, $loginsite, get_string("login"), $focus); 
    include("index_form.html");
    print_footer();

    exit;

    // No footer on this page

function update_user_login_times() {
    global $db, $USER;

    $USER->lastlogin = $USER->currentlogin;
    $USER->currentlogin = time();
    save_session("USER");

    return $db->Execute("UPDATE user 
                         SET lastlogin='$USER->lastlogin', currentlogin='$USER->currentlogin'
                         WHERE id = '$USER->id'");
}
?>
