<?PHP // $Id$
      // Allows the admin to edit a category, and rearrange courses

	require_once("../config.php");
	require_once("lib.php");

    require_variable($id);    // Category id

    require_login();

    if (!isadmin()) {
        error("Only administrators can use this page!");
    }

    if (!$site = get_site()) {
        error("Site isn't defined!");
    }

    if (!$category = get_record("course_categories", "id", $id)) {
        error("Category not known!");
    }


/// Print headings

    $stradministration = get_string("administration");
    $strcategories = get_string("categories");
    $strcategories = get_string("categories");
    $strcategory = get_string("category");
    $strcourses = get_string("courses");

	print_header("$site->shortname: $strcategory", "$site->fullname", 
                 "<a href=\"../$CFG->admin/index.php\">$stradministration</a> -> ".
                 "<a href=\"categories.php\">$strcategories</a> -> $strcategory");


/// Rename the category

    if (!empty($rename)) {
        $category->name = $rename;
        if (! set_field("course_categories", "name", $category->name, "id", $category->id)) {
            notify("An error occurred while renaming the category");
        } else { 
            notify("The category was renamed");
        }
    }

/// Print the category selector

    $displaylist = array();
    $parentlist = array();
    
    make_categories_list($displaylist, $parentlist, "");
    
    echo "<table align=center><tr><td>";
    popup_form("category.php?id=", $displaylist, "switchcategory", "$category->id", "", "", "", false);
    echo "</td></tr></table><br />";


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
            fix_category_courses($destcategory->id);
            fix_category_courses($category->id);
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


        $movecourse = isset($moveup) ? $moveup : $movedown;

        fix_category_courses($category->id);
        if (!$category = get_record("course_categories", "id", $category->id)) {  // Fresh copy
            error("Category not known!");
        }

        $courses = explode(',', $category->courseorder);
        $key = array_search($movecourse, $courses);
        if ($key === NULL or $key === false) {
            notify("Could not find that course in the category list!");

        } else {
            if (isset($moveup)) {
                $swapkey = $key-1;
            } else {
                $swapkey = $key+1;
            }
            $courses[$key] = $courses[$swapkey];
            $courses[$swapkey] = $movecourse;
            $category->courseorder = implode(",", $courses);

            if (! set_field("course_categories", "courseorder", $category->courseorder, "id", $category->id)) {
                notify("Database error while trying to update the category!");
            }
        }
    }

    
/// Print out the courses with all the knobs

    fix_category_courses($category->id);

    if (!$courses = get_courses($category)) {
        print_heading(get_string("nocoursesyet"));

    } else {

        $strcourses = get_string("courses");
        $strmovecourseto = get_string("movecourseto");
        $stredit = get_string("edit");
        $strdelete   = get_string("delete");
        $strmoveup   = get_string("moveup");
        $strmovedown = get_string("movedown");
        $strupdate   = get_string("update");
        $strhide     = get_string("hide");
        $strshow     = get_string("show");

        if (empty($THEME->custompix)) {
            $pixpath = "$CFG->wwwroot/pix";
        } else {
            $pixpath = "$CFG->wwwroot/theme/$CFG->theme/pix";
        }
    
        echo "<table align=\"center\" border=0 cellspacing=2 cellpadding=5 class=\"generalbox\"><tr>";
        echo "<th>$strcourses</th>";
        echo "<th>$stredit</th>";
        echo "<th>$strmovecourseto</th></tr>";

        $numcourses = count($courses);
        $count = 0;

        foreach ($courses as $course) {
            $count++;
            $up = ($count == 1) ? false : true;
            $down = ($count == $numcourses) ? false : true;

            $linkcss = $course->visible ? "" : " class=\"dimmed\" ";
            echo "<tr>";
            echo "<td><a $linkcss href=\"view.php?id=$course->id\">$course->fullname</a></td>";
            echo "<td>";
            if (!empty($course->visible)) {
                echo "<a title=\"$strhide\" href=\"category.php?id=$category->id&hide=$course->id\"><img".
                     " src=\"$pixpath/t/hide.gif\" height=11 width=11 border=0></a> ";
            } else {
                echo "<a title=\"$strshow\" href=\"category.php?id=$category->id&show=$course->id\"><img".
                     " src=\"$pixpath/t/show.gif\" height=11 width=11 border=0></a> ";
            }
            echo "<a title=\"$strdelete\" href=\"delete.php?id=$course->id\"><img".
                 " src=\"$pixpath/t/delete.gif\" height=11 width=11 border=0></a> ";
    
            if ($up) {
                echo "<a title=\"$strmoveup\" href=\"category.php?id=$category->id&moveup=$course->id\"><img".
                     " src=\"$pixpath/t/up.gif\" height=11 width=11 border=0></a> ";
            }

            if ($down) {
                echo "<a title=\"$strmovedown\" href=\"category.php?id=$category->id&movedown=$course->id\"><img".
                     " src=\"$pixpath/t/down.gif\" height=11 width=11 border=0></a> ";
            }

            echo "</td>";
            echo "<td>";
            popup_form ("category.php?id=$category->id&move=$course->id&moveto=", $displaylist, 
                        "moveform$course->id", "$course->category", "", "", "", false);
            echo "</td>";
            echo "</tr>";
        }
    
        echo "</table>";
        echo "<br />";
    }
    
/// Print form to rename the category

    $strrename= get_string("rename");

    print_simple_box_start("center");
    echo "<center>";
    echo "<form name=\"renameform\" action=\"category.php\" method=\"post\">";
    echo "<input type=\"hidden\" name=\"id\" value=\"$category->id\">";
    echo "<input type=\"text\" size=55 name=\"rename\" value=\"$category->name\">";
    echo "<input type=\"submit\" value=\"$strrename\">";
    echo "</form>";
    echo "</center>";
    print_simple_box_end();

    print_footer();

?>
