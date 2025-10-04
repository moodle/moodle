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
//

/**
 * Upgrade code for tiny_premium.
 *
 * @package    tiny_premium
 * @copyright  2024 David Woloszyn <david.woloszyn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Function to upgrade tiny_premium.
 *
 * @param int $oldversion the version we are upgrading from
 * @return bool result
 */
function xmldb_tiny_premium_upgrade($oldversion) {
    // Automatically generated Moodle v4.2.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v4.3.0 release upgrade line.
    // Put any upgrade step following this.
    if ($oldversion < 2024042201) {

        // Only enable the premium plugins if we have an API key.
        if (!empty(get_config('tiny_premium', 'apikey'))) {
            $premiumplugins = \tiny_premium\manager::get_plugins();
            foreach ($premiumplugins as $plugin) {
                \tiny_premium\manager::set_plugin_config(['enabled' => 1], $plugin);
            };
        }

        upgrade_plugin_savepoint(true, 2024042201, 'tiny', 'premium');
    }

    // Automatically generated Moodle v4.5.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v5.0.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v5.1.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
