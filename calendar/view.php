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

//  Display the calendar page.

    require_once('../config.php');
    require_once('lib.php');
    require_once('../course/lib.php');
    require_once('../mod/forum/lib.php');

    require_variable($_GET['view']);
    optional_variable($_GET['cal_d']);
    optional_variable($_GET['cal_m']);
    optional_variable($_GET['cal_y']);

    if(!$site = get_site()) {
        redirect($CFG->wwwroot.'/'.$CFG->admin.'/index.php');
    }

    //add_to_log($course->id, "course", "view", "view.php?id=$course->id", "$course->id");

    $firstcolumn = false;  // for now
    $lastcolumn = true;   // for now
    $side = 175;

    $prefmenu = true; // By default, display it
    calendar_session_vars();
    $now = usergetdate(time());
    $pagetitle = '';

    $nav = calendar_get_link_tag(get_string('calendar', 'calendar'), CALENDAR_URL.'view.php?view=upcoming&amp;', $now['mday'], $now['mon'], $now['year']);

    // Make sure that the GET variables are correct
    $day = intval($_GET['cal_d']);
    $mon = intval($_GET['cal_m']);
    $yr = intval($_GET['cal_y']);
    if(!checkdate($mon, $day, $yr)) {
        $day = intval($now['mday']);
        $mon = intval($now['mon']);
        $yr = intval($now['year']);
    }

    switch($_GET['view']) {
        case 'day':
            $nav .= ' -> '.$day.' '.calendar_month_name($mon).' '.$yr;
            $pagetitle = get_string('dayview', 'calendar');
        break;
        case 'month':
            $nav .= ' -> '.calendar_month_name($mon).' '.$yr;
            $pagetitle = get_string('detailedmonthview', 'calendar');
            $lastcolumn = false;
        break;
        case 'upcoming':
            $pagetitle = get_string('upcomingevents', 'calendar');
        break;
        case 'event':
            $pagetitle = get_string('eventview', 'calendar');
            $nav .= ' -> '.$pagetitle; // Smart guy... :)
        break;
    }

    // Let's see if we are supposed to provide a referring course link
    // but NOT for the "main page" course
    if($SESSION->cal_course_referer > 1 &&
      ($shortname = get_field('course', 'shortname', 'id', $SESSION->cal_course_referer)) !== false) {
        // If we know about the referring course, show a return link
        $nav = '<a href="'.$CFG->wwwroot.'/course/view.php?id='.$SESSION->cal_course_referer.'">'.$shortname.'</a> -> '.$nav;
    }

    // Print title and header
    if(!empty($pagetitle)) {
        $pagetitle = ': '.$pagetitle;
    }
    print_header(get_string('calendar', 'calendar').$pagetitle, $site->fullname, $nav,
                 '', '', true, '', '<p class="logininfo">'.user_login_string($site).'</p>');

    echo calendar_overlib_html();

    // Layout the whole page as three big columns.
    echo '<table border="0" cellpadding="3" cellspacing="0" width="100%">';

    // START: The left column ...
    echo '<tr valign="top"><td valign="top" width="180">';

    $sections = get_all_sections($site->id);

    // Latest site news
    if ($site->newsitems > 0 || $sections[0]->sequence || isediting($site->id) || isadmin()) {
        echo "<td width=\"$side\" valign=top nowrap>";
        $firstcolumn=true;

        if ($sections[0]->sequence or isediting($site->id)) {
            get_all_mods($site->id, $mods, $modnames, $modnamesplural, $modnamesused);
            print_section_block(get_string("mainmenu"), $site, $sections[0],
                                $mods, $modnames, $modnamesused, true, $side);
        }
        print_courses_sideblock(0, $side);
        if ($site->newsitems) {
            if ($news = forum_get_course_forum($site->id, "news")) {
                print_side_block_start(get_string("latestnews"), $side, "sideblocklatestnews");
                echo "<font size=\"-2\">";
                forum_print_latest_discussions($news->id, $site->newsitems, "minimal", "", false);
                echo "</font>";
                print_side_block_end();
            }
        }
        print_spacer(1,$side);
    }

    if (iscreator()) {
        if (!$firstcolumn) {
            echo "<td width=\"$side\" valign=top nowrap>";
            $firstcolumn=true;
        }
        print_admin_links($site->id, $side);
    }

    if ($firstcolumn) {
        echo '</td>';
    }

    // END: The left column

    // START: Middle column
    if ($lastcolumn) {
        echo '<td width="70%" valign="top\">';
    }
    else {
        echo '<td width="100%" valign="top">';
    }

    if($prefmenu) {
        $text = '<div style="float: left;">'.get_string('calendarheading', 'calendar', strip_tags($site->shortname)).'</div><div style="float: right;">';
        $text.= calendar_get_preferences_menu();
        $text.= '</div>';
    }
    else {
        $text = get_string('calendarheading', 'calendar', strip_tags($site->shortname));
    }

    print_heading_block($text);
    print_spacer(8, 1);

    $defaultcourses = calendar_get_default_courses();
    $courses = array();

    calendar_set_filters($courses, $groups, $users, $defaultcourses, $defaultcourses);

    // Are we left with a bad filter in effect?
    if($_GET['view'] != 'month') {
        if(is_int($SESSION->cal_show_course)) {
            // There is a filter in action that shows events from courses other than the current.
            // Reset the filter... this effectively allows course filtering only in the month display.
            // This filter resetting is also done in the course sideblock display, in case someone
            // sets a filter for course X and then goes to view course Y.
            $SESSION->cal_show_course = true;
        }
    }

    switch($_GET['view']) {
        case 'event':
            optional_variable($_GET['id'], 0);
            $event = get_record('event', 'id', intval($_GET['id']));
            if($event === false) {
                error('Invalid event id');
            }
            $date = calendar_show_event($event);
            $day = $date['mday'];
            $mon = $date['mon'];
            $yr  = $date['year'];
        break;
        case 'day':
            calendar_show_day($day, $mon, $yr, $courses, $groups, $users);
        break;
        case 'month':
            calendar_show_month_detailed($mon, $yr, $courses, $groups, $users);
        break;
        case 'upcoming':
            calendar_show_upcoming_events($courses, $groups, $users, get_user_preferences('calendar_lookahead', CALENDAR_UPCOMING_DAYS), get_user_preferences('calendar_maxevents', CALENDAR_UPCOMING_MAXEVENTS));
        break;
    }

    echo '</td>';

    // END: Middle column

    // START: Last column (3-month display)
    if ($lastcolumn) {
        echo '<td valign="top">';
        print_side_block_start(get_string('monthlyview', 'calendar'), '', 'sideblockmain');
        list($prevmon, $prevyr) = calendar_sub_month($mon, $yr);
        list($nextmon, $nextyr) = calendar_add_month($mon, $yr);
        echo calendar_filter_controls($_GET['view']);
        echo '<p>';
        echo calendar_top_controls('display', array('m' => $prevmon, 'y' => $prevyr));
        echo calendar_get_mini($courses, $groups, $users, $prevmon, $prevyr);
        echo '</p><p>';
        echo calendar_top_controls('display', array('m' => $mon, 'y' => $yr));
        echo calendar_get_mini($courses, $groups, $users, $mon, $yr);
        echo '</p><p>';
        echo calendar_top_controls('display', array('m' => $nextmon, 'y' => $nextyr));
        echo calendar_get_mini($courses, $groups, $users, $nextmon, $nextyr);
        echo '</p>';
        print_side_block_end();
        print_spacer(1, $side);
        echo '</td>';
    }

    echo '</tr></table>';
    print_footer();

