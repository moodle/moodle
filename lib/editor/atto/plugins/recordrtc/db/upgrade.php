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
 * Atto text editor recordrtc upgrade script.
 *
 * @package    atto_recordrtc
 * @copyright  2021 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade code for the recordrtc atto text editor.
 *
 * @param int $oldversion the version we are upgrading from.
 * @return bool
 */
function xmldb_atto_recordrtc_upgrade($oldversion) {
    global $CFG;

    // Change settings from timelimit to audiotimelimit and videotimelimit.
    require_once($CFG->dirroot . '/lib/editor/atto/plugins/recordrtc/lib.php');
    if ($oldversion < 2021073000) {
        $timelimit = get_config('atto_recordrtc', 'timelimit');
        if ($timelimit != DEFAULT_TIME_LIMIT) {
            set_config('audiotimelimit', $timelimit, 'atto_recordrtc');
            set_config('videotimelimit', $timelimit, 'atto_recordrtc');
        }
        // Recordrtc savepoint reached.
        upgrade_plugin_savepoint(true, 2021073000, 'atto', 'recordrtc');
    }

    // Automatically generated Moodle v4.0.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
