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

$id = required_param('id', PARAM_INT); // Course Module ID.
$userid = required_param('user', PARAM_INT); // User ID.
$attempt = optional_param('attempt', 1, PARAM_INT); // attempt number.

// Building the url to use for links.+ data details buildup.
$url = new moodle_url('/mod/scorm/report/userreport.php', array('id' => $id,
                                                                'user' => $userid,
                                                                'attempt' => $attempt));
$tracksurl = new moodle_url('/mod/scorm/report/userreporttracks.php', array('id' => $id,
                                                                            'user' => $userid,
                                                                            'attempt' => $attempt));
$cm = get_coursemodule_from_id('scorm', $id, 0, false, MUST_EXIST);
$course = get_course($cm->course);
$scorm = $DB->get_record('scorm', array('id' => $cm->instance), '*', MUST_EXIST);
$user = $DB->get_record('user', array('id' => $userid), user_picture::fields(), MUST_EXIST);
// Get list of attempts this user has made.
$attemptids = scorm_get_all_attempts($scorm->id, $userid);

$PAGE->set_url($url);
// END of url setting + data buildup.

// Checking login +logging +getting context.
require_login($course, false, $cm);
$contextmodule = context_module::instance($cm->id);
require_capability('mod/scorm:viewreport', $contextmodule);

// Check user has group access.
if (!groups_user_groups_visible($course, $userid, $cm)) {
    throw new moodle_exception('nopermissiontoshow');
}

// Trigger a user report viewed event.
$event = \mod_scorm\event\user_report_viewed::create(array(
    'context' => $contextmodule,
    'relateduserid' => $userid,
    'other' => array('attemptid' => $attempt, 'instanceid' => $scorm->id)
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
$PAGE->navbar->add(fullname($user). " - $strattempt $attempt");

echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($scorm->name));
// End of Print the page header.
$currenttab = 'scoes';
require($CFG->dirroot . '/mod/scorm/report/userreporttabs.php');

// Printing user details.
$output = $PAGE->get_renderer('mod_scorm');
echo $output->view_user_heading($user, $course, $PAGE->url, $attempt, $attemptids);

if ($scoes = $DB->get_records('scorm_scoes', array('scorm' => $scorm->id), 'sortorder, id')) {
    // Print general score data.
    $table = new html_table();
    $table->head = array(
            get_string('title', 'scorm'),
            get_string('status', 'scorm'),
            get_string('time', 'scorm'),
            get_string('score', 'scorm'),
            '');
    $table->align = array('left', 'center', 'center', 'right', 'left');
    $table->wrap = array('nowrap', 'nowrap', 'nowrap', 'nowrap', 'nowrap');
    $table->width = '80%';
    $table->size = array('*', '*', '*', '*', '*');
    foreach ($scoes as $sco) {
        if ($sco->launch != '') {
            $row = array();
            $score = '&nbsp;';
            if ($trackdata = scorm_get_tracks($sco->id, $userid, $attempt)) {
                if ($trackdata->score_raw != '') {
                    $score = $trackdata->score_raw;
                }
                if ($trackdata->status == '') {
                    if (!empty($trackdata->progress)) {
                        $trackdata->status = $trackdata->progress;
                    } else {
                        $trackdata->status = 'notattempted';
                    }
                }
                $tracksurl->param('scoid', $sco->id);
                $detailslink = html_writer::link($tracksurl, get_string('details', 'scorm'));
            } else {
                $trackdata = new stdClass();
                $trackdata->status = 'notattempted';
                $trackdata->total_time = '&nbsp;';
                $detailslink = '&nbsp;';
            }
            $strstatus = get_string($trackdata->status, 'scorm');
            $row[] = '<img src="'.$OUTPUT->pix_url($trackdata->status, 'scorm').'" alt="'.$strstatus.'" title="'.
            $strstatus.'" />&nbsp;'.format_string($sco->title);
            $row[] = get_string($trackdata->status, 'scorm');
            $row[] = scorm_format_duration($trackdata->total_time);
            $row[] = $score;
            $row[] = $detailslink;
        } else {
            $row = array(format_string($sco->title), '&nbsp;', '&nbsp;', '&nbsp;', '&nbsp;');
        }
        $table->data[] = $row;
    }
    echo html_writer::table($table);
}

// Print footer.

echo $OUTPUT->footer();
