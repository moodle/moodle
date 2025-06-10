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
 * This page prints a particular instance of a flashcard
 *
 * @package mod_flashcard
 * @category mod
 * @author Gustav Delius
 * @author Valery Fremaux
 * @author Tomasz Muras
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
require('../../config.php');
require_once($CFG->dirroot.'/mod/flashcard/lib.php');
require_once($CFG->dirroot.'/mod/flashcard/locallib.php');

$PAGE->requires->js('/mod/flashcard/js/ufo/ufo.js', true);
$PAGE->requires->js('/mod/flashcard/js/module.js', false);
$PAGE->requires->css('/mod/flashcard/players/flowplayer/skin/minimalist.css');
$PAGE->requires->js('/mod/flashcard/players/flowplayer/flowplayer.js');

$id = optional_param('id', '', PARAM_INT);    // Course Module ID, or.
$f = optional_param('f', '', PARAM_INT);     // Flashcard ID.
$view = optional_param('view', 'checkdecks', PARAM_ACTION); // View.
$page = optional_param('page', '', PARAM_ACTION); // Page.
$action = optional_param('what', '', PARAM_ACTION); // Command.

$thisurl = new moodle_url('/mod/flashcard/view.php');
$params = array('id' => $id);
if (!empty($view)) {
    $params['view'] = $view;
}
if (!empty($page)) {
    $params['page'] = $page;
}
if (!empty($action)) {
    $params['what'] = $action;
}
$url = new moodle_url('/mod/flashcard/view.php', $params);

$PAGE->set_url($url);
if ($id) {
    if (! $cm = $DB->get_record('course_modules', array('id' => $id))) {
        print_error('invalidcoursemodule');
    }
    if (! $course = $DB->get_record('course', array('id' => $cm->course))) {
        print_error('coursemisconf');
    }
    if (! $flashcard = $DB->get_record('flashcard', array('id' => $cm->instance))) {
        print_error('errorinvalidflashcardid', 'flashcard');
    }
} else {
    if (! $flashcard = $DB->get_record('flashcard', array('id' => $f))) {
        print_error('invalidcoursemodule');
    }
    if (! $course = $DB->get_record('course', array('id' => $flashcard->course))) {
        print_error('coursemisconf');
    }
    if (! $cm = get_coursemodule_from_instance('flashcard', $flashcard->id, $course->id)) {
        print_error('errorinvalidflashcardid', 'flashcard');
    }
}
// M4
$PAGE->set_cm($cm);
$PAGE->set_activity_record($flashcard);

// Security.

require_course_login($course->id, true, $cm);
$context = context_module::instance($cm->id);

// Print the page header.

$strflashcards = get_string('modulenameplural', 'flashcard');
$strflashcard  = get_string('modulename', 'flashcard');
$PAGE->set_title("$course->shortname: $flashcard->name");
$PAGE->set_heading("$course->fullname");
$PAGE->navbar->add($strflashcards, new moodle_url('/mod/flashcard/index.php', array('id' => $course->id)));
$PAGE->navbar->add($flashcard->name);
$PAGE->set_focuscontrol('');
$PAGE->set_cacheable(true);

$renderer = $PAGE->get_renderer('mod_flashcard');

$out = $OUTPUT->header();

// Non visible trap for timerange (security).

if (!has_capability('moodle/course:viewhiddenactivities', $context) && !$cm->visible) {
    echo $out;
    echo $OUTPUT->notification(get_string('activityiscurrentlyhidden'));
    echo $OUTPUT->footer();
    die;
}

// Non manager trap for timerange.

if (!has_capability('mod/flashcard:manage', $context)) {
    $now = time();
    if ((($flashcard->starttime != 0) && ($now < $flashcard->starttime)) ||
            (($flashcard->endtime != 0) && ($now > $flashcard->endtime))) {
        echo $out;
        echo $OUTPUT->notification(get_string('outoftimerange', 'flashcard'));
        echo $OUTPUT->footer();
        die;
    }
}

// Loads "per instance" customisation styles.

if (!empty($flashcard->extracss)) {
    $out .= '<style>';
    $out .= $flashcard->extracss;
    $out .= '</style>';
}

// Determine the current tab.

switch ($view) {
    case 'checkdecks': {
        $currenttab = 'play';
        break;
    }

    case 'play': {
        $currenttab = 'play';
        break;
    }

    case 'freeplay': {
        $currenttab = 'freeplay';
        break;
    }

    case 'summary': {
        $currenttab = 'summary';
        break;
    }

    case 'edit': {
        $currenttab = 'edit';
        break;
    }

    case 'manage': {
        $currenttab = 'manage';
        break;
    }

    default:
        $currenttab = 'play';
}

if ($action == 'import') {
    $currenttab = 'import';
}