function calendar_show_event($event) {
    // In this case, we haven't been given month, day, year. So we 'll have to
    // get them from the event date, and return them to the main function.

    $startdate = usergetdate($event->timestart); // This is only to be returned
    $coursecache = array();

    print_side_block_start(get_string('eventview', 'calendar'), '', 'mycalendar');
    calendar_print_event_table($event, $event->timestart, $event->timestart + $event->timeduration, $coursecache, true);
    print_side_block_end();

    return $startdate; // We need this to set "current" day, month, year
}

function calendar_show_day($d, $m, $y, $courses, $groups, $users) {
    global $CFG, $THEME;

    if(!checkdate($m, $d, $y)) {
        $now = usergetdate(time());
        list($d, $m, $y) = array(intval($now['mday']), intval($now['mon']), intval($now['year']));
    }

    $starttime = make_timestamp($y, $m, $d);
    $endtime = $starttime + SECS_IN_DAY - 1;
    $whereclause = calendar_sql_where($starttime, $endtime, $users, $groups, $courses);

    if($whereclause === false) {
        $events = array();
    }
    else {
        $events = get_records_select('event', $whereclause);
    }

    // New event button
    if(isguest()) {
        $text = get_string('dayview', 'calendar');
    }
    else {
        $text = '<div style="float: left;">'.get_string('dayview', 'calendar').'</div><div style="float: right;">';
        $text.= '<form style="display: inline;" action="'.CALENDAR_URL.'event.php" method="get">';
        $text.= '<input type="hidden" name="action" value="new" />';
        $text.= '<input type="hidden" name="cal_m" value="'.$m.'" />';
        $text.= '<input type="hidden" name="cal_y" value="'.$y.'" />';
        $text.= '<input type="submit" value="'.get_string('newevent', 'calendar').'" />';
        $text.= '</form></div>';
    }

    print_side_block_start($text, '', 'mycalendar');
    echo '<p>'.calendar_top_controls('day', array('d' => $d, 'm' => $m, 'y' => $y)).'</p>';

    if($events === false) {
        // There is nothing to display today.
        echo '<p style="text-align: center;">'.get_string('daywithnoevents', 'calendar').'</p>';
    }
    else {
        $coursecache = array();
        $summarize = array();

        // First, print details about events that start today
        foreach($events as $event) {
            if($event->timestart >= $starttime && $event->timestart <= $endtime) {
                // Print this
                calendar_print_event_table($event, $starttime, $endtime, $coursecache);
            }
            else {
                // Save this for later
                $summarize[] = $event->id;
            }
        }

        // Then, show a list of all events that just span this day
        if(!empty($summarize)) {
            $until = get_string('durationuntil', 'calendar');
            echo '<p style="text-align: center;"><strong>'.get_string('spanningevents', 'calendar').':</strong></p>';
            echo '<p style="text-align: center;"><ul>';
            foreach($summarize as $index) {
                $endstamp = $events[$index]->timestart + $events[$index]->timeduration;
                $enddate = usergetdate($endstamp);
                echo '<li><a href="view.php?view=event&amp;id='.$events[$index]->id.'">'.$events[$index]->name.'</a> ';
                echo '('.$until.' <a href="'.calendar_get_link_href('view.php?view=day&amp;', $enddate['mday'], $enddate['mon'], $enddate['year']).'">';
                echo calendar_day_representation($endstamp, false, false).'</a>)</li>';
            }
            echo '</ul></p>';
        }
    }

    print_side_block_end();
}

