<?php // $Id$

/////////////////////////////////////////////////////////////////////////////
//                                                                         //
// NOTICE OF COPYRIGHT                                                     //
//                                                                         //
// Moodle - Calendar extension                                             //
//                                                                         //
// Copyright (C) 2003-2004  Greek School Network            www.sch.gr     //
//                                                                         //
// Designed by:                                                            //
//     Avgoustos Tsinakos (tsinakos@uom.gr)                                //
//     Jon Papaioannou (pj@uom.gr)                                         //
//                                                                         //
// Programming and development:                                            //
//     Jon Papaioannou (pj@uom.gr)                                         //
//                                                                         //
// For bugs, suggestions, etc contact:                                     //
//     Jon Papaioannou (pj@uom.gr)                                         //
//                                                                         //
// The current module was developed at the University of Macedonia         //
// (www.uom.gr) under the funding of the Greek School Network (www.sch.gr) //
// The aim of this project is to provide additional and improved           //
// functionality to the Asynchronous Distance Education service that the   //
// Greek School Network deploys.                                           //
//                                                                         //
// This program is free software; you can redistribute it and/or modify    //
// it under the terms of the GNU General Public License as published by    //
// the Free Software Foundation; either version 2 of the License, or       //
// (at your option) any later version.                                     //
//                                                                         //
// This program is distributed in the hope that it will be useful,         //
// but WITHOUT ANY WARRANTY; without even the implied warranty of          //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the           //
// GNU General Public License for more details:                            //
//                                                                         //
//          http://www.gnu.org/copyleft/gpl.html                           //
//                                                                         //
/////////////////////////////////////////////////////////////////////////////

$firstday = get_string('firstdayofweek');
if(!is_numeric($firstday)) {
    define ('CALENDAR_STARTING_WEEKDAY', 1);
}
else {
    define ('CALENDAR_STARTING_WEEKDAY', intval($firstday) % 7);
}

define ('SECS_IN_DAY', 86400);
define ('CALENDAR_UPCOMING_DAYS', 14);
define ('CALENDAR_UPCOMING_MAXEVENTS', 10);
define ('CALENDAR_URL', $CFG->wwwroot.'/calendar/');
define ('CALENDAR_TF_24', '%H:%M');
define ('CALENDAR_TF_12', '%I:%M %p');

// Initialize the session variables here to be sure
calendar_session_vars();

