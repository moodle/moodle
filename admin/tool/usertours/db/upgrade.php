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
 * Upgrade code for install
 *
 * @package   tool_usertours
 * @copyright 2016 Ryan Wyllie <ryan@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use tool_usertours\manager;
use tool_usertours\tour;

/**
 * Upgrade the user tours plugin.
 *
 * @param int $oldversion The old version of the user tours plugin
 * @return bool
 */
function xmldb_tool_usertours_upgrade($oldversion) {
    // Automatically generated Moodle v4.1.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v4.2.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2023053000) {
        // Update shipped tours.
        // Normally, we just bump the version numbers because we need to call update_shipped_tours only once.
        manager::update_shipped_tours();

        upgrade_plugin_savepoint(true, 2023053000, 'tool', 'usertours');
    }

    // Automatically generated Moodle v4.3.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
