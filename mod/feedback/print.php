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

$context = context_module::instance($cm->id);

require_login($course, true, $cm);

require_capability('mod/feedback:view', $context);
$PAGE->set_pagelayout('embedded');

/// Print the page header
$strfeedbacks = get_string("modulenameplural", "feedback");
$strfeedback  = get_string("modulename", "feedback");

$feedback_url = new moodle_url('/mod/feedback/index.php', array('id'=>$course->id));
$PAGE->navbar->add($strfeedbacks, $feedback_url);
$PAGE->navbar->add(format_string($feedback->name));

$PAGE->set_title($feedback->name);
$PAGE->set_heading($course->fullname);
echo $OUTPUT->header();

/// Print the main part of the page
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
echo $OUTPUT->heading(format_text($feedback->name));

$feedbackitems = $DB->get_records('feedback_item', array('feedback'=>$feedback->id), 'position');
echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthwide');
echo $OUTPUT->continue_button('view.php?id='.$id);
if (is_array($feedbackitems)) {
    $itemnr = 0;
    $align = right_to_left() ? 'right' : 'left';

    echo $OUTPUT->box_start('feedback_items printview');
    //check, if there exists required-elements
    $params = array('feedback'=>$feedback->id, 'required'=>1);
    $countreq = $DB->count_records('feedback_item', $params);
    if ($countreq > 0) {
        echo '<span class="feedback_required_mark">(*)';
        echo get_string('items_are_required', 'feedback');
        echo '</span>';
    }
    //print the inserted items
    $itempos = 0;
    foreach ($feedbackitems as $feedbackitem) {
        echo $OUTPUT->box_start('feedback_item_box_'.$align);
        $itempos++;
        //Items without value only are labels
        if ($feedbackitem->hasvalue == 1 AND $feedback->autonumbering) {
            $itemnr++;
                echo $OUTPUT->box_start('feedback_item_number_'.$align);
                echo $itemnr;
                echo $OUTPUT->box_end();
        }
        echo $OUTPUT->box_start('box generalbox boxalign_'.$align);
        if ($feedbackitem->typ != 'pagebreak') {
            feedback_print_item_complete($feedbackitem, false, false);
        } else {
            echo $OUTPUT->box_start('feedback_pagebreak');
            echo '<hr class="feedback_pagebreak" />';
            echo $OUTPUT->box_end();
        }
        echo $OUTPUT->box_end();
        echo $OUTPUT->box_end();
    }
    echo $OUTPUT->box_end();
} else {
    echo $OUTPUT->box(get_string('no_items_available_yet', 'feedback'),
                    'generalbox boxaligncenter boxwidthwide');
}
echo $OUTPUT->continue_button('view.php?id='.$id);
echo $OUTPUT->box_end();
/// Finish the page
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////

echo $OUTPUT->footer();

