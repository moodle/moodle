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

use core_courseformat\formatactions;

require("../config.php");
require_once("lib.php");

$sectionreturn = optional_param('sr', null, PARAM_INT);
$add           = optional_param('add', '', PARAM_ALPHANUM);
$type          = optional_param('type', '', PARAM_ALPHA);
$indent        = optional_param('indent', 0, PARAM_INT); // TODO remove this param as part of MDL-83530.
$update        = optional_param('update', 0, PARAM_INT);
$duplicate     = optional_param('duplicate', 0, PARAM_INT); // TODO remove this param as part of MDL-83530.
$hide          = optional_param('hide', 0, PARAM_INT); // TODO remove this param as part of MDL-83530.
$stealth       = optional_param('stealth', 0, PARAM_INT); // TODO remove this param as part of MDL-83530.
$show          = optional_param('show', 0, PARAM_INT); // TODO remove this param as part of MDL-83530.
$copy          = optional_param('copy', 0, PARAM_INT); // TODO remove this param as part of MDL-83530.
$moveto        = optional_param('moveto', 0, PARAM_INT); // TODO remove this param as part of MDL-83530.
$movetosection = optional_param('movetosection', 0, PARAM_INT); // TODO remove this param as part of MDL-83530.
$delete        = optional_param('delete', 0, PARAM_INT); // TODO remove this param as part of MDL-83530.
$course        = optional_param('course', 0, PARAM_INT);
$groupmode     = optional_param('groupmode', -1, PARAM_INT); // TODO remove this param as part of MDL-83530.
$cancelcopy    = optional_param('cancelcopy', 0, PARAM_BOOL); // TODO remove this param as part of MDL-83530.
$confirm       = optional_param('confirm', 0, PARAM_BOOL); // TODO remove this param as part of MDL-83530.

