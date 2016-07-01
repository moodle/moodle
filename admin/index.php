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
if (version_compare(phpversion(), '5.4.4') < 0) {
    $phpversion = phpversion();
    // do NOT localise - lang strings would not work here and we CAN NOT move it to later place
    echo "Moodle 2.7 or later requires at least PHP 5.4.4 (currently using version $phpversion).<br />";
    echo "Please upgrade your server software or install older Moodle version.";
    die();
}

// make sure iconv is available and actually works
if (!function_exists('iconv')) {
    // this should not happen, this must be very borked install
    echo 'Moodle requires the iconv PHP extension. Please install or enable the iconv extension.';
    die();
}

// Make sure php5-json is available.
if (!function_exists('json_encode') || !function_exists('json_decode')) {
    // This also shouldn't happen.
    echo 'Moodle requires the json PHP extension. Please install or enable the json extension.';
    die();
}

// Make sure xml extension is available.
if (!extension_loaded('xml')) {
    echo 'Moodle requires the xml PHP extension. Please install or enable the xml extension.';
    die();
}

define('NO_OUTPUT_BUFFERING', true);

if (isset($_POST['upgradekey'])) {
    // Before you start reporting issues about the collision attacks against
    // SHA-1, you should understand that we are not actually attempting to do
    // any cryptography here. This is hashed purely so that the key is not
    // that apparent in the address bar itself. Anyone who catches the HTTP
    // traffic can immediately use it as a valid admin key.
    header('Location: index.php?cache=0&upgradekeyhash='.sha1($_POST['upgradekey']));
    die();
}

if ((isset($_GET['cache']) and $_GET['cache'] === '0')
        or (isset($_POST['cache']) and $_POST['cache'] === '0')
        or (!isset($_POST['cache']) and !isset($_GET['cache']) and empty($_GET['sesskey']) and empty($_POST['sesskey']))) {
    // Prevent caching at all cost when visiting this page directly,
    // we redirect to self once we known no upgrades are necessary.
    // Note: $_GET and $_POST are used here intentionally because our param cleaning is not loaded yet.
    // Note2: the sesskey is present in all block editing hacks, we can not redirect there, so enable caching.
    define('CACHE_DISABLE_ALL', true);

    // Force OPcache reset if used, we do not want any stale caches
    // when detecting if upgrade necessary or when running upgrade.
    if (function_exists('opcache_reset')) {
        opcache_reset();
    }
    $cache = 0;

} else {
    $cache = 1;
}

require('../config.php');

// Invalidate the cache of version.php in any circumstances to help core_component
// detecting if the version has changed and component cache should be reset.
if (function_exists('opcache_invalidate')) {
    opcache_invalidate($CFG->dirroot . '/version.php', true);
}
// Make sure the component cache gets rebuilt if necessary, any method that
// indirectly calls the protected init() method is good here.
core_component::get_core_subsystems();

require_once($CFG->libdir.'/adminlib.php');    // various admin-only functions
require_once($CFG->libdir.'/upgradelib.php');  // general upgrade/install related functions

