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
$forumid = optional_param('forumid', 0, PARAM_INT);
$perpage = optional_param('perpage', \forumreport_summary\summary_table::DEFAULT_PER_PAGE, PARAM_INT);
$download = optional_param('download', '', PARAM_ALPHA);
$filters = [];
$pageurlparams = [
    'courseid' => $courseid,
    'perpage' => $perpage,
];

// Establish filter values.
$filters['groups'] = optional_param_array('filtergroups', [], PARAM_INT);
$filters['datefrom'] = optional_param_array('datefrom', ['enabled' => 0], PARAM_INT);
$filters['dateto'] = optional_param_array('dateto', ['enabled' => 0], PARAM_INT);

$modinfo = get_fast_modinfo($courseid);
$course = $modinfo->get_course();
$courseforums = $modinfo->instances['forum'];
$cms = [];

// Determine which forums the user has access to in the course.
$accessallforums = false;
$allforumidsincourse = array_keys($courseforums);
$forumsvisibletouser = [];
$forumselectoptions = [0 => get_string('forumselectcourseoption', 'forumreport_summary')];

foreach ($courseforums as $courseforumid => $courseforum) {
    if ($courseforum->uservisible) {
        $forumsvisibletouser[$courseforumid] = $courseforum;
        $forumselectoptions[$courseforumid] = $courseforum->get_formatted_name();
    }
}

if ($forumid) {
    if (!isset($forumsvisibletouser[$forumid])) {
        throw new \moodle_exception('A valid forum ID is required to generate a summary report.');
    }

    $filters['forums'] = [$forumid];
    $title = $forumsvisibletouser[$forumid]->get_formatted_name();
    $forumcm = $forumsvisibletouser[$forumid];
    $cms[] = $forumcm;

    require_login($courseid, false, $forumcm);
    $context = $forumcm->context;
    $canexport = !$download && has_capability('mod/forum:exportforum', $context);
    $redirecturl = new moodle_url('/mod/forum/view.php', ['id' => $forumid]);
    $numforums = 1;
    $pageurlparams['forumid'] = $forumid;
    $iscoursereport = false;
} else {
    // Course level report.
    require_login($courseid, false);

    $filters['forums'] = array_keys($forumsvisibletouser);

    // Fetch the forum CMs for the course.
    foreach ($forumsvisibletouser as $visibleforum) {
        $cms[] = $visibleforum;
    }

    $context = \context_course::instance($courseid);
    $title = $course->fullname;
    // Export currently only supports single forum exports.
    $canexport = false;
    $redirecturl = new moodle_url('/course/view.php', ['id' => $courseid]);
    $numforums = count($forumsvisibletouser);
    $iscoursereport = true;

    // Specify whether user has access to all forums in the course.
    $accessallforums = empty(array_diff($allforumidsincourse, $filters['forums']));
}

$pageurl = new moodle_url('/mod/forum/report/summary/index.php', $pageurlparams);

$PAGE->set_url($pageurl);
$PAGE->set_pagelayout('report');
$PAGE->set_title($title);
$PAGE->set_heading($course->fullname);
$PAGE->activityheader->disable();
$PAGE->navbar->add(get_string('nodetitle', 'forumreport_summary'));

// Activate the secondary nav tab.
navigation_node::override_active_url(new moodle_url('/mod/forum/report/summary/index.php',
    ['courseid' => $courseid, 'forumid' => $forumid]));

$allowbulkoperations = !$download && !empty($CFG->messaging) && has_capability('moodle/course:bulkmessaging', $context);
$canseeprivatereplies = false;
$hasviewall = false;
$privatereplycapcount = 0;
$viewallcount = 0;
$canview = false;

foreach ($cms as $cm) {
    $forumcontext = $cm->context;

    // This capability is required in at least one of the given contexts to view any version of the report.
    if (has_capability('forumreport/summary:view', $forumcontext)) {
        $canview = true;
    }

    if (has_capability('mod/forum:readprivatereplies', $forumcontext)) {
        $privatereplycapcount++;
    }

    if (has_capability('forumreport/summary:viewall', $forumcontext)) {
        $viewallcount++;
    }
}

if (!$canview) {
    redirect($redirecturl);
}

// Only use private replies if user has that cap in all forums in the report.
if ($numforums === $privatereplycapcount) {
    $canseeprivatereplies = true;
}

// Will only show all users if user has the cap for all forums in the report.
if ($numforums === $viewallcount) {
    $hasviewall = true;
}

// Prepare and display the report.
$table = new \forumreport_summary\summary_table($courseid, $filters, $allowbulkoperations,
        $canseeprivatereplies, $perpage, $canexport, $iscoursereport, $accessallforums);
$table->baseurl = $pageurl;

$eventparams = [
    'context' => $context,
    'other' => [
        'forumid' => $forumid,
        'hasviewall' => $hasviewall,
    ],
];

if ($download) {
    \forumreport_summary\event\report_downloaded::create($eventparams)->trigger();
    $table->download($download);
} else {
    \forumreport_summary\event\report_viewed::create($eventparams)->trigger();

    echo $OUTPUT->header();

    if (!empty($filters['groups'])) {
        \core\notification::info(get_string('viewsdisclaimer', 'forumreport_summary'));
    }

    // Allow switching to course report (or other forum user has access to).
    $reporturl = new moodle_url('/mod/forum/report/summary/index.php', ['courseid' => $courseid]);
    $forumselect = new single_select($reporturl, 'forumid', $forumselectoptions, $forumid, '');
    $forumselect->set_label(get_string('forumselectlabel', 'forumreport_summary'));
    echo $OUTPUT->render($forumselect);
    echo $OUTPUT->heading(get_string('nodetitle', 'forumreport_summary'), 2, 'pb-5 mt-3');

    // Render the report filters form.
    $renderer = $PAGE->get_renderer('forumreport_summary');

    unset($filters['forums']);
    echo $renderer->render_filters_form($course, $cms, $pageurl, $filters);
    $table->show_download_buttons_at(array(TABLE_P_BOTTOM));
    echo $renderer->render_summary_table($table);
    echo $OUTPUT->footer();
}
