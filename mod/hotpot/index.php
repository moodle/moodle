<?PHP // $Id$

// This page lists all the instances of hotpot in a particular course

	require_once("../../config.php");
	require_once("lib.php");

	$id = required_param("id");   // course

	if (! $course = get_record("course", "id", $id)) {
		error("Course ID is incorrect");
	}

	require_login($course->id);

	add_to_log($course->id, "hotpot", "view all", "index.php?id=$course->id", "");

	// Print the header

	$strmodulenameplural = get_string("modulenameplural", "hotpot");
	$strmodulename  = get_string("modulename", "hotpot");

	$title = "$course->shortname: $strmodulenameplural";
	$heading = "$course->fullname";
	$navigation = "$strmodulenameplural";
	if ($course->category) {
		$navigation = "<a href=\"../../course/view.php?id=$course->id\">$course->shortname</a> -> $navigation";
	}
	print_header($title, $heading, $navigation, "", "", true, "", navmenu($course));

	$next_url = "$CFG->wwwroot/course/view.php?id=$course->id";

	// Get all instances of this module
	if (! $hotpots = get_all_instances_in_course("hotpot", $course)) {
		notice("There are no $strmodulenameplural", $next_url);
		die;
	}

	if (isadmin()) {
		if (isset($_POST['regrade'])) {
			$hotpotids = array();
			foreach ($hotpots as $hotpot) {
				$hotpotids[] = $hotpot->id;
			}
			$hotpotids = implode(',', $hotpotids);

			$select = "hotpot IN ($hotpotids)";

			$questionids = array();
			if ($questions = get_records_select("hotpot_questions", $select)) {
				$questionids = array_keys($questions);
			}
			$questionids = implode(',', $questionids);

			if ($questionids) {
				hotpot_delete_and_notify(
					'hotpot_questions', 
					"id IN ($questionids)", 
					get_string('question', 'quiz')
				);
				hotpot_delete_and_notify(
					'hotpot_responses', 
					"question IN ($questionids)", 
					get_string('answer', 'quiz')
				);
			}

			if ($attempts = get_records_select('hotpot_attempts', $select)) {
				$count = 0;
				foreach ($attempts as $attempt) {
					if (isset($attempt->score)) {
						hotpot_add_attempt_details($attempt);
						$attempt->details = addslashes($attempt->details);
						if (! update_record('hotpot_attempts', $attempt)) {
							error("Could not update attempt record: ".$db->ErrorMsg(), $next_url);
						}
						$count++;
						if ($count%10 == 0) {
							print ".";
							if ($count%200 == 0) {
								print "<br>\n";
							}
							hotpot_flush(300);
						}
					}
				}
				if ($count) {
					notify(get_string('added', 'moodle', "$count x ".get_string('attempts', 'quiz')));
				}
				notify(get_string('regradecomplete', 'quiz'));
			}
		}
		print '<center><form action="'.$ME.'" method="post">';
		print '<input type="hidden" name="id" value="'.$course->id.'">';
		print '<input type="submit" name="regrade" value="'.get_string('regrade', 'quiz').'">';
		print '</form></center>'."\n";
	}

	// Print the list of instances of this module

	$timenow = time();
	$strupdate = get_string("update");
	$strusers  = get_string("users");

	// Moodle 1.4+ requires sesskey to be passed in forms
	if (isset($USER->sesskey)) {
		$sesskey = '<input type="hidden" name="sesskey" value="'.$USER->sesskey.'" />';
	} else {
		$sesskey = '';
	}

	// column headings and attributes
	$table->head = array();
	$table->align = array();

	switch ($course->format) {
		case 'weeks' : 
			$title = get_string("week");
			break;
		case 'topics' : 
			$title = get_string("topic");
			break;
		default : 
			$title = '';
			break;
	}
	if ($title) {
		array_push($table->head, $title); 
		array_push($table->align, "center");
	}
	if (isteacheredit($course->id)) {
		array_push($table->head, $strupdate);
		array_push($table->align, "center");
	}
	array_push($table->head, 
		get_string("name"), 
		get_string("quizcloses", "quiz"), 
		get_string("bestgrade", "quiz"), 
		get_string("attempts", "quiz")
	);
	array_push($table->align, "left", "left", "center", "left");

	$currentsection = "";
	foreach ($hotpots as $hotpot) {

		$printsection = "";
		if ($hotpot->section !== $currentsection) {
			if ($hotpot->section) {
				$printsection = $hotpot->section;
			}
			if ($currentsection !== "") {
				$table->data[] = 'hr';
			}
			$currentsection = $hotpot->section;
		}

		$class = ($hotpot->visible) ? '' : 'class="dimmed" ';
		$quizname = '<A '.$class.'href="view.php?id='.$hotpot->coursemodule.'">'.$hotpot->name.'</A>';
		$quizclose = userdate($hotpot->timeclose);
		
		$select = isteacher($course->id) ? '' : "userid='$USER->id' AND ";
		$select .= "hotpot='$hotpot->id' AND timefinish>0";

		$attempttable = "{$CFG->prefix}hotpot_attempts";

		// get number of attempts. if any
		if ($attemptcount = count_records_sql("SELECT COUNT(*) FROM $attempttable WHERE $select")) {

			// report number of attempts (and users)
			$report = get_string("viewallreports","quiz", $attemptcount);
			if (isteacher($course->id)) {
				$usercount = count_records_sql("SELECT COUNT(DISTINCT userid) FROM $attempttable WHERE $select");
				$report .= " ($usercount $strusers)";
			}
			$report = '<a href="report.php?hp='.$hotpot->id.'">'.$report.'</a>';

			// get best score
			$bestscore = count_records_sql("SELECT MAX(score) FROM $attempttable WHERE $select");
			if (is_numeric($bestscore)) {
				$bestscore .= " / $hotpot->grade";
			} else {
				$bestscore = "&nbsp;";
			}
		} else { // no attempts
			$report = "&nbsp;";
			$bestscore = "&nbsp;";
		}

		$data = array ();

		if ($course->format=="weeks" || $course->format=="topics") {
			array_push($data, $printsection);
		}

		if (isteacheredit($course->id)) {
			$update = ''
			.	'<form target="_parent" method="get" action="'.$CFG->wwwroot.'/course/mod.php">'
			.	'<input type="hidden" name="update" value="'.$hotpot->coursemodule.'" />'
			.	$sesskey
			.	'<input type="submit" value="'.$strupdate.'" />'
			.	'</form>'
			;
			array_push($data, $update);
		}
		array_push($data, $quizname, $quizclose, $bestscore, $report);
		$table->data[] = $data;
	}

	echo "<br />";

	print_table($table);

	// Finish the page
	print_footer($course);
	
///////////////////
//    functions

function hotpot_flush($n=0, $time=false) {
	if ($time) {
		$ti = strftime("%X",time());
	} else {
		$ti = "";
	}
	echo str_repeat(" ", $n) . $ti . "\n";
	flush();
}

	
?>
