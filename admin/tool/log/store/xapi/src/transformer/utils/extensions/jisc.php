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
 * Transformer utility for JISC xAPI extensions.
 *
 * @package   logstore_xapi
 * @copyright Jerret Fowler <jerrett.fowler@gmail.com>
 *            Ryan Smith <https://www.linkedin.com/in/ryan-smith-uk/>
 *            David Pesce <david.pesce@exputo.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\transformer\utils\extensions;

use src\transformer\utils as utils;

/**
 * Transformer utility for base xAPI extensions - JISC specific.
 *
 * @param array $config The transformer config settings.
 * @param \stdClass $event The event to be transformed.
 * @param object $course The course object.
 * @return array
 */
function jisc(array $config, \stdClass $event, $course) {
    return array_merge(
        [
            'http://xapi.jisc.ac.uk/sessionId' => $config['session_id'],
            'http://id.tincanapi.com/extension/ip-address' => $event->ip,
            'http://xapi.jisc.ac.uk/statementCat' => 'VLE',
        ],
        utils\extensions\course_area($course)
    );
}
