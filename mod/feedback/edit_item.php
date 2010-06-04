<?php

/**
 * prints the form to edit a dedicated item
 *
 * @author Andreas Grabs
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package feedback
 */

require_once("../../config.php");
require_once("lib.php");

feedback_init_feedback_session();

// $cmid = optional_param('cmid', NULL, PARAM_INT);
$cmid = required_param('cmid', PARAM_INT);
$typ = optional_param('typ', false, PARAM_ALPHA);
$id = optional_param('id', false, PARAM_INT);
$action = optional_param('action', false, PARAM_ALPHA);

$editurl = new moodle_url('/mod/feedback/edit.php', array('id'=>$cmid));

if(!$typ)redirect($editurl->out(false));

$url = new moodle_url('/mod/feedback/edit_item.php', array('cmid'=>$cmid));
if ($typ !== false) {
    $url->param('typ', $typ);
}
if ($id !== false) {
    $url->param('id', $id);
}
$PAGE->set_url($url);

// set up some general variables
$usehtmleditor = can_use_html_editor();


if(($formdata = data_submitted()) AND !confirm_sesskey()) {
    print_error('invalidsesskey');
}

if (! $cm = get_coursemodule_from_id('feedback', $cmid)) {
    print_error('invalidcoursemodule');
}

if (! $course = $DB->get_record("course", array("id"=>$cm->course))) {
    print_error('coursemisconf');
}

if (! $feedback = $DB->get_record("feedback", array("id"=>$cm->instance))) {
    print_error('invalidcoursemodule');
}

if (!$context = get_context_instance(CONTEXT_MODULE, $cm->id)) {
        print_error('badcontext');
}

require_login($course->id, true, $cm);

require_capability('mod/feedback:edititems', $context);

//if the typ is pagebreak so the item will be saved directly
if($typ == 'pagebreak') {
    feedback_create_pagebreak($feedback->id);
    redirect($editurl->out(false));
    exit;
}

//get the existing item or create it
// $formdata->itemid = isset($formdata->itemid) ? $formdata->itemid : NULL;
if($id and $item = $DB->get_record('feedback_item', array('id'=>$id))) {
    $typ = $item->typ;
}else {
    $item = new stdClass();
    $item->id = null;
    $item->position = -1;
    if (!$typ) {
        print_error('typemissing', 'feedback', $editurl->out(false));
    }
    $item->typ = $typ;
    $item->options = '';
}

require_once($CFG->dirroot.'/mod/feedback/item/'.$typ.'/lib.php');

$itemobj = feedback_get_item_class($typ);

$itemobj->build_editform($item, $feedback, $cm);

if($itemobj->is_cancelled()) {
    redirect($editurl->out(false));
    exit;
}
if($itemobj->get_data()) {
    if($item = $itemobj->save_item()) {
        feedback_move_item($item, $item->position);
        redirect($editurl->out(false));
    }
}

////////////////////////////////////////////////////////////////////////////////////
/// Print the page header
$strfeedbacks = get_string("modulenameplural", "feedback");
$strfeedback  = get_string("modulename", "feedback");

if ($item->id) {
    $PAGE->navbar->add(get_string('edit_item', 'feedback'));
} else {
    $PAGE->navbar->add(get_string('add_item', 'feedback'));
}
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_title(format_string($feedback->name));
echo $OUTPUT->header();
/// print the tabs
include('tabs.php');
/// Print the main part of the page
echo $OUTPUT->heading(format_text($feedback->name));
//print errormsg
if(isset($error)) {
    echo $error;
}
feedback_print_errors();
$itemobj->show_editform();

// echo $OUTPUT->box_end();

if ($typ!='label') {
    $PAGE->requires->js('/mod/feedback/feedback.js');
    $PAGE->requires->js_function_call('set_item_focus', Array('id_itemname'));
}

/// Finish the page
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////

echo $OUTPUT->footer();

