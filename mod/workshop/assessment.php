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
 * @package    mod
 * @subpackage workshop
 * @copyright  2009 David Mudrak <david.mudrak@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/locallib.php');

$asid       = required_param('asid', PARAM_INT);  // assessment id
$assessment = $DB->get_record('workshop_assessments', array('id' => $asid), '*', MUST_EXIST);
$submission = $DB->get_record('workshop_submissions', array('id' => $assessment->submissionid, 'example' => 0), '*', MUST_EXIST);
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

$canviewallassessments  = has_capability('mod/workshop:viewallassessments', $workshop->context);
$canviewallsubmissions  = has_capability('mod/workshop:viewallsubmissions', $workshop->context);
$cansetassessmentweight = has_capability('mod/workshop:allocate', $workshop->context);
$canoverridegrades      = has_capability('mod/workshop:overridegrades', $workshop->context);
$isreviewer             = ($USER->id == $assessment->reviewerid);
$isauthor               = ($USER->id == $submission->authorid);

if ($isreviewer or $isauthor or ($canviewallassessments and $canviewallsubmissions)) {
    // such a user can continue
} else {
    print_error('nopermissions', 'error', $workshop->view_url(), 'view this assessment');
}

if ($isauthor and !$isreviewer and !$canviewallassessments and $workshop->phase != workshop::PHASE_CLOSED) {
    // authors can see assessments of their work at the end of workshop only
    print_error('nopermissions', 'error', $workshop->view_url(), 'view assessment of own work before workshop is closed');
}

// only the reviewer is allowed to modify the assessment
if ($isreviewer and $workshop->assessing_allowed($USER->id)) {
    $assessmenteditable = true;
} else {
    $assessmenteditable = false;
}

// check that all required examples have been assessed by the user
if ($assessmenteditable and $workshop->useexamples and $workshop->examplesmode == workshop::EXAMPLES_BEFORE_ASSESSMENT
        and !has_capability('mod/workshop:manageexamples', $workshop->context)) {
    // the reviewer must have submitted their own submission
    $reviewersubmission = $workshop->get_submission_by_author($assessment->reviewerid);
    if (!$reviewersubmission) {
        // no money, no love
        $assessmenteditable = false;
        echo $output->header();
        echo $output->heading(get_string('exampleneedsubmission', 'workshop'), 2);
        echo $output->footer();
        exit;
    } else {
        $examples = $workshop->get_examples_for_reviewer($assessment->reviewerid);
        foreach ($examples as $exampleid => $example) {
            if (is_null($example->grade)) {
                $assessmenteditable = false;
                echo $output->header();
                echo $output->heading(get_string('exampleneedassessed', 'workshop'), 2);
                echo $output->footer();
                exit;
            }
        }
    }
}

// load the grading strategy logic
$strategy = $workshop->grading_strategy_instance();

if (is_null($assessment->grade) and !$assessmenteditable) {
    $mform = null;
} else {
    // load the assessment form and process the submitted data eventually
    $mform = $strategy->get_assessment_form($PAGE->url, 'assessment', $assessment, $assessmenteditable,
                                        array('editableweight' => $cansetassessmentweight));
    $mform->set_data(array('weight' => $assessment->weight)); // other values are set by subplugins
    if ($mform->is_cancelled()) {
        redirect($workshop->view_url());
    } elseif ($assessmenteditable and ($data = $mform->get_data())) {
        if (is_null($assessment->grade)) {
            $workshop->log('add assessment', $workshop->assess_url($assessment->id), $assessment->submissionid);
        } else {
            $workshop->log('update assessment', $workshop->assess_url($assessment->id), $assessment->submissionid);
        }
        $rawgrade = $strategy->save_assessment($assessment, $data);
        if (isset($data->weight) and $cansetassessmentweight) {
            $DB->set_field('workshop_assessments', 'weight', $data->weight, array('id' => $assessment->id));
        }
        if (!is_null($rawgrade) and isset($data->saveandclose)) {
            redirect($workshop->view_url());
        } else {
            // either it is not possible to calculate the $rawgrade
            // or the reviewer has chosen "Save and continue"
            redirect($PAGE->url);
        }
    }
}

// load the form to override gradinggrade and/or set weight and process the submitted data eventually
if ($canoverridegrades or $cansetassessmentweight) {
    $options = array(
        'editable' => true,
        'editableweight' => $cansetassessmentweight,
        'overridablegradinggrade' => $canoverridegrades);
    $feedbackform = $workshop->get_feedbackreviewer_form($PAGE->url, $assessment, $options);
    if ($data = $feedbackform->get_data()) {
        $data = file_postupdate_standard_editor($data, 'feedbackreviewer', array(), $workshop->context);
        $record = new stdclass();
        $record->id = $assessment->id;
        if ($cansetassessmentweight) {
            $record->weight = $data->weight;
        }
        if ($canoverridegrades) {
            $record->gradinggradeover = $workshop->raw_grade_value($data->gradinggradeover, $workshop->gradinggrade);
            $record->gradinggradeoverby = $USER->id;
            $record->feedbackreviewer = $data->feedbackreviewer;
            $record->feedbackreviewerformat = $data->feedbackreviewerformat;
        }
        $DB->update_record('workshop_assessments', $record);
        redirect($workshop->view_url());
    }
}

// output starts here
$output = $PAGE->get_renderer('mod_workshop');      // workshop renderer
echo $output->header();
echo $output->heading(get_string('assessedsubmission', 'workshop'), 2);

$submission = $workshop->get_submission_by_id($submission->id);     // reload so can be passed to the renderer
echo $output->render($workshop->prepare_submission($submission, has_capability('mod/workshop:viewauthornames', $workshop->context)));

// show instructions for assessing as they may contain important information
// for evaluating the assessment
if (trim($workshop->instructreviewers)) {
    $instructions = file_rewrite_pluginfile_urls($workshop->instructreviewers, 'pluginfile.php', $PAGE->context->id,
        'mod_workshop', 'instructreviewers', 0, workshop::instruction_editors_options($PAGE->context));
    print_collapsible_region_start('', 'workshop-viewlet-instructreviewers', get_string('instructreviewers', 'workshop'));
    echo $output->box(format_text($instructions, $workshop->instructreviewersformat, array('overflowdiv'=>true)), array('generalbox', 'instructions'));
    print_collapsible_region_end();
}

// extend the current assessment record with user details
$assessment = $workshop->get_assessment_by_id($assessment->id);

if ($isreviewer) {
    $options    = array(
        'showreviewer'  => true,
        'showauthor'    => has_capability('mod/workshop:viewauthornames', $workshop->context),
        'showform'      => $assessmenteditable or !is_null($assessment->grade),
        'showweight'    => true,
    );
    $assessment = $workshop->prepare_assessment($assessment, $mform, $options);
    $assessment->title = get_string('assessmentbyyourself', 'workshop');
    echo $output->render($assessment);

} else {
    $options    = array(
        'showreviewer'  => has_capability('mod/workshop:viewreviewernames', $workshop->context),
        'showauthor'    => has_capability('mod/workshop:viewauthornames', $workshop->context),
        'showform'      => $assessmenteditable or !is_null($assessment->grade),
        'showweight'    => true,
    );
    $assessment = $workshop->prepare_assessment($assessment, $mform, $options);
    echo $output->render($assessment);
}

if (!$assessmenteditable and $canoverridegrades) {
    $feedbackform->display();
}

echo $output->footer();
