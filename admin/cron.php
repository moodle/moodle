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
    set_time_limit(0);
    $starttime = microtime();

/// The following is a hack necessary to allow this script to work well 
/// from the command line.

    define('FULLME', 'cron');


/// Do not set moodle cookie because we do not need it here, it is better to emulate session
    $nomoodlecookie = true;

/// The current directory in PHP version 4.3.0 and above isn't necessarily the
/// directory of the script when run from the command line. The require_once()
/// would fail, so we'll have to chdir()

    if (!isset($_SERVER['REMOTE_ADDR']) && isset($_SERVER['argv'][0])) {
        chdir(dirname($_SERVER['argv'][0]));
    }

    require_once(dirname(__FILE__) . '/../config.php');
    require_once($CFG->libdir.'/adminlib.php');

/// extra safety
    @session_write_close();

/// check if execution allowed
    if (isset($_SERVER['REMOTE_ADDR'])) { // if the script is accessed via the web.
        if (!empty($CFG->cronclionly)) { 
            // This script can only be run via the cli.
            print_error('cronerrorclionly', 'admin');
            exit;
        }
        // This script is being called via the web, so check the password if there is one.
        if (!empty($CFG->cronremotepassword)) {
            $pass = optional_param('password', '', PARAM_RAW);
            if($pass != $CFG->cronremotepassword) {
                // wrong password.
                print_error('cronerrorpassword', 'admin'); 
                exit;
            }
        }
    }


/// emulate normal session
    $SESSION = new object();
    $USER = get_admin();      /// Temporarily, to provide environment for this script

/// ignore admins timezone, language and locale - use site deafult instead!
    $USER->timezone = $CFG->timezone;
    $USER->lang = '';
    $USER->theme = '';
    course_setup(SITEID);

/// send mime type and encoding
    if (check_browser_version('MSIE')) {
        //ugly IE hack to work around downloading instead of viewing
        @header('Content-Type: text/html; charset=utf-8');
        echo "<xmp>"; //<pre> is not good enough for us here
    } else {
        //send proper plaintext header
        @header('Content-Type: text/plain; charset=utf-8');
    }

/// no more headers and buffers
    while(@ob_end_flush());

/// increase memory limit (PHP 5.2 does different calculation, we need more memory now)
    @raise_memory_limit('128M');

