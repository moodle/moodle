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
 * Adding attendance sessions
 *
 * @package    mod_attendance
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__).'/../../config.php');
require_once(dirname(__FILE__).'/locallib.php');
require_once($CFG->dirroot.'/lib/formslib.php');

$pageparams = new mod_attendance_sessions_page_params();

$id                     = required_param('id', PARAM_INT);
$pageparams->action     = required_param('action', PARAM_INT);

if (optional_param('deletehiddensessions', false, PARAM_TEXT)) {
    $pageparams->action = mod_attendance_sessions_page_params::ACTION_DELETE_HIDDEN;
}

if (empty($pageparams->action)) {
    // The form on manage.php can submit with the "choose" option - this should be fixed in the long term,
    // but in the meantime show a useful error and redirect when it occurs.
    $url = new moodle_url('/mod/attendance/view.php', array('id' => $id));
    redirect($url, get_string('invalidaction', 'mod_attendance'), 2);
}

$cm             = get_coursemodule_from_id('attendance', $id, 0, false, MUST_EXIST);
$course         = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$att            = $DB->get_record('attendance', array('id' => $cm->instance), '*', MUST_EXIST);

require_login($course, true, $cm);

$context = context_module::instance($cm->id);
require_capability('mod/attendance:manageattendances', $context);

$att = new mod_attendance_structure($att, $cm, $course, $context, $pageparams);

$PAGE->set_url($att->url_sessions(array('action' => $pageparams->action)));
$PAGE->set_title($course->shortname. ": ".$att->name);
$PAGE->set_heading($course->fullname);
$PAGE->force_settings_menu(true);
$PAGE->set_cacheable(true);
$PAGE->navbar->add($att->name);

