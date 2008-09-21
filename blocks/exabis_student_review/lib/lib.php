<?php

define('DECIMALPOINTS', 1);

function block_exabis_student_review_check_periods($printBoxInsteadOfError = false) {
	block_exabis_student_review_has_wrong_periods($printBoxInsteadOfError);
	block_exabis_student_review_check_if_period_ovelap($printBoxInsteadOfError);
}

function block_exabis_student_review_has_wrong_periods($printBoxInsteadOfError = false) {
	global $CFG;
	// check if any entry has a starttime after the endtime:
	$content = '';
	$wrongs = get_records_sql('SELECT p.description, p.starttime, p.endtime FROM ' . $CFG->prefix . 'block_exabstudreviperi p WHERE starttime > endtime');
	
	if ($wrongs) {
		foreach($wrongs as $wrong) {
			if($printBoxInsteadOfError) {
				notify(get_string('errorstarttimebeforeendtime', 'block_exabis_student_review', $wrong));
			}
			else {
				print_error('errorstarttimebeforeendtime', 'block_exabis_student_review', '', $wrong);
			}
		}
	}
	
	return true;
}

function block_exabis_student_review_check_if_period_ovelap($printBoxInsteadOfError = false) {
	global $CFG;
	$allPeriods = get_records('block_exabstudreviperi', '', '', 'id, description, starttime, endtime');
	
	$periodshistory = '';
	foreach ($allPeriods as $actPeriod) {
		if($periodshistory == '') {
			$periodshistory .= $actPeriod->id;
		}
		else {
			$periodshistory .= ', ' . $actPeriod->id;
		}
		$ovelapPeriods = get_records_sql('SELECT id, description, starttime, endtime FROM ' . $CFG->prefix . 'block_exabstudreviperi
										  WHERE (id NOT IN (' . $periodshistory . ')) AND NOT ( (starttime < ' . $actPeriod->starttime . ' AND endtime < ' . $actPeriod->starttime . ')
										  OR (starttime > ' . $actPeriod->endtime . ' AND endtime > ' . $actPeriod->endtime . ') )');
		
		if ($ovelapPeriods) {
			foreach ($ovelapPeriods as $overlapPeriod) {
				$a = new stdClass();
				$a->period1 = $actPeriod->description;
				$a->period2 = $overlapPeriod->description;
				
				if($printBoxInsteadOfError) {
					notify(get_string('periodoverlaps', 'block_exabis_student_review', $a));
				}
				else {
					print_error('periodoverlaps', 'block_exabis_student_review', '', $a);
				}
			}
		}
	}
}

function block_exabis_student_review_get_active_period($printBoxInsteadOfError = false) {
	global $CFG;
	$periods = get_records_sql('SELECT * FROM ' . $CFG->prefix . 'block_exabstudreviperi WHERE (starttime < ' . time() . ') AND (endtime > ' . time() . ')');

	// genau 1e periode?
	if(is_array($periods) && (count($periods) == 1)) {
		return array_shift($periods);
	} else {
		if($printBoxInsteadOfError) {
			notify(get_string('periodserror', 'block_exabis_student_review'));
		}
		else {
			print_error('periodserror', 'block_exabis_student_review');
		}
	}
}

function block_exabis_student_review_get_report($student_id, $period_id) {
	global $CFG;

	$report = new stdClass();
			
	$team = get_record_sql('SELECT \'1\' AS id, ROUND(AVG(team), ' . DECIMALPOINTS . ') AS avgteam FROM ' . $CFG->prefix . 'block_exabstudrevirevi WHERE student_id=' . $student_id . ' AND periods_id=' . $period_id);
	$report->team = is_null($team->avgteam) ? '': $team->avgteam;
			
	$resp = get_record_sql('SELECT \'1\' AS id, ROUND(AVG(resp), ' . DECIMALPOINTS . ') AS avgresp FROM ' . $CFG->prefix . 'block_exabstudrevirevi WHERE student_id=' . $student_id . ' AND periods_id=' . $period_id);
	$report->resp = is_null($resp->avgresp) ? '': $resp->avgresp;
	
	$inde = get_record_sql('SELECT \'1\' AS id, ROUND(AVG(inde), ' . DECIMALPOINTS . ') AS avginde FROM ' . $CFG->prefix . 'block_exabstudrevirevi WHERE student_id=' . $student_id . ' AND periods_id=' . $period_id);
	$report->inde = is_null($inde->avginde) ? '': $inde->avginde;
	
	$numrecords = get_record_sql('SELECT COUNT(id) AS count FROM ' . $CFG->prefix . 'block_exabstudrevirevi WHERE student_id=' . $student_id . ' AND periods_id=' . $period_id);
	$report->numberOfEvaluations = $numrecords->count;
	
	$comments = get_records_sql('SELECT id, teacher_id, review FROM ' . $CFG->prefix . 'block_exabstudrevirevi WHERE student_id = \'' . $student_id . '\' AND periods_id =  \'' . $period_id . '\' AND TRIM(review) !=  \'\'');

	$report->comments = array();
	if (is_array($comments)) {
		foreach($comments as $comment) {
			$teacher = get_record('user', 'id', $comment->teacher_id);
			
			$newcomment = new stdClass();
			$newcomment->name = fullname($teacher, $teacher->id);
			$newcomment->review = format_text($comment->review);
			
			$report->comments[] = $newcomment;
		}
	}
	
	return $report;
}

function block_exabis_student_review_read_template_file($filename) {
	global $CFG;
	$filecontent = '';
	
	if(is_file($CFG->dirroot . '/blocks/exabis_student_review/template/' . $filename)) {
		$filecontent = file_get_contents ($CFG->dirroot . '/blocks/exabis_student_review/template/' . $filename);
	}
	else if(is_file($CFG->dirroot. '/blocks/exabis_student_review/default_template/' . $filename)) {
		$filecontent = file_get_contents ($CFG->dirroot. '/blocks/exabis_student_review/default_template/' . $filename);
	}
	$filecontent = str_replace ( '###WWWROOT###', $CFG->wwwroot, $filecontent);
	return $filecontent;
}

function block_exabis_student_review_print_student_report_header() {
	echo block_exabis_student_review_read_template_file('header.html');
}
function block_exabis_student_review_print_student_report_footer() {
	echo block_exabis_student_review_read_template_file('footer.html');
}

function block_exabis_student_review_print_student_report($studentid, $periodid, $classstring)
{
	$studentreport = '';
	$studentreportcommentstemplate = '';
	$studentreportcomments = '';
	if(!$studentReport = block_exabis_student_review_get_report($studentid, $periodid)) {
		print_error('studentnotfound','block_exabis_student_review');
	}
	
	$student = get_record('user', 'id', $studentid);
	$studentreport = block_exabis_student_review_read_template_file('student.html');
	$studentreport = str_replace ( '###FIRSTNAME###', $student->firstname, $studentreport);
	$studentreport = str_replace ( '###LASTNAME###', $student->lastname, $studentreport);
	
	$studentreport = str_replace ( '###CLASS###', $classstring, $studentreport);
	$studentreport = str_replace ( '###NUM###', $studentReport->numberOfEvaluations, $studentreport);
	
	$studentreport = str_replace ( '###REPORT_TEAM###', $studentReport->team, $studentreport);
	$studentreport = str_replace ( '###REPORT_RESP###', $studentReport->resp, $studentreport);
	$studentreport = str_replace ( '###REPORT_INDE###', $studentReport->inde, $studentreport);
	
	
	if (!$studentReport->comments) {
		// Keine Kommentare
		$studentreportcomments .= block_exabis_student_review_read_template_file('no_comments.html');
	}
	else {
		// Kommentare vorhanden
		$studentreportcommentstemplate = block_exabis_student_review_read_template_file('comment.html');
		
		foreach($studentReport->comments as $comment) {
			$studentreportcommentstemplatetmp = $studentreportcommentstemplate;
			$studentreportcommentstemplatetmp = str_replace ( '###NAME###', $comment->name, $studentreportcommentstemplatetmp);
			$studentreportcommentstemplatetmp = str_replace ( '###REVIEW###', $comment->review, $studentreportcommentstemplatetmp);
			
			$studentreportcomments .= $studentreportcommentstemplatetmp;
		}
	}

	$studentreport = str_replace ( '###COMMENTS###', $studentreportcomments, $studentreport);
	
	echo $studentreport;
}

function block_exabis_student_review_print_header($items, $options = array())
{
	global $CFG, $COURSE;

	$items = (array)$items;
	$strheader = get_string('modulename', 'block_exabis_student_review');

	// navigationspfad
	$navlinks = array();
	$navlinks[] = array('name' => $strheader, 'link' => null, 'type' => 'title');

	$last_item_name = '';

	foreach ($items as $level => $item) {
		if (!is_array($item)) {
			if (!is_string($item)) {
				echo 'noch nicht unterstützt';
			}

			if ($item == 'periods')
				$link = 'periods.php?courseid='.$COURSE->id;
			elseif ($item == 'configuration')
				$link = 'configuration.php?courseid='.$COURSE->id;
			elseif ($item == 'review')
				$link = 'review.php?courseid='.$COURSE->id;
			else
				$link = null;

			if ($item[0] == '=')
				$item_name = substr($item, 1);
			else
				$item_name = get_string($item, "block_exabis_student_review");

			$item = array('name' => $item_name, 'link' => ($link ? $CFG->wwwroot.'/blocks/exabis_student_review/'.$link : null));
		}

		if (!isset($item['type']))
			$item['type'] = 'misc';

		$last_item_name = $item['name'];
		$navlinks[] = $item;
	}

	$navigation = build_navigation($navlinks);
	print_header_simple($strheader.': '.$last_item_name, '', $navigation, "", "", true);

	echo '<div id="exabis_student_review">';

	// header
	if (empty($options['noheading']))
		print_heading($last_item_name);
}

function block_exabis_student_review_print_footer()
{
	global $COURSE;

	echo '</div>';
	
	print_footer($COURSE);
}