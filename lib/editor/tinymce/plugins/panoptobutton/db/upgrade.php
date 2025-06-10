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
 * Scripts used for upgrading database when upgrading the button from an older version
 *
 * @package block_panopto
 * @copyright  Panopto 2009 - 2019
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrades Panopto for xmldb
 *
 * @param int $oldversion the previous version Panopto is being upgraded from
 */
function xmldb_tinymce_panoptobutton_upgrade($oldversion = 0) {
    global $CFG, $DB, $USER;
    $dbman = $DB->get_manager();

    if ($oldversion < 2019081201) {

        // We removed this setting a few version back, so lets remove it from the Moodle DB when the user upgrades..
        if (get_config('tinymce_panoptobutton', 'panoptoservername')) {
            unset_config('panoptoservername', 'tinymce_panoptobutton');
        }

        // Panopto savepoint reached.
        upgrade_plugin_savepoint(true, 2019081201, 'tinymce', 'panoptobutton');
    }

    return true;
}
