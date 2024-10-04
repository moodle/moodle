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
 * This script shows a list of all the question banks in a course.
 * It is normally reached from More -> Question banks in the course navigation.
 *
 * @package    core_question
 * @copyright  2024 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author     Simon Adams <simon.adams@catalyst-eu.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_question\local\bank\question_bank_helper;
use core_question\local\bank\question_edit_contexts;
use core_question\output\view_banks;

require_once(__DIR__ . '/../config.php');

global $CFG, $PAGE, $OUTPUT;

$courseid = required_param('courseid', PARAM_INT);
$createdefault = optional_param('createdefault', false, PARAM_BOOL);
$course = get_course($courseid);
$coursecontext = context_course::instance($course->id);

require_login($course, false);
require_capability('moodle/course:manageactivities', \context_course::instance($course->id));

if (empty(question_bank_helper::get_activity_types_with_shareable_questions())) {
    throw new moodle_exception('disabledbanks', 'question');
}

$allcaps = array_merge(question_edit_contexts::$caps['editq'], question_edit_contexts::$caps['categories']);
$sharedbanks = question_bank_helper::get_activity_instances_with_shareable_questions([$course->id], [], $allcaps);
$privatebanks = question_bank_helper::get_activity_instances_with_private_questions([$course->id], [], $allcaps);

$pageurl = question_bank_helper::get_url_for_qbank_list($course->id);
$PAGE->set_url($pageurl);
$PAGE->add_body_class('limitedwidth');
$PAGE->set_heading(format_string($course->fullname, true, ['context' => $coursecontext]));
$PAGE->set_title(get_string('questionbank_plural', 'question'));

if ($createdefault) {
    require_sesskey();
    question_bank_helper::create_default_open_instance(
        $course,
        question_bank_helper::get_bank_name_string('defaultbank', 'core_question', ['coursename' => $course->fullname]),
    );
    \core\notification::add(get_string('defaultcreated', 'question'), \core\notification::SUCCESS);
    redirect($pageurl);
}

$output = $PAGE->get_renderer('core_question', 'bank');

echo $output->header();
if (!question_bank_helper::has_bank_migration_task_completed_successfully()) {
    $defaultactivityname = question_bank_helper::get_default_question_bank_activity_name();
    echo $OUTPUT->notification(get_string('transfernotfinished', 'mod_' . $defaultactivityname),
        \core\output\notification::NOTIFY_WARNING
    );
}
echo $output->render(new view_banks($sharedbanks, $privatebanks, $course));
echo $output->footer();
