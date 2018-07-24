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
 * Event create form and update form mapper.
 *
 * @package    core_calendar
 * @copyright  2017 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_calendar\local\event\mappers;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/calendar/lib.php');

/**
 * Event create form and update form mapper class.
 *
 * This class will perform the necessary data transformations to take
 * a legacy event and build the appropriate data structure for both the
 * create and update event forms.
 *
 * It will also do the reverse transformation
 * and take the returned form data and provide a data structure that can
 * be used to set legacy event properties.
 *
 * @copyright 2017 Ryan Wyllie <ryan@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class create_update_form_mapper implements create_update_form_mapper_interface {

    /**
     * Generate the appropriate data for the form from a legacy event.
     *
     * @param \calendar_event $legacyevent
     * @return stdClass
     */
    public function from_legacy_event_to_data(\calendar_event $legacyevent) {
        $legacyevent->count_repeats();
        $data = $legacyevent->properties();
        $data->timedurationuntil = $legacyevent->timestart + $legacyevent->timeduration;
        $data->duration = (empty($legacyevent->timeduration)) ? 0 : 1;

        if ($legacyevent->eventtype == 'group') {
            // Set up the correct value for the to display on the form.
            $data->groupid = $legacyevent->groupid;
            $data->groupcourseid = $legacyevent->courseid;
        }
        if ($legacyevent->eventtype == 'course') {
            // Set up the correct value for the to display on the form.
            $data->courseid = $legacyevent->courseid;
        }
        $data->description = [
            'text' => $data->description,
            'format' => $data->format
        ];

        // Don't return context or subscription because they're not form values and break validation.
        if (isset($data->context)) {
            unset($data->context);
        }
        if (isset($data->subscription)) {
            unset($data->subscription);
        }

        return $data;
    }

    /**
     * Generate the appropriate calendar_event properties from the form data.
     *
     * @param \stdClass $data
     * @return stdClass
     */
    public function from_data_to_event_properties(\stdClass $data) {
        $properties = clone($data);

        if ($data->eventtype == 'group') {
            if (isset($data->groupcourseid)) {
                $properties->courseid = $data->groupcourseid;
                unset($properties->groupcourseid);
            }
            if (isset($data->groupid)) {
                $properties->groupid = $data->groupid;
            }
        } else {
            // Default course id if none is set.
            if (empty($properties->courseid)) {
                if ($properties->eventtype == 'site') {
                    $properties->courseid = SITEID;
                } else {
                    $properties->courseid = 0;
                }
            } else {
                $properties->courseid = $data->courseid;
            }
            if (empty($properties->groupid)) {
                $properties->groupid = 0;
            }
        }

        // Decode the form fields back into valid event property.
        $properties->timeduration = $this->get_time_duration_from_form_data($data);

        return $properties;
    }

    /**
     * A helper function to calculate the time duration for an event based on
     * the event_form data.
     *
     * @param \stdClass $data event_form data
     * @return int
     */
    private function get_time_duration_from_form_data(\stdClass $data) {
        if ($data->duration == 1) {
            return $data->timedurationuntil - $data->timestart;
        } else if ($data->duration == 2) {
            return $data->timedurationminutes * MINSECS;
        } else {
            return 0;
        }
    }
}
