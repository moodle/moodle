<?php // $Id$
      // Allows a creator to edit custom scales, and also display help about scales

    require_once("../config.php");
    require_once("lib.php");

    $id   = required_param('id', PARAM_INT);               // course id
    $scaleid  = optional_param('scaleid', 0, PARAM_INT);   // scale id (show only this one)

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID was incorrect");
    }

    require_login($course);
    $context = get_context_instance(CONTEXT_COURSE, $course->id);
    require_capability('moodle/course:viewscales', $context);

    $strscale = get_string("scale");
    $strscales = get_string("scales");
    $strcustomscale = get_string("scalescustom");
    $strstandardscale = get_string("scalesstandard");
    $strcustomscales = get_string("scalescustom");
    $strstandardscales = get_string("scalesstandard");
    $strname = get_string("name");
    $strdescription = get_string("description");
    $strhelptext = get_string("helptext");
    $stractivities = get_string("activities");

    print_header($strscales);

    if ($scaleid) {
        if ($scale = get_record("scale", 'id', $scaleid)) {
            if ($scale->courseid == 0 || $scale->courseid == $course->id) {

                $scalemenu = make_menu_from_list($scale->scale);

                print_simple_box_start("center");
                print_heading($scale->name);
                echo "<center>";
                choose_from_menu($scalemenu, "", "", "");
                echo "</center>";
                echo text_to_html($scale->description);
                print_simple_box_end();
                close_window_button();
                print_footer('empty');
                exit;
            }
        }
    }

    if ($scales = get_records("scale", "courseid", "$course->id", "name ASC")) {
        print_heading($strcustomscales);

        if (has_capability('moodle/course:managescales', $context)) {
            echo "<p align=\"center\">(";
            print_string('scalestip2');
            echo ")</p>";
        }

        foreach ($scales as $scale) {
            $scalemenu = make_menu_from_list($scale->scale);

            print_simple_box_start("center");
            print_heading($scale->name);
            echo "<center>";
            choose_from_menu($scalemenu, "", "", "");
            echo "</center>";
            echo text_to_html($scale->description);
            print_simple_box_end();
            echo "<hr />";
        }

    } else {
        if (has_capability('moodle/course:managescales', $context)) {
            echo "<p align=\"center\">(";
            print_string("scalestip2");
            echo ")</p>";
        }
    }

    if ($scales = get_records("scale", "courseid", "0", "name ASC")) {
        print_heading($strstandardscales);
        foreach ($scales as $scale) {
            $scalemenu = make_menu_from_list($scale->scale);

            print_simple_box_start("center");
            print_heading($scale->name);
            echo "<center>";
            choose_from_menu($scalemenu, "", "", "");
            echo "</center>";
            echo text_to_html($scale->description);
            print_simple_box_end();
            echo "<hr />";
        }
    }

    close_window_button();
    print_footer('empty');

?>
