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

    if (! $resources = get_all_instances_in_course("resource", $course)) {
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
    foreach ($resources as $resource) {
        if ($course->format == "weeks" or $course->format == "topics") {
            $printsection = "";
            if ($resource->section !== $currentsection) {
                if ($resource->section) {
                    $printsection = $resource->section;
                }
                if ($currentsection !== "") {
                    $table->data[] = 'hr';
                }
                $currentsection = $resource->section;
            }
        } else {
            $printsection = '<span class="smallinfo">'.userdate($resource->timemodified)."</span>";
        }
        if (!empty($resource->extra)) {
            $extra = urldecode($resource->extra);
        } else {
            $extra = "";
        }
        if (!$resource->visible) {      // Show dimmed if the mod is hidden
            $table->data[] = array ($printsection, 
                    "<a class=\"dimmed\" $extra href=\"view.php?id=$resource->coursemodule\">".format_string($resource->name,true)."</a>",
                    format_text($resource->summary, FORMAT_MOODLE, $options) );

        } else {                        //Show normal if the mod is visible
            $table->data[] = array ($printsection, 
                    "<a $extra href=\"view.php?id=$resource->coursemodule\">".format_string($resource->name,true)."</a>",
                    format_text($resource->summary, FORMAT_MOODLE, $options) );
        }
    }

    echo "<br />";

    print_table($table);

    print_footer($course);

?>
