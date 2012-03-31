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
 * This page lets admin convert imagetarget questions to the ddmarker question type.
 *
 * @package    qtype
 * @subpackage ddmarker
 * @copyright  2012 Jamie Pratt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot.'/question/type/ddmarker/questionlists.php');

class qtype_ddmarker_question_converter_list extends qtype_ddmarker_question_list {
    protected function new_list_item($record) {
        return new qtype_ddmarker_question_converter_list_item($record, $this, $this->categorylist);
    }
    public function prepare_for_processing($top) {
        global $DB;
        $questionids = $top->question_ids();
        list($inorequalsql, $inorequalparams) = $DB->get_in_or_equal($questionids);
        $imagetargetrecords = $DB->get_records_select('question_imagetarget', 'question '.$inorequalsql, $inorequalparams);
        $answers = array();
        foreach ($imagetargetrecords as $imagetargetrecord) {
            $this->get_instance($imagetargetrecord->question)->imagetargetrecord = $imagetargetrecord;
        }
        $answerrecords = $DB->get_records_select('question_answers', 'question '.$inorequalsql, $inorequalparams);
        foreach ($answerrecords as $answerrecord) {
            $this->get_instance($answerrecord->question)->answers[] = $answerrecord;
        }
    }
}
class qtype_ddmarker_question_converter_list_item extends qtype_ddmarker_question_list_item {
    public $imagetargetrecord = null;
    public $answers = array();
    public function process($progresstrace = null, $depth = 0) {
        $this->convert_question();
        parent::process($progresstrace, $depth);//outputs progress message
    }
    protected function convert_question() {
        global $DB;
        foreach ($this->answers as $answer) {
            $no = 1;
            if ('*' !== $answer->answer) {
                $drop = new stdClass();
                $drop->questionid = $this->record->id;
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
                $incorrectfeedback = $answer->feedback;
                $incorrectfeedbackformat = $answer->feedbackformat;
            }
        }
        $drag = new stdClass();
        $drag->questionid = $this->record->id;
        $drag->no = 1;
        $drag->label = "X";
        $drag->infinite = 0;
        $DB->insert_record('qtype_ddmarker_drags', $drag);

        $ddmarker = new stdClass();
        $ddmarker->questionid = $this->record->id;
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

        $newrec = clone($this->record);
        unset($newrec->contextid);
        $newrec->qtype = 'ddmarker';
        $newrec->timemodified = time();
        $DB->update_record('question', $newrec);

        $fs = get_file_storage();
        $bgimagefile = $fs->get_file($this->course_context_id(),
                                        'course',
                                        'legacy',
                                        '0',
                                        '/'.dirname($this->imagetargetrecord->qimage).'/',
                                        basename($this->imagetargetrecord->qimage));
        $newbgimagefile = new stdClass();
        $newbgimagefile->component = 'qtype_ddmarker';
        $newbgimagefile->filearea = 'bgimage';
        $newbgimagefile->filepath = '/';
        $newbgimagefile->itemid = $this->record->id;
        $fs->create_file_from_storedfile($newbgimagefile, $bgimagefile);

        $DB->delete_records('question_imagetarget', array('question' => $this->record->id));
        $DB->delete_records('question_answers', array('question' => $this->record->id));
    }
}

$categoryid = optional_param('categoryid', 0, PARAM_INT);
$qcontextid = optional_param('contextid', 0, PARAM_INT);
$questionid = optional_param('questionid', 0, PARAM_INT);
$confirm = optional_param('confirm', 0, PARAM_INT);
// Check the user is logged in.
require_login();
$context = get_context_instance(CONTEXT_SYSTEM);
require_capability('moodle/question:config', $context);

admin_externalpage_setup('qtypeddmarkerfromimagetarget');

// Header.
echo $OUTPUT->header();
echo $OUTPUT->heading_with_help(get_string('imagetargetconverter', 'qtype_ddmarker'), '', 'qtype_ddmarker');


$params = array();
$from = 'FROM {question_categories} cat, {question} q';
$where = ' WHERE q.qtype = \'imagetarget\' AND q.category =  cat.id ';

if ($qcontextid) {
    $qcontext = get_context_instance_by_id($qcontextid, MUST_EXIST);
    $from  .= ', {context} context';
    $where .= 'AND cat.contextid = context.id AND (context.path LIKE :path OR context.id = :id) ';
    $params['path'] = $qcontext->path.'/%';
    $params['id'] = $qcontext->id;
} else if ($categoryid) {
    //fetch all questions from this cats context
    $from  .= ', {question_categories} cat2';
    $where .= 'AND cat.contextid = cat2.contextid AND cat2.id = :categoryid ';
    $params['categoryid'] = $categoryid;
} else if ($questionid) {
    //fetch all questions from this cats context
    $where .= 'AND q.id = :questionid ';
    $params['questionid'] = $questionid;
}
$sql = 'SELECT q.*, cat.contextid '.$from.$where.'ORDER BY cat.id, q.name';

$questions = $DB->get_records_sql($sql, $params);

$contextids = array();
foreach ($questions as $question) {
    $contextids[] = $question->contextid;
}

$contextlist = new qtype_ddmarker_context_list(array_unique($contextids));
$categorylist = new qtype_ddmarker_category_list($contextids, $contextlist);
$questionlist = new qtype_ddmarker_question_converter_list($questions, $categorylist);

foreach ($questions as $question) {
    $questionlist->leaf_node($question->id, 1);
}
$questionsselected = (bool) ($categoryid || $qcontextid || $questionid);
if ($questionid) {
    $top = $questionlist->get_instance($questionid);
} else if ($categoryid) {
    $top = $categorylist->get_instance($categoryid);
} else if ($qcontextid) {
    $top = $contextlist->get_instance($qcontextid);
} else {
    $top = $contextlist->root_node();
}
if (!$confirm) {
    if ($questionsselected) {
        echo $contextlist->render('listitemaction', false, $top);
        $cofirmedurl = new moodle_url($PAGE->url, compact('categoryid', 'contextid', 'questionid') + array('confirm'=>1));
        $cancelurl = new moodle_url($PAGE->url);
        echo $OUTPUT->confirm(get_string('confirmimagetargetconversion', 'qtype_ddmarker'), $cofirmedurl, $cancelurl);
    } else {
        echo $contextlist->render('listitemlist', true, $top);
    }
} else if (confirm_sesskey()) {
    $questionlist->prepare_for_processing($top);
    $top->process();
}

// Footer.
echo $OUTPUT->footer();
