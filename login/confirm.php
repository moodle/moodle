<?PHP // $Id$

	require("../config.php");

	if ( isset($x) && isset($s) ) {     #  x = user.id   s = user.username

		$user = get_user_info_from_db("id", "$x");

		if ($user) {
			if ($user->username == $s) {

                if ($user->confirmed) {
				    print_header("Registration already confirmed", "Already confirmed", "Confirmed", "");
				    echo "<CENTER><H3>Thanks, ". $USER->firstname ." ". $USER->lastname . "</H3>\n";
        		    echo "<H4>Your registration has already been confirmed.</H4>\n";
        		    echo "<H3><A HREF=\"$CFG->wwwroot/course/\">Proceed to the courses</A></H3>\n";
				    print_footer();
                    exit;
                }

				$USER = $user;

				$timenow = time();

				$rs = $db->Execute("UPDATE user SET confirmed=1, lastIP='$REMOTE_ADDR', 
													firstaccess='$timenow', lastaccess='$timenow'
									WHERE id = '$USER->id' ");
				if (!$rs) error("Could not update this user while confirming");

				set_moodle_cookie($USER->username);

				$USER->loggedin = true;
				$USER->confirmed = 1;

				if ( ! empty($SESSION["wantsurl"]) ) {
					$goto = $SESSION["wantsurl"];
					redirect("$goto");
        		}
 
				print_header("Registration confirmed", "Confirmed", "Confirmed", "");
				echo "<CENTER><H3>Thanks, ". $USER->firstname ." ". $USER->lastname . "</H3>\n";
        		echo "<H4>Your registration is now confirmed.</H4>\n";
        		echo "<H3><A HREF=\"$CFG->wwwroot/course/\">Show me the courses</A></H3>\n";
				print_footer();
			} else {
				error("Invalid confirmation data");
			}
		}

	} else {
    	redirect("$CFG->wwwroot");
	}

?>
