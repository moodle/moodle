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
 * Command-line script for APC export.
 *
 * @package    report_lpmonitoring
 * @author     Serge Gauthier <serge.gauthier.2@umontreal.ca>
 * @copyright  2018 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_lpmonitoring;

define('CLI_SCRIPT', true);

require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/clilib.php');
require_once($CFG->dirroot . '/report/lpmonitoring/classes/apcexport.php');

// Now get cli options.
list($options, $unrecognized) = cli_get_params(
    [
        'help' => false,
        'templateid' => false,
        'userid' => false,
        'filepath' => false,
        'flatfiledelimiter' => false,
        'verbose' => false,
    ],
    [
        'h' => 'help',
        't' => 'templateid',
        'u' => 'userid',
        'f' => 'filepath',
        'd' => 'flatfiledelimiter',
        'v' => 'verbose',
    ]
);


$help = "Perform APC export.

    This script export to CSV file the APC data associate to a learning plan.

    Options:
    -h, --help                Print out this help
    -t, --templateid          The template id for which APC is exported
    -u, --userid              The user id for wich APC is exported
                              Default: all users associated to the template
    -f, --filepath            Indicate the full path where the csv file will be produced
    -d, --flatfiledelimiter   The csv delimiter used in the file
                              these delimiters are considered 'comma', 'semicolon', 'colon', 'tab'
                              Default: semicolon
    -v, --verbose             Print verbose progress information

    Example:
    \$ sudo -u www-data /usr/bin/php report/lpmonitoring/cli/apc_export.php -t=1234 -f=/tmp/apcfiles.csv
    \$ sudo -u www-data /usr/bin/php report/lpmonitoring/cli/apc_export.php -t=1234 -u=1234 -f=/tmp/apcfiles.csv
    ";

if ($options['help']) {
    echo $help;
    die;
}

$params = [
    'help',
    'templateid',
    'userid',
    'filepath',
    'flatfiledelimiter',
    'verbose',
];

foreach ($params as $param) {
    if ($options[$param] === false) {
        unset($options[$param]);
    }
}

// Assign default values for optional options.
if (!isset($options['userid']) || empty($options['userid'])) {
    $options['userid'] = '';
}

if (!isset($options['flatfiledelimiter']) || empty($options['flatfiledelimiter'])) {
    $options['flatfiledelimiter'] = 'semicolon';
}

\core\cron::setup_user();

// Initialise the timer.
$starttime = microtime();

if (empty($options['verbose'])) {
    $trace = new \null_progress_trace();
} else {
    $trace = new \text_progress_trace();
}

$apcexport = new apcexport($trace, $options);
if ($errors = $apcexport->get_errors()) {
    $trace = new \text_progress_trace();
    foreach ($errors as $error) {
        $trace->output($error);
    }
    die;
}

// Start output log.
$timenow = time();
$trace->output("Server Time: " . date('r', $timenow));

$trace->output('Start APC export');
$apcexport->prepare_data();
$apcexport->create_file();
$trace->output('Finish APC export');

$difftime = microtime_diff($starttime, microtime());
$trace->output("Execution took " . floor($difftime) . " seconds");
