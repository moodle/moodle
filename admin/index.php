<?php // $Id$

/// Check that config.php exists, if not then call the install script
    if (!file_exists('../config.php')) {
        header('Location: ../install.php');
        die;
    }

/// Check that PHP is of a sufficient version
/// Moved here because older versions do not allow while(@ob_end_clean());
    if (version_compare(phpversion(), "4.3.0") < 0) {
        $phpversion = phpversion();
        echo "Sorry, Moodle requires PHP 4.3.0 or later (currently using version $phpversion)";
        die;
    }

/// Turn off time limits and try to flush everything all the time, sometimes upgrades can be slow.

    @set_time_limit(0);
    @ob_implicit_flush(true);
    while(@ob_end_clean()); // ob_end_flush prevents sending of headers


    require_once('../config.php');
    require_once($CFG->libdir.'/adminlib.php');  // Contains various admin-only functions
    require_once($CFG->libdir.'/ddllib.php'); // Install/upgrade related db functions

    $id             = optional_param('id', '', PARAM_ALPHANUM);
    $confirmupgrade = optional_param('confirmupgrade', 0, PARAM_BOOL);
    $confirmrelease = optional_param('confirmrelease', 0, PARAM_BOOL);
    $agreelicence = optional_param('agreelicence',0, PARAM_BOOL);
    $ignoreupgradewarning = optional_param('ignoreupgradewarning', 0, PARAM_BOOL);

/// check upgrade status first
    if ($ignoreupgradewarning and !empty($_SESSION['upgraderunning'])) {
        $_SESSION['upgraderunning'] = 0;
    }
    upgrade_check_running("Upgrade already running in this session, please wait!<br />Click on the exclamation marks to ignore this warning (<a href=\"index.php?ignoreupgradewarning=1\">!!!</a>).", 10);

/// Check some PHP server settings

    $documentationlink = '<a href="http://docs.moodle.org/en/Installation">Installation docs</a>';

    if (ini_get_bool('session.auto_start')) {
        error("The PHP server variable 'session.auto_start' should be Off - $documentationlink");
    }

    if (ini_get_bool('magic_quotes_runtime')) {
        error("The PHP server variable 'magic_quotes_runtime' should be Off - $documentationlink");
    }

    if (!ini_get_bool('file_uploads')) {
        error("The PHP server variable 'file_uploads' is not turned On - $documentationlink");
    }

    if (empty($CFG->prefix) && $CFG->dbtype != 'mysql') {  //Enforce prefixes for everybody but mysql
        error('$CFG->prefix can\'t be empty for your target DB (' . $CFG->dbtype . ')');
    }

    if ($CFG->dbtype == 'oci8po' && strlen($CFG->prefix) > 2) { //Max prefix length for Oracle is 2cc
        error('$CFG->prefix maximum allowed length for Oracle DBs is 2cc.');
    }

/// Check that config.php has been edited

    if ($CFG->wwwroot == "http://example.com/moodle") {
        error("Moodle has not been configured yet.  You need to edit config.php first.");
    }


/// Check settings in config.php

    $dirroot = dirname(realpath("../index.php"));
    if (!empty($dirroot) and $dirroot != $CFG->dirroot) {
        error("Please fix your settings in config.php:
              <p>You have:
              <p>\$CFG->dirroot = \"".addslashes($CFG->dirroot)."\";
              <p>but it should be:
              <p>\$CFG->dirroot = \"".addslashes($dirroot)."\";",
              "./");
    }

/// Set some necessary variables during set-up to avoid PHP warnings later on this page
    if (!isset($CFG->framename)) {
        $CFG->framename = "_top";
    }
    if (!isset($CFG->release)) {
        $CFG->release = "";
    }
    if (!isset($CFG->version)) {
        $CFG->version = "";
    }

