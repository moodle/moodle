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
 * Upgrade steps for local_requirelogin.
 *
 * @package    local_requirelogin
 * @copyright  2026 IntelliVerse-X
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Re-apply the login policy on upgrade.
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_local_requirelogin_upgrade($oldversion) {
    if ($oldversion < 2026092600) {
        \local_requirelogin\policy::persist();
        upgrade_plugin_savepoint(true, 2026092600, 'local', 'requirelogin');
    }
    if ($oldversion < 2026092601) {
        \local_requirelogin\courselist::restrict();
        upgrade_plugin_savepoint(true, 2026092601, 'local', 'requirelogin');
    }
    return true;
}
