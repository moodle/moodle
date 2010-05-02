<?php

/**
 * print the single entries
 *
 * @author Andreas Grabs
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package feedback
 */

require_once("../../config.php");
require_once("lib.php");

////////////////////////////////////////////////////////
//get the params
////////////////////////////////////////////////////////
$id = required_param('id', PARAM_INT);
$userid = optional_param('userid', false, PARAM_INT);
$do_show = required_param('do_show', PARAM_ALPHA);
// $SESSION->feedback->current_tab = $do_show;
$current_tab = $do_show;

////////////////////////////////////////////////////////
//get the objects
////////////////////////////////////////////////////////

if($userid) {
    $formdata->userid = intval($userid);
}

if (! $cm = get_coursemodule_from_id('feedback', $id)) {
    print_error('invalidcoursemodule');
}

if (! $course = $DB->get_record("course", array("id"=>$cm->course))) {
    print_error('coursemisconf');
}

if (! $feedback = $DB->get_record("feedback", array("id"=>$cm->instance))) {
    print_error('invalidcoursemodule');
}

$url = new moodle_url('/mod/feedback/show_entries.php', array('id'=>$cm->id, 'do_show'=>$do_show));

$PAGE->set_url($url);

if (!$context = get_context_instance(CONTEXT_MODULE, $cm->id)) {
        print_error('badcontext');
}

require_login($course->id, true, $cm);

if(($formdata = data_submitted()) AND !confirm_sesskey()) {
    print_error('invalidsesskey');
}

require_capability('mod/feedback:viewreports', $context);

////////////////////////////////////////////////////////
//get the responses of given user
////////////////////////////////////////////////////////
if($do_show == 'showoneentry') {
    //get the feedbackitems
    $feedbackitems = $DB->get_records('feedback_item', array('feedback'=>$feedback->id), 'position');
    $feedbackcompleted = $DB->get_record('feedback_completed', array('feedback'=>$feedback->id, 'userid'=>$userid, 'anonymous_response'=>FEEDBACK_ANONYMOUS_NO)); //arb
}

/// Print the page header
$strfeedbacks = get_string("modulenameplural", "feedback");
$strfeedback  = get_string("modulename", "feedback");

$PAGE->navbar->add(get_string('show_entries','feedback'));
$PAGE->set_title(format_string($feedback->name));
echo $OUTPUT->header();

include('tabs.php');

/// Print the main part of the page
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////

