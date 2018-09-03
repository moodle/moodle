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
 * Logging support.
 *
 * @package    tool_log
 * @copyright  2013 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Install the plugin.
 */
function xmldb_tool_log_install() {
    global $CFG, $DB;

    $enabled = array();

    // Add data to new log only from now on.
    if (file_exists("$CFG->dirroot/$CFG->admin/tool/log/store/standard")) {
        $enabled[] = 'logstore_standard';
    }

    // Enable legacy log reading, but only if there are existing data.
    if (file_exists("$CFG->dirroot/$CFG->admin/tool/log/store/legacy")) {
        unset_config('loglegacy', 'logstore_legacy');
        // Do not enabled legacy logging if somebody installed a new
        // site and in less than one day upgraded to 2.7.
        $params = array('yesterday' => time() - 60*60*24);
        if ($DB->record_exists_select('log', "time < :yesterday", $params)) {
            $enabled[] = 'logstore_legacy';
        }
    }

    set_config('enabled_stores', implode(',', $enabled), 'tool_log');
}
