<?PHP // $Id$

    require_once("../config.php");
    require_once("../auth/$CFG->auth/lib.php");

    if (isset($_GET['p']) and isset($_GET['s']) ) {     #  p = user.secret   s = user.username

        $user = get_user_info_from_db("username", "$s");

        if (!empty($user)) {

            if ($user->confirmed) {
                print_header(get_string("alreadyconfirmed"), get_string("alreadyconfirmed"), "", "");
                echo "<CENTER><H3>".get_string("thanks").", ". $user->firstname ." ". $user->lastname . "</H3>\n";
                echo "<H4>".get_string("alreadyconfirmed")."</H4>\n";
                echo "<H3> -> <A HREF=\"$CFG->wwwroot/course/\">".get_string("courses")."</A></H3>\n";
                print_footer();
                exit;
            }

            if ($user->secret == $p) {   // They have provided the secret key to get in

                if (!set_field("user", "confirmed", 1, "id", $user->id)) {
                    error("Could not confirm this user!");
                }
                if (!set_field("user", "firstaccess", time(), "id", $user->id)) {
                    error("Could not set this user's first access date!");
                }
                if (isset($CFG->auth_user_create) and $CFG->auth_user_create==1 and function_exists('auth_user_activate') ) {
				    if (!auth_user_activate($user->username)) {
					  error("Could not activate this user!");
					}
                }

                // The user has confirmed successfully, let's log them in
                
                if (!$USER = get_user_info_from_db("username", $user->username)) {
                    error("Something serious is wrong with the database");
                }

                set_moodle_cookie($USER->username);

                $USER->loggedin = true;
                $USER->site = $CFG->wwwroot;

                if ( ! empty($SESSION->wantsurl) ) {   // Send them where they were going
                    $goto = $SESSION->wantsurl;
                    unset($SESSION->wantsurl);
                    redirect("$goto");
                }
 
                print_header(get_string("confirmed"), get_string("confirmed"), "", "");
                echo "<CENTER><H3>".get_string("thanks").", ". $USER->firstname ." ". $USER->lastname . "</H3>\n";
                echo "<H4>".get_string("confirmed")."</H4>\n";
                echo "<H3> -> <A HREF=\"$CFG->wwwroot/course/\">".get_string("courses")."</A></H3>\n";
                print_footer();
                exit;

            } else {
                error("Invalid confirmation data");
            }
        }
    } else {
        error(get_string("errorwhenconfirming"));
    }

    redirect("$CFG->wwwroot/");

?>
