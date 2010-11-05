<?php

/**
 * the first page to view the feedback
 *
 * @author Andreas Grabs
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package feedback
 */
require_once("../../config.php");
require_once("lib.php");

$id = required_param('id', PARAM_INT);
$courseid = optional_param('courseid', false, PARAM_INT);

// $SESSION->feedback->current_tab = 'view';
$current_tab = 'view';

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

$feedback_complete_cap = false;

if(has_capability('mod/feedback:complete', $context)) {
    $feedback_complete_cap = true;
}

if(isset($CFG->feedback_allowfullanonymous)
            AND $CFG->feedback_allowfullanonymous
            AND $course->id == SITEID
            AND (!$courseid OR $courseid == SITEID)
            AND $feedback->anonymous == FEEDBACK_ANONYMOUS_YES ) {
    $feedback_complete_cap = true;
}

//check whether the feedback is located and! started from the mainsite
if($course->id == SITEID AND !$courseid) {
    $courseid = SITEID;
}

//check whether the feedback is mapped to the given courseid
if($course->id == SITEID AND !has_capability('mod/feedback:edititems', $context)) {
    if($DB->get_records('feedback_sitecourse_map', array('feedbackid'=>$feedback->id))) {
        if(!$DB->get_record('feedback_sitecourse_map', array('feedbackid'=>$feedback->id, 'courseid'=>$courseid))){
            print_error('invalidcoursemodule');
        }
    }
}

if($feedback->anonymous != FEEDBACK_ANONYMOUS_YES) {
    if($course->id == SITEID) {
        require_login($course->id, true);
    }else {
        require_login($course->id, true, $cm);
    }
} else {
    if($course->id == SITEID) {
        require_course_login($course, true);
    }else {
        require_course_login($course, true, $cm);
    }
}

//check whether the given courseid exists
if($courseid AND $courseid != SITEID) {
    if($course2 = $DB->get_record('course', array('id'=>$courseid))){
        require_course_login($course2); //this overwrites the object $course :-(
        $course = $DB->get_record("course", array("id"=>$cm->course)); // the workaround
    }else {
        print_error('invalidcourseid');
    }
}

if($feedback->anonymous == FEEDBACK_ANONYMOUS_NO) {
    add_to_log($course->id, 'feedback', 'view', 'view.php?id='.$cm->id, $feedback->id,$cm->id);
}

/// Print the page header
$strfeedbacks = get_string("modulenameplural", "feedback");
$strfeedback  = get_string("modulename", "feedback");

if($course->id == SITEID) {
    $PAGE->set_context($context);
    $PAGE->set_cm($cm, $course); // set's up global $COURSE
    $PAGE->set_pagelayout('incourse');
}
$PAGE->set_url('/mod/feedback/view.php', array('id'=>$cm->id, 'do_show'=>'view'));
$PAGE->set_title(format_string($feedback->name));
$PAGE->set_heading(format_string($course->fullname));
echo $OUTPUT->header();

//ishidden check.
//feedback in courses
if ((empty($cm->visible) and !has_capability('moodle/course:viewhiddenactivities', $context)) AND $course->id != SITEID) {
    notice(get_string("activityiscurrentlyhidden"));
}

//ishidden check.
//feedback on mainsite
if ((empty($cm->visible) and !has_capability('moodle/course:viewhiddenactivities', $context)) AND $courseid == SITEID) {
    notice(get_string("activityiscurrentlyhidden"));
}

/// Print the main part of the page
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////

/// print the tabs
include('tabs.php');

$previewimg = $OUTPUT->pix_icon('t/preview', get_string('preview'));
$previewlnk = '<a href="'.$CFG->wwwroot.'/mod/feedback/print.php?id='.$id.'">'.$previewimg.'</a>';

echo $OUTPUT->heading(format_text($feedback->name.' '.$previewlnk));

//show some infos to the feedback
if(has_capability('mod/feedback:edititems', $context)) {
    //get the groupid
    $groupselect = groups_print_activity_menu($cm, $CFG->wwwroot . '/mod/feedback/view.php?id=' . $cm->id, true);
    $mygroupid = groups_get_activity_group($cm);

    echo $OUTPUT->box_start('boxaligncenter boxwidthwide');
    echo $groupselect.'<div class="clearer">&nbsp;</div>';
    $completedscount = feedback_get_completeds_group_count($feedback, $mygroupid);
    echo $OUTPUT->box_start('feedback_info');
    echo '<span class="feedback_info">'.get_string('completed_feedbacks', 'feedback').': </span><span class="feedback_info_value">'.$completedscount. '</span>';
    echo $OUTPUT->box_end();
    $itemscount = $DB->count_records('feedback_item', array('feedback'=>$feedback->id, 'hasvalue'=>1));
    echo $OUTPUT->box_start('feedback_info');
    echo '<span class="feedback_info">'.get_string('questions', 'feedback').': </span><span class="feedback_info_value">' .$itemscount. '</span>';
    echo $OUTPUT->box_end();

    if($feedback->timeopen) {
        echo $OUTPUT->box_start('feedback_info');
        echo '<span class="feedback_info">'.get_string('feedbackopen', 'feedback').': </span><span class="feedback_info_value">' .UserDate($feedback->timeopen). '</span>';
        echo $OUTPUT->box_end();
    }
    if($feedback->timeclose) {
        echo $OUTPUT->box_start('feedback_info');
        echo '<span class="feedback_info">'.get_string('timeclose', 'feedback').': </span><span class="feedback_info_value">' .UserDate($feedback->timeclose). '</span>';
        echo $OUTPUT->box_end();
    }
    echo $OUTPUT->box_end();
}

