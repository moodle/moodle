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
 * This installs Moodle into empty database, config.php must already exist.
 *
 * This script is intended for advanced usage such as in Debian packages.
 * - sudo to www-data (apache account) before
 * - not compatible with Windows platform
 *
 * @package    core
 * @subpackage cli
 * @copyright  2010 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);
define('CACHE_DISABLE_ALL', true);

// extra execution prevention - we can not just require config.php here
if (isset($_SERVER['REMOTE_ADDR'])) {
    exit(1);
}

// Force OPcache reset if used, we do not want any stale caches
// when preparing test environment.
if (function_exists('opcache_reset')) {
    opcache_reset();
}

$help =
"Advanced command line Moodle database installer.
Please note you must execute this script with the same uid as apache.

Site defaults may be changed via local/defaults.php.

Options:
--lang=CODE           Installation and default site language. Default is en.
--adminuser=USERNAME  Username for the moodle admin account. Default is admin.
--adminpass=PASSWORD  Password for the moodle admin account.
--adminemail=STRING   Email address for the moodle admin account.
--agree-license       Indicates agreement with software license.
--fullname=STRING     Name of the site
--shortname=STRING    Name of the site
--summary=STRING      The summary to be displayed on the front page
--supportemail=STRING Email address for support and help.
--noreplyemail=STRING Email address used for noreply.
-h, --help            Print out this help

Example:
\$sudo -u www-data /usr/bin/php admin/cli/install_database.php --lang=cs --adminpass=soMePass123 --agree-license
";

// Check that PHP is of a sufficient version as soon as possible.
require_once(dirname(__DIR__, 2) . '/public/lib/phpminimumversionlib.php');
moodle_require_minimum_php_version();

// Nothing to do if config.php does not exist
$configfile = __DIR__.'/../../config.php';
if (!file_exists($configfile)) {
    fwrite(STDERR, 'config.php does not exist, can not continue'); // do not localize
    fwrite(STDERR, "\n");
    exit(1);
}

// Include necessary libs
require($configfile);

require_once($CFG->libdir.'/clilib.php');
require_once($CFG->libdir.'/installlib.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/componentlib.class.php');

$CFG->early_install_lang = true;
get_string_manager(true);

raise_memory_limit(MEMORY_EXTRA);

// now get cli options
list($options, $unrecognized) = cli_get_params(
    array(
        'lang'              => 'en',
        'adminuser'         => 'admin',
        'adminpass'         => '',
        'adminemail'        => '',
        'fullname'          => '',
        'shortname'         => '',
        'summary'           => '',
        'supportemail'      => '',
        'noreplyemail'      => '',
        'agree-license'     => false,
        'help'              => false
    ),
    array(
        'h' => 'help'
    )
);

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

// We show help text even if tables are installed.
if ($options['help']) {
    echo $help;
    die;
}

// Make sure no tables are installed yet.
if ($DB->get_tables() ) {
    cli_error(get_string('clitablesexist', 'install'));
}

if (!$options['agree-license']) {
    cli_error('You have to agree to the license. --help prints out the help'); // TODO: localize
}

if ($options['adminpass'] === true or $options['adminpass'] === '') {
    cli_error('You have to specify admin password. --help prints out the help'); // TODO: localize
}

// Validate that the address provided was an e-mail address.
if (!empty($options['adminemail']) && !validate_email($options['adminemail'])) {
    $a = (object) array('option' => 'adminemail', 'value' => $options['adminemail']);
    cli_error(get_string('cliincorrectvalueerror', 'admin', $a));
}

// Validate that the supportemail provided was an e-mail address.
if (!empty($options['supportemail']) && !validate_email($options['supportemail'])) {
    $a = (object) [
        'option' => 'supportemail',
        'value' => $options['supportemail']
    ];
    cli_error(get_string('cliincorrectvalueerror', 'admin', $a));
}

// Validate that the noreply address provided is valid.
if (!empty($options['noreplyemail']) && !validate_email($options['noreplyemail'])) {
    $a = (object)['option' => 'noreplyemail', 'value' => $options['noreplyemail']];
    cli_error(get_string('cliincorrectvalueerror', 'admin', $a));
}

$options['lang'] = clean_param($options['lang'], PARAM_SAFEDIR);
if (!file_exists($CFG->dirroot.'/install/lang/'.$options['lang'])) {
    $options['lang'] = 'en';
}
$CFG->lang = $options['lang'];

// download required lang packs
if ($CFG->lang !== 'en') {
    make_upload_directory('lang');
    $installer = new lang_installer($CFG->lang);
    $results = $installer->run();
    foreach ($results as $langcode => $langstatus) {
        if ($langstatus === lang_installer::RESULT_DOWNLOADERROR) {
            $a       = new stdClass();
            $a->url  = $installer->lang_pack_url($langcode);
            $a->dest = $CFG->dataroot.'/lang';
            cli_problem(get_string('remotedownloaderror', 'error', $a));
        }
    }
}

// switch the string_manager instance to stop using install/lang/
$CFG->early_install_lang = false;
get_string_manager(true);

require("$CFG->dirroot/version.php");

// Test environment first.
require_once($CFG->libdir . '/environmentlib.php');
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
if (!core_plugin_manager::instance()->all_plugins_ok($version, $failed)) {
    cli_problem(get_string('pluginscheckfailed', 'admin', array('pluginslist' => implode(', ', array_unique($failed)))));
    cli_error(get_string('pluginschecktodo', 'admin'));
}

install_cli_database($options, true);

// This needs to happen at the end to ensure it occurs after all caches
// have been purged for the last time.
// This will build a cached version of the current theme for the user
// to immediately start browsing the site.
require_once($CFG->libdir.'/upgradelib.php');
upgrade_themes();

echo get_string('cliinstallfinished', 'install')."\n";
exit(0); // 0 means success
