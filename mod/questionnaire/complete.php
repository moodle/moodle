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

// This page prints a particular instance of questionnaire.

require_once("../../config.php");
require_once($CFG->libdir . '/completionlib.php');
require_once($CFG->dirroot.'/mod/questionnaire/questionnaire.class.php');

if (!isset($SESSION->questionnaire)) {
    $SESSION->questionnaire = new stdClass();
}
$SESSION->questionnaire->current_tab = 'view';

$id = optional_param('id', null, PARAM_INT);    // Course Module ID.
$a = optional_param('a', null, PARAM_INT);      // questionnaire ID.

$sid = optional_param('sid', null, PARAM_INT);  // Survey id.
$resume = optional_param('resume', null, PARAM_INT);    // Is this attempt a resume of a saved attempt?

list($cm, $course, $questionnaire) = questionnaire_get_standard_page_items($id, $a);

// Check login and get context.
require_course_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/questionnaire:view', $context);

$url = new moodle_url($CFG->wwwroot.'/mod/questionnaire/complete.php');
if (isset($id)) {
    $url->param('id', $id);
} else {
    $url->param('a', $a);
}

$PAGE->set_url($url);
$PAGE->set_context($context);
$questionnaire = new questionnaire(0, $questionnaire, $course, $cm);
// Add renderer and page objects to the questionnaire object for display use.
$questionnaire->add_renderer($PAGE->get_renderer('mod_questionnaire'));
$questionnaire->add_page(new \mod_questionnaire\output\completepage());

$questionnaire->strquestionnaires = get_string("modulenameplural", "questionnaire");
$questionnaire->strquestionnaire  = get_string("modulename", "questionnaire");

// Mark as viewed.
$completion = new completion_info($course);
$completion->set_module_viewed($cm);

if ($resume) {
    $context = context_module::instance($questionnaire->cm->id);
    $anonymous = $questionnaire->respondenttype == 'anonymous';

    $event = \mod_questionnaire\event\attempt_resumed::create(array(
                    'objectid' => $questionnaire->id,
                    'anonymous' => $anonymous,
                    'context' => $context
    ));
    $event->trigger();
}

// Generate the view HTML in the page.
$questionnaire->view();

// Output the page.
echo $questionnaire->renderer->header();
echo $questionnaire->renderer->render($questionnaire->page);
echo $questionnaire->renderer->footer($course);