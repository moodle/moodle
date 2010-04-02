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

$formdata = data_submitted();

if ($id) {
    if (! $cm = get_coursemodule_from_id('feedback', $id)) {
        print_error('invalidcoursemodule');
    }

    if (! $course = $DB->get_record("course", array("id"=>$cm->course))) {
        print_error('coursemisconf');
    }

    if (! $feedback = $DB->get_record("feedback", array("id"=>$cm->instance))) {
        print_error('invalidcoursemodule');
    }
}

if (!$context = get_context_instance(CONTEXT_MODULE, $cm->id)) {
        print_error('badcontext');
}

require_login($course->id, true, $cm);

if(!has_capability('mod/feedback:edititems', $context)){
    print_error('error');
}

/// Print the page header
$strfeedbacks = get_string("modulenameplural", "feedback");
$strfeedback  = get_string("modulename", "feedback");

$PAGE->navbar->add($strfeedbacks, new moodle_url('/mod/feedback/index.php', array('id'=>$course->id)));
$PAGE->navbar->add(format_string($feedback->name));

$PAGE->set_title(format_string($feedback->name));
echo $OUTPUT->header();

/// Print the main part of the page
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
echo $OUTPUT->heading(format_text($feedback->name));

feedback_print_errors();

$feedbackitems = $DB->get_records('feedback_item', array('feedback'=>$feedback->id), 'position');
if(is_array($feedbackitems)){
    $itemnr = 0;

    echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthwide');
    echo '<div class="mdl-align printview"><table>';
    //print the inserted items
    $itempos = 0;
    foreach($feedbackitems as $feedbackitem){
        $itempos++;
        echo '<tr>';
        //Items without value only are labels
        if($feedbackitem->hasvalue == 1 AND $feedback->autonumbering) {
            $itemnr++;
            echo '<td valign="top">' . $itemnr . '.&nbsp;</td>';
        } else {
            echo '<td>&nbsp;</td>';
        }
        if($feedbackitem->typ != 'pagebreak') {
            feedback_print_item($feedbackitem, false, false, true);
        }else {
            echo '<td class="feedback_print_pagebreak" colspan="2">&nbsp;</td>';
        }
        echo '</tr>';
    }
    echo '</table>';
    echo '<font color="red">(*)' . get_string('items_are_required', 'feedback') . '</font>';
    echo '</div>';
    echo $OUTPUT->box_end();
}else{
    echo $OUTPUT->box(get_string('no_items_available_yet','feedback'),'generalbox boxaligncenter boxwidthwide');
}
echo $OUTPUT->continue_button('view.php?id='.$id);
/// Finish the page
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////

echo $OUTPUT->footer();

