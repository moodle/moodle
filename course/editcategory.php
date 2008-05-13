<?php // $Id$
/**
 * Displays a category and all its sub-categories.
 * In editing mode, allows admins to move/delete/hide categories
 */

require_once("../config.php");
require_once("lib.php");
require_once('editcategory_form.php');

$id             = optional_param('id', 0, PARAM_INT);            // Category id: if not given, show Add a Category form. If given and 'categoryupdate': show edit form
$page           = optional_param('page', 0, PARAM_INT);          // which page to show
$perpage        = optional_param('perpage', $CFG->coursesperpage, PARAM_INT); // how many per page
$hide           = optional_param('hide', 0, PARAM_INT);
$show           = optional_param('show', 0, PARAM_INT);
$moveup         = optional_param('moveup', 0, PARAM_INT);
$movedown       = optional_param('movedown', 0, PARAM_INT);
$moveto         = optional_param('moveto', 0, PARAM_INT);
$categoryedit   = optional_param('categoryedit', -1, PARAM_BOOL);    // Enables Move/Delete/Hide icons near each category in the list
$categoryadd    = optional_param('categoryadd', 0, PARAM_BOOL);  // Enables the Add Category form
$categoryupdate = optional_param('categoryupdate', 0, PARAM_BOOL); // Enables the Edit Category form
$resort         = optional_param('resort', 0, PARAM_BOOL);

if (!$site = get_site()) {
    error("Site isn't defined!");
}

if ($categoryadd) { // Show Add category form: if $id is given, it is used as the parent category 
    $strtitle = get_string("addnewcategory");
    $context = get_context_instance(CONTEXT_SYSTEM);
    $category = null;
} elseif (!is_null($id) && !$categoryadd) { // Show Edit category form: $id is given as the identifier of the category being edited
    $strtitle = get_string("editcategorysettings");
    $context = get_context_instance(CONTEXT_COURSECAT, $id); 
    if (!$category = get_record("course_categories", "id", $id)) {
        error("Category not known!");
    }
}

$mform = new editcategory_form('editcategory.php', compact(array('category', 'id')));

if (!empty($category)) {
    $mform->set_data($category); 
} elseif (!is_null($id)) {
    $data = new stdClass();
    $data->parent = $id;
    $data->categoryadd = 1;
    $mform->set_data($data);
}
    
if ($mform->is_cancelled()){
    if (empty($category)) {
        redirect($CFG->wwwroot .'/course/index.php?categoryedit=on');
    } else {
        redirect($CFG->wwwroot.'/course/category.php?categoryedit=on&id='.$category->id);
    } 
} else if (($data = $mform->get_data())) {
    $newcategory = new stdClass();
    $newcategory->name = $data->name;
    $newcategory->description = $data->description;
    $newcategory->sortorder = 999;
    $newcategory->parent = $data->parent; // if $id = 0, the new category will be a top-level category

    if (!empty($data->theme) && !empty($CFG->allowcategorythemes)) {
        $newcategory->theme = $data->theme;
        theme_setup();
    }

    if (empty($category) && has_capability('moodle/category:create', $context)) { // Create a new category 
        if (!$newcategory->id = insert_record('course_categories', $newcategory)) {
            notify( "Could not insert the new category '$newcategory->name' ");
        } else {
            $newcategory->context = get_context_instance(CONTEXT_COURSECAT, $newcategory->id);
            mark_context_dirty($newcategory->context->path);
            redirect('index.php?categoryedit=on');
        }
    } elseif (has_capability('moodle/category:update', $context)) {
        $newcategory->id = $category->id;

        if ($newcategory->parent != $category->parent) {
            $parent_cat = get_record('course_categories', 'id', $newcategory->parent);
            move_category($newcategory, $parent_cat);
        }

        if (!update_record('course_categories', $newcategory)) {
            error( "Could not update the category '$newcategory->name' ");
        } else {
            if ($newcategory->parent == 0) {
                $redirect_link = 'index.php?categoryedit=on';
            } else {
                $redirect_link = 'category.php?id='.$newcategory->id.'&categoryedit=on'; 
            }
            fix_course_sortorder();
            redirect($redirect_link);
        }
    } 
}



