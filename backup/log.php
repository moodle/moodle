<?PHP  // $Id$
       // backup.php - allows admin to edit all configuration variables for scheduled backups

    require_once("../config.php");
    require_once("../backup/lib.php");

    optional_variable($courseid);

    require_login();

    if (!isadmin()) {
        error("Only an admin can use this page");
    }

    if (!$site = get_site()) {
        error("Site isn't defined!");
    }

    //Get needed strings
    $backuploglaststatus = get_string("backuploglaststatus");
    $backuplogdetailed = get_string("backuplogdetailed");
    $stradmin = get_string("administration");
    $strconfiguration = get_string("configuration");
    $strbackup = get_string("backup");
    $strlogs = get_string("logs");
    $strftimedatetime = get_string("strftimerecent");
    $strerror = get_string("error");
    $strok = get_string("ok");
    $strcourse = get_string("course");
    $strtimetaken = get_string("timetaken","quiz");
    $strstatus = get_string("status");
    $strnext = get_string("next");

    print_header("$site->shortname: $strconfiguration: $strbackup", $site->fullname,
                  "<a href=\"../admin/index.php\">$stradmin</a> -> ".
                  "<a href=\"../admin/configure.php\">$strconfiguration</a> -> ".
                  "<a href=\"../admin/backup.php\">$strbackup</a> -> ".
                  $strlogs);

    //Decide when to show last execution logs or detailed logs
    //Lastlog view
    if (!$courseid) {
        print_heading($backuploglaststatus);
        print_simple_box_start("center", "", "$THEME->cellheading");
        //Now, get every record from backup_courses
        $courses = get_records("backup_courses");

        if (!$courses) {
            notify("No logs found!");
        } else {
            echo "<table border=0 align=center cellpadding=3 cellspacing=3>";
            //Print table header
            echo "<tr nowrap>";
            echo "<td nowrap align=center><font size=3>$strcourse</font></td>";
            echo "<td nowrap align=center colspan=3><font size=3>$strtimetaken</font></td>";
            echo "<td nowrap align=center><font size=3>$strstatus</font></td>";
            echo "<td nowrap align=center><font size=3>$strnext</font></td>";
            foreach ($courses as $course) {
                //Get the course shortname
                $coursename = get_field ("course","fullname","id",$course->courseid);
                if ($coursename) {
                    echo "<tr nowrap>";
                    echo "<td nowrap><font size=2><a href=\"../course/view.php?id=$course->courseid\">".$coursename."</a></td>";
                    echo "<td nowrap><font size=2>".userdate($course->laststarttime,$strftimedatetime)."</td>";
                    echo "<td nowrap><font size=2> - </td>";
                    echo "<td nowrap><font size=2>".userdate($course->lastendtime,$strftimedatetime)."</td>";
                    if (!$course->laststatus) {
                        echo "<td nowrap align=center><font size=2 color=red>".$strerror."</td>";
                    } else {
                        echo "<td nowrap align=center><font size=2 color=green>".$strok."</td>";
                    }
                    echo "<td nowrap><font size=2>".userdate($course->nextstarttime,$strftimedatetime)."</td>";
                    echo "</tr>";
                }
            }
            echo "</table>";
        }
        print_simple_box_end();
    //Detailed View !!
    } else {
        print_heading($backuplogdetailed);
        print_simple_box_start("center", "", "$THEME->cellheading");
        
        //First, me get all the distinct backups for that course in backup_log
        $executions = get_records_sql("SELECT DISTINCT id,laststarttime
                                       FROM {$CFG->prefix}backup_log
                                       WHERE courseid = '$courseid'");
    
        print_simple_box_end();
    }


    print_footer();

?>
