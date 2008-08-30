<?php // $Id$

/// Check that config.php exists, if not then call the install script
    if (!file_exists('../config.php')) {
        header('Location: ../install.php');
        die;
    }

/// Check that PHP is of a sufficient version
/// Moved here because older versions do not allow while(@ob_end_clean());
    if (version_compare(phpversion(), "5.2.4") < 0) {
        $phpversion = phpversion();
        echo "Sorry, Moodle requires PHP 5.2.4 or later (currently using version $phpversion)";
        die;
    }

/// try to flush everything all the time
    @ob_implicit_flush(true);
    while(@ob_end_clean()); // ob_end_flush prevents sending of headers


    require('../config.php');
    require_once($CFG->libdir.'/adminlib.php');        // Contains various admin-only functions

    $id             = optional_param('id', '', PARAM_TEXT);
    $confirmupgrade = optional_param('confirmupgrade', 0, PARAM_BOOL);
    $confirmrelease = optional_param('confirmrelease', 0, PARAM_BOOL);
    $confirmplugins = optional_param('confirmplugincheck', 0, PARAM_BOOL);
    $agreelicense   = optional_param('agreelicense', 0, PARAM_BOOL);
    $autopilot      = optional_param('autopilot', 0, PARAM_BOOL);

/// set install/upgrade autocontinue session flag
    if ($autopilot) {
        $SESSION->installautopilot = $autopilot;
    }

/// Check some PHP server settings

    $documentationlink = '<a href="http://docs.moodle.org/en/Installation">Installation docs</a>';

    if (ini_get_bool('session.auto_start')) {
        print_error('phpvaroff', 'debug', '', (object)array('name'=>'session.auto_start', 'link'=>$documentationlink));
    }

    if (ini_get_bool('magic_quotes_runtime')) {
        print_error('phpvaroff', 'debug', '', (object)array('name'=>'magic_quotes_runtime', 'link'=>$documentationlink));
    }

    if (!ini_get_bool('file_uploads')) {
        print_error('phpvaron', 'debug', '', (object)array('name'=>'file_uploads', 'link'=>$documentationlink));
    }

/// Check that config.php has been edited

    if ($CFG->wwwroot == "http://example.com/moodle") {
        print_error('configmoodle', 'debug');
    }


