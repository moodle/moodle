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

// These are read by the administration component to provide default values
define('CALENDAR_DEFAULT_UPCOMING_LOOKAHEAD', 21);
define('CALENDAR_DEFAULT_UPCOMING_MAXEVENTS', 10);
define('CALENDAR_DEFAULT_STARTING_WEEKDAY',   1);
// This is a packed bitfield: day X is "weekend" if $field & (1 << X) is true
// Default value = 65 = 64 + 1 = 2^6 + 2^0 = Saturday & Sunday
define('CALENDAR_DEFAULT_WEEKEND',            65);

// Fetch the correct values from admin settings/lang pack
// If no such settings found, use the above defaults
$firstday = isset($CFG->calendar_startwday) ? $CFG->calendar_startwday : get_string('firstdayofweek');
if(!is_numeric($firstday)) {    
    define ('CALENDAR_STARTING_WEEKDAY', CALENDAR_DEFAULT_STARTING_WEEKDAY);
}
else {
    define ('CALENDAR_STARTING_WEEKDAY', intval($firstday) % 7);
}
define ('CALENDAR_UPCOMING_DAYS', isset($CFG->calendar_lookahead) ? intval($CFG->calendar_lookahead) : CALENDAR_DEFAULT_UPCOMING_LOOKAHEAD);
define ('CALENDAR_UPCOMING_MAXEVENTS', isset($CFG->calendar_maxevents) ? intval($CFG->calendar_maxevents) : CALENDAR_DEFAULT_UPCOMING_MAXEVENTS);
define ('CALENDAR_WEEKEND', isset($CFG->calendar_weekend) ? intval($CFG->calendar_weekend) : CALENDAR_DEFAULT_WEEKEND);
define ('CALENDAR_URL', $CFG->wwwroot.'/calendar/');
define ('CALENDAR_TF_24', '%H:%M');
define ('CALENDAR_TF_12', '%I:%M %p');

$CALENDARDAYS = array('sunday','monday','tuesday','wednesday','thursday','friday','saturday');

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

    if (get_user_timezone_offset() < 99) {
        // We 'll keep these values as GMT here, and offset them when the time comes to query the db
	    $display->tstart = gmmktime(0, 0, 0, $m, 1, $y); // This is GMT
	    $display->tend = gmmktime(23, 59, 59, $m, $display->maxdays, $y); // GMT
    } else {
        // no timezone info specified
	    $display->tstart = mktime(0, 0, 0, $m, 1, $y);
	    $display->tend = mktime(23, 59, 59, $m, $display->maxdays, $y);
    }

    $startwday = dayofweek(1, $m, $y);

    // Align the starting weekday to fall in our display range
    // This is simple, not foolproof.
    if($startwday < $display->minwday) {
        $startwday += 7;
    }

    // TODO: THIS IS TEMPORARY CODE!
    // [pj] I was just reading through this and realized that I when writing this code I was probably
    // asking for trouble, as all these time manipulations seem to be unnecessary and a simple
    // make_timestamp would accomplish the same thing. So here goes a test:
    //$test_start = make_timestamp($y, $m, 1);
    //$test_end   = make_timestamp($y, $m, $display->maxdays, 23, 59, 59);
    //if($test_start != usertime($display->tstart) - dst_offset_on($display->tstart)) {
        //notify('Failed assertion in calendar/lib.php line 126; display->tstart = '.$display->tstart.', dst_offset = '.dst_offset_on($display->tstart).', usertime = '.usertime($display->tstart).', make_t = '.$test_start);
    //}
    //if($test_end != usertime($display->tend) - dst_offset_on($display->tend)) {
        //notify('Failed assertion in calendar/lib.php line 130; display->tend = '.$display->tend.', dst_offset = '.dst_offset_on($display->tend).', usertime = '.usertime($display->tend).', make_t = '.$test_end);
    //}


    // Get the events matching our criteria. Don't forget to offset the timestamps for the user's TZ!
    $whereclause = calendar_sql_where(
        usertime($display->tstart) - dst_offset_on($display->tstart),
        usertime($display->tend) - dst_offset_on($display->tend),
        $users, $groups, $courses);

    if($whereclause === false) {
        $events = array();
    }
    else {
        $events = get_records_select('event', $whereclause, 'timestart');
    }

    // This is either a genius idea or an idiot idea: in order to not complicate things, we use this rule: if, after
    // possibly removing SITEID from $courses, there is only one course left, then clicking on a day in the month
    // will also set the $SESSION->cal_courses_shown variable to that one course. Otherwise, we 'd need to add extra
    // arguments to this function.

    $morehref = '';
    if(!empty($courses)) {
        $courses = array_diff($courses, array(SITEID));
        if(count($courses) == 1) {
            $morehref = '&amp;course='.reset($courses);
        }
    }

    // We want to have easy access by day, since the display is on a per-day basis.
    // Arguments passed by reference.
    //calendar_events_by_day($events, $display->tstart, $eventsbyday, $durationbyday, $typesbyday);
    calendar_events_by_day($events, $m, $y, $eventsbyday, $durationbyday, $typesbyday);

    //Accessibility: added summary and <abbr> elements.
    ///global $CALENDARDAYS; appears to be broken.
    $days_title = array('sunday','monday','tuesday','wednesday','thursday','friday','saturday');

    $summary = get_string('calendarheading', 'calendar', userdate(make_timestamp($y, $m), get_string('strftimemonthyear')));
    $summary = get_string('tabledata', 'access', $summary);
    $content .= '<table class="minicalendar" summary="'.$summary.'">'; // Begin table
    $content .= '<tr class="weekdays">'; // Header row: day names

    // Print out the names of the weekdays
    $days = array('sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat');
    for($i = $display->minwday; $i <= $display->maxwday; ++$i) {
        // This uses the % operator to get the correct weekday no matter what shift we have
        // applied to the $display->minwday : $display->maxwday range from the default 0 : 6
        $content .= '<th scope="col"><abbr title="'. get_string($days_title[$i % 7], 'calendar') .'">'.
            get_string($days[$i % 7], 'calendar') ."</abbr></th>\n";
    }

    $content .= '</tr><tr>'; // End of day names; prepare for day numbers

    // For the table display. $week is the row; $dayweek is the column.
    $dayweek = $startwday;

    // Paddding (the first week may have blank days in the beginning)
    for($i = $display->minwday; $i < $startwday; ++$i) {
        $content .= '<td>&nbsp;</td>'."\n";
    }

    // Now display all the calendar
    for($day = 1; $day <= $display->maxdays; ++$day, ++$dayweek) {
        if($dayweek > $display->maxwday) {
            // We need to change week (table row)
            $content .= '</tr><tr>';
            $dayweek = $display->minwday;
        }

        // Reset vars
        $cell = '';
        if(CALENDAR_WEEKEND & (1 << ($dayweek % 7))) {
            // Weekend. This is true no matter what the exact range is.
            $class = 'weekend day';
        }
        else {
            // Normal working day.
            $class = 'day';
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

                } else if ($event->courseid == SITEID) {                                // Site event
                    $popupicon = $CFG->pixpath.'/c/site.gif';
                    $popupalt  = '';
                } else if ($event->courseid != 0 && $event->courseid != SITEID && $event->groupid == 0) {      // Course event
                    $popupicon = $CFG->pixpath.'/c/course.gif';
                    $popupalt  = '';
                } else if ($event->groupid) {                                      // Group event
                    $popupicon = $CFG->pixpath.'/c/group.gif';
                    $popupalt  = '';
                } else if ($event->userid) {                                       // User event
                    $popupicon = $CFG->pixpath.'/c/user.gif';
                    $popupalt  = '';
                }
                $popupcontent .= '<div><img height="16" width="16" src="'.$popupicon.'" style="vertical-align: middle; margin-right: 4px;" alt="'.$popupalt.'" /><a href="'.$dayhref.'#event_'.$event->id.'">'.format_string($event->name,true).'</a></div>';
            }
            
            //Accessibility: functionality moved to calendar_get_popup.
            if($display->thismonth && $day == $d) {
                $popup = calendar_get_popup(true, $events[$eventid]->timestart, $popupcontent);
            } else {
                $popup = calendar_get_popup(false, $events[$eventid]->timestart, $popupcontent);
            } 

            // Class and cell content
            if(isset($typesbyday[$day]['startglobal'])) {
                $class .= ' event_global';
            }
            else if(isset($typesbyday[$day]['startcourse'])) {
                $class .= ' event_course';
            }
            else if(isset($typesbyday[$day]['startgroup'])) {
                $class .= ' event_group';
            }
            else if(isset($typesbyday[$day]['startuser'])) {
                $class .= ' event_user';
            }
            $cell = '<a href="'.$dayhref.'" '.$popup.'>'.$day.'</a>';
        }
        else {
            $cell = $day;
        }

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
        //Accessibility: hidden text for today, and popup.
        if($display->thismonth && $day == $d) {
            $class .= ' today';
            $today = get_string('today', 'calendar').' '.userdate(time(), get_string('strftimedayshort'));
                        
            if(! isset($eventsbyday[$day])) {
                $class .= ' eventnone';
                $popup = calendar_get_popup(true, false);
                $cell = '<a href="#" '.$popup.'>'.$day.'</a>';
            }
            $cell = '<span class="accesshide">'.$today.' </span>'.$cell;
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

    $content .= '</table>'; // Tabular display of days ends

    return $content;
}

