<?php // $Id$
      // For most people, just lists the course categories
      // Allows the admin to create, delete and rename course categories

    require_once("../config.php");
    require_once("lib.php");

    $categoryedit = optional_param('categoryedit', -1,PARAM_BOOL);
    $delete   = optional_param('delete',0,PARAM_INT);
    $hide     = optional_param('hide',0,PARAM_INT);
    $show     = optional_param('show',0,PARAM_INT);
    $move     = optional_param('move',0,PARAM_INT);
    $moveto   = optional_param('moveto',-1,PARAM_INT);
    $moveup   = optional_param('moveup',0,PARAM_INT);
    $movedown = optional_param('movedown',0,PARAM_INT);

    $sysctx  = get_context_instance(CONTEXT_SYSTEM);
    $context = $sysctx;

    if (!$site = get_site()) {
        error('Site isn\'t defined!');
    }

    if ($CFG->forcelogin) {
        require_login();
    }

    if (has_capability('moodle/category:update', $sysctx)) {
        if ($categoryedit !== -1) {
            $USER->categoryediting = $categoryedit;
        }
        $adminediting = !empty($USER->categoryediting);
    } else {
        $adminediting = false;
    }

    $stradministration = get_string('administration');
    $strcategories = get_string('categories');
    $strcategory = get_string('category');
    $strcourses = get_string('courses');
    $stredit = get_string('edit');
    $strdelete = get_string('delete');
    $straction = get_string('action');


/// Unless it's an editing admin, just print the regular listing of courses/categories

    if (!$adminediting) {

      /// Print form for creating new categories

        $countcategories = count_records('course_categories');

        if ($countcategories > 1 || ($countcategories == 1 && count_records('course') > 200)) {
            $strcourses = get_string('courses');
            $strcategories = get_string('categories');

            $navlinks = array();
            $navlinks[] = array('name'=>$strcategories,'link'=>'','type'=>'misc');
            $navigation = build_navigation($navlinks);
            print_header("$site->shortname: $strcategories", $strcourses, $navigation, '', '', true, update_categories_button());
            print_heading($strcategories);
            echo skip_main_destination();
            print_box_start('categorybox');
            print_whole_category_list();
            print_box_end();
            print_course_search();
        } else {
            $strfulllistofcourses = get_string('fulllistofcourses');
            print_header("$site->shortname: $strfulllistofcourses", $strfulllistofcourses,
                    build_navigation(array(array('name'=>$strfulllistofcourses, 'link'=>'','type'=>'misc'))),
                         '', '', true, update_categories_button());
            echo skip_main_destination();
            print_box_start('courseboxes');
            print_courses(0);
            print_box_end();
        }

        /// I am not sure this context in the next has_capability call is correct.
        if (isloggedin() and !isguest() and !has_capability('moodle/course:create', $sysctx) and $CFG->enablecourserequests) {  // Print link to request a new course
            print_single_button('request.php', NULL, get_string('courserequest'), 'get');
        }
        if (has_capability('moodle/course:create', $sysctx)) {       // Print link to create a new course
        /// Get the 1st available category
            $options = array('category' => get_field('course_categories', 'id', 'parent', '0'));
            print_single_button('edit.php', $options, get_string('addnewcourse'), 'get');
        }
        if (has_capability('moodle/site:approvecourse', $sysctx)  and !empty($CFG->enablecourserequests)) {
            print_single_button('pending.php',NULL, get_string('coursespending'),'get');
        }
        print_footer();
        exit;
    }

/// From now on is all the admin/course creator functions

/// Delete a category if necessary

    if (!empty($delete) and confirm_sesskey()) {
        require_once('delete_category_form.php');

        if (!$deletecat = get_record('course_categories', 'id', $delete)) {
            error('Incorrect category id', 'index.php');
        }

        $heading = get_string('deletecategory', '', format_string($deletecat->name));

        $context = get_context_instance(CONTEXT_COURSECAT, $delete);
        require_capability('moodle/category:delete', $context);

        $mform = new delete_category_form(null, $deletecat);
        $mform->set_data(array('delete'=>$delete));

        if ($mform->is_cancelled()) {
            redirect('index.php');

        } else if (!$data= $mform->get_data(false)) {
            require_once($CFG->libdir . '/questionlib.php');
            print_category_edit_header();
            print_heading($heading);
            print_box(get_string('deletecategorycheck2'), 'generalbox boxwidthnormal boxaligncenter');
            if (question_context_has_any_questions($context)) {
                print_box(get_string('deletecoursecategorywithquestions', 'question'), 
                        'generalbox boxwidthnormal boxaligncenter');
            }
            $mform->display();
            print_footer();
            exit();
        }

        print_category_edit_header();
        print_heading($heading);

        if ($data->fulldelete) {
            category_delete_full($deletecat, true);
        } else {
            category_delete_move($deletecat, $data->newparent, true);
        }

        print_continue('index.php');

        print_footer();
        die;
    }

/// Print headings
    print_category_edit_header();
    print_heading($strcategories);


