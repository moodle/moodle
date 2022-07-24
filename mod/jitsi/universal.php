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
 * @copyright  2021 Sergio Comerón <sergiocomeron@icloud.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(dirname(dirname(__FILE__))).'/lib/moodlelib.php');
require_once(dirname(__FILE__).'/lib.php');

$sessionid = required_param('ses', PARAM_INT);
$name = required_param('name', PARAM_TEXT);
$avatar = optional_param('avatar', null, PARAM_TEXT);
$id = required_param('id', PARAM_INT);

global $DB, $CFG;
$PAGE->set_url($CFG->wwwroot.'/mod/jitsi/universal.php');
$sesion = $DB->get_record('jitsi', array('id' => $sessionid));
$course = $DB->get_record('course', array('id' => $sesion->course));
$cm = get_coursemodule_from_id('jitsi', $id, 0, false, MUST_EXIST);
$PAGE->set_cm($cm);

$PAGE->set_context(context_module::instance($cm->id));

$event = \mod_jitsi\event\jitsi_session_enter::create(array(
  'objectid' => $PAGE->cm->instance,
  'context' => $PAGE->context,
));
$event->add_record_snapshot('course', $PAGE->course);
$event->add_record_snapshot($PAGE->cm->modname, $sesion);

$event->trigger();

$fieldssessionname = $CFG->jitsi_sesionname;
$allowed = explode(',', $fieldssessionname);
$max = count($allowed);

$sesparam = '';
$optionsseparator = ['.', '-', '_', ''];
for ($i = 0; $i < $max; $i++) {
    if ($i != $max - 1) {
        if ($allowed[$i] == 0) {
            $sesparam .= string_sanitize($course->shortname).$optionsseparator[$CFG->jitsi_separator];
        } else if ($allowed[$i] == 1) {
            $sesparam .= $sesion->id.$optionsseparator[$CFG->jitsi_separator];
        } else if ($allowed[$i] == 2) {
            $sesparam .= string_sanitize($sesion->name).$optionsseparator[$CFG->jitsi_separator];
        }
    } else {
        if ($allowed[$i] == 0) {
            $sesparam .= string_sanitize($course->shortname);
        } else if ($allowed[$i] == 1) {
            $sesparam .= $sessionid;
        } else if ($allowed[$i] == 2) {
            $sesparam .= string_sanitize($sesion->name);
        }
    }
}

$PAGE->set_title($sesion->name);
$PAGE->set_heading($sesion->name);

echo $OUTPUT->header();
if (!istimedout($sesion)) {
    createsession(0, $id,  $avatar, $name, $sesparam, null, $sesion, true, null);

} else {
    echo generateerrortime($sesion);
}

echo $OUTPUT->footer();
