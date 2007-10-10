<?php  //$Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
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

$nomoodlecookie = true; // session not used here
require '../../../config.php';

$id = required_param('id', PARAM_INT); // course id
if (!$course = get_record('course', 'id', $id)) {
    print_error('nocourseid');
}

require_user_key_login('grade/export', $id); // we want different keys for each course

if (empty($CFG->gradepublishing)) {
    error('Grade publishing disabled');
}

$context = get_context_instance(CONTEXT_COURSE, $id);
require_capability('gradeexport/txt:publish', $context);

// use the same page parameters as export.php and append &key=sdhakjsahdksahdkjsahksadjksahdkjsadhksa
require 'export.php';

?>
