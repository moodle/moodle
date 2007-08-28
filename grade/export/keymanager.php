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

require_once '../../config.php';
require_once $CFG->dirroot.'/grade/export/lib.php';

$id     = required_param('id', PARAM_INT); // course id

if (!$course = get_record('course', 'id', $id)) {
    print_error('nocourseid');
}

require_login($course);
$context = get_context_instance(CONTEXT_COURSE, $id);

require_capability('moodle/grade:export', $context);

$strgrades = get_string('grades', 'grades');
$navigation = grade_build_nav(__FILE__, null, array('courseid' => $course->id));

print_header($course->shortname.': '.get_string('grades'), $course->fullname, $navigation);
print_grade_plugin_selector($id, '', '');

$stredit         = get_string('edit');
$strdelete       = get_string('delete');

$data = array();
if ($keys = get_records_select('user_private_key', "script='grade/export' AND instance={$course->id} AND userid={$USER->id}")) {
    foreach($keys as $key) {
        $line = array();
        $line[0] = format_string($key->value);
        $line[1] = $key->iprestriction;
        $line[2] = empty($key->validuntil) ? get_string('always') : userdate($key->validuntil);

        $buttons  = "<a title=\"$stredit\" href=\"key.php?id=$key->id\"><img".
                    " src=\"$CFG->pixpath/t/edit.gif\" class=\"iconsmall\" alt=\"$stredit\" /></a> ";
        $buttons .= "<a title=\"$strdelete\" href=\"key.php?id=$key->id&amp;delete=1\"><img".
                    " src=\"$CFG->pixpath/t/delete.gif\" class=\"iconsmall\" alt=\"$strdelete\" /></a> ";

        $line[3] = $buttons;
        $data[] = $line;
    }
}
$table->head  = array(get_string('keyvalue'), get_string('keyiprestriction'), get_string('keyvaliduntil'), $stredit);
$table->size  = array('50%', '30%', '10%', '10%');
$table->align = array('left', 'left', 'left', 'center');
$table->width = '90%';
$table->data  = $data;
print_table($table);

echo '<div class="buttons">';
print_single_button('key.php', array('courseid'=>$course->id), get_string('newuserkey'));
echo '</div>';

print_footer();
?>
