<?php

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
define('CALENDAR_URL', $CFG->wwwroot.'/calendar/');
define('CALENDAR_TF_24', '%H:%M');
define('CALENDAR_TF_12', '%I:%M %p');

define('CALENDAR_EVENT_GLOBAL', 1);
define('CALENDAR_EVENT_COURSE', 2);
define('CALENDAR_EVENT_GROUP', 4);
define('CALENDAR_EVENT_USER', 8);

/**
 * CALENDAR_STARTING_WEEKDAY has since been deprecated please call calendar_get_starting_weekday() instead
 * @deprecated
 */
define('CALENDAR_STARTING_WEEKDAY', CALENDAR_DEFAULT_STARTING_WEEKDAY);

/**
 * Return the days of the week
 *
 * @return array
 */
function calendar_get_days() {
    return array('sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday');
}

/**
 * Gets the first day of the week
 *
 * Used to be define('CALENDAR_STARTING_WEEKDAY', blah);
 *
 * @return int
 */
function calendar_get_starting_weekday() {
    global $CFG;

    if (isset($CFG->calendar_startwday)) {
        $firstday = $CFG->calendar_startwday;
    } else {
        $firstday = get_string('firstdayofweek', 'langconfig');
    }

    if(!is_numeric($firstday)) {
        return CALENDAR_DEFAULT_STARTING_WEEKDAY;
    } else {
        return intval($firstday) % 7;
    }
}

/**
 * Generates the HTML for a miniature calendar
 *
 * @global core_renderer $OUTPUT
 * @param array $courses
 * @param array $groups
 * @param array $users
 * @param int $cal_month
 * @param int $cal_year
 * @return string
 */
function calendar_get_mini($courses, $groups, $users, $cal_month = false, $cal_year = false) {
    global $CFG, $USER, $OUTPUT;

    $display = new stdClass;
    $display->minwday = get_user_preferences('calendar_startwday', calendar_get_starting_weekday());
    $display->maxwday = $display->minwday + 6;

    $content = '';

    if(!empty($cal_month) && !empty($cal_year)) {
        $thisdate = usergetdate(time()); // Date and time the user sees at his location
        if($cal_month == $thisdate['mon'] && $cal_year == $thisdate['year']) {
            // Navigated to this month
            $date = $thisdate;
            $display->thismonth = true;
        } else {
            // Navigated to other month, let's do a nice trick and save us a lot of work...
            if(!checkdate($cal_month, 1, $cal_year)) {
                $date = array('mday' => 1, 'mon' => $thisdate['mon'], 'year' => $thisdate['year']);
                $display->thismonth = true;
            } else {
                $date = array('mday' => 1, 'mon' => $cal_month, 'year' => $cal_year);
                $display->thismonth = false;
            }
        }
    } else {
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
    $events = calendar_get_events(
        usertime($display->tstart) - dst_offset_on($display->tstart),
        usertime($display->tend) - dst_offset_on($display->tend),
        $users, $groups, $courses);

    // Set event course class for course events
    if (!empty($events)) {
        foreach ($events as $eventid => $event) {
            if (!empty($event->modulename)) {
                $cm = get_coursemodule_from_instance($event->modulename, $event->instance);
                if (!groups_course_module_visible($cm)) {
                    unset($events[$eventid]);
                }
            }
        }
    }

    // This is either a genius idea or an idiot idea: in order to not complicate things, we use this rule: if, after
    // possibly removing SITEID from $courses, there is only one course left, then clicking on a day in the month
    // will also set the $SESSION->cal_courses_shown variable to that one course. Otherwise, we 'd need to add extra
    // arguments to this function.

    $hrefparams = array();
    if(!empty($courses)) {
        $courses = array_diff($courses, array(SITEID));
        if(count($courses) == 1) {
            $hrefparams['course'] = reset($courses);
        }
    }

    // We want to have easy access by day, since the display is on a per-day basis.
    // Arguments passed by reference.
    //calendar_events_by_day($events, $display->tstart, $eventsbyday, $durationbyday, $typesbyday);
    calendar_events_by_day($events, $m, $y, $eventsbyday, $durationbyday, $typesbyday, $courses);

    //Accessibility: added summary and <abbr> elements.
    $days_title = calendar_get_days();

    $summary = get_string('calendarheading', 'calendar', userdate(make_timestamp($y, $m), get_string('strftimemonthyear')));
    $summary = get_string('tabledata', 'access', $summary);
    $content .= '<table class="minicalendar calendartable" summary="'.$summary.'">'; // Begin table
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
        $content .= '<td class="dayblank">&nbsp;</td>'."\n";
    }

    $weekend = CALENDAR_DEFAULT_WEEKEND;
    if (isset($CFG->calendar_weekend)) {
        $weekend = intval($CFG->calendar_weekend);
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
        if ($weekend & (1 << ($dayweek % 7))) {
            // Weekend. This is true no matter what the exact range is.
            $class = 'weekend day';
        } else {
            // Normal working day.
            $class = 'day';
        }

        // Special visual fx if an event is defined
        if(isset($eventsbyday[$day])) {
            $class .= ' hasevent';
            $hrefparams['view'] = 'day';
            $dayhref = calendar_get_link_href(new moodle_url(CALENDAR_URL.'view.php', $hrefparams), $day, $m, $y);

            $popupcontent = '';
            foreach($eventsbyday[$day] as $eventid) {
                if (!isset($events[$eventid])) {
                    continue;
                }
                $event = $events[$eventid];
                $popupalt  = '';
                $component = 'moodle';
                if(!empty($event->modulename)) {
                    $popupicon = 'icon';
                    $popupalt  = $event->modulename;
                    $component = $event->modulename;
                } else if ($event->courseid == SITEID) {                                // Site event
                    $popupicon = 'c/site';
                } else if ($event->courseid != 0 && $event->courseid != SITEID && $event->groupid == 0) {      // Course event
                    $popupicon = 'c/course';
                } else if ($event->groupid) {                                      // Group event
                    $popupicon = 'c/group';
                } else if ($event->userid) {                                       // User event
                    $popupicon = 'c/user';
                }

                $dayhref->set_anchor('event_'.$event->id);

                $popupcontent .= html_writer::start_tag('div');
                $popupcontent .= $OUTPUT->pix_icon($popupicon, $popupalt, $component);
                $popupcontent .= html_writer::link($dayhref, format_string($event->name, true));
                $popupcontent .= html_writer::end_tag('div');
            }

            //Accessibility: functionality moved to calendar_get_popup.
            if($display->thismonth && $day == $d) {
                $popup = calendar_get_popup(true, $events[$eventid]->timestart, $popupcontent);
            } else {
                $popup = calendar_get_popup(false, $events[$eventid]->timestart, $popupcontent);
            }

            // Class and cell content
            if(isset($typesbyday[$day]['startglobal'])) {
                $class .= ' calendar_event_global';
            } else if(isset($typesbyday[$day]['startcourse'])) {
                $class .= ' calendar_event_course';
            } else if(isset($typesbyday[$day]['startgroup'])) {
                $class .= ' calendar_event_group';
            } else if(isset($typesbyday[$day]['startuser'])) {
                $class .= ' calendar_event_user';
            }
            $cell = '<a href="'.(string)$dayhref.'" '.$popup.'>'.$day.'</a>';
        } else {
            $cell = $day;
        }

        $durationclass = false;
        if (isset($typesbyday[$day]['durationglobal'])) {
            $durationclass = ' duration_global';
        } else if(isset($typesbyday[$day]['durationcourse'])) {
            $durationclass = ' duration_course';
        } else if(isset($typesbyday[$day]['durationgroup'])) {
            $durationclass = ' duration_group';
        } else if(isset($typesbyday[$day]['durationuser'])) {
            $durationclass = ' duration_user';
        }
        if ($durationclass) {
            $class .= ' duration '.$durationclass;
        }

        // If event has a class set then add it to the table day <td> tag
        // Note: only one colour for minicalendar
        if(isset($eventsbyday[$day])) {
            foreach($eventsbyday[$day] as $eventid) {
                if (!isset($events[$eventid])) {
                    continue;
                }
                $event = $events[$eventid];
                if (!empty($event->class)) {
                    $class .= ' '.$event->class;
                }
                break;
            }
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
            $cell = get_accesshide($today.' ').$cell;
        }

        // Just display it
        if(!empty($class)) {
            $class = ' class="'.$class.'"';
        }
        $content .= '<td'.$class.'>'.$cell."</td>\n";
    }

    // Paddding (the last week may have blank days at the end)
    for($i = $dayweek; $i <= $display->maxwday; ++$i) {
        $content .= '<td class="dayblank">&nbsp;</td>';
    }
    $content .= '</tr>'; // Last row ends

    $content .= '</table>'; // Tabular display of days ends

    return $content;
}

/**
 * calendar_get_popup, called at multiple points in from calendar_get_mini.
 *        Copied and modified from calendar_get_mini.
 * @global moodle_page $PAGE
 * @param $is_today bool, false except when called on the current day.
 * @param $event_timestart mixed, $events[$eventid]->timestart, OR false if there are no events.
 * @param $popupcontent string.
 * @return $popup string, contains onmousover and onmouseout events.
 */
function calendar_get_popup($is_today, $event_timestart, $popupcontent='') {
    global $PAGE;
    static $popupcount;
    if ($popupcount === null) {
        $popupcount = 1;
    }
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
    $id = 'calendar_tooltip_'.$popupcount;
    $PAGE->requires->yui_module('moodle-calendar-eventmanager', 'M.core_calendar.add_event', array(array('eventId'=>$id,'title'=>$popupcaption, 'content'=>$popupcontent)));

    $popupcount++;
    return 'id="'.$id.'"';
}