/// Check if the main tables have been installed yet or not.

    if (! $tables = $db->Metatables() ) {    // No tables yet at all.
        $maintables = false;

    } else {                                 // Check for missing main tables
        $maintables = true;
        $mtables = array("config", "course", "course_categories", "course_modules",
                         "course_sections", "log", "log_display", "modules",
                         "user");
        foreach ($mtables as $mtable) {
            if (!in_array($CFG->prefix.$mtable, $tables)) {
                $maintables = false;
                break;
            }
        }
    }

    $linktoscrolltoerrors = '<script type="text/javascript" src="' . $CFG->wwwroot . '/lib/scroll_to_errors.js"></script>';
    if (! $maintables) {
    /// hide errors from headers in case debug enabled in config.php
        $origdebug = $CFG->debug;
        $CFG->debug = DEBUG_MINIMAL;
        error_reporting($CFG->debug);
        if (empty($agreelicence)) {
            $strlicense = get_string("license");
            print_header($strlicense, $strlicense, $strlicense, "", "", false, "&nbsp;", "&nbsp;");
            print_heading("<a href=\"http://moodle.org\">Moodle</a> - Modular Object-Oriented Dynamic Learning Environment");
            print_heading(get_string("copyrightnotice"));
            print_simple_box_start('center');
            echo text_to_html(get_string("gpl"));
            print_simple_box_end();
            echo "<br />";
            notice_yesno(get_string("doyouagree"), "index.php?agreelicence=true",
                                                   "http://docs.moodle.org/en/License");
            exit;
        }

        $strdatabasesetup    = get_string("databasesetup");
        $strdatabasesuccess  = get_string("databasesuccess");
        print_header($strdatabasesetup, $strdatabasesetup, $strdatabasesetup,
                        "", $linktoscrolltoerrors, false, "&nbsp;", "&nbsp;");
    /// return to original debugging level
        $CFG->debug = $origdebug;
        error_reporting($CFG->debug);
        upgrade_log_start();
        $db->debug = true;

    /// Both old .sql files and new install.xml are supported
    /// But we prioritise install.xml (XMLDB) if present
    
        change_db_encoding(); // first try to change db encoding to utf8
        $status = false;
        if (file_exists("$CFG->libdir/db/install.xml")) {
            $status = install_from_xmldb_file("$CFG->libdir/db/install.xml"); //New method
        } else if (file_exists("$CFG->libdir/db/$CFG->dbtype.sql")) {
            $status = modify_database("$CFG->libdir/db/$CFG->dbtype.sql"); //Old method
        } else {
            error("Error: Your database ($CFG->dbtype) is not yet fully supported by Moodle or install.xml is not present.  See the lib/db directory.");
        }

    /// Continue with the instalation
        $db->debug = false;
        if ($status) {
            // Install the roles system.
            moodle_install_roles();
            set_config('statsrolesupgraded',time());

            // Write default settings unconditionally (i.e. even if a setting is already set, overwrite it)
            // (this should only have any effect during initial install).
            $adminroot = admin_get_root();
            $adminroot->prune('backups'); // backup settings table not created yet
            apply_default_settings($adminroot);

            /// This is used to handle any settings that must exist in $CFG but which do not exist in
            /// admin_get_root()/$ADMIN as admin_setting objects (there are some exceptions).
            apply_default_exception_settings(array('alternateloginurl' => '',
                                                   'auth' => 'email',
                                                   'auth_pop3mailbox' => 'INBOX',
                                                   'changepassword' => '',
                                                   'enrol' => 'manual',
                                                   'enrol_plugins_enabled' => 'manual',
                                                   'guestloginbutton' => 1,
                                                   'style' => 'default',
                                                   'template' => 'default',
                                                   'theme' => 'standardwhite'));

            notify($strdatabasesuccess, "green");
        } else {
            error("Error: Main databases NOT set up successfully");
        }
        print_continue('index.php');
        die;
    }


