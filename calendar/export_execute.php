<?php // $Id$

require_once('../config.php');
//require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->dirroot.'/calendar/lib.php');
require_once($CFG->libdir.'/bennu/bennu.inc.php');

require_login();
if(isguest()) {
    redirect($CFG->wwwroot.'/calendar/view.php');
}

$action = optional_param('action', '', PARAM_ALPHA);
$course = optional_param('course', 0);
$day  = optional_param('cal_d', 0, PARAM_INT);
$mon  = optional_param('cal_m', 0, PARAM_INT);
$yr   = optional_param('cal_y', 0, PARAM_INT);

$what = optional_param('preset_what', 'all', PARAM_ALPHA);
$time = optional_param('preset_time', 'weeknow', PARAM_ALPHA);

$now = usergetdate(time());
// Let's see if we have sufficient and correct data
$allowed_what = array('all', 'courses');
$allowed_time = array('weeknow', 'weeknext', 'monthnow', 'monthnext');

if(!empty($what) && !empty($time)) {
    if(in_array($what, $allowed_what) && in_array($time, $allowed_time)) {
        $courses = array() + $USER->student + $USER->teacher;
        $courses = array_keys($courses);
        switch($time) {
            case 'weeknow':
                $startweekday  = get_user_preferences('calendar_startwday', CALENDAR_STARTING_WEEKDAY);
                $startmonthday = find_day_in_month($now['mday'] - 6, $startweekday, $now['mon'], $now['year']);
                $startmonth    = $now['mon'];
                $startyear     = $now['year'];
                if($startmonthday > calendar_days_in_month($startmonth, $startyear)) {
                    list($startmonth, $startyear) = calendar_add_month($startmonth, $startyear);
                    $startmonthday = find_day_in_month(1, $startweekday, $startmonth, $startyear);
                }
                $timestart = make_timestamp($startyear, $startmonth, $startmonthday);
                $endmonthday = $startmonthday + 7;
                $endmonth    = $startmonth;
                $endyear     = $startyear;
                if($endmonthday > calendar_days_in_month($endmonth, $endyear)) {
                    list($endmonth, $endyear) = calendar_add_month($endmonth, $endyear);
                    $endmonthday = find_day_in_month(1, $startweekday, $endmonth, $endyear);
                }
                $timeend = make_timestamp($endyear, $endmonth, $endmonthday) - 1;
            break;
            case 'weeknext':
                $startweekday  = get_user_preferences('calendar_startwday', CALENDAR_STARTING_WEEKDAY);
                $startmonthday = find_day_in_month($now['mday'] + 1, $startweekday, $now['mon'], $now['year']);
                $startmonth    = $now['mon'];
                $startyear     = $now['year'];
                if($startmonthday > calendar_days_in_month($startmonth, $startyear)) {
                    list($startmonth, $startyear) = calendar_add_month($startmonth, $startyear);
                    $startmonthday = find_day_in_month(1, $startweekday, $startmonth, $startyear);
                }
                $timestart = make_timestamp($startyear, $startmonth, $startmonthday);
                $endmonthday = $startmonthday + 7;
                $endmonth    = $startmonth;
                $endyear     = $startyear;
                if($endmonthday > calendar_days_in_month($endmonth, $endyear)) {
                    list($endmonth, $endyear) = calendar_add_month($endmonth, $endyear);
                    $endmonthday = find_day_in_month(1, $startweekday, $endmonth, $endyear);
                }
                $timeend = make_timestamp($endyear, $endmonth, $endmonthday) - 1;
            break;
            case 'monthnow':
                $timestart = make_timestamp($now['year'], $now['mon'], 1);
                $timeend   = make_timestamp($now['year'], $now['mon'], calendar_days_in_month($now['mon'], $now['year']), 23, 59, 59);
            break;
            case 'monthnext':
                list($nextmonth, $nextyear) = calendar_add_month($now['mon'], $now['year']);
                $timestart = make_timestamp($nextyear, $nextmonth, 1);
                $timeend   = make_timestamp($nextyear, $nextmonth, calendar_days_in_month($nextmonth, $nextyear), 23, 59, 59);
            break;
        }

        /*        
        print_object($now);
        print_object('start: '. $timestart);
        print_object('end: '. $timeend);
        */
    }
    else {
        // Parameters given but incorrect, redirect back to export page
        redirect($CFG->wwwroot.'/calendar/export.php');
        echo "aa";
        die();
    }
}

$whereclause = calendar_sql_where($timestart, $timeend, false, false, $courses, false);
if($whereclause === false) {
    $events = array();
}
else {
    $events = get_records_select('event', $whereclause, 'timestart');
}

if(empty($events)) {
    // TODO
    die('no events');
}

$ical = new iCalendar;
$ical->add_property('method', 'PUBLISH');
foreach($events as $event) {
    $ev = new iCalendar_event;
    $ev->add_property('summary', $event->name);
    $ev->add_property('description', $event->description);
    $ev->add_property('class', 'public'); // PUBLIC / PRIVATE / CONFIDENTIAL
    $ev->add_property('last-modified', 0); // lastmodified
    $ev->add_property('dtstamp', Bennu::timestamp_to_datetime()); // now
    $ev->add_property('dtstart', Bennu::timestamp_to_datetime($event->timestart)); // when event starts
    $ev->add_property('duration', 0); // when event starts
    $ical->add_component($ev);
}

$serialized = $ical->serialize();
if(empty($serialized)) {
    // TODO
    die('bad serialization');
}

//IE compatibiltiy HACK!
if(ini_get('zlib.output_compression')) {
    ini_set('zlib.output_compression', 'Off');
}

$filename = 'icalexport.ics';

header('Last-Modified: '. gmdate('D, d M Y H:i:s', time()) .' GMT');
header('Cache-Control: private, must-revalidate, pre-check=0, post-check=0, max-age=0');
header('Expires: '. gmdate('D, d M Y H:i:s', 0) .'GMT');
header('Pragma: no-cache');
header('Accept-Ranges: none'); // Comment out if PDFs do not work...
header('Content-disposition: attachment; filename='.$filename);
header('Content-length: '.strlen($serialized));
header('Content-type: text/plain');

echo $serialized;

?>
