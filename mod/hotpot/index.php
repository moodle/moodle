<?PHP // $Id$

// This page lists all the instances of hotpot in a particular course

	require_once("../../config.php");
	require_once("../../course/lib.php");
	require_once("lib.php");

	$id = required_param("id");   // course

	if (! $course = get_record("course", "id", $id)) {
		error("Course ID is incorrect");
	}

	require_login($course->id);

	add_to_log($course->id, "hotpot", "view all", "index.php?id=$course->id", "");

	// Moodle 1.4+ requires sesskey to be passed in forms
	if (isset($USER->sesskey)) {
		$sesskey = '<input type="hidden" name="sesskey" value="'.$USER->sesskey.'" />';
	} else {
		$sesskey = '';
	}

	// get message strings for titles
	$strmodulenameplural = get_string("modulenameplural", "hotpot");
	$strmodulename  = get_string("modulename", "hotpot");

	// string translation array for single and double quotes
	$quotes = array("'"=>"\'", '"'=>'&quot;');

	// Print the header

	$title = "$course->shortname: $strmodulenameplural";
	$heading = "$course->fullname";
	$navigation = "$strmodulenameplural";
	if ($course->category) {
		$navigation = "<a href=\"../../course/view.php?id=$course->id\">$course->shortname</a> -> $navigation";
	}
	print_header($title, $heading, $navigation, "", "", true, "", navmenu($course));

	$next_url = "$CFG->wwwroot/course/view.php?id=$course->id";

	// get display section, if any
	$section = optional_param('section', 0);
	if ($section) {
		$displaysection = course_set_display($course->id, $section);
	} else {
		if (isset($USER->display[$course->id])) {
			$displaysection = $USER->display[$course->id];
		} else {
			$displaysection = 0;
		}
	}
	
	// Get all instances of this module
	if (!$hotpots = hotpot_get_all_instances_in_course("hotpot", $course)) {
		$hotpots = array();
	}
	

	// if necessary, remove hotpots that are not in section0 or this $USER's display section
	if ($displaysection) {
		foreach ($hotpots as $cmid=>$hotpot) {
			if ($hotpot->section!=0 && $hotpot->section!=$displaysection) {
				unset($hotpots[$cmid]);
			}
		}
	}

	if (empty($hotpots)) {
		notice("There are no $strmodulenameplural", $next_url);
		exit;
	}

	// get list of hotpot ids
	$hotpotids = array();
	foreach ($hotpots as $cmid=>$hotpot) {
		$hotpotids[] = $hotpot->id;
	}
	$hotpotids = implode(',', $hotpotids);

	if (isadmin()) {

		// get regrade settings, if any
		$regrade = optional_param("regrade");
		$confirm = optional_param("confirm");

		// check regrade is valid
		unset($regrade_cmid);
		if (isset($regrade)) {
			foreach ($hotpots as $cmid=>$hotpot) {
				$found = false;
				if ($hotpot->id==$regrade) {
					$regrade_cmid = $cmid;
				}
			}
		}

		// regrade, if necessary
		if (isset($regrade_cmid)) {

			if (empty($confirm)) {

				$strregradecheck = get_string('regradecheck', 'hotpot', $hotpots[$regrade_cmid]->name);

				print_simple_box_start("center", "60%", "#FFAAAA", 20, "noticebox");
				print_heading($strregradecheck);
				print ''
				.	'<table border="0"><tr><td>'
				.	'<form target="_parent" method="post" action="'.$ME.'">'
				.	'<input type="hidden" name="id" value="'.$course->id.'">'
				.	'<input type="hidden" name="regrade" value="'.$regrade.'" />'
				.	'<input type="hidden" name="confirm" value="1" />'
				.	$sesskey
				.	'<input type="submit" value="'.get_string("yes").'" />'
				.	'</form>'
				.	'</td><td> &nbsp; </td><td>'
				.	'<form target="_parent" method="post" action="'.$ME.'">'
				.	'<input type="hidden" name="id" value="'.$course->id.'">'
				.	$sesskey
				.	'<input type="submit" value="'.get_string("no").'" />'
				.	'</form>'
				.	'</td></tr></table>'
				;
				print_simple_box_end();
				print_footer($course);
				exit;

			} else { // regrade has been confirmed, so proceed

				if ($regrade=='all') {
					$select = "hotpot IN ($hotpotids)";
				} else {
					$select = "hotpot=$regrade";
				}

				$questionids = array();
				if ($questions = get_records_select("hotpot_questions", $select)) {
					$questionids = array_keys($questions);
				}
				$questionids = implode(',', $questionids);

				if ($questionids) {
					hotpot_delete_and_notify('hotpot_questions', "id IN ($questionids)", get_string('question', 'quiz'));
					hotpot_delete_and_notify('hotpot_responses', "question IN ($questionids)", get_string('answer', 'quiz'));
				}

				if ($attempts = get_records_select('hotpot_attempts', $select)) {
				
					// start counter and timer
					$start = microtime();
					$count = 0;

					// use while loop instead of foreach loop
					// to allow the possibility of splitting a regrade 
					// and so avoid "maximum script time exceeded" errors
					$attemptids = array_keys($attempts);
					$i_max = count($attemptids);
					$i = 0;
					while ($i<$i_max) {

						$attemptid = $attemptids[$i];
						$attempt =&$attempts[$attemptid];

						$attempt->details = get_field('hotpot_details', 'details', 'attempt', $attemptid);
						if ($attempt->details) {

							hotpot_add_attempt_details($attempt);
							if (! update_record('hotpot_attempts', $attempt)) {
								error("Could not update attempt record: ".$db->ErrorMsg(), $next_url);
							}
						}
						$count++;
						$i++;
					}
					if ($count) {
						notify(get_string('added', 'moodle', "$count x ".get_string('attempts', 'quiz')));
					}
					$msg = get_string('regradecomplete', 'quiz');
					if (!empty($CFG->hotpot_showtimes)) {
						$duration = format_time(sprintf("%0.2f", microtime_diff($start, microtime())));
						$msg .= " ($duration)";
					}
					notify($msg);
				}
			}
		} // end regrade

		//print '<center><form action="'.$ME.'" method="post">';
		//print '<input type="hidden" name="id" value="'.$course->id.'">';
		//print '<input type="submit" name="regrade" value="'.get_string('regrade', 'quiz').'">';
		//print '</form></center>'."\n";


		// get duplicate hotpot-name questions
		//	- JMatch LHS is longer than 255 bytes
		//	- JQuiz question text is longer than 255 bytes
		//	- other unidentified situations ?!?

		$field = '';
		$questions = false;
		$regradehotpots = array();

		switch (strtolower($CFG->dbtype)) {
			case 'mysql' : 
				$field = "CONCAT(hotpot, '_', name)";
				break;
			case 'postgres7' :
				$field = "hotpot||'_'||name";
				break;
		}
		if ($field) {
			$questions = get_records_sql("
				SELECT $field, COUNT(*), hotpot, name
				FROM {$CFG->prefix}hotpot_questions 
				WHERE hotpot IN ($hotpotids)
				GROUP BY hotpot, name 
				HAVING COUNT(*) >1
			");
		}
		if ($questions) {
			foreach ($questions as $question) {
				$regradehotpots[] = $question->hotpot;
			}
			$regradehotpots = array_unique($regradehotpots);
			sort($regradehotpots);
		}
	}

	// start timer
	$start = microtime();

	// get total number of attempts, users and details for these hotpots
	$tables = "{$CFG->prefix}hotpot_attempts AS a";
	$fields = "
		a.hotpot AS hotpot,
		COUNT(DISTINCT a.clickreportid) AS attemptcount,
		COUNT(DISTINCT a.userid) AS usercount,
		MAX(a.score) AS maxscore
	";
	$select = "a.hotpot IN ($hotpotids)";
	if (isteacher($course->id)) {
		// do nothing (=get all users)
	} else {
		// restrict results to this user only
		$select .= " AND a.userid='$USER->id'";
	}
	$usejoin = 1;
	if (isadmin() && $usejoin) {
		// join attempts table and details table
		$tables .= ",{$CFG->prefix}hotpot_details AS d";
		$fields .= ',COUNT(DISTINCT d.id) AS detailcount';
		$select .= " AND a.id=d.attempt";

		// this may take about twice as long as getting the gradecounts separately :-(
		// so this operation could be done after getting the $totals from the attempts table
	}
	$totals = get_records_sql("SELECT $fields FROM $tables WHERE $select GROUP BY a.hotpot");

	if (isadmin() && empty($usejoin)) {
		foreach ($hotpots as $hotpot) {
			$totals[$hotpot->id]->detailcount = 0;
			if ($ids = get_records('hotpot_attempts', 'hotpot', $hotpot->id)) {
				$ids = join(',', array_keys($ids));
				$totals[$hotpot->id]->detailcount = count_records_select('hotpot_details', "attempt IN ($ids)");
			}
		}
	}

	// message strings for main table
	$strusers  = get_string('users');
	$strupdate = get_string('update');
	$strregrade = get_string('regrade', 'hotpot');
	$strneverclosed = get_string('neverclosed', 'hotpot');
	$strregraderequired = get_string('regraderequired', 'hotpot');

	// column headings and attributes
	$table->head = array();
	$table->align = array();

	if (!empty($CFG->hotpot_showtimes)) {
		print '<H3>'.sprintf("%0.3f", microtime_diff($start, microtime())).' secs'."</H3>\n";
	}

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
	array_push($table->align, 
		"left", "left", "center", "left"
	);
	if (isadmin()) {
		array_push($table->head, $strregrade);
		array_push($table->align, "center");
	}

	$currentsection = -1;
	foreach ($hotpots as $hotpot) {

		$printsection = "";
		if ($hotpot->section != $currentsection) {
			if ($hotpot->section) {
				$printsection = $hotpot->section;
				if ($course->format=='weeks' || $course->format=='topics') {
					// Show the zoom boxes
					if ($displaysection==$hotpot->section) {
						$strshowall = get_string('showall'.$course->format);
						$printsection .= '<br /><a href="index.php?id='.$course->id.'&section=all" title="'.$strshowall.'"><img src="'.$CFG->pixpath.'/i/all.gif" height=25 width=16 border=0></a><br />';
					} else {
						$strshowone = get_string('showonly'.preg_replace('|s$|', '', $course->format, 1), '', $hotpot->section);
						$printsection .=  '<br /><a href="index.php?id='.$course->id.'&section='.$hotpot->section.'" title="'.$strshowone.'"><img src="'.$CFG->pixpath.'/i/one.gif" height=16 width=16 border=0></a><br />';
					}
				}
			}
			if ($currentsection>=0) {
				$table->data[] = 'hr';
			}
			$currentsection = $hotpot->section;
		}

		$class = ($hotpot->visible) ? '' : 'class="dimmed" ';
		$quizname = '<A '.$class.'href="view.php?id='.$hotpot->coursemodule.'">'.$hotpot->name.'</A>';
		$quizclose = empty($hotpot->timeclose) ? $strneverclosed : userdate($hotpot->timeclose);
		
		// are there any totals for this hotpot?
		if (empty($totals[$hotpot->id]->attemptcount)) {
			$report = "&nbsp;";
			$bestscore = "&nbsp;";

		} else {
			// report number of attempts and users
			$report = get_string("viewallreports","quiz", $totals[$hotpot->id]->attemptcount);
			if (isteacher($course->id)) {
				$report .= " (".$totals[$hotpot->id]->usercount." $strusers)";
			}
			$report = '<a href="report.php?hp='.$hotpot->id.'">'.$report.'</a>';

			// get best score
			if (is_numeric($totals[$hotpot->id]->maxscore)) {
				$bestscore = $totals[$hotpot->id]->maxscore." / $hotpot->grade";
			} else {
				$bestscore = "&nbsp;";
			}
		}

		if (isadmin()) {
			if (in_array($hotpot->id, $regradehotpots)) {
				$report .= ' <FONT color="red">'.$strregraderequired.'</FONT>';
			}
		}

		$data = array ();

		if ($course->format=="weeks" || $course->format=="topics") {
			array_push($data, $printsection);
		}

		if (isteacheredit($course->id)) {
			$updatebutton = ''
			.	'<form target="'.$CFG->framename.'" method="get" action="'.$CFG->wwwroot.'/course/mod.php">'
			.	'<input type="hidden" name="update" value="'.$hotpot->coursemodule.'" />'
			.	$sesskey
			.	'<input type="submit" value="'.$strupdate.'" />'
			.	'</form>'
			;
			array_push($data, $updatebutton);
		}

		array_push($data, $quizname, $quizclose, $bestscore, $report);

		if (isadmin()) {
			if (empty($totals[$hotpot->id]->detailcount)) {
				// no details records for this hotpot, so disable regrade
				$regradebutton = '&nbsp;';
			} else {
				$strregradecheck = get_string('regradecheck', 'hotpot', strtr($hotpot->name, $quotes));
				$regradebutton = ''
				.	'<form target="_parent" method="post" action="'.$ME.'" onsubmit="var x=window.confirm('."'$strregradecheck'".');this.confirm.value=x;return x;">'
				.	'<input type="hidden" name="id" value="'.$course->id.'">'
				.	'<input type="hidden" name="regrade" value="'.$hotpot->id.'" />'
				.	'<input type="hidden" name="confirm" value="" />'
				.	$sesskey
				.	'<input type="submit" value="'.$strregrade.'" />'
				.	'</form>'
				;
			}
			array_push($data, $regradebutton);
		}

		$table->data[] = $data;
	}

	echo "<br />";

	print_table($table);

	// Finish the page
	print_footer($course);	
?>