////////////////////////////////////////////////////////
/// Print the links to get responses and analysis
////////////////////////////////////////////////////////
if($do_show == 'showentries'){
    //print the link to analysis
    if(has_capability('mod/feedback:viewreports', $context)) {
        //get the effective groupmode of this course and module
        if (isset($cm->groupmode) && empty($course->groupmodeforce)) {
            $groupmode =  $cm->groupmode;
        } else {
            $groupmode = $course->groupmode;
        }
        
        // $groupselect = groups_print_activity_menu($cm, $CFG->wwwroot . '/mod/feedback/show_entries.php?id=' . $cm->id.'&do_show=showentries', true);
        $groupselect = groups_print_activity_menu($cm, $url->out(), true);
        $mygroupid = groups_get_activity_group($cm);

        //get students in conjunction with groupmode
        if($groupmode > 0) {

            if($mygroupid > 0) {
                $students = feedback_get_complete_users($cm, $mygroupid);
            } else {
                $students = feedback_get_complete_users($cm);
            }
        }else {
            $students = feedback_get_complete_users($cm);
        }

        $completedFeedbackCount = feedback_get_completeds_group_count($feedback, $mygroupid);
        if($feedback->course == SITEID){
            $analysisurl = new moodle_url('/mod/feedback/analysis_course.php', array('id'=>$id, 'courseid'=>$courseid));
            echo $OUTPUT->box_start('mdl-align');
            echo '<a href="'.$analysisurl->out().'">'.get_string('course').' '.get_string('analysis', 'feedback').' ('.get_string('completed_feedbacks', 'feedback').': '.intval($completedFeedbackCount).')</a>';
            echo $OUTPUT->help_icon('viewcompleted', 'feedback');
            echo $OUTPUT->box_end();
        }else {
            $analysisurl = new moodle_url('/mod/feedback/analysis.php', array('id'=>$id, 'courseid'=>$courseid));
            echo $OUTPUT->box_start('mdl-align');
            echo '<a href="'.$analysisurl->out().'">'.get_string('analysis', 'feedback').' ('.get_string('completed_feedbacks', 'feedback').': '.intval($completedFeedbackCount).')</a>';
            echo $OUTPUT->box_end();
        }
    }

    //####### viewreports-start
    if(has_capability('mod/feedback:viewreports', $context)) {
        //print the list of students
        echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthwide');
        echo isset($groupselect) ? $groupselect : '';
        echo '<div class="clearer"></div>';
        echo $OUTPUT->box_start('mdl-align');
        echo '<table><tr><td width="400">';
        if (!$students) {
            if($courseid != SITEID){
                echo $OUTPUT->notification(get_string('noexistingstudents'));
            }
        } else{
            echo print_string('non_anonymous_entries', 'feedback');
            echo ' ('.count($students).')<hr />';

            foreach ($students as $student){
                $completedCount = $DB->count_records('feedback_completed', array('userid'=>$student->id, 'feedback'=>$feedback->id, 'anonymous_response'=>FEEDBACK_ANONYMOUS_NO));
                if($completedCount > 0) {
                 // Are we assuming that there is only one response per user? Should westep through a feedbackcompleteds? I added the addition anonymous check to the select so that only non-anonymous submissions are retrieved.
                    $feedbackcompleted = $DB->get_record('feedback_completed', array('feedback'=>$feedback->id, ' userid'=>$student->id, 'anonymous_response'=>FEEDBACK_ANONYMOUS_NO));
                ?>
                    <table width="100%">
                        <tr>
                            <td align="left">
                                <?php echo $OUTPUT->user_picture($student, array('courseid'=>$course->id));?>
                            </td>
                            <td align="left">
                                <?php echo fullname($student);?>
                            </td>
                            <td align="right">
                            <?php
                                $aurl = new moodle_url($url, array('sesskey'=>sesskey(), 'userid'=>$student->id, 'do_show'=>'showoneentry'));
                                echo $OUTPUT->single_button($aurl, get_string('show_entries', 'feedback'));
                            ?>
                            </td>
                <?php
                    if(has_capability('mod/feedback:deletesubmissions', $context)) {
                ?>
                            <td align="right">
                            <?php
                                $aurl = new moodle_url($CFG->wwwroot.'/mod/feedback/delete_completed.php', array('sesskey'=>sesskey(), 'id'=>$cm->id, 'completedid'=>$feedbackcompleted->id, 'do_show'=>'showoneentry'));
                                echo $OUTPUT->single_button($aurl, get_string('delete_entry', 'feedback'));
                            ?>
                            </td>
                <?php
                    }
                ?>
                        </tr>
                    </table>
                <?php
                }
            }
        }
?>
        <hr />
        <table width="100%">
            <tr>
                <td align="left" colspan="2">
                    <?php print_string('anonymous_entries', 'feedback');?>&nbsp;(<?php echo $DB->count_records('feedback_completed', array('feedback'=>$feedback->id, 'anonymous_response'=>FEEDBACK_ANONYMOUS_YES));?>)
                </td>
                <td align="right">
                    <?php
                        $aurl = new moodle_url('show_entries_anonym.php', array('sesskey'=>sesskey(), 'userid'=>0, 'do_show'=>'showoneentry', 'id'=>$id));
                        echo $OUTPUT->single_button($aurl, get_string('show_entries', 'feedback'));
                    ?>
                </td>
            </tr>
        </table>
<?php
        echo '</td></tr></table>';
        echo $OUTPUT->box_end();
        echo $OUTPUT->box_end();
    }

}
////////////////////////////////////////////////////////
/// Print the responses of the given user
////////////////////////////////////////////////////////
if($do_show == 'showoneentry') {
    echo $OUTPUT->heading(format_text($feedback->name));

    //print the items
    if(is_array($feedbackitems)){
        $align = right_to_left() ? 'right' : 'left';
        $usr = $DB->get_record('user', array('id'=>$userid));
        echo $OUTPUT->box_start('feedback_info');
        if($feedbackcompleted) {
            echo UserDate($feedbackcompleted->timemodified).'<br />('.fullname($usr).')';
        } else {
            echo get_string('not_completed_yet','feedback');
        }
        echo $OUTPUT->box_end();
        
        echo $OUTPUT->box_start('feedback_items');
        $itemnr = 0;
        foreach($feedbackitems as $feedbackitem){
            //get the values
            $value = $DB->get_record('feedback_value', array('completed'=>$feedbackcompleted->id, 'item'=>$feedbackitem->id));
            echo $OUTPUT->box_start('feedback_item_box_'.$align);
            if($feedbackitem->hasvalue == 1 AND $feedback->autonumbering) {
                $itemnr++;
                echo $OUTPUT->box_start('feedback_item_number_'.$align);
                echo $itemnr;
                echo $OUTPUT->box_end();
            }

            if($feedbackitem->typ != 'pagebreak') {
                echo $OUTPUT->box_start('box generalbox boxalign_'.$align);
                if(isset($value->value)) {
                    feedback_print_item_show_value($feedbackitem, $value->value);
                }else {
                    feedback_print_item_show_value($feedbackitem, false);
                }
                echo $OUTPUT->box_end();
            }
            echo $OUTPUT->box_end();
        }
        echo $OUTPUT->box_end();
    }
    echo $OUTPUT->continue_button(new moodle_url($url, array('do_show'=>'showentries')));
}
/// Finish the page
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////

echo $OUTPUT->footer();

?>