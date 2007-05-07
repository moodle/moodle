<?php // $Id$
/**
 * Page for editing questions using the new form library.
 *
 * @author T.J.Hunt@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questionbank
 *//** */

// Includes.
require_once(dirname(__FILE__) . '/../config.php');
require_once(dirname(__FILE__) . '/editlib.php');
require_once($CFG->libdir . '/filelib.php');
require_once($CFG->libdir . '/formslib.php');

$returnurl = optional_param('returnurl', 0, PARAM_URL);

// Read URL parameters telling us which question to edit.
$id = optional_param('id', 0, PARAM_INT); // question id
$cmid = optional_param('cmid', 0, PARAM_INT);
$qtype = optional_param('qtype', '', PARAM_FILE);
$categoryid = optional_param('category', 0, PARAM_INT);
$wizardnow =  optional_param('wizardnow', '', PARAM_ALPHA);

if ($cmid){
    list($module, $cm) = get_module_from_cmid($cmid); 
} else {
    $module = null;
    $cm = null;
}

// Validate the URL parameters.
if ($id) {
    if (!$question = get_record('question', 'id', $id)) {
        print_error('questiondoesnotexist', 'question', $returnurl);
    }
    get_question_options($question);
} else if ($categoryid && $qtype) { // only for creating new questions
    $question = new stdClass;
    $question->category = $categoryid;
    $question->qtype = $qtype;
} else {
    print_error('notenoughdatatoeditaquestion', 'question', $returnurl);
}

// Validate the question category.
if (!$category = get_record('question_categories', 'id', $question->category)) {
    print_error('categorydoesnotexist', 'question', $returnurl);
}
if (!$returnurl) {
    $returnurl = "{$CFG->wwwroot}/question/edit.php?courseid={$category->course}";
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
if ($wizardnow!==''){
    if (!method_exists($QTYPES[$question->qtype], 'next_wizard_form')){
        print_error('missingimportantcode', 'question', $returnurl, 'wizard form definition');
    } else {
        $mform = $QTYPES[$question->qtype]->next_wizard_form('question.php', $question, $wizardnow);
    }
} else {
    $mform = $QTYPES[$question->qtype]->create_editing_form('question.php', $question);
}

if ($mform === null) {
    print_error('missingimportantcode', 'question', $returnurl, 'question editing form definition for "'.$question->qtype.'"');
}
$toform = $question; // send the question object and a few more parameters to the form
$toform->returnurl = $returnurl;
if ($cm !== null){
    $toform->cmid = $cm->id;
}
$mform->set_data($toform);

if ($mform->is_cancelled()){
    redirect($returnurl);
} elseif ($data = $mform->get_data()){
    if (!empty($data->makecopy)) {
        $question->id = 0;  // causes a new question to be created.
        $question->hidden = 0; // Copies should not be hidden
    }
    $question = $QTYPES[$question->qtype]->save_question($question, $data, $COURSE, $wizardnow);
    if ($QTYPES[$qtype]->finished_edit_wizard($data)){
        if (optional_param('inpopup', 0, PARAM_BOOL)) {
            notify(get_string('changessaved'), '');
            close_window(3);
        } else {
            redirect($returnurl);
        }
        die;
    } else {
        //useful for passing data to the next page which is not saved in the database
        $queryappend = '';
        if (isset($data->nextpageparam)){
            foreach ($data->nextpageparam as $key => $param){
                $queryappend .= "&".urlencode($key).'='.urlencode($param);
            }
        }
        if ($question->id) {
            $nexturl = "question.php?id=$question->id&returnurl=" . urlencode($returnurl);
        } else { // only for creating new questions
            $nexturl = "question.php?category=$question->category&qtype=$question->qtype&returnurl=".urlencode($returnurl);
        }
        redirect($nexturl.'&wizardnow='.$data->wizard.$queryappend, '', 20);
    }
} else {

    list($streditingquestion,) = $QTYPES[$question->qtype]->get_heading();
    if ($cm !== null) {
        $strupdatemodule = has_capability('moodle/course:manageactivities', get_context_instance(CONTEXT_COURSE, $category->course))
            ? update_module_button($cm->id, $category->course, get_string('modulename', $cm->modname))
            : "";
        $crumbs = array();
        $crumbs[] = array('name' => get_string('modulenameplural', $cm->modname), 'link' => "$CFG->wwwroot/mod/{$cm->modname}/index.php?id=$category->course", 'type' => 'activity');
        $crumbs[] = array('name' => format_string($module->name), 'link' => "$CFG->wwwroot/mod/{$cm->modname}/view.php?cmid={$cm->id}", 'type' => 'title');
        $crumbs[] = array('name' => get_string('editingquiz', 'quiz'), 'link' => $returnurl, 'type' => 'title');
        $crumbs[] = array('name' => $streditingquestion, 'link' => '', 'type' => 'title');
        $navigation = build_navigation($crumbs);
        print_header_simple($streditingquestion, '', $navigation, "", "", true, $strupdatemodule);
    
    } else {
        $crumbs = array();
        $crumbs[] = array('name' => get_string('editquestions', "quiz"), 'link' => $returnurl, 'type' => 'title');
        $crumbs[] = array('name' => $streditingquestion, 'link' => '', 'type' => 'title');
        $strediting = '<a href="edit.php?courseid='.$category->course.'">'.
                get_string("editquestions", "quiz").'</a> -> '.$streditingquestion;
        $navigation = build_navigation($crumbs);
        print_header_simple($streditingquestion, '', $navigation);
    }

    // Display a heading, question editing form and possibly some extra content needed for
    // for this question type.
    $QTYPES[$question->qtype]->display_question_editing_page($mform, $question, $wizardnow);

    print_footer($COURSE);
}
?>
