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
 * Ddmarker question type installation code.
 *
 * @package    qtype_ddmarker
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Installation code for the ddmarker question type.
 *
 * It converts all existing imagetarget questions to ddmarker
 */
function xmldb_qtype_ddmarker_install() {
    global $DB, $OUTPUT;

    $from = 'FROM {question_categories} cat, {question} q';
    $where = ' WHERE q.qtype = \'imagetarget\' AND q.category =  cat.id ';

    $sql = 'SELECT q.*, cat.contextid '.$from.$where.'ORDER BY cat.id, q.name';

    $questions = $DB->get_records_sql($sql);

    if (!empty($questions)) {
        require_once(dirname(__FILE__).'/../lib.php');
        $dragssql = 'SELECT drag.* '.$from.', {qtype_ddmarker_drags} drag'.$where.' AND drag.questionid = q.id';
        $drags = qtype_ddmarker_index_array_of_records_by_key('questionid', $DB->get_records_sql($dragssql));

        $dropssql = 'SELECT drp.* '.$from.', {qtype_ddmarker_drops} drp'.$where.' AND drp.questionid = q.id';
        $drops = qtype_ddmarker_index_array_of_records_by_key('questionid', $DB->get_records_sql($dropssql));

        $answerssql = 'SELECT answer.* '.$from.', {question_answers} answer'.$where.' AND answer.question = q.id';
        $answers = qtype_ddmarker_index_array_of_records_by_key('question', $DB->get_records_sql($answerssql));

        $imgfiles = $DB->get_records_sql_menu('SELECT question, qimage FROM {question_imagetarget}');
        $progressbar = new progress_bar('qtype_ddmarker_convert_from_imagetarget');
        $progressbar->create();
        $done = 0;
        foreach ($questions as $question) {
            qtype_ddmarker_convert_image_target_question($question, $imgfiles[$question->id], $answers[$question->id]);
            $done++;
            $progressbar->update($done, count($questions),
                    get_string('convertingimagetargetquestion', 'qtype_ddmarker', $question));
        }
        list($qsql, $qparams) = $DB->get_in_or_equal(array_keys($questions));
        $DB->delete_records_select('question_answers', 'question '.$qsql, $qparams);
        $dbman = $DB->get_manager();
        $dbman->drop_table(new xmldb_table('question_imagetarget'));
    }
}


/**
 * Helper used by {@link xmldb_qtype_ddmarker_install}.
 *
 * Convert a one-dimensional array of records into a two-dimensional array
 * grouped by field $key.
 *
 * @param string $key the key to group by.
 * @param array $recs The records to group.
 * @return array the re-grouped array.
 */
function qtype_ddmarker_index_array_of_records_by_key($key, $recs) {
    $out = array();
    foreach ($recs as $id => $rec) {
        if (!isset($out[$rec->{$key}])) {
            $out[$rec->{$key}] = array();
        }
        $out[$rec->{$key}][$id] = $rec;
    }
    return $out;
}
