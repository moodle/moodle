<?PHP // $Id$

    require("../config.php");
    require("lib.php");

    $title = get_string("courses");

    print_header($title, $title, $title, "");

    optional_variable($cat, 1);

    echo "<TABLE WIDTH=80% ALIGN=CENTER><TR><TD>";

    print_all_courses($cat);

    echo "</TD></TR></TABLE>";

    print_footer();

?>


