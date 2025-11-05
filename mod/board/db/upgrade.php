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

use mod_board\board;
use mod_board\local\install;

defined('MOODLE_INTERNAL') || die;

require_once(__DIR__ . '/upgradelib.php');

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

    if ($oldversion < 2025070702) {
        // Make sure ownerid is set in all records.
        $sql = "UPDATE {board_notes}
                   SET ownerid = userid
                 WHERE ownerid IS NULL";
        $DB->execute($sql);

        // Changing nullability of field ownerid on table board_notes to not null.
        $table = new xmldb_table('board_notes');
        $field = new xmldb_field('ownerid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'columnid');

        // Launch change of nullability for field ownerid.
        $dbman->change_field_notnull($table, $field);

        // Board savepoint reached.
        upgrade_mod_savepoint(true, 2025070702, 'board');
    }

    if ($oldversion < 2025070703) {
        // Group mode is not used in private and public single user modes.
        $sql = "UPDATE {board_notes}
                   SET groupid = NULL
                 WHERE columnid IN (
                     SELECT c.id
                       FROM {board_columns} c
                       JOIN {board} b ON b.id = c.boardid
                      WHERE b.singleusermode = :private OR b.singleusermode = :public
                 )";
        $DB->execute($sql, ['private' => board::SINGLEUSER_PRIVATE, 'public' => board::SINGLEUSER_PUBLIC]);

        // Board savepoint reached.
        upgrade_mod_savepoint(true, 2025070703, 'board');
    }

    if ($oldversion < 2025070704) {
        // Make sure locked is set in all records.
        $sql = "UPDATE {board_columns}
                   SET locked = 0
                 WHERE locked IS NULL";
        $DB->execute($sql);

        // Changing nullability of field locked on table board_columns to not null.
        $table = new xmldb_table('board_columns');
        $field = new xmldb_field('locked', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0', 'name');

        // Launch change of nullability for field locked.
        $dbman->change_field_notnull($table, $field);

        // Launch change of default for field locked.
        $dbman->change_field_default($table, $field);

        // Board savepoint reached.
        upgrade_mod_savepoint(true, 2025070704, 'board');
    }

    if ($oldversion < 2025070706) {
        // Remove duplicate index on board_columns.boardid field.
        $table = new xmldb_table('board_columns');
        $key = new xmldb_key('fk_board', XMLDB_KEY_FOREIGN, ['boardid'], 'board', ['id']);
        $dbman->drop_key($table, $key);
        $index = new xmldb_index('boardid', XMLDB_INDEX_NOTUNIQUE, ['boardid']);
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }
        $dbman->add_key($table, $key);

        // Remove duplicate index on board_notes.columnid field.
        $table = new xmldb_table('board_notes');
        $key = new xmldb_key('fk_column', XMLDB_KEY_FOREIGN, ['columnid'], 'board_columns', ['id']);
        $dbman->drop_key($table, $key);
        $index = new xmldb_index('columnid', XMLDB_INDEX_NOTUNIQUE, ['columnid']);
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }
        $dbman->add_key($table, $key);

        // Remove duplicate index on board_history.boardid field.
        $table = new xmldb_table('board_history');
        $key = new xmldb_key('fk_board', XMLDB_KEY_FOREIGN, ['boardid'], 'board', ['id']);
        $dbman->drop_key($table, $key);
        $index = new xmldb_index('boardid', XMLDB_INDEX_NOTUNIQUE, ['boardid']);
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }
        $dbman->add_key($table, $key);

        // Define key fk_course (foreign) to be added to board.
        $table = new xmldb_table('board');
        $key = new xmldb_key('fk_course', XMLDB_KEY_FOREIGN, ['course'], 'course', ['id']);
        $dbman->add_key($table, $key);

        // Define key fk_ownerid (foreign) to be added to board_notes.
        $table = new xmldb_table('board_notes');
        $key = new xmldb_key('fk_ownerid', XMLDB_KEY_FOREIGN, ['ownerid'], 'user', ['id']);
        $dbman->add_key($table, $key);

        // Define key fk_groupid (foreign) to be added to board_notes.
        $table = new xmldb_table('board_notes');
        $key = new xmldb_key('fk_groupid', XMLDB_KEY_FOREIGN, ['groupid'], 'groups', ['id']);
        $dbman->add_key($table, $key);

        // Define key fk_comment_noteid (foreign) to be added to board_comments.
        $table = new xmldb_table('board_comments');
        $key = new xmldb_key('fk_comment_noteid', XMLDB_KEY_FOREIGN, ['noteid'], 'board_notes', ['id']);
        $dbman->add_key($table, $key);

        // Board savepoint reached.
        upgrade_mod_savepoint(true, 2025070706, 'board');
    }

    if ($oldversion < 2025070707) {
        // Fix historyid default and make it NOT NULL.
        $DB->set_field('board', 'historyid', 0, ['historyid' => null]);

        // Changing nullability of field historyid on table board to not null and add 0 as default.
        $table = new xmldb_table('board');
        $field = new xmldb_field('historyid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'introformat');
        $dbman->change_field_notnull($table, $field);
        $dbman->change_field_default($table, $field);

        // Board savepoint reached.
        upgrade_mod_savepoint(true, 2025070707, 'board');
    }

    if ($oldversion < 2025070708) {
        // Changing precision of field url on table board_notes to (1333).
        $table = new xmldb_table('board_notes');
        $field = new xmldb_field('url', XMLDB_TYPE_CHAR, '1333', null, null, null, null, 'info');

        // Launch change of precision for field url.
        $dbman->change_field_precision($table, $field);

        // Define field filename to be added to board_notes.
        $table = new xmldb_table('board_notes');
        $field = new xmldb_field('filename', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'url');

        // Conditionally launch add field filename.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        mod_board_migrate_image_url_to_filename();

        // Board savepoint reached.
        upgrade_mod_savepoint(true, 2025070708, 'board');
    }

    if ($oldversion < 2025070709) {
        // Define table board_templates to be created.
        $table = new xmldb_table('board_templates');

        // Adding fields to table board_templates.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('contextid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('description', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('columns', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('jsonsettings', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table board_templates.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('contextid', XMLDB_KEY_FOREIGN, ['contextid'], 'context', ['id']);

        // Conditionally launch create table for board_templates.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Board savepoint reached.
        upgrade_mod_savepoint(true, 2025070709, 'board');
    }

    if ($oldversion < 2025070711) {
        $table = new xmldb_table('board');

        $DB->set_field('board', 'addrating', '0', ['addrating' => null]);
        $field = new xmldb_field('addrating', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'background_color');
        $dbman->change_field_notnull($table, $field);

        $DB->set_field('board', 'hideheaders', '0', ['hideheaders' => null]);
        $field = new xmldb_field('hideheaders', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'addrating');
        $dbman->change_field_notnull($table, $field);

        $DB->set_field('board', 'sortby', '1', ['sortby' => null]);
        $field = new xmldb_field('sortby', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1', 'hideheaders');
        $dbman->change_field_notnull($table, $field);

        $DB->set_field('board', 'postby', '0', ['postby' => null]);
        $field = new xmldb_field('postby', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'sortby');
        $dbman->change_field_notnull($table, $field);

        $DB->set_field('board', 'userscanedit', '0', ['userscanedit' => null]);
        $field = new xmldb_field('userscanedit', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'postby');
        $dbman->change_field_notnull($table, $field);

        $DB->set_field('board', 'singleusermode', '0', ['singleusermode' => null]);
        $field = new xmldb_field('singleusermode', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0', 'userscanedit');
        $dbman->change_field_notnull($table, $field);

        $DB->set_field('board', 'enableblanktarget', '0', ['enableblanktarget' => null]);
        $field = new xmldb_field('enableblanktarget', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'singleusermode');
        $dbman->change_field_notnull($table, $field);

        $DB->set_field('board', 'completionnotes', '0', ['completionnotes' => null]);
        $field = new xmldb_field('completionnotes', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'enableblanktarget');
        $dbman->change_field_notnull($table, $field);

        $DB->set_field('board', 'embed', '0', ['embed' => null]);
        $field = new xmldb_field('embed', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'completionnotes');
        $dbman->change_field_notnull($table, $field);

        // Board savepoint reached.
        upgrade_mod_savepoint(true, 2025070711, 'board');
    }

    if ($oldversion < 2025070714) {
        install::setup_builtin_templates();
        // Board savepoint reached.
        upgrade_mod_savepoint(true, 2025070714, 'board');
    }

    return true;
}
