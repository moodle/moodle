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
    require_once('../course/lib.php');

    require_login();

    require_once('lib.php');

    optional_variable($_GET['view'], 'upcoming');
    optional_variable($_GET['course'], 0);
    optional_variable($_GET['cal_d']);
    optional_variable($_GET['cal_m']);
    optional_variable($_GET['cal_y']);

    if(!$site = get_site()) {
        redirect($CFG->wwwroot.'/'.$CFG->admin.'/index.php');
    }

    //add_to_log($course->id, "course", "view", "view.php?id=$course->id", "$course->id");
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
    $time = mktime(0, 0, 0, $mon, $day, $yr);

    switch($_GET['view']) {
        case 'day':
            $text = strftime(get_string('strftimedate'), $time);
            if($text[0] == '0') {
                $text = substr($text, 1);
            }
            $nav .= ' -> '.$text;
            $pagetitle = get_string('dayview', 'calendar');
        break;
        case 'month':
            $nav .= ' -> '.strftime(get_string('strftimemonthyear'), $time);
            $pagetitle = get_string('detailedmonthview', 'calendar');
        break;
        case 'upcoming':
            $pagetitle = get_string('upcomingevents', 'calendar');
        break;
    }

    // If a course has been supplied in the URL, change the filters to show that one
    if(!empty($_GET['course'])) {
        if(is_numeric($_GET['course']) && $_GET['course'] > 0 && record_exists('course', 'id', $_GET['course'])) {
            $SESSION->cal_courses_shown = intval($_GET['course']);
            calendar_set_referring_course($SESSION->cal_courses_shown);
        }
    }

    if(isguest($USER->id)) {
        $defaultcourses = calendar_get_default_courses();
        calendar_set_filters($courses, $groups, $users, $defaultcourses, $defaultcourses);
    }
    else {
        calendar_set_filters($courses, $groups, $users);
    }

    // Let's see if we are supposed to provide a referring course link
    // but NOT for the "main page" course
    if($SESSION->cal_course_referer > 1 &&
      ($shortname = get_field('course', 'shortname', 'id', $SESSION->cal_course_referer)) !== false) {
        // If we know about the referring course, show a return link
        $nav = '<a href="'.$CFG->wwwroot.'/course/view.php?id='.$SESSION->cal_course_referer.'">'.$shortname.'</a> -> '.$nav;
    }

    $strcalendar = get_string('calendar', 'calendar');
    $prefsbutton = calendar_preferences_button();

    // Print title and header
    print_header("$site->shortname: $strcalendar: $pagetitle", $strcalendar, $nav,
                 '', '', true, $prefsbutton, '<p class="logininfo">'.user_login_string($site).'</p>');

    echo calendar_overlib_html();

    // Layout the whole page as three big columns.
    echo '<table border="0" cellpadding="3" cellspacing="0" width="100%">';
    echo '<tr style="vertical-align: top;">';

    // START: Main column

    echo '<td style="vertical-align: top; width: 100%;">';

    switch($_GET['view']) {
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

    // END: Main column

    // START: Last column (3-month display)
    echo '<td style="vertical-align: top; width: 180px;">';
    print_side_block_start(get_string('monthlyview', 'calendar'), '', 'sideblockmain');
    list($prevmon, $prevyr) = calendar_sub_month($mon, $yr);
    list($nextmon, $nextyr) = calendar_add_month($mon, $yr);
    $getvars = 'cal_d='.$day.'&amp;cal_m='.$mon.'&amp;cal_y='.$yr; // For filtering
    echo calendar_filter_controls($_GET['view'], $getvars);
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
    print_spacer(1, 180);
    echo '</td>';

    echo '</tr></table>';

    print_footer();



function calendar_show_day($d, $m, $y, $courses, $groups, $users) {
    global $CFG, $THEME, $db;

    if (!checkdate($m, $d, $y)) {
        $now = usergetdate(time());
        list($d, $m, $y) = array(intval($now['mday']), intval($now['mon']), intval($now['year']));
    }

    $getvars = 'from=day&amp;cal_d='.$d.'&amp;cal_m='.$m.'&amp;cal_y='.$y; // For filtering

    $starttime = make_timestamp($y, $m, $d);
    $endtime   = $starttime + SECS_IN_DAY - 1;

    $events = calendar_get_upcoming($courses, $groups, $users, 1, 100, $starttime);

    // New event button
    if (isguest()) {
        $text = get_string('dayview', 'calendar').': '.calendar_course_filter_selector($getvars);

    } else {
        $text = '<div style="float: left;">'.get_string('dayview', 'calendar').': '.
                calendar_course_filter_selector($getvars).'</div><div style="float: right;">';
        $text.= '<form style="display: inline;" action="'.CALENDAR_URL.'event.php" method="get">';
        $text.= '<input type="hidden" name="action" value="new" />';
        $text.= '<input type="hidden" name="cal_d" value="'.$d.'" />';
        $text.= '<input type="hidden" name="cal_m" value="'.$m.'" />';
        $text.= '<input type="hidden" name="cal_y" value="'.$y.'" />';
        $text.= '<input type="submit" value="'.get_string('newevent', 'calendar').'" />';
        $text.= '</form></div>';
    }

    print_side_block_start($text, '', 'mycalendar');
    echo '<p>'.calendar_top_controls('day', array('d' => $d, 'm' => $m, 'y' => $y)).'</p>';

    if (empty($events)) {
        // There is nothing to display today.
        echo '<p style="text-align: center;">'.get_string('daywithnoevents', 'calendar').'</p>';

    } else {

        $underway = array();

        // First, print details about events that start today
        foreach ($events as $event) {
            if ($event->timestart >= $starttime && $event->timestart <= $endtime) {  // Print it now
                unset($event->time);
                calendar_print_event($event);

            } else {                                                                 // Save this for later
                $underway[] = $event;
            }
        }

        // Then, show a list of all events that just span this day
        if (!empty($underway)) {
            echo '<p style="text-align: center;"><strong>'.get_string('spanningevents', 'calendar').':</strong></p>';
            foreach ($underway as $event) {
                calendar_print_event($event);
            }
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
        $text = get_string('detailedmonthview', 'calendar').': '.calendar_course_filter_selector($getvars);
    }
    else {
        $text = '<div style="float: left;">'.get_string('detailedmonthview', 'calendar').': '.calendar_course_filter_selector($getvars).'</div><div style="float: right;">';
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
        $dayhref = calendar_get_link_href(CALENDAR_URL.'view.php?view=day&amp;', $day, $m, $y);

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
            $cell = '<strong><a href="'.$dayhref.'" title="'.$title.'">'.$day.'</a></strong>';
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
                echo '<td style="width: 100%;"><a href="'.$dayhref.'">'.$events[$eventindex]->name.'</a></td></tr>';
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

    // Course events
    if(!empty($SESSION->cal_show_course)) {
        echo '<td class="cal_event_course" style="width: 8px;"></td><td><strong>'.get_string('courseevents', 'calendar').':</strong> ';
        echo get_string('shown', 'calendar').' (<a href="'.CALENDAR_URL.'set.php?var=showcourses&amp;'.$getvars.'">'.get_string('clickhide', 'calendar').'</a>)</td>'."\n";
    }
    else {
        echo '<td style="width: 8px;"></td><td><strong>'.get_string('courseevents', 'calendar').':</strong> ';
        echo get_string('hidden', 'calendar').' (<a href="'.CALENDAR_URL.'set.php?var=showcourses&amp;'.$getvars.'">'.get_string('clickshow', 'calendar').'</a>)</td>'."\n";
    }

    echo "</tr>\n";

    if(!isguest($USER->id)) {
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
    }

    echo '<tbody></table><br />';
    print_side_block_end();
}

function calendar_show_upcoming_events($courses, $groups, $users, $futuredays, $maxevents) {

    $events = calendar_get_upcoming($courses, $groups, $users, $futuredays, $maxevents);

    // New event button
    if(isguest()) {
        $text = get_string('upcomingevents', 'calendar').': '.calendar_course_filter_selector('from=upcoming');

    } else {
        $text = '<div style="float: left;">'.get_string('upcomingevents', 'calendar').': '.calendar_course_filter_selector('from=upcoming').'</div><div style="float: right;">';
        $text.= '<form style="display: inline;" action="'.CALENDAR_URL.'event.php" method="get">';
        $text.= '<input type="hidden" name="action" value="new" />';
        /*
        $text.= '<input type="hidden" name="cal_m" value="'.$m.'" />';
        $text.= '<input type="hidden" name="cal_y" value="'.$y.'" />';
        */
        $text.= '<input type="submit" value="'.get_string('newevent', 'calendar').'" />';
        $text.= '</form></div>';
    }

    print_side_block_start($text, '', 'mycalendar');
    if ($events) {
        foreach ($events as $event) {
            calendar_print_event($event);
        }
    } else {
        echo '<br />';
        print_heading(get_string('noupcomingevents', 'calendar'));
    }
    print_side_block_end();
}


function calendar_print_event($event) {
    global $CFG, $THEME;

    static $strftimetime;

    echo '<table border="0" cellpadding="3" cellspacing="0" class="eventfull" width="100%">';
    echo "<tr><td bgcolor=\"$THEME->cellcontent2\" class=\"eventfullpicture\" width=\"32\" valign=\"top\">";
    if (!empty($event->icon)) {
        echo $event->icon;
    } else {
        print_spacer(16,16);
    }
    echo '</td>';
    echo "<td bgcolor=\"$THEME->cellheading\" class=\"eventfullheader\" width=\"100%\">";

    if (!empty($event->referer)) {
        echo '<span style="float:left;" class="calendarreferer">'.$event->referer.' </span>';
    } else {
        echo '<span style="float:left;" class="cal_event">'.$event->name."</span>";
    }
    if (!empty($event->courselink)) {
        echo '<br /><span style="float:left; font-size: 0.8em;">'.$event->courselink.' </span>';
    }
    if (!empty($event->time)) {
        echo '<span style="float:right;" class="cal_event_date">'.$event->time.'</span>';
    } else {
        echo '<span style="float:right;" class="cal_event_date">'.calendar_time_representation($event->timestart).'</span>';
    }

    echo "</td></tr>";
    echo "<tr><td bgcolor=\"$THEME->cellcontent2\" valign=\"top\" class=\"eventfullside\" width=\"32\">&nbsp;</td>";
    echo "<td bgcolor=\"$THEME->cellcontent\" class=\"eventfullmessage\">\n";
    echo format_text($event->description, FORMAT_HTML);
    if (calendar_edit_event_allowed($event)) {
        echo '<div align="right">';
        if (empty($event->cmid)) {
            $editlink = CALENDAR_URL.'event.php?action=edit&amp;id='.$event->id;
            $deletelink = CALENDAR_URL.'event.php?action=delete&amp;id='.$event->id;
        } else {
            $editlink   = "$CFG->wwwroot/mod/$event->modulename/view.php?id=$event->cmid";
            $deletelink = "$CFG->wwwroot/course/mod.php?delete=$event->cmid";
        }
        echo ' <a href="'.$editlink.'"><img
                  src="'.$CFG->pixpath.'/t/edit.gif" alt="'.get_string('tt_editevent', 'calendar').'"
                  title="'.get_string('tt_editevent', 'calendar').'" /></a>';
        echo ' <a href="'.$deletelink.'"><img
                  src="'.$CFG->pixpath.'/t/delete.gif" alt="'.get_string('tt_deleteevent', 'calendar').'"
                  title="'.get_string('tt_deleteevent', 'calendar').'" /></a>';
        echo '</div>';
    }
    echo "</td></tr>\n</table><br />\n\n";

}


function calendar_course_filter_selector($getvars = '') {
    global $USER, $SESSION;

    if (isguest($USER->id)) {
        return '';
    }

    if (isadmin($USER->id)) {
        $courses = get_courses('all', 'c.shortname');

    } else {
        $courses = get_my_courses($USER->id, 'shortname');
    }

    unset($courses[1]);

    $courseoptions[1] = get_string('fulllistofcourses');
    foreach ($courses as $course) {
        $courseoptions[$course->id] = $course->shortname;
    }

    if (is_numeric($SESSION->cal_courses_shown)) {
        $selected = $SESSION->cal_courses_shown;
    } else {
        $selected = '';
    }

    $form = popup_form(CALENDAR_URL.'set.php?var=setcourse&amp;'.$getvars.'&amp;id=',
                       $courseoptions, 'cal_course_flt', $selected, '', '', '', true);

    return str_replace('<form', '<form style="display: inline;"', $form);
}

?>
