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
 * Grade summary.
 *
 * @package   gradereport_summary
 * @copyright  2022 Ilya Tregubov <ilya@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once("{$CFG->libdir}/adminlib.php");
require_once($CFG->dirroot.'/grade/lib.php');

use core_reportbuilder\system_report_factory;
use gradereport_summary\local\systemreports\summary;

$courseid = required_param('id', PARAM_INT);

if (!$course = $DB->get_record('course', ['id' => $courseid])) {
    throw new \moodle_exception('invalidcourseid');
}
require_login($course);
$context = context_course::instance($course->id);

$PAGE->set_url('/grade/report/summary/index.php', ['id' => $courseid]);
$PAGE->set_context($context);
$PAGE->set_pagelayout('report');
$PAGE->add_body_class('limitedwidth');

require_capability('gradereport/summary:view', $context);
require_capability('moodle/grade:viewall', $context);

$taskindicator = new \core\output\task_indicator(
    \core_course\task\regrade_final_grades::create($courseid),
    get_string('recalculatinggrades', 'grades'),
    get_string('recalculatinggradesadhoc', 'grades'),
    $PAGE->url,
);

print_grade_page_head($courseid, 'report', 'summary');

if ($taskindicator->has_task_record()) {
    echo $OUTPUT->render($taskindicator);
} else {
    $report = system_report_factory::create(summary::class, context_course::instance($courseid));
    echo $report->output();
}

echo $OUTPUT->footer();
