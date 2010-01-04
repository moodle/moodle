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
    $PAGE->navbar->add(get_string('editingassessmentform', 'workshop'), $workshop->editform_url(), navigation_node::TYPE_CUSTOM);
    $PAGE->navbar->add(get_string('previewassessmentform', 'workshop'));
    $currenttab = 'editform';

} elseif ('assessment' == $mode) {
    // we do not require 'mod/workshop:peerassess' here, we just check that the assessment record
    // has been prepared for the current user. So even a user without the peerassess capability
    // (like a 'teacher', for example) can become a reviewer
    if ($USER->id !== $assessment->reviewerid) {
        print_error('nopermissions', '', $workshop->view_url());
    }
    $PAGE->set_url($workshop->assess_url($assessment->id));
    $PAGE->set_title($workshop->name);
    $PAGE->set_heading($course->fullname);
    $PAGE->navbar->add(get_string('assessingsubmission', 'workshop'));
    $currenttab = 'assessment';
}

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
        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('assessmentresult', 'workshop'), 2);
        echo $OUTPUT->box('Given grade: ' . $rawgrade . ' %'); // todo more detailed info using own renderer, format grade
        echo $OUTPUT->continue_button($workshop->view_url());
        echo $OUTPUT->footer();
        die();  // bye-bye
    } else {
        // either it is not possible to calculate the $rawgrade or the reviewer has chosen "Save and continue"
        // redirect to self to prevent data being re-posted by pressing "Reload"
        redirect($PAGE->url);
    }
}

// Output starts here

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('assessmentform', 'workshop'), 2);

if ('assessment' === $mode) {
    if (has_capability('mod/workshop:viewauthornames', $PAGE->context)) {
        $showname   = true;
        $author     = $workshop->user_info($submission->authorid);
    } else {
        $showname   = false;
        $author     = null;
    }
    $wsoutput = $PAGE->theme->get_renderer('mod_workshop', $PAGE);      // workshop renderer
    $submission = $workshop->get_submission_by_id($submission->id);     // reload so can be passed to the renderer
    echo $wsoutput->submission_full($submission, $showname);
}

$mform->display();
echo $OUTPUT->footer();
