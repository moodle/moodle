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
 * Course-level AI usage report.
 *
 * @package    report_aiusage
 * @copyright  2026 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_reportbuilder\system_report_factory;
use report_aiusage\reportbuilder\local\systemreports\course_usage;

require(__DIR__ . '/../../config.php');

$id = required_param('id', PARAM_INT);

$course = $DB->get_record('course', ['id' => $id], '*', MUST_EXIST);

require_login($course);
$context = context_course::instance($course->id);

$canviewall = has_capability('report/aiusage:view', $context);
$canviewown = has_capability('report/aiusage:viewown', $context);

$PAGE->set_url('/report/aiusage/index.php', ['id' => $id]);
$PAGE->set_pagelayout('report');
$PAGE->set_context($context);
$PAGE->set_title($course->shortname . ': ' . get_string('pluginname', 'report_aiusage'));
$PAGE->set_heading($course->fullname);

if (!$canviewall && !$canviewown) {
    echo $OUTPUT->header();
    echo $OUTPUT->notification(get_string('nopermissions', 'report_aiusage'));
    echo $OUTPUT->footer();
    die();
}

$event = \report_aiusage\event\report_viewed::create(['context' => $context]);
$event->trigger();

$reportparams = ['courseid' => $course->id];
if (!$canviewall) {
    // Caller only has the "view own" capability: restrict the report to their own actions.
    $reportparams['restricttouserid'] = $USER->id;
}

$report = system_report_factory::create(
    course_usage::class,
    $context,
    'report_aiusage',
    '',
    0,
    $reportparams,
);

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('pluginname', 'report_aiusage'));
echo $report->output();
echo $OUTPUT->footer();