function calendar_show_month_detailed($m, $y, $courses, $groups, $users) {
    global $CFG, $SESSION, $USER;
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

    // We 'll keep these values as GMT here, and offset them when the time comes to query the db
    $display->tstart = gmmktime(0, 0, 0, $m, 1, $y); // This is GMT
    $display->tend = gmmktime(23, 59, 59, $m, $display->maxdays, $y); // GMT

    $startwday = gmdate('w', $display->tstart); // $display->tstart is already GMT, so don't use date(): messes with server's TZ

    // Align the starting weekday to fall in our display range
    if($startwday < $display->minwday) {
        $startwday += 7;
    }

    // Get events from database
    $whereclause = calendar_sql_where(usertime($display->tstart), usertime($display->tend), $users, $groups, $courses);
    if($whereclause === false) {
        $events = array();
    }
    else {
        $events = get_records_select('event', $whereclause);
    }

    // Extract information: events vs. time
    calendar_events_by_day($events, $display->tstart, $eventsbyday, $durationbyday, $typesbyday);

    // New event button
    if(isguest()) {
        $text = get_string('detailedmonthview', 'calendar');
    }
    else {
        $text = '<div style="float: left;">'.get_string('detailedmonthview', 'calendar').'</div><div style="float: right;">';
        $text.= '<form style="display: inline;" action="'.CALENDAR_URL.'event.php" method="get">';
        $text.= '<input type="hidden" name="action" value="new" />';
        $text.= '<input type="hidden" name="cal_m" value="'.$m.'" />';
        $text.= '<input type="hidden" name="cal_y" value="'.$y.'" />';
        $text.= '<input type="submit" value="'.get_string('newevent', 'calendar').'" />';
        $text.= '</form></div>';
    }

    print_side_block_start($text, '', 'mycalendar');
    echo calendar_top_controls('month', array('m' => $m, 'y' => $y));

    // Start calendar display
    echo '<table class="calendarmonth"><thead><tr>'; // Begin table. First row: day names

    // Print out the names of the weekdays
    $days = array('sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday');
    for($i = $display->minwday; $i <= $display->maxwday; ++$i) {
        // This uses the % operator to get the correct weekday no matter what shift we have
        // applied to the $display->minwday : $display->maxwday range from the default 0 : 6
        echo '<td class="calendarheader">'.get_string($days[$i % 7], 'calendar').'</td>';
    }

    echo '</tr></thead><tbody><tr>'; // End of day names; prepare for day numbers

    // For the table display. $week is the row; $dayweek is the column.
    $week = 1;
    $dayweek = $startwday;

    // Paddding (the first week may have blank days in the beginning)
    for($i = $display->minwday; $i < $startwday; ++$i) {
        echo '<td>&nbsp;</td>'."\n";
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
            if(count($eventsbyday[$day]) == 1) {
                $title = get_string('oneevent', 'calendar');
            }
            else {
                $title = get_string('manyevents', 'calendar', count($eventsbyday[$day]));
            }
            $cell = '<strong><a href="'.calendar_get_link_href(CALENDAR_URL.'view.php?view=day&amp;', $day, $m, $y).'" title="'.$title.'">'.$day.'</a></strong>';
        }
        else {
            $cell = $day;
        }

        // Special visual fx if an event spans many days
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
            $class = ' class="'.trim($class).'"';
        }
        echo '<td'.$class.'>'.$cell;

        if(isset($eventsbyday[$day])) {
            echo '<table>';
            foreach($eventsbyday[$day] as $eventindex) {
                echo '<tr><td style="vertical-align: top; width: 10px;"><strong>&middot;</strong></td>';
                echo '<td style="width: 100%;"><a href="'.CALENDAR_URL.'view.php?view=event&amp;id='.$events[$eventindex]->id.'">'.$events[$eventindex]->name.'</a></td></tr>';
            }
            echo '</table>';
        }
        if(isset($durationbyday[$day])) {
            foreach($durationbyday[$day] as $eventindex) {
                echo '<div class="dimmed_text">('.$events[$eventindex]->name.')</div>';
            }
        }
        echo "</td>\n";
    }

    // Paddding (the last week may have blank days at the end)
    for($i = $dayweek; $i <= $display->maxwday; ++$i) {
        echo '<td>&nbsp;</td>';
    }
    echo "</tr>\n</tbody>\n"; // Last row ends

    echo "</table>\n<br />\n"; // Tabular display of days ends

    // OK, now for the filtering display
    echo '<table class="cal_filters">';
    echo '<tbody>';
    echo '<tr>';

    // Global events
    if($SESSION->cal_show_global) {
        echo '<td class="cal_event_global" style="width: 8px;"></td><td><strong>'.get_string('globalevents', 'calendar').':</strong> ';
        echo get_string('shown', 'calendar').' (<a href="'.CALENDAR_URL.'set.php?var=showglobal&amp;'.$getvars.'">'.get_string('clickhide', 'calendar').'</a>)</td>'."\n";
    }
    else {
        echo '<td style="width: 8px;"></td><td><strong>'.get_string('globalevents', 'calendar').':</strong> ';
        echo get_string('hidden', 'calendar').' (<a href="'.CALENDAR_URL.'set.php?var=showglobal&amp;'.$getvars.'">'.get_string('clickshow', 'calendar').'</a>)</td>'."\n";
    }

    // Course events (this is kinda... tricky... :)
    echo '<td ';
    if($SESSION->cal_show_course !== false) {
        echo 'class="cal_event_course" ';
    }
    echo 'style="width: 8px;"></td><td><strong>'.get_string('courseevents', 'calendar').':</strong> ';

    if(isadmin($USER->id)) {
        $coursesdata = get_courses('all', 'c.shortname');
    }
    else {
        $coursesdata = get_my_courses($USER->id);
    }
    $coursesdata = array_diff_assoc($coursesdata, array(1 => 1));

    echo '<select name="course" onchange="document.location.href=\''.CALENDAR_URL.'set.php?var=setcourse&amp;'.$getvars.'&amp;id=\' + this.value;">';
    echo '<option value="0"'.($SESSION->cal_show_course === false?' selected':'').'>'.get_string('hidden', 'calendar')."</option>\n";
    echo '<option value="1"'.($SESSION->cal_show_course === true?' selected':'').'>'.get_string('shown', 'calendar')."</option>\n";
    if($coursesdata !== false) {
        foreach($coursesdata as $coursedata) {
            echo "\n<option value='$coursedata->id'";
            if(is_int($SESSION->cal_show_course) && $coursedata->id == $SESSION->cal_show_course) {
                echo ' selected';
            }
            echo '>'.$coursedata->shortname."</option>\n";
        }
    }
    echo '</select>';
    echo '</td>';
    echo "</tr>\n";
    echo '<tr>';

    // Group events
    if($SESSION->cal_show_groups) {
        echo '<td class="cal_event_group" style="width: 8px;"></td><td><strong>'.get_string('groupevents', 'calendar').':</strong> ';
        echo get_string('shown', 'calendar').' (<a href="'.CALENDAR_URL.'set.php?var=showgroups&amp;'.$getvars.'">'.get_string('clickhide', 'calendar').'</a>)</td>'."\n";
    }
    else {
        echo '<td style="width: 8px;"></td><td><strong>'.get_string('groupevents', 'calendar').':</strong> ';
        echo get_string('hidden', 'calendar').' (<a href="'.CALENDAR_URL.'set.php?var=showgroups&amp;'.$getvars.'">'.get_string('clickshow', 'calendar').'</a>)</td>'."\n";
    }
    // User events
    if($SESSION->cal_show_user) {
        echo '<td class="cal_event_user" style="width: 8px;"></td><td><strong>'.get_string('userevents', 'calendar').':</strong> ';
        echo get_string('shown', 'calendar').' (<a href="'.CALENDAR_URL.'set.php?var=showuser&amp;'.$getvars.'">'.get_string('clickhide', 'calendar').'</a>)</td>'."\n";
    }
    else {
        echo '<td style="width: 8px;"></td><td><strong>'.get_string('userevents', 'calendar').':</strong> ';
        echo get_string('hidden', 'calendar').' (<a href="'.CALENDAR_URL.'set.php?var=showuser&amp;'.$getvars.'">'.get_string('clickshow', 'calendar').'</a>)</td>'."\n";
    }

    echo "</tr>\n";
    echo '<tbody></table><br />';
    print_side_block_end();
}

