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
 * Main administration script.
 *
 * @package    core
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Check that config.php exists, if not then call the install script
if (!file_exists('../config.php')) {
    header('Location: ../install.php');
    die();
}

// Check that PHP is of a sufficient version as soon as possible
if (version_compare(phpversion(), '5.3.2') < 0) {
    $phpversion = phpversion();
    // do NOT localise - lang strings would not work here and we CAN NOT move it to later place
    echo "Moodle 2.1 or later requires at least PHP 5.3.2 (currently using version $phpversion).<br />";
    echo "Please upgrade your server software or install older Moodle version.";
    die();
}

// make sure iconv is available and actually works
if (!function_exists('iconv')) {
    // this should not happen, this must be very borked install
    echo 'Moodle requires the iconv PHP extension. Please install or enable the iconv extension.';
    die();
}

define('NO_OUTPUT_BUFFERING', true);

require('../config.php');
require_once($CFG->libdir.'/adminlib.php');    // various admin-only functions
require_once($CFG->libdir.'/upgradelib.php');  // general upgrade/install related functions
require_once($CFG->libdir.'/pluginlib.php');   // available updates notifications

$id             = optional_param('id', '', PARAM_TEXT);
$confirmupgrade = optional_param('confirmupgrade', 0, PARAM_BOOL);
$confirmrelease = optional_param('confirmrelease', 0, PARAM_BOOL);
$confirmplugins = optional_param('confirmplugincheck', 0, PARAM_BOOL);
$showallplugins = optional_param('showallplugins', 0, PARAM_BOOL);
$agreelicense   = optional_param('agreelicense', 0, PARAM_BOOL);
$fetchupdates   = optional_param('fetchupdates', 0, PARAM_BOOL);

// Check some PHP server settings

$PAGE->set_url('/admin/index.php');
$PAGE->set_pagelayout('admin'); // Set a default pagelayout

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

if (is_float_problem()) {
    print_error('phpfloatproblem', 'admin', '', $documentationlink);
}

// Set some necessary variables during set-up to avoid PHP warnings later on this page
if (!isset($CFG->release)) {
    $CFG->release = '';
}
if (!isset($CFG->version)) {
    $CFG->version = '';
}
if (!isset($CFG->branch)) {
    $CFG->branch = '';
}

$version = null;
$release = null;
$branch = null;
require("$CFG->dirroot/version.php");       // defines $version, $release, $branch and $maturity
$CFG->target_release = $release;            // used during installation and upgrades

if (!$version or !$release) {
    print_error('withoutversion', 'debug'); // without version, stop
}

if (!core_tables_exist()) {
    $PAGE->set_pagelayout('maintenance');
    $PAGE->set_popup_notification_allowed(false);

    // fake some settings
    $CFG->docroot = 'http://docs.moodle.org';

    $strinstallation = get_string('installation', 'install');

    // remove current session content completely
    session_get_instance()->terminate_current();

    if (empty($agreelicense)) {
        $strlicense = get_string('license');

        $PAGE->navbar->add($strlicense);
        $PAGE->set_title($strinstallation.' - Moodle '.$CFG->target_release);
        $PAGE->set_heading($strinstallation);
        $PAGE->set_cacheable(false);

        $output = $PAGE->get_renderer('core', 'admin');
        echo $output->install_licence_page();
        die();
    }
    if (empty($confirmrelease)) {
        require_once($CFG->libdir.'/environmentlib.php');
        list($envstatus, $environment_results) = check_moodle_environment(normalize_version($release), ENV_SELECT_RELEASE);
        $strcurrentrelease = get_string('currentrelease');

        $PAGE->navbar->add($strcurrentrelease);
        $PAGE->set_title($strinstallation);
        $PAGE->set_heading($strinstallation . ' - Moodle ' . $CFG->target_release);
        $PAGE->set_cacheable(false);

        $output = $PAGE->get_renderer('core', 'admin');
        echo $output->install_environment_page($maturity, $envstatus, $environment_results, $release);
        die();
    }

    // check plugin dependencies
    $failed = array();
    if (!plugin_manager::instance()->all_plugins_ok($version, $failed)) {
        $PAGE->navbar->add(get_string('pluginscheck', 'admin'));
        $PAGE->set_title($strinstallation);
        $PAGE->set_heading($strinstallation . ' - Moodle ' . $CFG->target_release);

        $output = $PAGE->get_renderer('core', 'admin');
        $url = new moodle_url('/admin/index.php', array('agreelicense' => 1, 'confirmrelease' => 1, 'lang' => $CFG->lang));
        echo $output->unsatisfied_dependencies_page($version, $failed, $url);
        die();
    }
    unset($failed);

    //TODO: add a page with list of non-standard plugins here

    $strdatabasesetup = get_string('databasesetup');
    upgrade_init_javascript();

    $PAGE->navbar->add($strdatabasesetup);
    $PAGE->set_title($strinstallation.' - Moodle '.$CFG->target_release);
    $PAGE->set_heading($strinstallation);
    $PAGE->set_cacheable(false);

    $output = $PAGE->get_renderer('core', 'admin');
    echo $output->header();

    if (!$DB->setup_is_unicodedb()) {
        if (!$DB->change_db_encoding()) {
            // If could not convert successfully, throw error, and prevent installation
            print_error('unicoderequired', 'admin');
        }
    }

    install_core($version, true);
}