function calendar_get_mini($courses, $groups, $users, $cal_month = false, $cal_year = false) {
    global $CFG, $USER;

    $display = &New stdClass;
    $display->minwday = get_user_preferences('calendar_startwday', CALENDAR_STARTING_WEEKDAY);
    $display->maxwday = $display->minwday + 6;

    $content = '';

    if(!empty($cal_month) && !empty($cal_year)) {
        $thisdate = usergetdate(time()); // Date and time the user sees at his location
        if($cal_month == $thisdate['mon'] && $cal_year == $thisdate['year']) {
            // Navigated to this month
            $date = $thisdate;
            $display->thismonth = true;
        }
        else {
            // Navigated to other month, let's do a nice trick and save us a lot of work...
            if(!checkdate($cal_month, 1, $cal_year)) {
                $date = array('mday' => 1, 'mon' => $thisdate['mon'], 'year' => $thisdate['year']);
                $display->thismonth = true;
            }
            else {
                $date = array('mday' => 1, 'mon' => $cal_month, 'year' => $cal_year);
                $display->thismonth = false;
            }
        }
    }
    else {
        $date = usergetdate(time()); // Date and time the user sees at his location
        $display->thismonth = true;
    }

    // Fill in the variables we 're going to use, nice and tidy
    list($d, $m, $y) = array($date['mday'], $date['mon'], $date['year']); // This is what we want to display
    $display->maxdays = calendar_days_in_month($m, $y);

    // We 'll keep these values as GMT here, and offset them when the time comes to query the db
    $display->tstart = gmmktime(0, 0, 0, $m, 1, $y); // This is GMT
    $display->tend = gmmktime(23, 59, 59, $m, $display->maxdays, $y); // GMT

    $startwday = gmdate('w', $display->tstart); // $display->tstart is already GMT, so don't use date(): messes with server's TZ

    // Align the starting weekday to fall in our display range
    // This is simple, not foolproof.
    if($startwday < $display->minwday) {
        $startwday += 7;
    }

    // Get the events matching our criteria. Don't forget to offset the timestamps for the user's TZ!
    $whereclause = calendar_sql_where(usertime($display->tstart), usertime($display->tend), $users, $groups, $courses);

    if($whereclause === false) {
        $events = array();
    }
    else {
        $events = get_records_select('event', $whereclause);
    }

    // This is either a genius idea or an idiot idea: in order to not complicate things, we use this rule: if, after
    // possibly removing courseid 1 from $courses, there is only one course left, then clicking on a day in the month
    // will also set the $SESSION->cal_courses_shown variable to that one course. Otherwise, we 'd need to add extra
    // arguments to this function.

    $morehref = '';
    if(!empty($courses)) {
        $courses = array_diff($courses, array(1));
        if(count($courses) == 1) {
            $morehref = '&amp;course='.reset($courses);
        }
    }

    // We want to have easy access by day, since the display is on a per-day basis.
    // Arguments passed by reference.
    calendar_events_by_day($events, $display->tstart, $eventsbyday, $durationbyday, $typesbyday);

    $content .= '<table class="calendarmini">'; // Begin table
    $content .= '<thead><tr>'; // Header row: day names

    // Print out the names of the weekdays
    $days = array('sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat');
    for($i = $display->minwday; $i <= $display->maxwday; ++$i) {
        // This uses the % operator to get the correct weekday no matter what shift we have
        // applied to the $display->minwday : $display->maxwday range from the default 0 : 6
        $content .= '<td>'.get_string($days[$i % 7], 'calendar').'</td>';
    }

    $content .= '</tr></thead><tbody><tr>'; // End of day names; prepare for day numbers

    // For the table display. $week is the row; $dayweek is the column.
    $dayweek = $startwday;

    // Paddding (the first week may have blank days in the beginning)
    for($i = $display->minwday; $i < $startwday; ++$i) {
        $content .= '<td>&nbsp;</td>'."\n";
    }

    $strftimetimedayshort = get_string('strftimedayshort');

    // Now display all the calendar
    for($day = 1; $day <= $display->maxdays; ++$day, ++$dayweek) {
        if($dayweek > $display->maxwday) {
            // We need to change week (table row)
            $content .= '</tr><tr>';
            $dayweek = $display->minwday;
        }

        // Reset vars
        $cell = '';
        if($dayweek % 7 == 0 || $dayweek % 7 == 6) {
            // Weekend. This is true no matter what the exact range is.
            $class = 'cal_weekend';
        }
        else {
            // Normal working day.
            $class = '';
        }

        // Special visual fx if an event is defined
        if(isset($eventsbyday[$day])) {
            $dayhref = calendar_get_link_href(CALENDAR_URL.'view.php?view=day'.$morehref.'&amp;', $day, $m, $y);

            // OverLib popup
            $popupcontent = '';
            foreach($eventsbyday[$day] as $eventid) {
                if (!isset($events[$eventid])) {
                    continue;
                }
                $event = $events[$eventid];
                if(!empty($event->modulename)) {
                    $popupicon = $CFG->modpixpath.'/'.$event->modulename.'/icon.gif';
                    $popupalt  = $event->modulename;

                } else if ($event->courseid == 1) {                                // Site event
                    $popupicon = $CFG->pixpath.'/c/site.gif';
                    $popupalt  = '';
                } else if ($event->courseid > 1 and empty($event->groupid)) {      // Course event
                    $popupicon = $CFG->pixpath.'/c/course.gif';
                   $popupalt  = '';
                } else if ($event->groupid) {                                      // Group event
                    $popupicon = $CFG->pixpath.'/c/group.gif';
                    $popupalt  = '';
                } else if ($event->userid) {                                       // User event
                    $popupicon = $CFG->pixpath.'/c/user.gif';
                    $popupalt  = '';
                }
                $popupcontent .= '<div><img height=16 width=16 src=\\\''.$popupicon.'\\\' style=\\\'vertical-align: middle; margin-right: 4px;\\\' alt=\\\''.$popupalt.'\\\' /><a href=\\\''.$dayhref.'\\\'>'.addslashes(htmlspecialchars($event->name)).'</a></div>';
            }

            $popupcaption = get_string('eventsfor', 'calendar', userdate($events[$eventid]->timestart, $strftimetimedayshort));
            $popup = 'onmouseover="return overlib(\''.$popupcontent.'\', CAPTION, \''.$popupcaption.'\');" onmouseout="return nd();"';

            // Class and cell content
            if(isset($typesbyday[$day]['startglobal'])) {
                $class .= ' cal_event_global';
            }
            else if(isset($typesbyday[$day]['startcourse'])) {
                $class .= ' cal_event_course';
            }
            else if(isset($typesbyday[$day]['startgroup'])) {
                $class .= ' cal_event_group';
            }
            else if(isset($typesbyday[$day]['startuser'])) {
                $class .= ' cal_event_user';
            }
            $cell = '<strong><a href="'.$dayhref.'" '.$popup.'">'.$day.'</a></strong>';
        }
        else {
            $cell = $day;
        }

        if(isset($typesbyday[$day]['durationglobal'])) {
            $class .= ' cal_duration_global';
        }
        else if(isset($typesbyday[$day]['durationcourse'])) {
            $class .= ' cal_duration_course';
        }
        else if(isset($typesbyday[$day]['durationgroup'])) {
            $class .= ' cal_duration_group';
        }
        else if(isset($typesbyday[$day]['durationuser'])) {
            $class .= ' cal_duration_user';
        }

        // Special visual fx for today
        if($display->thismonth && $day == $d) {
            $class .= ' cal_today';
        }

        // Just display it
        if(!empty($class)) {
            $class = ' class="'.$class.'"';
        }
        $content .= '<td'.$class.'>'.$cell."</td>\n";
    }

    // Paddding (the last week may have blank days at the end)
    for($i = $dayweek; $i <= $display->maxwday; ++$i) {
        $content .= '<td>&nbsp;</td>';
    }
    $content .= '</tr>'; // Last row ends

    $content .= '</tbody></table>'; // Tabular display of days ends

    return $content;
}

