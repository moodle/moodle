<?PHP  // $Id$

// This script uses installed report plugins to print quiz reports

    require_once("../../config.php");
    require_once("lib.php");

    $id = optional_param('id', 0, PARAM_INT); // Course Module ID, or
    $hp = optional_param('hp', 0, PARAM_INT); // hotpot ID

    if ($id) {
        if (! $cm = get_coursemodule_from_id('hotpot', $id)) {
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

    // get the roles context for this course
    $sitecontext = get_context_instance(CONTEXT_SYSTEM);
    $modulecontext = get_context_instance(CONTEXT_MODULE, $cm->id);

    // set homeurl of couse (for error messages)
    $course_homeurl = "$CFG->wwwroot/course/view.php?id=$course->id";

    require_login($course);

    // check user can access this hotpot activity
    if (!hotpot_is_visible($cm)) {
        print_error("activityiscurrentlyhidden");
    }

    // get report mode
    if (has_capability('mod/hotpot:viewreport',$modulecontext)) {
        $mode = optional_param('mode', 'overview', PARAM_ALPHA);
    } else {
        // ordinary students have no choice
        $mode = 'overview';
    }

    // assemble array of form data
    $formdata = array(
        'mode' => $mode,
        'reportusers'      => has_capability('mod/hotpot:viewreport',$modulecontext) ? optional_param('reportusers', get_user_preferences('hotpot_reportusers', 'allusers'), PARAM_ALPHANUM) : 'this',
        'reportattempts'   => optional_param('reportattempts', get_user_preferences('hotpot_reportattempts', 'all'), PARAM_ALPHA),
        'reportformat'     => optional_param('reportformat', 'htm', PARAM_ALPHA),
        'reportshowlegend' => optional_param('reportshowlegend', get_user_preferences('hotpot_reportshowlegend', '0'), PARAM_INT),
        'reportencoding'   => optional_param('reportencoding', get_user_preferences('hotpot_reportencoding', ''), PARAM_ALPHANUM),
        'reportwrapdata'   => optional_param('reportwrapdata', get_user_preferences('hotpot_reportwrapdata', '1'), PARAM_INT),
    );

    foreach ($formdata as $name=>$value) {
        set_user_preference("hotpot_$name", $value);
    }

/// Start the report

    add_to_log($course->id, "hotpot", "report", "report.php?id=$cm->id&mode=$mode", "$hotpot->id", "$cm->id");

    // print page header. if required
    if ($formdata['reportformat']=='htm') {
        hotpot_print_report_heading($course, $cm, $hotpot, $mode);
        if (has_capability('mod/hotpot:viewreport',$modulecontext)) {
            hotpot_print_report_selector($course, $hotpot, $formdata);
        }
    }

    // delete selected attempts, if any
    if (has_capability('mod/hotpot:deleteattempt',$modulecontext)) {
        $del = optional_param('del', '', PARAM_ALPHA);
        hotpot_delete_selected_attempts($hotpot, $del);
    }

    // check for groups
    if (preg_match('/^group(\d*)$/', $formdata['reportusers'], $matches)) {
        $formdata['reportusers'] = 'group';
        $formdata['reportgroupid'] = 0;
        // validate groupid
        if ($groups = groups_get_all_groups($course->id)) {
            if (isset($groups[$matches[1]])) {
                $formdata['reportgroupid'] = $matches[1];
            }
        }
    }

    $user_ids = '';
    $users = array();

    switch ($formdata['reportusers']) {

        case 'allusers':
            // anyone who has ever attempted this hotpot
            if ($records = get_records_select('hotpot_attempts', "hotpot=$hotpot->id", '', 'id,userid')) {
                foreach ($records as $record) {
                    $users[$record->userid] = 0; // "0" means user is NOT currently allowed to attempt this HotPot
                }
                unset($records);
            }
            break;

        case 'group':
            // group members
            if ($members = groups_get_members($formdata['reportgroupid'])) {
                foreach ($members as $memberid=>$unused) {
                    $users[$memberid] = 1; // "1" signifies currently recognized participant
                }
            }
            break;

        case 'allparticipants':
            // anyone currently allowed to attempt this HotPot
            if ($records = hotpot_get_users_by_capability($modulecontext, 'mod/hotpot:attempt')) {
                foreach ($records as $record) {
                    $users[$record->id] = 1; // "1" means user is allowed to do this HotPot
                }
                unset($records);
            }
            break;

        case 'existingstudents':
            // anyone currently allowed to attempt this HotPot who is not a teacher
            $teachers = hotpot_get_users_by_capability($modulecontext, 'mod/hotpot:viewreport');
            if ($records = hotpot_get_users_by_capability($modulecontext, 'mod/hotpot:attempt')) {
                foreach ($records as $record) {
                    if (empty($teachers[$record->id])) {
                        $users[$record->id] = 1;
                    }
                }
                unset($records);
            }
            break;

        case 'this': // current user only
            $user_ids = $USER->id;
            break;

        default: // specific user selected by teacher
            if (is_numeric($formdata['reportusers'])) {
                $user_ids = $formdata['reportusers'];
            }
    }
    if (empty($user_ids) && count($users)) {
        ksort($users);
        $user_ids = join(',', array_keys($users));
    }
    if (empty($user_ids)) {
        print_heading(get_string('nousersyet'));
        print_footer($course);
        exit;
    }

    // database table and selection conditions
    $table = "{$CFG->prefix}hotpot_attempts a";
    $select = "a.hotpot=$hotpot->id AND a.userid IN ($user_ids)";
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

        $select = '';
        if ($records) {
            $ids = array();
            foreach ($records as $record) {
                $ids[] = $record->clickreportid;
            }
            if (count($ids)) {
                $select = "a.clickreportid IN (".join(',', $ids).")";
            }
        }
    }

    // pick out last attempt in each clickreport series
    if ($select) {
        $cr_attempts = hotpot_get_records_groupby('MAX', array('timefinish', 'id'), $table, $select, 'clickreportid');
    } else {
        $cr_attempts = array();
    }

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
        $table .= ", {$CFG->prefix}user u";
        $order = "u.lastname, a.attempt, a.timefinish";
        // get the attempts (at last!)
        $attempts = get_records_sql("SELECT $fields FROM $table WHERE $select ORDER BY $order");
    }

    // stop now if no attempts were found
    if (empty($attempts)) {
        print_heading(get_string('noattemptstoshow','quiz'));
        print_footer($course);
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

        if ($mode=='click') {
            // shortcut to clickreportid (=the id of the FIRST attempt in this clickreport series)
            $clickreportid = $attempt->clickreportid;
            if (isset($cr_attempts[$clickreportid])) {
                // store id and finish time of LAST attempt in this clickreport series
                $attempts[$id]->cr_lastclick = $cr_attempts[$clickreportid]->id;
                $attempts[$id]->cr_timefinish = $cr_attempts[$clickreportid]->timefinish;
            }
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
            $ids = array();
            $data = (array)data_submitted();
            foreach ($data as $name => $value) {
                if (preg_match('/^box\d+$/', $name)) {
                    $ids[] = intval($value);
                }
            }
            if (count($ids)) {
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

            // update grades for all users for this hotpot
            hotpot_update_grades($hotpot);
        }
    }

}

