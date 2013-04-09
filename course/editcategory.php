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
 * Page for creating or editing course category name/parent/description.
 * When called with an id parameter, edits the category with that id.
 * Otherwise it creates a new category with default parent from the parent
 * parameter, which may be 0.
 *
 * @package    core
 * @subpackage course
 * @copyright  2007 Nicolas Connault
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../config.php');
require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->dirroot.'/course/editcategory_form.php');
require_once($CFG->libdir.'/coursecatlib.php');

require_login();

$id = optional_param('id', 0, PARAM_INT);
$itemid = 0; //initalise itemid, as all files in category description has item id 0

if ($id) {
    if (!$category = $DB->get_record('course_categories', array('id' => $id))) {
        print_error('unknowcategory');
    }
    $PAGE->set_url('/course/editcategory.php', array('id' => $id));
    $categorycontext = context_coursecat::instance($id);
    $PAGE->set_context($categorycontext);
    require_capability('moodle/category:manage', $categorycontext);
    $strtitle = get_string('editcategorysettings');
    $editorcontext = $categorycontext;
    $title = $strtitle;
    $fullname = $category->name;
} else {
    $parent = required_param('parent', PARAM_INT);
    $PAGE->set_url('/course/editcategory.php', array('parent' => $parent));
    if ($parent) {
        if (!$DB->record_exists('course_categories', array('id' => $parent))) {
            print_error('unknowcategory');
        }
        $context = context_coursecat::instance($parent);
    } else {
        $context = get_system_context();
    }
    $PAGE->set_context($context);
    $category = new stdClass();
    $category->id = 0;
    $category->parent = $parent;
    require_capability('moodle/category:manage', $context);
    $strtitle = get_string("addnewcategory");
    $editorcontext = $context;
    $itemid = null; //set this explicitly, so files for parent category should not get loaded in draft area.
    $title = "$SITE->shortname: ".get_string('addnewcategory');
    $fullname = $SITE->fullname;
}

$PAGE->set_pagelayout('admin');

$editoroptions = array(
    'maxfiles'  => EDITOR_UNLIMITED_FILES,
    'maxbytes'  => $CFG->maxbytes,
    'trusttext' => true,
    'context'   => $editorcontext
);
$category = file_prepare_standard_editor($category, 'description', $editoroptions, $editorcontext, 'coursecat', 'description', $itemid);

$mform = new editcategory_form('editcategory.php', compact('category', 'editoroptions'));
$mform->set_data($category);

if ($mform->is_cancelled()) {
    if ($id) {
        redirect($CFG->wwwroot . '/course/manage.php?categoryid=' . $id);
    } else if ($parent) {
        redirect($CFG->wwwroot .'/course/manage.php?categoryid=' . $parent);
    } else {
        redirect($CFG->wwwroot .'/course/manage.php');
    }
} else if ($data = $mform->get_data()) {
    if ($id) {
        $newcategory = coursecat::get($id);
        if ($data->parent != $category->parent && !$newcategory->can_change_parent($data->parent)) {
            print_error('cannotmovecategory');
        }
        $newcategory->update($data, $editoroptions);
    } else {
        $newcategory = coursecat::create($data, $editoroptions);
    }

    redirect('manage.php?categoryid='.$newcategory->id);
}

// Page "Add new category" (with "Top" as a parent) does not exist in navigation.
// We pretend we are on course management page.
if (empty($id) && empty($parent)) {
    navigation_node::override_active_url(new moodle_url('/course/manage.php'));
}

$PAGE->set_title($title);
$PAGE->set_heading($fullname);
echo $OUTPUT->header();
echo $OUTPUT->heading($strtitle);
$mform->display();
echo $OUTPUT->footer();

