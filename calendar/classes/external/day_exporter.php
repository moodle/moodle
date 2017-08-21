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
 * Contains event class for displaying the day view.
 *
 * @package   core_calendar
 * @copyright 2017 Andrew Nicols <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_calendar\external;

defined('MOODLE_INTERNAL') || die();

use core\external\exporter;
use renderer_base;
use moodle_url;

/**
 * Class for displaying the day view.
 *
 * @package   core_calendar
 * @copyright 2017 Andrew Nicols <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class day_exporter extends exporter {

    /**
     * Return the list of properties.
     *
     * @return array
     */
    protected static function define_properties() {
        // These are the default properties as returned by getuserdate()
        // but without the formatted month and week names.
        return [
            'seconds' => [
                'type' => PARAM_INT,
            ],
            'minutes' => [
                'type' => PARAM_INT,
            ],
            'hours' => [
                'type' => PARAM_INT,
            ],
            'mday' => [
                'type' => PARAM_INT,
            ],
            'wday' => [
                'type' => PARAM_INT,
            ],
            'year' => [
                'type' => PARAM_INT,
            ],
            'yday' => [
                'type' => PARAM_INT,
            ],
        ];
    }

    /**
     * Return the list of additional properties.
     *
     * @return array
     */
    protected static function define_other_properties() {
        return [
            'timestamp' => [
                'type' => PARAM_INT,
            ],
            'neweventtimestamp' => [
                'type' => PARAM_INT,
            ],
            'istoday' => [
                'type' => PARAM_BOOL,
                'default' => false,
            ],
            'isweekend' => [
                'type' => PARAM_BOOL,
                'default' => false,
            ],
            'viewdaylink' => [
                'type' => PARAM_URL,
                'optional' => true,
            ],
            'events' => [
                'type' => calendar_event_exporter::read_properties_definition(),
                'multiple' => true,
            ]
        ];
    }

    /**
     * Get the additional values to inject while exporting.
     *
     * @param renderer_base $output The renderer.
     * @return array Keys are the property names, values are their values.
     */
    protected function get_other_values(renderer_base $output) {
        $timestamp = $this->data[0];
        // Need to account for user's timezone.
        $usernow = usergetdate(time());
        $today = new \DateTimeImmutable();
        // The start time should use the day's date but the current
        // time of the day (adjusted for user's timezone).
        $neweventstarttime = $today->setTimestamp($timestamp)->setTime(
            $usernow['hours'],
            $usernow['minutes'],
            $usernow['seconds']
        );

        $return = [
            'timestamp' => $timestamp,
            'neweventtimestamp' => $neweventstarttime->getTimestamp()
        ];

        $url = new moodle_url('/calendar/view.php', [
                'view' => 'day',
                'time' => $timestamp,
            ]);
        $return['viewdaylink'] = $url->out(false);

        $cache = $this->related['cache'];
        $return['events'] = array_map(function($event) use ($cache, $output, $url) {
            $context = $cache->get_context($event);
            $course = $cache->get_course($event);
            $exporter = new calendar_event_exporter($event, [
                'context' => $context,
                'course' => $course,
                'daylink' => $url,
            ]);

            return $exporter->export($output);
        }, $this->related['events']);

        return $return;
    }

    /**
     * Returns a list of objects that are related.
     *
     * @return array
     */
    protected static function define_related() {
        return [
            'events' => '\core_calendar\local\event\entities\event_interface[]',
            'cache' => '\core_calendar\external\events_related_objects_cache',
            'type' => '\core_calendar\type_base',
        ];
    }
}
