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
		$mode = optional_param("mode", "overview");
	} else {
		// students have no choice
		$mode = 'overview';
	}

	// assemble array of form data
	$formdata = array(
		'mode' => $mode,
		'reportcourse'     => isadmin() ? optional_param('reportcourse', get_user_preferences('hotpot_reportcourse', 'this')) : 'this',
		'reportusers'      => isteacher($course->id) ? optional_param('reportusers', get_user_preferences('hotpot_reportusers', 'all')) : 'this',
		'reportattempts'   => optional_param('reportattempts', get_user_preferences('hotpot_reportattempts', 'all')),
		'reportformat'     => optional_param('reportformat', 'htm'),
		'reportshowlegend' => optional_param('reportshowlegend', get_user_preferences('hotpot_reportshowlegend', '0')),
		'reportencoding'   => optional_param('reportencoding', get_user_preferences('hotpot_reportencoding', '')),
		'reportwrapdata'   => optional_param('reportwrapdata', get_user_preferences('hotpot_reportwrapdata', '1')),
	);

	foreach ($formdata as $name=>$value) {
		set_user_preference("hotpot_$name", $value);
	}

/// Start the report

	add_to_log($course->id, "hotpot", "report", "report.php?id=$cm->id&mode=$mode", "$hotpot->id", "$cm->id");

	// print page header. if required
	if ($formdata['reportformat']=='htm') {
		hotpot_print_report_heading($course, $cm, $hotpot, $mode);
		if (isteacher($course->id)) {
			hotpot_print_report_selector($course, $hotpot, $formdata);
		}
	}

	// delete selected attempts, if any
	if (isteacher($course->id)) {
		$del = optional_param("del", "");
		hotpot_delete_selected_attempts($hotpot, $del);
	}

	$hotpot_ids = '';
	$course_ids = '';
	switch ($formdata['reportcourse']) {
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
	switch ($formdata['reportusers']) {
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
			// add students next

		case 'students':
			$student_ids = get_records_select_menu('user_students', "course IN ($course_ids)", 'course', 'id, userid');
			if (is_array($student_ids)) {
				$users = array_merge($users, $student_ids);
			}
			$user_ids = array_values($users);
			sort($user_ids);
			$user_ids = join(',', array_unique($user_ids));
			break;

		case 'this': // current user only
			$user_ids = $USER->id;
			break;

		default: // specific user selected by teacher
			if (is_numeric($formdata['reportusers'])) {
				$user_ids = $formdata['reportusers'];
			}
	}

	if (empty($user_ids)) {
		print_heading(get_string('nousersyet'));
		exit;
	}

	// database table and selection conditions
	$table = "{$CFG->prefix}hotpot_attempts AS a";
	$select = "a.hotpot IN ($hotpot_ids) AND a.userid IN ($user_ids)";
	if ($mode!='overview') {
		$select .= ' AND a.status<>'.HOTPOT_STATUS_INPROGRESS;
	}

	// confine attempts if necessary
	switch ($formdata['reportattempts']) {
		case 'best':
			$function = 'MAX';
			$fieldnames = array('score', 'id', 'clickreportid');
			$defaultvalue = 0;
			break;
		case 'first':
			$function = 'MIN';
			$fieldnames = array('timefinish', 'id', 'clickreportid');
			$default_value = time();
			break;
		case 'last':
			$function = 'MAX';
			$fieldnames = array('timefinish', 'id', 'clickreportid');
			$defaultvalue = time();
			break;
		default: // 'all' and any others
			$function = '';
			$fieldnames = array();
			$defaultvalue = '';
			break;
	}
	if (empty($function) || empty($fieldnames)) {
		// do nothing (i.e. get ALL attempts)
	} else {
		$groupby = 'userid';
		$records = hotpot_get_records_groupby($function, $fieldnames, $table, $select, $groupby);

		$ids = array();
		foreach ($records as $record) {
			$ids[] = $record->clickreportid;
		}
		$select = "a.clickreportid IN (".join(',', $ids).")";
	}

	// pick out last attempt in each clickreport series
	$cr_attempts = hotpot_get_records_groupby('MAX', array('timefinish', 'id'), $table, $select, 'clickreportid');

	$fields = 'a.*, u.firstname, u.lastname, u.picture';
	if ($mode=='click') {
		$fields .= ', u.idnumber';
	} else { 
		// overview, simple and detailed reports 
		// get last attempt record in clickreport series
		$ids = array();
		foreach ($cr_attempts as $cr_attempt) {
			$ids[] = $cr_attempt->id;
		}
		if (empty($ids)) {
			$select = "";
		} else {
			$ids = array_unique($ids);
			sort($ids);
			$select = "a.id IN (".join(',', $ids).")";
		}
	}

	$attempts = array();

	if ($select) {
		// add user information to SQL query
		$select .= ' AND a.userid = u.id';
		$table .= ", {$CFG->prefix}user AS u";
		$order = "u.lastname, a.attempt, a.timefinish";
		// get the attempts (at last!)
		$attempts = get_records_sql("SELECT $fields FROM $table WHERE $select ORDER BY $order");
	}

	// stop now if no attempts were found
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

	// get list of attempts by user and set reference to last attempt in clickreport series
	$users = array();
	foreach ($attempts as $id=>$attempt) {

		$userid = $attempt->userid;

		if (!isset($users[$userid])) {
			$users[$userid]->grade = isset($grades[$userid]) ? $grades[$userid] : '&nbsp;';
			$users[$userid]->attempts = array();
		}

		$users[$userid]->attempts[] = &$attempts[$id];

		if ($mode=='click' && isset($cr_attempts[$id])) {
			$attempts[$id]->cr_lastclick = $cr_attempts[$id]->id;
			$attempts[$id]->cr_timefinish = $cr_attempts[$id]->timefinish;
		}
	}

	if ($mode!='overview') {

		// initialise details of responses to questions in these attempts
		foreach ($attempts as $a=>$attempt) {
			$attempts[$a]->responses = array();
		}
		foreach ($questions as $q=>$question) {
			$questions[$q]->attempts = array();
		}

		// get reponses to these attempts
		$attempt_ids = join(',',array_keys($attempts));
		if (!$responses = get_records_sql("SELECT * FROM {$CFG->prefix}hotpot_responses WHERE attempt IN ($attempt_ids)")) {
			$responses = array();
		}

		// ids of questions used in these responses
		$questionids = array();

		foreach ($responses as $response) {
			// shortcuts to the attempt and question ids
			$a = $response->attempt;
			$q = $response->question;

			// check the attempt and question objects exist
			// (if they don't exist, something is very wrong!)
			if (isset($attempts[$a]) || isset($questions[$q])) {

				// add the response for this attempt
				$attempts[$a]->responses[$q] = $response;

				// add a reference from the question to the attempt which includes this question
				$questions[$q]->attempts[] = &$attempts[$a];

				// flag this id as being used
				$questionids[$q] = true;
			}
		}

		// remove unused questions
		$questionids = array_keys($questionids);
		foreach ($questions as $id=>$question) {
			if (!in_array($id, $questionids)) {
				unset($questions[$id]);
			}
		}
	}

/// Open the selected hotpot report and display it

	$mode = clean_filename($mode);

	if (! is_readable("report/$mode/report.php")) {
		error("Report not known (".clean_text($mode).")", $course_homeurl);
	}

	include("report/default.php");  // Parent class
	include("report/$mode/report.php");

	$report = new hotpot_report();

	if (! $report->display($hotpot, $cm, $course, $users, $attempts, $questions, $formdata)) {
		error("Error occurred during report processing!", $course_homeurl);
	}

	if ($formdata['reportformat']=='htm') {
		print_footer($course);
	}
//////////////////////////////////////////////
/// functions to delete attempts and responses

function hotpot_grade_heading($hotpot, $formdata) {

	global $HOTPOT_GRADEMETHOD;
	$grademethod = $HOTPOT_GRADEMETHOD[$hotpot->grademethod];

	if ($hotpot->grade!=100) {
		$grademethod = "$hotpot->grade x $grademethod/100";
	}
	if ($formdata['reportformat']=='htm') {
		$grademethod = '<font size="1">'.$grademethod.'</font>';
	}
	$nl = $formdata['reportformat']=='htm' ? '<br />' : "\n";
	return get_string('grade')."$nl($grademethod)";
}
function hotpot_delete_selected_attempts(&$hotpot, $del) {

	$select = '';
	switch ($del) {
		case 'all' :
			$select = "hotpot='$hotpot->id'";
			break;
		case 'abandoned':
			$select = "hotpot='$hotpot->id' AND status=".HOTPOT_STATUS_ABANDONED;
			break;
		case 'selection':
			$ids = (array)data_submitted();
			unset($ids['del']);
			unset($ids['id']);
			if (!empty($ids)) {
				$select = "hotpot='$hotpot->id' AND clickreportid IN (".implode(',', $ids).")";
			}
			break;
	}

	// delete attempts using $select, if it is set
	if ($select) {

		$table = 'hotpot_attempts';
		if ($attempts = get_records_select($table, $select)) {

			hotpot_delete_and_notify($table, $select, get_string('attempts', 'quiz'));

			$select = 'attempt IN ('.implode(',', array_keys($attempts)).')';
			hotpot_delete_and_notify('hotpot_details', $select, get_string('rawdetails', 'hotpot'));
			hotpot_delete_and_notify('hotpot_responses', $select, get_string('answer', 'quiz'));
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
		if ($mode=='overview' || $mode=='simplestat' || $mode=='fullstat') {
			$module = "quiz";
		} else {
			$module = "hotpot";
		}
		$navigation .= get_string("report$mode", $module);

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
function hotpot_print_report_selector(&$course, &$hotpot, &$formdata) {

	global $CFG;

	$reports = hotpot_get_report_names('overview,simplestat,fullstat');

	print '<form method="post" action="'."$CFG->wwwroot/mod/hotpot/report.php?hp=$hotpot->id".'">';
	print '<table cellpadding="2" align="center">';

	$menus = array();

	$menus['mode'] = array();
	foreach ($reports as $name) {
		if ($name=='overview' || $name=='simplestat' || $name=='fullstat') {
			$module = "quiz";   // standard reports
		} else if ($name=='click' && empty($hotpot->clickreporting)) {
			$module =  "";      // clickreporting is disabled
		} else {
			$module = "hotpot"; // custom reports
		}
		if ($module) {
			$menus['mode'][$name] = get_string("report$name", $module);
		}
	}
	if (isadmin()) {
		$menus['reportcourse'] = array(
			'this' => get_string('thiscourse', 'hotpot'), // $course->shortname,
			'all' => get_string('allmycourses', 'hotpot')
		);
	}
	$menus['reportusers'] = array(
		'all' => get_string('allparticipants'),
		'students' => get_string('students')
	);
	$users = get_records_sql("
		SELECT 
			u.*
		FROM 
			{$CFG->prefix}user AS u,
			{$CFG->prefix}user_students AS us
		WHERE
			u.id = us.userid AND us.course=$course->id
		ORDER BY
			u.lastname
	");
	if ($users) {
		$menus['reportusers'][''] = '------'; // separator
		foreach ($users as $id=>$user) {
				$menus['reportusers']["$id"] = fullname($user);
		}
	}
	$menus['reportattempts'] = array(
		'all' => get_string('attemptsall', 'hotpot'),
		'best' => get_string('attemptsbest', 'hotpot'),
		'first' => get_string('attemptsfirst', 'hotpot'),
		'last' => get_string('attemptslast', 'hotpot')
	);

	print '<tr><td>';
	helpbutton('reportcontent', get_string('reportcontent', 'hotpot'), 'hotpot');
	print '</td><th align="right">'.get_string('reportcontent', 'hotpot').':</th><td colspan="7">';
	foreach ($menus as $name => $options) {
		$value = $formdata[$name];
		print choose_from_menu($options, $name, $value, "", "", 0, true);
	};
	print '<input type="submit" value="'.get_string('reportbutton', 'hotpot').'"></td></tr>';

	$menus = array();
	$menus['reportformat'] = array(
		'htm' => get_string('reportformathtml', 'hotpot'),
		'xls' => get_string('reportformatexcel', 'hotpot'),
		'txt' => get_string('reportformattext', 'hotpot'),
	);
	if (trim($CFG->hotpot_excelencodings)) {
		$menus['reportencoding'] = array(get_string('none')=>'');

		$encodings = explode(',', $CFG->hotpot_excelencodings);
		foreach ($encodings as $encoding) {

			$encoding = trim($encoding);
			if ($encoding) {
				$menus['reportencoding'][$encoding] = $encoding;
			}
		}
	}
	$menus['reportwrapdata'] = array(
		'1' => get_string('yes'),
		'0'  => get_string('no'),
	);
	$menus['reportshowlegend'] = array(
		'1' => get_string('yes'),
		'0'  => get_string('no'),
	);

	print '<tr><td>';
	helpbutton('reportformat', get_string('reportformat', 'hotpot'), 'hotpot');
	print '</td>';
	foreach ($menus as $name => $options) {
		$value = $formdata[$name];
		print '<th align="right">'.get_string($name, 'hotpot').':</th><td>'.choose_from_menu($options, $name, $value, "", "", 0, true).'</td>';
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

	$plugins = get_list_of_plugins('mod/hotpot/report');
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
function hotpot_get_report_users($course_ids, $formdata) {
	$users = array();

	/// Check to see if groups are being used in this module
	$currentgroup = false;
	if ($groupmode = groupmode($course, $cm)) {   // Groups are being used
		$currentgroup = setup_and_print_groups($course, $groupmode, "report.php?id=$cm->id&mode=simple");
	}

	$sort = "u.lastname ASC";

	switch ($formdata['reportusers']) {
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
function hotpot_get_records_groupby($function, $fieldnames, $table, $select, $groupby) {
	// $function is an SQL aggregate function (MAX or MIN)

	global $CFG;

	switch (strtolower($CFG->dbtype)) {
		case 'mysql':
			$fields = "$groupby, $function(CONCAT(".join(",'_',", $fieldnames).")) AS joinedvalues";
			break;
		case 'postgres7':
			$fields = "$groupby, $function(".join("||'_'||", $fieldnames).") AS joinedvalues";
			break;
		default:
			$fields = "";
			break;
	}

	if ($fields) {
		$records = get_records_sql("SELECT $fields FROM $table WHERE $select GROUP BY $groupby");
	}

	if (empty($fields) || empty($records)) {
		$records = array();
	}

	$fieldcount = count($fieldnames);

	foreach ($records as $id=>$record) {
		if (empty($record->joinedvalues)) {
			unset($records[$id]);
		} else {
			$formdata = explode('_', $record->joinedvalues);

			for ($i=0; $i<$fieldcount; $i++) {
				$fieldname = $fieldnames[$i];
				$records[$id]->$fieldname = $formdata[$i];
			}
		}
		unset($record->joinedvalues);
	}

	return $records;
}
?>
