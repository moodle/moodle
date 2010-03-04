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
//     Avgoustos Tsinakos (tsinakos@teikav.edu.gr)                         //
//     Jon Papaioannou (pj@moodle.org)                                     //
//                                                                         //
// Programming and development:                                            //
//     Jon Papaioannou (pj@moodle.org)                                     //
//                                                                         //
// For bugs, suggestions, etc contact:                                     //
//     Jon Papaioannou (pj@moodle.org)                                     //
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

//  Display the calendar page.

    require_once('../config.php');
    require_once($CFG->dirroot.'/course/lib.php');
    require_once($CFG->dirroot.'/calendar/lib.php');

    $courseid = optional_param('course', 0, PARAM_INT);
    $view = optional_param('view', 'upcoming', PARAM_ALPHA);
    $day  = optional_param('cal_d', 0, PARAM_INT);
    $mon  = optional_param('cal_m', 0, PARAM_INT);
    $yr   = optional_param('cal_y', 0, PARAM_INT);

    if(!$site = get_site()) {
        redirect($CFG->wwwroot.'/'.$CFG->admin.'/index.php');
    }

    if ($courseid && $courseid != SITEID) {
        require_login($courseid);
    } else if ($CFG->forcelogin) {
        require_login();
    }

    // Initialize the session variables
    calendar_session_vars();

    //add_to_log($course->id, "course", "view", "view.php?id=$course->id", "$course->id");
    $now = usergetdate(time());
    $pagetitle = '';

    $strcalendar = get_string('calendar', 'calendar');
    $navlinks = array();
    $navlinks[] = array('name' => $strcalendar,
                        'link' =>calendar_get_link_href(CALENDAR_URL.'view.php?view=upcoming&amp;course='.$courseid.'&amp;',
                                                        $now['mday'], $now['mon'], $now['year']),
                        'type' => 'misc');


    if(!checkdate($mon, $day, $yr)) {
        $day = intval($now['mday']);
        $mon = intval($now['mon']);
        $yr = intval($now['year']);
    }
    $time = make_timestamp($yr, $mon, $day);

    switch($view) {
        case 'day':
            $navlinks[] = array('name' => userdate($time, get_string('strftimedate')), 'link' => null, 'type' => 'misc');
            $pagetitle = get_string('dayview', 'calendar');
        break;
        case 'month':
            $navlinks[] = array('name' => userdate($time, get_string('strftimemonthyear')), 'link' => null, 'type' => 'misc');
            $pagetitle = get_string('detailedmonthview', 'calendar');
        break;
        case 'upcoming':
            $pagetitle = get_string('upcomingevents', 'calendar');
        break;
    }

    // If a course has been supplied in the URL, change the filters to show that one
    if (!empty($courseid)) {
        if ($course = get_record('course', 'id', $courseid)) {
            if ($course->id == SITEID) {
                // If coming from the home page, show all courses
                $SESSION->cal_courses_shown = calendar_get_default_courses(true);
                calendar_set_referring_course(0);

            } else {
                // Otherwise show just this one
                $SESSION->cal_courses_shown = $course->id;
                calendar_set_referring_course($SESSION->cal_courses_shown);
            }
        }
    } else {
        $course = null;
    }

    if (empty($USER->id) or isguest()) {
        $defaultcourses = calendar_get_default_courses();
        calendar_set_filters($courses, $groups, $users, $defaultcourses, $defaultcourses);

    } else {
        calendar_set_filters($courses, $groups, $users);
    }

    // Let's see if we are supposed to provide a referring course link
    // but NOT for the "main page" course
    if ($SESSION->cal_course_referer != SITEID &&
       ($shortname = get_field('course', 'shortname', 'id', $SESSION->cal_course_referer)) !== false) {
        require_login();
        if (empty($course)) {
            $course = get_record('course', 'id', $SESSION->cal_course_referer); // Useful to have around
        }
    }

    $strcalendar = get_string('calendar', 'calendar');
    $prefsbutton = calendar_preferences_button();

    // Print title and header
    $navigation = build_navigation($navlinks);
    print_header("$site->shortname: $strcalendar: $pagetitle", $strcalendar, $navigation,
                 '', '', true, $prefsbutton, user_login_string($site));

    echo calendar_overlib_html();

    // Layout the whole page as three big columns.
    echo '<table id="calendar" style="height:100%;">';
    echo '<tr>';

    // START: Main column

    echo '<td class="maincalendar">';
    echo '<div class="heightcontainer">';

    switch($view) {
        case 'day':
            calendar_show_day($day, $mon, $yr, $courses, $groups, $users, $courseid);
        break;
        case 'month':
            calendar_show_month_detailed($mon, $yr, $courses, $groups, $users, $courseid);
        break;
        case 'upcoming':
            calendar_show_upcoming_events($courses, $groups, $users, get_user_preferences('calendar_lookahead', CALENDAR_UPCOMING_DAYS), get_user_preferences('calendar_maxevents', CALENDAR_UPCOMING_MAXEVENTS), $courseid);
        break;
    }

    //Link to calendar export page
    echo '<div class="bottom">';
    if (!empty($CFG->enablecalendarexport)) {
        print_single_button('export.php', array('course'=>$courseid), get_string('exportcalendar', 'calendar'));

        if (!empty($USER->id)) {
            $authtoken = sha1($USER->username . $USER->password . $CFG->calendar_exportsalt);
            $usernameencoded = urlencode($USER->username);

            echo "<a href=\"export_execute.php?preset_what=all&amp;preset_time=recentupcoming&amp;username=$usernameencoded&amp;authtoken=$authtoken\">"
                 .'<img src="'.$CFG->pixpath.'/i/ical.gif" height="14" width="36" '
                 .'alt="'.get_string('ical', 'calendar').'" '
                 .'title="'.get_string('quickdownloadcalendar', 'calendar').'" />'
                 .'</a>';
        }
    }

    echo '</div>';
    echo '</div>';
    echo '</td>';

    // END: Main column

    // START: Last column (3-month display)
    echo '<td class="sidecalendar">';
    list($prevmon, $prevyr) = calendar_sub_month($mon, $yr);
    list($nextmon, $nextyr) = calendar_add_month($mon, $yr);
    $getvars = 'id='.$courseid.'&amp;cal_d='.$day.'&amp;cal_m='.$mon.'&amp;cal_y='.$yr; // For filtering

    echo '<div class="sideblock">';
    echo '<div class="header"><h2>'.get_string('eventskey', 'calendar').'</h2></div>';
    echo '<div class="filters">';
    echo calendar_filter_controls($view, $getvars, NULL, $courses);
    echo '</div>';
    echo '</div>';

    echo '<div class="sideblock">';
    echo '<div class="header"><h2>'.get_string('monthlyview', 'calendar').'</h2></div>';

    echo '<div class="minicalendarblock minicalendartop">';
    echo calendar_top_controls('display', array('id' => $courseid, 'm' => $prevmon, 'y' => $prevyr));
    echo calendar_get_mini($courses, $groups, $users, $prevmon, $prevyr);
    echo '</div><div class="minicalendarblock">';
    echo calendar_top_controls('display', array('id' => $courseid, 'm' => $mon, 'y' => $yr));
    echo calendar_get_mini($courses, $groups, $users, $mon, $yr);
    echo '</div><div class="minicalendarblock">';
    echo calendar_top_controls('display', array('id' => $courseid, 'm' => $nextmon, 'y' => $nextyr));
    echo calendar_get_mini($courses, $groups, $users, $nextmon, $nextyr);
    echo '</div>';
    echo '</div>';

    echo '</td>';

    echo '</tr></table>';

    print_footer();