function calendar_get_upcoming($courses, $groups, $users, $daysinfuture, $maxevents, $fromtime=0) {
    global $CFG, $COURSE, $DB;

    $display = new stdClass;
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
    $events = calendar_get_events($display->tstart, $display->tend, $users, $groups, $courses);

    // This is either a genius idea or an idiot idea: in order to not complicate things, we use this rule: if, after
    // possibly removing SITEID from $courses, there is only one course left, then clicking on a day in the month
    // will also set the $SESSION->cal_courses_shown variable to that one course. Otherwise, we 'd need to add extra
    // arguments to this function.

    $hrefparams = array();
    if(!empty($courses)) {
        $courses = array_diff($courses, array(SITEID));
        if(count($courses) == 1) {
            $hrefparams['course'] = reset($courses);
        }
    }

    if ($events !== false) {

        $modinfo =& get_fast_modinfo($COURSE);

        foreach($events as $event) {


            if (!empty($event->modulename)) {
                if ($event->courseid == $COURSE->id) {
                    if (isset($modinfo->instances[$event->modulename][$event->instance])) {
                        $cm = $modinfo->instances[$event->modulename][$event->instance];
                        if (!$cm->uservisible) {
                            continue;
                        }
                    }
                } else {
                    if (!$cm = get_coursemodule_from_instance($event->modulename, $event->instance)) {
                        continue;
                    }
                    if (!coursemodule_visible_for_user($cm)) {
                        continue;
                    }
                }
                if ($event->modulename == 'assignment'){
                    // create calendar_event to test edit_event capability
                    // this new event will also prevent double creation of calendar_event object
                    $checkevent = new calendar_event($event);
                    // TODO: rewrite this hack somehow
                    if (!calendar_edit_event_allowed($checkevent)){ // cannot manage entries, eg. student
                        if (!$assignment = $DB->get_record('assignment', array('id'=>$event->instance))) {
                            // print_error("invalidid", 'assignment');
                            continue;
                        }
                        // assign assignment to assignment object to use hidden_is_hidden method
                        require_once($CFG->dirroot.'/mod/assignment/lib.php');

                        if (!file_exists($CFG->dirroot.'/mod/assignment/type/'.$assignment->assignmenttype.'/assignment.class.php')) {
                            continue;
                        }
                        require_once ($CFG->dirroot.'/mod/assignment/type/'.$assignment->assignmenttype.'/assignment.class.php');

                        $assignmentclass = 'assignment_'.$assignment->assignmenttype;
                        $assignmentinstance = new $assignmentclass($cm->id, $assignment, $cm);

                        if ($assignmentinstance->description_is_hidden()){//force not to show description before availability
                            $event->description = get_string('notavailableyet', 'assignment');
                        }
                    }
                }
            }

            if ($processed >= $display->maxevents) {
                break;
            }

            $event->time = calendar_format_event_time($event, $now, $hrefparams);
            $output[] = $event;
            ++$processed;
        }
    }
    return $output;
}

function calendar_add_event_metadata($event) {
    global $CFG, $OUTPUT;

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
        if (get_string_manager()->string_exists($event->eventtype, $event->modulename)) {
            // will be used as alt text if the event icon
            $eventtype = get_string($event->eventtype, $event->modulename);
        } else {
            $eventtype = '';
        }
        $icon = $OUTPUT->pix_url('icon', $event->modulename) . '';

        $context = get_context_instance(CONTEXT_COURSE, $module->course);
        $fullname = format_string($coursecache[$module->course]->fullname, true, array('context' => $context));

        $event->icon = '<img height="16" width="16" src="'.$icon.'" alt="'.$eventtype.'" title="'.$modulename.'" style="vertical-align: middle;" />';
        $event->referer = '<a href="'.$CFG->wwwroot.'/mod/'.$event->modulename.'/view.php?id='.$module->id.'">'.$event->name.'</a>';
        $event->courselink = '<a href="'.$CFG->wwwroot.'/course/view.php?id='.$module->course.'">'.$fullname.'</a>';
        $event->cmid = $module->id;


    } else if($event->courseid == SITEID) {                              // Site event
        $event->icon = '<img height="16" width="16" src="'.$OUTPUT->pix_url('c/site') . '" alt="'.get_string('globalevent', 'calendar').'" style="vertical-align: middle;" />';
        $event->cssclass = 'calendar_event_global';
    } else if($event->courseid != 0 && $event->courseid != SITEID && $event->groupid == 0) {          // Course event
        calendar_get_course_cached($coursecache, $event->courseid);

        $context = get_context_instance(CONTEXT_COURSE, $event->courseid);
        $fullname = format_string($coursecache[$event->courseid]->fullname, true, array('context' => $context));

        $event->icon = '<img height="16" width="16" src="'.$OUTPUT->pix_url('c/course') . '" alt="'.get_string('courseevent', 'calendar').'" style="vertical-align: middle;" />';
        $event->courselink = '<a href="'.$CFG->wwwroot.'/course/view.php?id='.$event->courseid.'">'.$fullname.'</a>';
        $event->cssclass = 'calendar_event_course';
    } else if ($event->groupid) {                                    // Group event
        $event->icon = '<img height="16" width="16" src="'.$OUTPUT->pix_url('c/group') . '" alt="'.get_string('groupevent', 'calendar').'" style="vertical-align: middle;" />';
        $event->cssclass = 'calendar_event_group';
    } else if($event->userid) {                                      // User event
        $event->icon = '<img height="16" width="16" src="'.$OUTPUT->pix_url('c/user') . '" alt="'.get_string('userevent', 'calendar').'" style="vertical-align: middle;" />';
        $event->cssclass = 'calendar_event_user';
    }
    return $event;
}

/**
 * Prints a calendar event
 *
 * @deprecated 2.0
 */
function calendar_print_event($event, $showactions=true) {
    global $CFG, $USER, $OUTPUT, $PAGE;
    debugging('calendar_print_event is deprecated please update your code', DEBUG_DEVELOPER);
    $renderer = $PAGE->get_renderer('core_calendar');
    if (!($event instanceof calendar_event)) {
        $event = new calendar_event($event);
    }
    echo $renderer->event($event);
}

/**
 * Get calendar events
 * @param int $tstart Start time of time range for events
 * @param int $tend   End time of time range for events
 * @param array/int/boolean $users array of users, user id or boolean for all/no user events
 * @param array/int/boolean $groups array of groups, group id or boolean for all/no group events
 * @param array/int/boolean $courses array of courses, course id or boolean for all/no course events
 * @param boolean $withduration whether only events starting within time range selected
 *                              or events in progress/already started selected as well
 * @param boolean $ignorehidden whether to select only visible events or all events
 * @return array of selected events or an empty array if there aren't any (or there was an error)
 */
