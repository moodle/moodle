<?PHP // $Id$

	require("../config.php");

	if ( isset($x) && isset($s) ) {     #  x = user.id   s = user.username

		$user = get_user_info_from_db("id", "$x");

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

				$timenow = time();

				$rs = $db->Execute("UPDATE user SET confirmed=1, lastIP='$REMOTE_ADDR', 
													firstaccess='$timenow', lastaccess='$timenow'
									WHERE id = '$USER->id' ");
				if (!$rs) {
                    error("Could not update this user while confirming");
                }

				set_moodle_cookie($USER->username);

				$USER->loggedin = true;
				$USER->confirmed = 1;

                save_session("USER");

				if ( ! empty($SESSION->wantsurl) ) {
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