$confirmupgrade = optional_param('confirmupgrade', 0, PARAM_BOOL); // Core upgrade confirmed?
$confirmrelease = optional_param('confirmrelease', 0, PARAM_BOOL); // Core release info and server checks confirmed?
$confirmplugins = optional_param('confirmplugincheck', 0, PARAM_BOOL); // Plugins check page confirmed?
$showallplugins = optional_param('showallplugins', 0, PARAM_BOOL); // Show all plugins on the plugins check page?
$agreelicense = optional_param('agreelicense', 0, PARAM_BOOL); // GPL license confirmed for installation?
$fetchupdates = optional_param('fetchupdates', 0, PARAM_BOOL); // Should check for available updates?
$newaddonreq = optional_param('installaddonrequest', null, PARAM_RAW); // Plugin installation requested at moodle.org/plugins.
$upgradekeyhash = optional_param('upgradekeyhash', null, PARAM_ALPHANUM); // Hash of provided upgrade key.
$installdep = optional_param('installdep', null, PARAM_COMPONENT); // Install given missing dependency (required plugin).
$installdepx = optional_param('installdepx', false, PARAM_BOOL); // Install all missing dependencies.
$confirminstalldep = optional_param('confirminstalldep', false, PARAM_BOOL); // Installing dependencies confirmed.
$abortinstall = optional_param('abortinstall', null, PARAM_COMPONENT); // Cancel installation of the given new plugin.
$abortinstallx = optional_param('abortinstallx', null, PARAM_BOOL); // Cancel installation of all new plugins.
$confirmabortinstall = optional_param('confirmabortinstall', false, PARAM_BOOL); // Installation cancel confirmed.
$abortupgrade = optional_param('abortupgrade', null, PARAM_COMPONENT); // Cancel upgrade of the given existing plugin.
$abortupgradex = optional_param('abortupgradex', null, PARAM_BOOL); // Cancel upgrade of all upgradable plugins.
$confirmabortupgrade = optional_param('confirmabortupgrade', false, PARAM_BOOL); // Upgrade cancel confirmed.
$installupdate = optional_param('installupdate', null, PARAM_COMPONENT); // Install given available update.
$installupdateversion = optional_param('installupdateversion', null, PARAM_INT); // Version of the available update to install.
$installupdatex = optional_param('installupdatex', false, PARAM_BOOL); // Install all available plugin updates.
$confirminstallupdate = optional_param('confirminstallupdate', false, PARAM_BOOL); // Available update(s) install confirmed?

if (!empty($CFG->disableupdateautodeploy)) {
    // Invalidate all requests to install plugins via the admin UI.
    $newaddonreq = null;
    $installdep = null;
    $installdepx = false;
    $abortupgrade = null;
    $abortupgradex = null;
    $installupdate = null;
    $installupdateversion = null;
    $installupdatex = false;
}

// Set up PAGE.
$url = new moodle_url('/admin/index.php');
$url->param('cache', $cache);
if (isset($upgradekeyhash)) {
    $url->param('upgradekeyhash', $upgradekeyhash);
}
$PAGE->set_url($url);
unset($url);

// Are we returning from an add-on installation request at moodle.org/plugins?
if ($newaddonreq and !$cache and empty($CFG->disableupdateautodeploy)) {
    $target = new moodle_url('/admin/tool/installaddon/index.php', array(
        'installaddonrequest' => $newaddonreq,
        'confirm' => 0));
    if (!isloggedin() or isguestuser()) {
        // Login and go the the add-on tool page.
        $SESSION->wantsurl = $target->out();
        redirect(get_login_url());
    }
    redirect($target);
}

$PAGE->set_pagelayout('admin'); // Set a default pagelayout

$documentationlink = '<a href="http://docs.moodle.org/en/Installation">Installation docs</a>';

// Check some PHP server settings

