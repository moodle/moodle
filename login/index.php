<?PHP // $Id$
    require("../config.php");


    if (match_referer() && isset($HTTP_POST_VARS)) {    // form submitted

        $frm = (object)$HTTP_POST_VARS;
        $user = verify_login($frm->username, $frm->password);

	    update_login_count();

        if ($user) {
            if (! $user->confirmed ) {       // they never confirmed via email 
                print_header("Need to confirm", "Not confirmed yet", "", ""); 
                include("index_confirm.html");
                die;
            }
    
            $USER = $user;
            $USER->loggedin = true;
    
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
		    }
    
		    reset_login_count();
            add_to_log("Logged in");

            if ($CFG->smsnotify) {
                $time = date("H:i D j M", time());
                $smstring = "$time - $USER->firstname $USER->lastname logged in to $CFG->sitename";
                system("echo \"$smstring   \" | /opt/bin/sendsms &> /dev/null &");
            }
    
            die;
    
        } else {
            $errormsg = "Invalid login, please try again";
        }
    }
    
    if (empty($SESSION->wantsurl)) {
	    $SESSION->wantsurl = $HTTP_REFERER;  
    }
    
    if (!$frm->username) 
        $frm->username = get_moodle_cookie();
    
    if ($frm->username) {
        $focus = "form.password";
    } else {
        $focus = "form.username";
    }
    
    print_header("Login to the site", "Login to the site", "Login", $focus); 

    include("index_form.html");

    exit;

    // No footer on this page

function update_user_login_times() {
    global $db, $USER;

    $USER->lastlogin = $USER->currentlogin;
    $USER->currentlogin = time();

    return $db->Execute("UPDATE user 
                         SET lastlogin='$USER->lastlogin', currentlogin='$USER->currentlogin'
                         WHERE id = '$USER->id'");
}
?>
