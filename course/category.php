<?PHP // $Id$
      // Displays the top level category or all courses
      // In editing mode, allows the admin to edit a category, 
      // and rearrange courses

    require_once("../config.php");
    require_once("lib.php");

    require_variable($id);    // Category id
    optional_variable($page, "0");     // which page to show
    optional_variable($perpage, "20"); // how many per page

    if (!$site = get_site()) {
        error("Site isn't defined!");
    }

    if ($CFG->forcelogin) {
        require_login();
    }

    if (!$category = get_record("course_categories", "id", $id)) {
        error("Category not known!");
    }

    if (iscreator()) {
        if (isset($_GET['edit'])) {
            if ($edit == "on") {
                $USER->categoryediting = true;
            } else if ($edit == "off") {
                $USER->categoryediting = false;
            }
        }
        $navbaritem = update_category_button($category->id);

        $creatorediting = !empty($USER->categoryediting);
        $adminediting = (isadmin() and $creatorediting);

    } else {
        if (!$category->visible) {
            error(get_string('notavailable', 'error'));
        }
        $navbaritem = print_course_search("", true, "navbar");
        $adminediting = false;
        $creatorediting = false;
    }


    if (isadmin()) {
        /// Rename the category if requested
        if (!empty($_POST['rename'])) {
            $category->name = $_POST['rename'];
            if (! set_field("course_categories", "name", $category->name, "id", $category->id)) {
                notify("An error occurred while renaming the category");
            }
        }

        /// Resort the category if requested

        if (!empty($_GET['resort'])) {
            fix_course_sortorder($category->id, "fullname ASC");
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
	        print_header("$site->shortname: $category->name", "$site->fullname: $strcourses", 
                         "<a href=\"../$CFG->admin/index.php\">$stradministration</a> -> ".
                         "<a href=\"index.php\">$strcategories</a> -> $category->name",
                         "", "", true, $navbaritem);
        } else {
	        print_header("$site->shortname: $category->name", "$site->fullname: $strcourses", 
                         "<a href=\"index.php\">$strcategories</a> -> $category->name", "", "", true, $navbaritem);
        }
    } else {
	    print_header("$site->shortname: $category->name", "$site->fullname: $strcourses", 
                     "<a href=\"index.php\">$strcategories</a> -> $category->name", "", "", true, $navbaritem);
    }


/// Print the category selector

    $displaylist = array();
    $parentlist = array();
    
    make_categories_list($displaylist, $parentlist, "");
    
    echo "<table align=center><tr><td align=\"right\">";
    echo "<p>$strcategories:</p>";
    echo "</td><td>";
    popup_form("category.php?id=", $displaylist, "switchcategory", "$category->id", "", "", "", false);
    echo "</td></tr></table><br />";