/**
 * calendar_get_popup, called at multiple points in from calendar_get_mini.
 *        Copied and modified from calendar_get_mini.
 * @uses OverLib popup.
 * @param $is_today bool, false except when called on the current day.
 * @param $event_timestart mixed, $events[$eventid]->timestart, OR false if there are no events.
 * @param $popupcontent string.
 * @return $popup string, contains onmousover and onmouseout events. 
 */
function calendar_get_popup($is_today, $event_timestart, $popupcontent='') {
    $popupcaption = '';
    if($is_today) {
        $popupcaption = get_string('today', 'calendar').' ';
    }
    if (false === $event_timestart) {
        $popupcaption .= userdate(time(), get_string('strftimedayshort'));
        $popupcontent = get_string('eventnone', 'calendar');

    } else {
        $popupcaption .= get_string('eventsfor', 'calendar', userdate($event_timestart, get_string('strftimedayshort')));
    }
    $popupcontent = str_replace("'", "\'", htmlspecialchars($popupcontent));
    $popupcaption = str_replace("'", "\'", htmlspecialchars($popupcaption)); 
    $popup = 'onmouseover="return overlib(\''.$popupcontent.'\', CAPTION, \''.$popupcaption.'\');" onmouseout="return nd();"';
    return $popup;
}

