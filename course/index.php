<?php // $Id$
      // For most people, just lists the course categories
      // Allows the admin to create, delete and rename course categories

    require_once("../config.php");
    require_once("lib.php");

    $categoryedit = optional_param('categoryedit', -1,PARAM_BOOL);
    $delete   = optional_param('delete',0,PARAM_INT);
    $hide     = optional_param('hide',0,PARAM_INT);
    $show     = optional_param('show',0,PARAM_INT);
    $sure     = optional_param('sure','',PARAM_ALPHANUM);
    $move     = optional_param('move',0,PARAM_INT);
    $moveto   = optional_param('moveto',-1,PARAM_INT);
    $moveup   = optional_param('moveup',0,PARAM_INT);
    $movedown = optional_param('movedown',0,PARAM_INT);
    
    $context = get_context_instance(CONTEXT_SYSTEM, SITEID);

    if (!$site = get_site()) {
        error('Site isn\'t defined!');
    }

    if ($CFG->forcelogin) {
        require_login();
    }

    if (has_capability('moodle/category:update', get_context_instance(CONTEXT_SYSTEM, SITEID))) {
        if ($categoryedit !== -1) {
            $USER->categoryediting = $categoryedit;
        }
        $adminediting = !empty($USER->categoryediting);
    } else {
        $adminediting = false;
    }


/// Unless it's an editing admin, just print the regular listing of courses/categories

    if (!$adminediting) {
        $countcategories = count_records('course_categories');

        if ($countcategories > 1 || ($countcategories == 1 && count_records('course') > 200)) {
            $strcourses = get_string('courses');
            $strcategories = get_string('categories');
            print_header("$site->shortname: $strcategories", $strcourses, 
                          $strcategories, '', '', true, update_categories_button());
            print_heading($strcategories);
            print_box_start('categorybox');
            print_whole_category_list();
            print_box_end();
            print_course_search();
        } else {
            $strfulllistofcourses = get_string('fulllistofcourses');
            print_header("$site->shortname: $strfulllistofcourses", $strfulllistofcourses, $strfulllistofcourses,
                         '', '', true, update_categories_button());
            print_box_start('courseboxes');
            print_courses(0);
            print_box_end();
        }

        /// I am not sure this context in the next has_capability call is correct. 
        if (isloggedin() and !isguest() and !has_capability('moodle/course:create', get_context_instance(CONTEXT_SYSTEM, SITEID))) {  // Print link to request a new course
            print_single_button('request.php', NULL, get_string('courserequest'), 'get');
        }
        if (has_capability('moodle/course:create', get_context_instance(CONTEXT_SYSTEM, SITEID))) {       // Print link to create a new course
            print_single_button('edit.php', NULL, get_string('addnewcourse'), 'get');
        }
        if (has_capability('moodle/site:approvecourse', get_context_instance(CONTEXT_SYSTEM, SITEID))  and !empty($CFG->enablecourserequests)) {
            print_single_button('pending.php',NULL, get_string('coursespending'),'get');
        }
        print_footer();
        exit;
    }

/// From now on is all the admin functions


    require_once($CFG->libdir.'/adminlib.php');
    $adminroot = admin_get_root();
    admin_externalpage_setup('coursemgmt', $adminroot);


/// Print headings

    $stradministration = get_string('administration');
    $strcategories = get_string('categories');
    $strcategory = get_string('category');
    $strcourses = get_string('courses');
    $stredit = get_string('edit');
    $strdelete = get_string('delete');
    $straction = get_string('action');
    $straddnewcategory = get_string('addnewcategory');



    admin_externalpage_print_header($adminroot);

    print_heading($strcategories);


/// If data for a new category was submitted, then add it
    if ($form = data_submitted() and confirm_sesskey() and has_capability('moodle/category:create', $context)) {
        if (!empty($form->addcategory)) {
            unset($newcategory);
            $newcategory->name = $form->addcategory;
            $newcategory->sortorder = 999;
            if (!insert_record('course_categories', $newcategory)) {
                notify("Could not insert the new category '" . s($newcategory->name) . "'");
            } else {
                notify(get_string('categoryadded', '', s($newcategory->name)));
            }
        }
    }


/// Delete a category if necessary

    if (!empty($delete) and confirm_sesskey()) {
          
          // context is coursecat, if not present admins should have it set in site level
         $context = get_context_instance(CONTEXT_COURSECAT, $delete);        
        if ($deletecat = get_record('course_categories', 'id', $delete) and has_capability('moodle/category:delete', $context)) {
            if (!empty($sure) && $sure == md5($deletecat->timemodified)) {
                /// Send the children categories to live with their grandparent
                if ($childcats = get_records('course_categories', 'parent', $deletecat->id)) {
                    foreach ($childcats as $childcat) {
                        if (! set_field('course_categories', 'parent', $deletecat->parent, 'id', $childcat->id)) {
                            error('Could not update a child category!', 'index.php');
                        }
                    }
                }
                
                ///  If the grandparent is a valid (non-zero) category, then 
                ///  send the children courses to live with their grandparent as well
                if ($deletecat->parent) {
                    if ($childcourses = get_records('course', 'category', $deletecat->id)) {
                        foreach ($childcourses as $childcourse) {
                            if (! set_field('course', 'category', $deletecat->parent, 'id', $childcourse->id)) {
                                error('Could not update a child course!', 'index.php');
                            }
                        }
                    }
                }
                
                /// Finally delete the category itself
                if (delete_records('course_categories', 'id', $deletecat->id)) {
                    notify(get_string('categorydeleted', '', s($deletecat->name)));
                }
            }
            else {
                $strdeletecategorycheck = get_string('deletecategorycheck','', s($deletecat->name));
                notice_yesno($strdeletecategorycheck,
                             "index.php?delete=$delete&amp;sure=".md5($deletecat->timemodified)."&amp;sesskey=$USER->sesskey",
                             "index.php?sesskey=$USER->sesskey");

                admin_externalpage_print_footer($adminroot);
                exit();
            }
        }
    }


