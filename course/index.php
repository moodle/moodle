<?PHP // $Id$
      // Display list of all courses

    require_once("../config.php");
    require_once("lib.php");

    optional_variable($category, "0");

    $strcourses = get_string("courses");
    $strcategories = get_string("categories");
    $strmycourses = get_string("mycourses");
    $strfulllistofcourses = get_string("fulllistofcourses");

    if ($category = get_record("course_categories", "id", $category)) {
        print_header($strcourses, $strcourses, "<a href=\"index.php\">$strcourses</a> -> $category->name",
                     "", "", true, update_category_button($category->id));

    } else {
        print_header($strcourses, $strcourses, $strcourses);
        $category->id = 0;
    }


/// Print the category selector

    $categories = get_categories();
    $multicategories = count($categories) > 1;

    if (count($categories) > 1) {
        $parentlist = array();
        $displaylist = array();
        $displaylist["0"] = $strfulllistofcourses;
        make_categories_list($displaylist, $parentlist, "");
    
        echo "<table align=center><tr><td>";
        popup_form("index.php?category=", $displaylist, "switchcategory", "$category->id", "", "", "", false);
        echo "</td></tr></table><br />";
    }

    if (empty($category->id)) {
        print_courses(0, "80%");
    } else {
        print_courses($category, "80%");
    }

    print_footer();

?>


