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
 * Fix orphaned calendar events that were broken by MDL-67494.
 *
 * This script will look for all the calendar events which userids
 * where broken by a wrong upgrade step, affecting to Moodle 3.9.5
 * and up.
 *
 * It performs checks to both:
 *    a) Detect if the site was affected (ran the wrong upgrade step).
 *    b) Look for orphaned calendar events, categorising them as:
 *       - standard: site / category / course / group / user events
 *       - subscription: events created via subscriptions.
 *       - action: normal action events, created to show common important dates.
 *       - override: user and group override events, particular, that some activities support.
 *       - custom: other events, not being any of the above, common or particular.
 * By specifying it (--fix) try to recover as many broken events (missing userid) as
 * possible. Standard, subscription, action, override events in core are fully supported but
 * override or custom events should be fixed by each plugin as far as there isn't any standard
 * API (plugin-wise) to launch a rebuild of the calendar events.
 *
 * @package core
 * @copyright 2021 onwards Simey Lameze <simey@moodle.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . "/clilib.php");
require_once($CFG->libdir . '/db/upgradelib.php');

// Supported options.
$long = ['fix'  => false, 'help' => false];
$short = ['f' => 'fix', 'h' => 'help'];

// CLI options.
[$options, $unrecognized] = cli_get_params($long, $short);

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

if ($options['help']) {
    $help = <<<EOT
Fix orphaned calendar events.

  This script detects calendar events that have had their
  userid lost. By default it will perform various checks
  and report them, showing the site status in an easy way.

  Also, optionally (--fix), it wil try to recover as many
  lost userids as possible from different sources. Note that
  this script aims to process well-know events in core,
  leaving custom events in 3rd part plugins mostly unmodified
  because there isn't any consistent way to regenerate them.

  For more details:  https://tracker.moodle.org/browse/MDL-71156

Options:
  -h, --help    Print out this help.
  -f, --fix     Fix the orphaned calendar events in the DB.
                If not specified only check and report problems to STDERR.

Usage:
  - Only report:    \$ sudo -u www-data /usr/bin/php admin/cli/fix_orphaned_calendar_events.php
  - Report and fix: \$ sudo -u www-data /usr/bin/php admin/cli/fix_orphaned_calendar_events.php -f
EOT;

    cli_writeln($help);
    die;
}

// Check various usual pre-requisites.
if (empty($CFG->version)) {
    cli_error('Database is not yet installed.');
}

$admin = get_admin();
if (!$admin) {
    cli_error('Error: No admin account was found.');
}

if (moodle_needs_upgrading()) {
    cli_error('Moodle upgrade pending, script execution suspended.');
}

// Do everything as admin by default.
\core\session\manager::set_user($admin);

// Report current site status.
cli_heading('Checking the site status');
$needsfix = upgrade_calendar_site_status();

// Report current calendar events status.
cli_heading('Checking the calendar events status');
$info = upgrade_calendar_events_status();
$hasbadevents = $info['total']->bad > 0 || $info['total']->bad != $info['other']->bad;
$needsfix = $needsfix || $hasbadevents;

// If, selected, fix as many calendar events as possible.
if ($options['fix']) {

    // If the report has told us that the fix was not needed... ask for confirmation!
    if (!$needsfix) {
        cli_writeln("This site DOES NOT NEED to run the calendar events fix.");
        $input = cli_input('Are you completely sure that you want to run the fix? (y/N)', 'N', ['y', 'Y', 'n', 'N']);
        if (strtolower($input) != 'y') {
            exit(0);
        }
        cli_writeln("");
    }
    cli_heading('Fixing as many as possible calendar events');
    upgrade_calendar_events_fix_remaining($info);
    // Report current (after fix) calendar events status.
    cli_heading('Checking the calendar events status (after fix)');
    upgrade_calendar_events_status();
} else if ($needsfix) {
    // Fix option was not provided but problem events have been found. Notify the user and provide info how to fix these events.
    cli_writeln("This site NEEDS to run the calendar events fix!");
    cli_writeln("To fix the calendar events, re-run this script with the --fix option.");
}
