<?PHP  // $Id$

// This script uses installed report plugins to print quiz reports

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

	// set homeurl of couse (for error messages)
	$course_homeurl = "$CFG->wwwroot/course/view.php?id=$course->id";

	require_login($course->id);

	// get report mode
	if (isteacher($course->id)) {
		$mode = optional_param("mode", "overview", 0);
		if (is_array($mode)) {
			$mode = array_keys($mode);
			$mode = $mode[0];
		}
	} else {
		// students have no choice
		$mode = 'overview';
	}

	// get report attributes
	if (isadmin()) {
		$reportcourse = optional_param("reportcourse", "this");
	} else {
		// students and ordinary teachers have no choice
		$reportcourse = 'this';
	}
	if (isteacher($course->id)) {
		$reportusers = optional_param("reportusers", "all");
	} else {
		// students have no choice
		$reportusers = 'this';
	}
	$reportattempts = optional_param("reportattempts", "all");

/// Start the report

	add_to_log($course->id, "hotpot", "report", "report.php?id=$cm->id", "$hotpot->id", "$cm->id");

	// print page header. if required
	if (empty($noheader)) {
		hotpot_print_report_heading($course, $cm, $hotpot, $mode);
		if (isteacher($course->id)) {
			hotpot_print_report_selector($course, $hotpot, $mode, $reportcourse, $reportusers, $reportattempts);
		}
	}

	// delete selected attempts, if any
	if (isteacher($course->id)) {
		$del = optional_param("del", "");
		hotpot_delete_selected_attempts($hotpot, $del);
	}

	$hotpot_ids = '';
	$course_ids = '';
	switch ($reportcourse) {
		case 'this':
			$course_ids = $course->id;
			$hotpot_ids = $hotpot->id;
			break;
		case 'all' :
			$records = get_records_select_menu('user_teachers', "userid='$USER->id'", 'course', 'id, course');
			$course_ids = join(',', array_values($records));

			$records = get_records_select_menu('hotpot', "reference='$hotpot->reference'", 'reference', 'id, reference');
			$hotpot_ids = join(',', array_keys($records));
			break;
	}

	$user_ids = '';
	$users = array();
	switch ($reportusers) {
		case 'all':
			$admin_ids = get_records_select_menu('user_admins');
			if (is_array($admin_ids)) {
				$users = array_merge($users, $admin_ids);
			}
			$creator_ids = get_records_select_menu('user_coursecreators');
			if (is_array($creator_ids)) {
				$users = array_merge($users, $creator_ids);
			}
			$teacher_ids = get_records_select_menu('user_teachers', "course IN ($course_ids)", 'course', 'id, userid');
			if (is_array($teacher_ids)) {
				$users = array_merge($users, $teacher_ids);
			}
			$guest_id = get_records_select_menu('user', "username='guest'", '', 'id,id');
			if (is_array($guest_id)) {
				$users = array_merge($users, $guest_id);
			}
		case 'students':
			$student_ids = get_records_select_menu('user_students', "course IN ($course_ids)", 'course', 'id, userid');
			if (is_array($student_ids)) {
				$users = array_merge($users, $student_ids);
			}
			$user_ids = join(',', array_values($users));
			break;
		case 'this':
			$user_ids = $USER->id;
			break;
	}

	if (empty($user_ids)) {
		print_heading(get_string('nousersyet'));
		exit;
	}

	// get attempts for these users
	$fields = 'a.*, u.firstname, u.lastname, u.picture';
	$table = "{$CFG->prefix}hotpot_attempts AS a, {$CFG->prefix}user AS u";
	$select = ($mode=='simplestat' || $mode=='fullstat') ? 'a.score IS NOT NULL' : 'a.timefinish>0';
	$select .= " AND a.hotpot IN ($hotpot_ids) AND a.userid IN ($user_ids) AND a.userid = u.id";
	$order = "u.lastname, a.attempt";
	if ($reportattempts=='best') {
		$fields .= ", MAX(a.score) AS grade";
		$select .= " GROUP BY a.userid";
	}
	$attempts = get_records_sql("SELECT $fields FROM $table WHERE $select ORDER BY $order");

	if (empty($attempts)) {
		print_heading(get_string('noattempts','quiz'));
		exit;
	}

	// get the questions
	if (!$questions = get_records_select('hotpot_questions', "hotpot='$hotpot->id'")) {
		$questions = array();
	}

	// get grades
	$grades = hotpot_get_grades($hotpot, $user_ids);

	// get list of attempts by user
	$users = array();
	foreach ($attempts as $id=>$attempt) {

		$userid = $attempt->userid;

		if (!isset($users[$userid])) {
			$grade = isset($grades[$userid]) ? $grades[$userid] : '&nbsp;';
			$users[$userid]->grade = $grade;
			$users[$userid]->attempts = array();
		}

		$users[$userid]->attempts[] = &$attempts[$id];

	}

/// Open the selected hotpot report and display it

	$mode = clean_filename($mode);

	if (! is_readable("report/$mode/report.php")) {
		error("Report not known (".clean_text($mode).")", $course_homeurl);
	}

	include("report/default.php");  // Parent class
	include("report/$mode/report.php");

	$report = new hotpot_report();

	if (! $report->display($hotpot, $cm, $course, $users, $attempts, $questions)) {
		error("Error occurred during pre-processing!", $course_homeurl);
	}

	if (empty($noheader)) {
		print_footer($course);
	}

//////////////////////////////////////////////
/// functions to delete attempts and responses

