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
 * factor_totp upgrade library.
 *
 * @package    factor_totp
 * @copyright  2024 Daniel Ziegenberg <daniel@ziegenberg.at>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Factor totp upgrade helper function
 *
 * @param int $oldversion
 */
function xmldb_factor_totp_upgrade($oldversion): bool {
    if ($oldversion < 2024081600) {

        $window = get_config('factor_totp', 'window');
        if ($window && $window >= 30) {
            set_config('window', 29, 'factor_totp');
        }

        // Savepoint reached.
        upgrade_plugin_savepoint(true, 2024081600, 'factor', 'totp');
    }

    // Automatically generated Moodle v4.5.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v5.0.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
