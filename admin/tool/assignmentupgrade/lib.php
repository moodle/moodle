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
 * Lib functions (cron) to automatically complete the assignment module upgrade if it was not done all at once during the main upgrade.
 *
 * @package    tool_assignmentupgrade
 * @copyright  2012 NetSpot
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Standard cron function
 */
function tool_assignmentupgrade_cron() {
    $settings = get_config('tool_assignmentupgrade');
    if (empty($settings->cronenabled)) {
        return;
    }

    mtrace('assignmentupgrade: tool_assignmentupgrade_cron() started at '. date('H:i:s'));
    try {
        tool_assignmentupgrade_process($settings);
    } catch (Exception $e) {
        mtrace('assignmentupgrade: tool_assignmentupgrade_cron() failed with an exception:');
        mtrace($e->getMessage());
    }
    mtrace('assignmentupgrade: tool_assignmentupgrade_cron() finished at ' . date('H:i:s'));
}

/**
 * This function does the cron process within the time range according to settings.
 * This is not implemented yet
 * @param stdClass $settings - not used
 */
function tool_assignmentupgrade_process($settings) {
    global $CFG;
    require_once(dirname(__FILE__) . '/locallib.php');

    mtrace('assignmentupgrade: processing ...');

    mtrace('assignmentupgrade: Done.');
    return;
}
