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

    require_once('../config.php');
    require_once($CFG->dirroot.'/calendar/lib.php');
    require_once($CFG->dirroot.'/course/lib.php');
    require_once($CFG->dirroot.'/mod/forum/lib.php');

    require_login();

    if(isguest()) {
        // Guests cannot do anything with events
        redirect(CALENDAR_URL.'view.php?view=upcoming');
    }

    $action = required_param('action', PARAM_ALPHA);
    $eventid = optional_param('id', 0, PARAM_INT);
    $eventtype = optional_param('type', 'select', PARAM_ALPHA);
    $urlcourse = optional_param('course', 0, PARAM_INT);
    $cal_y = optional_param('cal_y');
    $cal_m = optional_param('cal_m');
    $cal_d = optional_param('cal_d');

    $focus = '';

    if(!$site = get_site()) {
        redirect($CFG->wwwroot.'/'.$CFG->admin.'/index.php');
    }

    $strcalendar = get_string('calendar', 'calendar');

    // Initialize the session variables
    calendar_session_vars();

    $now = usergetdate(time());
    $nav = calendar_get_link_tag($strcalendar, CALENDAR_URL.'view.php?view=upcoming&amp;', $now['mday'], $now['mon'], $now['year']);
    $day = intval($now['mday']);
    $mon = intval($now['mon']);
    $yr = intval($now['year']);

    if ($usehtmleditor = can_use_richtext_editor()) {
        $defaultformat = FORMAT_HTML;
    } else {
        $defaultformat = FORMAT_MOODLE;
    }

    // If a course has been supplied in the URL, change the filters to show that one
    if($urlcourse > 0 && record_exists('course', 'id', $urlcourse)) {
        require_login($urlcourse, false);

        if($urlcourse == SITEID) {
            // If coming from the site page, show all courses
            $SESSION->cal_courses_shown = calendar_get_default_courses(true);
            calendar_set_referring_course(0);
        }
        else {
            // Otherwise show just this one
            $SESSION->cal_courses_shown = $urlcourse;
            calendar_set_referring_course($SESSION->cal_courses_shown);
        }
    }

    switch($action) {
        case 'delete':
            $title = get_string('deleteevent', 'calendar');
            $event = get_record('event', 'id', $eventid);
            if($event === false) {
                error('Invalid event');
            }
            if(!calendar_edit_event_allowed($event)) {
                error('You are not authorized to do this');
            }
        break;

        case 'edit':
            $title = get_string('editevent', 'calendar');
            $event = get_record('event', 'id', $eventid);
            $repeats = optional_param('repeats', 0, PARAM_INT);

            if($event === false) {
                error('Invalid event');
            }
            if(!calendar_edit_event_allowed($event)) {
                error('You are not authorized to do this');
            }

            if($form = data_submitted()) {

                $form->name = clean_param(strip_tags($form->name,'<lang><span>'), PARAM_CLEAN);

                $form->timestart = make_timestamp($form->startyr, $form->startmon, $form->startday, $form->starthr, $form->startmin);
                if($form->duration == 1) {
                    $form->timeduration = make_timestamp($form->endyr, $form->endmon, $form->endday, $form->endhr, $form->endmin) - $form->timestart;
                    if($form->timeduration < 0) {
                        $form->timeduration = 0;
                    }
                }
                else if($form->duration == 2) {
                    $form->timeduration = $form->minutes * MINSECS;
                }
                else {
                    $form->timeduration = 0;
                }

                validate_form($form, $err);

                if (count($err) == 0) {

                    if($event->repeatid && $repeats) {
                        // Update all
                        if($form->timestart >= $event->timestart) {
                            $timestartoffset = 'timestart + '.($form->timestart - $event->timestart);
                        }
                        else {
                            $timestartoffset = 'timestart - '.($event->timestart - $form->timestart);
                        }

                        execute_sql('UPDATE '.$CFG->prefix.'event SET '.
                            'name = '.$db->qstr($form->name).','.
                            'description = '.$db->qstr($form->description).','.
                            'timestart = '.$timestartoffset.','.
                            'timeduration = '.$form->timeduration.','.
                            'timemodified = '.time().' WHERE repeatid = '.$event->repeatid);
                            
                        /// Log the event update.
                        $form->name = stripslashes($form->name);  //To avoid double-slashes
                        add_to_log($form->courseid, 'calendar', 'edit all', 'event.php?action=edit&amp;id='.$form->id, $form->name);
                    }

                    else {
                        // Update this
                        $form->timemodified = time();
                        update_record('event', $form);
    
                        /// Log the event update.
                        $form->name = stripslashes($form->name);  //To avoid double-slashes
                        add_to_log($form->courseid, 'calendar', 'edit', 'event.php?action=edit&amp;id='.$form->id, $form->name);
                    }

                    // OK, now redirect to day view
                    redirect(CALENDAR_URL.'view.php?view=day&cal_d='.$form->startday.'&cal_m='.$form->startmon.'&cal_y='.$form->startyr);
                }
                else {
                    foreach ($err as $key => $value) {
                        $focus = 'form.'.$key;
                    }
                }
            }
        break;

        case 'new':
            $title = get_string('newevent', 'calendar');
            $form = data_submitted();
            if(!empty($form) && !empty($form->name)) {

                $form->name = clean_text(strip_tags($form->name, '<lang><span>'));

                $form->timestart = make_timestamp($form->startyr, $form->startmon, $form->startday, $form->starthr, $form->startmin);
                if($form->duration == 1) {
                    $form->timeduration = make_timestamp($form->endyr, $form->endmon, $form->endday, $form->endhr, $form->endmin) - $form->timestart;
                    if($form->timeduration < 0) {
                        $form->timeduration = 0;
                    }
                }
                else if ($form->duration == 2) {
                    $form->timeduration = $form->minutes * MINSECS;
                }
                else {
                    $form->timeduration = 0;
                }
                if(!calendar_add_event_allowed($form)) {
                    error('You are not authorized to do this');
                }
                validate_form($form, $err);
                if (count($err) == 0) {
                    $form->timemodified = time();

                    if ($form->repeat) {
                        $fetch = get_record_sql('SELECT 1, MAX(repeatid) AS repeatid FROM '.$CFG->prefix.'event');
                        $form->repeatid = empty($fetch) ? 1 : $fetch->repeatid + 1;
                    }

                    /// Get the event id for the log record.
                    $eventid = insert_record('event', $form, true);

                    /// Log the event entry.
                    add_to_log($form->courseid, 'calendar', 'add', 'event.php?action=edit&amp;id='.$eventid, stripslashes($form->name));

                    if ($form->repeat) {
                        for($i = 1; $i < $form->repeats; $i++) {
                            // What's the DST offset for the previous repeat?
                            $dst_offset_prev = dst_offset_on($form->timestart);

                            $form->timestart += WEEKSECS;

                            // If the offset has changed in the meantime, update this repeat accordingly
                            $form->timestart += $dst_offset_prev - dst_offset_on($form->timestart);

                            /// Get the event id for the log record.
                            $eventid = insert_record('event', $form, true);

                            /// Log the event entry.
                            add_to_log($form->courseid, 'calendar', 'add', 'event.php?action=edit&amp;id='.$eventid, stripslashes($form->name));
                        }
                    }
                    // OK, now redirect to day view
                    redirect(CALENDAR_URL.'view.php?view=day&cal_d='.$form->startday.'&cal_m='.$form->startmon.'&cal_y='.$form->startyr);
                }
                else {
                    foreach ($err as $key => $value) {
                        $focus = 'form.'.$key;
                    }
                }
            }
        break;
        default: // no action
            $title='';
        break;
    }

    // Let's see if we are supposed to provide a referring course link
    // but NOT for the "main page" course
    if($SESSION->cal_course_referer != SITEID &&
      ($shortname = get_field('course', 'shortname', 'id', $SESSION->cal_course_referer)) !== false) {
        // If we know about the referring course, show a return link
        $nav = '<a href="'.$CFG->wwwroot.'/course/view.php?id='.$SESSION->cal_course_referer.'">'.$shortname.'</a> -> '.$nav;
    }

    if (!empty($SESSION->cal_course_referer)) {
        // TODO: This is part of the Great $course Hack in Moodle. Replace it at some point.
        $course = get_record('course', 'id', $SESSION->cal_course_referer);
    } else {
        $course = $site;
    }
    require_login($course, false);

    print_header($site->shortname.': '.$strcalendar.': '.$title, $strcalendar, $nav.' -> '.$title,
                 'eventform.name', '', true, '', user_login_string($site));

    echo calendar_overlib_html();

    echo '<table id="calendar">';
    echo '<tr><td class="maincalendar">';

    switch($action) {
        case 'delete':
            $confirm = optional_param('confirm', 0, PARAM_INT);
            $repeats = optional_param('repeats', 0, PARAM_INT);
            if($confirm) {
                // Kill it and redirect to day view
                if(($event = get_record('event', 'id', $eventid)) !== false) {

                    if($event->repeatid && $repeats) {
                        delete_records('event', 'repeatid', $event->repeatid);
                        add_to_log($event->courseid, 'calendar', 'delete all', '', $event->name);
                    }
                    else {
                        delete_records('event', 'id', $eventid);
                        add_to_log($event->courseid, 'calendar', 'delete', '', $event->name);
                    }
                }

                redirect(CALENDAR_URL.'view.php?view=day&cal_d='.$_REQUEST['d'].'&cal_m='.$_REQUEST['m'].'&cal_y='.$_REQUEST['y']);

            }
            else {
                $eventtime = usergetdate($event->timestart);
                $m = $eventtime['mon'];
                $d = $eventtime['mday'];
                $y = $eventtime['year'];

                if($event->repeatid) {
                    $fetch = get_record_sql('SELECT 1, COUNT(id) AS repeatcount FROM '.$CFG->prefix.'event WHERE repeatid = '.$event->repeatid);
                    $repeatcount = $fetch->repeatcount;
                }
                else {
                    $repeatcount = 0;
                }
                
                // Display confirmation form
                echo '<div class="header">'.get_string('deleteevent', 'calendar').': '.$event->name.'</div>';
                echo '<h2>'.get_string('confirmeventdelete', 'calendar').'</h2>';
                if($repeatcount > 1) {
                    echo '<p>'.get_string('youcandeleteallrepeats', 'calendar', $repeatcount).'</p>';
                }
                echo '<div class="eventlist">';
                $event->time = calendar_format_event_time($event, time(), '', false);
                calendar_print_event($event);
                echo '</div>';
                include('event_delete.html');
            }
        break;

        case 'edit':
            if(empty($form)) {
                $form->name = $event->name;
                $form->courseid = $event->courseid; // Not to update, but for date validation
                $form->description = $event->description;
                $form->timestart = $event->timestart;
                $form->timeduration = $event->timeduration;
                $form->id = $event->id;
                $form->format = $defaultformat;
                if($event->timeduration > HOURSECS) {
                    // More than one hour, so default to normal duration mode
                    $form->duration = 1;
                    $form->minutes = '';
                }
                else if($event->timeduration) {
                    // Up to one hour, "minutes" mode probably is better here
                    $form->duration = 2;
                    $form->minutes = $event->timeduration / MINSECS;
                }
                else {
                    // No duration
                    $form->duration = 0;
                    $form->minutes = '';
                }
            }

            if (!empty($form->courseid)) {
                // TODO: This is part of the Great $course Hack in Moodle. Replace it at some point.
                $course = get_record('course', 'id', $form->courseid);
            } else {
                $course = $site;
            }

            if($event->repeatid) {
                $fetch = get_record_sql('SELECT 1, COUNT(id) AS repeatcount FROM '.$CFG->prefix.'event WHERE repeatid = '.$event->repeatid);
                $repeatcount = $fetch->repeatcount;
            }
            else {
                $repeatcount = 0;
            }

            echo '<div class="header">'.get_string('editevent', 'calendar').'</div>';
            include('event_edit.html');
            if ($usehtmleditor) {
                use_html_editor("description");
            }
        break;

        case 'new':
            if($cal_y && $cal_m && $cal_d && checkdate($cal_m, $cal_d, $cal_y)) {
                $form->timestart = make_timestamp($cal_y, $cal_m, $cal_d, 0, 0, 0);
            }
            else if($cal_y && $cal_m && checkdate($cal_m, 1, $cal_y)) {
                if($cal_y == $now['year'] && $cal_m == $now['mon']) {
                    $form->timestart = make_timestamp($cal_y, $cal_m, $now['mday'], 0, 0, 0);
                }
                else {
                    $form->timestart = make_timestamp($cal_y, $cal_m, 1, 0, 0, 0);
                }
            }
            if(!isset($form->timestart) or $form->timestart < 0) {
                $form->timestart = time();
            }

            calendar_get_allowed_types($allowed);
            if(!$allowed->groups && !$allowed->courses && !$allowed->site) {
                // Take the shortcut
                $eventtype = 'user';
            }

            $header = '';

            switch($eventtype) {
                case 'user':
                    $form->name = '';
                    $form->description = '';
                    $form->courseid = 0;
                    $form->groupid = 0;
                    $form->userid = $USER->id;
                    $form->modulename = '';
                    $form->eventtype = '';
                    $form->instance = 0;
                    $form->timeduration = 0;
                    $form->duration = 0;
                    $form->repeat = 0;
                    $form->repeats = '';
                    $form->minutes = '';
                    $form->type = 'user';
                    $header = get_string('typeuser', 'calendar');
                break;
                case 'group':
                    $groupid = optional_param('groupid', 0, PARAM_INT);
                    if (! ($group = groups_get_group($groupid))) { //TODO:check.
                        calendar_get_allowed_types($allowed);
                        $eventtype = 'select';
                    }
                    else {
                        $form->name = '';
                        $form->description = '';
                        $form->courseid = $group->courseid;
                        $form->groupid = $group->id;
                        $form->userid = $USER->id;
                        $form->modulename = '';
                        $form->eventtype = '';
                        $form->instance = 0;
                        $form->timeduration = 0;
                        $form->duration = 0;
                        $form->repeat = 0;
                        $form->repeats = '';
                        $form->minutes = '';
                        $form->type = 'group';
                        $header = get_string('typegroup', 'calendar');
                    }
                break;
                case 'course':
                    $courseid = optional_param('courseid', 0, PARAM_INT);
                    if(!record_exists('course', 'id', $courseid)) {
                        calendar_get_allowed_types($allowed);
                        $eventtype = 'select';
                    }
                    else {
                        $form->name = '';
                        $form->description = '';
                        $form->courseid = $courseid;
                        $form->groupid = 0;
                        $form->userid = $USER->id;
                        $form->modulename = '';
                        $form->eventtype = '';
                        $form->instance = 0;
                        $form->timeduration = 0;
                        $form->duration = 0;
                        $form->repeat = 0;
                        $form->repeats = '';
                        $form->minutes = '';
                        $form->type = 'course';
                        $header = get_string('typecourse', 'calendar');
                    }
                break;
                case 'site':
                    $form->name = '';
                    $form->description = '';
                    $form->courseid = SITEID;
                    $form->groupid = 0;
                    $form->userid = $USER->id;
                    $form->modulename = '';
                    $form->eventtype = '';
                    $form->instance = 0;
                    $form->timeduration = 0;
                    $form->duration = 0;
                    $form->repeat = 0;
                    $form->repeats = '';
                    $form->minutes = '';
                    $form->type = 'site';
                    $header = get_string('typesite', 'calendar');
                break;
                case 'select':
                break;
                default:
                    error('Unsupported event type');
            }

            $form->format = $defaultformat;
            if(!empty($header)) {
                $header = ' ('.$header.')';
            }

            echo '<div class="header">'.get_string('newevent', 'calendar').$header.'</div>';

            if($eventtype == 'select') {
                $courseid = optional_param('courseid', $SESSION->cal_course_referer, PARAM_INT);
                if ($courseid == 0) { // workaround by Dan for bug #6130
                    $courseid = SITEID;
                }
                if (!$course = get_record('course', 'id', $courseid)) {
                    error('Incorrect course ID');
                }
                if ($groupmode = groupmode($course)) {   // Groups are being used
                    $changegroup = optional_param('group', -1, PARAM_INT);
                    $groupid = get_and_set_current_group($course, $groupmode, $changegroup);
                } else {
                    $groupid = 0;
                }

                echo '<h2>'.get_string('eventkind', 'calendar').':</h2>';
                echo '<div id="selecteventtype">';
                include('event_select.html');
                echo '</div>';
            }
            else {
                include('event_new.html');
                if ($usehtmleditor) {
                    use_html_editor("description");
                }
            }

        break;
    }
    echo '</td>';

    // START: Last column (3-month display)

    $defaultcourses = calendar_get_default_courses();
    calendar_set_filters($courses, $groups, $users, $defaultcourses, $defaultcourses);
    list($prevmon, $prevyr) = calendar_sub_month($mon, $yr);
    list($nextmon, $nextyr) = calendar_add_month($mon, $yr);
    
    echo '<td class="sidecalendar">';
    echo '<div class="sideblock">';
    echo '<div class="header">'.get_string('eventskey', 'calendar').'</div>';
    echo '<div class="filters">';
    echo calendar_filter_controls('event', 'action='.$action.'&amp;type='.$eventtype.'&amp;id='.$eventid);
    echo '</div>';
    echo '</div>';
    
    echo '<div class="sideblock">';
    echo '<div class="header">'.get_string('monthlyview', 'calendar').'</div>';
    echo '<div class="minicalendarblock minicalendartop">';
    echo calendar_top_controls('display', array('m' => $prevmon, 'y' => $prevyr));
    echo calendar_get_mini($courses, $groups, $users, $prevmon, $prevyr);
    echo '</div><div class="minicalendarblock">';
    echo calendar_top_controls('display', array('m' => $mon, 'y' => $yr));
    echo calendar_get_mini($courses, $groups, $users, $mon, $yr);
    echo '</div><div class="minicalendarblock">';
    echo calendar_top_controls('display', array('m' => $nextmon, 'y' => $nextyr));
    echo calendar_get_mini($courses, $groups, $users, $nextmon, $nextyr);
    echo '</div>';
    echo '</div>';

    echo '</td>';
    echo '</tr></table>';

    print_footer();


