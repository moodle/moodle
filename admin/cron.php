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

    $starttime = microtime();

/// The following is a hack necessary to allow this script to work well 
/// from the command line.

    define('FULLME', 'cron');
    
/// The current directory in PHP version 4.3.0 and above isn't necessarily the
/// directory of the script when run from the command line. The require_once()
/// would fail, so we'll have to chdir()

    if (!isset($_SERVER['REMOTE_ADDR']) && isset($_SERVER['argv'][0])) {
        chdir(dirname($_SERVER['argv'][0]));
    }

    require_once("../config.php");
    require_once($CFG->dirroot.'/lib/adminlib.php');

    if (!$alreadyadmin = isadmin()) {
        unset($_SESSION['USER']);
        unset($USER);
        unset($_SESSION['SESSION']);
        unset($SESSION);
        $USER = get_admin();      /// Temporarily, to provide environment for this script
    }

    //unset test cookie, user must login again anyway
    setcookie('MoodleSessionTest'.$CFG->sessioncookie, '', time() - 3600, '/');

/// Start output log

    $timenow  = time();

    mtrace("<pre>");
    mtrace("Server Time: ".date('r',$timenow)."\n\n");

/// Run all cron jobs for each module

    mtrace("Starting activity modules");
    if ($mods = get_records_select("modules", "cron > 0 AND (($timenow - lastcron) > cron)")) {
        foreach ($mods as $mod) {
            $libfile = "$CFG->dirroot/mod/$mod->name/lib.php";
            if (file_exists($libfile)) {
                include_once($libfile);
                $cron_function = $mod->name."_cron";
                if (function_exists($cron_function)) {
                    mtrace("Processing module function $cron_function ...", '');
                    if ($cron_function()) {
                        if (! set_field("modules", "lastcron", $timenow, "id", $mod->id)) {
                            mtrace("Error: could not update timestamp for $mod->fullname");
                        }
                    }
                    mtrace("done.");
                }
            }
        }
    }
    mtrace("Finished activity modules");

    if (!empty($CFG->langcache)) {
        mtrace('Updating languages cache');
        get_list_of_languages();
    }


/// Run all core cron jobs, but not every time since they aren't too important.
/// These don't have a timer to reduce load, so we'll use a random number 
/// to randomly choose the percentage of times we should run these jobs.

    srand ((double) microtime() * 10000000);
    $random100 = rand(0,100);

    if ($random100 < 20) {     // Approximately 20% of the time.
        mtrace("Running clean-up tasks...");

        /// Unenrol users who haven't logged in for $CFG->longtimenosee

        if ($CFG->longtimenosee) { // value in days
            $longtime = $timenow - ($CFG->longtimenosee * 3600 * 24);
            if ($students = get_users_longtimenosee($longtime)) {
                foreach ($students as $student) {
                    if (unenrol_student($student->userid, $student->course)) {
                        mtrace("Deleted student enrolment for user $student->userid from course $student->course");
                    }
                }
            }
        }
    
    
        /// Delete users who haven't confirmed within required period

        $oneweek = $timenow - ($CFG->deleteunconfirmed * 3600);
        if ($users = get_users_unconfirmed($oneweek)) {
            foreach ($users as $user) {
                if (delete_records("user", "id", $user->id)) {
                    mtrace("Deleted unconfirmed user for ".fullname($user, true)." ($user->id)");
                }
            }
        }
        flush();
    
        /// Delete old logs to save space (this might need a timer to slow it down...)
    
        if (!empty($CFG->loglifetime)) {  // value in days
            $loglifetime = $timenow - ($CFG->loglifetime * 3600 * 24);
            delete_records_select("log", "time < '$loglifetime'");
        }
        flush();

        /// Delete old cached texts

        if (!empty($CFG->cachetext)) {   // Defined in config.php
            $cachelifetime = time() - $CFG->cachetext;
            delete_records_select("cache_text", "timemodified < '$cachelifetime'");
        }
        flush();

        if (!empty($CFG->notifyloginfailures)) {
            notify_login_failures();
        }
        flush();

        sync_metacourses();

    } // End of occasional clean-up tasks


    if (!isset($CFG->disablescheduledbackups)) {   // Defined in config.php
        //Execute backup's cron
        //Perhaps a long time and memory could help in large sites
        @set_time_limit(0);
        @raise_memory_limit("128M");
        if (file_exists("$CFG->dirroot/backup/backup_scheduled.php") and
            file_exists("$CFG->dirroot/backup/backuplib.php") and
            file_exists("$CFG->dirroot/backup/lib.php") and
            file_exists("$CFG->libdir/blocklib.php")) {
            include_once("$CFG->dirroot/backup/backup_scheduled.php");
            include_once("$CFG->dirroot/backup/backuplib.php");
            include_once("$CFG->dirroot/backup/lib.php");
            require_once ("$CFG->libdir/blocklib.php");
            mtrace("Running backups if required...");
    
            if (! schedule_backup_cron()) {
                mtrace("ERORR: Something went wrong while performing backup tasks!!!");
            } else {
                mtrace("Backup tasks finished.");
            }
        }
    }

    if (!empty($CFG->enablerssfeeds)) {  //Defined in admin/variables page
        include_once("$CFG->libdir/rsslib.php");
        mtrace("Running rssfeeds if required...");

        if ( ! cron_rss_feeds()) {
            mtrace("Something went wrong while generating rssfeeds!!!");
        } else {
            mtrace("Rssfeeds finished");
        }
    }

/// Run the enrolment cron, if any
    require_once("$CFG->dirroot/enrol/$CFG->enrol/enrol.php");
    $enrol = new enrolment_plugin();
    $enrol->cron();
    if (!empty($enrol->log)) {
        mtrace($enrol->log);
    }

    if (!empty($CFG->enablestats)) {
        if (!get_field_sql('SELECT id FROM '.$CFG->prefix.'stats_daily LIMIT 1')) {
            // first run, set another lock. we'll check for this in subsequent runs to set the timeout to later for the normal lock.
            set_cron_lock('statsfirstrunlock',true,60*60*20,true);
            $firsttime = true;
        }
        $time = 60*60*2;
        $clobber = true;
        if ($config = get_record('config','name','statsfirstrunlock')) {
            if (!empty($config->value)) {
                $time = 60*60*20;
                $clobber = false;
            }
        }
        if (set_cron_lock('statsrunning',true,$time, $clobber)) {
            require_once($CFG->dirroot.'/lib/statslib.php');
            $return = stats_cron_daily();
            if ($return == STATS_RUN_COMPLETE) {
                $return = stats_cron_weekly();
            }
            if ($return == STATS_RUN_COMPLETE) {
                $return = stats_cron_monthly();
            }
            stats_clean_old();
            set_cron_lock('statsrunning',false);
            if (!empty($firsttime)) {
                set_cron_lock('statsfirstrunlock',false);
            }
        }
    }

    //Unset session variables and destroy it
    @session_unset();
    @session_destroy();

    mtrace("Cron script completed correctly");

    $difftime = microtime_diff($starttime, microtime());
    mtrace("Execution took ".$difftime." seconds"); 

?>
