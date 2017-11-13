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
 * Contains event class for displaying the day on month view.
 *
 * @package   core_calendar
 * @copyright 2017 Andrew Nicols <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_calendar\external;

defined('MOODLE_INTERNAL') || die();

use renderer_base;
use moodle_url;

/**
 * Class for displaying the day on month view.
 *
 * @package   core_calendar
 * @copyright 2017 Andrew Nicols <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class week_day_exporter extends day_exporter {

    /**
     * Return the list of properties.
     *
     * @return array
     */
    protected static function define_properties() {
        $return = parent::define_properties();
        $return = array_merge($return, [
            // These are additional params.
            'istoday' => [
                'type' => PARAM_BOOL,
                'default' => false,
            ],
            'isweekend' => [
                'type' => PARAM_BOOL,
                'default' => false,
            ],
        ]);

        return $return;
    }
    /**
     * Return the list of additional properties.
     *
     * @return array
     */
    protected static function define_other_properties() {
        $return = parent::define_other_properties();
        $return = array_merge($return, [
            'popovertitle' => [
                'type' => PARAM_RAW,
                'default' => '',
            ],
        ]);

        return $return;
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

        $return = parent::get_other_values($output);

        $url = new moodle_url('/calendar/view.php', [
                'view' => 'day',
                'time' => $timestamp,
            ]);

        if ($this->calendar->course && SITEID !== $this->calendar->course->id) {
            $url->param('course', $this->calendar->course->id);
        } else if ($this->calendar->categoryid) {
            $url->param('category', $this->calendar->categoryid);
        }

        $return['viewdaylink'] = $url->out(false);

        if ($popovertitle = $this->get_popover_title()) {
            $return['popovertitle'] = $popovertitle;
        }
        $cache = $this->related['cache'];
        $eventexporters = array_map(function($event) use ($cache, $output, $url) {
            $context = $cache->get_context($event);
            $course = $cache->get_course($event);
            $exporter = new calendar_event_exporter($event, [
                'context' => $context,
                'course' => $course,
                'daylink' => $url,
                'type' => $this->related['type'],
                'today' => $this->data[0],
            ]);

            return $exporter;
        }, $this->related['events']);

        $return['events'] = array_map(function($exporter) use ($output) {
            return $exporter->export($output);
        }, $eventexporters);

        if ($popovertitle = $this->get_popover_title()) {
            $return['popovertitle'] = $popovertitle;
        }

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
     * Get the title for this popover.
     *
     * @return string
     */
    protected function get_popover_title() {
        $title = null;

        $userdate = userdate($this->data[0], get_string('strftimedayshort'));
        if (count($this->related['events'])) {
            $title = get_string('eventsfor', 'calendar', $userdate);
        } else if ($this->data['istoday']) {
            $title = $userdate;
        }

        if ($this->data['istoday']) {
            $title = get_string('todayplustitle', 'calendar', $userdate);
        }

        return $title;
    }
}
