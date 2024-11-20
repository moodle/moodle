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
 * Transformer utility for retrieving the verb.
 *
 * @package   logstore_xapi
 * @copyright Jerret Fowler <jerrett.fowler@gmail.com>
 *            Ryan Smith <https://www.linkedin.com/in/ryan-smith-uk/>
 *            David Pesce <david.pesce@exputo.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\transformer\utils;

use src\transformer\utils as utils;

/**
 * Return the requested verb with details.
 *
 * @param string $verb The requested verb.
 * @param array $config Predefined config elements.
 * @param string $lang Language string.
 * @return array
 * @throws \coding_exception
 */
function get_verb(string $verb, array $config, string $lang) {

    $output = array();
    switch ($verb) {
        case 'completed':
            $output = [
                'id' => 'http://adlnet.gov/expapi/verbs/completed',
                'display' => [
                    $lang => 'completed'
                ],
            ];
            break;

        case 'loggedin':
            $output = [
                'id' => 'https://brindlewaye.com/xAPITerms/verbs/loggedin/',
                'display' => [
                    $lang => 'logged into'
                ]
            ];

            // JISC specific verb id.
            if (utils\is_enabled_config($config, 'send_jisc_data')) {
                $output['id'] = 'https://brindlewaye.com/xAPITerms/verbs/loggedin';
            }
            break;

        case 'loggedout':
            $output = [
                'id' => 'https://brindlewaye.com/xAPITerms/verbs/loggedout/',
                'display' => [
                    $lang => 'logged out of'
                ],
            ];

            // JISC specific verb id.
            if (utils\is_enabled_config($config, 'send_jisc_data')) {
                $output['id'] = 'https://brindlewaye.com/xAPITerms/verbs/loggedout';
            }
            break;

        case 'received':
            $output = [
                'id' => 'http://activitystrea.ms/schema/1.0/receive',
                'display' => [
                    $lang => 'received'
                ],
            ];
            break;

        case 'scored':
            $output = [
                'id' => 'http://adlnet.gov/expapi/verbs/scored',
                'display' => [
                    $lang => 'attained grade for'
                ],
            ];
            break;

        case 'started':
            $output = [
                'id' => 'http://activitystrea.ms/schema/1.0/start',
                'display' => [
                    $lang => 'started'
                ],
            ];
            break;

        case 'submitted':
            $output = [
                'id' => 'http://activitystrea.ms/schema/1.0/submit',
                'display' => [
                    $lang => 'submitted'
                ],
            ];
            break;

        case 'viewed':
            $output = [
                'id' => 'http://id.tincanapi.com/verb/viewed',
                'display' => [
                    $lang => 'viewed'
                ],
            ];
            break;

        default:
            break;
    }

    if (empty($output)) {
        throw new \coding_exception(get_string('unknownverb', 'logstore_xapi'));
    }

    return $output;
}
