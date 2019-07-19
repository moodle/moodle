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
 * This script displays the forum summary report for the given parameters, within a user's capabilities.
 *
 * @package   forumreport_summary
 * @copyright 2019 Michael Hawkins <michaelh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../../../config.php");

if (isguestuser()) {
    print_error('noguest');
}

$courseid = required_param('courseid', PARAM_INT);
$forumid = required_param('forumid', PARAM_INT);
$perpage = optional_param('perpage', 25, PARAM_INT);
$cm = null;
$modinfo = get_fast_modinfo($courseid);

if ($forumid > 0) {
    if (!isset($modinfo->instances['forum'][$forumid])) {
        throw new \moodle_exception("A valid forum ID is required to generate a summary report.");
    }

    $foruminfo = $modinfo->instances['forum'][$forumid];
    $forumname = $foruminfo->name;
    $cm = $foruminfo->get_course_module_record();
} else {
    throw new \moodle_exception("A valid forum ID is required to generate a summary report.");
}

require_login($courseid, false, $cm);

// This capability is required to view any version of the report.
$context = \context_module::instance($cm->id);
if (!has_capability("forumreport/summary:view", $context)) {
    $redirecturl = new moodle_url("/mod/forum/view.php");
    $redirecturl->param('id', $forumid);
    redirect($redirecturl);
}

$course = $modinfo->get_course();

$urlparams = [
    'courseid' => $courseid,
    'forumid' => $forumid,
    'perpage' => $perpage,
];
$url = new moodle_url("/mod/forum/report/summary/index.php", $urlparams);

$PAGE->set_url($url);
$PAGE->set_pagelayout('report');
$PAGE->set_title($forumname);
$PAGE->set_heading($course->fullname);

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('summarytitle', 'forumreport_summary', $forumname), 2, 'p-b-2');

$table = new \forumreport_summary\summary_table($courseid, $forumid);
$table->baseurl = $url;
$table->out($perpage, false);
echo $OUTPUT->footer();
