<?PHP 
	// $Id$

	/// This page prints a hotpot quiz

	require_once("../../config.php");
	require_once("lib.php");

	$id = optional_param("id"); // Course Module ID, or
	$hp = optional_param("hp"); // hotpot ID

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

	// set nextpage (for error messages)
	$nextpage = "$CFG->wwwroot/course/view.php?id=$course->id";

	require_login($course->id);

	// header strings
	$title = strip_tags($course->shortname.': '.$hotpot->name);
	$heading = "$course->fullname";

	$target = empty($CFG->framename) ? '' : ' target="'.$CFG->framename.'"'; 
	$navigation = '<a'.$target.' href="'.$CFG->wwwroot.'/mod/hotpot/index.php?id='.$course->id.'">'.get_string("modulenameplural", "hotpot")."</a> -> $hotpot->name";
	if ($course->category) {
		$navigation = '<a'.$target.' href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'">'.$course->shortname.'</a> -> '.$navigation;
	}

	$button = update_module_button($cm->id, $course->id, get_string("modulename", "hotpot").'" style="font-size:0.75em;');
	$loggedinas = '<span class="logininfo">'.user_login_string($course, $USER).'</span>';


	$time = time();

	if (!isteacher($course->id)) {
		// check this quiz is available to this student

		// error message, if quiz is unavailable
		$error = '';

		// check quiz is visible
		if (!$cm->visible) {
			$error = get_string("activityiscurrentlyhidden");

		// check network address
		} else if ($hotpot->subnet && !address_in_subnet($_SERVER['REMOTE_ADDR'], $hotpot->subnet)) {
			$error = get_string("subneterror", "quiz");

		// check number of attempts
		} else if ($hotpot->attempts && $hotpot->attempts <= count_records('hotpot_attempts', 'hotpot', $hotpot->id, 'userid', $USER->id)) {
			$error = get_string("nomoreattempts", "quiz");

		// get password
		} else if ($hotpot->password && empty($_POST['hppassword'])) {

			print_header($title, $heading, $navigation, "", "", true, $button, $loggedinas, false);
			print_heading($hotpot->name);

			$boxalign = 'center';
			$boxwidth = 500;

			if (trim(strip_tags($hotpot->summary))) {
				print_simple_box_start($boxalign, $boxwidth);
				print '<div align="center">'.format_text($hotpot->summary)."</div>\n";
				print_simple_box_end();
				print "<br />\n";
			}

			print '<form name="passwordform" method="post" action="view.php?id='.$cm->id.'">'."\n";
			print_simple_box_start($boxalign, $boxwidth);

			print '<div align="center">';
			print get_string('requirepasswordmessage', 'quiz').'<br /><br />';
			print '<b>'.get_string('password').':</b> ';
			print '<input name="hppassword" type="password" value=""> ';
			print '<input type="submit" value="'.get_string("ok").'"> ';
			print "</div>\n";

			print_simple_box_end();
			print "</form>\n";

			print_footer();
			exit;

		// check password
		} else if ($hotpot->password && strcmp($hotpot->password, $_POST['hppassword'])) {
			$error = get_string("passworderror", "quiz");
			$nextpage = "view.php?id=$cm->id";

		// check quiz is open
		} else if ($hotpot->timeopen && $hotpot->timeopen > $time) {
			$error = get_string("quiznotavailable", "quiz", userdate($hotpot->timeopen))."<BR>\n";

		// check quiz is not closed
		} else if ($hotpot->timeclose && $hotpot->timeclose < $time) {
			$error = get_string("quizclosed", "quiz", userdate($hotpot->timeclose))."<BR>\n";
		}

		if ($error) {
			print_header($title, $heading, $navigation, "", "", true, $button, $loggedinas, false);
			notice($error, $nextpage);
			//
			// script stops here, if quiz is unavailable to student
			//
		}
	}

	$available_msg = '';
	if (!empty($hotpot->timeclose) && $hotpot->timeclose > $time) {
		// quiz is available until 'timeclose'
		$available_msg = get_string("quizavailable", "quiz", userdate($hotpot->timeclose))."<BR>\n";
	}

	// open and parse the source file
	if(!$hp = new hotpot_xml_quiz($hotpot)) {
		error("Quiz is unavailable at the moment");
	}

	$frameset = isset($_GET['frameset']) ? $_GET['frameset'] : '';

	// if HTML is being requested ...
	if (empty($_GET['js']) && empty($_GET['css'])) {

		$n = $hotpot->navigation;
		if (($n!=HOTPOT_NAVIGATION_FRAME && $n!=HOTPOT_NAVIGATION_IFRAME) || $frameset=='main') {

			add_to_log($course->id, "hotpot", "view", "view.php?id=$cm->id", "$hotpot->id", "$cm->id");

			$attemptid = hotpot_add_attempt($hotpot->id);
			if (! is_numeric($attemptid)) {
				error('Could not insert attempt record: '.$db->ErrorMsg);
			}
	
			if ($n!=HOTPOT_NAVIGATION_BUTTONS) {
				$hp->remove_nav_buttons();
			}

			$hp->adjust_media_urls();

			$hp->insert_submission_form($attemptid);
	
			if ($n==HOTPOT_NAVIGATION_GIVEUP) {
				$hp->insert_giveup_form($attemptid);
			}
		}
	}

	// insert hot-potatoes.js
	$hp->insert_script(HOTPOT_JS);

	// extract <head> tag
	$head = '';
	$pattern = '|^(.*)<head([^>]*)>(.*?)</head>|is';
	if (preg_match($pattern, $hp->html, $matches)) {
		$head = $matches[3];
	}

	// extract <style> tags
	$styles = '';
	$pattern = '|<style([^>]*)>(.*?)</style>|is';
	if (preg_match_all($pattern, $head, $matches)) {
		$count = count($matches[0]);
		for ($i=0; $i<$count; $i++) {
			$styles .= $matches[0][$i]."\n";
			$head = str_replace($matches[0][$i], '', $head);
		}
	}

	// extract <script> tags
	$scripts = '';
	$pattern = '|<script([^>]*)>(.*?)</script>|is';
	if (preg_match_all($pattern, $head, $matches)) {
		$count = count($matches[0]);
		for ($i=0; $i<$count; $i++) {
			$scripts .= $matches[0][$i]."\n";
			$head = str_replace($matches[0][$i], '', $head);
		}
	}

	// extract <body> tags
	$body = '';
	$bodytags = '';
	$pattern = '|^(.*)<body([^>]*)>(.*?)</body>|is';
	if (preg_match($pattern, $hp->html, $matches)) {
		$bodytags = $matches[2];
		$body = $matches[3];

		// workaround to ensure javascript onload routine for quiz is always executed
		//	$bodytags will only be inserted into the <body ...> tag
		//	if it is included in the theme/$CFG->theme/header.html,
		//	so some old or modified themes may not insert $bodytags
		if (preg_match('/onload=("|\')(.*?)(\\1)/i', $bodytags, $matches)) {
			$body .= ""
				.'<SCRIPT type="text/javascript">'."\n"
				."<!--\n"
				."	var s = (typeof(window.onload)=='function') ? onload.toString() : '';\n"
				."	if (s.indexOf('".$matches[2]."')<0) {\n"
				."		if (s=='') {\n" // no previous onload
				."			window.onload = new Function('".$matches[2]."');\n"
				."		} else {\n"
				."			window.onload_hotpot = onload;\n"
				."			window.onload = new Function('window.onload_hotpot();' + '".$matches[2]."');\n"
				."		}\n"
				."	 }\n"
				."//-->\n"
				."</SCRIPT>\n"
			;
		}
	}

	$footer = '</body></html>';

	// print the quiz to the browser

	if (isset($_GET['js'])) {
		print($scripts);
		exit;
	}
	if (isset($_GET['css'])) {
		print($styles);
		exit;
	}

	switch ($hotpot->navigation) {

		case HOTPOT_NAVIGATION_BAR:
			//update_module_button($cm->id, $course->id, $strmodulename.'" style="font-size:0.8em')
			print_header(
				$title, $heading, $navigation,
				"", $head.$styles.$scripts, true, $button, 
				$loggedinas, false, $bodytags
			);
			if (!empty($available_msg)) {
				notify($available_msg);
			}
			print $body.$footer;
		break;

		case HOTPOT_NAVIGATION_FRAME:

			switch ($frameset) {
				case 'top':
					print_header(
						$title, $heading, $navigation, 
						"", "", true, $button, 
						$loggedinas
					);
					print $footer;
				break;
	
				case 'main';
					if (!empty($available_msg)) {
						$hp->insert_message('<!-- BeginTopNavButtons -->', $available_msg);
					}
					print $hp->html;
				break;
	
				default:
					print "<HTML>\n";
					print "<HEAD><TITLE>$title</TITLE></HEAD>\n";
					print "<FRAMESET rows=85,*>\n";
					print "<FRAME src=\"view.php?id=$cm->id&frameset=top\">\n";
					print "<FRAME src=\"view.php?id=$cm->id&frameset=main\">\n";
					print "</FRAMESET>\n";
					print "</HTML>\n";
				break;
			} // end switch $frameset
		break;

		case HOTPOT_NAVIGATION_IFRAME:

			switch ($frameset) {
				case 'main';
					print $hp->html;
				break;
		
				default:
					$iframe_id = 'hotpot_iframe';
					$bodytags = " onload=\"set_iframe_height('$iframe_id')\"";
	
					$iframe_js = '<SCRIPT src="iframe.js" type="text/javascript" language="javascript">'."\n";
	
					print_header(
						$title, $heading, $navigation, 
						"", $head.$styles.$scripts.$iframe_js, true, $button, 
						$loggedinas, false, $bodytags
					);
					if (!empty($available_msg)) {
						notify($available_msg);
					}
					print "<IFRAME id=\"$iframe_id\" src=\"view.php?id=$cm->id&frameset=main\" height=\"100%\" width=\"100%\">";
					print "<ILAYER name=\"$iframe_id\" src=\"view.php?id=$cm->id&frameset=main\" height=\"100%\" width=\"100%\">";
					print "</ILAYER>\n";
					print "</IFRAME>\n";
					print $footer;
				break;
			} // end switch $frameset
		break;

		default:
			// HOTPOT_NAVIGATION_BUTTONS
			// HOTPOT_NAVIGATION_GIVEUP
			// HOTPOT_NAVIGATION_NONE
	
			if (!empty($available_msg)) {
				$hp->insert_message('<!-- BeginTopNavButtons -->', $available_msg);
			}
			print($hp->html);
	}
	
?>
