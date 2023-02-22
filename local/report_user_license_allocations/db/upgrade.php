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
 * @package   local_report_license_usage
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * As of the implementation of this block and the general navigation code
 * in Moodle 2.0 the body of immediate upgrade work for this block and
 * settings is done in core upgrade {@see lib/db/upgrade.php}
 *
 * There were several reasons that they were put there and not here, both becuase
 * the process for the two blocks was very similar and because the upgrade process
 * was complex due to us wanting to remvoe the outmoded blocks that this
 * block was going to replace.
 *
 * @global moodle_database $DB
 * @param int $oldversion
 * @param object $block
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_local_report_user_license_allocations_upgrade($oldversion) {
    global $CFG, $DB;

    $result = true;
    $dbman = $DB->get_manager();

    if ($oldversion < 2019012100) {

        upgrade_set_timeout(7200); // Set installation time to 2 hours as this takes a long time.

        // Define table local_report_user_lic_allocs to be created.
        $table = new xmldb_table('local_report_user_lic_allocs');

        // Adding fields to table local_report_user_lic_allocs.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('licenseid', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('action', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, null);
        $table->add_field('issuedate', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
        $table->add_field('modifiedtime', XMLDB_TYPE_INTEGER, '20', null, null, null, null);

        // Adding keys to table local_report_user_lic_allocs.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Adding indexes to table local_report_user_lic_allocs.
        $table->add_index('userliccoursedate', XMLDB_INDEX_UNIQUE, ['userid', 'courseid', 'licenseid', 'issuedate']);

        // Conditionally launch create table for local_report_user_lic_allocs.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Deal with historic license allocations as they may have dropped out of the logs or was before we fired an event.
        // Find the first event.
        if ($firstrec = $DB->get_records_sql("SELECT * FROM {logstore_standard_log}
                                          WHERE eventname = :eventname
                                          ORDER BY ID",
                                          array('eventname' => '\block_iomad_company_admin\event\user_license_assigned',
                                          0,1))) {
            $first = array_pop($firstrec);
            if ($oldallocations = $DB->get_records_sql("SELECT * FROM {companylicense_users}
                                                        WHERE issuedate < :first",
                                                        array('first' => $first->timecreated))) {
                $totalold = count($oldallocations);
                mtrace("Dealing with old allocations $totalold to be processed");
                $currentcount = 0;
                $warn = 10;

                foreach ($oldallocations as $oldallocation) {
                    if (!$DB->get_record('local_report_user_lic_allocs',
                                       array('userid' => $oldallocation->userid,
                                             'licenseid' => $oldallocation->licenseid,
                                             'courseid' => $oldallocation->licensecourseid,
                                             'action' => 1,
                                             'issuedate' => $oldallocation->issuedate))) {

                        $DB->insert_record('local_report_user_lic_allocs',
                                           array('userid' => $oldallocation->userid,
                                                 'licenseid' => $oldallocation->licenseid,
                                                 'courseid' => $oldallocation->licensecourseid,
                                                 'action' => 1,
                                                 'issuedate' => $oldallocation->issuedate,
                                                 'modifiedtime' => time()));
                    }
                    $currentcount++;
                    if ($currentcount * 100 / $totalold > $warn) {
                        mtrace("$warn%");
                        $warn = $warn + 10;
                    }
                }
            }
        }
        $firstrec2 = $DB->get_records_sql("SELECT * FROM {logstore_standard_log}
                                           WHERE eventname = :eventname
                                           ORDER BY ID",
                                           array('eventname' => '\block_iomad_company_admin\event\user_license_unassigned'),
                                           0,1);
        if (count($firstrec2) > 0 || count($firstrec) > 0) {
            // Populate the report table from any previous users.
            $users = $DB->get_records_sql("SELECT id FROM {user} u
                                           WHERE  u.deleted=0
                                           AND EXISTS (
                                             SELECT 1 FROM {logstore_standard_log} l
                                             WHERE u.id = l.userid
                                             AND eventname IN (:eventname,:eventname2))",
                                           array('eventname' => '\block_iomad_company_admin\event\user_license_assigned',
                                                 'eventname2' => '\block_iomad_company_admin\event\user_license_unassigned'));

            foreach ($users as $user) {
                // Deal with any license allocations.
                $licenseallocations = $DB->get_records('logstore_standard_log', array('userid' => $user->id, 'eventname' => '\block_iomad_company_admin\event\user_license_assigned'));
                foreach ($licenseallocations as $event) {
                    // Get the payload.
                    $evententries = unserialize($event->other);

                    if (!$DB->get_record('local_report_user_lic_allocs', array('userid' => $user->id,
                                                                              'licenseid' => $evententries['licenseid'],
                                                                              'courseid' => $event->courseid,
                                                                              'action' => 1,
                                                                              'issuedate' => $event->timecreated))) {

                        // Insert the record.
                        $DB->insert_record('local_report_user_lic_allocs', array('userid' => $user->id,
                                                                                  'licenseid' => $evententries['licenseid'],
                                                                                  'courseid' => $event->courseid,
                                                                                  'action' => 1,
                                                                                  'issuedate' => $event->timecreated,
                                                                                  'modifiedtime' => time()));
                    }
                }

                // Deal with any license unallocations.
                $licenseunallocations = $DB->get_records('logstore_standard_log', array('userid' => $user->id, 'eventname' => '\block_iomad_company_admin\event\user_license_unassigned'));
                foreach ($licenseunallocations as $event) {
                    // Get the payload.
                    $evententries = unserialize($event->other);

                    if (!$DB->get_record('local_report_user_lic_allocs', array('userid' => $user->id,
                                                                              'licenseid' => $evententries['licenseid'],
                                                                              'courseid' => $event->courseid,
                                                                              'action' => 0,
                                                                              'issuedate' => $event->timecreated))) {
                        // Insert the record.
                        $DB->insert_record('local_report_user_lic_allocs', array('userid' => $user->id,
                                                                                  'licenseid' => $evententries['licenseid'],
                                                                                  'courseid' => $event->courseid,
                                                                                  'action' => 0,
                                                                                  'issuedate' => $event->timecreated,
                                                                                  'modifiedtime' => time()));
                    }
                }
            }
        }

        // Report_user_logins savepoint reached.
        upgrade_plugin_savepoint(true, 2019012100, 'local', 'report_user_license_allocations');
    }

    return $result;

}
