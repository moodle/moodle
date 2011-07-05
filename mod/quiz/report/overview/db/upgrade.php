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
 * Quiz overview report upgrade script.
 *
 * @package    quiz
 * @subpackage overview
 * @copyright  2008 Jamie Pratt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Quiz overview report upgrade function.
 * @param number $oldversion
 */
function xmldb_quiz_overview_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    //===== 1.9.0 upgrade line ======//

    if ($oldversion < 2009091400) {

        // Define table quiz_question_regrade to be created
        $table = new xmldb_table('quiz_question_regrade');

        // Adding fields to table quiz_question_regrade
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
                XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('questionid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
                XMLDB_NOTNULL, null, null);
        $table->add_field('attemptid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
                XMLDB_NOTNULL, null, null);
        $table->add_field('newgrade', XMLDB_TYPE_NUMBER, '12, 7', null,
                XMLDB_NOTNULL, null, null);
        $table->add_field('oldgrade', XMLDB_TYPE_NUMBER, '12, 7', null,
                XMLDB_NOTNULL, null, null);
        $table->add_field('regraded', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED,
                XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
                XMLDB_NOTNULL, null, null);

        // Adding keys to table quiz_question_regrade
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for quiz_question_regrade
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // overview savepoint reached
        upgrade_plugin_savepoint(true, 2009091400, 'quiz', 'overview');
    }

    if ($oldversion < 2010040600) {

        // Wipe the quiz_question_regrade before we changes its structure. The data
        // It contains is not important long-term, and it is almost impossible to upgrade.
        $DB->delete_records('quiz_question_regrade');

        // overview savepoint reached
        upgrade_plugin_savepoint(true, 2010040600, 'quiz', 'overview');
    }

    if ($oldversion < 2010040601) {

        // Rename field attemptid on table quiz_question_regrade to questionusageid
        $table = new xmldb_table('quiz_question_regrade');
        $field = new xmldb_field('attemptid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
                XMLDB_NOTNULL, null, null, 'id');

        // Launch rename field questionusageid
        $dbman->rename_field($table, $field, 'questionusageid');

        // overview savepoint reached
        upgrade_plugin_savepoint(true, 2010040601, 'quiz', 'overview');
    }

    if ($oldversion < 2010040602) {

        // Define field slot to be added to quiz_question_regrade
        $table = new xmldb_table('quiz_question_regrade');
        $field = new xmldb_field('slot', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
                XMLDB_NOTNULL, null, null, 'questionusageid');

        // Conditionally launch add field slot
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // overview savepoint reached
        upgrade_plugin_savepoint(true, 2010040602, 'quiz', 'overview');
    }

    if ($oldversion < 2010040603) {

        // Define field questionid to be dropped from quiz_question_regrade
        $table = new xmldb_table('quiz_question_regrade');
        $field = new xmldb_field('questionid');

        // Conditionally launch drop field questionusageid
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // overview savepoint reached
        upgrade_plugin_savepoint(true, 2010040603, 'quiz', 'overview');
    }

    if ($oldversion < 2010040604) {

        // Rename field newgrade on table quiz_question_regrade to newfraction
        $table = new xmldb_table('quiz_question_regrade');
        $field = new xmldb_field('newgrade', XMLDB_TYPE_NUMBER, '12, 7', null,
                null, null, null, 'slot');

        // Launch rename field newfraction
        $dbman->rename_field($table, $field, 'newfraction');

        // overview savepoint reached
        upgrade_plugin_savepoint(true, 2010040604, 'quiz', 'overview');
    }

    if ($oldversion < 2010040605) {

        // Rename field oldgrade on table quiz_question_regrade to oldfraction
        $table = new xmldb_table('quiz_question_regrade');
        $field = new xmldb_field('oldgrade', XMLDB_TYPE_NUMBER, '12, 7', null,
                null, null, null, 'slot');

        // Launch rename field newfraction
        $dbman->rename_field($table, $field, 'oldfraction');

        // overview savepoint reached
        upgrade_plugin_savepoint(true, 2010040605, 'quiz', 'overview');
    }

    if ($oldversion < 2010040606) {

        // Changing precision of field newfraction on table quiz_question_regrade to (12, 7)
        $table = new xmldb_table('quiz_question_regrade');
        $field = new xmldb_field('newfraction', XMLDB_TYPE_NUMBER, '12, 7', null,
                null, null, null, 'slot');

        // Launch change of precision for field newfraction
        $dbman->change_field_precision($table, $field);

        // overview savepoint reached
        upgrade_plugin_savepoint(true, 2010040606, 'quiz', 'overview');
    }

    if ($oldversion < 2010040607) {

        // Changing precision of field oldfraction on table quiz_question_regrade to (12, 7)
        $table = new xmldb_table('quiz_question_regrade');
        $field = new xmldb_field('oldfraction', XMLDB_TYPE_NUMBER, '12, 7', null,
                null, null, null, 'slot');

        // Launch change of precision for field newfraction
        $dbman->change_field_precision($table, $field);

        // overview savepoint reached
        upgrade_plugin_savepoint(true, 2010040607, 'quiz', 'overview');
    }

    if ($oldversion < 2010082700) {

        // Changing nullability of field newfraction on table quiz_question_regrade to null
        $table = new xmldb_table('quiz_question_regrade');
        $field = new xmldb_field('newfraction', XMLDB_TYPE_NUMBER, '12, 7', null,
                null, null, null, 'slot');

        // Launch change of nullability for field newfraction
        $dbman->change_field_notnull($table, $field);

        // overview savepoint reached
        upgrade_plugin_savepoint(true, 2010082700, 'quiz', 'overview');
    }

    if ($oldversion < 2010082701) {

        // Changing nullability of field oldfraction on table quiz_question_regrade to null
        $table = new xmldb_table('quiz_question_regrade');
        $field = new xmldb_field('oldfraction', XMLDB_TYPE_NUMBER, '12, 7', null,
                null, null, null, 'slot');

        // Launch change of nullability for field newfraction
        $dbman->change_field_notnull($table, $field);

        // overview savepoint reached
        upgrade_plugin_savepoint(true, 2010082701, 'quiz', 'overview');
    }

    if ($oldversion < 2011021600) {

        // Define table quiz_question_regrade to be renamed to quiz_overview_regrades
        // so that it follows the Moodle coding guidelines.
        $table = new xmldb_table('quiz_question_regrade');

        // Launch rename table for quiz_question_regrade
        $dbman->rename_table($table, 'quiz_overview_regrades');

        // overview savepoint reached
        upgrade_plugin_savepoint(true, 2011021600, 'quiz', 'overview');
    }

    // Moodle v2.1.0 release upgrade line
    // Put any upgrade step following this

    return true;
}