/// Check version of Moodle code on disk compared with database
/// and upgrade if possible.

    if ( is_readable("$CFG->dirroot/version.php")) {
        include_once("$CFG->dirroot/version.php");              # defines $version
    }
    if (!$version) {
        error('Main version.php was not readable or specified');# without version, stop
    }

    if (file_exists("$CFG->dirroot/lib/db/$CFG->dbtype.php")) {
        include_once("$CFG->dirroot/lib/db/$CFG->dbtype.php");  # defines old upgrades
    }
    if (file_exists("$CFG->dirroot/lib/db/upgrade.php")) {
        include_once("$CFG->dirroot/lib/db/upgrade.php");  # defines new upgrades
    }

    $stradministration = get_string("administration");

    if ($CFG->version) {
        if ($version > $CFG->version) {  // upgrade

            $a->oldversion = "$CFG->release ($CFG->version)";
            $a->newversion = "$release ($version)";
            $strdatabasechecking = get_string("databasechecking", "", $a);

            // hide errors from headers in case debug is enabled
            $origdebug = $CFG->debug;
            $CFG->debug = DEBUG_MINIMAL;
            error_reporting($CFG->debug);

            // logout in case we are upgrading from pre 1.7 version - prevention of weird session problems
            if ($CFG->version < 2006050600) {
                require_logout();
            }

            if (empty($confirmupgrade)) {

                print_header($strdatabasechecking, $stradministration, $strdatabasechecking,
                        "", "", false, "&nbsp;", "&nbsp;");

                notice_yesno(get_string('upgradesure', 'admin', $a->newversion), 'index.php?confirmupgrade=yes', 'index.php');
                exit;

            } else if (empty($confirmrelease)) {
                $strcurrentrelease = get_string("currentrelease");
                print_header($strcurrentrelease, $strcurrentrelease, $strcurrentrelease, "", "", false, "&nbsp;", "&nbsp;");
                print_heading("Moodle $release");
                print_simple_box(get_string('releasenoteslink', 'admin', 'http://docs.moodle.org/en/Release_Notes'), 'center');

                require_once($CFG->libdir.'/environmentlib.php');
                print_heading(get_string('environment', 'admin'));
                if (!check_moodle_environment($release, $environment_results, true)) {
                    notice_yesno(get_string('environmenterrorupgrade', 'admin'), 
                                 'index.php?confirmupgrade=1&confirmrelease=1', 'index.php');
                } else {
                    notify(get_string('environmentok', 'admin'), 'notifysuccess');

                    echo '<form action="index.php">';
                    echo '<input type="hidden" name="confirmupgrade" value="1" />';
                    echo '<input type="hidden" name="confirmrelease" value="1" />';
                    echo '<div class="continuebutton"><input type="submit" value="'.get_string('continue').'" /></div>';
                    echo '</form>';
                }

                print_footer('none');
                exit;

            } else {
                $strdatabasesuccess  = get_string("databasesuccess");
                print_header($strdatabasechecking, $stradministration, $strdatabasechecking,
                        "", $linktoscrolltoerrors, false, "&nbsp;", "&nbsp;");

            /// return to original debugging level
                $CFG->debug = $origdebug;
                error_reporting($CFG->debug);
                upgrade_log_start();

            /// Upgrade current language pack if we can
                upgrade_language_pack();   

                print_heading($strdatabasechecking);
                $db->debug=true;
            /// Launch the old main upgrade (if exists)
                $status = true;
                if (function_exists('main_upgrade')) {
                    $status = main_upgrade($CFG->version);
                }
            /// If succesful and exists launch the new main upgrade (XMLDB), called xmldb_main_upgrade
                if ($status && function_exists('xmldb_main_upgrade')) {
                    $status = xmldb_main_upgrade($CFG->version);
                }
                $db->debug=false;
            /// If successful, continue upgrading roles and setting everything properly
                if ($status) {
                    if (empty($CFG->rolesactive)) {
                        // Upgrade to the roles system.
                        moodle_install_roles();
                        set_config('rolesactive', 1);
                    } else if (!update_capabilities()) {
                        error('Had trouble upgrading the core capabilities for the Roles System');
                    }
                    require_once($CFG->libdir.'/statslib.php');
                    if (!stats_upgrade_for_roles_wrapper()) {
                        notify('Couldn\'t upgrade the stats tables to use the new roles system');
                    }
                    if (set_config("version", $version)) {
                        remove_dir($CFG->dataroot . '/cache', true); // flush cache
                        notify($strdatabasesuccess, "green");
                        print_continue("upgradesettings.php");
                        exit;
                    } else {
                        notify("Upgrade failed!  (Could not update version in config table)");
                    }
            /// Main upgrade not success
                } else {
                    notify("Upgrade failed!  See /version.php");
                }
                upgrade_log_finish();
            }
        } else if ($version < $CFG->version) {
            upgrade_log_start();
            notify("WARNING!!!  The code you are using is OLDER than the version that made these databases!");
            upgrade_log_finish();
        }
    } else {
        if (!set_config("version", $version)) {
            error("A problem occurred inserting current version into databases");
        }
    }

/// Updated human-readable release version if necessary

    if ($release <> $CFG->release) {  // Update the release version
        if (!set_config("release", $release)) {
            error("ERROR: Could not update release version in database!!");
        }
    }

/// Find and check all main modules and load them up or upgrade them if necessary
/// first old *.php update and then the new upgrade.php script
    upgrade_activity_modules("$CFG->wwwroot/$CFG->admin/index.php");  // Return here afterwards

/// Check all questiontype plugins and upgrade if necessary
/// first old *.php update and then the new upgrade.php script
/// It is important that this is done AFTER the quiz module has been upgraded
    upgrade_plugins('qtype', 'question/type', "$CFG->wwwroot/$CFG->admin/index.php");  // Return here afterwards

