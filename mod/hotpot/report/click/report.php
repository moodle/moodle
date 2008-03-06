<?php  // $Id$
/// Overview report just displays a big table of all the attempts
class hotpot_report extends hotpot_default_report {
	function display(&$hotpot, &$cm, &$course, &$users, &$attempts, &$questions, &$options) {
		global $CFG;
		// create the tables
		$tables = array();
		$this->create_clickreport_table($hotpot, $cm, $course, $users, $attempts, $questions, $options, $tables);
		// print the tables
		$this->print_report($course, $hotpot, $tables, $options);
		return true;
	}
	function create_clickreport_table(&$hotpot, &$cm, &$course, &$users, &$attempts, &$questions, &$options, &$tables) {
		global $CFG;
		$is_html = ($options['reportformat']=='htm');
		// time and date format strings		// date format strings
		$strftimetime = '%H:%M:%S';
		$strftimedate = get_string('strftimedate');
		// get the current time and max execution time
		$start_report_time = microtime();
		$max_execution_time = ini_get('max_execution_time');
		$correct = get_string('reportcorrectsymbol', 'hotpot');
		$wrong = get_string('reportwrongsymbol', 'hotpot');
		$nottried = get_string('reportnottriedsymbol', 'hotpot');
		// shortcuts for font tags
		$blank = $is_html ? '&nbsp;' : "";
		// store question count
		$questioncount = count($questions);
		// array to map columns onto question ids ($col => $id)
		$questionids = array_keys($questions);
		// store exercise type
		$exercisetype = $this->get_exercisetype($questions, $questionids, $blank);
		// initialize details ('events' must go last)
		$details = array('checks', 'status', 'answers', 'changes', 'hints', 'clues', 'events');
		// initialize $table
		unset($table);
		$table->border = 1;
		$table->width = '100%';
		// initialize legend, if necessary
		if (!empty($options['reportshowlegend'])) {
			$table->legend = array();
		}
		// start $table headings
		$this->set_head($options, $table, 'exercise');
		$this->set_head($options, $table, 'user');
		$this->set_head($options, $table, 'attempt');
		$this->set_head($options, $table, 'click');
		// store clicktype column number
		$clicktype_col = count($table->head)-1;
		// finish $table headings
		$this->set_head($options, $table, 'details', $exercisetype, $details, $questioncount);
		$this->set_head($options, $table, 'totals', $exercisetype);
		// set align and wrap
		$this->set_align_and_wrap($table);
		// is link to review allowed?
		$allow_review = ($is_html && (has_capability('mod/hotpot:viewreport',get_context_instance(CONTEXT_COURSE, $course->id)) || $hotpot->review));
		// initialize array of data values
		$this->data = array();
		// set exercise data values
		$this->set_data_exercise($cm, $course, $hotpot, $questions, $questionids, $questioncount, $blank);
		// add details of users' responses
		foreach ($users as $user) {
			$this->set_data_user($options, $course, $user);
			unset($clickreportid);
			foreach ($user->attempts as $attempt) {
				// initialize totals for
				$click = array(
					'qnumber' => array(),
					'correct' => array(),
					'wrong' => array(),
					'answers' => array(),
					'hints' => array(),
					'clues' => array(),
					'changes' => array(),
					'checks' => array(),
					'events' => array(),
					'score' => array(),
					'weighting' => array()
				);
				$clicktypes = array();
				// is the start of a new attempt?
				// (clicks in the same attempt have the same clickreportid)
				if (!isset($clickreportid) || $clickreportid != $attempt->clickreportid) {
					$clickcount = 1;
					$clickreportid = $attempt->clickreportid;
					// initialize totals for all clicks in this attempt
					$clicks = $click; // $click has just been initialized
					$this->set_data_attempt($attempt, $strftimedate, $strftimetime, $blank);
				}
				$cells = array();
				$this->set_data($cells, 'exercise');
				$this->set_data($cells, 'user');
				$this->set_data($cells, 'attempt');
				// get responses to questions in this attempt
				foreach ($attempt->responses as $response) {
					// set $q(uestion number)
					$q = array_search($response->question, $questionids);
					$click['qnumber'][$q] = true;
					// was this question answered correctly?
					if ($answer = hotpot_strings($response->correct)) {
						// mark the question as correctly answered
						if (empty($clicks['correct'][$q])) {
							$click['correct'][$q] = true;
							$clicks['correct'][$q] = true;
						}
						// unset 'wrong' flags, if necessary
						if (isset($click['wrong'][$q])) {
							unset($click['wrong'][$q]);
						}
						if (isset($clicks['wrong'][$q])) {
							unset($clicks['wrong'][$q]);
						}
					// otherwise, was the question answered wrongly?
					} else if ($answer = hotpot_strings($response->wrong)) {
						// mark the question as wrongly answered
						$click['wrong'][$q] = true;
						$clicks['wrong'][$q] = true;
					} else { // not correct or wrong (curious?!)
						unset($answer);
					}
					if (!empty($click['correct'][$q]) || !empty($click['wrong'][$q])) {
						$click['score'][$q] = $response->score;
						$clicks['score'][$q] = $response->score;
						$weighting = isset($response->weighting) ? $response->weighting : 100;
						$click['weighting'][$q] = $weighting;
						$clicks['weighting'][$q] =$weighting;
					}
					foreach($details as $detail) {
						switch ($detail) {
							case 'answers':
								if (isset($answer) && is_string($answer) && !empty($answer)) {
									$click[$detail][$q] = $answer;
								}
								break;
							case 'hints':
							case 'clues':
							case 'checks':
								if (isset($response->$detail) && is_numeric($response->$detail) && $response->$detail>0) {
									if (!isset($click[$detail][$q]) || $click[$detail][$q] < $response->$detail) {
										$click[$detail][$q] = $response->$detail;
									}
								}
								break;
						}
					} // end foreach $detail
				} // end foreach $response
				$click['types'] = array();
				$this->data['details'] = array();
				foreach($details as $detail) {
					for ($q=0; $q<$questioncount; $q++) {
						switch ($detail) {
							case 'status':
								if (isset($clicks['correct'][$q])) {
									$this->data['details'][] = $correct;
								} else if (isset($clicks['wrong'][$q])) {
									$this->data['details'][] = $wrong;
								} else if (isset($click['qnumber'][$q])) {
									$this->data['details'][] = $nottried;
								} else { // this question did not appear in this attempt
									$this->data['details'][] = $blank;
								}
								break;
							case 'answers':
							case 'hints':
							case 'clues':
							case 'checks':
								if (!isset($clicks[$detail][$q])) {
									if (!isset($click[$detail][$q])) {
										$this->data['details'][] = $blank;
									} else {
										$clicks[$detail][$q] = $click[$detail][$q];
										if ($detail=='answers') {
											$this->set_legend($table, $q, $click[$detail][$q], $questions[$questionids[$q]]);
										}
										$this->data['details'][] = $click[$detail][$q];
										$this->update_event_count($click, $detail, $q);
									}
								} else {
									if (!isset($click[$detail][$q])) {
										$this->data['details'][] = $blank;
									} else {
										$difference = '';
										if ($detail=='answers') {
											if ($click[$detail][$q] != $clicks[$detail][$q]) {
												$pattern = '/^'.preg_quote($clicks[$detail][$q], '/').',/';
												$difference = preg_replace($pattern, '', $click[$detail][$q], 1);
											}
										} else { // hints, clues, checks
											if ($click[$detail][$q] > $clicks[$detail][$q]) {
												$difference = $click[$detail][$q] - $clicks[$detail][$q];
											}
										}
										if ($difference) {
											$clicks[$detail][$q] = $click[$detail][$q];
											$click[$detail][$q] = $difference;
											if ($detail=='answers') {
												$this->set_legend($table, $q, $difference, $questions[$questionids[$q]]);
											}
											$this->data['details'][] = $difference;
											$this->update_event_count($click, $detail, $q);
										} else {
											unset($click[$detail][$q]);
											$this->data['details'][] = $blank;
										}
									}
								}
								break;
							case 'changes':
							case 'events':
								if (empty($click[$detail][$q])) {
									$this->data['details'][] = $blank;
								} else {
									$this->data['details'][] = $click[$detail][$q];
								}
								break;
							default:
								// do nothing
								break;
						} // end switch
					} // for $q
				} // foreach $detail
				// set data cell values for
				$this->set_data_click(
					$allow_review ? '<a href="review.php?hp='.$hotpot->id.'&amp;attempt='.$attempt->id.'">'.$clickcount.'</a>' : $clickcount,
					trim(userdate($attempt->timefinish, $strftimetime)),
					$exercisetype,
					$click
				);
				$this->set_data($cells, 'click');
				$this->set_data($cells, 'details');
				$this->set_data_totals($click, $clicks, $questioncount, $blank, $attempt);
				$this->set_data($cells, 'totals');
				$table->data[] = $cells;
				$clickcount++;
			} // end foreach $attempt
			 // insert 'tabledivider' between users
			$table->data[] = 'hr';
		} // end foreach $user
		// remove final 'hr' from data rows
		array_pop($table->data);
		if ($is_html && $CFG->hotpot_showtimes) {
			$count = count($users);
			$duration = sprintf("%0.3f", microtime_diff($start_report_time, microtime()));
			print "$count users processed in $duration seconds (".sprintf("%0.3f", $duration/$count).' secs/user)<hr size="1" noshade="noshade" />'."\n";
		}
		$tables[] = &$table;
		$this->create_legend_table($tables, $table);
	} // end function
	function get_exercisetype(&$questions, &$questionids, &$blank) {
		if (empty($questions)) {
			$type = $blank;
		} else {
			switch ($questions[$questionids[0]]->type) {
				case HOTPOT_JCB:
					$type = "JCB";
					break;
				case HOTPOT_JCLOZE :
					$type = "JCloze";
					break;
				case HOTPOT_JCROSS :
					$type = "JCross";
					break;
				case HOTPOT_JMATCH :
					$type = "JMatch";
					break;
				case HOTPOT_JMIX :
					$type = "JMix";
					break;
				case HOTPOT_JQUIZ :
					$type = "JQuiz";
					break;
				case HOTPOT_TEXTOYS_RHUBARB :
					$type = "Rhubarb";
					break;
				case HOTPOT_TEXTOYS_SEQUITUR :
					$type = "Sequitur";
					break;
				default:
					$type = $blank;
			}
		}
		return $type;
	}
	function set_head(&$options, &$table, $zone, $exercisetype='', $details=array(), $questioncount=0) {
		if (empty($table->head)) {
			$table->head = array();
		}
		switch ($zone) {
			case 'exercise':
				array_push($table->head,
					get_string('reportcoursename', 'hotpot'),
					get_string('reportsectionnumber', 'hotpot'),
					get_string('reportexercisenumber', 'hotpot'),
					get_string('reportexercisename', 'hotpot'),
					get_string('reportexercisetype', 'hotpot'),
					get_string('reportnumberofquestions', 'hotpot')
				);
				break;
			case 'user':
				array_push($table->head,
					get_string('reportstudentid', 'hotpot'),
					get_string('reportlogindate', 'hotpot'),
					get_string('reportlogintime', 'hotpot'),
					get_string('reportlogofftime', 'hotpot')
				);
				break;
			case 'attempt':
				array_push($table->head,
					get_string('reportattemptnumber', 'hotpot'),
					get_string('reportattemptstart', 'hotpot'),
					get_string('reportattemptfinish', 'hotpot')
				);
				break;
			case 'click':
				array_push($table->head,
					get_string('reportclicknumber', 'hotpot'),
					get_string('reportclicktime', 'hotpot'),
					get_string('reportclicktype', 'hotpot')
				);
				break;
			case 'details':
				foreach($details as $detail) {
					if ($exercisetype=='JQuiz' && $detail=='clues') {
						$detail = 'showanswer';
					}
					$detail = get_string("report$detail", 'hotpot');
					for ($i=0; $i<$questioncount; $i++) {
						$str = get_string('questionshort', 'hotpot', $i+1);
						if ($i==0 || $options['reportformat']!='htm') {
							$str = "$detail $str";
						}
						$table->head[] = $str;
					}
				}
				break;
			case 'totals':
				$reportpercentscore =get_string('reportpercentscore', 'hotpot');
				if (!function_exists('clean_getstring_data')) { // Moodle 1.4 (and less)
					$reportpercentscore = str_replace('%', '%%', $reportpercentscore);
				}
				array_push($table->head,
					get_string('reportthisclick', 'hotpot', get_string('reportquestionstried', 'hotpot')),
					get_string('reportsofar', 'hotpot', get_string('reportquestionstried', 'hotpot')),
					get_string('reportthisclick', 'hotpot', get_string('reportright', 'hotpot')),
					get_string('reportthisclick', 'hotpot', get_string('reportwrong', 'hotpot')),
					get_string('reportthisclick', 'hotpot', get_string('reportnottried', 'hotpot')),
					get_string('reportsofar', 'hotpot', get_string('reportright', 'hotpot')),
					get_string('reportsofar', 'hotpot', get_string('reportwrong', 'hotpot')),
					get_string('reportsofar', 'hotpot', get_string('reportnottried', 'hotpot')),
					get_string('reportthisclick', 'hotpot', get_string('reportanswers', 'hotpot')),
					get_string('reportthisclick', 'hotpot', get_string('reporthints', 'hotpot')),
					get_string('reportthisclick', 'hotpot', get_string($exercisetype=='JQuiz' ? 'reportshowanswer' : 'reportclues', 'hotpot')),
					get_string('reportthisclick', 'hotpot', get_string('reportevents', 'hotpot')),
					get_string('reportsofar', 'hotpot', get_string('reporthints', 'hotpot')),
					get_string('reportsofar', 'hotpot', get_string($exercisetype=='JQuiz' ? 'reportshowanswer' : 'reportclues', 'hotpot')),
					get_string('reportthisclick', 'hotpot', get_string('reportrawscore', 'hotpot')),
					get_string('reportthisclick', 'hotpot', get_string('reportmaxscore', 'hotpot')),
					get_string('reportthisclick', 'hotpot', $reportpercentscore),
					get_string('reportsofar', 'hotpot', get_string('reportrawscore', 'hotpot')),
					get_string('reportsofar', 'hotpot', get_string('reportmaxscore', 'hotpot')),
					get_string('reportsofar', 'hotpot', $reportpercentscore),
					get_string('reporthotpotscore', 'hotpot')
				);
				break;
		} // end switch
	}
	function set_align_and_wrap(&$table) {
		$count = count($table->head);
		for ($i=0; $i<$count; $i++) {
			if ($i==0 || $i==1 || $i==2 || $i==4 || $i==5 || $i>=7) {
				// numeric (and short text) columns
				$table->align[] = 'center';
				$table->wrap[] = '';
			} else {
				// text columns
				$table->align[] = 'left';
				$table->wrap[] = 'nowrap';
			}
		}
	}
	function set_data_exercise(&$cm, &$course, &$hotpot, &$questions, &$questionids, &$questioncount, &$blank) {
		// get exercise details (course name, section number, activity number, quiztype and question count)
		$record = get_record("course_sections", "id", $cm->section);
		$this->data['exercise'] = array(
			'course'  => $course->shortname,
			'section' => empty($record) ? $blank : $record->section+1,
			'number'  => empty($record) ? $blank : array_search($cm->id, explode(',', $record->sequence))+1,
			'name'    => $hotpot->name,
			'type'    => $this->get_exercisetype($questions, $questionids, $blank),
			'questioncount' => $questioncount
		);
	}
	function set_data_user(&$options, &$course, &$user) {
		global $CFG;
		// shortcut to first attempt record (which also hold user info)
		$attempt = &$user->attempts[0];
		$idnumber = $attempt->idnumber;
		if (empty($idnumber)) {
			$idnumber = fullname($attempt);
		}
		if ($options['reportformat']=='htm') {
			$idnumber = '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$attempt->userid.'&amp;course='.$course->id.'">'.$idnumber.'</a>';
		}
		$this->data['user'] = array(
			'idnumber' => $idnumber,
		);
	}
	function set_data_attempt(&$attempt, &$strftimedate, &$strftimetime, &$blank) {
		global $CFG;
		$records = get_records_sql_menu("
			SELECT userid, MAX(time) AS logintime
			FROM {$CFG->prefix}log
			WHERE userid=$attempt->userid AND action='login' AND time<$attempt->timestart
			GROUP BY userid
		");
		if (empty($records)) {
			$logindate = $blank;
			$logintime = $blank;
		} else {
			$logintime = $records[$attempt->userid];
			$logindate = trim(userdate($logintime, $strftimedate));
			$logintime = trim(userdate($logintime, $strftimetime));
		}
		$records = get_records_sql_menu("
			SELECT userid, MIN(time) AS logouttime
			FROM {$CFG->prefix}log
			WHERE userid=$attempt->userid AND action='logout' AND time>$attempt->cr_timefinish
			GROUP BY userid
		");
		if (empty($records)) {
			$logouttime = $blank;
		} else {
			$logouttime = $records[$attempt->userid];
			$logouttime = trim(userdate($logouttime, $strftimetime));
		}
		$this->data['attempt'] = array(
			'logindate'  => $logindate,
			'logintime'  => $logintime,
			'logouttime' => $logouttime,
			'number' => $attempt->attempt,
			'start'  => trim(userdate($attempt->timestart, $strftimetime)),
			'finish' => trim(userdate($attempt->cr_timefinish, $strftimetime)),
		);
	}
	function set_data_click($number, $time, $exercisetype, $click) {
		$types = array();
		foreach (array_keys($click['types']) as $type) {
			if ($exercisetype=='JQuiz' && $type=='clues') {
				$type = 'showanswer';
			} else {
				// remove final 's'
				$type = substr($type, 0, strlen($type)-1);
			}
			// $types[] = get_string($type, 'hotpot');
			$types[] = $type;
		}
		$this->data['click'] = array(
			'number' => $number,
			'time'   => $time,
			'type'   => empty($types) ? '??' : implode(',', $types)
		);
	}
	function set_data_totals(&$click, &$clicks, &$questioncount, &$blank, &$attempt) {
		$count= array(
			'click' => array(
				'correct' => count($click['correct']),
				'wrong' => count($click['wrong']),
				'answers' => count($click['answers']),
				'hints' => array_sum($click['hints']),
				'clues' => array_sum($click['clues']),
				'events' => array_sum($click['events']),
				'score' => array_sum($click['score']),
				'maxscore' => array_sum($click['weighting']),
			),
			'clicks' => array(
				'correct' => count($clicks['correct']),
				'wrong' => count($clicks['wrong']),
				'answers' => count($clicks['answers']),
				'hints' => array_sum($clicks['hints']),
				'clues' => array_sum($clicks['clues']),
				'score' => array_sum($clicks['score']),
				'maxscore' => array_sum($clicks['weighting']),
			)
		);
		foreach ($count as $period=>$values) {
			$count[$period]['nottried'] = $questioncount - ($values['correct'] + $values['wrong']);
			$count[$period]['percent'] = empty($values['maxscore']) ? $blank : round(100 * $values['score'] / $values['maxscore'], 0);
			// blank out zero click values
			if ($period=='click') {
				foreach ($values as $detail=>$value) {
					if ($detail=='answers' || $detail=='hints' || $detail=='clues' || $detail=='events') {
						if (empty($value)) {
							$count[$period][$detail] = $blank;
						}
					}
				}
			}
		}
		$this->data['totals'] = array(
			$count['click']['answers'],   // "q's tried"
			$count['clicks']['answers'],  // "q's tried so far"
			$count['click']['correct'],   // "right"
			$count['click']['wrong'],     // "wrong"
			$count['click']['nottried'],  // "not tried"
			$count['clicks']['correct'],  // "right so far"
			$count['clicks']['wrong'],    // "wrong so far"
			$count['clicks']['nottried'], // "not tried so far"
			$count['click']['answers'],   // "answers",
			$count['click']['hints'],     // "hints",
			$count['click']['clues'],     // "clues",
			$count['click']['events'],    // "answers",
			$count['clicks']['hints'],    // "hints so far",
			$count['clicks']['clues'],    // "clues so far",
			$count['click']['score'],     // 'raw score',
			$count['click']['maxscore'],  // 'max score',
			$count['click']['percent'],   // '% score'
			$count['clicks']['score'],    // 'raw score,
			$count['clicks']['maxscore'], // 'max score,
			$count['clicks']['percent'],  // '% score
			$attempt->score               // 'hotpot score'
		);
	}
	function update_event_count(&$click, $detail, $q) {
		if ($detail=='checks' || $detail=='hints' || $detail=='clues') {
			$click['types'][$detail] = true;
		}
		if ($detail=='answers' || $detail=='hints' || $detail=='clues') {
			$click['events'][$q] = isset($click['events'][$q]) ? $click['events'][$q]+1 : 1;
		}
		if ($detail=='answers') {
			$click['changes'][$q] = isset($click['changes'][$q]) ? $click['changes'][$q]+1 : 1;
		}
	}
	function set_data(&$cells, $zone) {
		foreach ($this->data[$zone] as $name=>$value) {
			$cells[] = $value;
		}
	}
} // end class
?>
