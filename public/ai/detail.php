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
 * Full detail of a single logged AI action, linked from the sitewide and course-level AI usage reports.
 *
 * @package    core_ai
 * @copyright  2026 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_ai\manager;
use core_ai\output\action_detail;

require(__DIR__ . '/../config.php');

$id = required_param('id', PARAM_INT);

$pageurl = new moodle_url('/ai/detail.php', ['id' => $id]);
$systemcontext = context_system::instance();

// Require a logged-in session before any database read of the user-supplied id, regardless of what
// the id resolves to. The course-specific require_login() call below (once the record's course context
// is known) additionally enforces enrolment; this call only guards against unauthenticated access.
require_login();

$record = manager::get_action_detail($id);
if (!$record) {
    $PAGE->set_context($systemcontext);
    $PAGE->set_url($pageurl);
    $PAGE->set_pagelayout('report');
    $PAGE->set_title(get_string('detailpagetitle', 'core_ai'));
    echo $OUTPUT->header();
    echo $OUTPUT->notification(get_string('invaliddetailrecord', 'core_ai'), 'notifyproblem');
    echo $OUTPUT->footer();
    die();
}

$actioncontext = context::instance_by_id($record->contextid, IGNORE_MISSING);
// Get_course_context() returns false (not null) when there is no course context, so normalise it here:
// the rest of this page relies on null-coalescing to fall back to the system context.
$coursecontext = $actioncontext ? ($actioncontext->get_course_context(false) ?: null) : null;

if ($coursecontext) {
    require_login($coursecontext->instanceid);
} else {
    require_login();
}

// Access mirrors the visibility of the reports this page is linked from: the sitewide report capability
// can view any entry; the course report's "view all" capability can view any entry in that course; the
// course report's "view own" capability can only view the signed-in user's own entries.
$canview = has_capability('moodle/ai:viewaiusagereport', $systemcontext);
if (!$canview && $coursecontext) {
    if (has_capability('report/aiusage:view', $coursecontext)) {
        $canview = true;
    } else if (
        (int) $record->userid === (int) $USER->id
        && has_capability('report/aiusage:viewown', $coursecontext)
    ) {
        $canview = true;
    }
}

$PAGE->set_context($coursecontext ?? $systemcontext);
$PAGE->set_url($pageurl);
$PAGE->set_pagelayout('report');
$PAGE->set_title(get_string('detailpagetitle', 'core_ai'));

// Mirror the heading shown on the report this page is linked from: the course-level report shows the
// course full name as its page heading, so do the same here rather than falling back to the site name.
if ($coursecontext) {
    $course = get_course($coursecontext->instanceid);
    $PAGE->set_heading($course->fullname);
}

// The report this page was actually linked from is not necessarily the report matching the action's own
// context: a sitewide-report viewer can open the detail of a course-context action. The reportbuilder
// "Detail" column passes its own page URL as returnurl, so prefer that; fall back to a context-based
// guess only when it is missing (for example a bookmarked or hand-built link).
$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);
if ($returnurl !== '') {
    $backurl = new moodle_url($returnurl);
} else if ($coursecontext) {
    $backurl = new moodle_url('/report/aiusage/index.php', ['id' => $coursecontext->instanceid]);
} else {
    $backurl = new moodle_url('/ai/usage_report.php');
}

if (!$canview) {
    echo $OUTPUT->header();
    echo $OUTPUT->notification(get_string('nopermissiontoviewdetail', 'core_ai'), 'notifyproblem');
    echo $OUTPUT->footer();
    die();
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('detailpagetitle', 'core_ai'));
echo $OUTPUT->render(new action_detail($record));
echo $OUTPUT->single_button($backurl, get_string('backtousagereport', 'core_ai'), 'get');
echo $OUTPUT->footer();
