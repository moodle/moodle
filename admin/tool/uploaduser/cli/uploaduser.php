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
 * CLI script to upload users
 *
 * @package     tool_uploaduser
 * @copyright   2020 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require_once(__DIR__ . '/../../../../config.php');
require_once($CFG->libdir . '/clilib.php');

if (moodle_needs_upgrading()) {
    cli_error("Moodle upgrade pending, export execution suspended.");
}

// Increase time and memory limit.
core_php_time_limit::raise();
raise_memory_limit(MEMORY_EXTRA);

// Emulate normal session - we use admin account by default, set language to the site language.
cron_setup_user();
$USER->lang = $CFG->lang;

$clihelper = new \tool_uploaduser\cli_helper();

if ($clihelper->get_cli_option('help')) {
    $clihelper->print_help();
    die();
}

$clihelper->process();

foreach ($clihelper->get_stats() as $line) {
    cli_writeln($line);
}
