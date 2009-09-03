<?php // $Id$
      // Allows a creator to edit custom scales, and also display help about scales

    require_once("../config.php");
    require_once("lib.php");

    $id   = required_param('id', PARAM_INT);               // course id
    $scaleid  = optional_param('scaleid', 0, PARAM_INT);   // scale id (show only this one)

    if (!$course = $DB->get_record('course', array('id'=>$id))) {
        print_error("invalidcourseid");
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

    $PAGE->set_title($strscales);
    echo $OUTPUT->header();

    if ($scaleid) {
        if ($scale = $DB->get_record("scale", array('id'=>$scaleid))) {
            if ($scale->courseid == 0 || $scale->courseid == $course->id) {

                $scalemenu = make_menu_from_list($scale->scale);

                echo $OUTPUT->box_start();
                echo $OUTPUT->heading($scale->name);
                echo "<center>";
                echo $OUTPUT->select(html_select::make($scalemenu));
                echo "</center>";
                echo text_to_html($scale->description);
                echo $OUTPUT->box_end();
                echo $OUTPUT->close_window_button();
                echo $OUTPUT->footer();
                exit;
            }
        }
    }

    if ($scales = $DB->get_records("scale", array("courseid"=>$course->id), "name ASC")) {
        echo $OUTPUT->heading($strcustomscales);

        if (has_capability('moodle/course:managescales', $context)) {
            echo "<p align=\"center\">(";
            print_string('scalestip2');
            echo ")</p>";
        }

        foreach ($scales as $scale) {
            $scalemenu = make_menu_from_list($scale->scale);

            echo $OUTPUT->box_start();
            echo $OUTPUT->heading($scale->name);
            echo "<center>";
            echo $OUTPUT->select(html_select::make($scalemenu));
            echo "</center>";
            echo text_to_html($scale->description);
            echo $OUTPUT->box_end();
            echo "<hr />";
        }

    } else {
        if (has_capability('moodle/course:managescales', $context)) {
            echo "<p align=\"center\">(";
            print_string("scalestip");
            echo ")</p>";
        }
    }

    if ($scales = $DB->get_records("scale", array("courseid"=>0), "name ASC")) {
        echo $OUTPUT->heading($strstandardscales);
        foreach ($scales as $scale) {
            $scalemenu = make_menu_from_list($scale->scale);

            echo $OUTPUT->box_start();
            echo $OUTPUT->heading($scale->name);
            echo "<center>";
            echo $OUTPUT->select(html_select::make($scalemenu, ''));
            echo "</center>";
            echo text_to_html($scale->description);
            echo $OUTPUT->box_end();
            echo "<hr />";
        }
    }

    echo $OUTPUT->close_window_button();
    echo $OUTPUT->footer();

?>
