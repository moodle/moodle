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
 * Review all example assessments for a given user
 *
 * @package    mod
 * @subpackage workshop
 * @copyright  2009 David Mudrak <david.mudrak@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/locallib.php');

$id         = required_param('id', PARAM_INT);  // workshop id
$uid        = required_param('uid', PARAM_INT); // user id
$reviewer   = $DB->get_record('user', array('id' => $uid), '*', MUST_EXIST);
$workshop   = $DB->get_record('workshop', array('id' => $id), '*', MUST_EXIST);
$course     = $DB->get_record('course', array('id' => $workshop->course), '*', MUST_EXIST);
$cm         = get_coursemodule_from_instance('workshop', $workshop->id, $course->id, false, MUST_EXIST);

require_login($course, false, $cm);
if (isguestuser()) {
    print_error('guestsarenotallowed');
}
$workshop = new workshop($workshop, $cm, $course);

$assessments = $workshop->get_examples_for_reviewer($reviewer->id);
$references = $workshop->get_examples_for_manager();

$PAGE->set_url($workshop->all_exassess_url($reviewer->id));
$PAGE->set_title($workshop->name);
$PAGE->set_heading($course->fullname);
$PAGE->navbar->add(get_string('assessingexample', 'workshop')); //todo

$canmanage  = has_capability('mod/workshop:manageexamples', $workshop->context);
$isreviewer = ($USER->id == $reviewer->id);

if ($isreviewer or $canmanage) {
    // such a user can continue
} else {
    print_error('nopermissions', 'error', $workshop->view_url(), 'assess example submission');
}

//todo: stop reviewer from viewing before assessment is closed

// load the grading strategy logic
$strategy = $workshop->grading_strategy_instance();

// load the assessment form and process the submitted data eventually

// output starts here
$output = $PAGE->get_renderer('mod_workshop');      // workshop renderer
echo $output->header();
echo $output->heading(get_string('exampleassessments', 'workshop', fullname($reviewer)), 2);

foreach($assessments as $k => $v) {

    $reference = $workshop->get_assessment_by_id($references[$k]->assessmentid);
    $mformreference = $strategy->get_assessment_form($PAGE->url, 'assessment', $reference, false);
        
    $mformassessment = null;
    if (!empty($v->assessmentid)) {
        $assessment = $workshop->get_assessment_by_id($v->assessmentid);
        $mformassessment = $strategy->get_assessment_form($PAGE->url, 'assessment', $assessment, false);
        
        $options = array(
            'showreviewer'  => true,
            'showauthor'    => false,
            'showform'      => true,
        );
    
        $exassessment = $workshop->prepare_example_assessment($assessment, $mformassessment, $options);
        $exassessment->reference_form = $mformreference;
    
        echo $output->render($exassessment);
    }
    
}

echo $output->footer();
