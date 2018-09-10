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
//
// Sometimes, changes between versions involve
// alterations to database structures and other
// major things that may break installations.
//
// The upgrade function in this file will attempt
// to perform all the necessary actions to upgrade
// your older installation to the current version.
//
// If there's something it cannot do itself, it
// will tell you what you need to do.
//
// The commands in here will all be database-neutral,
// using the methods of database_manager class
//
// Please do not forget to use upgrade_set_timeout()
// before any action that may take longer time to finish.

/**
 * This file keeps track of upgrades to the game module
 *
 * @package    mod_game
 * @copyright  2007 Vasilis Daloukas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Copies a string field to int field
 *
 * @param string $table the name of table
 * @param string $from the name of source field
 * @param string $to the name of destination field
 */
function mpgame_db_copy_string_to_int( $table, $from, $to) {
    global $CFG, $DB;

    $sql = "SELECT * FROM {$CFG->prefix}$table";
    $recs = $DB->get_records_sql( $sql);
    foreach ($recs as $rec) {
        $val = intval( $rec->$from);
        if ($rec->$to == $val) {
            continue;
        }
        if ($val == 0) {
            continue;
        }
        $updrec = new stdClass;
        $updrec->id = $rec->id;
        $updrec->$to = $val;
        $DB->update_record( $table, $updrec);
    }
}

/**
 * Upgrades database
 *
 * @param int $oldversion
 */
