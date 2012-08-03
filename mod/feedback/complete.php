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
 * prints the form so the user can fill out the feedback
 *
 * @author Andreas Grabs
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package feedback
 */

require_once("../../config.php");
require_once("lib.php");
require_once($CFG->libdir . '/completionlib.php');

feedback_init_feedback_session();

$id = required_param('id', PARAM_INT);
$completedid = optional_param('completedid', false, PARAM_INT);
$preservevalues  = optional_param('preservevalues', 0,  PARAM_INT);
$courseid = optional_param('courseid', false, PARAM_INT);
$gopage = optional_param('gopage', -1, PARAM_INT);
$lastpage = optional_param('lastpage', false, PARAM_INT);
$startitempos = optional_param('startitempos', 0, PARAM_INT);
$lastitempos = optional_param('lastitempos', 0, PARAM_INT);
$anonymous_response = optional_param('anonymous_response', 0, PARAM_INT); //arb

$highlightrequired = false;

if (($formdata = data_submitted()) AND !confirm_sesskey()) {
    print_error('invalidsesskey');
}

//if the use hit enter into a textfield so the form should not submit
if (isset($formdata->sesskey) AND
    !isset($formdata->savevalues) AND
    !isset($formdata->gonextpage) AND
    !isset($formdata->gopreviouspage)) {

    $gopage = $formdata->lastpage;
}

if (isset($formdata->savevalues)) {
    $savevalues = true;
} else {
    $savevalues = false;
}

