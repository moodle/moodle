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
 * Transform for the book chapter viewed event.
 *
 * @package   logstore_xapi
 * @copyright Jerret Fowler <jerrett.fowler@gmail.com>
 *            Ryan Smith <https://www.linkedin.com/in/ryan-smith-uk/>
 *            David Pesce <david.pesce@exputo.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\transformer\events\mod_book;

use src\transformer\utils as utils;

/**
 * Transformer for book chapter viewed event.
 *
 * @param array $config The transformer config settings.
 * @param \stdClass $event The event to be transformed.
 * @return array
 */
function chapter_viewed(array $config, \stdClass $event) {
    $repo = $config['repo'];
    $user = $repo->read_record_by_id('user', $event->userid);
    $course = $repo->read_record_by_id('course', $event->courseid);
    $chapter = $repo->read_record_by_id('book_chapters', $event->objectid);
    $lang = utils\get_course_lang($course);

    $statement = [
        'actor' => utils\get_user($config, $user),
        'verb' => [
            'id' => 'http://id.tincanapi.com/verb/viewed',
            'display' => [
                $lang => 'viewed'
            ]
        ],
        'object' => utils\get_activity\book_chapter($config, $course, $chapter, $event->contextinstanceid),
        'timestamp' => utils\get_event_timestamp($event),
        'context' => [
            'platform' => $config['source_name'],
            'language' => $lang,
            'extensions' => utils\extensions\base($config, $event, $course),
            'contextActivities' => [
                'grouping' => [
                    utils\get_activity\site($config),
                    utils\get_activity\course($config, $course),
                    utils\get_activity\course_module(
                        $config,
                        $course,
                        $event->contextinstanceid,
                        'http://id.tincanapi.com/activitytype/book'
                    )
                ],
                'category' => [
                    utils\get_activity\source($config),
                ]
            ]
        ]
    ];

    if ($chapter->subchapter != '0') {
        $parentchapter = $repo->read_record_by_id('book_chapters', $chapter->subchapter);
        $statement['context']['contextActivities']['parent'] = [
            utils\get_activity\book_chapter($config, $course, $parentchapter, $event->contextinstanceid)
        ];
    }

    return [$statement];
}
