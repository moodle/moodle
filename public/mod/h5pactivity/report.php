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
 * Prints an instance of mod_h5pactivity.
 *
 * @package     mod_h5pactivity
 * @copyright   2020 Ferran Recio <ferran@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_h5pactivity\local\manager;
use mod_h5pactivity\event\report_viewed;

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');

$userid = optional_param('userid', null, PARAM_INT);
$attemptid = optional_param('attemptid', null, PARAM_INT);

// Attempts have only the instance id information but system events
// have only cmid. To prevent unnecesary db queries, this page accept both.
$id = optional_param('id', null, PARAM_INT);
if (empty($id)) {
    $a = required_param('a', PARAM_INT);
    list ($course, $cm) = get_course_and_cm_from_instance($a, 'h5pactivity');
} else {
    list ($course, $cm) = get_course_and_cm_from_cmid($id, 'h5pactivity');
}

require_login($course, true, $cm);

$currentgroup = groups_get_activity_group($cm, true);

$manager = manager::create_from_coursemodule($cm);

$report = $manager->get_report($userid, $attemptid, $currentgroup);
if (!$report) {
    throw new \moodle_exception('permissiondenied');
}

$user = $report->get_user();
$attempt = $report->get_attempt();

$moduleinstance = $manager->get_instance();

$context = $manager->get_context();

$params = ['a' => $cm->instance];
if ($user) {
    $params['userid'] = $user->id;
}
if ($attempt) {
    $params['attemptid'] = $attempt->get_id();
}
$PAGE->set_url('/mod/h5pactivity/report.php', $params);

// Trigger event.
$other = [
    'instanceid' => $params['a'],
    'userid' => $params['userid'] ?? null,
    'attemptid' => $params['attemptid'] ?? null,
];
$event = report_viewed::create([
    'objectid' => $moduleinstance->id,
    'context' => $context,
    'other' => $other,
]);
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('h5pactivity', $moduleinstance);
$event->trigger();

$shortname = format_string($course->shortname, true, ['context' => $context]);
$pagetitle = strip_tags($shortname.': '.format_string($moduleinstance->name));
$PAGE->set_title(format_string($pagetitle));
$PAGE->activityheader->disable();

$navbar = [];
if ($manager->can_view_all_attempts()) {
    // Report navbar have 3 levels for teachers:
    // - Participants list
    // - Participant attempts list
    // - Individual attempt details.
    $nav = [get_string('attempts', 'mod_h5pactivity'), null];
    if ($user) {
        $nav[1] = new moodle_url('/mod/h5pactivity/report.php', ['a' => $cm->instance]);
        $navbar[] = $nav;

        $nav = [fullname($user), null];
        if ($attempt) {
            $nav[1] = new moodle_url('/mod/h5pactivity/report.php', ['a' => $cm->instance, 'userid' => $user->id]);
        }
    }
    $navbar[] = $nav;
} else {
    // Report navbar have 2 levels for a regular participant:
    // - My attempts
    // - Individual attempt details.
    $nav = [get_string('myattempts', 'mod_h5pactivity'), null];
    if ($attempt) {
        $nav[1] = new moodle_url('/mod/h5pactivity/report.php', ['a' => $cm->instance]);
    }
    $navbar[] = $nav;

}
if ($attempt) {
    $navbar[] = [get_string('attempt_number', 'mod_h5pactivity', $attempt->get_attempt()), null];
}
foreach ($navbar as $nav) {
    $PAGE->navbar->add($nav[0], $nav[1]);
}

$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

echo $OUTPUT->header();

groups_print_activity_menu($cm, $PAGE->url);

echo html_writer::start_div('mt-4');
echo $report->print();
echo html_writer::end_div();

echo $OUTPUT->footer();
