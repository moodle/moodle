<?PHP // $Id$
      // Displays different views of the logs.

    require_once("../config.php");
    require_once("lib.php");

    require_variable($id);    // Course ID
    optional_variable($user, 0); // User to display
    optional_variable($date, 0); // Date to display
    optional_variable($url, ""); // eg resource/view.php?id=66
    optional_variable($page, "0");     // which page to show
    optional_variable($perpage, "100"); // how many per page

    require_login();

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
    }

    $strlogs = get_string("logs");
    $stradministration = get_string("administration");


    if (!empty($_GET['chooselog'])) {
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

        if ($url) {     // parse into parts:  resource/view.php?id=66 
            $urlraw = urldecode($url);
            $urlparts = split("/", $urlraw);
            if ($urlparts[0] != "section" and $urlparts[0] != "") {
                $modname = $urlparts[0];
                $modparts = split('.php\?id=', $urlparts[1]);
                if (count($modparts) == 2) {
                    if (record_exists('course_modules', 'id', $modparts[1], 'course', $course->id)) {
                        $modpage = $modparts[0];
                        $modid = $modparts[1];
                    }
                }
            }
        }

        if ($course->category) {
            print_header("$course->shortname: $strlogs", "$course->fullname", 
                         "<a href=\"view.php?id=$course->id\">$course->shortname</a> ->
                          <a href=\"log.php?id=$course->id\">$strlogs</a> -> $userinfo, $dateinfo", "");
        } else {
            print_header("$course->shortname: $strlogs", "$course->fullname", 
                         "<a href=\"../$CFG->admin/index.php\">$stradministration</a> ->
                          <a href=\"log.php?id=$course->id\">$strlogs</a> -> $userinfo, $dateinfo", "");
        }
        
        print_heading("$course->fullname: $userinfo, $dateinfo (".usertimezone().")");

        print_log_selector_form($course, $user, $date, $modname, $modpage, $modid);

        print_log($course, $user, $date, "l.time DESC", $page, $perpage, 
                  "log.php?id=$course->id&chooselog=1&user=$user&date=$date&url=$url", 
                  $modname, $modpage, $modid);

    } else {
        if ($course->category) {
            print_header("$course->shortname: $strlogs", "$course->fullname", 
                     "<a href=\"view.php?id=$course->id\">$course->shortname</a> -> $strlogs", "");
        } else {
            print_header("$course->shortname: $strlogs", "$course->fullname", 
                     "<a href=\"../$CFG->admin/index.php\">$stradministration</a> -> $strlogs", "");
        }

        print_heading(get_string("chooselogs").":");

        print_log_selector_form($course);

        echo "<br />";
        print_heading(get_string("chooselivelogs").":");

        echo "<center><h3>";
        link_to_popup_window("/course/loglive.php?id=$course->id","livelog", get_string("livelogs"), 500, 800);
        echo "</h3></center>";
    }

    print_footer($course);

    exit;

?>
