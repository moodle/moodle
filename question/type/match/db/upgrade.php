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
 * Matching question type upgrade code.
 *
 * @package    qtype
 * @subpackage match
 * @copyright  1999 onwards Martin Dougiamas {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Upgrade code for the matching question type.
 * @param int $oldversion the version we are upgrading from.
 */
function xmldb_qtype_match_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2009072100) {

        // Define field questiontextformat to be added to question_match_sub
        $table = new xmldb_table('question_match_sub');
        $field = new xmldb_field('questiontextformat', XMLDB_TYPE_INTEGER, '2', null,
                XMLDB_NOTNULL, null, '0', 'questiontext');

        // Conditionally launch add field questiontextformat
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // In the past, question_match_sub.questiontext assumed to contain
        // content of the same form as question.questiontextformat. If we are
        // using the HTML editor, then convert FORMAT_MOODLE content to FORMAT_HTML.
        $rs = $DB->get_recordset_sql('
                SELECT qms.*, q.oldquestiontextformat
                FROM {question_match_sub} qms
                JOIN {question} q ON qms.question = q.id');
        foreach ($rs as $record) {
            if ($CFG->texteditors !== 'textarea' &&
                    $record->oldquestiontextformat == FORMAT_MOODLE) {
                $record->questiontext = text_to_html($record->questiontext, false, false, true);
                $record->questiontextformat = FORMAT_HTML;
            } else {
                $record->questiontextformat = $record->oldquestiontextformat;
            }
            $DB->update_record('question_match_sub', $record);
        }
        $rs->close();

        // match savepoint reached
        upgrade_plugin_savepoint(true, 2009072100, 'qtype', 'match');
    }

    if ($oldversion < 2011011300) {

        // Define field correctfeedback to be added to question_match
        $table = new xmldb_table('question_match');
        $field = new xmldb_field('correctfeedback', XMLDB_TYPE_TEXT, 'small', null,
                null, null, null, 'shuffleanswers');

        // Conditionally launch add field correctfeedback
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);

            // Now fill it with '';
            $DB->set_field('question_match', 'correctfeedback', '');

            // Now add the not null constraint.
            $field = new xmldb_field('correctfeedback', XMLDB_TYPE_TEXT, 'small', null,
                    XMLDB_NOTNULL, null, null, 'shuffleanswers');
            $dbman->change_field_notnull($table, $field);
        }

        // match savepoint reached
        upgrade_plugin_savepoint(true, 2011011300, 'qtype', 'match');
    }

    if ($oldversion < 2011011301) {

        // Define field correctfeedbackformat to be added to question_match
        $table = new xmldb_table('question_match');
        $field = new xmldb_field('correctfeedbackformat', XMLDB_TYPE_INTEGER, '2', null,
                XMLDB_NOTNULL, null, '0', 'correctfeedback');

        // Conditionally launch add field correctfeedbackformat
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // match savepoint reached
        upgrade_plugin_savepoint(true, 2011011301, 'qtype', 'match');
    }

    if ($oldversion < 2011011302) {

        // Define field partiallycorrectfeedback to be added to question_match
        $table = new xmldb_table('question_match');
        $field = new xmldb_field('partiallycorrectfeedback', XMLDB_TYPE_TEXT, 'small', null,
                null, null, null, 'correctfeedbackformat');

        // Conditionally launch add field partiallycorrectfeedback
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);

            // Now fill it with '';
            $DB->set_field('question_match', 'partiallycorrectfeedback', '');

            // Now add the not null constraint.
            $field = new xmldb_field('partiallycorrectfeedback', XMLDB_TYPE_TEXT, 'small', null,
                    XMLDB_NOTNULL, null, null, 'correctfeedbackformat');
            $dbman->change_field_notnull($table, $field);
        }

        // match savepoint reached
        upgrade_plugin_savepoint(true, 2011011302, 'qtype', 'match');
    }

    if ($oldversion < 2011011303) {

        // Define field partiallycorrectfeedbackformat to be added to question_match
        $table = new xmldb_table('question_match');
        $field = new xmldb_field('partiallycorrectfeedbackformat', XMLDB_TYPE_INTEGER, '2', null,
                XMLDB_NOTNULL, null, '0', 'partiallycorrectfeedback');

        // Conditionally launch add field partiallycorrectfeedbackformat
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // match savepoint reached
        upgrade_plugin_savepoint(true, 2011011303, 'qtype', 'match');
    }

    if ($oldversion < 2011011304) {

        // Define field incorrectfeedback to be added to question_match
        $table = new xmldb_table('question_match');
        $field = new xmldb_field('incorrectfeedback', XMLDB_TYPE_TEXT, 'small', null,
                null, null, null, 'partiallycorrectfeedbackformat');

        // Conditionally launch add field incorrectfeedback
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);

            // Now fill it with '';
            $DB->set_field('question_match', 'incorrectfeedback', '');

            // Now add the not null constraint.
            $field = new xmldb_field('incorrectfeedback', XMLDB_TYPE_TEXT, 'small', null,
                    XMLDB_NOTNULL, null, null, 'partiallycorrectfeedbackformat');
            $dbman->change_field_notnull($table, $field);
        }

        // match savepoint reached
        upgrade_plugin_savepoint(true, 2011011304, 'qtype', 'match');
    }

    if ($oldversion < 2011011305) {

        // Define field incorrectfeedbackformat to be added to question_match
        $table = new xmldb_table('question_match');
        $field = new xmldb_field('incorrectfeedbackformat', XMLDB_TYPE_INTEGER, '2', null,
                XMLDB_NOTNULL, null, '0', 'incorrectfeedback');

        // Conditionally launch add field incorrectfeedbackformat
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // match savepoint reached
        upgrade_plugin_savepoint(true, 2011011305, 'qtype', 'match');
    }

    if ($oldversion < 2011011306) {

        // Define field shownumcorrect to be added to question_match
        $table = new xmldb_table('question_match');
        $field = new xmldb_field('shownumcorrect', XMLDB_TYPE_INTEGER, '2', null,
                XMLDB_NOTNULL, null, '0', 'incorrectfeedbackformat');

        // Conditionally launch add field shownumcorrect
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // match savepoint reached
        upgrade_plugin_savepoint(true, 2011011306, 'qtype', 'match');
    }

    // Moodle v2.1.0 release upgrade line
    // Put any upgrade step following this

    return true;
}
