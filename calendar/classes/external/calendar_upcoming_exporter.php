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
 * Contains event class for displaying the upcoming view.
 *
 * @package   core_calendar
 * @copyright 2017 Simey Lameze <simey@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_calendar\external;

defined('MOODLE_INTERNAL') || die();

use core\external\exporter;
use renderer_base;
use moodle_url;
use \core_calendar\local\event\container;

/**
 * Class for displaying the day view.
 *
 * @package   core_calendar
 * @copyright 2017 Simey Lameze <simey@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class calendar_upcoming_exporter extends exporter {
    /**
     * @var \calendar_information $calendar The calendar to be rendered.
     */
    protected $calendar;

    /**
     * @var moodle_url $url The URL for the upcoming view page.
     */
    protected $url;

    /**
     * Constructor for upcoming exporter.
     *
     * @param \calendar_information $calendar The calendar being represented.
     * @param array $related The related information
     */
    public function __construct(\calendar_information $calendar, $related) {
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
            'events' => [
                'type' => calendar_event_exporter::read_properties_definition(),
                'multiple' => true,
            ],
            'defaulteventcontext' => [
                'type' => PARAM_INT,
                'default' => 0,
            ],
            'filter_selector' => [
                'type' => PARAM_RAW,
            ],
            'courseid' => [
                'type' => PARAM_INT,
            ],
            'categoryid' => [
                'type' => PARAM_INT,
                'optional' => true,
                'default' => 0,
            ],
            'isloggedin' => [
                'type' => PARAM_BOOL,
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
        $timestamp = $this->calendar->time;

        $cache = $this->related['cache'];
        $url = new moodle_url('/calendar/view.php', [
            'view' => 'upcoming',
            'time' => $timestamp,
            'course' => $this->calendar->course->id,
        ]);
        $this->url = $url;
        $return['isloggedin'] = isloggedin();
        $return['events'] = array_map(function($event) use ($cache, $output, $url) {
            $context = $cache->get_context($event);
            $course = $cache->get_course($event);
            $moduleinstance = $cache->get_module_instance($event);
            $exporter = new calendar_event_exporter($event, [
                'context' => $context,
                'course' => $course,
                'moduleinstance' => $moduleinstance,
                'daylink' => $url,
                'type' => $this->related['type'],
                'today' => $this->calendar->time,
            ]);

            $data = $exporter->export($output);

            // We need to override default formatted time because it differs from day view.
            // Formatted time for upcoming view adds a link to the day view.
            $legacyevent = container::get_event_mapper()->from_event_to_legacy_event($event);
            $data->formattedtime = calendar_format_event_time($legacyevent, time(), null);

            return $data;
        }, $this->related['events']);

        if ($context = $this->get_default_add_context()) {
            $return['defaulteventcontext'] = $context->id;
        }
        $return['filter_selector'] = $this->get_course_filter_selector($output);
        $return['courseid'] = $this->calendar->courseid;

        if ($this->calendar->categoryid) {
            $return['categoryid'] = $this->calendar->categoryid;
        }

        return $return;
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

    /**
     * Get the course filter selector.
     *
     * @param renderer_base $output
     * @return string The html code for the course filter selector.
     */
    protected function get_course_filter_selector(renderer_base $output) {
        $langstr = get_string('upcomingeventsfor', 'calendar');
        return $output->course_filter_selector($this->url, $langstr, $this->calendar->course->id);
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