function calendar_get_upcoming($courses, $groups, $users, $daysinfuture, $maxevents, $fromtime=0) {
    global $CFG;

    $display = &new stdClass;
    $display->range = $daysinfuture; // How many days in the future we 'll look
    $display->maxevents = $maxevents;

    $output = array();

    // Prepare "course caching", since it may save us a lot of queries
    $coursecache = array();

    $processed = 0;
    $now = time(); // We 'll need this later
    $usermidnighttoday = usergetmidnight($now);

    if ($fromtime) {
        $display->tstart = $fromtime;
    } else {
        $display->tstart = $usermidnighttoday;
    }

    // This works correctly with respect to the user's DST, but it is accurate
    // only because $fromtime is always the exact midnight of some day!
    $display->tend = usergetmidnight($display->tstart + DAYSECS * $display->range + 3 * HOURSECS) - 1;

    // Get the events matching our criteria
    $whereclause = calendar_sql_where($display->tstart, $display->tend, $users, $groups, $courses);
    if ($whereclause === false) {
        $events = false;
    } else {
        $events = get_records_select('event', $whereclause, 'timestart');
    }

    // This is either a genius idea or an idiot idea: in order to not complicate things, we use this rule: if, after
    // possibly removing SITEID from $courses, there is only one course left, then clicking on a day in the month
    // will also set the $SESSION->cal_courses_shown variable to that one course. Otherwise, we 'd need to add extra
    // arguments to this function.

    $morehref = '';
    if(!empty($courses)) {
        $courses = array_diff($courses, array(SITEID));
        if(count($courses) == 1) {
            $morehref = '&amp;course='.reset($courses);
        }
    }

    if($events !== false) {

        foreach($events as $event) {

            if($processed >= $display->maxevents) {
                break;
            }

            $event->time = calendar_format_event_time($event, $now, $morehref);
            $output[] = $event;
            ++$processed;
        }
    }
    return $output;
}

function calendar_add_event_metadata($event) {
    global $CFG;

    //Support multilang in event->name 
    $event->name = format_string($event->name,true);
   
    if(!empty($event->modulename)) {                                // Activity event
        // The module name is set. I will assume that it has to be displayed, and
        // also that it is an automatically-generated event. And of course that the
        // fields for get_coursemodule_from_instance are set correctly.
        $module = calendar_get_module_cached($coursecache, $event->modulename, $event->instance);

        if ($module === false) {
            return;
        }

        $modulename = get_string('modulename', $event->modulename);
        $eventtype = get_string($event->eventtype, $event->modulename);
        $icon = $CFG->modpixpath.'/'.$event->modulename.'/icon.gif';

        $event->icon = '<img height="16" width="16" src="'.$icon.'" alt="" title="'.$modulename.'" style="vertical-align: middle;" />';
        $event->referer = '<a href="'.$CFG->wwwroot.'/mod/'.$event->modulename.'/view.php?id='.$module->id.'">'.$event->name.'</a>';
        $event->courselink = '<a href="'.$CFG->wwwroot.'/course/view.php?id='.$module->course.'">'.$coursecache[$module->course]->fullname.'</a>';
        $event->cmid = $module->id;


    } else if($event->courseid == SITEID) {                              // Site event
        $event->icon = '<img height="16" width="16" src="'.$CFG->pixpath.'/c/site.gif" alt="" style="vertical-align: middle;" />';

    } else if($event->courseid != 0 && $event->courseid != SITEID && $event->groupid == 0) {          // Course event
        calendar_get_course_cached($coursecache, $event->courseid);
        $event->icon = '<img height="16" width="16" src="'.$CFG->pixpath.'/c/course.gif" alt="" style="vertical-align: middle;" />';
        $event->courselink = '<a href="'.$CFG->wwwroot.'/course/view.php?id='.$event->courseid.'">'.$coursecache[$event->courseid]->fullname.'</a>';

    } else if ($event->groupid) {                                    // Group event
        $event->icon = '<img height="16" width="16" src="'.$CFG->pixpath.'/c/group.gif" alt="" style="vertical-align: middle;" />';

    } else if($event->userid) {                                      // User event
        $event->icon = '<img height="16" width="16" src="'.$CFG->pixpath.'/c/user.gif" alt="" style="vertical-align: middle;" />';
    }

    return $event;
}    

