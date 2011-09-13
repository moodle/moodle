<?php

require_once('../config.php');
//require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->dirroot.'/calendar/lib.php');
require_once($CFG->libdir.'/bennu/bennu.inc.php');

$username = required_param('username', PARAM_TEXT);
$authtoken = required_param('authtoken', PARAM_ALPHANUM);

if (empty($CFG->enablecalendarexport)) {
    die('no export');
}

//Fetch user information
if (!$user = $DB->get_record('user', array('username' => $username), 'id,password')) {
   //No such user
    die('Invalid authentication');
}

//Check authentication token
if ($authtoken != sha1($username . $user->password . $CFG->calendar_exportsalt)) {
    die('Invalid authentication');
}

$what = optional_param('preset_what', 'all', PARAM_ALPHA);
$time = optional_param('preset_time', 'weeknow', PARAM_ALPHA);

$now = usergetdate(time());
// Let's see if we have sufficient and correct data
$allowed_what = array('all', 'courses');
$allowed_time = array('weeknow', 'weeknext', 'monthnow', 'monthnext', 'recentupcoming');

if(!empty($what) && !empty($time)) {
    if(in_array($what, $allowed_what) && in_array($time, $allowed_time)) {
        $courses = enrol_get_users_courses($user->id, true, 'id, visible, shortname');

        if ($what == 'all') {
            $users = $user->id;
            $groups = array();
            foreach ($courses as $course) {
                $course_groups = groups_get_all_groups($course->id, $user->id);
                $groups = array_merge($groups, array_keys($course_groups));
            }
            if (empty($groups)) {
                $groups = false;
            }
            $courses[SITEID] = new stdClass;
            $courses[SITEID]->shortname = get_string('globalevents', 'calendar');
        } else {
            $users = false;
            $groups = false;
        }

        switch($time) {
            case 'weeknow':
                $startweekday  = get_user_preferences('calendar_startwday', calendar_get_starting_weekday());
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
                $startweekday  = get_user_preferences('calendar_startwday', calendar_get_starting_weekday());
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
            case 'recentupcoming':
                //Events in the last 5 or next 60 days
                $timestart = time() - 432000;
                $timeend = time() + 5184000;
            break;
        }
    }
    else {
        // Parameters given but incorrect, redirect back to export page
        redirect($CFG->wwwroot.'/calendar/export.php');
        die();
    }
}
$events = calendar_get_events($timestart, $timeend, $users, $groups, array_keys($courses), false);

$ical = new iCalendar;
$ical->add_property('method', 'PUBLISH');
foreach($events as $event) {
   if (!empty($event->modulename)) {
        $cm = get_coursemodule_from_instance($event->modulename, $event->instance);
        if (!groups_course_module_visible($cm)) {
            continue;
        }
    }
    $hostaddress = str_replace('http://', '', $CFG->wwwroot);
    $hostaddress = str_replace('https://', '', $hostaddress);

    $ev = new iCalendar_event;
    $ev->add_property('uid', $event->id.'@'.$hostaddress);
    $ev->add_property('summary', $event->name);
    $ev->add_property('description', $event->description);
    $ev->add_property('class', 'PUBLIC'); // PUBLIC / PRIVATE / CONFIDENTIAL
    $ev->add_property('last-modified', Bennu::timestamp_to_datetime($event->timemodified));
    $ev->add_property('dtstamp', Bennu::timestamp_to_datetime()); // now
    $ev->add_property('dtstart', Bennu::timestamp_to_datetime($event->timestart)); // when event starts
    if ($event->timeduration > 0) {
        //dtend is better than duration, because it works in Microsoft Outlook and works better in Korganizer
        $ev->add_property('dtend', Bennu::timestamp_to_datetime($event->timestart + $event->timeduration));
    }
    if ($event->courseid != 0) {
        $coursecontext = get_context_instance(CONTEXT_COURSE, $event->courseid);
        $ev->add_property('categories', format_string($courses[$event->courseid]->shortname, true, array('context' => $coursecontext)));
    }
    $ical->add_component($ev);
}

$serialized = $ical->serialize();
if(empty($serialized)) {
    // TODO
    die('bad serialization');
}

//IE compatibility HACK!
if (ini_get_bool('zlib.output_compression')) {
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
header('Content-type: text/calendar; charset=utf-8');

echo $serialized;
