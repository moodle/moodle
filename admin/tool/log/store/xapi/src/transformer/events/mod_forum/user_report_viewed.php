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
 * Transform for the forum user report viewed event.
 *
 * @package   logstore_xapi
 * @copyright Jerret Fowler <jerrett.fowler@gmail.com>
 *            Ryan Smith <https://www.linkedin.com/in/ryan-smith-uk/>
 *            David Pesce <david.pesce@exputo.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\transformer\events\mod_forum;

use src\transformer\utils as utils;

/**
 * Transformer for forum user report viewed event.
 *
 * @param array $config The transformer config settings.
 * @param \stdClass $event The event to be transformed.
 * @return array
 */
function user_report_viewed(array $config, \stdClass $event) {
    $repo = $config['repo'];
    $user = $repo->read_record_by_id('user', $event->userid);
    $relateduser = $repo->read_record_by_id('user', $event->relateduserid);

    if ($event->courseid == "0") {
        $course = (object) [
            "id" => 0
        ];
        $lang = "en";
    } else {
        $course = $repo->read_record_by_id('course', $event->courseid);
        $lang = utils\get_course_lang($course);
    }

    $statement = [
        'actor' => utils\get_user($config, $user),
        'verb' => [
            'id' => 'http://id.tincanapi.com/verb/viewed',
            'display' => [
                $lang => 'viewed'
            ],
        ],
        'object' => utils\get_activity\user_report($config, $relateduser, $course, $lang),
        'timestamp' => utils\get_event_timestamp($event),
        'context' => [
            'platform' => $config['source_name'],
            'language' => $lang,
            'extensions' => utils\extensions\base($config, $event, $course),
            'contextActivities' => [
                'grouping' => [
                    utils\get_activity\site($config),
                ],
                'category' => [
                    utils\get_activity\source($config),
                ]
            ],
        ]
    ];

    if ($event->courseid != "0") {
        array_push($statement['context']['contextActivities']['grouping'], utils\get_activity\course($config, $course));
    }

    return[$statement];
}
