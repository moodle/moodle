<?PHP // $Id$
      // Displays different views of the logs.

    require_once("../config.php");
    require_once("lib.php");

    require_variable($id);    // Course ID
    optional_variable($user, 0); // User to display
    optional_variable($date, 0); // Date to display

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

    $strlogs = get_string("logs");
    $stradministration = get_string("administration");


    if ($user || $date) {
        $userinfo = get_string("allparticipants");
        $dateinfo = get_string("alldays");

        if ($user) {
            if (!$u = get_record("user", "id", $user) ) {
                error("That's an invalid user!");
            }
            $userinfo = "$u->firstname $u->lastname";
        }
        if ($date) {
            $dateinfo = userdate($date, get_string("strftimedaydate"));
        }

        if ($course->category) {
            print_header("$course->shortname: $strlogs", "$course->fullname", 
                         "<A HREF=\"view.php?id=$course->id\">$course->shortname</A> ->
                          <A HREF=\"log.php?id=$course->id\">$strlogs</A> -> $userinfo, $dateinfo", "");
        } else {
            print_header("$course->shortname: $strlogs", "$course->fullname", 
                         "<A HREF=\"$CFG->wwwroot/admin/\">$stradministration</A> ->
                          <A HREF=\"log.php?id=$course->id\">$strlogs</A> -> $userinfo, $dateinfo", "");
        }
        
        print_heading("$course->fullname: $userinfo, $dateinfo (".usertimezone().")");

        print_log_selector_form($course, $user, $date);

        print_log($course, $user, $date, "ORDER BY l.time DESC");

    } else {
        if ($course->category) {
            print_header("$course->shortname: $strlogs", "$course->fullname", 
                     "<A HREF=\"view.php?id=$course->id\">$course->shortname</A> -> $strlogs", "");
        } else {
            print_header("$course->shortname: $strlogs", "$course->fullname", 
                     "<A HREF=\"$CFG->wwwroot/admin/\">$stradministration</A> -> $strlogs", "");
        }

        print_heading(get_string("chooselogs").":");

        print_log_selector_form($course);

        echo "<BR>";
        print_heading(get_string("chooselivelogs").":");

        echo "<CENTER><H3>";
        link_to_popup_window("/course/loglive.php?id=$course->id","livelog", get_string("livelogs"), 500, 800);
        echo "</H3></CENTER>";
    }

    print_footer($course);

    exit;

?>