function calendar_show_day($d, $m, $y, $courses, $groups, $users, $courseid) {
    global $CFG, $USER;

    if (!checkdate($m, $d, $y)) {
        $now = usergetdate(time());
        list($d, $m, $y) = array(intval($now['mday']), intval($now['mon']), intval($now['year']));
    }

    $getvars = 'from=day&amp;cal_d='.$d.'&amp;cal_m='.$m.'&amp;cal_y='.$y; // For filtering

    $starttime = make_timestamp($y, $m, $d);
    $endtime   = make_timestamp($y, $m, $d + 1) - 1;

    $events = calendar_get_upcoming($courses, $groups, $users, 1, 100, $starttime);

    $text = '';
    if (!isguest() && !empty($USER->id) && calendar_user_can_add_event()) {
        $text.= '<div class="buttons">';
        $text.= '<form action="'.CALENDAR_URL.'event.php" method="get">';
        $text.= '<div>';
        $text.= '<input type="hidden" name="action" value="new" />';
        $text.= '<input type="hidden" name="course" value="'.$courseid.'" />';
        $text.= '<input type="hidden" name="cal_d" value="'.$d.'" />';
        $text.= '<input type="hidden" name="cal_m" value="'.$m.'" />';
        $text.= '<input type="hidden" name="cal_y" value="'.$y.'" />';
        $text.= '<input type="submit" value="'.get_string('newevent', 'calendar').'" />';
        $text.= '</div></form></div>';
    }

    $text .= '<label for="cal_course_flt_jump">'.
               get_string('dayview', 'calendar').
             ':</label>'.
             calendar_course_filter_selector($getvars);

    echo '<div class="header">'.$text.'</div>';

    echo '<div class="controls">'.calendar_top_controls('day', array('id' => $courseid, 'd' => $d, 'm' => $m, 'y' => $y)).'</div>';

    if (empty($events)) {
        // There is nothing to display today.
        echo '<h3>'.get_string('daywithnoevents', 'calendar').'</h3>';

    } else {

        echo '<div class="eventlist">';

        $underway = array();

        // First, print details about events that start today
        foreach ($events as $event) {

            $event->calendarcourseid = $courseid;

            if ($event->timestart >= $starttime && $event->timestart <= $endtime) {  // Print it now


/*
                $dayend = calendar_day_representation($event->timestart + $event->timeduration);
                $timeend = calendar_time_representation($event->timestart + $event->timeduration);
                $enddate = usergetdate($event->timestart + $event->timeduration);
                // Set printable representation
                echo calendar_get_link_tag($dayend, CALENDAR_URL.'view.php?view=day'.$morehref.'&amp;', $enddate['mday'], $enddate['mon'], $enddate['year']).' ('.$timeend.')';
*/
                //unset($event->time);

                $event->time = calendar_format_event_time($event, time(), '', false, $starttime);
                calendar_print_event($event);

            } else {                                                                 // Save this for later
                $underway[] = $event;
            }
        }

        // Then, show a list of all events that just span this day
        if (!empty($underway)) {
            echo '<h3>'.get_string('spanningevents', 'calendar').':</h3>';
            foreach ($underway as $event) {
                $event->time = calendar_format_event_time($event, time(), '', false, $starttime);
                calendar_print_event($event);
            }
        }

        echo '</div>';

    }
}

