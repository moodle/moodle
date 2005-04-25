<?php  // $Id$

/// Overview report just displays a big table of all the attempts

class hotpot_report extends hotpot_default_report {

	function display(&$hotpot, &$cm, &$course, &$users, &$attempts, &$questions) {

		global $CFG;

		// retrieve form variables, if any
		global $download, $tablename;
		optional_variable($download, "");
		optional_variable($tablename, "");
		
		$strbestgrade  = "highest"; // $QUIZ_GRADE_METHOD[$hotpot->grademethod];
		
		// get responses for the attempts by these users
		foreach ($attempts as $a => $attempt) {

			// initialize the responses array for this attempt
			$attempts[$a]->responses = array();

			foreach ($questions as $q=>$question) {

				if (!isset($questions[$q]->attempts)) {
					$questions[$q]->attempts = array();
				}

				// get the response, if any, to this question on this attempt
				if ($response = get_record('hotpot_responses', 'attempt', $attempt->id, 'question', $question->id)) {

					// add the response for this attempt
					$attempts[$a]->responses[$q] = $response;

					// add a reference from the question to the attempt which includes this question
					$questions[$q]->attempts[] = &$attempts[$a];
				}
			}
		}

		// create the tables
		$this->create_responses_table($users, $attempts, $questions, $r_table=NULL, $download, $course, $hotpot);
		$this->create_analysis_table($users, $attempts, $questions, $a_table=NULL, $download);

		switch ($download) {
			case 'txt':
				switch ($tablename) {
					case 'r':
						$this->print_text($course, $hotpot, $r_table);
						break;
					case 'a':
						$this->print_text($course, $hotpot, $a_table);
						break;
				}
				break;

			case 'xls':
				switch ($tablename) {
					case 'r':
						$this->print_excel($course, $hotpot, $r_table);
						break;
					case 'a':
						$this->print_excel($course, $hotpot, $a_table);
						break;
				}
				break;

			default:
				$this->print_html($cm, $r_table, 'fullstat', 'r');
				print_spacer(50, 10, true);

				$this->print_html($cm, $a_table, 'fullstat', 'a');
		}		

		return true;
	}

	function create_responses_table(&$users, &$attempts, &$questions, &$table, $download, &$course, &$hotpot) {

		global $CFG;

		// shortcuts for font tags
		$br = $download ? "\n" : "<br />\n";
		$blank = $download ? "" : '&nbsp;';
		$font_end = $download ? '' : '</font>';
		$font_red = $download ? '' : '<font color="red">';
		$font_blue = $download ? '' : '<font color="blue">';
		$font_brown = $download ? '' : '<font color="brown">';
		$font_green = $download ? '' : '<font color="green">';
		$font_small = $download ? '' : '<font size="-2">';
		$nobr_start = $download ? '' : '<nobr>';
		$nobr_end = $download ? '' : '</nobr>';

		// is review allowed? (do this once here, to save time later)
		$allow_review = (!$download && (isteacher($course->id) || $hotpot->review));

		// assume penalties column is NOT required
		$show_penalties = false;

		// initialize $table
		$table->border = 1;
		$table->width = '100%';

		// headings for name, attempt number, score/grade and penalties
		$table->head = array(
			get_string("name"), 
			hotpot_grade_heading($hotpot, $download),
			get_string('attempt', 'quiz'), 
		);
		$table->align = array('left', 'center', 'center');
		$table->size = array(150, 80, 10);
		$table->wrap = array(0, 0, 0);
		$table->fontsize = array(0, 0, 0);

		// question headings
		$this->add_question_headings($questions, $table, 'left', 0, false, 2);

		// penalties (not always needed) and raw score
		array_push($table->head, 
			get_string('penalties', 'hotpot'), 
			get_string('score', 'quiz')
		);
		array_push($table->align, 'center', 'center');
		array_push($table->size, 50, 50);
		array_push($table->wrap, 0, 0);
		array_push($table->fontsize, 0, 0);

		// message strings
		$strnoresponse = get_string('noresponse', 'quiz');

		// array to map columns onto question ids ($col => $id)
		$questionids = array_keys($questions);

		// add details of users' responses
		foreach ($users as $user) {

			// shortcut to user info held in first attempt record
			$u = &$user->attempts[0];
			
			if (function_exists("fullname")) {
				$name = fullname($u);
			} else {
				$name = "$u->firstname $u->lastname";
			}
			if (!$download) { // html
				$name = '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$u->userid.'&course='.$course->id.'">'.$name.'</a>';
			}
			$grade = isset($user->grade) ? $user->grade : $blank;

			foreach ($user->attempts as $attempt) {
	
				$attemptnumber = $attempt->attempt;
				if ($allow_review) {
					$attemptnumber = ' <a href="review.php?hp='.$hotpot->id.'&attempt='.$attempt->id.'">'.$attemptnumber.'</a>';
				}
				$cells = array ($name, $grade, $attemptnumber);

				// $name and $grade are only printed on first line per user
				$name = $blank; 
				$grade = $blank;

				$start_col = count($cells);
				foreach ($questionids as $col => $id) {
					$cells[$start_col + $col] = "$font_brown($strnoresponse)$font_end";
				}

				if (isset($attempt->penalties)) {
					$show_penalties = true;
					$penalties = $attempt->penalties;
				} else {
					$penalties = $blank;
				}
				array_push($cells, $penalties, hotpot_format_score($attempt));

				// get responses to questions in this attempt
				foreach ($attempt->responses as $response) {

					// correct
					if (!$correct = hotpot_strings($response->correct)) {
						$correct = "($strnoresponse)";
					}
					$cell = $font_red.$correct.$font_end;

					// wrong
					if ($wrong = hotpot_strings($response->wrong)) {
						$cell .= $br.$font_blue.$wrong.$font_end;
					}

					// ignored
					if ($ignored = hotpot_strings($response->ignored)) {
						$cell .= $br.$font_brown.$ignored.$font_end;
					}

					// numeric
					if (is_numeric($response->score)) {
						if (empty($table->caption)) {
							$table->caption = get_string('indivresp', 'quiz');
							if (!$download) {
								$table->caption .= helpbutton('responsestable', $table->caption, 'hotpot', true, false, '', true);
							}
						}
						$hints = empty($response->hints) ? 0 : $response->hints;
						$clues = empty($response->clues) ? 0 : $response->clues;
						$checks = empty($response->checks) ? 0 : $response->checks;
						$numeric = $response->score.'% '.$blank.' ('.$hints.','.$clues.','.$checks.')';
						$cell .= $br.$nobr_start.$font_green.$numeric.$font_end.$nobr_end;
					}

					// add responses into $cells
					if (is_numeric($col = array_search($response->question, $questionids))) {
						$cells[$start_col + $col] = $cell;
					}
				}
				$table->data[] = $cells;
			}
		} // end foreach
		
		if (!$show_penalties) {
			$col = 3 + count($questionids);
			$this->remove_column($col, $table);
		}
	}

