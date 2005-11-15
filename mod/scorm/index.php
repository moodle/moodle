<?php // $Id$

    require_once("../../config.php");

    $id = required_param('id', '', PARAM_INT);   // course id

    if (!empty($id)) {
        if (! $course = get_record("course", "id", $id)) {
            error("Course ID is incorrect");
        }
    } else {
        error('A required parameter is missing');
    }

    require_course_login($course);

    add_to_log($course->id, "scorm", "view all", "index.php?id=$course->id", "");

    $strscorm = get_string("modulename", "scorm");
    $strscorms = get_string("modulenameplural", "scorm");
    $strweek = get_string("week");
    $strtopic = get_string("topic");
    $strname = get_string("name");
    $strsummary = get_string("summary");
    $strreport = get_string("report",'scorm');
    $strlastmodified = get_string("lastmodified");

    print_header_simple("$strscorms", "", "$strscorms",
                 "", "", true, "", navmenu($course));

    if ($course->format == "weeks" or $course->format == "topics") {
        $sortorder = "cw.section ASC";
    } else {
        $sortorder = "m.timemodified DESC";
    }

    if (! $scorms = get_all_instances_in_course("scorm", $course)) {
        notice("There are no scorms", "../../course/view.php?id=$course->id");
        exit;
    }

    if ($course->format == "weeks") {
        $table->head  = array ($strweek, $strname, $strsummary, $strreport);
        $table->align = array ("center", "left", "left", "left");
    } else if ($course->format == "topics") {
        $table->head  = array ($strtopic, $strname, $strsummary, $strreport);
        $table->align = array ("center", "left", "left", "left");
    } else {
        $table->head  = array ($strlastmodified, $strname, $strsummary, $strreport);
        $table->align = array ("left", "left", "left", "left");
    }

    foreach ($scorms as $scorm) {

        $tt = "";
        if ($course->format == "weeks" or $course->format == "topics") {
            if ($scorm->section) {
                $tt = "$scorm->section";
            }
        } else {
            $tt = userdate($scorm->timemodified);
        }
        $report = '&nbsp;';
        if (isteacher($course->id)) {
            $trackedusers = get_record('scorm_scoes_track', 'scormid', $scorm->id, '', '', '', '', 'count(distinct(userid)) as c');
            if ($trackedusers->c > 0) {
                $report = '<a href="report.php?a='.$scorm->id.'">'.get_string('viewallreports','scorm',$trackedusers->c).'</a></div>';
            } else {
                $report = get_string('noreports','scorm');
            }
        } else if (isstudent($course->id)) {
           require_once('locallib.php');
           $report = scorm_grade_user(get_records('scorm_scoes','scorm',$scorm->id), $USER->id, $scorm->grademethod);
        }
        if (!$scorm->visible) {
           //Show dimmed if the mod is hidden
           $table->data[] = array ($tt, "<a class=\"dimmed\" href=\"view.php?id=$scorm->coursemodule\">".format_string($scorm->name,true)."</a>",
                                   format_text($scorm->summary), $report);
        } else {
           //Show normal if the mod is visible
           $table->data[] = array ($tt, "<a href=\"view.php?id=$scorm->coursemodule\">".format_string($scorm->name,true)."</a>",
                                   format_text($scorm->summary), $report);
        }
    }

    echo "<br />";

    print_table($table);

    print_footer($course);


?>

