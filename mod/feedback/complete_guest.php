<?php

    /**
    * prints the form so an anonymous user can fill out the feedback on the mainsite
    *
    * @author Andreas Grabs
    * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
    * @package feedback
    */

    require_once("../../config.php");
    require_once("lib.php");

    feedback_init_feedback_session();

    $id = required_param('id', PARAM_INT);
    $completedid = optional_param('completedid', false, PARAM_INT);
    $preservevalues  = optional_param('preservevalues', 0,  PARAM_INT);
    $courseid = optional_param('courseid', false, PARAM_INT);
    $gopage = optional_param('gopage', -1, PARAM_INT);
    $lastpage = optional_param('lastpage', false, PARAM_INT);
    $startitempos = optional_param('startitempos', 0, PARAM_INT);
    $lastitempos = optional_param('lastitempos', 0, PARAM_INT);

    $url = new moodle_url('/mod/feedback/complete_guest.php', array('id'=>$id));
    if ($completedid !== false) {
        $url->param('completedid', $completedid);
    }
    if ($preservevalues !== 0) {
        $url->param('preservevalues', $preservevalues);
    }
    if ($courseid !== false) {
        $url->param('courseid', $courseid);
    }
    if ($gopage !== -1) {
        $url->param('gopage', $gopage);
    }
    if ($lastpage !== false) {
        $url->param('lastpage', $lastpage);
    }
    if ($startitempos !== 0) {
        $url->param('startitempos', $startitempos);
    }
    if ($lastitempos !== 0) {
        $url->param('lastitempos', $lastitempos);
    }
    $PAGE->set_url($url);

    $highlightrequired = false;

    if(($formdata = data_submitted()) AND !confirm_sesskey()) {
        print_error('invalidsesskey');
    }

    //if the use hit enter into a textfield so the form should not submit
    if(isset($formdata->sesskey) AND !isset($formdata->savevalues) AND !isset($formdata->gonextpage) AND !isset($formdata->gopreviouspage)) {
        $gopage = $formdata->lastpage;
    }
    if(isset($formdata->savevalues)) {
        $savevalues = true;
    }else {
        $savevalues = false;
    }

    if($gopage < 0 AND !$savevalues) {
        if(isset($formdata->gonextpage)){
            $gopage = $lastpage + 1;
            $gonextpage = true;
            $gopreviouspage = false;
        }else if(isset($formdata->gopreviouspage)){
            $gopage = $lastpage - 1;
            $gonextpage = false;
            $gopreviouspage = true;
        }else {
            print_error('parameters_missing', 'feedback');
        }
    }else {
        $gonextpage = $gopreviouspage = false;
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

    if (!$context = get_context_instance(CONTEXT_MODULE, $cm->id)) {
            print_error('badcontext');
    }

    $feedback_complete_cap = false;

    if(isset($CFG->feedback_allowfullanonymous)
                AND $CFG->feedback_allowfullanonymous
                AND $course->id == SITEID
                AND (!$courseid OR $courseid == SITEID)
                AND $feedback->anonymous == FEEDBACK_ANONYMOUS_YES ) {
        $feedback_complete_cap = true;
    }

    //check whether the feedback is anonymous
    if(isset($CFG->feedback_allowfullanonymous)
                    AND $CFG->feedback_allowfullanonymous
                    AND $feedback->anonymous == FEEDBACK_ANONYMOUS_YES
                    AND $course->id == SITEID ) {
        $feedback_complete_cap = true;
    }
    if($feedback->anonymous != FEEDBACK_ANONYMOUS_YES) {
        print_error('feedback_is_not_for_anonymous', 'feedback');
    }

    //check whether the user has a session
    // there used to be a sesskey test - this could not work - sorry

    //check whether the feedback is located and! started from the mainsite
    if($course->id == SITEID AND !$courseid) {
        $courseid = SITEID;
    }

    require_course_login($course);

    if($courseid AND $courseid != SITEID) {
        $course2 = $DB->get_record('course', array('id'=>$courseid));
        require_course_login($course2); //this overwrites the object $course :-(
        $course = $DB->get_record("course", array("id"=>$cm->course)); // the workaround
    }

    if(!$feedback_complete_cap) {
        print_error('error');
    }


    /// Print the page header
    $strfeedbacks = get_string("modulenameplural", "feedback");
    $strfeedback  = get_string("modulename", "feedback");

    $PAGE->set_cm($cm, $course); // set's up global $COURSE
    $PAGE->set_pagelayout('incourse');

    $PAGE->navbar->add($strfeedbacks, new moodle_url('/mod/feedback/index.php', array('id'=>$course->id)));
    $PAGE->navbar->add(format_string($feedback->name));
    $PAGE->set_heading(format_string($course->fullname));
    $PAGE->set_title(format_string($feedback->name));
    echo $OUTPUT->header();

    //ishidden check. hidden feedbacks except feedbacks on mainsite are only accessible with related capabilities
    if ((empty($cm->visible) AND
            !has_capability('moodle/course:viewhiddenactivities', $context)) AND
            $course->id != SITEID) {
        notice(get_string("activityiscurrentlyhidden"));
    }

    feedback_print_errors();

    //check, if the feedback is open (timeopen, timeclose)
    $checktime = time();
    if(($feedback->timeopen > $checktime) OR ($feedback->timeclose < $checktime AND $feedback->timeclose > 0)) {
        echo $OUTPUT->box_start('generalbox boxaligncenter');
            echo '<h2><font color="red">'.get_string('feedback_is_not_open', 'feedback').'</font></h2>';
            echo $OUTPUT->continue_button($CFG->wwwroot.'/course/view.php?id='.$course->id);
        echo $OUTPUT->box_end();
        echo $OUTPUT->footer();
        exit;
    }

    //additional check for multiple-submit (prevent browsers back-button). the main-check is in view.php
    $feedback_can_submit = true;
    if($feedback->multiple_submit == 0 ) {
        // if($multiple_count = $DB->get_record('feedback_tracking', array('userid'=>$USER->id, 'feedback'=>$feedback->id))) {
        if(feedback_is_already_submitted($feedback->id, $courseid)) {
            $feedback_can_submit = false;
        }
    }
    if($feedback_can_submit) {
        //preserving the items
        if($preservevalues == 1){
            if (!$SESSION->feedback->is_started == true) {
                print_error('error', 'error', $CFG->wwwroot.'/course/view.php?id='.$course->id);
            }
            //check, if all required items have a value
            if(feedback_check_values($startitempos, $lastitempos)) {
                $userid = $USER->id; //arb
                if($completedid = feedback_save_guest_values(sesskey())){
                    add_to_log($course->id, 'feedback', 'startcomplete', 'view.php?id='.$cm->id, $feedback->id); //arb: log even guest submissions or at least the startcomplete since the other add log event is elsewhere

                    if(!$gonextpage AND !$gopreviouspage) $preservevalues = false;//es kann gespeichert werden

                }else {
                    $savereturn = 'failed';
                    if(isset($lastpage)) {
                        $gopage = $lastpage;
                    }else {
                        print_error('parameters_missing', 'feedback');
                    }
                }
            }else {
                $savereturn = 'missing';
                $highlightrequired = true;
                if(isset($lastpage)) {
                    $gopage = $lastpage;
                }else {
                    print_error('parameters_missing', 'feedback');
                }
            }
        }

        //saving the items
        if($savevalues AND !$preservevalues){
            //exists there any pagebreak, so there are values in the feedback_valuetmp
            $userid = $USER->id; //arb changed from 0 to $USER->id - no strict anonymous feedbacks - if it is a guest taking it then I want to know that it was a guest (at least in the data saved in the feedback tables)

            $feedbackcompletedtmp = $DB->get_record('feedback_completedtmp', array('id'=>$completedid));

            //fake saving for switchrole
            $is_switchrole = feedback_check_is_switchrole();
            if($is_switchrole) {
                $savereturn = 'saved';
                feedback_delete_completedtmp($completedid);
            }else if($new_completed_id = feedback_save_tmp_values($feedbackcompletedtmp, false, $userid)) {
                $savereturn = 'saved';
                feedback_send_email_anonym($cm, $feedback, $course, $userid);
                unset($SESSION->feedback->is_started);

            }else {
                $savereturn = 'failed';
            }
        }

        if($allbreaks = feedback_get_all_break_positions($feedback->id)){
            if($gopage <= 0) {
                $startposition = 0;
            }else {
                if(!isset($allbreaks[$gopage - 1])) {
                    $gopage = count($allbreaks);
                }
                $startposition = $allbreaks[$gopage - 1];
            }
            $ispagebreak = true;
        }else {
            $startposition = 0;
            $newpage = 0;
            $ispagebreak = false;
        }

        //get the feedbackitems after the last shown pagebreak
        $feedbackitems = $DB->get_records_select('feedback_item', 'feedback = ? AND position > ?', array($feedback->id, $startposition), 'position');

        //get the first pagebreak
        if($pagebreaks = $DB->get_records('feedback_item', array('feedback'=>$feedback->id, 'typ'=>'pagebreak'), 'position')) {
            $pagebreaks = array_values($pagebreaks);
            $firstpagebreak = $pagebreaks[0];
        }else {
            $firstpagebreak = false;
        }
        $maxitemcount = $DB->count_records('feedback_item', array('feedback'=>$feedback->id));
        $feedbackcompletedtmp = feedback_get_current_completed($feedback->id, true, $courseid, sesskey());

        /// Print the main part of the page
        ///////////////////////////////////////////////////////////////////////////
        ///////////////////////////////////////////////////////////////////////////
        ///////////////////////////////////////////////////////////////////////////
        $analysisurl = new moodle_url('/mod/feedback/analysis.php', array('id'=>$id));
        if($courseid > 0) {
            $analysisurl->param('courseid', $courseid);
        }
        echo $OUTPUT->heading(format_text($feedback->name));

        if( (intval($feedback->publish_stats) == 1) AND
                ( has_capability('mod/feedback:viewanalysepage', $context)) AND
                !( has_capability('mod/feedback:viewreports', $context)) ) {
            echo $OUTPUT->box_start('mdl-align');
            echo '<a href="'.$analysisurl->out().'">'.get_string('completed_feedbacks', 'feedback').'</a>';
            echo $OUTPUT->box_end();
        }

        if(isset($savereturn) && $savereturn == 'saved') {
            if($feedback->page_after_submit) {
                require_once($CFG->libdir . '/filelib.php');

                $page_after_submit_output = file_rewrite_pluginfile_urls($feedback->page_after_submit,
                                                                        'pluginfile.php',
                                                                        $context->id,
                                                                        'mod_feedback',
                                                                        'page_after_submit',
                                                                        0);

                echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthwide');
                echo format_text($page_after_submit_output,
                                 $feedback->page_after_submitformat,
                                 array('overflowdiv' => true));
                echo $OUTPUT->box_end();
            } else {
                echo '<p align="center"><b><font color="green">'.get_string('entries_saved','feedback').'</font></b></p>';
                if( intval($feedback->publish_stats) == 1) {
                    echo '<p align="center"><a href="'.$analysisurl->out().'">';
                    echo get_string('completed_feedbacks', 'feedback').'</a>';
                    echo '</p>';
                }
            }
            if($feedback->site_after_submit) {
                echo $OUTPUT->continue_button(feedback_encode_target_url($feedback->site_after_submit));
            }else {
                if($courseid) {
                    if($courseid == SITEID) {
                        echo $OUTPUT->continue_button($CFG->wwwroot);
                    }else {
                        echo $OUTPUT->continue_button($CFG->wwwroot.'/course/view.php?id='.$courseid);
                    }
                }else {
                    if($course->id == SITEID) {
                        echo $OUTPUT->continue_button($CFG->wwwroot);
                    } else {
                        echo $OUTPUT->continue_button($CFG->wwwroot.'/course/view.php?id='.$course->id);
                    }
                }
            }
        }else {
            if(isset($savereturn) && $savereturn == 'failed') {
                echo $OUTPUT->box_start('mform error');
                echo get_string('saving_failed','feedback');
                echo $OUTPUT->box_end();
            }

            if(isset($savereturn) && $savereturn == 'missing') {
                echo $OUTPUT->box_start('mform error');
                echo get_string('saving_failed_because_missing_or_false_values','feedback');
                echo $OUTPUT->box_end();
            }

            //print the items
            if(is_array($feedbackitems)){
                // echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthwide');
                echo $OUTPUT->box_start('feedback_form');
                echo '<form action="complete_guest.php" method="post" onsubmit=" ">';
                echo '<fieldset>';
                echo '<input type="hidden" name="anonymous" value="0" />';
                echo '<input type="hidden" name="anonymous_response" value="'.FEEDBACK_ANONYMOUS_YES.'" />';
                echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
                //check, if there exists required-elements
                $countreq = $DB->count_records('feedback_item', array('feedback'=>$feedback->id, 'required'=>1));
                if($countreq > 0) {
                    echo '<span class="feedback_required_mark">(*)' . get_string('items_are_required', 'feedback') . '</span>';
                }
                echo $OUTPUT->box_start('feedback_items');

                $startitem = null;
                $itemnr = $DB->count_records_select('feedback_item', 'feedback = ? AND hasvalue = 1 AND position < ?', array($feedback->id, $startposition));
                $lastbreakposition = 0;
                $align = right_to_left() ? 'right' : 'left';

                foreach($feedbackitems as $feedbackitem){
                    if(!isset($startitem)) {
                        //avoid showing double pagebreaks
                        if($feedbackitem->typ == 'pagebreak') {
                            continue;
                        }
                        $startitem = $feedbackitem;
                    }

                    if($feedbackitem->dependitem > 0) {
                        //chech if the conditions are ok
                        if(!isset($feedbackcompletedtmp->id) OR !feedback_compare_item_value($feedbackcompletedtmp->id, $feedbackitem->dependitem, $feedbackitem->dependvalue, true)) {
                            $lastitem = $feedbackitem;
                            $lastbreakposition = $feedbackitem->position;
                            continue;
                        }
                    }

                    if($feedbackitem->dependitem > 0) {
                        $dependstyle = ' feedback_complete_depend';
                    }else {
                        $dependstyle = '';
                    }

                    echo $OUTPUT->box_start('feedback_item_box_'.$align.$dependstyle);
                        $value = '';
                        //get the value
                        $frmvaluename = $feedbackitem->typ . '_'. $feedbackitem->id;
                        if(isset($savereturn)) {
                            $value =  isset($formdata->{$frmvaluename})?$formdata->{$frmvaluename}:NULL;
                        }else {
                            if(isset($feedbackcompletedtmp->id)) {
                                $value = feedback_get_item_value($feedbackcompletedtmp->id, $feedbackitem->id, sesskey());
                            }
                        }
                        if($feedbackitem->hasvalue == 1 AND $feedback->autonumbering) {
                            $itemnr++;
                            echo $OUTPUT->box_start('feedback_item_number_'.$align);
                            echo $itemnr;
                            echo $OUTPUT->box_end();
                        }
                        if($feedbackitem->typ != 'pagebreak') {
                            echo $OUTPUT->box_start('box generalbox boxalign_'.$align);
                                feedback_print_item_complete($feedbackitem, $value, $highlightrequired);
                            echo $OUTPUT->box_end();
                        }
                    echo $OUTPUT->box_end();

                    $lastbreakposition = $feedbackitem->position; //last item-pos (item or pagebreak)
                    if($feedbackitem->typ == 'pagebreak'){
                        break;
                    }else {
                        $lastitem = $feedbackitem;
                    }
                }
                echo $OUTPUT->box_end();
                echo '<input type="hidden" name="id" value="'.$id.'" />';
                echo '<input type="hidden" name="feedbackid" value="'.$feedback->id.'" />';
                echo '<input type="hidden" name="lastpage" value="'.$gopage.'" />';
                echo '<input type="hidden" name="completedid" value="'.(isset($feedbackcompletedtmp->id)?$feedbackcompletedtmp->id:'').'" />';
                echo '<input type="hidden" name="courseid" value="'. $courseid . '" />';
                echo '<input type="hidden" name="preservevalues" value="1" />';
                if(isset($startitem)) {
                    echo '<input type="hidden" name="startitempos" value="'. $startitem->position . '" />';
                    echo '<input type="hidden" name="lastitempos" value="'. $lastitem->position . '" />';
                }

                if($ispagebreak AND $lastbreakposition > $firstpagebreak->position) {
                    echo '<input name="gopreviouspage" type="submit" value="'.get_string('previous_page','feedback').'" />';
                }
                if($lastbreakposition < $maxitemcount){
                    echo '<input name="gonextpage" type="submit" value="'.get_string('next_page','feedback').'" />';
                }
                if($lastbreakposition >= $maxitemcount) { //last page
                    echo '<input name="savevalues" type="submit" value="'.get_string('save_entries','feedback').'" />';
                }

                echo '</fieldset>';
                echo '</form>';
                echo $OUTPUT->box_end();

                echo $OUTPUT->box_start('feedback_complete_cancel');
                if($courseid) {
                    echo '<form action="'.$CFG->wwwroot.'/course/view.php?id='.$courseid.'" method="post" onsubmit=" ">';
                }else{
                    if($course->id == SITEID) {
                        echo '<form action="'.$CFG->wwwroot.'" method="post" onsubmit=" ">';
                    } else {
                        echo '<form action="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'" method="post" onsubmit=" ">';
                    }
                }
                echo '<fieldset>';
                echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
                echo '<input type="hidden" name="courseid" value="'. $courseid . '" />';
                echo '<button type="submit">'.get_string('cancel').'</button>';
                echo '</fieldset>';
                echo '</form>';
                echo $OUTPUT->box_end();
                $SESSION->feedback->is_started = true;
            }
        }
    }else {
        echo $OUTPUT->box_start('generalbox boxaligncenter');
            echo '<h2><font color="red">'.get_string('this_feedback_is_already_submitted', 'feedback').'</font></h2>';
            echo $OUTPUT->continue_button($CFG->wwwroot.'/course/view.php?id='.$course->id);
        echo $OUTPUT->box_end();
    }
    /// Finish the page
    ///////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////

    echo $OUTPUT->footer();

