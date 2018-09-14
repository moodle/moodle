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

$id     = optional_param('id', 0, PARAM_INT);
$sid    = optional_param('sid', 0, PARAM_INT);

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
}

// Check login and get context.
require_login($course->id, false, $cm);
$context = $cm ? context_module::instance($cm->id) : false;

$url = new moodle_url('/mod/questionnaire/fbsections.php');
if ($id !== 0) {
    $url->param('id', $id);
}
if ($sid) {
    $url->param('sid', $sid);
}
$questionnaire = new questionnaire(0, $questionnaire, $course, $cm);
$questions = $questionnaire->questions;
$sid = $questionnaire->survey->id;
$viewform = data_submitted($CFG->wwwroot."/mod/questionnaire/fbsections.php");
$feedbacksections = $questionnaire->survey->feedbacksections;
$errormsg = '';

if (data_submitted()) {
    $vf = (array)$viewform;
    if (isset($vf['savesettings'])) {
        $action = 'savesettings';
        unset($vf['savesettings']);
    }
    $scorecalculation = [];
    $submittedvf = [];
    $scorecalculationweights = [];
    foreach ($vf as $key => $value) {
        $qidsection = explode("|", $key);
        if ($qidsection[0] !== "weight") {
            continue;
        }
        if (!isset($scorecalculationweights[$qidsection[0]]) || !is_array($scorecalculationweights[$qidsection[0]])) {
            $scorecalculationweights[$qidsection[0]] = [];
        }
        // Info: $qidsection[1] = qid;  $qidsection[2] = section.
        $scorecalculationweights[$qidsection[1]][$qidsection[2]] = $value;
    }
    foreach ($vf as $qs) {
        $sectionqid = explode("_", $qs);
        if ($sectionqid[0] != 0) {
            if (isset($sectionqid[1]) && isset($scorecalculationweights[$sectionqid[1]][$sectionqid[0]])) {
                // Info: $scorecalculation[$sectionqid[0]][$sectionqid[1]] != null.
                $scorecalculation[$sectionqid[0]][$sectionqid[1]] = $scorecalculationweights[$sectionqid[1]][$sectionqid[0]];
            } else if (isset($sectionqid[1])) {
                $scorecalculation[$sectionqid[0]][$sectionqid[1]] = 0;
            }
            if (count($sectionqid) == 2) {
                // Info: [1] - id; [0] - section.
                $submittedvf[$sectionqid[1]] = $sectionqid[0];
            }
        }
    }
    $c = count($scorecalculation);
    if ($c < $feedbacksections) {
        $sectionsnotset = '';
        for ($section = 1; $section <= $feedbacksections; $section++) {
            if (!isset($scorecalculation[$section])) {
                $sectionsnotset .= $section.'&nbsp;';
            }
        }
        $errormsg = get_string('sectionsnotset', 'questionnaire', $sectionsnotset);
        $vf = $submittedvf;
    } else {
        for ($section = 1; $section <= $feedbacksections; $section++) {
            $fbcalculation[$section] = serialize($scorecalculation[$section]);
        }

        $sections = $DB->get_records('questionnaire_fb_sections',
            array('survey_id' => $questionnaire->survey->id), 'section DESC');
        // Delete former feedbacks if number of feedbacksections has been reduced.
        foreach ($sections as $section) {
            if ($section->section > $feedbacksections) {
                // Delete section record.
                $DB->delete_records('questionnaire_fb_sections', array('survey_id' => $sid, 'section' => $section->section));
                // Delete associated feedback records.
                $DB->delete_records('questionnaire_feedback', array('section_id' => $section->section));
            }
        }

        // Check if the number of feedback sections has been increased and insert new ones
        // must also insert section heading!
        for ($section = 1; $section <= $feedbacksections; $section++) {
            if ($existsection = $DB->get_record('questionnaire_fb_sections',
                array('survey_id' => $sid, 'section' => $section), '*', IGNORE_MULTIPLE) ) {
                $DB->set_field('questionnaire_fb_sections', 'scorecalculation', serialize($scorecalculation[$section]),
                    array('survey_id' => $sid, 'section' => $section));
            } else {
                $feedbacksection = new stdClass();
                $feedbacksection->survey_id = $sid;
                $feedbacksection->section = $section;
                $feedbacksection->scorecalculation = serialize($scorecalculation[$section]);
                $feedbacksection->id = $DB->insert_record('questionnaire_fb_sections', $feedbacksection);
            }
        }

        $currentsection = 1;
        $SESSION->questionnaire->currentfbsection = 1;
        redirect ($CFG->wwwroot.'/mod/questionnaire/fbsettings.php?id='.
            $questionnaire->cm->id.'&currentsection='.$currentsection, '', 0);
    }
}

