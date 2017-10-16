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
            'popovertitle' => [
                'type' => PARAM_RAW,
                'default' => '',
            ],
            'haslastdayofevent' => [
                'type' => PARAM_BOOL,
                'default' => false,
            ],
            'filter_selector' => [
                'type' => PARAM_RAW,
            ],
            'new_event_button' => [
                'type' => PARAM_RAW,
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
            'filter_selector' => $this->get_course_filter_selector($output),
            'new_event_button' => $this->get_new_event_button(),
            'viewdaylink' => $this->url->out(false),
        ];


        $cache = $this->related['cache'];
        $eventexporters = array_map(function($event) use ($cache, $output) {
            $context = $cache->get_context($event);
            $course = $cache->get_course($event);
            $exporter = new calendar_event_exporter($event, [
                'context' => $context,
                'course' => $course,
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

    /**
     * Get the course filter selector.
     *
     * This is a temporary solution, this code will be removed by MDL-60096.
     *
     * @param renderer_base $output
     * @return string The html code for the course filter selector.
     */
    protected function get_course_filter_selector(renderer_base $output) {
        global $CFG;
        // TODO remove this code on MDL-60096.
        if (!isloggedin() or isguestuser()) {
            return '';
        }

        if (has_capability('moodle/calendar:manageentries', \context_system::instance()) && !empty($CFG->calendar_adminseesall)) {
            $courses = get_courses('all', 'c.shortname', 'c.id, c.shortname');
        } else {
            $courses = enrol_get_my_courses();
        }

        unset($courses[SITEID]);

        $courseoptions = array();
        $courseoptions[SITEID] = get_string('fulllistofcourses');
        foreach ($courses as $course) {
            $coursecontext = \context_course::instance($course->id);
            $courseoptions[$course->id] = format_string($course->shortname, true, array('context' => $coursecontext));
        }

        if ($this->calendar->courseid !== SITEID) {
            $selected = $this->calendar->courseid;
        } else {
            $selected = '';
        }

        $courseurl = new moodle_url($this->url);
        $courseurl->remove_params('course');
        $select = new \single_select($courseurl, 'courseselect', $courseoptions, $selected, null);
        $select->class = 'm-r-1';
        $label = get_string('dayviewfor', 'calendar');
        if ($label !== null) {
            $select->set_label($label);
        } else {
            $select->set_label(get_string('listofcourses'), array('class' => 'accesshide'));
        }

        return $output->render($select);
    }

    /**
     * Get the course filter selector.
     *
     * This is a temporary solution, this code will be removed by MDL-60096.
     *
     * @return string The html code for the course filter selector.
     */
    protected function get_new_event_button() {
        // TODO remove this code on MDL-60096.
        $output = \html_writer::start_tag('div', array('class' => 'buttons'));
        $output .= \html_writer::start_tag('form',
                array('action' => CALENDAR_URL . 'event.php', 'method' => 'get'));
        $output .= \html_writer::start_tag('div');
        $output .= \html_writer::empty_tag('input',
                array('type' => 'hidden', 'name' => 'action', 'value' => 'new'));
        $output .= \html_writer::empty_tag('input',
                array('type' => 'hidden', 'name' => 'course', 'value' => $this->calendar->courseid));
        $output .= \html_writer::empty_tag('input',
                array('type' => 'hidden', 'name' => 'time', 'value' => $this->calendar->time));
        $attributes = array('type' => 'submit', 'value' => get_string('newevent', 'calendar'),
            'class' => 'btn btn-secondary');
        $output .= \html_writer::empty_tag('input', $attributes);
        $output .= \html_writer::end_tag('div');
        $output .= \html_writer::end_tag('form');
        $output .= \html_writer::end_tag('div');
        return $output;
    }
}