// Print tabs.
if (!preg_match("/summary|freeplay|play|checkdecks|manage|edit/", $view)) {
    $view = 'checkdecks';
}

if ($flashcard->models & FLASHCARD_MODEL_LEITNER) {
    $tabname = get_string('leitnergame', 'flashcard');
    $params = array('id' => $cm->id, 'view' => 'checkdecks');
    $taburl = new moodle_url('/mod/flashcard/view.php', $params);
    $row[] = new tabobject('play', $taburl, $tabname);
}

if ($flashcard->models & FLASHCARD_MODEL_FREEUSE) {
    $tabname = get_string('freegame', 'flashcard');
    $params = array('view' => 'freeplay', 'id' => $cm->id);
    $taburl = new moodle_url('/mod/flashcard/view.php', $params);
    $row[] = new tabobject('freeplay', $taburl, $tabname);
}

$tabrows[] = $row;

$activated = array();

// Print second line.

if ($view == 'edit') {
    $currenttab = 'manage';
} else if ($view == 'summary') {
    switch ($page) {
        case 'bycards': {
            $currenttab = 'bycards';
            $activated[] = 'summary';
            break;
        }

        default:
            $currenttab = 'byusers';
            $activated[] = 'summary';
    }

    $tabname = get_string('byusers', 'flashcard');
    $params = array('id' => $cm->id, 'view' => 'summary', 'page' => 'byusers');
    $taburl = new moodle_url('/mod/flashcard/view.php', $params);
    $row1[] = new tabobject('byusers', $taburl, $tabname);

    $tabname = get_string('bycards', 'flashcard');
    $params = array('id' => $cm->id, 'view' => 'summary', 'page' => 'bycards');
    $taburl = new moodle_url('/mod/flashcard/view.php', $params);
    $row1[] = new tabobject('bycards', $taburl, $tabname);

    $tabrows[] = $row1;
}

$out .= print_tabs($tabrows, $currenttab, null, $activated, true);

// Print active view.

// Trigger module viewed event.
$eventparams = array(
    'objectid' => $flashcard->id,
    'context' => $context,
);

switch ($view) {
    case 'summary': {
        if (!has_capability('mod/flashcard:manage', $context)) {
            $params = array('view' => 'checkdecks', 'id' => $cm->id);
            redirect(new moodle_url('/mod/flashcard/view.php', $params));
        }
        $event = \mod_flashcard\event\course_module_viewed_summary::create($eventparams);
        if ($page == 'bycards') {
            include($CFG->dirroot.'/mod/flashcard/cardsummaryview.php');
        } else {
            include($CFG->dirroot.'/mod/flashcard/usersummaryview.php');
        }
        break;
    }

    case 'manage': {
        if (!has_capability('mod/flashcard:manage', $context)) {
            $params = array('view' => 'checkdecks', 'id' => $cm->id);
            redirect(new moodle_url('/mod/flashcard/view.php', $params));
        }
        $event = \mod_flashcard\event\course_module_managed::create($eventparams);
        include($CFG->dirroot.'/mod/flashcard/managecards.php');
        break;
    }

    case 'edit': {
        if (!has_capability('mod/flashcard:manage', $context)) {
            redirect($thisurl."?view=checkdecks&amp;id={$cm->id}");
        }
        $event = \mod_flashcard\event\course_module_edited::create($eventparams);
        include($CFG->dirroot.'/mod/flashcard/editview.php');
        break;
    }

    case 'freeplay': {
        $event = \mod_flashcard\event\course_module_freeplayed::create($eventparams);
        include($CFG->dirroot.'/mod/flashcard/freeplayview.php');
        break;
    }

    case 'play': {
        $event = \mod_flashcard\event\course_module_played::create($eventparams);
        include($CFG->dirroot.'/mod/flashcard/playview.php');
        break;
    }

    default:
        $event = \mod_flashcard\event\course_module_viewed::create($eventparams);
        include($CFG->dirroot.'/mod/flashcard/checkview.php');
}

if ($course->format == 'page') {
    include_once($CFG->dirroot.'/course/format/page/xlib.php');
    echo '<center>';
    page_print_page_format_navigation($cm, true);
    echo '</center>';
} else {
    /*
    // Nav principle obsolete in moodle 4
    if ($COURSE->format != 'singleactivity') {
        $buttonurl = new moodle_url('/course/view.php', array('id' => $course->id));
        $label = get_string('backtocourse', 'flashcard');
        echo '<center>';
        echo $OUTPUT->single_button($buttonurl, $label, 'post', array('class' => 'flashcard-backtocourse'));
        echo '</center>';
    }
    */
}

$event->add_record_snapshot('course_modules', $cm);
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('flashcard', $flashcard);
$event->trigger();



// Finish the page.

echo $OUTPUT->footer($course);
