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
 * print the single entries
 *
 * @author Andreas Grabs
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package mod_feedback
 */

require_once("../../config.php");
require_once("lib.php");
require_once($CFG->libdir.'/tablelib.php');

////////////////////////////////////////////////////////
//get the params
////////////////////////////////////////////////////////
$id = required_param('id', PARAM_INT);
$userid = optional_param('userid', false, PARAM_INT);
$showcompleted = optional_param('showcompleted', false, PARAM_INT);
$deleteid = optional_param('delete', null, PARAM_INT);

////////////////////////////////////////////////////////
//get the objects
////////////////////////////////////////////////////////

list($course, $cm) = get_course_and_cm_from_cmid($id, 'feedback');

$baseurl = new moodle_url('/mod/feedback/show_entries.php', array('id' => $cm->id));
$PAGE->set_url(new moodle_url($baseurl, array('userid' => $userid, 'showcompleted' => $showcompleted,
        'delete' => $deleteid)));

$context = context_module::instance($cm->id);

require_login($course, true, $cm);
$feedback = $PAGE->activityrecord;

require_capability('mod/feedback:viewreports', $context);

// Process delete template result.
if ($deleteid && optional_param('confirm', 0, PARAM_BOOL) && confirm_sesskey()) {
    require_capability('mod/feedback:deletesubmissions', $context);
    $completed = $DB->get_record('feedback_completed', array('id' => $deleteid), '*', MUST_EXIST);
    feedback_delete_completed($deleteid);
    redirect($baseurl);
}

/// Print the page header
$strfeedbacks = get_string("modulenameplural", "feedback");
$strfeedback  = get_string("modulename", "feedback");

navigation_node::override_active_url($baseurl);
$PAGE->set_heading($course->fullname);
$PAGE->set_title($feedback->name);
echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($feedback->name));

$current_tab = 'showentries';
require('tabs.php');

/// Print the main part of the page
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////

// Print the list of responses.
if (!$showcompleted && !$deleteid && !$userid) {
    // Show non-anonymous responses.
    $responsestable = new mod_feedback_responses_table($cm);
    $totalrows = $responsestable->get_total_responses_count();
    if ($feedback->anonymous == FEEDBACK_ANONYMOUS_NO || $totalrows) {
        echo $OUTPUT->heading(get_string('non_anonymous_entries', 'feedback', $totalrows), 4);


        $responsestable->display();
    }

    // Show anonymous responses.
    feedback_shuffle_anonym_responses($feedback);
    $anonresponsestable = new mod_feedback_responses_anon_table($cm);
    $totalrows = $anonresponsestable->get_total_responses_count();
    if ($feedback->anonymous == FEEDBACK_ANONYMOUS_YES || $totalrows) {
        echo $OUTPUT->heading(get_string('anonymous_entries', 'feedback', $totalrows), 4);
        $anonresponsestable->display();
    }

}

// Print the response of the given user.
if ($userid || $showcompleted) {
    //get the feedbackitems
    $feedbackitems = $DB->get_records('feedback_item', array('feedback' => $feedback->id), 'position');

    if ($userid) {
        $user = $DB->get_record('user', array('id' => $userid, 'deleted' => 0), '*', MUST_EXIST);
        $params = ['feedback' => $feedback->id, 'userid' => $userid, 'anonymous_response' => FEEDBACK_ANONYMOUS_NO];
        if ($showcompleted) {
            $params['id'] = $showcompleted;
        }
        $feedbackcompleted = $DB->get_record('feedback_completed', $params);
        $responsetitle = userdate($feedbackcompleted->timemodified) . ' (' . fullname($user) . ')';
    } else if ($showcompleted) {
        $feedbackcompleted = $DB->get_record('feedback_completed',
                array('feedback' => $feedback->id, 'id' => $showcompleted,
                    'anonymous_response' => FEEDBACK_ANONYMOUS_YES), '*', MUST_EXIST);
        $responsetitle = get_string('response_nr', 'feedback') . ': ' .
            $feedbackcompleted->random_response . ' (' . get_string('anonymous', 'feedback') . ')';
    }

    echo $OUTPUT->heading($responsetitle, 4);

    // Print the items.
    if (is_array($feedbackitems)) {
        $align = right_to_left() ? 'right' : 'left';

        echo $OUTPUT->box_start('feedback_items');
        $itemnr = 0;
        foreach ($feedbackitems as $feedbackitem) {
            //get the values
            $params = array('completed'=>$feedbackcompleted->id, 'item'=>$feedbackitem->id);
            $value = $DB->get_record('feedback_value', $params);
            echo $OUTPUT->box_start('feedback_item_box_'.$align);
            if ($feedbackitem->hasvalue == 1 AND $feedback->autonumbering) {
                $itemnr++;
                echo $OUTPUT->box_start('feedback_item_number_'.$align);
                echo $itemnr;
                echo $OUTPUT->box_end();
            }

            if ($feedbackitem->typ != 'pagebreak') {
                echo $OUTPUT->box_start('box generalbox boxalign_'.$align);
                if (isset($value->value)) {
                    feedback_print_item_show_value($feedbackitem, $value->value);
                } else {
                    feedback_print_item_show_value($feedbackitem, false);
                }
                echo $OUTPUT->box_end();
            }
            echo $OUTPUT->box_end();
        }
        echo $OUTPUT->box_end();
    }

    // Show navigation to previous/next reponse.
    if ($userid) {
        $responsestable = new mod_feedback_responses_table($cm);
    } else {
        $responsestable = new mod_feedback_responses_anon_table($cm);
    }
    list($prevresponseurl, $returnurl, $nextresponseurl) = $responsestable->get_reponse_navigation_links($feedbackcompleted);

    echo html_writer::start_div('response_navigation');
    echo $prevresponseurl ? html_writer::link($prevresponseurl, get_string('prev'), ['class' => 'prev_response']) : '';
    echo html_writer::link($returnurl, get_string('back'), ['class' => 'back_to_list']);
    echo $nextresponseurl ? html_writer::link($nextresponseurl, get_string('next'), ['class' => 'next_response']) : '';
    echo html_writer::end_div();
}

// Print confirmation form to delete a response.
if ($deleteid) {
    $continueurl = new moodle_url($baseurl, array('delete' => $deleteid, 'confirm' => 1, 'sesskey' => sesskey()));
    echo $OUTPUT->confirm(get_string('confirmdeleteentry', 'feedback'), $continueurl, $baseurl);
}

/// Finish the page
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////

echo $OUTPUT->footer();

