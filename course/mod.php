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
 * Moves, adds, updates, duplicates or deletes modules in a course
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package course
 */

require("../config.php");
require_once("lib.php");

$sectionreturn = optional_param('sr', null, PARAM_INT);
$add           = optional_param('add', '', PARAM_ALPHA);
$type          = optional_param('type', '', PARAM_ALPHA);
$indent        = optional_param('indent', 0, PARAM_INT);
$update        = optional_param('update', 0, PARAM_INT);
$duplicate     = optional_param('duplicate', 0, PARAM_INT);
$hide          = optional_param('hide', 0, PARAM_INT);
$show          = optional_param('show', 0, PARAM_INT);
$copy          = optional_param('copy', 0, PARAM_INT);
$moveto        = optional_param('moveto', 0, PARAM_INT);
$movetosection = optional_param('movetosection', 0, PARAM_INT);
$delete        = optional_param('delete', 0, PARAM_INT);
$course        = optional_param('course', 0, PARAM_INT);
$groupmode     = optional_param('groupmode', -1, PARAM_INT);
$cancelcopy    = optional_param('cancelcopy', 0, PARAM_BOOL);
$confirm       = optional_param('confirm', 0, PARAM_BOOL);

// This page should always redirect
$url = new moodle_url('/course/mod.php');
foreach (compact('indent','update','hide','show','copy','moveto','movetosection','delete','course','cancelcopy','confirm') as $key=>$value) {
    if ($value !== 0) {
        $url->param($key, $value);
    }
}
$url->param('sr', $sectionreturn);
if ($add !== '') {
    $url->param('add', $add);
}
if ($type !== '') {
    $url->param('type', $type);
}
if ($groupmode !== '') {
    $url->param('groupmode', $groupmode);
}
$PAGE->set_url($url);

require_login();

