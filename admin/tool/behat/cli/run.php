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
        'feature'  => '',
        'suite'    => '',
        'fromrun'  => 1,
        'torun'    => 0,
        'single-run' => false,
        'rerun' => 0,
        'auto-rerun' => 0,
    ),
    array(
        'h' => 'help',
        't' => 'tags',
        'p' => 'profile',
        's' => 'single-run',
    )
);

// Checking run.php CLI script usage.
$help = "
Behat utilities to run behat tests in parallel

Usage:
  php run.php [--BEHAT_OPTION=\"value\"] [--feature=\"value\"] [--replace] [--fromrun=value --torun=value] [--help]

Options:
--BEHAT_OPTION     Any combination of behat option specified in http://behat.readthedocs.org/en/v2.5/guides/6.cli.html
--feature          Only execute specified feature file (Absolute path of feature file).
--suite            Specified theme scenarios will be executed.
--replace          Replace args string with run process number, useful for output.
--fromrun          Execute run starting from (Used for parallel runs on different vms)
--torun            Execute run till (Used for parallel runs on different vms)
--rerun            Re-run scenarios that failed during last execution.
--auto-rerun       Automatically re-run scenarios that failed during last execution.

-h, --help         Print out this help

Example from Moodle root directory:
\$ php admin/tool/behat/cli/run.php --tags=\"@javascript\"

More info in http://docs.moodle.org/dev/Acceptance_testing#Running_tests
";

if (!empty($options['help'])) {
    echo $help;
    exit(0);
}

$parallelrun = behat_config_manager::get_behat_run_config_value('parallel');

// Check if the options provided are valid to run behat.
if ($parallelrun === false) {
    // Parallel run should not have fromrun or torun options greater than 1.
    if (($options['fromrun'] > 1) || ($options['torun'] > 1)) {
        echo "Test site is not initialized  for parallel run." . PHP_EOL;
        exit(1);
    }
} else {
    // Ensure fromrun is within limits of initialized test site.
    if (!empty($options['fromrun']) && ($options['fromrun'] > $parallelrun)) {
        echo "From run (" . $options['fromrun'] . ") is more than site with parallel runs (" . $parallelrun . ")" . PHP_EOL;
        exit(1);
    }

    // Default torun is maximum parallel runs and should be less than equal to parallelruns.
    if (empty($options['torun'])) {
        $options['torun'] = $parallelrun;
    } else {
        if ($options['torun'] > $parallelrun) {
            echo "To run (" . $options['torun'] . ") is more than site with parallel runs (" . $parallelrun . ")" . PHP_EOL;
            exit(1);
        }
    }
}

// Capture signals and ensure we clean symlinks.
if (extension_loaded('pcntl')) {
    $disabled = explode(',', ini_get('disable_functions'));
    if (!in_array('pcntl_signal', $disabled)) {
        pcntl_signal(SIGTERM, "signal_handler");
        pcntl_signal(SIGINT, "signal_handler");
    }
}

$time = microtime(true);
array_walk($unrecognised, function (&$v) {
    if ($x = preg_filter("#^(-+\w+)=(.+)#", "\$1=\"\$2\"", $v)) {
        $v = $x;
    } else if (!preg_match("#^-#", $v)) {
        $v = escapeshellarg($v);
    }
});
$extraopts = $unrecognised;

if ($options['profile']) {
    $profile = $options['profile'];

    // If profile passed is not set, then exit.
    if (!isset($CFG->behat_config[$profile]) && !isset($CFG->behat_profiles[$profile]) &&
        !(isset($options['replace']) && (strpos($options['profile'], $options['replace']) >= 0 ))) {
        echo "Invalid profile passed: " . $profile . PHP_EOL;
        exit(1);
    }

    $extraopts['profile'] = '--profile="' . $profile . '"';
    // By default, profile tags will be used.
    if (!empty($CFG->behat_config[$profile]['filters']['tags'])) {
        $tags = $CFG->behat_config[$profile]['filters']['tags'];
    }
}

// Command line tags have precedence (std behat behavior).
if ($options['tags']) {
    $tags = $options['tags'];
    $extraopts['tags'] = '--tags="' . $tags . '"';
}

// Add suite option if specified.
if ($options['suite']) {
    $extraopts['suite'] = '--suite="' . $options['suite'] . '"';
}

// Feature should be added to last, for behat command.
if ($options['feature']) {
    $extraopts['feature'] = $options['feature'];
    // Only run 1 process as process.
    // Feature file is picked from absolute path provided, so no need to check for behat.yml.
    $options['torun'] = $options['fromrun'];
}

// Set of options to pass to behat.
$extraoptstr = implode(' ', $extraopts);

// If rerun is passed then ensure we just run the failed processes.
$lastfailedstatus = 0;
$lasttorun = $options['torun'];
$lastfromrun = $options['fromrun'];
if ($options['rerun']) {
    // Get last combined failed status.
    $lastfailedstatus = behat_config_manager::get_behat_run_config_value('lastcombinedfailedstatus');
    $lasttorun = behat_config_manager::get_behat_run_config_value('lasttorun');
    $lastfromrun = behat_config_manager::get_behat_run_config_value('lastfromrun');

    if ($lastfailedstatus !== false) {
        $extraoptstr .= ' --rerun';
    }

    // If torun is less than last torun, then just set this to min last to run and similar for fromrun.
    if ($options['torun'] < $lasttorun) {
        $options['torun'];
    }
    if ($options['fromrun'] > $lastfromrun) {
        $options['fromrun'];
    }
    unset($options['rerun']);
}

$cmds = array();
$exitcodes = array();
$status = 0;
$verbose = empty($options['verbose']) ? false : true;

// Execute behat run commands.
if (empty($parallelrun)) {
    $cwd = getcwd();
    chdir(__DIR__);
    $runtestscommand = behat_command::get_behat_command(false, false, true);
    $runtestscommand .= ' --config ' . behat_config_manager::get_behat_cli_config_filepath();
    $runtestscommand .= ' ' . $extraoptstr;
    $cmds['singlerun'] = $runtestscommand;

    echo "Running single behat site:" . PHP_EOL;
    passthru("php $runtestscommand", $status);
    $exitcodes['singlerun'] = $status;
    chdir($cwd);
} else {

    echo "Running " . ($options['torun'] - $options['fromrun'] + 1) . " parallel behat sites:" . PHP_EOL;

    for ($i = $options['fromrun']; $i <= $options['torun']; $i++) {
        $lastfailed = 1 & $lastfailedstatus >> ($i - 1);

        // Bypass if not failed in last run.
        if ($lastfailedstatus && !$lastfailed && ($i <= $lasttorun) && ($i >= $lastfromrun)) {
            continue;
        }

        $CFG->behatrunprocess = $i;

        // Options parameters to be added to each run.
        $myopts = !empty($options['replace']) ? str_replace($options['replace'], $i, $extraoptstr) : $extraoptstr;

        $behatcommand = behat_command::get_behat_command(false, false, true);
        $behatconfigpath = behat_config_manager::get_behat_cli_config_filepath($i);

        // Command to execute behat run.
        $cmds[BEHAT_PARALLEL_SITE_NAME . $i] = $behatcommand . ' --config ' . $behatconfigpath . " " . $myopts;
        echo "[" . BEHAT_PARALLEL_SITE_NAME . $i . "] " . $cmds[BEHAT_PARALLEL_SITE_NAME . $i] . PHP_EOL;
    }

    if (empty($cmds)) {
        echo "No commands to execute " . PHP_EOL;
        exit(1);
    }

    // Create site symlink if necessary.
    if (!behat_config_manager::create_parallel_site_links($options['fromrun'], $options['torun'])) {
        echo "Check permissions. If on windows, make sure you are running this command as admin" . PHP_EOL;
        exit(1);
    }

    // Save torun and from run, so it can be used to detect if it was executed in last run.
    behat_config_manager::set_behat_run_config_value('lasttorun', $options['torun']);
    behat_config_manager::set_behat_run_config_value('lastfromrun', $options['fromrun']);

    // Keep no delay by default, between each parallel, let user decide.
    if (!defined('BEHAT_PARALLEL_START_DELAY')) {
        define('BEHAT_PARALLEL_START_DELAY', 0);
    }

    // Execute all commands, relative to moodle root directory.
    $processes = cli_execute_parallel($cmds, __DIR__ . "/../../../../", BEHAT_PARALLEL_START_DELAY);
    $stoponfail = empty($options['stop-on-failure']) ? false : true;

    // Print header.
    print_process_start_info($processes);

    // Print combined run o/p from processes.
    $exitcodes = print_combined_run_output($processes, $stoponfail);
    // Time to finish run.
    $time = round(microtime(true) - $time, 1);
    echo "Finished in " . gmdate("G\h i\m s\s", $time) . PHP_EOL . PHP_EOL;
    ksort($exitcodes);

    // Print exit info from each run.
    // Status bits contains pass/fail status of parallel runs.
    foreach ($exitcodes as $name => $exitcode) {
        if ($exitcode) {
            $runno = str_replace(BEHAT_PARALLEL_SITE_NAME, '', $name);
            $status |= (1 << ($runno - 1));
        }
    }

    // Print each process information.
    print_each_process_info($processes, $verbose, $status);
}

// Save final exit code containing which run failed.
behat_config_manager::set_behat_run_config_value('lastcombinedfailedstatus', $status);

// Show exit code from each process, if any process failed and how to rerun failed process.
if ($verbose || $status) {
    // Check if status of last run is failure and rerun is suggested.
    if (!empty($options['auto-rerun']) && $status) {
        // Rerun for the number of tries passed.
        for ($i = 0; $i < $options['auto-rerun']; $i++) {

            // Run individual commands, to avoid parallel failures.
            foreach ($exitcodes as $behatrunname => $exitcode) {
                // If not failed in last run, then skip.
                if ($exitcode == 0) {
                    continue;
                }

                // This was a failure.
                echo "*** Re-running behat run: $behatrunname ***" . PHP_EOL;
                if ($verbose) {
                    echo "Executing: " . $cmds[$behatrunname] . " --rerun" . PHP_EOL;
                }

                passthru("php $cmds[$behatrunname] --rerun", $rerunstatus);

                // Update exit code.
                $exitcodes[$behatrunname] = $rerunstatus;
            }
        }

        // Update status after auto-rerun finished.
        $status = 0;
        foreach ($exitcodes as $name => $exitcode) {
            if ($exitcode) {
                if (!empty($parallelrun)) {
                    $runno = str_replace(BEHAT_PARALLEL_SITE_NAME, '', $name);
                } else {
                    $runno = 1;
                }
                $status |= (1 << ($runno - 1));
            }
        }
    }

    // Show final o/p with re-run commands.
    if ($status) {
        if (!empty($parallelrun)) {
            // Echo exit codes.
            echo "Exit codes for each behat run: " . PHP_EOL;
            foreach ($exitcodes as $run => $exitcode) {
                echo $run . ": " . $exitcode . PHP_EOL;
            }
            unset($extraopts['fromrun']);
            unset($extraopts['torun']);
            if (!empty($options['replace'])) {
                $extraopts['replace'] = '--replace="' . $options['replace'] . '"';
            }
        }

        echo "To re-run failed processes, you can use following command:" . PHP_EOL;
        $extraopts['rerun'] = '--rerun';
        $extraoptstr = implode(' ', $extraopts);
        echo behat_command::get_behat_command(true, true, true) . " " . $extraoptstr . PHP_EOL;
    }
    echo PHP_EOL;
}

// Remove site symlink if necessary.
behat_config_manager::drop_parallel_site_links();

exit($status);

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
function print_each_process_info($processes, $verbose = false, $status = 0) {
    foreach ($processes as $name => $process) {
        echo "**************** [" . $name . "] ****************" . PHP_EOL;
        if ($verbose) {
            echo $process->getOutput();
            echo $process->getErrorOutput();

        } else if ($status) {
            // Only show failed o/p.
            $runno = str_replace(BEHAT_PARALLEL_SITE_NAME, '', $name);
            if ((1 << ($runno - 1)) & $status) {
                echo $process->getOutput();
                echo $process->getErrorOutput();
            } else {
                echo get_status_lines_from_run_op($process);
            }

        } else {
            echo get_status_lines_from_run_op($process);
        }
        echo PHP_EOL;
    }
}

/**
 * Extract status information from behat o/p and return.
 * @param Symfony\Component\Process\Process $process
 * @return string
 */
function get_status_lines_from_run_op(Symfony\Component\Process\Process $process) {
    $statusstr = '';
    $op = explode(PHP_EOL, $process->getOutput());
    foreach ($op as $line) {
        // Don't print progress .
        if (trim($line) && (strpos($line, '.') !== 0) && (strpos($line, 'Moodle ') !== 0) &&
            (strpos($line, 'Server OS ') !== 0) && (strpos($line, 'Started at ') !== 0) &&
            (strpos($line, 'Browser specific fixes ') !== 0)) {
            $statusstr .= $line . PHP_EOL;
        }
    }

    return $statusstr;
}

