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
 * Upgrade script for tool_installaddon.
 *
 * @package    tool_installaddon
 * @copyright  2026 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Upgrade the plugin.
 *
 * @param int $oldversion
 * @return bool always true
 */
function xmldb_tool_installaddon_upgrade(int $oldversion): bool {
    if ($oldversion < 2025100601) {
        // Set the activity chooser active footer to include marketplace regardless of the previous setting.
        // We are deliberately setting this to increase awareness of marketplace.
        set_config('activitychooseractivefooter', 'tool_installaddon');

        upgrade_plugin_savepoint(true, 2025100601, 'tool', 'installaddon');
    }

    return true;
}
