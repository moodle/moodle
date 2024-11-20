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
 * Transformer utility for retrieving (course assignment) activities.
 *
 * @package   logstore_xapi
 * @copyright Jerret Fowler <jerrett.fowler@gmail.com>
 *            Ryan Smith <https://www.linkedin.com/in/ryan-smith-uk/>
 *            David Pesce <david.pesce@exputo.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\transformer\utils\get_activity;

use src\transformer\utils as utils;

/**
 * Transformer utility for retrieving (course assignment) activities.
 *
 * @param array $config The transformer config settings.
 * @param string $cmid The id of the context.
 * @param string $name The name of the assignment.
 * @param string $lang The language of the assignment.
 * @return array
 */
function course_assignment(array $config, string $cmid, string $name, string $lang) {
    $object = [
        'id' => $config['app_url'] . '/mod/assign/view.php?id=' . $cmid,
        'definition' => [
            'type' => 'http://adlnet.gov/expapi/activities/assessment',
            'name' => [
                $lang => $name,
            ],
        ],
    ];

    if (utils\is_enabled_config($config, 'send_jisc_data')) {
        $repo = $config['repo'];
        $coursemodule = $repo->read_record_by_id('course_modules', $cmid);
        $course = $repo->read_record_by_id('course', $coursemodule->course);

        $object['definition']['extensions']['http://xapi.jisc.ac.uk/dueDate'] = $course->startdate;
    }

    return $object;
}
