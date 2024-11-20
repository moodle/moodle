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
 * Transformer utility for retrieving facetoface session duration.
 *
 * @package   logstore_xapi
 * @copyright Jerret Fowler <jerrett.fowler@gmail.com>
 *            Ryan Smith <https://www.linkedin.com/in/ryan-smith-uk/>
 *            David Pesce <david.pesce@exputo.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\transformer\utils;

/**
 * Transformer utility for retrieving facetoface session duration.
 *
 * @param array $config The transformer config settings.
 * @param string $sessionid The id of the session.
 * @return int/float
 */
function get_session_duration(array $config, string $sessionid) {
    $repo = $config['repo'];
    $dates = $repo->read_records('facetoface_sessions_dates', [ 'sessionid' => $sessionid ]);
    $duration = 0;
    foreach ($dates as $index => $date) {
        $duration -= $date->timestart;
        $duration += $date->timefinish;
    }
    return $duration;
}
