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

require_once('../config.php');
require_once($CFG->dirroot.'/calendar/lib.php');

require_sesskey();

$var = required_param('var', PARAM_ALPHA);
$return = clean_param(base64_decode(required_param('return', PARAM_RAW)), PARAM_URL);
$courseid = optional_param('id', -1, PARAM_INT);
if ($courseid != -1) {
    $return = new moodle_url($return, array('course' => $courseid));
} else {
    $return = new moodle_url($return);
}
$url = new moodle_url('/calendar/set.php', array('return'=>base64_encode($return->out(false)), 'course' => $courseid, 'var'=>$var, 'sesskey'=>sesskey()));
$PAGE->set_url($url);
$PAGE->set_context(get_context_instance(CONTEXT_SYSTEM));

switch($var) {
    case 'showgroups':
        calendar_set_event_type_display(CALENDAR_EVENT_GROUP);
        break;
    case 'showcourses':
        calendar_set_event_type_display(CALENDAR_EVENT_COURSE);
        break;
    case 'showglobal':
        calendar_set_event_type_display(CALENDAR_EVENT_GLOBAL);
        break;
    case 'showuser':
        calendar_set_event_type_display(CALENDAR_EVENT_USER);
        break;
}

redirect($return);