$currenttab = attendance_tabs::TAB_ADD;
$formparams = array('course' => $course, 'cm' => $cm, 'modcontext' => $context, 'att' => $att);
switch ($att->pageparams->action) {
    case mod_attendance_sessions_page_params::ACTION_ADD:
        $url = $att->url_sessions(array('action' => mod_attendance_sessions_page_params::ACTION_ADD));
        $mform = new \mod_attendance\form\addsession($url, $formparams);

        if ($mform->is_cancelled()) {
            redirect($att->url_manage());
        }

        if ($formdata = $mform->get_data()) {
            $sessions = attendance_construct_sessions_data_for_add($formdata, $att);
            $att->add_sessions($sessions);
            if (count($sessions) == 1) {
                $message = get_string('sessiongenerated', 'attendance');
            } else {
                $message = get_string('sessionsgenerated', 'attendance', count($sessions));
            }

            mod_attendance_notifyqueue::notify_success($message);
            // Redirect to the sessions tab always showing all sessions.
            $SESSION->attcurrentattview[$cm->course] = ATT_VIEW_ALL;
            redirect($att->url_manage());
        }
        break;
    case mod_attendance_sessions_page_params::ACTION_UPDATE:
        $sessionid = required_param('sessionid', PARAM_INT);

        $url = $att->url_sessions(array('action' => mod_attendance_sessions_page_params::ACTION_UPDATE, 'sessionid' => $sessionid));
        $formparams['sessionid'] = $sessionid;
        $mform = new \mod_attendance\form\updatesession($url, $formparams);

        if ($mform->is_cancelled()) {
            redirect($att->url_manage());
        }

        if ($formdata = $mform->get_data()) {
            if (empty($formdata->autoassignstatus)) {
                $formdata->autoassignstatus = 0;
            }
            $att->update_session_from_form_data($formdata, $sessionid);

            mod_attendance_notifyqueue::notify_success(get_string('sessionupdated', 'attendance'));
            redirect($att->url_manage());
        }
        $currenttab = attendance_tabs::TAB_UPDATE;
        break;
    case mod_attendance_sessions_page_params::ACTION_DELETE:
        $sessionid = required_param('sessionid', PARAM_INT);
        $confirm   = optional_param('confirm', null, PARAM_INT);

        if (isset($confirm) && confirm_sesskey()) {
            $att->delete_sessions(array($sessionid));
            attendance_update_users_grade($att);
            redirect($att->url_manage(), get_string('sessiondeleted', 'attendance'));
        }

        $sessinfo = $att->get_session_info($sessionid);

        $message = get_string('deletecheckfull', 'attendance', get_string('session', 'attendance'));
        $message .= str_repeat(html_writer::empty_tag('br'), 2);
        $message .= userdate($sessinfo->sessdate, get_string('strftimedmyhm', 'attendance'));
        $message .= html_writer::empty_tag('br');
        $message .= $sessinfo->description;

        $params = array('action' => $att->pageparams->action, 'sessionid' => $sessionid, 'confirm' => 1, 'sesskey' => sesskey());

        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('attendanceforthecourse', 'attendance').' :: ' .format_string($course->fullname));
        echo $OUTPUT->confirm($message, $att->url_sessions($params), $att->url_manage());
        echo $OUTPUT->footer();
        exit;
    case mod_attendance_sessions_page_params::ACTION_DELETE_SELECTED:
        $confirm    = optional_param('confirm', null, PARAM_INT);
        $message = get_string('deletecheckfull', 'attendance', get_string('sessions', 'attendance'));

        if (isset($confirm) && confirm_sesskey()) {
            $sessionsids = required_param('sessionsids', PARAM_ALPHANUMEXT);
            $sessionsids = explode('_', $sessionsids);
            if ($att->pageparams->action == mod_attendance_sessions_page_params::ACTION_DELETE_SELECTED) {
                $att->delete_sessions($sessionsids);
                attendance_update_users_grade($att);
                redirect($att->url_manage(), get_string('sessiondeleted', 'attendance'));
            }
        }
        $sessid = optional_param_array('sessid', '', PARAM_SEQUENCE);
        if (empty($sessid)) {
            throw new moodle_exception('nosessionsselected', 'mod_attendance', $att->url_manage());
        }
        $sessionsinfo = $att->get_sessions_info($sessid);

        $message .= html_writer::empty_tag('br');
        foreach ($sessionsinfo as $sessinfo) {
            $message .= html_writer::empty_tag('br');
            $message .= userdate($sessinfo->sessdate, get_string('strftimedmyhm', 'attendance'));
            $message .= html_writer::empty_tag('br');
            $message .= $sessinfo->description;
        }

        $sessionsids = implode('_', $sessid);
        $params = array('action' => $att->pageparams->action, 'sessionsids' => $sessionsids,
                        'confirm' => 1, 'sesskey' => sesskey());

        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('attendanceforthecourse', 'attendance').' :: ' .format_string($course->fullname));
        echo $OUTPUT->confirm($message, $att->url_sessions($params), $att->url_manage());
        echo $OUTPUT->footer();
        exit;
    case mod_attendance_sessions_page_params::ACTION_CHANGE_DURATION:
        $sessid = optional_param_array('sessid', '', PARAM_SEQUENCE);
        $ids = optional_param('ids', '', PARAM_ALPHANUMEXT);

        $slist = !empty($sessid) ? implode('_', $sessid) : '';

        $url = $att->url_sessions(array('action' => mod_attendance_sessions_page_params::ACTION_CHANGE_DURATION));
        $formparams['ids'] = $slist;
        $mform = new mod_attendance\form\duration($url, $formparams);

        if ($mform->is_cancelled()) {
            redirect($att->url_manage());
        }

        if ($formdata = $mform->get_data()) {
            $sessionsids = explode('_', $ids);
            $duration = $formdata->durtime['hours'] * HOURSECS + $formdata->durtime['minutes'] * MINSECS;
            $att->update_sessions_duration($sessionsids, $duration);
            redirect($att->url_manage(), get_string('sessionupdated', 'attendance'));
        }

        if ($slist === '') {
            throw new moodle_exception('nosessionsselected', 'mod_attendance', $att->url_manage());
        }

        break;
    case mod_attendance_sessions_page_params::ACTION_DELETE_HIDDEN:
        $confirm  = optional_param('confirm', null, PARAM_INT);
        if ($confirm && confirm_sesskey()) {
            $sessions = $att->get_hidden_sessions();
            $att->delete_sessions(array_keys($sessions));
            redirect($att->url_manage(), get_string('hiddensessionsdeleted', 'attendance'));
        }

        $a = new stdClass();
        $a->count = $att->get_hidden_sessions_count();
        $a->date = userdate($course->startdate);
        $message = get_string('confirmdeletehiddensessions', 'attendance', $a);

        $params = array('action' => $att->pageparams->action, 'confirm' => 1, 'sesskey' => sesskey());
        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('attendanceforthecourse', 'attendance').' :: ' .format_string($course->fullname));
        echo $OUTPUT->confirm($message, $att->url_sessions($params), $att->url_manage());
        echo $OUTPUT->footer();
        exit;
}

$output = $PAGE->get_renderer('mod_attendance');
$tabs = new attendance_tabs($att, $currenttab);
echo $output->header();
echo $output->heading(get_string('attendanceforthecourse', 'attendance').' :: ' .format_string($course->fullname));
echo $output->render($tabs);

$mform->display();

echo $OUTPUT->footer();