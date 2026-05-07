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

use core\task\adhoc_task;
use mod_quiz\quiz_attempt;

/**
 * Ad-hoc task to grade a submitted attempt.
 *
 * This is used (currently) for fixing quiz attempts which are stuck in "submitted" state, and
 * in the future will also support the asynchronous grading of quiz attempts.
 *
 * @package   mod_quiz
 * @copyright 2024 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class grade_submission extends adhoc_task {
    /**
     * Return an instance of the task, with the attempt ID stored in custom data.
     *
     * @param int $attemptid
     * @return self
     */
    public static function instance(int $attemptid): self {
        $task = new self();
        $task->set_custom_data((object)['attemptid' => $attemptid]);
        return $task;
    }

    /**
     * Perform grading for the referenced submitted attempt.
     */
    public function execute(): void {
        global $DB;
        $data = $this->get_custom_data();
        if ($DB->record_exists('quiz_attempts', ['id' => $data->attemptid, 'state' => quiz_attempt::SUBMITTED])) {
            $attempt = quiz_attempt::create($data->attemptid);
            mtrace(
                'Grading attempt for user ID ' .
                $attempt->get_userid() . ' for quiz ' .
                $attempt->get_quiz_name() . ' on course ' .
                $attempt->get_course()->shortname
            );
            $attempt->process_grade_submission(time());
        } else {
            mtrace('Attempt ID ' . $data->attemptid . ' not found, or not in submitted state.');
        }
    }
}
