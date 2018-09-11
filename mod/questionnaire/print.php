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

require_once("../../config.php");
require_once($CFG->dirroot.'/mod/questionnaire/questionnaire.class.php');

$qid = required_param('qid', PARAM_INT);
$rid = required_param('rid', PARAM_INT);
$courseid = required_param('courseid', PARAM_INT);
$sec = required_param('sec', PARAM_INT);
$null = null;
$referer = $CFG->wwwroot.'/mod/questionnaire/report.php';

if (! $questionnaire = $DB->get_record("questionnaire", array("id" => $qid))) {
    print_error('invalidcoursemodule');
}
if (! $course = $DB->get_record("course", array("id" => $questionnaire->course))) {
    print_error('coursemisconf');
}
if (! $cm = get_coursemodule_from_instance("questionnaire", $questionnaire->id, $course->id)) {
    print_error('invalidcoursemodule');
}

// Check login and get context.
require_login($courseid);

$questionnaire = new questionnaire(0, $questionnaire, $course, $cm);

// Add renderer and page objects to the questionnaire object for display use.
$questionnaire->add_renderer($PAGE->get_renderer('mod_questionnaire'));
if (!empty($rid)) {
    $questionnaire->add_page(new \mod_questionnaire\output\reportpage());
} else {
    $questionnaire->add_page(new \mod_questionnaire\output\previewpage());
}

// If you can't view the questionnaire, or can't view a specified response, error out.
if (!($questionnaire->capabilities->view && (($rid == 0) || $questionnaire->can_view_response($rid)))) {
    // Should never happen, unless called directly by a snoop...
    print_error('nopermissions', 'moodle', $CFG->wwwroot.'/mod/questionnaire/view.php?id='.$cm->id);
}
$blankquestionnaire = true;
if ($rid != 0) {
    $blankquestionnaire = false;
}
$url = new moodle_url($CFG->wwwroot.'/mod/questionnaire/print.php');
$url->param('qid', $qid);
$url->param('rid', $rid);
$url->param('courseid', $courseid);
$url->param('sec', $sec);
$PAGE->set_url($url);
$PAGE->set_title($questionnaire->survey->title);
$PAGE->set_pagelayout('popup');
echo $questionnaire->renderer->header();
$questionnaire->page->add_to_page('closebutton', $questionnaire->renderer->close_window_button());
$questionnaire->survey_print_render('', 'print', $courseid, $rid, $blankquestionnaire);
echo $questionnaire->renderer->render($questionnaire->page);
echo $questionnaire->renderer->footer();
