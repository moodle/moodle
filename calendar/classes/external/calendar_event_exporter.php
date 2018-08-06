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

use \core_calendar\local\event\container;
use \core_course\external\course_summary_exporter;
use \renderer_base;
require_once($CFG->dirroot . '/course/lib.php');
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
        $values['mindaytimestamp'] = [
            'type' => PARAM_INT,
            'optional' => true
        ];
        $values['mindayerror'] = [
            'type' => PARAM_TEXT,
            'optional' => true
        ];
        $values['maxdaytimestamp'] = [
            'type' => PARAM_INT,
            'optional' => true
        ];
        $values['maxdayerror'] = [
            'type' => PARAM_TEXT,
            'optional' => true
        ];
        $values['draggable'] = [
            'type' => PARAM_BOOL,
            'default' => false
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
        $event = $this->event;
        $course = $this->related['course'];
        $hascourse = !empty($course);

        // By default all events that can be edited are
        // draggable.
        $values['draggable'] = $values['canedit'];

        if ($moduleproxy = $event->get_course_module()) {
            $modulename = $moduleproxy->get('modname');
            $moduleid = $moduleproxy->get('id');
            $url = new \moodle_url(sprintf('/mod/%s/view.php', $modulename), ['id' => $moduleid]);

            // Build edit event url for action events.
            $params = array('update' => $moduleid, 'return' => true, 'sesskey' => sesskey());
            $editurl = new \moodle_url('/course/mod.php', $params);
            $values['editurl'] = $editurl->out(false);
        } else if ($event->get_type() == 'category') {
            $url = $event->get_category()->get_proxied_instance()->get_view_link();
        } else {
            // TODO MDL-58866 We do not have any way to find urls for events outside of course modules.
            $url = course_get_url($hascourse ? $course : SITEID);
        }

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

        // Include category name into the event name, if applicable.
        $proxy = $this->event->get_category();
        if ($proxy && $proxy->get('id')) {
            $category = $proxy->get_proxied_instance();
            $eventnameparams = (object) [
                'name' => $values['popupname'],
                'category' => $category->get_formatted_name(),
            ];
            $values['popupname'] = get_string('eventnameandcategory', 'calendar', $eventnameparams);
        }

        // Include course's shortname into the event name, if applicable.
        if ($hascourse && $course->id !== SITEID) {
            $eventnameparams = (object) [
                'name' => $values['popupname'],
                'course' => $values['course']->shortname,
            ];
            $values['popupname'] = get_string('eventnameandcourse', 'calendar', $eventnameparams);
        }

        $values['calendareventtype'] = $this->get_calendar_event_type();

        if ($event->get_course_module()) {
            $values = array_merge($values, $this->get_module_timestamp_limits($event));
        } else if ($hascourse && $course->id != SITEID && empty($event->get_group())) {
            // This is a course event.
            $values = array_merge($values, $this->get_course_timestamp_limits($event));
        }

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
        $related['moduleinstance'] = 'stdClass?';

        return $related;
    }

    /**
     * Return the normalised event type.
     * Activity events are normalised to be course events.
     *
     * @return string
     */
    public function get_calendar_event_type() {
        if ($this->event->get_course_module()) {
            return 'course';
        }

        return $this->event->get_type();
    }

    /**
     * Return the set of minimum and maximum date timestamp values
     * for the given event.
     *
     * @param event_interface $event
     * @return array
     */
    protected function get_course_timestamp_limits($event) {
        $values = [];
        $mapper = container::get_event_mapper();
        $starttime = $event->get_times()->get_start_time();

        list($min, $max) = component_callback(
            'core_course',
            'core_calendar_get_valid_event_timestart_range',
            [$mapper->from_event_to_legacy_event($event), $event->get_course()->get_proxied_instance()],
            [false, false]
        );

        // The callback will return false for either of the
        // min or max cutoffs to indicate that there are no
        // valid timestart values. In which case the event is
        // not draggable.
        if ($min === false || $max === false) {
            return ['draggable' => false];
        }

        if ($min) {
            $values = array_merge($values, $this->get_timestamp_min_limit($starttime, $min));
        }

        if ($max) {
            $values = array_merge($values, $this->get_timestamp_max_limit($starttime, $max));
        }

        return $values;
    }

    /**
     * Return the set of minimum and maximum date timestamp values
     * for the given event.
     *
     * @param event_interface $event
     * @return array
     */
    protected function get_module_timestamp_limits($event) {
        $values = [];
        $mapper = container::get_event_mapper();
        $starttime = $event->get_times()->get_start_time();
        $modname = $event->get_course_module()->get('modname');
        $moduleinstance = $this->related['moduleinstance'];

        list($min, $max) = component_callback(
            'mod_' . $modname,
            'core_calendar_get_valid_event_timestart_range',
            [$mapper->from_event_to_legacy_event($event), $moduleinstance],
            [false, false]
        );

        // The callback will return false for either of the
        // min or max cutoffs to indicate that there are no
        // valid timestart values. In which case the event is
        // not draggable.
        if ($min === false || $max === false) {
            return ['draggable' => false];
        }

        if ($min) {
            $values = array_merge($values, $this->get_timestamp_min_limit($starttime, $min));
        }

        if ($max) {
            $values = array_merge($values, $this->get_timestamp_max_limit($starttime, $max));
        }

        return $values;
    }

    /**
     * Get the correct minimum midnight day limit based on the event start time
     * and the minimum timestamp limit of what the event belongs to.
     *
     * @param DateTimeInterface $starttime The event start time
     * @param array $min The module's minimum limit for the event
     * @return array Returns an array with mindaytimestamp and mindayerror keys.
     */
    protected function get_timestamp_min_limit(\DateTimeInterface $starttime, $min) {
        // We need to check that the minimum valid time is earlier in the
        // day than the current event time so that if the user drags and drops
        // the event to this day (which changes the date but not the time) it
        // will result in a valid time start for the event.
        //
        // For example:
        // An event that starts on 2017-01-10 08:00 with a minimum cutoff
        // of 2017-01-05 09:00 means that 2017-01-05 is not a valid start day
        // for the drag and drop because it would result in the event start time
        // being set to 2017-01-05 08:00, which is invalid. Instead the minimum
        // valid start day would be 2017-01-06.
        $values = [];
        $timestamp = $min[0];
        $errorstring = $min[1];
        $mindate = (new \DateTimeImmutable())->setTimestamp($timestamp);
        $minstart = $mindate->setTime(
            $starttime->format('H'),
            $starttime->format('i'),
            $starttime->format('s')
        );
        $midnight = usergetmidnight($timestamp);

        if ($mindate <= $minstart) {
            $values['mindaytimestamp'] = $midnight;
        } else {
            $tomorrow = (new \DateTime())->setTimestamp($midnight)->modify('+1 day');
            $values['mindaytimestamp'] = $tomorrow->getTimestamp();
        }

        // Get the human readable error message to display if the min day
        // timestamp is violated.
        $values['mindayerror'] = $errorstring;
        return $values;
    }

    /**
     * Get the correct maximum midnight day limit based on the event start time
     * and the maximum timestamp limit of what the event belongs to.
     *
     * @param DateTimeInterface $starttime The event start time
     * @param array $max The module's maximum limit for the event
     * @return array Returns an array with maxdaytimestamp and maxdayerror keys.
     */
    protected function get_timestamp_max_limit(\DateTimeInterface $starttime, $max) {
        // We're doing a similar calculation here as we are for the minimum
        // day timestamp. See the explanation above.
        $values = [];
        $timestamp = $max[0];
        $errorstring = $max[1];
        $maxdate = (new \DateTimeImmutable())->setTimestamp($timestamp);
        $maxstart = $maxdate->setTime(
            $starttime->format('H'),
            $starttime->format('i'),
            $starttime->format('s')
        );
        $midnight = usergetmidnight($timestamp);

        if ($maxdate >= $maxstart) {
            $values['maxdaytimestamp'] = $midnight;
        } else {
            $yesterday = (new \DateTime())->setTimestamp($midnight)->modify('-1 day');
            $values['maxdaytimestamp'] = $yesterday->getTimestamp();
        }

        // Get the human readable error message to display if the max day
        // timestamp is violated.
        $values['maxdayerror'] = $errorstring;
        return $values;
    }

    /**
     * Get the correct minimum midnight day limit based on the event start time
     * and the module's minimum timestamp limit.
     *
     * @param DateTimeInterface $starttime The event start time
     * @param array $min The module's minimum limit for the event
     * @return array Returns an array with mindaytimestamp and mindayerror keys.
     */
    protected function get_module_timestamp_min_limit(\DateTimeInterface $starttime, $min) {
        return $this->get_timestamp_min_limit($starttime, $min);
    }

    /**
     * Get the correct maximum midnight day limit based on the event start time
     * and the module's maximum timestamp limit.
     *
     * @param DateTimeInterface $starttime The event start time
     * @param array $max The module's maximum limit for the event
     * @return array Returns an array with maxdaytimestamp and maxdayerror keys.
     */
    protected function get_module_timestamp_max_limit(\DateTimeInterface $starttime, $max) {
        return $this->get_timestamp_max_limit($starttime, $max);
    }
}
