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
 * CLI tool with utilities to manage Behat integration in Moodle
 *
 * All CLI utilities uses $CFG->behat_dataroot and $CFG->prefix_dataroot as
 * $CFG->dataroot and $CFG->prefix
 *
 * @package    tool_behat
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


if (isset($_SERVER['REMOTE_ADDR'])) {
    die(); // No access from web!.
}

// Basic functions.
require_once(__DIR__ . '/../../../../lib/clilib.php');
require_once(__DIR__ . '/../../../../lib/behat/lib.php');


// CLI options.
list($options, $unrecognized) = cli_get_params(
    array(
        'help'    => false,
        'install' => false,
        'drop'    => false,
        'enable'  => false,
        'disable' => false,
        'diag'    => false
    ),
    array(
        'h' => 'help'
    )
);

if ($options['install'] or $options['drop']) {
    define('CACHE_DISABLE_ALL', true);
}

// Checking util.php CLI script usage.
$help = "
Behat utilities to manage the test environment

Options:
--install  Installs the test environment for acceptance tests
--drop     Drops the database tables and the dataroot contents
--enable   Enables test environment and updates tests list
--disable  Disables test environment
--diag     Get behat test environment status code

-h, --help     Print out this help

Example from Moodle root directory:
\$ php admin/tool/behat/cli/util.php --enable

More info in http://docs.moodle.org/dev/Acceptance_testing#Running_tests
";

if (!empty($options['help'])) {
    echo $help;
    exit(0);
}

// Describe this script.
define('BEHAT_UTIL', true);
define('CLI_SCRIPT', true);
define('NO_OUTPUT_BUFFERING', true);
define('IGNORE_COMPONENT_CACHE', true);

// Only load CFG from config.php, stop ASAP in lib/setup.php.
define('ABORT_AFTER_CONFIG', true);
require_once(__DIR__ . '/../../../../config.php');

// Remove error handling overrides done in config.php.
$CFG->debug = (E_ALL | E_STRICT);
$CFG->debugdisplay = 1;
error_reporting($CFG->debug);
ini_set('display_errors', '1');
ini_set('log_errors', '1');

// Finish moodle init.
define('ABORT_AFTER_CONFIG_CANCEL', true);
require("$CFG->dirroot/lib/setup.php");

raise_memory_limit(MEMORY_HUGE);

require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/upgradelib.php');
require_once($CFG->libdir.'/clilib.php');
require_once($CFG->libdir.'/installlib.php');
require_once($CFG->libdir.'/testing/classes/test_lock.php');

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

// Behat utilities.
require_once($CFG->libdir . '/behat/classes/util.php');
require_once($CFG->libdir . '/behat/classes/behat_command.php');

// Run command (only one per time).
if ($options['install']) {
    behat_util::install_site();
    mtrace("Acceptance tests site installed");
} else if ($options['drop']) {
    // Ensure no tests are running.
    test_lock::acquire('behat');
    behat_util::drop_site();
    mtrace("Acceptance tests site dropped");
} else if ($options['enable']) {
    behat_util::start_test_mode();
    $runtestscommand = behat_command::get_behat_command(true) .
        ' --config ' . behat_config_manager::get_behat_cli_config_filepath();
    mtrace("Acceptance tests environment enabled on $CFG->behat_wwwroot, to run the tests use:\n " . $runtestscommand);
} else if ($options['disable']) {
    behat_util::stop_test_mode();
    mtrace("Acceptance tests environment disabled");
} else if ($options['diag']) {
    $code = behat_util::get_behat_status();
    exit($code);
} else {
    echo $help;
}

exit(0);
