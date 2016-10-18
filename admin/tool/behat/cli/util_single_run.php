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
        'help'        => false,
        'install'     => false,
        'parallel'    => 0,
        'run'         => 0,
        'drop'        => false,
        'enable'      => false,
        'disable'     => false,
        'diag'        => false,
        'tags'        => '',
        'updatesteps' => false,
        'optimize-runs' => '',
        'add-core-features-to-theme' => false,
    ),
    array(
        'h' => 'help',
        'o' => 'optimize-runs',
        'a' => 'add-core-features-to-theme',
    )
);

if ($options['install'] or $options['drop']) {
    define('CACHE_DISABLE_ALL', true);
}

// Checking util_single_run.php CLI script usage.
$help = "
Behat utilities to manage the test environment

Usage:
  php util_single_run.php [--install|--drop|--enable|--disable|--diag|--updatesteps|--help]

Options:
--install        Installs the test environment for acceptance tests
--drop           Drops the database tables and the dataroot contents
--enable         Enables test environment and updates tests list
--disable        Disables test environment
--diag           Get behat test environment status code
--updatesteps    Update feature step file.

-o, --optimize-runs Split features with specified tags in all parallel runs.
-a, --add-core-features-to-theme Add all core features to specified theme's

-h, --help Print out this help

Example from Moodle root directory:
\$ php admin/tool/behat/cli/util_single_run.php --enable

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

// Set run value, to be used by setup for configuring proper CFG variables.
if ($options['run']) {
    define('BEHAT_CURRENT_RUN', $options['run']);
}

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
    $unrecognized = implode(PHP_EOL . "  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

// Behat utilities.
require_once($CFG->libdir . '/behat/classes/util.php');
require_once($CFG->libdir . '/behat/classes/behat_command.php');
require_once($CFG->libdir . '/behat/classes/behat_config_manager.php');

// Ensure run option is <= parallel run installed.
$run = 0;
$parallel = 0;
if ($options['run']) {
    $run = $options['run'];
    // If parallel option is not passed, then try get it form config.
    if (!$options['parallel']) {
        $parallel = behat_config_manager::get_parallel_test_runs();
    } else {
        $parallel = $options['parallel'];
    }

    if (empty($parallel) || $run > $parallel) {
        echo "Parallel runs can't be more then ".$parallel.PHP_EOL;
        exit(1);
    }
    $CFG->behatrunprocess = $run;
}

// Run command (only one per time).
if ($options['install']) {
    behat_util::install_site();

    // This is only displayed once for parallel install.
    if (empty($run)) {
        mtrace("Acceptance tests site installed");
    }

} else if ($options['drop']) {
    // Ensure no tests are running.
    test_lock::acquire('behat');
    behat_util::drop_site();
    // This is only displayed once for parallel install.
    if (empty($run)) {
        mtrace("Acceptance tests site dropped");
    }

} else if ($options['enable']) {
    if (!empty($parallel)) {
        // Save parallel site info for enable and install options.
        $filepath = behat_config_manager::get_parallel_test_file_path();
        if (!file_put_contents($filepath, $parallel)) {
            behat_error(BEHAT_EXITCODE_PERMISSIONS, 'File ' . $filepath . ' can not be created');
        }
    }

    // Enable test mode.
    behat_util::start_test_mode($options['add-core-features-to-theme'], $options['optimize-runs'], $parallel, $run);

    // This is only displayed once for parallel install.
    if (empty($run)) {
        // Notify user that 2.5 profile has been converted to 3.5.
        if (behat_config_manager::$autoprofileconversion) {
            mtrace("2.5 behat profile detected, automatically converted to current 3.x format");
        }

        $runtestscommand = behat_command::get_behat_command(true, !empty($run));

        $runtestscommand .= ' --config ' . behat_config_manager::get_behat_cli_config_filepath();
        mtrace("Acceptance tests environment enabled on $CFG->behat_wwwroot, to run the tests use: " . PHP_EOL .
            $runtestscommand);
    }

} else if ($options['disable']) {
    behat_util::stop_test_mode();
    // This is only displayed once for parallel install.
    if (empty($run)) {
        mtrace("Acceptance tests environment disabled");
    }

} else if ($options['diag']) {
    $code = behat_util::get_behat_status();
    exit($code);

} else if ($options['updatesteps']) {
    if (defined('BEHAT_FEATURE_STEP_FILE') && BEHAT_FEATURE_STEP_FILE) {
        $behatstepfile = BEHAT_FEATURE_STEP_FILE;
    } else {
        echo "BEHAT_FEATURE_STEP_FILE is not set, please ensure you set this to writable file" . PHP_EOL;
        exit(1);
    }

    // Run behat command to get steps in feature files.
    $featurestepscmd = behat_command::get_behat_command(true);
    $featurestepscmd .= ' --config ' . behat_config_manager::get_behat_cli_config_filepath();
    $featurestepscmd .= ' --dry-run --format=moodle_step_count';
    $processes = cli_execute_parallel(array($featurestepscmd), __DIR__ . "/../../../../");
    $status = print_update_step_output(array_pop($processes), $behatstepfile);

    exit($status);
} else {
    echo $help;
    exit(1);
}

exit(0);

/**
 * Print update progress as dots for updating feature file step list.
 *
 * @param Process $process process executing update step command.
 * @param string $featurestepfile feature step file in which steps will be saved.
 * @return int exitcode.
 */
function print_update_step_output($process, $featurestepfile) {
    $printedlength = 0;

    echo "Updating steps feature file for parallel behat runs" . PHP_EOL;

    // Show progress while running command.
    while ($process->isRunning()) {
        usleep(10000);
        $op = $process->getIncrementalOutput();
        if (trim($op)) {
            echo ".";
            $printedlength++;
            if ($printedlength > 70) {
                $printedlength = 0;
                echo PHP_EOL;
            }
        }
    }

    // If any error then exit.
    $exitcode = $process->getExitCode();
    // Output err.
    if ($exitcode != 0) {
        echo $process->getErrorOutput();
        exit($exitcode);
    }

    // Extract features with step info and save it in file.
    $featuresteps = $process->getOutput();
    $featuresteps = explode(PHP_EOL, $featuresteps);

    $realroot = realpath(__DIR__.'/../../../../').'/';
    foreach ($featuresteps as $featurestep) {
        if (trim($featurestep)) {
            $step = explode("::", $featurestep);
            $step[0] = str_replace($realroot, '', $step[0]);
            $steps[$step[0]] = $step[1];
        }
    }

    if ($existing = @json_decode(file_get_contents($featurestepfile), true)) {
        $steps = array_merge($existing, $steps);
    }
    arsort($steps);

    if (!@file_put_contents($featurestepfile, json_encode($steps, JSON_PRETTY_PRINT))) {
        behat_error(BEHAT_EXITCODE_PERMISSIONS, 'File ' . $featurestepfile . ' can not be created');
        $exitcode = -1;
    }

    echo PHP_EOL. "Updated step count in " . $featurestepfile . PHP_EOL;

    return $exitcode;
}