function calendar_show_month_detailed($m, $y, $courses, $groups, $users, $courseid) {
    global $CFG, $SESSION, $USER, $CALENDARDAYS;
    global $day, $mon, $yr;

    $getvars = 'from=month&amp;cal_d='.$day.'&amp;cal_m='.$mon.'&amp;cal_y='.$yr; // For filtering

    $display = &New stdClass;
    $display->minwday = get_user_preferences('calendar_startwday', CALENDAR_STARTING_WEEKDAY);
    $display->maxwday = $display->minwday + 6;

    if(!empty($m) && !empty($y)) {
        $thisdate = usergetdate(time()); // Time and day at the user's location
        if($m == $thisdate['mon'] && $y == $thisdate['year']) {
            // Navigated to this month
            $date = $thisdate;
            $display->thismonth = true;
        }
        else {
            // Navigated to other month, let's do a nice trick and save us a lot of work...
            if(!checkdate($m, 1, $y)) {
                $date = array('mday' => 1, 'mon' => $thisdate['mon'], 'year' => $thisdate['year']);
                $display->thismonth = true;
            }
            else {
                $date = array('mday' => 1, 'mon' => $m, 'year' => $y);
                $display->thismonth = false;
            }
        }
    }
    else {
        $date = usergetdate(time());
        $display->thismonth = true;
    }

    // Fill in the variables we 're going to use, nice and tidy
    list($d, $m, $y) = array($date['mday'], $date['mon'], $date['year']); // This is what we want to display
    $display->maxdays = calendar_days_in_month($m, $y);

    $startwday = 0;
    if (get_user_timezone_offset() < 99) {
        // We 'll keep these values as GMT here, and offset them when the time comes to query the db
        $display->tstart = gmmktime(0, 0, 0, $m, 1, $y); // This is GMT
        $display->tend = gmmktime(23, 59, 59, $m, $display->maxdays, $y); // GMT
        $startwday = gmdate('w', $display->tstart); // $display->tstart is already GMT, so don't use date(): messes with server's TZ
    } else {
        // no timezone info specified
        $display->tstart = mktime(0, 0, 0, $m, 1, $y);
        $display->tend = mktime(23, 59, 59, $m, $display->maxdays, $y);
        $startwday = date('w', $display->tstart); // $display->tstart not necessarily GMT, so use date()
    }

    // Align the starting weekday to fall in our display range
    if($startwday < $display->minwday) {
        $startwday += 7;
    }

    // Get events from database
    $events = calendar_get_events(usertime($display->tstart), usertime($display->tend), $users, $groups, $courses);
    if (!empty($events)) {
        foreach($events as $eventid => $event) {
            if (!empty($event->modulename)) {
                $cm = get_coursemodule_from_instance($event->modulename, $event->instance);
                if (!groups_course_module_visible($cm)) {
                    unset($events[$eventid]);
                }
            }
        }
    }

    // Extract information: events vs. time
    calendar_events_by_day($events, $m, $y, $eventsbyday, $durationbyday, $typesbyday, $courses);

    $text = '';
    if(!isguest() && !empty($USER->id) && calendar_user_can_add_event()) {
        $text.= '<div class="buttons"><form action="'.CALENDAR_URL.'event.php" method="get">';
        $text.= '<div>';
        $text.= '<input type="hidden" name="action" value="new" />';
        $text.= '<input type="hidden" name="course" value="'.$courseid.'" />';
        $text.= '<input type="hidden" name="cal_m" value="'.$m.'" />';
        $text.= '<input type="hidden" name="cal_y" value="'.$y.'" />';
        $text.= '<input type="submit" value="'.get_string('newevent', 'calendar').'" />';
        $text.= '</div></form></div>';
    }

    $text .= '<label for="cal_course_flt_jump">'.
               get_string('detailedmonthview', 'calendar').
             ':</label>'.
             calendar_course_filter_selector($getvars);

    echo '<div class="header">'.$text.'</div>';

    echo '<div class="controls">';
    echo calendar_top_controls('month', array('id' => $courseid, 'm' => $m, 'y' => $y));
    echo '</div>';

    // Start calendar display
    echo '<table class="calendarmonth"><tr class="weekdays">'; // Begin table. First row: day names

    // Print out the names of the weekdays
    for($i = $display->minwday; $i <= $display->maxwday; ++$i) {
        // This uses the % operator to get the correct weekday no matter what shift we have
        // applied to the $display->minwday : $display->maxwday range from the default 0 : 6
        echo '<th scope="col">'.get_string($CALENDARDAYS[$i % 7], 'calendar').'</th>';
    }

    echo '</tr><tr>'; // End of day names; prepare for day numbers

    // For the table display. $week is the row; $dayweek is the column.
    $week = 1;
    $dayweek = $startwday;

    // Paddding (the first week may have blank days in the beginning)
    for($i = $display->minwday; $i < $startwday; ++$i) {
        echo '<td class="nottoday">&nbsp;</td>'."\n";
    }

    // Now display all the calendar
    for($day = 1; $day <= $display->maxdays; ++$day, ++$dayweek) {
        if($dayweek > $display->maxwday) {
            // We need to change week (table row)
            echo "</tr>\n<tr>";
            $dayweek = $display->minwday;
            ++$week;
        }

        // Reset vars
        $cell = '';
        $dayhref = calendar_get_link_href(CALENDAR_URL.'view.php?view=day&amp;course='.$courseid.'&amp;', $day, $m, $y);

        if(CALENDAR_WEEKEND & (1 << ($dayweek % 7))) {
            // Weekend. This is true no matter what the exact range is.
            $class = 'weekend';
        }
        else {
            // Normal working day.
            $class = '';
        }

        // Special visual fx if an event is defined
        if(isset($eventsbyday[$day])) {
            if(count($eventsbyday[$day]) == 1) {
                $title = get_string('oneevent', 'calendar');
            }
            else {
                $title = get_string('manyevents', 'calendar', count($eventsbyday[$day]));
            }
            $cell = '<div class="day"><a href="'.$dayhref.'" title="'.$title.'">'.$day.'</a></div>';
        }
        else {
            $cell = '<div class="day">'.$day.'</div>';
        }

        // Special visual fx if an event spans many days
        if(isset($typesbyday[$day]['durationglobal'])) {
            $class .= ' duration_global';
        }
        else if(isset($typesbyday[$day]['durationcourse'])) {
            $class .= ' duration_course';
        }
        else if(isset($typesbyday[$day]['durationgroup'])) {
            $class .= ' duration_group';
        }
        else if(isset($typesbyday[$day]['durationuser'])) {
            $class .= ' duration_user';
        }

        // Special visual fx for today
        if($display->thismonth && $day == $d) {
            $class .= ' today';
        } else {
            $class .= ' nottoday';
        }

        // Just display it
        if(!empty($class)) {
            $class = ' class="'.trim($class).'"';
        }
        echo '<td'.$class.'>'.$cell;

        if(isset($eventsbyday[$day])) {
            echo '<ul class="events-new">';
            foreach($eventsbyday[$day] as $eventindex) {

                // If event has a class set then add it to the event <li> tag
                $eventclass = '';
                if (!empty($events[$eventindex]->class)) {
                    $eventclass = ' class="'.$events[$eventindex]->class.'"';
                }

                echo '<li'.$eventclass.'><a href="'.$dayhref.'#event_'.$events[$eventindex]->id.'">'.format_string($events[$eventindex]->name, true).'</a></li>';
            }
            echo '</ul>';
        }
        if(isset($durationbyday[$day])) {
            echo '<ul class="events-underway">';
            foreach($durationbyday[$day] as $eventindex) {
                echo '<li>['.format_string($events[$eventindex]->name,true).']</li>';
            }
            echo '</ul>';
        }
        echo "</td>\n";
    }

    // Paddding (the last week may have blank days at the end)
    for($i = $dayweek; $i <= $display->maxwday; ++$i) {
        echo '<td class="nottoday">&nbsp;</td>';
    }
    echo "</tr>\n"; // Last row ends

    echo "</table>\n"; // Tabular display of days ends

    // OK, now for the filtering display
    echo '<div class="filters"><table><tr>';

    // Global events
    if($SESSION->cal_show_global) {
        echo '<td class="event_global" style="width: 8px;"></td><td><strong>'.get_string('globalevents', 'calendar').':</strong> ';
        echo get_string('shown', 'calendar').' (<a href="'.CALENDAR_URL.'set.php?var=showglobal&amp;'.$getvars.'">'.get_string('clickhide', 'calendar').'</a>)</td>'."\n";
    } else {
        echo '<td style="width: 8px;"></td><td><strong>'.get_string('globalevents', 'calendar').':</strong> ';
        echo get_string('hidden', 'calendar').' (<a href="'.CALENDAR_URL.'set.php?var=showglobal&amp;'.$getvars.'">'.get_string('clickshow', 'calendar').'</a>)</td>'."\n";
    }

    // Course events
    if(!empty($SESSION->cal_show_course)) {
        echo '<td class="event_course" style="width: 8px;"></td><td><strong>'.get_string('courseevents', 'calendar').':</strong> ';
        echo get_string('shown', 'calendar').' (<a href="'.CALENDAR_URL.'set.php?var=showcourses&amp;'.$getvars.'">'.get_string('clickhide', 'calendar').'</a>)</td>'."\n";
    } else {
        echo '<td style="width: 8px;"></td><td><strong>'.get_string('courseevents', 'calendar').':</strong> ';
        echo get_string('hidden', 'calendar').' (<a href="'.CALENDAR_URL.'set.php?var=showcourses&amp;'.$getvars.'">'.get_string('clickshow', 'calendar').'</a>)</td>'."\n";
    }

    echo "</tr>\n";

    if(!empty($USER->id) && !isguest()) {
        echo '<tr>';
        // Group events
        if($SESSION->cal_show_groups) {
            echo '<td class="event_group" style="width: 8px;"></td><td><strong>'.get_string('groupevents', 'calendar').':</strong> ';
            echo get_string('shown', 'calendar').' (<a href="'.CALENDAR_URL.'set.php?var=showgroups&amp;'.$getvars.'">'.get_string('clickhide', 'calendar').'</a>)</td>'."\n";
        } else {
            echo '<td style="width: 8px;"></td><td><strong>'.get_string('groupevents', 'calendar').':</strong> ';
            echo get_string('hidden', 'calendar').' (<a href="'.CALENDAR_URL.'set.php?var=showgroups&amp;'.$getvars.'">'.get_string('clickshow', 'calendar').'</a>)</td>'."\n";
        }
        // User events
        if($SESSION->cal_show_user) {
            echo '<td class="event_user" style="width: 8px;"></td><td><strong>'.get_string('userevents', 'calendar').':</strong> ';
            echo get_string('shown', 'calendar').' (<a href="'.CALENDAR_URL.'set.php?var=showuser&amp;'.$getvars.'">'.get_string('clickhide', 'calendar').'</a>)</td>'."\n";
        } else {
            echo '<td style="width: 8px;"></td><td><strong>'.get_string('userevents', 'calendar').':</strong> ';
            echo get_string('hidden', 'calendar').' (<a href="'.CALENDAR_URL.'set.php?var=showuser&amp;'.$getvars.'">'.get_string('clickshow', 'calendar').'</a>)</td>'."\n";
        }
        echo "</tr>\n";
    }

    echo '</table></div>';
}

