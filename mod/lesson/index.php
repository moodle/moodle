<?PHP // $Id$

/// This page lists all the instances of lesson in a particular course

    require_once("../../config.php");
    require_once("lib.php");

    require_variable($id);   // course

    if (!$course = get_record("course", "id", $id)) {
        error("Course ID is incorrect");
    }

    require_login($course->id);

    add_to_log($course->id, "lesson", "view all", "index.php?id=$course->id", "");


/// Get all required strings

    $strlessons = get_string("modulenameplural", "lesson");
    $strlesson  = get_string("modulename", "lesson");


/// Print the header

    if ($course->category) {
        $navigation = "<A HREF=\"../../course/view.php?id=$course->id\">$course->shortname</A> ->";
    }

    print_header("$course->shortname: $strlessons", "$course->fullname", "$navigation $strlessons", "", "", true, "", navmenu($course));

/// Get all the appropriate data

    if (! $lessons = get_all_instances_in_course("lesson", $course)) {
        notice("There are no lessons", "../../course/view.php?id=$course->id");
        die;
    }

/// Print the list of instances (your module will probably extend this)

    $timenow = time();
    $strname  = get_string("name");
    $strgrade  = get_string("grade");
    $strdeadline  = get_string("deadline", "lesson");
    $strweek  = get_string("week");
    $strtopic  = get_string("topic");

    if ($course->format == "weeks") {
        $table->head  = array ($strweek, $strname, $strgrade, $strdeadline);
        $table->align = array ("CENTER", "LEFT", "CENTER", "CENTER");
    } else if ($course->format == "topics") {
        $table->head  = array ($strtopic, $strname, $strgrade, $strdeadline);
        $table->align = array ("CENTER", "LEFT", "CENTER", "CENTER");
    } else {
        $table->head  = array ($strname, $strgrade, $strdeadline);
        $table->align = array ("LEFT", "CENTER", "CENTER");
    }

    foreach ($lessons as $lesson) {
        if (!$lesson->visible) {
            //Show dimmed if the mod is hidden
            $link = "<A class=\"dimmed\" HREF=\"view.php?id=$lesson->coursemodule\">$lesson->name</A>";
        } else {
            //Show normal if the mod is visible
            $link = "<A HREF=\"view.php?id=$lesson->coursemodule\">$lesson->name</A>";
        }

        if ($lesson->deadline > $timenow) {
            $due = userdate($lesson->deadline);
        } else {
            $due = "<FONT COLOR=\"red\">".userdate($lesson->deadline)."</FONT>";
        }

        if ($course->format == "weeks" or $course->format == "topics") {
            if (isteacher($course->id)) {
                $grade_value = $lesson->grade;
            } else {
                // it's a student, show their mean or maximum grade
                if ($lesson->usemaxgrade) {
                    $grade = get_record_sql("SELECT MAX(grade) as grade FROM {$CFG->prefix}lesson_grades 
                            WHERE lessonid = $lesson->id AND userid = $USER->id GROUP BY userid");
                } else {
                    $grade = get_record_sql("SELECT AVG(grade) as grade FROM {$CFG->prefix}lesson_grades 
                            WHERE lessonid = $lesson->id AND userid = $USER->id GROUP BY userid");
                }
                if ($grade) {
                    // grades are stored as percentages
                    $grade_value = number_format($grade->grade * $lesson->grade / 100, 1);
                } else {
                    $grade_value = 0;
                }
            }
            $table->data[] = array ($lesson->section, $link, $grade_value, $due);
        } else {
            $table->data[] = array ($link, $grade_value, $due);
        }
    }

    echo "<BR>";

    print_table($table);

/// Finish the page

    print_footer($course);

?>