function hotpot_grade_heading($hotpot, $download) {

	global $HOTPOT_GRADEMETHOD;
	$grademethod = $HOTPOT_GRADEMETHOD[$hotpot->grademethod];

	if ($hotpot->grade!=100) {
		$grademethod = "$hotpot->grade x $grademethod/100";
	}
	if (empty($download)) {
		$grademethod = '<FONT size="1">'.$grademethod.'</FONT>';
	}
	$nl = $download ? "\n" : '<br>';
	return get_string('grade')."$nl($grademethod)";
}
function hotpot_delete_selected_attempts(&$hotpot, $del) {

	$select = '';
	switch ($del) {
		case 'all' :
			$select = "hotpot='$hotpot->id'";
			break;
		case 'abandoned':
			$select = "hotpot='$hotpot->id' AND timefinish>0 AND score IS NULL";
			break;
		case 'selection':
			$ids = (array)data_submitted();
			unset($ids['del']);
			unset($ids['id']);
			if (!empty($ids)) {
				$select = "hotpot='$hotpot->id' AND id IN (".implode(',', $ids).")";
			}
			break;
	}

	// delete attempts using $select, if it is set
	if ($select) {

		$table = 'hotpot_attempts';
		if ($attempts = get_records_select($table, $select)) {

			hotpot_delete_and_notify($table, $select, get_string('attempts', 'quiz'));

			$table = 'hotpot_responses';
			$select = 'attempt IN ('.implode(',', array_keys($attempts)).')';
			hotpot_delete_and_notify($table, $select, get_string('answer', 'quiz'));
		}
	}

}

//////////////////////////////////////////////
/// functions to print the report headings and 
/// report selector menus

function hotpot_print_report_heading(&$course, &$cm, &$hotpot, &$mode) {
	$strmodulenameplural = get_string("modulenameplural", "hotpot");
	$strmodulename  = get_string("modulename", "hotpot");

	$title = "$course->shortname: $hotpot->name";
	$heading = "$course->fullname";

	$navigation = "<a href=index.php?id=$course->id>$strmodulenameplural</a> -> ";
	$navigation .= "<a href=\"view.php?id=$cm->id\">$hotpot->name</a> -> ";
	if (isteacher($course->id)) {
		$navigation .= get_string("report$mode", "quiz");
	} else {
		$navigation .= get_string("report", "quiz");
	}
	if ($course->category) {
		$navigation = "<a href=\"../../course/view.php?id=$course->id\">$course->shortname</a> -> $navigation";
	}
	$button = update_module_button($cm->id, $course->id, $strmodulename);

	print_header($title, $heading, $navigation, "", "", true, $button, navmenu($course, $cm));

	print_heading($hotpot->name);
}
function hotpot_print_report_selector(&$course, &$hotpot, &$mode, &$reportcourse, &$reportusers, &$reportattempts) {

	global $CFG;

	$reports = hotpot_get_report_names('overview,simplestat');

	print '<form method="post" action="'."$CFG->wwwroot/mod/hotpot/report.php?hp=$hotpot->id".'">';
	print '<table cellpadding="4" align="center">';

	$menus = array();
	if (isadmin()) {
		$menus['reportcourse'] = array(
			'this' => get_string('thiscourse', 'hotpot'),
			'all' => get_string('allmycourses', 'hotpot')
		);
	}
	$menus['reportusers'] = array(
		'students' => get_string('students'),
		'all' => get_string('allparticipants')
	);
	$menus['reportattempts'] = array(
		'best' => get_string('bestattempt', 'hotpot'),
		'all' => get_string('allattempts', 'hotpot')
	);

	print '<tr><td align="center" colspan="'.count($reports).'">';
	foreach ($menus as $name => $options) {
		eval('$value=$'.$name.';');
		print choose_from_menu($options, $name, $value, "", "", 0, true);
	}
	if (isteacher($course->id)) {
		helpbutton('reportselector', get_string('report'), 'hotpot');
	}
	print '</td></tr>';

	print '<tr>';
	foreach ($reports as $name) {
		print '<td><input type="submit" name="'."mode[$name]".'" value="'.get_string("report$name", "quiz").'"></td>';
	}
	print '</tr>';

	print '</table>';

	print '<hr size="1" noshade="noshade" />';
	print '</form>'."\n";
}
function hotpot_get_report_names($names='') {
	// $names : optional list showing required order reports names

	$reports = array();

	// convert $names to an array, if necessary (usually is)
	if (!is_array($names)) {
		$names = explode(',', $names);
	}

	$plugins = get_list_of_plugins("mod/hotpot/report");
	foreach($names as $name) {
		if (is_numeric($i = array_search($name, $plugins))) {
			$reports[] = $name;
			unset($plugins[$i]);
		}
	}

	// append remaining plugins
	$reports = array_merge($reports, $plugins);

	return $reports;
}
function hotpot_get_report_users($course_ids, $reportusers) {
	$users = array();

	/// Check to see if groups are being used in this module
	$currentgroup = false;
	if ($groupmode = groupmode($course, $cm)) {   // Groups are being used
		$currentgroup = setup_and_print_groups($course, $groupmode, "report.php?id=$cm->id&mode=simplestat");
	}

	$sort = "u.lastname ASC";

	switch ($reportusers) {
		case 'students':
			if ($currentgroup) {
				$users = get_group_students($currentgroup, $sort);
			} else {
				$users = get_course_students($course->id, $sort);
			}
			break;
		case 'all':
			if ($currentgroup) {
				$users = get_group_users($currentgroup, $sort);
			} else {
				$users = get_course_users($course->id, $sort);
			}
			break;
	}

	return $users;
}
?>
