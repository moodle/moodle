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
 * CLI interface for creating a test plan
 *
 * @package tool_generator
 * @copyright 2013 David Monllaó
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $CLI_VMOODLE_PRECHECK;

define('CLI_SCRIPT', true);
define('CACHE_DISABLE_ALL', true);
$CLI_VMOODLE_PRECHECK = true; // force first config to be minimal

require(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');

if (!isset($CFG->dirroot)) {
    die ('$CFG->dirroot must be explicitely defined in moodle config.php for this script to be used');
}

require_once($CFG->dirroot.'/lib/clilib.php');         // cli only functions

// CLI options.
list($options, $unrecognized) = cli_get_params(
    array(
        'help' => false,
        'host' => false,
        'full' => false
    ),
    array(
        'h' => 'help',
        'F' => 'full',
        'H' => 'host'
    )
);

// Display help.
if (!empty($options['help'])) {

    echo "Options:
-h, --help              Print out this help
-F, --full              Clears all the cache and process all logs. This can take hours !
--host                  the hostname

Example from Moodle root directory:
\$ sudo -u www-data /usr/bin/php blocks/use_stats/cli/build_session_cache.php
\$ sudo -u www-data /usr/bin/php blocks/use_stats/cli/build_session_cache.php --host=http://myvhost.mymoodle.org
";
    // Exit with error unless we're showing this because they asked for it.
    exit(empty($options['help']) ? 1 : 0);
}

// now get cli options

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error("Not recognized options ".$unrecognized);
}

if (!empty($options['host'])) {
    // Arms the vmoodle switching.
    echo('Arming for '.$options['host']."\n"); // Mtrace not yet available.
    define('CLI_VMOODLE_OVERRIDE', $options['host']);
}

// Replay full config whenever. If vmoodle switch is armed, will switch now config.

require(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php'); // Global moodle config file.
echo('Config check : playing for '.$CFG->wwwroot."\n");

require_once($CFG->dirroot.'/blocks/use_stats/locallib.php');

if ($options['full']) {

    echo "Full compilation\n";

    $DB->delete_records('block_use_stats_session');

    $users = $DB->get_records('user', array(), 'id', 'id,username');

    foreach ($users as $u) {
        // Get all logs.
        $logs = use_stats_extract_logs(0, time(), $u->id);
        $logsize = count($logs);
        $progress = "    Compiling for {$u->username} %%PROGRESS%%";
        echo str_replace('%%PROGRESS%%', "(0/$logsize)", $progress);
        use_stats_aggregate_logs($logs, 'module', 0, 0, time(), $progress);
        echo "\n";
    }

    echo "Full compilation done.\n";
}