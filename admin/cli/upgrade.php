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
 * This script creates config.php file and prepares database.
 *
 * This script is not intended for beginners!
 * Potential problems:
 * - su to apache account or sudo before execution
 * - not compatible with Windows platform
 *
 * @package    core
 * @subpackage cli
 * @copyright  2009 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Force OPcache reset if used, we do not want any stale caches
// when detecting if upgrade necessary or when running upgrade.
if (function_exists('opcache_reset') and !isset($_SERVER['REMOTE_ADDR'])) {
    opcache_reset();
}

define('CLI_SCRIPT', true);
define('CACHE_DISABLE_ALL', true);

require(__DIR__.'/../../config.php');
require_once($CFG->libdir.'/adminlib.php');       // various admin-only functions
require_once($CFG->libdir.'/upgradelib.php');     // general upgrade/install related functions
require_once($CFG->libdir.'/clilib.php');         // cli only functions
require_once($CFG->libdir.'/environmentlib.php');

// now get cli options
$lang = isset($SESSION->lang) ? $SESSION->lang : $CFG->lang;
list($options, $unrecognized) = cli_get_params(
    array(
        'allow-unstable'            => false,
        'help'                      => false,
        'is-maintenance-required'   => false,
        'is-pending'                => false,
        'lang'                      => $lang,
        'maintenance'               => true,
        'non-interactive'           => false,
        'set-ui-upgrade-lock'       => false,
        'unset-ui-upgrade-lock'     => false,
        'verbose-settings'          => false,
    ),
    array(
        'h' => 'help'
    )
);

if ($options['lang']) {
    $SESSION->lang = $options['lang'];
}

$interactive = empty($options['non-interactive']);

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

if ($options['help']) {
    $help =
"Command line Moodle upgrade.
Please note you must execute this script with the same uid as apache!

Site defaults may be changed via local/defaults.php.

Options:
--allow-unstable            Upgrade even if the version is not marked as stable yet,
                            required in non-interactive mode.
-h, --help                  Print out this help.
--is-maintenance-required   Returns exit code 2 if the upgrade requires maintenance mode.
                            Returns exit code 3 if no maintenance is required for the upgrade.
--is-pending                Exit with error code 2 if an upgrade is required.
--lang=CODE                 Set preferred language for CLI output. Defaults to the
                            site language if not set. Defaults to 'en' if the lang
                            parameter is invalid or if the language pack is not
                            installed.
--maintenance               Sets whether this upgrade will use maintenance mode.
                            If not possible, the upgrade will not happen and the script will exit.
                            WARNING: Caches (except theme) will be STALE and MUST be purged after upgrading.
                            DO NOT USE if the upgrade contains known breaking changes to the way data
                            and the database interact.
                            RECOMMENDED for lightweight deployments, to allow for a graceful purge and
                            rebuild of the cache.
--non-interactive           No interactive questions or confirmations.
--set-ui-upgrade-lock       Sets the upgrade to CLI only and unable to be triggered from the frontend.
                            If called with --maintenance=false, the lock WILL NOT be released when the
                            upgrade finishes, and MUST be manually removed.
                            If called with --is-maintenance-required before an upgrade,
                            The lock WILL be released when the upgrade finishes.
--unset-ui-upgrade-lock     Removes the frontend upgrade lock, if the lock exists.
                            Useful when an error during the upgrade leaves the upgrade locked,
                            or there is need to control the time where the unlock happens.
--verbose-settings          Show new settings values. By default only the name of
                            new core or plugin settings are displayed. This option
                            outputs the new values as well as the setting name.

Example:
\$sudo -u www-data /usr/bin/php admin/cli/upgrade.php
"; //TODO: localize - to be translated later when everything is finished

    echo $help;
    die;
}

if (empty($CFG->version)) {
    cli_error(get_string('missingconfigversion', 'debug'));
}

require("$CFG->dirroot/version.php");       // defines $version, $release, $branch and $maturity
$CFG->target_release = $release;            // used during installation and upgrades

if ($version < $CFG->version) {
    cli_error(get_string('downgradedcore', 'error'));
}

$oldversion = "$CFG->release ($CFG->version)";
$newversion = "$release ($version)";

if ($options['unset-ui-upgrade-lock']) {
    // Unconditionally unset this config if requested.
    set_config('outagelessupgrade', false);
    cli_writeln(get_string('cliupgradeunsetlock', 'admin'));
}

$allhash = core_component::get_all_component_hash();

// Initialise allcomponent hash if not set. It will be correctly set after upgrade.
$CFG->allcomponenthash = $CFG->allcomponenthash ?? '';
if (!$options['maintenance']) {
    if ($allhash !== $CFG->allcomponenthash) {
        // Throw an error here, we can't proceed, this needs to set maintenance.
        cli_error(get_string('cliupgrademaintenancerequired', 'core_admin'), 2);
    }

    // Set a constant to stop any upgrade var from being set during processing.
    // This protects against the upgrade setting timeouts and maintenance during the upgrade.
    define('CLI_UPGRADE_RUNNING', true);

    // This database control is the control to block the GUI from doing upgrade related actions.
    set_config('outagelessupgrade', true);
}

// We should ignore all upgrade locks here.
if (!moodle_needs_upgrading(false)) {
    cli_error(get_string('cliupgradenoneed', 'core_admin', $newversion), 0);
}

