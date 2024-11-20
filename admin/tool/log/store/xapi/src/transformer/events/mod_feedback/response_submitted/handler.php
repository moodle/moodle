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
 * Generic feedback handler for the response_submitted event.
 *
 * @package   logstore_xapi
 * @copyright Jerret Fowler <jerrett.fowler@gmail.com>
 *            Ryan Smith <https://www.linkedin.com/in/ryan-smith-uk/>
 *            David Pesce <david.pesce@exputo.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\transformer\events\mod_feedback\response_submitted;

use src\transformer\utils as utils;
use src\transformer\events\mod_feedback\item_answered as item_answered;

/**
 * Generic handler for the mod_feedback response submitted event.
 *
 * @param array $config The transformer config settings.
 * @param \stdClass $event The event to be transformed.
 * @return array
 */
function handler(array $config, \stdClass $event) {
    $repo = $config['repo'];
    $feedbackvalues = $repo->read_records('feedback_value', [
        'completed' => $event->objectid
    ]);

    return array_merge(
        response_submitted($config, $event),
        array_reduce($feedbackvalues, function ($result, $feedbackvalue) use ($config, $event) {
            return array_merge($result, item_answered\handler($config, $event, $feedbackvalue));
        }, [])
    );
}
