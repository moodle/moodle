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
 * Event mapper.
 *
 * @package    core_calendar
 * @copyright  2017 Cameron Ball <cameron@cameron1729.xyz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_calendar\local\event\mappers;

defined('MOODLE_INTERNAL') || die();

use core_calendar\event;
use core_calendar\local\event\entities\action_event_interface;
use core_calendar\local\event\entities\event_interface;
use core_calendar\local\event\factories\event_factory_interface;

/**
 * Event mapper class.
 *
 * @copyright 2017 Cameron Ball <cameron@cameron1729.xyz>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class event_mapper implements event_mapper_interface {
    /**
     * @var event_factory_interface $factory Event factory.
     */
    protected $factory;

    /**
     * Constructor.
     *
     * @param event_factory_interface $factory Event factory.
     */
    public function __construct(event_factory_interface $factory) {
        $this->factory = $factory;
    }

    public function from_legacy_event_to_event(\calendar_event $legacyevent) {
        $coalesce = function($property) use ($legacyevent) {
            try {
                return $legacyevent->$property;
            } catch (\coding_exception $e) {
                // The magic setter throews an exception if the
                // property doesn't exist.
                return null;
            }
        };

        return $this->factory->create_instance(
            (object)[
                'id' => $coalesce('id'),
                'name' => $coalesce('name'),
                'description' => $coalesce('description'),
                'location' => $coalesce('location'),
                'format' => $coalesce('format'),
                'categoryid' => $coalesce('categoryid'),
                'courseid' => $coalesce('courseid'),
                'groupid' => $coalesce('groupid'),
                'userid' => $coalesce('userid'),
                'repeatid' => $coalesce('repeatid'),
                'modulename' => $coalesce('modulename'),
                'instance' => $coalesce('instance'),
                'eventtype' => $coalesce('eventtype'),
                'timestart' => $coalesce('timestart'),
                'timeduration' => $coalesce('timeduration'),
                'timemodified' => $coalesce('timemodified'),
                'timesort' => $coalesce('timesort'),
                'visible' => $coalesce('visible'),
                'subscriptionid' => $coalesce('subscriptionid')
            ]
        );
    }

    public function from_event_to_legacy_event(event_interface $event) {
        $action = ($event instanceof action_event_interface) ? $event->get_action() : null;
        $timeduration = $event->get_times()->get_end_time()->getTimestamp() - $event->get_times()->get_start_time()->getTimestamp();
        $properties = $this->from_event_to_stdclass($event);

        // Normalise for the legacy event because it wants zero rather than null.
        $properties->courseid = empty($properties->courseid) ? 0 : $properties->courseid;
        $properties->groupid = empty($properties->groupid) ? 0 : $properties->groupid;
        $properties->userid = empty($properties->userid) ? 0 : $properties->userid;
        $properties->modulename = empty($properties->modulename) ? 0 : $properties->modulename;
        $properties->instance = empty($properties->instance) ? 0 : $properties->instance;
        $properties->repeatid = empty($properties->repeatid) ? 0 : $properties->repeatid;

        return new \calendar_event($properties);
    }

    public function from_event_to_stdclass(event_interface $event) {
        $action = ($event instanceof action_event_interface) ? $event->get_action() : null;
        $timeduration = $event->get_times()->get_end_time()->getTimestamp() - $event->get_times()->get_start_time()->getTimestamp();

        return (object)$this->from_event_to_assoc_array($event);
    }

    public function from_event_to_assoc_array(event_interface $event) {
        $action = ($event instanceof action_event_interface) ? $event->get_action() : null;
        $timeduration = $event->get_times()->get_end_time()->getTimestamp() - $event->get_times()->get_start_time()->getTimestamp();

        return [
            'id'               => $event->get_id(),
            'name'             => $event->get_name(),
            'description'      => $event->get_description()->get_value(),
            'format'           => $event->get_description()->get_format(),
            'location'         => $event->get_location(),
            'courseid'         => $event->get_course() ? $event->get_course()->get('id') : null,
            'categoryid'       => $event->get_category() ? $event->get_category()->get('id') : null,
            'groupid'          => $event->get_group() ? $event->get_group()->get('id') : null,
            'userid'           => $event->get_user() ? $event->get_user()->get('id') : null,
            'repeatid'         => $event->get_repeats() ? $event->get_repeats()->get_id() : null,
            'modulename'       => $event->get_course_module() ? $event->get_course_module()->get('modname') : null,
            'instance'         => $event->get_course_module() ? $event->get_course_module()->get('instance') : null,
            'eventtype'        => $event->get_type(),
            'timestart'        => $event->get_times()->get_start_time()->getTimestamp(),
            'timeduration'     => $timeduration,
            'timesort'         => $event->get_times()->get_sort_time()->getTimestamp(),
            'visible'          => $event->is_visible() ? 1 : 0,
            'timemodified'     => $event->get_times()->get_modified_time()->getTimestamp(),
            'subscriptionid'   => $event->get_subscription() ? $event->get_subscription()->get('id') : null,
            'actionname'       => $action ? $action->get_name() : null,
            'actionurl'        => $action ? $action->get_url() : null,
            'actionnum'        => $action ? $action->get_item_count() : null,
            'actionactionable' => $action ? $action->is_actionable() : null,
            'sequence' => 1
        ];
    }
}
