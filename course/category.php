<?php // $Id$
      // Displays the top level category or all courses
      // In editing mode, allows the admin to edit a category,
      // and rearrange courses

    require_once("../config.php");
    require_once("lib.php");

    $id           = required_param('id', PARAM_INT);          // Category id
    $page         = optional_param('page', 0, PARAM_INT);     // which page to show
    $perpage      = optional_param('perpage', $CFG->coursesperpage, PARAM_INT); // how many per page
    $categoryedit = optional_param('categoryedit', -1, PARAM_BOOL);
    $hide         = optional_param('hide', 0, PARAM_INT);
    $show         = optional_param('show', 0, PARAM_INT);
    $moveup       = optional_param('moveup', 0, PARAM_INT);
    $movedown     = optional_param('movedown', 0, PARAM_INT);
    $moveto       = optional_param('moveto', 0, PARAM_INT);
    $rename       = optional_param('rename', '', PARAM_NOTAGS);
    $resort       = optional_param('resort', 0, PARAM_BOOL);

    if (!$site = get_site()) {
        error("Site isn't defined!");
    }
    
    $context = get_context_instance(CONTEXT_COURSECAT, $id);
    
    if ($CFG->forcelogin) {
        require_login();
    }

    if (!$category = get_record("course_categories", "id", $id)) {
        error("Category not known!");
    }

    if (has_capability('moodle/course:create', $context)) {
        if ($categoryedit !== -1) {
            $USER->categoryediting = $categoryedit;
        }
        $navbaritem = update_category_button($category->id);
        $creatorediting = !empty($USER->categoryediting);
        $adminediting = (has_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM, SITEID)) and $creatorediting);

    } else {
        if (!$category->visible) {
            error(get_string('notavailable', 'error'));
        }
        $navbaritem = print_course_search("", true, "navbar");
        $adminediting = false;
        $creatorediting = false;
    }


    if (has_capability('moodle/category:update', $context)) {
        /// Rename the category if requested
        if (!empty($rename) and confirm_sesskey()) {
            $category->name = $rename;
            if (! set_field("course_categories", "name", $category->name, "id", $category->id)) {
                notify("An error occurred while renaming the category");
            }
        }

        /// Resort the category if requested

        if ($resort and confirm_sesskey()) {
            if ($courses = get_courses($category->id, "fullname ASC", 'c.id,c.fullname,c.sortorder')) {
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
    }


/// Print headings

    $numcategories = count_records("course_categories");

    $stradministration = get_string("administration");
    $strcategories = get_string("categories");
    $strcategory = get_string("category");
    $strcourses = get_string("courses");

    if ($creatorediting) {
        if ($adminediting) {
            // modify this to treat this as an admin page

            require_once($CFG->libdir.'/adminlib.php');
            $adminroot = admin_get_root();
            admin_externalpage_setup('coursemgmt', $adminroot);
            admin_externalpage_print_header($adminroot);
        } else {
            print_header("$site->shortname: $category->name", "$site->fullname: $strcourses",
                         "<a href=\"index.php\">$strcategories</a> -> $category->name", "", "", true, $navbaritem);
        }
    } else {
        print_header("$site->shortname: $category->name", "$site->fullname: $strcourses",
                     "<a href=\"index.php\">$strcategories</a> -> $category->name", "", "", true, $navbaritem);
    }

/// Print button to turn editing off
    if ($adminediting) {
        echo '<div class="categoryediting button" align="right">'.update_category_button($category->id).'</div>';
    }

/// Print link to roles

    if (has_capability('moodle/role:assign', $context)) { 
        echo '<div class="rolelink" align="right"><a href="'.$CFG->wwwroot.'/'.$CFG->admin.'/roles/assign.php?contextid='.
         $context->id.'">'.get_string('assignroles','role').'</a></div>';
    }
/// Print the category selector

    $displaylist = array();
    $parentlist = array();

    make_categories_list($displaylist, $parentlist, "");

    echo '<div class="categorypicker">';
    popup_form('category.php?id=', $displaylist, 'switchcategory', $category->id, '', '', '', false, 'self', $strcategories.':');
    echo '</div>';


/// Editing functions

    if ($adminediting) {

    /// Move a specified course to a new category

        if (!empty($moveto) and $data = data_submitted() and confirm_sesskey()) {   // Some courses are being moved

            if (! $destcategory = get_record("course_categories", "id", $data->moveto)) {
                error("Error finding the category");
            }

            
            $courses = array();        
            foreach ( $data as $key => $value ) {
                if (preg_match('/^c\d+$/', $key)) {
                    array_push($courses, substr($key, 1));
                }
            } 
            move_courses($courses, $data->moveto);
        }

    /// Hide or show a course

        if ((!empty($hide) or !empty($show)) and confirm_sesskey()) {
            if (!empty($hide)) {
                $course = get_record("course", "id", $hide);
                $visible = 0;
            } else {
                $course = get_record("course", "id", $show);
                $visible = 1;
            }
            if ($course) {
                if (! set_field("course", "visible", $visible, "id", $course->id)) {
                    notify("Could not update that course!");
                }
            }
        }


    /// Move a course up or down

        if ((!empty($moveup) or !empty($movedown)) and confirm_sesskey()) {

            $movecourse = NULL;
            $swapcourse = NULL;

            // ensure the course order has no gaps
            // and isn't at 0
            fix_course_sortorder($category->id);

            // we are going to need to know the range
            $max = get_record_sql('SELECT MAX(sortorder) AS max, 1
                                         FROM ' . $CFG->prefix . 'course WHERE category=' . $category->id);
            $max = $max->max + 100;

            if (!empty($moveup)) {
                $movecourse = get_record('course', 'id', $moveup);
                $swapcourse = get_record('course',
                                         'category',  $category->id,
                                         'sortorder', $movecourse->sortorder - 1);
            } else {
                $movecourse = get_record('course', 'id', $movedown);
                $swapcourse = get_record('course',
                                         'category',  $category->id,
                                         'sortorder', $movecourse->sortorder + 1);
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

/// Print out all the sub-categories
    if ($subcategories = get_records("course_categories", "parent", $category->id, "sortorder ASC")) {
        $firstentry = true;
        foreach ($subcategories as $subcategory) {
            if ($subcategory->visible or has_capability('moodle/course:create', $context)) {
                $subcategorieswereshown = true;
                if ($firstentry) {
                    echo '<table align="center" border="0" cellspacing="2" cellpadding="4" class="generalbox">';
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


/// Print out all the courses
    unset($course);    // To avoid unwanted language effects later

    $courses = get_courses_page($category->id, 'c.sortorder ASC',
                                'c.id,c.sortorder,c.shortname,c.fullname,c.summary,c.visible,c.teacher,c.guest,c.password',
                                $totalcount, $page*$perpage, $perpage);
    $numcourses = count($courses);

    if (!$courses) {
        if (empty($subcategorieswereshown)) {
            print_heading(get_string("nocoursesyet"));
        }

    } else if ($numcourses <= COURSE_MAX_SUMMARIES_PER_PAGE and !$page and !$creatorediting) {
        print_box_start('courseboxes');
        print_courses($category);
        print_box_end();

    } else {
        print_paging_bar($totalcount, $page, $perpage, "category.php?id=$category->id&amp;perpage=$perpage&");

        $strcourses  = get_string("courses");
        $strselect   = get_string("select");
        $stredit     = get_string("edit");
        $strdelete   = get_string("delete");
        $strbackup   = get_string("backup");
        $strrestore  = get_string("restore");
        $strmoveup   = get_string("moveup");
        $strmovedown = get_string("movedown");
        $strupdate   = get_string("update");
        $strhide     = get_string("hide");
        $strshow     = get_string("show");
        $strsummary  = get_string("summary");
        $strsettings = get_string("settings");
        $strassignteachers  = get_string("assignteachers");
        $strallowguests     = get_string("allowguests");
        $strrequireskey     = get_string("requireskey");


        echo '<form id="movecourses" action="category.php" method="post">';
        echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
        echo '<table align="center" border="0" cellspacing="2" cellpadding="4" class="generalbox"><tr>';
        echo '<th scope="col">'.$strcourses.'</th>';
        if ($creatorediting) {
            echo '<th scope="col">'.$stredit.'</th>';
            if ($adminediting) {
                echo '<th scope="col">'.$strselect.'</th>';
            }
        } else {
            echo '<th scope="col">&nbsp;</th>';
        }
        echo '</tr>';


        $count = 0;
        $abletomovecourses = false;  // for now

        // Checking if we are at the first or at the last page, to allow courses to
        // be moved up and down beyond the paging border
        if ($totalcount > $perpage) {
            $atfirstpage = ($page == 0);
            $atlastpage = (($page + 1) == ceil($totalcount / $perpage));
        } else {
            $atfirstpage = true;
            $atlastpage = true;
        }

        foreach ($courses as $acourse) {
          
            $coursecontext = get_context_instance(CONTEXT_COURSE, $acourse->id);
          
            $count++;
            $up = ($count > 1 || !$atfirstpage);
            $down = ($count < $numcourses || !$atlastpage);

            $linkcss = $acourse->visible ? "" : ' class="dimmed" ';
            echo '<tr>';
            echo '<td><a '.$linkcss.' href="view.php?id='.$acourse->id.'">'. format_string($acourse->fullname) .'</a></td>';
            if ($creatorediting) {
                echo "<td>";
                if (has_capability('moodle/course:update', $coursecontext)) {
                    echo '<a title="'.$strsettings.'" href="'.$CFG->wwwroot.'/course/edit.php?id='.
                         $acourse->id.'">'.
                         '<img src="'.$CFG->pixpath.'/t/edit.gif" class="iconsmall" alt="'.$stredit.'" /></a> ';        }
                
                // role assignment link     
                if (has_capability('moodle/role:assign', $coursecontext)) { 
                    echo'<a title="'.get_string('assignroles', 'role').'" href="'.$CFG->wwwroot.'/'.$CFG->admin.'/roles/assign.php?contextid='.$coursecontext->id.'"><img src="'.$CFG->pixpath.'/i/roles.gif" class="iconsmall" alt="'.get_string('assignroles', 'role').'" /></a>';
                }                       
                         
                if (can_delete_course($acourse->id)) {
                    echo '<a title="'.$strdelete.'" href="delete.php?id='.$acourse->id.'">'.
                            '<img src="'.$CFG->pixpath.'/t/delete.gif" class="iconsmall" alt="'.$strdelete.'" /></a> ';
                }

                // MDL-8885, users with no capability to view hidden courses, should not be able to lock themselves out
                if (has_capability('moodle/course:visibility', $coursecontext) && has_capability('moodle/course:viewhiddencourses', $coursecontext)) {
                    if (!empty($acourse->visible)) {
                        echo '<a title="'.$strhide.'" href="category.php?id='.$category->id.'&amp;page='.$page.
                            '&amp;perpage='.$perpage.'&amp;hide='.$acourse->id.'&amp;sesskey='.$USER->sesskey.'">'.
                            '<img src="'.$CFG->pixpath.'/t/hide.gif" class="iconsmall" alt="'.$strhide.'" /></a> ';
                    } else {
                        echo '<a title="'.$strshow.'" href="category.php?id='.$category->id.'&amp;page='.$page.
                            '&amp;perpage='.$perpage.'&amp;show='.$acourse->id.'&amp;sesskey='.$USER->sesskey.'">'.
                            '<img src="'.$CFG->pixpath.'/t/show.gif" class="iconsmall" alt="'.$strshow.'" /></a> ';
                    }
                }

                if (has_capability('moodle/site:backup', $coursecontext)) {
                    echo '<a title="'.$strbackup.'" href="../backup/backup.php?id='.$acourse->id.'">'.
                            '<img src="'.$CFG->pixpath.'/t/backup.gif" class="iconsmall" alt="'.$strbackup.'" /></a> ';
                }
                    
                if (has_capability('moodle/site:restore', $coursecontext)) {
                    echo '<a title="'.$strrestore.'" href="../files/index.php?id='.$acourse->id.
                         '&amp;wdir=/backupdata">'.
                         '<img src="'.$CFG->pixpath.'/t/restore.gif" class="iconsmall" alt="'.$strrestore.'" /></a> ';
                }

                if (has_capability('moodle/category:update', $context)) {
                    if ($up) {
                        echo '<a title="'.$strmoveup.'" href="category.php?id='.$category->id.'&amp;page='.$page.
                             '&amp;perpage='.$perpage.'&amp;moveup='.$acourse->id.'&amp;sesskey='.$USER->sesskey.'">'.
                             '<img src="'.$CFG->pixpath.'/t/up.gif" class="iconsmall" alt="'.$strmoveup.'" /></a> ';
                    } else {
                        echo '<img src="'.$CFG->wwwroot.'/pix/spacer.gif" class="iconsmall" alt="" /> ';
                    }

                    if ($down) {
                        echo '<a title="'.$strmovedown.'" href="category.php?id='.$category->id.'&amp;page='.$page.
                             '&amp;perpage='.$perpage.'&amp;movedown='.$acourse->id.'&amp;sesskey='.$USER->sesskey.'">'.
                             '<img src="'.$CFG->pixpath.'/t/down.gif" class="iconsmall" alt="'.$strmovedown.'" /></a> ';
                    } else {
                        echo '<img src="'.$CFG->wwwroot.'/pix/spacer.gif" class="iconsmall" alt="" /> ';
                    }
                    $abletomovecourses = true;
                }
                
                echo '</td>';
                echo '<td align="center">';
                echo '<input type="checkbox" name="c'.$acourse->id.'" />';
                echo '</td>';        
            } else {
                echo '<td align="right">';
                if (!empty($acourse->guest)) {
                    echo '<a href="view.php?id='.$acourse->id.'"><img title="'.
                         $strallowguests.'" class="icon" src="'.
                         $CFG->pixpath.'/i/user.gif" alt="'.$strallowguests.'" /></a>';
                }
                if (!empty($acourse->password)) {
                    echo '<a href="view.php?id='.$acourse->id.'"><img title="'.
                         $strrequireskey.'" class="icon" src="'.
                         $CFG->pixpath.'/i/key.gif" alt="'.$strrequireskey.'" /></a>';
                }
                if (!empty($acourse->summary)) {
                    link_to_popup_window ("/course/info.php?id=$acourse->id", "courseinfo",
                                          '<img alt="'.get_string('info').'" class="icon" src="'.$CFG->pixpath.'/i/info.gif" />',
                                           400, 500, $strsummary);
                }
                echo "</td>";
            }
            echo "</tr>";
        }

        if ($abletomovecourses) {
            echo '<tr><td colspan="3" align="right">';
            echo '<br />';
            unset($displaylist[$category->id]);
            choose_from_menu ($displaylist, "moveto", "", get_string("moveselectedcoursesto"), "javascript: getElementById('movecourses').submit()");
            echo '<input type="hidden" name="id" value="'.$category->id.'" />';
            echo '</td></tr>';
        }

        echo '</table>';
        echo '</form>';
        echo '<br />';
    }
    
    if (has_capability('moodle/category:update', get_context_instance(CONTEXT_SYSTEM, SITEID)) and $numcourses > 1) {           /// Print button to re-sort courses by name
        unset($options);
        $options['id'] = $category->id;
        $options['resort'] = 'name';
        $options['sesskey'] = $USER->sesskey;
        print_single_button('category.php', $options, get_string('resortcoursesbyname'), 'get');
    }

    if (has_capability('moodle/course:create', $context)) {         /// Print button to create a new course
        unset($options);
        $options['category'] = $category->id;
        print_single_button('edit.php', $options, get_string('addnewcourse'), 'get');
        echo '<br />';
    }

    if (has_capability('moodle/category:update', $context)) {           /// Print form to rename the category
        $strrename= get_string('rename');
        echo '<form id="renameform" action="category.php" method="post">';
        echo '<input type="hidden" name="id" value="'.$category->id.'" />';
        echo '<input type="hidden" name="sesskey" value="'.$USER->sesskey.'" />';
        echo '<input type="text" size="30" name="rename" value="'.format_string($category->name).'" alt="'.$strrename.'" />';
        echo '<input type="submit" value="'.$strrename.'" />';
        echo "</form>";
        echo "<br />";
    }
    
    print_course_search();
    
    if ($adminediting) {
        admin_externalpage_print_footer($adminroot);
    } else {
        print_footer();
    }

?>
