<?PHP // $Id$

/// index.php for attendance module
/// This page lists all the instances of NEWMODULE in a particular course
/// Replace NEWMODULE with the name of your module

    require_once("../../config.php");
    require_once("lib.php");

    require_variable($id);   // course

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID is incorrect");
    }

    require_login($course->id);

    add_to_log($course->id, "attendance", "viewall", "index.php?id=$course->id", "");


/// Get all required strings

    $strattendances = get_string("modulenameplural", "attendance");
    $strattendance  = get_string("modulename", "attendance");


/// Print the header

    if ($course->category) {
        $navigation = "<A HREF=\"../../course/view.php?id=$course->id\">$course->shortname</A> ->";
    }

    print_header("$course->shortname: $strattendance", "$course->fullname", "$navigation $strattendances", "", "", true, "", navmenu($course));

/// Get all the appropriate data

    if (! $attendances = get_all_instances_in_course("attendance", $course)) {
        notice("There are no attendances", "../../course/view.php?id=$course->id");
        die;
    }

/// Print the list of instances (your module will probably extend this)

    $timenow = time();
    $strname  = get_string("name");
    $strweek  = get_string("week");
    $strtopic  = get_string("topic");

    if ($course->format == "weeks") {
        $table->head  = array ($strweek, $strname);
        $table->align = array ("CENTER", "LEFT");
    } else if ($course->format == "topics") {
        $table->head  = array ($strtopic, $strname);
        $table->align = array ("CENTER", "LEFT", "LEFT", "LEFT");
    } else {
        $table->head  = array ($strname);
        $table->align = array ("LEFT", "LEFT", "LEFT");
    }

   if ($attendances) foreach ($attendances as $attendance) {
        if (!$attendance->visible) {
            //Show dimmed if the mod is hidden
            $link = "<A class=\"dimmed\" HREF=\"view.php?id=$attendance->coursemodule\">$attendance->name</A>";
        } else {
            //Show normal if the mod is visible
            $link = "<A HREF=\"view.php?id=$attendance->coursemodule\">$attendance->name</A>";
        }

        if ($course->format == "weeks" or $course->format == "topics") {
            $table->data[] = array ($attendance->section, $link);
        } else {
            $table->data[] = array ($link);
        }
    }

    echo "<BR>";

    print_table($table);

/// Finish the page

    print_footer($course);

?>
