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
    global $CFG, $DB, $OUTPUT;

    if ($oldversion < 2011030900) {
        unset_config('filter_mediaplugin_enable_img'); // migrated to filter_urltolink
        unset_config('filter_mediaplugin_enable_ram'); // --> rm
        unset_config('filter_mediaplugin_enable_rpm'); // --> rm
        unset_config('filter_mediaplugin_enable_ogg'); // --> html5audio
        unset_config('filter_mediaplugin_enable_ogv'); // --> html5video
        unset_config('filter_mediaplugin_enable_avi'); // --> wmp
        unset_config('filter_mediaplugin_enable_wmv'); // --> wmp
        unset_config('filter_mediaplugin_enable_mov'); // --> qt
        unset_config('filter_mediaplugin_enable_mpg'); // --> qt
        upgrade_plugin_savepoint(true, 2011030900, 'filter', 'mediaplugin');
    }

    // Moodle v2.1.0 release upgrade line
    // Put any upgrade step following this

    return true;
}