/// Upgrade backup/restore system if necessary
/// first old *.php update and then the new upgrade.php script
    require_once("$CFG->dirroot/backup/lib.php");
    upgrade_backup_db("$CFG->wwwroot/$CFG->admin/index.php");  // Return here afterwards

/// Upgrade blocks system if necessary
/// first old *.php update and then the new upgrade.php script
    require_once("$CFG->dirroot/lib/blocklib.php");
    upgrade_blocks_db("$CFG->wwwroot/$CFG->admin/index.php");  // Return here afterwards

/// Check all blocks and load (or upgrade them if necessary)
/// first old *.php update and then the new upgrade.php script
    upgrade_blocks_plugins("$CFG->wwwroot/$CFG->admin/index.php");  // Return here afterwards

/// Check all enrolment plugins and upgrade if necessary
/// first old *.php update and then the new upgrade.php script
    upgrade_plugins('enrol', 'enrol', "$CFG->wwwroot/$CFG->admin/index.php");  // Return here afterwards

/// Check for local database customisations
/// first old *.php update and then the new upgrade.php script
    require_once("$CFG->dirroot/lib/locallib.php");
    upgrade_local_db("$CFG->wwwroot/$CFG->admin/index.php");  // Return here afterwards

/// just make sure upgrade logging is properly terminated
    upgrade_log_finish();

/// Set up the blank site - to be customized later at the end of install.
    if (! $site = get_site()) {
        // We are about to create the site "course"
        require_once($CFG->libdir.'/blocklib.php');

        $newsite = new Object();
        $newsite->fullname = "";
        $newsite->shortname = "";
        $newsite->summary = "";
        $newsite->newsitems = 3;
        $newsite->numsections = 0;
        $newsite->category = 0;
        $newsite->format = 'site';  // Only for this course
        $newsite->teacher = get_string("defaultcourseteacher");
        $newsite->teachers = get_string("defaultcourseteachers");
        $newsite->student = get_string("defaultcoursestudent");
        $newsite->students = get_string("defaultcoursestudents");
        $newsite->timemodified = time();

        if ($newid = insert_record('course', $newsite)) {
            // Site created, add blocks for it
            $page = page_create_object(PAGE_COURSE_VIEW, $newid);
            blocks_repopulate_page($page); // Return value not checked because you can always edit later

            $cat = new Object();
            $cat->name = get_string('miscellaneous');
            if (insert_record('course_categories', $cat)) {
                  redirect('index.php');
            } else {
                 error("Serious Error! Could not set up a default course category!");
            }
        } else {
            error("Serious Error! Could not set up the site!");
        }
    }

    // initialise default blocks on admin and site page if needed
    if (empty($CFG->adminblocks_initialised)) {
        require_once("$CFG->dirroot/$CFG->admin/pagelib.php");
        require_once($CFG->libdir.'/blocklib.php');
        page_map_class(PAGE_ADMIN, 'page_admin');
        $page = page_create_object(PAGE_ADMIN, 0); // there must be some id number
        blocks_repopulate_page($page);

        //add admin_tree block to site if not already present
        if ($admintree = get_record('block', 'name', 'admin_tree')) {
            $page = page_create_object(PAGE_COURSE_VIEW, SITEID);
            blocks_execute_action($page, blocks_get_by_page($page), 'add', (int)$admintree->id, false, false);
            if ($admintreeinstance = get_record('block_instance', 'pagetype', $page->type, 'pageid', SITEID, 'blockid', $admintree->id)) {
                blocks_execute_action($page, blocks_get_by_page($page), 'moveleft', $admintreeinstance, false, false);
            }
        }

        set_config('adminblocks_initialised', 1);
    }

/// Define the unique site ID code if it isn't already
    if (empty($CFG->siteidentifier)) {    // Unique site identification code
        set_config('siteidentifier', random_string(32).$_SERVER['HTTP_HOST']);
    }

/// Check if the guest user exists.  If not, create one.
    if (! record_exists("user", "username", "guest")) {
        $guest->auth        = "manual";
        $guest->username    = "guest";
        $guest->password    = md5("guest");
        $guest->firstname   = addslashes(get_string("guestuser"));
        $guest->lastname    = " ";
        $guest->email       = "root@localhost";
        $guest->description = addslashes(get_string("guestuserinfo"));
        $guest->confirmed   = 1;
        $guest->lang        = $CFG->lang;
        $guest->timemodified= time();

        if (! $guest->id = insert_record("user", $guest)) {
            notify("Could not create guest user record !!!");
        }
    }