function calendar_get_upcoming($courses, $groups, $users, $daysinfuture, $maxevents, $fromtime=0) {
    global $CFG;

    $display = &New stdClass;
    $display->range = $daysinfuture; // How many days in the future we 'll look
    $display->maxevents = $maxevents;

    $output = array();

    // Prepare "course caching", since it may save us a lot of queries
    $coursecache = array();

    $processed = 0;
    $now = time(); // We 'll need this later
    $nowsecs = $now % SECS_IN_DAY; // this too
    $nowdays = $now - $nowsecs; // and this

    if ($fromtime) {
        $display->tstart = $fromtime;
    } else {
        $display->tstart = usergetmidnight(time());
    }

    // This effectively adds as many days as needed, and the final SECS_IN_DAY - 1
    // serves to cover the duration until the end of the final day. We could
    // just do another gmmktime() and an addition, but this is "faster" :)
    $display->tend = $display->tstart + (SECS_IN_DAY * $display->range) - 1;

    // Get the events matching our criteria
    $whereclause = calendar_sql_where($display->tstart, $display->tend, $users, $groups, $courses);
    if ($whereclause === false) {
        $events = false;
    } else {
        $whereclause .= ' ORDER BY timestart'; // We want them this way
        $events = get_records_select('event', $whereclause);
    }

    // This is either a genius idea or an idiot idea: in order to not complicate things, we use this rule: if, after
    // possibly removing courseid 1 from $courses, there is only one course left, then clicking on a day in the month
    // will also set the $SESSION->cal_courses_shown variable to that one course. Otherwise, we 'd need to add extra
    // arguments to this function.

    $morehref = '';
    if(!empty($courses)) {
        $courses = array_diff($courses, array(1));
        if(count($courses) == 1) {
            $morehref = '&amp;course='.reset($courses);
        }
    }

    if($events !== false) {
        foreach($events as $event) {
            if($processed >= $display->maxevents) break;

            $startdate = usergetdate($event->timestart);
            $enddate = usergetdate($event->timestart + $event->timeduration);

            $starttimesecs = $event->timestart % SECS_IN_DAY; // Seconds after that day's midnight
            $starttimedays = $event->timestart - $starttimesecs; // Timestamp of midnight of that day

            if($event->timeduration) {
                // To avoid doing the math if one IF is enough :)
                $endtimesecs = ($event->timestart + $event->timeduration) % SECS_IN_DAY; // Seconds after that day's midnight
                $endtimedays = ($event->timestart + $event->timeduration) - $endtimesecs; // Timestamp of midnight of that day
            }
            else {
                $endtimesecs = $starttimesecs;
                $endtimedays = $starttimedays;
            }

            // Keep in mind: $starttimeXXX, $endtimeXXX and $nowXXX are all in GMT-based
            // OK, now to get a meaningful display...

            // First of all we have to construct a human-readable date/time representation
            if($endtimedays < $nowdays || $endtimedays == $nowdays && $endtimesecs <= $nowsecs) {
                // It has expired, so we don't care about duration
                $day = calendar_day_representation($event->timestart + $event->timeduration, $now);
                $time = calendar_time_representation($event->timestart + $event->timeduration);

                // This var always has the printable time representation
                $eventtime = '<span class="dimmed_text"><a class="dimmed" href="'.calendar_get_link_href(CALENDAR_URL.'view.php?view=day'.$morehref.'&amp;', $enddate['mday'], $enddate['mon'], $enddate['year']).'">'.$day.'</a> ('.$time.')</span>';

            }
            else if($event->timeduration) {
                // It has a duration
                if($starttimedays == $endtimedays) {
                    // But it's all on one day
                    $day = calendar_day_representation($event->timestart, $now);
                    $timestart = calendar_time_representation($event->timestart);
                    $timeend = calendar_time_representation($event->timestart + $event->timeduration);

                    // Set printable representation
                    $eventtime = calendar_get_link_tag($day, CALENDAR_URL.'view.php?view=day'.$morehref.'&amp;', $enddate['mday'], $enddate['mon'], $enddate['year']).
                        ' ('.$timestart.' -> '.$timeend.')';
                }
                else {
                    // It spans two or more days
                    $daystart = calendar_day_representation($event->timestart, $now);
                    $dayend = calendar_day_representation($event->timestart + $event->timeduration, $now);
                    $timestart = calendar_time_representation($event->timestart);
                    $timeend = calendar_time_representation($event->timestart + $event->timeduration);

                    // Set printable representation
                    $eventtime = calendar_get_link_tag($daystart, CALENDAR_URL.'view.php?view=day'.$morehref.'&amp;', $startdate['mday'], $startdate['mon'], $startdate['year']).
                        ' ('.$timestart.') -> '.calendar_get_link_tag($dayend, CALENDAR_URL.'view.php?view=day'.$morehref.'&amp;', $enddate['mday'], $enddate['mon'], $enddate['year']).
                        ' ('.$timeend.')';
                }
            }
            else {
                // It's an "instantaneous" event
                $day = calendar_day_representation($event->timestart, $now);
                $time = calendar_time_representation($event->timestart);

                // Set printable representation
                $eventtime = calendar_get_link_tag($day, CALENDAR_URL.'view.php?view=day'.$morehref.'&amp;', $startdate['mday'], $startdate['mon'], $startdate['year']).' ('.$time.')';
            }

            $outkey = count($output);

            $output[$outkey] = $event;   // Grab the whole raw event by default

            // Now we know how to display the time, we have to see how to display the event
            if(!empty($event->modulename)) {                                // Activity event

                // The module name is set. I will assume that it has to be displayed, and
                // also that it is an automatically-generated event. And of course that the
                // three fields for get_coursemodule_from_instance are set correctly.

                calendar_get_course_cached($coursecache, $event->courseid);

                $module = calendar_get_module_cached($coursecache, $event->modulename, $event->instance, $event->courseid);

                if ($module === false) {
                    // This shouldn't have happened. What to do now?
                    // Just ignore it
                    continue;
                }

                $modulename = get_string('modulename', $event->modulename);
                $eventtype = get_string($event->eventtype, $event->modulename);
                $icon = $CFG->modpixpath.'/'.$event->modulename.'/icon.gif';

                $output[$outkey]->icon = '<img height=16 width=16 src="'.$icon.'" alt="" title="'.$modulename.'" style="vertical-align: middle;" />';
                $output[$outkey]->referer = '<a href="'.$CFG->wwwroot.'/mod/'.$event->modulename.'/view.php?id='.$module->id.'">'.$event->name.'</a>';
                $output[$outkey]->time = $eventtime;
                $output[$outkey]->courselink = '<a href="'.$CFG->wwwroot.'/course/view.php?id='.$event->courseid.'">'.$coursecache[$event->courseid]->fullname.'</a>';
                $output[$outkey]->cmid = $module->id;



            } else if($event->courseid == 1) {                              // Site event
                $output[$outkey]->icon = '<img height=16 width=16 src="'.$CFG->pixpath.'/c/site.gif" alt="" style="vertical-align: middle;" />';
                $output[$outkey]->time = $eventtime;



            } else if($event->courseid > 1 and !$event->groupid) {          // Course event
                calendar_get_course_cached($coursecache, $event->courseid);

                $output[$outkey]->icon = '<img height=16 width=16 src="'.$CFG->pixpath.'/c/course.gif" alt="" style="vertical-align: middle;" />';
                $output[$outkey]->time = $eventtime;
                $output[$outkey]->courselink = '<a href="'.$CFG->wwwroot.'/course/view.php?id='.$event->courseid.'">'.$coursecache[$event->courseid]->fullname.'</a>';



            } else if ($event->groupid) {                                    // Group event
                $output[$outkey]->icon = '<img height=16 width=16 src="'.$CFG->pixpath.'/c/group.gif" alt="" style="vertical-align: middle;" />';
                $output[$outkey]->time = $eventtime;



            } else if($event->userid) {                                      // User event
                $output[$outkey]->icon = '<img height=16 width=16 src="'.$CFG->pixpath.'/c/user.gif" alt="" style="vertical-align: middle;" />';
                $output[$outkey]->time = $eventtime;
            }
            ++$processed;
        }
    }
    return $output;
}

