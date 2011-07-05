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
 * Post-install script for the quiz statistics report.
 *
 * @package    quiz
 * @subpackage statistics
 * @copyright  2008 Jamie Pratt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Quiz statistics report upgrade code.
 */
function xmldb_quiz_statistics_upgrade($oldversion) {

    global $DB;

    $dbman = $DB->get_manager();

    // In Moodle 2.0, this table was incorrectly called quiz_report, which breaks
    // the moodle coding guidelines. In 2.1 it was renamed to quiz_reports. This
    // bit of code lets us handle all the various upgrade paths without problems.
    if ($dbman->table_exists('quiz_reports')) {
        $quizreportstablename = 'quiz_reports';
    } else {
        $quizreportstablename = 'quiz_report';
    }

    //===== 1.9.0 upgrade line ======//

    if ($oldversion < 2008072401) {
        //register cron to run every 5 hours.
        $DB->set_field($quizreportstablename, 'cron', HOURSECS*5, array('name'=>'statistics'));

        // statistics savepoint reached
        upgrade_plugin_savepoint(true, 2008072401, 'quiz', 'statistics');
    }

    if ($oldversion < 2008072500) {

        // Define field s to be added to quiz_question_statistics
        $table = new xmldb_table('quiz_question_statistics');
        $field = new xmldb_field('s', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
                XMLDB_NOTNULL, null, '0', 'subquestion');

        // Conditionally launch add field s
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // statistics savepoint reached
        upgrade_plugin_savepoint(true, 2008072500, 'quiz', 'statistics');
    }

    if ($oldversion < 2008072800) {

        // Define field maxgrade to be added to quiz_question_statistics
        $table = new xmldb_table('quiz_question_statistics');
        $field = new xmldb_field('maxgrade', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
                null, null, null, 'subquestions');

        // Conditionally launch add field maxgrade
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // statistics savepoint reached
        upgrade_plugin_savepoint(true, 2008072800, 'quiz', 'statistics');
    }

    if ($oldversion < 2008072801) {

        // Define field positions to be added to quiz_question_statistics
        $table = new xmldb_table('quiz_question_statistics');
        $field = new xmldb_field('positions', XMLDB_TYPE_TEXT, 'medium', null,
                null, null, null, 'maxgrade');

        // Conditionally launch add field positions
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // statistics savepoint reached
        upgrade_plugin_savepoint(true, 2008072801, 'quiz', 'statistics');
    }

    if ($oldversion < 2008081500) {
        // Changing type of field maxgrade on table quiz_question_statistics to number
        $table = new xmldb_table('quiz_question_statistics');
        $field = new xmldb_field('maxgrade', XMLDB_TYPE_NUMBER, '12, 7', null,
                null, null, null, 'subquestions');

        // Launch change of type for field maxgrade
        $dbman->change_field_type($table, $field);

        // statistics savepoint reached
        upgrade_plugin_savepoint(true, 2008081500, 'quiz', 'statistics');
    }

    if ($oldversion < 2008082600) {

        // Define table quiz_question_response_stats to be created
        $table = new xmldb_table('quiz_question_response_stats');

        // Adding fields to table quiz_question_response_stats
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
                XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('quizstatisticsid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
                XMLDB_NOTNULL, null, null);
        $table->add_field('questionid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
                XMLDB_NOTNULL, null, null);
        $table->add_field('anssubqid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
                null, null, null);
        $table->add_field('response', XMLDB_TYPE_TEXT, 'big', null,
                null, null, null);
        $table->add_field('rcount', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
                null, null, null);
        $table->add_field('credit', XMLDB_TYPE_NUMBER, '15, 5', null,
                XMLDB_NOTNULL, null, null);

        // Adding keys to table quiz_question_response_stats
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for quiz_question_response_stats
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // statistics savepoint reached
        upgrade_plugin_savepoint(true, 2008082600, 'quiz', 'statistics');
    }

    if ($oldversion < 2008090500) {
        //delete all cached results first
        $DB->delete_records('quiz_statistics');
        $DB->delete_records('quiz_question_statistics');
        $DB->delete_records('quiz_question_response_stats');
        // Define field anssubqid to be dropped from quiz_question_response_stats
        $table = new xmldb_table('quiz_question_response_stats');
        $field = new xmldb_field('anssubqid');

        // Conditionally launch drop field subqid
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define field subqid to be added to quiz_question_response_stats
        $field = new xmldb_field('subqid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
                XMLDB_NOTNULL, null, null, 'questionid');

        // Conditionally launch add field subqid
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field aid to be added to quiz_question_response_stats
        $field = new xmldb_field('aid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
                XMLDB_NOTNULL, null, null, 'subqid');

        // Conditionally launch add field aid
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // statistics savepoint reached
        upgrade_plugin_savepoint(true, 2008090500, 'quiz', 'statistics');
    }

    if ($oldversion < 2008111000) {
        // Delete all cached results first
        $DB->delete_records('quiz_statistics');
        $DB->delete_records('quiz_question_statistics');
        $DB->delete_records('quiz_question_response_stats');

        // Define field anssubqid to be dropped from quiz_question_response_stats
        $table = new xmldb_table('quiz_question_statistics');
        // Define field subqid to be added to quiz_question_response_stats
        $field = new xmldb_field('negcovar', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED,
                XMLDB_NOTNULL, null, '0', 'effectiveweight');

        // Conditionally launch add field subqid
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // statistics savepoint reached
        upgrade_plugin_savepoint(true, 2008111000, 'quiz', 'statistics');
    }

    if ($oldversion < 2008112100) {
        $DB->set_field($quizreportstablename, 'capability', 'quizreport/statistics:view',
                array('name'=>'statistics'));

        // statistics savepoint reached
        upgrade_plugin_savepoint(true, 2008112100, 'quiz', 'statistics');
    }

    if ($oldversion < 2008112101) {
        // Removed UNSIGNED from all NUMBER columns in the quiz_statistics table.
        $table = new xmldb_table('quiz_statistics');

        // Change of sign for field firstattemptsavg
        $field = new xmldb_field('firstattemptsavg', XMLDB_TYPE_NUMBER, '15, 5', null,
                null, null, null, 'allattemptscount');
        $dbman->change_field_unsigned($table, $field);

        // Change of sign for field allattemptsavg
        $field = new xmldb_field('allattemptsavg', XMLDB_TYPE_NUMBER, '15, 5', null,
                null, null, null, 'firstattemptsavg');
        $dbman->change_field_unsigned($table, $field);

        // Change of sign for field median
        $field = new xmldb_field('median', XMLDB_TYPE_NUMBER, '15, 5', null,
                null, null, null, 'allattemptsavg');
        $dbman->change_field_unsigned($table, $field);

        // Change of sign for field standarddeviation
        $field = new xmldb_field('standarddeviation', XMLDB_TYPE_NUMBER, '15, 5', null,
                null, null, null, 'median');
        $dbman->change_field_unsigned($table, $field);

        // Change of sign for field errorratio
        $field = new xmldb_field('errorratio', XMLDB_TYPE_NUMBER, '15, 10', null,
                null, null, null, 'cic');
        $dbman->change_field_unsigned($table, $field);

        // Change of sign for field standarderror
        $field = new xmldb_field('standarderror', XMLDB_TYPE_NUMBER, '15, 10', null,
                null, null, null, 'errorratio');
        $dbman->change_field_unsigned($table, $field);

        // statistics savepoint reached
        upgrade_plugin_savepoint(true, 2008112101, 'quiz', 'statistics');
    }

    if ($oldversion < 2008112102) {
        // Removed UNSIGNED from all NUMBER columns in the quiz_question_statistics table.
        $table = new xmldb_table('quiz_question_statistics');

        // Change of sign for field effectiveweight
        $field = new xmldb_field('effectiveweight', XMLDB_TYPE_NUMBER, '15, 5', null,
                null, null, null, 's');
        $dbman->change_field_unsigned($table, $field);

        // Change of sign for field sd
        $field = new xmldb_field('sd', XMLDB_TYPE_NUMBER, '15, 10', null,
                null, null, null, 'discriminativeefficiency');
        $dbman->change_field_unsigned($table, $field);

        // Change of sign for field facility
        $field = new xmldb_field('facility', XMLDB_TYPE_NUMBER, '15, 10', null,
                null, null, null, 'sd');
        $dbman->change_field_unsigned($table, $field);

        // Change of sign for field maxgrade
        $field = new xmldb_field('maxgrade', XMLDB_TYPE_NUMBER, '12, 7', null,
                null, null, null, 'subquestions');
        $dbman->change_field_unsigned($table, $field);

        // statistics savepoint reached
        upgrade_plugin_savepoint(true, 2008112102, 'quiz', 'statistics');
    }

    if ($oldversion < 2008112103) {
        // Removed UNSIGNED from all NUMBER columns in the quiz_question_response_stats table.
        $table = new xmldb_table('quiz_question_response_stats');

        // Change of sign for field credit
        $field = new xmldb_field('credit', XMLDB_TYPE_NUMBER, '15, 5', null,
                XMLDB_NOTNULL, null, null, 'rcount');
        $dbman->change_field_unsigned($table, $field);

        // statistics savepoint reached
        upgrade_plugin_savepoint(true, 2008112103, 'quiz', 'statistics');
    }

    if ($oldversion < 2010031700) {

        // Define field randomguessscore to be added to quiz_question_statistics
        $table = new xmldb_table('quiz_question_statistics');
        $field = new xmldb_field('randomguessscore', XMLDB_TYPE_NUMBER, '12, 7', null,
                null, null, null, 'positions');

        // Conditionally launch add field randomguessscore
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // statistics savepoint reached
        upgrade_plugin_savepoint(true, 2010031700, 'quiz', 'statistics');
    }

    if ($oldversion < 2010032400) {

        // Define field slot to be added to quiz_question_statistics
        $table = new xmldb_table('quiz_question_statistics');
        $field = new xmldb_field('slot', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null,
                null, null, 'questionid');

        // Conditionally launch add field slot
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // statistics savepoint reached
        upgrade_plugin_savepoint(true, 2010032400, 'quiz', 'statistics');
    }

    if ($oldversion < 2010032401) {

        // Delete all cached data
        $DB->delete_records('quiz_question_response_stats');
        $DB->delete_records('quiz_question_statistics');
        $DB->delete_records('quiz_statistics');

        // Rename field maxgrade on table quiz_question_statistics to maxmark
        $table = new xmldb_table('quiz_question_statistics');
        $field = new xmldb_field('maxgrade', XMLDB_TYPE_NUMBER, '12, 7', XMLDB_UNSIGNED,
                null, null, null, 'subquestions');

        // Launch rename field maxmark
        $dbman->rename_field($table, $field, 'maxmark');

        // statistics savepoint reached
        upgrade_plugin_savepoint(true, 2010032401, 'quiz', 'statistics');
    }

    if ($oldversion < 2010062200) {

        // Changing nullability of field aid on table quiz_question_response_stats to null
        $table = new xmldb_table('quiz_question_response_stats');
        $field = new xmldb_field('aid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
                null, null, null, 'subqid');

        // Launch change of nullability for field aid
        $dbman->change_field_notnull($table, $field);

        // statistics savepoint reached
        upgrade_plugin_savepoint(true, 2010062200, 'quiz', 'statistics');
    }

    if ($oldversion < 2010070301) {

        // Changing type of field maxmark on table quiz_question_statistics to number
        $table = new xmldb_table('quiz_question_statistics');
        $field = new xmldb_field('maxmark', XMLDB_TYPE_NUMBER, '12, 7', XMLDB_UNSIGNED,
                null, null, null, 'subquestions');

        // Launch change of type for field maxmark
        $dbman->change_field_type($table, $field);

        // statistics savepoint reached
        upgrade_plugin_savepoint(true, 2010070301, 'quiz', 'statistics');
    }

    if ($oldversion < 2011021500) {
        $DB->set_field($quizreportstablename, 'capability', 'quiz/statistics:view',
                array('name' => 'statistics'));

        // statistics savepoint reached
        upgrade_plugin_savepoint(true, 2011021500, 'quiz', 'statistics');
    }

    // Signed fixes - MDL-28032
    if ($oldversion < 2011062600) {

        // Changing sign of field maxmark on table quiz_question_statistics to signed
        $table = new xmldb_table('quiz_question_statistics');
        $field = new xmldb_field('maxmark', XMLDB_TYPE_NUMBER, '12, 7', null, null, null, null, 'subquestions');

        // Launch change of sign for field maxmark
        $dbman->change_field_unsigned($table, $field);

        // statistics savepoint reached
        upgrade_plugin_savepoint(true, 2011062600, 'quiz', 'statistics');
    }

    // Moodle v2.1.0 release upgrade line
    // Put any upgrade step following this

    return true;
}

