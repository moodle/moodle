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
 * Enable or disable maintenance mode.
 *
 * @package    core
 * @subpackage cli
 * @copyright  2009 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__.'/../../config.php');
require_once("$CFG->libdir/clilib.php");
require_once("$CFG->libdir/adminlib.php");


// Now get cli options.
list($options, $unrecognized) = cli_get_params(array('enable'=>false, 'enablelater'=>0, 'enableold'=>false, 'disable'=>false, 'help'=>false),
                                               array('h'=>'help'));

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

if ($options['help']) {
    $help =
"Maintenance mode settings.
Current status displayed if not option specified.

Options:
--enable              Enable CLI maintenance mode
--enablelater=MINUTES Number of minutes before entering CLI maintenance mode
--enableold           Enable legacy half-maintenance mode
--disable             Disable maintenance mode
-h, --help            Print out this help

Example:
\$ sudo -u www-data /usr/bin/php admin/cli/maintenance.php
"; //TODO: localize - to be translated later when everything is finished

    echo $help;
    die;
}

cli_heading(get_string('sitemaintenancemode', 'admin')." ($CFG->wwwroot)");

if ($options['enablelater']) {
    if (file_exists("$CFG->dataroot/climaintenance.html")) {
        // Already enabled, sorry.
        echo get_string('clistatusenabled', 'admin')."\n";
        return 1;
    }

    $time = time() + ($options['enablelater']*60);
    set_config('maintenance_later', $time);

    echo get_string('clistatusenabledlater', 'admin', userdate($time))."\n";
    return 0;

} else if ($options['enable']) {
    if (file_exists("$CFG->dataroot/climaintenance.html")) {
        // The maintenance is already enabled, nothing to do.
    } else {
        enable_cli_maintenance_mode();
    }
    set_config('maintenance_enabled', 0);
    unset_config('maintenance_later');
    echo get_string('sitemaintenanceoncli', 'admin')."\n";
    exit(0);

} else if ($options['enableold']) {
    set_config('maintenance_enabled', 1);
    unset_config('maintenance_later');
    echo get_string('sitemaintenanceon', 'admin')."\n";
    exit(0);

} else if ($options['disable']) {
    set_config('maintenance_enabled', 0);
    unset_config('maintenance_later');
    if (file_exists("$CFG->dataroot/climaintenance.html")) {
        unlink("$CFG->dataroot/climaintenance.html");
    }
    echo get_string('sitemaintenanceoff', 'admin')."\n";
    exit(0);
}

if (!empty($CFG->maintenance_enabled) or file_exists("$CFG->dataroot/climaintenance.html")) {
    echo get_string('clistatusenabled', 'admin')."\n";

} else if (isset($CFG->maintenance_later)) {
    echo get_string('clistatusenabledlater', 'admin', userdate($CFG->maintenance_later))."\n";

} else {
    echo get_string('clistatusdisabled', 'admin')."\n";
}
