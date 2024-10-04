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

namespace mod_quiz\task;

use core\task\scheduled_task;
use mod_quiz\notification_helper;

/**
 * Scheduled task to queue tasks for notifying about quizzes with an approaching open date.
 *
 * @package    mod_quiz
 * @copyright  2024 David Woloszyn <david.woloszyn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class queue_all_quiz_open_notification_tasks extends scheduled_task {

    public function get_name(): string {
        return get_string('sendnotificationopendatesoon', 'mod_quiz');
    }

    public function execute(): void {
        $quizzes = notification_helper::get_quizzes_within_date_range();
        foreach ($quizzes as $quiz) {
            $task = new queue_quiz_open_notification_tasks_for_users();
            $task->set_custom_data($quiz);
            \core\task\manager::queue_adhoc_task($task, true);
        }
        $quizzes->close();
    }
}
