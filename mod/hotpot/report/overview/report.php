<?php  // $Id$
/// Overview report just displays a big table of all the attempts
class hotpot_report extends hotpot_default_report {

	function display(&$hotpot, &$cm, &$course, &$users, &$attempts, &$questions, &$options) {
		$tables = array();
		$this->create_overview_table($hotpot, $cm, $course, $users, $attempts, $questions, $options, $tables);
		$this->print_report($course, $hotpot, $tables, $options);
		return true;
	}
	function create_overview_table(&$hotpot, &$cm, &$course, &$users, &$attempts, &$questions, &$options, &$tables) {
		global $CFG;
		$strtimeformat = get_string('strftimedatetime');
		$is_html = ($options['reportformat']=='htm');
		$spacer = $is_html ? '&nbsp;' : ' ';
		$br = $is_html ? "<br />\n" : "\n";
		// initialize $table
		unset($table);
		$table->border = 1;
		$table->width = 10;
		$table->head = array();
		$table->align = array();
		$table->size = array();
		$table->wrap = array();
		// picture column, if required
		if ($is_html) {
			$table->head[] = $spacer;
			$table->align[] = 'center';
			$table->size[] = 10;
			$table->wrap[] = "nowrap";
		}
		array_push($table->head,
			get_string("name"),
			hotpot_grade_heading($hotpot, $options),
			get_string("attempt", "quiz"),
			get_string("time", "quiz"),
			get_string("reportstatus", "hotpot"),
			get_string("timetaken", "quiz"),
			get_string("score", "quiz")
		);
		array_push($table->align, "left", "center", "center", "left", "center", "center", "center");
		array_push($table->wrap, "nowrap", "nowrap", "nowrap", "nowrap", "nowrap", "nowrap", "nowrap");
		array_push($table->size, "*", "*", "*", "*", "*", "*", "*");
		$abandoned = 0;
		foreach ($users as $user) {
			// shortcut to user info held in first attempt record
			$u = &$user->attempts[0];
			$picture = '';
			$name = fullname($u);
			if ($is_html) {
				$picture = print_user_picture($u->userid, $course->id, $u->picture, false, true);
				$name = '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$u->userid.'&amp;course='.$course->id.'">'.$name.'</a>';
			}
			$grade = isset($user->grade) && $user->grade<>'&nbsp;' ? $user->grade : $spacer;
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
				// increment count of abandoned attempts
				// if attempt is marked as finished but has no score
				if ($attempt->status==HOTPOT_STATUS_ABANDONED) {
					$abandoned++;
				}
				$attemptnumber = $attempt->attempt;
				$starttime = trim(userdate($attempt->timestart, $strtimeformat));
				if ($is_html && isset($attempt->score) && (has_capability('mod/hotpot:viewreport',get_context_instance(CONTEXT_COURSE, $course->id)) || $hotpot->review)) {
					$attemptnumber = '<a href="review.php?hp='.$hotpot->id.'&amp;attempt='.$attempt->id.'">'.$attemptnumber.'</a>';
					$starttime = '<a href="review.php?hp='.$hotpot->id.'&amp;attempt='.$attempt->id.'">'.$starttime.'</a>';
				}
				if ($is_html && has_capability('mod/hotpot:viewreport',get_context_instance(CONTEXT_COURSE, $course->id))) {
					$checkbox = '<input type="checkbox" name="box'.$attempt->clickreportid.'" value="'.$attempt->clickreportid.'" />'.$spacer;
				} else {
					$checkbox = '';
				}
				$timetaken = empty($attempt->timefinish) ? $spacer : format_time($attempt->timefinish - $attempt->timestart);
				$score = hotpot_format_score($attempt);
				if ($is_html && is_numeric($score) && $score==$user->grade) { // best grade
					$score = '<span class="highlight">'.$score.'</span>';
				}
				array_push($data,
					$attemptnumber,
					$checkbox.$starttime,
					hotpot_format_status($attempt),
					$timetaken,
					$score
				);
				$table->data[] = $data;
				$data = array();
			} // end foreach $attempt
			$table->data[] = 'hr';
		} // end foreach $user
		// remove final 'hr' from data rows
		array_pop($table->data);
		// add the "delete" form to the table
		if ($options['reportformat']=='htm' && has_capability('mod/hotpot:viewreport',get_context_instance(CONTEXT_COURSE, $course->id))) {
			$strdeletecheck = get_string('deleteattemptcheck','quiz');
			$table->start = $this->deleteform_javascript();
			$table->start .= '<form method="post" action="report.php" id="deleteform" onsubmit="'."return deletecheck('".$strdeletecheck."', 'selection')".'">'."\n";
			$table->start .= '<input type="hidden" name="del" value="selection" />'."\n";
			$table->start .= '<input type="hidden" name="id" value="'.$cm->id.'" />'."\n";
			$table->finish = '<center>'."\n";
			$table->finish .= '<input type="submit" value="'.get_string("deleteselected").'" />&nbsp;'."\n";
			if ($abandoned) {
				$table->finish .= '<input type="button" value="'.get_string('deleteabandoned', 'hotpot').'" onClick="if(deletecheck('."'".addslashes(get_string('deleteabandonedcheck', 'hotpot', $abandoned))."', 'abandoned', true".')) document.getElementById(\'deleteform\').submit();" />'."\n";
			}
			$table->finish .= '<input type="button" value="'.get_string("deleteall").'" onClick="if(deletecheck('."'".addslashes($strdeletecheck)."', 'all', true".'))document.getElementById(\'deleteform\').submit();" />'."\n";
			$table->finish .= '</center>'."\n";
			$table->finish .= '</form>'."\n";
		}
		$tables[] = &$table;
	}
	function deleteform_javascript() {
		$strselectattempt = addslashes(get_string('selectattempt','hotpot'));
		return <<<END_OF_JAVASCRIPT
<script type="text/javascript">
<!--
function deletecheck(p, v, x) {
	var r = false; // result
	// get length of form elements
	var f = document.getElementById('deleteform');
	var l = f ? f.elements.length : 0;
	// count selected items, if necessary
	if (!x) {
		x = 0;
		for (var i=0; i<l; i++) {
			var obj = f.elements[i];
			if (obj.type && obj.type=='checkbox' && obj.checked) {
				x++;
			}
		}
	}
	// confirm deletion
	var n = navigator;
	if (x || (n.appName=='Netscape' && parseInt(n.appVersion)==2)) {
		r = confirm(p);
		if (r) {
			f.del.value = v;
		}
	} else {
		alert('$strselectattempt');
	}
	return r;
}
//-->
</script>
END_OF_JAVASCRIPT
;
	} // end function
} // end class
?>
