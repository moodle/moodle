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
 * Atto upgrade script.
 *
 * @package    editor_atto
 * @copyright  2014 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Run all Atto upgrade steps between the current DB version and the current version on disk.
 * @param int $oldversion The old version of atto in the DB.
 * @return bool
 */
function xmldb_editor_atto_upgrade($oldversion) {
    global $CFG;

    // Automatically generated Moodle v3.3.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.4.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2018041100) {
        $toolbar = get_config('editor_atto', 'toolbar');

        if (strpos($toolbar, 'recordrtc') === false) {
            $glue = "\r\n";
            if (strpos($toolbar, $glue) === false) {
                $glue = "\n";
            }
            $groups = explode($glue, $toolbar);
            // Try to put recordrtc in files group.
            foreach ($groups as $i => $group) {
                $parts = explode('=', $group);
                if (trim($parts[0]) == 'files') {
                    $groups[$i] = 'files = ' . trim($parts[1]) . ', recordrtc';
                    // Update config variable.
                    $toolbar = implode($glue, $groups);
                    set_config('toolbar', $toolbar, 'editor_atto');
                }
            }
        }

        // Atto editor savepoint reached.
        upgrade_plugin_savepoint(true, 2018041100, 'editor', 'atto');
    }

    if ($oldversion < 2018051401) {
        $toolbar = get_config('editor_atto', 'toolbar');
        $glue = "\r\n";
        $iconorderold = 'image, media, managefiles, recordrtc';
        $iconordernew = 'image, media, recordrtc, managefiles';

        if (strpos($toolbar, $glue) === false) {
            $glue = "\n";
        }

        $groups = explode($glue, $toolbar);

        // Reorder atto media icons if in default configuration.
        foreach ($groups as $i => $group) {
            $parts = explode('=', $group);

            if (trim($parts[0]) == 'files') {
                if (trim(preg_replace('/,\s*/', ', ', $parts[1])) == $iconorderold) {
                    $groups[$i] = 'files = ' . $iconordernew;

                    // Update config variable.
                    $toolbar = implode($glue, $groups);
                    set_config('toolbar', $toolbar, 'editor_atto');
                }
            }
        }

        // Atto editor savepoint reached.
        upgrade_plugin_savepoint(true, 2018051401, 'editor', 'atto');
    }

    // Automatically generated Moodle v3.5.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.6.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
