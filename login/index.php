<?PHP // $Id$

    require_once("../config.php");

    // Check if the guest user exists.  If not, create one.
    if (! record_exists("user", "username", "guest")) {
        $guest->username    = "guest"; 
        $guest->password    = md5("guest");
        $guest->firstname   = addslashes(get_string("guestuser"));
        $guest->lastname    = " ";
        $guest->email       = "root@localhost";
        $guest->description = addslashes(get_string("guestuserinfo"));
        $guest->confirmed   = 1;
        $guest->lang        = $CFG->lang;
        $guest->timemodified= time();

        if (! $guest->id = insert_record("user", $guest)) {
            notify("Could not create guest user record !!!");
        }
    }

    $frm = false;
    if (!empty($SESSION->wantsurl) and strstr($SESSION->wantsurl,"username=guest")) {
        /// Log in as guest automatically (idea from Zbigniew Fiedorowicz)
        $frm->username = "guest";
        $frm->password = "guest";
    } else {
        $frm = data_submitted();
    }

    if ($frm) {
        $frm->username = trim(moodle_strtolower($frm->username));
        $user = authenticate_user_login($frm->username, $frm->password);
        update_login_count();

        if ($user) {
            if (! $user->confirmed ) {       // they never confirmed via email 
                print_header(get_string("mustconfirm"), get_string("mustconfirm") ); 
                print_heading(get_string("mustconfirm"));
                print_simple_box(get_string("emailconfirmsent", "", $user->email), "center");
                print_footer();
                die;
            }

            $USER = $user;
            if (!empty($USER->description)) {
                $USER->description = true;       // No need to cart all of it around
            }
            $USER->loggedin = true;
            $USER->site = $CFG->wwwroot;   // for added security
            
            if ($USER->username == "guest") {
                $USER->lang       = $CFG->lang;               // Guest language always same as site
                $USER->firstname  = get_string("guestuser");  // Name always in current language
                $USER->lastname   = " ";
            }
    
            if (!update_user_in_db()) {
                error("Weird error: User not found");
            }
            
            if (!update_user_login_times()) {
                error("Wierd error: could not update login records");
            }

            set_moodle_cookie($USER->username);

            $wantsurl = $SESSION->wantsurl;

            unset($SESSION->wantsurl);
            unset($SESSION->lang);

            if (user_not_fully_set_up($USER)) {
                $site = get_site();
                redirect("$CFG->wwwroot/user/edit.php?id=$USER->id&course=$site->id");

            } else if (strpos($wantsurl, $CFG->wwwroot) === 0) {   /// Matches site address
                redirect($wantsurl);

            } else {
                redirect("$CFG->wwwroot/");      /// Go to the standard home page
            }
    
            reset_login_count();

            die;
    
        } else {
            $errormsg = get_string("invalidlogin");
        }
    }

    
    if (empty($errormsg)) {
        $errormsg = "";
    }

    if (empty($SESSION->wantsurl)) {
        $SESSION->wantsurl = $_SERVER["HTTP_REFERER"];
    }
    
    if (empty($frm->username)) {
        $frm->username = get_moodle_cookie();
        $frm->password = "";
    }
    
    if (!empty($frm->username)) {
        $focus = "login.password";
    } else {
        $focus = "login.username";
    }

    if ($CFG->auth == "email" or $CFG->auth == "none" or chop($CFG->auth_instructions) <> "" ) {
        $show_instructions = true;
    } else {
        $show_instructions = false;
    }
    
    if (!$site = get_site()) {
        error("No site found!");
    }

    if (empty($CFG->langmenu)) {
        $langmenu = "";
    } else {
        $currlang = current_language();
        $langs    = get_list_of_languages();
        $langmenu = popup_form ("$CFG->wwwroot/login/index.php?lang=", $langs, "chooselang", $currlang, "", "", "", true);
    }

    $loginsite = get_string("loginsite");

    print_header("$site->fullname: $loginsite", "$site->fullname", $loginsite, $focus, "", true, "<div align=right>$langmenu</div>"); 
    include("index_form.html");
    print_footer();

    exit;

    // No footer on this page

?>
