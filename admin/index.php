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
    require_once($CFG->libdir.'/db/upgradelib.php');  // Upgrade-related functions

    $id             = optional_param('id', '', PARAM_TEXT);
    $confirmupgrade = optional_param('confirmupgrade', 0, PARAM_BOOL);
    $confirmrelease = optional_param('confirmrelease', 0, PARAM_BOOL);
    $agreelicense   = optional_param('agreelicense', 0, PARAM_BOOL);
    $autopilot      = optional_param('autopilot', 0, PARAM_BOOL);
    $ignoreupgradewarning = optional_param('ignoreupgradewarning', 0, PARAM_BOOL);
    $confirmplugincheck = optional_param('confirmplugincheck', 0, PARAM_BOOL);

/// check upgrade status first
    if ($ignoreupgradewarning and !empty($_SESSION['upgraderunning'])) {
        $_SESSION['upgraderunning'] = 0;
    }
    upgrade_check_running("Upgrade already running in this session, please wait!<br />Click on the exclamation marks to ignore this warning (<a href=\"index.php?ignoreupgradewarning=1\">!!!</a>).", 10);

/// set install/upgrade autocontinue session flag
    if ($autopilot) {
        $_SESSION['installautopilot'] = $autopilot;
    }

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

    if (empty($CFG->prefix) && $CFG->dbfamily != 'mysql') {  //Enforce prefixes for everybody but mysql
        error('$CFG->prefix can\'t be empty for your target DB (' . $CFG->dbtype . ')');
    }

    if ($CFG->dbfamily == 'oracle' && strlen($CFG->prefix) > 2) { //Max prefix length for Oracle is 2cc
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

    if (is_readable("$CFG->dirroot/version.php")) {
        include_once("$CFG->dirroot/version.php");              # defines $version
    }

    if (!$version or !$release) {
        error('Main version.php was not readable or specified');# without version, stop
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
    
    if (! $maintables) {
    /// hide errors from headers in case debug enabled in config.php
        $origdebug = $CFG->debug;
        $CFG->debug = DEBUG_MINIMAL;
        error_reporting($CFG->debug);
        if (empty($agreelicense)) {
            $strlicense = get_string('license');
            $navigation = build_navigation(array(array('name'=>$strlicense, 'link'=>null, 'type'=>'misc')));
            print_header($strlicense, $strlicense, $navigation, "", "", false, "&nbsp;", "&nbsp;");
            print_heading("<a href=\"http://moodle.org\">Moodle</a> - Modular Object-Oriented Dynamic Learning Environment");
            print_heading(get_string('copyrightnotice'));
            print_box(text_to_html(get_string('gpl')), 'copyrightnotice');
            echo "<br />";
            notice_yesno(get_string('doyouagree'), "index.php?agreelicense=1",
                                                   "http://docs.moodle.org/en/License");
            print_footer('none');
            exit;
        }
        if (empty($confirmrelease)) {
            $strcurrentrelease = get_string("currentrelease");
            $navigation = build_navigation(array(array('name'=>$strcurrentrelease, 'link'=>null, 'type'=>'misc')));
            print_header($strcurrentrelease, $strcurrentrelease, $navigation, "", "", false, "&nbsp;", "&nbsp;");
            print_heading("Moodle $release");
            print_box(get_string('releasenoteslink', 'admin', 'http://docs.moodle.org/en/Release_Notes'), 'generalbox boxaligncenter boxwidthwide');
            echo '<form action="index.php"><div>';
            echo '<input type="hidden" name="agreelicense" value="1" />';
            echo '<input type="hidden" name="confirmrelease" value="1" />';
            echo '</div>';
            echo '<div class="continuebutton"><input name="autopilot" id="autopilot" type="checkbox" value="1" /><label for="autopilot">'.get_string('unattendedoperation', 'admin').'</label>';
            echo '<br /><br /><input type="submit" value="'.get_string('continue').'" /></div>';
            echo '</form>';
            print_footer('none');
            die;
        }


        $strdatabasesetup    = get_string("databasesetup");
        $strdatabasesuccess  = get_string("databasesuccess");
        $navigation = build_navigation(array(array('name'=>$strdatabasesetup, 'link'=>null, 'type'=>'misc')));
        print_header($strdatabasesetup, $strdatabasesetup, $navigation,
                        "", upgrade_get_javascript(), false, "&nbsp;", "&nbsp;");
    /// return to original debugging level
        $CFG->debug = $origdebug;
        error_reporting($CFG->debug);
        upgrade_log_start();
        $db->debug = true;

    /// Both old .sql files and new install.xml are supported
    /// But we prioritise install.xml (XMLDB) if present

        change_db_encoding(); // first try to change db encoding to utf8
        if (!setup_is_unicodedb()) {
            // If could not convert successfully, throw error, and prevent installation
            print_error('unicoderequired', 'admin');
        }

        $status = false;
        if (file_exists("$CFG->libdir/db/install.xml")) {
            $status = install_from_xmldb_file("$CFG->libdir/db/install.xml"); //New method
        } else if (file_exists("$CFG->libdir/db/$CFG->dbtype.sql")) {
            $status = modify_database("$CFG->libdir/db/$CFG->dbtype.sql"); //Old method
        } else {
            error("Error: Your database ($CFG->dbtype) is not yet fully supported by Moodle or install.xml is not present.  See the lib/db directory.");
        }

        // all new installs are in unicode - keep for backwards compatibility and 1.8 upgrade checks
        set_config('unicodedb', 1);

    /// Continue with the instalation
        $db->debug = false;
        if ($status) {

            /// Groups install is now in core above.

            // Install the roles system.
            moodle_install_roles();
            set_config('statsrolesupgraded',time());

            // install core event handlers
            events_update_definition();

            /// This is used to handle any settings that must exist in $CFG but which do not exist in
            /// admin_get_root()/$ADMIN as admin_setting objects (there are some exceptions).
            apply_default_exception_settings(array('auth' => 'email',
                                                   'auth_pop3mailbox' => 'INBOX',
                                                   'enrol' => 'manual',
                                                   'enrol_plugins_enabled' => 'manual',
                                                   'style' => 'default',
                                                   'template' => 'default',
                                                   'theme' => 'standardwhite',
                                                   'filter_multilang_converted' => 1));

            // Write default settings unconditionally (i.e. even if a setting is already set, overwrite it)
            // (this should only have any effect during initial install).
            admin_apply_default_settings(NULL, true);

            notify($strdatabasesuccess, "green");
            require_once $CFG->dirroot.'/mnet/lib.php';
        } else {
            error("Error: Main databases NOT set up successfully");
        }
        print_continue('index.php');
        print_footer('none');
        die;
    }


/// Check version of Moodle code on disk compared with database
/// and upgrade if possible.

    if (file_exists("$CFG->dirroot/lib/db/$CFG->dbtype.php")) {
        include_once("$CFG->dirroot/lib/db/$CFG->dbtype.php");  # defines old upgrades
    }
    if (file_exists("$CFG->dirroot/lib/db/upgrade.php")) {
        include_once("$CFG->dirroot/lib/db/upgrade.php");  # defines new upgrades
    }

    $stradministration = get_string("administration");

    if ($CFG->version) {
        if ($version > $CFG->version) {  // upgrade

        /// If the database is not already Unicode then we do not allow upgrading!
        /// Instead, we print an error telling them to upgrade to 1.7 first.  MDL-6857
            if (empty($CFG->unicodedb)) {
                print_error('unicodeupgradeerror', 'error', '', $version);
            }

            $a->oldversion = "$CFG->release ($CFG->version)";
            $a->newversion = "$release ($version)";
            $strdatabasechecking = get_string("databasechecking", "", $a);

            // hide errors from headers in case debug is enabled
            $origdebug = $CFG->debug;
            $CFG->debug = DEBUG_MINIMAL;
            error_reporting($CFG->debug);

            // logo ut in case we are upgrading from pre 1.9 version in order to prevent
            // weird session/role problems caused by incorrect data in USER and SESSION
            if ($CFG->version < 2007101500) {
                require_logout();
            }

            if (empty($confirmupgrade)) {
                $navigation = build_navigation(array(array('name'=>$strdatabasechecking, 'link'=>null, 'type'=>'misc')));
                print_header($strdatabasechecking, $stradministration, $navigation,
                        "", "", false, "&nbsp;", "&nbsp;");

                notice_yesno(get_string('upgradesure', 'admin', $a->newversion), 'index.php?confirmupgrade=1', 'index.php');
                print_footer('none');
                exit;

            } else if (empty($confirmrelease)){
                $strcurrentrelease = get_string("currentrelease");
                $navigation = build_navigation(array(array('name'=>$strcurrentrelease, 'link'=>null, 'type'=>'misc')));
                print_header($strcurrentrelease, $strcurrentrelease, $navigation, "", "", false, "&nbsp;", "&nbsp;");
                print_heading("Moodle $release");
                print_box(get_string('releasenoteslink', 'admin', 'http://docs.moodle.org/en/Release_Notes'));

                require_once($CFG->libdir.'/environmentlib.php');
                print_heading(get_string('environment', 'admin'));
                if (!check_moodle_environment($release, $environment_results, true)) {
                    print_box_start('generalbox', 'notice'); // MDL-8330
                    print_string('langpackwillbeupdated', 'admin');
                    print_box_end();
                    notice_yesno(get_string('environmenterrorupgrade', 'admin'),
                                 'index.php?confirmupgrade=1&confirmrelease=1', 'index.php');
                } else {
                    notify(get_string('environmentok', 'admin'), 'notifysuccess');
                    print_box_start('generalbox', 'notice'); // MDL-8330
                    print_string('langpackwillbeupdated', 'admin');
                    print_box_end();
                    echo '<form action="index.php"><div>';
                    echo '<input type="hidden" name="confirmupgrade" value="1" />';
                    echo '<input type="hidden" name="confirmrelease" value="1" />';
                    echo '</div>';
                    echo '<div class="continuebutton">';
                    echo '<br /><br /><input type="submit" value="'.get_string('continue').'" /></div>';
                    echo '</form>';
                }

                print_footer('none');
                die;
            } elseif (empty($confirmplugincheck)) { 
                $strplugincheck = get_string('plugincheck');
                $navigation = build_navigation(array(array('name'=>$strplugincheck, 'link'=>null, 'type'=>'misc')));
                print_header($strplugincheck, $strplugincheck, $navigation, "", "", false, "&nbsp;", "&nbsp;");
                print_heading($strplugincheck);
                print_box_start('generalbox', 'notice'); // MDL-8330
                print_string('pluginchecknotice');
                print_box_end();
                print_plugin_tables();
                echo "<br />";
                echo '<div class="continuebutton">';
                print_single_button('index.php', array('confirmupgrade' => 1, 'confirmrelease' => 1), get_string('reload'), 'get');
                echo '</div><br />';
                echo '<form action="index.php"><div>';
                echo '<input type="hidden" name="confirmupgrade" value="1" />';
                echo '<input type="hidden" name="confirmrelease" value="1" />';
                echo '<input type="hidden" name="confirmplugincheck" value="1" />';
                echo '</div>';
                echo '<div class="continuebutton"><input name="autopilot" id="autopilot" type="checkbox" value="1" /><label for="autopilot">'.get_string('unattendedoperation', 'admin').'</label>';
                echo '<br /><br /><input type="submit" value="'.get_string('continue').'" /></div>';
                echo '</form>';
                print_footer('none');
                die();
    
            } else {
                $strdatabasesuccess  = get_string("databasesuccess");
                $navigation = build_navigation(array(array('name'=>$strdatabasesuccess, 'link'=>null, 'type'=>'misc')));
                print_header($strdatabasechecking, $stradministration, $navigation,
                        "", upgrade_get_javascript(), false, "&nbsp;", "&nbsp;");

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

                        /// Groups upgrade is now in core above.

                        // Upgrade to the roles system.
                        moodle_install_roles();
                        set_config('rolesactive', 1);
                    } else if (!update_capabilities()) {
                        error('Had trouble upgrading the core capabilities for the Roles System');
                    }
                    // update core events
                    events_update_definition();

                    require_once($CFG->libdir.'/statslib.php');
                    if (!stats_upgrade_for_roles_wrapper()) {
                        notify('Couldn\'t upgrade the stats tables to use the new roles system');
                    }
                    if (set_config("version", $version)) {
                        remove_dir($CFG->dataroot . '/cache', true); // flush cache
                        notify($strdatabasesuccess, "green");
                        print_continue("upgradesettings.php");
                        print_footer('none');
                        exit;
                    } else {
                        error('Upgrade failed!  (Could not update version in config table)');
                    }
            /// Main upgrade not success
                } else {
                    notify('Main Upgrade failed!  See lib/db/upgrade.php');
                    print_continue('index.php?confirmupgrade=1&amp;confirmrelease=1&amp;confirmplugincheck=1');
                    print_footer('none');
                    die;
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

/// Groups install/upgrade is now in core above.


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

/// Check all auth plugins and upgrade if necessary
    upgrade_plugins('auth','auth',"$CFG->wwwroot/$CFG->admin/index.php");

/// Check all course formats and upgrade if necessary
    upgrade_plugins('format','course/format',"$CFG->wwwroot/$CFG->admin/index.php");

/// Check for local database customisations
/// first old *.php update and then the new upgrade.php script
    require_once("$CFG->dirroot/lib/locallib.php");
    upgrade_local_db("$CFG->wwwroot/$CFG->admin/index.php");  // Return here afterwards

/// Check for changes to RPC functions
    require_once("$CFG->dirroot/$CFG->admin/mnet/adminlib.php");
    upgrade_RPC_functions("$CFG->wwwroot/$CFG->admin/index.php");  // Return here afterwards

/// Upgrade all plugins for gradebook
    upgrade_plugins('gradeexport', 'grade/export', "$CFG->wwwroot/$CFG->admin/index.php");
    upgrade_plugins('gradeimport', 'grade/import', "$CFG->wwwroot/$CFG->admin/index.php");
    upgrade_plugins('gradereport', 'grade/report', "$CFG->wwwroot/$CFG->admin/index.php");

/// Check all message output plugins and upgrade if necessary
    upgrade_plugins('message','message/output',"$CFG->wwwroot/$CFG->admin/index.php");

/// Check all admin report plugins and upgrade if necessary
    upgrade_plugins('report', $CFG->admin.'/report', "$CFG->wwwroot/$CFG->admin/index.php");


/// just make sure upgrade logging is properly terminated
    upgrade_log_finish();

    unset($_SESSION['installautopilot']);

/// Set up the blank site - to be customized later at the end of install.
    if (! $site = get_site()) {
        // We are about to create the site "course"
        require_once($CFG->libdir.'/blocklib.php');

        $newsite = new object();
        $newsite->fullname = "";
        $newsite->shortname = "";
        $newsite->summary = NULL;
        $newsite->newsitems = 3;
        $newsite->numsections = 0;
        $newsite->category = 0;
        $newsite->format = 'site';  // Only for this course
        $newsite->teacher = get_string("defaultcourseteacher");
        $newsite->teachers = get_string("defaultcourseteachers");
        $newsite->student = get_string("defaultcoursestudent");
        $newsite->students = get_string("defaultcoursestudents");
        $newsite->timemodified = time();

        if (!$newid = insert_record('course', $newsite)) {
            error("Serious Error! Could not set up the site!");
        }
        // make sure course context exists
        get_context_instance(CONTEXT_COURSE, $newid);

        // Site created, add blocks for it
        $page = page_create_object(PAGE_COURSE_VIEW, $newid);
        blocks_repopulate_page($page); // Return value not checked because you can always edit later

        $cat = new object();
        $cat->name = get_string('miscellaneous');
        $cat->depth = 1;
        if (!$catid = insert_record('course_categories', $cat)) {
            error("Serious Error! Could not set up a default course category!");
        }
        // make sure category context exists
        get_context_instance(CONTEXT_COURSECAT, $catid);
        mark_context_dirty('/'.SYSCONTEXTID);

        redirect('index.php');
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
            $pageblocks=blocks_get_by_page($page);
            blocks_execute_action($page, $pageblocks, 'add', (int)$admintree->id, false, false);
            if ($admintreeinstance = get_record('block_instance', 'pagetype', $page->type, 'pageid', SITEID, 'blockid', $admintree->id)) {
                $pageblocks=blocks_get_by_page($page); // Needs to be re-got, since has just changed
                blocks_execute_action($page, $pageblocks, 'moveleft', $admintreeinstance, false, false);
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
        if (! $guest = create_guest_record()) {
            notify("Could not create guest user record !!!");
        }
    }


/// Set up the admin user
    if (empty($CFG->rolesactive)) {
        build_context_path(); // just in case - should not be needed
        create_admin_user();
    }

/// Check for valid admin user - no guest autologin
    require_login(0, false);

    $context = get_context_instance(CONTEXT_SYSTEM);

    require_capability('moodle/site:config', $context);

/// check that site is properly customized
    if (empty($site->shortname)) {
        // probably new installation - lets return to frontpage after this step
        // remove settings that we want uninitialised
        unset_config('registerauth');
        redirect('upgradesettings.php?return=site');
    }

/// Check if we are returning from moodle.org registration and if so, we mark that fact to remove reminders

    if (!empty($id)) {
        if ($id == $CFG->siteidentifier) {
            set_config('registered', time());
        }
    }

    $adminroot =& admin_get_root();

/// Check if there are any new admin settings which have still yet to be set
    if (any_new_admin_settings($adminroot)){
        redirect('upgradesettings.php');
    }

/// Everything should now be set up, and the user is an admin

/// Print default admin page with notifications.

    admin_externalpage_setup('adminnotifications');
    admin_externalpage_print_header();

/// Deprecated database! Warning!!
    if (!empty($CFG->migrated_to_new_db)) {
        print_box(print_string('dbmigrationdeprecateddb', 'admin'), 'generalbox adminwarning');
    }

/// Check for any special upgrades that might need to be run
    if (!empty($CFG->upgrade)) {
        print_box(get_string("upgrade$CFG->upgrade", "admin", "$CFG->wwwroot/$CFG->admin/upgrade$CFG->upgrade.php"));
    }

    if (ini_get_bool('register_globals')) {
        print_box(get_string('globalswarning', 'admin'), 'generalbox adminwarning');
    }

    if (is_dataroot_insecure()) {
        print_box(get_string('datarootsecuritywarning', 'admin', $CFG->dataroot), 'generalbox adminwarning');
    }

    if (defined('WARN_DISPLAY_ERRORS_ENABLED')) {
        print_box(get_string('displayerrorswarning', 'admin'), 'generalbox adminwarning');
    }

    if (substr($CFG->wwwroot, -1) == '/') {
        print_box(get_string('cfgwwwrootslashwarning', 'admin'), 'generalbox adminwarning');
    }
    if (strpos($ME, $CFG->httpswwwroot.'/') === false) {
        print_box(get_string('cfgwwwrootwarning', 'admin'), 'generalbox adminwarning');
    }

/// If no recently cron run
    $lastcron = get_field_sql('SELECT max(lastcron) FROM ' . $CFG->prefix . 'modules');
    if (time() - $lastcron > 3600 * 24) {
        $strinstallation = get_string('installation', 'install');
        $helpbutton = helpbutton('install', $strinstallation, 'moodle', true, false, '', true);
        print_box(get_string('cronwarning', 'admin')."&nbsp;".$helpbutton, 'generalbox adminwarning');
    }

/// Print multilang upgrade notice if needed
    if (empty($CFG->filter_multilang_converted)) {
        print_box(get_string('multilangupgradenotice', 'admin'), 'generalbox adminwarning');
    }

/// Alert if we are currently in maintenance mode
    if (file_exists($CFG->dataroot.'/1/maintenance.html')) {
        print_box(get_string('sitemaintenancewarning', 'admin'), 'generalbox adminwarning');
    }


/// Print slightly annoying registration button
    $options = array();
    $options['sesskey'] = $USER->sesskey;
    print_box_start('generalbox adminwarning');
    if(!isset($CFG->registered)) {
       print_string('pleaseregister', 'admin');
    }
    else { /* if (isset($CFG->registered) && $CFG->registered < (time() - 3600*24*30*6)) { */
       print_string('pleaserefreshregistration', 'admin', userdate($CFG->registered));
    }
    print_single_button('register.php', $options, get_string('registration'));
    print_box_end();


    //////////////////////////////////////////////////////////////////////////////////////////////////
    ////  IT IS ILLEGAL AND A VIOLATION OF THE GPL TO HIDE, REMOVE OR MODIFY THIS COPYRIGHT NOTICE ///
    $copyrighttext = '<a href="http://moodle.org/">Moodle</a> '.
                     '<a href="http://docs.moodle.org/en/Release" title="'.$CFG->version.'">'.$CFG->release.'</a><br />'.
                     'Copyright &copy; 1999 onwards, Martin Dougiamas<br />'.
                     'and <a href="http://docs.moodle.org/en/Credits">many other contributors</a>.<br />'.
                     '<a href="http://docs.moodle.org/en/License">GNU Public License</a>';
    print_box($copyrighttext, 'copyright');
    //////////////////////////////////////////////////////////////////////////////////////////////////

    admin_externalpage_print_footer();

?>
