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

    require_login();

    require_variable($_GET['from']);
    require_variable($_GET['var']);
    optional_variable($_GET['value']);
    optional_variable($_GET['id']);
    optional_variable($_GET['cal_d']);
    optional_variable($_GET['cal_m']);
    optional_variable($_GET['cal_y']);

    switch($_GET['var']) {
        case 'setcourse':
            $id = intval($_GET['id']);
            if($id == 0) {
                $SESSION->cal_courses_shown = array();
                calendar_set_referring_course(0);
            }
            else if($id == 1) {
                $SESSION->cal_courses_shown = calendar_get_default_courses(true);
                calendar_set_referring_course(0);
            }
            else {
                // We don't check for membership anymore: if(isstudent($id, $USER->id) || isteacher($id, $USER->id)) {
                if(get_record('course', 'id', $id) === false) {
                    // There is no such course
                    $SESSION->cal_courses_shown = array();
                    calendar_set_referring_course(0);
                }
                else {
                    calendar_set_referring_course($id);
                    $SESSION->cal_courses_shown = $id;
                }
            }
        break;
        case 'showgroups':
            $SESSION->cal_show_groups = !$SESSION->cal_show_groups;
        break;
        case 'showcourses':
            $SESSION->cal_show_course = !$SESSION->cal_show_course;
        break;
        case 'showglobal':
            $SESSION->cal_show_global = !$SESSION->cal_show_global;
        break;
        case 'showuser':
            if($SESSION->cal_show_user) {
                $SESSION->cal_show_user = false;
            }
            else {
                $SESSION->cal_show_user = $USER->id;
            }
        break;
    }

    switch($_GET['from']) {
        case 'event':
            redirect(CALENDAR_URL.'event.php?action='.$_GET['action'].'&type='.$_GET['type'].'&id='.intval($_GET['id']));
        break;
        case 'month':
            redirect(CALENDAR_URL.'view.php?view=month&cal_d='.$_GET['cal_d'].'&cal_m='.$_GET['cal_m'].'&cal_y='.$_GET['cal_y']);
        break;
        case 'upcoming':
            redirect(CALENDAR_URL.'view.php?view=upcoming');
        break;
        case 'day':
            redirect(CALENDAR_URL.'view.php?view=day&cal_d='.$_GET['cal_d'].'&cal_m='.$_GET['cal_m'].'&cal_y='.$_GET['cal_y']);
        break;
        case 'course':
            redirect($CFG->wwwroot.'/course/view.php?id='.intval($_GET['id']));
        break;
        default:

    }
?>