function calendar_sql_where($tstart, $tend, $users, $groups, $courses, $withduration=true, $ignorehidden=true) {
    $whereclause = '';
    // Quick test
    if(is_bool($users) && is_bool($groups) && is_bool($courses)) {
        return false;
    }

    if(is_array($users) && !empty($users)) {
        // Events from a number of users
        if(!empty($whereclause)) $whereclause .= ' OR';
        $whereclause .= ' userid IN ('.implode(',', $users).') AND courseid = 0 AND groupid = 0';
    }
    else if(is_numeric($users)) {
        // Events from one user
        if(!empty($whereclause)) $whereclause .= ' OR';
        $whereclause .= ' userid = '.$users.' AND courseid = 0 AND groupid = 0';
    }
    else if($users === true) {
        // Events from ALL users
        if(!empty($whereclause)) $whereclause .= ' OR';
        $whereclause .= ' userid != 0 AND courseid = 0 AND groupid = 0';
    }
    if(is_array($groups) && !empty($groups)) {
        // Events from a number of groups
        if(!empty($whereclause)) $whereclause .= ' OR';
        $whereclause .= ' groupid IN ('.implode(',', $groups).')';
    }
    else if(is_numeric($groups)) {
        // Events from one group
        if(!empty($whereclause)) $whereclause .= ' OR ';
        $whereclause .= ' groupid = '.$groups;
    }
    else if($groups === true) {
        // Events from ALL groups
        if(!empty($whereclause)) $whereclause .= ' OR ';
        $whereclause .= ' groupid != 0';
    }
    if(is_array($courses) && !empty($courses)) {
        // A number of courses
        if(!empty($whereclause)) $whereclause .= ' OR';
        $whereclause .= ' groupid = 0 AND courseid IN ('.implode(',', $courses).')';
    }
    else if(is_numeric($courses)) {
        // One course
        if(!empty($whereclause)) $whereclause .= ' OR';
        $whereclause .= ' groupid = 0 AND courseid = '.$courses;
    }
    else if($courses === true) {
        // Events from ALL courses
        if(!empty($whereclause)) $whereclause .= ' OR';
        $whereclause .= ' groupid = 0 AND courseid != 0';
    }

    if ($ignorehidden) {
        if (!empty($whereclause)) $whereclause .= ' AND';
        $whereclause .= ' visible = 1';
    }

    if($withduration) {
        $timeclause = 'timestart + timeduration >= '.$tstart.' AND timestart <= '.$tend;
    }
    else {
        $timeclause = 'timestart >= '.$tstart.' AND timestart <= '.$tend;
    }
    if(!empty($whereclause)) {
        // We have additional constraints
        $whereclause = $timeclause.' AND ('.$whereclause.')';
    }
    else {
        // Just basic time filtering
        $whereclause = $timeclause;
    }
    return $whereclause;
}

