<?PHP // $Id$
      // Displays the top level category or all courses
      // In editing mode, allows the admin to edit a category, 
      // and rearrange courses

	require_once("../config.php");
	require_once("lib.php");

    require_variable($id);    // Category id


    if (!$site = get_site()) {
        error("Site isn't defined!");
    }

    if (!$category = get_record("course_categories", "id", $id)) {
        error("Category not known!");
    }

    if (iscreator()) {
        if (isset($_GET['edit'])) {
            if ($edit == "on") {
                $USER->editing = true;
            } else if ($edit == "off") {
                $USER->editing = false;
            }
        }
        $updatebutton = update_category_button($category->id);

        $creatorediting = !empty($USER->editing);
        $adminediting = (isadmin() and $creatorediting);

    } else {
        $updatebutton = "";
        $adminediting = false;
    }


/// Rename the category if requested

    if (!empty($rename)) {
        $category->name = $rename;
        if (! set_field("course_categories", "name", $category->name, "id", $category->id)) {
            notify("An error occurred while renaming the category");
        }
    }


/// Print headings

    $numcategories = count_records("course_categories");

    $stradministration = get_string("administration");
    $strcategories = get_string("categories");
    $strcategory = get_string("category");
    $strcourses = get_string("courses");
    $strcoursemanagement = get_string("coursemanagement");

    if ($creatorediting) {
        if ($adminediting) {
	        print_header("$site->shortname: $category->name", "$site->fullname", 
                         "<a href=\"../$CFG->admin/index.php\">$stradministration</a> -> ".
                         "<a href=\"index.php\">$strcoursemanagement</a> -> $category->name",
                         "", "", true, $updatebutton);
        } else {
	        print_header("$site->shortname: $category->name", "$site->fullname", 
                         "<a href=\"index.php\">$strcourses</a> -> $category->name", "", "", true, $updatebutton);
        }
    } else {
	    print_header("$site->shortname: $category->name", "$site->fullname", 
                     "<a href=\"index.php\">$strcourses</a> -> $category->name", "", "", true, $updatebutton);
    }


/// Print the category selector

    $displaylist = array();
    $parentlist = array();
    
    make_categories_list($displaylist, $parentlist, "");
    
    echo "<table align=center><tr><td>";
    popup_form("category.php?id=", $displaylist, "switchcategory", "$category->id", "", "", "", false);
    echo "</td></tr></table><br />";


/// Editing functions

    if ($adminediting) {

    /// Move a specified course to a new category 

        if (isset($move) and isset($moveto)) {
            if (! $course  = get_record("course", "id", $move)) {
                notify("Error finding the course");
            } else if (! $destcategory = get_record("course_categories", "id", $moveto)) {
                notify("Error finding the category");
            } else {
                if (!set_field("course", "category", $destcategory->id, "id", $course->id)) {
                    notify("An error occurred - course not moved!");
                }
                fix_course_sortorder($destcategory->id);
                fix_course_sortorder($category->id);
                $category = get_record("course_categories", "id", $category->id);
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

    
/// Print out all the courses


    if (!$courses = get_courses($category->id)) {
        print_heading(get_string("nocoursesyet"));

    } else {

        $strcourses  = get_string("courses");
        $strmovecourseto = get_string("movecourseto");
        $stredit     = get_string("edit");
        $strdelete   = get_string("delete");
        $strbackup   = get_string("backup");
        $strmoveup   = get_string("moveup");
        $strmovedown = get_string("movedown");
        $strupdate   = get_string("update");
        $strhide     = get_string("hide");
        $strshow     = get_string("show");
        $strassignteachers     = get_string("assignteachers");

        if (empty($THEME->custompix)) {
            $pixpath = "$CFG->wwwroot/pix";
        } else {
            $pixpath = "$CFG->wwwroot/theme/$CFG->theme/pix";
        }

    
        echo "<table align=\"center\" border=0 cellspacing=2 cellpadding=4 class=\"generalbox\"><tr>";
        echo "<th>$strcourses</th>";
        if ($creatorediting) {
            echo "<th>$stredit</th>";
            if ($adminediting) {
                echo "<th>$strmovecourseto</th>";
            }
        }
        echo "</tr>";


        $numcourses = count($courses);
        $count = 0;

        foreach ($courses as $course) {
            $count++;
            $up = ($count == 1) ? false : true;
            $down = ($count == $numcourses) ? false : true;

            $linkcss = $course->visible ? "" : " class=\"dimmed\" ";
            echo "<tr>";
            echo "<td><a $linkcss href=\"view.php?id=$course->id\">$course->fullname</a></td>";
            if ($creatorediting) {
                echo "<td>";
                echo "<a title=\"$strassignteachers\" href=\"$CFG->wwwroot/$CFG->admin/teacher.php?id=$course->id\"><img".
                     " src=\"$pixpath/t/user.gif\" height=11 width=11 border=0></a> ";
                if ($adminediting) {
                    echo "<a title=\"$strdelete\" href=\"delete.php?id=$course->id\"><img".
                         " src=\"$pixpath/t/delete.gif\" height=11 width=11 border=0></a> ";
                    if (!empty($course->visible)) {
                        echo "<a title=\"$strhide\" href=\"category.php?id=$category->id&hide=$course->id\"><img".
                             " src=\"$pixpath/t/hide.gif\" height=11 width=11 border=0></a> ";
                    } else {
                        echo "<a title=\"$strshow\" href=\"category.php?id=$category->id&show=$course->id\"><img".
                             " src=\"$pixpath/t/show.gif\" height=11 width=11 border=0></a> ";
                    }
    
                    echo "<a title=\"$strbackup\" href=\"../backup/backup.php?id=$course->id\"><img".
                         " src=\"$pixpath/t/backup.gif\" height=11 width=11 border=0></a> ";
    
            
                    if ($up) {
                        echo "<a title=\"$strmoveup\" href=\"category.php?id=$category->id&moveup=$course->id\"><img".
                             " src=\"$pixpath/t/up.gif\" height=11 width=11 border=0></a> ";
                    }
        
                    if ($down) {
                        echo "<a title=\"$strmovedown\" href=\"category.php?id=$category->id&movedown=$course->id\"><img".
                             " src=\"$pixpath/t/down.gif\" height=11 width=11 border=0></a> ";
                    }
                }
    
                echo "</td>";
                echo "<td>";
                popup_form ("category.php?id=$category->id&move=$course->id&moveto=", $displaylist, 
                            "moveform$course->id", "$course->category", "", "", "", false);
                echo "</td>";
            }
            echo "</tr>";
        }
    
        echo "</table>";
        echo "<br />";
    }

    if ($adminediting) {
    /// First print form to rename the category
        $strrename= get_string("rename");
        print_simple_box_start("center");
        echo "<center>";
        echo "<form name=\"renameform\" action=\"category.php\" method=\"post\">";
        echo "<input type=\"hidden\" name=\"id\" value=\"$category->id\">";
        echo "<input type=\"text\" size=30 name=\"rename\" value=\"$category->name\">";
        echo "<input type=\"submit\" value=\"$strrename\">";
        echo "</form>";
        echo "</center>";
        print_simple_box_end();
        echo "<br />";
    }


    
    print_footer();

?>
