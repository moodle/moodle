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
 * Assess an example submission
 *
 * @package   mod-workshop
 * @copyright 2009 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/locallib.php');

$asid       = required_param('asid', PARAM_INT);  // assessment id
$assessment = $DB->get_record('workshop_assessments', array('id' => $asid), '*', MUST_EXIST);
$example    = $DB->get_record('workshop_submissions', array('id' => $assessment->submissionid, 'example' => 1), '*', MUST_EXIST);
$workshop   = $DB->get_record('workshop', array('id' => $example->workshopid), '*', MUST_EXIST);
$course     = $DB->get_record('course', array('id' => $workshop->course), '*', MUST_EXIST);
$cm         = get_coursemodule_from_instance('workshop', $workshop->id, $course->id, false, MUST_EXIST);

require_login($course, false, $cm);
if (isguestuser()) {
    print_error('guestsarenotallowed');
}
$workshop = new workshop($workshop, $cm, $course);

$PAGE->set_url($workshop->exassess_url($assessment->id));
$PAGE->set_title($workshop->name);
$PAGE->set_heading($course->fullname);
$PAGE->navbar->add(get_string('assessingexample', 'workshop'));
$currenttab = 'assessment';

$canmanage  = has_capability('mod/workshop:manageexamples', $workshop->context);
$isreviewer = ($USER->id == $assessment->reviewerid);

if ($isreviewer or $canmanage) {
    // such a user can continue
} else {
    print_error('nopermissions', 'error', $workshop->view_url(), 'assess example submission');
}

// only the reviewer is allowed to modify the assessment
if ($canmanage or ($isreviewer and $workshop->assessing_examples_allowed())) {
    $assessmenteditable = true;
} else {
    $assessmenteditable = false;
}

// load the grading strategy logic
$strategy = $workshop->grading_strategy_instance();

// load the assessment form and process the submitted data eventually
$mform = $strategy->get_assessment_form($PAGE->url, 'assessment', $assessment, $assessmenteditable);
if ($mform->is_cancelled()) {
    redirect($workshop->view_url());
} elseif ($assessmenteditable and ($data = $mform->get_data())) {
    $rawgrade = $strategy->save_assessment($assessment, $data);
    if ($canmanage) {
        // remember the last one who edited the reference assessment
        $DB->set_field('workshop_assessments', 'reviewerid', $USER->id, array('id' => $assessment->id));
    }
    if (!is_null($rawgrade) and isset($data->saveandclose)) {
        if ($canmanage) {
            redirect($workshop->view_url());
        } else {
            redirect($workshop->excompare_url($example->id, $assessment->id));
        }
    } else {
        // either it is not possible to calculate the $rawgrade
        // or the reviewer has chosen "Save and continue"
        redirect($PAGE->url);
    }
}

// output starts here
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('assessedexample', 'workshop'), 2);

$wsoutput = $PAGE->get_renderer('mod_workshop');      // workshop renderer
$example = $workshop->get_example_by_id($example->id);     // reload so can be passed to the renderer
echo $wsoutput->example_full($example);

// show instructions for assessing as thay may contain important information
// for evaluating the assessment
if (trim($workshop->instructreviewers)) {
    $instructions = file_rewrite_pluginfile_urls($workshop->instructreviewers, 'pluginfile.php', $PAGE->context->id,
        'mod_workshop', 'instructreviewers', 0, workshop::instruction_editors_options($PAGE->context));
    print_collapsible_region_start('', 'workshop-viewlet-instructreviewers', get_string('instructreviewers', 'workshop'));
    echo $OUTPUT->box(format_text($instructions, $workshop->instructreviewersformat), array('generalbox', 'instructions'));
    print_collapsible_region_end();
}

if ($canmanage) {
    echo $OUTPUT->heading(get_string('assessmentreference', 'workshop'), 2);
} elseif ($isreviewer) {
    echo $OUTPUT->heading(get_string('assessmentbyyourself', 'workshop'), 2);
} else {
    $assessment = $workshop->get_assessment_by_id($assessment->id); // extend the current record with user details
    $reviewer   = new stdclass();
    $reviewer->firstname = $assessment->reviewerfirstname;
    $reviewer->lastname = $assessment->reviewerlastname;
    echo $OUTPUT->heading(get_string('assessmentbyknown', 'workshop', fullname($reviewer)), 2);
}

$mform->display();
echo $OUTPUT->footer();