function calendar_print_event($event) {
    global $CFG, $USER;

    static $strftimetime;

    $event = calendar_add_event_metadata($event);
    echo '<a name="event_'.$event->id.'"></a><table class="event" cellspacing="0">';
    echo '<tr><td class="picture">';
    if (!empty($event->icon)) {
        echo $event->icon;
    } else {
        print_spacer(16,16);
    }
    echo '</td>';
    echo '<td class="topic">';

    if (!empty($event->referer)) {
        echo '<div class="referer">'.$event->referer.'</div>';
    } else {
        echo '<div class="name">'.$event->name."</div>";
    }
    if (!empty($event->courselink)) {
        echo '<div class="course">'.$event->courselink.' </div>';
    }
    if (!empty($event->time)) {
        echo '<span class="date">'.$event->time.'</span>';
    } else {
        echo '<span class="date">'.calendar_time_representation($event->timestart).'</span>';
    }

    echo '</td></tr>';
    echo '<tr><td class="side">&nbsp;</td>';
    echo '<td class="description">';
    echo format_text($event->description, FORMAT_HTML);
    if (calendar_edit_event_allowed($event)) {
        echo '<div class="commands">';
        if (empty($event->cmid)) {
            $editlink   = CALENDAR_URL.'event.php?action=edit&amp;id='.$event->id;
            $deletelink = CALENDAR_URL.'event.php?action=delete&amp;id='.$event->id;
        } else {
            $editlink   = $CFG->wwwroot.'/course/mod.php?update='.$event->cmid.'&amp;return=true&amp;sesskey='.$USER->sesskey;
            $deletelink = $CFG->wwwroot.'/course/mod.php?delete='.$event->cmid.'&amp;sesskey='.$USER->sesskey;;
        }
        echo ' <a href="'.$editlink.'"><img
                  src="'.$CFG->pixpath.'/t/edit.gif" alt="'.get_string('tt_editevent', 'calendar').'"
                  title="'.get_string('tt_editevent', 'calendar').'" /></a>';
        echo ' <a href="'.$deletelink.'"><img
                  src="'.$CFG->pixpath.'/t/delete.gif" alt="'.get_string('tt_deleteevent', 'calendar').'"
                  title="'.get_string('tt_deleteevent', 'calendar').'" /></a>';
        echo '</div>';
    }
    echo '</td></tr></table>';

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
        $whereclause .= ' (userid IN ('.implode(',', $users).') AND courseid = 0 AND groupid = 0)';
    }
    else if(is_numeric($users)) {
        // Events from one user
        if(!empty($whereclause)) $whereclause .= ' OR';
        $whereclause .= ' (userid = '.$users.' AND courseid = 0 AND groupid = 0)';
    }
    else if($users === true) {
        // Events from ALL users
        if(!empty($whereclause)) $whereclause .= ' OR';
        $whereclause .= ' (userid != 0 AND courseid = 0 AND groupid = 0)';
    }
    else if($users === false) {
        // No user at all, do nothing
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
    // boolean false (no groups at all): we don't need to do anything

    if(is_array($courses)) {
        // A number of courses (maybe none at all!)
        if(!empty($courses)) {
            if(!empty($whereclause)) {
                $whereclause .= ' OR';
            }
            $whereclause .= ' (groupid = 0 AND courseid IN ('.implode(',', $courses).'))';
        }
        else {
            // This means NO courses, not that we don't care!
            // No need to do anything
        }
    }
    else if(is_numeric($courses)) {
        // One course
        if(!empty($whereclause)) $whereclause .= ' OR';
        $whereclause .= ' (groupid = 0 AND courseid = '.$courses.')';
    }
    else if($courses === true) {
        // Events from ALL courses
        if(!empty($whereclause)) $whereclause .= ' OR';
        $whereclause .= ' (groupid = 0 AND courseid != 0)';
    }

    // Security check: if, by now, we have NOTHING in $whereclause, then it means
    // that NO event-selecting clauses were defined. Thus, we won't be returning ANY
    // events no matter what. Allowing the code to proceed might return a completely
    // valid query with only time constraints, thus selecting ALL events in that time frame!
    if(empty($whereclause)) {
        return false;
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

    if ($ignorehidden) {
        $whereclause .= ' AND visible = 1';
    }

    return $whereclause;
}

function calendar_top_controls($type, $data) {
    global $CFG, $CALENDARDAYS;
    $content = '';
    if(!isset($data['d'])) {
        $data['d'] = 1;
    }

    if(!checkdate($data['m'], $data['d'], $data['y'])) {
        $time = time();
    }
    else {
        $time = make_timestamp($data['y'], $data['m'], $data['d']);
    }
    $date = usergetdate($time);
    
    $data['m'] = $date['mon'];
    $data['y'] = $date['year'];

    //Accessibility: calendar block controls, replaced <table> with <div>.
    $nexttext = '<img src="'. $CFG->pixpath .'/a/r_next.gif" alt="'.get_string('monthnext','access').'" class="resize" />';
    $prevtext = '<img src="'. $CFG->pixpath .'/a/r_previous.gif" alt="'.get_string('monthprev','access').'" class="resize" />';

    switch($type) {
        case 'frontpage':
            list($prevmonth, $prevyear) = calendar_sub_month($data['m'], $data['y']);
            list($nextmonth, $nextyear) = calendar_add_month($data['m'], $data['y']);
            $nextlink = calendar_get_link_tag($nexttext, 'index.php?', 0, $nextmonth, $nextyear);
            $prevlink = calendar_get_link_tag($prevtext, 'index.php?', 0, $prevmonth, $prevyear);
            $content .= '<div class="calendar-controls">';
            $content .= '<span class="previous" title="'.get_string('monthprev','access').'">'.$prevlink."</span>\n";
            $content .= '<span class="hide"> | </span><span class="current"><a href="'.calendar_get_link_href(CALENDAR_URL.'view.php?view=month&amp;', 1, $data['m'], $data['y']).'">'.userdate($time, get_string('strftimemonthyear')).'</a></span>';
            $content .= '<span class="hide"> | </span><span class="next" title="'.get_string('monthnext','access').'">'.$nextlink."</span>\n";
            $content .= "<span class=\"clearer\"></span></div>\n";
        break;
        case 'course':
            list($prevmonth, $prevyear) = calendar_sub_month($data['m'], $data['y']);
            list($nextmonth, $nextyear) = calendar_add_month($data['m'], $data['y']);
            $nextlink = calendar_get_link_tag($nexttext, 'view.php?id='.$data['id'].'&amp;', 0, $nextmonth, $nextyear);
            $prevlink = calendar_get_link_tag($prevtext, 'view.php?id='.$data['id'].'&amp;', 0, $prevmonth, $prevyear);
            $content .= '<div class="calendar-controls">';
            $content .= '<span class="previous" title="'.get_string('monthprev','access').'">'.$prevlink."</span>\n";
            $content .= '<span class="hide"> | </span><span class="current"><a href="'.calendar_get_link_href(CALENDAR_URL.'view.php?view=month&amp;course='.$data['id'].'&amp;', 1, $data['m'], $data['y']).'">'.userdate($time, get_string('strftimemonthyear')).'</a></span>';
            $content .= '<span class="hide"> | </span><span class="next" title="'.get_string('monthnext','access').'">'.$nextlink."</span>\n";
            $content .= "<span class=\"clearer\"></span></div>\n";
        break;
        case 'upcoming':
            $content .= '<div style="text-align: center;"><a href="'.CALENDAR_URL.'view.php?view=upcoming">'.userdate($time, get_string('strftimemonthyear'))."</a></div>\n";
        break;
        case 'display':
            $content .= '<div style="text-align: center;"><a href="'.calendar_get_link_href(CALENDAR_URL.'view.php?view=month&amp;', 1, $data['m'], $data['y']).'">'.userdate($time, get_string('strftimemonthyear'))."</a></div>\n";
        break;
        case 'month':
            list($prevmonth, $prevyear) = calendar_sub_month($data['m'], $data['y']);
            list($nextmonth, $nextyear) = calendar_add_month($data['m'], $data['y']);
            $prevdate = make_timestamp($prevyear, $prevmonth, 1);
            $nextdate = make_timestamp($nextyear, $nextmonth, 1);
            $content .= '<div class="calendar-controls">';
            $content .= '<span class="previous"><a href="'.calendar_get_link_href('view.php?view=month&amp;', 1, $prevmonth, $prevyear).'">&lt; '.userdate($prevdate, get_string('strftimemonthyear')).'</a></span>';
            $content .= '<span class="hide"> | </span><span class="current">'.userdate($time, get_string('strftimemonthyear'))."</span>\n";
            $content .= '<span class="hide"> | </span><span class="next"><a href="'.calendar_get_link_href('view.php?view=month&amp;', 1, $nextmonth, $nextyear).'">'.userdate($nextdate, get_string('strftimemonthyear'))." &gt;</a></span>\n";
            $content .= "<span class=\"clearer\"></span></div>\n";
        break;
        case 'day':
            $data['d'] = $date['mday']; // Just for convenience
            $prevdate = usergetdate(make_timestamp($data['y'], $data['m'], $data['d'] - 1));
            $nextdate = usergetdate(make_timestamp($data['y'], $data['m'], $data['d'] + 1));
            $prevname = calendar_wday_name($CALENDARDAYS[$prevdate['wday']]);
            $nextname = calendar_wday_name($CALENDARDAYS[$nextdate['wday']]);
            $content .= '<div class="calendar-controls">';
            $content .= '<span class="previous"><a href="'.calendar_get_link_href('view.php?view=day&amp;', $prevdate['mday'], $prevdate['mon'], $prevdate['year']).'">&lt; '.$prevname."</a></span>\n";

            // Get the format string
            $text = get_string('strftimedaydate');
            /*
            // Regexp hackery to make a link out of the month/year part
            $text = ereg_replace('(%B.+%Y|%Y.+%B|%Y.+%m[^ ]+)', '<a href="'.calendar_get_link_href('view.php?view=month&amp;', 1, $data['m'], $data['y']).'">\\1</a>', $text);
            $text = ereg_replace('(F.+Y|Y.+F|Y.+m[^ ]+)', '<a href="'.calendar_get_link_href('view.php?view=month&amp;', 1, $data['m'], $data['y']).'">\\1</a>', $text);
            */
            // Replace with actual values and lose any day leading zero
            $text = userdate($time, $text);
            // Print the actual thing
            $content .= '<span class="hide"> | </span><span class="current">'.$text.'</span>';

            $content .= '<span class="hide"> | </span><span class="next"><a href="'.calendar_get_link_href('view.php?view=day&amp;', $nextdate['mday'], $nextdate['mon'], $nextdate['year']).'">'.$nextname." &gt;</a></span>\n";
            $content .= "<span class=\"clearer\"></span></div>\n";
        break;
    }
    return $content;
}

function calendar_filter_controls($type, $vars = NULL, $course = NULL) {
    global $CFG, $SESSION, $USER;

    $groupevents = true;
    $getvars = '';
   
    $id = optional_param( 'id',0,PARAM_INT );

    switch($type) {
        case 'event':
        case 'upcoming':
        case 'day':
        case 'month':
            $getvars = '&amp;from='.$type;
        break;
        case 'course':
            if ($id > 0) {
                $getvars = '&amp;from=course&amp;id='.$id;
            } else {
                $getvars = '&amp;from=course';
            }
            if (isset($course->groupmode) and $course->groupmode == NOGROUPS and $course->groupmodeforce) {
                $groupevents = false;
            }
        break;
    }

    if (!empty($vars)) {
        $getvars .= '&amp;'.$vars;
    }

    $content = '<table>';

    $content .= '<tr>';
    if($SESSION->cal_show_global) {
        $content .= '<td class="event_global" style="width: 8px;"></td><td><a href="'.CALENDAR_URL.'set.php?var=showglobal'.$getvars.'" title="'.get_string('tt_hideglobal', 'calendar').'">'.get_string('globalevents', 'calendar').'</a></td>'."\n";
    }
    else {
        $content .= '<td style="width: 8px;"></td><td><a href="'.CALENDAR_URL.'set.php?var=showglobal'.$getvars.'" title="'.get_string('tt_showglobal', 'calendar').'">'.get_string('globalevents', 'calendar').'</a></td>'."\n";
    }
    if($SESSION->cal_show_course) {
        $content .= '<td class="event_course" style="width: 8px;"></td><td><a href="'.CALENDAR_URL.'set.php?var=showcourses'.$getvars.'" title="'.get_string('tt_hidecourse', 'calendar').'">'.get_string('courseevents', 'calendar').'</a></td>'."\n";
    }
    else {
        $content .= '<td style="width: 8px;"></td><td><a href="'.CALENDAR_URL.'set.php?var=showcourses'.$getvars.'" title="'.get_string('tt_showcourse', 'calendar').'">'.get_string('courseevents', 'calendar').'</a></td>'."\n";
    }

    if(!empty($USER->id) && !isguest()) {
        $content .= "</tr>\n<tr>";

        if($groupevents) {
            // This course MIGHT have group events defined, so show the filter
            if($SESSION->cal_show_groups) {
                $content .= '<td class="event_group" style="width: 8px;"></td><td><a href="'.CALENDAR_URL.'set.php?var=showgroups'.$getvars.'" title="'.get_string('tt_hidegroups', 'calendar').'">'.get_string('groupevents', 'calendar').'</a></td>'."\n";
            } else {
                $content .= '<td style="width: 8px;"></td><td><a href="'.CALENDAR_URL.'set.php?var=showgroups'.$getvars.'" title="'.get_string('tt_showgroups', 'calendar').'">'.get_string('groupevents', 'calendar').'</a></td>'."\n";
            }
            if ($SESSION->cal_show_user) {
                $content .= '<td class="event_user" style="width: 8px;"></td><td><a href="'.CALENDAR_URL.'set.php?var=showuser'.$getvars.'" title="'.get_string('tt_hideuser', 'calendar').'">'.get_string('userevents', 'calendar').'</a></td>'."\n";
            } else {
                $content .= '<td style="width: 8px;"></td><td><a href="'.CALENDAR_URL.'set.php?var=showuser'.$getvars.'" title="'.get_string('tt_showuser', 'calendar').'">'.get_string('userevents', 'calendar').'</a></td>'."\n";
            }

        } else {
            // This course CANNOT have group events, so lose the filter
            $content .= '<td style="width: 8px;"></td><td>&nbsp;</td>'."\n";

            if($SESSION->cal_show_user) {
                $content .= '<td class="event_user" style="width: 8px;"></td><td><a href="'.CALENDAR_URL.'set.php?var=showuser'.$getvars.'" title="'.get_string('tt_hideuser', 'calendar').'">'.get_string('userevents', 'calendar').'</a></td>'."\n";
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

    $datestamp = usergetdate($tstamp);
    $datenow   = usergetdate($now);

    if($usecommonwords == false) {
        // We don't want words, just a date
        return $formal;
    }
    else if($datestamp['year'] == $datenow['year'] && $datestamp['yday'] == $datenow['yday']) {
        // Today
        return get_string('today', 'calendar');
    }
    else if(
        ($datestamp['year'] == $datenow['year'] && $datestamp['yday'] == $datenow['yday'] - 1 ) ||
        ($datestamp['year'] == $datenow['year'] - 1 && $datestamp['mday'] == 31 && $datestamp['mon'] == 12 && $datenow['yday'] == 1)
        ) {
        // Yesterday
        return get_string('yesterday', 'calendar');
    }
    else if(
        ($datestamp['year'] == $datenow['year'] && $datestamp['yday'] == $datenow['yday'] + 1 ) ||
        ($datestamp['year'] == $datenow['year'] + 1 && $datenow['mday'] == 31 && $datenow['mon'] == 12 && $datestamp['yday'] == 1)
        ) {
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

function calendar_wday_name($englishname) {
    return get_string(strtolower($englishname), 'calendar');
}

function calendar_days_in_month($month, $year) {
   return intval(date('t', mktime(0, 0, 0, $month, 1, $year)));
}

function calendar_get_sideblock_upcoming($events, $linkhref = NULL) {
    $content = '';
    $lines = count($events);
    if (!$lines) {
        return $content;
    }

    for ($i = 0; $i < $lines; ++$i) {
        if (!isset($events[$i]->time)) {   // Just for robustness
            continue;
        }
        $events[$i] = calendar_add_event_metadata($events[$i]);
        $content .= '<div class="event"><span class="icon c0">'.$events[$i]->icon.'</span> ';
        if (!empty($events[$i]->referer)) {
            // That's an activity event, so let's provide the hyperlink
            $content .= $events[$i]->referer;
        } else {
            if(!empty($linkhref)) {
                $ed = usergetdate($events[$i]->timestart);
                $href = calendar_get_link_href(CALENDAR_URL.$linkhref, $ed['mday'], $ed['mon'], $ed['year']);
                $content .= '<a href="'.$href.'#event_'.$events[$i]->id.'">'.$events[$i]->name.'</a>';
            }
            else {
                $content .= $events[$i]->name;
            }
        }
        $events[$i]->time = str_replace('&raquo;', '<br />&raquo;', $events[$i]->time);
        $content .= '<div class="date">'.$events[$i]->time.'</div></div>';
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

function calendar_events_by_day($events, $month, $year, &$eventsbyday, &$durationbyday, &$typesbyday) {
    $eventsbyday = array();
    $typesbyday = array();
    $durationbyday = array();

    if($events === false) {
        return;
    }

    foreach($events as $event) {

        $startdate = usergetdate($event->timestart);
        $enddate   = usergetdate($event->timestart + $event->timeduration);

        // Simple arithmetic: $year * 13 + $month is a distinct integer for each distinct ($year, $month) pair
        if(!($startdate['year'] * 13 + $startdate['mon'] <= $year * 13 + $month) && ($enddate['year'] * 13 + $enddate['mon'] >= $year * 13 + $month)) {
            // Out of bounds
            continue;
        }

        $eventdaystart = intval($startdate['mday']);

        if($startdate['mon'] == $month && $startdate['year'] == $year) {
            // Give the event to its day
            $eventsbyday[$eventdaystart][] = $event->id;

            // Mark the day as having such an event
            if($event->courseid == SITEID && $event->groupid == 0) {
                $typesbyday[$eventdaystart]['startglobal'] = true;
            }
            else if($event->courseid != 0 && $event->courseid != SITEID && $event->groupid == 0) {
                $typesbyday[$eventdaystart]['startcourse'] = true;
            }
            else if($event->groupid) {
                $typesbyday[$eventdaystart]['startgroup'] = true;
            }
            else if($event->userid) {
                $typesbyday[$eventdaystart]['startuser'] = true;
            }
        }

        if($event->timeduration == 0) {
            // Proceed with the next
            continue;
        }

        // The event starts on $month $year or before. So...
        $lowerbound = $startdate['mon'] == $month && $startdate['year'] == $year ? intval($startdate['mday']) : 0;

        // Also, it ends on $month $year or later...
        $upperbound = $enddate['mon'] == $month && $enddate['year'] == $year ? intval($enddate['mday']) : calendar_days_in_month($month, $year);

        // Mark all days between $lowerbound and $upperbound (inclusive) as duration
        for($i = $lowerbound + 1; $i <= $upperbound; ++$i) {
            $durationbyday[$i][] = $event->id;
            if($event->courseid == SITEID && $event->groupid == 0) {
                $typesbyday[$i]['durationglobal'] = true;
            }
            else if($event->courseid != 0 && $event->courseid != SITEID && $event->groupid == 0) {
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
    return;
}

function calendar_get_module_cached(&$coursecache, $modulename, $instance) {
    $module = get_coursemodule_from_instance($modulename, $instance);

    if($module === false) return false;
    if(!calendar_get_course_cached($coursecache, $module->course)) {
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

    if(!empty($USER->id) && isset($USER->realuser) && !isset($SESSION->cal_loggedinas)) {
        // We just logged in as someone else, update the filtering
        unset($SESSION->cal_users_shown);
        unset($SESSION->cal_courses_shown);
        $SESSION->cal_loggedinas = true;
        if(intval(get_user_preferences('calendar_persistflt', 0))) {
            calendar_set_filters_status(get_user_preferences('calendar_savedflt', 0xff));
        }
    }
    else if(!empty($USER->id) && !isset($USER->realuser) && isset($SESSION->cal_loggedinas)) {
        // We just logged back to our real self, update again
        unset($SESSION->cal_users_shown);
        unset($SESSION->cal_courses_shown);
        unset($SESSION->cal_loggedinas);
        if(intval(get_user_preferences('calendar_persistflt', 0))) {
            calendar_set_filters_status(get_user_preferences('calendar_savedflt', 0xff));
        }
    }

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
        $SESSION->cal_show_user = true;
    }
    if(empty($SESSION->cal_courses_shown)) {
        $SESSION->cal_courses_shown = calendar_get_default_courses(true);
    }
    if(empty($SESSION->cal_users_shown)) {
        // The empty() instead of !isset() here makes a whole world of difference,
        // as it will automatically change to the user's id when the user first logs
        // in. With !isset(), it would never do that.
        $SESSION->cal_users_shown = isset($USER->id) ? $USER->id : false;
    }
    else if(is_numeric($SESSION->cal_users_shown) && !empty($USER->id) && $SESSION->cal_users_shown != $USER->id) {
        // Follow the white rabbit, for example if a teacher logs in as a student
        $SESSION->cal_users_shown = $USER->id;
    }
}

function calendar_overlib_html() {
    return '<div id="overDiv" style="position: absolute; visibility: hidden; z-index:1000;"></div>'
          .'<script type="text/javascript" src="'.CALENDAR_URL.'overlib.cfg.php"></script>';
}

function calendar_set_referring_course($courseid) {
    global $SESSION;
    $SESSION->cal_course_referer = intval($courseid);
}

function calendar_set_filters(&$courses, &$group, &$user, $courseeventsfrom = NULL, $groupeventsfrom = NULL, $ignorefilters = false) {
    global $SESSION, $USER, $CFG;

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
            $courses = array(SITEID, $courseeventsfrom);
        }
        else if(is_array($courseeventsfrom)) {
            $courses = array_keys($courseeventsfrom);
            $courses[] = SITEID;
        }
    }
    else if($SESSION->cal_show_course) {
        if(is_int($courseeventsfrom)) {
            $courses = array($courseeventsfrom);
        }
        else if(is_array($courseeventsfrom)) {
            $courses = array_keys($courseeventsfrom);
        }
        $courses = array_diff($courses, array(SITEID));
    }
    else if($SESSION->cal_show_global) {
        $courses = array(SITEID);
    }
    else {
        $courses = false;
    }
   //BUG 6130 clean $courses array as SESSION has bad entries. 
   foreach ($courses as $index => $value) {
       if (empty($value)) unset($courses[$index]);
   }

    if($SESSION->cal_show_user || $ignorefilters) {
        // This doesn't work for arrays yet (maybe someday it will)
        $user = $SESSION->cal_users_shown;
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

        if(isadmin() && !empty($CFG->calendar_adminseesall)) {
            $group = true;
        }
        else {
            $grouparray = array();
            $groupmodes = NULL;

            // We already have the courses to examine in $courses
            // For each course...
            foreach($groupcourses as $courseid) {

                // If the user is an editing teacher in there,
                if(!empty($USER->id) && isteacheredit($courseid, $USER->id)) {

                    // The first time we get in here, retrieve all groupmodes at once
                    if($groupmodes === NULL) {
                        $groupmodes = get_records_list('course', 'id', implode(',', $groupcourses), '', 'id, groupmode, groupmodeforce');
                    }

                    // If this course has groups, show events from all of them
                    if(isset($groupmodes[$courseid]) && ($groupmodes[$courseid]->groupmode != NOGROUPS || !$groupmodes[$courseid]->groupmodeforce) && ($grouprecords = get_groups($courseid)) !== false) {
                        $grouparray = array_merge($grouparray, array_keys($grouprecords));
                    }
                }

                // Otherwise (not editing teacher) show events from the group he is a member of
                else if(isset($USER->groupmember[$courseid])) {
                    //changed to 2D array
                    foreach ($USER->groupmember[$courseid] as $groupid){
                        $grouparray[] = $groupid;
                    }
                }
            }
            if(empty($grouparray)) {
                $group = false;
            }
            else {
                $group = $grouparray;
            }
        }
        
    }
    else {
        $group = false;
    }
}

function calendar_edit_event_allowed($event) {
    global $USER;

    if(empty($USER->id) || isguest($USER->id)) {
        return false;
    }

    if (isadmin($USER->id)) return true; // Admins are allowed anything

    if ($event->courseid != 0 && isteacher($event->courseid)) {
        return true;
    } else if($event->courseid == 0 && $event->groupid != 0) {
        // Group event
        $group = get_record('groups', 'id', $event->groupid);
        if($group === false) {
            return false;
        }
        return isteacheredit($group->courseid) || isteacher($group->courseid) && ismember($event->groupid);
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

    if(empty($USER->id)) {
        return array();
    }

    $courses = array();
    if(isadmin($USER->id)) {
        if(!empty($CFG->calendar_adminseesall)) {
            $courses = get_records_sql('SELECT id, 1 FROM '.$CFG->prefix.'course');
            return $courses;
        }
    }
    if(isset($USER->student) && is_array($USER->student)) {
        $courses = $courses + $USER->student;
    }
    if(isset($USER->teacher) && is_array($USER->teacher)) {
        $courses = $courses + $USER->teacher;
    }
    return $courses;
}

function calendar_preferences_button() {
    global $CFG, $USER;

    // Guests have no preferences
    if (empty($USER->id) || isguest()) {
        return '';
    }

    return "<form target=\"$CFG->framename\" method=\"get\" ".
           " action=\"$CFG->wwwroot/calendar/preferences.php\">".
           "<input type=\"submit\" value=\"".get_string("preferences", "calendar")." ...\" /></form>";
}

function calendar_format_event_time($event, $now, $morehref, $usecommonwords = true) {
    $startdate = usergetdate($event->timestart);
    $enddate = usergetdate($event->timestart + $event->timeduration);
    $usermidnightstart = usergetmidnight($event->timestart);

    if($event->timeduration) {
        // To avoid doing the math if one IF is enough :)
        $usermidnightend = usergetmidnight($event->timestart + $event->timeduration);
    }
    else {
        $usermidnightend = $usermidnightstart;
    }

    // OK, now to get a meaningful display...
    // First of all we have to construct a human-readable date/time representation

    if($event->timestart + $event->timeduration < $now) {
        // It has expired, so we don't care about duration
        $day = calendar_day_representation($event->timestart + $event->timeduration, $now, $usecommonwords);
        $time = calendar_time_representation($event->timestart + $event->timeduration);

        // This var always has the printable time representation
        $eventtime = '<span class="dimmed_text"><a class="dimmed" href="'.calendar_get_link_href(CALENDAR_URL.'view.php?view=day'.$morehref.'&amp;', $enddate['mday'], $enddate['mon'], $enddate['year']).'">'.$day.'</a> ('.$time.')</span>';

    }
    else if($event->timeduration) {
        // It has a duration
        if($usermidnightstart == $usermidnightend) {
            // But it's all on the same day
            $day = calendar_day_representation($event->timestart, $now, $usecommonwords);
            $timestart = calendar_time_representation($event->timestart);
            $timeend = calendar_time_representation($event->timestart + $event->timeduration);

            // Set printable representation
            $eventtime = calendar_get_link_tag($day, CALENDAR_URL.'view.php?view=day'.$morehref.'&amp;', $enddate['mday'], $enddate['mon'], $enddate['year']).
                ' ('.$timestart.' <strong>&raquo;</strong> '.$timeend.')';
        }
        else {
            // It spans two or more days
            $daystart = calendar_day_representation($event->timestart, $now, $usecommonwords);
            $dayend = calendar_day_representation($event->timestart + $event->timeduration, $now, $usecommonwords);
            $timestart = calendar_time_representation($event->timestart);
            $timeend = calendar_time_representation($event->timestart + $event->timeduration);

            // Set printable representation
            $eventtime = calendar_get_link_tag($daystart, CALENDAR_URL.'view.php?view=day'.$morehref.'&amp;', $startdate['mday'], $startdate['mon'], $startdate['year']).
                ' ('.$timestart.') <strong>&raquo;</strong> '.calendar_get_link_tag($dayend, CALENDAR_URL.'view.php?view=day'.$morehref.'&amp;', $enddate['mday'], $enddate['mon'], $enddate['year']).
                ' ('.$timeend.')';
        }
    }
    else {
        // It's an "instantaneous" event
        $day = calendar_day_representation($event->timestart, $now, $usecommonwords);
        $time = calendar_time_representation($event->timestart);

        // Set printable representation
        $eventtime = calendar_get_link_tag($day, CALENDAR_URL.'view.php?view=day'.$morehref.'&amp;', $startdate['mday'], $startdate['mon'], $startdate['year']).' ('.$time.')';
    }

    return $eventtime;
}

function calendar_print_month_selector($name, $selected) {
    
    $months = array();

    for ($i=1; $i<=12; $i++) {
        $months[$i] = userdate(gmmktime(12, 0, 0, $i, 1, 2000), '%B');
    }

    choose_from_menu($months, $name, $selected, '');
}

function calendar_get_filters_status() {
    global $SESSION;

    $status = 0;
    if($SESSION->cal_show_global) {
        $status += 1;
    }
    if($SESSION->cal_show_course) {
        $status += 2;
    }
    if($SESSION->cal_show_groups) {
        $status += 4;
    }
    if($SESSION->cal_show_user) {
        $status += 8;
    }
    return $status;
}

function calendar_set_filters_status($packed_bitfield) {
    global $SESSION, $USER;

    if(!isset($USER) || empty($USER->id)) {
        return false;
    }

    $SESSION->cal_show_global = ($packed_bitfield & 1);
    $SESSION->cal_show_course = ($packed_bitfield & 2);
    $SESSION->cal_show_groups = ($packed_bitfield & 4);    
    $SESSION->cal_show_user   = ($packed_bitfield & 8);

    return true;
}

?>