// This page should always redirect
$url = new moodle_url('/course/mod.php');
foreach (compact('indent','update','hide','show','copy','moveto','movetosection','delete','course','cancelcopy','confirm') as $key=>$value) {
    if ($value !== 0) {
        $url->param($key, $value);
    }
}
// Force it to be null if it's not a valid section number.
if ($sectionreturn < 0) {
    $sectionreturn = null;
}
$urloptions = [];
if (!is_null($sectionreturn)) {
    $url->param('sr', $sectionreturn);
    $urloptions['sr'] = $sectionreturn;
}
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
    $sectionid   = optional_param('sectionid', null, PARAM_INT);
    $type        = optional_param('type', '', PARAM_ALPHA);
    $returntomod = optional_param('return', 0, PARAM_BOOL);
    $beforemod   = optional_param('beforemod', 0, PARAM_INT);

    if (empty($sectionid)) {
        $section = required_param('section', PARAM_INT);
        $sectioninfo = get_fast_modinfo($id)->get_section_info($section);
        $sectionid = $sectioninfo?->id;
    }

    $params = [
        'add' => $add,
        'type' => $type,
        'course' => $id,
        'sectionid' => $sectionid,
        'return' => $returntomod,
        'beforemod' => $beforemod,
    ];
    if (!is_null($sectionreturn)) {
        $params['sr'] = $sectionreturn;
    }

    redirect(
        new moodle_url(
            '/course/modedit.php',
            $params,
        )
    );

} else if (!empty($update)) {
    $cm = get_coursemodule_from_id('', $update, 0, true, MUST_EXIST);
    $returntomod = optional_param('return', 0, PARAM_BOOL);

    $params = [
        'update' => $update,
        'return' => $returntomod,
    ];
    if (!is_null($sectionreturn)) {
        $params['sr'] = $sectionreturn;
    }
    redirect(
        new moodle_url(
            '/course/modedit.php',
            $params,
        )
    );
} else if (!empty($duplicate) and confirm_sesskey()) {
    // TODO remove this else if as part of MDL-83530.
    debugging(
        'The duplicate param is deprecated. Please use action cm_duplicate in course/format/update.php instead.',
        DEBUG_DEVELOPER
    );
     $cm     = get_coursemodule_from_id('', $duplicate, 0, true, MUST_EXIST);
     $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

    require_login($course, false, $cm);
    $coursecontext = context_course::instance($course->id);
    require_all_capabilities(['moodle/backup:backuptargetimport', 'moodle/restore:restoretargetimport'], $coursecontext);

    // Duplicate the module.
    $newcm = duplicate_module($course, $cm);
    redirect(course_get_url($course, $cm->sectionnum, $urloptions));

} else if (!empty($delete)) {
    // TODO remove this else if as part of MDL-83530.
    debugging(
        'The delete param is deprecated. Please use action cm_delete in course/format/update.php instead.',
        DEBUG_DEVELOPER
    );
    $cm     = get_coursemodule_from_id('', $delete, 0, true, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

    require_login($course, false, $cm);
    $modcontext = context_module::instance($cm->id);
    require_capability('moodle/course:manageactivities', $modcontext);

    if (plugin_supports('mod', $cm->modname, FEATURE_PUBLISHES_QUESTIONS)) {
        $return = \core_question\local\bank\question_bank_helper::get_url_for_qbank_list($course->id);
    } else {
        $return = course_get_url($course, $cm->sectionnum, $urloptions);
    }

    if (!$confirm or !confirm_sesskey()) {
        $fullmodulename = get_string('modulename', $cm->modname);

        $optionsyes = [
            'confirm' => 1,
            'delete' => $cm->id,
            'sesskey' => sesskey(),
        ];
        if (!is_null($sectionreturn)) {
            $optionsyes['sr'] = $sectionreturn;
        }
        $strdeletecheck = get_string('deletecheck', '', $fullmodulename);
        $strparams = (object)array('type' => $fullmodulename, 'name' => $cm->name);
        $strdeletechecktypename = get_string('deletechecktypename', '', $strparams);

        $PAGE->set_pagetype('mod-' . $cm->modname . '-delete');
        $PAGE->set_title($strdeletecheck);
        $PAGE->set_heading($course->fullname);
        $PAGE->navbar->add($strdeletecheck);

        echo $OUTPUT->header();
        echo $OUTPUT->box_start('noticebox');
        $formcontinue = new single_button(new moodle_url("$CFG->wwwroot/course/mod.php", $optionsyes), get_string('yes'));
        $formcancel = new single_button($return, get_string('no'), 'get');
        echo $OUTPUT->confirm($strdeletechecktypename, $formcontinue, $formcancel);
        echo $OUTPUT->box_end();
        echo $OUTPUT->footer();

        exit;
    }

    // Delete the module.
    \core_courseformat\formatactions::cm($course->id)->delete($cm->id);

    redirect($return);
}


if ((!empty($movetosection) or !empty($moveto)) and confirm_sesskey()) {
    // TODO remove this if as part of MDL-83530.
    debugging(
        'The moveto param is deprecated. Please use the standard move modal instead.',
        DEBUG_DEVELOPER
    );
    $cm     = get_coursemodule_from_id('', $USER->activitycopy, 0, true, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

    require_login($course, false, $cm);
    $coursecontext = context_course::instance($course->id);
    $modcontext = context_module::instance($cm->id);
    require_capability('moodle/course:manageactivities', $modcontext);

    if (!empty($movetosection)) {
        if (!$section = $DB->get_record('course_sections', array('id'=>$movetosection, 'course'=>$cm->course))) {
            throw new \moodle_exception('sectionnotexist');
        }
        $beforecm = NULL;

    } else {                      // normal moveto
        if (!$beforecm = get_coursemodule_from_id('', $moveto, $cm->course, true)) {
            throw new \moodle_exception('invalidcoursemodule');
        }
        if (!$section = $DB->get_record('course_sections', array('id'=>$beforecm->section, 'course'=>$cm->course))) {
            throw new \moodle_exception('sectionnotexist');
        }
    }

    if (!ismoving($section->course)) {
        throw new \moodle_exception('needcopy', '', "view.php?id=$section->course");
    }

    moveto_module($cm, $section, $beforecm);

    $sectionreturn = $USER->activitycopysectionreturn;
    unset($USER->activitycopy);
    unset($USER->activitycopycourse);
    unset($USER->activitycopyname);
    unset($USER->activitycopysectionreturn);

    redirect(course_get_url($course, $section->section, $urloptions));

} else if (!empty($indent) and confirm_sesskey()) {
    // TODO remove this else if as part of MDL-83530.
    debugging(
        'The indent param deprecated. Please use action cm_moveleft and cm_moveright in course/format/update.php instead.',
        DEBUG_DEVELOPER
    );
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

    \course_modinfo::purge_course_module_cache($cm->course, $cm->id);
    // Rebuild invalidated module cache.
    rebuild_course_cache($cm->course, false, true);

    redirect(course_get_url($course, $cm->sectionnum, $urloptions));

} else if (!empty($hide) and confirm_sesskey()) {
    // TODO remove this else if as part of MDL-83530.
    debugging(
        'The hide param deprecated. Please use action cm_hide in course/format/update.php instead.',
        DEBUG_DEVELOPER
    );
    $cm     = get_coursemodule_from_id('', $hide, 0, true, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

    require_login($course, false, $cm);
    $coursecontext = context_course::instance($course->id);
    $modcontext = context_module::instance($cm->id);
    require_capability('moodle/course:activityvisibility', $modcontext);

    if (set_coursemodule_visible($cm->id, 0)) {
        \core\event\course_module_updated::create_from_cm($cm, $modcontext)->trigger();
    }
    redirect(course_get_url($course, $cm->sectionnum, $urloptions));

} else if (!empty($stealth) and confirm_sesskey()) {
    // TODO remove this else if as part of MDL-83530.
    debugging(
        'The stealth param deprecated. Please use action cm_stealth in course/format/update.php instead.',
        DEBUG_DEVELOPER
    );
    list($course, $cm) = get_course_and_cm_from_cmid($stealth);
    require_login($course, false, $cm);
    require_capability('moodle/course:activityvisibility', $cm->context);

    if (set_coursemodule_visible($cm->id, 1, 0)) {
        \core\event\course_module_updated::create_from_cm($cm)->trigger();
    }
    redirect(course_get_url($course, $cm->sectionnum, array('sr' => $sectionreturn)));

} else if (!empty($show) and confirm_sesskey()) {
    // TODO remove this else if as part of MDL-83530.
    debugging(
        'The show param deprecated. Please use action cm_show in course/format/update.php instead.',
        DEBUG_DEVELOPER
    );
    list($course, $cm) = get_course_and_cm_from_cmid($show);
    require_login($course, false, $cm);
    require_capability('moodle/course:activityvisibility', $cm->context);
    $section = $cm->get_section_info();

    if (set_coursemodule_visible($cm->id, 1)) {
        \core\event\course_module_updated::create_from_cm($cm)->trigger();
    }
    redirect(course_get_url($course, $section->section, $urloptions));

} else if ($groupmode > -1 and confirm_sesskey()) {
    // TODO remove this else if as part of MDL-83530.
    debugging(
        'The groupmode param deprecated. Please use the group mode actions in course/format/update.php instead.',
        DEBUG_DEVELOPER
    );
    $id = required_param('id', PARAM_INT);

    $cm     = get_coursemodule_from_id('', $id, 0, true, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

    require_login($course, false, $cm);
    $coursecontext = context_course::instance($course->id);
    $modcontext = context_module::instance($cm->id);
    require_capability('moodle/course:manageactivities', $modcontext);

    formatactions::cm($coursecontext->instanceid)->set_groupmode($cm->id, $groupmode);
    \core\event\course_module_updated::create_from_cm($cm, $modcontext)->trigger();
    redirect(course_get_url($course, $cm->sectionnum, $urloptions));

} else if (!empty($copy) and confirm_sesskey()) { // value = course module
    // TODO remove this else if as part of MDL-83530.
    debugging(
        'The copy param is deprecated. Please use the standard move modal instead.',
        DEBUG_DEVELOPER
    );
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

    redirect(course_get_url($course, $section->section, $urloptions));

} else if (!empty($cancelcopy) and confirm_sesskey()) { // value = course module
    // TODO remove this else if as part of MDL-83530.
    debugging(
        'The copy param is deprecated. Please use the standard move modal instead.',
        DEBUG_DEVELOPER
    );
    $courseid = $USER->activitycopycourse;
    $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);

    $cm     = get_coursemodule_from_id('', $USER->activitycopy, 0, true, IGNORE_MISSING);
    $sectionreturn = $USER->activitycopysectionreturn;
    unset($USER->activitycopy);
    unset($USER->activitycopycourse);
    unset($USER->activitycopyname);
    unset($USER->activitycopysectionreturn);
    redirect(course_get_url($course, $cm->sectionnum, $urloptions));
} else {
    throw new \moodle_exception('unknowaction');
}
