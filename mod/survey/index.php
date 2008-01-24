<?php // $Id$

    require_once("../../config.php");
    require_once("lib.php");

    $id = required_param('id', PARAM_INT);    // Course Module ID

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID is incorrect");
    }

    require_course_login($course);

    add_to_log($course->id, "survey", "view all", "index.php?id=$course->id", "");

    $strsurveys = get_string("modulenameplural", "survey");
    $strweek = get_string("week");
    $strtopic = get_string("topic");
    $strname = get_string("name");
    $strstatus = get_string("status");
    $strdone  = get_string("done", "survey");
    $strnotdone  = get_string("notdone", "survey");

    $navlinks = array();
    $navlinks[] = array('name' => $strsurveys, 'link' => '', 'type' => 'activity');
    $navigation = build_navigation($navlinks);

    print_header_simple("$strsurveys", "", $navigation,
                 "", "", true, "", navmenu($course));

    if (!$cms = get_coursemodules_in_course('survey', $course->id)) {
        notice(get_string('thereareno', 'moodle', $strsurveys), "../../course/view.php?id=$course->id");
    }

    if ($course->format == "weeks") {
        $table->head  = array ($strweek, $strname, $strstatus);
        $table->align = array ("CENTER", "LEFT", "LEFT");
    } else if ($course->format == "topics") {
        $table->head  = array ($strtopic, $strname, $strstatus);
        $table->align = array ("CENTER", "LEFT", "LEFT");
    } else {
        $table->head  = array ($strname, $strstatus);
        $table->align = array ("LEFT", "LEFT");
    }

    $currentsection = '';

    foreach ($cms as $cm) {
        if (!coursemodule_visible_for_user($cm)) {
            continue;
        }

        if (!empty($USER->id) and survey_already_done($cm->instance, $USER->id)) {
            $ss = $strdone;
        } else {
            $ss = $strnotdone;
        }
        $printsection = "";
        if ($cm->section !== $currentsection) {
            if ($cm->section) {
                $printsection = $cm->section;
            }
            if ($currentsection !== "") {
                $table->data[] = 'hr';
            }
            $currentsection = $cm->section;
        }
        //Calculate the href
        $class = $cm->visible ? '' : 'class="dimmed"';
        $tt_href = "<a $class href=\"view.php?id=$cm->id\">".format_string($cm->name,true)."</a>";

        if ($course->format == "weeks" or $course->format == "topics") {
            $table->data[] = array ($printsection, $tt_href, "<a href=\"view.php?id=$cm->id\">$ss</a>");
        } else {
            $table->data[] = array ($tt_href, "<a href=\"view.php?id=$cm->id\">$ss</a>");
        }
    }

    echo "<br />";
    print_table($table);
    print_footer($course);

?>
