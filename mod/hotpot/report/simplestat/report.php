<?php  // $Id$

/// Overview report: displays a big table of all the attempts

class hotpot_report extends hotpot_default_report {

	function display(&$hotpot, &$cm, &$course, &$users, &$attempts, &$questions) {
	
		global $download;
		optional_variable($download, "");

		$this->create_scores_table($users, $attempts, $questions, $s_table=NULL, $download, $course, $hotpot);

		switch ($download) {
			case 'txt':
				$this->print_text($course, $hotpot, $s_table);
				break;

			case 'xls':
				$this->print_excel($course, $hotpot, $s_table);
				break;

			default:
				$this->print_html($cm, $s_table, 'simplestat');
		}		

		return true;
	}

	function create_scores_table(&$users, &$attempts, &$questions, &$table, $download, $course, $hotpot) {

		global $CFG;

		$blank = ($download ? '' : '&nbsp;');
		$no_value = ($download ? '' : '-');

		$allow_review = true; // (!$download && (isteacher($course->id) || $hotpot->review));

		// start the table
		$table->border = 1;

		$table->head = array();
		$table->align = array();
		$table->size = array();

		// picture column, if required
		if (!$download) {
			$table->head = array('&nbsp;');
			$table->align = array('center');
			$table->size = array(10);
		}		

		// name, grade and attempt number
		array_push($table->head, 
			get_string("name"),
			hotpot_grade_heading($hotpot, $download), 
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
			if (function_exists("fullname")) {
				$name = fullname($u);
			} else {
				$name = "$u->firstname $u->lastname";
			}
			if (!$download) { // html
				$picture = print_user_picture($u->userid, $course->id, $u->picture, false, true);
				$name = '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$u->userid.'&course='.$course->id.'">'.$name.'</a>';
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

			$br = '';			
			$data = array();
			if (!$download) {
				array_push($data, $picture);
			}
			array_push($data, $name, $grade);
			$start_col = count($data);

			$data[] = ''; // attempt number
			foreach ($questions as $question) {
				$data[] = '';
			}
			array_push($data, '', ''); // penalties and raw score

			foreach ($user->attempts as $attempt) {
				$col = $start_col;
				$is_best_grade = (!$download && $attempt->score==$user->grade);

				// attempt number
				$attemptnumber= $attempt->attempt;
				if ($allow_review) {
						$attemptnumber = '<a href="review.php?hp='.$hotpot->id.'&attempt='.$attempt->id.'">'.$attemptnumber.'</a>';
				}
				if ($is_best_grade) {
					$score = '<span class="highlight">'.$attemptnumber.'</span>';
				}
				$data[$col++] .= $br.$attemptnumber;

				// get responses to questions in this attempt by this user
				foreach ($questions as $question) {
					$id = $question->id;
					if (!isset($q[$id])) {
						$q[$id] = array('count'=>0, 'total'=>0);
					}
	
					$score = get_field('hotpot_responses', 'score', 'attempt', $attempt->id, 'question', $question->id);
	
					if (isset($score)) {
						if (is_numeric($score)) {
							$q[$id]['count'] ++;
							$q[$id]['total'] += $score;
						}
						if ($is_best_grade) {
							$score = '<span class="highlight">'.$score.'</span>';
						}
					} else {
						$score = $no_value;
					}
					$data[$col++] .= $br.$score;
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

				$data[$col++] .= $br.$penalties;
				$data[$col++] .= $br.$score;
				$br = ($download) ? "\n" : '<br />';

			} // end foreach $attempt

			// append data for this user
			$table->data[] = $data;

		} // end foreach $user

		// add averages to foot of table
		$averages = array();
		if (!$download) {
			$averages[] = $blank;
		}
		array_push($averages, get_string('average', 'hotpot'));

		$col = count($averages);

		if (empty($q['grade']['count'])) {
			// remove score $col from $table
			$this->remove_column($col, $table);
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
				$this->remove_column($col, $table);
			} else {
				$averages[] = round($q[$id]['total'] / $q[$id]['count']);
				$col++;
			}
		}
		if (empty($q['penalties']['count'])) {
			// remove penalties $col from $table
			$this->remove_column($col, $table);
		} else {
			$averages[] = round($q['penalties']['total'] / $q['penalties']['count']);
			$col++;
		}
		if (empty($q['score']['count'])) {
			// remove score $col from $table
			$this->remove_column($col, $table);
		} else {
			$averages[] = round($q['score']['total'] / $q['score']['count']);
			$col++;
		}
		$table->foot = array($averages);
	}

} // end class
?>
