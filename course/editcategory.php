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
require_once('lib.php');
require_once('editcategory_form.php');

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
        redirect($CFG->wwwroot . '/course/manage.php?id=' . $id);
    } else if ($parent) {
        redirect($CFG->wwwroot .'/course/manage.php?id=' . $parent);
    } else {
        redirect($CFG->wwwroot .'/course/manage.php');
    }
} else if ($data = $mform->get_data()) {
    $newcategory = new stdClass();
    $newcategory->name = $data->name;
    $newcategory->idnumber = $data->idnumber;
    $newcategory->description_editor = $data->description_editor;
    $newcategory->parent = $data->parent; // if $data->parent = 0, the new category will be a top-level category

    if (isset($data->theme) && !empty($CFG->allowcategorythemes)) {
        $newcategory->theme = $data->theme;
    }

    $logaction = 'update';
    if ($id) {
        // Update an existing category.
        $newcategory->id = $category->id;
        if ($newcategory->parent != $category->parent) {
            // check category manage capability if parent changed
            require_capability('moodle/category:manage', get_category_or_system_context((int)$newcategory->parent));
            $parent_cat = $DB->get_record('course_categories', array('id' => $newcategory->parent));
            move_category($newcategory, $parent_cat);
        }
    } else {
        // Create a new category.
        $newcategory->description = $data->description_editor['text'];

        // Don't overwrite the $newcategory object as it'll be processed by file_postupdate_standard_editor in a moment
        $category = create_course_category($newcategory);
        $newcategory->id = $category->id;
        $categorycontext = $category->context;
        $logaction = 'add';
    }

    $newcategory = file_postupdate_standard_editor($newcategory, 'description', $editoroptions, $categorycontext, 'coursecat', 'description', 0);
    $DB->update_record('course_categories', $newcategory);
    add_to_log(SITEID, "category", $logaction, "editcategory.php?id=$newcategory->id", $newcategory->id);
    fix_course_sortorder();

    redirect('manage.php?id='.$newcategory->id);
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