	function create_analysis_table(&$users, &$attempts, &$questions, &$table, $download) {

		// the fields we are interested in, in the order we want them
		// 	currently some fields are redundant for some types of quiz
		// 	so the the fields could also be set depending on quiz type
		// 	(see hotpot_set_fields_by_quiz_type function below)
		$fields = array('correct', 'wrong', 'ignored', 'hints', 'clues', 'checks', 'weighting');
		$string_fields = array('correct', 'wrong', 'ignored');

		$q = array(); // statistics about the $q(uestions)
		$f = array(); // statistics about the $f(ields)
		
		////////////////////////////////////////////
		// compile the statistics about the questions
		////////////////////////////////////////////

		foreach ($questions as $id=>$question) {

			// extract scores for attempts at this question
			$scores = array();
			foreach ($question->attempts as $attempt) {
				$scores[] = $attempt->score;
			}
	
			// sort scores values (in ascending order)
			asort($scores);
	
			// get the borderline high and low scores
			$count = count($scores);
			switch ($count) {
				case 0:
					$lo_score = 0;
					$hi_score = 0;
					break;
				case 1:
					$lo_score = 0;
					$hi_score = $scores[0];
					break;
				default:
					$lo_score = $scores[round($count*1/3)];
					$hi_score = $scores[round($count*2/3)];
					break;
			}
	
			// initialize statistics array for this question
			$q[$id] = array();

			// get statistics for each attempt which includes this question
			foreach ($question->attempts as $attempt) {
	
				$is_hi_score = ($attempt->score >= $hi_score);
				$is_lo_score = ($attempt->score <  $lo_score);
	
				// reference to the response to the current question
				$response = &$attempt->responses[$id];

				// update statistics for fields in this response
				foreach($fields as $field) {

					if (!isset($f[$field])) {
						$f[$field] = array('count' => 0);
					}

					if (!isset($q[$id][$field])) {
						$q[$id][$field] = array('count' => 0);
					}

					$values = explode(',', $response->$field);
					$values = array_unique($values);
					foreach($values as $value) {

						// $value should be an integer (string_id or count)
						if (is_numeric($value)) {

							$f[$field]['count']++;

							if (!isset($q[$id][$field][$value])) {
								$q[$id][$field][$value] = 0;
							}

							$q[$id][$field]['count']++;
							$q[$id][$field][$value]++;
						}
					}
				} // end foreach $field

				// initialize counters for this question, if necessary
				if (!isset($q[$id]['count'])) {
					$q[$id]['count'] = array('hi'=>0, 'lo'=>0, 'correct'=>0, 'total'=>0, 'sum'=>0);
				}

				// increment counters
				$q[$id]['count']['sum'] += $response->score;
				$q[$id]['count']['total']++;
				if ($response->score==100) {
					$q[$id]['count']['correct']++;
					if ($is_hi_score) {
						$q[$id]['count']['hi']++;
					} else if ($is_lo_score) {
						$q[$id]['count']['lo']++;
					}
				}

			} // end foreach attempt
		} // end foreach question

		// check we have some details
		if ($q) {

			$showhideid = 'showhide';

			// shortcuts for html tags
			$bold_start = $download ? "" : '<strong>';
			$bold_end = $download ? "" : '</strong>';
			$div_start = $download ? "" : '<div id="'.$showhideid.'">';
			$div_end = $download ? "" : '</div>';

			$font_red = $download ? '' : '<font color="red" size="-2">';
			$font_blue = $download ? '' : '<font color="blue" size="-2">';
			$font_green = $download ? '' : '<font color="green" size="-2">';
			$font_brown = $download ? '' : '<font color="brown" size="-2">';
			$font_end = $download ? '' : '</font>'."\n";

			$br = $download ? "\n" : '<br />';
			$space = $download ? "" : '&nbsp;';
			$no_value = $download ? "" : '--';
			$help_button = $download ? "" : helpbutton("discrimination", "", "quiz", true, false, "", true);

			// table properties
			$table->border = 1;
			$table->width = '100%';
			$table->caption = get_string('itemanal', 'quiz');
			if (!$download) {
				$table->caption .= helpbutton('analysistable', $table->caption, 'hotpot', true, false, '', true);
			}

			// headings for name, attempt number and score/grade
			$table->head = array($space);
			$table->align = array('right');
			$table->size = array(80);

			// question headings
			$this->add_question_headings($questions, $table, 'left', 0);

			// initialize statistics
			$table->stat = array();
			$table->statheadercols = array(0);

			// add headings for the $foot of the $table
			$table->foot = array();
			$table->foot[0] = array(get_string('average', 'hotpot'));
			$table->foot[1] = array(get_string('percentcorrect', 'quiz'));
			$table->foot[2] = array(get_string('discrimination', 'quiz').$help_button);

			// maximum discrimination index (also default the default value)
			$max_d_index = 10;

			////////////////////////////////////////////
			// format the statistics into the $table
			////////////////////////////////////////////

			// add $stat(istics) and $foot of $table
			$questionids = array_keys($questions);
			foreach ($questionids as $col => $id) {			

				$row = 0;

				// add button to show/hide question text
				if (!isset($table->stat[0])) {
					$button = $download ? "" : hotpot_showhide_button($showhideid);
					$table->stat[0] = array(get_string('question', 'quiz').$button);
				}

				// add the question name/text
				$name = hotpot_get_question_name($questions[$id]);
				$table->stat[$row++][$col+1] = $div_start.$bold_start.$name.$bold_end.$div_end.$space;

				// add details about each field
				foreach ($fields as $field) {

					// check this row is required
					if ($f[$field]['count']) {

						$values = array();
						$string_type = array_search($field, $string_fields);
		
						// get the value of each response to this field
						// and the count of that value
						foreach ($q[$id][$field] as $value => $count) {
		
							if (is_numeric($value) && $count) {
								if (is_numeric($string_type)) {
									$value = hotpot_string($value);
									switch ($string_type) {
										case 0: // correct
											$font_start = $font_red;
											break;
										case 1: // wrong
											$font_start = $font_blue;
											break;
										case 2: // ignored
											$font_start = $font_brown;
											break;
									}
								} else { // numeric field
									$font_start = $font_green;
								}
								$values[] = $font_start.round(100*$count/$q[$id]['count']['total']).'%'.$font_end.' '.$value;
							}
		
						} // end foreach $value => $count
		
						// initialize stat(istics) row for this field, if required
						if (!isset($table->stat[$row])) {
							$table->stat[$row] = array(get_string($field, 'hotpot'));
						}
		
						// sort the values by frequency (using user-defined function)
						usort($values, "hotpot_sort_stat_values");
		
						// add stat(istics) values for this field
						$table->stat[$row++][$col+1] = count($values) ? implode($br, $values) : $space;
					}
				} // end foreach field

				// default percent correct and discrimination index for this question
				$average = $no_value;
				$percent = $no_value;
				$d_index = $no_value;

				if (isset($q[$id]['count'])) {

					// average and percent correct
					if ($q[$id]['count']['total']) {
						$average = round($q[$id]['count']['sum'] / $q[$id]['count']['total']).'%';
						$percent = round(100*$q[$id]['count']['correct'] / $q[$id]['count']['total']).'%';
						$percent .= ' ('.$q[$id]['count']['correct'].'/'.$q[$id]['count']['total'].')';
					}

					// discrimination index
					if ($q[$id]['count']['lo']) {
						$d_index = min($max_d_index, round($q[$id]['count']['hi'] / $q[$id]['count']['lo'], 1));
					} else {
						$d_index = $q[$id]['count']['hi'] ? $max_d_index : 0;
					}
					$d_index .= ' ('.$q[$id]['count']['hi'].'/'.$q[$id]['count']['lo'].')';

				}
				$table->foot[0][$col+1] = $average;
				$table->foot[1][$col+1] = $percent;
				$table->foot[2][$col+1] = $d_index;

			} // end foreach $question ($col)

			// add javascript to show/hide question text
			if (isset($table->stat[0]) && !$download) {
				$i = count($table->stat[0]);
				$table->stat[0][$i-1] .= hotpot_showhide_set($showhideid);
			}
		}
	}
} // end class

