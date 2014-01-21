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
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

/**
 * This page displays the user data from a single attempt
 *
 * @package mod_scorm
 * @copyright 1999 onwards Martin Dougiamas {@link http://moodle.com}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../../config.php");
require_once($CFG->dirroot.'/mod/scorm/locallib.php');
require_once($CFG->libdir.'/tablelib.php');

$id = required_param('id', PARAM_INT); // Course Module ID.
$userid = required_param('user', PARAM_INT); // User ID.
$scoid = required_param('scoid', PARAM_INT); // SCO ID.
$attempt = optional_param('attempt', 1, PARAM_INT); // attempt number.
$download = optional_param('download', '', PARAM_ALPHA);

// Building the url to use for links.+ data details buildup.
$url = new moodle_url('/mod/scorm/report/userreporttracks.php', array('id' => $id,
    'user' => $userid,
    'attempt' => $attempt,
    'scoid' => $scoid));
$cm = get_coursemodule_from_id('scorm', $id, 0, false, MUST_EXIST);
$course = get_course($cm->course);
$scorm = $DB->get_record('scorm', array('id' => $cm->instance), '*', MUST_EXIST);
$user = $DB->get_record('user', array('id' => $userid), user_picture::fields(), MUST_EXIST);
$selsco = $DB->get_record('scorm_scoes', array('id' => $scoid), '*', MUST_EXIST);

$PAGE->set_url($url);
// END of url setting + data buildup.

// Checking login +logging +getting context.
require_login($course, false, $cm);
$contextmodule = context_module::instance($cm->id);
require_capability('mod/scorm:viewreport', $contextmodule);

// Trigger a tracks viewed event.
$event = \mod_scorm\event\tracks_viewed::create(array(
    'context' => $contextmodule,
    'relateduserid' => $userid,
    'other' => array('attemptid' => $attempt, 'instanceid' => $scorm->id, 'scoid' => $scoid)
));
$event->add_record_snapshot('course_modules', $cm);
$event->add_record_snapshot('scorm', $scorm);
$event->trigger();

// Print the page header.
$strreport = get_string('report', 'scorm');
$strattempt = get_string('attempt', 'scorm');

$PAGE->set_title("$course->shortname: ".format_string($scorm->name));
$PAGE->set_heading($course->fullname);
$PAGE->navbar->add($strreport, new moodle_url('/mod/scorm/report.php', array('id' => $cm->id)));
$PAGE->navbar->add("$strattempt $attempt - ".fullname($user),
    new moodle_url('/mod/scorm/report/userreport.php', array('id' => $id, 'user' => $userid, 'attempt' => $attempt)));
$PAGE->navbar->add($selsco->title . ' - '. get_string('details', 'scorm'));

if ($trackdata = scorm_get_tracks($selsco->id, $userid, $attempt)) {
    if ($trackdata->status == '') {
        $trackdata->status = 'notattempted';
    }
} else {
    $trackdata = new stdClass();
    $trackdata->status = 'notattempted';
    $trackdata->total_time = '';
}

$courseshortname = format_string($course->shortname, true,
    array('context' => context_course::instance($course->id)));
$exportfilename = $courseshortname . '-' . format_string($scorm->name, true) . '-' . get_string('details', 'scorm');

$table = new flexible_table('mod_scorm-userreporttracks');

if (!$table->is_downloading($download, $exportfilename)) {
    echo $OUTPUT->header();
    echo $OUTPUT->heading(format_string($scorm->name));
    $currenttab = '';
    require($CFG->dirroot . '/mod/scorm/report/userreporttabs.php');
    echo $OUTPUT->box_start('generalbox boxaligncenter');
    echo $OUTPUT->heading("$strattempt $attempt - ". fullname($user).': '.
    format_string($selsco->title). ' - '. get_string('details', 'scorm'), 3);
}
$table->define_baseurl($PAGE->url);
$table->define_columns(array('element', 'value'));
$table->define_headers(array(get_string('element', 'scorm'), get_string('value', 'scorm')));
$table->set_attribute('class', 'generaltable generalbox boxaligncenter scormtrackreport');
$table->show_download_buttons_at(array(TABLE_P_BOTTOM));
$table->setup();

foreach ($trackdata as $element => $value) {
    if (substr($element, 0, 3) == 'cmi') {
        $existelements = true;
        $row = array();
        $string = false;
        if (stristr($element, '.id') !== false) {
            $string = "trackid";
        } else if (stristr($element, '.result') !== false) {
            $string = "trackresult";
        } else if (stristr($element, '.student_response') !== false or // SCORM 1.2 value.
            stristr($element, '.learner_response') !== false) { // SCORM 2004 value.
            $string = "trackresponse";
        } else if (stristr($element, '.type') !== false) {
            $string = "tracktype";
        } else if (stristr($element, '.weighting') !== false) {
            $string = "trackweight";
        } else if (stristr($element, '.time') !== false) {
            $string = "tracktime";
        } else if (stristr($element, '.correct_responses._count') !== false) {
            $string = "trackcorrectcount";
        } else if (stristr($element, '.score.min') !== false) {
            $string = "trackscoremin";
        } else if (stristr($element, '.score.max') !== false) {
            $string = "trackscoremax";
        } else if (stristr($element, '.score.raw') !== false) {
            $string = "trackscoreraw";
        } else if (stristr($element, '.latency') !== false) {
            $string = "tracklatency";
        } else if (stristr($element, '.pattern') !== false) {
            $string = "trackpattern";
        } else if (stristr($element, '.suspend_data') !== false) {
            $string = "tracksuspenddata";
        }

        if (empty($string) || $table->is_downloading()) {
            $row[] = $element;
        } else {
            $row[] = $element.$OUTPUT->help_icon($string, 'scorm');
        }
        if (strpos($element, '_time') === false) {
            $row[] = s($value);
        } else {
            $row[] = s(scorm_format_duration($value));
        }
        $table->add_data($row);
    }
}
$table->finish_output();
if (!$table->is_downloading()) {
    echo $OUTPUT->box_end();
    echo $OUTPUT->footer();
}

