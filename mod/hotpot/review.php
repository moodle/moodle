<?PHP // $Id$

// This page prints a review of a particular quiz attempt

	require_once("../../config.php");
	require_once("lib.php");

	$id = optional_param("id"); // Course Module ID, or
	$hp = optional_param("hp"); // hotpot ID

	$attempt = required_param("attempt"); // A particular attempt ID for review

	if ($id) {
		if (! $cm = get_record("course_modules", "id", $id)) {
			error("Course Module ID was incorrect");
		}
		if (! $course = get_record("course", "id", $cm->course)) {
			error("Course is misconfigured");
		}
		if (! $hotpot = get_record("hotpot", "id", $cm->instance)) {
			error("Course module is incorrect");
		}
	
	} else {
		if (! $hotpot = get_record("hotpot", "id", $hp)) {
			error("Course module is incorrect");
		}
		if (! $course = get_record("course", "id", $hotpot->course)) {
			error("Course is misconfigured");
		}
		if (! $cm = get_coursemodule_from_instance("hotpot", $hotpot->id, $course->id)) {
			error("Course Module ID was incorrect");
		}
	}

	if (! $attempt = get_record("hotpot_attempts", "id", $attempt)) {
		error("No such attempt ID exists");
	}

	require_login($course->id);

	if (!isteacher($course->id)) {
		if (!$hotpot->review) {
			error(get_string("noreview", "quiz"));
		}
		//if (time() < $hotpot->timeclose) {
		//	error(get_string("noreviewuntil", "quiz", userdate($hotpot->timeclose)));
		//}
		if ($attempt->userid != $USER->id) {
			error("This is not your attempt!");
		}
	}

	add_to_log($course->id, "hotpot", "review", "review.php?id=$cm->id&attempt=$attempt->id", "$hotpot->id", "$cm->id");


// Print the page header

	$strmodulenameplural = get_string("modulenameplural", "hotpot");
	$strmodulename  = get_string("modulename", "hotpot");

	// print header
	$title = "$course->shortname: $hotpot->name";
	$heading = "$course->fullname";
	$navigation = "<a href=\"index.php?id=$course->id\">$strmodulenameplural</a> -> ".get_string("review", "quiz");
	if ($course->category) {
		$navigation = "<a href=\"../../course/view.php?id=$course->id\">$course->shortname</a> -> $navigation";
	}
	$button = update_module_button($cm->id, $course->id, $strmodulename);

	print_header($title, $heading, $navigation, "", "", true, $button, navmenu($course, $cm));


	echo '<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>'; // for overlib

	print_heading($hotpot->name);

	// format attempt properties
	if (!empty($attempt->timefinish)) {
		$attempt->timecompleted = userdate($attempt->timefinish);
		$attempt->timetaken = format_time($attempt->timefinish - $attempt->timestart);
	} else {
		$attempt->timecompleted = '-';
		$attempt->timetaken = '-';
	}
	$attempt->score = hotpot_format_score($attempt);

	$html  = '';

	// start table
	$html .= '<table width="100%" border="1" valign="top" align="center" cellpadding="2" cellspacing="2" class="generaltable">'."\n";

	// add attempt properties
	$fields = array('attempt', 'score', 'penalties', 'timetaken', 'timecompleted');
	foreach ($fields as $field) {
		if (isset($attempt->$field)) {
			$module = ($field=='penalties') ? 'hotpot' : 'quiz';
			$html .= '<tr><th align="right" width="100" class="generaltableheader">'.get_string($field, $module).':</th><td class="generaltablecell">'.$attempt->$field.'</td></tr>';
		}
	}

	// finish table
	$html .= '</table>';

	print_simple_box_start("center", "80%", "#ffffff", 0);
	print $html;
	print_simple_box_end();

	print_continue("report.php?id=$cm->id");
	hotpot_print_attempt_details($hotpot, $attempt);
	print_continue("report.php?id=$cm->id");

	print_footer($course);

///////////////////////////
//    functions
///////////////////////////

function hotpot_print_attempt_details(&$hotpot, &$attempt) {

	// define fields to print
	$textfields = array('correct', 'ignored', 'wrong');
	$numfields = array('score', 'weighting', 'hints', 'clues', 'checks');

	$fields = array_merge($textfields, $numfields);

	$q = array(); // questions
	$f = array(); // fields
	foreach ($fields as $field) {
		$name = get_string($field, 'hotpot');
		$f[$field] = array('count'=>0, 'name'=>$name);
	}

	// get questions and responses for this attempt
	$questions = get_records_select('hotpot_questions', "hotpot='$hotpot->id'", 'id');
	$responses = get_records_select('hotpot_responses', "attempt='$attempt->id'", 'id');

	if ($questions && $responses) {
		foreach ($responses as $response) {
			$id = $response->question;
			foreach ($fields as $field) {
				if (!isset($f[$field])) {
					$name = get_string($field, 'hotpot');
					$f[$field] = array('count'=>0, 'name'=>$name);
				}
				if (isset($response->$field)) {
					$f[$field]['count']++;

					if (!isset($q[$id])) {
						$name = hotpot_get_question_name($questions[$id]);
						$q[$id] = array('name'=>$name);
					}

					$q[$id][$field] = $response->$field;
				}
			}
		}
	}

	// count the number of columns required in the table
	$colspan = 0;
	foreach ($numfields as $field) {
		if ($f[$field]['count']) {
			$colspan += 2;
		}
	}
	$colspan = max(2, $colspan);

	$html  = '';

	// start table of questions and responses
	$html .= '<table width="100%" border="1" valign="top" align="center" cellpadding="2" cellspacing="2" class="generaltable">'."\n";

	if (empty($q)) {
		$html .= '<tr><td align="center" class="generaltablecell"><b>'.get_string("noresponses", "hotpot").'</b></td></tr>';

	} else {
		foreach ($q as $question) {

			// flag to ensure questions are only printed when there is at least one response
			$printedquestion = false;
			
			// add rows of text fields
			foreach ($textfields as $field) {
				if (isset($question[$field])) {
					$text = hotpot_strings($question[$field]);
					if (trim($text)) {

						// print question if necessary
						if (!$printedquestion) {
							$html .= '<tr><td colspan="'.$colspan.'" class="generaltablecell"><b>'.$question['name'].'</b></td></tr>';
							$printedquestion = true;
						}

						// print response
						$html .= '<tr><th align="right" width="100" class="generaltableheader">'.$f[$field]['name'].':</th><td colspan="'.($colspan-1).'" class="generaltablecell">'.$text.'</td></tr>';
					}
				}
			}
	
			// add row of numeric fields
			$html .= '<tr>';
			foreach ($numfields as $field) {
				if ($f[$field]['count']) {

					// print question if necessary
					if (!$printedquestion) {
						$html .= '<tr><td colspan="'.$colspan.'" class="generaltablecell"><b>'.$question['name'].'</b></td></tr>';
						$printedquestion = true;
					}

					// print numeric response
					$value = isset($question[$field]) ? $question[$field] : '-';
					$html .= '<th align="right" width="100" class="generaltableheader">'.$f[$field]['name'].':</th><td class="generaltablecell">'.$value.'</td>';
				}
			}
			$html .= '</tr>';
	
			// add separator
			if ($printedquestion) {
				$html .= '<tr><td colspan="'.$colspan.'"><div class="tabledivider"></div></td></tr>';
			}
	
		} // foreach $q
	}

	// finish table
	$html .= '</table>';

	print_simple_box_start("center", "80%", "#ffffff", 0);
	print $html;
	print_simple_box_end();
}

?>
