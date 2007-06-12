<?php // $Id$

///////////////////////////////////////////////////////////////////////////
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1999 onwards  Martin Dougiamas  http://moodle.com       //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////


    require_once("../config.php");
    require_once("../lib/gradelib.php");

    $id       = required_param('id');              // course id
    $report   = optional_param('report', '', PARAM_FILE);              // course id

    if (!$course = get_record('course', 'id', $id)) {
        errorcode('nocourseid');
    }

    require_login($course->id);

    $strgrades = get_string('grades');

    $crumbs[] = array('name' => $strgrades, 'link' => "view.php?f=$forum->id", 'type' => 'misc');
    
    $navigation = build_navigation($crumbs);
    
    print_header_simple($strgrades, "", $navigation, "", "", true, '', navmenu($course));


    print_heading('New interface under construction');

    
    print_footer($course);


?>
