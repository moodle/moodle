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
    $_REQUEST['id'] = intval($_REQUEST['id']); // Always a good idea, against SQL injections

    if(!$site = get_site()) {
        redirect($CFG->wwwroot.'/'.$CFG->admin.'/index.php');
    }

    $firstcolumn = false;  // for now
    $side = 175;

    calendar_session_vars();
    $now = usergetdate(time());
    $nav = calendar_get_link_tag(get_string('calendar', 'calendar'), CALENDAR_URL.'view.php?view=upcoming&amp;', $now['mday'], $now['mon'], $now['year']);

    // Minor hack to default to FORMAT_MOODLE and no html editor for now
    // maybe in the future...

    if ($usehtmleditor = can_use_richtext_editor() && false) {
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
                $form->description = strip_tags($form->description);  // Strip all tags
                //$form->description = clean_text($form->description , $form->format);   // Clean up any bad tags

                $form->timestart = make_timestamp($form->startyr, $form->startmon, $form->startday, $form->starthr, $form->startmin);
                if($form->duration == 1) {
                    $form->timeduration = make_timestamp($form->endyr, $form->endmon, $form->endday, $form->endhr, $form->endmin) - $form->timestart;
                    if($form->timeduration < 0) {
                        $form->timeduration = 0;
                    }
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
                        $focus = "form.$key";
                    }
                }
            }
        break;

        case 'new':
            $title = get_string('newevent', 'calendar');
            $form = data_submitted();
            if(!empty($form) && $form->type == 'defined') {

                $form->name = strip_tags($form->name);  // Strip all tags
                $form->description = strip_tags($form->description);  // Strip all tags
                //$form->description = clean_text($form->description , $form->format);   // Clean up any bad tags

                $form->timestart = make_timestamp($form->startyr, $form->startmon, $form->startday, $form->starthr, $form->startmin);
                if($form->duration == 1) {
                    $form->timeduration = make_timestamp($form->endyr, $form->endmon, $form->endday, $form->endhr, $form->endmin) - $form->timestart;
                    if($form->timeduration < 0) {
                        $form->timeduration = 0;
                    }
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

                    // OK, now redirect to day view
                    redirect(CALENDAR_URL.'view.php?view=day&cal_d='.$form->startday.'&cal_m='.$form->startmon.'&cal_y='.$form->startyr);
                }
                else {
                    foreach ($err as $key => $value) {
                        $focus = "form.$key";
                    }
                }
            }
        break;
    }
    if(empty($focus)) $focus = '';

    // Let's see if we are supposed to provide a referring course link
    // but NOT for the front page
    if($SESSION->cal_course_referer > 1 &&
      ($shortname = get_field('course', 'shortname', 'id', $SESSION->cal_course_referer)) !== false) {
        // If we know about the referring course, show a return link
        $nav = '<a href="'.$CFG->wwwroot.'/course/view.php?id='.$SESSION->cal_course_referer.'">'.$shortname.'</a> -> '.$nav;
    }

    print_header(get_string('calendar', 'calendar').': '.$title, $site->fullname, $nav.' -> '.$title,
                 $focus, '', true, '', '<p class="logininfo">'.user_login_string($site).'</p>');

    /// Layout the whole page as three big columns.
    echo '<table border="0" cellpadding="3" cellspacing="0" width="100%"><tr valign="top">';

    $sections = get_all_sections($site->id);

    if ($site->newsitems > 0 or $sections[0]->sequence or isediting($site->id) or isadmin()) {
        echo "<td width=\"$side\" valign='top nowrap'>";
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
        echo '<td valign="top\">';
    }
    else {
        echo '<td valign="top">';
    }

    $text = '<div style="float: left;">'.get_string('calendarheading', 'calendar', strip_tags($site->shortname)).'</div><div style="float: right;">';
    $text.= calendar_get_preferences_menu();
    $text.= '</div>';
    print_heading_block($text);
    print_spacer(8, 1);

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
                if($event->timeduration) {
                    $form->duration = 1;
                }
                else {
                    $form->duration = 0;
                }
            }
            print_side_block_start(get_string('editevent', 'calendar'), '', 'mycalendar');
            include('event_edit.html');
            print_side_block_end();
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

            if(!isset($_REQUEST['type'])) {
                // We don't know what kind of event we want
                calendar_get_allowed_types($allowed);
                if(!$allowed->groups && !$allowed->courses && !$allowed->site) {
                    // Take the shortcut
                    $_REQUEST['type'] = 'user';
                }
                else {
                    $_REQUEST['type'] = 'select';
                }
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
                include('event_select.html');
            }
            else {
                include('event_new.html');
            }
            print_side_block_end();
        break;
    }


    echo '</td></table>';

    if ($usehtmleditor) {
        use_html_editor();
    }

    print_footer();


function validate_form(&$form, &$err) {
    if(empty($form->name)) {
        $err['name'] = get_string('errornoeventname', 'calendar');
    }
    if(empty($form->description)) {
        $err['description'] = get_string('errornodescription', 'calendar');
    }
    if(!checkdate($form->startmon, $form->startday, $form->startyr)) {
        $err['timestart'] = get_string('errorinvaliddate', 'calendar');
    }
    if(!checkdate($form->endmon, $form->endday, $form->endyr)) {
        $err['timeduration'] = get_string('errorinvaliddate', 'calendar');
    }
    if(!empty($form->courseid)) {
        // Timestamps must be >= course startdate
        $course = get_record('course', 'id', $form->courseid);
        if($course === false) {
            error('Event belongs to invalid course');
        }
        else if($form->timestart < $form->startdate) {
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
    global $USER, $CFG;

    $allowed->user = true; // User events always allowed
    $allowed->groups = false; // This may change just below
    $allowed->courses = false; // This may change just below
    $allowed->site = isadmin();
    if(!empty($USER->teacheredit)) {
        $allowed->courses = get_records_select('course', 'id != 1 AND id IN ('.implode(',', array_keys($USER->teacheredit)).')');
        $allowed->groups = get_records_sql('SELECT g.*, c.fullname FROM '.$CFG->prefix.'groups g LEFT JOIN '.$CFG->prefix.'course c ON g.courseid = c.id WHERE g.courseid IN ('.implode(',', array_keys($USER->teacheredit)).')');
    }
}

?>
