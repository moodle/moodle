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

// Read URL parameters telling us which question to edit.
$id = optional_param('id', 0, PARAM_INT); // question id
$qtype = optional_param('qtype', '', PARAM_FILE);
$categoryid = optional_param('category', 0, PARAM_INT);
$cmid = optional_param('cmid', 0, PARAM_INT);
$courseid = optional_param('courseid', 0, PARAM_INT);
$wizardnow = optional_param('wizardnow', '', PARAM_ALPHA);
$movecontext = optional_param('movecontext', 0, PARAM_BOOL); // Switch to make
        // question uneditable - form is displayed to edit category only
$returnurl = optional_param('returnurl', 0, PARAM_LOCALURL);
$inpopup = optional_param('inpopup', 0, PARAM_BOOL);

if ($movecontext && !$id){
    print_error('questiondoesnotexist', 'question', $returnurl);
}

if ($cmid){
    list($module, $cm) = get_module_from_cmid($cmid);
    require_login($cm->course, false, $cm);
    $thiscontext = get_context_instance(CONTEXT_MODULE, $cmid);
} elseif ($courseid) {
    require_login($courseid, false);
    $thiscontext = get_context_instance(CONTEXT_COURSE, $courseid);
    $module = null;
    $cm = null;
} else {
    error('Need to pass courseid or cmid to this script.');
}
$contexts = new question_edit_contexts($thiscontext);

if (!$returnurl) {
    $returnurl = "{$CFG->wwwroot}/question/edit.php?courseid={$COURSE->id}";
}

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

//permissions
$question->formoptions = new object();

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
    $question->formoptions->repeatelements = true;
    $question->formoptions->movecontext = false;
}


// Validate the question type.
if (!isset($QTYPES[$question->qtype])) {
    print_error('unknownquestiontype', 'question', $returnurl, $question->qtype);
}
$CFG->pagepath = 'question/type/' . $question->qtype;

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
$toform->returnurl = $returnurl;
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
        $nexturl->param('lastchanged', $question->id);
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
    $returnurl = $returnurl->out();

    /// Call the appropriate method.
    if ($movecontext) {
        list($tocatid, $tocontextid) = explode(',', $fromform->categorymoveto);
        $tocontext = get_context_instance_by_id($tocontextid);
        require_capability('moodle/question:add', $tocontext);
        if (get_filesdir_from_context($categorycontext) != get_filesdir_from_context($tocontext)){
            $movecontexturl  = new moodle_url($CFG->wwwroot.'/question/contextmoveq.php',
                                            array('returnurl' => $returnurl,
                                                    'ids'=>$question->id,
                                                    'tocatid'=> $tocatid));
            if ($cmid){
                $movecontexturl->param('cmid', $cmid);
            } else {
                $movecontexturl->param('courseid', $COURSE->id);
            }
            redirect($movecontexturl->out());
        }
    }

    $question = $QTYPES[$question->qtype]->save_question($question, $fromform, $COURSE, $wizardnow);
    if (($QTYPES[$question->qtype]->finished_edit_wizard($fromform)) || $movecontext){
        if ($inpopup) {
            notify(get_string('changessaved'), '');
            close_window(3);
        } else {
            $nexturl = new moodle_url($returnurl);
            $nexturl->param('lastchanged',$question->id);
            redirect($nexturl->out());
        }
    } else {
        $nexturlparams = array('returnurl'=>$returnurl);
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
        redirect($nexturl->out());
    }
} else {

    list($streditingquestion,) = $QTYPES[$question->qtype]->get_heading();
    $headtags = get_editing_head_contributions($question);
    if ($cm !== null) {
        $strmodule = get_string('modulename', $cm->modname);
        $strupdatemodule = has_capability('moodle/course:manageactivities', get_context_instance(CONTEXT_COURSE, $COURSE->id))
            ? update_module_button($cm->id, $cm->course, $strmodule)
            : "";

        $streditingmodule = get_string('editinga', 'moodle', $strmodule);

        $navlinks = array();
        $navlinks[] = array('name' => get_string('modulenameplural', $cm->modname), 'link' => "$CFG->wwwroot/mod/{$cm->modname}/index.php?id=$cm->course", 'type' => 'activity');
        $navlinks[] = array('name' => format_string($module->name), 'link' => "$CFG->wwwroot/mod/{$cm->modname}/view.php?id={$cm->id}", 'type' => 'title');
        if (stripos($returnurl, "$CFG->wwwroot/mod/{$cm->modname}/view.php")!== 0){
            //don't need this link if returnurl returns to view.php
            $navlinks[] = array('name' => $streditingmodule, 'link' => $returnurl, 'type' => 'title');
        }
        $navlinks[] = array('name' => $streditingquestion, 'link' => '', 'type' => 'title');
        $navigation = build_navigation($navlinks);
        print_header_simple($streditingquestion, '', $navigation, '', $headtags, true, $strupdatemodule);

    } else {
        $navlinks = array();
        $navlinks[] = array('name' => get_string('editquestions', "quiz"), 'link' => $returnurl, 'type' => 'title');
        $navlinks[] = array('name' => $streditingquestion, 'link' => '', 'type' => 'title');
        $strediting = '<a href="edit.php?courseid='.$COURSE->id.'">'.
                get_string("editquestions", "quiz").'</a> -> '.$streditingquestion;
        $navigation = build_navigation($navlinks);
        print_header_simple($streditingquestion, '', $navigation, '', $headtags);
    }


    // Display a heading, question editing form and possibly some extra content needed for
    // for this question type.
    $QTYPES[$question->qtype]->display_question_editing_page($mform, $question, $wizardnow);
    print_footer($COURSE);
}
?>
