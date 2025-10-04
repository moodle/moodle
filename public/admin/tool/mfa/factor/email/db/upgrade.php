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
 * factor_email upgrade library.
 *
 * @package    factor_email
 * @copyright  2024 David Woloszyn <david.woloszyn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * MFA upgrade helper function.
 *
 * @param int $oldversion
 */
function xmldb_factor_email_upgrade($oldversion): bool {
    if ($oldversion < 2024122400) {
        // Check for sites that don't have MFA enabled.
        if (!get_config('tool_mfa', 'enabled')) {
            // Enable email factor.
            set_config('enabled', 1, 'factor_email');

            // Check factor order config to ensure email is situated in there.
            $factororderconfig = get_config('tool_mfa', 'factor_order');
            if (!$factororderconfig) {
                set_config('factor_order', 'email', 'tool_mfa');
            } else {
                $order = explode(',', $factororderconfig);
                // Remove any empty entries (this happens with entries like ',sms,email').
                $order = array_filter($order);
                if (!in_array('email', $order)) {
                    array_unshift($order, 'email');
                    $orderstring = implode(',', $order);
                    set_config('factor_order', $orderstring, 'tool_mfa');
                }
            }
        }

        upgrade_plugin_savepoint(true, 2024122400, 'factor', 'email');
    }

    // Automatically generated Moodle v5.0.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v5.1.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
