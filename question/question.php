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
 * Page for editing questions.
 *
 * @package    moodlecore
 * @subpackage questionbank
 * @copyright  1999 onwards Martin Dougiamas {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


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
$scrollpos = optional_param('scrollpos', 0, PARAM_INT);

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
if ($scrollpos) {
    $url->param('scrollpos', $scrollpos);
}
$PAGE->set_url($url);

if ($originalreturnurl) {
    if (strpos($originalreturnurl, '/') !== 0) {
        throw new coding_exception("returnurl must be a local URL starting with '/'. $originalreturnurl was given.");
    }
    $returnurl = new moodle_url($originalreturnurl);
} else if ($cmid) {
    $returnurl = new moodle_url('/question/edit.php', array('cmid' => $cmid));
} else {
    $returnurl = new moodle_url('/question/edit.php', array('courseid' => $courseid));
}
if ($scrollpos) {
    $returnurl->param('scrollpos', $scrollpos);
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
    $thiscontext = get_context_instance(CONTEXT_COURSE, $courseid);
    $module = null;
    $cm = null;
} else {
    print_error('missingcourseorcmid', 'question');
}
$contexts = new question_edit_contexts($thiscontext);
$PAGE->set_pagelayout('admin');

if (optional_param('addcancel', false, PARAM_BOOL)) {
    redirect($returnurl);
}

if ($id) {
    if (!$question = $DB->get_record('question', array('id' => $id))) {
        print_error('questiondoesnotexist', 'question', $returnurl);
    }
    get_question_options($question, true);

} else if ($categoryid && $qtype) { // only for creating new questions
    $question = new stdClass();
    $question->category = $categoryid;
    $question->qtype = $qtype;

    // Check that users are allowed to create this question type at the moment.
    if (!question_bank::qtype_enabled($qtype)) {
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

$qtypeobj = question_bank::get_qtype($question->qtype);

// Validate the question category.
if (!$category = $DB->get_record('question_categories', array('id' => $question->category))) {
    print_error('categorydoesnotexist', 'question', $returnurl);
}

// Check permissions
$question->formoptions = new stdClass();

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
$PAGE->set_pagetype('question-type-' . $question->qtype);

// Create the question editing form.
if ($wizardnow !== '' && !$movecontext){
    $mform = $qtypeobj->next_wizard_form('question.php', $question, $wizardnow, $formeditable);
} else {
    $mform = $qtypeobj->create_editing_form('question.php', $question, $category, $contexts, $formeditable);
}
$toform = fullclone($question); // send the question object and a few more parameters to the form
$toform->category = "$category->id,$category->contextid";
$toform->scrollpos = $scrollpos;
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

if ($mform->is_cancelled()) {
    if ($inpopup) {
        close_window();
    } else {
        redirect($returnurl);
    }

} else if ($fromform = $mform->get_data()) {
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

    // Ensure we redirect back to the category the question is being saved into.
    $returnurl->param('category', $fromform->category);

    if ($movecontext) {
        // We are just moving the question to a different context.
        list($tocatid, $tocontextid) = explode(',', $fromform->categorymoveto);
        require_capability('moodle/question:add', get_context_instance_by_id($tocontextid));
        question_move_questions_to_category(array($question->id), $tocatid);

    } else {
        // We are acutally saving the question.
        $question = $qtypeobj->save_question($question, $fromform);
        if (!empty($CFG->usetags) && isset($fromform->tags)) {
            // A wizardpage from multipe pages questiontype like calculated may not
            // allow editing the question tags, hence the isset($fromform->tags) test.
            require_once($CFG->dirroot.'/tag/lib.php');
            tag_set('question', $question->id, $fromform->tags);
        }
    }

    if (($qtypeobj->finished_edit_wizard($fromform)) || $movecontext) {
        if ($inpopup) {
            echo $OUTPUT->notification(get_string('changessaved'), '');
            close_window(3);
        } else {
            $returnurl->param('lastchanged', $question->id);
            if ($appendqnumstring) {
                $returnurl->param($appendqnumstring, $question->id);
                $returnurl->param('sesskey', sesskey());
                $returnurl->param('cmid', $cmid);
            }
            redirect($returnurl);
        }

    } else {
        $nexturlparams = array(
                'returnurl' => $originalreturnurl,
                'appendqnumstring' => $appendqnumstring,
                'scrollpos' => $scrollpos);
        if (isset($fromform->nextpageparam) && is_array($fromform->nextpageparam)){
            //useful for passing data to the next page which is not saved in the database.
            $nexturlparams += $fromform->nextpageparam;
        }
        $nexturlparams['id'] = $question->id;
        $nexturlparams['wizardnow'] = $fromform->wizard;
        $nexturl = new moodle_url('/question/question.php', $nexturlparams);
        if ($cmid){
            $nexturl->param('cmid', $cmid);
        } else {
            $nexturl->param('courseid', $COURSE->id);
        }
        redirect($nexturl);
    }

} else {
    $streditingquestion = $qtypeobj->get_heading();
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
        $strediting = '<a href="edit.php?courseid='.$COURSE->id.'">'.get_string('editquestions', 'question').'</a> -> '.$streditingquestion;
        $PAGE->navbar->add(get_string('editquestions', 'question'), $returnurl);
        $PAGE->navbar->add($streditingquestion);
        echo $OUTPUT->header();
    }

    // Display a heading, question editing form and possibly some extra content needed for
    // for this question type.
    $qtypeobj->display_question_editing_page($mform, $question, $wizardnow);
    echo $OUTPUT->footer();
}
