<?PHP // $Id$

	require_once("../config.php");

	if ( isset($p) && isset($s) ) {     #  p = user.secret   s = user.username

		$user = get_user_info_from_db("secret", "$p");

		if ($user) {
			if ($user->username == $s) {

                if ($user->confirmed) {
				    print_header(get_string("alreadyconfirmed"), get_string("alreadyconfirmed"), "", "");
				    echo "<CENTER><H3>".get_string("thanks").", ". $USER->firstname ." ". $USER->lastname . "</H3>\n";
        		    echo "<H4>".get_string("alreadyconfirmed")."</H4>\n";
        		    echo "<H3> -> <A HREF=\"$CFG->wwwroot/course/\">".get_string("courses")."</A></H3>\n";
				    print_footer();
                    exit;
                }

				$USER = $user;

                if (!set_field("user", "confirmed", 1, "id", $USER->id)) {
                    error("Could not confirm this user!");
                }
                if (!set_field("user", "firstaccess", time(), "id", $USER->id)) {
                    error("Could not set this user's first access date!");
                }
                if (!update_user_in_db($USER->id)) {
                    error("Could not update this user's information");
                }

				set_moodle_cookie($USER->username);

                // The user has confirmed successfully, let's log them in

				$USER->loggedin = true;
				$USER->confirmed = 1;
				$USER->site = $CFG->wwwroot;
                save_session("USER");

				if ( ! empty($SESSION->wantsurl) ) {   // Send them where they were going
					$goto = $SESSION->wantsurl;
                    unset($SESSION->wantsurl);
                    save_session("SESSION");
					redirect("$goto");
        		}
 
				print_header(get_string("confirmed"), get_string("confirmed"), "", "");
				echo "<CENTER><H3>".get_string("thanks").", ". $USER->firstname ." ". $USER->lastname . "</H3>\n";
        		echo "<H4>".get_string("confirmed")."</H4>\n";
        		echo "<H3> -> <A HREF=\"$CFG->wwwroot/course/\">".get_string("courses")."</A></H3>\n";
				print_footer();

			} else {
				error("Invalid confirmation data");
			}
		}

	} else {
    	redirect("$CFG->wwwroot");
	}

?>
