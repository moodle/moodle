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

// This page displays a non-completable instance of questionnaire.

require_once("../../config.php");
require_once($CFG->dirroot.'/mod/questionnaire/questionnaire.class.php');

$id     = optional_param('id', 0, PARAM_INT);
$sid    = optional_param('sid', 0, PARAM_INT);
$popup  = optional_param('popup', 0, PARAM_INT);
$qid    = optional_param('qid', 0, PARAM_INT);
$currentgroupid = optional_param('group', 0, PARAM_INT); // Groupid.

if ($id) {
    if (! $cm = get_coursemodule_from_id('questionnaire', $id)) {
        print_error('invalidcoursemodule');
    }

    if (! $course = $DB->get_record("course", array("id" => $cm->course))) {
        print_error('coursemisconf');
    }

    if (! $questionnaire = $DB->get_record("questionnaire", array("id" => $cm->instance))) {
        print_error('invalidcoursemodule');
    }
} else {
    if (! $survey = $DB->get_record("questionnaire_survey", array("id" => $sid))) {
        print_error('surveynotexists', 'questionnaire');
    }
    if (! $course = $DB->get_record("course", ["id" => $survey->courseid])) {
        print_error('coursemisconf');
    }
    // Dummy questionnaire object.
    $questionnaire = new stdClass();
    $questionnaire->id = 0;
    $questionnaire->course = $course->id;
    $questionnaire->name = $survey->title;
    $questionnaire->sid = $sid;
    $questionnaire->resume = 0;
    // Dummy cm object.
    if (!empty($qid)) {
        $cm = get_coursemodule_from_instance('questionnaire', $qid, $course->id);
    } else {
        $cm = false;
    }
}

// Check login and get context.
// Do not require login if this questionnaire is viewed from the Add questionnaire page
// to enable teachers to view template or public questionnaires located in a course where they are not enroled.
if (!$popup) {
    require_login($course->id, false, $cm);
}
$context = $cm ? context_module::instance($cm->id) : false;

$url = new moodle_url('/mod/questionnaire/preview.php');
if ($id !== 0) {
    $url->param('id', $id);
}
if ($sid) {
    $url->param('sid', $sid);
}
$PAGE->set_url($url);

$PAGE->set_context($context);
$PAGE->set_cm($cm);   // CONTRIB-5872 - I don't know why this is needed.

$questionnaire = new questionnaire($qid, $questionnaire, $course, $cm);

// Add renderer and page objects to the questionnaire object for display use.
$questionnaire->add_renderer($PAGE->get_renderer('mod_questionnaire'));
$questionnaire->add_page(new \mod_questionnaire\output\previewpage());

$canpreview = (!isset($questionnaire->capabilities) &&
               has_capability('mod/questionnaire:preview', context_course::instance($course->id))) ||
              (isset($questionnaire->capabilities) && $questionnaire->capabilities->preview);
if (!$canpreview && !$popup) {
    // Should never happen, unless called directly by a snoop...
    print_error('nopermissions', 'questionnaire', $CFG->wwwroot.'/mod/questionnaire/view.php?id='.$cm->id);
}

if (!isset($SESSION->questionnaire)) {
    $SESSION->questionnaire = new stdClass();
}
$SESSION->questionnaire->current_tab = new stdClass();
$SESSION->questionnaire->current_tab = 'preview';

$qp = get_string('preview_questionnaire', 'questionnaire');
$pq = get_string('previewing', 'questionnaire');

// Print the page header.
if ($popup) {
    $PAGE->set_pagelayout('popup');
}
$PAGE->set_title(format_string($qp));
if (!$popup) {
    $PAGE->set_heading(format_string($course->fullname));
}

// Include the needed js.


$PAGE->requires->js('/mod/questionnaire/module.js');
// Print the tabs.


echo $questionnaire->renderer->header();
if (!$popup) {
    require('tabs.php');
}
$questionnaire->page->add_to_page('heading', clean_text($pq));

if ($questionnaire->capabilities->printblank) {
    // Open print friendly as popup window.

    $linkname = '&nbsp;'.get_string('printblank', 'questionnaire');
    $title = get_string('printblanktooltip', 'questionnaire');
    $url = '/mod/questionnaire/print.php?qid='.$questionnaire->id.'&amp;rid=0&amp;'.'courseid='.
            $questionnaire->course->id.'&amp;sec=1';
    $options = array('menubar' => true, 'location' => false, 'scrollbars' => true, 'resizable' => true,
                    'height' => 600, 'width' => 800, 'title' => $title);
    $name = 'popup';
    $link = new moodle_url($url);
    $action = new popup_action('click', $link, $name, $options);
    $class = "floatprinticon";
    $questionnaire->page->add_to_page('printblank',
        $questionnaire->renderer->action_link($link, $linkname, $action, array('class' => $class, 'title' => $title),
            new pix_icon('t/print', $title)));
}
$questionnaire->survey_print_render('', 'preview', $course->id, $rid = 0, $popup);
if ($popup) {
    $questionnaire->page->add_to_page('closebutton', $questionnaire->renderer->close_window_button());
}
echo $questionnaire->renderer->render($questionnaire->page);
echo $questionnaire->renderer->footer($course);

// Log this questionnaire preview.
$context = context_module::instance($questionnaire->cm->id);
$anonymous = $questionnaire->respondenttype == 'anonymous';

$event = \mod_questionnaire\event\questionnaire_previewed::create(array(
                'objectid' => $questionnaire->id,
                'anonymous' => $anonymous,
                'context' => $context
));
$event->trigger();
