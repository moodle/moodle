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

$PAGE->set_url('/report/usersessions/user.php');
$PAGE->set_context($context);
$PAGE->set_title(get_string('navigationlink', 'report_usersessions'));
$PAGE->set_pagelayout('admin');

if ($delete and confirm_sesskey()) {
    report_usersessions_kill_session($delete);
    redirect($PAGE->url);
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('mysessions', 'report_usersessions'));

$data = array();
$sql = "SELECT id, timecreated, timemodified, firstip, lastip, sid
          FROM {sessions}
         WHERE userid = :userid
      ORDER BY timemodified DESC";
$params = array('userid' => $USER->id, 'sid' => session_id());

$sessions = $DB->get_records_sql($sql, $params);
foreach ($sessions as $session) {
    if ($session->sid === $params['sid']) {
        $lastaccess = get_string('thissession', 'report_usersessions');
        $deletelink = '';

    } else {
        $lastaccess = report_usersessions_format_duration(time() - $session->timemodified);
        $url = new moodle_url($PAGE->url, array('delete' => $session->id, 'sesskey' => sesskey()));
        $deletelink = html_writer::link($url, get_string('logout'));
    }
    $data[] = array(userdate($session->timecreated), $lastaccess, report_usersessions_format_ip($session->lastip), $deletelink);
}

$table = new html_table();
$table->head  = array(get_string('login'), get_string('lastaccess'), get_string('lastip'), get_string('action'));
$table->align = array('left', 'left', 'left', 'right');
$table->data  = $data;
echo html_writer::table($table);

echo $OUTPUT->footer();

