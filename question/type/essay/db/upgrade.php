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
 * Essay question type upgrade code.
 *
 * @package    qtype
 * @subpackage essay
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Upgrade code for the essay question type.
 * @param int $oldversion the version we are upgrading from.
 */
function xmldb_qtype_essay_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2011031000) {
        // Define table qtype_essay_options to be created
        $table = new xmldb_table('qtype_essay_options');

        // Adding fields to table qtype_essay_options
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
                XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('questionid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
                XMLDB_NOTNULL, null, null);
        $table->add_field('responseformat', XMLDB_TYPE_CHAR, '16', null,
                XMLDB_NOTNULL, null, 'editor');
        $table->add_field('responsefieldlines', XMLDB_TYPE_INTEGER, '4', null,
                XMLDB_NOTNULL, null, '15');
        $table->add_field('attachments', XMLDB_TYPE_INTEGER, '4', null,
                XMLDB_NOTNULL, null, '0');
        $table->add_field('graderinfo', XMLDB_TYPE_TEXT, 'small', null,
                null, null, null);
        $table->add_field('graderinfoformat', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED,
                XMLDB_NOTNULL, null, '0');

        // Adding keys to table qtype_essay_options
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('questionid', XMLDB_KEY_FOREIGN_UNIQUE,
                array('questionid'), 'question', array('id'));

        // Conditionally launch create table for qtype_essay_options
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // essay savepoint reached
        upgrade_plugin_savepoint(true, 2011031000, 'qtype', 'essay');
    }

    if ($oldversion < 2011060300) {
        // Insert a row into the qtype_essay_options table for each existing essay question.
        $DB->execute("
                INSERT INTO {qtype_essay_options} (questionid, responseformat,
                        responsefieldlines, attachments, graderinfo, graderinfoformat)
                SELECT q.id, 'editor', 15, 0, '', " . FORMAT_MOODLE . "
                FROM {question} q
                WHERE q.qtype = 'essay'
                AND NOT EXISTS (
                    SELECT 'x'
                    FROM {qtype_essay_options} qeo
                    WHERE qeo.questionid = q.id)");

        // essay savepoint reached
        upgrade_plugin_savepoint(true, 2011060300, 'qtype', 'essay');
    }

    // Moodle v2.1.0 release upgrade line
    // Put any upgrade step following this

    if ($oldversion < 2011060301) {
        // In Moodle <= 2.0 essay had both question.generalfeedback and question_answers.feedback.
        // This was silly, and in Moodel >= 2.1 only question.generalfeedback. To avoid
        // dataloss, we concatenate question_answers.feedback onto the end of question.generalfeedback.
        $toupdate = $DB->get_recordset_sql("
                SELECT q.id,
                       q.generalfeedback,
                       q.generalfeedbackformat,
                       qa.feedback,
                       qa.feedbackformat

                  FROM {question} q
                  JOIN {question_answers} qa ON qa.question = q.id

                 WHERE q.qtype = 'essay'
                   AND " . $DB->sql_isnotempty('question_answers', 'feedback', false, true));

        foreach ($toupdate as $data) {
            upgrade_set_timeout(60);
            if ($data->generalfeedbackformat == $data->feedbackformat) {
                $DB->set_field('question', 'generalfeedback',
                        $data->generalfeedback . $data->feedback,
                        array('id' => $data->id));

            } else {
                $newdata = new stdClass();
                $newdata->id = $data->id;
                $newdata->generalfeedback =
                        qtype_essay_convert_to_html($data->generalfeedback, $data->generalfeedbackformat) .
                        qtype_essay_convert_to_html($data->feedback,        $data->feedbackformat);
                $newdata->generalfeedbackformat = FORMAT_HTML;
                $DB->update_record('question', $newdata);
            }
        }

        $toupdate->close();

        // Essay savepoint reached.
        upgrade_plugin_savepoint(true, 2011060301, 'qtype', 'essay');
    }

    if ($oldversion < 2011060302) {
        // Then we delete the old question_answers rows for essay questions.
        $DB->delete_records_select('question_answers',
                "question IN (SELECT id FROM {question} WHERE qtype = 'essay')");

        // Essay savepoint reached.
        upgrade_plugin_savepoint(true, 2011060302, 'qtype', 'essay');
    }

    return true;
}

/**
 * Convert some content to HTML.
 * @param string $text the content to convert to HTML
 * @param int $oldformat One of the FORMAT_... constants.
 */
function qtype_essay_convert_to_html($text, $oldformat) {
    switch ($oldformat) {
        // Similar to format_text.

        case FORMAT_PLAIN:
            $text = s($text);
            $text = str_replace(' ', '&nbsp; ', $text);
            $text = nl2br($text);
            return $text;

        case FORMAT_MARKDOWN:
            return markdown_to_html($text);

        case FORMAT_MOODLE:
            return text_to_html($text);

        case FORMAT_HTML:
            return $text;

        default:
            throw new coding_exception(
                    'Unexpected text format when upgrading essay questions.');
    }
}
