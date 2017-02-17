<?php

require_once('../config.php');
//require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->dirroot.'/calendar/lib.php');
require_once($CFG->libdir.'/bennu/bennu.inc.php');

$userid = optional_param('userid', 0, PARAM_INT);
$username = optional_param('username', '', PARAM_TEXT);
$authtoken = required_param('authtoken', PARAM_ALPHANUM);
$generateurl = optional_param('generateurl', '', PARAM_TEXT);

if (empty($CFG->enablecalendarexport)) {
    die('no export');
}

//Fetch user information
$checkuserid = !empty($userid) && $user = $DB->get_record('user', array('id' => $userid), 'id,password');
//allowing for fallback check of old url - MDL-27542
$checkusername = !empty($username) && $user = $DB->get_record('user', array('username' => $username), 'id,password');
if (!$checkuserid && !$checkusername) {
    //No such user
    die('Invalid authentication');
}

//Check authentication token
$authuserid = !empty($userid) && $authtoken == sha1($userid . $user->password . $CFG->calendar_exportsalt);
//allowing for fallback check of old url - MDL-27542
$authusername = !empty($username) && $authtoken == sha1($username . $user->password . $CFG->calendar_exportsalt);
if (!$authuserid && !$authusername) {
    die('Invalid authentication');
}

// Get the calendar type we are using.
$calendartype = \core_calendar\type_factory::get_calendar_instance();

$what = optional_param('preset_what', 'all', PARAM_ALPHA);
$time = optional_param('preset_time', 'weeknow', PARAM_ALPHA);

$now = $calendartype->timestamp_to_date_array(time());

// Let's see if we have sufficient and correct data
$allowed_what = array('all', 'user', 'groups', 'courses');
$allowed_time = array('weeknow', 'weeknext', 'monthnow', 'monthnext', 'recentupcoming', 'custom');

if (!empty($generateurl)) {
    $authtoken = sha1($user->id . $user->password . $CFG->calendar_exportsalt);
    $params = array();
    $params['preset_what'] = $what;
    $params['preset_time'] = $time;
    $params['userid'] = $userid;
    $params['authtoken'] = $authtoken;
    $params['generateurl'] = true;

    $link = new moodle_url('/calendar/export.php', $params);
    redirect($link->out());
    die;
}

if(!empty($what) && !empty($time)) {
    if(in_array($what, $allowed_what) && in_array($time, $allowed_time)) {
        $courses = enrol_get_users_courses($user->id, true, 'id, visible, shortname');
        // Array of courses that we will pass to calendar_get_events() which is initially set to the list of the user's courses.
        $paramcourses = $courses;
        if ($what == 'all' || $what == 'groups') {
            $groups = array();
            foreach ($courses as $course) {
                $course_groups = groups_get_all_groups($course->id, $user->id);
                $groups = array_merge($groups, array_keys($course_groups));
            }
            if (empty($groups)) {
                $groups = false;
            }
        }
        if ($what == 'all') {
            $users = $user->id;
            $courses[SITEID] = new stdClass;
            $courses[SITEID]->shortname = get_string('globalevents', 'calendar');
            $paramcourses[SITEID] = $courses[SITEID];
        } else if ($what == 'groups') {
            $users = false;
            $paramcourses = array();
        } else if ($what == 'user') {
            $users = $user->id;
            $groups = false;
            $paramcourses = array();
        } else {
            $users = false;
            $groups = false;
        }

        // Store the number of days in the week.
        $numberofdaysinweek = $calendartype->get_num_weekdays();

        switch($time) {
            case 'weeknow':
                $startweekday = calendar_get_starting_weekday();
                $startmonthday = find_day_in_month($now['mday'] - ($numberofdaysinweek - 1), $startweekday, $now['mon'], $now['year']);
                $startmonth = $now['mon'];
                $startyear = $now['year'];
                if($startmonthday > calendar_days_in_month($startmonth, $startyear)) {
                    list($startmonth, $startyear) = calendar_add_month($startmonth, $startyear);
                    $startmonthday = find_day_in_month(1, $startweekday, $startmonth, $startyear);
                }
                $gregoriandate = $calendartype->convert_to_gregorian($startyear, $startmonth, $startmonthday);
                $timestart = make_timestamp($gregoriandate['year'], $gregoriandate['month'], $gregoriandate['day'],
                    $gregoriandate['hour'], $gregoriandate['minute']);

                $endmonthday = $startmonthday + $numberofdaysinweek;
                $endmonth = $startmonth;
                $endyear = $startyear;
                if($endmonthday > calendar_days_in_month($endmonth, $endyear)) {
                    list($endmonth, $endyear) = calendar_add_month($endmonth, $endyear);
                    $endmonthday = find_day_in_month(1, $startweekday, $endmonth, $endyear);
                }
                $gregoriandate = $calendartype->convert_to_gregorian($endyear, $endmonth, $endmonthday);
                $timeend = make_timestamp($gregoriandate['year'], $gregoriandate['month'], $gregoriandate['day'],
                    $gregoriandate['hour'], $gregoriandate['minute']);
            break;
            case 'weeknext':
                $startweekday = calendar_get_starting_weekday();
                $startmonthday = find_day_in_month($now['mday'] + 1, $startweekday, $now['mon'], $now['year']);
                $startmonth = $now['mon'];
                $startyear = $now['year'];
                if($startmonthday > calendar_days_in_month($startmonth, $startyear)) {
                    list($startmonth, $startyear) = calendar_add_month($startmonth, $startyear);
                    $startmonthday = find_day_in_month(1, $startweekday, $startmonth, $startyear);
                }
                $gregoriandate = $calendartype->convert_to_gregorian($startyear, $startmonth, $startmonthday);
                $timestart = make_timestamp($gregoriandate['year'], $gregoriandate['month'], $gregoriandate['day'],
                    $gregoriandate['hour'], $gregoriandate['minute']);

                $endmonthday = $startmonthday + $numberofdaysinweek;
                $endmonth = $startmonth;
                $endyear = $startyear;
                if($endmonthday > calendar_days_in_month($endmonth, $endyear)) {
                    list($endmonth, $endyear) = calendar_add_month($endmonth, $endyear);
                    $endmonthday = find_day_in_month(1, $startweekday, $endmonth, $endyear);
                }
                $gregoriandate = $calendartype->convert_to_gregorian($endyear, $endmonth, $endmonthday);
                $timeend = make_timestamp($gregoriandate['year'], $gregoriandate['month'], $gregoriandate['day'],
                    $gregoriandate['hour'], $gregoriandate['minute']);
            break;
            case 'monthnow':
                // Convert to gregorian.
                $gregoriandate = $calendartype->convert_to_gregorian($now['year'], $now['mon'], 1);

                $timestart = make_timestamp($gregoriandate['year'], $gregoriandate['month'], $gregoriandate['day'],
                    $gregoriandate['hour'], $gregoriandate['minute']);
                $timeend = $timestart + (calendar_days_in_month($now['mon'], $now['year']) * DAYSECS);
            break;
            case 'monthnext':
                // Get the next month for this calendar.
                list($nextmonth, $nextyear) = calendar_add_month($now['mon'], $now['year']);

                // Convert to gregorian.
                $gregoriandate = $calendartype->convert_to_gregorian($nextyear, $nextmonth, 1);

                // Create the timestamps.
                $timestart = make_timestamp($gregoriandate['year'], $gregoriandate['month'], $gregoriandate['day'],
                    $gregoriandate['hour'], $gregoriandate['minute']);
                $timeend = $timestart + (calendar_days_in_month($nextmonth, $nextyear) * DAYSECS);
            break;
            case 'recentupcoming':
                //Events in the last 5 or next 60 days
                $timestart = time() - 432000;
                $timeend = time() + 5184000;
            break;
            case 'custom':
                // Events based on custom date range.
                $timestart = time() - $CFG->calendar_exportlookback * DAYSECS;
                $timeend = time() + $CFG->calendar_exportlookahead * DAYSECS;
            break;
        }
    }
    else {
        // Parameters given but incorrect, redirect back to export page
        redirect($CFG->wwwroot.'/calendar/export.php');
        die();
    }
}
$events = calendar_get_events($timestart, $timeend, $users, $groups, array_keys($paramcourses), false);

