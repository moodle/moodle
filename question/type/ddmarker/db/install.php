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


function index_array_of_records_by_key($key, $recs) {
    $out = array();
    foreach ($recs as $id => $rec) {
        if (!isset($out[$rec->{$key}])) {
            $out[$rec->{$key}] = array();
        }
        $out[$rec->{$key}][$id] = $rec;
    }
    return $out;
}

function course_context_id($catcontextid) {
    $context = get_context_instance_by_id($catcontextid);
    while ($context->contextlevel != CONTEXT_COURSE) {
        $context = get_context_instance_by_id(get_parent_contextid($context));
    }
    return $context->id;
}
/**
 * Upgrade code for the matching question type.
 * @param int $oldversion the version we are upgrading from.
 */
function xmldb_qtype_ddmarker_install() {
    global $DB, $OUTPUT;

    $from = 'FROM {question_categories} cat, {question} q';
    $where = ' WHERE q.qtype = \'imagetarget\' AND q.category =  cat.id ';

    $sql = 'SELECT q.*, cat.contextid '.$from.$where.'ORDER BY cat.id, q.name';

    $questions = $DB->get_records_sql($sql);

    if (!empty($questions)) {
        foreach ($questions as $question) {
            $dragssql = 'SELECT drag.* '.$from.', {qtype_ddmarker_drags} drag'.$where.' AND drag.questionid = q.id';
            $drags = index_array_of_records_by_key('questionid', $DB->get_records_sql($dragssql));

            $dropssql = 'SELECT drop.* '.$from.', {qtype_ddmarker_drops} drop'.$where.' AND drop.questionid = q.id';
            $drops = index_array_of_records_by_key('questionid', $DB->get_records_sql($dropssql));

            $answerssql = 'SELECT answer.* '.$from.', {question_answers} answer'.$where.' AND answer.question = q.id';
            $answers = index_array_of_records_by_key('question', $DB->get_records_sql($answerssql));

            $imgfiles = $DB->get_records_sql_menu('SELECT question, qimage FROM {question_imagetarget}');

            $correctfeedback = '';
            $correctfeedbackformat = 1;
            $incorrectfeedback = '';
            $incorrectfeedbackformat = 1;
            $foundincorrectanswer = false;
            foreach ($answers[$question->id] as $answer) {
                $no = 1;
                if ('*' !== $answer->answer) {
                    $drop = new stdClass();
                    $drop->questionid = $question->id;
                    $drop->shape = 'rectangle';
                    $drop->no = $no;
                    list($x1, $y1, $x2, $y2) = explode(',', $answer->answer);
                    $width = $x2 - $x1;
                    $height = $y2 - $y1;
                    $drop->coords = "{$x1},{$y1};{$width},{$height}";
                    $drop->choice = 1;
                    $DB->insert_record('qtype_ddmarker_drops', $drop);
                    $no++;
                    $correctfeedback = $answer->feedback;
                    $correctfeedbackformat = $answer->feedbackformat;
                } else {
                    $foundincorrectanswer = false;
                    $incorrectfeedback = $answer->feedback;
                    $incorrectfeedbackformat = $answer->feedbackformat;
                }
            }
            if (count($answers[$question->id]) < 2) {
                echo $OUTPUT->notification('There are less than 2 answers. '.
                                            '(Normally we expect at least a correct and incorrect answer). '.
                                            'For question id '.$question->id.' "'.$question->name.'".',
                                            'notifyproblem');
            }
            if (!$foundincorrectanswer) {
                echo $OUTPUT->notification('No incorrect answer found for question id '.$question->id.' "'.$question->name.'".',
                                            'notifyproblem');
            }
            $drag = new stdClass();
            $drag->questionid = $question->id;
            $drag->no = 1;
            $drag->label = "X";
            $drag->infinite = 0;
            $DB->insert_record('qtype_ddmarker_drags', $drag);

            $ddmarker = new stdClass();
            $ddmarker->questionid = $question->id;
            $ddmarker->shuffleanswers = 0;
            $ddmarker->correctfeedback = $correctfeedback;
            $ddmarker->correctfeedbackformat = $correctfeedbackformat;
            $ddmarker->partiallycorrectfeedback = '';
            $ddmarker->partiallycorrectfeedbackformat = 1;
            $ddmarker->incorrectfeedback = $incorrectfeedback;
            $ddmarker->incorrectfeedbackformat = $incorrectfeedbackformat;
            $ddmarker->shownumcorrect = 0;
            $ddmarker->showmisplaced = 0;
            $DB->insert_record('qtype_ddmarker', $ddmarker);

            $newrec = clone($question);
            unset($newrec->contextid);
            $newrec->qtype = 'ddmarker';
            $newrec->timemodified = time();
            $DB->update_record('question', $newrec);

            $fs = get_file_storage();
            //we need to look in the course legacy files area for file
            $bgimagefile = $fs->get_file(course_context_id($question->contextid),
                                            'course',
                                            'legacy',
                                            '0',
                                            '/'.dirname($imgfiles[$question->id]).'/',
                                            basename($imgfiles[$question->id]));
            if ($bgimagefile === false) {
                echo $OUTPUT->notification('File "'.$imgfiles[$question->id].'" not found in legacy course files area. '.
                                            'For question id '.$question->id.' "'.$question->name.'".',
                                            'notifyproblem');
            } else {
                $newbgimagefile = new stdClass();
                $newbgimagefile->component = 'qtype_ddmarker';
                $newbgimagefile->filearea = 'bgimage';
                $newbgimagefile->filepath = '/';
                $newbgimagefile->itemid = $question->id;
                $newbgimagefile->contextid = $question->contextid;
                $fs->create_file_from_storedfile($newbgimagefile, $bgimagefile);
            }
        }

        list($qsql, $qparams) = $DB->get_in_or_equal(array_keys($questions));
        $DB->delete_records_select('question_answers', 'question '.$qsql, $qparams);
        $dbman = $DB->get_manager();
        $dbman->drop_table(new xmldb_table('question_imagetarget'));
    }
}
