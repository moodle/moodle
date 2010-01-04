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
 * Keeps track of upgrades to the workshop module
 *
 * @package   mod-workshop
 * @copyright 2009 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Performs upgrade of the database structure and data
 *
 * Workshop supports upgrades from version 1.9.0 and higher only. During 1.9 > 2.0 upgrade,
 * there are significant database changes.
 *
 * @param int $oldversion the version we are upgrading from
 * @return bool result
 */
function xmldb_workshop_upgrade($oldversion) {
    global $CFG, $DB, $OUTPUT;

    $dbman = $DB->get_manager();
    $result = true;

    /**
     * Upgrading from workshop 1.9.x - big things going to happen now...
     * The migration procedure is divided into smaller chunks using incremental
     * versions 2009102900, 2009102901, 2009102902 etc. The day zero of the new
     * workshop 2.0 is version 2009103000 since when the upgrade code is maintained.
     */

    /**
     * Migration from 1.9 - step 1 - rename old tables
     */
    if ($result && $oldversion < 2009102901) {
        echo $OUTPUT->notification('Renaming old workshop module tables', 'notifysuccess');
        foreach (array('workshop', 'workshop_elements', 'workshop_rubrics', 'workshop_submissions', 'workshop_assessments',
                'workshop_grades', 'workshop_comments', 'workshop_stockcomments') as $tableorig) {
            $tablearchive = $tableorig . '_old';
            if ($dbman->table_exists($tableorig)) {
                $dbman->rename_table(new XMLDBTable($tableorig), $tablearchive);
            }
            // append a new field 'newid' in every archived table. null value means the record was not migrated yet
            $table = new xmldb_table($tablearchive);
            $field = new xmldb_field('newid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
            if (!$dbman->field_exists($table, $field)) {
                $dbman->add_field($table, $field);
            }
        }
        upgrade_mod_savepoint($result, 2009102901, 'workshop');
    }

    /**
     * Migration from 1.9 - step 2 - create new workshop core tables
     */
    if ($result && $oldversion < 2009102902) {
        require_once(dirname(__FILE__) . '/upgradelib.php');
        echo $OUTPUT->notification('Preparing new workshop module tables', 'notifysuccess');
        workshop_upgrade_prepare_20_tables();
        upgrade_mod_savepoint($result, 2009102902, 'workshop');
    }

    /**
     * Migration from 1.9 - step 3 - migrate workshop instances
     */
    if ($result && $oldversion < 2009102903) {
        require_once(dirname(__FILE__) . '/upgradelib.php');
        echo $OUTPUT->notification('Copying workshop core data', 'notifysuccess');
        workshop_upgrade_copy_instances();
        upgrade_mod_savepoint($result, 2009102903, 'workshop');
    }

    return $result;
}