if ($gopage < 0 AND !$savevalues) {
    if (isset($formdata->gonextpage)) {
        $gopage = $lastpage + 1;
        $gonextpage = true;
        $gopreviouspage = false;
    } else if (isset($formdata->gopreviouspage)) {
        $gopage = $lastpage - 1;
        $gonextpage = false;
        $gopreviouspage = true;
    } else {
        print_error('missingparameter');
    }
} else {
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

$context = context_module::instance($cm->id);

$feedback_complete_cap = false;

if (has_capability('mod/feedback:complete', $context)) {
    $feedback_complete_cap = true;
}

//check whether the feedback is located and! started from the mainsite
if ($course->id == SITEID AND !$courseid) {
    $courseid = SITEID;
}

//check whether the feedback is mapped to the given courseid
if ($course->id == SITEID AND !has_capability('mod/feedback:edititems', $context)) {
    if ($DB->get_records('feedback_sitecourse_map', array('feedbackid'=>$feedback->id))) {
        $params = array('feedbackid'=>$feedback->id, 'courseid'=>$courseid);
        if (!$DB->get_record('feedback_sitecourse_map', $params)) {
            print_error('notavailable', 'feedback');
        }
    }
}

if ($feedback->anonymous != FEEDBACK_ANONYMOUS_YES) {
    if ($course->id == SITEID) {
        require_login($course, true);
    } else {
        require_login($course, true, $cm);
    }
} else {
    if ($course->id == SITEID) {
        require_course_login($course, true);
    } else {
        require_course_login($course, true, $cm);
    }
}

//check whether the given courseid exists
if ($courseid AND $courseid != SITEID) {
    if ($course2 = $DB->get_record('course', array('id'=>$courseid))) {
        require_course_login($course2); //this overwrites the object $course :-(
        $course = $DB->get_record("course", array("id"=>$cm->course)); // the workaround
    } else {
        print_error('invalidcourseid');
    }
}

if (!$feedback_complete_cap) {
    print_error('error');
}

// Mark activity viewed for completion-tracking
$completion = new completion_info($course);
$completion->set_module_viewed($cm);

/// Print the page header
$strfeedbacks = get_string("modulenameplural", "feedback");
$strfeedback  = get_string("modulename", "feedback");

if ($course->id == SITEID) {
    $PAGE->set_cm($cm, $course); // set's up global $COURSE
    $PAGE->set_pagelayout('incourse');
}

$PAGE->navbar->add(get_string('feedback:complete', 'feedback'));
$urlparams = array('id'=>$cm->id, 'gopage'=>$gopage, 'courseid'=>$course->id);
$PAGE->set_url('/mod/feedback/complete.php', $urlparams);
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_title(format_string($feedback->name));
echo $OUTPUT->header();

//ishidden check.
//feedback in courses
if ((empty($cm->visible) AND
        !has_capability('moodle/course:viewhiddenactivities', $context)) AND
        $course->id != SITEID) {
    notice(get_string("activityiscurrentlyhidden"));
}

//ishidden check.
//feedback on mainsite
if ((empty($cm->visible) AND
        !has_capability('moodle/course:viewhiddenactivities', $context)) AND
        $courseid == SITEID) {
    notice(get_string("activityiscurrentlyhidden"));
}

//check, if the feedback is open (timeopen, timeclose)
$checktime = time();
$feedback_is_closed = ($feedback->timeopen > $checktime) OR
                      ($feedback->timeclose < $checktime AND
                            $feedback->timeclose > 0);

if ($feedback_is_closed) {
    echo $OUTPUT->box_start('generalbox boxaligncenter');
        echo '<h2><font color="red">';
        echo get_string('feedback_is_not_open', 'feedback');
        echo '</font></h2>';
        echo $OUTPUT->continue_button($CFG->wwwroot.'/course/view.php?id='.$course->id);
    echo $OUTPUT->box_end();
    echo $OUTPUT->footer();
    exit;
}

//additional check for multiple-submit (prevent browsers back-button).
//the main-check is in view.php
$feedback_can_submit = true;
if ($feedback->multiple_submit == 0 ) {
    if (feedback_is_already_submitted($feedback->id, $courseid)) {
        $feedback_can_submit = false;
    }
}
if ($feedback_can_submit) {
    //preserving the items
    if ($preservevalues == 1) {
        if (!isset($SESSION->feedback->is_started) OR !$SESSION->feedback->is_started == true) {
            print_error('error', '', $CFG->wwwroot.'/course/view.php?id='.$course->id);
        }
        //checken, ob alle required items einen wert haben
        if (feedback_check_values($startitempos, $lastitempos)) {
            $userid = $USER->id; //arb
            if ($completedid = feedback_save_values($USER->id, true)) {
                if ($userid > 0) {
                    add_to_log($course->id,
                               'feedback',
                               'startcomplete',
                               'view.php?id='.$cm->id,
                               $feedback->id,
                               $cm->id,
                               $userid);
                }
                if (!$gonextpage AND !$gopreviouspage) {
                    $preservevalues = false;//es kann gespeichert werden
                }

            } else {
                $savereturn = 'failed';
                if (isset($lastpage)) {
                    $gopage = $lastpage;
                } else {
                    print_error('missingparameter');
                }
            }
        } else {
            $savereturn = 'missing';
            $highlightrequired = true;
            if (isset($lastpage)) {
                $gopage = $lastpage;
            } else {
                print_error('missingparameter');
            }

        }
    }

    //saving the items
    if ($savevalues AND !$preservevalues) {
        //exists there any pagebreak, so there are values in the feedback_valuetmp
        $userid = $USER->id; //arb

        if ($feedback->anonymous == FEEDBACK_ANONYMOUS_NO) {
            $feedbackcompleted = feedback_get_current_completed($feedback->id, false, $courseid);
        } else {
            $feedbackcompleted = false;
        }
        $params = array('id' => $completedid);
        $feedbackcompletedtmp = $DB->get_record('feedback_completedtmp', $params);
        //fake saving for switchrole
        $is_switchrole = feedback_check_is_switchrole();
        if ($is_switchrole) {
            $savereturn = 'saved';
            feedback_delete_completedtmp($completedid);
        } else {
            $new_completed_id = feedback_save_tmp_values($feedbackcompletedtmp,
                                                         $feedbackcompleted,
                                                         $userid);
            if ($new_completed_id) {
                $savereturn = 'saved';
                if ($feedback->anonymous == FEEDBACK_ANONYMOUS_NO) {
                    add_to_log($course->id,
                              'feedback',
                              'submit',
                              'view.php?id='.$cm->id,
                              $feedback->id,
                              $cm->id,
                              $userid);

                    feedback_send_email($cm, $feedback, $course, $userid);
                } else {
                    feedback_send_email_anonym($cm, $feedback, $course, $userid);
                }
                //tracking the submit
                $tracking = new stdClass();
                $tracking->userid = $USER->id;
                $tracking->feedback = $feedback->id;
                $tracking->completed = $new_completed_id;
                $DB->insert_record('feedback_tracking', $tracking);
                unset($SESSION->feedback->is_started);

                // Update completion state
                $completion = new completion_info($course);
                if ($completion->is_enabled($cm) && $feedback->completionsubmit) {
                    $completion->update_state($cm, COMPLETION_COMPLETE);
                }

            } else {
                $savereturn = 'failed';
            }
        }

    }


    if ($allbreaks = feedback_get_all_break_positions($feedback->id)) {
        if ($gopage <= 0) {
            $startposition = 0;
        } else {
            if (!isset($allbreaks[$gopage - 1])) {
                $gopage = count($allbreaks);
            }
            $startposition = $allbreaks[$gopage - 1];
        }
        $ispagebreak = true;
    } else {
        $startposition = 0;
        $newpage = 0;
        $ispagebreak = false;
    }

    //get the feedbackitems after the last shown pagebreak
    $select = 'feedback = ? AND position > ?';
    $params = array($feedback->id, $startposition);
    $feedbackitems = $DB->get_records_select('feedback_item', $select, $params, 'position');

    //get the first pagebreak
    $params = array('feedback' => $feedback->id, 'typ' => 'pagebreak');
    if ($pagebreaks = $DB->get_records('feedback_item', $params, 'position')) {
        $pagebreaks = array_values($pagebreaks);
        $firstpagebreak = $pagebreaks[0];
    } else {
        $firstpagebreak = false;
    }
    $maxitemcount = $DB->count_records('feedback_item', array('feedback'=>$feedback->id));

    //get the values of completeds before done. Anonymous user can not get these values.
    if ((!isset($SESSION->feedback->is_started)) AND
                          (!isset($savereturn)) AND
                          ($feedback->anonymous == FEEDBACK_ANONYMOUS_NO)) {

        $feedbackcompletedtmp = feedback_get_current_completed($feedback->id, true, $courseid);
        if (!$feedbackcompletedtmp) {
            $feedbackcompleted = feedback_get_current_completed($feedback->id, false, $courseid);
            if ($feedbackcompleted) {
                //copy the values to feedback_valuetmp create a completedtmp
                $feedbackcompletedtmp = feedback_set_tmp_values($feedbackcompleted);
            }
        }
    } else {
        $feedbackcompletedtmp = feedback_get_current_completed($feedback->id, true, $courseid);
    }

    /// Print the main part of the page
    ///////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////
    $analysisurl = new moodle_url('/mod/feedback/analysis.php', array('id'=>$id));
    if ($courseid > 0) {
        $analysisurl->param('courseid', $courseid);
    }
    echo $OUTPUT->heading(format_text($feedback->name));

    if ( (intval($feedback->publish_stats) == 1) AND
            ( has_capability('mod/feedback:viewanalysepage', $context)) AND
            !( has_capability('mod/feedback:viewreports', $context)) ) {

        $params = array('userid' => $USER->id, 'feedback' => $feedback->id);
        if ($multiple_count = $DB->count_records('feedback_tracking', $params)) {
            echo $OUTPUT->box_start('mdl-align');
            echo '<a href="'.$analysisurl->out().'">';
            echo get_string('completed_feedbacks', 'feedback').'</a>';
            echo $OUTPUT->box_end();
        }
    }

    if (isset($savereturn) && $savereturn == 'saved') {
        if ($feedback->page_after_submit) {

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
            echo '<p align="center">';
            echo '<b><font color="green">';
            echo get_string('entries_saved', 'feedback');
            echo '</font></b>';
            echo '</p>';
            if ( intval($feedback->publish_stats) == 1) {
                echo '<p align="center"><a href="'.$analysisurl->out().'">';
                echo get_string('completed_feedbacks', 'feedback').'</a>';
                echo '</p>';
            }
        }

        if ($feedback->site_after_submit) {
            $url = feedback_encode_target_url($feedback->site_after_submit);
        } else {
            if ($courseid) {
                if ($courseid == SITEID) {
                    $url = $CFG->wwwroot;
                } else {
                    $url = $CFG->wwwroot.'/course/view.php?id='.$courseid;
                }
            } else {
                if ($course->id == SITEID) {
                    $url = $CFG->wwwroot;
                } else {
                    $url = $CFG->wwwroot.'/course/view.php?id='.$course->id;
                }
            }
        }
        echo $OUTPUT->continue_button($url);
    } else {
        if (isset($savereturn) && $savereturn == 'failed') {
            echo $OUTPUT->box_start('mform error');
            echo get_string('saving_failed', 'feedback');
            echo $OUTPUT->box_end();
        }

        if (isset($savereturn) && $savereturn == 'missing') {
            echo $OUTPUT->box_start('mform error');
            echo get_string('saving_failed_because_missing_or_false_values', 'feedback');
            echo $OUTPUT->box_end();
        }

        //print the items
        if (is_array($feedbackitems)) {
            echo $OUTPUT->box_start('feedback_form');
            echo '<form action="complete.php" method="post" onsubmit=" ">';
            echo '<fieldset>';
            echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
            echo $OUTPUT->box_start('feedback_anonymousinfo');
            switch ($feedback->anonymous) {
                case FEEDBACK_ANONYMOUS_YES:
                    echo '<input type="hidden" name="anonymous" value="1" />';
                    $inputvalue = 'value="'.FEEDBACK_ANONYMOUS_YES.'"';
                    echo '<input type="hidden" name="anonymous_response" '.$inputvalue.' />';
                    echo get_string('mode', 'feedback').': '.get_string('anonymous', 'feedback');
                    break;
                case FEEDBACK_ANONYMOUS_NO:
                    echo '<input type="hidden" name="anonymous" value="0" />';
                    $inputvalue = 'value="'.FEEDBACK_ANONYMOUS_NO.'"';
                    echo '<input type="hidden" name="anonymous_response" '.$inputvalue.' />';
                    echo get_string('mode', 'feedback').': ';
                    echo get_string('non_anonymous', 'feedback');
                    break;
            }
            echo $OUTPUT->box_end();
            //check, if there exists required-elements
            $params = array('feedback' => $feedback->id, 'required' => 1);
            $countreq = $DB->count_records('feedback_item', $params);
            if ($countreq > 0) {
                echo '<span class="feedback_required_mark">(*)';
                echo get_string('items_are_required', 'feedback');
                echo '</span>';
            }
            echo $OUTPUT->box_start('feedback_items');

            unset($startitem);
            $select = 'feedback = ? AND hasvalue = 1 AND position < ?';
            $params = array($feedback->id, $startposition);
            $itemnr = $DB->count_records_select('feedback_item', $select, $params);
            $lastbreakposition = 0;
            $align = right_to_left() ? 'right' : 'left';

            foreach ($feedbackitems as $feedbackitem) {
                if (!isset($startitem)) {
                    //avoid showing double pagebreaks
                    if ($feedbackitem->typ == 'pagebreak') {
                        continue;
                    }
                    $startitem = $feedbackitem;
                }

                if ($feedbackitem->dependitem > 0) {
                    //chech if the conditions are ok
                    $fb_compare_value = feedback_compare_item_value($feedbackcompletedtmp->id,
                                                                    $feedbackitem->dependitem,
                                                                    $feedbackitem->dependvalue,
                                                                    true);
                    if (!isset($feedbackcompletedtmp->id) OR !$fb_compare_value) {
                        $lastitem = $feedbackitem;
                        $lastbreakposition = $feedbackitem->position;
                        continue;
                    }
                }

                if ($feedbackitem->dependitem > 0) {
                    $dependstyle = ' feedback_complete_depend';
                } else {
                    $dependstyle = '';
                }

                echo $OUTPUT->box_start('feedback_item_box_'.$align.$dependstyle);
                $value = '';
                //get the value
                $frmvaluename = $feedbackitem->typ . '_'. $feedbackitem->id;
                if (isset($savereturn)) {
                    $value = isset($formdata->{$frmvaluename}) ? $formdata->{$frmvaluename} : null;
                    $value = feedback_clean_input_value($feedbackitem, $value);
                } else {
                    if (isset($feedbackcompletedtmp->id)) {
                        $value = feedback_get_item_value($feedbackcompletedtmp->id,
                                                         $feedbackitem->id,
                                                         true);
                    }
                }
                if ($feedbackitem->hasvalue == 1 AND $feedback->autonumbering) {
                    $itemnr++;
                    echo $OUTPUT->box_start('feedback_item_number_'.$align);
                    echo $itemnr;
                    echo $OUTPUT->box_end();
                }
                if ($feedbackitem->typ != 'pagebreak') {
                    echo $OUTPUT->box_start('box generalbox boxalign_'.$align);
                    feedback_print_item_complete($feedbackitem, $value, $highlightrequired);
                    echo $OUTPUT->box_end();
                }

                echo $OUTPUT->box_end();

                $lastbreakposition = $feedbackitem->position; //last item-pos (item or pagebreak)
                if ($feedbackitem->typ == 'pagebreak') {
                    break;
                } else {
                    $lastitem = $feedbackitem;
                }
            }
            echo $OUTPUT->box_end();
            echo '<input type="hidden" name="id" value="'.$id.'" />';
            echo '<input type="hidden" name="feedbackid" value="'.$feedback->id.'" />';
            echo '<input type="hidden" name="lastpage" value="'.$gopage.'" />';
            if (isset($feedbackcompletedtmp->id)) {
                $inputvalue = 'value="'.$feedbackcompletedtmp->id.'"';
            } else {
                $inputvalue = 'value=""';
            }
            echo '<input type="hidden" name="completedid" '.$inputvalue.' />';
            echo '<input type="hidden" name="courseid" value="'. $courseid . '" />';
            echo '<input type="hidden" name="preservevalues" value="1" />';
            if (isset($startitem)) {
                echo '<input type="hidden" name="startitempos" value="'.$startitem->position.'" />';
                echo '<input type="hidden" name="lastitempos" value="'.$lastitem->position.'" />';
            }

            if ( $ispagebreak AND $lastbreakposition > $firstpagebreak->position) {
                $inputvalue = 'value="'.get_string('previous_page', 'feedback').'"';
                echo '<input name="gopreviouspage" type="submit" '.$inputvalue.' />';
            }
            if ($lastbreakposition < $maxitemcount) {
                $inputvalue = 'value="'.get_string('next_page', 'feedback').'"';
                echo '<input name="gonextpage" type="submit" '.$inputvalue.' />';
            }
            if ($lastbreakposition >= $maxitemcount) { //last page
                $inputvalue = 'value="'.get_string('save_entries', 'feedback').'"';
                echo '<input name="savevalues" type="submit" '.$inputvalue.' />';
            }

            echo '</fieldset>';
            echo '</form>';
            echo $OUTPUT->box_end();

            echo $OUTPUT->box_start('feedback_complete_cancel');
            if ($courseid) {
                $action = 'action="'.$CFG->wwwroot.'/course/view.php?id='.$courseid.'"';
            } else {
                if ($course->id == SITEID) {
                    $action = 'action="'.$CFG->wwwroot.'"';
                } else {
                    $action = 'action="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'"';
                }
            }
            echo '<form '.$action.' method="post" onsubmit=" ">';
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
} else {
    echo $OUTPUT->box_start('generalbox boxaligncenter');
        echo '<h2>';
        echo '<font color="red">';
        echo get_string('this_feedback_is_already_submitted', 'feedback');
        echo '</font>';
        echo '</h2>';
        echo $OUTPUT->continue_button($CFG->wwwroot.'/course/view.php?id='.$course->id);
    echo $OUTPUT->box_end();
}
/// Finish the page
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////

echo $OUTPUT->footer();
