<?php // $Id$
      // index.php - scheduled backup logs

    require_once('../../../config.php');
    require_once($CFG->libdir.'/adminlib.php');
    require_once($CFG->dirroot.'/backup/lib.php');

    $courseid = optional_param('courseid', 0, PARAM_INT);

    admin_externalpage_setup('reportbackups');
    admin_externalpage_print_header();

/// Scheduled backups are disabled by the server admin
    if (!empty($CFG->disablescheduledbackups)) {
        print_error('scheduledbackupsdisabled', '', '', NULL, true);
    }

/// Scheduled backups aren't active by the site admin
    $backup_config = backup_get_config();
    if (empty($backup_config->backup_sche_active)) {
        notify(get_string('scheduledbackupsinactive'));
    }

/// Get needed strings
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
    $strnext = get_string("backupnext");

/// Decide when to show last execution logs or detailed logs
/// Lastlog view
    if (!$courseid) {
        print_heading($backuploglaststatus);
        print_simple_box_start('center');
    /// Now, get every record from backup_courses
        $courses = get_records("backup_courses");

        if (!$courses) {
            notify(get_string('nologsfound'));
        } else {
            echo "<table border=\"0\" align=\"center\" cellpadding=\"3\" cellspacing=\"3\">";
            //Print table header
            echo "<tr>";
            echo "<td nowrap=\"nowrap\" align=\"center\"><font size=\"3\">$strcourse</font></td>";
            echo "<td nowrap=\"nowrap\" align=\"center\" colspan=\"3\"><font size=\"3\">$strtimetaken</font></td>";
            echo "<td nowrap=\"nowrap\" align=\"center\"><font size=\"3\">$strstatus</font></td>";
            echo "<td nowrap=\"nowrap\" align=\"center\"><font size=\"3\">$strnext</font></td></tr>";
            foreach ($courses as $course) {
            /// Get the course shortname
                $coursename = get_field ("course","fullname","id",$course->courseid);
                if ($coursename) {
                    echo "<tr>";
                    echo "<td nowrap=\"nowrap\"><font size=\"2\"><a href=\"index.php?courseid=$course->courseid\">".$coursename."</a></font></td>";
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
/// Detailed View !!
    } else {
        print_heading($backuplogdetailed);

        $coursename = get_field("course","fullname","id","$courseid");
        print_heading("$strcourse: $coursename");

        print_simple_box_start('center');

    /// First, me get all the distinct backups for that course in backup_log
        $executions = get_records_sql("SELECT DISTINCT laststarttime,laststarttime
                                       FROM {$CFG->prefix}backup_log
                                       WHERE courseid = '$courseid'
                                       ORDER BY laststarttime DESC");

    /// Iterate over backup executions
        if (!$executions) {
            notify(get_string('nologsfound'));
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
