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
 * Transform for the quiz attempt submitted event.
 *
 * @package   logstore_xapi
 * @copyright Jerret Fowler <jerrett.fowler@gmail.com>
 *            Ryan Smith <https://www.linkedin.com/in/ryan-smith-uk/>
 *            David Pesce <david.pesce@exputo.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\transformer\events\mod_quiz\attempt_submitted;

use src\transformer\utils as utils;

/**
 * Transformer for quiz attempt submitted event.
 *
 * @param array $config The transformer config settings.
 * @param \stdClass $event The event to be transformed.
 * @return array
 */
function attempt_submitted(array $config, \stdClass $event) {
    $repo = $config['repo'];
    $user = $repo->read_record_by_id('user', $event->relateduserid);
    $course = $repo->read_record_by_id('course', $event->courseid);
    $attempt = $repo->read_record_by_id('quiz_attempts', $event->objectid);
    $coursemodule = $repo->read_record_by_id('course_modules', $event->contextinstanceid);
    $quiz = $repo->read_record_by_id('quiz', $attempt->quiz);
    $gradeitem = $repo->read_record('grade_items', [
        'itemmodule' => 'quiz',
        'iteminstance' => $quiz->id,
    ]);
    $attemptgrade = $repo->read_record('grade_grades', [
        'itemid' => $gradeitem->id,
        'userid' => $event->relateduserid
    ]);
    $lang = utils\get_course_lang($course);

    return [[
        'actor' => utils\get_user($config, $user),
        'verb' => utils\get_verb('completed', $config, $lang),
        'object' => utils\get_activity\course_quiz($config, $course, $event->contextinstanceid),
        'timestamp' => utils\get_event_timestamp($event),
        'result' => utils\get_attempt_result($config, $attempt, $gradeitem, $attemptgrade),
        'context' => [
            'platform' => $config['source_name'],
            'language' => $lang,
            'extensions' => utils\extensions\base($config, $event, $course),
            'contextActivities' => [
                'other' => [
                    utils\get_activity\quiz_attempt($config, $attempt->id, $coursemodule->id),
                ],
                'grouping' => [
                    utils\get_activity\site($config),
                    utils\get_activity\course($config, $course),
                ],
                'category' => [
                    utils\get_activity\source($config),
                ]
            ],
        ]
    ]];
}