if(has_capability('mod/feedback:edititems', $context)) {
    echo $OUTPUT->heading(get_string('description', 'feedback'), 4);
}
echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthwide');
$options = (object)array('noclean'=>true);
echo format_module_intro('feedback', $feedback, $cm->id);
echo $OUTPUT->box_end();

if(has_capability('mod/feedback:edititems', $context)) {
    echo $OUTPUT->heading(get_string("page_after_submit", "feedback"), 4);
    echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthwide');
    echo format_text($feedback->page_after_submit, $feedback->page_after_submitformat, array('overflowdiv'=>true));
    echo $OUTPUT->box_end();
}

if( (intval($feedback->publish_stats) == 1) AND
                ( has_capability('mod/feedback:viewanalysepage', $context)) AND
                !( has_capability('mod/feedback:viewreports', $context)) ) {
    if($multiple_count = $DB->count_records('feedback_tracking', array('userid'=>$USER->id, 'feedback'=>$feedback->id))) {
        $analysisurl = new moodle_url('/mod/feedback/analysis.php', array('id'=>$id, 'courseid'=>$courseid));
        echo '<div class="mdl-align"><a href="'.$analysisurl->out().'">';
        echo get_string('completed_feedbacks', 'feedback').'</a>';
        echo '</div>';
    }
}
// echo '<p>';

//####### mapcourse-start
if(has_capability('mod/feedback:mapcourse', $context)) {
    if($feedback->course == SITEID) {
        echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthwide');
        echo '<div class="mdl-align">';
        echo '<form action="mapcourse.php" method="get">';
        echo '<fieldset>';
        echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
        echo '<input type="hidden" name="id" value="'.$id.'" />';
        echo '<button type="submit">'.get_string('mapcourses', 'feedback').'</button>';
        echo $OUTPUT->help_icon('mapcourse', 'feedback');
        echo '</fieldset>';
        echo '</form>';
        echo '<br />';
        echo '</div>';
        echo $OUTPUT->box_end();
    }
}
//####### mapcourse-end

//####### completed-start
if($feedback_complete_cap) {
    echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthwide');
    //check, whether the feedback is open (timeopen, timeclose)
    $checktime = time();
    if(($feedback->timeopen > $checktime) OR ($feedback->timeclose < $checktime AND $feedback->timeclose > 0)) {
            echo '<h2><font color="red">'.get_string('feedback_is_not_open', 'feedback').'</font></h2>';
            echo $OUTPUT->continue_button($CFG->wwwroot.'/course/view.php?id='.$course->id);
        echo $OUTPUT->box_end();
        echo $OUTPUT->footer();
        exit;
    }

    //check multiple Submit
    $feedback_can_submit = true;
    if($feedback->multiple_submit == 0 ) {
        if(feedback_is_already_submitted($feedback->id, $courseid)) {
            $feedback_can_submit = false;
        }
    }
    if($feedback_can_submit) {
        //if the user is not known so we cannot save the values temporarly
        if(!isloggedin() or isguestuser()) {
            $completefile = 'complete_guest.php';
            $guestid = sesskey();
        }else {
            $completefile = 'complete.php';
            $guestid = false;
        }
        $completeurl = new moodle_url('/mod/feedback/'.$completefile, array('id'=>$id, 'courseid'=>$courseid, 'gopage'=>0));

        if($feedbackcompletedtmp = feedback_get_current_completed($feedback->id, true, $courseid, $guestid)) {
            if($startpage = feedback_get_page_to_continue($feedback->id, $courseid, $guestid)) {
                $completeurl->param('gopage', $startpage);
            }
            echo '<a href="'.$completeurl->out().'">'.get_string('continue_the_form', 'feedback').'</a>';
        }else {
            echo '<a href="'.$completeurl->out().'">'.get_string('complete_the_form', 'feedback').'</a>';
        }
    }else {
        echo '<h2><font color="red">'.get_string('this_feedback_is_already_submitted', 'feedback').'</font></h2>';
        if($courseid) {
            echo $OUTPUT->continue_button($CFG->wwwroot.'/course/view.php?id='.$courseid);
        }else {
            echo $OUTPUT->continue_button($CFG->wwwroot.'/course/view.php?id='.$course->id);
        }
    }
    echo $OUTPUT->box_end();
}
//####### completed-end
// echo "</p>";

/// Finish the page
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////

echo $OUTPUT->footer();

