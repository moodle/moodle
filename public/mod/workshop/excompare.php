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
 * Display example submission followed by its reference assessment and the user's assessment to compare them
 *
 * @package    mod_workshop
 * @copyright  2009 David Mudrak <david.mudrak@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/locallib.php');

$cmid   = required_param('cmid', PARAM_INT);    // course module id
$sid    = required_param('sid', PARAM_INT);     // example submission id
$aid    = required_param('aid', PARAM_INT);     // the user's assessment id

$cm     = get_coursemodule_from_id('workshop', $cmid, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

require_login($course, false, $cm);
if (isguestuser()) {
    throw new \moodle_exception('guestsarenotallowed');
}

$workshop = $DB->get_record('workshop', array('id' => $cm->instance), '*', MUST_EXIST);
$workshop = new workshop($workshop, $cm, $course);
$strategy = $workshop->grading_strategy_instance();

$PAGE->set_url($workshop->excompare_url($sid, $aid));

$example    = $workshop->get_example_by_id($sid);
$assessment = $workshop->get_assessment_by_id($aid);
if ($assessment->submissionid != $example->id) {
    throw new \moodle_exception('invalidarguments');
}
$mformassessment = $strategy->get_assessment_form($PAGE->url, 'assessment', $assessment, false);
if ($refasid = $DB->get_field('workshop_assessments', 'id', array('submissionid' => $example->id, 'weight' => 1))) {
    $reference = $workshop->get_assessment_by_id($refasid);
    $mformreference = $strategy->get_assessment_form($PAGE->url, 'assessment', $reference, false);
}

$canmanage  = has_capability('mod/workshop:manageexamples', $workshop->context);
$isreviewer = ($USER->id == $assessment->reviewerid);

if ($canmanage) {
    // ok you can go
} elseif ($isreviewer and $workshop->assessing_examples_allowed()) {
    // ok you can go
} else {
    throw new \moodle_exception('nopermissions', 'error', $workshop->view_url(), 'compare example assessment');
}

$PAGE->set_title($workshop->name);
$PAGE->set_heading($course->fullname);
$PAGE->navbar->add(get_string('examplecomparing', 'workshop'));

// Output starts here
$output = $PAGE->get_renderer('mod_workshop');
echo $output->header();
// Output the back button.
echo $output->single_button($workshop->view_url(), get_string('back'), 'get', ['class' => 'mb-3']);
if (!$PAGE->has_secondary_navigation()) {
    echo $output->heading(format_string($workshop->name));
}
echo $output->heading(get_string('assessedexample', 'workshop'), 3);

echo $output->render($workshop->prepare_example_submission($example));

// if the reference assessment is available, display it
if (!empty($mformreference)) {
    $options = array(
        'showreviewer'  => false,
        'showauthor'    => false,
        'showform'      => true,
    );
    $reference = $workshop->prepare_example_reference_assessment($reference, $mformreference, $options);
    $reference->title = get_string('assessmentreference', 'workshop');
    if ($canmanage) {
        $reference->url = $workshop->exassess_url($reference->id);
    }
    echo $output->render($reference);
}

if ($isreviewer) {
    $options = array(
        'showreviewer'  => true,
        'showauthor'    => false,
        'showform'      => true,
    );
    $assessment = $workshop->prepare_example_assessment($assessment, $mformassessment, $options);
    $assessment->title = get_string('assessmentbyyourself', 'workshop');
    if ($workshop->assessing_examples_allowed()) {
        $assessment->add_action(
            new moodle_url($workshop->exsubmission_url($example->id), array('assess' => 'on', 'sesskey' => sesskey())),
            get_string('reassess', 'workshop')
        );
    }
    echo $output->render($assessment);

} elseif ($canmanage) {
    $options = array(
        'showreviewer'  => true,
        'showauthor'    => false,
        'showform'      => true,
    );
    $assessment = $workshop->prepare_example_assessment($assessment, $mformassessment, $options);
    echo $output->render($assessment);
}

echo $output->footer();
