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

    require_once('../config.php');
    require_once('lib.php');
    require_once('../course/lib.php');
    require_once('../mod/forum/lib.php');

    require_login();

    if(isguest()) {
        // Guests cannot do anything with events
        redirect(CALENDAR_URL.'view.php?view=upcoming');
    }

    require_variable($_REQUEST['action']);
    optional_variable($_REQUEST['id']);
    optional_variable($_REQUEST['type'], 'select');
    $_REQUEST['id'] = intval($_REQUEST['id']); // Always a good idea, against SQL injections

    if(!$site = get_site()) {
        redirect($CFG->wwwroot.'/'.$CFG->admin.'/index.php');
    }

    $now = usergetdate(time());
    $nav = calendar_get_link_tag(get_string('calendar', 'calendar'), CALENDAR_URL.'view.php?view=upcoming&amp;', $now['mday'], $now['mon'], $now['year']);
    $day = intval($now['mday']);
    $mon = intval($now['mon']);
    $yr = intval($now['year']);

    if ($usehtmleditor = can_use_richtext_editor()) {
        $defaultformat = FORMAT_HTML;
    } else {
        $defaultformat = FORMAT_MOODLE;
    }

    switch($_REQUEST['action']) {
        case 'delete':
            $title = get_string('deleteevent', 'calendar');
            $event = get_record('event', 'id', $_REQUEST['id']);
            if($event === false) {
                error('Invalid event');
            }
            if(!calendar_edit_event_allowed($event)) {
                error('You are not authorized to do this');
            }
        break;

        case 'edit':
            $title = get_string('editevent', 'calendar');
            $event = get_record('event', 'id', $_REQUEST['id']);
            if($event === false) {
                error('Invalid event');
            }
            if(!calendar_edit_event_allowed($event)) {
                error('You are not authorized to do this');
            }

            if($form = data_submitted()) {

                $form->name = strip_tags($form->name);  // Strip all tags
                $form->description = clean_text($form->description , $form->format);   // Clean up any bad tags

                $form->timestart = make_timestamp($form->startyr, $form->startmon, $form->startday, $form->starthr, $form->startmin);
                if($form->duration == 1) {
                    $form->timeduration = make_timestamp($form->endyr, $form->endmon, $form->endday, $form->endhr, $form->endmin) - $form->timestart;
                    if($form->timeduration < 0) {
                        $form->timeduration = 0;
                    }
                }
                else if($form->duration == 2) {
                    $form->timeduration = $form->minutes * 60;
                }
                else {
                    $form->timeduration = 0;
                }
                validate_form($form, $err);
                if (count($err) == 0) {
                    $form->timemodified = time();
                    update_record('event', $form);

                    /// Log the event update.
                    add_to_log($form->courseid, 'calendar', 'edit', 'event.php?action=edit&amp;id='.$form->id, $form->name);

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
            if(!empty($form) && $form->type == 'defined') {

                $form->name = strip_tags($form->name);  // Strip all tags
                $form->description = clean_text($form->description , $form->format);   // Clean up any bad tags

                $form->timestart = make_timestamp($form->startyr, $form->startmon, $form->startday, $form->starthr, $form->startmin);
                if($form->duration == 1) {
                    $form->timeduration = make_timestamp($form->endyr, $form->endmon, $form->endday, $form->endhr, $form->endmin) - $form->timestart;
                    if($form->timeduration < 0) {
                        $form->timeduration = 0;
                    }
                }
                else if ($form->duration == 2) {
                    $form->timeduration = $form->minutes * 60;
                }
                else {
                    $form->timeduration = 0;
                }
                if(!calendar_add_event_allowed($form->courseid, $form->groupid, $form->userid)) {
                    error('You are not authorized to do this');
                }
                validate_form($form, $err);
                if (count($err) == 0) {
                    $form->timemodified = time();

                    /// Get the event id for the log record.
                    $eventid = insert_record('event', $form, true);

                    /// Log the event entry.
                    add_to_log($form->courseid, 'calendar', 'add', 'event.php?action=edit&amp;id='.$eventid, $form->name);

                    if ($form->repeat) {
                        for($i = 1; $i < $form->repeats; $i++) {
                            $form->timestart += 604800;  // add one week
                            /// Get the event id for the log record.
                            $eventid = insert_record('event', $form, true);
                            /// Log the event entry.
                            add_to_log($form->courseid, 'calendar', 'add', 'event.php?action=edit&amp;id='.$eventid, $form->name);
                        }
                    }

                    // OK, now redirect to day view
                    redirect(CALENDAR_URL.'view.php?view=day&cal_d='.$form->startday.'&cal_m='.$form->startmon.'&cal_y='.$form->startyr);
                }
                else {
                    foreach ($err as $key => $value) {
                        $focus = 'form'.$key;
                    }
                }
            }
        break;
    }
    if(empty($focus)) $focus = '';

    // Let's see if we are supposed to provide a referring course link
    // but NOT for the "main page" course
    if($SESSION->cal_course_referer > 1 &&
      ($shortname = get_field('course', 'shortname', 'id', $SESSION->cal_course_referer)) !== false) {
        // If we know about the referring course, show a return link
        $nav = '<a href="'.$CFG->wwwroot.'/course/view.php?id='.$SESSION->cal_course_referer.'">'.$shortname.'</a> -> '.$nav;
    }

    print_header(get_string('calendar', 'calendar').': '.$title, $site->fullname, $nav.' -> '.$title,
                 $focus, '', true, '', '<p class="logininfo">'.user_login_string($site).'</p>');

    echo calendar_overlib_html();

    echo '<table border="0" cellpadding="3" cellspacing="0" width="100%"><tr valign="top">';
    echo '<td valign="top" width="100%">';

    switch($_REQUEST['action']) {
        case 'delete':
            if($_REQUEST['confirm'] == 1) {
                // Kill it and redirect to day view
                if(($event = get_record('event', 'id', $_REQUEST['id'])) !== false) {
                    /// Log the event delete.

                    delete_records('event', 'id', $_REQUEST['id']);

                    // pj - fixed the course id problem, but now we have another one:
                    // what to do with the URL?
                    add_to_log($event->courseid, 'calendar', 'delete', '', $event->name);
                }

                if(checkdate($_REQUEST['m'], $_REQUEST['d'], $_REQUEST['y'])) {
                    // Being a bit paranoid to check this, but it doesn't hurt
                    redirect(CALENDAR_URL.'view.php?view=day&cal_d='.$_REQUEST['d'].'&cal_m='.$_REQUEST['m'].'&cal_y='.$_REQUEST['y']);
                }
                else {
                    // Redirect to now
                    redirect(CALENDAR_URL.'view.php?view=day&cal_d='.$now['mday'].'&cal_m='.$now['mon'].'&cal_y='.$now['year']);
                }
            }
            else {
                $eventtime = usergetdate($event->timestart);
                $m = $eventtime['mon'];
                $d = $eventtime['mday'];
                $y = $eventtime['year'];
                // Display confirmation form
                print_side_block_start(get_string('deleteevent', 'calendar').': '.$event->name, '', 'mycalendar');
                include('event_delete.html');
                print_side_block_end();
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
                if($event->timeduration > 3600) {
                    // More than one hour, so default to normal duration mode
                    $form->duration = 1;
                    $form->minutes = '';
                }
                else if($event->timeduration) {
                    // Up to one hour, "minutes" mode probably is better here
                    $form->duration = 2;
                    $form->minutes = $event->timeduration / 60;
                }
                else {
                    // No duration
                    $form->duration = 0;
                    $form->minutes = '';
                }
            }
            print_side_block_start(get_string('editevent', 'calendar'), '', 'mycalendar');
            include('event_edit.html');
            print_side_block_end();
            if ($usehtmleditor) {
                use_html_editor("description");
            }
        break;

        case 'new':
            optional_variable($_GET['cal_y']);
            optional_variable($_GET['cal_m']);
            optional_variable($_GET['cal_d']);
            optional_variable($form->timestart, -1);

            if($_GET['cal_y'] && $_GET['cal_m'] && $_GET['cal_d'] && checkdate($_GET['cal_m'], $_GET['cal_d'], $_GET['cal_y'])) {
                $form->timestart = make_timestamp($_GET['cal_y'], $_GET['cal_m'], $_GET['cal_d'], 0, 0, 0);
            }
            else if($_GET['cal_y'] && $_GET['cal_m'] && checkdate($_GET['cal_m'], 1, $_GET['cal_y'])) {
                if($_GET['cal_y'] == $now['year'] && $_GET['cal_m'] == $now['mon']) {
                    $form->timestart = make_timestamp($_GET['cal_y'], $_GET['cal_m'], $now['mday'], 0, 0, 0);
                }
                else {
                    $form->timestart = make_timestamp($_GET['cal_y'], $_GET['cal_m'], 1, 0, 0, 0);
                }
            }
            if($form->timestart < 0) {
                $form->timestart = time();
            }

            calendar_get_allowed_types($allowed);
            if(!$allowed->groups && !$allowed->courses && !$allowed->site) {
                // Take the shortcut
                $_REQUEST['type'] = 'user';
            }

            $header = '';

            switch($_REQUEST['type']) {
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
                    $header = get_string('typeuser', 'calendar');
                break;
                case 'group':
                    optional_variable($_REQUEST['groupid']);
                    $groupid = $_REQUEST['groupid'];
                    if(!($group = get_record('groups', 'id', $groupid) )) {
                        calendar_get_allowed_types($allowed);
                        $_REQUEST['type'] = 'select';
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
                        $header = get_string('typegroup', 'calendar');
                    }
                break;
                case 'course':
                    optional_variable($_REQUEST['courseid']);
                    $courseid = $_REQUEST['courseid'];
                    if(!record_exists('course', 'id', $courseid)) {
                        calendar_get_allowed_types($allowed);
                        $_REQUEST['type'] = 'select';
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
                        $header = get_string('typecourse', 'calendar');
                    }
                break;
                case 'site':
                    $form->name = '';
                    $form->description = '';
                    $form->courseid = 1;
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
                    $header = get_string('typesite', 'calendar');
                break;
                case 'defined':
                case 'select':
                break;
                default:
                    error('Unsupported event type');
            }

            $form->format = $defaultformat;
            if(!empty($header)) {
                $header = ' ('.$header.')';
            }

            print_side_block_start(get_string('newevent', 'calendar').$header, '', 'mycalendar');
            if($_REQUEST['type'] == 'select') {
                $defaultcourse = $SESSION->cal_course_referer;
                if(isteacheredit($defaultcourse, $USER->id)) {
                    $defaultgroup = 0;
                }
                else {
                    $defaultgroup = user_group($defaultcourse, $USER->id);
                }
                optional_variable($_REQUEST['groupid'], $defaultgroup->id);
                optional_variable($_REQUEST['courseid'], $defaultcourse);
                $groupid = $_REQUEST['groupid'];
                $courseid = $_REQUEST['courseid'];
                include('event_select.html');
            }
            else {
                include('event_new.html');
                if ($usehtmleditor) {
                    use_html_editor("description");
                }
            }
            print_side_block_end();
        break;
    }
    echo '</td>';

    // START: Last column (3-month display)
    echo '<td style="vertical-align: top; width: 180px;">';

    $defaultcourses = calendar_get_default_courses();
    calendar_set_filters($courses, $groups, $users, $defaultcourses, $defaultcourses);

    print_side_block_start(get_string('monthlyview', 'calendar'), '', 'sideblockmain');
    list($prevmon, $prevyr) = calendar_sub_month($mon, $yr);
    list($nextmon, $nextyr) = calendar_add_month($mon, $yr);

    echo calendar_filter_controls('event', 'action='.$_REQUEST['action'].'&amp;type='.$_REQUEST['type'].'&amp;id='.$_REQUEST['id']);
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

function calendar_add_event_allowed($courseid, $groupid, $userid) {
    global $USER;

    if(isadmin()) {
        return true;
    }
    else if($courseid == 0 && $groupid == 0 && $userid == $USER->id) {
        return true;
    }
    else if($courseid != 0 && isteacheredit($courseid)) {
        return true;
    }

    return false;
}

function calendar_get_allowed_types(&$allowed) {
    global $USER, $CFG, $SESSION;

    $allowed->user = true; // User events always allowed
    $allowed->groups = false; // This may change just below
    $allowed->courses = false; // This may change just below
    $allowed->site = isadmin($USER->id);

    if(!empty($SESSION->cal_course_referer) && isteacheredit($SESSION->cal_course_referer, $USER->id)) {
        $allowed->courses = array($SESSION->cal_course_referer => 1);
        $allowed->groups = get_groups($SESSION->cal_course_referer);
    }

    //[pj]: This was used when we wanted to display all legal choices
    /*
    if($allowed->site) {
        $allowed->courses = get_courses('all', 'c.shortname');
        $allowed->groups = get_records_sql('SELECT g.*, c.fullname FROM '.$CFG->prefix.'groups g LEFT JOIN '.$CFG->prefix.'course c ON g.courseid = c.id ORDER BY c.shortname');
    }
    else if(!empty($USER->teacheredit)) {
        $allowed->courses = get_records_select('course', 'id != 1 AND id IN ('.implode(',', array_keys($USER->teacheredit)).')');
        $allowed->groups = get_records_sql('SELECT g.*, c.fullname FROM '.$CFG->prefix.'groups g LEFT JOIN '.$CFG->prefix.'course c ON g.courseid = c.id WHERE g.courseid IN ('.implode(',', array_keys($USER->teacheredit)).')');
    }
    */
}

?>
