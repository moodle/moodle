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

    //  Edit calendar preferences

    require_once('../config.php');
    require_once('lib.php');
    require_once('../course/lib.php');
    require_once('../mod/forum/lib.php');

    require_login();

    if(!$site = get_site()) {
        redirect($CFG->wwwroot.'/'.$CFG->admin.'/index.php');
    }
    if(isguest()) {
        redirect($CFG->wwwroot.'/index.php');
    }

    optional_variable($_GET['edit'], '');
    optional_variable($_GET['commit'], 0);
    $knownpreference = false; // Defensive coding: let's be suspicious from the beginning
    $prefs = calendar_preferences_array(); // Get this now, we 'll be using it all the time

    if(in_array($_GET['edit'], array_keys($prefs))) {
        // If we know this preference, you 'll get to see the setting page
        $knownpreference = true;
    }

    if($_GET['commit']) {
        switch($_GET['edit']) {
            case 'timeformat':
                if($_GET['timeformat'] == '12') {
                    $timeformat = CALENDAR_TF_12;
                }
                else if($_GET['timeformat'] == '24') {
                    $timeformat = CALENDAR_TF_24;
                }
                else {
                    $timeformat = '';
                }
                set_user_preference('calendar_'.$_GET['edit'], $timeformat);
            break;
            case 'startwday':
                $day = intval($_GET[$_GET['edit']]);
                if($day < 0 || $day > 6) {
                    $day = abs($day % 7);
                }
                set_user_preference('calendar_'.$_GET['edit'], $day);
            break;
            case 'maxevents':
                $events = intval($_GET[$_GET['edit']]);
                if($events >= 1) {
                    set_user_preference('calendar_'.$_GET['edit'], $events);
                }
            break;
            case 'lookahead':
                $days = intval($_GET[$_GET['edit']]);
                if($days >= 1) {
                    set_user_preference('calendar_'.$_GET['edit'], $days);
                }
            break;
        }
        // Use this trick to get back to the preferences list after editing one
        $knownpreference = false;
        $_GET['edit'] = '';
    }

    $firstcolumn = false;  // for now
    $side = 175;

    calendar_session_vars();

    $now = usergetdate(time());
    $pagetitle = get_string('preferences', 'calendar');
    $nav = calendar_get_link_tag(get_string('calendar', 'calendar'), $CFG->wwwroot.'/calendar/view.php?view=upcoming&amp;', $now['mday'], $now['mon'], $now['year']);
    if($knownpreference) {
        $nav .= ' -> <a href="'.$CFG->wwwroot.'/calendar/preferences.php">'.$pagetitle.'</a> -> '.$prefs[$_GET['edit']];
    }
    else {
        $nav .= ' -> '.$pagetitle;
    }

    // Let's see if we are supposed to provide a referring course link
    // but NOT for the front page
    if($SESSION->cal_course_referer > 1 &&
      ($shortname = get_field('course', 'shortname', 'id', $SESSION->cal_course_referer)) !== false) {
        // If we know about the referring course, show a return link
        $nav = '<a href="'.$CFG->wwwroot.'/course/view.php?id='.$SESSION->cal_course_referer.'">'.$shortname.'</a> -> '.$nav;
    }

    print_header(get_string('calendar', 'calendar').': '.$pagetitle, $site->fullname, $nav,
                 '', '', true, '', '<p class="logininfo">'.user_login_string($site).'</p>');

    echo calendar_overlib_html();

    // Layout the whole page as three big columns.
    echo '<table border="0" cellpadding="3" cellspacing="0" width="100%">';

    // START: The main column
    echo '<tr valign="top">';

    echo '<td width="100%" valign="top\">';

    $text = '<div style="float: left;">'.get_string('calendarheading', 'calendar', strip_tags($site->shortname)).'</div><div style="float: right;">';
    $text.= calendar_get_preferences_menu();
    $text.= '</div>';

    print_heading_block($text);
    print_spacer(8,1);

    $defaultcourses = calendar_get_default_courses();

    $courses = array();

    calendar_set_filters($courses, $groups, $users, $defaultcourses, $defaultcourses);

    $day = $now['mday'];
    $mon = $now['mon'];
    $yr = $now['year'];

    if($knownpreference) {
        print_side_block_start($prefs[$_GET['edit']], '', 'mycalendar');
        echo '<form name="preference" method="get" action="preferences.php"><p style="text-align: justify;">';
        print_string('explain_'.$_GET['edit'], 'calendar');
        echo '</p><div style="text-align: center;"><table style="margin: auto;"><tr>';
        echo '<td><strong>'.$prefs[$_GET['edit']].':</strong></td>';
    }

    switch($_GET['edit']) {
        case 'timeformat':
            $sel = array('default' => ' selected="selected"', '12' => '', '24' => '');
            switch(get_user_preferences('calendar_timeformat', '')) {
                case CALENDAR_TF_12:
                    $sel['12'] = $sel['default'];
                    $sel['default'] = '';
                break;
                case CALENDAR_TF_24:
                    $sel['24'] = $sel['default'];
                    $sel['default'] = '';
                break;
            }
            echo '<td><select name="timeformat">';
            echo '<option value="default"'.$sel['default'].'>'.get_string('default', 'calendar').'</option>';
            echo '<option value="12"'.$sel['12'].'>'.get_string('timeformat_12', 'calendar').'</option>';
            echo '<option value="24"'.$sel['24'].'>'.get_string('timeformat_24', 'calendar').'</option>';
            echo '</select></td>';
        break;
        case 'startwday':
            echo '<td>';
            $days = array(
                get_string('sunday', 'calendar'), get_string('monday', 'calendar'),
                get_string('tuesday', 'calendar'), get_string('wednesday', 'calendar'),
                get_string('thursday', 'calendar'), get_string('friday', 'calendar'),
                get_string('saturday', 'calendar'));
            choose_from_menu($days, 'startwday', get_user_preferences('calendar_startwday', CALENDAR_STARTING_WEEKDAY), '');
            echo '</td>';
        break;
        case 'maxevents':
            echo '<td><input type="text" name="maxevents" size="5" value="'.get_user_preferences('calendar_maxevents', CALENDAR_UPCOMING_MAXEVENTS).'" /></td>';
        break;
        case 'lookahead':
            echo '<td><input type="text" name="lookahead" size="5" value="'.get_user_preferences('calendar_lookahead', CALENDAR_UPCOMING_DAYS).'" /></td>';
        break;
        default:
            // Print a form displaying all the preferences and their values
            print_side_block_start(get_string('preferences', 'calendar'), '', 'mycalendar');
            echo '<div style="text-align: center; font-weight: bold;">'.get_string('preferences_available', 'calendar').'</div>';
            echo '<p style="text-align: center;"><table style="width: 100%">';

            // Get the actual values of all preferences
            $values = array();
            foreach($prefs as $name => $description) {
                $values[$name] = get_user_preferences('calendar_'.$name);
            }

            // Fix 'display-friendly' values now
            $days = array(
                get_string('sunday', 'calendar'), get_string('monday', 'calendar'),
                get_string('tuesday', 'calendar'), get_string('wednesday', 'calendar'),
                get_string('thursday', 'calendar'), get_string('friday', 'calendar'),
                get_string('saturday', 'calendar'));
            $values['startwday'] = $days[$values['startwday']];
            switch($values['timeformat']) {
                case '':
                    $values['timeformat'] = get_string('default', 'calendar');
                break;
                case CALENDAR_TF_12:
                    $values['timeformat'] = get_string('timeformat_12', 'calendar');
                break;
                case CALENDAR_TF_24:
                    $values['timeformat'] = get_string('timeformat_24', 'calendar');
                break;
            }

            // OK, display them
            foreach($prefs as $name => $description) {
                echo '<tr><td style="text-align: right; width: 50%;"><a href="preferences.php?edit='.$name.'">'.$description.'</a>:</td>';
                echo '<td>'.($values[$name] === NULL?get_string('default', 'calendar'):$values[$name]) .'</td></tr>';
            }

            // Done
            echo '</table></p>';
            print_side_block_end();
        break;
    }

    if($knownpreference) {
        echo '</tr></table>';
        echo '<p><input type="submit" value=" '.get_string('ok').' "/></p>';
        echo '</div>';
        echo '<p><input type="hidden" name="commit" value="1" /><input type="hidden" name="edit" value="'.$_GET['edit'].'" />';
        echo '</form>';
        print_side_block_end();
    }

    echo '</td>';
    // END: Middle column

    // START: Last column (3-month display)
    echo '<td valign="top" width="'.$side.'">';
    print_side_block_start(get_string('monthlyview', 'calendar'), '', 'sideblockmain');
    list($prevmon, $prevyr) = calendar_sub_month($mon, $yr);
    list($nextmon, $nextyr) = calendar_add_month($mon, $yr);

    echo calendar_filter_controls('prefs');
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
    // END: Last column (3-month display)

    echo '</tr></table>';
    print_footer();

?>