function calendar_show_upcoming_events($courses, $groups, $users, $futuredays, $maxevents) {
    $events = calendar_get_upcoming($courses, $groups, $users, $futuredays, $maxevents);
    $numevents = count($events);

    if(!$numevents) {
        // There are no events in the specified time period
        return;
    }

    print_side_block_start(get_string('upcomingevents', 'calendar'), '', 'mycalendar');
    for($i = 0; $i < $numevents; ++$i) {
        echo '<p>';
        if(!empty($events[$i]->icon)) {
            echo '<span class="cal_event">'.$events[$i]->icon.' </span>';
        }
        if(!empty($events[$i]->referer) && empty($events[$i]->icon)) {
            echo '<span class="calendarreferer">'.$events[$i]->referer.': </span>';
        }
        echo '<span class="cal_event">'.$events[$i]->name.":</span>\n";
        if(!empty($events[$i]->referer) && !empty($events[$i]->icon)) {
            echo '<span class="calendarreferer">'.$events[$i]->referer.': </span>';
        }
        echo '<span class="cal_event_date">'.$events[$i]->time.'</span>';
        echo '<br />'.$events[$i]->description.'<br />';
        if($i < $numevents - 1) {
            echo '<hr />';
        }
        echo '</p>';
    }
    print_side_block_end();
}