/// Create a default category if necessary
    if (!$categories = get_categories()) {    /// No category yet!
        // Try and make one
        unset($tempcat);
        $tempcat->name = get_string('miscellaneous');
        if (!$tempcat->id = insert_record('course_categories', $tempcat)) {
            error('Serious error: Could not create a default category!');
        }
        $tempcat->context = get_context_instance(CONTEXT_COURSECAT, $tempcat->id);
        mark_context_dirty('/'.SYSCONTEXTID);
    }


/// Move a category to a new parent if required

    if (!empty($move) and ($moveto>=0) and confirm_sesskey()) {
        if ($tempcat = get_record('course_categories', 'id', $move)) {
            if ($tempcat->parent != $moveto) {
                $newp = get_record('course_categories', 'id', $moveto);
                if (! move_category($tempcat, $newp)) {
                    notify('Could not update that category!');
                }
            }
        }
    }


/// Hide or show a category
    if ((!empty($hide) or !empty($show)) and confirm_sesskey()) {
        if (!empty($hide)) {
            $tempcat = get_record('course_categories', 'id', $hide);
            $visible = 0;
        } else {
            $tempcat = get_record('course_categories', 'id', $show);
            $visible = 1;
        }
        if ($tempcat) {
            if (! set_field('course_categories', 'visible', $visible, 'id', $tempcat->id)) {
                notify('Could not update that category!');
            }
            if (! set_field('course', 'visible', $visible, 'category', $tempcat->id)) {
                notify('Could not hide/show any courses in this category !');
            }
        }
    }


/// Move a category up or down

    if ((!empty($moveup) or !empty($movedown)) and confirm_sesskey()) {

        $swapcategory = NULL;
        $movecategory = NULL;

        if (!empty($moveup)) {
            if ($movecategory = get_record('course_categories', 'id', $moveup)) {
                $categories = get_categories($movecategory->parent);

                foreach ($categories as $category) {
                    if ($category->id == $movecategory->id) {
                        break;
                    }
                    $swapcategory = $category;
                }
                unset($category);
            }
        }
        if (!empty($movedown)) {
            if ($movecategory = get_record('course_categories', 'id', $movedown)) {
                $categories = get_categories($movecategory->parent);

                $choosenext = false;
                foreach ($categories as $category) {
                    if ($choosenext) {
                        $swapcategory = $category;
                        break;
                    }
                    if ($category->id == $movecategory->id) {
                        $choosenext = true;
                    }
                }
                unset($category);
            }
        }
        if ($swapcategory and $movecategory) {        // Renumber everything for robustness
            $count=0;
            foreach ($categories as $category) {
                $count++;
                if ($category->id == $swapcategory->id) {
                    $category = $movecategory;
                } else if ($category->id == $movecategory->id) {
                    $category = $swapcategory;
                }
                if (! set_field('course_categories', 'sortorder', $count, 'id', $category->id)) {
                    notify('Could not update that category!');
                }
            }
            unset($category);
        }
    }

/// Find any orphan courses that don't yet have a valid category and set to default
    fix_coursecategory_orphans();

/// Should be a no-op 99% of the cases
    fix_course_sortorder();

/// Print out the categories with all the knobs

    $strcategories = get_string('categories');
    $strcourses = get_string('courses');
    $strmovecategoryto = get_string('movecategoryto');
    $stredit = get_string('edit');

    $displaylist = array();
    $parentlist = array();

    $displaylist[0] = get_string('top');
    make_categories_list($displaylist, $parentlist, '');

    echo '<table class="generalbox editcourse boxaligncenter"><tr class="header">';
    echo '<th class="header" scope="col">'.$strcategories.'</th>';
    echo '<th class="header" scope="col">'.$strcourses.'</th>';
    echo '<th class="header" scope="col">'.$stredit.'</th>';
    echo '<th class="header" scope="col">'.$strmovecategoryto.'</th>';
    echo '</tr>';

    print_category_edit(NULL, $displaylist, $parentlist);

    echo '</table>';

    echo '<div class="buttons">';

    if (!empty($category->id)) {
        // Print link to create a new course in current category
        if (has_capability('moodle/course:create', $context)) {
            $options = array();
            $options['category'] = $category->id;
            print_single_button('edit.php', $options, get_string('addnewcourse'), 'get');
        }
    }else{
        if (has_capability('moodle/course:create', $sysctx)) {
            // print create course link to first category
            $options = array();
            $options = array('category' => get_field('course_categories', 'id', 'parent', '0'));
            print_single_button('edit.php', $options, get_string('addnewcourse'), 'get');
        }
    }

    // Print button for creating new categories
    if (has_capability('moodle/category:create', $context)) {
        unset($options);
        if (!empty($category->id)) {
            $options['id'] = $category->id;
        } else {
            $options['id'] = 0;
        }
        $options['categoryadd'] = 1;
        print_single_button('editcategory.php', $options, get_string('addnewcategory'), 'get');
    }

    if (has_capability('moodle/site:approvecourse', $sysctx)  and !empty($CFG->enablecourserequests)) {
        print_single_button('pending.php',NULL, get_string('coursespending'), 'get');
    }
    // admin page does not allow custom buttons in the navigation bar
    echo '<div class="singlebutton">';
    echo update_categories_button();
    echo '</div></div>';

    print_footer();

