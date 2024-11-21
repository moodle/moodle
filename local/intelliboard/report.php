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
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 *
 * @package    local_intelliboard
 * @copyright  2020 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    https://intelliboard.net/
 */

require('../../config.php');

$reportid = required_param('id', PARAM_INT);
$search = optional_param('q', '', PARAM_TEXT);
$download = optional_param('download', '', PARAM_ALPHA);

require_login();

if (!class_exists('\local_intelliboard\output\tables\free_reports\report' . $reportid)) {
    exit('report does not exists');
}

$title = get_string("report{$reportid}_name", "local_intelliboard");

$PAGE->set_context(context_system::instance());
$PAGE->set_url("/local/intelliboard/report.php", ["id" => $reportid, "q" => $search]);
$PAGE->set_pagetype("courses");
$PAGE->set_pagelayout("report");
$PAGE->set_context(context_system::instance());
$PAGE->set_title($title);
$PAGE->set_heading($title);

if ($download) {
    $reportname = '\local_intelliboard\output\tables\free_reports\report' . $reportid;
    /** @var \local_intelliboard\output\tables\intelliboard_table $report */
    $report = new $reportname("freereport{$reportid}", $search);
    $report->is_downloading($download, $title, $title);
    $report->out(10, true);
    exit;
}

$renderer = $PAGE->get_renderer("local_intelliboard");

echo $OUTPUT->header();
echo $renderer->render(new \local_intelliboard\output\report([
    "search" => $search,
    "report_id" => $reportid
]));
echo $OUTPUT->footer();