//////////////////////////////////////////////
/// functions to print the report headings and
/// report selector menus

function hotpot_print_report_heading(&$course, &$cm, &$hotpot, &$mode) {
    global $CFG;
    $strmodulenameplural = get_string("modulenameplural", "hotpot");
    $strmodulename  = get_string("modulename", "hotpot");

    $title = format_string($course->shortname) . ": $hotpot->name";
    $heading = $course->fullname;

    $modulecontext = get_context_instance(CONTEXT_MODULE, $cm->id);
    if (has_capability('mod/hotpot:viewreport',$modulecontext)) {
        if ($mode=='overview' || $mode=='simplestat' || $mode=='fullstat') {
            $module = "quiz";
        } else {
            $module = "hotpot";
        }

        $navigation = build_navigation(get_string("report$mode", $module), $cm);
    } else {
        $navigation = build_navigation(get_string("report", "quiz"), $cm);
    }

    $button = update_module_button($cm->id, $course->id, $strmodulename);
    print_header($title, $heading, $navigation, "", "", true, $button, navmenu($course, $cm));
    $course_context = get_context_instance(CONTEXT_COURSE, $course->id);
    if (has_capability('gradereport/grader:view', $course_context) && has_capability('moodle/grade:viewall', $course_context)) {
        echo '<div class="allcoursegrades"><a href="' . $CFG->wwwroot . '/grade/report/grader/index.php?id=' . $course->id . '">'
            . get_string('seeallcoursegrades', 'grades') . '</a></div>';
    }
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

    $menus['reportusers'] = array(
        'allusers' => get_string('allusers', 'hotpot'),
        'allparticipants' => get_string('allparticipants')
    );

    // groups
    if ($groups = groups_get_all_groups($course->id)) {
        foreach ($groups as $gid => $group) {
            $menus['reportusers']["group$gid"] = get_string('group').': '.format_string($group->name);
        }
    }

    // get users who have ever atetmpted this HotPot
    $users = get_records_sql("
        SELECT
            u.id, u.firstname, u.lastname
        FROM
            {$CFG->prefix}user u,
            {$CFG->prefix}hotpot_attempts ha
        WHERE
            u.id = ha.userid AND ha.hotpot=$hotpot->id
        ORDER BY
            u.lastname
    ");

    if (!empty($users)) {
        // get context
        $cm = get_coursemodule_from_instance('hotpot', $hotpot->id);
        $modulecontext = get_context_instance(CONTEXT_MODULE, $cm->id);

        $teachers = hotpot_get_users_by_capability($modulecontext, 'mod/hotpot:viewreport');
        $students = hotpot_get_users_by_capability($modulecontext, 'mod/hotpot:attempt');

        // current students
        if (!empty($students)) {
            $firsttime = true;
            foreach ($users as $user) {
                if (array_key_exists($user->id, $teachers)) {
                    continue; // skip teachers
                }
                if (array_key_exists($user->id, $students)) {
                    if ($firsttime) {
                        $firsttime = false; // so we only do this once
                        $menus['reportusers']['existingstudents'] = get_string('existingstudents');
                        $menus['reportusers'][] = '------';
                    }
                    $menus['reportusers']["$user->id"] = fullname($user);
                    unset($users[$user->id]);
                }
            }
            unset($students);
        }
        // others (former students, teachers, admins, course creators)
        $firsttime = true;
        foreach ($users as $user) {
            if ($firsttime) {
                $firsttime = false; // so we only do this once
                $menus['reportusers'][] = '======';
            }
            $menus['reportusers']["$user->id"] = fullname($user);
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
    print '</td><th align="right" scope="col">'.get_string('reportcontent', 'hotpot').':</th><td colspan="7">';
    foreach ($menus as $name => $options) {
        $value = $formdata[$name];
        print choose_from_menu($options, $name, $value, "", "", 0, true);
    };
    print '<input type="submit" value="'.get_string('reportbutton', 'hotpot').'" /></td></tr>';

    $menus = array();

    $menus['reportformat'] = array();
    $menus['reportformat']['htm'] = get_string('reportformathtml', 'hotpot');
    if (file_exists("$CFG->libdir/excel") || file_exists("$CFG->libdir/excellib.class.php")) {
        $menus['reportformat']['xls'] = get_string('reportformatexcel', 'hotpot');
    }
    $menus['reportformat']['txt'] = get_string('reportformattext', 'hotpot');

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
        print '<th align="right" scope="col">'.get_string($name, 'hotpot').':</th><td>'.choose_from_menu($options, $name, $value, "", "", 0, true).'</td>';
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
function hotpot_get_report_users($course, $formdata) {
    $users = array();

    /// Check to see if groups are being used in this module
    $groupmode = groupmode($course, $cm); //TODO: there is no $cm defined!
    $currentgroup = setup_and_print_groups($course, $groupmode, "report.php?id=$cm->id&mode=simple");

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

    $fields = sql_concat_join("'_'", $fieldnames);
    $fields = "$groupby, $function($fields) AS joinedvalues";

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
            $values = explode('_', $record->joinedvalues);

            for ($i=0; $i<$fieldcount; $i++) {
                $fieldname = $fieldnames[$i];
                $records[$id]->$fieldname = $values[$i];
            }
        }
        unset($record->joinedvalues);
    }

    return $records;
}
function hotpot_get_users_by_capability(&$modulecontext, $capability) {
    static $users = array();
    if (! array_key_exists($capability, $users)) {
        $users[$capability] = get_users_by_capability($modulecontext, $capability, 'u.id,u.id', 'u.id');
    }
    return $users[$capability];
}
?>
