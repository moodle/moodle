<?PHP  // $Id$
       // preferences.php - user prefs for calendar

    require_once('../config.php');
    require_once($CFG->dirroot.'/calendar/lib.php');

    if (isset($SESSION->cal_course_referer)) {
        if (! $course = get_record('course', 'id', $SESSION->cal_course_referer)) {
            $course = get_site();
        }
    }

    if ($course->id != SITEID) {
        require_login($course->id);
    }
    // Initialize the session variables
    calendar_session_vars();

/// If data submitted, then process and store.

    if ($form = data_submitted() and confirm_sesskey()) {
        foreach ($form as $preference => $value) {
            switch ($preference) {
                case 'timeformat':
                    if ($value != CALENDAR_TF_12 and $value != CALENDAR_TF_24) {
                        $value = '';
                    }
                    set_user_preference('calendar_timeformat', $value);
                break;
                case 'startwday':
                    $value = intval($value);
                    if ($value < 0 or $value > 6) {
                        $value = abs($value % 7);
                    }
                    set_user_preference('calendar_startwday', $value);
                break;
                case 'maxevents':
                    if (intval($value) >= 1) {
                        set_user_preference('calendar_maxevents', $value);
                    }
                break;
                case 'lookahead':
                    if (intval($value) >= 1) {
                        set_user_preference('calendar_lookahead', $value);
                    }
                break;
                case 'persistflt':
                    set_user_preference('calendar_persistflt', intval($value));
                break;
            }
        }
        redirect('view.php?course='.$course->id, get_string('changessaved'), 1);
        exit;
    }

    $site = get_site();

    $strcalendar = get_string('calendar', 'calendar');
    $strpreferences = get_string('preferences', 'calendar');

    $navlinks = array();
    if ($course->id != SITEID) {
        $navlinks[] = array('name' => $course->shortname,
                            'link' => "$CFG->wwwroot/course/view.php?id=$course->id",
                            'type' => 'misc');
    }
    $navlinks[] = array('name' => $strpreferences, 'link' => 'view.php', 'type' => 'misc');
    $navigation = build_navigation($navlinks);

    print_header("$site->shortname: $strcalendar: $strpreferences", $strcalendar, $navigation,
                 '', '', true, '', user_login_string($site));


    print_heading($strpreferences);

    print_simple_box_start("center");

    $prefs->timeformat = get_user_preferences('calendar_timeformat', '');
    $prefs->startwday  = get_user_preferences('calendar_startwday', CALENDAR_STARTING_WEEKDAY);
    $prefs->maxevents  = get_user_preferences('calendar_maxevents', CALENDAR_UPCOMING_MAXEVENTS);
    $prefs->lookahead  = get_user_preferences('calendar_lookahead', CALENDAR_UPCOMING_DAYS);
    $prefs->persistflt = get_user_preferences('calendar_persistflt', 0);

    include('./preferences.html');
    print_simple_box_end();

    print_footer($course);

?>