if (ini_get_bool('session.auto_start')) {
    print_error('phpvaroff', 'debug', '', (object)array('name'=>'session.auto_start', 'link'=>$documentationlink));
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
    \core\session\manager::terminate_current();

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
    if (!core_plugin_manager::instance()->all_plugins_ok($version, $failed)) {
        $PAGE->navbar->add(get_string('pluginscheck', 'admin'));
        $PAGE->set_title($strinstallation);
        $PAGE->set_heading($strinstallation . ' - Moodle ' . $CFG->target_release);

        $output = $PAGE->get_renderer('core', 'admin');
        $url = new moodle_url($PAGE->url, array('agreelicense' => 1, 'confirmrelease' => 1, 'lang' => $CFG->lang));
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

if (!$cache) {
    // Do not try to do anything fancy in non-cached mode,
    // this prevents themes from fetching data from non-existent tables.
    $PAGE->set_pagelayout('maintenance');
    $PAGE->set_popup_notification_allowed(false);
}

$stradministration = get_string('administration');
$PAGE->set_context(context_system::instance());

if (empty($CFG->version)) {
    print_error('missingconfigversion', 'debug');
}

// Detect config cache inconsistency, this happens when you switch branches on dev servers.
if ($CFG->version != $DB->get_field('config', 'value', array('name'=>'version'))) {
    purge_all_caches();
    redirect(new moodle_url($PAGE->url), 'Config cache inconsistency detected, resetting caches...');
}

if (!$cache and $version > $CFG->version) {  // upgrade

    $PAGE->set_url(new moodle_url($PAGE->url, array(
        'confirmupgrade' => $confirmupgrade,
        'confirmrelease' => $confirmrelease,
        'confirmplugincheck' => $confirmplugins,
    )));

    check_upgrade_key($upgradekeyhash);

    // Warning about upgrading a test site.
    $testsite = false;
    if (defined('BEHAT_SITE_RUNNING')) {
        $testsite = 'behat';
    }

    // We purge all of MUC's caches here.
    // Caches are disabled for upgrade by CACHE_DISABLE_ALL so we must set the first arg to true.
    // This ensures a real config object is loaded and the stores will be purged.
    // This is the only way we can purge custom caches such as memcache or APC.
    // Note: all other calls to caches will still used the disabled API.
    cache_helper::purge_all(true);
    // We then purge the regular caches.
    purge_all_caches();

    $output = $PAGE->get_renderer('core', 'admin');

    if (upgrade_stale_php_files_present()) {
        $PAGE->set_title($stradministration);
        $PAGE->set_cacheable(false);

        echo $output->upgrade_stale_php_files_page();
        die();
    }

    if (empty($confirmupgrade)) {
        $a = new stdClass();
        $a->oldversion = "$CFG->release (".sprintf('%.2f', $CFG->version).")";
        $a->newversion = "$release (".sprintf('%.2f', $version).")";
        $strdatabasechecking = get_string('databasechecking', '', $a);

        $PAGE->set_title($stradministration);
        $PAGE->set_heading($strdatabasechecking);
        $PAGE->set_cacheable(false);

        echo $output->upgrade_confirm_page($a->newversion, $maturity, $testsite);
        die();

    } else if (empty($confirmrelease)){
        require_once($CFG->libdir.'/environmentlib.php');
        list($envstatus, $environment_results) = check_moodle_environment($release, ENV_SELECT_RELEASE);
        $strcurrentrelease = get_string('currentrelease');

        $PAGE->navbar->add($strcurrentrelease);
        $PAGE->set_title($strcurrentrelease);
        $PAGE->set_heading($strcurrentrelease);
        $PAGE->set_cacheable(false);

        echo $output->upgrade_environment_page($release, $envstatus, $environment_results);
        die();

    } else if (empty($confirmplugins)) {
        $strplugincheck = get_string('plugincheck');

        $PAGE->navbar->add($strplugincheck);
        $PAGE->set_title($strplugincheck);
        $PAGE->set_heading($strplugincheck);
        $PAGE->set_cacheable(false);

        $pluginman = core_plugin_manager::instance();

        // Check for available updates.
        if ($fetchupdates) {
            // No sesskey support guaranteed here, because sessions might not work yet.
            $updateschecker = \core\update\checker::instance();
            if ($updateschecker->enabled()) {
                $updateschecker->fetch();
            }
            redirect($PAGE->url);
        }

        // Cancel all plugin installations.
        if ($abortinstallx) {
            // No sesskey support guaranteed here, because sessions might not work yet.
            $abortables = $pluginman->list_cancellable_installations();
            if ($abortables) {
                if ($confirmabortinstall) {
                    foreach ($abortables as $plugin) {
                        $pluginman->cancel_plugin_installation($plugin->component);
                    }
                    redirect($PAGE->url);
                } else {
                    $continue = new moodle_url($PAGE->url, array('abortinstallx' => $abortinstallx, 'confirmabortinstall' => 1));
                    echo $output->upgrade_confirm_abort_install_page($abortables, $continue);
                    die();
                }
            }
            redirect($PAGE->url);
        }

        // Cancel single plugin installation.
        if ($abortinstall) {
            // No sesskey support guaranteed here, because sessions might not work yet.
            if ($confirmabortinstall) {
                $pluginman->cancel_plugin_installation($abortinstall);
                redirect($PAGE->url);
            } else {
                $continue = new moodle_url($PAGE->url, array('abortinstall' => $abortinstall, 'confirmabortinstall' => 1));
                $abortable = $pluginman->get_plugin_info($abortinstall);
                if ($pluginman->can_cancel_plugin_installation($abortable)) {
                    echo $output->upgrade_confirm_abort_install_page(array($abortable), $continue);
                    die();
                }
                redirect($PAGE->url);
            }
        }

        // Cancel all plugins upgrades (that is, restore archived versions).
        if ($abortupgradex) {
            // No sesskey support guaranteed here, because sessions might not work yet.
            $restorable = $pluginman->list_restorable_archives();
            if ($restorable) {
                upgrade_install_plugins($restorable, $confirmabortupgrade,
                    get_string('cancelupgradehead', 'core_plugin'),
                    new moodle_url($PAGE->url, array('abortupgradex' => 1, 'confirmabortupgrade' => 1))
                );
            }
            redirect($PAGE->url);
        }

        // Cancel single plugin upgrade (that is, install the archived version).
        if ($abortupgrade) {
            // No sesskey support guaranteed here, because sessions might not work yet.
            $restorable = $pluginman->list_restorable_archives();
            if (isset($restorable[$abortupgrade])) {
                $restorable = array($restorable[$abortupgrade]);
                upgrade_install_plugins($restorable, $confirmabortupgrade,
                    get_string('cancelupgradehead', 'core_plugin'),
                    new moodle_url($PAGE->url, array('abortupgrade' => $abortupgrade, 'confirmabortupgrade' => 1))
                );
            }
            redirect($PAGE->url);
        }

        // Install all available missing dependencies.
        if ($installdepx) {
            // No sesskey support guaranteed here, because sessions might not work yet.
            $installable = $pluginman->filter_installable($pluginman->missing_dependencies(true));
            upgrade_install_plugins($installable, $confirminstalldep,
                get_string('dependencyinstallhead', 'core_plugin'),
                new moodle_url($PAGE->url, array('installdepx' => 1, 'confirminstalldep' => 1))
            );
        }

        // Install single available missing dependency.
        if ($installdep) {
            // No sesskey support guaranteed here, because sessions might not work yet.
            $installable = $pluginman->filter_installable($pluginman->missing_dependencies(true));
            if (!empty($installable[$installdep])) {
                $installable = array($installable[$installdep]);
                upgrade_install_plugins($installable, $confirminstalldep,
                    get_string('dependencyinstallhead', 'core_plugin'),
                    new moodle_url($PAGE->url, array('installdep' => $installdep, 'confirminstalldep' => 1))
                );
            }
        }

        // Install all available updates.
        if ($installupdatex) {
            // No sesskey support guaranteed here, because sessions might not work yet.
            $installable = $pluginman->filter_installable($pluginman->available_updates());
            upgrade_install_plugins($installable, $confirminstallupdate,
                get_string('updateavailableinstallallhead', 'core_admin'),
                new moodle_url($PAGE->url, array('installupdatex' => 1, 'confirminstallupdate' => 1))
            );
        }

        // Install single available update.
        if ($installupdate and $installupdateversion) {
            // No sesskey support guaranteed here, because sessions might not work yet.
            if ($pluginman->is_remote_plugin_installable($installupdate, $installupdateversion)) {
                $installable = array($pluginman->get_remote_plugin_info($installupdate, $installupdateversion, true));
                upgrade_install_plugins($installable, $confirminstallupdate,
                    get_string('updateavailableinstallallhead', 'core_admin'),
                    new moodle_url($PAGE->url, array('installupdate' => $installupdate,
                        'installupdateversion' => $installupdateversion, 'confirminstallupdate' => 1)
                    )
                );
            }
        }

        echo $output->upgrade_plugin_check_page(core_plugin_manager::instance(), \core\update\checker::instance(),
                $version, $showallplugins, $PAGE->url, new moodle_url($PAGE->url, array('confirmplugincheck' => 1)));
        die();

    } else {
        // Always verify plugin dependencies!
        $failed = array();
        if (!core_plugin_manager::instance()->all_plugins_ok($version, $failed)) {
            echo $output->unsatisfied_dependencies_page($version, $failed, $PAGE->url);
            die();
        }
        unset($failed);

        // Launch main upgrade.
        upgrade_core($version, true);
    }
} else if ($version < $CFG->version) {
    // better stop here, we can not continue with plugin upgrades or anything else
    throw new moodle_exception('downgradedcore', 'error', new moodle_url('/admin/'));
}

// Updated human-readable release version if necessary
if (!$cache and $release <> $CFG->release) {  // Update the release version
    set_config('release', $release);
}

if (!$cache and $branch <> $CFG->branch) {  // Update the branch
    set_config('branch', $branch);
}

if (!$cache and moodle_needs_upgrading()) {

    $PAGE->set_url(new moodle_url($PAGE->url, array('confirmplugincheck' => $confirmplugins)));

    check_upgrade_key($upgradekeyhash);

    if (!$PAGE->headerprinted) {
        // means core upgrade or installation was not already done

        $pluginman = core_plugin_manager::instance();
        $output = $PAGE->get_renderer('core', 'admin');

        if (!$confirmplugins) {
            $strplugincheck = get_string('plugincheck');

            $PAGE->navbar->add($strplugincheck);
            $PAGE->set_title($strplugincheck);
            $PAGE->set_heading($strplugincheck);
            $PAGE->set_cacheable(false);

            // Check for available updates.
            if ($fetchupdates) {
                require_sesskey();
                $updateschecker = \core\update\checker::instance();
                if ($updateschecker->enabled()) {
                    $updateschecker->fetch();
                }
                redirect($PAGE->url);
            }

            // Cancel all plugin installations.
            if ($abortinstallx) {
                require_sesskey();
                $abortables = $pluginman->list_cancellable_installations();
                if ($abortables) {
                    if ($confirmabortinstall) {
                        foreach ($abortables as $plugin) {
                            $pluginman->cancel_plugin_installation($plugin->component);
                        }
                        redirect($PAGE->url);
                    } else {
                        $continue = new moodle_url($PAGE->url, array('abortinstallx' => $abortinstallx,
                            'confirmabortinstall' => 1));
                        echo $output->upgrade_confirm_abort_install_page($abortables, $continue);
                        die();
                    }
                }
                redirect($PAGE->url);
            }

            // Cancel single plugin installation.
            if ($abortinstall) {
                require_sesskey();
                if ($confirmabortinstall) {
                    $pluginman->cancel_plugin_installation($abortinstall);
                    redirect($PAGE->url);
                } else {
                    $continue = new moodle_url($PAGE->url, array('abortinstall' => $abortinstall, 'confirmabortinstall' => 1));
                    $abortable = $pluginman->get_plugin_info($abortinstall);
                    if ($pluginman->can_cancel_plugin_installation($abortable)) {
                        echo $output->upgrade_confirm_abort_install_page(array($abortable), $continue);
                        die();
                    }
                    redirect($PAGE->url);
                }
            }

            // Cancel all plugins upgrades (that is, restore archived versions).
            if ($abortupgradex) {
                require_sesskey();
                $restorable = $pluginman->list_restorable_archives();
                if ($restorable) {
                    upgrade_install_plugins($restorable, $confirmabortupgrade,
                        get_string('cancelupgradehead', 'core_plugin'),
                        new moodle_url($PAGE->url, array('abortupgradex' => 1, 'confirmabortupgrade' => 1))
                    );
                }
                redirect($PAGE->url);
            }

            // Cancel single plugin upgrade (that is, install the archived version).
            if ($abortupgrade) {
                require_sesskey();
                $restorable = $pluginman->list_restorable_archives();
                if (isset($restorable[$abortupgrade])) {
                    $restorable = array($restorable[$abortupgrade]);
                    upgrade_install_plugins($restorable, $confirmabortupgrade,
                        get_string('cancelupgradehead', 'core_plugin'),
                        new moodle_url($PAGE->url, array('abortupgrade' => $abortupgrade, 'confirmabortupgrade' => 1))
                    );
                }
                redirect($PAGE->url);
            }

            // Install all available missing dependencies.
            if ($installdepx) {
                require_sesskey();
                $installable = $pluginman->filter_installable($pluginman->missing_dependencies(true));
                upgrade_install_plugins($installable, $confirminstalldep,
                    get_string('dependencyinstallhead', 'core_plugin'),
                    new moodle_url($PAGE->url, array('installdepx' => 1, 'confirminstalldep' => 1))
                );
            }

            // Install single available missing dependency.
            if ($installdep) {
                require_sesskey();
                $installable = $pluginman->filter_installable($pluginman->missing_dependencies(true));
                if (!empty($installable[$installdep])) {
                    $installable = array($installable[$installdep]);
                    upgrade_install_plugins($installable, $confirminstalldep,
                        get_string('dependencyinstallhead', 'core_plugin'),
                        new moodle_url($PAGE->url, array('installdep' => $installdep, 'confirminstalldep' => 1))
                    );
                }
            }

            // Install all available updates.
            if ($installupdatex) {
                require_sesskey();
                $installable = $pluginman->filter_installable($pluginman->available_updates());
                upgrade_install_plugins($installable, $confirminstallupdate,
                    get_string('updateavailableinstallallhead', 'core_admin'),
                    new moodle_url($PAGE->url, array('installupdatex' => 1, 'confirminstallupdate' => 1))
                );
            }

            // Install single available update.
            if ($installupdate and $installupdateversion) {
                require_sesskey();
                if ($pluginman->is_remote_plugin_installable($installupdate, $installupdateversion)) {
                    $installable = array($pluginman->get_remote_plugin_info($installupdate, $installupdateversion, true));
                    upgrade_install_plugins($installable, $confirminstallupdate,
                        get_string('updateavailableinstallallhead', 'core_admin'),
                        new moodle_url($PAGE->url, array('installupdate' => $installupdate,
                            'installupdateversion' => $installupdateversion, 'confirminstallupdate' => 1)
                        )
                    );
                }
            }

            // Show plugins info.
            echo $output->upgrade_plugin_check_page($pluginman, \core\update\checker::instance(),
                    $version, $showallplugins,
                    new moodle_url($PAGE->url),
                    new moodle_url($PAGE->url, array('confirmplugincheck' => 1, 'cache' => 0)));
            die();
        }

        // Make sure plugin dependencies are always checked.
        $failed = array();
        if (!$pluginman->all_plugins_ok($version, $failed)) {
            $output = $PAGE->get_renderer('core', 'admin');
            echo $output->unsatisfied_dependencies_page($version, $failed, $PAGE->url);
            die();
        }
        unset($failed);
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

if (has_capability('moodle/site:config', context_system::instance())) {
    if ($fetchupdates) {
        require_sesskey();
        $updateschecker = \core\update\checker::instance();
        if ($updateschecker->enabled()) {
            $updateschecker->fetch();
        }
        redirect(new moodle_url('/admin/index.php', array('cache' => 0)));
    }
}

// Now we can be sure everything was upgraded and caches work fine,
// redirect if necessary to make sure caching is enabled.
if (!$cache) {
    redirect(new moodle_url('/admin/index.php', array('cache' => 1)));
}

// Check for valid admin user - no guest autologin
require_login(0, false);
if (isguestuser()) {
    // Login as real user!
    $SESSION->wantsurl = (string)new moodle_url('/admin/index.php');
    redirect(get_login_url());
}
$context = context_system::instance();

if (!has_capability('moodle/site:config', $context)) {
    // Do not throw exception display an empty page with administration menu if visible for current user.
    $PAGE->set_title($SITE->fullname);
    $PAGE->set_heading($SITE->fullname);
    echo $OUTPUT->header();
    echo $OUTPUT->footer();
    exit;
}

// check that site is properly customized
$site = get_site();
if (empty($site->shortname)) {
    // probably new installation - lets return to frontpage after this step
    // remove settings that we want uninitialised
    unset_config('registerauth');
    unset_config('timezone'); // Force admin to select timezone!
    redirect('upgradesettings.php?return=site');
}

// setup critical warnings before printing admin tree block
$insecuredataroot = is_dataroot_insecure(true);
$SESSION->admin_critical_warning = ($insecuredataroot==INSECURE_DATAROOT_ERROR);

$adminroot = admin_get_root();

// Check if there are any new admin settings which have still yet to be set
if (any_new_admin_settings($adminroot)){
    redirect('upgradesettings.php');
}

// Return to original page that started the plugin uninstallation if necessary.
if (isset($SESSION->pluginuninstallreturn)) {
    $return = $SESSION->pluginuninstallreturn;
    unset($SESSION->pluginuninstallreturn);
    if ($return) {
        redirect($return);
    }
}

// Everything should now be set up, and the user is an admin

// Print default admin page with notifications.
$errorsdisplayed = defined('WARN_DISPLAY_ERRORS_ENABLED');

// We make the assumption that at least one schedule task should run once per day.
$lastcron = $DB->get_field_sql('SELECT MAX(lastruntime) FROM {task_scheduled}');
$cronoverdue = ($lastcron < time() - 3600 * 24);
$dbproblems = $DB->diagnose();
$maintenancemode = !empty($CFG->maintenance_enabled);

// Available updates for Moodle core.
$updateschecker = \core\update\checker::instance();
$availableupdates = array();
$availableupdatesfetch = null;

if ($updateschecker->enabled()) {
    // Only compute the update information when it is going to be displayed to the user.
    $availableupdates['core'] = $updateschecker->get_update_info('core',
        array('minmaturity' => $CFG->updateminmaturity, 'notifybuilds' => $CFG->updatenotifybuilds));

    // Available updates for contributed plugins
    $pluginman = core_plugin_manager::instance();
    foreach ($pluginman->get_plugins() as $plugintype => $plugintypeinstances) {
        foreach ($plugintypeinstances as $pluginname => $plugininfo) {
            $pluginavailableupdates = $plugininfo->available_updates();
            if (!empty($pluginavailableupdates)) {
                foreach ($pluginavailableupdates as $pluginavailableupdate) {
                    if (!isset($availableupdates[$plugintype.'_'.$pluginname])) {
                        $availableupdates[$plugintype.'_'.$pluginname] = array();
                    }
                    $availableupdates[$plugintype.'_'.$pluginname][] = $pluginavailableupdate;
                }
            }
        }
    }

    // The timestamp of the most recent check for available updates
    $availableupdatesfetch = $updateschecker->get_last_timefetched();
}

$buggyiconvnomb = (!function_exists('mb_convert_encoding') and @iconv('UTF-8', 'UTF-8//IGNORE', '100'.chr(130).'€') !== '100€');
//check if the site is registered on Moodle.org
$registered = $DB->count_records('registration_hubs', array('huburl' => HUB_MOODLEORGHUBURL, 'confirmed' => 1));
// Check if there are any cache warnings.
$cachewarnings = cache_helper::warnings();

admin_externalpage_setup('adminnotifications');

$output = $PAGE->get_renderer('core', 'admin');

echo $output->admin_notifications_page($maturity, $insecuredataroot, $errorsdisplayed, $cronoverdue, $dbproblems,
                                       $maintenancemode, $availableupdates, $availableupdatesfetch, $buggyiconvnomb,
                                       $registered, $cachewarnings);
