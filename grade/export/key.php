<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


require_once('../../config.php');
require_once('key_form.php');

/// get url variables
$courseid = optional_param('courseid', 0, PARAM_INT);
$id       = optional_param('id', 0, PARAM_INT);
$delete   = optional_param('delete', 0, PARAM_BOOL);
$confirm  = optional_param('confirm', 0, PARAM_BOOL);

if ($id) {
    if (!$key = get_record('user_private_key', 'id', $id)) {
        error('Group ID was incorrect');
    }
    if (empty($courseid)) {
        $courseid = $key->instance;

    } else if ($courseid != $key->instance) {
        error('Course ID was incorrect');
    }

    if (!$course = get_record('course', 'id', $courseid)) {
        error('Course ID was incorrect');
    }

} else {
    if (!$course = get_record('course', 'id', $courseid)) {
        error('Course ID was incorrect');
    }
    $key = new object();
}

$key->courseid = $course->id;

require_login($course);
$context = get_context_instance(CONTEXT_COURSE, $course->id);
require_capability('moodle/grade:export', $context);

// extra security check
if (!empty($key->userid) and $USER->id != $key->userid) {
    error('You are not owner of this key');
}

$returnurl = $CFG->wwwroot.'/grade/export/keymanager.php?id='.$course->id;

if ($id and $delete) {
    if (!$confirm) {
        print_header(get_string('deleteselectedkey'), get_string('deleteselectedkey'));
        $optionsyes = array('id'=>$id, 'delete'=>1, 'courseid'=>$courseid, 'sesskey'=>sesskey(), 'confirm'=>1);
        $optionsno  = array('id'=>$courseid);
        notice_yesno(get_string('deletekeyconfirm', 'userkey', $key->value), 'key.php', 'keymanager.php', $optionsyes, $optionsno, 'get', 'get');
        print_footer();
        die;

    } else if (confirm_sesskey()){
        delete_records('user_private_key', 'id', $id);
        redirect('keymanager.php?id='.$course->id);
    }
}

/// First create the form
$editform = new key_form();
$editform->set_data($key);

if ($editform->is_cancelled()) {
    redirect($returnurl);

} elseif ($data = $editform->get_data()) {

    if ($data->id) {
        $record = new object();
        $record->id            = $data->id;
        $record->iprestriction = $data->iprestriction;
        $record->validuntil    = $data->validuntil;
        update_record('user_private_key', $record);
    } else {
        create_user_key('grade/export', $USER->id, $course->id, $data->iprestriction, $data->validuntil);
    }

    redirect($returnurl);
}

$strkeys   = get_string('userkeys', 'userkey');
$strgrades = get_string('grades');

if ($id) {
    $strheading = get_string('edituserkey', 'userkey');
} else {
    $strheading = get_string('createuserkey', 'userkey');
}


$navlinks = array(array('name'=>$strgrades, 'link'=>$CFG->wwwroot.'/grade/index.php?id='.$courseid, 'type'=>'misc'),
                  array('name'=>$strkeys, 'link'=>$CFG->wwwroot.'/grade/export/keymanager.php?id='.$courseid, 'type'=>'misc'),
                  array('name'=>$strheading, 'link'=>'', 'type'=>'misc'));
$navigation = build_navigation($navlinks);

/// Print header
print_header_simple($strkeys, ': '.$strkeys, $navigation, '', '', true, '', navmenu($course));

$editform->display();
print_footer($course);
?>
