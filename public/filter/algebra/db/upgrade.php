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
 * Algebra filter upgrade code.
 *
 * @package    filter_algebra
 * @copyright  2025 Yusuf Wibisono <yusuf.wibisono@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Upgrade function for the algebra filter.
 *
 * @param int $oldversion the version we are upgrading from
 * @return bool result
 */
function xmldb_filter_algebra_upgrade($oldversion) {

    if ($oldversion < 2025122200) {
        global $OUTPUT;

        // Show notification if filter_algebra is enabled.
        if (filter_is_enabled('algebra')) {
            echo $OUTPUT->notification(get_string('mimetexdeprecated', 'admin', ['plugin_name' => 'filter_algebra']), 'info');
        }

        // Main savepoint reached.
        upgrade_plugin_savepoint(true, 2025122200, 'filter', 'algebra');
    }

    return true;
}
