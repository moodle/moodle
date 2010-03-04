<?php // $Id$

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
    require_once($CFG->libdir.'/gradelib.php');

/// Extra debugging (set in config.php)
    if (!empty($CFG->showcronsql)) {
        $db->debug = true;
    }
    if (!empty($CFG->showcrondebugging)) {
        $CFG->debug = DEBUG_DEVELOPER;
        $CFG->debugdisplay = true;
    }

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
    get_mailer('buffer');
    if ($mods = get_records_select("modules", "cron > 0 AND (($timenow - lastcron) > cron) AND visible = 1 ")) {
        foreach ($mods as $mod) {
            $libfile = "$CFG->dirroot/mod/$mod->name/lib.php";
            if (file_exists($libfile)) {
                include_once($libfile);
                $cron_function = $mod->name."_cron";
                if (function_exists($cron_function)) {
                    mtrace("Processing module function $cron_function ...", '');
                    $pre_dbqueries = null;
                    if (!empty($PERF->dbqueries)) {
                        $pre_dbqueries = $PERF->dbqueries;
                        $pre_time      = microtime(1);
                    }
                    if ($cron_function()) {
                        if (! set_field("modules", "lastcron", $timenow, "id", $mod->id)) {
                            mtrace("Error: could not update timestamp for $mod->fullname");
                        }
                    }
                    if (isset($pre_dbqueries)) {
                        mtrace("... used " . ($PERF->dbqueries - $pre_dbqueries) . " dbqueries");
                        mtrace("... used " . (microtime(1) - $pre_time) . " seconds");
                    }
                /// Reset possible changes by modules to time_limit. MDL-11597
                    @set_time_limit(0);
                    mtrace("done.");
                }
            }
        }
    }
    get_mailer('close');
    mtrace("Finished activity modules");

    mtrace("Starting blocks");
    if ($blocks = get_records_select("block", "cron > 0 AND (($timenow - lastcron) > cron) AND visible = 1")) {
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
                /// Reset possible changes by blocks to time_limit. MDL-11597
                    @set_time_limit(0);
                    mtrace('done.');
                }
            }

        }
    }
    mtrace('Finished blocks');

    mtrace('Starting admin reports');
    // Admin reports do not have a database table that lists them. Instead a
    // report includes cron.php with function report_reportname_cron() if it wishes
    // to be cronned. It is up to cron.php to handle e.g. if it only needs to
    // actually do anything occasionally.
    $reports = get_list_of_plugins($CFG->admin.'/report');
    foreach($reports as $report) {
        $cronfile = $CFG->dirroot.'/'.$CFG->admin.'/report/'.$report.'/cron.php';
        if (file_exists($cronfile)) {
            require_once($cronfile);
            $cronfunction = 'report_'.$report.'_cron';
            mtrace('Processing cron function for '.$report.'...', '');
            $pre_dbqueries = null;
            if (!empty($PERF->dbqueries)) {
                $pre_dbqueries = $PERF->dbqueries;
                $pre_time      = microtime(true);
            }
            $cronfunction();
            if (isset($pre_dbqueries)) {
                mtrace("... used " . ($PERF->dbqueries - $pre_dbqueries) . " dbqueries");
                mtrace("... used " . round(microtime(true) - $pre_time, 2) . " seconds");
            }
            mtrace('done.');
        }
    }
    mtrace('Finished admin reports');

    if (!empty($CFG->langcache)) {
        mtrace('Updating languages cache');
        get_list_of_languages(true);
    }

    mtrace('Removing expired enrolments ...', '');     // See MDL-8785
    $timenow = time();
    $somefound = false;
    // The preferred way saves memory, dmllib.php
    // find courses where limited enrolment is enabled
    global $CFG;
    $rs_enrol = get_recordset_sql("SELECT ra.roleid, ra.userid, ra.contextid
        FROM {$CFG->prefix}course c
        INNER JOIN {$CFG->prefix}context cx ON cx.instanceid = c.id
        INNER JOIN {$CFG->prefix}role_assignments ra ON ra.contextid = cx.id
        WHERE cx.contextlevel = '".CONTEXT_COURSE."'
        AND ra.timeend > 0
        AND ra.timeend < '$timenow'
        AND c.enrolperiod > 0
        ");
    while ($oldenrolment = rs_fetch_next_record($rs_enrol)) {
        role_unassign($oldenrolment->roleid, $oldenrolment->userid, 0, $oldenrolment->contextid);
        $somefound = true;
    }
    rs_close($rs_enrol);
    if($somefound) {
        mtrace('Done');
    } else {
        mtrace('none found');
    }


    mtrace('Starting main gradebook job ...');
    grade_cron();
    mtrace('done.');

    mtrace('Starting processing the event queue...');
    events_cron();
    mtrace('done.');

/// Run all core cron jobs, but not every time since they aren't too important.
/// These don't have a timer to reduce load, so we'll use a random number 
/// to randomly choose the percentage of times we should run these jobs.

    srand ((double) microtime() * 10000000);
    $random100 = rand(0,100);

    if ($random100 < 20) {     // Approximately 20% of the time.
        mtrace("Running clean-up tasks...");

        /// Unenrol users who haven't logged in for $CFG->longtimenosee

        if ($CFG->longtimenosee) { // value in days
            $cuttime = $timenow - ($CFG->longtimenosee * 3600 * 24);
            $rs = get_recordset_sql ("SELECT id, userid, courseid
                                        FROM {$CFG->prefix}user_lastaccess
                                       WHERE courseid != ".SITEID."
                                         AND timeaccess < $cuttime ");
            while ($assign = rs_fetch_next_record($rs)) {
                if ($context = get_context_instance(CONTEXT_COURSE, $assign->courseid)) {
                    if (role_unassign(0, $assign->userid, 0, $context->id)) {
                        mtrace("removing user $assign->userid from course $assign->courseid as they have not accessed the course for over $CFG->longtimenosee days");
                    }
                }
            }
            rs_close($rs);
        /// Execute the same query again, looking for remaining records and deleting them
        /// if the user hasn't moodle/course:view in the CONTEXT_COURSE context (orphan records)
            $rs = get_recordset_sql ("SELECT id, userid, courseid
                                        FROM {$CFG->prefix}user_lastaccess
                                       WHERE courseid != ".SITEID."
                                         AND timeaccess < $cuttime ");
            while ($assign = rs_fetch_next_record($rs)) {
                if ($context = get_context_instance(CONTEXT_COURSE, $assign->courseid)) {
                    if (!has_capability('moodle/course:view', $context, $assign->userid)) {
                        delete_records('user_lastaccess', 'userid', $assign->userid, 'courseid', $assign->courseid);
                        mtrace("Deleted orphan user_lastaccess for user $assign->userid from course $assign->courseid");
                    }
                }
            }
            rs_close($rs);
        }
        flush();


        /// Delete users who haven't confirmed within required period

        if (!empty($CFG->deleteunconfirmed)) {
            $cuttime = $timenow - ($CFG->deleteunconfirmed * 3600);
            $rs = get_recordset_sql ("SELECT id, firstname, lastname
                                        FROM {$CFG->prefix}user
                                       WHERE confirmed = 0
                                         AND firstaccess > 0
                                         AND firstaccess < $cuttime");
            while ($user = rs_fetch_next_record($rs)) {
                if (delete_records('user', 'id', $user->id)) {
                    mtrace("Deleted unconfirmed user for ".fullname($user, true)." ($user->id)");
                }
            }
            rs_close($rs);
        }
        flush();


        /// Delete users who haven't completed profile within required period

        if (!empty($CFG->deleteincompleteusers)) {
            $cuttime = $timenow - ($CFG->deleteincompleteusers * 3600);
            $rs = get_recordset_sql ("SELECT id, username
                                        FROM {$CFG->prefix}user
                                       WHERE confirmed = 1
                                         AND lastaccess > 0
                                         AND lastaccess < $cuttime
                                         AND deleted = 0
                                         AND (lastname = '' OR firstname = '' OR email = '')");
            while ($user = rs_fetch_next_record($rs)) {
                if (delete_user($user)) {
                    mtrace("Deleted not fully setup user $user->username ($user->id)");
                }
            }
            rs_close($rs);
        }
        flush();


        /// Delete old logs to save space (this might need a timer to slow it down...)

        if (!empty($CFG->loglifetime)) {  // value in days
            $loglifetime = $timenow - ($CFG->loglifetime * 3600 * 24);
            if (delete_records_select("log", "time < '$loglifetime'")) {
                mtrace("Deleted old log records");
            }
        }
        flush();


        /// Delete old cached texts

        if (!empty($CFG->cachetext)) {   // Defined in config.php
            $cachelifetime = time() - $CFG->cachetext - 60;  // Add an extra minute to allow for really heavy sites
            if (delete_records_select('cache_text', "timemodified < '$cachelifetime'")) {
                mtrace("Deleted old cache_text records");
            }
        }
        flush();

        if (!empty($CFG->notifyloginfailures)) {
            notify_login_failures();
            mtrace('Notified login failured');
        }
        flush();

        sync_metacourses();
        mtrace('Synchronised metacourses');

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
                                        WHERE p.name='create_password' AND p.value='1' AND u.email !='' ");

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
        
        if (!empty($CFG->usetags)) {
            require_once($CFG->dirroot.'/tag/lib.php');
            tag_cron();
            mtrace ('Executed tag cron');
        }
        
        // Accesslib stuff
        cleanup_contexts();
        mtrace ('Cleaned up contexts');
        gc_cache_flags();
        mtrace ('Cleaned cache flags');
        // If you suspect that the context paths are somehow corrupt
        // replace the line below with: build_context_path(true); 
        build_context_path();
        mtrace ('Built context paths');

        mtrace("Finished clean-up tasks...");

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

/// Run the auth cron, if any
/// before enrolments because it might add users that will be needed in enrol plugins
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

    if (!empty($CFG->enablestats) and empty($CFG->disablestatsprocessing)) {
        require_once($CFG->dirroot.'/lib/statslib.php');
        // check we're not before our runtime
        $timetocheck = stats_get_base_daily() + $CFG->statsruntimestarthour*60*60 + $CFG->statsruntimestartminute*60;

        if (time() > $timetocheck) {
            // process configured number of days as max (defaulting to 31)
            $maxdays = empty($CFG->statsruntimedays) ? 31 : abs($CFG->statsruntimedays);
            if (stats_cron_daily($maxdays)) {
                if (stats_cron_weekly()) {
                    if (stats_cron_monthly()) {
                        stats_clean_old();
                    }
                }
            }
            @set_time_limit(0);
        } else {
            mtrace('Next stats run after:'. userdate($timetocheck));
        }
    }

    // run gradebook import/export/report cron
    if ($gradeimports = get_list_of_plugins('grade/import')) {
        foreach ($gradeimports as $gradeimport) {
            if (file_exists($CFG->dirroot.'/grade/import/'.$gradeimport.'/lib.php')) {
                require_once($CFG->dirroot.'/grade/import/'.$gradeimport.'/lib.php');
                $cron_function = 'grade_import_'.$gradeimport.'_cron';
                if (function_exists($cron_function)) {
                    mtrace("Processing gradebook import function $cron_function ...", '');
                    $cron_function();
                }
            }
        }
    }

    if ($gradeexports = get_list_of_plugins('grade/export')) {
        foreach ($gradeexports as $gradeexport) {
            if (file_exists($CFG->dirroot.'/grade/export/'.$gradeexport.'/lib.php')) {
                require_once($CFG->dirroot.'/grade/export/'.$gradeexport.'/lib.php');
                $cron_function = 'grade_export_'.$gradeexport.'_cron';
                if (function_exists($cron_function)) {
                    mtrace("Processing gradebook export function $cron_function ...", '');
                    $cron_function();
                }
            }
        }
    }

    if ($gradereports = get_list_of_plugins('grade/report')) {
        foreach ($gradereports as $gradereport) {
            if (file_exists($CFG->dirroot.'/grade/report/'.$gradereport.'/lib.php')) {
                require_once($CFG->dirroot.'/grade/report/'.$gradereport.'/lib.php');
                $cron_function = 'grade_report_'.$gradereport.'_cron';
                if (function_exists($cron_function)) {
                    mtrace("Processing gradebook report function $cron_function ...", '');
                    $cron_function();
                }
            }
        }
    }

    // run any customized cronjobs, if any
    // looking for functions in lib/local/cron.php
    if (file_exists($CFG->dirroot.'/local/cron.php')) {
        mtrace('Processing customized cron script ...', '');
        include_once($CFG->dirroot.'/local/cron.php');
        mtrace('done.');
    }


    //Unset session variables and destroy it
    @session_unset();
    @session_destroy();

    mtrace("Cron script completed correctly");

    $difftime = microtime_diff($starttime, microtime());
    mtrace("Execution took ".$difftime." seconds"); 

/// finish the IE hack
    if (check_browser_version('MSIE')) {
        echo "</xmp>";
    }

?>
