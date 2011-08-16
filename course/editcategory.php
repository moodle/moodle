<?php
/**
 * Page for creating or editing course category name/parent/description.
 * When called with an id parameter, edits the category with that id.
 * Otherwise it creates a new category with default parent from the parent
 * parameter, which may be 0.
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
    $categorycontext = get_context_instance(CONTEXT_COURSECAT, $id);
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
        $context = get_context_instance(CONTEXT_COURSECAT, $parent);
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
        redirect($CFG->wwwroot . '/course/category.php?id=' . $id . '&categoryedit=on');
    } else if ($parent) {
        redirect($CFG->wwwroot .'/course/category.php?id=' . $parent . '&categoryedit=on');
    } else {
        redirect($CFG->wwwroot .'/course/index.php?categoryedit=on');
    }
} else if ($data = $mform->get_data()) {
    $newcategory = new stdClass();
    $newcategory->name = $data->name;
    $newcategory->description_editor = $data->description_editor;
    $newcategory->parent = $data->parent; // if $data->parent = 0, the new category will be a top-level category

    if (isset($data->theme) && !empty($CFG->allowcategorythemes)) {
        $newcategory->theme = $data->theme;
    }

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
        $newcategory->sortorder = 999;
        $newcategory->id = $DB->insert_record('course_categories', $newcategory);
        $newcategory->context = get_context_instance(CONTEXT_COURSECAT, $newcategory->id);
        $categorycontext = $newcategory->context;
        mark_context_dirty($newcategory->context->path);
    }

    $newcategory = file_postupdate_standard_editor($newcategory, 'description', $editoroptions, $categorycontext, 'coursecat', 'description', 0);
    $DB->update_record('course_categories', $newcategory);
    fix_course_sortorder();

    redirect('category.php?id='.$newcategory->id.'&categoryedit=on');
}

$PAGE->set_title($title);
$PAGE->set_heading($fullname);
echo $OUTPUT->header();
echo $OUTPUT->heading($strtitle);
$mform->display();
echo $OUTPUT->footer();

