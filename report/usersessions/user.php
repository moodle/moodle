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
 * Listing of all sessions for current user.
 *
 * @package   report_usersessions
 * @copyright 2014 Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Petr Skoda <petr.skoda@totaralms.com>
 */

require(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/locallib.php');

require_login(null, false);

if (isguestuser()) {
    // No guests here!
    redirect(new moodle_url('/'));
    die;
}
if (\core\session\manager::is_loggedinas()) {
    // No login-as users.
    redirect(new moodle_url('/user/index.php'));
    die;
}

$context = context_user::instance($USER->id);
require_capability('report/usersessions:manageownsessions', $context);

$delete = optional_param('delete', 0, PARAM_INT);
$deleteall = optional_param('deleteall', false, PARAM_BOOL);
$lastip = cleanremoteaddr(optional_param('lastip', '', PARAM_TEXT));

$PAGE->set_url('/report/usersessions/user.php');
$PAGE->set_context($context);
$PAGE->set_title(get_string('navigationlink', 'report_usersessions'));
$PAGE->set_heading(fullname($USER));
$PAGE->set_pagelayout('admin');

// Delete a specific session.
if ($delete && confirm_sesskey()) {
    report_usersessions_kill_session($delete);
    redirect(
        url: $PAGE->url,
        message: get_string('logoutsinglesessionsuccess', 'report_usersessions', $lastip),
        messagetype: \core\output\notification::NOTIFY_SUCCESS,
    );
}

// Delete all sessions except current.
if ($deleteall && confirm_sesskey()) {
    \core\session\manager::destroy_user_sessions($USER->id, session_id());
    redirect(
        url: $PAGE->url,
        message: get_string('logoutothersessionssuccess', 'report_usersessions'),
        messagetype: \core\output\notification::NOTIFY_SUCCESS,
    );
}

// Create the breadcrumb.
$PAGE->add_report_nodes($USER->id, array(
        'name' => get_string('navigationlink', 'report_usersessions'),
        'url' => new moodle_url('/report/usersessions/user.php')
    ));

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('mysessions', 'report_usersessions'));

$data = array();
$sessions = \core\session\manager::get_sessions_by_userid($USER->id);
// Order records by timemodified DESC.
usort($sessions, function($a, $b){
    return $b->timemodified <=> $a->timemodified;
});
foreach ($sessions as $session) {
    if ($session->sid === session_id()) {
        $lastaccess = get_string('thissession', 'report_usersessions');
        $deletelink = '';

    } else {
        $lastaccess = report_usersessions_format_duration(time() - $session->timemodified);
        $url = new moodle_url($PAGE->url, ['delete' => $session->id, 'sesskey' => sesskey(), 'lastip' => $session->lastip]);
        $deletelink = html_writer::link($url, get_string('logout'));
    }
    $data[] = array(userdate($session->timecreated), $lastaccess, report_usersessions_format_ip($session->lastip), $deletelink);
}

$table = new html_table();
$table->head  = array(get_string('login'), get_string('lastaccess'), get_string('lastip'), get_string('action'));
$table->align = array('left', 'left', 'left', 'right');
$table->data  = $data;
echo html_writer::table($table);

// Provide button to log out all other sessions.
if (count($sessions) > 1) {
    $url = new moodle_url($PAGE->url, ['deleteall' => true]);
    echo $OUTPUT->single_button($url, get_string('logoutothersessions', 'report_usersessions'));
}

echo $OUTPUT->footer();
