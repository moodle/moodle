<?php
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

// Read URL parameters telling us which question to edit.
$id = optional_param('id', 0, PARAM_INT); // question id
$qtype = optional_param('qtype', '', PARAM_FILE);
$categoryid = optional_param('category', 0, PARAM_INT);
$cmid = optional_param('cmid', 0, PARAM_INT);
$courseid = optional_param('courseid', 0, PARAM_INT);
$wizardnow = optional_param('wizardnow', '', PARAM_ALPHA);
$movecontext = optional_param('movecontext', 0, PARAM_BOOL); // Switch to make
        // question uneditable - form is displayed to edit category only
$originalreturnurl = optional_param('returnurl', 0, PARAM_LOCALURL);
$appendqnumstring = optional_param('appendqnumstring', '', PARAM_ALPHA);
$inpopup = optional_param('inpopup', 0, PARAM_BOOL);

$url = new moodle_url('/question/question.php');
if ($id !== 0) {
    $url->param('id', $id);
}
if ($qtype !== '') {
    $url->param('qtype', $qtype);
}
if ($categoryid !== 0) {
    $url->param('category', $categoryid);
}
if ($cmid !== 0) {
    $url->param('cmid', $cmid);
}
if ($courseid !== 0) {
    $url->param('courseid', $courseid);
}
if ($wizardnow !== '') {
    $url->param('wizardnow', $wizardnow);
}
if ($movecontext !== 0) {
    $url->param('movecontext', $movecontext);
}
if ($originalreturnurl !== 0) {
    $url->param('returnurl', $originalreturnurl);
}
if ($appendqnumstring !== '') {
    $url->param('appendqnumstring', $appendqnumstring);
}
if ($inpopup !== 0) {
    $url->param('inpopup', $inpopup);
}
$PAGE->set_url($url);

if ($originalreturnurl) {
    $returnurl = $CFG->wwwroot . '/' . $originalreturnurl;
} else {
    $returnurl = "{$CFG->wwwroot}/question/edit.php?courseid={$COURSE->id}";
}

if ($movecontext && !$id){
    print_error('questiondoesnotexist', 'question', $returnurl);
}

if ($cmid){
    list($module, $cm) = get_module_from_cmid($cmid);
    require_login($cm->course, false, $cm);
    $thiscontext = get_context_instance(CONTEXT_MODULE, $cmid);
} elseif ($courseid) {
    require_login($courseid, false);
    $PAGE->set_pagelayout('course');
    $thiscontext = get_context_instance(CONTEXT_COURSE, $courseid);
    $module = null;
    $cm = null;
} else {
    print_error('missingcourseorcmid', 'question');
}
$contexts = new question_edit_contexts($thiscontext);

if (optional_param('addcancel', false, PARAM_BOOL)) {
    redirect($returnurl);
}

if ($id) {
    if (!$question = $DB->get_record('question', array('id' => $id))) {
        print_error('questiondoesnotexist', 'question', $returnurl);
    }
    get_question_options($question, true);

} else if ($categoryid && $qtype) { // only for creating new questions
    $question = new stdClass;
    $question->category = $categoryid;
    $question->qtype = $qtype;

    // Check that users are allowed to create this question type at the moment.
    $allowedtypes = question_type_menu();
    if (!isset($allowedtypes[$qtype])) {
        print_error('cannotenable', 'question', $returnurl, $qtype);
    }

} else if ($categoryid) {
    // Category, but no qtype. They probably came from the addquestion.php
    // script without choosing a question type. Send them back.
    $addurl = new moodle_url('/question/addquestion.php', $url->params());
    $addurl->param('validationerror', 1);
    redirect($addurl);

} else {
    print_error('notenoughdatatoeditaquestion', 'question', $returnurl);
}

// Validate the question category.
if (!$category = $DB->get_record('question_categories', array('id' => $question->category))) {
    print_error('categorydoesnotexist', 'question', $returnurl);
}

// Check permissions
$question->formoptions = new stdClass;

$categorycontext = get_context_instance_by_id($category->contextid);
$addpermission = has_capability('moodle/question:add', $categorycontext);

