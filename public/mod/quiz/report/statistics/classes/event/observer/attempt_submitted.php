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

namespace quiz_statistics\event\observer;

use core\check\performance\debugging;
use quiz_statistics\task\recalculate;

/**
 * Event observer for \mod_quiz\event\attempt_submitted
 *
 * @package   quiz_statistics
 * @copyright 2023 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @deprecated Since Moodle 4.4 MDL-80099.
 * @todo Final deprecation in Moodle 4.8 MDL-80956.
 */
class attempt_submitted {
    /**
     * Queue an ad-hoc task to recalculate statistics for the quiz.
     *
     * This will defer running the task for 1 hour, to give other attempts in progress
     * a chance to submit.
     *
     * @param \mod_quiz\event\attempt_submitted $event
     * @return void
     * @deprecated Since Moodle 4.4 MDL-80099
     */
    public static function process(\mod_quiz\event\attempt_submitted $event): void {
        debugging('quiz_statistics\event\observer\attempt_submitted event observer has been deprecated in favour of ' .
            'the quiz_statistics\hook_callbacks::quiz_attempt_submitted_or_deleted hook callback.', DEBUG_DEVELOPER);
        $data = $event->get_data();
        recalculate::queue_future_run($data['other']['quizid']);
    }
}
