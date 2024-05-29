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
 * Tiny text editor recordrtc plugin upgrade script.
 *
 * @package    tiny_recordrtc
 * @copyright  2024 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Run all Tiny recordrtc upgrade steps between the current DB version and the current version on disk.
 * @param int $oldversion The old version of atto equation in the DB.
 * @return bool
 */
function xmldb_tiny_recordrtc_upgrade($oldversion) {
    if ($oldversion < 2024042400) {
        // Convert the old setting to the new one.
        $allowedtypes = get_config('tiny_recordrtc', 'allowedtypes');
        if ($allowedtypes === 'both') {
            set_config(
                'allowedtypes',
                implode(',', [
                    tiny_recordrtc\constants::TINYRECORDRTC_AUDIO_TYPE,
                    tiny_recordrtc\constants::TINYRECORDRTC_VIDEO_TYPE,
                ]),
                'tiny_recordrtc'
            );
        }

        upgrade_plugin_savepoint(true, 2024042400, 'tiny', 'recordrtc');
    }

    return true;
}