function hotpot_sort_stat_values($a, $b) {
	// sorts in descending order
	// assumes first chars in $a and $b are a percentage
	$a_val = intval(strip_tags($a));
	$b_val = intval(strip_tags($b));
	return ($a_val<$b_val) ? 1 : ($a_val==$b_val ? 0 : -1);
}
function hotpot_showhide_button($id) {
	$show = get_string('show');
	$hide = get_string('hide');
	$pref = '1';
	$text = ($pref=='1' ? $hide : $show);
return <<<SHOWHIDE_BUTTON

<script language="javascript">
<!--
	function showhide (id, toggle) {
		var show = true;

		obj = document.getElementById(id+'pref');
		if (obj) {
			show = (obj.value=='1');
			if (toggle) {
				show = !show;
				obj.value = (show ? '1' :  '0');
			}
		}

		obj = document.getElementById(id+'button');
		if (obj) {
			obj.value = (show ? '$hide' : '$show');
		}

		obj = document.getElementsByName(id);
		var i_max = obj.length;
		for (var i=0; i<i_max; i++) {
			obj[i].style.display = (show ? 'block' : 'none');
		}
	}
	var showhide_allowed = (document.getElementById && document.getElementsByName);
	if (showhide_allowed) {
		var html = '';
		html += '<form onsubmit="return false">';
		html += '<input type="button" value="$text" id="{$id}button" onClick="javascript: return showhide(\\'$id\\', true);" />';
		html += '<input type="hidden" name="{$id}pref" id="{$id}pref" value="$pref" />';
		html += '</form>';
		document.writeln(html);
	}
//-->
</script>
SHOWHIDE_BUTTON
;
}

