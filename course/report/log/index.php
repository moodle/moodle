<?php // $Id$
      // Displays different views of the logs.

    require_once('../../../config.php');
    require_once('../../lib.php');
    require_once('lib.php');
    require_once($CFG->libdir.'/adminlib.php');

    $id          = optional_param('id', 0, PARAM_INT);// Course ID
    
    $host_course = optional_param('host_course', '', PARAM_PATH);// Course ID
    
    if (empty($host_course)) {
        $hostid = $CFG->mnet_localhost_id;
        if (empty($id)) {
            $site = get_site();
            $id = $site->id;
        }
    } else {
        list($hostid, $id) = explode('/', $host_course);
    }
    
    $group       = optional_param('group', -1, PARAM_INT); // Group to display
    $user        = optional_param('user', 0, PARAM_INT); // User to display
    $date        = optional_param('date', 0, PARAM_FILE); // Date to display - number or some string
    $modname     = optional_param('modname', '', PARAM_CLEAN); // course_module->id
    $modid       = optional_param('modid', 0, PARAM_FILE); // number or 'site_errors'
    $modaction   = optional_param('modaction', '', PARAM_PATH); // an action as recorded in the logs
    $page        = optional_param('page', '0', PARAM_INT);     // which page to show
    $perpage     = optional_param('perpage', '100', PARAM_INT); // how many per page 
    $showcourses = optional_param('showcourses', 0, PARAM_INT); // whether to show courses if we're over our limit.
    $showusers   = optional_param('showusers', 0, PARAM_INT); // whether to show users if we're over our limit.
    $chooselog   = optional_param('chooselog', 0, PARAM_INT);
    $logformat   = optional_param('logformat', 'showashtml', PARAM_ALPHA);

    require_login();

    if ($hostid == $CFG->mnet_localhost_id) {
        if (!$course = get_record('course', 'id', $id) ) {
            error('That\'s an invalid course id'.$id);
        }
        $context = get_context_instance(CONTEXT_COURSE, $course->id);
        require_capability('moodle/site:viewreports', $context);
        add_to_log($course->id, "course", "report log", "report/log/index.php?id=$course->id", $course->id);
    } else {
        $course_stub       = array_pop(get_records_select('mnet_log', " hostid='$hostid' AND course='$id' ", '', '*', '', '1'));
        $course->id        = $id;
        $course->shortname = $course_stub->coursename;
        $course->fullname  = $course_stub->coursename;
        $context = get_context_instance(CONTEXT_SYSTEM, SITEID);
        require_capability('moodle/site:viewreports', $context);
        add_to_log(0, "mnet course", "report log", "report/log/index.php?chooselog=1&host_course=".urlencode($host_course), $course->fullname);
    }

    $strlogs = get_string('logs');
    $stradministration = get_string('administration');
    $strreports = get_string('reports');

    session_write_close();

    if (!empty($chooselog)) {
        $userinfo = get_string('allparticipants');
        $dateinfo = get_string('alldays');

        if ($user) {
            if (!$u = get_record('user', 'id', $user) ) {
                error('That\'s an invalid user!');
            }
            $userinfo = fullname($u, has_capability('moodle/site:viewfullnames', $context));
        }
        if ($date) {
            $dateinfo = userdate($date, get_string('strftimedaydate'));
        }

        switch ($logformat) {
            case 'showashtml':
                if ($hostid != $CFG->mnet_localhost_id || ($hostid == $CFG->mnet_localhost_id && $course->id == SITEID)) {
                    $adminroot = admin_get_root();
                    admin_externalpage_setup('reportlog', $adminroot);
                    admin_externalpage_print_header($adminroot);

                } else {
                    print_header($course->shortname .': '. $strlogs, $course->fullname, 
                                 "<a href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</a> ->
                                  <a href=\"$CFG->wwwroot/course/report.php?id=$course->id\">$strreports</a> ->
                                  <a href=\"index.php?id=$course->id\">$strlogs</a> -> $userinfo, $dateinfo", '');
                }

                print_heading(format_string($course->fullname) . ": $userinfo, $dateinfo (".usertimezone().")");
                print_mnet_log_selector_form($hostid, $course, $user, $date, $modname, $modid, $modaction, $group, $showcourses, $showusers, $logformat);
                
                if($hostid == $CFG->mnet_localhost_id) {
                    print_log($course, $user, $date, 'l.time DESC', $page, $perpage, 
                            "index.php?id=$course->id&amp;chooselog=1&amp;user=$user&amp;date=$date&amp;modid=$modid&amp;modaction=$modaction&amp;group=$group", 
                            $modname, $modid, $modaction, $group);
                } else {
                    print_mnet_log($hostid, $id, $user, $date, 'l.time DESC', $page, $perpage, "", $modname, $modid, $modaction, $group);
                }
                break;
            case 'downloadascsv':
                if (!print_log_csv($course, $user, $date, 'l.time DESC', $modname,
                        $modid, $modaction, $group)) {
                    notify("No logs found!");
                    print_footer($course);
                }
                exit;
            case 'downloadasods':
                if (!print_log_ods($course, $user, $date, 'l.time DESC', $modname,
                        $modid, $modaction, $group)) {
                    notify("No logs found!");
                    print_footer($course);
                }
                exit;
            case 'downloadasexcel':
                if (!print_log_xls($course, $user, $date, 'l.time DESC', $modname,
                        $modid, $modaction, $group)) {
                    notify("No logs found!");
                    print_footer($course);
                }
                exit;
        }


    } else {
        if ($hostid != $CFG->mnet_localhost_id || ($hostid == $CFG->mnet_localhost_id && $course->id == SITEID)) {
                    $adminroot = admin_get_root();
                    admin_externalpage_setup('reportlog', $adminroot);
                    admin_externalpage_print_header($adminroot);
        } else {
            print_header($course->shortname .': '. $strlogs, $course->fullname, 
                     "<a href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</a> -> 
                      <a href=\"$CFG->wwwroot/course/report.php?id=$course->id\">$strreports</a> ->
                      $strlogs", '');
        }

        print_heading(get_string('chooselogs') .':');

        print_log_selector_form($course, $user, $date, $modname, $modid, $modaction, $group, $showcourses, $showusers, $logformat);

        echo '<br />';
        print_heading(get_string('chooselivelogs') .':');

        echo '<center><h3>';
        link_to_popup_window('/course/report/log/live.php?id='. $course->id,'livelog', get_string('livelogs'), 500, 800);
        echo '</h3></center>';

    }

    if ($hostid != $CFG->mnet_localhost_id || $course->id == SITEID) {
        admin_externalpage_print_footer($adminroot);
    } else {
        print_footer($course);
    }

    exit;
?>
