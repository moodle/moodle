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
require_once($CFG->dirroot.'/mod/questionnaire/questionnaire.class.php');

$id = required_param('id', PARAM_INT);    // Course module ID.
$currentgroupid = optional_param('group', 0, PARAM_INT); // Groupid.
$cancel = optional_param('cancel', '', PARAM_ALPHA);
$submitbutton2 = optional_param('submitbutton2', '', PARAM_ALPHA);

if (! $cm = get_coursemodule_from_id('questionnaire', $id)) {
    print_error('invalidcoursemodule');
}

if (! $course = $DB->get_record("course", array("id" => $cm->course))) {
    print_error('coursemisconf');
}

if (! $questionnaire = $DB->get_record("questionnaire", array("id" => $cm->instance))) {
    print_error('invalidcoursemodule');
}

// Needed here for forced language courses.
require_course_login($course, true, $cm);
$context = context_module::instance($cm->id);

$url = new moodle_url($CFG->wwwroot.'/mod/questionnaire/qsettings.php', array('id' => $id));
$PAGE->set_url($url);
$PAGE->set_context($context);
if (!isset($SESSION->questionnaire)) {
    $SESSION->questionnaire = new stdClass();
}
$questionnaire = new questionnaire(0, $questionnaire, $course, $cm);

// Add renderer and page objects to the questionnaire object for display use.
$questionnaire->add_renderer($PAGE->get_renderer('mod_questionnaire'));
$questionnaire->add_page(new \mod_questionnaire\output\qsettingspage());

$SESSION->questionnaire->current_tab = 'settings';

if (!$questionnaire->capabilities->manage) {
    print_error('nopermissions', 'error', 'mod:questionnaire:manage');
}

$settingsform = new \mod_questionnaire\settings_form('qsettings.php');
$sdata = clone($questionnaire->survey);
$sdata->sid = $questionnaire->survey->id;
$sdata->id = $cm->id;

$draftideditor = file_get_submitted_draft_itemid('info');
$currentinfo = file_prepare_draft_area($draftideditor, $context->id, 'mod_questionnaire', 'info',
                $sdata->sid, array('subdirs' => true), $questionnaire->survey->info);
$sdata->info = array('text' => $currentinfo, 'format' => FORMAT_HTML, 'itemid' => $draftideditor);

$draftideditor = file_get_submitted_draft_itemid('thankbody');
$currentinfo = file_prepare_draft_area($draftideditor, $context->id, 'mod_questionnaire', 'thankbody',
                $sdata->sid, array('subdirs' => true), $questionnaire->survey->thank_body);
$sdata->thank_body = array('text' => $currentinfo, 'format' => FORMAT_HTML, 'itemid' => $draftideditor);

$draftideditor = file_get_submitted_draft_itemid('feedbacknotes');
$currentinfo = file_prepare_draft_area($draftideditor, $context->id, 'mod_questionnaire', 'feedbacknotes',
        $sdata->sid, array('subdirs' => true), $questionnaire->survey->feedbacknotes);
$sdata->feedbacknotes = array('text' => $currentinfo, 'format' => FORMAT_HTML, 'itemid' => $draftideditor);

$settingsform->set_data($sdata);

if ($settingsform->is_cancelled()) {
    redirect ($CFG->wwwroot.'/mod/questionnaire/view.php?id='.$questionnaire->cm->id, '');
}

