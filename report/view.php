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
 * Page to view the course reports
 *
 * @package    core
 * @subpackage report
 * @copyright  2021 Sujith Haridasan
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../config.php');

// Course id.
$courseid = required_param('courseid', PARAM_INT);

$PAGE->set_url(new moodle_url('/report/view.php', array('courseid' => $courseid)));

// Basic access checks.
if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('invalidcourseid');
}
require_login($course);

// Get the last viewed Page.
$lasturl = null;
$reportsnode = $PAGE->settingsnav->find('coursereports', \navigation_node::TYPE_CONTAINER);
$reportchildrennode = $reportsnode->children;
// If there are available course reports to the user.
if ($reportsnode && $reportchildrennode->count() > 0) {
    // By default, set the first valid report URL for the redirect URL.
    $lasturl = $reportchildrennode->getIterator()[0]->action()->out(false);
    // If exist the last viewed report page.
    if (isset($USER->course_last_report[$courseid])) {
        // Check if the most recently report link is existed on report navigation node.
        foreach ($reportchildrennode as $node) {
            if ($node->action()->get_path() === $USER->course_last_report[$courseid]->get_path()) {
                $lasturl = $USER->course_last_report[$courseid];
                break;
            }
        }
    }
}
if ($lasturl) {
    redirect($lasturl);
}
// Otherwise, output the page with a notification stating that there are no available course reports.
$PAGE->set_title(get_string('reports'));
$PAGE->set_pagelayout('incourse');
$PAGE->set_heading($course->fullname);
$PAGE->set_pagetype('course-view-' . $course->format);

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('reports'));
echo html_writer::div($OUTPUT->notification(get_string('noreports', 'debug'), 'error'), 'mt-3');
echo $OUTPUT->footer();