/// Check settings in config.php

    $dirroot = dirname(realpath("../index.php"));
    if (!empty($dirroot) and $dirroot != $CFG->dirroot) {
        print_error('fixsetting', 'debug', '', (object)array('current'=>$CFG->dirroot, 'found'=>$dirroot));
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

    $version = null;
    $release = null;
    include("$CFG->dirroot/version.php");       // defines $version and $release

    if (!$version or !$release) {
        print_error('withoutversion', 'debug'); // without version, stop
    }

/// Check if the main tables have been installed yet or not.
    if (!$tables = $DB->get_tables() ) {    // No tables yet at all.
        $maintables = false;

    } else {                                 // Check for missing main tables
        $maintables = true;
        $mtables = array('config', 'course', 'groupings'); // some tables used in 1.9 and 2.0, preferable something from the start and end of install.xml 
        foreach ($mtables as $mtable) {
            if (!in_array($mtable, $tables)) {
                $maintables = false;
                break;
            }
        }
    }
    unset($mtables);
    unset($tables);

    if (!$maintables) {
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
        $DB->set_debug(true);

        if (!$DB->setup_is_unicodedb()) {
            if (!$DB->change_db_encoding()) {
                // If could not convert successfully, throw error, and prevent installation
                print_error('unicoderequired', 'admin');
            }
        }

        $DB->get_manager()->install_from_xmldb_file("$CFG->libdir/db/install.xml");

    /// Continue with the instalation

        // Install the roles system.
        moodle_install_roles();

        // Install core event handlers
        events_update_definition();

        // Install core message providers
        message_update_providers();
	message_update_providers('message');

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

        // store main version
        if (!set_config('version', $version)) {
            print_error('cannotupdateversion', 'debug');
        }

        // Write default settings unconditionally (i.e. even if a setting is already set, overwrite it)
        // (this should only have any effect during initial install).
        admin_apply_default_settings(NULL, true);

        notify($strdatabasesuccess, 'notifysuccess');

        /// do not show certificates in log ;-) 
        $DB->set_debug(false);

        // hack - set up mnet
        require_once $CFG->dirroot.'/mnet/lib.php';

        print_continue('index.php');
        print_footer('none');

        die;
    }


/// Check version of Moodle code on disk compared with database
/// and upgrade if possible.

    $stradministration = get_string('administration');

    if (empty($CFG->version)) {
        print_error('missingconfigversion', 'debug');
    }

    if ($version > $CFG->version) {  // upgrade

        require_once($CFG->libdir.'/db/upgrade.php'); // Defines upgrades
        require_once($CFG->libdir.'/db/upgradelib.php');   // Upgrade-related functions

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
        } elseif (empty($confirmplugins)) {
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
            $DB->set_debug(true);
        /// Launch the old main upgrade (if exists)
            $status = true;
            if (function_exists('main_upgrade')) {
                $status = main_upgrade($CFG->version);
            }
        /// If succesful and exists launch the new main upgrade (XMLDB), called xmldb_main_upgrade
            if ($status && function_exists('xmldb_main_upgrade')) {
                $status = xmldb_main_upgrade($CFG->version);
            }
            $DB->set_debug(false);
        /// If successful, continue upgrading roles and setting everything properly
            if ($status) {
                if (!update_capabilities()) {
                    print_error('cannotupgradecapabilities', 'debug');
                }

                // Update core events
                events_update_definition();

                // Update core message providers
                message_update_providers();
		message_update_providers('message');

                if (set_config("version", $version)) {
                    remove_dir($CFG->dataroot . '/cache', true); // flush cache
                    notify($strdatabasesuccess, "green");
                    print_continue("upgradesettings.php");
                    print_footer('none');
                    exit;
                } else {
                    print_error('cannotupdateversion', 'debug');
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

/// Updated human-readable release version if necessary

    if ($release <> $CFG->release) {  // Update the release version
        if (!set_config("release", $release)) {
            print_error("cannotupdaterelease", 'debug');
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

/// Check all quiz report plugins and upgrade if necessary
    upgrade_plugins('quizreport', 'mod/quiz/report', "$CFG->wwwroot/$CFG->admin/index.php");

/// Check all portfolio plugins and upgrade if necessary
    upgrade_plugins('portfolio', 'portfolio/type', "$CFG->wwwroot/$CFG->admin/index.php");

/// Check all progress tracker plugins and upgrade if necessary
    upgrade_plugins('trackerexport', 'tracker/export', "$CFG->wwwroot/$CFG->admin/index.php");
    upgrade_plugins('trackerimport', 'tracker/import', "$CFG->wwwroot/$CFG->admin/index.php");
    upgrade_plugins('trackerreport', 'tracker/report', "$CFG->wwwroot/$CFG->admin/index.php");

/// just make sure upgrade logging is properly terminated
    upgrade_log_finish();

    unset($SESSION->installautopilot);

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

        if (!$newid = $DB->insert_record('course', $newsite)) {
            print_error('cannotsetupsite', 'error');
        }
        // make sure course context exists
        get_context_instance(CONTEXT_COURSE, $newid);

        // Site created, add blocks for it
        $page = page_create_object(PAGE_COURSE_VIEW, $newid);
        blocks_repopulate_page($page); // Return value not checked because you can always edit later

        // create default course category
        $cat = get_course_category();

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
        if ($admintree = $DB->get_record('block', array('name'=>'admin_tree'))) {
            $page = page_create_object(PAGE_COURSE_VIEW, SITEID);
            $pageblocks=blocks_get_by_page($page);
            blocks_execute_action($page, $pageblocks, 'add', (int)$admintree->id, false, false);
            if ($admintreeinstance = $DB->get_record('block_instance', array('pagetype'=>$page->type, 'pageid'=>SITEID, 'blockid'=>$admintree->id))) {
                $pageblocks=blocks_get_by_page($page);
                blocks_execute_action($page, $pageblocks, 'moveleft', $admintreeinstance, false, false);
            }
        }

        set_config('adminblocks_initialised', 1);
    }

/// Define the unique site ID code if it isn't already
    if (empty($CFG->siteidentifier)) {    // Unique site identification code
        set_config('siteidentifier', random_string(32).$_SERVER['HTTP_HOST']);
    }

/// ugly hack - if mnet is not initialised include the mnet lib, it adds needed mnet records and configures config options  
///             we should not do such crazy stuff in lib functions!!!
    if (empty($CFG->mnet_localhost_id)) {
        require_once $CFG->dirroot.'/mnet/lib.php';
    }

/// Check if the guest user exists.  If not, create one.
    if (!$DB->record_exists('user', array('username'=>'guest'))) {
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

/// setup critical warnings before printing admin tree block
    $insecuredataroot         = is_dataroot_insecure(true);
    $register_globals_enabled = ini_get_bool('register_globals'); 

    $SESSION->admin_critical_warning = ($register_globals_enabled || $insecuredataroot==INSECURE_DATAROOT_ERROR); 

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

    if ($register_globals_enabled) {
        print_box(get_string('globalswarning', 'admin'), 'generalbox adminerror');
    }

    if ($insecuredataroot == INSECURE_DATAROOT_WARNING) {
        print_box(get_string('datarootsecuritywarning', 'admin', $CFG->dataroot), 'generalbox adminwarning');
    } else if ($insecuredataroot == INSECURE_DATAROOT_ERROR) {
        print_box(get_string('datarootsecurityerror', 'admin', $CFG->dataroot), 'generalbox adminerror');
        
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
    $lastcron = $DB->get_field_sql('SELECT MAX(lastcron) FROM {modules}');
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