if ($settings = $settingsform->get_data()) {
    $sdata = new stdClass();
    $sdata->id = $settings->sid;
    $sdata->name = $settings->name;
    $sdata->realm = $settings->realm;
    $sdata->title = $settings->title;
    $sdata->subtitle = $settings->subtitle;

    $sdata->infoitemid = $settings->info['itemid'];
    $sdata->infoformat = $settings->info['format'];
    $sdata->info       = $settings->info['text'];
    $sdata->info       = file_save_draft_area_files($sdata->infoitemid, $context->id, 'mod_questionnaire', 'info',
                                                    $sdata->id, array('subdirs' => true), $sdata->info);

    $sdata->theme = ''; // Deprecated theme field.
    $sdata->thanks_page = $settings->thanks_page;
    $sdata->thank_head = $settings->thank_head;

    $sdata->thankitemid = $settings->thank_body['itemid'];
    $sdata->thankformat = $settings->thank_body['format'];
    $sdata->thank_body  = $settings->thank_body['text'];
    $sdata->thank_body  = file_save_draft_area_files($sdata->thankitemid, $context->id, 'mod_questionnaire', 'thankbody',
                                                     $sdata->id, array('subdirs' => true), $sdata->thank_body);
    $sdata->email = $settings->email;

    if (isset ($settings->feedbackscores)) {
        $sdata->feedbackscores = $settings->feedbackscores;
    } else {
        $sdata->feedbackscores = 0;
    }

    if (isset ($settings->feedbacknotes)) {
        $sdata->fbnotesitemid = $settings->feedbacknotes['itemid'];
        $sdata->fbnotesformat = $settings->feedbacknotes['format'];
        $sdata->feedbacknotes  = $settings->feedbacknotes['text'];
        $sdata->feedbacknotes  = file_save_draft_area_files($sdata->fbnotesitemid,
                        $context->id, 'mod_questionnaire', 'feedbacknotes',
                        $sdata->id, array('subdirs' => true), $sdata->feedbacknotes);
    } else {
        $sdata->feedbacknotes = '';
    }

    if (isset ($settings->feedbacksections)) {
        $sdata->feedbacksections = $settings->feedbacksections;
        $usergraph = get_config('questionnaire', 'usergraph');
        if ($usergraph) {
            if ($settings->feedbacksections == 1) {
                $sdata->chart_type = $settings->chart_type_global;
            } else if ($settings->feedbacksections == 2) {
                $sdata->chart_type = $settings->chart_type_two_sections;
            } else if ($settings->feedbacksections > 2) {
                $sdata->chart_type = $settings->chart_type_sections;
            }
        }
    } else {
        $sdata->feedbacksections = '';
    }
    $sdata->courseid = $settings->courseid;
    if (!($sid = $questionnaire->survey_update($sdata))) {
        print_error('couldnotcreatenewsurvey', 'questionnaire');
    } else {
        if ($submitbutton2) {
            $redirecturl = course_get_url($cm->course);
        } else {
            $redirecturl = $CFG->wwwroot.'/mod/questionnaire/view.php?id='.$questionnaire->cm->id;
        }

        // Save current advanced settings only.
        if (isset($settings->submitbutton) || isset($settings->submitbutton2)) {
            redirect ($redirecturl, get_string('settingssaved', 'questionnaire'));
        }

        // Delete existing section and feedback records for this questionnaire if any were previously set and None are wanted now
        // or Global feedback is now wanted.
        if ($sdata->feedbacksections == 0 || ($questionnaire->survey->feedbacksections > 1 && $sdata->feedbacksections == 1)) {
            if ($feedbacksections = $DB->get_records('questionnaire_fb_sections',
                    array('survey_id' => $sid), '', 'id') ) {
                foreach ($feedbacksections as $key => $feedbacksection) {
                    $DB->delete_records('questionnaire_feedback', array('section_id' => $key));
                }
                $DB->delete_records('questionnaire_fb_sections', array('survey_id' => $sid));
            }
        }

        // Save current advanced settings and go to edit feedback page(s).
        $SESSION->questionnaire->currentfbsection = 1;
        switch ($settings->feedbacksections) {
            // 1 fbsection means Global feedback, redirect immediately to the fb settings page.
            case 1:
                redirect ($CFG->wwwroot.'/mod/questionnaire/fbsettings.php?id='.$questionnaire->cm->id,
                        get_string('settingssaved', 'questionnaire'), 0);
                break;
            // More than 1 section, go to fb sections page for user to put questions inside sections.
            default:
                // This questionnaire has more than one feedback sections, so needs to set sections questions first
                // before setting feedback messages.
                redirect ($CFG->wwwroot.'/mod/questionnaire/fbsections.php?id='.$questionnaire->cm->id, '', 0);
                break;
        }
    }
}

// Print the page header.
$PAGE->set_title(get_string('editingquestionnaire', 'questionnaire'));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->navbar->add(get_string('editingquestionnaire', 'questionnaire'));
echo $questionnaire->renderer->header();
require('tabs.php');
$questionnaire->page->add_to_page('formarea', $settingsform->render());
echo $questionnaire->renderer->render($questionnaire->page);
echo $questionnaire->renderer->footer($course);
