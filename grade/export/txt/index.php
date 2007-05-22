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
require_once('grade_export_txt.php');

$id = required_param('id', PARAM_INT); // course id
$feedback = optional_param('feedback', '', PARAM_ALPHA);

// process post information
if (($data = data_submitted()) && confirm_sesskey()) {

    if (!is_array($data->itemids)) {
        $itemidsurl = $data->itemids;
    } else {
        $itemidsurl = implode(",",$data->itemids);
    }
        
    $course = get_record('course', 'id', $id);
    $action = 'exporttxt';
    print_header($course->shortname.': '.get_string('grades'), $course->fullname, grade_nav($course, $action));
    
    $export = new grade_export($id, $data->itemids);
    $export->display_grades($feedback);
    
    // this redirect should trigger a download prompt
    redirect('export.php?id='.$id.'&amp;itemids='.$itemidsurl.'&amp;separator='.$data->separator);
    exit; 
}


$course = get_record('course', 'id', $id);
$action = 'exporttxt';
print_header($course->shortname.': '.get_string('grades'), $course->fullname, grade_nav($course, $action));

// print_gradeitem_selections($id);
// print all items for selections
// make this a standard function in lib maybe
if ($grade_items = grade_get_items($id)) {
    echo '<form action="index.php" method="post">';
    echo '<div>';
    foreach ($grade_items as $grade_item) {
            
        echo '<br/><input type="checkbox" name="itemids[]" value="'.$grade_item->id.'" checked="checked"/>';
            
        if ($grade_item->itemtype == 'category') {
            // grade categories should be displayed bold
            echo '<b>'.$grade_item->itemname.'</b>';
        } else {
            echo $grade_item->itemname;
        } 
    }
    echo '<br/>';
    echo 'tab<input type="radio" name="separator" value="tab"/>';
    echo 'comma<input type="radio" name="separator" value="comma" checked="checked"/>';
    echo '<input type="hidden" name="id" value="'.$id.'"/>';
    echo '<input type="hidden" name="sesskey" value="'.sesskey().'"/>';
    echo '<br/>';
    echo '<input type="submit" value="'.get_string('submit').'" />';
    echo '</div>';
    echo '</form>';
}

print_footer();
?>