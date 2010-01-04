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
 * Assess a submission or view the single assessment
 *
 * Assessment id parameter must be passed. The script displays the submission and
 * the assessment form. If the current user is the reviewer and the assessing is
 * allowed, new assessment can be saved.
 * If the assessing is not allowed (for example, the assessment period is over
 * or the current user is eg a teacher), the assessment form is opened
 * in a non-editable mode.
 * The capability 'mod/workshop:peerassess' is intentionally not checked here.
 * The user is considered as a reviewer if the corresponding assessment record
 * has been prepared for him/her (during the allocation). So even a user without the
 * peerassess capability (like a 'teacher', for example) can become a reviewer.
 *
 * @package   mod-workshop
 * @copyright 2009 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/locallib.php');

$asid       = required_param('asid', PARAM_INT);  // assessment id
$assessment = $DB->get_record('workshop_assessments', array('id' => $asid), '*', MUST_EXIST);
$submission = $DB->get_record('workshop_submissions', array('id' => $assessment->submissionid), '*', MUST_EXIST);
$workshop   = $DB->get_record('workshop', array('id' => $submission->workshopid), '*', MUST_EXIST);
$course     = $DB->get_record('course', array('id' => $workshop->course), '*', MUST_EXIST);
$cm         = get_coursemodule_from_instance('workshop', $workshop->id, $course->id, false, MUST_EXIST);

require_login($course, false, $cm);
if (isguestuser()) {
    print_error('guestsarenotallowed');
}
$workshop = new workshop($workshop, $cm, $course);

$PAGE->set_url($workshop->assess_url($assessment->id));
$PAGE->set_title($workshop->name);
$PAGE->set_heading($course->fullname);
$PAGE->navbar->add(get_string('assessingsubmission', 'workshop'));
$currenttab = 'assessment';

$canviewallassessments  = has_capability('mod/workshop:viewallassessments', $workshop->context);
$canviewallsubmissions  = has_capability('mod/workshop:viewallsubmissions', $workshop->context);
$isreviewer             = ($USER->id == $assessment->reviewerid);
$isauthor               = ($USER->id == $submission->authorid);

if ($isreviewer or $isauthor or ($canviewallassessments and $canviewallsubmissions)) {
    // such a user can continue
} else {
    print_error('nopermissions', '', $workshop->view_url());
}

// only the reviewer is allowed to modify the assessment
if ($isreviewer and $workshop->assessing_allowed()) {
    $editable = true;
} else {
    $editable = false;
}

// load the grading strategy logic
$strategy = $workshop->grading_strategy_instance();

// load the assessment form
$mform = $strategy->get_assessment_form($PAGE->url, 'assessment', $assessment, $editable);

if ($mform->is_cancelled()) {
    redirect($workshop->view_url());

} elseif ($editable and ($data = $mform->get_data())) {
    $rawgrade = $strategy->save_assessment($assessment, $data);
    if (!is_null($rawgrade) and isset($data->saveandclose)) {
        redirect($workshop->view_url());
    } else {
        // either it is not possible to calculate the $rawgrade
        // or the reviewer has chosen "Save and continue"
        redirect($PAGE->url);
    }
}

// output starts here
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('assessmentform', 'workshop'), 2);

$wsoutput = $PAGE->theme->get_renderer('mod_workshop', $PAGE);      // workshop renderer
$submission = $workshop->get_submission_by_id($submission->id);     // reload so can be passed to the renderer
echo $wsoutput->submission_full($submission, has_capability('mod/workshop:viewauthornames', $workshop->context));

$mform->display();
echo $OUTPUT->footer();
