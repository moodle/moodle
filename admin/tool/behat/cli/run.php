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
 * Wrapper to run previously set-up behat tests in parallel.
 *
 * @package    tool_behat
 * @copyright  2014 NetSpot Pty Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (isset($_SERVER['REMOTE_ADDR'])) {
    die(); // No access from web!
}

define('BEHAT_UTIL', true);
define('CLI_SCRIPT', true);
define('ABORT_AFTER_CONFIG', true);
define('CACHE_DISABLE_ALL', true);
define('NO_OUTPUT_BUFFERING', true);

require_once(__DIR__ .'/../../../../config.php');
require_once(__DIR__.'/../../../../lib/clilib.php');
require_once(__DIR__.'/../../../../lib/behat/lib.php');
require_once(__DIR__.'/../../../../lib/behat/classes/behat_command.php');
require_once(__DIR__.'/../../../../lib/behat/classes/behat_config_manager.php');

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', '1');
ini_set('log_errors', '1');

list($options, $unrecognised) = cli_get_params(
    array(
        'stop-on-failure' => 0,
        'verbose'  => false,
        'replace'  => false,
        'help'     => false,
        'tags'     => '',
        'profile'  => '',
        'fromrun'  => 1,
        'torun'    => 0,
    ),
    array(
        'h' => 'help',
        't' => 'tags',
        'p' => 'profile',
    )
);

// Checking run.php CLI script usage.
$help = "
Behat utilities to run behat tests in parallel
Options:
-t, --tags         Tags to execute.
-p, --profile      Profile to execute.
--stop-on-failure  Stop on failure in any parallel run.
--verbose          Verbose output
--replace          Replace args string with run process number, useful for output.
--fromrun          Execute run starting from (Used for parallel runs on different vms)
--torun            Execute run till (Used for parallel runs on different vms)

-h, --help         Print out this help

Example from Moodle root directory:
\$ php admin/tool/behat/cli/run.php --parallel=2

More info in http://docs.moodle.org/dev/Acceptance_testing#Running_tests
";

if (!empty($options['help'])) {
    echo $help;
    exit(0);
}

$parallelrun = behat_config_manager::get_parallel_test_runs($options['fromrun']);

// Default torun is maximum parallel runs.
if (empty($options['torun'])) {
    $options['torun'] = $parallelrun;
}

// Capture signals and ensure we clean symlinks.
if (extension_loaded('pcntl')) {
    $disabled = explode(',', ini_get('disable_functions'));
    if (!in_array('pcntl_signal', $disabled)) {
        pcntl_signal(SIGTERM, "signal_handler");
        pcntl_signal(SIGINT, "signal_handler");
    }
}

// If empty parallelrun then just check with user if it's a run single behat test.
if (empty($parallelrun)) {
    if (cli_input("This is not a parallel site, do you want to run single behat run? (Y/N)", 'n', array('y', 'n')) == 'y') {
        $runtestscommand = behat_command::get_behat_command();
        $runtestscommand .= ' --config ' . behat_config_manager::get_behat_cli_config_filepath();
        exec("php $runtestscommand", $output, $code);
        echo implode(PHP_EOL, $output) . PHP_EOL;
        exit($code);
    } else {
        exit(1);
    }
}

// Create site symlink if necessary.
if (!behat_config_manager::create_parallel_site_links($options['fromrun'], $options['torun'])) {
    echo "Check permissions. If on windows, make sure you are running this command as admin" . PHP_EOL;
    exit(1);
}

$time = microtime(true);
array_walk($unrecognised, function (&$v) {
    if ($x = preg_filter("#^(-+\w+)=(.+)#", "\$1='\$2'", $v)) {
        $v = $x;
    } else if (!preg_match("#^-#", $v)) {
        $v = escapeshellarg($v);
    }
});
$extraopts = $unrecognised;

$tags = '';

if ($options['profile']) {
    $profile = $options['profile'];
    if (empty($CFG->behat_config[$profile]['filters']['tags'])) {
        echo "Invaid profile passed: " . $profile;
        exit(1);
    }
    $tags = $CFG->behat_config[$profile]['filters']['tags'];
    $extraopts[] = '--profile=\'' . $profile . "'";
} else if ($options['tags']) {
    $tags = $options['tags'];
    $extraopts[] = '--tags="' . $tags . '"';
}

// Update config file if tags defined.
if ($tags) {
    // Hack to set proper dataroot and wwwroot.
    $behatdataroot = $CFG->behat_dataroot;
    $behatwwwroot  = $CFG->behat_wwwroot;
    for ($i = 1; $i <= $parallelrun; $i++) {
        $CFG->behatrunprocess = $i;
        $CFG->behat_dataroot = $behatdataroot . $i;
        if (!empty($CFG->behat_parallel_run['behat_wwwroot'][$i - 1]['behat_wwwroot'])) {
            $CFG->behat_wwwroot = $CFG->behat_parallel_run['behat_wwwroot'][$i - 1]['behat_wwwroot'];
        } else {
            $CFG->behat_wwwroot = $behatwwwroot . "/" . BEHAT_PARALLEL_SITE_NAME . $i;
        }
        behat_config_manager::update_config_file('', true, $tags);
    }
    $CFG->behat_dataroot = $behatdataroot;
    $CFG->behat_wwwroot = $behatwwwroot;
    unset($CFG->behatrunprocess);
}

$cmds = array();
$extraopts = implode(' ', $extraopts);
echo "Running " . ($options['torun'] - $options['fromrun'] + 1) . " parallel behat sites:" . PHP_EOL;

