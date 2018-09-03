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

require_once($CFG->dirroot . '/calendar/lib.php');

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
     * @var \calendar_information $calendar The calendar being displayed.
     */
    protected $calendar;

    /**
     * @var moodle_url
     */
    protected $url;
    /**
     * Constructor.
     *
     * @param \calendar_information $calendar The calendar information for the period being displayed
     * @param mixed $data Either an stdClass or an array of values.
     * @param array $related Related objects.
     */
    public function __construct(\calendar_information $calendar, $data, $related) {
        $this->calendar = $calendar;

        $url = new moodle_url('/calendar/view.php', [
                'view' => 'day',
                'time' => $calendar->time,
            ]);

        if ($this->calendar->course && SITEID !== $this->calendar->course->id) {
            $url->param('course', $this->calendar->course->id);
        } else if ($this->calendar->categoryid) {
            $url->param('category', $this->calendar->categoryid);
        }

        $this->url = $url;

        parent::__construct($data, $related);
    }

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
            'viewdaylink' => [
                'type' => PARAM_URL,
                'optional' => true,
            ],
            'events' => [
                'type' => calendar_event_exporter::read_properties_definition(),
                'multiple' => true,
            ],
            'hasevents' => [
                'type' => PARAM_BOOL,
                'default' => false,
            ],
            'calendareventtypes' => [
                'type' => PARAM_RAW,
                'multiple' => true,
            ],
            'previousperiod' => [
                'type' => PARAM_INT,
            ],
            'nextperiod' => [
                'type' => PARAM_INT,
            ],
            'navigation' => [
                'type' => PARAM_RAW,
            ],
            'haslastdayofevent' => [
                'type' => PARAM_BOOL,
                'default' => false,
            ],
        ];
    }

    /**
     * Get the additional values to inject while exporting.
     *
     * @param renderer_base $output The renderer.
     * @return array Keys are the property names, values are their values.
     */
    protected function get_other_values(renderer_base $output) {
        $daytimestamp = $this->calendar->time;
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
            'neweventtimestamp' => $neweventstarttime->getTimestamp(),
            'previousperiod' => $this->get_previous_day_timestamp($daytimestamp),
            'nextperiod' => $this->get_next_day_timestamp($daytimestamp),
            'navigation' => $this->get_navigation(),
            'viewdaylink' => $this->url->out(false),
        ];


        $cache = $this->related['cache'];
        $eventexporters = array_map(function($event) use ($cache, $output) {
            $context = $cache->get_context($event);
            $course = $cache->get_course($event);
            $moduleinstance = $cache->get_module_instance($event);
            $exporter = new calendar_event_exporter($event, [
                'context' => $context,
                'course' => $course,
                'moduleinstance' => $moduleinstance,
                'daylink' => $this->url,
                'type' => $this->related['type'],
                'today' => $this->data[0],
            ]);

            return $exporter;
        }, $this->related['events']);

        $return['events'] = array_map(function($exporter) use ($output) {
            return $exporter->export($output);
        }, $eventexporters);

        $return['hasevents'] = !empty($return['events']);

        $return['calendareventtypes'] = array_map(function($exporter) {
            return $exporter->get_calendar_event_type();
        }, $eventexporters);
        $return['calendareventtypes'] = array_values(array_unique($return['calendareventtypes']));

        $return['haslastdayofevent'] = false;
        foreach ($return['events'] as $event) {
            if ($event->islastday) {
                $return['haslastdayofevent'] = true;
                break;
            }
        }

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

    /**
     * Get the previous day timestamp.
     *
     * @param int $daytimestamp The current day timestamp.
     * @return int The previous day timestamp.
     */
    protected function get_previous_day_timestamp($daytimestamp) {
        return $this->related['type']->get_prev_day($daytimestamp);
    }

    /**
     * Get the next day timestamp.
     *
     * @param int $daytimestamp The current day timestamp.
     * @return int The next day timestamp.
     */
    protected function get_next_day_timestamp($daytimestamp) {
        return $this->related['type']->get_next_day($daytimestamp);
    }

    /**
     * Get the calendar navigation controls.
     *
     * @return string The html code to the calendar top navigation.
     */
    protected function get_navigation() {
        return calendar_top_controls('day', [
            'id' => $this->calendar->courseid,
            'time' => $this->calendar->time,
        ]);
    }
}
