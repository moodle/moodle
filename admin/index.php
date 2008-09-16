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

    upgrade_db($version, $release);


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
