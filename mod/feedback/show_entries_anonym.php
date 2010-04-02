<?php

/**
 * print the single-values of anonymous completeds
 *
 * @author Andreas Grabs
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package feedback
 */

require_once("../../config.php");
require_once("lib.php");

// $SESSION->feedback->current_tab = 'showoneentry';
$current_tab = 'showentries';

$id = required_param('id', PARAM_INT);
$userid = optional_param('userid', false, PARAM_INT);

$url = new moodle_url('/mod/feedback/show_entries_anonym.php', array('id'=>$id));
if ($userid !== '') {
    $url->param('userid', $userid);
}
$PAGE->set_url($url);

if(($formdata = data_submitted()) AND !confirm_sesskey()) {
    print_error('invalidsesskey');
}

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

if(!has_capability('mod/feedback:viewreports', $context)){
    print_error('error');
}


//get the completeds
// if a new anonymous record has not been assigned a random response number
if ($feedbackcompleteds = $DB->get_records('feedback_completed', array('feedback'=>$feedback->id, 'random_response'=>0, 'anonymous_response'=>FEEDBACK_ANONYMOUS_YES), 'random_response')){ //arb
    //then get all of the anonymous records and go through them
    $feedbackcompleteds = $DB->get_records('feedback_completed', array('feedback'=>$feedback->id, 'anonymous_response'=>FEEDBACK_ANONYMOUS_YES), 'id'); //arb
    shuffle($feedbackcompleteds);
    $num = 1;
    foreach($feedbackcompleteds as $compl){
        $compl->random_response = $num;
        $DB->update_record('feedback_completed', $compl);
        $num++;
    }
}
$feedbackcompleteds = $DB->get_records('feedback_completed', array('feedback'=>$feedback->id, 'anonymous_response'=>FEEDBACK_ANONYMOUS_YES), 'random_response'); //arb

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
include('tabs.php');

echo $OUTPUT->heading(format_text($feedback->name));

$continueurl = new moodle_url('/mod/feedback/show_entries.php', array('id'=>$id, 'do_show'=>'showentries'));
echo $OUTPUT->continue_button($continueurl);
//print the list with anonymous completeds
echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthwide');
$PAGE->requires->js('/mod/feedback/feedback.js');
?>
<div class="mdl-align">
<form name="frm" action="<?php echo me();?>" method="post">
    <table>
        <tr>
            <td>
                <input type="hidden" name="sesskey" value="<?php echo sesskey() ?>" />
                <select name="completedid" size="<?php echo (sizeof($feedbackcompleteds)>10)?10:5;?>">
<?php
                if(is_array($feedbackcompleteds)) {
                    $num = 1;
                    foreach($feedbackcompleteds as $compl) {
                        $selected = (isset($formdata->completedid) AND $formdata->completedid == $compl->id)?'selected="selected"':'';
                        echo '<option value="'.$compl->id.'" '. $selected .'>'.get_string('response_nr', 'feedback').': '. $compl->random_response. '</option>';//arb
                        $num++;
                    }
                }
?>
                </select>
                <input type="hidden" name="showanonym" value="<?php echo FEEDBACK_ANONYMOUS_YES;?>" />
                <input type="hidden" name="id" value="<?php echo $id;?>" />
            </td>
            <td valign="top">
                <button type="submit"><?php print_string('show_entry', 'feedback');?></button><br />
                <button type="button" onclick="feedbackGo2delete(this.form);"><?php print_string('delete_entry', 'feedback');?></button>
            </td>
        </tr>
    </table>
</form>
</div>
<?php
echo $OUTPUT->box_end();
if(!isset($formdata->completedid)) {
    $formdata = null;
}
//print the items
if(isset($formdata->showanonym) && $formdata->showanonym == FEEDBACK_ANONYMOUS_YES) {
    //get the feedbackitems
    $feedbackitems = $DB->get_records('feedback_item', array('feedback'=>$feedback->id), 'position');
    $feedbackcompleted = $DB->get_record('feedback_completed', array('id'=>$formdata->completedid));
    if(is_array($feedbackitems)){
        if($feedbackcompleted) {
            echo '<p align="center">'.get_string('chosen_feedback_response', 'feedback').'<br />('.get_string('anonymous', 'feedback').')</p>';//arb
        } else {
            echo '<p align="center">'.get_string('not_completed_yet','feedback').'</p>';
        }
        echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthnormal');
        echo '<form>';
        echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
        echo '<table width="100%">';
        $itemnr = 0;
        foreach($feedbackitems as $feedbackitem){
            //get the values
            $value = $DB->get_record('feedback_value', array('completed'=>$feedbackcompleted->id, 'item'=>$feedbackitem->id));
            echo '<tr>';
            if($feedbackitem->hasvalue == 1 AND $feedback->autonumbering) {
                $itemnr++;
                echo '<td valign="top">' . $itemnr . '.&nbsp;</td>';
            } else {
                echo '<td>&nbsp;</td>';
            }
            if($feedbackitem->typ != 'pagebreak') {
                $itemvalue = isset($value->value) ? $value->value : false;
                feedback_print_item($feedbackitem, $itemvalue, true);
            }else {
                echo '<td colspan="2"><hr /></td>';
            }
            echo '</tr>';
        }
        echo '<tr><td colspan="2" align="center">';
        echo '</td></tr>';
        echo '</table>';
        echo '</form>';
        echo $OUTPUT->box_end();
    }
}
/// Finish the page
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////

echo $OUTPUT->footer();

?>