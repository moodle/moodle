<?php // $Id$

    require_once("../../config.php");

    $id = required_param( 'id', PARAM_INT ); // course

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID is incorrect");
    }

    require_course_login($course, true);

    if ($course->id != SITEID) {
        require_login($course->id);
    }
    add_to_log($course->id, "resource", "view all", "index.php?id=$course->id", "");

    $strresource = get_string("modulename", "resource");
    $strresources = get_string("modulenameplural", "resource");
    $strweek = get_string("week");
    $strtopic = get_string("topic");
    $strname = get_string("name");
    $strsummary = get_string("summary");
    $strlastmodified = get_string("lastmodified");

    $navlinks = array();
    $navlinks[] = array('name' => $strresources, 'link' => '', 'type' => 'activityinstance');
    $navigation = build_navigation($navlinks);

    print_header("$course->shortname: $strresources", $course->fullname, $navigation,
                 "", "", true, "", navmenu($course));

    if (!$cms = get_coursemodules_in_course('resource', $course->id, 'm.timemodified, m.summary')) {
        notice(get_string('thereareno', 'moodle', $strresources), "../../course/view.php?id=$course->id");
        exit;
    }

    if ($course->format == "weeks") {
        $table->head  = array ($strweek, $strname, $strsummary);
        $table->align = array ("center", "left", "left");
    } else if ($course->format == "topics") {
        $table->head  = array ($strtopic, $strname, $strsummary);
        $table->align = array ("center", "left", "left");
    } else {
        $table->head  = array ($strlastmodified, $strname, $strsummary);
        $table->align = array ("left", "left", "left");
    }

    $currentsection = "";
    $options->para = false;

    $modinfo = get_fast_modinfo($course);
    foreach ($modinfo->instances['resource'] as $cm) {
        if (!$cm->uservisible) {
            continue;
        }

        $cm->summary      = $cms[$cm->id]->summary;
        $cm->timemodified = $cms[$cm->id]->timemodified;

        if ($course->format == "weeks" or $course->format == "topics") {
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
        } else {
            $printsection = '<span class="smallinfo">'.userdate($cm->timemodified)."</span>";
        }

        $class = $cm->visible ? '' : 'class="dimmed"';
        $table->data[] = array ($printsection,
                "<a $class href=\"view.php?id=$cm->id\">".format_string($cm->name)."</a>",
                format_text($cm->summary, FORMAT_MOODLE, $options) );
    }

    echo "<br />";

    print_table($table);

    print_footer($course);

?>