if ($id) {
    $canview = question_has_capability_on($question, 'view');
    if ($movecontext){
        $question->formoptions->canedit = false;
        $question->formoptions->canmove = (question_has_capability_on($question, 'move') && $contexts->have_cap('moodle/question:add'));
        $question->formoptions->cansaveasnew = false;
        $question->formoptions->repeatelements = false;
        $question->formoptions->movecontext = true;
        $formeditable = true;
        question_require_capability_on($question, 'view');
    } else {
        $question->formoptions->canedit = question_has_capability_on($question, 'edit');
        $question->formoptions->canmove = (question_has_capability_on($question, 'move') && $addpermission);
        $question->formoptions->cansaveasnew = (($canview ||question_has_capability_on($question, 'edit')) && $addpermission);
        $question->formoptions->repeatelements = ($question->formoptions->canedit || $question->formoptions->cansaveasnew);
        $formeditable =  $question->formoptions->canedit || $question->formoptions->cansaveasnew || $question->formoptions->canmove;
        $question->formoptions->movecontext = false;
        if (!$formeditable){
            question_require_capability_on($question, 'view');
        }
    }

} else  { // creating a new question
    require_capability('moodle/question:add', $categorycontext);
    $formeditable = true;
    $question->formoptions->canedit = question_has_capability_on($question, 'edit');
    $question->formoptions->canmove = (question_has_capability_on($question, 'move') && $addpermission);
    $question->formoptions->repeatelements = true;
    $question->formoptions->movecontext = false;
}

// Validate the question type.
if (!isset($QTYPES[$question->qtype])) {
    print_error('unknownquestiontype', 'question', $returnurl, $question->qtype);
}
$PAGE->set_pagetype('question-type-' . $question->qtype);

// Create the question editing form.
if ($wizardnow!=='' && !$movecontext){
    if (!method_exists($QTYPES[$question->qtype], 'next_wizard_form')){
        print_error('missingimportantcode', 'question', $returnurl, 'wizard form definition');
    } else {
        $mform = $QTYPES[$question->qtype]->next_wizard_form('question.php', $question, $wizardnow, $formeditable);
    }
} else {
    $mform = $QTYPES[$question->qtype]->create_editing_form('question.php', $question, $category, $contexts, $formeditable);
}
if ($mform === null) {
    print_error('missingimportantcode', 'question', $returnurl, 'question editing form definition for "'.$question->qtype.'"');
}
$toform = fullclone($question); // send the question object and a few more parameters to the form
$toform->category = "$category->id,$category->contextid";
if ($formeditable && $id){
    $toform->categorymoveto = $toform->category;
}

$toform->appendqnumstring = $appendqnumstring;
$toform->returnurl = $originalreturnurl;
$toform->movecontext = $movecontext;
if ($cm !== null){
    $toform->cmid = $cm->id;
    $toform->courseid = $cm->course;
} else {
    $toform->courseid = $COURSE->id;
}

$toform->inpopup = $inpopup;

$mform->set_data($toform);