for ($i = $options['fromrun']; $i <= $options['torun']; $i++) {
    $CFG->behatrunprocess = $i;

    // Options parameters to be added to each run.
    $myopts = !empty($options['replace']) ? str_replace($options['replace'], $i, $extraopts) : $extraopts;

    $behatcommand = behat_command::get_behat_command();
    $behatconfigpath = behat_config_manager::get_behat_cli_config_filepath($i);

    // Command to execute behat run.
    $cmds[BEHAT_PARALLEL_SITE_NAME . $i] = $behatcommand . ' --config ' . $behatconfigpath . " " . $myopts;
    echo "[" . BEHAT_PARALLEL_SITE_NAME . $i . "] " . $cmds[BEHAT_PARALLEL_SITE_NAME . $i] . PHP_EOL;
}

if (empty($cmds)) {
    echo "No commands to execute " . PHP_EOL;
    exit(1);
}

// Execute all commands.
$processes = cli_execute_parallel($cmds);
$stoponfail = empty($options['stop-on-failure']) ? false : true;

// Print header.
print_process_start_info($processes);

// Print combined run o/p from processes.
$exitcodes = print_combined_run_output($processes, $stoponfail);
$time = round(microtime(true) - $time, 1);
echo "Finished in " . gmdate("G\h i\m s\s", $time) . PHP_EOL . PHP_EOL;


// Print exit info from each run.
$status = false;
foreach ($exitcodes as $exitcode) {
    $status = (bool)$status || (bool)$exitcode;
}

// Show exit code from each process, if any process failed.
if ($status) {
    echo "Exit codes: " . implode(" ", $exitcodes) . PHP_EOL;
    echo "To re-run failed processes, you can use following commands:" . PHP_EOL;
    foreach ($cmds as $name => $cmd) {
        if (!empty($exitcodes[$name])) {
            echo "[" . $name . "] " . $cmd . PHP_EOL;
        }
    }
    echo PHP_EOL;
}

// Run finished. Show exit code and output from individual process.
$verbose = empty($options['verbose']) ? false : true;
$verbose = $verbose || $status;
print_each_process_info($processes, $verbose);

// Remove site symlink if necessary.
behat_config_manager::drop_parallel_site_links();

exit((int) $status);

/**
 * Signal handler for terminal exit.
 *
 * @param int $signal signal number.
 */
function signal_handler($signal) {
    switch ($signal) {
        case SIGTERM:
        case SIGKILL:
        case SIGINT:
            // Remove site symlink if necessary.
            behat_config_manager::drop_parallel_site_links();
            exit(1);
    }
}

/**
 * Prints header from the first process.
 *
 * @param array $processes list of processes to loop though.
 */
function print_process_start_info($processes) {
    $printed = false;
    // Keep looping though processes, till we get first process o/p.
    while (!$printed) {
        usleep(10000);
        foreach ($processes as $name => $process) {
            // Exit if any process has stopped.
            if (!$process->isRunning()) {
                $printed = true;
                break;
            }

            $op = explode(PHP_EOL, $process->getOutput());
            if (count($op) >= 3) {
                foreach ($op as $line) {
                    if (trim($line) && (strpos($line, '.') !== 0)) {
                        echo $line . PHP_EOL;
                    }
                }
                $printed = true;
            }
        }
    }
}

/**
 * Loop though all processes and print combined o/p
 *
 * @param array $processes list of processes to loop though.
 * @param bool $stoponfail Stop all processes and exit if failed.
 * @return array list of exit codes from all processes.
 */
function print_combined_run_output($processes, $stoponfail = false) {
    $exitcodes = array();
    $maxdotsonline = 70;
    $remainingprintlen = $maxdotsonline;
    $progresscount = 0;
    while (count($exitcodes) != count($processes)) {
        usleep(10000);
        foreach ($processes as $name => $process) {
            if ($process->isRunning()) {
                $op = $process->getIncrementalOutput();
                if (trim($op)) {
                    $update = preg_filter('#^\s*([FS\.\-]+)(?:\s+\d+)?\s*$#', '$1', $op);
                    // Exit process if anything fails.
                    if ($stoponfail && (strpos($update, 'F') !== false)) {
                        $process->stop(0);
                    }

                    $strlentoprint = strlen($update);

                    // If not enough dots printed on line then just print.
                    if ($strlentoprint < $remainingprintlen) {
                        echo $update;
                        $remainingprintlen = $remainingprintlen - $strlentoprint;
                    } else if ($strlentoprint == $remainingprintlen) {
                        $progresscount += $maxdotsonline;
                        echo $update ." " . $progresscount . PHP_EOL;
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
                $exitcodes[$name] = $process->getExitCode();
                if ($stoponfail && ($exitcodes[$name] != 0)) {
                    foreach ($processes as $l => $p) {
                        $exitcodes[$l] = -1;
                        $process->stop(0);
                    }
                }
            }
        }
    }

    echo PHP_EOL;
    return $exitcodes;
}

/**
 * Loop though all processes and print combined o/p
 *
 * @param array $processes list of processes to loop though.
 * @param bool $verbose Show verbose output for each process.
 */
function print_each_process_info($processes, $verbose = false) {
    foreach ($processes as $name => $process) {
        echo "**************** [" . $name . "] ****************" . PHP_EOL;
        if ($verbose) {
            echo $process->getOutput();
            echo $process->getErrorOutput();
        } else {
            $op = explode(PHP_EOL, $process->getOutput());
            foreach ($op as $line) {
                // Don't print progress .
                if (trim($line) && (strpos($line, '.') !== 0) && (strpos($line, 'Moodle ') !== 0) &&
                    (strpos($line, 'Server OS ') !== 0) && (strpos($line, 'Started at ') !== 0) &&
                    (strpos($line, 'Browser specific fixes ') !== 0)) {
                    echo $line . PHP_EOL;
                }
            }
        }
        echo PHP_EOL;
    }
}
