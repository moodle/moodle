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
 * This file keeps track of upgrades to the data module
 *
 * Sometimes, changes between versions involve
 * alterations to database structures and other
 * major things that may break installations.
 *
 * The upgrade function in this file will attempt
 * to perform all the necessary actions to upgrade
 * your older installation to the current version.
 *
 * If there's something it cannot do itself, it
 * will tell you what you need to do.
 *
 * The commands in here will all be database-neutral,
 * using the methods of database_manager class
 *
 * Please do not forget to use upgrade_set_timeout()
 * before any action that may take longer time to finish.
 *
 * @package    mod_data
 * @copyright  2006 Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function xmldb_data_upgrade($oldversion) {
    global $DB;

    // Automatically generated Moodle v4.1.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v4.2.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2023061300) {
        // Clean orphan data_records.
        $sql = "SELECT d.id FROM {data} d
            LEFT JOIN {data_fields} f ON d.id = f.dataid
            WHERE f.id IS NULL";
        $emptydatas = $DB->get_records_sql($sql);
        if (!empty($emptydatas)) {
            $dataids = array_keys($emptydatas);
            [$datainsql, $dataparams] = $DB->get_in_or_equal($dataids, SQL_PARAMS_NAMED, 'data');
            $DB->delete_records_select('data_records', "dataid $datainsql", $dataparams);
        }

        // Data savepoint reached.
        upgrade_mod_savepoint(true, 2023061300, 'data');
    }

    // Automatically generated Moodle v4.3.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2023100901) {
        // Clean param1 for "text" fields because it was unused.
        $DB->execute(
            "UPDATE {data_fields}
                SET param1 = ''
              WHERE type = 'text'"
        );

        // Data savepoint reached.
        upgrade_mod_savepoint(true, 2023100901, 'data');
    }

    // Automatically generated Moodle v4.4.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
