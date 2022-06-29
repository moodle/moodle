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
 * Contains event class for displaying the week view.
 *
 * @package   core_calendar
 * @copyright 2017 Andrew Nicols <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_calendar\external;

defined('MOODLE_INTERNAL') || die();

use core\external\exporter;
use renderer_base;

/**
 * Class for displaying the week view.
 *
 * @package   core_calendar
 * @copyright 2017 Andrew Nicols <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class week_exporter extends exporter {

    /**
     * @var array $days An array of day_exporter objects.
     */
    protected $days = [];

    /**
     * @var int $prepadding The number of pre-padding days at the start of the week.
     */
    protected $prepadding = 0;

    /**
     * @var int $postpadding The number of post-padding days at the start of the week.
     */
    protected $postpadding = 0;

    /**
     * @var \calendar_information $calendar The calendar being displayed.
     */
    protected $calendar;

    /**
     * Constructor.
     *
     * @param \calendar_information $calendar The calendar information for the period being displayed
     * @param mixed $days An array of day_exporter objects.
     * @param int $prepadding The number of pre-padding days at the start of the week.
     * @param int $postpadding The number of post-padding days at the start of the week.
     * @param array $related Related objects.
     */
    public function __construct(\calendar_information $calendar, $days, $prepadding, $postpadding, $related) {
        $this->days = $days;
        $this->prepadding = $prepadding;
        $this->postpadding = $postpadding;
        $this->calendar = $calendar;

        parent::__construct([], $related);
    }

    /**
     * Return the list of additional properties.
     *
     * @return array
     */
    protected static function define_other_properties() {
        return [
            'prepadding' => [
                'type' => PARAM_INT,
                'multiple' => true,
            ],
            'postpadding' => [
                'type' => PARAM_INT,
                'multiple' => true,
            ],
            'days' => [
                'type' => week_day_exporter::read_properties_definition(),
                'multiple' => true,
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
        global $CFG;
        $return = [
            'prepadding' => [],
            'postpadding' => [],
            'days' => [],
        ];

        for ($i = 0; $i < $this->prepadding; $i++) {
            $return['prepadding'][] = $i;
        }
        for ($i = 0; $i < $this->postpadding; $i++) {
            $return['postpadding'][] = $i;
        }

        $return['days'] = [];
        $today = $this->related['type']->timestamp_to_date_array(time());

        $weekend = CALENDAR_DEFAULT_WEEKEND;
        if (isset($CFG->calendar_weekend)) {
            $weekend = intval($CFG->calendar_weekend);
        }
        $numberofdaysinweek = $this->related['type']->get_num_weekdays();

        foreach ($this->days as $daydata) {
            $events = [];
            foreach ($this->related['events'] as $event) {
                $times = $event->get_times();
                $starttime = $times->get_start_time()->getTimestamp();
                $startdate = $this->related['type']->timestamp_to_date_array($starttime);
                $endtime = $times->get_end_time()->getTimestamp();
                $enddate = $this->related['type']->timestamp_to_date_array($endtime);

                if ((($startdate['year'] * 366) + $startdate['yday']) > ($daydata['year'] * 366) + $daydata['yday']) {
                    // Starts after today.
                    continue;
                }
                if ((($enddate['year'] * 366) + $enddate['yday']) < ($daydata['year'] * 366) + $daydata['yday']) {
                    // Ends before today.
                    continue;
                }
                $events[] = $event;
            }

            $istoday = true;
            $istoday = $istoday && $today['year'] == $daydata['year'];
            $istoday = $istoday && $today['yday'] == $daydata['yday'];
            $daydata['istoday'] = $istoday;

            $daydata['isweekend'] = !!($weekend & (1 << ($daydata['wday'] % $numberofdaysinweek)));

            $day = new week_day_exporter($this->calendar, $daydata, [
                'events' => $events,
                'cache' => $this->related['cache'],
                'type' => $this->related['type'],
            ]);

            $return['days'][] = $day->export($output);
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
}