function calendar_top_controls($type, $data) {
    global $CFG;
    $content = '';
    if(!isset($data['d'])) {
        $data['d'] = 1;
    }
    $time = calendar_mktime_check($data['m'], $data['d'], $data['y']);
    $date = getdate($time);
    $data['m'] = $date['mon'];
    $data['y'] = $date['year'];

    switch($type) {
        case 'frontpage':
            list($prevmonth, $prevyear) = calendar_sub_month($data['m'], $data['y']);
            list($nextmonth, $nextyear) = calendar_add_month($data['m'], $data['y']);
            $nextlink = calendar_get_link_tag('&gt;&gt;', 'index.php?', 0, $nextmonth, $nextyear);
            $prevlink = calendar_get_link_tag('&lt;&lt;', 'index.php?', 0, $prevmonth, $prevyear);
            $content .= '<table class="generaltable" style="width: 100%;"><tr>';
            $content .= '<td style="text-align: left; width: 12%;">'.$prevlink."</td>\n";
            $content .= '<td style="text-align: center;"><a href="'.calendar_get_link_href(CALENDAR_URL.'view.php?view=month&amp;', 1, $data['m'], $data['y']).'">'.strftime(get_string('strftimemonthyear'), $time)."</a></td>\n";
            $content .= '<td style="text-align: right; width: 12%;">'.$nextlink."</td>\n";
            $content .= '</tr></table>';
        break;
        case 'course':
            list($prevmonth, $prevyear) = calendar_sub_month($data['m'], $data['y']);
            list($nextmonth, $nextyear) = calendar_add_month($data['m'], $data['y']);
            $nextlink = calendar_get_link_tag('&gt;&gt;', 'view.php?id='.$data['id'].'&amp;', 0, $nextmonth, $nextyear);
            $prevlink = calendar_get_link_tag('&lt;&lt;', 'view.php?id='.$data['id'].'&amp;', 0, $prevmonth, $prevyear);
            $content .= '<table class="generaltable" style="width: 100%;"><tr>';
            $content .= '<td style="text-align: left; width: 12%;">'.$prevlink."</td>\n";
            $content .= '<td style="text-align: center;"><a href="'.calendar_get_link_href(CALENDAR_URL.'view.php?view=month&amp;course='.$data['id'].'&amp;', 1, $data['m'], $data['y']).'">'.strftime(get_string('strftimemonthyear'), $time)."</a></td>\n";
            $content .= '<td style="text-align: right; width: 12%;">'.$nextlink."</td>\n";
            $content .= '</tr></table>';
        break;
        case 'upcoming':
            $content .= '<div style="text-align: center;"><a href="'.CALENDAR_URL.'view.php?view=upcoming">'.strftime(get_string('strftimemonthyear'), $time)."</a></div>\n";
        break;
        case 'display':
            $content .= '<div style="text-align: center;"><a href="'.calendar_get_link_href(CALENDAR_URL.'view.php?view=month&amp;', 1, $data['m'], $data['y']).'">'.strftime(get_string('strftimemonthyear'), $time)."</a></div>\n";
        break;
        case 'month':
            list($prevmonth, $prevyear) = calendar_sub_month($data['m'], $data['y']);
            list($nextmonth, $nextyear) = calendar_add_month($data['m'], $data['y']);
            $prevdate = calendar_mktime_check($prevmonth, 1, $prevyear);
            $nextdate = calendar_mktime_check($nextmonth, 1, $nextyear);
            $content .= "<table style='width: 100%;'><tr>\n";
            $content .= '<td style="text-align: left; width: 30%;"><a href="'.calendar_get_link_href('view.php?view=month&amp;', 1, $prevmonth, $prevyear).'">&lt;&lt; '.strftime(get_string('strftimemonthyear'), $prevdate)."</a></td>\n";
            $content .= '<td style="text-align: center"><strong>'.strftime(get_string('strftimemonthyear'), $time)."</strong></td>\n";
            $content .= '<td style="text-align: right; width: 30%;"><a href="'.calendar_get_link_href('view.php?view=month&amp;', 1, $nextmonth, $nextyear).'">'.strftime(get_string('strftimemonthyear'), $nextdate)." &gt;&gt;</a></td>\n";
            $content .= "</tr></table>\n";
        break;
        case 'day':
            $data['d'] = $date['mday']; // Just for convenience
            $dayname = calendar_wday_name($date['weekday']);
            $prevdate = getdate($time - SECS_IN_DAY);
            $nextdate = getdate($time + SECS_IN_DAY);
            $prevname = calendar_wday_name($prevdate['weekday']);
            $nextname = calendar_wday_name($nextdate['weekday']);
            $content .= "<table style='width: 100%;'><tr>\n";
            $content .= '<td style="text-align: left; width: 20%;"><a href="'.calendar_get_link_href('view.php?view=day&amp;', $prevdate['mday'], $prevdate['mon'], $prevdate['year']).'">&lt;&lt; '.$prevname."</a></td>\n";

            // Get the format string
            $text = get_string('strftimedaydate');
            // Regexp hackery to make a link out of the month/year part
            $text = ereg_replace('(%B.+%Y|%Y.+%B|%Y.+%m[^ ]+)', '<a href="'.calendar_get_link_href('view.php?view=month&amp;', 1, $data['m'], $data['y']).'">\\1</a>', $text);
            // Replace with actual values and lose any day leading zero
            $text = strftime($text, $time);
            $text = str_replace(' 0', ' ', $text);
            // Print the actual thing
            $content .= '<td style="text-align: center"><strong>'.$text."</strong></td>\n";

            $content .= '<td style="text-align: right; width: 20%;"><a href="'.calendar_get_link_href('view.php?view=day&amp;', $nextdate['mday'], $nextdate['mon'], $nextdate['year']).'">'.$nextname." &gt;&gt;</a></td>\n";
            $content .= "</tr></table>\n";
        break;
    }
    return $content;
}

