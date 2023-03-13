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
 * Upgrades for coursesize
 *
 * @package    report_coursesize
 * @copyright  2022 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 /**
  * Upgrade coursesize plugin.
  *
  * @param int $oldversion
  * @return boolean
  */
function xmldb_report_coursesize_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2021030802) {
        // Define table report_coursesize to be created.
        $table = new xmldb_table('report_coursesize');

        // Adding fields to table report_coursesize.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('filesize', XMLDB_TYPE_INTEGER, '15', null, XMLDB_NOTNULL, null, null);
        $table->add_field('backupsize', XMLDB_TYPE_INTEGER, '15', null, null, null, null);

        // Adding keys to table report_coursesize.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Adding indexes to table report_coursesize.
        $table->add_index('course', XMLDB_INDEX_NOTUNIQUE, ['course']);

        // Conditionally launch create table for report_coursesize.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        } else {
            // Throw warning - some old unsupported branches use a similar table that is not compatible with this version,
            // these must be cleaned up manually.
            print_error("Cannot upgrade this old coursereport plugin - you should check/delete the old table before upgrading to this release.");
        }

        // Coursesize savepoint reached.
        upgrade_plugin_savepoint(true, 2021030802, 'report', 'coursesize');
    }
    return true;
}
