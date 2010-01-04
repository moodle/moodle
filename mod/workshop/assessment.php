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
 * Assess a submission or preview the assessment form
 *
 * Displays an assessment form and saves the grades given by current user (reviewer)
 * for the dimensions.
 *
 * @package   mod-workshop
 * @copyright 2009 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/locallib.php');

if ($preview = optional_param('preview', 0, PARAM_INT)) {
    $mode = 'preview';
    if (!$cm = get_coursemodule_from_id('workshop', $preview)) {
        print_error('invalidcoursemodule');
    }
    if (!$course = $DB->get_record('course', array('id' => $cm->course))) {
        print_error('coursemisconf');
    }
    if (!$workshop = $DB->get_record('workshop', array('id' => $cm->instance))) {
        print_error('err_invalidworkshopid', 'workshop');
    }
    $submission = new stdClass();
    $assessment = new stdClass();
} else {
    $mode = 'assessment';
    $assessmentid = required_param('asid', PARAM_INT);  // assessment id
    if (!$assessment = $DB->get_record('workshop_assessments', array('id' => $assessmentid))) {
        print_error('err_unknownassessment', 'workshop');
    }
    if (!$submission = $DB->get_record('workshop_submissions', array('id' => $assessment->submissionid))) {
        print_error('err_unknownsubmission', 'workshop');
    }
    if (!$workshop = $DB->get_record('workshop', array('id' => $submission->workshopid))) {
        print_error('err_invalidworkshopid', 'workshop');
    }
    if (!$cm = get_coursemodule_from_instance('workshop', $workshop->id, $workshop->course)) {
        print_error('invalidcoursemodule');
    }   
    if (!$course = $DB->get_record('course', array('id' => $cm->course))) {
        print_error('coursemisconf');
    }   
}

require_login($course, false, $cm);

$workshop = new workshop_api($workshop, $cm);

$context = $PAGE->context;

if (isguestuser()) {
    print_error('err_noguests', 'workshop', "$CFG->wwwroot/mod/workshop/view.php?id=$cmid");
}

// where should the user be sent after closing the assessment form
$returnurl = "{$CFG->wwwroot}/mod/workshop/view.php?id={$cm->id}";
// the URL of this handler
if ($mode == 'preview') {
    $selfurl = "{$CFG->wwwroot}/mod/workshop/assessment.php?preview={$cm->id}";
} elseif ($mode == 'assessment') {
    $selfurl = "{$CFG->wwwroot}/mod/workshop/assessment.php?asid={$assessment->id}";
}
// the URL to edit this assessment form
$editurl = "{$CFG->wwwroot}/mod/workshop/editform.php?cmid={$cm->id}";

// load the grading strategy logic
$strategy = $workshop->grading_strategy_instance();

// load the assessment form definition from the database
// this must be called before get_assessment_form() where we have to know
// the number of repeating fieldsets

//todo $formdata = $strategy->load_assessment($assessment);



// load the form to edit the grading strategy dimensions
$mform = $strategy->get_assessment_form($selfurl, $mode);

// initialize form data
//todo $mform->set_data($formdata);

if ($mform->is_cancelled()) {
    redirect($returnurl);
} elseif ($data = $mform->get_data()) {
    if (isset($data->backtoeditform)) {
        redirect($editurl);
    }
    $strategy->save_assessment($data);
    if (isset($data->saveandclose)) {
        redirect($returnurl);
    } else {
        // save and continue - redirect to self to prevent data being re-posted by pressing "Reload"
        redirect($selfurl);
    }
}

// build the navigation and the header
$navlinks = array();
$navlinks[] = array('name' => get_string('modulenameplural', 'workshop'), 
                    'link' => "index.php?id=$course->id", 
                    'type' => 'activity');
$navlinks[] = array('name' => format_string($workshop->name), 
                    'link' => "view.php?id=$cm->id",
                    'type' => 'activityinstance');
if ($mode == 'preview') {
    $navlinks[] = array('name' => get_string('editingassessmentform', 'workshop'),       
                        'link' => $editurl,
                        'type' => 'title');
    $navlinks[] = array('name' => get_string('previewassessmentform', 'workshop'), 
                        'link' => '',
                        'type' => 'title');
} elseif ($mode == 'assessment') {
    $navlinks[] = array('name' => get_string('assessingsubmission', 'workshop'), 
                        'link' => '',
                        'type' => 'title');
}
$navigation = build_navigation($navlinks);

// OUTPUT STARTS HERE

print_header_simple(format_string($workshop->name), '', $navigation, '', '', true, '', navmenu($course, $cm));

print_heading(get_string('assessmentform', 'workshop'));

$mform->display();

/// Finish the page
print_footer($course);
