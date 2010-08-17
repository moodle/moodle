<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Cron functions.
 *
 * @package    core
 * @subpackage admin
 * @copyright  1999 onwards Martin Dougiamas  http://dougiamas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function cron_run() {
    global $DB, $CFG, $OUTPUT;

    require_once($CFG->libdir.'/adminlib.php');
    require_once($CFG->libdir.'/gradelib.php');

    if (!empty($CFG->showcronsql)) {
        $DB->set_debug(true);
    }
    if (!empty($CFG->showcrondebugging)) {
        $CFG->debug = DEBUG_DEVELOPER;
        $CFG->debugdisplay = true;
    }

    set_time_limit(0);
    $starttime = microtime();

/// increase memory limit (PHP 5.2 does different calculation, we need more memory now)
    @raise_memory_limit('128M');

/// emulate normal session
    cron_setup_user();

/// Start output log

    $timenow  = time();

    mtrace("Server Time: ".date('r',$timenow)."\n\n");


/// Session gc

    mtrace("Cleaning up stale sessions");
    session_gc();

/// Run all cron jobs for each module

    mtrace("Starting activity modules");
    get_mailer('buffer');
    if ($mods = $DB->get_records_select("modules", "cron > 0 AND ((? - lastcron) > cron) AND visible = 1", array($timenow))) {
        foreach ($mods as $mod) {
            $libfile = "$CFG->dirroot/mod/$mod->name/lib.php";
            if (file_exists($libfile)) {
                include_once($libfile);
                $cron_function = $mod->name."_cron";
                if (function_exists($cron_function)) {
                    mtrace("Processing module function $cron_function ...", '');
                    $pre_dbqueries = null;
                    $pre_dbqueries = $DB->perf_get_queries();
                    $pre_time      = microtime(1);
                    if ($cron_function()) {
                        if (!$DB->set_field("modules", "lastcron", $timenow, array("id"=>$mod->id))) {
                            mtrace("Error: could not update timestamp for $mod->fullname");
                        }
                    }
                    if (isset($pre_dbqueries)) {
                        mtrace("... used " . ($DB->perf_get_queries() - $pre_dbqueries) . " dbqueries");
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
    if ($blocks = $DB->get_records_select("block", "cron > 0 AND ((? - lastcron) > cron) AND visible = 1", array($timenow))) {
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
                        if (!$DB->set_field('block', 'lastcron', $timenow, array('id'=>$block->id))) {
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

    mtrace("Starting quiz reports");
    if ($reports = $DB->get_records_select('quiz_report', "cron > 0 AND ((? - lastcron) > cron)", array($timenow))) {
        foreach ($reports as $report) {
            $cronfile = "$CFG->dirroot/mod/quiz/report/$report->name/cron.php";
            if (file_exists($cronfile)) {
                include_once($cronfile);
                $cron_function = 'quiz_report_'.$report->name."_cron";
                if (function_exists($cron_function)) {
                    mtrace("Processing quiz report cron function $cron_function ...", '');
                    $pre_dbqueries = null;
                    $pre_dbqueries = $DB->perf_get_queries();
                    $pre_time      = microtime(1);
                    if ($cron_function()) {
                        if (!$DB->set_field('quiz_report', "lastcron", $timenow, array("id"=>$report->id))) {
                            mtrace("Error: could not update timestamp for $report->name");
                        }
                    }
                    if (isset($pre_dbqueries)) {
                        mtrace("... used " . ($DB->perf_get_queries() - $pre_dbqueries) . " dbqueries");
                        mtrace("... used " . (microtime(1) - $pre_time) . " seconds");
                    }
                    mtrace("done.");
                }
            }
        }
    }
    mtrace("Finished quiz reports");

    mtrace('Starting admin reports');
    // Admin reports do not have a database table that lists them. Instead a
    // report includes cron.php with function report_reportname_cron() if it wishes
    // to be cronned. It is up to cron.php to handle e.g. if it only needs to
    // actually do anything occasionally.
    $reports = get_plugin_list('report');
    foreach($reports as $report => $reportdir) {
        $cronfile = $reportdir.'/cron.php';
        if (file_exists($cronfile)) {
            require_once($cronfile);
            $cronfunction = 'report_'.$report.'_cron';
            mtrace('Processing cron function for '.$report.'...', '');
            $pre_dbqueries = null;
            $pre_dbqueries = $DB->perf_get_queries();
            $pre_time      = microtime(true);
            $cronfunction();
            if (isset($pre_dbqueries)) {
                mtrace("... used " . ($DB->perf_get_queries() - $pre_dbqueries) . " dbqueries");
                mtrace("... used " . round(microtime(true) - $pre_time, 2) . " seconds");
            }
            mtrace('done.');
        }
    }
    mtrace('Finished admin reports');

    mtrace('Starting main gradebook job ...');
    grade_cron();
    mtrace('done.');


    mtrace('Starting processing the event queue...');
    events_cron();
    mtrace('done.');


    if ($CFG->enablecompletion) {
        // Completion cron
        mtrace('Starting the completion cron...');
        require_once($CFG->libdir . '/completion/cron.php');
        completion_cron();
        mtrace('done');
    }


    if ($CFG->enableportfolios) {
        // Portfolio cron
        mtrace('Starting the portfolio cron...');
        require_once($CFG->libdir . '/portfoliolib.php');
        portfolio_cron();
        mtrace('done');
    }

/// Run all core cron jobs, but not every time since they aren't too important.
/// These don't have a timer to reduce load, so we'll use a random number
/// to randomly choose the percentage of times we should run these jobs.

    srand ((double) microtime() * 10000000);
    $random100 = rand(0,100);

    if ($random100 < 20) {     // Approximately 20% of the time.
        mtrace("Running clean-up tasks...");

        /// Delete users who haven't confirmed within required period

        if (!empty($CFG->deleteunconfirmed)) {
            $cuttime = $timenow - ($CFG->deleteunconfirmed * 3600);
            $rs = $DB->get_recordset_sql ("SELECT id, firstname, lastname
                                             FROM {user}
                                            WHERE confirmed = 0 AND firstaccess > 0
                                                  AND firstaccess < ?", array($cuttime));
            foreach ($rs as $user) {
                if ($DB->delete_records('user', array('id'=>$user->id))) {
                    mtrace("Deleted unconfirmed user for ".fullname($user, true)." ($user->id)");
                }
            }
            $rs->close();
        }
        flush();


        /// Delete users who haven't completed profile within required period

        if (!empty($CFG->deleteincompleteusers)) {
            $cuttime = $timenow - ($CFG->deleteincompleteusers * 3600);
            $rs = $DB->get_recordset_sql ("SELECT id, username
                                             FROM {user}
                                            WHERE confirmed = 1 AND lastaccess > 0
                                                  AND lastaccess < ? AND deleted = 0
                                                  AND (lastname = '' OR firstname = '' OR email = '')",
                                          array($cuttime));
            foreach ($rs as $user) {
                if (delete_user($user)) {
                    mtrace("Deleted not fully setup user $user->username ($user->id)");
                }
            }
            $rs->close();
        }
        flush();


        /// Delete old logs to save space (this might need a timer to slow it down...)

        if (!empty($CFG->loglifetime)) {  // value in days
            $loglifetime = $timenow - ($CFG->loglifetime * 3600 * 24);
            if ($DB->delete_records_select("log", "time < ?", array($loglifetime))) {
                mtrace("Deleted old log records");
            }
        }
        flush();


        /// Delete old cached texts

        if (!empty($CFG->cachetext)) {   // Defined in config.php
            $cachelifetime = time() - $CFG->cachetext - 60;  // Add an extra minute to allow for really heavy sites
            if ($DB->delete_records_select('cache_text', "timemodified < ?", array($cachelifetime))) {
                mtrace("Deleted old cache_text records");
            }
        }
        flush();

        if (!empty($CFG->notifyloginfailures)) {
            notify_login_failures();
            mtrace('Notified login failured');
        }
        flush();

        //
        // generate new password emails for users
        //
        mtrace('checking for create_password');
        if ($DB->count_records('user_preferences', array('name'=>'create_password', 'value'=>'1'))) {
            mtrace('creating passwords for new users');
            $newusers = $DB->get_records_sql("SELECT u.id as id, u.email, u.firstname,
                                                     u.lastname, u.username,
                                                     p.id as prefid
                                                FROM {user} u
                                                JOIN {user_preferences} p ON u.id=p.userid
                                               WHERE p.name='create_password' AND p.value='1' AND u.email !='' ");

            foreach ($newusers as $newuserid => $newuser) {
                $newuser->emailstop = 0; // send email regardless
                // email user
                if (setnew_password_and_mail($newuser)) {
                    // remove user pref
                    $DB->delete_records('user_preferences', array('id'=>$newuser->prefid));
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

    // Disabled until implemented. MDL-21432, MDL-22184
    if (1 == 2 && empty($CFG->disablescheduledbackups)) {   // Defined in config.php
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
            mtrace("Running backups if required...");

            if (! schedule_backup_cron()) {
                mtrace("ERROR: Something went wrong while performing backup tasks!!!");
            } else {
                mtrace("Backup tasks finished.");
            }
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

    mtrace("Running enrol crons if required...");
    $enrols = enrol_get_plugins(true);
    foreach($enrols as $ename=>$enrol) {
        // do this for all plugins, disabled plugins might want to cleanup stuff such as roles
        if (!$enrol->is_cron_required()) {
            continue;
        }
        mtrace("Running cron for enrol_$ename...");
        $enrol->cron();
        $enrol->set_config('lastcron', time());
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
    if ($gradeimports = get_plugin_list('gradeimport')) {
        foreach ($gradeimports as $gradeimport => $plugindir) {
            if (file_exists($plugindir.'/lib.php')) {
                require_once($plugindir.'/lib.php');
                $cron_function = 'grade_import_'.$gradeimport.'_cron';
                if (function_exists($cron_function)) {
                    mtrace("Processing gradebook import function $cron_function ...", '');
                    $cron_function();
                }
            }
        }
    }

    if ($gradeexports = get_plugin_list('gradeexport')) {
        foreach ($gradeexports as $gradeexport => $plugindir) {
            if (file_exists($plugindir.'/lib.php')) {
                require_once($plugindir.'/lib.php');
                $cron_function = 'grade_export_'.$gradeexport.'_cron';
                if (function_exists($cron_function)) {
                    mtrace("Processing gradebook export function $cron_function ...", '');
                    $cron_function();
                }
            }
        }
    }

    if ($gradereports = get_plugin_list('gradereport')) {
        foreach ($gradereports as $gradereport => $plugindir) {
            if (file_exists($plugindir.'/lib.php')) {
                require_once($plugindir.'/lib.php');
                $cron_function = 'grade_report_'.$gradereport.'_cron';
                if (function_exists($cron_function)) {
                    mtrace("Processing gradebook report function $cron_function ...", '');
                    $cron_function();
                }
            }
        }
    }

    // Run external blog cron if needed
    if ($CFG->useexternalblogs) {
        require_once($CFG->dirroot . '/blog/lib.php');
        mtrace("Fetching external blog entries...", '');
        $sql = "timefetched < ? OR timefetched = 0";
        $externalblogs = $DB->get_records_select('blog_external', $sql, array(mktime() - $CFG->externalblogcrontime));

        foreach ($externalblogs as $eb) {
            blog_sync_external_entries($eb);
        }
    }

    // Run blog associations cleanup
    if ($CFG->useblogassociations) {
        require_once($CFG->dirroot . '/blog/lib.php');
        // delete entries whose contextids no longer exists
        mtrace("Deleting blog associations linked to non-existent contexts...", '');
        $DB->delete_records_select('blog_association', 'contextid NOT IN (SELECT id FROM {context})');
    }

    //Run registration updated cron
    mtrace(get_string('siteupdatesstart', 'hub'));
    require_once($CFG->dirroot . '/admin/registration/lib.php');
    $registrationmanager = new registration_manager();
    $registrationmanager->cron();
    mtrace(get_string('siteupdatesend', 'hub'));

    // cleanup file trash
    $fs = get_file_storage();
    $fs->cron();

    //cleanup old session linked tokens
    //deletes the session linked tokens that are over a day old.
    mtrace("Deleting session linked tokens more than one day old...", '');
    $DB->delete_records_select('external_tokens', 'lastaccess < :onedayago AND tokentype = :tokentype',
                    array('onedayago' => time() - DAYSECS, 'tokentype' => EXTERNAL_TOKEN_EMBEDDED));
    mtrace('done.');

    // run any customized cronjobs, if any
    if ($locals = get_plugin_list('local')) {
        mtrace('Processing customized cron scripts ...', '');
        foreach ($locals as $local => $localdir) {
            if (file_exists("$localdir/cron.php")) {
                include("$localdir/cron.php");
            }
        }
        mtrace('done.');
    }


    mtrace("Cron script completed correctly");

    $difftime = microtime_diff($starttime, microtime());
    mtrace("Execution took ".$difftime." seconds");

}