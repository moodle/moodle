<?PHP // $Id$

    require("../../config.php");
    require("lib.php");

    require_variable($id);   // course

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID is incorrect");
    }

    require_login($course->id);
    add_to_log($course->id, "workshop", "view all", "index.php?id=$course->id", "");

    if ($course->category) {
        $navigation = "<A HREF=\"../../course/view.php?id=$course->id\">$course->shortname</A> ->";
    }

    $strworkshops = get_string("modulenameplural", "workshop");
    $strworkshop = get_string("modulename", "workshop");
    $strweek = get_string("week");
    $strtopic = get_string("topic");
    $strname = get_string("name");
    $strdeadline = get_string("deadline", "workshop");
	$strsubmitted = get_string("submitted", "assignment");

	print_header("$course->shortname: $strworkshops", "$course->fullname", "$navigation $strworkshops", "", "", true, "", navmenu($course));

    if (! $workshops = get_all_instances_in_course("workshop", $course)) {
        notice("There are no workshops", "../../course/view.php?id=$course->id");
        die;
    }

    $timenow = time();

    if ($course->format == "weeks") {
        $table->head  = array ($strweek, $strname, $strdeadline, $strsubmitted);
        $table->align = array ("CENTER", "LEFT", "LEFT", "LEFT");
    } else if ($course->format == "topics") {
        $table->head  = array ($strtopic, $strname, $strdeadline, $strsubmitted);
        $table->align = array ("CENTER", "LEFT", "LEFT", "LEFT");
    } else {
        $table->head  = array ($strname, $strdeadline, $strsubmitted);
        $table->align = array ("LEFT", "LEFT", "LEFT");
    }

    foreach ($workshops as $workshop) {
        if ($submissions = workshop_get_user_submissions($workshop, $USER)) {
			foreach ($submissions as $submission) {
				if ($submission->timecreated <= $workshop->deadline) {
					$submitted = userdate($submission->timecreated);
					} 
				else {
					$submitted = "<FONT COLOR=red>".userdate($submission->timecreated)."</FONT>";
					}
				$due = userdate($workshop->deadline);
                                if (!$workshop->visible) {
                                    //Show dimmed if the mod is hidden
				    $link = "<A class=\"dimmed\" HREF=\"view.php?id=$workshop->coursemodule\">$workshop->name</A><BR>".
					    "($submission->title)";
                                } else {
                                    //Show normal if the mod is visible
				    $link = "<A HREF=\"view.php?id=$workshop->coursemodule\">$workshop->name</A><BR>".
					    "($submission->title)";
                                }
				if ($course->format == "weeks" or $course->format == "topics") {
					$table->data[] = array ($workshop->section, $link, $due, $submitted);
					} 
				else {
					$table->data[] = array ($link, $due, $submitted);
					}
				}
			}
		else {
            $submitted = get_string("no");
			$due = userdate($workshop->deadline);
                        if (!$workshop->visible) {
                            //Show dimmed if the mod is hidden
                            $link = "<A class=\"dimmed\" HREF=\"view.php?id=$workshop->coursemodule\">$workshop->name</A>";
                        } else {
                            //Show normal if the mod is visible
                            $link = "<A HREF=\"view.php?id=$workshop->coursemodule\">$workshop->name</A>";
                        }
			if ($course->format == "weeks" or $course->format == "topics") {
				$table->data[] = array ($workshop->section, $link, $due, $submitted);
				} 
			else {
				$table->data[] = array ($link, $due, $submitted);
				}
			}
		}
    echo "<BR>";

    print_table($table);

    print_footer($course);
?>
