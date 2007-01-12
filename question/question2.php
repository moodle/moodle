<?php // $Id$
/**
 * Page for editing questions using the new form library.
 *
 * TODO: currently this still treats the quiz as special
 *
 * @author T.J.Hunt@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package question
 *//** */

// Includes.
require_once(dirname(__FILE__) . '/../config.php');
require_once(dirname(__FILE__) . '/editlib.php');
require_once($CFG->libdir . '/filelib.php');
require_once($CFG->libdir . '/formslib.php');

$returnurl = optional_param('returnurl', 0, PARAM_URL);
if (!$returnurl && isset($SESSION->fromurl)) {
    $returnurl = $SESSION->fromurl;
}

// Read URL parameters telling us which question to edit.
$id = optional_param('id', 0, PARAM_INT); // question id
$qtype = optional_param('qtype', '', PARAM_FILE);
$categoryid = optional_param('category', 0, PARAM_INT);
$wizard =  optional_param('wizard', '', PARAM_ALPHA);

// Validate the URL parameters.
if ($id = optional_param('id', 0, PARAM_INT)) {
    if (!$question = get_record("question", "id", $id)) {
        print_error('questiondoesnotexist', 'question', $returnurl);
    }
    get_question_options($question);
    $submiturl = "question2.php?id=$id&returnurl=" . urlencode($returnurl).'&wizard='.$wizard;
} else if ($categoryid && $qtype) { // only for creating new questions
    $question = new stdClass;
    $question->category = $categoryid;
    $question->qtype = $qtype;
    $submiturl = "question2.php?category=$categoryid&qtype=$qtype&returnurl=" . urlencode($returnurl).'&wizard='.$wizard;
} else {
    print_error('notenoughdatatoeditaquestion', 'question', $returnurl);
}

// Validate the question category.
if (!$category = get_record('question_categories', 'id', $question->category)) {
    print_error('categorydoesnotexist', 'question', $returnurl);
}
if (!$returnurl) {
    $returnurl = "{$CFG->wwwroot}/question/edit.php?courseid={$category->course}";
    $SESSION->fromurl = $returnurl;
}

// Validate the question type.
if (!isset($QTYPES[$question->qtype])) {
    print_error('unknownquestiontype', 'question', $returnurl, $question->qtype);
}
$CFG->pagepath = 'question/type/' . $question->qtype;

// Check the user is logged in and has enough premissions.
require_login($category->course, false);
$coursecontext = get_context_instance(CONTEXT_COURSE, $category->course);
require_capability('moodle/question:manage', $coursecontext);

// Create the question editing form.
if ($wizard!==''){
    if (!method_exists($QTYPES[$question->qtype], 'next_wizard_form')){
        print_error('missingimportantcode', 'question', $returnurl, 'wizard form definition');
    } else {
        $mform = $QTYPES[$question->qtype]->next_wizard_form($submiturl, $question, $wizard);
    }
} else {
    $mform = $QTYPES[$question->qtype]->create_editing_form($submiturl, $question, $category->course);
}

if ($mform === null) {
    print_error('missingimportantcode', 'question', $returnurl, 'question editing form definition');
}
$mform->set_data($question);

if ($mform->is_cancelled()){
    redirect($returnurl);
} else if ($data = $mform->get_data()){
    if (!empty($data->makecopy)) {
        $question->id = 0;  // causes a new question to be created.
        $question->hidden = 0; // Copies should not be hidden
    }
    $question = $QTYPES[$qtype]->save_question($question, $data, $COURSE);
    if ($QTYPES[$qtype]->finished_edit_wizard($question)){
        if (optional_param('inpopup', 0, PARAM_BOOL)) {
            notify(get_string('changessaved'), '');
            close_window(3);
        } else {
            redirect($SESSION->returnurl);
        }
        die;
    } else {
        redirect($submiturl.'&wizard='.$data->wizardpage);
    }
} else {
    // Display the question editing form
    $streditingquestion = get_string('editingquestion', 'question');
    if (isset($SESSION->modform->instance)) {
        // TODO: remove restriction to quiz
        $strediting = '<a href="' . $returnurl . '">' . get_string('editingquiz', 'quiz') . '</a> -> '.
                $streditingquestion;
    } else {
        $strediting = '<a href="edit.php?courseid='.$course->id.'">'.
                get_string("editquestions", "quiz").'</a> -> '.$streditingquestion;
    }
    print_header_simple($streditingquestion, '', $strediting);
    if (isset($mform->heading)){
        print $mform->heading;
    } else {
        print_heading_with_help(get_string("editing".$question->qtype, "quiz"), $question->qtype, "quiz");
    }
    $mform->display();
    print_footer($COURSE);
}
?>