// Handle exit based options for outputting upgrade state.
if ($options['is-pending'] || $options['is-maintenance-required']) {
    // If we aren't doing a maintenance check, plain pending check.
    if (!$options['is-maintenance-required']) {
        cli_error(get_string('cliupgradepending', 'core_admin'), 2);
    }

    // Can we do this safely with no maintenance/outage? Detect if there is a schema or other application state change.
    if ($allhash !== $CFG->allcomponenthash) {
        // State change here, we need to do this in maintenance.
        cli_writeln(get_string('cliupgradepending', 'core_admin'));
        cli_error(get_string('cliupgrademaintenancerequired', 'core_admin'), 2);
    }

    // If requested, we should always set the upgrade lock here, so this cannot be run from frontend.
    if ($options['set-ui-upgrade-lock']) {
        set_config('outagelessupgrade', true);
        cli_writeln(get_string('cliupgradesetlock', 'admin'));
    }

    // We can do an upgrade without maintenance!
    cli_writeln(get_string('cliupgradepending', 'core_admin'));
    cli_error(get_string('cliupgrademaintenancenotrequired', 'core_admin'), 3);
}

// Test environment first.
list($envstatus, $environment_results) = check_moodle_environment(normalize_version($release), ENV_SELECT_RELEASE);
if (!$envstatus) {
    $errors = environment_get_errors($environment_results);
    cli_heading(get_string('environment', 'admin'));
    foreach ($errors as $error) {
        list($info, $report) = $error;
        echo "!! $info !!\n$report\n\n";
    }
    exit(1);
}

// Make sure there are no files left over from previous versions.
if (upgrade_stale_php_files_present()) {
    cli_problem(get_string('upgradestalefiles', 'admin'));

    // Stale file info contains HTML elements which aren't suitable for CLI.
    $upgradestalefilesinfo = get_string('upgradestalefilesinfo', 'admin', get_docs_url('Upgrading'));
    cli_error(strip_tags($upgradestalefilesinfo));
}

// Test plugin dependencies.
$failed = array();
if (!core_plugin_manager::instance()->all_plugins_ok($version, $failed, $CFG->branch)) {
    cli_problem(get_string('pluginscheckfailed', 'admin', array('pluginslist' => implode(', ', array_unique($failed)))));
    cli_error(get_string('pluginschecktodo', 'admin'));
}

$a = new stdClass();
$a->oldversion = $oldversion;
$a->newversion = $newversion;

if ($interactive) {
    echo cli_heading(get_string('databasechecking', '', $a)) . PHP_EOL;
}

// make sure we are upgrading to a stable release or display a warning
if (isset($maturity)) {
    if (($maturity < MATURITY_STABLE) and !$options['allow-unstable']) {
        $maturitylevel = get_string('maturity'.$maturity, 'admin');

        if ($interactive) {
            cli_separator();
            cli_heading(get_string('notice'));
            echo get_string('maturitycorewarning', 'admin', $maturitylevel) . PHP_EOL;
            echo get_string('morehelp') . ': ' . get_docs_url('admin/versions') . PHP_EOL;
            cli_separator();
        } else {
            cli_problem(get_string('maturitycorewarning', 'admin', $maturitylevel));
            cli_error(get_string('maturityallowunstable', 'admin'));
        }
    }
}

if ($interactive) {
    echo html_to_text(get_string('upgradesure', 'admin', $newversion))."\n";
    $prompt = get_string('cliyesnoprompt', 'admin');
    $input = cli_input($prompt, '', array(get_string('clianswerno', 'admin'), get_string('cliansweryes', 'admin')));
    if ($input == get_string('clianswerno', 'admin')) {
        exit(1);
    }
}

if ($version > $CFG->version) {

    // Only purge caches if this is a plain upgrade.
    // In the case of a no-outage upgrade, we will gracefully roll caches after upgrade.
    if ($options['maintenance']) {
        // We purge all of MUC's caches here.
        // Caches are disabled for upgrade by CACHE_DISABLE_ALL so we must set the first arg to true.
        // This ensures a real config object is loaded and the stores will be purged.
        // This is the only way we can purge custom caches such as memcache or APC.
        // Note: all other calls to caches will still used the disabled API.
        cache_helper::purge_all(true);
    }

    upgrade_core($version, true);
}
set_config('release', $release);
set_config('branch', $branch);

// unconditionally upgrade
upgrade_noncore(true);

// log in as admin - we need doanything permission when applying defaults
\core\session\manager::set_user(get_admin());

// Apply default settings and output those that have changed.
cli_heading(get_string('cliupgradedefaultheading', 'admin'));
$settingsoutput = admin_apply_default_settings(null, false);

foreach ($settingsoutput as $setting => $value) {

    if ($options['verbose-settings']) {
        $stringvlaues = array(
                'name' => $setting,
                'defaultsetting' => var_export($value, true) // Expand objects.
        );
        echo get_string('cliupgradedefaultverbose', 'admin', $stringvlaues) . PHP_EOL;

    } else {
        echo get_string('cliupgradedefault', 'admin', $setting) . PHP_EOL;

    }
}

// This needs to happen at the end to ensure it occurs after all caches
// have been purged for the last time.
// This will build a cached version of the current theme for the user
// to immediately start browsing the site.
upgrade_themes();

echo get_string('cliupgradefinished', 'admin', $a)."\n";

if (!$options['maintenance']) {
    cli_writeln(get_string('cliupgradecompletenomaintenanceupgrade', 'admin'));

    // Here we check if upgrade lock has not been specifically set during this upgrade run.
    // This supports wider server orchestration actions happening, which should call with no-maintenance AND set-ui-upgrade-lock,
    // such as a new docker container deployment, of which the moodle upgrade is only a component.
    if (!$options['set-ui-upgrade-lock']) {
        // In this case we should release the lock now, as the upgrade is finished.
        // We weren't told to keep the lock with set-ui-upgrade-lock, so release.
        set_config('outagelessupgrade', false);
    }
}

exit(0); // 0 means success
