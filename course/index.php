<?PHP // $Id$
      // Display list of all courses

    require_once("../config.php");
    require_once("lib.php");

    optional_variable($category, "");

    $strcourses = get_string("courses");
    $strcategories = get_string("categories");
    $strmycourses = get_string("mycourses");
    $strfulllistofcourses = get_string("fulllistofcourses");

    if (!$categories = get_categories()) {
        error("Could not find any course categories!");
    }

    if ($category == "all") {
        $title = $strfulllistofcourses;
        $navigation = "<A HREF=\"index.php\">$strcourses</A> -> $title";
    } else if ($category == "my") {
        $title = $strmycourses;
        $navigation = "<A HREF=\"index.php\">$strcourses</A> -> $title";
    } else if (isset($categories[$category])) {
        $title = $categories[$category]->name;
        $navigation = "<A HREF=\"index.php\">$strcourses</A> -> $title";
    } else {
        $navigation = $strcourses;
    }

    print_header($strcourses, $strcourses, $navigation);

    $showcategories = (count($categories) > 1);
    if ($showcategories) {
        echo "<TABLE WIDTH=\"100%\" CELLPADDING=\"8\" BORDER=\"0\">";
        echo "<TR><TD WIDTH=\"200\" VALIGN=\"TOP\">";
        print_course_categories($categories, $category, 200);
        echo "</TD><TD WIDTH=\"*\" VALIGN=\"TOP\">";
    } else {
        echo "<TABLE WIDTH=80% ALIGN=CENTER><TR><TD VALIGN=top>";
        $category="all";
        unset($title);
    }

    if ($category) {
        if (isset($title)) {
            print_heading_block($title);
        }
        echo "<BR>";
        print_all_courses($category);
    }

    echo "</TD></TR></TABLE>";

    print_footer();

?>


