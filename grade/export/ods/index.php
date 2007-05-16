<?php
///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 2001-2003  Martin Dougiamas  http://dougiamas.com       //
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
require_once("../../../config.php");
require_once($CFG->dirroot.'/grade/export/lib.php');
require_once('grade_export_ods.php');

$id = required_param('id', PARAM_INT); // course id
$feedback = optional_param('feedback', '', PARAM_ALPHA);

// process post information
if ($data = data_submitted() && confirm_sesskey()) {

    if (!is_array($data->itemids)) {
        $itemidsurl = $data->itemids;
    } else {
        $itemidsurl = implode(",",$data->itemids);
    }
        
    // print the grades on screen for feedbacks
    print_header(get_string('grade'),get_string('grade'),get_string('grade'));
    $export = new grade_export($id, $data->itemids);
    $export->display_grades($feedback);
    print_footer();
    // this redirect should trigger a download prompt
    redirect('export.php?id='.$id.'&amp;itemids='.$itemidsurl);
    exit; 
}

print_gradeitem_selections($id);
?>