/// Set up the admin user
    if (empty($CFG->rolesactive)) {
        redirect('user.php');
    }

/// Check for valid admin user
    require_login();

    $context = get_context_instance(CONTEXT_SYSTEM, SITEID);

    require_capability('moodle/site:config', $context);

/// check that site is properly customized
    if (empty($site->shortname) or empty($site->shortname)) {
        redirect('settings.php?section=frontpage&amp;return=site');
    }

/// Check if we are returning from moodle.org registration and if so, we mark that fact to remove reminders

    if (!empty($id)) {
        if ($id == $CFG->siteidentifier) {
            set_config('registered', time());
        }
    }

/// Everything should now be set up, and the user is an admin

/// Print default admin page with notifications.

    $adminroot = admin_get_root();
    admin_externalpage_setup('adminnotifications', $adminroot);
    admin_externalpage_print_header($adminroot);

/// Deprecated database! Warning!!
    if (!empty($CFG->migrated_to_new_db)) {
        print_simple_box_start('center','60%');
        print_string('dbmigrationdeprecateddb','admin');
        print_simple_box_end();
    }

/// Check for any special upgrades that might need to be run
    if (!empty($CFG->upgrade)) {
        print_simple_box(get_string("upgrade$CFG->upgrade", "admin",
                                    "$CFG->wwwroot/$CFG->admin/upgrade$CFG->upgrade.php"), "center", '60%');
        print_spacer(10,10);
    }

    if (ini_get_bool('register_globals') && !ini_get_bool('magic_quotes_gpc')) {
        print_simple_box(get_string('globalsquoteswarning', 'admin'), 'center', '60%');
    }

    if (is_dataroot_insecure()) {
        print_simple_box(get_string('datarootsecuritywarning', 'admin', $CFG->dataroot), 'center', '60%');
    }

/// If no recently cron run
    $lastcron = get_field_sql('SELECT max(lastcron) FROM ' . $CFG->prefix . 'modules');
    if (time() - $lastcron > 3600 * 24) {
        $strinstallation = get_string('installation', 'install');
        $helpbutton = helpbutton('install', $strinstallation, 'moodle', true, false, '', true);
        print_simple_box(get_string('cronwarning', 'admin')."&nbsp;".$helpbutton, 'center', '60%');
    }

/// Alert if we are currently in maintenance mode
    if (file_exists($CFG->dataroot.'/1/maintenance.html')) {
        print_simple_box(get_string('sitemaintenancewarning', 'admin') , 'center', '60%');
    }

/// Alert to display the utf-8 migration button (if !unicode yet and DB is MySQL or PG)
    if (empty($CFG->unicodedb) && in_array($CFG->dbtype, array('mysql', 'postgres7'))) {
        print_simple_box(get_string('unicodeupgradenotice', 'admin') , 'center', '60%');
    }

/// Print slightly annoying registration button every six months   ;-)
/// You can set the "registered" variable to something far in the future
/// if you really want to prevent this.   eg  9999999999
    if (!isset($CFG->registered) || $CFG->registered < (time() - 3600*24*30*6)) {
        $options = array();
        $options['sesskey'] = $USER->sesskey;
        print_simple_box_start('center','60%');
        echo '<div align="center">';
        print_string('pleaseregister', 'admin');
        print_single_button('register.php', $options, get_string('registration'));
        echo '</div>';
        print_simple_box_end();
        $registrationbuttonshown = true;
    }

    //////////////////////////////////////////////////////////////////////////////////////////////////
    ////  IT IS ILLEGAL AND A VIOLATION OF THE GPL TO HIDE, REMOVE OR MODIFY THIS COPYRIGHT NOTICE ///
    $copyrighttext = '<a href="http://moodle.org/">Moodle</a> '.
                     '<a href="http://docs.moodle.org/en/Release">'.$CFG->release.'</a> ('.$CFG->version.')<br />'.
                     'Copyright &copy; 1999 onwards, Martin Dougiamas<br />'.
                     'and <a href="http://docs.moodle.org/en/Credits">many other contributors</a>.<br />'.
                     '<a href="http://docs.moodle.org/en/License">GNU Public License</a>';
    echo '<p class="copyright">'.$copyrighttext.'</p>';
    //////////////////////////////////////////////////////////////////////////////////////////////////


    if (empty($registrationbuttonshown)) {
        echo '<div align="center">';
        $options = array();
        $options['sesskey'] = $USER->sesskey;
        print_single_button('register.php', $options, get_string('registration'));
        echo '</div>';
    }

    admin_externalpage_print_footer($adminroot);



?>
