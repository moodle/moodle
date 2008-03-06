<?php  // $Id$
/// Overview report: displays a big table of all the attempts
class hotpot_report extends hotpot_default_report {
	function display(&$hotpot, &$cm, &$course, &$users, &$attempts, &$questions, &$options) {
		global $CFG;
		// create the table
		$tables = array();
		$this->create_scores_table($hotpot, $course, $users, $attempts, $questions, $options, $tables);
		$this->print_report($course, $hotpot, $tables, $options);
		return true;
	}
	function create_scores_table(&$hotpot, &$course, &$users, &$attempts, &$questions, &$options, &$tables) {
		global $CFG;
		$download = ($options['reportformat']=='htm') ? false : true;
		$is_html = ($options['reportformat']=='htm');
		$blank = ($download ? '' : '&nbsp;');
		$no_value = ($download ? '' : '-');
		$allow_review = true;
		// start the table
		unset($table);
		$table->border = 1;
		$table->head = array();
		$table->align = array();
		$table->size = array();
		// picture column, if required
		if ($is_html) {
			$table->head[] = '&nbsp;';
			$table->align[] = 'center';
			$table->size[] = 10;
		}
		// name, grade and attempt number
		array_push($table->head,
			get_string("name"),
			hotpot_grade_heading($hotpot, $options),
			get_string("attempt", "quiz")
		);
		array_push($table->align, "left", "center", "center");
		array_push($table->size, '', '', '');
		// question headings
		$this->add_question_headings($questions, $table);
		// penalties and raw score
		array_push($table->head,
			get_string('penalties', 'hotpot'),
			get_string('score', 'quiz')
		);
		array_push($table->align, "center", "center");
		array_push($table->size, '', '');
		$table->data = array();
		$q = array(
			'grade'     => array('count'=>0, 'total'=>0),
			'penalties' => array('count'=>0, 'total'=>0),
			'score'     => array('count'=>0, 'total'=>0),
		);
		foreach ($users as $user) {
			// shortcut to user info held in first attempt record
			$u = &$user->attempts[0];
			$picture = '';
			$name = fullname($u);
			if ($is_html) {
				$picture = print_user_picture($u->userid, $course->id, $u->picture, false, true);
				$name = '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$u->userid.'&amp;course='.$course->id.'">'.$name.'</a>';
			}
			if (isset($user->grade)) {
				$grade = $user->grade;
				$q['grade']['count'] ++;
				if (is_numeric($grade)) {
					$q['grade']['total'] += $grade;
				}
			} else {
				$grade = $no_value;
			}
			$attemptcount = count($user->attempts);
			if ($attemptcount>1) {
				$text = $name;
				$name = NULL;
				$name->text = $text;
				$name->rowspan = $attemptcount;
				$text = $grade;
				$grade = NULL;
				$grade->text = $text;
				$grade->rowspan = $attemptcount;
			}
			$data = array();
			if ($is_html) {
				if ($attemptcount>1) {
					$text = $picture;
					$picture = NULL;
					$picture->text = $text;
					$picture->rowspan = $attemptcount;
				}
				$data[] = $picture;
			}
			array_push($data, $name, $grade);
			foreach ($user->attempts as $attempt) {
				// set flag if this is best grade
				$is_best_grade = ($is_html && $attempt->score==$user->grade);
				// get attempt number
				$attemptnumber= $attempt->attempt;
				if ($is_html && $allow_review) {
					$attemptnumber = '<a href="review.php?hp='.$hotpot->id.'&amp;attempt='.$attempt->id.'">'.$attemptnumber.'</a>';
				}
				if ($is_best_grade) {
					$score = '<span class="highlight">'.$attemptnumber.'</span>';
				}
				$data[] = $attemptnumber;
				// get responses to questions in this attempt by this user
				foreach ($questions as $id=>$question) {
					if (!isset($q[$id])) {
						$q[$id] = array('count'=>0, 'total'=>0);
					}
					if (isset($attempt->responses[$id])) {
						$score = $attempt->responses[$id]->score;
						if (is_numeric($score)) {
							$q[$id]['count'] ++;
							$q[$id]['total'] += $score;
							if ($is_best_grade) {
								$score = '<span class="highlight">'.$score.'</span>';
							}
						} else if (empty($score)) {
							$score = $no_value;
						}
					} else {
						$score = $no_value;
					}
					$data[] = $score;
				} // foreach $questions
				if (isset($attempt->penalties)) {
					$penalties = $attempt->penalties;
					if (is_numeric($penalties)) {
						$q['penalties']['count'] ++;
						$q['penalties']['total'] += $penalties;
					}
					if ($is_best_grade) {
						$penalties = '<span class="highlight">'.$penalties.'</span>';
					}
				} else {
					$penalties = $no_value;
				}
				$data[] = $penalties;
				if (isset($attempt->score)) {
					$score = $attempt->score;
					if (is_numeric($score)) {
						$q['score']['total'] += $score;
						$q['score']['count'] ++;
					}
					if ($is_best_grade) {
						$score = '<span class="highlight">'.$score.'</span>';
					}
				} else {
					$score = $no_value;
				}
				$data[] = $score;
				// append data for this attempt
				$table->data[] = $data;
				// reset data array for next attempt, if any
				$data = array();
			} // end foreach $attempt
			$table->data[] = 'hr';
		} // end foreach $user
		// remove final 'hr' from data rows
		array_pop($table->data);
		// add averages to foot of table
		$averages = array();
		if ($is_html) {
			$averages[] = $blank;
		}
		array_push($averages, get_string('average', 'hotpot'));
		$col = count($averages);
		if (empty($q['grade']['count'])) {
			// remove score $col from $table
			$this->remove_column($table, $col);
		} else {
			$precision = ($hotpot->grademethod==HOTPOT_GRADEMETHOD_AVERAGE || $hotpot->grade<100) ? 1 : 0;
			$averages[] = round($q['grade']['total'] / $q['grade']['count'], $precision);
			$col++;
		}
		// skip the attempt number column
		$averages[$col++] = $blank;
		foreach ($questions as $id=>$question) {
			if (empty($q[$id]['count'])) {
				// remove this question $col from $table
				$this->remove_column($table, $col);
			} else {
				$averages[$col++] = round($q[$id]['total'] / $q[$id]['count']);
			}
		}
		if (empty($q['penalties']['count'])) {
			// remove penalties $col from $table
			$this->remove_column($table, $col);
		} else {
			$averages[$col++] = round($q['penalties']['total'] / $q['penalties']['count']);
		}
		if (empty($q['score']['count'])) {
			// remove score $col from $table
			$this->remove_column($table, $col);
		} else {
			$averages[$col++] = round($q['score']['total'] / $q['score']['count']);
		}
		$table->foot = array($averages);
		$tables[] = &$table;
	}
} // end class
?>
