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

$categoryid = optional_param('categoryid', 0, PARAM_INT);
$qcontextid = optional_param('contextid', 0, PARAM_INT);
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
$where = ' WHERE q.category =  cat.id ';

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
}
$sql = 'SELECT q.id, cat.id AS cat_id, cat.contextid, q.name '.
                                            $from.
                                            $where.
                                            'ORDER BY cat.id, q.name';

$questions = $DB->get_records_sql($sql, $params);

$contextids = array();
foreach ($questions as $question) {
    $contextids[] = $question->contextid;
}

$contextlist = new qtype_ddmarker_context_list(array_unique($contextids));
$categorylist = new qtype_ddmarker_category_list($contextids, $contextlist);
$questionlist = new qtype_ddmarker_question_list($questions, $categorylist);

foreach ($questions as $question) {
    $questionlist->leaf_node($question->id, 1);
}
$questionsselected = (bool) ($categoryid || $qcontextid);
if ($categoryid) {
    $top = $categorylist->get_instance($categoryid);
} else if ($qcontextid) {
    $top = $contextlist->get_instance($qcontextid);
} else {
    $top = $contextlist->root_node();
}
if (!$confirm) {
    if ($questionsselected) {
        echo $contextlist->render('listitemaction', false, $top);
        $cofirmedurl = new moodle_url($PAGE->url, compact('categoryid', 'contextid')+array('confirm'=>1));
        $cancelurl = new moodle_url($PAGE->url);
        echo $OUTPUT->confirm(get_string('confirmimagetargetconversion', 'qtype_ddmarker'), $cofirmedurl, $cancelurl);
    } else {
        echo $contextlist->render('listitem', true, $top);
    }
} else if (confirm_sesskey()) {
    $top->process();
}

// Footer.
echo $OUTPUT->footer();
