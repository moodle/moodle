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
 * Contains event class for displaying a calendar event.
 *
 * @package   core_calendar
 * @copyright 2017 Ryan Wyllie <ryan@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_calendar\external;

defined('MOODLE_INTERNAL') || die();

use \core_course\external\course_summary_exporter;
use \renderer_base;

/**
 * Class for displaying a calendar event.
 *
 * @package   core_calendar
 * @copyright 2017 Ryan Wyllie <ryan@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class calendar_event_exporter extends event_exporter_base {

    /**
     * Return the list of additional properties.
     *
     * @return array
     */
    protected static function define_other_properties() {

        $values = parent::define_other_properties();
        $values['url'] = ['type' => PARAM_URL];
        $values['islastday'] = [
            'type' => PARAM_BOOL,
            'default' => false,
        ];
        $values['calendareventtype'] = [
            'type' => PARAM_TEXT,
        ];
        $values['popupname'] = [
            'type' => PARAM_RAW,
        ];

        return $values;
    }

    /**
     * Get the additional values to inject while exporting.
     *
     * @param renderer_base $output The renderer.
     * @return array Keys are the property names, values are their values.
     */
    protected function get_other_values(renderer_base $output) {
        global $CFG;

        $values = parent::get_other_values($output);

        $eventid = $this->event->get_id();

        $url = new \moodle_url($this->related['daylink'], [], "event_{$eventid}");
        $values['url'] = $url->out(false);

        $values['islastday'] = false;
        $today = $this->related['type']->timestamp_to_date_array($this->related['today']);

        $values['popupname'] = $this->event->get_name();

        $times = $this->event->get_times();
        if ($duration = $times->get_duration()) {
            $enddate = $this->related['type']->timestamp_to_date_array($times->get_end_time()->getTimestamp());
            $values['islastday'] = true;
            $values['islastday'] = $values['islastday'] && $enddate['year'] == $today['year'];
            $values['islastday'] = $values['islastday'] && $enddate['mon'] == $today['mon'];
            $values['islastday'] = $values['islastday'] && $enddate['mday'] == $today['mday'];
        }

        $subscription = $this->event->get_subscription();
        if ($subscription && !empty($subscription->get('id')) && $CFG->calendar_showicalsource) {
            $a = (object) [
                'name' => $values['popupname'],
                'source' => $subscription->get('name'),
            ];
            $values['popupname'] = get_string('namewithsource', 'calendar', $a);
        } else {
            if ($values['islastday']) {
                $startdate = $this->related['type']->timestamp_to_date_array($times->get_start_time()->getTimestamp());
                $samedate = true;
                $samedate = $samedate && $startdate['mon'] == $enddate['mon'];
                $samedate = $samedate && $startdate['year'] == $enddate['year'];
                $samedate = $samedate && $startdate['mday'] == $enddate['mday'];

                if (!$samedate) {
                    $values['popupname'] = get_string('eventendtimewrapped', 'calendar', $values['popupname']);
                }
            }
        }

        // Include course's shortname into the event name, if applicable.
        $course = $this->event->get_course();
        if ($course && $course->get('id') && $course->get('id') !== SITEID) {
            $eventnameparams = (object) [
                'name' => $values['popupname'],
                'course' => format_string($course->get('shortname'), true, [
                        'context' => $this->related['context'],
                    ])
            ];
            $values['popupname'] = get_string('eventnameandcourse', 'calendar', $eventnameparams);
        }

        $values['calendareventtype'] = $this->get_calendar_event_type();

        return $values;
    }

    /**
     * Returns a list of objects that are related.
     *
     * @return array
     */
    protected static function define_related() {
        $related = parent::define_related();
        $related['daylink'] = \moodle_url::class;
        $related['type'] = '\core_calendar\type_base';
        $related['today'] = 'int';

        return $related;
    }

    /**
     * Return the normalised event type.
     * Activity events are normalised to be course events.
     *
     * @return string
     */
    public function get_calendar_event_type() {
        $type = $this->event->get_type();
        if ($type == 'open' || $type == 'close') {
            $type = 'course';
        }

        return $type;
    }
}
