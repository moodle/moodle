<?PHP // $Id$

    require("../config.php");
    require("lib.php");

    print_header("Courses", "Courses", "Courses", "");

    optional_variable($cat, 1);

    print_simple_box_start("CENTER", "80%");

    print_all_courses($cat);

    print_simple_box_end();

    print_footer();

?>


