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
 * Transformer utility for retrieving the SCORM verb.
 *
 * @package   logstore_xapi
 * @copyright Jerret Fowler <jerrett.fowler@gmail.com>
 *            Ryan Smith <https://www.linkedin.com/in/ryan-smith-uk/>
 *            David Pesce <david.pesce@exputo.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\transformer\utils;

/**
 * Transformer utility for retrieving the SCORM verb.
 *
 * @param array $scormscoestracks An array of the SCORM tracks
 * @param string $lang The language of the event.
 * @return array
 */
function get_scorm_verb(array $scormscoestracks, string $lang) {
    $scormstatus = null;
    foreach ($scormscoestracks as $st) {
        if ($st->element == 'cmi.core.lesson_status') {
            $scormstatus = $st->value;
        }
    }

    switch ($scormstatus) {
        case 'failed':
            return [
                'id' => 'http://adlnet.gov/expapi/verbs/failed',
                'display' => [
                    $lang => 'failed'
                ],
            ];
        case 'passed':
            return [
                'id' => 'http://adlnet.gov/expapi/verbs/passed',
                'display' => [
                    $lang => 'passed'
                ],
            ];
        default:
            return [
                'id' => 'http://adlnet.gov/expapi/verbs/completed',
                'display' => [
                    $lang => 'completed'
                ],
            ];
    }
}