$ical = new iCalendar;
$ical->add_property('method', 'PUBLISH');
foreach($events as $event) {
   if (!empty($event->modulename)) {
        $cm = get_coursemodule_from_instance($event->modulename, $event->instance);
        if (!\core_availability\info_module::is_user_visible($cm, $userid, false)) {
            continue;
        }
    }
    $hostaddress = str_replace('http://', '', $CFG->wwwroot);
    $hostaddress = str_replace('https://', '', $hostaddress);

    $me = new calendar_event($event); // To use moodle calendar event services.
    $ev = new iCalendar_event; // To export in ical format.
    $ev->add_property('uid', $event->id.'@'.$hostaddress);

    // Set iCal event summary from event name.
    $ev->add_property('summary', format_string($event->name, true, ['context' => $me->context]));

    // Format the description text.
    $description = format_text($me->description, $me->format, ['context' => $me->context]);
    // Then convert it to plain text, since it's the only format allowed for the event description property.
    // We use html_to_text in order to convert <br> and <p> tags to new line characters for descriptions in HTML format.
    $description = html_to_text($description, 0);
    $ev->add_property('description', $description);

    $ev->add_property('class', 'PUBLIC'); // PUBLIC / PRIVATE / CONFIDENTIAL
    $ev->add_property('last-modified', Bennu::timestamp_to_datetime($event->timemodified));
    $ev->add_property('dtstamp', Bennu::timestamp_to_datetime()); // now
    if ($event->timeduration > 0) {
        //dtend is better than duration, because it works in Microsoft Outlook and works better in Korganizer
        $ev->add_property('dtstart', Bennu::timestamp_to_datetime($event->timestart)); // when event starts.
        $ev->add_property('dtend', Bennu::timestamp_to_datetime($event->timestart + $event->timeduration));
    } else {
        // When no duration is present, ie an all day event, VALUE should be date instead of time and dtend = dtstart + 1 day.
        $ev->add_property('dtstart', Bennu::timestamp_to_date($event->timestart), array('value' => 'DATE')); // All day event.
        $ev->add_property('dtend', Bennu::timestamp_to_date($event->timestart + DAYSECS), array('value' => 'DATE')); // All day event.
    }
    if ($event->courseid != 0) {
        $coursecontext = context_course::instance($event->courseid);
        $ev->add_property('categories', format_string($courses[$event->courseid]->shortname, true, array('context' => $coursecontext)));
    }
    $ical->add_component($ev);
}

$serialized = $ical->serialize();
if(empty($serialized)) {
    // TODO
    die('bad serialization');
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
