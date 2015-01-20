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
 * CLI tool with utilities to manage parallel Behat integration in Moodle
 *
 * All CLI utilities uses $CFG->behat_dataroot and $CFG->prefix_dataroot as
 * $CFG->dataroot and $CFG->prefix
 *
 * @package    tool_behat
 * @copyright  2015 Rajesh Taneja
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


if (isset($_SERVER['REMOTE_ADDR'])) {
    die(); // No access from web!.
}

define('BEHAT_UTIL', true);
define('CLI_SCRIPT', true);
define('NO_OUTPUT_BUFFERING', true);
define('IGNORE_COMPONENT_CACHE', true);

require_once(__DIR__ . '/../../../../lib/clilib.php');
require_once(__DIR__ . '/../../../../lib/behat/lib.php');

// CLI options.
list($options, $unrecognized) = cli_get_params(
    array(
        'help'     => false,
        'install'  => false,
        'drop'     => false,
        'enable'   => false,
        'disable'  => false,
        'diag'     => false,
        'parallel' => 0,
        'maxruns'  => false
    ),
    array(
        'h' => 'help',
        'j' => 'parallel',
        'm' => 'maxruns'
    )
);

// Checking util.php CLI script usage.
$help = "
Behat utilities to manage the test environment

Options:
--install      Installs the test environment for acceptance tests
--drop         Drops the database tables and the dataroot contents
--enable       Enables test environment and updates tests list
--disable      Disables test environment
--diag         Get behat test environment status code
-j, --parallel Number of parallel behat run operation
-m, --maxruns  Max parallel processes to be executed at one time.

-h, --help     Print out this help

Example from Moodle root directory:
\$ php admin/tool/behat/cli/utilparallel.php --enable --parallel=4

More info in http://docs.moodle.org/dev/Acceptance_testing#Running_tests
";

if (!empty($options['help'])) {
    echo $help;
    exit(0);
}

if (empty($options['parallel'])) {
    echo $help;
    exit(1);
}

$status = 0;
$cmds = commands_to_execute($options);
$cwd = getcwd();
chdir(__DIR__);

// Start executing commands either sequential/parallel for options provided.
if ($options['diag'] || $options['drop'] || $options['enable'] || $options['disable']) {
    $code = cli_execute_sequential($cmds, true);
    // If any error then exit.
    foreach ($code as $c) {
        if ($c != 0) {
            exit($c);
        }
    }
} else if ($options['install']) {
    // This is intensive compared to behat itself so run them in chunk if $CFG->behat_max_parallel_init not set.
    if ($options['maxruns']) {
        foreach (array_chunk($cmds, maxruns, true) as $chunk) {
            $chunkstatus = (bool)cli_execute_parallel($chunk, __DIR__, true, true);
            $status = $chunkstatus || (bool) $status;
        }
    } else {
        $status = (bool)cli_execute_parallel($cmds, __DIR__, true, true);
    }
} else {
    // We should never reach here.
    echo $help;
    exit(1);
}

// Ensure we have success status to show following information.
if ($status) {
    echo "Unknown failure $status".PHP_EOL;
    exit((int)$status);
}

// Only load CFG from config.php for 1st run amd stop ASAP in lib/setup.php.
define('ABORT_AFTER_CONFIG', true);
define('BEHAT_CURRENT_RUN', 1);
require_once(__DIR__ . '/../../../../config.php');
require_once(__DIR__ . '/../../../../lib/behat/classes/behat_command.php');
require_once(__DIR__ . '/../../../../lib/behat/classes/behat_config_manager.php');

// Remove first link from wwwroot, as it is set to first run.
$CFG->behat_wwwroot = str_replace('/'.BEHAT_PARALLEL_SITE_WWW_SUFFIX . '1', '', $CFG->behat_wwwroot);

// Show command o/p (only one per time).
if ($options['install']) {
    echo "Acceptance tests site installed for sites:".PHP_EOL;
    // Display all sites which are installed/drop/diabled.
    for ($i = 1; $i <= $options['parallel']; $i++ ) {
        echo $CFG->behat_wwwroot . "/" . BEHAT_PARALLEL_SITE_WWW_SUFFIX . $i . PHP_EOL;
    }
} else if ($options['drop']) {
    echo "Acceptance tests site dropped for ".$options['parallel']." parallel sites".PHP_EOL;

} else if ($options['enable']) {
    echo "Acceptance tests environment enabled on $CFG->behat_wwwroot, to run the tests use:".PHP_EOL;
    echo behat_command::get_behat_command(true, true);
    echo PHP_EOL;

} else if ($options['disable']) {
    echo "Acceptance tests environment disabled for ".$options['parallel']." parallel sites".PHP_EOL;

} else {
    echo $help;
}

chdir($cwd);
exit(0);

/**
 * Create commands to be executed for parallel run.
 *
 * @param array $options options provided by user.
 * @return array commands to be executed.
 */
function commands_to_execute($options) {
    $removeoptions = array('maxruns');
    $cmds = array();
    $extraoptions = $options;
    $extra = "";

    // Remove extra options not in util.php
    foreach ($removeoptions as $ro) {
        $extraoptions[$ro] = null;
        unset($extraoptions[$ro]);
    }

    foreach ($extraoptions as $option => $value) {
        if ($options[$option]) {
            $extra .= " --$option";
            if ($value) {
                $extra .= "='$value'";
            }
        }
    }

    // Create commands which has to be executed for parallel site.
    for ($i = 1; $i <= $options['parallel']; $i++) {
        $prefix = BEHAT_PARALLEL_SITE_WWW_SUFFIX . $i;
        $cmds[$prefix] = "php util.php ".$extra." --run=".$i." 2>&1";
    }
    return $cmds;
}