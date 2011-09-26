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
 * Calculated question type upgrade code.
 *
 * @package    qtype
 * @subpackage calculated
 * @copyright  1999 onwards Martin Dougiamas {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Upgrade code for the calculated question type.
 * @param int $oldversion the version we are upgrading from.
 */
function xmldb_qtype_calculated_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    // MDL-16505.
    if ($oldversion < 2008091700 ) { //New version in version.php
        if (get_config('qtype_calculated', 'version')) {
            unset_config('version', 'qtype_calculated');
        }
        upgrade_plugin_savepoint(true, 2008091700, 'qtype', 'calculated');
    }

    if ($oldversion < 2009082000) { //New version in version.php

        // Define table question_calculated_options to be created
        $table = new xmldb_table('question_calculated_options');

        // Adding fields to table question_calculated_options
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
                XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('question', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
                XMLDB_NOTNULL, null, '0');
        $table->add_field('synchronize', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED,
                XMLDB_NOTNULL, null, '0');

        // Adding keys to table question_calculated_options
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('question', XMLDB_KEY_FOREIGN, array('question'), 'question', array('id'));

        // Conditionally launch create table for question_calculated_options
        if (!$dbman->table_exists($table)) {
            // $dbman->create_table doesnt return a result, we just have to trust it
            $dbman->create_table($table);
        }
        upgrade_plugin_savepoint(true, 2009082000 , 'qtype', 'calculated');
    }

    if ( $oldversion < 2009092000) { //New version in version.php

        // Define field single to be added to question_calculated_options
        $table = new xmldb_table('question_calculated_options');
        $field = new xmldb_field('single', XMLDB_TYPE_INTEGER, '4', null,
                XMLDB_NOTNULL, null, '0', 'synchronize');

        // Conditionally launch add field single
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field shuffleanswers to be added to question_calculated_options
        $table = new xmldb_table('question_calculated_options');
        $field = new xmldb_field('shuffleanswers', XMLDB_TYPE_INTEGER, '4', null,
                XMLDB_NOTNULL, null, '0', 'single');

        // Conditionally launch add field shuffleanswers
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        // Define field correctfeedback to be added to question_calculated_options
        $table = new xmldb_table('question_calculated_options');
        $field = new xmldb_field('correctfeedback', XMLDB_TYPE_TEXT, 'small', null,
                null, null, null, 'shuffleanswers');

        // Conditionally launch add field correctfeedback
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field partiallycorrectfeedback to be added to question_calculated_options
        $table = new xmldb_table('question_calculated_options');
        $field = new xmldb_field('partiallycorrectfeedback', XMLDB_TYPE_TEXT, 'small', null,
                null, null, null, 'correctfeedback');

        // Conditionally launch add field partiallycorrectfeedback
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        // Define field incorrectfeedback to be added to question_calculated_options
        $table = new xmldb_table('question_calculated_options');
        $field = new xmldb_field('incorrectfeedback', XMLDB_TYPE_TEXT, 'small', null,
                null, null, null, 'partiallycorrectfeedback');

        // Conditionally launch add field incorrectfeedback
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        // Define field answernumbering to be added to question_calculated_options
        $table = new xmldb_table('question_calculated_options');
        $field = new xmldb_field('answernumbering', XMLDB_TYPE_CHAR, '10', null,
                XMLDB_NOTNULL, null, 'abc', 'incorrectfeedback');

        // Conditionally launch add field answernumbering
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_plugin_savepoint(true, 2009092000, 'qtype', 'calculated');
    }

    if ($oldversion < 2010020800) {

        // Define field multiplechoice to be dropped from question_calculated_options
        $table = new xmldb_table('question_calculated_options');
        $field = new xmldb_field('multichoice');

        // Conditionally launch drop field multiplechoice
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // calculated savepoint reached
        upgrade_plugin_savepoint(true, 2010020800, 'qtype', 'calculated');
    }

    if ($oldversion < 2010020801) {

        // Define field correctfeedbackformat to be added to question_calculated_options
        $table = new xmldb_table('question_calculated_options');
        $field = new xmldb_field('correctfeedbackformat', XMLDB_TYPE_INTEGER, '2', null,
                XMLDB_NOTNULL, null, '0', 'correctfeedback');

        // Conditionally launch add field correctfeedbackformat
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field partiallycorrectfeedbackformat to be added to question_calculated_options
        $field = new xmldb_field('partiallycorrectfeedbackformat', XMLDB_TYPE_INTEGER, '2', null,
                XMLDB_NOTNULL, null, '0', 'partiallycorrectfeedback');

        // Conditionally launch add field partiallycorrectfeedbackformat
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field incorrectfeedbackformat to be added to question_calculated_options
        $field = new xmldb_field('incorrectfeedbackformat', XMLDB_TYPE_INTEGER, '2', null,
                XMLDB_NOTNULL, null, '0', 'incorrectfeedback');

        // Conditionally launch add field incorrectfeedbackformat
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // In the past, the correctfeedback, partiallycorrectfeedback,
        // incorrectfeedback columns were assumed to contain content of the same
        // form as questiontextformat. If we are using the HTML editor, then
        // convert FORMAT_MOODLE content to FORMAT_HTML.
        $rs = $DB->get_recordset_sql('
                SELECT qco.*, q.oldquestiontextformat
                FROM {question_calculated_options} qco
                JOIN {question} q ON qco.question = q.id');
        foreach ($rs as $record) {
            if ($CFG->texteditors !== 'textarea' &&
                    $record->oldquestiontextformat == FORMAT_MOODLE) {
                $record->correctfeedback = text_to_html(
                        $record->correctfeedback, false, false, true);
                $record->correctfeedbackformat = FORMAT_HTML;
                $record->partiallycorrectfeedback = text_to_html(
                        $record->partiallycorrectfeedback, false, false, true);
                $record->partiallycorrectfeedbackformat = FORMAT_HTML;
                $record->incorrectfeedback = text_to_html(
                        $record->incorrectfeedback, false, false, true);
                $record->incorrectfeedbackformat = FORMAT_HTML;
            } else {
                $record->correctfeedbackformat = $record->oldquestiontextformat;
                $record->partiallycorrectfeedbackformat = $record->oldquestiontextformat;
                $record->incorrectfeedbackformat = $record->oldquestiontextformat;
            }
            $DB->update_record('question_calculated_options', $record);
        }
        $rs->close();

        // calculated savepoint reached
        upgrade_plugin_savepoint(true, 2010020801, 'qtype', 'calculated');
    }

    // Add new shownumcorrect field. If this is true, then when the user gets a
    // multiple-response question partially correct, tell them how many choices
    // they got correct alongside the feedback.
    if ($oldversion < 2011051900) {

        // Define field shownumcorrect to be added to question_multichoice
        $table = new xmldb_table('question_calculated_options');
        $field = new xmldb_field('shownumcorrect', XMLDB_TYPE_INTEGER, '2', null,
                XMLDB_NOTNULL, null, '0', 'answernumbering');

        // Launch add field shownumcorrect
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // multichoice savepoint reached
        upgrade_plugin_savepoint(true, 2011051900, 'qtype', 'calculated');
    }

    // Moodle v2.1.0 release upgrade line
    // Put any upgrade step following this

    return true;
}


