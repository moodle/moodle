<?php


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

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    // Can students see the report? By default they can, but a teacher can disable this
    $settings->add(
        new admin_setting_configcheckbox(
            'grade_report_grade_breakdown_allowstudents',
            get_string('allowstudents', 'gradereport_grade_breakdown'),
            get_string('allowstudents_desc', 'gradereport_grade_breakdown'),
            0
        )
    );
}
