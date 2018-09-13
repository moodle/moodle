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
 * Allows default warnings to be modified.
 *
 * @package   mod_attendance
 * @copyright 2017 Dan Marsden http://danmarsden.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__.'/../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/formslib.php');
require_once($CFG->dirroot.'/mod/attendance/lib.php');
require_once($CFG->dirroot.'/mod/attendance/locallib.php');

$action = optional_param('action', '', PARAM_ALPHA);
$notid = optional_param('notid', 0, PARAM_INT);
$id = optional_param('id', 0, PARAM_INT);

$url = new moodle_url('/mod/attendance/warnings.php');

// This page is used for configuring default set and for configuring attendance level set.
if (empty($id)) {
    // This is the default status set - show appropriate admin stuff and check admin permissions.
    admin_externalpage_setup('managemodules');

    $output = $PAGE->get_renderer('mod_attendance');
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('defaultwarnings', 'mod_attendance'));
    $tabmenu = attendance_print_settings_tabs('defaultwarnings');
    echo $tabmenu;

} else {
    // This is an attendance level config.
    $cm             = get_coursemodule_from_id('attendance', $id, 0, false, MUST_EXIST);
    $course         = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $att            = $DB->get_record('attendance', array('id' => $cm->instance), '*', MUST_EXIST);

    require_login($course, false, $cm);
    $context = context_module::instance($cm->id);
    require_capability('mod/attendance:changepreferences', $context);

    $att = new mod_attendance_structure($att, $cm, $course, $PAGE->context);

    $PAGE->set_url($url);
    $PAGE->set_title($course->shortname. ": ".$att->name);
    $PAGE->set_heading($course->fullname);
    $PAGE->navbar->add($att->name);

    $output = $PAGE->get_renderer('mod_attendance');
    $tabs = new attendance_tabs($att, attendance_tabs::TAB_WARNINGS);
    echo $output->header();
    echo $output->heading(get_string('attendanceforthecourse', 'attendance').' :: ' .format_string($course->fullname));
    echo $output->render($tabs);

}

$mform = new mod_attendance_add_warning_form($url, array('notid' => $notid, 'id' => $id));

