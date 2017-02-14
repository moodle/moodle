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
use core_calendar\local\interfaces\event_factory_interface;
use core_calendar\local\interfaces\event_interface;
use core_calendar\local\interfaces\event_mapper_interface;

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

    public function from_legacy_event_to_event(event $legacyevent) {
        $coalesce = function($property) use ($legacyevent) {
            return property_exists($legacyevent, $property) ? $legacyevent->{$property} : null;
        };

        return $this->factory->create_instance(
            (object)[
                $coalesce('id'),
                $coalesce('name'),
                $coalesce('description'),
                $coalesce('format'),
                $coalesce('courseid'),
                $coalesce('groupid'),
                $coalesce('userid'),
                $coalesce('repeatid'),
                $coalesce('modulename'),
                $coalesce('instance'),
                $coalesce('type'),
                $coalesce('timestart'),
                $coalesce('timeduration'),
                $coalesce('timemodified'),
                $coalesce('timesort'),
                $coalesce('visible'),
                $coalesce('subscriptioni')
            ]
        );
    }

    public function from_event_to_legacy_event(event_interface $event) {
        $timeduration = $event->get_times()->get_end_time()->getTimestamp() - $event->get_times()->get_start_time()->getTimestamp();

        return new event((object) [
            'id'             => $event->get_id(),
            'name'           => $event->get_name(),
            'description'    => $event->get_description()->get_value(),
            'format'         => $event->get_description()->get_format(),
            'courseid'       => $event->get_course() ? $event->get_course()->get_id() : null,
            'groupid'        => $event->get_group() ? $event->get_group()->get_id() : null,
            'userid'         => $event->get_user() ? $event->get_user()->get_id() : null,
            'repeatid'       => $event->get_repeats()->get_id(),
            'modulename'     => $event->get_course_module()->get('modname'),
            'instance'       => $event->get_course_module()->get('instance'),
            'eventtype'      => $event->get_type(),
            'timestart'      => $event->get_times()->get_start_time()->getTimestamp(),
            'timeduration'   => $timeduration,
            'timesort'       => $event->get_times()->get_sort_time()->getTimestamp(),
            'visible'        => $event->is_visible() ? 1 : 0,
            'timemodified'   => $event->get_times()->get_modified_time()->getTimestamp(),
            'subscriptionid' => $event->get_subscription() ? $event->get_subscription()->get_id() : null
        ]);
    }
}
