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
 * @package   qtype_ddmarker
 * @copyright 2012 The Open University
 * @author    Jamie Pratt <me@jamiep.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once(dirname(__FILE__).'/questionlists.php');
require_once(dirname(__FILE__).'/lib.php');


/**
 * Class representing a list of questions.
 *
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_ddmarker_question_converter_list extends qtype_ddmarker_question_list {
    protected function new_list_item($stringidentifier, $link, $record) {
        return new qtype_ddmarker_question_converter_list_item($stringidentifier, $link, $record, $this, $this->categorylist);
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


/**
 * Class representing an item in the list of questions.
 *
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_ddmarker_question_converter_list_item extends qtype_ddmarker_question_list_item {
    public $imagetargetrecord = null;
    public $answers = array();
    public function process($renderer) {
        qtype_ddmarker_convert_image_target_question($this->record, $this->imagetargetrecord->qimage, $this->answers);
        parent::process($renderer); // Outputs progress message.
    }
}


$categoryid = optional_param('categoryid', 0, PARAM_INT);
$qcontextid = optional_param('contextid', 0, PARAM_INT);
$questionid = optional_param('questionid', 0, PARAM_INT);
$confirm = optional_param('confirm', 0, PARAM_INT);

// Check the user is logged in.
require_login();
$context = context_system::instance();
require_capability('moodle/question:config', $context);

admin_externalpage_setup('qtypeddmarkerfromimagetarget');

// Header.
$renderer = $PAGE->get_renderer('qtype_ddmarker', 'list');
echo $renderer->header();
echo $renderer->heading(get_string('imagetargetconverter', 'qtype_ddmarker'), 2);


$params = array();
$from = 'FROM {question_categories} cat, {question} q';
$where = ' WHERE q.qtype = \'imagetarget\' AND q.category =  cat.id ';

if ($qcontextid) {
    $qcontext = context::instance_by_id($qcontextid);
    $from  .= ', {context} context';
    $where .= 'AND cat.contextid = context.id AND (context.path LIKE :path OR context.id = :id) ';
    $params['path'] = $qcontext->path.'/%';
    $params['id'] = $qcontext->id;
} else if ($categoryid) {
    // Fetch all questions from this cats context.
    $from  .= ', {question_categories} cat2';
    $where .= 'AND cat.contextid = cat2.contextid AND cat2.id = :categoryid ';
    $params['categoryid'] = $categoryid;
} else if ($questionid) {
    // Fetch all questions from this cats context.
    $where .= 'AND q.id = :questionid ';
    $params['questionid'] = $questionid;
}
$sql = 'SELECT q.*, cat.contextid '.$from.$where.'ORDER BY cat.id, q.name';

$questions = $DB->get_records_sql($sql, $params);

if (!count($questions)) {
    echo html_writer::tag('div', get_string('noquestionsfound', 'qtype_ddmarker'));
} else {
    $contextids = array();
    foreach ($questions as $question) {
        $contextids[] = $question->contextid;
    }

    $questionsselected = (bool) ($categoryid || $qcontextid || $questionid);
    if (!$confirm) {
        if (!$questionsselected) {
            $pagestate = 'listall';
        } else {
            $pagestate = 'confirm';
        }
    } else if (confirm_sesskey()) {
        $pagestate = 'processing';
    }
    $link = ($pagestate == 'listall');
    $contextlist = new qtype_ddmarker_context_list($pagestate, $link, array_unique($contextids));
    $categorylist = new qtype_ddmarker_category_list($pagestate, $link, $contextids, $contextlist);
    $questionlist = new qtype_ddmarker_question_converter_list($pagestate, $link, $questions, $categorylist);

    foreach ($questions as $question) {
        $questionlist->leaf_node($question->id, 1);
    }
    if ($questionid) {
        $top = $questionlist->get_instance($questionid);
    } else if ($categoryid) {
        $top = $categorylist->get_instance($categoryid);
    } else if ($qcontextid) {
        $top = $contextlist->get_instance($qcontextid);
    } else {
        $top = $contextlist->root_node();
    }
    switch ($pagestate) {
        case 'listall' :
            echo $renderer->render_qtype_ddmarker_list($top);
            break;
        case 'confirm' :
            echo $renderer->render_qtype_ddmarker_list($top);
            $cofirmedurl = new moodle_url($PAGE->url, compact('categoryid', 'contextid', 'questionid') + array('confirm' => 1));
            $cancelurl = new moodle_url($PAGE->url);
            echo $renderer->confirm(get_string('confirmimagetargetconversion', 'qtype_ddmarker'), $cofirmedurl, $cancelurl);
            break;
        case 'processing' :
            $questionlist->prepare_for_processing($top);
            echo '<ul>';
            $top->process($renderer);
            echo '</ul>';
            break;
        default :
            break;
    }
}
// Footer.
echo $renderer->footer();
