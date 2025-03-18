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
* Adds or updates modules in a course using new formslib
*
* @package    moodlecore
* @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
* @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

require_once("../config.php");
require_once("lib.php");
require_once($CFG->libdir.'/filelib.php');
require_once($CFG->libdir.'/gradelib.php');
require_once($CFG->libdir.'/completionlib.php');
require_once($CFG->libdir.'/plagiarismlib.php');
require_once($CFG->dirroot . '/course/modlib.php');

$add    = optional_param('add', '', PARAM_ALPHANUM);     // Module name.
$update = optional_param('update', 0, PARAM_INT);
$return = optional_param('return', 0, PARAM_BOOL);    //return to course/view.php if false or mod/modname/view.php if true
$type   = optional_param('type', '', PARAM_ALPHANUM); //TODO: hopefully will be removed in 2.0
$sectionreturn = optional_param('sr', null, PARAM_INT);
$beforemod = optional_param('beforemod', 0, PARAM_INT);
$showonly = optional_param('showonly', '', PARAM_TAGLIST); // Settings group to show expanded and hide the rest.

// Force it to be null if it's not a valid section number.
if ($sectionreturn < 0) {
    $sectionreturn = null;
}

$url = new moodle_url('/course/modedit.php');
if (!is_null($sectionreturn)) {
    $url->param('sr', $sectionreturn);
}
if (!empty($return)) {
    $url->param('return', $return);
}
if (!empty($showonly)) {
    $url->param('showonly', $showonly);
}

if (!empty($add)) {
    $section = required_param('section', PARAM_INT);
    $course  = required_param('course', PARAM_INT);

    $url->param('add', $add);
    $url->param('section', $section);
    $url->param('course', $course);
    $PAGE->set_url($url);

    $course = $DB->get_record('course', array('id'=>$course), '*', MUST_EXIST);
    require_login($course);

    // There is no page for this in the navigation. The closest we'll have is the course section.
    // If the course section isn't displayed on the navigation this will fall back to the course which
    // will be the closest match we have.
    navigation_node::override_active_url(course_get_url($course, $section));

    // MDL-69431 Validate that $section (url param) does not exceed the maximum for this course / format.
    // If too high (e.g. section *id* not number) non-sequential sections inserted in course_sections table.
    // Then on import, backup fills 'gap' with empty sections (see restore_rebuild_course_cache). Avoid this.
    $courseformat = course_get_format($course);
    $maxsections = $courseformat->get_max_sections();
    if ($section > $maxsections) {
        throw new \moodle_exception('maxsectionslimit', 'moodle', '', $maxsections);
    }

    list($module, $context, $cw, $cm, $data) = prepare_new_moduleinfo_data($course, $add, $section);
    $data->return = 0;
    if (!is_null($sectionreturn)) {
        $data->sr = $sectionreturn;
    }
    $data->add = $add;
    $data->beforemod = $beforemod;
    if (!empty($type)) { //TODO: hopefully will be removed in 2.0
        $data->type = $type;
    }

    $sectionname = get_section_name($course, $cw);
    $fullmodulename = get_string('modulename', $module->name);
    $pageheading = $pagetitle = get_string('addinganew', 'moodle', $fullmodulename);
    $navbaraddition = $pageheading;

} else if (!empty($update)) {

    $url->param('update', $update);
    $PAGE->set_url($url);

    // Select the "Edit settings" from navigation.
    navigation_node::override_active_url(new moodle_url('/course/modedit.php', array('update'=>$update, 'return'=>1)));

    // Check the course module exists.
    $cm = get_coursemodule_from_id('', $update, 0, false, MUST_EXIST);

    // Check the course exists.
    $course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);

    // require_login
    require_login($course, false, $cm); // needed to setup proper $COURSE

    list($cm, $context, $module, $data, $cw) = get_moduleinfo_data($cm, $course);
    $data->return = $return;
    if (!is_null($sectionreturn)) {
        $data->sr = $sectionreturn;
    }
    $data->update = $update;
    if (!empty($showonly)) {
        $data->showonly = $showonly;
    }

    $sectionname = get_section_name($course, $cw);
    $fullmodulename = get_string('modulename', $module->name);
    $pageheading = get_string('editsettings', 'moodle');
    $pagetitle = get_string('edita', 'moodle', $fullmodulename) . ': ' . $cm->name;
    $navbaraddition = null;

} else {
    require_login();
    throw new \moodle_exception('invalidaction');
}

