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
 * Contains event class for displaying the month view.
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
 * Class for displaying the month view.
 *
 * @package   core_calendar
 * @copyright 2017 Andrew Nicols <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class month_exporter extends exporter {

    /**
     * @var \calendar_information $calendar The calendar to be rendered.
     */
    protected $calendar;

    /**
     * @var int $firstdayofweek The first day of the week.
     */
    protected $firstdayofweek;

    /**
     * @var moodle_url $url The URL for the events page.
     */
    protected $url;

    /**
     * Constructor for month_exporter.
     *
     * @param \calendar_information $calendar The calendar being represented
     * @param \core_calendar\type_base $type The calendar type (e.g. Gregorian)
     * @param array $related The related information
     */
    public function __construct(\calendar_information $calendar, \core_calendar\type_base $type, $related) {
        $this->calendar = $calendar;
        $this->firstdayofweek = $type->get_starting_weekday();

        $this->url = new moodle_url('/calendar/view.php', [
                'view' => 'month',
                'time' => $calendar->time,
            ]);

        if ($this->calendar->courseid) {
            $this->url->param('course', $this->calendar->courseid);
        }

        $related['type'] = $type;

        parent::__construct([], $related);
    }

    /**
     * Return the list of additional properties.
     *
     * @return array
     */
    protected static function define_other_properties() {
        return [
            'courseid' => [
                'type' => PARAM_INT,
            ],
            'filter_selector' => [
                'type' => PARAM_RAW,
            ],
            'navigation' => [
                'type' => PARAM_RAW,
            ],
            'weeks' => [
                'type' => week_exporter::read_properties_definition(),
                'multiple' => true,
            ],
            'daynames' => [
                'type' => day_name_exporter::read_properties_definition(),
                'multiple' => true,
            ],
            'view' => [
                'type' => PARAM_ALPHA,
            ],
            'previousperiod' => [
                'type' => PARAM_INT,
            ],
            'nextperiod' => [
                'type' => PARAM_INT,
            ],
            'time' => [
                'type' => PARAM_INT,
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
        return [
            'courseid' => $this->calendar->courseid,
            'view' => 'month',
            'previousperiod' => $this->get_previous_month_timestamp(),
            'nextperiod' => $this->get_next_month_timestamp(),
            'filter_selector' => $this->get_course_filter_selector($output),
            'navigation' => $this->get_navigation($output),
            'weeks' => $this->get_weeks($output),
            'daynames' => $this->get_day_names($output),
            'time' => $this->calendar->time
        ];
    }

    /**
     * Get the course filter selector.
     *
     * @param renderer_base $output
     * @return string The html code for the course filter selector.
     */
    protected function get_course_filter_selector(renderer_base $output) {
        $content = '';
        $content .= $output->course_filter_selector($this->url, get_string('detailedmonthviewfor', 'calendar'));
        if (calendar_user_can_add_event($this->calendar->course)) {
            $content .= $output->add_event_button($this->calendar->courseid, 0, 0, 0, $this->calendar->time);
        }

        return $content;
    }

    /**
     * Get the calendar navigation controls.
     *
     * @param renderer_base $output
     * @return string The html code to the calendar top navigation.
     */
    protected function get_navigation(renderer_base $output) {
        return calendar_top_controls('month', [
            'id' => $this->calendar->courseid,
            'time' => $this->calendar->time,
        ]);
    }

    /**
     * Get the list of day names for display, re-ordered from the first day
     * of the week.
     *
     * @param   renderer_base $output
     * @return  day_name_exporter[]
     */
    protected function get_day_names(renderer_base $output) {
        $weekdays = $this->related['type']->get_weekdays();
        $daysinweek = count($weekdays);

        $daynames = [];
        for ($i = 0; $i < $daysinweek; $i++) {
            // Bump the currentdayno and ensure it loops.
            $dayno = ($i + $this->firstdayofweek + $daysinweek) % $daysinweek;
            $dayname = new day_name_exporter($dayno, $weekdays[$dayno]);
            $daynames[] = $dayname->export($output);
        }

        return $daynames;
    }

    /**
     * Get the list of week days, ordered into weeks and padded according
     * to the value of the first day of the week.
     *
     * @param renderer_base $output
     * @return array The list of weeks.
     */
    protected function get_weeks(renderer_base $output) {
        $weeks = [];
        $alldays = $this->get_days();

        $daysinweek = count($this->related['type']->get_weekdays());

        // Calculate which day number is the first, and last day of the week.
        $firstdayofweek = $this->firstdayofweek;
        $lastdayofweek = ($firstdayofweek + $daysinweek - 1) % $daysinweek;

        // The first week is special as it may have padding at the beginning.
        $day = reset($alldays);
        $firstdayno = $day['wday'];

        $prepadding = ($firstdayno + $daysinweek - 1) % $daysinweek;
        $daysinfirstweek = $daysinweek - $prepadding;
        $days = array_slice($alldays, 0, $daysinfirstweek);
        $week = new week_exporter($this->calendar, $days, $prepadding, ($daysinweek - count($days) - $prepadding), $this->related);
        $weeks[] = $week->export($output);

        // Now chunk up the remaining day. and turn them into weeks.
        $daychunks = array_chunk(array_slice($alldays, $daysinfirstweek), $daysinweek);
        foreach ($daychunks as $days) {
            $week = new week_exporter($this->calendar, $days, 0, ($daysinweek - count($days)), $this->related);
            $weeks[] = $week->export($output);
        }

        return $weeks;
    }

    /**
     * Get the list of days with the matching date array.
     *
     * @return array
     */
    protected function get_days() {
        $date = $this->related['type']->timestamp_to_date_array($this->calendar->time);
        $monthdays = $this->related['type']->get_num_days_in_month($date['year'], $date['mon']);

        $days = [];
        for ($dayno = 1; $dayno <= $monthdays; $dayno++) {
            // Get the gregorian representation of the day.
            $timestamp = $this->related['type']->convert_to_timestamp($date['year'], $date['mon'], $dayno);

            $days[] = $this->related['type']->timestamp_to_date_array($timestamp);
        }

        return $days;
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
     * Get the previous month timestamp.
     *
     * @return int The previous month timestamp.
     */
    protected function get_previous_month_timestamp() {
        $date = $this->related['type']->timestamp_to_date_array($this->calendar->time);
        $month = calendar_sub_month($date['mon'], $date['year']);
        $monthtime = $this->related['type']->convert_to_gregorian($month[1], $month[0], 1);

        return make_timestamp($monthtime['year'], $monthtime['month'], $monthtime['day'], $monthtime['hour'], $monthtime['minute']);
    }

    /**
     * Get the next month timestamp.
     *
     * @return int The next month timestamp.
     */
    protected function get_next_month_timestamp() {
        $date = $this->related['type']->timestamp_to_date_array($this->calendar->time);
        $month = calendar_sub_month($date['mon'], $date['year']);
        $monthtime = $this->related['type']->convert_to_gregorian($month[1], $month[0], 1);

        return make_timestamp($monthtime['year'], $monthtime['month'], $monthtime['day'], $monthtime['hour'], $monthtime['minute']);
    }
}