function xmldb_game_upgrade($oldversion) {

    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2007082802) {
        $table = new xmldb_table('game');
        $field = new xmldb_field('questioncategoryid', XMLDB_TYPE_INTEGER, '10',
            XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'glossarycategoryid');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $table = new xmldb_table('game_hangman');
        $field = new xmldb_field('quizid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, '0', 'questionid');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2007082802, 'game');
    }

    if ($oldversion < 2007082803) {
        $table = new xmldb_table('game_hangman');
        $field = new xmldb_field('glossaryid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
            null, null, '0', 'quizid');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $table = new xmldb_table('game_hangman');
        $field = new xmldb_field('glossarycategoryid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
            null, null, '0', 'glossaryid');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $table = new xmldb_table('game_hangman');
        $field = new xmldb_field('questioncategoryid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
            null, null, '0', 'glossarycategoryid');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2007082803, 'game');
    }

    if ($oldversion < 2007082804) {
        $table = new xmldb_table('game_millionaire');
        $field = new xmldb_field('questioncategoryid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
            null, null, '0', 'quizid');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2007082804, 'game');
    }

    if ($oldversion < 2007082805) {
        $table = new xmldb_table('game_hangman');
        $field = new xmldb_field('try', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
            null, null, '0', 'answer');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $table = new xmldb_table('game_hangman');
        $field = new xmldb_field('maxtries', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, '0', 'try');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2007082805, 'game');
    }

    if ($oldversion < 2007082807) {
        $table = new xmldb_table('game_hangman');
        $field = new xmldb_field('finishedword', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, '0', 'maxtries');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $table = new xmldb_table('game_hangman');
        $field = new xmldb_field('corrects', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
            null, null, '0', 'finishedword');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2007082807, 'game');
    }

    if ($oldversion < 2007082808) {
        $table = new xmldb_table('game');
        $field = new xmldb_field('param7', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
            null, null, '0', 'param6');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2007082808, 'game');
    }

    if ($oldversion < 2007082809) {
        $table = new xmldb_table('game_hangman');
        $field = new xmldb_field('letters', XMLDB_TYPE_CHAR, '30');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2007082809, 'game');
    }

    if ($oldversion < 2007082901) {
        $table = new xmldb_table('game_hangman');
        $field = new xmldb_field('glossaryid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
            null, null, '0', 'quizid');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2007082901, 'game');
    }

    if ($oldversion < 2007083002) {
        $table = new xmldb_table('game_instances');
        $field = new xmldb_field('lastip', XMLDB_TYPE_CHAR, '30', null, null, '', 'grade');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2007083002, 'game');
    }

    if ($oldversion < 2007091001) {
        $table = new xmldb_table('game_bookquiz_questions');
        $field = new xmldb_field('questioncategoryid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, '0');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2007091001, 'game');
    }

    if ($oldversion < 2007091701) {
        $table = new xmldb_table( 'game_bookquiz_chapters');

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE);
        $table->add_field('gameinstanceid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
            XMLDB_NOTNULL, null, '0');
        $table->add_field('chapterid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');

        $table->add_key('PRIMARY', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('gameinstanceidchapterid', XMLDB_KEY_NOTUNIQUE, array('gameinstanceid', 'chapterid'));

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }
        upgrade_mod_savepoint(true, 2007091701, 'game');
    }

    if ($oldversion < 2007092207) {
        $table = new xmldb_table( 'game_snakes_database');

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE);
        $table->add_field('name', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, '');
        $table->add_field('cols', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('rows', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('data', XMLDB_TYPE_TEXT, '0', null, XMLDB_NOTNULL, null, '');
        $table->add_field('file', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, '');
        $table->add_field('direction', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('headerx', XMLDB_TYPE_INTEGER, '5', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('headery', XMLDB_TYPE_INTEGER, '5', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('footerx', XMLDB_TYPE_INTEGER, '5', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('footery', XMLDB_TYPE_INTEGER, '5', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');

        $table->add_key('PRIMARY', XMLDB_KEY_PRIMARY, array('id'));

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }
        upgrade_mod_savepoint(true, 2007092207, 'game');
    }

    if ($oldversion < 2007092208) {
        $table = new xmldb_table( 'game_snakes');

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE);
        $table->add_field('snakesdatabaseid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('position', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');

        $table->add_key('PRIMARY', XMLDB_KEY_PRIMARY, array('id'));

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }
        upgrade_mod_savepoint(true, 2007092208, 'game');
    }

    if ($oldversion < 2007092301) {
        $table = new xmldb_table('game_snakes_database');
        $field = new xmldb_field('width', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, '0');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2007092301, 'game');
    }

    if ($oldversion < 2007092302) {
        $table = new xmldb_table('game_snakes_database');
        $field = new xmldb_field('height', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
            null, null, '0');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2007092302, 'game');
    }

    if ($oldversion < 2007092306) {
        $table = new xmldb_table('game_snakes');
        $field = new xmldb_field('sourcemodule', XMLDB_TYPE_CHAR, '20', null, null, null, '');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2007092306, 'game');
    }

    if ($oldversion < 2007092307) {
        $table = new xmldb_table('game_snakes');
        $field = new xmldb_field('questionid', XMLDB_TYPE_INTEGER, '10');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2007092307, 'game');
    }

    if ($oldversion < 2007092308) {
        $table = new xmldb_table('game_snakes');
        $field = new xmldb_field('glossaryentryid', XMLDB_TYPE_INTEGER, '10');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2007092308, 'game');
    }

    if ($oldversion < 2007092309) {
        $table = new xmldb_table('game_snakes');
        $field = new xmldb_field('dice', XMLDB_TYPE_INTEGER, '1');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2007092309, 'game');
    }

    if ($oldversion < 2007100601) {
        $table = new xmldb_table('game_instances');
        $field = new xmldb_field('lastremotehost', XMLDB_TYPE_CHAR, '50', null, null, null, '');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2007100601, 'game');
    }

    if ($oldversion < 2007100605) {
        $table = new xmldb_table('game_questions');
        $field = new xmldb_field('timelastattempt', XMLDB_TYPE_INTEGER, '10');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2007100605, 'game');
    }

    if ($oldversion < 2007101301) {
        $table = new xmldb_table('game_instances');
        $field = new xmldb_field('tries', XMLDB_TYPE_INTEGER, '10');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2007101301, 'game');
    }

    if ($oldversion < 2007110801) {
        $table = new xmldb_table('game_bookquiz_questions');
        $field = new xmldb_field('bookid');

        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2007110801, 'game');
    }

    if ($oldversion < 2007110802) {
        $table = new xmldb_table( 'game_grades');

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE);
        $table->add_field('gameid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('score', XMLDB_TYPE_FLOAT, null, null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');

        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('userid', XMLDB_KEY_NOTUNIQUE, array('userid'));
        $table->add_key('gameid', XMLDB_KEY_NOTUNIQUE, array('gameid'));

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }
        upgrade_mod_savepoint(true, 2007110802, 'game');
    }

    if ($oldversion < 2007110811) {
        $table = new xmldb_table('game_hangman');
        $field = new xmldb_field('sourcemodule');

        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2007110811, 'game');
    }

    if ($oldversion < 2007110812) {
        $table = new xmldb_table('game_hangman');
        $field = new xmldb_field('questionsid');

        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2007110812, 'game');
    }

    if ($oldversion < 2007110813) {
        $table = new xmldb_table('game_hangman');
        $field = new xmldb_field('quizid');

        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2007110813, 'game');
    }

    if ($oldversion < 2007110814) {
        $table = new xmldb_table('game_hangman');
        $field = new xmldb_field('glossaryid');

        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2007110814, 'game');
    }

    if ($oldversion < 2007110815) {
        $table = new xmldb_table('game_hangman');
        $field = new xmldb_field('glossarycategoryid');

        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2007110815, 'game');
    }

    if ($oldversion < 2007110816) {
        $table = new xmldb_table('game_hangman');
        $field = new xmldb_field('glossaryentryid');

        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2007110816, 'game');
    }

    if ($oldversion < 2007110818) {
        $table = new xmldb_table('game_hangman');
        $field = new xmldb_field('question');

        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2007110818, 'game');
    }

    if ($oldversion < 2007110819) {
        $table = new xmldb_table('game_hangman');
        $field = new xmldb_field('answer');

        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2007110819, 'game');
    }

    if ($oldversion < 2007110820) {
        $table = new xmldb_table('game_millionaire');
        $field = new xmldb_field('sourcemodule');

        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2007110820, 'game');
    }

    if ($oldversion < 2007110821) {
        $table = new xmldb_table('game_millionaire');
        $field = new xmldb_field('quizid');

        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2007110821, 'game');
    }

    if ($oldversion < 2007110822) {
        $table = new xmldb_table('game_millionaire');
        $field = new xmldb_field('questionid');

        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2007110822, 'game');
    }

    if ($oldversion < 2007110823) {
        $table = new xmldb_table('game_millionaire');
        $field = new xmldb_field('queryid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, '0', 'id');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2007110823, 'game');
    }

    if ($oldversion < 2007110825) {
        $table = new xmldb_table('game_sudoku');
        $field = new xmldb_field('sourcemodule');

        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2007110825, 'game');
    }

    if ($oldversion < 2007110826) {
        $table = new xmldb_table('game_millionaire');
        $field = new xmldb_field('queryid', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, null, null, '0', 'id');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2007110826, 'game');
    }

    if ($oldversion < 2007110827) {
        $table = new xmldb_table('game_sudoku');
        $field = new xmldb_field('quizid');

        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2007110827, 'game');
    }

    if ($oldversion < 2007110828) {
        $table = new xmldb_table('game_sudoku');
        $field = new xmldb_field('glossaryid');

        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2007110828, 'game');
    }

    if ($oldversion < 2007110829) {
        $table = new xmldb_table('game_sudoku');
        $field = new xmldb_field('glossarycategoryid');

        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2007110829, 'game');
    }

    if ($oldversion < 2007110830) {
        $table = new xmldb_table('game_sudoku_questions');

        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }
        upgrade_mod_savepoint(true, 2007110830, 'game');
    }

    if ($oldversion < 2007110832) {
        $table = new xmldb_table('game_cross');
        $field = new xmldb_field('sourcemodule');

        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2007110832, 'game');
    }

    if ($oldversion < 2007110833) {
        $table = new xmldb_table('game_cross');
        $field = new xmldb_field('createscore', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, '0', 'wordsall');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2007110833, 'game');
    }

    if ($oldversion < 2007110834) {
        $table = new xmldb_table( 'game_bookquiz');
        $field = new xmldb_field( 'attemptid', XMLDB_TYPE_FLOAT, null, null, null, null, '0');

        $dbman->rename_field($table, $field, 'score');

        upgrade_mod_savepoint(true, 2007110834, 'game');
    }

    if ($oldversion < 2007110835) {
        $table = new xmldb_table('game_cross');
        $field = new xmldb_field('tries');

        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2007110835, 'game');
    }

    if ($oldversion < 2007110836) {
        $table = new xmldb_table( 'game_cross');
        $field = new xmldb_field( 'timelimit', XMLDB_TYPE_FLOAT, null, null, null, null, '0');

        $dbman->rename_field($table, $field, 'createtimelimit');
        upgrade_mod_savepoint(true, 2007110836, 'game');
    }

    if ($oldversion < 2007110837) {
        $table = new xmldb_table('game_cross');
        $field = new xmldb_field('createconnectors', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, '0');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2007110837, 'game');
    }

    if ($oldversion < 2007110838) {
        $table = new xmldb_table('game_cross');
        $field = new xmldb_field('createfilleds', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, '0');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2007110838, 'game');
    }

    if ($oldversion < 2007110839) {
        $table = new xmldb_table('game_cross');
        $field = new xmldb_field('createspaces', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, '0');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2007110839, 'game');
    }

    if ($oldversion < 2007110840) {
        $table = new xmldb_table('game_cross_questions');

        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }
        upgrade_mod_savepoint(true, 2007110840, 'game');
    }

    if ($oldversion < 2007110841) {
        $table = new xmldb_table( 'game_questions');
        $dbman->rename_table( $table, 'game_queries');

        upgrade_mod_savepoint(true, 2007110841, 'game');
    }

    if ($oldversion < 2007110853) {
        $table = new xmldb_table('game_snakes');
        $field = new xmldb_field('sourcemodule');

        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2007110853, 'game');
    }

    if ($oldversion < 2007110854) {
        $table = new xmldb_table('game_snakes');
        $field = new xmldb_field('questionid');

        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2007110854, 'game');
    }

    if ($oldversion < 2007110855) {
        $table = new xmldb_table('game_snakes');
        $field = new xmldb_field('glossaryentryid');

        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2007110855, 'game');
    }

    if ($oldversion < 2007110856) {
        $table = new xmldb_table( 'game_instances');
        $dbman->rename_table( $table, 'game_attempts');

        upgrade_mod_savepoint(true, 2007110856, 'game');
    }

    if ($oldversion < 2007110857) {
        $table = new xmldb_table('game_attempts');
        $field = new xmldb_field('gamekind');

        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2007110857, 'game');
    }

    if ($oldversion < 2007110858) {
        $table = new xmldb_table('game_attempts');
        $field = new xmldb_field( 'finished');

        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2007110858, 'game');
    }

    if ($oldversion < 2007110859) {
        $table = new xmldb_table( 'game_attempts');
        $field = new xmldb_field( 'timestarted', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, '0');

        $dbman->rename_field($table, $field, 'timestart');
        upgrade_mod_savepoint(true, 2007110859, 'game');
    }

    if ($oldversion < 2007110860) {
        $table = new xmldb_table( 'game_attempts');
        $field = new xmldb_field( 'timefinished', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, '0');

        $dbman->rename_field( $table, $field, 'timefinish');
        upgrade_mod_savepoint(true, 2007110860, 'game');
    }

    if ($oldversion < 2007110861) {
        $table = new xmldb_table('game_attempts');
        $field = new xmldb_field('grade');

        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2007110861, 'game');
    }

    if ($oldversion < 2007110862) {
        $table = new xmldb_table( 'game_attempts');
        $field = new xmldb_field( 'tries', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, '0');

        $dbman->rename_field( $table, $field, 'attempts');
        upgrade_mod_savepoint(true, 2007110862, 'game');
    }

    if ($oldversion < 2007110863) {
        $table = new xmldb_table( 'game_attempts');
        $field = new xmldb_field( 'preview', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, null, null, '0', 'lastremotehost');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2007110863, 'game');
    }

    if ($oldversion < 2007110864) {
        $table = new xmldb_table( 'game_attempts');
        $field = new xmldb_field( 'attempt', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, '0', 'preview');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2007110864, 'game');
    }

    if ($oldversion < 2007110865) {
        $table = new xmldb_table( 'game_attempts');
        $field = new xmldb_field( 'score', XMLDB_TYPE_FLOAT, null, XMLDB_UNSIGNED, null, null, '0', 'attempt');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2007110865, 'game');
    }

    if ($oldversion < 2007110866) {
        $table = new xmldb_table( 'game_course_input');

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE);
        $table->add_field('name', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, '');
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('sourcemodule', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null,  '');
        $table->add_field('ids', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, '');

        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }
        upgrade_mod_savepoint(true, 2007110866, 'game');
    }

    if ($oldversion < 2007111302) {
        $table = new xmldb_table('game');
        $field = new xmldb_field('gameinputid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, '0', 'bookid');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2007111302, 'game');
    }

    if ($oldversion < 2007111303) {
        $table = new xmldb_table('game');
        $field = new xmldb_field('bottomtext', XMLDB_TYPE_TEXT);
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2007111303, 'game');
    }

    if ($oldversion < 2007111304) {
        $table = new xmldb_table('game');
        $field = new xmldb_field('grademethod', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, null, null, '0');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2007111304, 'game');
    }

    if ($oldversion < 2007111305) {
        $table = new xmldb_table('game');
        $field = new xmldb_field('grade', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, '0', 'bottomtext');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2007111305, 'game');
    }

    if ($oldversion < 2007111306) {
        $table = new xmldb_table('game');
        $field = new xmldb_field('decimalpoints', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, null, null, '0');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2007111306, 'game');
    }

    if ($oldversion < 2007111307) {
        $table = new xmldb_table('game');
        $field = new xmldb_field('popup', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, null, null, '0');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2007111307, 'game');
    }

    if ($oldversion < 2007111308) {
        $table = new xmldb_table('game');
        $field = new xmldb_field('review', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, '0');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2007111308, 'game');
    }

    if ($oldversion < 2007111309) {
        $table = new xmldb_table('game');
        $field = new xmldb_field('attempts', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, '0');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2007111309, 'game');
    }

    if ($oldversion < 2007111310) {
        $DB->execute('UPDATE {game} SET grade=0 WHERE grade IS NULL', true);

        upgrade_mod_savepoint(true, 2007111310, 'game');
    }

    if ($oldversion < 2007111842) {
        $table = new xmldb_table( 'game_queries');
        $field = new xmldb_field( 'gameinstanceid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, '0');

        $dbman->rename_field( $table, $field, 'attemptid');

        upgrade_mod_savepoint(true, 2007111842, 'game');
    }

    if ($oldversion < 2007111843) {
        $table = new xmldb_table('game_queries');
        $field = new xmldb_field('grade');

        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2007111843, 'game');
    }

    if ($oldversion < 2007111844) {
        $table = new xmldb_table('game_queries');
        $field = new xmldb_field('questiontext', XMLDB_TYPE_TEXT, null, null, null, null, '', 'glossaryentryid');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2007111844, 'game');
    }

    if ($oldversion < 2007111845) {
        $table = new xmldb_table('game_queries');
        $field = new xmldb_field('score', XMLDB_TYPE_FLOAT, null, null, null, null, '0', 'questiontext');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2007111845, 'game');
    }

    if ($oldversion < 2007111846) {
        $table = new xmldb_table('game_queries');
        $field = new xmldb_field('studentanswer', XMLDB_TYPE_TEXT, null, null, null, null, '', 'glossaryentryid');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2007111846, 'game');
    }

    if ($oldversion < 2007111847) {
        $table = new xmldb_table( 'game_queries');
        $field = new xmldb_field( 'col', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, '0');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2007111847, 'game');
    }

    if ($oldversion < 2007111848) {
        $table = new xmldb_table( 'game_queries');
        $field = new xmldb_field( 'row', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, '0');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2007111848, 'game');
    }

    if ($oldversion < 2007111849) {
        $table = new xmldb_table( 'game_queries');
        $field = new xmldb_field( 'horizontal', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, null, null, '0');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2007111849, 'game');
    }

    if ($oldversion < 2007111850) {
        $table = new xmldb_table('game_queries');
        $field = new xmldb_field('answertext', XMLDB_TYPE_TEXT);
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2007111850, 'game');
    }

    if ($oldversion < 2007111851) {
        $table = new xmldb_table( 'game_queries');
        $field = new xmldb_field( 'correct', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, '0');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2007111851, 'game');
    }

    if ($oldversion < 2007111853) {
        execute_sql('UPDATE {game} SET grademethod=1 WHERE grademethod=0 OR grademethod IS NULL', true);

        upgrade_mod_savepoint(true, 2007111853, 'game');
    }

    if ($oldversion < 2007111854) {
        $table = new xmldb_table('game_hangman');
        $field = new xmldb_field('queryid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, '0', 'id');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2007111854, 'game');
    }

    if ($oldversion < 2007111855) {
        $table = new xmldb_table('game_snakes');
        $field = new xmldb_field('queryid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, '0', 'snakesdatabaseid');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2007111855, 'game');
    }

    if ($oldversion < 2007111856) {
        $table = new xmldb_table( 'game_bookquiz_chapters');
        $field = new xmldb_field( 'attemptid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, '0', 'id');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2007111856, 'game');
    }

    if ($oldversion < 2007120103) {
        $table = new xmldb_table('game_hangman');
        $field = new xmldb_field('letters', XMLDB_TYPE_CHAR, '100');

        $dbman->change_field_precision($table, $field);
        upgrade_mod_savepoint(true, 2007120103, 'game');
    }

    if ($oldversion < 2007120104) {
        $table = new xmldb_table('game_hangman');
        $field = new xmldb_field('allletters', XMLDB_TYPE_CHAR, '100');

        $dbman->change_field_precision($table, $field);
        upgrade_mod_savepoint(true, 2007120104, 'game');
    }

    if ($oldversion < 2007120106) {
        $table = new xmldb_table('game_queries');
        $field = new xmldb_field('attachment', XMLDB_TYPE_CHAR, '100');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2007120106, 'game');
    }

    // Here starts year 2008.

    if ($oldversion < 2008011301) {
        $table = new xmldb_table('game');
        $field = new xmldb_field('glossaryid2', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, '0');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2008011301, 'game');
    }

    if ($oldversion < 2008011302) {
        $table = new xmldb_table('game');
        $field = new xmldb_field('glossarycategoryid2', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, '0');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2008011302, 'game');
    }

    if ($oldversion < 2008011308) {
        $table = new xmldb_table('game_queries');
        $field = new xmldb_field('attachment', XMLDB_TYPE_CHAR, '200', null, null, null, '');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2008011308, 'game');
    }

    if ($oldversion < 2008011504) {
        $table = new xmldb_table( 'game_hiddenpicture');

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE);
        $table->add_field('correct', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('wrong', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('found', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');

        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }
        upgrade_mod_savepoint(true, 2008011504, 'game');
    }

    if ($oldversion < 2008012701) {
        $table = new xmldb_table('game');
        $field = new xmldb_field('param8', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, '0', 'param7');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2008012701, 'game');
    }

    if ($oldversion < 2008071101) {
        $table = new xmldb_table('game');
        $field = new xmldb_field('language', XMLDB_TYPE_CHAR, '10', null, null, null, '');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2008071101, 'game');
    }

    if ($oldversion < 2008072204) {
        $table = new xmldb_table( 'game_export_javame');

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE);
        $table->add_field('gameid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('filename', XMLDB_TYPE_CHAR, '20');
        $table->add_field('icon', XMLDB_TYPE_CHAR, '100');
        $table->add_field('createdby', XMLDB_TYPE_CHAR, '50');
        $table->add_field('vendor', XMLDB_TYPE_CHAR, '50');
        $table->add_field('name', XMLDB_TYPE_CHAR, '20');
        $table->add_field('description', XMLDB_TYPE_CHAR, '100');
        $table->add_field('version', XMLDB_TYPE_CHAR, '10');

        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('gameid', XMLDB_KEY_UNIQUE, array('gameid'));

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }
        upgrade_mod_savepoint(true, 2008072204, 'game');
    }

    if ($oldversion < 2008072501) {
        $table = new xmldb_table('game_hangman');
        $field = new xmldb_field('quizid');

        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2008072501, 'game');
    }

    if ($oldversion < 2008072502) {
        $table = new xmldb_table('game_hangman');
        $field = new xmldb_field('glossaryid');

        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2008072502, 'game');
    }

    if ($oldversion < 2008072503) {
        $table = new xmldb_table('game_hangman');
        $field = new xmldb_field('questioncategoryid');

        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2008072503, 'game');
    }

    if ($oldversion < 2008072504) {
        $table = new xmldb_table('game_hangman');
        $field = new xmldb_field('gameinputid');

        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2008072504, 'game');
    }

    if ($oldversion < 2008090101) {
        $table = new xmldb_table('game');
        $field = new xmldb_field('subcategories', XMLDB_TYPE_INTEGER, '1');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2008090101, 'game');
    }

    if ($oldversion < 2008101103) {
        $table = new xmldb_table('game_millionaire');
        $field = new xmldb_field('state', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, null, null, '0');

        $dbman->change_field_precision($table, $field);
        upgrade_mod_savepoint(true, 2008101103, 'game');
    }

    if ($oldversion < 2008101104) {
        $table = new xmldb_table('game_millionaire');
        $field = new xmldb_field('level', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, null, null, '0');

        $dbman->change_field_precision($table, $field);
        upgrade_mod_savepoint(true, 2008101104, 'game');
    }

    if ($oldversion < 2008101106) {
        $table = new xmldb_table('game_sudoku');
        $field = new xmldb_field('level', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, null, null, '0');

        $dbman->cchange_field_precision($table, $field);
        upgrade_mod_savepoint(true, 2008101106, 'game');
    }

    if ($oldversion < 2008101107) {
        $table = new xmldb_table('game_hiddenpicture');
        $field = new xmldb_field('correct', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, null, null, '0');

        $dbman->change_field_precision($table, $field);
        upgrade_mod_savepoint(true, 2008101107, 'game');
    }

    if ($oldversion < 2008101108) {
        $table = new xmldb_table('game_hiddenpicture');
        $field = new xmldb_field('wrong', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, null, null, '0');

        $dbman->change_field_precision($table, $field);
        upgrade_mod_savepoint(true, 2008101108, 'game');
    }

    if ($oldversion < 2008101109) {
        $table = new xmldb_table('game_hiddenpicture');
        $field = new xmldb_field('found', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, null, null, '0');

        $dbman->change_field_precision($table, $field);
        upgrade_mod_savepoint(true, 2008101109, 'game');
    }

    if ($oldversion < 2008102701) {
        $table = new xmldb_table('game_queries');
        $field = new xmldb_field('answerid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, '0');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2008102701, 'game');
    }

    if ($oldversion < 2008110701) {
        $table = new xmldb_table( 'game_export_html');

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE);
        $table->add_field('gameid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('filename', XMLDB_TYPE_CHAR, '30');
        $table->add_field('title', XMLDB_TYPE_CHAR, '200');
        $table->add_field('checkbutton', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL);
        $table->add_field('printbutton', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL);

        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('gameid', XMLDB_INDEX_UNIQUE, array('gameid'));

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }
        upgrade_mod_savepoint(true, 2008110701, 'game');
    }

    if ($oldversion < 2008111701) {
        $table = new xmldb_table( 'game_snakes_database');
        $field = new xmldb_field( 'file', XMLDB_TYPE_CHAR, 100, null, null, null,  '');

        $dbman->rename_field( $table, $field, 'fileboard');
        upgrade_mod_savepoint(true, 2008111701, 'game');
    }

    if ($oldversion < 2008111801) {
        $table = new xmldb_table('game');
        $field = new xmldb_field('bottomtext', XMLDB_TYPE_TEXT);
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2008111801, 'game');
    }

    // Year 2009 starts here.

    if ($oldversion < 2009010502) {
        $table = new xmldb_table('game_export_javame');
        $field = new xmldb_field('maxpicturewidth', XMLDB_TYPE_INTEGER, '7');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2009010502, 'game');
    }

    if ($oldversion < 2009031801) {
        $table = new xmldb_table('game_repetitions');

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE);
        $table->add_field('gameid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('questionid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('glossaryentryid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('repetitions', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');

        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('main', XMLDB_KEY_UNIQUE, array('gameid,userid,questionid,glossaryentryid'));

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }
        upgrade_mod_savepoint(true, 2009031801, 'game');
    }

    if ($oldversion < 2009071403) {
        $table = new xmldb_table('game');
        $field = new xmldb_field('shuffle', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, null, null, '1', 'param8');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2009071403, 'game');
    }

    if ($oldversion < 2009072801) {
        $table = new xmldb_table('game_export_html');
        $field = new xmldb_field('inputsize', XMLDB_TYPE_INTEGER, '3', XMLDB_UNSIGNED);
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2009072801, 'game');
    }

    if ($oldversion < 2009072901) {
        $table = new xmldb_table('game_export_html');
        $field = new xmldb_field('maxpicturewidth', XMLDB_TYPE_INTEGER, '7');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2009072901, 'game');
    }

    if ($oldversion < 2009073101) {
        $table = new xmldb_table('game_export_html');
        $field = new xmldb_field('maxpictureheight', XMLDB_TYPE_INTEGER, '7');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2009073101, 'game');
    }

    if ($oldversion < 2009073102) {
        $table = new xmldb_table('game_export_javame');
        $field = new xmldb_field('maxpictureheight', XMLDB_TYPE_INTEGER, '7');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2009073102, 'game');
    }

    if ($oldversion < 2009083102) {
        $table = new xmldb_table('game');
        $field = new xmldb_field('toptext', XMLDB_TYPE_TEXT, null, null, null, null, null, 'gameinputid');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2009083102, 'game');
    }

    if ($oldversion < 2010031101) {
        $table = new xmldb_table('game_queries');
        $field = new xmldb_field('tries', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, null, null, '0', 'answerid');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2010031101, 'game');
    }

    if ($oldversion < 2010071606) {
        $table = new xmldb_table('game_export_html');
        $field = new xmldb_field('id');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2010071606, 'game');
    }

    if ($oldversion < 2010071607) {
        $table = new xmldb_table( 'game_export_html');
        $field = new xmldb_field( 'gameid', XMLDB_TYPE_INTEGER, 10, null, null, null, null, null, '0');

        $dbman->rename_field($table, $field, 'id');

        upgrade_mod_savepoint(true, 2010071607, 'game');
    }

    if ($oldversion < 2010071609) {
        $table = new xmldb_table('game_export_javame');
        $field = new xmldb_field('id');

        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2010071609, 'game');
    }

    if ($oldversion < 2010071610) {
        $table = new xmldb_table( 'game_export_javame');
        $field = new xmldb_field( 'gameid', XMLDB_TYPE_INTEGER, 10, null, null, null, null, null, '0');

        $dbman->rename_field($table, $field, 'id');

        upgrade_mod_savepoint(true, 2010071610, 'game');
    }

    if ($oldversion < 2010072605) {

        // Define field language to be added to game_attempts.
        $table = new xmldb_table('game_attempts');
        $field = new xmldb_field('language', XMLDB_TYPE_CHAR, '10', null, null, null, null, 'attempts');

        // Conditionally launch add field language.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Game savepoint reached.
        upgrade_mod_savepoint(true, 2010072605, 'game');
    }

    if ($oldversion < 2010090301) {

        // Define field param9 to be added to game.
        $table = new xmldb_table('game');
        $field = new xmldb_field('param9', XMLDB_TYPE_TEXT, 'medium', null, null, null, null, 'param8');

        // Conditionally launch add field param9.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Game savepoint reached.
        upgrade_mod_savepoint(true, 2010090301, 'game');
    }

    if ($oldversion < 2011072703) {
        $table = new xmldb_table('game_bookquiz');
        $field = new xmldb_field('bookid');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2011072703, 'game');
    }

    if ($oldversion < 2011072704) {
        $table = new xmldb_table('game_export_javame');
        $field = new xmldb_field('type', XMLDB_TYPE_CHAR, '10');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2011072704, 'game');
    }

    if ($oldversion < 2011072705) {
        $table = new xmldb_table('game_export_html');
        $field = new xmldb_field('type', XMLDB_TYPE_CHAR, '10');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2011072705, 'game');
    }

    if ($oldversion < 2011072902) {

        // Define field param10 to be added to game.
        $table = new xmldb_table('game');
        $field = new xmldb_field('param10', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'param9');

        // Conditionally launch add field param10.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Game savepoint reached.
        upgrade_mod_savepoint(true, 2011072902, 'game');
    }

    if ($oldversion < 2011082603) {

        // Define field timeopen to be added to game.
        $table = new xmldb_table('game');
        $field = new xmldb_field('timeopen', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, '0', 'sourcemodule');

        // Conditionally launch add field timeopen.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Game savepoint reached.
        upgrade_mod_savepoint(true, 2011082603, 'game');
    }

    if ($oldversion < 2011082604) {

        // Define field timeclose to be added to game.
        $table = new xmldb_table('game');
        $field = new xmldb_field('timeclose', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, '0', 'timeopen');

        // Conditionally launch add field timeclose.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Game savepoint reached.
        upgrade_mod_savepoint(true, 2011082604, 'game');
    }

    if ($oldversion < 2013072601) {

        // Define field timeclose to be added to game.
        $table = new xmldb_table('game');
        $field = new xmldb_field('maxattempts', XMLDB_TYPE_INTEGER, '3', XMLDB_UNSIGNED, null, null, '0', 'subcategories');

        // Conditionally launch add field maxattempts.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Game savepoint reached.
        upgrade_mod_savepoint(true, 2013072601, 'game');
    }

    if ($oldversion < 2015122105) {
        $table = new xmldb_table('game');
        $field = new xmldb_field('userlanguage', XMLDB_TYPE_CHAR, '100');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2015122105, 'game');
    }

    if ($oldversion < ($ver = 2015123101)) {
        $table = new xmldb_table('game');
        $field = new xmldb_field('disablesummarize', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, null, null, '0');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, $ver, 'game');
    }

    if ($oldversion < ($ver = 2016061402)) {
        $table = new xmldb_table('game');
        $field = new xmldb_field('glossaryonlyapproved', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, null, null, '0');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, $ver, 'game');
    }

    if ($oldversion < ($ver = 2016062603)) {
        $table = new xmldb_table('game_cross');
        $field = new xmldb_field('createscore', XMLDB_TYPE_FLOAT, null, null, null, null, '0');

        // Launch change of type for field thisfield.
        $dbman->change_field_type($table, $field);

        upgrade_mod_savepoint(true, $ver, 'game');
    }

    if ($oldversion < ($ver = 2017062801)) {
        $table = new xmldb_table('game_cross');
        $field = new xmldb_field('usedrows', XMLDB_TYPE_INTEGER, '3', XMLDB_UNSIGNED, null, null, '0', 'id');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('rows', XMLDB_TYPE_INTEGER, '3', XMLDB_UNSIGNED, null, null, '0', 'id');
        if ($dbman->field_exists($table, $field)) {
            mpgame_db_copy_string_to_int( 'game_cross', 'rows', 'usedrows');
        } else {
            $dbman->add_field($table, $field);
        }

    }

    if ($oldversion < ($ver = 2017062801)) {
        $table = new xmldb_table('game_cross');
        $field = new xmldb_field('usedcols', XMLDB_TYPE_INTEGER, '3', XMLDB_UNSIGNED, null, null, '0', 'id');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('cols', XMLDB_TYPE_INTEGER, '3', XMLDB_UNSIGNED, null, null, '0', 'id');
        if ($dbman->field_exists($table, $field)) {
            mpgame_db_copy_string_to_int( 'game_cross', 'cols', 'usedcols');
        } else {
            $dbman->add_field($table, $field);
        }
    }

    if ($oldversion < ($ver = 2017062801)) {
        $table = new xmldb_table('game_snakes_database');
        $field = new xmldb_field('usedrows', XMLDB_TYPE_INTEGER, '3', XMLDB_UNSIGNED, null, null, '0', 'id');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('rows', XMLDB_TYPE_INTEGER, '3', XMLDB_UNSIGNED, null, null, '0', 'id');
        if ($dbman->field_exists($table, $field)) {
            mpgame_db_copy_string_to_int( 'game_snakes_database', 'rows', 'usedrows');
        } else {
            $dbman->add_field($table, $field);
        }
    }

    if ($oldversion < ($ver = 2017062801)) {
        $table = new xmldb_table('game_snakes_database');
        $field = new xmldb_field('usedcols', XMLDB_TYPE_INTEGER, '3', XMLDB_UNSIGNED, null, null, '0', 'id');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('cols', XMLDB_TYPE_INTEGER, '3', XMLDB_UNSIGNED, null, null, '0', 'id');
        if ($dbman->field_exists($table, $field)) {
            mpgame_db_copy_string_to_int( 'game_snakes_database', 'cols', 'usedcols');
        } else {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, $ver, 'game');
    }

    if ($oldversion < ($ver = 2017070301)) {
        $sql = "UPDATE {$CFG->prefix}game SET glossarycategoryid=0 WHERE glossarycategoryid < 0";
        $DB->execute( $sql);

        upgrade_mod_savepoint(true, $ver, 'game');
    }

    if ($oldversion < ($ver = 2017070403)) {
        $sql = "SELECT * FROM {$CFG->prefix}game_snakes_database";
        $recs = $DB->get_records_sql( $sql);
        foreach ($recs as $rec) {
            if (($rec->usedcols != 0) and ($rec->usedrows != 0)) {
                continue;
            }

            $updrec = new stdClass;
            $updrec->id = $rec->id;
            if ( $rec->id == 1) {
                $value = 8;
            } else if ( $rec->id == 2) {
                $value = 6;
            } else {
                continue;
            }
            if ($rec->usedcols == 0) {
                $updrec->usedcols = $value;
            }
            if ($rec->usedrows == 0) {
                $updrec->usedrows = $value;
            }
            $DB->update_record( 'game_snakes_database', $updrec);
        }

        upgrade_mod_savepoint(true, $ver, 'game');
    }

    if ($oldversion < ($ver = 2017071901)) {

        // Define field completionattemptsexhausted to be added to game.
        $table = new xmldb_table('game');
        $field = new xmldb_field('completionattemptsexhausted', XMLDB_TYPE_INTEGER, '1', null, null, null, '0');

        // Conditionally launch add field completionattemptsexhausted.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, $ver, 'game');
    }

    if ($oldversion < ($ver = 2017071902)) {
        // Define field completionpass to be added to game.
        $table = new xmldb_table('game');
        $field = new xmldb_field('completionpass', XMLDB_TYPE_INTEGER, '1', null, null, null, 0, 'completionattemptsexhausted');

        // Conditionally launch add field completionpass.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, $ver, 'game');
    }

    if ($oldversion < ($ver = 2018060401)) {
        // Define field highscore to be added to game.
        $table = new xmldb_table('game');
        $field = new xmldb_field('highscore', XMLDB_TYPE_INTEGER, '2', null, null, null, 0);

        // Conditionally launch add field completionpass.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, $ver, 'game');
    }

    if ($oldversion < ($ver = 2018060402)) {
        // Change the number of imageset on hangman to 2.
        $config = get_config('game');
        if ($config->hangmanimagesets < 2) {
            set_config( 'hangmanimagesets', 2, 'game');
        }

        upgrade_mod_savepoint(true, $ver, 'game');
    }

    if ($oldversion < ($ver = 2018060404)) {
        // Import 2 new boards.

        require( 'importsnakes.php');
        $sql = "SELECT * FROM {$CFG->prefix}game_snakes_database WHERE fileboard='fidaki3.jpg'";
        $rec = $DB->get_record_sql( $sql);
        if ($rec === false) {
            game_importsnakes3();
        }
        $sql = "SELECT * FROM {$CFG->prefix}game_snakes_database WHERE fileboard='fidaki4.jpg'";
        $rec = $DB->get_record_sql( $sql);
        if ($rec === false) {
            game_importsnakes4();
        }

        upgrade_mod_savepoint(true, $ver, 'game');
    }

    return true;
}

