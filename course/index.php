<?PHP // $Id$

    require("../config.php");
    require("lib.php");

    print_header("Courses", "Courses", "Courses", "");

    optional_variable($cat, 1);

    echo "<TABLE WIDTH=80% ALIGN=CENTER><TR><TD>";

    print_all_courses($cat);

    echo "</TD></TR></TABLE>";

    print_footer();

?>