if ($mform->is_cancelled()){
    if ($inpopup) {
        close_window();
    } else {
        $nexturl = new moodle_url($returnurl);
        if (!empty($question->id)) {
            $nexturl->param('lastchanged', $question->id);
        }
        redirect($nexturl->out());
    }
} elseif ($fromform = $mform->get_data()) {
    /// If we are saving as a copy, break the connection to the old question.
    if (!empty($fromform->makecopy)) {
        $question->id = 0;
        $question->hidden = 0; // Copies should not be hidden
    }

    /// Process the combination of usecurrentcat, categorymoveto and category form
    /// fields, so the save_question method only has to consider $fromform->category
    if (!empty($fromform->usecurrentcat)) {
        // $fromform->category is the right category to save in.
    } else {
        if (!empty($fromform->categorymoveto)) {
            $fromform->category = $fromform->categorymoveto;
        } else {
            // $fromform->category is the right category to save in.
        }
    }

    /// If we are moving a question, check we have permission to move it from
    /// whence it came. (Where we are moving to is validated by the form.)
    list($newcatid) = explode(',', $fromform->category);
    if (!empty($question->id) && $newcatid != $question->category) {
        question_require_capability_on($question, 'move');
    }

    /// Ensure we redirect back to the category the question is being saved into.
    $returnurl = new moodle_url($returnurl);
    $returnurl->param('category', $fromform->category);
    // TODO: it is sloppy to pass arounf full URLs through page parameters and some servers do not like that
    $returnurl = $returnurl->out(false);

    /// Call the appropriate method.
    if ($movecontext) {
        list($tocatid, $tocontextid) = explode(',', $fromform->categorymoveto);
        $tocontext = get_context_instance_by_id($tocontextid);
        require_capability('moodle/question:add', $tocontext);
        if (get_filesdir_from_context($categorycontext) != get_filesdir_from_context($tocontext)){
            $movecontexturl  = new moodle_url('/question/contextmoveq.php',
                                            array('returnurl' => $returnurl,
                                                    'ids'=>$question->id,
                                                    'tocatid'=> $tocatid));
            if ($cmid){
                $movecontexturl->param('cmid', $cmid);
            } else {
                $movecontexturl->param('courseid', $COURSE->id);
            }
            redirect($movecontexturl);
        }
    }

    $question = $QTYPES[$question->qtype]->save_question($question, $fromform, $COURSE, $wizardnow, true);
    // a wizardpage from multipe pages questiontype like calculated may not allow editing the question tags
    if (!empty($CFG->usetags) && isset($fromform->tags)) {
        require_once($CFG->dirroot.'/tag/lib.php');
        tag_set('question', $question->id, $fromform->tags);
    }

    if (($QTYPES[$question->qtype]->finished_edit_wizard($fromform)) || $movecontext){
        if ($inpopup) {
            echo $OUTPUT->notification(get_string('changessaved'), '');
            close_window(3);
        } else {
            $nexturl = new moodle_url($returnurl);
            $nexturl->param('lastchanged', $question->id);
            if($appendqnumstring) {
                $nexturl->params(array($appendqnumstring=>($question->id), "sesskey"=>sesskey(), "cmid"=>$cmid));
            }
            redirect($nexturl);
        }
    } else {
        $nexturlparams = array('returnurl'=>$returnurl, 'appendqnumstring'=>$appendqnumstring);
        if (isset($fromform->nextpageparam) && is_array($fromform->nextpageparam)){
            $nexturlparams += $fromform->nextpageparam;//useful for passing data to the next page which is not saved in the database
        }
        $nexturlparams['id'] = $question->id;
        $nexturlparams['wizardnow'] = $fromform->wizard;
        $nexturl = new moodle_url('question.php', $nexturlparams);
        if ($cmid){
            $nexturl->param('cmid', $cmid);
        } else {
            $nexturl->param('courseid', $COURSE->id);
        }
        redirect($nexturl);
    }
} else {

    $streditingquestion = $QTYPES[$question->qtype]->get_heading();
    $PAGE->set_title($streditingquestion);
    $PAGE->set_heading($COURSE->fullname);
    if ($cm !== null) {
        $strmodule = get_string('modulename', $cm->modname);
        $streditingmodule = get_string('editinga', 'moodle', $strmodule);
        $PAGE->navbar->add(get_string('modulenameplural', $cm->modname), new moodle_url('/mod/'.$cm->modname.'/index.php', array('id'=>$cm->course)));
        $PAGE->navbar->add(format_string($module->name), new moodle_url('/mod/'.$cm->modname.'/view.php', array('id'=>$cm->id)));
        if (stripos($returnurl, "$CFG->wwwroot/mod/{$cm->modname}/view.php")!== 0){
            //don't need this link if returnurl returns to view.php
            $PAGE->navbar->add($streditingmodule, $returnurl);
        }
        $PAGE->navbar->add($streditingquestion);
        echo $OUTPUT->header();

    } else {
        $strediting = '<a href="edit.php?courseid='.$COURSE->id.'">'.get_string("editquestions", "quiz").'</a> -> '.$streditingquestion;
        $PAGE->navbar->add(get_string('editquestions', "quiz"), $returnurl);
        $PAGE->navbar->add($streditingquestion);
        echo $OUTPUT->header();
    }

    // Display a heading, question editing form and possibly some extra content needed for
    // for this question type.
    $QTYPES[$question->qtype]->display_question_editing_page($mform, $question, $wizardnow);
    echo $OUTPUT->footer();
}

