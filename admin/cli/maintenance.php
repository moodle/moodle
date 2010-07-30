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
 * Enable or disable maintenance mode
 *
 * @package    core
 * @subpackage cli
 * @copyright  2009 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (isset($_SERVER['REMOTE_ADDR'])) {
    error_log("admin/cli/maintenance.php can not be called from web server!");
    exit;
}

require_once dirname(dirname(dirname(__FILE__))).'/config.php';
require_once($CFG->libdir.'/clilib.php');      // cli only functions


// now get cli options
list($options, $unrecognized) = cli_get_params(array('enable'=>false, 'disable'=>false, 'help'=>false),
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
--enable              Enable maintenance mode
--disable             Disable maintenance mode
-h, --help            Print out this help

Example: \$sudo -u wwwrun /usr/bin/php admin/cli/maintenance.php
"; //TODO: localize - to be translated later when everything is finished

    echo $help;
    die;
}

cli_heading(get_string('sitemaintenancemode', 'admin')." ($CFG->wwwroot)");

if ($options['enable']) {
    set_config('maintenance_enabled', 1);
    echo get_string('sitemaintenanceon', 'admin')."\n";
    exit(0);
} else if ($options['disable']) {
    set_config('maintenance_enabled', 0);
    echo get_string('sitemaintenanceoff', 'admin')."\n";
    exit(0);
}

if (!empty($CFG->maintenance_enabled)) {
    echo get_string('clistatusenabled', 'admin')."\n";
} else {
    echo get_string('clistatusdisabled', 'admin')."\n";
}
