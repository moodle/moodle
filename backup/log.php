<?php  // $Id$
       // backup.php - allows admin to edit all configuration variables for scheduled backups

    require_once("../config.php");
    require_once("../backup/lib.php");

    $courseid = optional_param('courseid',0,PARAM_INT);

    require_login();

    require_capability('moodle/site:backup', get_context_instance(CONTEXT_SYSTEM, SITEID));

    if (!$site = get_site()) {
        error("Site isn't defined!");
    }

    //Get needed strings
    $backuploglaststatus = get_string("backuploglaststatus");
    $backuplogdetailed = get_string("backuplogdetailed");
    $stradmin = get_string("administration");
    $strconfiguration = get_string("configuration");
    $strbackup = get_string("backup");
    $strbackupdetails = get_string("backupdetails");
    $strlogs = get_string("logs");
    $strftimedatetime = get_string("strftimerecent");
    $strftimetime = get_string("strftimetime").":%S";
    $strerror = get_string("error");
    $strok = get_string("ok");
    $strunfinished = get_string("unfinished");
    $strskipped = get_string("skipped");
    $strcourse = get_string("course");
    $strtimetaken = get_string("timetaken","quiz");
    $strstatus = get_string("status");
    $strnext = get_string("next");

    //Decide when to show last execution logs or detailed logs
    //Lastlog view
    if (!$courseid) {
        $navlinks = array();
        $navlinks[] = array('name' => $stradmin, 'link' => "../$CFG->admin/index.php", 'type' => 'misc');
        $navlinks[] = array('name' => $strconfiguration, 'link' => "../$CFG->admin/configure.php", 'type' => 'misc');
        $navlinks[] = array('name' => $strbackup, 'link' => "../$CFG->admin/backup.php?sesskey=$USER->sesskey", 'type' => 'misc');
        $navlinks[] = array('name' => $strlogs, 'link' => null, 'type' => 'misc');
        $navigation = build_navigation($navlinks);

        print_header("$site->shortname: $strconfiguration: $strbackup", $site->fullname, $navigation);

        print_heading($backuploglaststatus);
        print_simple_box_start('center');
        //Now, get every record from backup_courses
        $courses = get_records("backup_courses");

        if (!$courses) {
            notify("No logs found!");
        } else {
            echo "<table border=\"0\" align=\"center\" cellpadding=\"3\" cellspacing=\"3\">";
            //Print table header
            echo "<tr>";
            echo "<td nowrap=\"nowrap\" align=\"center\"><font size=\"3\">$strcourse</font></td>";
            echo "<td nowrap=\"nowrap\" align=\"center\" colspan=\"3\"><font size=\"3\">$strtimetaken</font></td>";
            echo "<td nowrap=\"nowrap\" align=\"center\"><font size=\"3\">$strstatus</font></td>";
            echo "<td nowrap=\"nowrap\" align=\"center\"><font size=\"3\">$strnext</font></td></tr>";
            foreach ($courses as $course) {
                //Get the course shortname
                $coursename = get_field ("course","fullname","id",$course->courseid);
                if ($coursename) {
                    echo "<tr>";
                    echo "<td nowrap=\"nowrap\"><font size=\"2\"><a href=\"log.php?courseid=$course->courseid\">".$coursename."</a></font></td>";
                    echo "<td nowrap=\"nowrap\"><font size=\"2\">".userdate($course->laststarttime,$strftimedatetime)."</font></td>";
                    echo "<td nowrap=\"nowrap\"><font size=\"2\"> - </font></td>";
                    echo "<td nowrap=\"nowrap\"><font size=\"2\">".userdate($course->lastendtime,$strftimedatetime)."</font></td>";
                    if ($course->laststatus == 1) {
                        echo "<td nowrap=\"nowrap\" align=\"center\"><font size=\"2\" color=\"green\">".$strok."</font></td>";
                    } else if ($course->laststatus == 2) {
                        echo "<td nowrap=\"nowrap\" align=\"center\"><font size=\"2\" color=\"red\">".$strunfinished."</font></td>";
                    } else if ($course->laststatus == 3) {
                        echo "<td nowrap=\"nowrap\" align=\"center\"><font size=\"2\" color=\"green\">".$strskipped."</font></td>";
                    } else {
                        echo "<td nowrap=\"nowrap\" align=\"center\"><font size=\"2\" color=\"red\">".$strerror."</font></td>";
                    }
                    echo "<td nowrap=\"nowrap\"><font size=\"2\">".userdate($course->nextstarttime,$strftimedatetime)."</font></td>";
                    echo "</tr>";
                }
            }
            echo "</table>";
        }
        print_simple_box_end();
    //Detailed View !!
    } else {
        $navlinks = array();
        $navlinks[] = array('name' => $stradmin, 'link' => "../$CFG->admin/index.php", 'type' => 'misc');
        $navlinks[] = array('name' => $strconfiguration, 'link' => "../$CFG->admin/configure.php", 'type' => 'misc');
        $navlinks[] = array('name' => $strbackup, 'link' => "../$CFG->admin/backup.php?sesskey=$USER->sesskey", 'type' => 'misc');
        $navlinks[] = array('name' => $strlogs, 'link' => 'log.php', 'type' => 'misc');
        $navlinks[] = array('name' => $strbackupdetails, 'link' => null, 'type' => 'misc');
        $navigation = build_navigation($navlinks);

        print_header("$site->shortname: $strconfiguration: $strbackup", $site->fullname, $navigation);

        print_heading($backuplogdetailed);

        $coursename = get_field("course","fullname","id","$courseid");
        print_heading("$strcourse: $coursename");

        print_simple_box_start('center');

        //First, me get all the distinct backups for that course in backup_log
        $executions = get_records_sql("SELECT DISTINCT laststarttime,laststarttime
                                       FROM {$CFG->prefix}backup_log
                                       WHERE courseid = '$courseid'
                                       ORDER BY laststarttime DESC");

        //Iterate over backup executions
        if (!$executions) {
            notify("No logs found!");
        } else {
            echo "<table border=\"0\" align=\"center\" cellpadding=\"3\" cellspacing=\"3\">";
            foreach($executions as $execution) {
                echo "<tr>";
                echo "<td nowrap=\"nowrap\" align=\"center\" colspan=\"3\">";
                print_simple_box("<center>".userdate($execution->laststarttime)."</center>", "center");
                echo "</td>";
                echo "</tr>";
                $logs = get_records_sql("SELECT *
                                         FROM {$CFG->prefix}backup_log
                                         WHERE courseid = '$courseid'  AND
                                               laststarttime = '$execution->laststarttime'
                                         ORDER BY id");
                if ($logs) {
                    foreach ($logs as $log) {
                        echo "<tr>";
                        echo "<td nowrap=\"nowrap\"><font size=\"2\">".userdate($log->time,$strftimetime)."</font></td>";
                        $log->info = str_replace("- ERROR!!","- <font color=\"red\">ERROR!!</font>",$log->info);
                        $log->info = str_replace("- OK","- <font color=\"green\">OK</font>",$log->info);
                        echo "<td nowrap=\"nowrap\"><font size=\"2\">".str_replace("  ","&nbsp;&nbsp;&nbsp;&nbsp;",$log->info)."</font></td>";
                        echo "</tr>";
                    }
                }
            }
            echo "</table>";
        }
        print_simple_box_end();
    }


    print_footer();

?>
