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
    $strphase = get_string("phase", "exercise");
    $strgrade = get_string("grade");
    $strdeadline = get_string("deadline", "exercise");
	$strsubmitted = get_string("submitted", "assignment");

	print_header("$course->shortname: $strexercises", "$course->fullname", "$navigation $strexercises", "", "", true, "", navmenu($course));

    if (! $exercises = get_all_instances_in_course("exercise", $course)) {
        notice("There are no exercises", "../../course/view.php?id=$course->id");
        die;
    }

    $timenow = time();

    if ($course->format == "weeks") {
        if (isteacher($course->id)) {
            $table->head  = array ($strweek, $strname, $strtitle, $strphase, $strsubmitted, $strdeadline);
        } else {
            $table->head  = array ($strweek, $strname, $strtitle, $strgrade, $strsubmitted, $strdeadline);
        }
        $table->align = array ("CENTER", "LEFT", "LEFT","center","LEFT", "LEFT");
    } else if ($course->format == "topics") {
        if (isteacher($course->id)) {
            $table->head  = array ($strtopic, $strname, $strtitle, $strphase, $strsubmitted, $strdeadline);
        } else {
            $table->head  = array ($strtopic, $strname, $strtitle, $strgrade, $strsubmitted, $strdeadline);
        }
        $table->align = array ("CENTER", "LEFT", "LEFT", "center", "LEFT", "LEFT");
    } else {
        $table->head  = array ($strname, $strsubmitted, $strdeadline);
        $table->align = array ("LEFT", "LEFT", "LEFT");
    }

    foreach ($exercises as $exercise) {
		if ($exercise->deadline > $timenow) {
            $due = userdate($exercise->deadline);
        } else {
            $due = "<FONT COLOR=\"red\">".userdate($exercise->deadline)."</FONT>";
        }
        if ($submissions = exercise_get_user_submissions($exercise, $USER)) {
            foreach ($submissions as $submission) {
				if ($submission->late) {
					$submitted = "<FONT COLOR=\"red\">".userdate($submission->timecreated)."</FONT>";
					} 
				else {
					$submitted = userdate($submission->timecreated);
					}
				$link = "<A HREF=\"view.php?id=$exercise->coursemodule\">$exercise->name</A>";
				$title = $submission->title;
				if ($course->format == "weeks" or $course->format == "topics") {
                    if (isteacher($course->id)) {
                        $phase = '';
                        switch ($exercise->phase) {
                            case 1: $phase = get_string("phase1short", "exercise");
                                    break;
                            case 2: $phase = get_string("phase2short", "exercise");
                                    break;
                            case 3: $phase = get_string("phase3short", "exercise");
                                    break;
                        }
					    $table->data[] = array ($exercise->section, $link, $title, $phase, 
                                $submitted, $due);
                    } else {
                        $assessed = false; 
                        if ($exercise->usemaximum) {
                            $maximum = exercise_get_best_grade($submission);
                            if (isset($maximum)) {
                                $grade = $maximum->grade;
                                $assessed = true;
                            }
                        }else { // use mean value
                            $mean = exercise_get_mean_grade($submission);
                            if (isset($mean->grade)) {
                                $grade = $mean->grade;
                                $assessed = true;
                            }
                        }
                        if ($assessed) {
                            $actualgrade = number_format($grade * $exercise->grade / 100.0, 1);
                            if ($submission->late) {
                                $actualgrade = "<font color=\"red\">(".$actualgrade.")<font color=\"red\">";
                            } else {
                            }
    					    $table->data[] = array ($exercise->section, $link, $title, 
                                    $actualgrade, $submitted, $due);
                        } else {
    					    $table->data[] = array ($exercise->section, $link, $title, 
                                    "-", $submitted, $due);
                        }
					} 
                }
				else {
					$table->data[] = array ($link, $submitted, $due);
				}
			}
		}
		else {
            $submitted = get_string("no");
			$title = '';
			$link = "<A HREF=\"view.php?id=$exercise->coursemodule\">$exercise->name</A>";
			if ($course->format == "weeks" or $course->format == "topics") {
                if (isteacher($course->id)) {
				    $table->data[] = array ($exercise->section, $link, $title, $exercise->phase, 
                            $submitted, $due);
                } else {
    				$table->data[] = array ($exercise->section, $link, $title, "-", $submitted, $due);
				} 
            } else {
				$table->data[] = array ($link, $submitted, $due);
			}
		}
	}
    echo "<BR>";

    print_table($table);

    print_footer($course);
?>