/// Create a default category if necessary
    if (!$categories = get_categories()) {    /// No category yet!
        // Try and make one
        unset($tempcat);
        $tempcat->name = get_string('miscellaneous');
        if (!$tempcat->id = insert_record('course_categories', $tempcat)) {
            error('Serious error: Could not create a default category!');
        }
    }


/// Move a category to a new parent if required

    if (!empty($move) and ($moveto>=0) and confirm_sesskey()) {
        if ($tempcat = get_record('course_categories', 'id', $move)) {
            if ($tempcat->parent != $moveto) {
                if (! set_field('course_categories', 'parent', $moveto, 'id', $tempcat->id)) {
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
        }
    }

/// Find the default category (the one with the lowest ID)
    $categories = get_categories();
    $default = 99999;
    foreach ($categories as $category) {
        if ($category->id < $default) {
            $default = $category->id;
        }
    }

/// Find any orphan courses that don't yet have a valid category and set to default
    if ($courses = get_courses(NULL,NULL,'c.id, c.category, c.sortorder, c.visible')) {
        foreach ($courses as $course) {
            if ($course->category and !isset($categories[$course->category])) {
                set_field('course', 'category', $default, 'id', $course->id);
            }
        }
    }
    
    fix_course_sortorder();

/// Print form for creating new categories
    if (has_capability('moodle/category:create', $context)) {
        echo '<div class="addcategory">';
        echo '<form id="addform" action="index.php" method="post">';
        echo '<fieldset class="invisiblefieldset">';
        echo '<input type="text" size="30" alt="'.$straddnewcategory.'" name="addcategory" />';
        echo '<input type="submit" value="'.$straddnewcategory.'" />';
        echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
        echo '</fieldset>';
        echo '</form>';
        echo '</div>';
    }

/// Print out the categories with all the knobs

    $strcategories = get_string('categories');
    $strcourses = get_string('courses');
    $strmovecategoryto = get_string('movecategoryto');
    $stredit = get_string('edit');

    $displaylist = array();
    $parentlist = array();

    $displaylist[0] = get_string('top');
    make_categories_list($displaylist, $parentlist, '');

    echo '<table class="generalbox editcourse"><tr class="header">';
    echo '<th class="header" scope="col">'.$strcategories.'</th>';
    echo '<th class="header" scope="col">'.$strcourses.'</th>';
    echo '<th class="header" scope="col">'.$stredit.'</th>';
    echo '<th class="header" scope="col">'.$strmovecategoryto.'</th>';
    echo '</tr>';

    print_category_edit(NULL, $displaylist, $parentlist);

    echo '</table>';

    /// Print link to create a new course
    if (has_capability('moodle/course:create', $context)) {
        unset($options);
        $options['category'] = $category->id;
        print_single_button('edit.php', $options, get_string('addnewcourse'), 'get');
    }

    print_single_button('pending.php',NULL, get_string('coursespending'), 'get');

    admin_externalpage_print_footer($adminroot);



function print_category_edit($category, $displaylist, $parentslist, $depth=-1, $up=false, $down=false) {
/// Recursive function to print all the categories ready for editing

    global $CFG, $USER;

    static $str = '';
    
    if (empty($str)) {
        $str->delete   = get_string('delete');
        $str->moveup   = get_string('moveup');
        $str->movedown = get_string('movedown');
        $str->edit     = get_string('editthiscategory');
        $str->hide     = get_string('hide');
        $str->show     = get_string('show');
    }
    
    if ($category) {

        $context  = get_context_instance(CONTEXT_COURSECAT, $category->id);
          
        echo '<tr><td align="left" class="name">';
        for ($i=0; $i<$depth;$i++) {
            echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        }
        $linkcss = $category->visible ? '' : ' class="dimmed" ';
        echo '<a '.$linkcss.' title="'.$str->edit.'" '.
             ' href="category.php?id='.$category->id.'&amp;categoryedit=on&amp;sesskey='.sesskey().'">'.
             s($category->name).'</a>';
        echo '</td>';

        echo '<td class="count">'.$category->coursecount.'</td>';

        echo '<td class="icons">';    /// Print little icons

        if (has_capability('moodle/category:delete', $context)) {
            echo '<a title="'.$str->delete.'" href="index.php?delete='.$category->id.'&amp;sesskey='.sesskey().'"><img'.
                 ' src="'.$CFG->pixpath.'/t/delete.gif" class="iconsmall" alt="'.$str->delete.'" /></a> ';
        }
        
        if (has_capability('moodle/category:visibility', $context)) {
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


?>