function validate_form(&$form, &$err) {

    $form->name = trim($form->name);
    $form->description = trim($form->description);

    if(empty($form->name)) {
        $err['name'] = get_string('errornoeventname', 'calendar');
    }
    if(empty($form->description)) {
        $err['description'] = get_string('errornodescription', 'calendar');
    }
    if(!checkdate($form->startmon, $form->startday, $form->startyr)) {
        $err['timestart'] = get_string('errorinvaliddate', 'calendar');
    }
    if($form->duration == 2 and !checkdate($form->endmon, $form->endday, $form->endyr)) {
        $err['timeduration'] = get_string('errorinvaliddate', 'calendar');
    }
    if($form->duration == 2 and !($form->minutes > 0 and $form->minutes < 1000)) {
        $err['minutes'] = get_string('errorinvalidminutes', 'calendar');
    }
    if (!empty($form->repeat) and !($form->repeats > 1 and $form->repeats < 100)) {
        $err['repeats'] = get_string('errorinvalidrepeats', 'calendar');
    }
    if(!empty($form->courseid)) {
        // Timestamps must be >= course startdate
        $course = get_record('course', 'id', $form->courseid);
        if($course === false) {
            error('Event belongs to invalid course');
        }
        else if($form->timestart < $course->startdate) {
            $err['timestart'] = get_string('errorbeforecoursestart', 'calendar');
        }
    }
}