// If no data from the form, extract any existing score weights from the database, and note if we are using sections beyond the
// global section.
$questionsinsections = [];
if (!isset($scorecalculationweights)) {
    $scorecalculationweights = [];
    if ($fbsections = $DB->get_records('questionnaire_fb_sections', ['survey_id' => $sid], 'section ASC')) {
        for ($section = 1; $section <= $feedbacksections; $section++) {
            // Retrieve the scorecalculation formula and the section heading only once.
            foreach ($fbsections as $fbsection) {
                if (isset($fbsection->scorecalculation) && $fbsection->section == $section) {
                    $scorecalculation = unserialize($fbsection->scorecalculation);
                    foreach ($scorecalculation as $qid => $key) {
                        if (!isset($questionsinsections[$qid]) || !is_array($questionsinsections[$qid])) {
                            $questionsinsections[$qid] = [];
                            $scorecalculationweights[$qid] = [];
                        }
                        array_push($questionsinsections[$qid], $section);
                        $scorecalculationweights[$qid][$section] = $key;
                    }
                    break;
                }
            }
        }
    }
}

if (!isset($vf)) {
    // If Global Feedback (only 1 section) and no questions have yet been put in section 1 check all questions.
    if (!empty($questionsinsections)) {
        $vf = $questionsinsections;
    }
}

$PAGE->set_url($url);
// Print the page header.
$PAGE->set_title(get_string('feedbackeditingsections', 'questionnaire'));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->navbar->add(get_string('feedbackeditingsections', 'questionnaire'));

// Add renderer and page objects to the questionnaire object for display use.
$questionnaire->add_renderer($PAGE->get_renderer('mod_questionnaire'));
$questionnaire->add_page(new \mod_questionnaire\output\fbsectionspage());

$feedbacksections = $questionnaire->survey->feedbacksections + 1;

if ($errormsg != '') {
    $questionnaire->page->add_to_page('notifications', $questionnaire->renderer->notification($errormsg));
}
$n = 0;
// Number of sectiontext questions.
$fb = 0;
$bg = 'c0';

$questionnaire->page->add_to_page('formarea', $questionnaire->renderer->box_start());

$questionnaire->page->add_to_page('formarea', $questionnaire->renderer->help_icon('feedbacksectionsselect', 'questionnaire'));
$questionnaire->page->add_to_page('formarea', '<b>Sections:</b><br /><br />');
$formdata = new stdClass();
$descendantsdata = [];

