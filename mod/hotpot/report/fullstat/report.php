<?php  // $Id$
/// Overview report just displays a big table of all the attempts
class hotpot_report extends hotpot_default_report {
	function display(&$hotpot, &$cm, &$course, &$users, &$attempts, &$questions, &$options) {
		global $CFG;
		// create the tables
		$tables = array();
		$this->create_responses_table($hotpot, $course, $users, $attempts, $questions, $options, $tables);
		$this->create_analysis_table($users, $attempts, $questions, $options, $tables);
		// print report
		$this->print_report($course, $hotpot, $tables, $options);
		return true;
	}
	function create_responses_table(&$hotpot, &$course, &$users, &$attempts, &$questions, &$options, &$tables) {
		global $CFG;
		$is_html = ($options['reportformat']=='htm');
		// shortcuts for font tags
		$br = $is_html ? "<br />\n" : "\n";
		$blank = $is_html ? '&nbsp;' : "";
		$font_end   = $is_html ? '</font>' : '';
		$font_red   = $is_html ? '<font color="red">'   : '';
		$font_blue  = $is_html ? '<font color="blue">'  : '';
		$font_brown = $is_html ? '<font color="brown">' : '';
		$font_green = $is_html ? '<font color="green">' : '';
		$font_small = $is_html ? '<font size="-2">' : '';
		$nobr_start = $is_html ? '<nobr>'  : '';
		$nobr_end   = $is_html ? '</nobr>' : '';
		// is review allowed? (do this once here, to save time later)
		$allow_review = ($is_html && (has_capability('mod/hotpot:viewreport',get_context_instance(CONTEXT_COURSE, $course->id)) || $hotpot->review));
		// assume penalties column is NOT required
		$show_penalties = false;
		// initialize $table
		unset($table);
		$table->border = 1;
		$table->width = '100%';
		// initialize legend, if necessary
		if (!empty($options['reportshowlegend'])) {
			$table->legend = array();
		}
		// headings for name, attempt number, score/grade and penalties
		$table->head = array(
			get_string("name"),
			hotpot_grade_heading($hotpot, $options),
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
			if ($is_html) {
				$name = '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$u->userid.'&amp;course='.$course->id.'">'.$name.'</a>';
			}
			$grade = isset($user->grade) ? $user->grade : $blank;
			foreach ($user->attempts as $attempt) {
				$attemptnumber = $attempt->attempt;
				if ($allow_review) {
					$attemptnumber = ' <a href="review.php?hp='.$hotpot->id.'&amp;attempt='.$attempt->id.'">'.$attemptnumber.'</a>';
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
					// check this question id is OK (should be)
					$col = array_search($response->question, $questionids);
					if (is_numeric($col)) {
						// correct
						if ($value = hotpot_strings($response->correct)) {
							$this->set_legend($table, $col, $value, $questions[$response->question]);
						} else {
							$value = "($strnoresponse)";
						}
						$cell = $font_red.$value.$font_end;
						// wrong
						if ($value = hotpot_strings($response->wrong)) {
							if (isset($table->legend)) {
								$values = array();
								foreach (explode(',', $value) as $v) {
									$this->set_legend($table, $col, $v, $questions[$response->question]);
									$values[] = $v;
								}
								$value = implode(',', $values);
							}
							$cell .= $br.$font_blue.$value.$font_end;
						}
						// ignored
						if ($value = hotpot_strings($response->ignored)) {
							if (isset($table->legend)) {
								$values = array();
								foreach (explode(',', $value) as $v) {
									$this->set_legend($table, $col, $v, $questions[$response->question]);
									$values[] = $v;
								}
								$value = implode(',', $values);
							}
							$cell .= $br.$font_brown.$value.$font_end;
						}
						// numeric
						if (is_numeric($response->score)) {
							if (empty($table->caption)) {
								$table->caption = get_string('indivresp', 'quiz');
								if ($is_html) {
									$table->caption .= helpbutton('responsestable', $table->caption, 'hotpot', true, false, '', true);
								}
							}
							$hints = empty($response->hints) ? 0 : $response->hints;
							$clues = empty($response->clues) ? 0 : $response->clues;
							$checks = empty($response->checks) ? 0 : $response->checks;
							$numeric = $response->score.'% '.$blank.' ('.$hints.','.$clues.','.$checks.')';
							$cell .= $br.$nobr_start.$font_green.$numeric.$font_end.$nobr_end;
						}
						$cells[$start_col + $col] = $cell;
					}
				}
				$table->data[] = $cells;
			}
			// insert 'tabledivider' between users
			$table->data[] = 'hr';
		} // end foreach $users
		// remove final 'hr' from data rows
		array_pop($table->data);
		if (!$show_penalties) {
			$col = 3 + count($questionids);
			$this->remove_column($table, $col);
		}
		$tables[] = &$table;
	}
	function create_analysis_table(&$users, &$attempts, &$questions, &$options, &$tables) {
		$is_html = ($options['reportformat']=='htm');
		// the fields we are interested in, in the order we want them
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
			// get statistics for each attempt which includes this question
			foreach ($question->attempts as $attempt) {
				$is_hi_score = ($attempt->score >= $hi_score);
				$is_lo_score = ($attempt->score <  $lo_score);
				// reference to the response to the current question
				$response = &$attempt->responses[$id];
				// update statistics for fields in this response
				foreach($fields as $field) {
					if (!isset($q[$id])) {
						$q[$id] = array();
					}
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
		if (count($q)) {
			$showhideid = 'showhide';
			// shortcuts for html tags
			$bold_start = $is_html ? '<strong>' :  "";
			$bold_end = $is_html ? '</strong>' : "";
			$div_start = $is_html ? '<div id="'.$showhideid.'">' : "";
			$div_end = $is_html ? '</div>' : "";
			$font_red   = $is_html ? '<font color="red" size="-2">' : '';
			$font_blue  = $is_html ? '<font color="blue" size="-2">' : '';
			$font_green = $is_html ? '<font color="green" size="-2">' : '';
			$font_brown = $is_html ? '<font color="brown" size="-2">' : '';
			$font_end = $is_html ? '</font>'."\n" : '';
			$br = $is_html ? '<br />' : "\n";
			$space = $is_html ? '&nbsp;' : "";
			$no_value = $is_html ? '--' : "";
			$help_button = $is_html ? helpbutton("discrimination", get_string('discrimination', 'quiz'), "quiz", true, false, "", true) : "";
			// table properties
			unset($table);
			$table->border = 1;
			$table->width = '100%';
			$table->caption = get_string('itemanal', 'quiz');
			if ($is_html) {
				$table->caption .= helpbutton('analysistable', $table->caption, 'hotpot', true, false, '', true);
			}
			// initialize legend, if necessary
			if (!empty($options['reportshowlegend'])) {
				if (empty($tables) || empty($tables[0]->legend)) {
					$table->legend = array();
				} else {
					$table->legend = $tables[0]->legend;
					unset($tables[0]->legend);
				}
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
			$questionids = array_keys($q);
			foreach ($questionids as $col => $id) {
				$row = 0;
				// print the question text if there is no legend
				if (empty($table->legend)) {
					// add button to show/hide question text
					if (!isset($table->stat[0])) {
						$button = $is_html ? hotpot_showhide_button($showhideid) : "";
						$table->stat[0] = array(get_string('question', 'quiz').$button);
					}
					// add the question name/text
					$name = hotpot_get_question_name($questions[$id]);
					$table->stat[$row++][$col+1] = $div_start.$bold_start.$name.$bold_end.$div_end.$space;
				}
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
									$this->set_legend($table, $col, $value, $questions[$id]);
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
			if (isset($table->stat[0]) && $is_html && empty($table->legend)) {
				$i = count($table->stat[0]);
				$table->stat[0][$i-1] .= hotpot_showhide_set($showhideid);
			}
			$tables[] = &$table;
			$this->create_legend_table($tables, $table);
		} // end if (empty($q)
	} // end function
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
<script type="text/javascript">
//<![CDATA[
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
//]]>
</script>
SHOWHIDE_BUTTON
;
}
function hotpot_showhide_set($id) {
return <<<SHOWHIDE_SET
<script type="text/javascript">
//<![CDATA[
	if (showhide_allowed) {
		showhide('$id');
	}
//]]>
</script>
SHOWHIDE_SET
;
}
?>
