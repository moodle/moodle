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
    $mode       = 'preview';
    $cm         = get_coursemodule_from_id('workshop', $preview, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $workshop   = $DB->get_record('workshop', array('id' => $cm->instance), '*', MUST_EXIST);
    $submission = new stdClass();
    $assessment = new stdClass();

} else {
    $mode       = 'assessment';
    $asid       = required_param('asid', PARAM_INT);  // assessment id
    $assessment = $DB->get_record('workshop_assessments', array('id' => $asid), '*', MUST_EXIST);
    $submission = $DB->get_record('workshop_submissions', array('id' => $assessment->submissionid), '*', MUST_EXIST);
    $workshop   = $DB->get_record('workshop', array('id' => $submission->workshopid), '*', MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $workshop->course), '*', MUST_EXIST);
    $cm         = get_coursemodule_from_instance('workshop', $workshop->id, $course->id, false, MUST_EXIST);
}

require_login($course, false, $cm);
if (isguestuser()) {
    print_error('guestsarenotallowed');
}
$workshop = new workshop($workshop, $cm, $course);

if ('preview' == $mode) {
    require_capability('mod/workshop:editdimensions', $PAGE->context);
    $PAGE->set_url($workshop->previewform_url());
    $PAGE->set_title($workshop->name);
    $PAGE->set_heading($course->fullname);

} elseif ('assessment' == $mode) {
    if (!has_any_capability(array('mod/workshop:peerassess', 'mod/workshop:assessallsubmissions'), $PAGE->context)) {
        print_error('nopermissions', '', $workshop->view_url());
    }
    // todo do a check that the user is allowed to assess this submission
    $PAGE->set_url($workshop->assess_url($assessment->id));
    $PAGE->set_title($workshop->name);
    $PAGE->set_heading($course->fullname);
}

// build the navigation and the header - todo this will be changed by the new navigation api
$navlinks = array();
$navlinks[] = array('name' => get_string('modulenameplural', 'workshop'),
                    'link' => "index.php?id=$course->id",
                    'type' => 'activity');
$navlinks[] = array('name' => format_string($workshop->name),
                    'link' => "view.php?id=$cm->id",
                    'type' => 'activityinstance');
if ($mode == 'preview') {
    $navlinks[] = array('name' => get_string('editingassessmentform', 'workshop'),
                        'link' => $workshop->editform_url()->out(),
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

// load the grading strategy logic
$strategy = $workshop->grading_strategy_instance();

// load the form to edit the grading strategy dimensions
$mform = $strategy->get_assessment_form($PAGE->url, $mode, $assessment);

if ($mform->is_cancelled()) {
    redirect($workshop->view_url());

} elseif ($data = $mform->get_data()) {
    if (isset($data->backtoeditform)) {
        // user wants to return from preview to form editing
        redirect($workshop->editform_url());
    }
    $rawgrade = $strategy->save_assessment($assessment, $data);
    if (!is_null($rawgrade) and isset($data->saveandclose)) {
        echo $OUTPUT->header($navigation);
        echo $OUTPUT->heading(get_string('assessmentresult', 'workshop'), 2);
        echo $OUTPUT->box('Given grade: ' . sprintf("%01.2f", $rawgrade * 100) . ' %'); // todo more detailed info using own renderer
        echo $OUTPUT->continue_button($workshop->view_url());
        echo $OUTPUT->footer();
        die();  // bye-bye
    } else {
        // save and continue - redirect to self to prevent data being re-posted by pressing "Reload"
        redirect($PAGE->url);
    }
}

// Output starts here

echo $OUTPUT->header($navigation);
echo $OUTPUT->heading(get_string('assessmentform', 'workshop'), 2);

if ('assessment' === $mode) {
    if (has_capability('mod/workshop:viewauthornames', $PAGE->context)) {
        $showname   = true;
        $author     = $workshop->user_info($submission->userid);
    } else {
        $showname   = false;
        $author     = null;
    }
    $wsoutput = $PAGE->theme->get_renderer('mod_workshop', $PAGE);    // workshop renderer
    echo $wsoutput->submission_full($submission, $showname, $author);
}

$mform->display();
echo $OUTPUT->footer();
