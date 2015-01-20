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
-t, --tags{{color_green}}         Tags to execute.
-p, --profile{{color_green}}      Profile to execute.
--stop-on-failure{{color_green}}  Stop on failure in any parallel run.
--verbose{{color_green}}          Verbose output
--replace{{color_green}}          Replace args string with run process number, useful for output.

-h, --help{{color_green}}         Print out this help

Example from Moodle root directory:
\$ php admin/tool/behat/cli/run.php --parallel=2

More info in http://docs.moodle.org/dev/Acceptance_testing#Running_tests
";

if (!empty($options['help'])) {
    echo $help;
    exit(0);
}

// Ensure we have parallel runs initialised and it's >= 1.
$parallelrun = behat_config_manager::get_parallel_test_runs(1);

// Capture signals and ensure we clean symlinks.
pcntl_signal(SIGTERM, "signal_handler");
pcntl_signal(SIGINT, "signal_handler");
/**
 * Signal handler for terminal exit.
 *
 * @param $signal
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

// If empty parallelrun then just check with user if it's a run single behat test.
if (empty($parallelrun)) {
    if (cli_input("This is not a parallel site, do you want to run single behat run? (Y/N)", 'n', array('y', 'n')) == 'y') {
        $runtestscommand = behat_command::get_behat_command();
        $runtestscommand .= ' --config ' . behat_config_manager::get_behat_cli_config_filepath();
        exec("php $runtestscommand", $output, $code);
        echo implode("\n", $output) . "\n";
        exit($code);
    } else {
        exit(1);
    }
}

// Create site symlink if necessary.
if (!behat_config_manager::create_parallel_site_links()) {
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
$extraopts = implode(' ', $unrecognised);

$checkfail = array();
$outputs = array();
$handles = array();
$pipe2i = array();
$exits = array();
$unused = null;
$linelencnt = 0;
$procs = array();
$behatdataroot = $CFG->behat_dataroot;

$tags = '';
// Options parameters to be added to each run.
$myopts = !empty($options['replace']) ? str_replace($options['replace'], $i, $extraopts) : $extraopts;
if ($options['profile']) {
    $profile = $options['profile'];
    if (empty($CFG->behat_config[$profile]['filters']['tags'])) {
        echo "Invaid profile passed: " . $profile;
        exit(1);
    }
    $tags = $CFG->behat_config[$profile]['filters']['tags'];
    $myopts .= '--profile=\'' . $profile . "'";
} else if ($options['tags']) {
    $tags = $options['tags'];
    $myopts .= '--tags=' . $tags;
}

// Update config file if tags defined.
if ($tags) {
    // Hack to set proper dataroot and wwroot.
    $behatdataroot = $CFG->behat_dataroot;
    $behatwwwroot  = $CFG->behat_wwwroot;
    for ($i = 1; $i <= $parallelrun; $i++) {
        $CFG->behatrunprocess = $i;
        $CFG->behat_dataroot = $behatdataroot . $i;
        $CFG->behat_wwwroot = $behatwwwroot . "/" . BEHAT_PARALLEL_SITE_WWW_SUFFIX . $i;
        behat_config_manager::update_config_file('', true, $tags);
    }
    $CFG->behat_dataroot = $behatdataroot;
    $CFG->behat_wwwroot = $behatwwwroot;
    unset($CFG->behatrunprocess);
}

for ($i = 1; $i <= $parallelrun; $i++) {
    $CFG->behatrunprocess = $i;
    $behatcommand = behat_command::get_behat_command();
    $behatconfigpath = behat_config_manager::get_behat_cli_config_filepath($i);

    // Command to execute behat run.
    $cmd = $behatcommand .' --config ' . $behatconfigpath . " " . $myopts;

    echo "[" . BEHAT_PARALLEL_SITE_WWW_SUFFIX . $i . "] ". $cmd . "\n";

    list($handle, $pipes) = cli_execute($cmd, true);
    @fclose($pipes[0]);
    unset($pipes[0]);
    $exits[$i] = 1;
    $handles[$i] = array($handle, $pipes[1], $pipes[2]);
    $procs[$i] = $handle;
    $checkfail[$i] = false;
    $outputs[$i] = array('');
    $pipe2i[(int) $pipes[1]] = $i;
    $pipe2i[(int) $pipes[2]] = $i;
    stream_set_blocking($pipes[1], 0);
    stream_set_blocking($pipes[2], 0);
}

$progresscount = 0;
while (!empty($procs)) {
    usleep(10000);

    foreach ($handles as $i => $p) {
        if (!($status = @proc_get_status($p[0])) || !$status['running']) {
            if ($exits[$i] !== 0) {
                $exits[$i] = !empty($status) ? $status['exitcode'] : 1;
            }
            unset($procs[$i]);
            unset($handles[$i][0]);
            $last = array_pop($outputs[$i]);
            for ($l = 2; $l >= 1; $l--) {
                while ($part = @fread($handles[$i][$l], 8192)) {
                    $last .= $part;
                }
            }
            $outputs[$i] = array_merge($outputs[$i], explode("\n", $last));
        }
    }

    $ready = array();
    foreach ($handles as $i => $set) {
        $ready[] = $set[1];
        $ready[] = $set[2];
    }

    // Poll for any process with output or ended.
    if (!$result = @stream_select($ready, $unused, $unused, 1)) {
        // Nothing; try again.
        continue;
    }
    if (!$fh = reset($ready)) {
        continue;
    }

    $i = $pipe2i[(int) $fh];
    $last = array_pop($outputs[$i]);
    $read = fread($fh, 4096);
    $newlines = explode("\n", $last.$read);
    $outputs[$i] = array_merge($outputs[$i], $newlines);

    if (!$checkfail[$i]) {
        foreach ($newlines as $l => $line) {
            unset($newlines[$l]);
            if (preg_match('#^Started at [\d\-]+#', $line) || (strlen($line) > 3 &&
                    preg_match('#^\s*([FS\.\-]+)(?:\s+\d+)?\s*$#', $line))) {
                $checkfail[$i] = true;
                break;
            }
        }
    }
    if ($progress = preg_filter('#^\s*([FUS\.\-]+)(?:\s+\d+)?\s*$#', '$1', $newlines)) {
        if ($checkfail[$i] && preg_filter('#^\s*[S\.\-]*[FU][S\.\-]*(?:\s+\d+)?\s*$#', '$1', $progress)) {
            $exits[$i] = 1;
            if ($options['stop-on-failure']) {
                foreach ($handles as $l => $p) {
                    $exits[$l] = $l != $i ? 0 : $exits[$i];
                    @proc_terminate($p[0], SIGINT);
                }
            }
        }
    }
    // Process has gone, assume this is the last output for it.
    if (empty($procs[$i])) {
        unset($handles[$i]);
    }
    if (empty($checkfail[$i]) || !($update = preg_filter('#^\s*([FS\.\-]+)(?:\s+\d+)?\s*$#', '$1', $read))) {
        continue;
    }
    while ($update) {
        $part = substr($update, 0, 70 - $linelencnt);
        $update = substr($update, strlen($part));
        $linelencnt += strlen($part);
        echo $part;
        if ($linelencnt >= 70) {
            $progresscount += 70;
            echo " $progresscount\n";
            $linelencnt = 0;
        }
    }
}
echo "\n\n";

$exits = array_filter($exits,
    function ($v) {
        return $v !== 0;
    }
);

if ($exits || $options['verbose']) {
    echo "Exit codes: ".implode(" ", $exits)."\n\n";
    foreach ($outputs as $i => $output) {
        unset($outputs[$i]);
        if (!end($output)) {
            array_pop($output);
        }
        $prefix = "[" . BEHAT_PARALLEL_SITE_WWW_SUFFIX . $i . "] ";
        array_walk($output, function (&$l) use ($prefix) {
            $l = $prefix.$l;
        });
        echo implode("\n", $output)."\n\n";
    }
    $failed = true;
}

$time = round(microtime(true) - $time, 1);
echo "Finished in {$time}s\n";

// Remove site symlink if necessary.
behat_config_manager::drop_parallel_site_links();

exit(!empty($failed) ? 1 : 0);