function print_category_edit($category, $displaylist, $parentslist, $depth=-1, $up=false, $down=false) {
/// Recursive function to print all the categories ready for editing

    global $CFG, $USER;

    static $str = '';

    if (empty($str)) {
        $str->edit     = get_string('edit');
        $str->delete   = get_string('delete');
        $str->moveup   = get_string('moveup');
        $str->movedown = get_string('movedown');
        $str->edit     = get_string('editthiscategory');
        $str->hide     = get_string('hide');
        $str->show     = get_string('show');
    }

    if ($category) {

        if (!isset($category->context)) {
            $category->context = get_context_instance(CONTEXT_COURSECAT, $category->id);
        }

        echo '<tr><td align="left" class="name">';
        for ($i=0; $i<$depth;$i++) {
            echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        }
        $linkcss = $category->visible ? '' : ' class="dimmed" ';
        echo '<a '.$linkcss.' title="'.$str->edit.'" '.
             ' href="category.php?id='.$category->id.'&amp;categoryedit=on&amp;sesskey='.sesskey().'">'.
             format_string($category->name).'</a>';
        echo '</td>';

        echo '<td class="count">'.$category->coursecount.'</td>';

        echo '<td class="icons">';    /// Print little icons

        if (has_capability('moodle/category:update', $category->context)) {
            echo '<a title="'.$str->edit.'" href="editcategory.php?id='.$category->id.'&amp;sesskey='.sesskey().'"><img'.
                 ' src="'.$CFG->pixpath.'/t/edit.gif" class="iconsmall" alt="'.$str->edit.'" /></a> ';
        }

        if (has_capability('moodle/category:delete', $category->context)) {
            echo '<a title="'.$str->delete.'" href="index.php?delete='.$category->id.'&amp;sesskey='.sesskey().'"><img'.
                 ' src="'.$CFG->pixpath.'/t/delete.gif" class="iconsmall" alt="'.$str->delete.'" /></a> ';
        }

        if (has_capability('moodle/category:visibility', $category->context)) {
            if (!empty($category->visible)) {
                echo '<a title="'.$str->hide.'" href="index.php?hide='.$category->id.'&amp;sesskey='.sesskey().'"><img'.
                     ' src="'.$CFG->pixpath.'/t/hide.gif" class="iconsmall" alt="'.$str->hide.'" /></a> ';
            } else {
                echo '<a title="'.$str->show.'" href="index.php?show='.$category->id.'&amp;sesskey='.sesskey().'"><img'.
                     ' src="'.$CFG->pixpath.'/t/show.gif" class="iconsmall" alt="'.$str->show.'" /></a> ';
            }
        }

        if ($up) {
            echo '<a title="'.$str->moveup.'" href="index.php?moveup='.$category->id.'&amp;sesskey='.sesskey().'"><img'.
                 ' src="'.$CFG->pixpath.'/t/up.gif" class="iconsmall" alt="'.$str->moveup.'" /></a> ';
        }
        if ($down) {
            echo '<a title="'.$str->movedown.'" href="index.php?movedown='.$category->id.'&amp;sesskey='.sesskey().'"><img'.
                 ' src="'.$CFG->pixpath.'/t/down.gif" class="iconsmall" alt="'.$str->movedown.'" /></a> ';
        }
        echo '</td>';

        echo '<td align="left">';
        $tempdisplaylist = $displaylist;
        unset($tempdisplaylist[$category->id]);
        foreach ($parentslist as $key => $parents) {
            if (in_array($category->id, $parents)) {
                unset($tempdisplaylist[$key]);
            }
        }
        popup_form ("index.php?move=$category->id&amp;sesskey=$USER->sesskey&amp;moveto=", $tempdisplaylist, "moveform$category->id", $category->parent, '', '', '', false);
        echo '</td>';
        echo '</tr>';
    } else {
        $category->id = '0';
    }

    if ($categories = get_categories($category->id)) {   // Print all the children recursively
        $countcats = count($categories);
        $count = 0;
        $first = true;
        $last = false;
        foreach ($categories as $cat) {
            $count++;
            if ($count == $countcats) {
                $last = true;
            }
            $up = $first ? false : true;
            $down = $last ? false : true;
            $first = false;

            print_category_edit($cat, $displaylist, $parentslist, $depth+1, $up, $down);
        }
    }
}

function print_category_edit_header() {
    global $CFG;
    global $SITE;

    if (has_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM))) {
        require_once($CFG->libdir.'/adminlib.php');
        admin_externalpage_setup('coursemgmt');
        admin_externalpage_print_header();
    } else {
        print_header("$SITE->shortname:". get_string('categories'), get_string('courses'),
            build_navigation(array(array('name'=>get_string('categories'),'link'=>'','type'=>'misc'))), '', '', true, update_categories_button());
    }
}
?>
