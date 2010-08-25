<?php // $Id$
      // Displays the top level category or all courses
      // In editing mode, allows the admin to edit a category,
      // and rearrange courses

    require_once("../config.php");
    require_once("lib.php");

    $id = required_param('id', PARAM_INT);          // Category id
    $page = optional_param('page', 0, PARAM_INT);     // which page to show
    $perpage = optional_param('perpage', $CFG->coursesperpage, PARAM_INT); // how many per page
    $categoryedit = optional_param('categoryedit', -1, PARAM_BOOL);
    $hide = optional_param('hide', 0, PARAM_INT);
    $show = optional_param('show', 0, PARAM_INT);
    $moveup = optional_param('moveup', 0, PARAM_INT);
    $movedown = optional_param('movedown', 0, PARAM_INT);
    $moveto = optional_param('moveto', 0, PARAM_INT);
    $resort = optional_param('resort', 0, PARAM_BOOL);

    if ($CFG->forcelogin) {
        require_login();
    }

    if (!$site = get_site()) {
        error('Site isn\'t defined!');
    }

    if (empty($id)) {
        error("Category not known!");
    }

    if (!$context = get_context_instance(CONTEXT_COURSECAT, $id)) {
        error("Category not known!");
    }

    if (!$category = get_record("course_categories", "id", $id)) {
        error("Category not known!");
    }
    if (!$category->visible) {
        require_capability('moodle/category:viewhiddencategories', $context);
    }

    if (update_category_button($category->id)) {
        if ($categoryedit !== -1) {
            $USER->categoryediting = $categoryedit;
        }
        $editingon = !empty($USER->categoryediting);
        $navbaritem = update_category_button($category->id); // Must call this again after updating the state.
    } else {
        $navbaritem = print_course_search("", true, "navbar");
        $editingon = false;
    }

    // Process any category actions.
    if (has_capability('moodle/category:manage', $context)) {
        /// Resort the category if requested
        if ($resort and confirm_sesskey()) {
            if ($courses = get_courses($category->id, "fullname ASC", 'c.id,c.fullname,c.sortorder')) {
                // move it off the range
                
                $sortorderresult = get_record_sql('SELECT MIN(sortorder) AS min, 1
                                         FROM ' . $CFG->prefix . 'course WHERE category=' . $category->id);
                $sortordermin = $sortorderresult->min;

                $sortorderresult = get_record_sql('SELECT MAX(sortorder) AS max, 1
                                         FROM ' . $CFG->prefix . 'course WHERE category=' . $category->id);
                $sortorder = $sortordermax = $sortorderresult->max + 100;

                //place the courses above the maximum existing sortorder to avoid duplicate index errors
                //after they've been sorted we'll shift them down again
                begin_sql();
                foreach ($courses as $course) {
                    set_field('course', 'sortorder', $sortorder, 'id', $course->id);
                    $sortorder++;
                }
                commit_sql();

                //shift course sortorder back down the amount we moved them up
                execute_sql('UPDATE '. $CFG->prefix .'course SET sortorder = sortorder-'.($sortordermax-$sortordermin).
                        ' WHERE category='.$category->id);

                fix_course_sortorder($category->id);
            }
        }
    }

    if(!empty($CFG->allowcategorythemes) && isset($category->theme)) {
        // specifying theme here saves us some dbqs
        theme_setup($category->theme);
    }

/// Print headings
    $numcategories = count_records('course_categories');

    $stradministration = get_string('administration');
    $strcategories = get_string('categories');
    $strcategory = get_string('category');
    $strcourses = get_string('courses');

    $navlinks = array();
    $navlinks[] = array('name' => $strcategories, 'link' => 'index.php', 'type' => 'misc');
    $navlinks[] = array('name' => format_string($category->name), 'link' => null, 'type' => 'misc');
    $navigation = build_navigation($navlinks);

    if ($editingon && update_category_button()) {
        // Integrate into the admin tree only if the user can edit categories at the top level,
        // otherwise the admin block does not appear to this user, and you get an error.
        require_once($CFG->libdir.'/adminlib.php');
        admin_externalpage_setup('coursemgmt', $navbaritem, array('id' => $id,
                'page' => $page, 'perpage' => $perpage), $CFG->wwwroot . '/course/category.php');
        admin_externalpage_print_header();
    } else {
        print_header("$site->shortname: $category->name", "$site->fullname: $strcourses", $navigation, '', '', true, $navbaritem);
    }

/// Print link to roles
    if (has_capability('moodle/role:assign', $context)) {
        echo '<div class="rolelink"><a href="'.$CFG->wwwroot.'/'.$CFG->admin.'/roles/assign.php?contextid='.
         $context->id.'">'.get_string('assignroles','role').'</a></div>';
    }

/// Print the category selector
    $displaylist = array();
    $notused = array();
    make_categories_list($displaylist, $notused);

    echo '<div class="categorypicker">';
    popup_form('category.php?id=', $displaylist, 'switchcategory', $category->id, '', '', '', false, 'self', $strcategories.':');
    echo '</div>';

/// Print current category description
    if (!$editingon && $category->description) {
        print_box_start();
        echo format_text($category->description); // for multilang filter
        print_box_end();
    }

/// Process any course actions.
    if ($editingon) {
    /// Move a specified course to a new category
        if (!empty($moveto) and $data = data_submitted() and confirm_sesskey()) {   // Some courses are being moved
            // user must have category update in both cats to perform this
            require_capability('moodle/category:manage', $context);
            require_capability('moodle/category:manage', get_context_instance(CONTEXT_COURSECAT, $moveto));

            if (!$destcategory = get_record('course_categories', 'id', $data->moveto)) {
                error('Error finding the category');
            }

            $courses = array();
            foreach ($data as $key => $value) {
                if (preg_match('/^c\d+$/', $key)) {
                    $courseid = substr($key, 1);
                    array_push($courses, $courseid);

                    // check this course's category
                    if ($movingcourse = get_record('course', 'id', $courseid)) {
                        if ($movingcourse->category != $id ) {
                            error('The course doesn\'t belong to this category');
                        }
                    } else {
                        error('Error finding the course');
                    }
                }
            }
            move_courses($courses, $data->moveto);
        }

    /// Hide or show a course
        if ((!empty($hide) or !empty($show)) and confirm_sesskey()) {
            if (!empty($hide)) {
                $course = get_record('course', 'id', $hide);
                $visible = 0;
            } else {
                $course = get_record('course', 'id', $show);
                $visible = 1;
            }

            if ($course) {
                $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);
                require_capability('moodle/course:visibility', $coursecontext);
                if (!set_field('course', 'visible', $visible, 'id', $course->id)) {
                    notify('Could not update that course!');
                }
            }
        }

    /// Move a course up or down
        if ((!empty($moveup) or !empty($movedown)) and confirm_sesskey()) {
            require_capability('moodle/category:manage', $context);
            $movecourse = NULL;
            $swapcourse = NULL;

            // ensure the course order has no gaps and isn't at 0
            fix_course_sortorder($category->id);

            // we are going to need to know the range
            $max = get_record_sql('SELECT MAX(sortorder) AS max, 1
                    FROM ' . $CFG->prefix . 'course WHERE category=' . $category->id);
            $max = $max->max + 100;

            if (!empty($moveup)) {
                $movecourse = get_record('course', 'id', $moveup);
                $swapcourse = get_record('course', 'category',  $category->id,
                        'sortorder', $movecourse->sortorder - 1);
            } else {
                $movecourse = get_record('course', 'id', $movedown);
                $swapcourse = get_record('course', 'category',  $category->id,
                        'sortorder', $movecourse->sortorder + 1);
            }
            if ($swapcourse and $movecourse) {
                // check course's category
                if ($movecourse->category != $id) {
                    error('The course doesn\'t belong to this category');
                }
                // Renumber everything for robustness
                begin_sql();
                if (!(    set_field('course', 'sortorder', $max, 'id', $swapcourse->id)
                       && set_field('course', 'sortorder', $swapcourse->sortorder, 'id', $movecourse->id)
                       && set_field('course', 'sortorder', $movecourse->sortorder, 'id', $swapcourse->id)
                    )) {
                    notify('Could not update that course!');
                }
                commit_sql();
            }

        }
    } // End of editing stuff

    if ($editingon && has_capability('moodle/category:manage', $context)) {
        echo '<div class="buttons">';

        // Print button to update this category
        $options = array('id' => $category->id);
        print_single_button($CFG->wwwroot.'/course/editcategory.php', $options, get_string('editcategorythis'), 'get');

        // Print button for creating new categories
        $options = array('parent' => $category->id);
        print_single_button($CFG->wwwroot.'/course/editcategory.php', $options, get_string('addsubcategory'), 'get');

        echo '</div>';
    }

/// Print out all the sub-categories
    if ($subcategories = get_records('course_categories', 'parent', $category->id, 'sortorder ASC')) {
        $firstentry = true;
        foreach ($subcategories as $subcategory) {
            if ($subcategory->visible || has_capability('moodle/category:viewhiddencategories', $context)) {
                $subcategorieswereshown = true;
                if ($firstentry) {
                    echo '<table border="0" cellspacing="2" cellpadding="4" class="generalbox boxaligncenter">';
                    echo '<tr><th scope="col">'.get_string('subcategories').'</th></tr>';
                    echo '<tr><td style="white-space: nowrap">';
                    $firstentry = false;
                }
                $catlinkcss = $subcategory->visible ? '' : 'class="dimmed" ';
                echo '<a '.$catlinkcss.' href="category.php?id='.$subcategory->id.'">'.
                     format_string($subcategory->name).'</a><br />';
            }
        }
        if (!$firstentry) {
            echo '</td></tr></table>';
            echo '<br />';
        }
    }


/// Print out all the courses
    $courses = get_courses_page($category->id, 'c.sortorder ASC',
            'c.id,c.sortorder,c.shortname,c.fullname,c.summary,c.visible,c.teacher,c.guest,c.password',
            $totalcount, $page*$perpage, $perpage);
    $numcourses = count($courses);

    if (!$courses) {
        if (empty($subcategorieswereshown)) {
            print_heading(get_string("nocoursesyet"));
        }

    } else if ($numcourses <= COURSE_MAX_SUMMARIES_PER_PAGE and !$page and !$editingon) {
        print_box_start('courseboxes');
        print_courses($category);
        print_box_end();

    } else {
        print_paging_bar($totalcount, $page, $perpage, "category.php?id=$category->id&amp;perpage=$perpage&amp;");

        $strcourses = get_string('courses');
        $strselect = get_string('select');
        $stredit = get_string('edit');
        $strdelete = get_string('delete');
        $strbackup = get_string('backup');
        $strrestore = get_string('restore');
        $strmoveup = get_string('moveup');
        $strmovedown = get_string('movedown');
        $strupdate = get_string('update');
        $strhide = get_string('hide');
        $strshow = get_string('show');
        $strsummary = get_string('summary');
        $strsettings = get_string('settings');
        $strassignteachers = get_string('assignteachers');
        $strallowguests = get_string('allowguests');
        $strrequireskey = get_string('requireskey');


        echo '<form id="movecourses" action="category.php" method="post"><div>';
        echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
        echo '<table border="0" cellspacing="2" cellpadding="4" class="generalbox boxaligncenter"><tr>';
        echo '<th class="header" scope="col">'.$strcourses.'</th>';
        if ($editingon) {
            echo '<th class="header" scope="col">'.$stredit.'</th>';
            echo '<th class="header" scope="col">'.$strselect.'</th>';
        } else {
            echo '<th class="header" scope="col">&nbsp;</th>';
        }
        echo '</tr>';


        $count = 0;
        $abletomovecourses = false;  // for now

        // Checking if we are at the first or at the last page, to allow courses to
        // be moved up and down beyond the paging border
        if ($totalcount > $perpage) {
            $atfirstpage = ($page == 0);
            if ($perpage > 0) {
                $atlastpage = (($page + 1) == ceil($totalcount / $perpage));
            } else {
                $atlastpage = true;
            }
        } else {
            $atfirstpage = true;
            $atlastpage = true;
        }

        $spacer = '<img src="'.$CFG->wwwroot.'/pix/spacer.gif" class="iconsmall" alt="" /> ';
        foreach ($courses as $acourse) {
            if (isset($acourse->context)) {
                $coursecontext = $acourse->context;
            } else {
                $coursecontext = get_context_instance(CONTEXT_COURSE, $acourse->id);
            }

            $count++;
            $up = ($count > 1 || !$atfirstpage);
            $down = ($count < $numcourses || !$atlastpage);

            $linkcss = $acourse->visible ? '' : ' class="dimmed" ';
            echo '<tr>';
            echo '<td><a '.$linkcss.' href="view.php?id='.$acourse->id.'">'. format_string($acourse->fullname) .'</a></td>';
            if ($editingon) {
                echo '<td>';
                if (has_capability('moodle/course:update', $coursecontext)) {
                    echo '<a title="'.$strsettings.'" href="'.$CFG->wwwroot.'/course/edit.php?id='.$acourse->id.'">'.
                            '<img src="'.$CFG->pixpath.'/t/edit.gif" class="iconsmall" alt="'.$stredit.'" /></a> ';
                } else {
                    echo $spacer;
                }

                // role assignment link
                if (has_capability('moodle/role:assign', $coursecontext)) {
                    echo '<a title="'.get_string('assignroles', 'role').'" href="'.$CFG->wwwroot.'/'.$CFG->admin.'/roles/assign.php?contextid='.$coursecontext->id.'">'.
                            '<img src="'.$CFG->pixpath.'/i/roles.gif" class="iconsmall" alt="'.get_string('assignroles', 'role').'" /></a> ';
                } else {
                    echo $spacer;
                }

                if (can_delete_course($acourse->id)) {
                    echo '<a title="'.$strdelete.'" href="delete.php?id='.$acourse->id.'">'.
                            '<img src="'.$CFG->pixpath.'/t/delete.gif" class="iconsmall" alt="'.$strdelete.'" /></a> ';
                } else {
                    echo $spacer;
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
                } else {
                    echo $spacer;
                }

                if (has_capability('moodle/site:backup', $coursecontext)) {
                    echo '<a title="'.$strbackup.'" href="../backup/backup.php?id='.$acourse->id.'">'.
                            '<img src="'.$CFG->pixpath.'/t/backup.gif" class="iconsmall" alt="'.$strbackup.'" /></a> ';
                } else {
                    echo $spacer;
                }

                if (has_capability('moodle/site:restore', $coursecontext)) {
                    echo '<a title="'.$strrestore.'" href="../files/index.php?id='.$acourse->id.
                         '&amp;wdir=/backupdata">'.
                         '<img src="'.$CFG->pixpath.'/t/restore.gif" class="iconsmall" alt="'.$strrestore.'" /></a> ';
                } else {
                    echo $spacer;
                }

                if (has_capability('moodle/category:manage', $context)) {
                    if ($up) {
                        echo '<a title="'.$strmoveup.'" href="category.php?id='.$category->id.'&amp;page='.$page.
                             '&amp;perpage='.$perpage.'&amp;moveup='.$acourse->id.'&amp;sesskey='.$USER->sesskey.'">'.
                             '<img src="'.$CFG->pixpath.'/t/up.gif" class="iconsmall" alt="'.$strmoveup.'" /></a> ';
                    } else {
                        echo $spacer;
                    }

                    if ($down) {
                        echo '<a title="'.$strmovedown.'" href="category.php?id='.$category->id.'&amp;page='.$page.
                             '&amp;perpage='.$perpage.'&amp;movedown='.$acourse->id.'&amp;sesskey='.$USER->sesskey.'">'.
                             '<img src="'.$CFG->pixpath.'/t/down.gif" class="iconsmall" alt="'.$strmovedown.'" /></a> ';
                    } else {
                        echo $spacer;
                    }
                    $abletomovecourses = true;
                } else {
                    echo $spacer, $spacer;
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
                         $CFG->pixpath.'/i/guest.gif" alt="'.$strallowguests.'" /></a>';
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
            $movetocategories = array();
            $notused = array();
            make_categories_list($movetocategories, $notused, 'moodle/category:manage');
            $movetocategories[$category->id] = get_string('moveselectedcoursesto');
            echo '<tr><td colspan="3" align="right">';
            choose_from_menu($movetocategories, 'moveto', $category->id, '', "javascript:submitFormById('movecourses')");
            echo '<input type="hidden" name="id" value="'.$category->id.'" />';
            echo '</td></tr>';
        }

        echo '</table>';
        echo '</div></form>';
        echo '<br />';
    }

    echo '<div class="buttons">';
    if (has_capability('moodle/category:manage', $context) and $numcourses > 1) {
    /// Print button to re-sort courses by name
        unset($options);
        $options['id'] = $category->id;
        $options['resort'] = 'name';
        $options['sesskey'] = $USER->sesskey;
        print_single_button('category.php', $options, get_string('resortcoursesbyname'), 'get');
    }

    if (has_capability('moodle/course:create', $context)) {
    /// Print button to create a new course
        unset($options);
        $options['category'] = $category->id;
        print_single_button('edit.php', $options, get_string('addnewcourse'), 'get');
    }

    if (!empty($CFG->enablecourserequests) && $category->id == $CFG->enablecourserequests) {
        print_course_request_buttons(get_context_instance(CONTEXT_SYSTEM));
    }
    echo '</div>';

    print_course_search();

    print_footer();

?>