function calendar_filter_controls($type, $vars = NULL, $course = NULL) {
    global $CFG, $SESSION, $USER;

    $groupevents = true;
    $getvars = '';

    switch($type) {
        case 'event':
        case 'upcoming':
        case 'day':
        case 'month':
            $getvars = '&amp;from='.$type;
        break;
        case 'course':
            $getvars = '&amp;from=course&amp;id='.$_GET['id'];
            if (isset($course->groupmode) and !$course->groupmode and $course->groupmodeforce) {
                $groupevents = false;
            }
        break;
    }

    if (!empty($vars)) {
        $getvars .= '&amp;'.$vars;
    }

    $content = '<table class="cal_controls" style="width: 98%;">';

    $content .= '<tr>';
    if($SESSION->cal_show_global) {
        $content .= '<td class="cal_event_global" style="width: 8px;"></td><td><a href="'.CALENDAR_URL.'set.php?var=showglobal'.$getvars.'" title="'.get_string('tt_hideglobal', 'calendar').'">'.get_string('globalevents', 'calendar').'</a></td>'."\n";
    }
    else {
        $content .= '<td style="width: 8px;"></td><td><a href="'.CALENDAR_URL.'set.php?var=showglobal'.$getvars.'" title="'.get_string('tt_showglobal', 'calendar').'">'.get_string('globalevents', 'calendar').'</a></td>'."\n";
    }
    if($SESSION->cal_show_course) {
        $content .= '<td class="cal_event_course" style="width: 8px;"></td><td><a href="'.CALENDAR_URL.'set.php?var=showcourses'.$getvars.'" title="'.get_string('tt_hidecourse', 'calendar').'">'.get_string('courseevents', 'calendar').'</a></td>'."\n";
    }
    else {
        $content .= '<td style="width: 8px;"></td><td><a href="'.CALENDAR_URL.'set.php?var=showcourses'.$getvars.'" title="'.get_string('tt_showcourse', 'calendar').'">'.get_string('courseevents', 'calendar').'</a></td>'."\n";
    }

    if(!isguest($USER->id)) {
        $content .= "</tr>\n<tr>";

        if($groupevents) {
            // This course MIGHT have group events defined, so show the filter
            if($SESSION->cal_show_groups) {
                $content .= '<td class="cal_event_group" style="width: 8px;"></td><td><a href="'.CALENDAR_URL.'set.php?var=showgroups'.$getvars.'" title="'.get_string('tt_hidegroups', 'calendar').'">'.get_string('groupevents', 'calendar').'</a></td>'."\n";
            } else {
                $content .= '<td style="width: 8px;"></td><td><a href="'.CALENDAR_URL.'set.php?var=showgroups'.$getvars.'" title="'.get_string('tt_showgroups', 'calendar').'">'.get_string('groupevents', 'calendar').'</a></td>'."\n";
            }
            if ($SESSION->cal_show_user) {
                $content .= '<td class="cal_event_user" style="width: 8px;"></td><td><a href="'.CALENDAR_URL.'set.php?var=showuser'.$getvars.'" title="'.get_string('tt_hideuser', 'calendar').'">'.get_string('userevents', 'calendar').'</a></td>'."\n";
            } else {
                $content .= '<td style="width: 8px;"></td><td><a href="'.CALENDAR_URL.'set.php?var=showuser'.$getvars.'" title="'.get_string('tt_showuser', 'calendar').'">'.get_string('userevents', 'calendar').'</a></td>'."\n";
            }

        } else {
            // This course CANNOT have group events, so lose the filter
            $content .= '<td style="width: 8px;"></td><td>&nbsp;</td>'."\n";

            if($SESSION->cal_show_user) {
                $content .= '<td class="cal_event_user" style="width: 8px;"></td><td><a href="'.CALENDAR_URL.'set.php?var=showuser'.$getvars.'" title="'.get_string('tt_hideuser', 'calendar').'">'.get_string('userevents', 'calendar').'</a></td>'."\n";
            } else {
                $content .= '<td style="width: 8px;"></td><td><a href="'.CALENDAR_URL.'set.php?var=showuser'.$getvars.'" title="'.get_string('tt_showuser', 'calendar').'">'.get_string('userevents', 'calendar').'</a></td>'."\n";
            }
        }
    }
    $content .= "</tr>\n</table>\n";

    return $content;
}

function calendar_day_representation($tstamp, $now = false, $usecommonwords = true) {

    static $shortformat;
    if(empty($shortformat)) {
        $shortformat = get_string('strftimedayshort');
    }

    if($now === false) {
        $now = time();
    }

    // To have it in one place, if a change is needed
    $formal = userdate($tstamp, $shortformat);

    // Reverse TZ compensation: make GMT stamps correspond to user's TZ
    $tzfix = calendar_get_tz_offset();
    $tstamp += $tzfix;
    $now += $tzfix;

    $eventdays = intval($tstamp / SECS_IN_DAY);
    $nowdays = intval($now / SECS_IN_DAY);

    if($usecommonwords == false) {
        // We don't want words, just a date
        return $formal;
    }
    else if($eventdays == $nowdays) {
        // Today
        return get_string('today', 'calendar');
    }
    else if($eventdays == $nowdays - 1) {
        // Yesterday
        return get_string('yesterday', 'calendar');
    }
    else if($eventdays == $nowdays + 1) {
        // Tomorrow
        return get_string('tomorrow', 'calendar');
    }
    else {
        return $formal;
    }
}

function calendar_time_representation($time) {
    static $langtimeformat = NULL;
    if($langtimeformat === NULL) {
        $langtimeformat = get_string('strftimetime');
    }
    $timeformat = get_user_preferences('calendar_timeformat');
    // The ? is needed because the preference might be present, but empty
    return userdate($time, empty($timeformat) ? $langtimeformat : $timeformat);
}

function calendar_get_link_href($linkbase, $d, $m, $y) {
    if(empty($linkbase)) return '';
    $paramstr = '';
    if(!empty($d)) $paramstr .= '&amp;cal_d='.$d;
    if(!empty($m)) $paramstr .= '&amp;cal_m='.$m;
    if(!empty($y)) $paramstr .= '&amp;cal_y='.$y;
    if(!empty($paramstr)) $paramstr = substr($paramstr, 5);
    return $linkbase.$paramstr;
}

function calendar_get_link_tag($text, $linkbase, $d, $m, $y) {
    $href = calendar_get_link_href($linkbase, $d, $m, $y);
    if(empty($href)) return $text;
    return '<a href="'.$href.'">'.$text.'</a>';
}

function calendar_gmmktime_check($m, $d, $y, $default = false) {
    if($default === false) $default = time();
    if(!checkdate($m, $d, $y)) {
        return $default;
    }
    else {
        return gmmktime(0, 0, 0, $m, $d, $y);
    }
}

function calendar_mktime_check($m, $d, $y, $default = false) {
    if($default === false) $default = time();
    if(!checkdate($m, $d, $y)) {
        return $default;
    }
    else {
        return mktime(0, 0, 0, $m, $d, $y);
    }
}

function calendar_month_name($month) {
    if(is_int($month)) {
        // 1 ... 12 integer converted to month name
        $months = array('january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december');
        return get_string($months[$month - 1], 'calendar');
    }
    else {
        return get_string(strtolower($month), 'calendar');
    }
}

function calendar_wday_name($englishname) {
    return get_string(strtolower($englishname), 'calendar');
}

function calendar_days_in_month($month, $year) {
   return date('t', mktime(0, 0, 0, $month, 1, $year));
}