function hotpot_showhide_set($id) {
return <<<SHOWHIDE_SET

<script language="javascript">
<!--
	if (showhide_allowed) {
		showhide('$id');
	}
//-->
</script>
SHOWHIDE_SET
;
}

function hotpot_set_fields_by_question_type(&$questions) {
		// this function is not used
		$fields = array();
		foreach ($questions as $question) {
			// all questions should be the same type,
			// but just in case, they are all checked
			switch ($question->type) {
				case 1: // jcb
					break;
				case 2: // jcloze
					$fields['correct'] = true;
					$fields['wrong'] = true;
					$fields['ignored'] = true;
					$fields['clues'] = true;
					break;
				case 3: // jcross
					$fields['correct'] = true;
					break;
				case 4: // jmix
					$fields['correct'] = true;
					$fields['ignored'] = true;
					$fields['checks'] = true;
					break;
				case 5: // jmatch
					$fields['correct'] = true;
					$fields['checks'] = true;
					break;
				case 6: // jmatch
				case 6.1: // multi-choice
				case 6.2: // short-answer
				case 6.3: // hybrid
				case 6.4: // multi-select
					$fields['correct'] = true;
					$fields['wrong'] = true;
					$fields['ignored'] = true;
					$fields['hints'] = true;
					$fields['checks'] = true;
					break;
			}
		}
		$fields['weighting'] = true;

		$fields = array_keys($fields);
		return $fields;
}
?>
