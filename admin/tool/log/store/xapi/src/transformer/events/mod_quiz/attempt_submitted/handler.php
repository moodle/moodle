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
 * Generic quiz handler for the attempt submitted event.
 *
 * @package   logstore_xapi
 * @copyright Jerret Fowler <jerrett.fowler@gmail.com>
 *            Ryan Smith <https://www.linkedin.com/in/ryan-smith-uk/>
 *            David Pesce <david.pesce@exputo.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\transformer\events\mod_quiz\attempt_submitted;

use src\transformer\utils as utils;
use src\transformer\events\mod_quiz\question_answered as question_answered;

/**
 * Generic handler for the attempt submitted event.
 *
 * @param array $config The transformer config settings.
 * @param \stdClass $event The event to be transformed.
 * @return array
 */
function handler(array $config, \stdClass $event) {
    $repo = $config['repo'];
    $quizattempt = $repo->read_record_by_id('quiz_attempts', $event->objectid);
    // Other two look ups should be returning one record, This one should return all questions attempted.
    $questionattempts = $repo->read_records('question_attempts', ['questionusageid' => $quizattempt->uniqueid]);

    return array_merge(
        attempt_submitted($config, $event),
        array_reduce($questionattempts, function ($result, $questionattempt) use ($config, $event) {
            return array_merge($result, question_answered\handler($config, $event, $questionattempt));
        }, [])
    );
}