function calendar_get_sideblock_upcoming($courses, $groups, $users, $daysinfuture, $maxevents) {
    $events = calendar_get_upcoming($courses, $groups, $users, $daysinfuture, $maxevents);

    $content = '';
    $lines = count($events);
    if (!$lines) {
        return $content;
    }

    for ($i = 0; $i < $lines; ++$i) {
        $content .= '<div class="cal_event">'.$events[$i]->icon.' ';
        if (!empty($events[$i]->referer)) {
            // That's an activity event, so let's provide the hyperlink
            $content .= $events[$i]->referer;
        } else {
            $content .= $events[$i]->name;
        }
        $events[$i]->time = str_replace('->', '<br />->', $events[$i]->time);
        $content .= '</div><div class="cal_event_date" style="text-align:right;">'.$events[$i]->time.'</div>';
        if ($i < $lines - 1) $content .= '<hr />';
    }

    return $content;
}

function calendar_add_month($month, $year) {
    if($month == 12) {
        return array(1, $year + 1);
    }
    else {
        return array($month + 1, $year);
    }
}

function calendar_sub_month($month, $year) {
    if($month == 1) {
        return array(12, $year - 1);
    }
    else {
        return array($month - 1, $year);
    }
}

function calendar_events_by_day($events, $starttime, &$eventsbyday, &$durationbyday, &$typesbyday) {
    $eventsbyday = array();
    $typesbyday = array();
    $durationbyday = array();

    if($events === false) {
        return;
    }

    // Reverse TZ compensation: make GMT stamps (from event table) correspond to user's TZ
    $tzfix = calendar_get_tz_offset();

    foreach($events as $event) {
        $eventdaystart = 1 + floor(($event->timestart + $tzfix - $starttime) / SECS_IN_DAY);
        $eventdayend = 1 + floor(($event->timestart + $event->timeduration + $tzfix - $starttime) / SECS_IN_DAY);

        // Give the event to its day
        $eventsbyday[$eventdaystart][] = $event->id;

        // Mark the day as having such an event
        if($event->courseid == 1 && $event->groupid == 0) {
            $typesbyday[$eventdaystart]['startglobal'] = true;
        }
        else if($event->courseid > 1 && $event->groupid == 0) {
            $typesbyday[$eventdaystart]['startcourse'] = true;
        }
        else if($event->groupid) {
            $typesbyday[$eventdaystart]['startgroup'] = true;
        }
        else if($event->userid) {
            $typesbyday[$eventdaystart]['startuser'] = true;
        }

        // Mark all days up to and including ending day as duration
        if($eventdaystart < $eventdayend) {

            // Normally this should be

            // $bound = min($eventdayend, $display->maxdays);
            // for($i = $eventdaystart + 1; $i <= $bound; ++$i) {

            // So that we don't go on marking days after the end of
            // the month if the event continues. However, this code
            // has moved and now we don't have access to $display->maxdays.
            // In order to save the overhead of recomputing it, we just
            // use this "dumb" approach. Anyway, the function that called
            // us already knows up to what day it should display.

            for($i = $eventdaystart + 1; $i <= $eventdayend; ++$i) {
                $durationbyday[$i][] = $event->id;
                if($event->courseid == 1 && $event->groupid == 0) {
                    $typesbyday[$i]['durationglobal'] = true;
                }
                else if($event->courseid > 1 && $event->groupid == 0) {
                    $typesbyday[$i]['durationcourse'] = true;
                }
                else if($event->groupid) {
                    $typesbyday[$i]['durationgroup'] = true;
                }
                else if($event->userid) {
                    $typesbyday[$i]['durationuser'] = true;
                }
            }
        }
    }
    return;
}

function calendar_get_module_cached(&$coursecache, $modulename, $instance, $courseid) {
    $module = get_coursemodule_from_instance($modulename, $instance, $courseid);

    if($module === false) return false;
    if(!calendar_get_course_cached($coursecache, $courseid)) {
        return false;
    }
    return $module;
}

function calendar_get_course_cached(&$coursecache, $courseid) {
    if(!isset($coursecache[$courseid])) {
        $coursecache[$courseid] = get_record('course', 'id', $courseid);
    }
    return $coursecache[$courseid];
}

function calendar_session_vars() {
    global $SESSION, $USER;

    if(!isset($SESSION->cal_course_referer)) {
        $SESSION->cal_course_referer = 0;
    }
    if(!isset($SESSION->cal_show_global)) {
        $SESSION->cal_show_global = true;
    }
    if(!isset($SESSION->cal_show_groups)) {
        $SESSION->cal_show_groups = true;
    }
    if(!isset($SESSION->cal_show_course)) {
        $SESSION->cal_show_course = true;
    }
    if(!isset($SESSION->cal_show_user)) {
        $SESSION->cal_show_user = isset($USER->id) ? $USER->id : false;
    }
    if(empty($SESSION->cal_courses_shown)) {
        $SESSION->cal_courses_shown = calendar_get_default_courses(true);
    }
}

function calendar_overlib_html() {
    global $CFG;

    $html = '';
    $html .= '<div id="overDiv" style="position: absolute; visibility: hidden; z-index:1000;"></div>';
    $html .= '<script type="text/javascript" src="'.CALENDAR_URL.'overlib.cfg.php"></script>';

    return $html;
}

function calendar_set_referring_course($courseid) {
    global $SESSION;
    $SESSION->cal_course_referer = intval($courseid);
}

