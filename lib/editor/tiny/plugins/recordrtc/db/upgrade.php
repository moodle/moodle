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
 * @copyright  Meirza <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Run all Tiny recordrtc upgrade steps between the current DB version and the current version on disk.
 * @param int $oldversion The old version of atto equation in the DB.
 * @return bool
 */
function xmldb_tiny_recordrtc_upgrade($oldversion) {
    if ($oldversion < 2024042201) {
        // The input bitrate to be converted.
        $currentbitrate = get_config('tiny_recordrtc', 'audiobitrate');

        // Supported bitrates.
        $supportedbitrates = \tiny_recordrtc\constants::TINYRECORDRTC_AUDIO_BITRATES;

        // Find the nearest value.
        usort($supportedbitrates, fn($a, $b) => abs($currentbitrate - $a) <=> abs($currentbitrate - $b));
        $nearestbitrate = $supportedbitrates[0];

        // Update the bitrate setting with the nearest supported bitrate.
        set_config('audiobitrate', $nearestbitrate, 'tiny_recordrtc');

        upgrade_plugin_savepoint(true, 2024042201, 'tiny', 'recordrtc');
    }

    return true;
}
