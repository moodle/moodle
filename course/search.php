<?PHP // $Id$

/// Displays external information about a course

    require_once("../config.php");
    require_once("lib.php");

    optional_variable($search, "");    // search words
    optional_variable($page, "0");   // which page to show
    optional_variable($perpage, "10");   // which page to show

    $search = trim(strip_tags($search));

    $site = get_site();

    if (empty($THEME->custompix)) {
        $pixpath = "$CFG->wwwroot/pix";
    } else {
        $pixpath = "$CFG->wwwroot/theme/$CFG->theme/pix";
    }

    $displaylist = array();
    $parentlist = array();
    make_categories_list($displaylist, $parentlist, "");

    $strcourses = get_string("courses");
    $strsearch = get_string("search");
    $strsearchresults = get_string("searchresults");
    $strcategory = get_string("category");

    if (!$search) {
        print_header("$site->fullname : $strsearch", $site->fullname, 
                     "<a href=\"index.php\">$strcourses</a> -> $strsearch", "", "");
        print_course_search();
        print_footer();
        exit;
    }

    print_header("$site->fullname : $strsearchresults", $site->fullname, 
                 "<a href=\"index.php\">$strcourses</a> -> $strsearchresults -> '$search'", "", "");

    print_heading("$strsearchresults");

    $lastcategory = -1;
    if ($courses = get_courses_search($search, "category ASC", $page*$perpage, $perpage)) {
        foreach ($courses as $course) {
            if ($course->category != $lastcategory) {
                $lastcategory = $course->category;
                echo "<br /><p align=\"center\">";
                echo "<a href=\"category.php?id=$course->category\">";
                echo $displaylist[$course->category];
                echo "</a></p>";
            }
            $course->fullname = highlight("$search", $course->fullname);
            $course->summary = highlight("$search", $course->summary);
            print_course($course);
            print_spacer(5,5);
        }

        if (count($courses) == $perpage) {
            $options = array();
            $options["search"] = $search;
            $options["page"] = $page+1;
            $options["perpage"] = $perpage;
            echo "<center>";
            echo "<br />";
            print_single_button("search.php", $options, get_string("findmorecourses"));
            echo "</center>";
        } else {
            print_heading(get_string("nomorecourses", "", $search));
        }
    } else {
        print_heading(get_string("nocoursesfound", "", $search));
    }

    echo "<br /><br />";

    print_course_search($search);

    print_footer();


?>

