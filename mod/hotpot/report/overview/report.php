<?php  // $Id$

/// Overview report just displays a big table of all the attempts

class hotpot_report extends hotpot_default_report {

	function display(&$hotpot, &$cm, &$course, &$users, &$attempts, &$questions) {
	
		// retrieve form variables, if any
		global $download, $tablename;
		optional_variable($download, "");
		optional_variable($tablename, "");

		// message strings
		$strdeletecheck = get_string('deleteattemptcheck','quiz');

		// create scores table
		$this->create_overview_table($users, $attempts, $questions, $s_table=NULL, $download, $course, $hotpot, $abandoned=0);
		
		if (isteacher($course->id)) {
			$this->print_javascript();
			$onsub = "return delcheck('".$strdeletecheck."', 'selection')";

			// print buttons
			echo '<form method="post" action="report.php" name="delform" onsubmit="'.$onsub.'">'."\n";
			echo '<input type="hidden" name="del" value="selection">'."\n";
			echo '<input type="hidden" name="id" value="'.$cm->id.'">'."\n";
		}

		// print scores table
		$this->print_html_table($s_table);

		if (isteacher($course->id)) {
			//There might be a more elegant way than using the <center> tag for this
			echo '<center>'."\n";
			echo '<input type="submit" value="'.get_string("deleteselected").'">&nbsp;'."\n";
			if ($abandoned) {
				echo '<input type=button value="'.get_string('deleteabandoned', 'hotpot').'" onClick="if(delcheck('."'".addslashes(get_string('deleteabandonedcheck', 'hotpot', $abandoned))."', 'abandoned', true".'))document.delform.submit();">'."\n";
			}
			echo '<input type=button value="'.get_string("deleteall").'" onClick="if(delcheck('."'".addslashes($strdeletecheck)."', 'all', true".'))document.delform.submit();">'."\n";
			echo '</center>'."\n";
			echo '</form>'."\n";
		}

		return true;
	}
	function create_overview_table(&$users, &$attempts, &$questions, &$table, &$download, &$course, &$hotpot, &$abandoned) {

		global $CFG;
		
		$strtimeformat = get_string('strftimedatetime');
		$spacer = '&nbsp;';

		// start the table
		$table->border = 1;

		$table->head = array(
			"&nbsp;",  // picture
			get_string("name"), 
			hotpot_grade_heading($hotpot, $download),
			get_string("attempt", "quiz"), 
			get_string("time", "quiz"), 
			get_string("timetaken", "quiz"), 
			get_string("score", "quiz"),
		);
		$table->align = array("center", "left", "center", "center", "left", "left", "center");
		$table->wrap = array("nowrap", "nowrap", "nowrap", "nowrap", "nowrap", "nowrap", "nowrap");
		$table->width = 10;
		$table->size = array(10, "*", "*", "*", "*", "*", "*");

		foreach ($users as $user) {

			// shortcut to user info held in first attempt record
			$u = &$user->attempts[0];
			
			$picture = print_user_picture($u->userid, $course->id, $u->picture, false, true);
			if (function_exists("fullname")) {
				$name = fullname($u);
			} else {
				$name = "$u->firstname $u->lastname";
			}
			$name = '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$u->userid.'&course='.$course->id.'">'.$name.'</a>';
			$grade = isset($user->grade) ? $user->grade : $spacer;

			$attempts = array();
			$starttimes = array();
			$durations = array();
			$scores = array();

			foreach ($user->attempts as $attempt) {

				// increment count of abandoned attempts
				// if attempt is marked as finished but has no score
				if (!empty($attempt->timefinish) && !isset($attempt->score)) {
					$abandoned++;
				}

				$attemptnumber = $attempt->attempt;
				$starttime = trim(userdate($attempt->timestart, $strtimeformat));

				if (isset($attempt->score) && (isteacher($course->id) || $hotpot->review)) {
					$attemptnumber = '<a href="review.php?hp='.$hotpot->id.'&attempt='.$attempt->id.'">'.$attemptnumber.'</a>';
					$starttime = '<a href="review.php?hp='.$hotpot->id.'&attempt='.$attempt->id.'">'.$starttime.'</a>';
				}

				if (isteacher($course->id)) {
					$checkbox = '<input type=checkbox name="box'.$attempt->id.'" value="'.$attempt->id.'">'.$spacer;
				} else {
					$checkbox = '';
				}

				$attempts[] = $attemptnumber;
				$starttimes[] = $checkbox.$starttime;

				$durations[] = empty($attempt->timefinish) ? $spacer : format_time($attempt->timefinish - $attempt->timestart);

				$score = hotpot_format_score($attempt);
				if (is_numeric($score) && $score==$user->grade) { // best grade
					$score = '<span class="highlight">'.$score.'</span>';
				}
				$scores[] = $score;

			} // end foreach $attempt

			$attempts = implode("<br />\n", $attempts);
			$starttimes = implode("<br />\n", $starttimes);
			$durations = implode("<br />\n", $durations);
			$scores = implode("<br />\n", $scores);

			$table->data[] = array ($picture, $name, $grade, $attempts, $starttimes, $durations, $scores);

		} // end foreach $user
	}
	function print_javascript() {
		$strselectattempt = addslashes(get_string('selectattempt','hotpot'));
		print <<<END_OF_JAVASCRIPT

<script type="text/javascript">
<!--
function delcheck(p, v, x) {
	var r = false; // result

	// get length of form elements
	var f = document.delform;
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
	if (x || (n.appName=='Netscape' && parseint(n.appVersion)==2)) {
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
