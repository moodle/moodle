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
 * Attendance manual auto-mark process.
 *
 * @package    mod_attendance
 * @copyright  2022 Dan Marsden <dan@danmarsden.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__).'/../../config.php');
require_once(dirname(__FILE__).'/locallib.php');

$id         = required_param('id', PARAM_INT);
$sessionid  = required_param('sessionid', PARAM_INT);
$grouptype  = required_param('grouptype', PARAM_INT);

$cm             = get_coursemodule_from_id('attendance', $id, 0, false, MUST_EXIST);
$course         = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$att            = $DB->get_record('attendance', array('id' => $cm->instance), '*', MUST_EXIST);
$session        = $DB->get_record('attendance_sessions', array('id' => $sessionid, 'attendanceid' => $att->id),
                                  '*', MUST_EXIST);

require_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/attendance:manualautomark', $context);

if (empty($session->automark)) {
    throw new moodle_exception('automarkingnotenabled', 'attendance');
}
if ($session->automark == ATTENDANCE_AUTOMARK_CLOSE && ($session->sessdate + $session->duration) > time() ) {
    throw new moodle_exception('automarkingnotavailableyet', 'attendance');
}
// TODO Check Get session unmarked value for statusset used by this session.
$errors = \mod_attendance\local\automark::session($session, $course, $cm, $att, true);
$url = new moodle_url('/mod/attendance/take.php', ['id' => $id, 'sessionid' => $session->id, 'grouptype' => $grouptype]);
if (!empty($errors)) {
    redirect($url, $errors, null, \core\output\notification::NOTIFY_ERROR);
} else {
    redirect($url, get_string('automarkingcomplete', 'attendance'));
}
