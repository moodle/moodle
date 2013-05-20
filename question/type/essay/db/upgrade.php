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

    // Moodle v2.2.0 release upgrade line
    // Put any upgrade step following this.

    if ($oldversion < 2011102701) {
        $sql = "
                  FROM {question} q
                  JOIN {question_answers} qa ON qa.question = q.id

                 WHERE q.qtype = 'essay'
                   AND " . $DB->sql_isnotempty('question_answers', 'feedback', false, true);
        // In Moodle <= 2.0 essay had both question.generalfeedback and question_answers.feedback
        // This was silly, and in Moodel >= 2.1 only question.generalfeedback. To avoid
        // dataloss, we concatenate question_answers.feedback onto the end of question.generalfeedback.
        $count = $DB->count_records_sql("
                SELECT COUNT(1) $sql");
        if ($count) {
            $progressbar = new progress_bar('essay23', 500, true);
            $done = 0;

            $toupdate = $DB->get_recordset_sql("
                    SELECT q.id,
                           q.generalfeedback,
                           q.generalfeedbackformat,
                           qa.feedback,
                           qa.feedbackformat
                    $sql");

            foreach ($toupdate as $data) {
                $progressbar->update($done, $count, "Updating essay feedback ($done/$count).");
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

            $progressbar->update($count, $count, "Updating essay feedback complete!");
            $toupdate->close();
        }

        // Essay savepoint reached.
        upgrade_plugin_savepoint(true, 2011102701, 'qtype', 'essay');
    }

    if ($oldversion < 2011102702) {
        // Then we delete the old question_answers rows for essay questions.
        $DB->delete_records_select('question_answers',
                "question IN (SELECT id FROM {question} WHERE qtype = 'essay')");

        // Essay savepoint reached.
        upgrade_plugin_savepoint(true, 2011102702, 'qtype', 'essay');
    }

    // Moodle v2.3.0 release upgrade line
    // Put any upgrade step following this.

    // Moodle v2.4.0 release upgrade line
    // Put any upgrade step following this.

    if ($oldversion < 2013011800) {
        // Then we delete the old question_answers rows for essay questions.
        $DB->delete_records_select('qtype_essay_options', "NOT EXISTS (
                SELECT 1 FROM {question} WHERE qtype = 'essay' AND
                    {question}.id = {qtype_essay_options}.questionid)");

        // Essay savepoint reached.
        upgrade_plugin_savepoint(true, 2013011800, 'qtype', 'essay');
    }

    if ($oldversion < 2013021700) {
        // Create new fields responsetemplate and responsetemplateformat in qtyep_essay_options table.
        $table = new xmldb_table('qtype_essay_options');
        $field = new xmldb_field('responsetemplate', XMLDB_TYPE_TEXT, null, null,
                    null, null, null, 'graderinfoformat');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('responsetemplateformat', XMLDB_TYPE_INTEGER, '4',
                null, XMLDB_NOTNULL, null, '0', 'responsetemplate');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $DB->execute("UPDATE {qtype_essay_options} SET responsetemplate = '',
                responsetemplateformat = " . FORMAT_HTML . " WHERE responsetemplate IS NULL");

        // Essay savepoint reached.
        upgrade_plugin_savepoint(true, 2013021700, 'qtype', 'essay');
    }

    // Moodle v2.5.0 release upgrade line.
    // Put any upgrade step following this.


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