if ($data = $mform->get_data()) {
    if (empty($data->notid)) {
        // Insert new record.
        $notify = new stdClass();
        if (empty($id)) {
            $notify->idnumber = 0;
        } else {
            $notify->idnumber = $att->id;
        }

        $notify->warningpercent = $data->warningpercent;
        $notify->warnafter = $data->warnafter;
        $notify->maxwarn = $data->maxwarn;
        $notify->emailuser = empty($data->emailuser) ? 0 : $data->emailuser;
        $notify->emailsubject = $data->emailsubject;
        $notify->emailcontent = $data->emailcontent['text'];
        $notify->emailcontentformat = $data->emailcontent['format'];
        $notify->thirdpartyemails = '';
        if (!empty($data->thirdpartyemails)) {
            $notify->thirdpartyemails = implode(',', $data->thirdpartyemails);
        }
        $existingrecord = $DB->record_exists('attendance_warning', array('idnumber' => $notify->idnumber,
                                                                         'warningpercent' => $notify->warningpercent,
                                                                              'warnafter' => $notify->warnafter));
        if (empty($existingrecord)) {
            $DB->insert_record('attendance_warning', $notify);
            echo $OUTPUT->notification(get_string('warningupdated', 'mod_attendance'), 'success');
        } else {
            echo $OUTPUT->notification(get_string('warningfailed', 'mod_attendance'), 'warning');
        }

    } else {
        $notify = $DB->get_record('attendance_warning', array('id' => $data->notid));
        if (!empty($id) && $data->idnumber != $att->id) {
            // Someone is trying to update a record for a different attendance.
            print_error('invalidcoursemodule');
        } else {
            $notify = new stdClass();
            $notify->id = $data->notid;
            $notify->idnumber = $data->idnumber;
            $notify->warningpercent = $data->warningpercent;
            $notify->warnafter = $data->warnafter;
            $notify->maxwarn = $data->maxwarn;
            $notify->emailuser = empty($data->emailuser) ? 0 : $data->emailuser;
            $notify->emailsubject = $data->emailsubject;
            $notify->emailcontentformat = $data->emailcontent['format'];
            $notify->emailcontent = $data->emailcontent['text'];
            $notify->thirdpartyemails = '';
            if (!empty($data->thirdpartyemails)) {
                $notify->thirdpartyemails = implode(',', $data->thirdpartyemails);
            }
            $existingrecord = $DB->get_record('attendance_warning', array('idnumber' => $notify->idnumber,
                'warningpercent' => $notify->warningpercent, 'warnafter' => $notify->warnafter));
            if (empty($existingrecord) || $existingrecord->id == $notify->id) {
                $DB->update_record('attendance_warning', $notify);
                echo $OUTPUT->notification(get_string('warningupdated', 'mod_attendance'), 'success');
            } else {
                echo $OUTPUT->notification(get_string('warningfailed', 'mod_attendance'), 'error');
            }
        }
    }
}
if ($action == 'delete' && !empty($notid)) {
    if (!optional_param('confirm', false, PARAM_BOOL)) {
        $cancelurl = $url;
        $url->params(array('action' => 'delete', 'notid' => $notid, 'sesskey' => sesskey(), 'confirm' => true, 'id' => $id));
        echo $OUTPUT->confirm(get_string('deletewarningconfirm', 'mod_attendance'), $url, $cancelurl);
        echo $OUTPUT->footer();
        exit;
    } else {
        require_sesskey();
        $params = array('id' => $notid);
        if (!empty($att)) {
            // Add id/level to array.
            $params['idnumber'] = $att->id;
        }
        $DB->delete_records('attendance_warning', $params);
        echo $OUTPUT->notification(get_string('warningdeleted', 'mod_attendance'), 'success');
    }
}
if ($action == 'update' && !empty($notid)) {
    $existing = $DB->get_record('attendance_warning', array('id' => $notid));
    $content = $existing->emailcontent;
    $existing->emailcontent = array();
    $existing->emailcontent['text'] = $content;
    $existing->emailcontent['format'] = $existing->emailcontentformat;
    $existing->notid = $existing->id;
    $existing->id = $id;
    $mform->set_data($existing);
    $mform->display();
} else if ($action == 'add' && confirm_sesskey()) {
    $mform->display();
} else {
    if (empty($id)) {
        $warningdesc = get_string('warningdesc', 'mod_attendance');
        $idnumber = 0;
    } else {
        $warningdesc = get_string('warningdesc_course', 'mod_attendance');
        $idnumber = $att->id;
    }
    echo $OUTPUT->box($warningdesc, 'generalbox attendancedesc', 'notice');
    $existingnotifications = $DB->get_records('attendance_warning',
        array('idnumber' => $idnumber),
        'warningpercent');

    if (!empty($existingnotifications)) {
        $table = new html_table();
        $table->head = array(get_string('warningthreshold', 'mod_attendance'),
            get_string('numsessions', 'mod_attendance'),
            get_string('emailsubject', 'mod_attendance'),
            '');
        foreach ($existingnotifications as $notification) {
            $url->params(array('action' => 'delete', 'notid' => $notification->id, 'id' => $id));
            $actionbuttons = $OUTPUT->action_icon($url, new pix_icon('t/delete',
                get_string('delete', 'attendance')), null, null);
            $url->params(array('action' => 'update', 'notid' => $notification->id, 'id' => $id));
            $actionbuttons .= $OUTPUT->action_icon($url, new pix_icon('t/edit',
                get_string('update', 'attendance')), null, null);
            $table->data[] = array($notification->warningpercent, $notification->warnafter,
                                   $notification->emailsubject, $actionbuttons);
        }
        echo html_writer::table($table);
    }
    $addurl = new moodle_url('/mod/attendance/warnings.php', array('action' => 'add', 'id' => $id));
    echo $OUTPUT->single_button($addurl, get_string('addwarning', 'mod_attendance'));

}

echo $OUTPUT->footer();