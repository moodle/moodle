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
 * CLI search indexer
 *
 * @package    search
 * @copyright  2016 Dan Poltawski <dan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__.'/../../config.php');
require_once($CFG->libdir.'/clilib.php');      // cli only functions

list($options, $unrecognized) = cli_get_params(array('help' => false, 'force' => false, 'reindex' => false),
                                               array('h' => 'help', 'f' => 'force', 'r' => 'reindex'));

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

if ($options['help']) {
    $help =
"Index search data

Options:
-h, --help            Print out this help
-r, --reindex         Reindex data
-f, --force           Allow indexer to run, even if global search is disabled.

Example:
\$ sudo -u www-data /usr/bin/php search/cli/indexer.php --reindex
";

    echo $help;
    die;
}

if (!\core_search\manager::is_global_search_enabled() && empty($options['force'])) {
    cli_error('Global search is disabled. Use --force if you want to force an index while disabled');
}

if (!$searchengine = \core_search\manager::search_engine_instance()) {
    cli_error(get_string('engineserverstatus', 'search'));
}
if (!$searchengine->is_installed()) {
    cli_error('enginenotinstalled', 'search', $CFG->searchengine);
}
$serverstatus = $searchengine->is_server_ready();
if ($serverstatus !== true) {
    cli_error($serverstatus);
}

$globalsearch = \core_search\manager::instance();

if (empty($options['reindex'])) {
    echo "Running full index of site\n";
    echo "==========================\n";
    $globalsearch->index();
} else {
    echo "Running full reindex of site\n";
    echo "============================\n";
    $globalsearch->index(true);
}

// Optimize index at last.
$globalsearch->optimize_index();
