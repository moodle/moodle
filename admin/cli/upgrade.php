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

define('CLI_SCRIPT', true);

require(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once($CFG->libdir.'/adminlib.php');       // various admin-only functions
require_once($CFG->libdir.'/upgradelib.php');     // general upgrade/install related functions
require_once($CFG->libdir.'/clilib.php');         // cli only functions
require_once($CFG->libdir.'/environmentlib.php');
require_once($CFG->libdir.'/pluginlib.php');

// now get cli options
list($options, $unrecognized) = cli_get_params(
    array(
        'non-interactive'   => false,
        'allow-unstable'    => false,
        'help'              => false
    ),
    array(
        'h' => 'help'
    )
);

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
--non-interactive     No interactive questions or confirmations
--allow-unstable      Upgrade even if the version is not marked as stable yet,
                      required in non-interactive mode.
-h, --help            Print out this help

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

if (!moodle_needs_upgrading()) {
    cli_error(get_string('cliupgradenoneed', 'core_admin', $newversion), 0);
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

// Test plugin dependencies.
$failed = array();
if (!plugin_manager::instance()->all_plugins_ok($version, $failed)) {
    cli_problem(get_string('pluginscheckfailed', 'admin', array('pluginslist' => implode(', ', array_unique($failed)))));
    cli_error(get_string('pluginschecktodo', 'admin'));
}

if ($interactive) {
    $a = new stdClass();
    $a->oldversion = $oldversion;
    $a->newversion = $newversion;
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
    upgrade_core($version, true);
}
set_config('release', $release);
set_config('branch', $branch);

// unconditionally upgrade
upgrade_noncore(true);

// log in as admin - we need doanything permission when applying defaults
session_set_user(get_admin());

// apply all default settings, just in case do it twice to fill all defaults
admin_apply_default_settings(NULL, false);
admin_apply_default_settings(NULL, false);

echo get_string('cliupgradefinished', 'admin')."\n";
exit(0); // 0 means success
