<?php // $Id$

    require_once("../../config.php");
    require_once("lib.php");

    $id = required_param('id', PARAM_INT);   // course

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID is incorrect");
    }

    require_course_login($course);
    add_to_log($course->id, "assignment", "view all", "index.php?id=$course->id", "");

    $strassignments = get_string("modulenameplural", "assignment");
    $strassignment = get_string("modulename", "assignment");
    $strassignmenttype = get_string("assignmenttype", "assignment");
    $strweek = get_string("week");
    $strtopic = get_string("topic");
    $strname = get_string("name");
    $strduedate = get_string("duedate", "assignment");
    $strsubmitted = get_string("submitted", "assignment");
    $strgrade = get_string("grade");

    $crumbs[] = array('name' => $strassignments, 'link' => '', 'type' => 'activity');
    $navigation = build_navigation($crumbs, $course);

    print_header_simple($strassignments, "", $navigation, "", "", true, "", navmenu($course));

    if (! $assignments = get_all_instances_in_course("assignment", $course)) {
        notice(get_string('noassignments', 'assignment'), "../../course/view.php?id=$course->id");
        die;
    }

    $timenow = time();

    if ($course->format == "weeks") {
        $table->head  = array ($strweek, $strname, $strassignmenttype, $strduedate, $strsubmitted, $strgrade);
        $table->align = array ("center", "left", "left", "left", "right");
    } else if ($course->format == "topics") {
        $table->head  = array ($strtopic, $strname, $strassignmenttype, $strduedate, $strsubmitted, $strgrade);
        $table->align = array ("center", "left", "left", "left", "right");
    } else {
        $table->head  = array ($strname, $strassignmenttype, $strduedate, $strsubmitted, $strgrade);
        $table->align = array ("left", "left", "left", "right");
    }

    $currentgroup = get_current_group($course->id);
    if ($currentgroup and has_capability('moodle/site:accessallgroups', get_context_instance(CONTEXT_COURSE, $id))) {
        $group = groups_get_group($currentgroup, false);
        $groupname = " ($group->name)";
    } else {
        $groupname = "";
    }

    $currentsection = "";

    $types = assignment_types();

    foreach ($assignments as $assignment) {

        if (!file_exists($CFG->dirroot.'/mod/assignment/type/'.$assignment->assignmenttype.'/assignment.class.php')) {
            continue;
        }

        require_once ($CFG->dirroot.'/mod/assignment/type/'.$assignment->assignmenttype.'/assignment.class.php');
        $assignmentclass = 'assignment_'.$assignment->assignmenttype;
        $assignmentinstance = new $assignmentclass($assignment->coursemodule);
    
        $submitted = $assignmentinstance->submittedlink();

        $grade = '-';
        if ($submission = $assignmentinstance->get_submission($USER->id)) {
            if ($submission->timemarked) {
                $grade = $assignmentinstance->display_grade($submission->grade);
            }
        }

        $type = $types[$assignment->assignmenttype];

        $due = $assignment->timedue ? userdate($assignment->timedue) : '-';
        if (!$assignment->visible) {
            //Show dimmed if the mod is hidden
            $link = "<a class=\"dimmed\" href=\"view.php?id=$assignment->coursemodule\">".format_string($assignment->name,true)."</a>";
        } else {
            //Show normal if the mod is visible
            $link = "<a href=\"view.php?id=$assignment->coursemodule\">".format_string($assignment->name,true)."</a>";
        }

        $printsection = "";
        if ($assignment->section !== $currentsection) {
            if ($assignment->section) {
                $printsection = $assignment->section;
            }
            if ($currentsection !== "") {
                $table->data[] = 'hr';
            }
            $currentsection = $assignment->section;
        }

        if ($course->format == "weeks" or $course->format == "topics") {
            $table->data[] = array ($printsection, $link, $type, $due, $submitted, $grade);
        } else {
            $table->data[] = array ($link, $type, $due, $submitted, $grade);
        }
    }

    echo "<br />";

    print_table($table);

    print_footer($course);
?>
