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
$perpage = optional_param('perpage', \forumreport_summary\summary_table::DEFAULT_PER_PAGE, PARAM_INT);
$filters = [];

// Establish filter values.
$filters['forums'] = [$forumid];
$filters['groups'] = optional_param_array('filtergroups', [], PARAM_INT);
$filters['datefrom'] = optional_param_array('datefrom', ['enabled' => 0], PARAM_INT);
$filters['dateto'] = optional_param_array('dateto', ['enabled' => 0], PARAM_INT);

$download = optional_param('download', '', PARAM_ALPHA);

$cm = null;
$modinfo = get_fast_modinfo($courseid);

if (!isset($modinfo->instances['forum'][$forumid])) {
    throw new \moodle_exception("A valid forum ID is required to generate a summary report.");
}

$foruminfo = $modinfo->instances['forum'][$forumid];
$forumname = $foruminfo->name;
$cm = $foruminfo->get_course_module_record();

require_login($courseid, false, $cm);
$context = \context_module::instance($cm->id);

// This capability is required to view any version of the report.
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
$PAGE->navbar->add(get_string('nodetitle', "forumreport_summary"));

// Prepare and display the report.
$allowbulkoperations = !$download && !empty($CFG->messaging) && has_capability('moodle/course:bulkmessaging', $context);
$canseeprivatereplies = has_capability('mod/forum:readprivatereplies', $context);
$canexport = !$download && has_capability('mod/forum:exportforum', $context);

$table = new \forumreport_summary\summary_table($courseid, $filters, $allowbulkoperations,
        $canseeprivatereplies, $perpage, $canexport);
$table->baseurl = $url;

$eventparams = [
    'context' => $context,
    'other' => [
        'forumid' => $forumid,
        'hasviewall' => has_capability('forumreport/summary:viewall', $context),
    ],
];

if ($download) {
    \forumreport_summary\event\report_downloaded::create($eventparams)->trigger();
    $table->download($download);
} else {
    \forumreport_summary\event\report_viewed::create($eventparams)->trigger();

    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('summarytitle', 'forumreport_summary', $forumname), 2, 'p-b-2');

    if (!empty($filters['groups'])) {
        \core\notification::info(get_string('viewsdisclaimer', 'forumreport_summary'));
    }

    // Render the report filters form.
    $renderer = $PAGE->get_renderer('forumreport_summary');

    echo $renderer->render_filters_form($cm, $url, $filters);
    $table->show_download_buttons_at(array(TABLE_P_BOTTOM));
    echo $renderer->render_summary_table($table);
    echo $OUTPUT->footer();
}
