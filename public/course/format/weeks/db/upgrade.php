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
 * Upgrade scripts for course format "Weeks"
 *
 * @package    format_weeks
 * @copyright  2017 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Upgrade script for format_weeks
 *
 * @param int $oldversion the version we are upgrading from
 * @return bool result
 */
function xmldb_format_weeks_upgrade($oldversion) {
    global $CFG, $DB;

    // Automatically generated Moodle v4.4.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v4.5.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v5.0.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2025052600) {
        $config = get_config('format_weeks');
        // Crerate the default maxinitialsections setting if is not set.
        if (!isset($config->maxinitialsections)) {
            // The system may have some maxsections defined. We will keep the same value.
            $courseconfig = get_config('moodlecourse');
            $max = (int) $courseconfig->maxsections;
            $config->maxinitialsections = $max ?: 52;
            set_config('maxinitialsections', $config->maxinitialsections, 'format_weeks');
        }

        upgrade_plugin_savepoint(true, 2025052600, 'format', 'weeks');
    }

    // Automatically generated Moodle v5.1.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
