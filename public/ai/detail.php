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
require_once($CFG->libdir . '/adminlib.php');

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

$hassitewideaccess = has_capability('moodle/ai:viewaiusagereport', $systemcontext);

// The report this page was actually linked from is not necessarily the report matching the action's own
// context: a sitewide-report viewer can open the detail of a course-context action. The reportbuilder
// "Detail" column passes its own page URL as returnurl, so prefer that; fall back to a context-based
// guess only when it is missing (for example a bookmarked or hand-built link).
$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);
$camefromsitewidereport = $returnurl !== ''
    && (new moodle_url($returnurl))->compare(new moodle_url('/ai/usage_report.php'), URL_MATCH_BASE);

// The returnurl is client-supplied, so require $hassitewideaccess too before trusting it for chrome -
// otherwise a crafted returnurl could deny a course-only viewer who never needed that capability.
$showsitewidechrome = $camefromsitewidereport && $hassitewideaccess;

if ($coursecontext && !$showsitewidechrome) {
    require_login($coursecontext->instanceid);
} else {
    // No course-scoped require_login() here, matching ai/usage_report.php: it also sets $PAGE->course,
    // which would override the sitewide chrome set up below with course-flavoured navigation instead.
    require_login();
}

// Access mirrors the visibility of the reports this page is linked from: the sitewide report capability
// can view any entry; the course report's "view all" capability can view any entry in that course; the
// course report's "view own" capability can only view the signed-in user's own entries.
$canview = $hassitewideaccess;
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

$displaycoursecontext = $showsitewidechrome ? null : $coursecontext;

if ($displaycoursecontext) {
    $PAGE->set_context($displaycoursecontext);
} else if ($showsitewidechrome) {
    // Reuse ai/usage_report.php's own setup for identical admin chrome.
    admin_externalpage_setup('aiusagereport');
} else {
    $PAGE->set_context($systemcontext);
}

$PAGE->set_url($pageurl);
$PAGE->set_pagelayout('report');
$PAGE->set_title(get_string('detailpagetitle', 'core_ai'));

// Sitewide heading is already set by admin_externalpage_setup() above.
if ($displaycoursecontext) {
    $course = get_course($displaycoursecontext->instanceid);
    $PAGE->set_heading($course->fullname);
}

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
