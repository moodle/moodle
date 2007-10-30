<?php // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas  http://dougiamas.com     //
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

/// Add settings for this module to the $settings object (it's already defined)

$settings->add(new admin_setting_configcheckbox('grade_report_user_showrank', get_string('showrank', 'grades'), get_string('configshowrank', 'grades'), 0, PARAM_INT));

$options = array(0 => get_string('shownohidden', 'grades'),
                 1 => get_string('showhiddenuntilonly', 'grades'),
                 2 => get_string('showallhidden', 'grades'));
$settings->add(new admin_setting_configselect('grade_report_user_showhiddenitems', get_string('showhiddenitems', 'grades'), get_string('configshowhiddenitems', 'grades'), 1, $options));

?>