foreach ($questionnaire->questions as $question) {
    $qtype = $question->type_id;
    $qname = $question->name;
    $qid = $question->id;

    // Questions to be included in feedback sections must be required, have a name
    // and must not be child of a parent question.
    // Radio buttons need different names.
    if ($qtype != QUESPAGEBREAK ) { // && $qtype != QUESSECTIONTEXT ) {
        $n++;
    }

    $cannotuse = false;
    $strcannotuse = '';
    if ($question->supports_feedback()) {
        $qn = '<strong>' . $n . '</strong>';
        if ($qname == '') {
            $cannotuse = true;
            $strcannotuse = get_string('missingname', 'questionnaire', $qn);
        }
        if (!$question->required()) {
            $cannotuse = true;
            if ($qname == '') {
                $strcannotuse = get_string('missingnameandrequired', 'questionnaire', $qn);
            } else {
                $strcannotuse = get_string('missingrequired', 'questionnaire', $qn);
            }
        }

        if (!$cannotuse) {
            if ($question->valid_feedback()) {
                $questionnaire->page->add_to_page('formarea', '<div id="group_'.$qid.'">');
                $emptyisglobalfeedback = ($questionnaire->survey->feedbacksections == 1) && empty($questionsinsections);
                $questionnaire->page->add_to_page('formarea', '<div style="margin-bottom:5px;">[' . $qname . ']</div>');
                for ($i = 0; $i < $feedbacksections; $i++) {
                    // TODO - Add renderer for feedback section select.
                    $output = '<div style="float:left; padding-right:5px;">';
                    if ($i != 0) {
                        // RadioButton -> Checkbox
                        // onclick: Section > 0 selected? -> uncheck section 0.
                        $output .= '<div class="' . $bg . '"><input type="checkbox" style="width: 60px;" name="' . $n . '_' . $i . '"' .
                            ' id="' . $qid . '_' . $i . '" value="' . $i . '_' . $qid . '" ' .
                            'onclick="document.getElementsByName(\''.$n.'_0\')[0].checked=false;"';
                    } else {
                        // Section 0
                        // onclick: uncheck_boxes see below.
                        $output .= '<div class="' . $bg . '">' .
                            '<input type="checkbox" style="width: 60px;" onclick="uncheck_boxes(\''.$n.'\');" name="' .
                            $n . '_' . $i . '"' . ' id="' . $i . '" value="' . $i . '"';
                    }

                    if ($i == 0 && !isset($vf[$qid])) {
                        $output .= ' checked="checked"';
                    }
                    // Question already present in this section OR this is a Global feedback and questions are not set yet.
                    if ($emptyisglobalfeedback) {
                        $output .= ' checked="checked"';
                    } else {
                        // Check not only one checkbox per question.
                        if (isset($vf[$qid])) {
                            foreach ($vf[$qid] as $key => $value) {
                                if ($i == $value) {
                                    $output .= ' checked="checked"';
                                }
                            }
                        }
                    }
                    $output .= ' />';
                    // Without last </div>, add inputfield for question in section.
                    $output .= '<label for="' . $qid . '_' . $i . '">' . '<div style="padding-left: 2px;">' . $i . '</div>' .
                        '</label></div>';
                    // TODO - Add renderer for feedback weight select.
                    if (($i > 0) && $question->supports_feedback_scores()) {
                        // Add Input fields for weights per section.
                        if (isset($scorecalculationweights[$qid][$i]) && $scorecalculationweights[$qid][$i]) {
                            $output .= '<input type="number" style="width: 80px;" id="weight' . $qname . "_" . $i . '" ' .
                                'name="weight|' . $qid . '|' . $i . '" min="0.0" max="1.0" step="0.01" ' .
                                'value="'. $scorecalculationweights[$qid][$i] .'">';
                        } else {
                            $output .= '<input type="number" style="width: 80px;" id="weight' . $qname . "_" . $i . '" ' .
                                'name="weight|' . $qid . '|' . $i . '" min="0.0" max="1.0" step="0.01" value="0">';
                        }
                    }
                    // Now close div-Tag.
                    $output .= '</div>';
                    $questionnaire->page->add_to_page('formarea', $output);
                    if ($bg == 'c0') {
                        $bg = 'c1';
                    } else {
                        $bg = 'c0';
                    }
                }
                $questionnaire->page->add_to_page('formarea',
                    $questionnaire->renderer->question_output($question, $formdata, [], $n, true));
                $questionnaire->page->add_to_page('formarea', '</div>');
            } else if ($qtype == QUESSECTIONTEXT) {
                $questionnaire->page->add_to_page('formarea',
                    $questionnaire->renderer->question_output($question, $formdata, [], $n, true));
            }
        } else {
            $questionnaire->page->add_to_page('formarea', '<div class="notifyproblem">');
            $questionnaire->page->add_to_page('formarea', $strcannotuse);
            $questionnaire->page->add_to_page('formarea', '</div>');
            $questionnaire->page->add_to_page('formarea', '<div class="qn-question">' . $question->content . '</div>');
        }
    }
}

// Customized checkbox behavior
// section 0 selected? -> uncheck all other.
$strfunc = "\n<script>\n";
$strfunc .= ' function uncheck_boxes(name){
        var boxes = document.querySelectorAll("[name^=\'"+name+"_\']");
        for(var i=0;i<boxes.length; i++){
            if(boxes[i].name != name+"_0"){
                boxes[i].checked=false;
            }
        }
     }';
// Var boxes = document.querySelectorAll("[name^="+ name +"_"]); console.log(boxes);}';.
$strfunc .= "\n</script>\n";
$questionnaire->page->add_to_page('formarea', $strfunc);

// Submit/Cancel buttons.
$url = $CFG->wwwroot.'/mod/questionnaire/view.php?id='.$cm->id;
$questionnaire->page->add_to_page('formarea', '<div><input type="submit" name="savesettings" value="' .
    get_string('feedbackeditmessages', 'questionnaire').'" class="btn btn-primary" /></div>');
$questionnaire->page->add_to_page('formarea', $questionnaire->renderer->box_end());
echo $questionnaire->renderer->header();
echo $questionnaire->renderer->render($questionnaire->page);
echo $questionnaire->renderer->footer($course);