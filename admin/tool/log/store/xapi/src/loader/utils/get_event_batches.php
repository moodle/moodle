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
 * Retrieve batches of events.
 *
 * @package   logstore_xapi
 * @copyright Jerret Fowler <jerrett.fowler@gmail.com>
 *            Ryan Smith <https://www.linkedin.com/in/ryan-smith-uk/>
 *            David Pesce <david.pesce@exputo.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\loader\utils;

/**
 * Retrieve batches of events.
 *
 * @param array $config An array of configuration settings.
 * @param array $transformedevents An array of events.
 * @return array
 */
function get_event_batches(array $config, array $transformedevents) {
    $maxbatchsize = $config['lrs_max_batch_size'];
    if (!empty($maxbatchsize) && $maxbatchsize < count($transformedevents)) {
        return array_chunk($transformedevents, $maxbatchsize);
    }
    return [$transformedevents];
}