//check if we are adding / editing a module that has new forms using formslib
if (!empty($add)) {
    $id          = required_param('id', PARAM_INT);
    $section     = required_param('section', PARAM_INT);
    $type        = optional_param('type', '', PARAM_ALPHA);
    $returntomod = optional_param('return', 0, PARAM_BOOL);

    redirect("$CFG->wwwroot/course/modedit.php?add=$add&type=$type&course=$id&section=$section&return=$returntomod&sr=$sectionreturn");

} else if (!empty($update)) {
    $cm = get_coursemodule_from_id('', $update, 0, true, MUST_EXIST);
    $returntomod = optional_param('return', 0, PARAM_BOOL);
    redirect("$CFG->wwwroot/course/modedit.php?update=$update&return=$returntomod&sr=$sectionreturn");

} else if (!empty($duplicate) and confirm_sesskey()) {
     $cm     = get_coursemodule_from_id('', $duplicate, 0, true, MUST_EXIST);
     $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

    require_login($course, false, $cm);
    $modcontext = context_module::instance($cm->id);
    require_capability('moodle/course:manageactivities', $modcontext);

     // Duplicate the module.
     $newcm = duplicate_module($course, $cm);
     redirect(course_get_url($course, $cm->sectionnum, array('sr' => $sectionreturn)));

} else if (!empty($delete)) {
    $cm     = get_coursemodule_from_id('', $delete, 0, true, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

    require_login($course, false, $cm);
    $modcontext = context_module::instance($cm->id);
    require_capability('moodle/course:manageactivities', $modcontext);

    $return = course_get_url($course, $cm->sectionnum, array('sr' => $sectionreturn));

    if (!$confirm or !confirm_sesskey()) {
        $fullmodulename = get_string('modulename', $cm->modname);

        $optionsyes = array('confirm'=>1, 'delete'=>$cm->id, 'sesskey'=>sesskey(), 'sr' => $sectionreturn);

        $strdeletecheck = get_string('deletecheck', '', $fullmodulename);
        $strdeletecheckfull = get_string('deletecheckfull', '', "$fullmodulename '$cm->name'");

        $PAGE->set_pagetype('mod-' . $cm->modname . '-delete');
        $PAGE->set_title($strdeletecheck);
        $PAGE->set_heading($course->fullname);
        $PAGE->navbar->add($strdeletecheck);

        echo $OUTPUT->header();
        echo $OUTPUT->box_start('noticebox');
        $formcontinue = new single_button(new moodle_url("$CFG->wwwroot/course/mod.php", $optionsyes), get_string('yes'));
        $formcancel = new single_button($return, get_string('no'), 'get');
        echo $OUTPUT->confirm($strdeletecheckfull, $formcontinue, $formcancel);
        echo $OUTPUT->box_end();
        echo $OUTPUT->footer();

        exit;
    }

    // Delete the module.
    course_delete_module($cm->id);

    redirect($return);
}


if ((!empty($movetosection) or !empty($moveto)) and confirm_sesskey()) {
    $cm     = get_coursemodule_from_id('', $USER->activitycopy, 0, true, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

    require_login($course, false, $cm);
    $coursecontext = context_course::instance($course->id);
    $modcontext = context_module::instance($cm->id);
    require_capability('moodle/course:manageactivities', $modcontext);

    if (!empty($movetosection)) {
        if (!$section = $DB->get_record('course_sections', array('id'=>$movetosection, 'course'=>$cm->course))) {
            print_error('sectionnotexist');
        }
        $beforecm = NULL;

    } else {                      // normal moveto
        if (!$beforecm = get_coursemodule_from_id('', $moveto, $cm->course, true)) {
            print_error('invalidcoursemodule');
        }
        if (!$section = $DB->get_record('course_sections', array('id'=>$beforecm->section, 'course'=>$cm->course))) {
            print_error('sectionnotexist');
        }
    }

    if (!ismoving($section->course)) {
        print_error('needcopy', '', "view.php?id=$section->course");
    }

    moveto_module($cm, $section, $beforecm);

    $sectionreturn = $USER->activitycopysectionreturn;
    unset($USER->activitycopy);
    unset($USER->activitycopycourse);
    unset($USER->activitycopyname);
    unset($USER->activitycopysectionreturn);

    redirect(course_get_url($course, $section->section, array('sr' => $sectionreturn)));

} else if (!empty($indent) and confirm_sesskey()) {
    $id = required_param('id', PARAM_INT);

    $cm     = get_coursemodule_from_id('', $id, 0, true, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

    require_login($course, false, $cm);
    $coursecontext = context_course::instance($course->id);
    $modcontext = context_module::instance($cm->id);
    require_capability('moodle/course:manageactivities', $modcontext);

    $cm->indent += $indent;

    if ($cm->indent < 0) {
        $cm->indent = 0;
    }

    $DB->set_field('course_modules', 'indent', $cm->indent, array('id'=>$cm->id));

    rebuild_course_cache($cm->course);

    redirect(course_get_url($course, $cm->sectionnum, array('sr' => $sectionreturn)));

} else if (!empty($hide) and confirm_sesskey()) {
    $cm     = get_coursemodule_from_id('', $hide, 0, true, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

    require_login($course, false, $cm);
    $coursecontext = context_course::instance($course->id);
    $modcontext = context_module::instance($cm->id);
    require_capability('moodle/course:activityvisibility', $modcontext);

    set_coursemodule_visible($cm->id, 0);
    \core\event\course_module_updated::create_from_cm($cm, $modcontext)->trigger();
    redirect(course_get_url($course, $cm->sectionnum, array('sr' => $sectionreturn)));

} else if (!empty($show) and confirm_sesskey()) {
    $cm     = get_coursemodule_from_id('', $show, 0, true, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

    require_login($course, false, $cm);
    $coursecontext = context_course::instance($course->id);
    $modcontext = context_module::instance($cm->id);
    require_capability('moodle/course:activityvisibility', $modcontext);

    $section = $DB->get_record('course_sections', array('id'=>$cm->section), '*', MUST_EXIST);

    $module = $DB->get_record('modules', array('id'=>$cm->module), '*', MUST_EXIST);

    if ($module->visible and ($section->visible or (SITEID == $cm->course))) {
        set_coursemodule_visible($cm->id, 1);
        \core\event\course_module_updated::create_from_cm($cm, $modcontext)->trigger();
    }

    redirect(course_get_url($course, $section->section, array('sr' => $sectionreturn)));

} else if ($groupmode > -1 and confirm_sesskey()) {
    $id = required_param('id', PARAM_INT);

    $cm     = get_coursemodule_from_id('', $id, 0, true, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

    require_login($course, false, $cm);
    $coursecontext = context_course::instance($course->id);
    $modcontext = context_module::instance($cm->id);
    require_capability('moodle/course:manageactivities', $modcontext);

    set_coursemodule_groupmode($cm->id, $groupmode);
    \core\event\course_module_updated::create_from_cm($cm, $modcontext)->trigger();
    redirect(course_get_url($course, $cm->sectionnum, array('sr' => $sectionreturn)));

} else if (!empty($copy) and confirm_sesskey()) { // value = course module
    $cm     = get_coursemodule_from_id('', $copy, 0, true, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

    require_login($course, false, $cm);
    $coursecontext = context_course::instance($course->id);
    $modcontext = context_module::instance($cm->id);
    require_capability('moodle/course:manageactivities', $modcontext);

    $section = $DB->get_record('course_sections', array('id'=>$cm->section), '*', MUST_EXIST);

    $USER->activitycopy              = $copy;
    $USER->activitycopycourse        = $cm->course;
    $USER->activitycopyname          = $cm->name;
    $USER->activitycopysectionreturn = $sectionreturn;

    redirect(course_get_url($course, $section->section, array('sr' => $sectionreturn)));

} else if (!empty($cancelcopy) and confirm_sesskey()) { // value = course module

    $courseid = $USER->activitycopycourse;
    $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);

    $cm     = get_coursemodule_from_id('', $USER->activitycopy, 0, true, IGNORE_MISSING);
    $sectionreturn = $USER->activitycopysectionreturn;
    unset($USER->activitycopy);
    unset($USER->activitycopycourse);
    unset($USER->activitycopyname);
    unset($USER->activitycopysectionreturn);
    redirect(course_get_url($course, $cm->sectionnum, array('sr' => $sectionreturn)));
} else {
    print_error('unknowaction');
}
