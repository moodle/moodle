<?php

/**
 * print a printview of feedback-items
 *
 * @author Andreas Grabs
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package feedback
 */

require_once("../../config.php");
require_once("lib.php");

$id = required_param('id', PARAM_INT);

$PAGE->set_url('/mod/feedback/print.php', array('id'=>$id));

if (! $cm = get_coursemodule_from_id('feedback', $id)) {
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

require_capability('mod/feedback:view', $context);
$PAGE->set_pagelayout('embedded');

/// Print the page header
$strfeedbacks = get_string("modulenameplural", "feedback");
$strfeedback  = get_string("modulename", "feedback");

$PAGE->navbar->add($strfeedbacks, new moodle_url('/mod/feedback/index.php', array('id'=>$course->id)));
$PAGE->navbar->add(format_string($feedback->name));

$PAGE->set_title(format_string($feedback->name));
$PAGE->set_heading(format_string($course->fullname));
echo $OUTPUT->header();

/// Print the main part of the page
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
echo $OUTPUT->heading(format_text($feedback->name));

$feedbackitems = $DB->get_records('feedback_item', array('feedback'=>$feedback->id), 'position');
echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthwide');
echo $OUTPUT->continue_button('view.php?id='.$id);
if(is_array($feedbackitems)){
    $itemnr = 0;
    $align = right_to_left() ? 'right' : 'left';

    echo $OUTPUT->box_start('feedback_items printview');
    //check, if there exists required-elements
    $countreq = $DB->count_records('feedback_item', array('feedback'=>$feedback->id, 'required'=>1));
    if($countreq > 0) {
        echo '<span class="feedback_required_mark">(*)' . get_string('items_are_required', 'feedback') . '</span>';
    }
    //print the inserted items
    $itempos = 0;
    foreach($feedbackitems as $feedbackitem){
        echo $OUTPUT->box_start('feedback_item_box_'.$align);
            $itempos++;
            //Items without value only are labels
            if($feedbackitem->hasvalue == 1 AND $feedback->autonumbering) {
                $itemnr++;
                    echo $OUTPUT->box_start('feedback_item_number_'.$align);
                    echo $itemnr;
                    echo $OUTPUT->box_end();
            }
            echo $OUTPUT->box_start('box generalbox boxalign_'.$align);
            if($feedbackitem->typ != 'pagebreak') {
                feedback_print_item_complete($feedbackitem, false, false);
            }else {
                echo $OUTPUT->box_start('feedback_pagebreak');
                echo '<hr class="feedback_pagebreak" />';
                echo $OUTPUT->box_end();
            }
            echo $OUTPUT->box_end();
        echo $OUTPUT->box_end();
    }
    echo $OUTPUT->box_end();
}else{
    echo $OUTPUT->box(get_string('no_items_available_yet','feedback'),'generalbox boxaligncenter boxwidthwide');
}
echo $OUTPUT->continue_button('view.php?id='.$id);
echo $OUTPUT->box_end();
/// Finish the page
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////

echo $OUTPUT->footer();

