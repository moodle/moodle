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
 * Manual authentication plugin upgrade code
 *
 * @package    filter
 * @subpackage mediaplugin
 * @copyright  2011 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @param int $oldversion the version we are upgrading from
 * @return bool result
 */
function xmldb_filter_mediaplugin_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();


    if ($oldversion < 2011121200) {
        // Move all the media enable setttings that are now handled by core media renderer.
        foreach (array('html5video', 'html5audio', 'mp3', 'flv', 'wmp', 'qt', 'rm',
                'youtube', 'vimeo', 'swf') as $type) {
            $existingkey = 'filter_mediaplugin_enable_' . $type;
            if (array_key_exists($existingkey, $CFG)) {
                set_config('core_media_enable_' . $type, $CFG->{$existingkey});
                unset_config($existingkey);
            }
        }

        // Override setting for html5 to turn it on (previous default was off; because
        // of changes in the way fallbacks are handled, this is now unlikely to cause
        // a problem, and is required for mobile a/v support on non-Flash devices, so
        // this change is basically needed in order to maintain existing behaviour).
        set_config('core_media_enable_html5video', 1);
        set_config('core_media_enable_html5audio', 1);

        upgrade_plugin_savepoint(true, 2011121200, 'filter', 'mediaplugin');
    }

    // Moodle v2.3.0 release upgrade line
    // Put any upgrade step following this


    // Moodle v2.4.0 release upgrade line
    // Put any upgrade step following this


    return true;
}
