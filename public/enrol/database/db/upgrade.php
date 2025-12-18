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
 * Database enrolment plugin upgrade.
 *
 * @package    enrol_database
 * @copyright  2011 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function xmldb_enrol_database_upgrade($oldversion) {
    global $DB;

    // Automatically generated Moodle v4.4.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v4.5.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v5.0.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2025070501) {
        // Remove duplicated enrolment records, keeping only the earliest records.
        $transaction = $DB->start_delegated_transaction();
        $courses = $DB->get_records_sql(
            "
             SELECT courseid
               FROM {enrol}
              WHERE enrol = 'database'
           GROUP BY courseid
             HAVING COUNT(*) > 1"
        );
        foreach ($courses as $course) {
            $instances = $DB->get_records('enrol', ['enrol' => 'database', 'courseid' => $course->courseid], 'id ASC');
            $idtokeep = array_key_first($instances);
            $idstodelete = array_slice(array_keys($instances), 1);
            [$insql, $inparams] = $DB->get_in_or_equal($idstodelete, SQL_PARAMS_NAMED);

            // Migrate enrolments where possible.
            // First, get the user enrolments that can be migrated.
            // Only select the earliest (MIN id) user_enrolments per user to avoid
            // duplicate key violations when a user has multiple enrolments across
            // duplicate database enrol instances.
            $migrateusers = $DB->get_records_sql(
                "SELECT MIN(ue.id) AS id
                   FROM {user_enrolments} ue
                  WHERE ue.enrolid $insql
                        AND NOT EXISTS (
                            SELECT 1
                              FROM {user_enrolments} ue2
                             WHERE ue2.userid  = ue.userid
                               AND ue2.enrolid = :idtokeep)
                    GROUP BY ue.userid",
                    array_merge($inparams, ['idtokeep' => $idtokeep]),
            );

            // Then update them if any exist.
            if (!empty($migrateusers)) {
                $migrateids = array_keys($migrateusers);
                [$migratein, $migrateparams] = $DB->get_in_or_equal($migrateids, SQL_PARAMS_NAMED);
                $DB->execute(
                    "UPDATE {user_enrolments} SET enrolid = :idtokeep WHERE id $migratein",
                    array_merge($migrateparams, ['idtokeep' => $idtokeep]),
                );
            }

            $DB->delete_records_select('user_enrolments', "enrolid $insql", $inparams);

            // Migrate role assignments.
            $DB->execute(
                "
                UPDATE {role_assignments}
                   SET itemid = :idtokeep
                 WHERE component = :component
                   AND itemid $insql",
                array_merge($inparams, ['component' => 'enrol_database', 'idtokeep' => $idtokeep])
            );
            $DB->delete_records_select('role_assignments', "itemid $insql", $inparams);

            $DB->delete_records_list('enrol', 'id', $idstodelete);
        }
        $transaction->allow_commit();
        upgrade_plugin_savepoint(true, 2025070501, 'enrol', 'database');
    }

    // Automatically generated Moodle v5.1.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