// If id is given, but not categoryadd or categoryupdate, we show the category with its list of subcategories
if ($id && !$categoryadd && !$categoryupdate && false) { 
    /* TODO implement

    if ($CFG->forcelogin) {
        require_login();
    }

    // Determine whether to allow user to see this category
    if (has_capability('moodle/course:create', $context)) {
        if ($categoryedit !== -1) {
            $USER->categoryediting = $categoryedit;
        }
        $navbaritem = update_category_button($category->id);
        $creatorediting = !empty($USER->categoryediting);
        $adminediting = (has_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM)) and $creatorediting);

    } else {
        if (!$category->visible) {
            print_error('notavailable', 'error');
        }
        $navbaritem = print_course_search("", true, "navbar");
        $adminediting = false;
        $creatorediting = false;
    }

    // Resort the category if requested 
    if ($resort and confirm_sesskey()) {
        if ($courses = get_courses($id, "fullname ASC", 'c.id,c.fullname,c.sortorder')) {
            // move it off the range
            $count = get_record_sql('SELECT MAX(sortorder) AS max, 1
                                     FROM ' . $CFG->prefix . 'course WHERE category=' . $category->id);
            $count = $count->max + 100;
            begin_sql();
            foreach ($courses as $course) {
                set_field('course', 'sortorder', $count, 'id', $course->id);
                $count++;
            }
            commit_sql();
            fix_course_sortorder($category->id);
        }
    }
    
    // Print headings 
    $numcategories = count_records("course_categories");

    $stradministration = get_string("administration");
    $strcategories = get_string("categories");
    $strcategory = get_string("category");
    $strcourses = get_string("courses");

    $navlinks = array();
    $navlinks[] = array('name' => $strcategories, 'link' => 'index.php', 'type' => 'misc');
    $navlinks[] = array('name' => $category->name, 'link' => null, 'type' => 'misc');
    $navigation = build_navigation($navlinks);

    if ($creatorediting) {
        if ($adminediting) {
            // modify this to treat this as an admin page

            require_once($CFG->libdir.'/adminlib.php');
            admin_externalpage_setup('categorymgmt');
            admin_externalpage_print_header();
        } else {
            print_header("$site->shortname: $category->name", "$site->fullname: $strcategories", $navigation, "", "", true, $navbaritem);
        }
    } else {
        print_header("$site->shortname: $category->name", "$site->fullname: $strcategories", $navigation, "", "", true, $navbaritem);
    }

    // Print button to turn editing off
    if ($adminediting) {
        echo '<div class="categoryediting button" align="right">'.update_category_button($category->id).'</div>';
    }

    // Print link to roles

    if (has_capability('moodle/role:assign', $context)) {
        echo '<div class="rolelink"><a href="'.$CFG->wwwroot.'/'.$CFG->admin.'/roles/assign.php?contextid='.
         $context->id.'">'.get_string('assignroles','role').'</a></div>';
    }
    
    // Print the category selector

    $displaylist = array();
    $parentlist = array();

    make_categories_list($displaylist, $parentlist, "");

    echo '<div class="categorypicker">';
    popup_form('category.php?id=', $displaylist, 'switchcategory', $category->id, '', '', '', false, 'self', $strcategories.':');
    echo '</div>';

    // Print current category description
    if ($category->description) {
        print_box_start();
        print_heading(get_string('description'));
        echo $category->description;
        print_box_end();
    }
    
    // Editing functions 
    if ($creatorediting) {
    // Move a specified category to a new category

        if (!empty($moveto) and $data = data_submitted() and confirm_sesskey()) {   // Some courses are being moved

            // user must have category update in both cats to perform this
            require_capability('moodle/category:update', $context);
            require_capability('moodle/category:update', get_context_instance(CONTEXT_COURSECAT, $moveto));

            if (!$destcategory = get_record("course_categories", "id", $data->moveto)) {
                error("Error finding the destination category");
            } 
            // TODO function to move the category
        }

        // Hide or show a category 
        if ((!empty($hide) or !empty($show)) and confirm_sesskey()) {
            require_capability('moodle/category:visibility', $context);
            if (!empty($hide)) {
                $category = get_record("course_categories", "id", $hide);
                $visible = 0;
            } else {
                $category = get_record("course_categories", "id", $show);
                $visible = 1;
            }
            if ($category) {
                if (! set_field("course_categories", "visible", $visible, "id", $category->id)) {
                    notify("Could not update that category!");
                }
            }
        }


        // Move a category up or down 
        if ((!empty($moveup) or !empty($movedown)) and confirm_sesskey()) {
            require_capability('moodle/category:update', $context);
            $movecategory = NULL;
            $swapcategory = NULL;

            // TODO something like fix_course_sortorder() ?

            // we are going to need to know the range
            $max = get_record_sql('SELECT MAX(sortorder) AS max, 1 FROM ' . $CFG->prefix . 'course_categories WHERE id=' . $category->id);
            $max = $max->max + 100;

            if (!empty($moveup)) {
                $movecategory = get_record('course_categories', 'id', $moveup);
                $swapcategory = get_record('course_categories',
                                         'category',  $category->id,
                                         'sortorder', $movecategory->sortorder - 1);
            } else {
                $movecategory = get_record('course_categories', 'id', $movedown);
                $swapcategory = get_record('course_categories',
                                         'category',  $category->id,
                                         'sortorder', $movecategory->sortorder + 1);
            }

            if ($swapcourse and $movecourse) {        // Renumber everything for robustness
                begin_sql();
                if (!(    set_field("course", "sortorder", $max, "id", $swapcourse->id)
                       && set_field("course", "sortorder", $swapcourse->sortorder, "id", $movecourse->id)
                       && set_field("course", "sortorder", $movecourse->sortorder, "id", $swapcourse->id)
                    )) {
                    notify("Could not update that course!");
                }
                commit_sql();
            }

        }

    } // End of editing stuff

    // Print out all the sub-categories
    if ($subcategories = get_records("course_categories", "parent", $category->id, "sortorder ASC")) {
        $firstentry = true;
        foreach ($subcategories as $subcategory) {
            if ($subcategory->visible or has_capability('moodle/course:create', $context)) {
                $subcategorieswereshown = true;
                if ($firstentry) {
                    echo '<table border="0" cellspacing="2" cellpadding="4" class="generalbox boxaligncenter">';
                    echo '<tr><th scope="col">'.get_string('subcategories').'</th></tr>';
                    echo '<tr><td style="white-space: nowrap">';
                    $firstentry = false;
                }
                $catlinkcss = $subcategory->visible ? "" : " class=\"dimmed\" ";
                echo '<a '.$catlinkcss.' href="category.php?id='.$subcategory->id.'">'.
                     format_string($subcategory->name).'</a><br />';
            }
        }
        if (!$firstentry) {
            echo "</td></tr></table>";
            echo "<br />";
        }
    }

    // print option to add a subcategory
    if (has_capability('moodle/category:create', $context) && $creatorediting) {
        $cat->id = $id;
        $mform->set_data($cat);
        $mform->display();
    }
    */
} 
// Print the form

$site = get_site();

$straddnewcategory = get_string("addnewcategory");
$stradministration = get_string("administration");
$strcategories = get_string("categories");
$navlinks = array();

if (!empty($category->name)) {
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
    $title = "$site->shortname: $straddnewcategory";
    $fullname = $site->fullname;
}

$navigation = build_navigation($navlinks);
print_header($title, $fullname, $navigation, $mform->focus());
print_heading($strtitle);

$mform->display();

print_footer();
?>
