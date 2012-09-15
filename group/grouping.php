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


/**
 * Create grouping OR edit grouping settings.
 *
 * @copyright 2006 The Open University, N.D.Freear AT open.ac.uk, J.White AT open.ac.uk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   core_group
 */
require_once('../config.php');
require_once('lib.php');
require_once('grouping_form.php');

/// get url variables
$courseid = optional_param('courseid', 0, PARAM_INT);
$id       = optional_param('id', 0, PARAM_INT);
$delete   = optional_param('delete', 0, PARAM_BOOL);
$confirm  = optional_param('confirm', 0, PARAM_BOOL);

$url = new moodle_url('/group/grouping.php');
if ($id) {
    $url->param('id', $id);
    if (!$grouping = $DB->get_record('groupings', array('id'=>$id))) {
        print_error('invalidgroupid');
    }
    $grouping->description = clean_text($grouping->description);
    if (empty($courseid)) {
        $courseid = $grouping->courseid;
    } else if ($courseid != $grouping->courseid) {
        print_error('invalidcourseid');
    } else {
        $url->param('courseid', $courseid);
    }

    if (!$course = $DB->get_record('course', array('id'=>$courseid))) {
        print_error('invalidcourseid');
    }

} else {
    $url->param('courseid', $courseid);
    if (!$course = $DB->get_record('course', array('id'=>$courseid))) {
        print_error('invalidcourseid');
    }
    $grouping = new stdClass();
    $grouping->courseid = $course->id;
}

$PAGE->set_url($url);

require_login($course);
$context = context_course::instance($course->id);
require_capability('moodle/course:managegroups', $context);

$returnurl = $CFG->wwwroot.'/group/groupings.php?id='.$course->id;


if ($id and $delete) {
    if (!empty($grouping->idnumber) && !has_capability('moodle/course:changeidnumber', $context)) {
        print_error('groupinghasidnumber', '', '', $grouping->name);
    }
    if (!$confirm) {
        $PAGE->set_title(get_string('deletegrouping', 'group'));
        $PAGE->set_heading($course->fullname. ': '. get_string('deletegrouping', 'group'));
        echo $OUTPUT->header();
        $optionsyes = array('id'=>$id, 'delete'=>1, 'courseid'=>$courseid, 'sesskey'=>sesskey(), 'confirm'=>1);
        $optionsno  = array('id'=>$courseid);
        $formcontinue = new single_button(new moodle_url('grouping.php', $optionsyes), get_string('yes'), 'get');
        $formcancel = new single_button(new moodle_url('groupings.php', $optionsno), get_string('no'), 'get');
        echo $OUTPUT->confirm(get_string('deletegroupingconfirm', 'group', $grouping->name), $formcontinue, $formcancel);
        echo $OUTPUT->footer();
        die;

    } else if (confirm_sesskey()){
        if (groups_delete_grouping($id)) {
            redirect($returnurl);
        } else {
            print_error('erroreditgrouping', 'group', $returnurl);
        }
    }
}

// Prepare the description editor: We do support files for grouping descriptions
$editoroptions = array('maxfiles'=>EDITOR_UNLIMITED_FILES, 'maxbytes'=>$course->maxbytes, 'trust'=>true, 'context'=>$context, 'noclean'=>true);
if (!empty($grouping->id)) {
    $grouping = file_prepare_standard_editor($grouping, 'description', $editoroptions, $context, 'grouping', 'description', $grouping->id);
} else {
    $grouping = file_prepare_standard_editor($grouping, 'description', $editoroptions, $context, 'grouping', 'description', null);
}

/// First create the form
$editform = new grouping_form(null, compact('editoroptions'));
$editform->set_data($grouping);

if ($editform->is_cancelled()) {
    redirect($returnurl);

} elseif ($data = $editform->get_data()) {
    $success = true;
    if (!has_capability('moodle/course:changeidnumber', $context)) {
        // Remove the idnumber if the user doesn't have permission to modify it
        unset($data->idnumber);
    }

    if ($data->id) {
        groups_update_grouping($data, $editoroptions);
    } else {
        groups_create_grouping($data, $editoroptions);
    }

    redirect($returnurl);

}

$strgroupings    = get_string('groupings', 'group');
$strparticipants = get_string('participants');

if ($id) {
    $strheading = get_string('editgroupingsettings', 'group');
} else {
    $strheading = get_string('creategrouping', 'group');
}

$PAGE->navbar->add($strparticipants, new moodle_url('/user/index.php', array('id'=>$courseid)));
$PAGE->navbar->add($strgroupings, new moodle_url('/group/groupings.php', array('id'=>$courseid)));
$PAGE->navbar->add($strheading);

/// Print header
$PAGE->set_title($strgroupings);
$PAGE->set_heading($course->fullname. ': '.$strgroupings);
echo $OUTPUT->header();
echo $OUTPUT->heading($strheading);
$editform->display();
echo $OUTPUT->footer();