function calendar_set_filters(&$courses, &$group, &$user, $courseeventsfrom = NULL, $groupeventsfrom = NULL, $ignorefilters = false) {
    global $SESSION, $USER;

    // Insidious bug-wannabe: setting $SESSION->cal_courses_shown to $course->id would cause
    // the code to function incorrectly UNLESS we convert it to an integer. One case where
    // PHP's loose type system works against us.
    if(is_string($SESSION->cal_courses_shown)) {
        $SESSION->cal_courses_shown = intval($SESSION->cal_courses_shown);
    }

    if($courseeventsfrom === NULL) {
        $courseeventsfrom = $SESSION->cal_courses_shown;
    }
    if($groupeventsfrom === NULL) {
        $groupeventsfrom = $SESSION->cal_courses_shown;
    }

    if(($SESSION->cal_show_course && $SESSION->cal_show_global) || $ignorefilters) {
        if(is_int($courseeventsfrom)) {
            $courses = array(1, $courseeventsfrom);
        }
        else if(is_array($courseeventsfrom)) {
            $courses = array_keys($courseeventsfrom);
            $courses[] = 1;
        }
    }
    else if($SESSION->cal_show_course) {
        if(is_int($courseeventsfrom)) {
            $courses = array($courseeventsfrom);
        }
        else if(is_array($courseeventsfrom)) {
            $courses = array_keys($courseeventsfrom);
        }
        $courses = array_diff($courses, array(1));
    }
    else if($SESSION->cal_show_global) {
        $courses = array(1);
    }
    else {
        $courses = false;
    }

    if($SESSION->cal_show_user || $ignorefilters) {
        // This ignores the "which user to see" setting
        // The functionality to do that does exist, but this was
        // the most painless way to solve bug 1323. And anyway,
        // it wasn't being used anywhere.
        $user = $USER->id;
        //$user = $SESSION->cal_show_user;
    }
    else {
        $user = false;
    }
    if($SESSION->cal_show_groups || $ignorefilters) {
        if(is_int($groupeventsfrom)) {
            $groupcourses = array($groupeventsfrom);
        }
        else if(is_array($groupeventsfrom)) {
            $groupcourses = array_keys($groupeventsfrom);
        }
        $grouparray = array();

        // We already have the courses to examine in $courses
        // For each course...
        foreach($groupcourses as $courseid) {
            // If the user is an editing teacher in there,
            if(isteacheredit($courseid, $USER->id)) {
                // Show events from all groups
                if(($grouprecords = get_groups($courseid)) !== false) {
                    $grouparray = array_merge($grouparray, array_keys($grouprecords));
                }
            }
            // Otherwise show events from the group he is a member of
            else if(isset($USER->groupmember[$courseid])) {
                $grouparray[] = $USER->groupmember[$courseid];
            }
        }
        if(empty($grouparray)) {
            $group = false;
        }
        else {
            $group = $grouparray;
        }
    }
    else {
        $group = false;
    }
}

function calendar_edit_event_allowed($event) {
    global $USER;

    if (isadmin($USER->id)) return true; // Admins are allowed anything

    if ($event->courseid > 1) {
        // Course event, only editing teachers may... edit :P
        if(isteacheredit($event->courseid)) {
            return true;
        }

    } else if($event->courseid == 0 && $event->groupid != 0) {
        // Group event
        $group = get_record('groups', 'id', $event->groupid);
        if($group === false) return false;
        if(isteacheredit($group->courseid)) {
            return true;
        }

    } else if($event->courseid == 0 && $event->groupid == 0 && $event->userid == $USER->id) {
        // User event, owned by this user
        return true;
    }

    return false;
}

function calendar_get_default_courses($ignoreref = false) {
    global $USER, $CFG, $SESSION;

    if(!empty($SESSION->cal_course_referer) && !$ignoreref) {
        return array($SESSION->cal_course_referer => 1);
    }

    if(empty($USER)) {
        return array();
    }

    $courses = array();
    if(isadmin($USER->id)) {
        $courses = get_records_sql('SELECT id, 1 FROM '.$CFG->prefix.'course');
        return $courses;
    }
    if(isset($USER->student) && is_array($USER->student)) {
        $courses = $USER->student + $courses;
    }
    if(isset($USER->teacher) && is_array($USER->teacher)) {
        $courses = $USER->teacher + $courses;
    }
    return $courses;
}

function calendar_get_tz_offset() {
    global $USER, $CFG;
    static $tzfix;

    // Caching
    if(isset($tzfix)) {
        return $tzfix;
    }

    if(empty($USER)) {
        // Don't forget that there are users which have NOT logged in, even as guests
        $timezone = $CFG->timezone;
    }
    else {
        // If, on the other hand, we do have a user...
        $timezone = $USER->timezone;
        if(abs($timezone > 13)) {
            // But if the user has specified 'server default' time,
            // don't get the server's; get the Moodle $CFG setting
            // (Martin's help text on site cfg implies this)
            $timezone = $CFG->timezone;
        }
    }

    if(abs($timezone) <= 13) {
        $tzfix = $timezone * 3600;
    }
    else {
        $tzfix = date('Z');
    }

    return $tzfix;
}

function calendar_preferences_array() {
    return array(
        'startwday' => get_string('pref_startwday', 'calendar'),
        'maxevents' => get_string('pref_maxevents', 'calendar'),
        'lookahead' => get_string('pref_lookahead', 'calendar'),
        'timeformat' => get_string('pref_timeformat', 'calendar'),
    );
}

function calendar_preferences_button() {
    global $CFG, $USER;

    // Guests have no preferences
    if (empty($USER->id) or isguest()) {
        return '';
    }

    return "<form target=\"$CFG->framename\" method=\"get\" ".
           " action=\"$CFG->wwwroot/calendar/preferences.php\">".
           "<input type=\"submit\" value=\"".get_string("preferences", "calendar")." ...\" /></form>";
}

if(!function_exists('array_diff_assoc')) {
    // PHP < 4.3.0
    function array_diff_assoc($source, $diff) {
        $res = $source;
        foreach ($diff as $key=>$data) {
            unset($res[$key]);
        }
        return $res;
    }
}

?>
