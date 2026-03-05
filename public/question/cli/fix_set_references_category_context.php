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
 * Fix bad set reference data due to MDL-86691.
 *
 * @todo Deprecate in Moodle 6.0 (MDL-87844) for removal in 7.0 (MDL-87845).
 *
 * @package   core_question
 * @copyright 2026 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define('CLI_SCRIPT', true);

require(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/clilib.php');

[$options, $unrecognized] = cli_get_params(['help' => false], ['h' => 'help']);

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized), 2);
}

if ($options['help']) {
    $help = <<<EOF
Fix bad set reference data

Due to MDL-86691, some question set reference records may have the wrong contextid stored in their filter condition after
upgrading to Moodle 5.x. This checks all records in the question_set_references table and corrects any that are needed.
It is not necessary if you upgraded to 5.x with the fix for MDL-86691 already in place.

Options:
-h, --help  Print out this help

Example:
\$sudo -u www-data /usr/bin/php question/cli/fix_set_references_category_context.php
EOF;

    echo $help;
    exit(0);
}

cli_writeln('Checking for incorrect context IDs in question_set_references...');

$fixcount = core_question\question_reference_manager::fix_set_references_category_context();

cli_writeln("Found and fixed {$fixcount} incorrect records.");

exit(0);