function calendar_print_event_table($event, $starttime, $endtime, &$coursecache, $alldetails = false) {
    global $CFG;

    echo '<table class="cal_event_table"><thead>';

    if(calendar_edit_event_allowed($event)) {
        echo '<tr><td colspan="2">'.$event->name;
        echo ' <a href="'.CALENDAR_URL.'event.php?action=edit&amp;id='.$event->id.'"><img style="vertical-align: middle;" src="'.$CFG->pixpath.'/t/edit.gif" alt="'.get_string('tt_editevent', 'calendar').'" title="'.get_string('tt_editevent', 'calendar').'" /></a>';
        echo ' <a href="'.CALENDAR_URL.'event.php?action=delete&amp;id='.$event->id.'"><img style="vertical-align: middle;" src="'.$CFG->pixpath.'/t/delete.gif" alt="'.get_string('tt_deleteevent', 'calendar').'" title="'.get_string('tt_deleteevent', 'calendar').'" /></a>';
        echo '</td></tr>';
    }
    else {
        echo '<tr><td colspan="2">'.$event->name.'</td></tr>';
    }

    echo "</thead>\n<tbody>\n<tr><td style='vertical-align: top;'>";

    if(!empty($event->modulename)) {
        // The module name is set. This handling code should be synchronized with that in calendar_get_upcoming()
        $module = calendar_get_module_cached($coursecache, $event->modulename, $event->instance, $event->courseid);
        if($module === false) {
            // This shouldn't have happened. What to do now? Just ignore it...
            echo '</td></tr></table>';
            return;
        }
        $modulename = get_string('modulename', $event->modulename);
        $eventtype = get_string($event->eventtype, $event->modulename);
        $icon = $CFG->modpixpath.'/'.$event->modulename.'/icon.gif';
        $coursereferer = '<a href="'.$CFG->wwwroot.'/course/view.php?id='.$module->course.'">'.$coursecache[$module->course]->fullname.'</a>';
        $instancereferer = '<a href="'.$CFG->wwwroot.'/mod/'.$event->modulename.'/view.php?id='.$module->id.'">'.$module->name.'</a>';

        echo '<div><strong>'.get_string('course').':</strong></div><div>'.$coursereferer.'</div>';
        echo '<div><strong><img src="'.$icon.'" title="'.$modulename.'" style="vertical-align: middle;" /> '.$modulename.':</strong></div><div>'.$instancereferer.'</div>';
    }
    else if($event->courseid > 1) {
        $course = calendar_get_course_cached($coursecache, $event->courseid);
        $coursereferer = '<a href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'">'.$course->fullname.'</a>';
        echo '<div><strong>'.get_string('course').':</strong></div><div>'.$coursereferer.'</div>';
    }
    else if($event->courseid == 1) {
        echo '<div><strong>'.get_string('typesite', 'calendar').'</strong></div>';
    }

    if($event->timeduration) {
        if($event->timestart + $event->timeduration > $endtime || $alldetails) {
            // It doesn't end today, or full details requested, so we 'll go into a little more trouble
            $enddate = usergetdate($event->timestart + $event->timeduration);
            $enddisplay = calendar_get_link_tag(
            calendar_day_representation($event->timestart + $event->timeduration, $starttime, false),
            CALENDAR_URL.'view.php?view=day&amp;', $enddate['mday'], $enddate['mon'], $enddate['year']);
            $enddisplay .= ', '.calendar_time_representation($event->timestart + $event->timeduration);
        }
        else {
            $enddisplay = calendar_time_representation($event->timestart + $event->timeduration);
        }
        if($alldetails) {
            // We want to give a full representation of the event's date
            $startdate = usergetdate($event->timestart);
            $startdisplay = calendar_get_link_tag(
            calendar_day_representation($event->timestart, $starttime, false),
            CALENDAR_URL.'view.php?view=day&amp;', $startdate['mday'], $startdate['mon'], $startdate['year']);
            $startdisplay .= ', '.calendar_time_representation($event->timestart);
        }
        else {
            $startdisplay = calendar_time_representation($event->timestart);
        }
        echo '<div><strong>'.get_string('eventstarttime', 'calendar').':</strong></div><div>'.$startdisplay.'</div>';
        echo '<div><strong>'.get_string('eventendtime', 'calendar').':</strong></div><div>'.$enddisplay.'</div>';
    }
    else {
        // Event without duration
        if($alldetails) {
            // We want to give a full representation of the event's date
            $startdate = usergetdate($event->timestart);
            $startdisplay = calendar_get_link_tag(
            calendar_day_representation($event->timestart, $starttime, false),
            CALENDAR_URL.'view.php?view=day&amp;', $startdate['mday'], $startdate['mon'], $startdate['year']);
            $startdisplay .= ', '.calendar_time_representation($event->timestart);
        }
        else {
            $startdisplay = calendar_time_representation($event->timestart);
        }
        echo '<div><strong>'.get_string('eventinstanttime', 'calendar').':</strong></div><div>'.$startdisplay.'</div>';
    }

    echo '</td><td class="cal_event_description">'.$event->description.'</td></tr>'."\n";
    echo "</tbody>\n</table>\n";
}

?>
