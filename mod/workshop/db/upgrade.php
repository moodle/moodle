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
 * @package    mod_workshop
 * @category   upgrade
 * @copyright  2009 David Mudrak <david.mudrak@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

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
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    // Automatically generated Moodle v3.2.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.3.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.4.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2018042700) {
        // Drop the old Moodle 1.x tables, thanks privacy by design for forcing me to do so finally.

        $oldtables = ['workshop_old', 'workshop_elements_old', 'workshop_rubrics_old', 'workshop_submissions_old',
            'workshop_assessments_old', 'workshop_grades_old', 'workshop_stockcomments_old', 'workshop_comments_old'];

        foreach ($oldtables as $oldtable) {
            $table = new xmldb_table($oldtable);

            if ($dbman->table_exists($table)) {
                $dbman->drop_table($table);
            }
        }

        upgrade_mod_savepoint(true, 2018042700, 'workshop');
    }

    // Automatically generated Moodle v3.5.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2018062600) {

        // Define field submissiontypetext to be added to workshop.
        $table = new xmldb_table('workshop');
        $field = new xmldb_field('submissiontypetext', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1', 'gradedecimals');

        // Conditionally launch add field submissiontypetext.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('submissiontypefile', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1',
                'submissiontypetext');

        // Conditionally launch add field submissiontypefile.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Convert existing workshops with attachments disabled to use the new settings.
        $workshops = $DB->get_records('workshop', ['nattachments' => 0]);
        foreach ($workshops as $workshop) {
            $update = (object) [
                'id' => $workshop->id,
                'submissiontypefile' => 0,
                'submissiontypetext' => 2,
                'nattachments' => 1
            ];
            $DB->update_record('workshop', $update);
        }

        // Changing the default of field nattachments on table workshop to 1.
        $field = new xmldb_field('nattachments', XMLDB_TYPE_INTEGER, '3', null, null, null, '1', 'submissiontypefile');

        // Launch change of default for field nattachments.
        $dbman->change_field_default($table, $field);

        // Workshop savepoint reached.
        upgrade_mod_savepoint(true, 2018062600, 'workshop');
    }

    // Automatically generated Moodle v3.6.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