function calendar_show_upcoming_events($courses, $groups, $users, $futuredays, $maxevents, $courseid) {
    global $USER;

    $events = calendar_get_upcoming($courses, $groups, $users, $futuredays, $maxevents);

    $text = '';

    if(!isguest() && !empty($USER->id) && calendar_user_can_add_event()) {
        $text.= '<div class="buttons">';
        $text.= '<form action="'.CALENDAR_URL.'event.php" method="get">';
        $text.= '<div>';
        $text.= '<input type="hidden" name="action" value="new" />';
        $text.= '<input type="hidden" name="course" value="'.$courseid.'" />';
        $text.= '<input type="submit" value="'.get_string('newevent', 'calendar').'" />';
        $text.= '</div></form></div>';
    }

    $text .= '<label for="cal_course_flt_jump">'.
               get_string('upcomingevents', 'calendar').
             ': </label>'.
             calendar_course_filter_selector('from=upcoming');

    echo '<div class="header">'.$text.'</div>';

    if ($events) {

        echo '<div class="eventlist">';
        foreach ($events as $event) {
            $event->calendarcourseid = $courseid;
            calendar_print_event($event);
        }
        echo '</div>';
    } else {
        print_heading(get_string('noupcomingevents', 'calendar'));
    }
}

function calendar_course_filter_selector($getvars = '') {
    global $USER, $SESSION;

    if (empty($USER->id) or isguest()) {
        return '';
    }

    if (has_capability('moodle/calendar:manageentries', get_context_instance(CONTEXT_SYSTEM)) && !empty($CFG->calendar_adminseesall)) {
        $courses = get_courses('all', 'c.shortname','c.id,c.shortname');
    } else {
        $courses = get_my_courses($USER->id, 'shortname');
    }

    unset($courses[SITEID]);

    $courseoptions[SITEID] = get_string('fulllistofcourses');
    foreach ($courses as $course) {
        $courseoptions[$course->id] = format_string($course->shortname);
    }

    if (is_numeric($SESSION->cal_courses_shown)) {
        $selected = $SESSION->cal_courses_shown;
    } else {
        $selected = '';
    }

    return popup_form(CALENDAR_URL.'set.php?var=setcourse&amp;'.$getvars.'&amp;id=',
                       $courseoptions, 'cal_course_flt', $selected, '', '', '', true);
}

?>