/// Start output log

    $timenow  = time();

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

    mtrace("Starting blocks");
    if ($blocks = get_records_select("block", "cron > 0 AND (($timenow - lastcron) > cron)")) {
        // we will need the base class.
        require_once($CFG->dirroot.'/blocks/moodleblock.class.php');
        foreach ($blocks as $block) {
            $blockfile = $CFG->dirroot.'/blocks/'.$block->name.'/block_'.$block->name.'.php';
            if (file_exists($blockfile)) {
                require_once($blockfile);
                $classname = 'block_'.$block->name;
                $blockobj = new $classname; 
                if (method_exists($blockobj,'cron')) {
                    mtrace("Processing cron function for ".$block->name.'....','');
                    if ($blockobj->cron()) {
                        if (!set_field('block','lastcron',$timenow,'id',$block->id)) {
                            mtrace('Error: could not update timestamp for '.$block->name);
                        }
                    }
                    mtrace('done.');
                }
            }

        }
    }
    mtrace('Finished blocks');

    if (!empty($CFG->langcache)) {
        mtrace('Updating languages cache');
        get_list_of_languages(true);
    }

    mtrace('Removing expired enrolments ...', '');     // See MDL-8785
    $timenow = time();
    if ($oldenrolments = get_records_select('role_assignments', "timeend > 0 AND timeend < '$timenow'")) {
        mtrace(count($oldenrolments).' to delete');
        foreach ($oldenrolments as $oldenrolment) {
            if (role_unassign($oldenrolment->roleid, $oldenrolment->userid, 0, $oldenrolment->contextid)) {
                mtrace("Deleted expired role assignment $oldenrolment->roleid for user $oldenrolment->userid from context $oldenrolment->contextid");
            }
        }
        mtrace('Done');
    } else {
        mtrace('none found');
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
            if ($assigns = get_users_longtimenosee($longtime)) {
                foreach ($assigns as $assign) {
                    if ($context = get_context_instance(CONTEXT_COURSE, $assign->courseid)) {
                        if (role_unassign(0, $assign->id, 0, $context->id)) {
                            mtrace("Deleted assignment for user $assign->id from course $assign->courseid");
                        }
                    }
                }
            }
        }
    
    
        /// Delete users who haven't confirmed within required period

        if (!empty($CFG->deleteunconfirmed)) {
            $oneweek = $timenow - ($CFG->deleteunconfirmed * 3600);
            if ($users = get_users_unconfirmed($oneweek)) {
                foreach ($users as $user) {
                    if (delete_records('user', 'id', $user->id)) {
                        mtrace("Deleted unconfirmed user for ".fullname($user, true)." ($user->id)");
                    }
                }
            }
        }
        flush();



        /// Delete users who haven't completed profile within required period

        if (!empty($CFG->deleteunconfirmed)) {
            $oneweek = $timenow - ($CFG->deleteunconfirmed * 3600);
            if ($users = get_users_not_fully_set_up($oneweek)) {
                foreach ($users as $user) {
                    if (delete_records('user', 'id', $user->id)) {
                        mtrace("Deleted not fully setup user $user->username ($user->id)");
                    }
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
            $cachelifetime = time() - $CFG->cachetext - 60;  // Add an extra minute to allow for really heavy sites
            delete_records_select('cache_text', "timemodified < '$cachelifetime'");
        }
        flush();

        if (!empty($CFG->notifyloginfailures)) {
            notify_login_failures();
        }
        flush();

        sync_metacourses();

        //
        // generate new password emails for users 
        //
        mtrace('checking for create_password');
        if (count_records('user_preferences', 'name', 'create_password', 'value', '1')) {
            mtrace('creating passwords for new users');
            $newusers = get_records_sql("SELECT  u.id as id, u.email, u.firstname, 
                                                u.lastname, u.username,
                                                p.id as prefid 
                                        FROM {$CFG->prefix}user u 
                                             JOIN {$CFG->prefix}user_preferences p ON u.id=p.userid
                                        WHERE p.name='create_password' AND p.value=1 AND u.email !='' ");

            foreach ($newusers as $newuserid => $newuser) {
                $newuser->emailstop = 0; // send email regardless
                // email user                               
                if (setnew_password_and_mail($newuser)) {
                    // remove user pref
                    delete_records('user_preferences', 'id', $newuser->prefid);
                } else {
                    trigger_error("Could not create and mail new user password!");
                }
            }
        }
        
        // attemps to grab grades from third party/non-stard mods, or mods with no event
        // implemented for 1.9 and above.
        mtrace("Grabbing grades from modules if required...");
        include_once($CFG->dirroot.'/lib/gradelib.php');
        grades_grab_grades();

    } // End of occasional clean-up tasks


    if (empty($CFG->disablescheduledbackups)) {   // Defined in config.php
        //Execute backup's cron
        //Perhaps a long time and memory could help in large sites
        @set_time_limit(0);
        @raise_memory_limit("192M");
        if (function_exists('apache_child_terminate')) {
            // if we are running from Apache, give httpd a hint that 
            // it can recycle the process after it's done. Apache's 
            // memory management is truly awful but we can help it.
            @apache_child_terminate();
        }
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
                mtrace("ERROR: Something went wrong while performing backup tasks!!!");
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
    if (!($plugins = explode(',', $CFG->enrol_plugins_enabled))) {
        $plugins = array($CFG->enrol);
    }
    require_once($CFG->dirroot .'/enrol/enrol.class.php');
    foreach ($plugins as $p) {
        $enrol = enrolment_factory::factory($p);
        if (method_exists($enrol, 'cron')) {
            $enrol->cron();
        }
        if (!empty($enrol->log)) {
            mtrace($enrol->log);
        }
        unset($enrol);
    }

/// Run the auth cron, if any
    $auths = get_enabled_auth_plugins();

    mtrace("Running auth crons if required...");
    foreach ($auths as $auth) {
        $authplugin = get_auth_plugin($auth);
        if (method_exists($authplugin, 'cron')) {
            mtrace("Running cron for auth/$auth...");
            $authplugin->cron();
            if (!empty($authplugin->log)) {
                mtrace($authplugin->log);
            }
        }
        unset($authplugin);
    }

    if (!empty($CFG->enablestats) and empty($CFG->disablestatsprocessing)) {

        // check we're not before our runtime
        $timetocheck = strtotime("$CFG->statsruntimestarthour:$CFG->statsruntimestartminute today");

        if (time() > $timetocheck) {
            $time = 60*60*20; // set it to 20 here for first run... (overridden by $CFG)
            $clobber = true;
            if (!empty($CFG->statsmaxruntime)) {
                $time = $CFG->statsmaxruntime+(60*30); // add on half an hour just to make sure (it could take that long to break out of the loop)
            }
            if (!get_field_sql('SELECT id FROM '.$CFG->prefix.'stats_daily')) {
                // first run, set another lock. we'll check for this in subsequent runs to set the timeout to later for the normal lock.
                set_cron_lock('statsfirstrunlock',true,$time,true);
                $firsttime = true;
            }
            $time = 60*60*2; // this time set to 2.. (overridden by $CFG)
            if (!empty($CFG->statsmaxruntime)) {
                $time = $CFG->statsmaxruntime+(60*30); // add on half an hour to make sure (it could take that long to break out of the loop)
            }
            if ($config = get_record('config','name','statsfirstrunlock')) {
                if (!empty($config->value)) {
                    $clobber = false; // if we're on the first run, just don't clobber it.
                }
            }
            if (set_cron_lock('statsrunning',true,$time, $clobber)) {
                require_once($CFG->dirroot.'/lib/statslib.php');
                $return = stats_cron_daily();
                if (stats_check_runtime() && $return == STATS_RUN_COMPLETE) {
                    stats_cron_weekly();
                }
                if (stats_check_runtime() && $return == STATS_RUN_COMPLETE) {
                    $return = $return && stats_cron_monthly();
                }
                if (stats_check_runtime() && $return == STATS_RUN_COMPLETE) {
                    stats_clean_old();
                }
                set_cron_lock('statsrunning',false);
                if (!empty($firsttime)) {
                    set_cron_lock('statsfirstrunlock',false);
                }
            }
        }
    }

    //Unset session variables and destroy it
    @session_unset();
    @session_destroy();

    mtrace("Cron script completed correctly");

    $difftime = microtime_diff($starttime, microtime());
    mtrace("Execution took ".$difftime." seconds"); 

/// finishe the IE hack
    if (check_browser_version('MSIE')) {
        echo "</xmp>";
    }

?>
