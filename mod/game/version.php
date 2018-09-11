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
 * Version information
 *
 * @package mod_game
 * @copyright 2007 Vasilis Daloukas
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if (!isset( $plugin)) {
    $plugin = new stdClass;
    $useplugin = 0;
} else if ($plugin == 'mod_game') {
    $plugin = new stdClass;
    $useplugin = 1;
} else {
    $useplugin = 2;
}

$plugin->component = 'mod_game';  // Full name of the plugin (used for diagnostics).
$plugin->version   = 2018070701;  // The current module version (Date: YYYYMMDDXX).
$plugin->requires  = 2010112400;  // Requires Moodle 2.0.
$plugin->cron      = 0;           // Period for cron to check this module (secs).
$plugin->release   = '2018-07-07';
$plugin->maturity = MATURITY_STABLE;

if ($useplugin != 2) {
    $module = $plugin;
}
