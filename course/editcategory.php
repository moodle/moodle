<?php // $Id$
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
if ($id) {
    if (!$category = get_record('course_categories', 'id', $id)) {
        error("Category not known!");
    }
    require_capability('moodle/category:manage', get_context_instance(CONTEXT_COURSECAT, $id));
    $strtitle = get_string('editcategorysettings');
} else {
    $parent = required_param('parent', PARAM_INT);
    if ($parent) {
        if (!record_exists('course_categories', 'id', $parent)) {
            error('Unknown parent category ' . $parent);
        }
        $context = get_context_instance(CONTEXT_COURSECAT, $parent);
    } else {
        $context = get_system_context();
    }
    $category = new stdClass();
    $category->id = 0;
    $category->parent = $parent;
    require_capability('moodle/category:manage', $context);
    $strtitle = get_string("addnewcategory");
}

$mform = new editcategory_form('editcategory.php', $category);
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
    $newcategory->description = $data->description;
    $newcategory->parent = $data->parent; // if $data->parent = 0, the new category will be a top-level category

    if (isset($data->theme) && !empty($CFG->allowcategorythemes)) {
        $newcategory->theme = $data->theme;
    }

    if ($id) {
        // Update an existing category.
        $newcategory->id = $category->id;
        if ($newcategory->parent != $category->parent) {
            $parent_cat = get_record('course_categories', 'id', $newcategory->parent);
            move_category($newcategory, $parent_cat);
        }
        if (!update_record('course_categories', $newcategory)) {
            error( "Could not update the category '$newcategory->name' ");
        }
        fix_course_sortorder();

    } else {
        // Create a new category.
        $newcategory->sortorder = 999;
        if (!$newcategory->id = insert_record('course_categories', $newcategory)) {
            error("Could not insert the new category '$newcategory->name' ");
        }
        $newcategory->context = get_context_instance(CONTEXT_COURSECAT, $newcategory->id);
        mark_context_dirty($newcategory->context->path);
        fix_course_sortorder(); // Required to build course_categories.depth and .path.
    }
    redirect('category.php?id='.$newcategory->id.'&categoryedit=on');
}

// Print the form
$straddnewcategory = get_string('addnewcategory');
$stradministration = get_string('administration');
$strcategories = get_string('categories');
$navlinks = array();

if ($id) {
    $navlinks[] = array('name' => $strtitle,
                        'link' => null,
                        'type' => 'misc');
    $title = $strtitle;
    $fullname = $category->name;
} else {
    $navlinks[] = array('name' => $stradministration,
                        'link' => "$CFG->wwwroot/$CFG->admin/index.php",
                        'type' => 'misc');
    $navlinks[] = array('name' => $strcategories,
                        'link' => 'index.php',
                        'type' => 'misc');
    $navlinks[] = array('name' => $straddnewcategory,
                        'link' => null,
                        'type' => 'misc');
    $title = "$SITE->shortname: $straddnewcategory";
    $fullname = $SITE->fullname;
}

$navigation = build_navigation($navlinks);
print_header($title, $fullname, $navigation, $mform->focus());
print_heading($strtitle);

$mform->display();

print_footer();
?>
