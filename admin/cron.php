<?PHP // $Id$

/// This script looks through all the module directories for cron.php files
/// and runs them.  These files can contain cleanup functions, email functions
/// or anything that needs to be run on a regular basis.
///
/// This file is best run from cron on the host system (ie outside PHP).
/// The script can either be invoked via the web server or via a standalone
/// version of PHP compiled for CGI.
///
/// eg   wget -q -O /dev/null 'http://moodle.somewhere.edu/admin/cron.php'
/// or   php /web/moodle/admin/cron.php 

    $FULLME = "cron";

    $starttime = microtime();
    
/// The current directory in PHP version 4.3.0 and above isn't necessarily the
/// directory of the script when run from the command line. The require_once()
/// would fail, so we'll have to chdir()

    if (!isset($_SERVER['REMOTE_ADDR']) && isset($_SERVER['argv'][0])) {
        chdir(dirname($_SERVER['argv'][0]));
    }

    require_once("../config.php");

    if (!$alreadyadmin = isadmin()) {
        unset($_SESSION['USER']);
        unset($USER);
        unset($_SESSION['SESSION']);
        unset($SESSION);
        $USER = get_admin();      /// Temporarily, to provide environment for this script
    }

    echo "<pre>\n";

    $timenow  = time();
    echo "Server Time: ".date('r',$timenow)."\n\n";

/// Run all cron jobs for each module

    if ($mods = get_records_select("modules", "cron > 0 AND (($timenow - lastcron) > cron)")) {
        foreach ($mods as $mod) {
            $libfile = "$CFG->dirroot/mod/$mod->name/lib.php";
            if (file_exists($libfile)) {
                include_once($libfile);
                $cron_function = $mod->name."_cron";
                if (function_exists($cron_function)) {
                    if ($cron_function()) {
                        if (! set_field("modules", "lastcron", $timenow, "id", $mod->id)) {
                            echo "Error: could not update timestamp for $mod->fullname\n";
                        }
                    }
                }
            }
        }
    }

/// Run all core cron jobs, but not every time since they aren't too important.
/// These don't have a timer to reduce load, so we'll use a random number 
/// to randomly choose the percentage of times we should run these jobs.

    srand ((double) microtime() * 10000000);
    $random100 = rand(0,100);

    if ($random100 < 20) {     // Approximately 20% of the time.
        echo "Running clean-up tasks...\n";

        /// Unenrol users who haven't logged in for $CFG->longtimenosee

        if ($CFG->longtimenosee) { // value in days
            $longtime = $timenow - ($CFG->longtimenosee * 3600 * 24);
            if ($students = get_users_longtimenosee($longtime)) {
                foreach ($students as $student) {
                    if (unenrol_student($student->userid, $student->course)) {
                        echo "Deleted student enrolment for user $student->userid from course $student->course\n";
                    }
                }
            }
        }
    
    
        /// Delete users who haven't confirmed within required period

        $oneweek = $timenow - ($CFG->deleteunconfirmed * 3600);
        if ($users = get_users_unconfirmed($oneweek)) {
            foreach ($users as $user) {
                if (delete_records("user", "id", $user->id)) {
                    echo "Deleted unconfirmed user for ".fullname($user, true)." ($user->id)\n";
                }
            }
        }
    
    
        /// Delete duplicate enrolments (don't know what causes these yet - expired sessions?)
    
        if ($users = get_records_select("user_students", "userid > 0 GROUP BY course, userid ".
                                        "HAVING count(*) > 1", "", "*,count(*)")) {
            foreach ($users as $user) {
               delete_records_select("user_students", "userid = '$user->userid' ".
                                     "AND course = '$user->course' AND id <> '$user->id'");
            }
        }
    
    
        /// Delete old logs to save space (this might need a timer to slow it down...)
    
        if (!empty($CFG->loglifetime)) {  // value in days
            $loglifetime = $timenow - ($CFG->loglifetime * 3600 * 24);
            delete_records_select("log", "time < '$loglifetime'");
        }

        /// Delete old cached texts

        if (!empty($CFG->cachetext)) {   // Defined in config.php
            $cachelifetime = time() - $CFG->cachetext;
            delete_records_select("cache_text", "timemodified < '$cachelifetime'");
        }
    }

    if (file_exists("$CFG->dataroot/cronextra.php")) {
        include("$CFG->dataroot/cronextra.php");
    }

    if (!isset($CFG->disablescheduledbackups)) {   // Defined in config.php
        //Execute backup's cron
        //Perhaps a long time and memory could help in large sites
        @set_time_limit(0);
        ini_set("memory_limit","56M");
        if (file_exists("$CFG->dirroot/backup/backup_scheduled.php") and
            file_exists("$CFG->dirroot/backup/backuplib.php") and
            file_exists("$CFG->dirroot/backup/lib.php") and
            file_exists("$CFG->libdir/blocklib.php")) {
            include_once("$CFG->dirroot/backup/backup_scheduled.php");
            include_once("$CFG->dirroot/backup/backuplib.php");
            include_once("$CFG->dirroot/backup/lib.php");
            require_once ("$CFG->libdir/blocklib.php");
            echo "Running backups if required...\n";
            flush();
    
            if (! schedule_backup_cron()) {
                echo "Something went wrong while performing backup tasks!!!\n";
            } else {
                echo "Backup tasks finished\n";
            }
        }
    }

    if (!empty($CFG->enablerssfeeds)) {  //Defined in admin/variables page
        if (file_exists("$CFG->dirroot/rss/rsslib.php")) {
            include_once("$CFG->dirroot/rss/rsslib.php");
            echo "Running rssfeeds if required...\n";
            flush();

            if ( ! cron_rss_feeds()) {
                echo "Something went wrong while generating rssfeeds!!!\n";
            } else {
                echo "Rssfeeds finished\n";
            }
        }
    }

    //Unset session variables and destroy it
    @session_unset();
    @session_destroy();

    echo "Cron script completed correctly\n";

    $difftime = microtime_diff($starttime, microtime());
    echo "Execution took ".$difftime." seconds\n"; 

?>
