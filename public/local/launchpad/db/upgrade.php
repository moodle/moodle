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
 * Upgrade steps for local_launchpad.
 *
 * @package    local_launchpad
 * @copyright  2026 IntelliVerse-X
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade plugin.
 *
 * @param int $oldversion Previous version.
 * @return bool
 */
function xmldb_local_launchpad_upgrade($oldversion) {
    if ($oldversion < 2026102812) {
        \local_launchpad\registration::ensure_email_self_registration();
        upgrade_plugin_savepoint(true, 2026102812, 'local', 'launchpad');
    }

    if ($oldversion < 2026102813) {
        \local_launchpad\registration::ensure_email_self_registration(true);
        upgrade_plugin_savepoint(true, 2026102813, 'local', 'launchpad');
    }

    if ($oldversion < 2026102814) {
        \local_launchpad\registration::ensure_email_self_registration(true);
        upgrade_plugin_savepoint(true, 2026102814, 'local', 'launchpad');
    }

    return true;
}
