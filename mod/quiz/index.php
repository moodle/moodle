<?PHP // $Id$

// This page lists all the instances of quiz in a particular course

    require("../../config.php");
    require("lib.php");

    require_variable($id);   // course

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID is incorrect");
    }

    require_login($course->id);

    add_to_log($course->id, "quiz", "view all", "index.php?id=$course->id", "");


// Print the header

    $strquizzes = get_string("modulenameplural", "quiz");
    $strquiz  = get_string("modulename", "quiz");

    if ($course->category) {
        $navigation = "<A HREF=\"../../course/view.php?id=$course->id\">$course->shortname</A> ->";
    }

    print_header("$course->shortname: $strquizzes", "$course->fullname", "$navigation $strquizzes");

// Get all the appropriate data

    if (! $quizzes = get_all_instances_in_course("quiz", $course->id, "cw.section ASC")) {
        notice("There are no quizzes", "../../course/view.php?id=$course->id");
        die;
    }

// Print the list of instances (your module will probably extend this)

    $timenow = time();
    $strname  = get_string("name");
    $strweek  = get_string("week");
    $strtopic  = get_string("topic");
    $strgrades  = get_string("grades");

    if ($course->format == "weeks") {
        $table->head  = array ($strweek, $strname, $strgrades);
        $table->align = array ("CENTER", "LEFT");
        $table->width = array (10, "*", 10);
    } else if ($course->format == "topics") {
        $table->head  = array ($strtopic, $strname, $strgrades);
        $table->align = array ("CENTER", "LEFT", "LEFT", "LEFT");
        $table->width = array (10, "*", 10);
    } else {
        $table->head  = array ($strname, $strgrades);
        $table->align = array ("LEFT", "LEFT");
        $table->width = array ("*", 10);
    }

    foreach ($quizzes as $quiz) {
        $link = "<A HREF=\"view.php?id=$quiz->coursemodule\">$quiz->name</A>";

        if ($course->format == "weeks" or $course->format == "topics") {
            $table->data[] = array ($quiz->section, $link);
        } else {
            $table->data[] = array ($link);
        }
    }

    echo "<BR>";

    print_table($table);

// Finish the page

    print_footer($course);

?>
