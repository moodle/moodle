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
 * Upgrade code for popup message processor
 *
 * @package   message_popup
 * @copyright 2008 Luis Rodrigues
 * @license   http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade code for the popup message processor
 *
 * @param int $oldversion The version that we are upgrading from
 */
function xmldb_message_popup_upgrade($oldversion) {
    global $DB;

    // Automatically generated Moodle v3.5.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.6.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.7.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.8.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2020020600) {
        // Clean up orphaned popup notification records.
        $fromsql = "FROM {message_popup_notifications} mpn
               LEFT JOIN {notifications} n
                      ON mpn.notificationid = n.id
                   WHERE n.id IS NULL";
        $total = $DB->count_records_sql("SELECT COUNT(mpn.id) " . $fromsql);
        if ($total > 0) {
            $i = 0;
            $pbar = new progress_bar('deletepopupnotification', 500, true);
            do {
                if ($popupnotifications = $DB->get_records_sql("SELECT mpn.id " . $fromsql, null, 0, 1000)) {
                    list($insql, $inparams) = $DB->get_in_or_equal(array_keys($popupnotifications));
                    $DB->delete_records_select('message_popup_notifications', "id $insql", $inparams);
                    // Update progress.
                    $i += count($inparams);
                    $pbar->update($i, $total, "Cleaning up orphaned popup notification records - $i/$total.");
                }
            } while ($popupnotifications);
        }

        // Reportbuilder savepoint reached.
        upgrade_plugin_savepoint(true, 2020020600, 'message', 'popup');
    }

    // Automatically generated Moodle v3.9.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
