<?php // $Id$

require_once('../config.php');
require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->dirroot.'/calendar/lib.php');
//require_once($CFG->libdir.'/bennu/bennu.inc.php');

$action = optional_param('action', '', PARAM_ALPHA);
$course = optional_param('course', 0);
$day  = optional_param('cal_d', 0, PARAM_INT);
$mon  = optional_param('cal_m', 0, PARAM_INT);
$yr   = optional_param('cal_y', 0, PARAM_INT);

require_login();

if(!$site = get_site()) {
    redirect($CFG->wwwroot.'/'.$CFG->admin.'/index.php');
}

$pagetitle = get_string('export', 'calendar');
$now = usergetdate(time());
$nav = calendar_get_link_tag(get_string('calendar', 'calendar'), CALENDAR_URL.'view.php?view=upcoming&amp;', $now['mday'], $now['mon'], $now['year']).' -> '.$pagetitle;

if(!checkdate($mon, $day, $yr)) {
    $day = intval($now['mday']);
    $mon = intval($now['mon']);
    $yr = intval($now['year']);
}
$time = make_timestamp($yr, $mon, $day);

$SESSION->cal_courses_shown = calendar_get_default_courses(true);
calendar_set_referring_course(0);

if (empty($USER->id) or isguest()) {
    $defaultcourses = calendar_get_default_courses();
    calendar_set_filters($courses, $groups, $users, $defaultcourses, $defaultcourses);

} else {
    calendar_set_filters($courses, $groups, $users);
}

$strcalendar = get_string('calendar', 'calendar');
$prefsbutton = calendar_preferences_button();

// Print title and header
print_header("$site->shortname: $strcalendar: $pagetitle", $strcalendar, $nav,
             '', '', true, $prefsbutton, user_login_string($site));

echo calendar_overlib_html();

// Layout the whole page as three big columns.
echo '<table id="calendar">';
echo '<tr>';

// START: Main column

echo '<td class="maincalendar">';

$username = $USER->username;
$usernameencoded = urlencode($USER->username);
$authtoken = sha1($USER->username . $USER->password);

switch($action) {
    case 'advanced':
    break;
    case '':
    default:
        // Let's populate some vars to let "common tasks" be somewhat smart...
        // If today it's weekend, give the "next week" option
        $allownextweek  = CALENDAR_WEEKEND & (1 << $now['wday']);
        // If it's the last week of the month, give the "next month" option
        $allownextmonth = calendar_days_in_month($now['mon'], $now['year']) - $now['mday'] < 7;
        // If today it's weekend but tomorrow it isn't, do NOT give the "this week" option
        $allowthisweek  = !((CALENDAR_WEEKEND & (1 << $now['wday'])) && !(CALENDAR_WEEKEND & (1 << (($now['wday'] + 1) % 7))));
        echo '<div class="header">' . get_string('export', 'calendar') . '</div>';
        include('export_basic.html');
}



echo '</td>';

// END: Main column

// START: Last column (3-month display)
echo '<td class="sidecalendar">';
echo '<div class="header">'.get_string('monthlyview', 'calendar').'</div>';

list($prevmon, $prevyr) = calendar_sub_month($mon, $yr);
list($nextmon, $nextyr) = calendar_add_month($mon, $yr);
$getvars = 'cal_d='.$day.'&amp;cal_m='.$mon.'&amp;cal_y='.$yr; // For filtering

echo '<div class="minicalendarblock">';
echo calendar_top_controls('display', array('m' => $prevmon, 'y' => $prevyr));
echo calendar_get_mini($courses, $groups, $users, $prevmon, $prevyr);
echo '</div><div class="minicalendarblock">';
echo calendar_top_controls('display', array('m' => $mon, 'y' => $yr));
echo calendar_get_mini($courses, $groups, $users, $mon, $yr);
echo '</div><div class="minicalendarblock">';
echo calendar_top_controls('display', array('m' => $nextmon, 'y' => $nextyr));
echo calendar_get_mini($courses, $groups, $users, $nextmon, $nextyr);
echo '</div>';

echo '</td>';

echo '</tr></table>';

print_footer();



?>
