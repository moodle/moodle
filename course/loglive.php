<?PHP // $Id$
      //  Displays live view of recent logs

    require("../config.php");
    require("lib.php");

    require_login($id);

    if (! $course = get_record("course", "id", $id) ) {
        error("That's an invalid course id");
    }

    if (! isteacher($course->id)) {
        error("Only teachers can view logs");
    }

    $strlivelogs = get_string("livelogs");
    $strupdatesevery = get_string("updatesevery", "moodle", $COURSE_LIVELOG_REFRESH);

    print_header("$strlivelogs ($strupdatesevery)", "$strlivelogs", "", "", 
                 "<META HTTP-EQUIV='Refresh' CONTENT='$COURSE_LIVELOG_REFRESH; URL=loglive.php?id=$id'>");

    $user=0;
    $date=time() - 3600;

    print_log($course, $user, $date, "ORDER BY l.time DESC");

    exit;

?>