// Check version of Moodle code on disk compared with database
// and upgrade if possible.

$stradministration = get_string('administration');
$PAGE->set_context(context_system::instance());

if (empty($CFG->version)) {
    print_error('missingconfigversion', 'debug');
}

if ($version > $CFG->version) {  // upgrade
    purge_all_caches();

    $PAGE->set_pagelayout('maintenance');
    $PAGE->set_popup_notification_allowed(false);

    if (upgrade_stale_php_files_present()) {
        $PAGE->set_title($stradministration);
        $PAGE->set_cacheable(false);

        $output = $PAGE->get_renderer('core', 'admin');
        echo $output->upgrade_stale_php_files_page();
        die();
    }

    if (empty($confirmupgrade)) {
        $a = new stdClass();
        $a->oldversion = "$CFG->release ($CFG->version)";
        $a->newversion = "$release ($version)";
        $strdatabasechecking = get_string('databasechecking', '', $a);

        $PAGE->set_title($stradministration);
        $PAGE->set_heading($strdatabasechecking);
        $PAGE->set_cacheable(false);

        $output = $PAGE->get_renderer('core', 'admin');
        echo $output->upgrade_confirm_page($a->newversion, $maturity);
        die();

    } else if (empty($confirmrelease)){
        require_once($CFG->libdir.'/environmentlib.php');
        list($envstatus, $environment_results) = check_moodle_environment($release, ENV_SELECT_RELEASE);
        $strcurrentrelease = get_string('currentrelease');

        $PAGE->navbar->add($strcurrentrelease);
        $PAGE->set_title($strcurrentrelease);
        $PAGE->set_heading($strcurrentrelease);
        $PAGE->set_cacheable(false);

        $output = $PAGE->get_renderer('core', 'admin');
        echo $output->upgrade_environment_page($release, $envstatus, $environment_results);
        die();

    } else if (empty($confirmplugins)) {
        $strplugincheck = get_string('plugincheck');

        $PAGE->navbar->add($strplugincheck);
        $PAGE->set_title($strplugincheck);
        $PAGE->set_heading($strplugincheck);
        $PAGE->set_cacheable(false);

        $reloadurl = new moodle_url('/admin/index.php', array('confirmupgrade' => 1, 'confirmrelease' => 1));

        // check plugin dependencies first
        $failed = array();
        if (!plugin_manager::instance()->all_plugins_ok($version, $failed)) {
            $output = $PAGE->get_renderer('core', 'admin');
            echo $output->unsatisfied_dependencies_page($version, $failed, $reloadurl);
            die();
        }
        unset($failed);

        if ($fetchupdates) {
            // no sesskey support guaranteed here
            if (empty($CFG->disableupdatenotifications)) {
                available_update_checker::instance()->fetch();
            }
            redirect($reloadurl);
        }

        $output = $PAGE->get_renderer('core', 'admin');

        $deployer = available_update_deployer::instance();
        if ($deployer->enabled()) {
            $deployer->initialize($reloadurl, $reloadurl);

            $deploydata = $deployer->submitted_data();
            if (!empty($deploydata)) {
                echo $output->upgrade_plugin_confirm_deploy_page($deployer, $deploydata);
                die();
            }
        }

        echo $output->upgrade_plugin_check_page(plugin_manager::instance(), available_update_checker::instance(),
                $version, $showallplugins, $reloadurl,
                new moodle_url('/admin/index.php', array('confirmupgrade'=>1, 'confirmrelease'=>1, 'confirmplugincheck'=>1)));
        die();

    } else {
        // Launch main upgrade
        upgrade_core($version, true);
    }
} else if ($version < $CFG->version) {
    // better stop here, we can not continue with plugin upgrades or anything else
    throw new moodle_exception('downgradedcore', 'error', new moodle_url('/admin/'));
}

