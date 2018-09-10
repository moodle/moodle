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

$id = required_param('id', PARAM_INT); // Course module ID.
$currentsection   = $SESSION->questionnaire->currentfbsection;
if (! $cm = get_coursemodule_from_id('questionnaire', $id)) {
    print_error('invalidcoursemodule');
}

if (! $course = $DB->get_record("course", array("id" => $cm->course))) {
    print_error('coursemisconf');
}

if (! $questionnaire = $DB->get_record("questionnaire", array("id" => $cm->instance))) {
    print_error('invalidcoursemodule');
}

require_course_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_once($CFG->dirroot.'/mod/questionnaire/lib.php');

$url = new moodle_url($CFG->wwwroot.'/mod/questionnaire/fbsettings.php', array('id' => $id));
$PAGE->set_url($url);
$PAGE->set_context($context);

$questionnaire = new questionnaire(0, $questionnaire, $course, $cm);

if (!$questionnaire->capabilities->manage) {
    print_error('nopermissions', 'error', 'mod:questionnaire:manage');
}
$sid = $questionnaire->survey->id;

$sdata = clone($questionnaire->survey);
$sdata->sid = $sid;
$sdata->id = $cm->id;

$feedbacksections = $questionnaire->survey->feedbacksections;

// Get the current section heading.
$sectionid = null;
$scorecalculation = null;
if ($section = $DB->get_record('questionnaire_fb_sections',
        array('survey_id' => $sid, 'section' => $currentsection))) {
    $sectionid = $section->id;
    $sectionheading = $section->sectionheading;
    $scorecalculation = $section->scorecalculation;
    $draftideditor = file_get_submitted_draft_itemid('sectionheading');
    $currentinfo = file_prepare_draft_area($draftideditor, $context->id, 'mod_questionnaire', 'sectionheading',
            $sectionid, array('subdirs' => true), $sectionheading);
    $sdata->sectionlabel = $section->sectionlabel;
    $sdata->sectionheading = array('text' => $currentinfo, 'format' => FORMAT_HTML, 'itemid' => $draftideditor);
}

$feedbackform = new \mod_questionnaire\feedback_form( null, array('currentsection' => $currentsection, 'sectionid' => $sectionid) );
$feedbackform->set_data($sdata);
if ($feedbackform->is_cancelled()) {
    // Redirect to view questionnaire page.
    redirect($CFG->wwwroot.'/mod/questionnaire/view.php?id='.$questionnaire->cm->id);
}
if ($settings = $feedbackform->get_data()) {
    $i = 0;
    while (!empty($settings->feedbackboundaries[$i])) {
        $boundary = trim($settings->feedbackboundaries[$i]);
        if (strlen($boundary) > 0 && $boundary[strlen($boundary) - 1] == '%') {
            $boundary = trim(substr($boundary, 0, -1));
        }
        $settings->feedbackboundaries[$i] = $boundary;
        $i += 1;
    }
    $numboundaries = $i;
    $settings->feedbackboundaries[-1] = 101;
    $settings->feedbackboundaries[$numboundaries] = 0;
    $settings->feedbackboundarycount = $numboundaries;

    // Save current section.
    $section = new stdClass();
    $section->survey_id = $settings->sid;
    $section->section = $currentsection;
    $section->scorecalculation = $scorecalculation;
    $section->sectionlabel = $settings->sectionlabel;
    $section->sectionheading = '';
    $section->sectionheadingformat = $settings->sectionheading['format'];

    // Check if we are updating an existing section record or creating a new one.
    if ($existsection = $DB->get_record('questionnaire_fb_sections',
            array('survey_id' => $sid, 'section' => $currentsection) ) ) {
        $section->id = $existsection->id;
    } else {
        $section->id = $DB->insert_record('questionnaire_fb_sections', $section);
    }
    $sectionheading = file_save_draft_area_files((int)$settings->sectionheading['itemid'],
            $context->id, 'mod_questionnaire', 'sectionheading', $section->id,
            array('subdirs' => false, 'maxfiles' => -1, 'maxbytes' => 0),
            $settings->sectionheading['text']);
    $DB->set_field('questionnaire_fb_sections', 'sectionheading', $sectionheading,
            array('id' => $section->id));
    $DB->set_field('questionnaire_fb_sections', 'sectionlabel', $settings->sectionlabel,
            array('id' => $section->id));

    // Save current section's feedbacks
    // first delete all existing feedbacks for this section - if any
    // because we never know whether editing feedbacks will have more or less texts, so it's easiest to delete all and stard afresh.
    $DB->delete_records('questionnaire_feedback', array('section_id' => $section->id));
    for ($i = 0; $i <= $settings->feedbackboundarycount; $i++) {
        $feedback = new stdClass();
        $feedback->section_id = $section->id;
        if (isset($settings->feedbacklabel[$i])) {
            $feedback->feedbacklabel = $settings->feedbacklabel[$i];
        }
        $feedback->feedbacktext = '';
        $feedback->feedbacktextformat = $settings->feedbacktext[$i]['format'];
        $feedback->minscore = $settings->feedbackboundaries[$i];
        $feedback->maxscore = $settings->feedbackboundaries[$i - 1];
        $feedback->id = $DB->insert_record('questionnaire_feedback', $feedback);

        $feedbacktext = file_save_draft_area_files((int)$settings->feedbacktext[$i]['itemid'],
                $context->id, 'mod_questionnaire', 'feedback', $feedback->id,
                array('subdirs' => false, 'maxfiles' => -1, 'maxbytes' => 0),
                $settings->feedbacktext[$i]['text']);
        $DB->set_field('questionnaire_feedback', 'feedbacktext', $feedbacktext,
                array('id' => $feedback->id));
    }
}
if (isset($settings->savesettings)) {
    redirect ($CFG->wwwroot.'/mod/questionnaire/view.php?id='.$questionnaire->cm->id, '', 0);
} else if (isset($settings->submitbutton)) {
    $SESSION->questionnaire->currentfbsection ++;
    redirect ($CFG->wwwroot.'/mod/questionnaire/fbsettings.php?id='.$questionnaire->cm->id, '', 0);
}

// Print the page header.
    $PAGE->set_title(get_string('feedbackeditingmessages', 'questionnaire'));
    $PAGE->set_heading(format_string($course->fullname));
    $PAGE->navbar->add(get_string('feedbackeditingmessages', 'questionnaire'));
    echo $OUTPUT->header();
    $feedbackform->display();
    echo $OUTPUT->footer($course);
