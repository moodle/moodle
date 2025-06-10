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
 * Upgrade functions.
 * @package     mod_board
 * @author      Mike Churchward <mike@brickfieldlabs.ie>
 * @copyright   2021 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * The main upgrade function.
 * @param int $oldversion
 * @return bool
 */
function xmldb_board_upgrade(int $oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2021052400) {
        // Board savepoint reached.
        upgrade_mod_savepoint(true, 2021052400, 'board');
    }

    if ($oldversion < 2021052405) {
        // Define field userscanedit to be added to board.
        $table = new xmldb_table('board');
        $field = new xmldb_field('userscanedit', XMLDB_TYPE_INTEGER, '1', null, null, null, '0', 'postby');

        // Conditionally launch add field userscanedit.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Board savepoint reached.
        upgrade_mod_savepoint(true, 2021052405, 'board');
    }

    if ($oldversion < 2021052406) {

        // Define field sortorder to be added to board_notes.
        $table = new xmldb_table('board_notes');
        $field = new xmldb_field('sortorder', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'timecreated');

        // Conditionally launch add field sortorder.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Board savepoint reached.
        upgrade_mod_savepoint(true, 2021052406, 'board');
    }

    // Release 1.39.06.
    if ($oldversion < 2021052407) {
        mod_board_remove_unattached_ratings();
        // Board savepoint reached.
        upgrade_mod_savepoint(true, 2021052407, 'board');
    }

    if ($oldversion < 2021052413) {
        // Version 2021052410.
        // Define field singleusermode to be added to board.
        $table = new xmldb_table('board');
        $field = new xmldb_field('singleusermode', XMLDB_TYPE_INTEGER, '4', null, null, null, '0', 'userscanedit');

        // Conditionally launch add field singleusermode.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field ownerid to be added to board_history.
        $table = new xmldb_table('board_history');
        $field = new xmldb_field('ownerid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'groupid');

        // Conditionally launch add field ownerid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field owner to be added to board_notes.
        $table = new xmldb_table('board_notes');
        $field = new xmldb_field('ownerid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'columnid');

        // Conditionally launch add field owner.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Version 2021052408.
        // Define field enableblanktarget to be added to board.
        $table = new xmldb_table('board');
        $field = new xmldb_field('enableblanktarget', XMLDB_TYPE_INTEGER, '1', null, null, null, '0', 'singleusermode');

        // Conditionally launch add field enableblanktarget.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Version 2021052409.
        // Define field completionnotes to be added to board.
        $table = new xmldb_table('board');
        $field = new xmldb_field('completionnotes', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'enableblanktarget');

        // Conditionally launch add field completionnotes.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Version 2021052412.
        // Define field locked to be added to board_columns.
        $table = new xmldb_table('board_columns');
        $field = new xmldb_field('locked', XMLDB_TYPE_INTEGER, '4', null, null, null, null, 'name');

        // Conditionally launch add field locked.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('sortorder', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'name');

        // Conditionally launch add field sortorder.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Version 2021052413.
        // Define table board_note_comments to be created.
        $table = new xmldb_table('board_comments');

        // Adding fields to table board_note_comments.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('noteid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('content', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Adding keys to table board_note_comments.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Conditionally launch create table for board_note_comments.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Board savepoint reached.
        upgrade_mod_savepoint(true, 2021052413, 'board');
    }

    // Release 1.400.01.
    if ($oldversion < 2022040104) {
        // Define field embed to be added to board.
        $table = new xmldb_table('board');
        $field = new xmldb_field('embed', XMLDB_TYPE_INTEGER, '1', null, null, null, '0', 'completionnotes');

        // Conditionally launch add field embed.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Board savepoint reached.
        upgrade_mod_savepoint(true, 2022040104, 'board');
    }

    if ($oldversion < 2022040105) {
        // Update incorrect value for singleusermode disabled.
        $DB->set_field(
            'board',
            'singleusermode',
            0,
            ['singleusermode' => 3]
        );

        // Board savepoint reached.
        upgrade_mod_savepoint(true, 2022040105, 'board');
    }

    if ($oldversion < 2022040109) {

        // Changing the default of field historyid on table board to 0.
        $table = new xmldb_table('board');
        $field = new xmldb_field('historyid', XMLDB_TYPE_INTEGER, '10', null, null, null, '0', 'introformat');

        // Launch change of default for field historyid.
        $dbman->change_field_default($table, $field);

        // Update all existing boards to have a 0 historyid in case they were created before this change.
        $DB->set_field(
            'board',
            'historyid',
            0,
            ['historyid' => null]
        );

        // Board savepoint reached.
        upgrade_mod_savepoint(true, 2022040109, 'board');
    }

    if ($oldversion < 2022040110) {

        // Define field deleted to be added to board_notes.
        $table = new xmldb_table('board_notes');
        $field = new xmldb_field('deleted', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'sortorder');

        // Conditionally launch add field deleted.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field deleted to be added to board_comments.
        $table = new xmldb_table('board_comments');
        $field = new xmldb_field('deleted', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'timecreated');

        // Conditionally launch add field deleted.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Board savepoint reached.
        upgrade_mod_savepoint(true, 2022040110, 'board');
    }

    if ($oldversion < 2022040114) {

        // Define field hidename to be added to board.
        $table = new xmldb_table('board');
        $field = new xmldb_field('hidename', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'name');

        // Conditionally launch add field hidename.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Board savepoint reached.
        upgrade_mod_savepoint(true, 2022040114, 'board');
    }

    return true;
}
