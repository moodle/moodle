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
 * Prints a particular instance of jitsi
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod_jitsi
 * @copyright  2019 Sergio Comerón <sergiocomeron@icloud.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(dirname(dirname(__FILE__))).'/lib/moodlelib.php');
require_once(dirname(__FILE__).'/lib.php');
$PAGE->set_url($CFG->wwwroot.'/mod/jitsi/session.php');
$PAGE->set_context(context_system::instance());
require_login();
$nombre = required_param('nom', PARAM_USERNAME);
$userid = required_param('u', PARAM_INT);
$session = required_param('ses', PARAM_TEXT);
$user = $DB->get_record('user', array('id' => $userid));
$sessionnorm = str_replace(array(' ', ':', '"'), '', $user->username);
$avatar = $CFG->jitsi_showavatars == true ? required_param('avatar', PARAM_TEXT) : null;

$PAGE->set_title(get_string('privatesession', 'jitsi', $user->firstname));
$PAGE->set_heading(get_string('privatesession', 'jitsi', $user->firstname));
echo $OUTPUT->header();
if ($CFG->jitsi_privatesessions == 1) {
    $teacher = 0;
    if ($USER->id == $user->id) {
        $teacher = 1;
    }
    if ($USER->id != $user->id) {
        sendnotificationprivatesession($USER, $user);
    }
    createsession($teacher, 0, $avatar, $nombre, $session, null, 0, false , $user->id);
} else {
    echo get_string('privatesessiondisabled', 'jitsi');
}


echo $OUTPUT->footer();