$pagepath = 'mod-' . $module->name . '-';
if (!empty($type)) { //TODO: hopefully will be removed in 2.0
    $pagepath .= $type;
} else {
    $pagepath .= 'mod';
}
$PAGE->set_pagetype($pagepath);
$PAGE->set_pagelayout('admin');
$PAGE->add_body_class('limitedwidth');


$modmoodleform = "$CFG->dirroot/mod/$module->name/mod_form.php";
if (file_exists($modmoodleform)) {
    require_once($modmoodleform);
} else {
    throw new \moodle_exception('noformdesc');
}

$mformclassname = 'mod_'.$module->name.'_mod_form';
$mform = new $mformclassname($data, $cw->section, $cm, $course);
$mform->set_data($data);
if (!empty($showonly)) {
    $mform->filter_shown_headers(explode(',', $showonly));
}

if ($mform->is_cancelled()) {
    if ($return && !empty($cm->id)) {
        $urlparams = [
            'id' => $cm->id, // We always need the activity id.
            'forceview' => 1, // Stop file downloads in resources.
        ];
        $activityurl = new moodle_url("/mod/$module->name/view.php", $urlparams);
        redirect($activityurl);
    } else if (plugin_supports('mod', $module->name, FEATURE_PUBLISHES_QUESTIONS)) {
        redirect(\core_question\local\bank\question_bank_helper::get_url_for_qbank_list($course->id));
    } else {
        $options = [];
        if (!is_null($sectionreturn)) {
            $options['sr'] = $sectionreturn;
        }
        redirect(course_get_url($course, $cw->section, $options));
    }
} else if ($fromform = $mform->get_data()) {
    // Mark that this is happening in the front-end UI. This is used to indicate that we are able to
    // do regrading with a progress bar and redirect, if necessary.
    $fromform->frontend = true;
    if (!empty($fromform->update)) {
        list($cm, $fromform) = update_moduleinfo($cm, $fromform, $course, $mform);
    } else if (!empty($fromform->add)) {
        $fromform = add_moduleinfo($fromform, $course, $mform);
    } else {
        throw new \moodle_exception('invaliddata');
    }

    if (isset($fromform->submitbutton)) {
        $url = new moodle_url("/mod/$module->name/view.php", array('id' => $fromform->coursemodule, 'forceview' => 1));
        if (!empty($fromform->showgradingmanagement)) {
            $url = $fromform->gradingman->get_management_url($url);
        }
    } else if (plugin_supports('mod', $fromform->modulename, FEATURE_PUBLISHES_QUESTIONS)) {
        $url = \core_question\local\bank\question_bank_helper::get_url_for_qbank_list($course->id);
    } else {
        $options = [];
        if (!is_null($sectionreturn)) {
            $options['sr'] = $sectionreturn;
        }
        $url = course_get_url($course, $cw->section, $options);
    }

    redirect($url);
    exit;

} else {
    if (!empty($cm->id)) {
        $context = context_module::instance($cm->id);
    } else {
        $context = context_course::instance($course->id);
    }

    $PAGE->set_heading($course->fullname);
    if ($course->id !== $SITE->id) {
        $pagetitle = $pagetitle . moodle_page::TITLE_SEPARATOR . $course->shortname;
    }
    $PAGE->set_title($pagetitle);
    $PAGE->set_cacheable(false);

    if (isset($navbaraddition)) {
        $PAGE->navbar->add($navbaraddition);
    }
    $PAGE->activityheader->disable();

    echo $OUTPUT->header();
    echo $OUTPUT->heading_with_help($pageheading, '', $module->name);

    $mform->display();

    echo $OUTPUT->footer();
}
