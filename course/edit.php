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
 * Edit course settings
 *
 * @package    moodlecore
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../config.php');
require_once('lib.php');
require_once('edit_form.php');

$id         = optional_param('id', 0, PARAM_INT);       // course id
$categoryid = optional_param('category', 0, PARAM_INT); // course category - can be changed in edit form

$PAGE->set_pagelayout('admin');
$PAGE->set_url('/course/edit.php');

// basic access control checks
if ($id) { // editing course
    if ($id == SITEID){
        // don't allow editing of  'site course' using this from
        print_error('cannoteditsiteform');
    }

    $course = $DB->get_record('course', array('id'=>$id), '*', MUST_EXIST);
    require_login($course->id);
    $category = $DB->get_record('course_categories', array('id'=>$course->category), '*', MUST_EXIST);
    $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);
    require_capability('moodle/course:update', $coursecontext);
    $PAGE->url->param('id',$id);

} else if ($categoryid) { // creating new course in this category
    $course = null;
    require_login();
    $category = $DB->get_record('course_categories', array('id'=>$categoryid), '*', MUST_EXIST);
    require_capability('moodle/course:create', get_context_instance(CONTEXT_COURSECAT, $category->id));
    $PAGE->url->param('category',$categoryid);

} else {
    require_login();
    print_error('needcoursecategroyid');
}

// Prepare course and the editor
$editoroptions = array('maxfiles' => EDITOR_UNLIMITED_FILES, 'maxbytes'=>$CFG->maxbytes, 'trusttext'=>false, 'noclean'=>true);
if (!empty($course)) {
    $allowedmods = array();
    if ($am = $DB->get_records('course_allowed_modules', array('course'=>$course->id))) {
        foreach ($am as $m) {
            $allowedmods[] = $m->module;
        }
    } else {
        // this happens in case we edit course created before enabling module restrictions or somebody disabled everything :-(
        if (empty($course->restrictmodules) and !empty($CFG->defaultallowedmodules)) {
            $allowedmods = explode(',', $CFG->defaultallowedmodules);
        }
    }
    $course->allowedmods = $allowedmods;
    $course = file_prepare_standard_editor($course, 'summary', $editoroptions, $coursecontext, 'course_summary', 0);

} else {
    $course = file_prepare_standard_editor($course, 'summary', $editoroptions, null, 'course_summary', null);
}

// first create the form
$editform = new course_edit_form(NULL, array('course'=>$course, 'category'=>$category, 'editoroptions'=>$editoroptions));

if ($editform->is_cancelled()) {
    if (empty($course)) {
        redirect($CFG->wwwroot.'/');
    } else {
        redirect($CFG->wwwroot.'/course/view.php?id='.$course->id);
    }

} else if ($data = $editform->get_data()) {
    // process data if submitted

    if (empty($course->id)) {
        // In creating the course
        $course = create_course($data, $editoroptions);

        // Get the context of the newly created course
        $context = get_context_instance(CONTEXT_COURSE, $course->id, MUST_EXIST);

        // try to deal with course creators - enrol them internally with default role
        if (!empty($CFG->creatornewroleid) and !is_viewing($context, NULL, 'moodle/role:assign') and !is_enrolled($context, NULL, 'moodle/role:assign')) {
            enrol_try_internal_enrol($course->id, $USER->id, $CFG->creatornewroleid);
        }

        // Redirect to manual enrolment page if possible
        $instances = enrol_get_instances($course->id, true);
        foreach($instances as $instance) {
            if ($plugin = enrol_get_plugin($instance->enrol)) {
                if ($link = $plugin->get_manual_enrol_link($instance)) {
                    redirect($link);
                }
            }
        }

        redirect($CFG->wwwroot."/course/view.php?id=$course->id");

    } else {
        // Save any changes to the files used in the editor
        update_course($data, $editoroptions);
        redirect($CFG->wwwroot."/course/view.php?id=$course->id");
    }
}


// Print the form

$site = get_site();

$streditcoursesettings = get_string("editcoursesettings");
$straddnewcourse = get_string("addnewcourse");
$stradministration = get_string("administration");
$strcategories = get_string("categories");

if (!empty($course->id)) {
    $PAGE->navbar->add($streditcoursesettings);
    $title = $streditcoursesettings;
    $fullname = $course->fullname;
} else {
    $PAGE->navbar->add($stradministration, new moodle_url('/admin/index.php'));
    $PAGE->navbar->add($strcategories, new moodle_url('/course/index.php'));
    $PAGE->navbar->add($straddnewcourse);
    $title = "$site->shortname: $straddnewcourse";
    $fullname = $site->fullname;
}

$PAGE->set_title($title);
$PAGE->set_heading($fullname);
$PAGE->set_focuscontrol($editform->focus());

echo $OUTPUT->header();
echo $OUTPUT->heading($streditcoursesettings);

$editform->display();

echo $OUTPUT->footer();

