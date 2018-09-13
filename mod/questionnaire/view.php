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
require_once($CFG->dirroot.'/mod/questionnaire/locallib.php');
require_once($CFG->libdir . '/completionlib.php');
require_once($CFG->dirroot.'/mod/questionnaire/questionnaire.class.php');

if (!isset($SESSION->questionnaire)) {
    $SESSION->questionnaire = new stdClass();
}
$SESSION->questionnaire->current_tab = 'view';

$id = optional_param('id', null, PARAM_INT);    // Course Module ID.
$a = optional_param('a', null, PARAM_INT);      // Or questionnaire ID.

$sid = optional_param('sid', null, PARAM_INT);  // Survey id.

list($cm, $course, $questionnaire) = questionnaire_get_standard_page_items($id, $a);

// Check login and get context.
require_course_login($course, true, $cm);
$context = context_module::instance($cm->id);

$url = new moodle_url($CFG->wwwroot.'/mod/questionnaire/view.php');
if (isset($id)) {
    $url->param('id', $id);
} else {
    $url->param('a', $a);
}
if (isset($sid)) {
    $url->param('sid', $sid);
}

$PAGE->set_url($url);
$PAGE->set_context($context);
$questionnaire = new questionnaire(0, $questionnaire, $course, $cm);
// Add renderer and page objects to the questionnaire object for display use.
$questionnaire->add_renderer($PAGE->get_renderer('mod_questionnaire'));
$questionnaire->add_page(new \mod_questionnaire\output\viewpage());

$PAGE->set_title(format_string($questionnaire->name));
$PAGE->set_heading(format_string($course->fullname));

echo $questionnaire->renderer->header();
$questionnaire->page->add_to_page('questionnairename', format_string($questionnaire->name));

// Print the main part of the page.
if ($questionnaire->intro) {
    $questionnaire->page->add_to_page('intro', format_module_intro('questionnaire', $questionnaire, $cm->id));
}

$cm = $questionnaire->cm;
$currentgroupid = groups_get_activity_group($cm);
if (!groups_is_member($currentgroupid, $USER->id)) {
    $currentgroupid = 0;
}

if (!$questionnaire->is_active()) {
    if ($questionnaire->capabilities->manage) {
        $msg = 'removenotinuse';
    } else {
        $msg = 'notavail';
    }
    $questionnaire->page->add_to_page('message', get_string($msg, 'questionnaire'));

} else if ($questionnaire->survey->realm == 'template') {
    // If this is a template survey, notify and exit.
    $questionnaire->page->add_to_page('message', get_string('templatenotviewable', 'questionnaire'));
    echo $questionnaire->renderer->render($questionnaire->page);
    echo $questionnaire->renderer->footer($questionnaire->course);
    exit();

} else if (!$questionnaire->is_open()) {
    $questionnaire->page->add_to_page('message', get_string('notopen', 'questionnaire', userdate($questionnaire->opendate)));

} else if ($questionnaire->is_closed()) {
    $questionnaire->page->add_to_page('message', get_string('closed', 'questionnaire', userdate($questionnaire->closedate)));

} else if (!$questionnaire->user_is_eligible($USER->id)) {
    if ($questionnaire->questions) {
        $questionnaire->page->add_to_page('message', get_string('noteligible', 'questionnaire'));
    }

} else if (!$questionnaire->user_can_take($USER->id)) {
    switch ($questionnaire->qtype) {
        case QUESTIONNAIREDAILY:
            $msgstring = ' '.get_string('today', 'questionnaire');
            break;
        case QUESTIONNAIREWEEKLY:
            $msgstring = ' '.get_string('thisweek', 'questionnaire');
            break;
        case QUESTIONNAIREMONTHLY:
            $msgstring = ' '.get_string('thismonth', 'questionnaire');
            break;
        default:
            $msgstring = '';
            break;
    }
    $questionnaire->page->add_to_page('message', get_string("alreadyfilled", "questionnaire", $msgstring));

} else if ($questionnaire->user_can_take($USER->id)) {
    if ($questionnaire->questions) { // Sanity check.
        if (!$questionnaire->user_has_saved_response($USER->id)) {
            $questionnaire->page->add_to_page('complete',
                '<a href="'.$CFG->wwwroot.htmlspecialchars('/mod/questionnaire/complete.php?' .
                'id='.$questionnaire->cm->id).'">'.get_string('answerquestions', 'questionnaire').'</a>');
        } else {
            $questionnaire->page->add_to_page('complete',
                '<a href="'.$CFG->wwwroot.htmlspecialchars('/mod/questionnaire/complete.php?' .
                'id='.$questionnaire->cm->id.'&resume=1').'">'.get_string('resumesurvey', 'questionnaire').'</a>');
        }
    } else {
        $questionnaire->page->add_to_page('message', get_string('noneinuse', 'questionnaire'));
    }
}

if ($questionnaire->capabilities->editquestions && !$questionnaire->questions && $questionnaire->is_active()) {
    $questionnaire->page->add_to_page('complete',
        '<a href="'.$CFG->wwwroot.htmlspecialchars('/mod/questionnaire/questions.php?'.
        'id='.$questionnaire->cm->id).'">'.'<strong>'.get_string('addquestions', 'questionnaire').'</strong></a>');
}

if (isguestuser()) {
    $guestno = html_writer::tag('p', get_string('noteligible', 'questionnaire'));
    $liketologin = html_writer::tag('p', get_string('liketologin'));
    $questionnaire->page->add_to_page('guestuser',
        $questionnaire->renderer->confirm($guestno."\n\n".$liketologin."\n", get_login_url(), get_local_referer(false)));
}

// Log this course module view.
// Needed for the event logging.
$context = context_module::instance($questionnaire->cm->id);
$anonymous = $questionnaire->respondenttype == 'anonymous';

$event = \mod_questionnaire\event\course_module_viewed::create(array(
                'objectid' => $questionnaire->id,
                'anonymous' => $anonymous,
                'context' => $context
));
$event->trigger();

$usernumresp = $questionnaire->count_submissions($USER->id);

if ($questionnaire->capabilities->readownresponses && ($usernumresp > 0)) {
    $argstr = 'instance='.$questionnaire->id.'&user='.$USER->id;
    if ($usernumresp > 1) {
        $titletext = get_string('viewyourresponses', 'questionnaire', $usernumresp);
    } else {
        $titletext = get_string('yourresponse', 'questionnaire');
        $argstr .= '&byresponse=1&action=vresp';
    }
    $questionnaire->page->add_to_page('yourresponse',
        '<a href="'.$CFG->wwwroot.htmlspecialchars('/mod/questionnaire/myreport.php?'.$argstr).'">'.$titletext.'</a>');
}

if ($questionnaire->can_view_all_responses($usernumresp)) {
    $argstr = 'instance='.$questionnaire->id.'&group='.$currentgroupid;
    $questionnaire->page->add_to_page('allresponses',
        '<a href="'.$CFG->wwwroot.htmlspecialchars('/mod/questionnaire/report.php?'.$argstr).'">'.
        get_string('viewallresponses', 'questionnaire').'</a>');
}

echo $questionnaire->renderer->render($questionnaire->page);
echo $questionnaire->renderer->footer();
