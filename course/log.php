<?php // $Id$
      // Displays different views of the logs.

    require_once('../config.php');
    require_once('lib.php');

    $id = required_param('id',PARAM_INT);// Course ID
    $group = optional_param('group', -1, PARAM_INT); // Group to display
    $user = optional_param('user', 0, PARAM_INT); // User to display
    $date = optional_param('date', 0, PARAM_CLEAN); // Date to display
    $modname = optional_param('modname', ''); // course_module->id
    $modid = optional_param('modid', ''); // course_module->id
    $modaction = optional_param('modaction', ''); // an action as recorded in the logs
    $page = optional_param('page', '0', PARAM_INT);     // which page to show
    $perpage = optional_param('perpage', '100', PARAM_INT); // how many per page 
    $showcourses = optional_param('showcourses',0,PARAM_INT); // whether to show courses if we're over our limit.
    $showusers = optional_param('showusers',0,PARAM_INT); // whether to show users if we're over our limit.
    $chooselog = optional_param('chooselog',0,PARAM_INT);

    require_login();

    if (! $course = get_record('course', 'id', $id) ) {
        error('That\'s an invalid course id');
    }

    if (! isteacher($course->id)) {
        error('Only teachers can view logs');
    }

    if (! $course->category) {
        if (!isadmin()) {
            error('Only administrators can look at the site logs');
        }
    }

    $strlogs = get_string('logs');
    $stradministration = get_string('administration');

    session_write_close();

    if (!empty($chooselog)) {
        $userinfo = get_string('allparticipants');
        $dateinfo = get_string('alldays');

        if ($user) {
            if (!$u = get_record('user', 'id', $user) ) {
                error('That\'s an invalid user!');
            }
            $userinfo = fullname($u, isteacher($course->id));
        }
        if ($date) {
            $dateinfo = userdate($date, get_string('strftimedaydate'));
        }

        if ($course->category) {
            print_header($course->shortname .': '. $strlogs, $course->fullname, 
                         "<a href=\"view.php?id=$course->id\">$course->shortname</a> ->
                          <a href=\"log.php?id=$course->id\">$strlogs</a> -> $userinfo, $dateinfo", '');
        } else {
            print_header($course->shortname .': '. $strlogs, $course->fullname, 
                         "<a href=\"../$CFG->admin/index.php\">$stradministration</a> ->
                          <a href=\"log.php?id=$course->id\">$strlogs</a> -> $userinfo, $dateinfo", '');
        }
        
        print_heading("$course->fullname: $userinfo, $dateinfo (".usertimezone().")");

        print_log_selector_form($course, $user, $date, $modname, $modid, $modaction, $group, $showcourses, $showusers);
        
        echo '<p align="center">[ <a href="'.$CFG->wwwroot.'/course/stats.php?course='.$course->id.'">'.get_string('seealsostats').'</a> ]</p>';

        print_log($course, $user, $date, 'l.time DESC', $page, $perpage, 
                  "log.php?id=$course->id&amp;chooselog=1&amp;user=$user&amp;date=$date&amp;modid=$modid&amp;modaction=$modaction&amp;group=$group", 
                  $modname, $modid, $modaction, $group);

    } else {
        if ($course->category) {
            print_header($course->shortname .': '. $strlogs, $course->fullname, 
                     "<a href=\"view.php?id=$course->id\">$course->shortname</a> -> $strlogs", '');
        } else {
            print_header($course->shortname .': '. $strlogs, $course->fullname, 
                     "<a href=\"../$CFG->admin/index.php\">$stradministration</a> -> $strlogs", '');
        }

        print_heading(get_string('chooselogs') .':');

        print_log_selector_form($course, $user, $date, $modname, $modid, $modaction, $group, $showcourses, $showusers);

        echo '<br />';
        print_heading(get_string('chooselivelogs') .':');

        echo '<center><h3>';
        link_to_popup_window('/course/loglive.php?id='. $course->id,'livelog', get_string('livelogs'), 500, 800);
        echo '</h3></center>';

        $activityreport = get_string( 'activityreport' );
        echo "<center><a href=\"{$CFG->wwwroot}/course/outline_rep.php?id={$course->id}\">";
        echo "$activityreport</a></center>\n";
    }

    print_footer($course);

    exit;

?>
