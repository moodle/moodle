<?PHP // $Id$

    require("../config.php");
    require("lib.php");

    print_header("Courses", "Courses", "Courses", "");

    optional_variable($cat, 1);

    if ($courses = get_records("course", "category", $cat, "fullname ASC")) {
   
        foreach ($courses as $key => $course) {
            print_course($course);
            echo "<BR>\n";
        }

    } else {
        echo "<H3>No courses have been defined yet</H3>";
    }

    print_footer();

?>


