<?PHP // $Id$

    require("../../config.php");
    require("lib.php");

    require_variable($id);   // course

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID is incorrect");
    }

    require_login($course->id);
    add_to_log($course->id, "exercise", "view all", "index.php?id=$course->id", "");

    if ($course->category) {
        $navigation = "<A HREF=\"../../course/view.php?id=$course->id\">$course->shortname</A> ->";
    }

    $strexercises = get_string("modulenameplural", "exercise");
    $strexercise = get_string("modulename", "exercise");
    $strweek = get_string("week");
    $strtopic = get_string("topic");
    $strname = get_string("name");
	$strtitle = get_string("title", "exercise");
    $strdeadline = get_string("deadline", "exercise");
	$strsubmitted = get_string("submitted", "assignment");

	print_header("$course->shortname: $strexercises", "$course->fullname", "$navigation $strexercises", "", "", true, "", navmenu($course));

    if (! $exercises = get_all_instances_in_course("exercise", $course)) {
        notice("There are no exercises", "../../course/view.php?id=$course->id");
        die;
    }

    $timenow = time();

    if ($course->format == "weeks") {
        $table->head  = array ($strweek, $strname, $strtitle, $strsubmitted, $strdeadline);
        $table->align = array ("CENTER", "LEFT", "LEFT","LEFT", "LEFT");
    } else if ($course->format == "topics") {
        $table->head  = array ($strtopic, $strname, $strtitle, $strsubmitted, $strdeadline);
        $table->align = array ("CENTER", "LEFT", "LEFT", "LEFT", "LEFT");
    } else {
        $table->head  = array ($strname, $strsubmitted, $strdeadline);
        $table->align = array ("LEFT", "LEFT", "LEFT");
    }

    foreach ($exercises as $exercise) {
        if ($submissions = exercise_get_user_submissions($exercise, $USER)) {
			foreach ($submissions as $submission) {
				if ($submission->timecreated <= $exercise->deadline) {
					$submitted = userdate($submission->timecreated);
					} 
				else {
					$submitted = "<FONT COLOR=red>".userdate($submission->timecreated)."</FONT>";
					}
				$due = userdate($exercise->deadline);
				$link = "<A HREF=\"view.php?id=$exercise->coursemodule\">$exercise->name</A>";
				$title = $submission->title;
				if ($course->format == "weeks" or $course->format == "topics") {
					$table->data[] = array ($exercise->section, $link, $title, $submitted, $due);
					} 
				else {
					$table->data[] = array ($link, $submitted, $due);
					}
				}
			}
		else {
            $submitted = get_string("no");
			$title = '';
			$due = userdate($exercise->deadline);
			$link = "<A HREF=\"view.php?id=$exercise->coursemodule\">$exercise->name</A>";
			if ($course->format == "weeks" or $course->format == "topics") {
				$table->data[] = array ($exercise->section, $link, $title, $submitted, $due);
				} 
			else {
				$table->data[] = array ($link, $submitted, $due);
				}
			}
		}
    echo "<BR>";

    print_table($table);

    print_footer($course);
?>
