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
     * @var bool $includenavigation Whether navigation should be included on the output.
     */
    protected $includenavigation = true;

    /**
     * @var bool $initialeventsloaded Whether the events have been loaded for this month.
     */
    protected $initialeventsloaded = true;

    /**
     * @var bool $showcoursefilter Whether to render the course filter selector as well.
     */
    protected $showcoursefilter = false;

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

        if ($this->calendar->course && SITEID !== $this->calendar->course->id) {
            $this->url->param('course', $this->calendar->course->id);
        } else if ($this->calendar->categoryid) {
            $this->url->param('category', $this->calendar->categoryid);
        }

        $related['type'] = $type;

        $data = [
            'url' => $this->url->out(false),
        ];

        parent::__construct($data, $related);
    }

    protected static function define_properties() {
        return [
            'url' => [
                'type' => PARAM_URL,
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
            'courseid' => [
                'type' => PARAM_INT,
            ],
            'categoryid' => [
                'type' => PARAM_INT,
                'optional' => true,
                'default' => 0,
            ],
            'filter_selector' => [
                'type' => PARAM_RAW,
                'optional' => true,
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
            'date' => [
                'type' => date_exporter::read_properties_definition(),
            ],
            'periodname' => [
                // Note: We must use RAW here because the calendar type returns the formatted month name based on a
                // calendar format.
                'type' => PARAM_RAW,
            ],
            'includenavigation' => [
                'type' => PARAM_BOOL,
                'default' => true,
            ],
            // Tracks whether the first set of events have been loaded and provided
            // to the exporter.
            'initialeventsloaded' => [
                'type' => PARAM_BOOL,
                'default' => true,
            ],
            'previousperiod' => [
                'type' => date_exporter::read_properties_definition(),
            ],
            'previousperiodlink' => [
                'type' => PARAM_URL,
            ],
            'previousperiodname' => [
                // Note: We must use RAW here because the calendar type returns the formatted month name based on a
                // calendar format.
                'type' => PARAM_RAW,
            ],
            'nextperiod' => [
                'type' => date_exporter::read_properties_definition(),
            ],
            'nextperiodname' => [
                // Note: We must use RAW here because the calendar type returns the formatted month name based on a
                // calendar format.
                'type' => PARAM_RAW,
            ],
            'nextperiodlink' => [
                'type' => PARAM_URL,
            ],
            'larrow' => [
                // The left arrow defined by the theme.
                'type' => PARAM_RAW,
            ],
            'rarrow' => [
                // The right arrow defined by the theme.
                'type' => PARAM_RAW,
            ],
            'defaulteventcontext' => [
                'type' => PARAM_INT,
                'default' => 0,
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
        $previousperiod = $this->get_previous_month_data();
        $nextperiod = $this->get_next_month_data();
        $date = $this->related['type']->timestamp_to_date_array($this->calendar->time);

        $nextperiodlink = new moodle_url($this->url);
        $nextperiodlink->param('time', $nextperiod[0]);

        $previousperiodlink = new moodle_url($this->url);
        $previousperiodlink->param('time', $previousperiod[0]);

        $return = [
            'courseid' => $this->calendar->courseid,
            'weeks' => $this->get_weeks($output),
            'daynames' => $this->get_day_names($output),
            'view' => 'month',
            'date' => (new date_exporter($date))->export($output),
            'periodname' => userdate($this->calendar->time, get_string('strftimemonthyear')),
            'previousperiod' => (new date_exporter($previousperiod))->export($output),
            'previousperiodname' => userdate($previousperiod[0], get_string('strftimemonthyear')),
            'previousperiodlink' => $previousperiodlink->out(false),
            'nextperiod' => (new date_exporter($nextperiod))->export($output),
            'nextperiodname' => userdate($nextperiod[0], get_string('strftimemonthyear')),
            'nextperiodlink' => $nextperiodlink->out(false),
            'larrow' => $output->larrow(),
            'rarrow' => $output->rarrow(),
            'includenavigation' => $this->includenavigation,
            'initialeventsloaded' => $this->initialeventsloaded,
        ];

        if ($this->showcoursefilter) {
            $return['filter_selector'] = $this->get_course_filter_selector($output);
        }

        if ($context = $this->get_default_add_context()) {
            $return['defaulteventcontext'] = $context->id;
        }

        if ($this->calendar->categoryid) {
            $return['categoryid'] = $this->calendar->categoryid;
        }

        return $return;
    }

    /**
     * Get the course filter selector.
     *
     * @param renderer_base $output
     * @return string The html code for the course filter selector.
     */
    protected function get_course_filter_selector(renderer_base $output) {
        $content = '';
        $content .= $output->course_filter_selector($this->url, get_string('detailedmonthviewfor', 'calendar'),
            $this->calendar->course->id);

        return $content;
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

        // The first week is special as it may have padding at the beginning.
        $day = reset($alldays);
        $firstdayno = $day['wday'];

        $prepadding = ($firstdayno + $daysinweek - $firstdayofweek) % $daysinweek;
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
     * Get the current month timestamp.
     *
     * @return int The month timestamp.
     */
    protected function get_month_data() {
        $date = $this->related['type']->timestamp_to_date_array($this->calendar->time);
        $monthtime = $this->related['type']->convert_to_gregorian($date['year'], $date['month'], 1);

        return make_timestamp($monthtime['year'], $monthtime['month']);
    }

    /**
     * Get the previous month timestamp.
     *
     * @return int The previous month timestamp.
     */
    protected function get_previous_month_data() {
        $type = $this->related['type'];
        $date = $type->timestamp_to_date_array($this->calendar->time);
        list($date['mon'], $date['year']) = $type->get_prev_month($date['year'], $date['mon']);
        $time = $type->convert_to_timestamp($date['year'], $date['mon'], 1);

        return $type->timestamp_to_date_array($time);
    }

    /**
     * Get the next month timestamp.
     *
     * @return int The next month timestamp.
     */
    protected function get_next_month_data() {
        $type = $this->related['type'];
        $date = $type->timestamp_to_date_array($this->calendar->time);
        list($date['mon'], $date['year']) = $type->get_next_month($date['year'], $date['mon']);
        $time = $type->convert_to_timestamp($date['year'], $date['mon'], 1);

        return $type->timestamp_to_date_array($time);
    }

    /**
     * Set whether the navigation should be shown.
     *
     * @param   bool    $include
     * @return  $this
     */
    public function set_includenavigation($include) {
        $this->includenavigation = $include;

        return $this;
    }

    /**
     * Set whether the initial events have already been loaded and
     * provided to the exporter.
     *
     * @param   bool    $loaded
     * @return  $this
     */
    public function set_initialeventsloaded(bool $loaded) {
        $this->initialeventsloaded = $loaded;

        return $this;
    }

    /**
     * Set whether the course filter selector should be shown.
     *
     * @param   bool    $show
     * @return  $this
     */
    public function set_showcoursefilter(bool $show) {
        $this->showcoursefilter = $show;

        return $this;
    }

    /**
     * Get the default context for use when adding a new event.
     *
     * @return null|\context
     */
    protected function get_default_add_context() {
        if (calendar_user_can_add_event($this->calendar->course)) {
            return \context_course::instance($this->calendar->course->id);
        }

        return null;
    }
}