function calendar_get_events($tstart, $tend, $users, $groups, $courses, $withduration=true, $ignorehidden=true) {
    global $DB;

    $whereclause = '';
    // Quick test
    if(is_bool($users) && is_bool($groups) && is_bool($courses)) {
        return array();
    }

    if(is_array($users) && !empty($users)) {
        // Events from a number of users
        if(!empty($whereclause)) $whereclause .= ' OR';
        $whereclause .= ' (userid IN ('.implode(',', $users).') AND courseid = 0 AND groupid = 0)';
    } else if(is_numeric($users)) {
        // Events from one user
        if(!empty($whereclause)) $whereclause .= ' OR';
        $whereclause .= ' (userid = '.$users.' AND courseid = 0 AND groupid = 0)';
    } else if($users === true) {
        // Events from ALL users
        if(!empty($whereclause)) $whereclause .= ' OR';
        $whereclause .= ' (userid != 0 AND courseid = 0 AND groupid = 0)';
    } else if($users === false) {
        // No user at all, do nothing
    }

    if(is_array($groups) && !empty($groups)) {
        // Events from a number of groups
        if(!empty($whereclause)) $whereclause .= ' OR';
        $whereclause .= ' groupid IN ('.implode(',', $groups).')';
    } else if(is_numeric($groups)) {
        // Events from one group
        if(!empty($whereclause)) $whereclause .= ' OR ';
        $whereclause .= ' groupid = '.$groups;
    } else if($groups === true) {
        // Events from ALL groups
        if(!empty($whereclause)) $whereclause .= ' OR ';
        $whereclause .= ' groupid != 0';
    }
    // boolean false (no groups at all): we don't need to do anything

    if(is_array($courses) && !empty($courses)) {
        if(!empty($whereclause)) {
            $whereclause .= ' OR';
        }
        $whereclause .= ' (groupid = 0 AND courseid IN ('.implode(',', $courses).'))';
    } else if(is_numeric($courses)) {
        // One course
        if(!empty($whereclause)) $whereclause .= ' OR';
        $whereclause .= ' (groupid = 0 AND courseid = '.$courses.')';
    } else if ($courses === true) {
        // Events from ALL courses
        if(!empty($whereclause)) $whereclause .= ' OR';
        $whereclause .= ' (groupid = 0 AND courseid != 0)';
    }

    // Security check: if, by now, we have NOTHING in $whereclause, then it means
    // that NO event-selecting clauses were defined. Thus, we won't be returning ANY
    // events no matter what. Allowing the code to proceed might return a completely
    // valid query with only time constraints, thus selecting ALL events in that time frame!
    if(empty($whereclause)) {
        return array();
    }

    if($withduration) {
        $timeclause = '(timestart >= '.$tstart.' OR timestart + timeduration > '.$tstart.') AND timestart <= '.$tend;
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

    $events = $DB->get_records_select('event', $whereclause, null, 'timestart');
    if ($events === false) {
        $events = array();
    }
    return $events;
}

function calendar_top_controls($type, $data) {
    global $CFG;
    $content = '';
    if(!isset($data['d'])) {
        $data['d'] = 1;
    }

    // Ensure course id passed if relevant
    // Required due to changes in view/lib.php mainly (calendar_session_vars())
    $courseid = '';
    if (!empty($data['id'])) {
        $courseid = '&amp;course='.$data['id'];
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
    //$nexttext = link_arrow_right(get_string('monthnext', 'access'), $url='', $accesshide=true);
    //$prevtext = link_arrow_left(get_string('monthprev', 'access'), $url='', $accesshide=true);

    switch($type) {
        case 'frontpage':
            list($prevmonth, $prevyear) = calendar_sub_month($data['m'], $data['y']);
            list($nextmonth, $nextyear) = calendar_add_month($data['m'], $data['y']);
            $nextlink = calendar_get_link_next(get_string('monthnext', 'access'), 'index.php?', 0, $nextmonth, $nextyear, $accesshide=true);
            $prevlink = calendar_get_link_previous(get_string('monthprev', 'access'), 'index.php?', 0, $prevmonth, $prevyear, true);

            $calendarlink = calendar_get_link_href(new moodle_url(CALENDAR_URL.'view.php', array('view'=>'month')), 1, $data['m'], $data['y']);
            if (!empty($data['id'])) {
                $calendarlink->param('course', $data['id']);
            }

            if (right_to_left()) {
                $left = $nextlink;
                $right = $prevlink;
            } else {
                $left = $prevlink;
                $right = $nextlink;
            }

            $content .= html_writer::start_tag('div', array('class'=>'calendar-controls'));
            $content .= $left.'<span class="hide"> | </span>';
            $content .= html_writer::tag('span', html_writer::link($calendarlink, userdate($time, get_string('strftimemonthyear')), array('title'=>get_string('monththis','calendar'))), array('class'=>'current'));
            $content .= '<span class="hide"> | </span>'. $right;
            $content .= "<span class=\"clearer\"><!-- --></span>\n";
            $content .= html_writer::end_tag('div');

            break;
        case 'course':
            list($prevmonth, $prevyear) = calendar_sub_month($data['m'], $data['y']);
            list($nextmonth, $nextyear) = calendar_add_month($data['m'], $data['y']);
            $nextlink = calendar_get_link_next(get_string('monthnext', 'access'), 'view.php?id='.$data['id'].'&amp;', 0, $nextmonth, $nextyear, $accesshide=true);
            $prevlink = calendar_get_link_previous(get_string('monthprev', 'access'), 'view.php?id='.$data['id'].'&amp;', 0, $prevmonth, $prevyear, true);

            $calendarlink = calendar_get_link_href(new moodle_url(CALENDAR_URL.'view.php', array('view'=>'month')), 1, $data['m'], $data['y']);
            if (!empty($data['id'])) {
                $calendarlink->param('course', $data['id']);
            }

            if (right_to_left()) {
                $left = $nextlink;
                $right = $prevlink;
            } else {
                $left = $prevlink;
                $right = $nextlink;
            }

            $content .= html_writer::start_tag('div', array('class'=>'calendar-controls'));
            $content .= $left.'<span class="hide"> | </span>';
            $content .= html_writer::tag('span', html_writer::link($calendarlink, userdate($time, get_string('strftimemonthyear')), array('title'=>get_string('monththis','calendar'))), array('class'=>'current'));
            $content .= '<span class="hide"> | </span>'. $right;
            $content .= "<span class=\"clearer\"><!-- --></span>";
            $content .= html_writer::end_tag('div');
            break;
        case 'upcoming':
            $calendarlink = calendar_get_link_href(new moodle_url(CALENDAR_URL.'view.php', array('view'=>'upcoming')), 1, $data['m'], $data['y']);
            if (!empty($data['id'])) {
                $calendarlink->param('course', $data['id']);
            }
            $calendarlink = html_writer::link($calendarlink, userdate($time, get_string('strftimemonthyear')));
            $content .= html_writer::tag('div', $calendarlink, array('class'=>'centered'));
            break;
        case 'display':
            $calendarlink = calendar_get_link_href(new moodle_url(CALENDAR_URL.'view.php', array('view'=>'month')), 1, $data['m'], $data['y']);
            if (!empty($data['id'])) {
                $calendarlink->param('course', $data['id']);
            }
            $calendarlink = html_writer::link($calendarlink, userdate($time, get_string('strftimemonthyear')));
            $content .= html_writer::tag('h3', $calendarlink);
            break;
        case 'month':
            list($prevmonth, $prevyear) = calendar_sub_month($data['m'], $data['y']);
            list($nextmonth, $nextyear) = calendar_add_month($data['m'], $data['y']);
            $prevdate = make_timestamp($prevyear, $prevmonth, 1);
            $nextdate = make_timestamp($nextyear, $nextmonth, 1);
            $prevlink = calendar_get_link_previous(userdate($prevdate, get_string('strftimemonthyear')), 'view.php?view=month'.$courseid.'&amp;', 1, $prevmonth, $prevyear);
            $nextlink = calendar_get_link_next(userdate($nextdate, get_string('strftimemonthyear')), 'view.php?view=month'.$courseid.'&amp;', 1, $nextmonth, $nextyear);

            if (right_to_left()) {
                $left = $nextlink;
                $right = $prevlink;
            } else {
                $left = $prevlink;
                $right = $nextlink;
            }

            $content .= html_writer::start_tag('div', array('class'=>'calendar-controls'));
            $content .= $left . '<span class="hide"> | </span><h1 class="current">'.userdate($time, get_string('strftimemonthyear'))."</h1>";
            $content .= '<span class="hide"> | </span>' . $right;
            $content .= '<span class="clearer"><!-- --></span>';
            $content .= html_writer::end_tag('div')."\n";
            break;
        case 'day':
            $days = calendar_get_days();
            $data['d'] = $date['mday']; // Just for convenience
            $prevdate = usergetdate(make_timestamp($data['y'], $data['m'], $data['d'] - 1));
            $nextdate = usergetdate(make_timestamp($data['y'], $data['m'], $data['d'] + 1));
            $prevname = calendar_wday_name($days[$prevdate['wday']]);
            $nextname = calendar_wday_name($days[$nextdate['wday']]);
            $prevlink = calendar_get_link_previous($prevname, 'view.php?view=day'.$courseid.'&amp;', $prevdate['mday'], $prevdate['mon'], $prevdate['year']);
            $nextlink = calendar_get_link_next($nextname, 'view.php?view=day'.$courseid.'&amp;', $nextdate['mday'], $nextdate['mon'], $nextdate['year']);

            if (right_to_left()) {
                $left = $nextlink;
                $right = $prevlink;
            } else {
                $left = $prevlink;
                $right = $nextlink;
            }

            $content .= html_writer::start_tag('div', array('class'=>'calendar-controls'));
            $content .= $left;
            $content .= '<span class="hide"> | </span><span class="current">'.userdate($time, get_string('strftimedaydate')).'</span>';
            $content .= '<span class="hide"> | </span>'. $right;
            $content .= "<span class=\"clearer\"><!-- --></span>";
            $content .= html_writer::end_tag('div')."\n";

            break;
    }
    return $content;
}

function calendar_filter_controls(moodle_url $returnurl) {
    global $CFG, $USER, $OUTPUT;

    $groupevents = true;

    $id = optional_param( 'id',0,PARAM_INT );

    $seturl = new moodle_url('/calendar/set.php', array('return' => base64_encode($returnurl->out(false)), 'sesskey'=>sesskey()));

    $content = '<table>';
    $content .= '<tr>';

    $seturl->param('var', 'showglobal');
    if (calendar_show_event_type(CALENDAR_EVENT_GLOBAL)) {
        $content .= '<td class="eventskey calendar_event_global" style="width: 11px;"><img src="'.$OUTPUT->pix_url('t/hide') . '" class="iconsmall" alt="'.get_string('hide').'" title="'.get_string('tt_hideglobal', 'calendar').'" style="cursor:pointer" onclick="location.href='."'".$seturl."'".'" /></td>';
        $content .= '<td><a href="'.$seturl.'" title="'.get_string('tt_hideglobal', 'calendar').'">'.get_string('global', 'calendar').'</a></td>'."\n";
    } else {
        $content .= '<td style="width: 11px;"><img src="'.$OUTPUT->pix_url('t/show') . '" class="iconsmall" alt="'.get_string('show').'" title="'.get_string('tt_showglobal', 'calendar').'" style="cursor:pointer" onclick="location.href='."'".$seturl."'".'" /></td>';
        $content .= '<td><a href="'.$seturl.'" title="'.get_string('tt_showglobal', 'calendar').'">'.get_string('global', 'calendar').'</a></td>'."\n";
    }

    $seturl->param('var', 'showcourses');
    if (calendar_show_event_type(CALENDAR_EVENT_COURSE)) {
        $content .= '<td class="eventskey calendar_event_course" style="width: 11px;"><img src="'.$OUTPUT->pix_url('t/hide') . '" class="iconsmall" alt="'.get_string('hide').'" title="'.get_string('tt_hidecourse', 'calendar').'" style="cursor:pointer" onclick="location.href='."'".$seturl."'".'" /></td>';
        $content .= '<td><a href="'.$seturl.'" title="'.get_string('tt_hidecourse', 'calendar').'">'.get_string('course', 'calendar').'</a></td>'."\n";
    } else {
        $content .= '<td style="width: 11px;"><img src="'.$OUTPUT->pix_url('t/show') . '" class="iconsmall" alt="'.get_string('hide').'" title="'.get_string('tt_showcourse', 'calendar').'" style="cursor:pointer" onclick="location.href='."'".$seturl."'".'" /></td>';
        $content .= '<td><a href="'.$seturl.'" title="'.get_string('tt_showcourse', 'calendar').'">'.get_string('course', 'calendar').'</a></td>'."\n";
    }

    if (isloggedin() && !isguestuser()) {
        $content .= "</tr>\n<tr>";

        if ($groupevents) {
            // This course MIGHT have group events defined, so show the filter
            $seturl->param('var', 'showgroups');
            if (calendar_show_event_type(CALENDAR_EVENT_GROUP)) {
                $content .= '<td class="eventskey calendar_event_group" style="width: 11px;"><img src="'.$OUTPUT->pix_url('t/hide') . '" class="iconsmall" alt="'.get_string('hide').'" title="'.get_string('tt_hidegroups', 'calendar').'" style="cursor:pointer" onclick="location.href='."'".$seturl."'".'" /></td>';
                $content .= '<td><a href="'.$seturl.'" title="'.get_string('tt_hidegroups', 'calendar').'">'.get_string('group', 'calendar').'</a></td>'."\n";
            } else {
                $content .= '<td style="width: 11px;"><img src="'.$OUTPUT->pix_url('t/show') . '" class="iconsmall" alt="'.get_string('show').'" title="'.get_string('tt_showgroups', 'calendar').'" style="cursor:pointer" onclick="location.href='."'".$seturl."'".'" /></td>';
                $content .= '<td><a href="'.$seturl.'" title="'.get_string('tt_showgroups', 'calendar').'">'.get_string('group', 'calendar').'</a></td>'."\n";
            }
        } else {
            // This course CANNOT have group events, so lose the filter
            $content .= '<td style="width: 11px;"></td><td>&nbsp;</td>'."\n";
        }

        $seturl->param('var', 'showuser');
        if (calendar_show_event_type(CALENDAR_EVENT_USER)) {
            $content .= '<td class="eventskey calendar_event_user" style="width: 11px;"><img src="'.$OUTPUT->pix_url('t/hide') . '" class="iconsmall" alt="'.get_string('hide').'" title="'.get_string('tt_hideuser', 'calendar').'" style="cursor:pointer" onclick="location.href='."'".$seturl."'".'" /></td>';
            $content .= '<td><a href="'.$seturl.'" title="'.get_string('tt_hideuser', 'calendar').'">'.get_string('user', 'calendar').'</a></td>'."\n";
        } else {
            $content .= '<td style="width: 11px;"><img src="'.$OUTPUT->pix_url('t/show') . '" class="iconsmall" alt="'.get_string('show').'" title="'.get_string('tt_showuser', 'calendar').'" style="cursor:pointer" onclick="location.href='."'".$seturl."'".'" /></td>';
            $content .= '<td><a href="'.$seturl.'" title="'.get_string('tt_showuser', 'calendar').'">'.get_string('user', 'calendar').'</a></td>'."\n";
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
    if(empty($timeformat)){
        $timeformat = get_config(NULL,'calendar_site_timeformat');
    }
    // The ? is needed because the preference might be present, but empty
    return userdate($time, empty($timeformat) ? $langtimeformat : $timeformat);
}

/**
 * Adds day, month, year arguments to a URL and returns a moodle_url object.
 *
 * @param string|moodle_url $linkbase
 * @param int $d
 * @param int $m
 * @param int $y
 * @return moodle_url
 */
function calendar_get_link_href($linkbase, $d, $m, $y) {
    if (empty($linkbase)) {
        return '';
    }
    if (!($linkbase instanceof moodle_url)) {
        $linkbase = new moodle_url();
    }
    if (!empty($d)) {
        $linkbase->param('cal_d', $d);
    }
    if (!empty($m)) {
        $linkbase->param('cal_m', $m);
    }
    if (!empty($y)) {
        $linkbase->param('cal_y', $y);
    }
    return $linkbase;
}

/**
 * This function has been deprecated as of Moodle 2.0... DO NOT USE!!!!!
 *
 * @deprecated
 * @since 2.0
 *
 * @param string $text
 * @param string|moodle_url $linkbase
 * @param int|null $d
 * @param int|null $m
 * @param int|null $y
 * @return string HTML link
 */
function calendar_get_link_tag($text, $linkbase, $d, $m, $y) {
    $url = calendar_get_link_href(new moodle_url($linkbase), $d, $m, $y);
    if (empty($url)) {
        return $text;
    }
    return html_writer::link($url, $text);
}

/**
 * Build and return a previous month HTML link, with an arrow.
 *
 * @param string $text The text label.
 * @param string|moodle_url $linkbase The URL stub.
 * @param int $d $m $y Day of month, month and year numbers.
 * @param bool $accesshide Default visible, or hide from all except screenreaders.
 * @return string HTML string.
 */
function calendar_get_link_previous($text, $linkbase, $d, $m, $y, $accesshide=false) {
    $href = calendar_get_link_href(new moodle_url($linkbase), $d, $m, $y);
    if (empty($href)) {
        return $text;
    }
    return link_arrow_left($text, (string)$href, $accesshide, 'previous');
}

/**
 * Build and return a next month HTML link, with an arrow.
 *
 * @param string $text The text label.
 * @param string|moodle_url $linkbase The URL stub.
 * @param int $d $m $y Day of month, month and year numbers.
 * @param bool $accesshide Default visible, or hide from all except screenreaders.
 * @return string HTML string.
 */
function calendar_get_link_next($text, $linkbase, $d, $m, $y, $accesshide=false) {
    $href = calendar_get_link_href(new moodle_url($linkbase), $d, $m, $y);
    if (empty($href)) {
        return $text;
    }
    return link_arrow_right($text, (string)$href, $accesshide, 'next');
}

function calendar_wday_name($englishname) {
    return get_string(strtolower($englishname), 'calendar');
}

function calendar_days_in_month($month, $year) {
   return intval(date('t', mktime(0, 0, 0, $month, 1, $year)));
}

function calendar_get_block_upcoming($events, $linkhref = NULL) {
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
                $href = calendar_get_link_href(new moodle_url(CALENDAR_URL.$linkhref), $ed['mday'], $ed['mon'], $ed['year']);
                $href->set_anchor('event_'.$events[$i]->id);
                $content .= html_writer::link($href, $events[$i]->name);
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

function calendar_events_by_day($events, $month, $year, &$eventsbyday, &$durationbyday, &$typesbyday, &$courses) {
    $eventsbyday = array();
    $typesbyday = array();
    $durationbyday = array();

    if($events === false) {
        return;
    }

    foreach($events as $event) {

        $startdate = usergetdate($event->timestart);
        // Set end date = start date if no duration
        if ($event->timeduration) {
            $enddate   = usergetdate($event->timestart + $event->timeduration - 1);
        } else {
            $enddate = $startdate;
        }

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
                // Set event class for global event
                $events[$event->id]->class = 'calendar_event_global';
            }
            else if($event->courseid != 0 && $event->courseid != SITEID && $event->groupid == 0) {
                $typesbyday[$eventdaystart]['startcourse'] = true;
                // Set event class for course event
                $events[$event->id]->class = 'calendar_event_course';
            }
            else if($event->groupid) {
                $typesbyday[$eventdaystart]['startgroup'] = true;
                // Set event class for group event
                $events[$event->id]->class = 'calendar_event_group';
            }
            else if($event->userid) {
                $typesbyday[$eventdaystart]['startuser'] = true;
                // Set event class for user event
                $events[$event->id]->class = 'calendar_event_user';
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
    global $COURSE, $DB;

    if (!isset($coursecache[$courseid])) {
        if ($courseid == $COURSE->id) {
            $coursecache[$courseid] = $COURSE;
        } else {
            $coursecache[$courseid] = $DB->get_record('course', array('id'=>$courseid));
        }
    }
    return $coursecache[$courseid];
}

/**
 * Returns the courses to load events for, the
 *
 * @global moodle_database $DB
 * @param array $courseeventsfrom An array of courses to load calendar events for
 * @param bool $ignorefilters
 * @return array An array of courses, groups, and user to load calendar events for based upon filters
 */
function calendar_set_filters(array $courseeventsfrom, $ignorefilters = false) {
    global $USER, $CFG, $DB;

    // For backwards compatability we have to check whether the courses array contains
    // just id's in which case we need to load course objects.
    $coursestoload = array();
    foreach ($courseeventsfrom as $id => $something) {
        if (!is_object($something)) {
            $coursestoload[] = $id;
            unset($courseeventsfrom[$id]);
        }
    }
    if (!empty($coursestoload)) {
        // TODO remove this in 2.2
        debugging('calendar_set_filters now preferes an array of course objects with preloaded contexts', DEBUG_DEVELOPER);
        $courseeventsfrom = array_merge($courseeventsfrom, $DB->get_records_list('course', 'id', $coursestoload));
    }

    $courses = array();
    $user = false;
    $group = false;

    $isloggedin = isloggedin();

    if ($ignorefilters || calendar_show_event_type(CALENDAR_EVENT_COURSE)) {
        $courses = array_keys($courseeventsfrom);
    }
    if ($ignorefilters || calendar_show_event_type(CALENDAR_EVENT_GLOBAL)) {
        $courses[] = SITEID;
    }
    $courses = array_unique($courses);
    sort($courses);

    if (!empty($courses) && in_array(SITEID, $courses)) {
        // Sort courses for consistent colour highlighting
        // Effectively ignoring SITEID as setting as last course id
        $key = array_search(SITEID, $courses);
        unset($courses[$key]);
        $courses[] = SITEID;
    }

    if ($ignorefilters || ($isloggedin && calendar_show_event_type(CALENDAR_EVENT_USER))) {
        $user = $USER->id;
    }

    if (!empty($courseeventsfrom) && (calendar_show_event_type(CALENDAR_EVENT_GROUP) || $ignorefilters)) {

        if (!empty($CFG->calendar_adminseesall) && has_capability('moodle/calendar:manageentries', get_system_context())) {
            $group = true;
        } else if ($isloggedin) {
            $groupids = array();

            // We already have the courses to examine in $courses
            // For each course...
            foreach ($courseeventsfrom as $courseid => $course) {
                // If the user is an editing teacher in there,
                if (!empty($USER->groupmember[$course->id])) {
                    // We've already cached the users groups for this course so we can just use that
                    $groupids = array_merge($groupids, $USER->groupmember[$course->id]);
                } else if (($course->groupmode != NOGROUPS || !$course->groupmodeforce) && has_capability('moodle/calendar:manageentries', get_context_instance(CONTEXT_COURSE, $course->id))) {
                    // If this course has groups, show events from all of them
                    $coursegroups = groups_get_user_groups($course->id, $USER->id);
                    $groupids = array_merge($groupids, $coursegroups['0']);
                }
            }
            if (!empty($groupids)) {
                $group = $groupids;
            }
        }
    }
    if (empty($courses)) {
        $courses = false;
    }

    return array($courses, $group, $user);
}

function calendar_edit_event_allowed($event) {
    global $USER, $DB;

    // Must be logged in
    if (!isloggedin()) {
        return false;
    }

    // can not be using guest account
    if (isguestuser()) {
        return false;
    }

    $sitecontext = get_context_instance(CONTEXT_SYSTEM);
    // if user has manageentries at site level, return true
    if (has_capability('moodle/calendar:manageentries', $sitecontext)) {
        return true;
    }

    // if groupid is set, it's definitely a group event
    if (!empty($event->groupid)) {
        // Allow users to add/edit group events if:
        // 1) They have manageentries (= entries for whole course)
        // 2) They have managegroupentries AND are in the group
        $group = $DB->get_record('groups', array('id'=>$event->groupid));
        return $group && (
            has_capability('moodle/calendar:manageentries', $event->context) ||
            (has_capability('moodle/calendar:managegroupentries', $event->context)
                && groups_is_member($event->groupid)));
    } else if (!empty($event->courseid)) {
    // if groupid is not set, but course is set,
    // it's definiely a course event
        return has_capability('moodle/calendar:manageentries', $event->context);
    } else if (!empty($event->userid) && $event->userid == $USER->id) {
    // if course is not set, but userid id set, it's a user event
        return (has_capability('moodle/calendar:manageownentries', $event->context));
    } else if (!empty($event->userid)) {
        return (has_capability('moodle/calendar:manageentries', $event->context));
    }
    return false;
}

/**
 * Returns the default courses to display on the calendar when there isn't a specific
 * course to display.
 *
 * @global moodle_database $DB
 * @return array Array of courses to display
 */
function calendar_get_default_courses() {
    global $CFG, $DB;

    if (!isloggedin()) {
        return array();
    }

    $courses = array();
    if (!empty($CFG->calendar_adminseesall) && has_capability('moodle/calendar:manageentries', get_context_instance(CONTEXT_SYSTEM))) {
        list ($select, $join) = context_instance_preload_sql('c.id', CONTEXT_COURSE, 'ctx');
        $sql = "SELECT c.* $select
                  FROM {course} c
                  JOIN {event} e ON e.courseid = c.id
                  $join";
        $courses = $DB->get_records_sql($sql, null, 0, 20);
        foreach ($courses as $course) {
            context_instance_preload($course);
        }
        return $courses;
    }

    $courses = enrol_get_my_courses();

    return $courses;
}

function calendar_preferences_button(stdClass $course) {
    global $OUTPUT;

    // Guests have no preferences
    if (!isloggedin() || isguestuser()) {
        return '';
    }

    return $OUTPUT->single_button(new moodle_url('/calendar/preferences.php', array('course' => $course->id)), get_string("preferences", "calendar"));
}

function calendar_format_event_time($event, $now, $linkparams = null, $usecommonwords = true, $showtime=0) {
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

    if (empty($linkparams) || !is_array($linkparams)) {
        $linkparams = array();
    }
    $linkparams['view'] = 'day';

    // OK, now to get a meaningful display...
    // First of all we have to construct a human-readable date/time representation

    if($event->timeduration) {
        // It has a duration
        if($usermidnightstart == $usermidnightend ||
           ($event->timestart == $usermidnightstart) && ($event->timeduration == 86400 || $event->timeduration == 86399) ||
           ($event->timestart + $event->timeduration <= $usermidnightstart + 86400)) {
            // But it's all on the same day
            $timestart = calendar_time_representation($event->timestart);
            $timeend = calendar_time_representation($event->timestart + $event->timeduration);
            $time = $timestart.' <strong>&raquo;</strong> '.$timeend;

            if ($event->timestart == $usermidnightstart && ($event->timeduration == 86400 || $event->timeduration == 86399)) {
                $time = get_string('allday', 'calendar');
            }

            // Set printable representation
            if (!$showtime) {
                $day = calendar_day_representation($event->timestart, $now, $usecommonwords);
                $url = calendar_get_link_href(new moodle_url(CALENDAR_URL.'view.php', $linkparams), $enddate['mday'], $enddate['mon'], $enddate['year']);
                $eventtime = html_writer::link($url, $day).', '.$time;
            } else {
                $eventtime = $time;
            }
        } else {
            // It spans two or more days
            $daystart = calendar_day_representation($event->timestart, $now, $usecommonwords).', ';
            if ($showtime == $usermidnightstart) {
                $daystart = '';
            }
            $timestart = calendar_time_representation($event->timestart);
            $dayend = calendar_day_representation($event->timestart + $event->timeduration, $now, $usecommonwords).', ';
            if ($showtime == $usermidnightend) {
                $dayend = '';
            }
            $timeend = calendar_time_representation($event->timestart + $event->timeduration);

            // Set printable representation
            if ($now >= $usermidnightstart && $now < ($usermidnightstart + 86400)) {
                $url = calendar_get_link_href(new moodle_url(CALENDAR_URL.'view.php', $linkparams), $enddate['mday'], $enddate['mon'], $enddate['year']);
                $eventtime = $timestart.' <strong>&raquo;</strong> '.html_writer::link($url, $dayend).$timeend;
            } else {
                $url = calendar_get_link_href(new moodle_url(CALENDAR_URL.'view.php', $linkparams), $enddate['mday'], $enddate['mon'], $enddate['year']);
                $eventtime  = html_writer::link($url, $daystart).$timestart.' <strong>&raquo;</strong> ';

                $url = calendar_get_link_href(new moodle_url(CALENDAR_URL.'view.php', $linkparams), $startdate['mday'], $startdate['mon'], $startdate['year']);
                $eventtime .= html_writer::link($url, $dayend).$timeend;
            }
        }
    } else {
        $time = ' ';

        // Set printable representation
        if (!$showtime) {
            $day = calendar_day_representation($event->timestart, $now, $usecommonwords);
            $url = calendar_get_link_href(new moodle_url(CALENDAR_URL.'view.php', $linkparams), $startdate['mday'], $startdate['mon'], $startdate['year']);
            $eventtime = html_writer::link($url, $day).trim($time);
        } else {
            $eventtime = $time;
        }
    }

    if($event->timestart + $event->timeduration < $now) {
        // It has expired
        $eventtime = '<span class="dimmed_text">'.str_replace(' href=', ' class="dimmed" href=', $eventtime).'</span>';
    }

    return $eventtime;
}

function calendar_print_month_selector($name, $selected) {
    $months = array();
    for ($i=1; $i<=12; $i++) {
        $months[$i] = userdate(gmmktime(12, 0, 0, $i, 15, 2000), '%B');
    }
    echo html_writer::select($months, $name, $selected, false);
}

/**
 * Checks to see if the requested type of event should be shown for the given user.
 *
 * @param CALENDAR_EVENT_GLOBAL|CALENDAR_EVENT_COURSE|CALENDAR_EVENT_GROUP|CALENDAR_EVENT_USER $type
 *          The type to check the display for (default is to display all)
 * @param stdClass|int|null $user The user to check for - by default the current user
 * @return bool True if the tyep should be displayed false otherwise
 */
function calendar_show_event_type($type, $user = null) {
    $default = CALENDAR_EVENT_GLOBAL + CALENDAR_EVENT_COURSE + CALENDAR_EVENT_GROUP + CALENDAR_EVENT_USER;
    if (get_user_preferences('calendar_persistflt', 0, $user) === 0) {
        global $SESSION;
        if (!isset($SESSION->calendarshoweventtype)) {
            $SESSION->calendarshoweventtype = $default;
        }
        return $SESSION->calendarshoweventtype & $type;
    } else {
        return get_user_preferences('calendar_savedflt', $default, $user) & $type;
    }
}

/**
 * Sets the display of the event type given $display.
 * If $display = true the event type will be shown.
 * If $display = false the event type will NOT be shown.
 * If $display = null the current value will be toggled and saved.
 *
 * @param CALENDAR_EVENT_GLOBAL|CALENDAR_EVENT_COURSE|CALENDAR_EVENT_GROUP|CALENDAR_EVENT_USER $type
 * @param true|false|null $display
 * @param stdClass|int|null $user
 */
function calendar_set_event_type_display($type, $display = null, $user = null) {
    $persist = get_user_preferences('calendar_persistflt', 0, $user);
    $default = CALENDAR_EVENT_GLOBAL + CALENDAR_EVENT_COURSE + CALENDAR_EVENT_GROUP + CALENDAR_EVENT_USER;
    if ($persist === 0) {
        global $SESSION;
        if (!isset($SESSION->calendarshoweventtype)) {
            $SESSION->calendarshoweventtype = $default;
        }
        $preference = $SESSION->calendarshoweventtype;
    } else {
        $preference = get_user_preferences('calendar_savedflt', $default, $user);
    }
    $current = $preference & $type;
    if ($display === null) {
        $display = !$current;
    }
    if ($display && !$current) {
        $preference += $type;
    } else if (!$display && $current) {
        $preference -= $type;
    }
    if ($persist === 0) {
        $SESSION->calendarshoweventtype = $preference;
    } else {
        if ($preference == $default) {
            unset_user_preference('calendar_savedflt', $user);
        } else {
            set_user_preference('calendar_savedflt', $preference, $user);
        }
    }
}

function calendar_get_allowed_types(&$allowed, $course = null) {
    global $USER, $CFG, $DB;
    $allowed->user = has_capability('moodle/calendar:manageownentries', get_system_context());
    $allowed->groups = false; // This may change just below
    $allowed->courses = false; // This may change just below
    $allowed->site = has_capability('moodle/calendar:manageentries', get_context_instance(CONTEXT_COURSE, SITEID));

    if (!empty($course)) {
        if (!is_object($course)) {
            $course = $DB->get_record('course', array('id' => $course), '*', MUST_EXIST);
        }
        if ($course->id != SITEID) {
            $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);

            if (has_capability('moodle/calendar:manageentries', $coursecontext)) {
                $allowed->courses = array($course->id => 1);

                if ($course->groupmode != NOGROUPS || !$course->groupmodeforce) {
                    $allowed->groups = groups_get_all_groups($course->id);
                }
            } else if (has_capability('moodle/calendar:managegroupentries', $coursecontext)) {
                if($course->groupmode != NOGROUPS || !$course->groupmodeforce) {
                    $allowed->groups = groups_get_all_groups($course->id);
                }
            }
        }
    }
}

/**
 * see if user can add calendar entries at all
 * used to print the "New Event" button
 * @return bool
 */
function calendar_user_can_add_event($course) {
    if (!isloggedin() || isguestuser()) {
        return false;
    }
    calendar_get_allowed_types($allowed, $course);
    return (bool)($allowed->user || $allowed->groups || $allowed->courses || $allowed->site);
}

/**
 * Check wether the current user is permitted to add events
 *
 * @param object $event
 * @return bool
 */
function calendar_add_event_allowed($event) {
    global $USER, $DB;

    // can not be using guest account
    if (!isloggedin() or isguestuser()) {
        return false;
    }

    $sitecontext = get_context_instance(CONTEXT_SYSTEM);
    // if user has manageentries at site level, always return true
    if (has_capability('moodle/calendar:manageentries', $sitecontext)) {
        return true;
    }

    switch ($event->eventtype) {
        case 'course':
            return has_capability('moodle/calendar:manageentries', $event->context);

        case 'group':
            // Allow users to add/edit group events if:
            // 1) They have manageentries (= entries for whole course)
            // 2) They have managegroupentries AND are in the group
            $group = $DB->get_record('groups', array('id'=>$event->groupid));
            return $group && (
                has_capability('moodle/calendar:manageentries', $event->context) ||
                (has_capability('moodle/calendar:managegroupentries', $event->context)
                    && groups_is_member($event->groupid)));

        case 'user':
            if ($event->userid == $USER->id) {
                return (has_capability('moodle/calendar:manageownentries', $event->context));
            }
            //there is no 'break;' intentionally

        case 'site':
            return has_capability('moodle/calendar:manageentries', $event->context);

        default:
            return has_capability('moodle/calendar:manageentries', $event->context);
    }
}

/**
 * A class to manage calendar events
 *
 * This class provides the required functionality in order to manage calendar events.
 * It was introduced as part of Moodle 2.0 and was created in order to provide a
 * better framework for dealing with calendar events in particular regard to file
 * handling through the new file API
 *
 * @property int $id The id within the event table
 * @property string $name The name of the event
 * @property string $description The description of the event
 * @property int $format The format of the description FORMAT_?
 * @property int $courseid The course the event is associated with (0 if none)
 * @property int $groupid The group the event is associated with (0 if none)
 * @property int $userid The user the event is associated with (0 if none)
 * @property int $repeatid If this is a repeated event this will be set to the
 *                          id of the original
 * @property string $modulename If added by a module this will be the module name
 * @property int $instance If added by a module this will be the module instance
 * @property string $eventtype The event type
 * @property int $timestart The start time as a timestamp
 * @property int $timeduration The duration of the event in seconds
 * @property int $visible 1 if the event is visible
 * @property int $uuid ?
 * @property int $sequence ?
 * @property int $timemodified The time last modified as a timestamp
 */
class calendar_event {

    /**
     * An object containing the event properties can be accessed via the
     * magic __get/set methods
     * @var array
     */
    protected $properties = null;
    /**
     * The converted event discription with file paths resolved
     * This gets populated when someone requests description for the first time
     * @var string
     */
    protected $_description = null;
    /**
     * The options to use with this description editor
     * @var array
     */
    protected $editoroptions = array(
            'subdirs'=>false,
            'forcehttps'=>false,
            'maxfiles'=>-1,
            'maxbytes'=>null,
            'trusttext'=>false);
    /**
     * The context to use with the description editor
     * @var object
     */
    protected $editorcontext = null;

    /**
     * Instantiates a new event and optionally populates its properties with the
     * data provided
     *
     * @param stdClass $data Optional. An object containing the properties to for
     *                  an event
     */
    public function __construct($data=null) {
        global $CFG, $USER;

        // First convert to object if it is not already (should either be object or assoc array)
        if (!is_object($data)) {
            $data = (object)$data;
        }

        $this->editoroptions['maxbytes'] = $CFG->maxbytes;

        $data->eventrepeats = 0;

        if (empty($data->id)) {
            $data->id = null;
        }

        // Default to a user event
        if (empty($data->eventtype)) {
            $data->eventtype = 'user';
        }

        // Default to the current user
        if (empty($data->userid)) {
            $data->userid = $USER->id;
        }

        if (!empty($data->timeduration) && is_array($data->timeduration)) {
            $data->timeduration = make_timestamp($data->timeduration['year'], $data->timeduration['month'], $data->timeduration['day'], $data->timeduration['hour'], $data->timeduration['minute']) - $data->timestart;
        }
        if (!empty($data->description) && is_array($data->description)) {
            $data->format = $data->description['format'];
            $data->description = $data->description['text'];
        } else if (empty($data->description)) {
            $data->description = '';
            $data->format = editors_get_preferred_format();
        }
        // Ensure form is defaulted correctly
        if (empty($data->format)) {
            $data->format = editors_get_preferred_format();
        }

        if (empty($data->context)) {
            $data->context = $this->calculate_context($data);
        }
        $this->properties = $data;
    }

    /**
     * Magic property method
     *
     * Attempts to call a set_$key method if one exists otherwise falls back
     * to simply set the property
     *
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value) {
        if (method_exists($this, 'set_'.$key)) {
            $this->{'set_'.$key}($value);
        }
        $this->properties->{$key} = $value;
    }

    /**
     * Magic get method
     *
     * Attempts to call a get_$key method to return the property and ralls over
     * to return the raw property
     *
     * @param str $key
     * @return mixed
     */
    public function __get($key) {
        if (method_exists($this, 'get_'.$key)) {
            return $this->{'get_'.$key}();
        }
        if (!isset($this->properties->{$key})) {
            throw new coding_exception('Undefined property requested');
        }
        return $this->properties->{$key};
    }

    /**
     * Stupid PHP needs an isset magic method if you use the get magic method and
     * still want empty calls to work.... blah ~!
     *
     * @param string $key
     * @return bool
     */
    public function __isset($key) {
        return !empty($this->properties->{$key});
    }

    /**
     * Calculate the context value needed for calendar_event.
     * Event's type can be determine by the available value store in $data
     * It is important to check for the existence of course/courseid to determine
     * the course event.
     * Default value is set to CONTEXT_USER
     *
     * @return stdClass
     */
    protected function calculate_context(stdClass $data) {
        global $USER;

        $context = null;
        if (isset($data->courseid) && $data->courseid > 0) {
            $context =  get_context_instance(CONTEXT_COURSE, $data->courseid);
        } else if (isset($data->course) && $data->course > 0) {
            $context =  get_context_instance(CONTEXT_COURSE, $data->course);
        } else if (isset($data->groupid) && $data->groupid > 0) {
            $group = $DB->get_record('groups', array('id'=>$data->groupid));
            $context = get_context_instance(CONTEXT_COURSE, $group->courseid);
        } else if (isset($data->userid) && $data->userid > 0 && $data->userid == $USER->id) {
            $context =  get_context_instance(CONTEXT_USER, $data->userid);
        } else if (isset($data->userid) && $data->userid > 0 && $data->userid != $USER->id &&
                   isset($data->instance) && $data->instance > 0) {
            $cm = get_coursemodule_from_instance($data->modulename, $data->instance, 0, false, MUST_EXIST);
            $context =  get_context_instance(CONTEXT_COURSE, $cm->course);
        } else {
            $context =  get_context_instance(CONTEXT_USER);
        }

        return $context;
    }

    /**
     * Returns an array of editoroptions for this event: Called by __get
     * Please use $blah = $event->editoroptions;
     * @return array
     */
    protected function get_editoroptions() {
        return $this->editoroptions;
    }

    /**
     * Returns an event description: Called by __get
     * Please use $blah = $event->description;
     *
     * @return string
     */
    protected function get_description() {
        global $CFG;

        require_once($CFG->libdir . '/filelib.php');

        if ($this->_description === null) {
            // Check if we have already resolved the context for this event
            if ($this->editorcontext === null) {
                // Switch on the event type to decide upon the appropriate context
                // to use for this event
                $this->editorcontext = $this->properties->context;
                if ($this->properties->eventtype != 'user' && $this->properties->eventtype != 'course'
                        && $this->properties->eventtype != 'site' && $this->properties->eventtype != 'group') {
                    return clean_text($this->properties->description, $this->properties->format);
                }
            }

            // Work out the item id for the editor, if this is a repeated event then the files will
            // be associated with the original
            if (!empty($this->properties->repeatid) && $this->properties->repeatid > 0) {
                $itemid = $this->properties->repeatid;
            } else {
                $itemid = $this->properties->id;
            }

            // Convert file paths in the description so that things display correctly
            $this->_description = file_rewrite_pluginfile_urls($this->properties->description, 'pluginfile.php', $this->editorcontext->id, 'calendar', 'event_description', $itemid);
            // Clean the text so no nasties get through
            $this->_description = clean_text($this->_description, $this->properties->format);
        }
        // Finally return the description
        return $this->_description;
    }

    /**
     * Return the number of repeat events there are in this events series
     *
     * @return int
     */
    public function count_repeats() {
        global $DB;
        if (!empty($this->properties->repeatid)) {
            $this->properties->eventrepeats = $DB->count_records('event', array('repeatid'=>$this->properties->repeatid));
            // We don't want to count ourselves
            $this->properties->eventrepeats--;
        }
        return $this->properties->eventrepeats;
    }

    /**
     * Update or create an event within the database
     *
     * Pass in a object containing the event properties and this function will
     * insert it into the database and deal with any associated files
     *
     * @see add_event()
     * @see update_event()
     *
     * @param stdClass $data
     * @param boolean $checkcapability if moodle should check calendar managing capability or not
     */
    public function update($data, $checkcapability=true) {
        global $CFG, $DB, $USER;

        foreach ($data as $key=>$value) {
            $this->properties->$key = $value;
        }

        $this->properties->timemodified = time();
        $usingeditor = (!empty($this->properties->description) && is_array($this->properties->description));

        if (empty($this->properties->id) || $this->properties->id < 1) {

            if ($checkcapability) {
                if (!calendar_add_event_allowed($this->properties)) {
                    print_error('nopermissiontoupdatecalendar');
                }
            }

            if ($usingeditor) {
                switch ($this->properties->eventtype) {
                    case 'user':
                        $this->editorcontext = $this->properties->context;
                        $this->properties->courseid = 0;
                        $this->properties->groupid = 0;
                        $this->properties->userid = $USER->id;
                        break;
                    case 'site':
                        $this->editorcontext = $this->properties->context;
                        $this->properties->courseid = SITEID;
                        $this->properties->groupid = 0;
                        $this->properties->userid = $USER->id;
                        break;
                    case 'course':
                        $this->editorcontext = $this->properties->context;
                        $this->properties->groupid = 0;
                        $this->properties->userid = $USER->id;
                        break;
                    case 'group':
                        $this->editorcontext = $this->properties->context;
                        $this->properties->userid = $USER->id;
                        break;
                    default:
                        // Ewww we should NEVER get here, but just incase we do lets
                        // fail gracefully
                        $usingeditor = false;
                        break;
                }

                $editor = $this->properties->description;
                $this->properties->format = $this->properties->description['format'];
                $this->properties->description = $this->properties->description['text'];
            }

            // Insert the event into the database
            $this->properties->id = $DB->insert_record('event', $this->properties);

            if ($usingeditor) {
                $this->properties->description = file_save_draft_area_files(
                                                $editor['itemid'],
                                                $this->editorcontext->id,
                                                'calendar',
                                                'event_description',
                                                $this->properties->id,
                                                $this->editoroptions,
                                                $editor['text'],
                                                $this->editoroptions['forcehttps']);

                $DB->set_field('event', 'description', $this->properties->description, array('id'=>$this->properties->id));
            }

            // Log the event entry.
            add_to_log($this->properties->courseid, 'calendar', 'add', 'event.php?action=edit&amp;id='.$this->properties->id, $this->properties->name);

            $repeatedids = array();

            if (!empty($this->properties->repeat)) {
                $this->properties->repeatid = $this->properties->id;
                $DB->set_field('event', 'repeatid', $this->properties->repeatid, array('id'=>$this->properties->id));

                $eventcopy = clone($this->properties);
                unset($eventcopy->id);

                for($i = 1; $i < $eventcopy->repeats; $i++) {

                    $eventcopy->timestart = ($eventcopy->timestart+WEEKSECS) + dst_offset_on($eventcopy->timestart) - dst_offset_on($eventcopy->timestart+WEEKSECS);

                    // Get the event id for the log record.
                    $eventcopyid = $DB->insert_record('event', $eventcopy);

                    // If the context has been set delete all associated files
                    if ($usingeditor) {
                        $fs = get_file_storage();
                        $files = $fs->get_area_files($this->editorcontext->id, 'calendar', 'event_description', $this->properties->id);
                        foreach ($files as $file) {
                            $fs->create_file_from_storedfile(array('itemid'=>$eventcopyid), $file);
                        }
                    }

                    $repeatedids[] = $eventcopyid;
                    // Log the event entry.
                    add_to_log($eventcopy->courseid, 'calendar', 'add', 'event.php?action=edit&amp;id='.$eventcopyid, $eventcopy->name);
                }
            }

            // Hook for tracking added events
            self::calendar_event_hook('add_event', array($this->properties, $repeatedids));
            return true;
        } else {

            if ($checkcapability) {
                if(!calendar_edit_event_allowed($this->properties)) {
                    print_error('nopermissiontoupdatecalendar');
                }
            }

            if ($usingeditor) {
                if ($this->editorcontext !== null) {
                    $this->properties->description = file_save_draft_area_files(
                                                    $this->properties->description['itemid'],
                                                    $this->editorcontext->id,
                                                    'calendar',
                                                    'event_description',
                                                    $this->properties->id,
                                                    $this->editoroptions,
                                                    $this->properties->description['text'],
                                                    $this->editoroptions['forcehttps']);
                } else {
                    $this->properties->format = $this->properties->description['format'];
                    $this->properties->description = $this->properties->description['text'];
                }
            }

            $event = $DB->get_record('event', array('id'=>$this->properties->id));

            $updaterepeated = (!empty($this->properties->repeatid) && !empty($this->properties->repeateditall));

            if ($updaterepeated) {
                // Update all
                if ($this->properties->timestart != $event->timestart) {
                    $timestartoffset = $this->properties->timestart - $event->timestart;
                    $sql = "UPDATE {event}
                               SET name = ?,
                                   description = ?,
                                   timestart = timestart + ?,
                                   timeduration = ?,
                                   timemodified = ?
                             WHERE repeatid = ?";
                    $params = array($this->properties->name, $this->properties->description, $timestartoffset, $this->properties->timeduration, time(), $event->repeatid);
                } else {
                    $sql = "UPDATE {event} SET name = ?, description = ?, timeduration = ?, timemodified = ? WHERE repeatid = ?";
                    $params = array($this->properties->name, $this->properties->description, $this->properties->timeduration, time(), $event->repeatid);
                }
                $DB->execute($sql, $params);

                // Log the event update.
                add_to_log($this->properties->courseid, 'calendar', 'edit all', 'event.php?action=edit&amp;id='.$this->properties->id, $this->properties->name);
            } else {
                $DB->update_record('event', $this->properties);
                $event = calendar_event::load($this->properties->id);
                $this->properties = $event->properties();
                add_to_log($this->properties->courseid, 'calendar', 'edit', 'event.php?action=edit&amp;id='.$this->properties->id, $this->properties->name);
            }

            // Hook for tracking event updates
            self::calendar_event_hook('update_event', array($this->properties, $updaterepeated));
            return true;
        }
    }

    /**
     * Deletes an event and if selected an repeated events in the same series
     *
     * This function deletes an event, any associated events if $deleterepeated=true,
     * and cleans up any files associated with the events.
     *
     * @see delete_event()
     *
     * @param bool $deleterepeated
     * @return bool
     */
    public function delete($deleterepeated=false) {
        global $DB;

        // If $this->properties->id is not set then something is wrong
        if (empty($this->properties->id)) {
            debugging('Attempting to delete an event before it has been loaded', DEBUG_DEVELOPER);
            return false;
        }

        // Delete the event
        $DB->delete_records('event', array('id'=>$this->properties->id));

        // If the editor context hasn't already been set then set it now
        if ($this->editorcontext === null) {
            $this->editorcontext = $this->properties->context;
        }

        // If the context has been set delete all associated files
        if ($this->editorcontext !== null) {
            $fs = get_file_storage();
            $files = $fs->get_area_files($this->editorcontext->id, 'calendar', 'event_description', $this->properties->id);
            foreach ($files as $file) {
                $file->delete();
            }
        }

        // Fire the event deleted hook
        self::calendar_event_hook('delete_event', array($this->properties->id, $deleterepeated));

        // If we need to delete repeated events then we will fetch them all and delete one by one
        if ($deleterepeated && !empty($this->properties->repeatid) && $this->properties->repeatid > 0) {
            // Get all records where the repeatid is the same as the event being removed
            $events = $DB->get_records('event', array('repeatid'=>$this->properties->repeatid));
            // For each of the returned events populate a calendar_event object and call delete
            // make sure the arg passed is false as we are already deleting all repeats
            foreach ($events as $event) {
                $event = new calendar_event($event);
                $event->delete(false);
            }
        }

        return true;
    }

    /**
     * Fetch all event properties
     *
     * This function returns all of the events properties as an object and optionally
     * can prepare an editor for the description field at the same time. This is
     * designed to work when the properties are going to be used to set the default
     * values of a moodle forms form.
     *
     * @param bool $prepareeditor If set to true a editor is prepared for use with
     *              the mforms editor element. (for description)
     * @return stdClass Object containing event properties
     */
    public function properties($prepareeditor=false) {
        global $USER, $CFG, $DB;

        // First take a copy of the properties. We don't want to actually change the
        // properties or we'd forever be converting back and forwards between an
        // editor formatted description and not
        $properties = clone($this->properties);
        // Clean the description here
        $properties->description = clean_text($properties->description, $properties->format);

        // If set to true we need to prepare the properties for use with an editor
        // and prepare the file area
        if ($prepareeditor) {

            // We may or may not have a property id. If we do then we need to work
            // out the context so we can copy the existing files to the draft area
            if (!empty($properties->id)) {

                if ($properties->eventtype === 'site') {
                    // Site context
                    $this->editorcontext = $this->properties->context;
                } else if ($properties->eventtype === 'user') {
                    // User context
                    $this->editorcontext = $this->properties->context;
                } else if ($properties->eventtype === 'group' || $properties->eventtype === 'course') {
                    // First check the course is valid
                    $course = $DB->get_record('course', array('id'=>$properties->courseid));
                    if (!$course) {
                        print_error('invalidcourse');
                    }
                    // Course context
                    $this->editorcontext = $this->properties->context;
                    // We have a course and are within the course context so we had
                    // better use the courses max bytes value
                    $this->editoroptions['maxbytes'] = $course->maxbytes;
                } else {
                    // If we get here we have a custom event type as used by some
                    // modules. In this case the event will have been added by
                    // code and we won't need the editor
                    $this->editoroptions['maxbytes'] = 0;
                    $this->editoroptions['maxfiles'] = 0;
                }

                if (empty($this->editorcontext) || empty($this->editorcontext->id)) {
                    $contextid = false;
                } else {
                    // Get the context id that is what we really want
                    $contextid = $this->editorcontext->id;
                }
            } else {

                // If we get here then this is a new event in which case we don't need a
                // context as there is no existing files to copy to the draft area.
                $contextid = null;
            }

            // If the contextid === false we don't support files so no preparing
            // a draft area
            if ($contextid !== false) {
                // Just encase it has already been submitted
                $draftiddescription = file_get_submitted_draft_itemid('description');
                // Prepare the draft area, this copies existing files to the draft area as well
                $properties->description = file_prepare_draft_area($draftiddescription, $contextid, 'calendar', 'event_description', $properties->id, $this->editoroptions, $properties->description);
            } else {
                $draftiddescription = 0;
            }

            // Structure the description field as the editor requires
            $properties->description = array('text'=>$properties->description, 'format'=>$properties->format, 'itemid'=>$draftiddescription);
        }

        // Finally return the properties
        return $properties;
    }

    /**
     * Toggles the visibility of an event
     *
     * @param null|bool $force If it is left null the events visibility is flipped,
     *                   If it is false the event is made hidden, if it is true it
     *                   is made visible.
     */
    public function toggle_visibility($force=null) {
        global $CFG, $DB;

        // Set visible to the default if it is not already set
        if (empty($this->properties->visible)) {
            $this->properties->visible = 1;
        }

        if ($force === true || ($force !== false && $this->properties->visible == 0)) {
            // Make this event visible
            $this->properties->visible = 1;
            // Fire the hook
            self::calendar_event_hook('show_event', array($this->properties));
        } else {
            // Make this event hidden
            $this->properties->visible = 0;
            // Fire the hook
            self::calendar_event_hook('hide_event', array($this->properties));
        }

        // Update the database to reflect this change
        return $DB->set_field('event', 'visible', $this->properties->visible, array('id'=>$this->properties->id));
    }

    /**
     * Attempts to call the hook for the specified action should a calendar type
     * by set $CFG->calendar, and the appopriate function defined
     *
     * @static
     * @staticvar bool $extcalendarinc Used to track the inclusion of the calendar lib
     * @param string $action One of `update_event`, `add_event`, `delete_event`, `show_event`, `hide_event`
     * @param array $args The args to pass to the hook, usually the event is the first element
     * @return bool
     */
    public static function calendar_event_hook($action, array $args) {
        global $CFG;
        static $extcalendarinc;
        if ($extcalendarinc === null) {
            if (!empty($CFG->calendar) && file_exists($CFG->dirroot .'/calendar/'. $CFG->calendar .'/lib.php')) {
                include_once($CFG->dirroot .'/calendar/'. $CFG->calendar .'/lib.php');
                $extcalendarinc = true;
            } else {
                $extcalendarinc = false;
            }
        }
        if($extcalendarinc === false) {
            return false;
        }
        $hook = $CFG->calendar .'_'.$action;
        if (function_exists($hook)) {
            call_user_func_array($hook, $args);
            return true;
        }
        return false;
    }

    /**
     * Returns a calendar_event object when provided with an event id
     *
     * This function makes use of MUST_EXIST, if the event id passed in is invalid
     * it will result in an exception being thrown
     *
     * @param int|object $param
     * @return calendar_event|false
     */
    public static function load($param) {
        global $DB;
        if (is_object($param)) {
            $event = new calendar_event($param);
        } else {
            $event = $DB->get_record('event', array('id'=>(int)$param), '*', MUST_EXIST);
            $event = new calendar_event($event);
        }
        return $event;
    }

    /**
     * Creates a new event and returns a calendar_event object
     *
     * @param object|array $properties An object containing event properties
     * @return calendar_event|false The event object or false if it failed
     */
    public static function create($properties) {
        if (is_array($properties)) {
            $properties = (object)$properties;
        }
        if (!is_object($properties)) {
            throw new coding_exception('When creating an event properties should be either an object or an assoc array');
        }
        $event = new calendar_event($properties);
        if ($event->update($properties)) {
            return $event;
        } else {
            return false;
        }
    }
}

/**
 * Calendar information class
 *
 * This class is used simply to organise the information pertaining to a calendar
 * and is used primarily to make information easily available.
 */
class calendar_information {
    /**
     * The day
     * @var int
     */
    public $day;
    /**
     * The month
     * @var int
     */
    public $month;
    /**
     * The year
     * @var int
     */
    public $year;

    /**
     * A course id
     * @var int
     */
    public $courseid = null;
    /**
     * An array of courses
     * @var array
     */
    public $courses = array();
    /**
     * An array of groups
     * @var array
     */
    public $groups = array();
    /**
     * An array of users
     * @var array
     */
    public $users = array();

    /**
     * Creates a new instance
     *
     * @param int $day
     * @param int $month
     * @param int $year
     */
    public function __construct($day=0, $month=0, $year=0) {

        $date = usergetdate(time());

        if (empty($day)) {
            $day = $date['mday'];
        }

        if (empty($month)) {
            $month = $date['mon'];
        }

        if (empty($year)) {
            $year =  $date['year'];
        }

        $this->day = $day;
        $this->month = $month;
        $this->year = $year;
    }

    /**
     *
     * @param stdClass $course
     * @param array $coursestoload An array of courses [$course->id => $course]
     * @param type $ignorefilters
     */
    public function prepare_for_view(stdClass $course, array $coursestoload, $ignorefilters = false) {
        $this->courseid = $course->id;
        $this->course = $course;
        list($courses, $group, $user) = calendar_set_filters($coursestoload, $ignorefilters);
        $this->courses = $courses;
        $this->groups = $group;
        $this->users = $user;
    }

    /**
     * Ensures the date for the calendar is correct and either sets it to now
     * or throws a moodle_exception if not
     *
     * @param bool $defaultonow
     * @return bool
     */
    public function checkdate($defaultonow = true) {
        if (!checkdate($this->month, $this->day, $this->year)) {
            if ($defaultonow) {
                $now = usergetdate(time());
                $this->day = intval($now['mday']);
                $this->month = intval($now['mon']);
                $this->year = intval($now['year']);
                return true;
            } else {
                throw new moodle_exception('invaliddate');
            }
        }
        return true;
    }
    /**
     * Gets todays timestamp for the calendar
     * @return int
     */
    public function timestamp_today() {
        return make_timestamp($this->year, $this->month, $this->day);
    }
    /**
     * Gets tomorrows timestamp for the calendar
     * @return int
     */
    public function timestamp_tomorrow() {
        return make_timestamp($this->year, $this->month, $this->day+1);
    }
    /**
     * Adds the pretend blocks for teh calendar
     *
     * @param core_calendar_renderer $renderer
     * @param bool $showfilters
     * @param string|null $view
     */
    public function add_sidecalendar_blocks(core_calendar_renderer $renderer, $showfilters=false, $view=null) {
        if ($showfilters) {
            $filters = new block_contents();
            $filters->content = $renderer->fake_block_filters($this->courseid, $this->day, $this->month, $this->year, $view, $this->courses);
            $filters->footer = '';
            $filters->title = get_string('eventskey', 'calendar');
            $renderer->add_pretend_calendar_block($filters, BLOCK_POS_RIGHT);
        }
        $block = new block_contents;
        $block->content = $renderer->fake_block_threemonths($this);
        $block->footer = '';
        $block->title = get_string('monthlyview', 'calendar');
        $renderer->add_pretend_calendar_block($block, BLOCK_POS_RIGHT);
    }
}
