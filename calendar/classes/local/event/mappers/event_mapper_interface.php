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
 * Event mapper interface.
 *
 * @package    core_calendar
 * @copyright  2017 Cameron Ball <cameron@cameron1729.xyz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_calendar\local\event\mappers;

defined('MOODLE_INTERNAL') || die();

use core_calendar\event;
use core_calendar\local\event\entities\event_interface;

/**
 * Interface for an event mapper class
 *
 * @copyright  2017 Cameron Ball <cameron@cameron1729.xyz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface event_mapper_interface {
    /**
     * Map a legacy event to an event.
     *
     * @param \calendar_event $event The legacy event.
     * @return event_interface The mapped event.
     */
    public function from_legacy_event_to_event(\calendar_event $event);

    /**
     * Map an event to a legacy event.
     *
     * @param event_interface $event The legacy event.
     * @return \calendar_event The mapped legacy event.
     */
    public function from_event_to_legacy_event(event_interface $event);

    /**
     * Map an event to a stdClass
     *
     * @param event_interface $event The legacy event.
     * @return \stdClass The mapped stdClass.
     */
    public function from_event_to_stdclass(event_interface $event);

    /**
     * Map an event to an associative array.
     *
     * @param event_interface $event The legacy event.
     * @return array The mapped legacy event array.
     */
    public function from_event_to_assoc_array(event_interface $event);
}
