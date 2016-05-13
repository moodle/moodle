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
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


if (isset($_SERVER['REMOTE_ADDR'])) {
    die(); // No access from web!.
}

define('BEHAT_UTIL', true);
define('CLI_SCRIPT', true);
define('NO_OUTPUT_BUFFERING', true);
define('IGNORE_COMPONENT_CACHE', true);
define('ABORT_AFTER_CONFIG', true);

require_once(__DIR__ . '/../../../../config.php');
require_once(__DIR__ . '/../../../../lib/clilib.php');
require_once(__DIR__ . '/../../../../lib/behat/lib.php');
require_once(__DIR__ . '/../../../../lib/behat/classes/behat_command.php');
require_once(__DIR__ . '/../../../../lib/behat/classes/behat_config_manager.php');

// CLI options.
list($options, $unrecognized) = cli_get_params(
    array(
        'help'        => false,
        'install'     => false,
        'drop'        => false,
        'enable'      => false,
        'disable'     => false,
        'diag'        => false,
        'parallel'    => 0,
        'maxruns'     => false,
        'updatesteps' => false,
        'fromrun'     => 1,
        'torun'       => 0,
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

Usage:
  php util.php [--install|--drop|--enable|--disable|--diag|--updatesteps|--help] [--parallel=value [--maxruns=value]]

Options:
--install      Installs the test environment for acceptance tests
--drop         Drops the database tables and the dataroot contents
--enable       Enables test environment and updates tests list
--disable      Disables test environment
--diag         Get behat test environment status code
--updatesteps  Update feature step file.
-j, --parallel Number of parallel behat run operation
-m, --maxruns  Max parallel processes to be executed at one time.

-h, --help     Print out this help

Example from Moodle root directory:
\$ php admin/tool/behat/cli/util.php --enable --parallel=4

More info in http://docs.moodle.org/dev/Acceptance_testing#Running_tests
";

if (!empty($options['help'])) {
    echo $help;
    exit(0);
}

$cwd = getcwd();

// For drop option check if parallel site.
if ((empty($options['parallel'])) && ($options['drop']) || $options['updatesteps']) {
    // Get parallel run info from first run.
    $options['parallel'] = behat_config_manager::get_parallel_test_runs($options['fromrun']);
}

// If not a parallel site then open single run.
if (empty($options['parallel'])) {
    chdir(__DIR__);
    // Check if behat is initialised, if not exit.
    passthru("php util_single_run.php --diag", $status);
    if ($status) {
        exit ($status);
    }
    $cmd = commands_to_execute($options);
    $processes = cli_execute_parallel(array($cmd), __DIR__);
    $status = print_sequential_output($processes, false);
    chdir($cwd);
    exit($status);
}

// Default torun is maximum parallel runs.
if (empty($options['torun'])) {
    $options['torun'] = $options['parallel'];
}

$status = false;
$cmds = commands_to_execute($options);

// Start executing commands either sequential/parallel for options provided.
if ($options['diag'] || $options['enable'] || $options['disable']) {
    // Do it sequentially as it's fast and need to be displayed nicely.
    foreach (array_chunk($cmds, 1, true) as $cmd) {
        $processes = cli_execute_parallel($cmd, __DIR__);
        print_sequential_output($processes);
    }

} else if ($options['drop']) {
    $processes = cli_execute_parallel($cmds, __DIR__);
    $exitcodes = print_combined_drop_output($processes);
    foreach ($exitcodes as $exitcode) {
        $status = (bool)$status || (bool)$exitcode;
    }

} else if ($options['install']) {
    // This is intensive compared to behat itself so run them in chunk if option maxruns not set.
    if ($options['maxruns']) {
        foreach (array_chunk($cmds, $options['maxruns'], true) as $chunk) {
            $processes = cli_execute_parallel($chunk, __DIR__);
            $exitcodes = print_combined_install_output($processes);
            foreach ($exitcodes as $name => $exitcode) {
                if ($exitcode != 0) {
                    echo "Failed process [[$name]]" . PHP_EOL;
                    echo $processes[$name]->getOutput();
                    echo PHP_EOL;
                    echo $processes[$name]->getErrorOutput();
                    echo PHP_EOL . PHP_EOL;
                }
                $status = (bool)$status || (bool)$exitcode;
            }
        }
    } else {
        $processes = cli_execute_parallel($cmds, __DIR__);
        $exitcodes = print_combined_install_output($processes);
        foreach ($exitcodes as $name => $exitcode) {
            if ($exitcode != 0) {
                echo "Failed process [[$name]]" . PHP_EOL;
                echo $processes[$name]->getOutput();
                echo PHP_EOL;
                echo $processes[$name]->getErrorOutput();
                echo PHP_EOL . PHP_EOL;
            }
            $status = (bool)$status || (bool)$exitcode;
        }
    }

} else if ($options['updatesteps']) {
    // Rewrite config file to ensure we have all the features covered.
    if (empty($options['parallel'])) {
        behat_config_manager::update_config_file();
    } else {
        // Update config file, ensuring we have up-to-date behat.yml.
        for ($i = $options['fromrun']; $i <= $options['torun']; $i++) {
            $CFG->behatrunprocess = $i;
            behat_config_manager::update_config_file();
        }
        unset($CFG->behatrunprocess);
    }

    // Do it sequentially as it's fast and need to be displayed nicely.
    foreach (array_chunk($cmds, 1, true) as $cmd) {
        $processes = cli_execute_parallel($cmd, __DIR__);
        print_sequential_output($processes);
    }
    exit(0);

} else {
    // We should never reach here.
    echo $help;
    exit(1);
}

// Ensure we have success status to show following information.
if ($status) {
    echo "Unknown failure $status" . PHP_EOL;
    exit((int)$status);
}

// Show command o/p (only one per time).
if ($options['install']) {
    echo "Acceptance tests site installed for sites:".PHP_EOL;

    // Display all sites which are installed/drop/diabled.
    for ($i = $options['fromrun']; $i <= $options['torun']; $i++) {
        if (empty($CFG->behat_parallel_run[$i - 1]['behat_wwwroot'])) {
            echo $CFG->behat_wwwroot . "/" . BEHAT_PARALLEL_SITE_NAME . $i . PHP_EOL;
        } else {
            echo $CFG->behat_parallel_run[$i - 1]['behat_wwwroot'] . PHP_EOL;
        }

    }
} else if ($options['drop']) {
    echo "Acceptance tests site dropped for " . $options['parallel'] . " parallel sites" . PHP_EOL;

} else if ($options['enable']) {
    echo "Acceptance tests environment enabled on $CFG->behat_wwwroot, to run the tests use:" . PHP_EOL;
    echo behat_command::get_behat_command(true, true);
    echo PHP_EOL;

} else if ($options['disable']) {
    echo "Acceptance tests environment disabled for " . $options['parallel'] . " parallel sites" . PHP_EOL;

} else if ($options['diag']) {
    // Valid option, so nothing to do.
} else {
    echo $help;
    chdir($cwd);
    exit(1);
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
    $removeoptions = array('maxruns', 'fromrun', 'torun');
    $cmds = array();
    $extraoptions = $options;
    $extra = "";

    // Remove extra options not in util_single_run.php.
    foreach ($removeoptions as $ro) {
        $extraoptions[$ro] = null;
        unset($extraoptions[$ro]);
    }

    foreach ($extraoptions as $option => $value) {
        if ($options[$option]) {
            $extra .= " --$option";
            if ($value) {
                $extra .= "=$value";
            }
        }
    }

    if (empty($options['parallel'])) {
        $cmds = "php util_single_run.php " . $extra;
    } else {
        // Create commands which has to be executed for parallel site.
        for ($i = $options['fromrun']; $i <= $options['torun']; $i++) {
            $prefix = BEHAT_PARALLEL_SITE_NAME . $i;
            $cmds[$prefix] = "php util_single_run.php " . $extra . " --run=" . $i . " 2>&1";
        }
    }
    return $cmds;
}

/**
 * Print drop output merging each run.
 *
 * @param array $processes list of processes.
 * @return array exit codes of each process.
 */
function print_combined_drop_output($processes) {
    $exitcodes = array();
    $maxdotsonline = 70;
    $remainingprintlen = $maxdotsonline;
    $progresscount = 0;
    echo "Dropping tables:" . PHP_EOL;

    while (count($exitcodes) != count($processes)) {
        usleep(10000);
        foreach ($processes as $name => $process) {
            if ($process->isRunning()) {
                $op = $process->getIncrementalOutput();
                if (trim($op)) {
                    $update = preg_filter('#^\s*([FS\.\-]+)(?:\s+\d+)?\s*$#', '$1', $op);
                    $strlentoprint = strlen($update);

                    // If not enough dots printed on line then just print.
                    if ($strlentoprint < $remainingprintlen) {
                        echo $update;
                        $remainingprintlen = $remainingprintlen - $strlentoprint;
                    } else if ($strlentoprint == $remainingprintlen) {
                        $progresscount += $maxdotsonline;
                        echo $update . " " . $progresscount . PHP_EOL;
                        $remainingprintlen = $maxdotsonline;
                    } else {
                        while ($part = substr($update, 0, $remainingprintlen) > 0) {
                            $progresscount += $maxdotsonline;
                            echo $part . " " . $progresscount . PHP_EOL;
                            $update = substr($update, $remainingprintlen);
                            $remainingprintlen = $maxdotsonline;
                        }
                    }
                }
            } else {
                // Process exited.
                $process->clearOutput();
                $exitcodes[$name] = $process->getExitCode();
            }
        }
    }

    echo PHP_EOL;
    return $exitcodes;
}

/**
 * Print install output merging each run.
 *
 * @param array $processes list of processes.
 * @return array exit codes of each process.
 */
function print_combined_install_output($processes) {
    $exitcodes = array();
    $line = array();

    // Check what best we can do to accommodate  all parallel run o/p on single line.
    // Windows command line has length of 80 chars, so default we will try fit o/p in 80 chars.
    if (defined('BEHAT_MAX_CMD_LINE_OUTPUT') && BEHAT_MAX_CMD_LINE_OUTPUT) {
        $lengthofprocessline = (int)max(10, BEHAT_MAX_CMD_LINE_OUTPUT / count($processes));
    } else {
        $lengthofprocessline = (int)max(10, 80 / count($processes));
    }

    echo "Installing behat site for " . count($processes) . " parallel behat run" . PHP_EOL;

    // Show process name in first row.
    foreach ($processes as $name => $process) {
        // If we don't have enough space to show full run name then show runX.
        if ($lengthofprocessline < strlen($name + 2)) {
            $name = substr($name, -5);
        }
        // One extra padding as we are adding | separator for rest of the data.
        $line[$name] = str_pad('[' . $name . '] ', $lengthofprocessline + 1);
    }
    ksort($line);
    $tableheader = array_keys($line);
    echo implode("", $line) . PHP_EOL;

    // Now print o/p from each process.
    while (count($exitcodes) != count($processes)) {
        usleep(50000);
        $poutput = array();
        // Create child process.
        foreach ($processes as $name => $process) {
            if ($process->isRunning()) {
                $output = $process->getIncrementalOutput();
                if (trim($output)) {
                    $poutput[$name] = explode(PHP_EOL, $output);
                }
            } else {
                // Process exited.
                $exitcodes[$name] = $process->getExitCode();
            }
        }
        ksort($poutput);

        // Get max depth of o/p before displaying.
        $maxdepth = 0;
        foreach ($poutput as $pout) {
            $pdepth = count($pout);
            $maxdepth = $pdepth >= $maxdepth ? $pdepth : $maxdepth;
        }

        // Iterate over each process to get line to print.
        for ($i = 0; $i <= $maxdepth; $i++) {
            $pline = "";
            foreach ($tableheader as $name) {
                $po = empty($poutput[$name][$i]) ? "" : substr($poutput[$name][$i], 0, $lengthofprocessline - 1);
                $po = str_pad($po, $lengthofprocessline);
                $pline .= "|". $po;
            }
            if (trim(str_replace("|", "", $pline))) {
                echo $pline . PHP_EOL;
            }
        }
        unset($poutput);
        $poutput = null;

    }
    echo PHP_EOL;
    return $exitcodes;
}

/**
 * Print install output merging showing one run at a time.
 * If any process fail then exit.
 *
 * @param array $processes list of processes.
 * @param bool $showprefix show prefix.
 * @return bool exitcode.
 */
function print_sequential_output($processes, $showprefix = true) {
    $status = false;
    foreach ($processes as $name => $process) {
        $shownname = false;
        while ($process->isRunning()) {
            $op = $process->getIncrementalOutput();
            if (trim($op)) {
                // Show name of the run once for sequential.
                if ($showprefix && !$shownname) {
                    echo '[' . $name . '] ';
                    $shownname = true;
                }
                echo $op;
            }
        }
        // If any error then exit.
        $exitcode = $process->getExitCode();
        if ($exitcode != 0) {
            exit($exitcode);
        }
        $status = $status || (bool)$exitcode;
    }
    return $status;
}
