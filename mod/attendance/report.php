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
 * Attendance report
 *
 * @package    mod_attendance
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__).'/../../config.php');
require_once(dirname(__FILE__).'/locallib.php');

$pageparams = new mod_attendance_report_page_params();

$id                     = required_param('id', PARAM_INT);
$from                   = optional_param('from', null, PARAM_ACTION);
$pageparams->view       = optional_param('view', null, PARAM_INT);
$pageparams->curdate    = optional_param('curdate', null, PARAM_INT);
$pageparams->group      = optional_param('group', null, PARAM_INT);
$pageparams->sort       = optional_param('sort', ATT_SORT_DEFAULT, PARAM_INT);
$pageparams->page       = optional_param('page', 1, PARAM_INT);
$pageparams->perpage    = get_config('attendance', 'resultsperpage');

$cm             = get_coursemodule_from_id('attendance', $id, 0, false, MUST_EXIST);
$course         = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$attrecord = $DB->get_record('attendance', array('id' => $cm->instance), '*', MUST_EXIST);

require_login($course, true, $cm);

$context = context_module::instance($cm->id);
require_capability('mod/attendance:viewreports', $context);

// If separate groups and user does not have accessallgroups force a group to be selected - don't show "all users" view.
if (empty($pageparams->group) && !has_capability('moodle/site:accessallgroups', $PAGE->context)) {
    $groupmode = groups_get_activity_groupmode($cm, $course);
    if ($groupmode == SEPARATEGROUPS) {
        $allowedgroups = groups_get_all_groups($cm->course, $USER->id, $cm->groupingid);
        if (empty($allowedgroups)) {
            throw new moodle_exception('cannottakethisgroup', 'attendance');
        }
        $pageparams->group = array_shift($allowedgroups)->id;
    }
}

$pageparams->init($cm);
$pageparams->showextrauserdetails = optional_param('showextrauserdetails', $attrecord->showextrauserdetails, PARAM_INT);
$pageparams->showsessiondetails = optional_param('showsessiondetails', $attrecord->showsessiondetails, PARAM_INT);
$pageparams->sessiondetailspos = optional_param('sessiondetailspos', $attrecord->sessiondetailspos, PARAM_TEXT);

$att = new mod_attendance_structure($attrecord, $cm, $course, $context, $pageparams);

$PAGE->set_url($att->url_report());
$PAGE->set_pagelayout('report');
$PAGE->set_title($course->shortname. ": ".$att->name.' - '.get_string('report', 'attendance'));
$PAGE->set_heading($course->fullname);
$PAGE->force_settings_menu(true);
$PAGE->set_cacheable(true);
$PAGE->navbar->add(get_string('report', 'attendance'));

$output = $PAGE->get_renderer('mod_attendance');
$filtercontrols = new mod_attendance\output\filter_controls($att, true);
$reportdata = new mod_attendance\output\report_data($att);

// Trigger a report viewed event.
$event = \mod_attendance\event\report_viewed::create(array(
    'objectid' => $att->id,
    'context' => $PAGE->context,
    'other' => array()
));
$event->add_record_snapshot('course_modules', $cm);
$event->add_record_snapshot('attendance', $attrecord);
$event->trigger();

// Output starts here.
echo $output->header();
echo $output->render($filtercontrols);
echo $output->render($reportdata);
echo $output->footer();