// Updated human-readable release version if necessary
if ($release <> $CFG->release) {  // Update the release version
    set_config('release', $release);
}

if ($branch <> $CFG->branch) {  // Update the branch
    set_config('branch', $branch);
}

if (moodle_needs_upgrading()) {
    if (!$PAGE->headerprinted) {
        // means core upgrade or installation was not already done
        if (!$confirmplugins) {
            $strplugincheck = get_string('plugincheck');

            $PAGE->set_pagelayout('maintenance');
            $PAGE->set_popup_notification_allowed(false);
            $PAGE->navbar->add($strplugincheck);
            $PAGE->set_title($strplugincheck);
            $PAGE->set_heading($strplugincheck);
            $PAGE->set_cacheable(false);

            if ($fetchupdates) {
                // no sesskey support guaranteed here
                available_update_checker::instance()->fetch();
                redirect($PAGE->url);
            }

            $output = $PAGE->get_renderer('core', 'admin');

            $deployer = available_update_deployer::instance();
            if ($deployer->enabled()) {
                $deployer->initialize($PAGE->url, $PAGE->url);

                $deploydata = $deployer->submitted_data();
                if (!empty($deploydata)) {
                    echo $output->upgrade_plugin_confirm_deploy_page($deployer, $deploydata);
                    die();
                }
            }

            // check plugin dependencies first
            $failed = array();
            if (!plugin_manager::instance()->all_plugins_ok($version, $failed)) {
                echo $output->unsatisfied_dependencies_page($version, $failed, $PAGE->url);
                die();
            }
            unset($failed);

            // dependencies check passed, let's rock!
            echo $output->upgrade_plugin_check_page(plugin_manager::instance(), available_update_checker::instance(),
                    $version, $showallplugins,
                    new moodle_url($PAGE->url),
                    new moodle_url('/admin/index.php', array('confirmplugincheck'=>1)));
            die();
        }
    }
    // install/upgrade all plugins and other parts
    upgrade_noncore(true);
}

// If this is the first install, indicate that this site is fully configured
// except the admin password
if (during_initial_install()) {
    set_config('rolesactive', 1); // after this, during_initial_install will return false.
    set_config('adminsetuppending', 1);
    // we need this redirect to setup proper session
    upgrade_finished("index.php?sessionstarted=1&amp;lang=$CFG->lang");
}

// make sure admin user is created - this is the last step because we need
// session to be working properly in order to edit admin account
 if (!empty($CFG->adminsetuppending)) {
    $sessionstarted = optional_param('sessionstarted', 0, PARAM_BOOL);
    if (!$sessionstarted) {
        redirect("index.php?sessionstarted=1&lang=$CFG->lang");
    } else {
        $sessionverify = optional_param('sessionverify', 0, PARAM_BOOL);
        if (!$sessionverify) {
            $SESSION->sessionverify = 1;
            redirect("index.php?sessionstarted=1&sessionverify=1&lang=$CFG->lang");
        } else {
            if (empty($SESSION->sessionverify)) {
                print_error('installsessionerror', 'admin', "index.php?sessionstarted=1&lang=$CFG->lang");
            }
            unset($SESSION->sessionverify);
        }
    }

    // Cleanup SESSION to make sure other code does not complain in the future.
    unset($SESSION->has_timed_out);
    unset($SESSION->wantsurl);

    // at this stage there can be only one admin unless more were added by install - users may change username, so do not rely on that
    $adminids = explode(',', $CFG->siteadmins);
    $adminuser = get_complete_user_data('id', reset($adminids));

    if ($adminuser->password === 'adminsetuppending') {
        // prevent installation hijacking
        if ($adminuser->lastip !== getremoteaddr()) {
            print_error('installhijacked', 'admin');
        }
        // login user and let him set password and admin details
        $adminuser->newadminuser = 1;
        complete_user_login($adminuser);
        redirect("$CFG->wwwroot/user/editadvanced.php?id=$adminuser->id"); // Edit thyself

    } else {
        unset_config('adminsetuppending');
    }

} else {
    // just make sure upgrade logging is properly terminated
    upgrade_finished('upgradesettings.php');
}

