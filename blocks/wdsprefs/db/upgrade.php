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
 * @package    block_wdsprefs
 * @copyright  2025 onwards Louisiana State University
 * @copyright  2025 onwards Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade function for the wdsprefs block
 *
 * @param int $oldversion The old version of the wdsprefs block
 * @return bool
 */
function xmldb_block_wdsprefs_upgrade($oldversion) {
    global $DB;
    
    $dbman = $DB->get_manager();
    
    if ($oldversion < 2025050600) {
        // Define table block_wdsprefs_blueprints to be created
        $table = new xmldb_table('block_wdsprefs_blueprints');

        // Add fields
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '19', null, XMLDB_NOTNULL, null, null);
        $table->add_field('universal_id', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('course_definition_id', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('moodle_course_id', XMLDB_TYPE_INTEGER, '19', null, null, null, null);
        $table->add_field('status', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, 'pending');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Add keys
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Add indexes
        $table->add_index('blueprint_uid_ix', XMLDB_INDEX_NOTUNIQUE, ['userid']);
        $table->add_index('blueprint_cdid_ix', XMLDB_INDEX_NOTUNIQUE, ['course_definition_id']);
        $table->add_index('blueprint_unid_ix', XMLDB_INDEX_NOTUNIQUE, ['universal_id']);
        $table->add_index('blueprint_status_ix', XMLDB_INDEX_NOTUNIQUE, ['status']);

        // Create the table
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Update version
        upgrade_block_savepoint(true, 2025050600, 'wdsprefs');
    }
    
    if ($oldversion < 2025050700) {
        // Define table block_wdsprefs_crosssplits to be created
        $table = new xmldb_table('block_wdsprefs_crosssplits');

        // Add fields
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '19', null, XMLDB_NOTNULL, null, null);
        $table->add_field('universal_id', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('academic_period_id', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('shell_name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('moodle_course_id', XMLDB_TYPE_INTEGER, '19', null, null, null, null);
        $table->add_field('status', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, 'pending');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Add keys
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Add indexes
        $table->add_index('crosssplit_uid_ix', XMLDB_INDEX_NOTUNIQUE, ['userid']);
        $table->add_index('crosssplit_apid_ix', XMLDB_INDEX_NOTUNIQUE, ['academic_period_id']);
        $table->add_index('crosssplit_unid_ix', XMLDB_INDEX_NOTUNIQUE, ['universal_id']);
        $table->add_index('crosssplit_status_ix', XMLDB_INDEX_NOTUNIQUE, ['status']);

        // Create the table
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }
        
        // Define table block_wdsprefs_crosssplit_sections to be created
        $table = new xmldb_table('block_wdsprefs_crosssplit_sections');

        // Add fields
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('crosssplit_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('section_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('section_listing_id', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('status', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, 'pending');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Add keys
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('fk_crosssplit_id', XMLDB_KEY_FOREIGN, ['crosssplit_id'], 'block_wdsprefs_crosssplits', ['id']);

        // Add indexes - making sure index names don't collide with key names
        $table->add_index('cls_section_id_ix', XMLDB_INDEX_NOTUNIQUE, ['section_id']);
        $table->add_index('cls_section_listing_id_ix', XMLDB_INDEX_NOTUNIQUE, ['section_listing_id']);
        $table->add_index('cls_status_ix', XMLDB_INDEX_NOTUNIQUE, ['status']);

        // Create the table
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Update version
        upgrade_block_savepoint(true, 2025050700, 'wdsprefs');
    }

    return true;
}
