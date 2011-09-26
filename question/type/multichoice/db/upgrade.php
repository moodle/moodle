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
 * Multiple choice question type upgrade code.
 *
 * @package    qtype
 * @subpackage multichoice
 * @copyright  1999 onwards Martin Dougiamas {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Upgrade code for the multiple choice question type.
 * @param int $oldversion the version we are upgrading from.
 */
function xmldb_qtype_multichoice_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    // This upgrade actually belongs to the random question type,
    // but that does not have a DB upgrade script. Therefore, multichoice
    // is doing it.
    // Rename random questions to give them more helpful names.
    if ($oldversion < 2008021800) {
        require_once($CFG->dirroot . '/question/type/random/questiontype.php');
        $randomqtype = new qtype_random();

        // Get all categories containing random questions.
        $categories = $DB->get_recordset_sql("
                SELECT qc.id, qc.name
                FROM {question_categories} qc
                JOIN {question} q ON q.category = qc.id
                WHERE q.qtype = 'random'
                GROUP BY qc.id, qc.name");

        // Rename the random qusetions in those categories.
        $where = "qtype = 'random' AND category = ? AND " .
                $DB->sql_compare_text('questiontext') . " = " . $DB->sql_compare_text('?');
        foreach ($categories as $cat) {
            $randomqname = $randomqtype->question_name($cat, false);
            $DB->set_field_select('question', 'name', $randomqname, $where, array($cat->id, '0'));

            $randomqname = $randomqtype->question_name($cat, true);
            $DB->set_field_select('question', 'name', $randomqname, $where, array($cat->id, '1'));
        }

        upgrade_plugin_savepoint(true, 2008021800, 'qtype', 'multichoice');
    }

    if ($oldversion < 2009021801) {

        // Define field correctfeedbackformat to be added to question_multichoice
        $table = new xmldb_table('question_multichoice');
        $field = new xmldb_field('correctfeedbackformat', XMLDB_TYPE_INTEGER, '2', null,
                XMLDB_NOTNULL, null, '0', 'correctfeedback');

        // Conditionally launch add field correctfeedbackformat
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field partiallycorrectfeedbackformat to be added to question_multichoice
        $field = new xmldb_field('partiallycorrectfeedbackformat', XMLDB_TYPE_INTEGER, '2', null,
                XMLDB_NOTNULL, null, '0', 'partiallycorrectfeedback');

        // Conditionally launch add field partiallycorrectfeedbackformat
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field incorrectfeedbackformat to be added to question_multichoice
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
                SELECT qm.*, q.oldquestiontextformat
                FROM {question_multichoice} qm
                JOIN {question} q ON qm.question = q.id');
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
            $DB->update_record('question_multichoice', $record);
        }
        $rs->close();

        // multichoice savepoint reached
        upgrade_plugin_savepoint(true, 2009021801, 'qtype', 'multichoice');
    }

    // Add new shownumcorrect field. If this is true, then when the user gets a
    // multiple-response question partially correct, tell them how many choices
    // they got correct alongside the feedback.
    if ($oldversion < 2011011200) {

        // Define field shownumcorrect to be added to question_multichoice
        $table = new xmldb_table('question_multichoice');
        $field = new xmldb_field('shownumcorrect', XMLDB_TYPE_INTEGER, '2', null,
                XMLDB_NOTNULL, null, '0', 'answernumbering');

        // Launch add field shownumcorrect
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // multichoice savepoint reached
        upgrade_plugin_savepoint(true, 2011011200, 'qtype', 'multichoice');
    }

    // Moodle v2.1.0 release upgrade line
    // Put any upgrade step following this

    return true;
}