// Check for valid admin user - no guest autologin
require_login(0, false);
$context = context_system::instance();
require_capability('moodle/site:config', $context);

// check that site is properly customized
$site = get_site();
if (empty($site->shortname)) {
    // probably new installation - lets return to frontpage after this step
    // remove settings that we want uninitialised
    unset_config('registerauth');
    redirect('upgradesettings.php?return=site');
}

// Check if we are returning from moodle.org registration and if so, we mark that fact to remove reminders
if (!empty($id) and $id == $CFG->siteidentifier) {
    set_config('registered', time());
}

// setup critical warnings before printing admin tree block
$insecuredataroot = is_dataroot_insecure(true);
$SESSION->admin_critical_warning = ($insecuredataroot==INSECURE_DATAROOT_ERROR);

$adminroot = admin_get_root();

// Check if there are any new admin settings which have still yet to be set
if (any_new_admin_settings($adminroot)){
    redirect('upgradesettings.php');
}

// Everything should now be set up, and the user is an admin

// Print default admin page with notifications.
$errorsdisplayed = defined('WARN_DISPLAY_ERRORS_ENABLED');

$lastcron = $DB->get_field_sql('SELECT MAX(lastcron) FROM {modules}');
$cronoverdue = ($lastcron < time() - 3600 * 24);
$dbproblems = $DB->diagnose();
$maintenancemode = !empty($CFG->maintenance_enabled);

// Available updates for Moodle core
$updateschecker = available_update_checker::instance();
$availableupdates = array();
$availableupdates['core'] = $updateschecker->get_update_info('core',
    array('minmaturity' => $CFG->updateminmaturity, 'notifybuilds' => $CFG->updatenotifybuilds));

// Available updates for contributed plugins
$pluginman = plugin_manager::instance();
foreach ($pluginman->get_plugins() as $plugintype => $plugintypeinstances) {
    foreach ($plugintypeinstances as $pluginname => $plugininfo) {
        if (!empty($plugininfo->availableupdates)) {
            foreach ($plugininfo->availableupdates as $pluginavailableupdate) {
                if ($pluginavailableupdate->version > $plugininfo->versiondisk) {
                    if (!isset($availableupdates[$plugintype.'_'.$pluginname])) {
                        $availableupdates[$plugintype.'_'.$pluginname] = array();
                    }
                    $availableupdates[$plugintype.'_'.$pluginname][] = $pluginavailableupdate;
                }
            }
        }
    }
}

// The timestamp of the most recent check for available updates
$availableupdatesfetch = $updateschecker->get_last_timefetched();

$buggyiconvnomb = (!function_exists('mb_convert_encoding') and @iconv('UTF-8', 'UTF-8//IGNORE', '100'.chr(130).'€') !== '100€');
//check if the site is registered on Moodle.org
$registered = $DB->count_records('registration_hubs', array('huburl' => HUB_MOODLEORGHUBURL, 'confirmed' => 1));

admin_externalpage_setup('adminnotifications');

if ($fetchupdates) {
    require_sesskey();
    $updateschecker->fetch();
    redirect($PAGE->url);
}

$output = $PAGE->get_renderer('core', 'admin');
echo $output->admin_notifications_page($maturity, $insecuredataroot, $errorsdisplayed,
        $cronoverdue, $dbproblems, $maintenancemode, $availableupdates, $availableupdatesfetch, $buggyiconvnomb,
        $registered);
