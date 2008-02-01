<?php  // $Id$

    require_once("../../config.php");
    require_once("lib.php");

    $id = required_param('id',PARAM_INT);   // course

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID is incorrect");
    }

    require_course_login($course);

    add_to_log($course->id, "choice", "view all", "index?id=$course->id", "");

    $strchoice = get_string("modulename", "choice");
    $strchoices = get_string("modulenameplural", "choice");
    $navlinks = array();
    $navlinks[] = array('name' => $strchoices, 'link' => '', 'type' => 'activity');
    $navigation = build_navigation($navlinks);

    print_header_simple("$strchoices", "", $navigation, "", "", true, "", navmenu($course));


    if (!$cms = get_coursemodules_in_course('choice', $course->id)) {
        notice(get_string('thereareno', 'moodle', $strchoices), "../../course/view.php?id=$course->id");
    }

    if ( !empty($USER->id) and $allanswers = get_records("choice_answers", "userid", $USER->id)) { // TODO: limit to choices from this course only
        foreach ($allanswers as $aa) {
            $answers[$aa->choiceid] = $aa;
        }

    } else {
        $answers = array () ;
    }


    $timenow = time();

    if ($course->format == "weeks") {
        $table->head  = array (get_string("week"), get_string("question"), get_string("answer"));
        $table->align = array ("center", "left", "left");
    } else if ($course->format == "topics") {
        $table->head  = array (get_string("topic"), get_string("question"), get_string("answer"));
        $table->align = array ("center", "left", "left");
    } else {
        $table->head  = array (get_string("question"), get_string("answer"));
        $table->align = array ("left", "left");
    }

    $currentsection = "";

    $modinfo = get_fast_modinfo($course);
    foreach ($modinfo->instances['choice'] as $cm) {
        if (!$cm->uservisible) {
            continue;
        }

        $printsection = "";
        if ($cm->sectionnum !== $currentsection) {
            if ($cm->sectionnum) {
                $printsection = $cm->sectionnum;
            }
            if ($currentsection !== "") {
                $table->data[] = 'hr';
            }
            $currentsection = $cm->sectionnum;
        }

        $class = $cm->visible ? '' : 'class="dimmed"';
        $tt_href = "<a $class href=\"view.php?id=$cm->id\">".format_string($cm->name)."</a>";

        if (!empty($answers[$cm->instance])) {
            $answer = $answers[$cm->instance];
        } else {
            $answer = "";
        }
        if (!empty($answer->optionid)) {
            $aa = format_string(choice_get_option_text(null, $answer->optionid));
        } else {
            $aa = "";
        }

        if ($course->format == "weeks" || $course->format == "topics") {
            $table->data[] = array ($printsection, $tt_href, $aa);
        } else {
            $table->data[] = array ($tt_href, $aa);
        }
    }
    echo "<br />";
    print_table($table);

    print_footer($course);

?>