/// Editing functions

    if ($adminediting) {

    /// Move a specified course to a new category 

        if (isset($moveto) and $data = data_submitted()) {   // Some courses are being moved

            if (! $destcategory = get_record("course_categories", "id", $data->moveto)) {
                error("Error finding the category");
            }

            unset($data->moveto);
            unset($data->id);

            if ($data) {
                foreach ($data as $code => $junk) {
                    $courseid = substr($code, 1);

                    if (! $course  = get_record("course", "id", $courseid)) {
                        notify("Error finding course $courseid");
                    } else {
                        if (!set_field("course", "category", $destcategory->id, "id", $course->id)) {
                            notify("An error occurred - course not moved!");
                        }
                        fix_course_sortorder($destcategory->id);
                        fix_course_sortorder($category->id);
                        $category = get_record("course_categories", "id", $category->id);
                    }
                }
            }
        }


    /// Hide or show a course 
    
        if (isset($hide) or isset($show)) {
            if (isset($hide)) {
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

        if (isset($moveup) or isset($movedown)) {

            $movecourse = NULL;
            $swapcourse = NULL;

            $courses = get_courses($category->id);

            if (isset($moveup)) {
                if ($movecourse = get_record("course", "id", $moveup)) {
                    foreach ($courses as $course) {
                        if ($course->id == $movecourse->id) {
                            break;
                        }
                        $swapcourse = $course;
                    }
                }
            }
            if (isset($movedown)) {
                if ($movecourse = get_record("course", "id", $movedown)) {
                    $choosenext = false;
                    foreach ($courses as $course) {
                        if ($choosenext) {
                            $swapcourse = $course;
                            break;
                        }
                        if ($course->id == $movecourse->id) {
                            $choosenext = true;
                        }
                    }
                }
            }
            if ($swapcourse and $movecourse) {        // Renumber everything for robustness
                $count=0;
                foreach ($courses as $course) {
                    $count++;
                    if ($course->id == $swapcourse->id) {
                        $course = $movecourse;
                    } else if ($course->id == $movecourse->id) {
                        $course = $swapcourse;
                    }
                    if (! set_field("course", "sortorder", $count, "id", $course->id)) {
                        notify("Could not update that course!");
                    }
                }
            }
        }

        fix_course_sortorder($category->id);

    } // End of editing stuff

/// Print out all the sub-categories

    if ($subcategories = get_records("course_categories", "parent", $category->id, "sortorder ASC")) {
        $firstentry = true;
        foreach ($subcategories as $subcategory) {
            if ($subcategory->visible or iscreator()) {
                if ($firstentry) {
                    echo "<table align=\"center\" border=0 cellspacing=2 cellpadding=4 class=\"generalbox\">";
                    echo "<tr><th>".get_string("subcategories")."</th></tr>";
                    echo "<tr><td nowrap>";
                    $firstentry = false;
                }
                $catlinkcss = $subcategory->visible ? "" : " class=\"dimmed\" ";
                echo "<a $catlinkcss href=\"category.php?id=$subcategory->id\">$subcategory->name</a><br />";
            }
        }
        if (!$firstentry) {
            echo "</td></tr></table>";
            echo "<br />";
        }
    }
    

/// Print out all the courses
    $courses = get_courses_page($category->id, "c.sortorder ASC", "c.*", $totalcount, $page*$perpage, $perpage);
    $numcourses = count($courses);

    if (!$courses) {
        print_heading(get_string("nocoursesyet"));

    } else if ($numcourses <= COURSE_MAX_SUMMARIES_PER_PAGE and !$page and !$creatorediting) {
        print_courses($category, "80%");

    } else { 
        print_paging_bar($totalcount, $page, $perpage, "category.php?id=$category->id&perpage=$perpage&");

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
        $strassignteachers     = get_string("assignteachers");
        $strallowguests     = get_string("allowguests");
        $strrequireskey     = get_string("requireskey");

        if (empty($THEME->custompix)) {
            $pixpath = "$CFG->wwwroot/pix";
        } else {
            $pixpath = "$CFG->wwwroot/theme/$CFG->theme/pix";
        }

        echo "<form name=\"movecourses\" action=\"category.php\" method=\"post\">";
        echo "<table align=\"center\" border=0 cellspacing=2 cellpadding=4 class=\"generalbox\"><tr>";
        echo "<th>$strcourses</th>";
        if ($creatorediting) {
            echo "<th>$stredit</th>";
            if ($adminediting) {
                echo "<th>$strselect</th>";
            }
        } else {
            echo "<th>&nbsp;</th>";
        }
        echo "</tr>";


        $count = 0;
        $abletomovecourses = false;  // for now

        foreach ($courses as $acourse) {
            $count++;
            $up = ($count == 1) ? false : true;
            $down = ($count == $numcourses) ? false : true;

            $linkcss = $acourse->visible ? "" : " class=\"dimmed\" ";
            echo "<tr>";
            echo "<td><a $linkcss href=\"view.php?id=$acourse->id\">$acourse->fullname</a></td>";
            if ($creatorediting) {
                if ($adminediting) {
                    echo "<td>";
                    echo "<a title=\"$strassignteachers\" href=\"$CFG->wwwroot/course/teacher.php?id=$acourse->id\"><img".
                         " src=\"$pixpath/t/user.gif\" height=11 width=11 border=0></a> ";
                    echo "<a title=\"$strdelete\" href=\"delete.php?id=$acourse->id\"><img".
                         " src=\"$pixpath/t/delete.gif\" height=11 width=11 border=0></a> ";
                    if (!empty($acourse->visible)) {
                        echo "<a title=\"$strhide\" href=\"category.php?id=$category->id&hide=$acourse->id\"><img".
                             " src=\"$pixpath/t/hide.gif\" height=11 width=11 border=0></a> ";
                    } else {
                        echo "<a title=\"$strshow\" href=\"category.php?id=$category->id&show=$acourse->id\"><img".
                             " src=\"$pixpath/t/show.gif\" height=11 width=11 border=0></a> ";
                    }
    
                    echo "<a title=\"$strbackup\" href=\"../backup/backup.php?id=$acourse->id\"><img".
                         " src=\"$pixpath/t/backup.gif\" height=11 width=11 border=0></a> ";

                        echo "<a title=\"$strrestore\" href=\"../files/index.php?id=$acourse->id&wdir=/backupdata\"><img".
                             " src=\"$pixpath/t/restore.gif\" height=11 width=11 border=0></a> ";
            
                    if ($up) {
                        echo "<a title=\"$strmoveup\" href=\"category.php?id=$category->id&moveup=$acourse->id\"><img".
                             " src=\"$pixpath/t/up.gif\" height=11 width=11 border=0></a> ";
                    } else {
                        echo "<img src=\"$CFG->wwwroot/pix/spacer.gif\" height=11 width=11 border=0></a> ";
                    }
        
                    if ($down) {
                        echo "<a title=\"$strmovedown\" href=\"category.php?id=$category->id&movedown=$acourse->id\"><img".
                             " src=\"$pixpath/t/down.gif\" height=11 width=11 border=0></a> ";
                    } else {
                        echo "<img src=\"$CFG->wwwroot/pix/spacer.gif\" height=11 width=11 border=0></a> ";
                    }
    
                    echo "</td>";
                    echo "<td align=\"center\">";
                    echo "<input type=\"checkbox\" name=\"c$acourse->id\">";
                    $abletomovecourses = true;

                } else if (isteacheredit($acourse->id)) {
                    echo "<td>";
                    echo "<a title=\"$strassignteachers\" href=\"$CFG->wwwroot/course/teacher.php?id=$acourse->id\"><img".
                         " src=\"$pixpath/t/user.gif\" height=11 width=11 border=0></a> ";
                }
                echo "</td>";
            } else {
                echo "<td align=\"right\">";
                if ($acourse->guest ) {
                    echo "<a href=\"view.php?id=$acourse->id\"><img hspace=2 title=\"$strallowguests\" alt=\"\" height=16 width=16 border=0 src=\"$pixpath/i/user.gif\"></a>";
                }
                if ($acourse->password) {
                    echo "<a href=\"view.php?id=$acourse->id\"><img hspace=2 title=\"$strrequireskey\" alt=\"\" height=16 width=16 border=0 src=\"$pixpath/i/key.gif\"></a>";
                }
                if ($acourse->summary) {
                    link_to_popup_window ("/course/info.php?id=$acourse->id", "courseinfo", 
                                          "<img hspace=2 alt=\"info\" height=16 width=16 border=0 src=\"$pixpath/i/info.gif\">", 
                                           400, 500, $strsummary);
                }
                echo "</td>";
            }
            echo "</tr>";
        }

        if ($abletomovecourses) {
            echo "<tr><td colspan=3 align=right>";
            echo "<br />";
            unset($displaylist[$category->id]);
            choose_from_menu ($displaylist, "moveto", "", get_string("moveselectedcoursesto"), "javascript:document.movecourses.submit()");
            echo "<input type=\"hidden\" name=\"id\" value=\"$category->id\">";
            echo "</td></tr>";
        }
    
        echo "</table>";
        echo "</form>";
        echo "<br />";
    }


    echo "<center>";
    if (isadmin() and $numcourses > 1) {           /// Print button to re-sort courses by name
        unset($options);
        $options["id"] = $category->id;
        $options["resort"] = "name";
        print_single_button("category.php", $options, get_string("resortcoursesbyname"), "get");
    }

    if (iscreator()) {         /// Print button to create a new course
        unset($options);
        $options["category"] = $category->id;
        print_single_button("edit.php", $options, get_string("addnewcourse"), "get");
        echo "<br />";
    }

    if (isadmin()) {           /// Print form to rename the category
        $strrename= get_string("rename");
        echo "<form name=\"renameform\" action=\"category.php\" method=\"post\">";
        echo "<input type=\"hidden\" name=\"id\" value=\"$category->id\">";
        echo "<input type=\"text\" size=30 name=\"rename\" value=\"".s($category->name)."\">";
        echo "<input type=\"submit\" value=\"$strrename\">";
        echo "</form>";
        echo "<br />";
    }
    echo "</center>";
    
    print_footer();

?>
