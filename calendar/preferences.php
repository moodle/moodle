<?PHP  // $Id$
       // preferences.php - user prefs for calendar

    require("../config.php");
    require_once('lib.php');


    if (isset($SESSION->cal_course_referer)) {
        if (! $course = get_record("course", "id", $SESSION->cal_course_referer)) {
            $course = get_site();
        }
    }

    if ($course->category) {
        require_login($course->id);
    }

/// If data submitted, then process and store.

	if ($form = data_submitted()) {  
        print_header();
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
                    if ((int)$value >= 1) {
                        set_user_preference('calendar_maxevents', $value);
                    }
                break;
                case 'lookahead':
                    if ((int)$value >= 1) {
                        set_user_preference('calendar_lookahead', $value);
                    }
                break;
            }
        }
        redirect("view.php", get_string("changessaved"), 1);
        exit;
    }

    $site = get_site();

    $strcalendar = get_string('calendar', 'calendar');
    $strpreferences = get_string('preferences', 'calendar');

    if ($course->category) {
        $navigation = "<a href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</a> ->
                       <a href=\"view.php\">$strcalendar</a> -> $strpreferences";
    } else {
        $navigation = "<a href=\"view.php\">$strcalendar</a> -> $strpreferences";
    }


    print_header("$site->shortname: $strcalendar: $strpreferences", $strcalendar, $navigation,
                 '', '', true, '', '<p class="logininfo">'.user_login_string($site).'</p>');


    print_heading($strpreferences);

    print_simple_box_start("center", "", "$THEME->cellheading");

    $prefs->timeformat = get_user_preferences('calendar_timeformat', '');
    $prefs->startwday = get_user_preferences('calendar_startwday', CALENDAR_STARTING_WEEKDAY);
    $prefs->maxevents = get_user_preferences('calendar_maxevents', CALENDAR_UPCOMING_MAXEVENTS);
    $prefs->lookahead = get_user_preferences('calendar_lookahead', CALENDAR_UPCOMING_DAYS);

	include("preferences.html");
    print_simple_box_end();

    print_footer($course->id);

?>