function calendar_add_event_allowed($event) {
    global $USER;

    // can not be using guest account
    if (empty($USER->id) or $USER->username == 'guest') {
        return false;  
    }

    $sitecontext = get_context_instance(CONTEXT_SYSTEM);
    // if user has manageentries at site level, always return true
    if (has_capability('moodle/calendar:manageentries', $sitecontext)) {
        return true;
    }

    switch ($event->type) {
        case 'course':
            return has_capability('moodle/calendar:manageentries', get_context_instance(CONTEXT_COURSE, $event->courseid));

        case 'group':
            if (! groups_group_exists($event->groupid)) { //TODO:check.
                return false;
            } 
            // this is ok because if you have this capability at course level, you should be able 
            // to edit group calendar too
            // there is no need to check membership, because if you have this capability
            // you will have a role in this group context
            return has_capability('moodle/calendar:manageentries', get_context_instance(CONTEXT_GROUP, $event->groupid));

        case 'user':
            if ($event->userid == $USER->id) {
                return (has_capability('moodle/calendar:manageownentries', $sitecontext));
            }
            //there is no 'break;' intentionally

        case 'site':
            return has_capability('moodle/calendar:manageentries', get_context_instance(CONTEXT_COURSE, SITEID));

        default:
            return false;
    }
}

function calendar_get_allowed_types(&$allowed) {
    global $USER, $CFG, $SESSION;

    $allowed->user = true; // User events always allowed
    $allowed->groups = false; // This may change just below
    $allowed->courses = false; // This may change just below
    $allowed->site = has_capability('moodle/calendar:manageentries', get_context_instance(CONTEXT_COURSE, SITEID));

    if(!empty($SESSION->cal_course_referer) && $SESSION->cal_course_referer != SITEID && has_capability('moodle/calendar:manageentries', get_context_instance(CONTEXT_COURSE, $SESSION->cal_course_referer))) {
        $course = get_record('course', 'id', $SESSION->cal_course_referer);

        $allowed->courses = array($course->id => 1);

        if($course->groupmode != NOGROUPS || !$course->groupmodeforce) {
            $allowed->groups = get_groups($SESSION->cal_course_referer);
        }
    }
}

?>
