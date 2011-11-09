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
 * Numerical question type upgrade code.
 *
 * @package    qtype
 * @subpackage numerical
 * @copyright  1999 onwards Martin Dougiamas {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Upgrade code for the numerical question type.
 * @param int $oldversion the version we are upgrading from.
 */
function xmldb_qtype_numerical_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    //===== 1.9.0 upgrade line ======//
    if ($oldversion < 2009100100 ) { //New version in version.php

        // Define table question_numerical_options to be created
        $table = new xmldb_table('question_numerical_options');

        // Adding fields to table question_numerical_options
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
                XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('question', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
                XMLDB_NOTNULL, null, '0');
        $table->add_field('instructions', XMLDB_TYPE_TEXT, 'small', null,
                null, null, null);
        $table->add_field('showunits', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED,
                XMLDB_NOTNULL, null, '0');
        $table->add_field('unitsleft', XMLDB_TYPE_INTEGER, '4', null,
                XMLDB_NOTNULL, null, '0');
        $table->add_field('unitgradingtype', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED,
                XMLDB_NOTNULL, null, '0');
        $table->add_field('unitpenalty', XMLDB_TYPE_NUMBER, '12, 7', XMLDB_UNSIGNED,
                XMLDB_NOTNULL, null, '0.1');

        // Adding keys to table question_numerical_options
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('question', XMLDB_KEY_FOREIGN, array('question'), 'question', array('id'));

        // Conditionally launch create table for question_calculated_options
        if (!$dbman->table_exists($table)) {
            // $dbman->create_table doesnt return a result, we just have to trust it
            $dbman->create_table($table);
        }

        // Set a better default for questions without units.
        $DB->execute('
                UPDATE {question_numerical_options} qno
                   SET showunits = 3
                 WHERE NOT EXISTS (
                         SELECT 1
                           FROM {question_numerical_units} qnu
                          WHERE qnu.question = qno.question)');

        upgrade_plugin_savepoint(true, 2009100100, 'qtype', 'numerical');
    }

    if ($oldversion < 2009100101) {

        // Define field instructionsformat to be added to question_numerical_options
        $table = new xmldb_table('question_numerical_options');
        $field = new xmldb_field('instructionsformat', XMLDB_TYPE_INTEGER, '2', null,
                XMLDB_NOTNULL, null, '0', 'instructions');

        // Conditionally launch add field instructionsformat
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // In the past, question_numerical_options.instructions assumed to contain
        // content of the same form as question.questiontextformat. If we are
        // using the HTML editor, then convert FORMAT_MOODLE content to FORMAT_HTML.
        $rs = $DB->get_recordset_sql('
                SELECT qno.*, q.oldquestiontextformat
                FROM {question_numerical_options} qno
                JOIN {question} q ON qno.question = q.id');
        foreach ($rs as $record) {
            if ($CFG->texteditors !== 'textarea' &&
                    $record->oldquestiontextformat == FORMAT_MOODLE) {
                $record->instructions = text_to_html($record->instructions, false, false, true);
                $record->instructionsformat = FORMAT_HTML;
            } else {
                $record->instructionsformat = $record->oldquestiontextformat;
            }
            $DB->update_record('question_numerical_options', $record);
        }
        $rs->close();

        // numerical savepoint reached
        upgrade_plugin_savepoint(true, 2009100101, 'qtype', 'numerical');
    }

    if ($oldversion < 2011042600) {
        // Get rid of the instructions field by adding it to the qestion
        // text. Also, if the unit was set to be displayed beside the input,
        // deal with that within the question text too.

        // The hard-coded constants used here are:
        // 2 = the old qtype_numerical::UNITDISPLAY for ->showunits
        // 3 = qtype_numerical::UNITNONE

        $fs = get_file_storage();

        $rs = $DB->get_recordset_sql('
                SELECT q.id AS questionid,
                       q.questiontext,
                       q.questiontextformat,
                       qc.contextid,
                       qno.id AS qnoid,
                       qno.instructions,
                       qno.instructionsformat,
                       qno.showunits,
                       qno.unitsleft,
                       qnu.unit AS defaultunit

                  FROM {question} q
                  JOIN {question_categories} qc ON qc.id = q.category
                  JOIN {question_numerical_options} qno ON qno.question = q.id
                  JOIN {question_numerical_units} qnu ON qnu.id = (
                            SELECT min(id)
                              FROM {question_numerical_units}
                             WHERE question = q.id AND ABS(multiplier - 1) < 0.0000000001)');
        foreach ($rs as $numericaloptions) {
            if ($numericaloptions->showunits != 2 && empty($numericaloptions->instructions)) {
                // Nothing to do for this question.
                continue;
            }

            $ishtml = qtype_numerical_convert_text_format($numericaloptions);

            $response = '_______________';
            if ($numericaloptions->showunits == 2) {
                if ($numericaloptions->unitsleft) {
                    $response = $numericaloptions->defaultunit . ' _______________';
                } else {
                    $response = '_______________ ' . $numericaloptions->defaultunit;
                }

                $DB->set_field('question_numerical_options', 'showunits', 3,
                        array('id' => $numericaloptions->qnoid));
            }

            if ($ishtml) {
                $numericaloptions->questiontext .= '<p>' . $response . '</p>';
            } else {
                $numericaloptions->questiontext .= "\n\n" . $response;
            }

            if (!empty($numericaloptions->instructions)) {
                if ($ishtml) {
                    $numericaloptions->questiontext .= $numericaloptions->instructions;
                } else {
                    $numericaloptions->questiontext .= "\n\n" . $numericaloptions->instructions;
                }

                $oldfiles = $fs->get_area_files($numericaloptions->contextid,
                        'qtype_numerical', 'instruction', $numericaloptions->questionid,
                        'id', false);
                foreach ($oldfiles as $oldfile) {
                    $filerecord = new stdClass();
                    $filerecord->component = 'question';
                    $filerecord->filearea = 'questiontext';
                    $fs->create_file_from_storedfile($filerecord, $oldfile);
                }

                if ($oldfiles) {
                    $fs->delete_area_files($numericaloptions->contextid,
                        'qtype_numerical', 'instruction', $numericaloptions->questionid);
                }
            }

            $updaterecord = new stdClass();
            $updaterecord->id = $numericaloptions->questionid;
            $updaterecord->questiontext = $numericaloptions->questiontext;
            $updaterecord->questiontextformat = $numericaloptions->questiontextformat;
            $DB->update_record('question', $updaterecord);
        }
        $rs->close();

        // numerical savepoint reached
        upgrade_plugin_savepoint(true, 2011042600, 'qtype', 'numerical');
    }

    if ($oldversion < 2011042601) {
        // Define field instructions to be dropped from question_numerical_options
        $table = new xmldb_table('question_numerical_options');
        $field = new xmldb_field('instructions');

        // Conditionally launch drop field instructions
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // numerical savepoint reached
        upgrade_plugin_savepoint(true, 2011042601, 'qtype', 'numerical');
    }

    if ($oldversion < 2011042602) {
        // Define field instructionsformat to be dropped from question_numerical_options
        $table = new xmldb_table('question_numerical_options');
        $field = new xmldb_field('instructionsformat');

        // Conditionally launch drop field instructionsformat
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // numerical savepoint reached
        upgrade_plugin_savepoint(true, 2011042602, 'qtype', 'numerical');
    }

    // Moodle v2.1.0 release upgrade line
    // Put any upgrade step following this

    return true;
}

/**
 * Convert the ->questiontext and ->instructions fields to have the same text format.
 * If they are already the same, do nothing. Otherwise, this method works by
 * converting both to HTML.
 * @param $numericaloptions the data to convert.
 * @return bool true if the resulting fields are in HTML, as opposed to one of
 * the text-based formats.
 */
function qtype_numerical_convert_text_format($numericaloptions) {
    if ($numericaloptions->questiontextformat == $numericaloptions->instructionsformat) {
        // Nothing to do:
        return $numericaloptions->questiontextformat == FORMAT_HTML;
    }

    if ($numericaloptions->questiontextformat != FORMAT_HTML) {
        $numericaloptions->questiontext = qtype_numerical_convert_to_html(
                $numericaloptions->questiontext, $numericaloptions->questiontextformat);
        $numericaloptions->questiontextformat = FORMAT_HTML;
    }

    if ($numericaloptions->instructionsformat != FORMAT_HTML) {
        $numericaloptions->instructions = qtype_numerical_convert_to_html(
                $numericaloptions->instructions, $numericaloptions->instructionsformat);
        $numericaloptions->instructionsformat = FORMAT_HTML;
    }

    return true;
}

// Add some helper functions that should be in upgradelib.php, but having there already
// the question attempts updater classes prevents us to do so :-(

/**
 * Convert some content to HTML.
 * @param string $text the content to convert to HTML
 * @param int $oldformat One of the FORMAT_... constants.
 */
function qtype_numerical_convert_to_html($text, $oldformat) {
    switch ($oldformat) {
        // Similar to format_text.

        case FORMAT_PLAIN:
            $text = s($text);
            $text = str_replace('  ', '&nbsp; ', $text);
            $text = nl2br($text);
            return $text;

        case FORMAT_MARKDOWN:
            return markdown_to_html($text);

        case FORMAT_MOODLE:
            return text_to_html($text, null, $options['para'], $options['newlines']);

        default:
            throw new coding_exception(
                    'Unexpected text format when upgrading numerical questions.');
    }
}
