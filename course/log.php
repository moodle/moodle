<?PHP // $Id$

//  log.php - displays different views of the logs.

    require("../config.php");
    require("lib.php");

    require_variable($id);    // Course ID
    optional_variable($user); // User to display
    optional_variable($date); // Date to display

    require_login($id);

    if (! $course = get_record("course", "id", $id) ) {
        error("That's an invalid course id");
    }

    if (! isteacher($course->id)) {
        error("Only teachers can view logs");
    }

    if (! $course->category) {
        if (!isadmin()) {
            error("Only administrators can look at the site logs");
        }
        $user = "";
    }


    if ($user || $date) {

        $userinfo = "all users";
        $dateinfo = "any day";

        if ($user) {
            if (!$u = get_record("user", "id", $user) ) {
                error("That's an invalid user!");
            }
            $userinfo = "$u->firstname $u->lastname";
        }
        if ($date) {
            $dateinfo = userdate($date, "l, j F Y");
        }

        print_header("$course->shortname: Logs", "$course->fullname", 
                     "<A HREF=\"view.php?id=$course->id\">$course->shortname</A> ->
                      <A HREF=\"log.php?id=$course->id\">Logs</A> -> Logs for $userinfo, $dateinfo", "");
        
        print_heading("$course->fullname: $userinfo, $dateinfo (".usertimezone().")");

        print_log_selector_form($course, $user, $date);

        print_log($course, $user, $date, "ORDER BY l.time DESC");


    } else {
        print_header("$course->shortname: Logs", "$course->fullname", 
                 "<A HREF=\"view.php?id=$course->id\">$course->shortname</A> -> Logs", "");

        print_heading("Choose which logs you want to look at");

        print_log_selector_form($course);

        print_heading("Or see what is happening right now");

        echo "<CENTER><H3>";
        link_to_popup_window("/course/loglive.php?id=$course->id","livelog","Live logs", 500, 800);
        echo "</H3></CENTER>";

    }

    print_footer($course);

    exit;

?>
