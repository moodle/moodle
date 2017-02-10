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
 * Event factory interface.
 *
 * @package    core_calendar
 * @copyright  2017 Cameron Ball <cameron@cameron1729.xyz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_calendar\local\interfaces;

defined('MOODLE_INTERNAL') || die();

/**
 * Interface for an event factory class.
 *
 * @copyright  2017 Cameron Ball <cameron@cameron1729.xyz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface event_factory_interface {
    /**
     * Creates an instance of an event.
     *
     * @param int    $id                     The event's ID from the database.
     * @param string $name                   The event's name.
     * @param string $descriptionvalue       The event's description value.
     * @param string $descriptionformat      The event's description format.
     * @param int    $courseid               The course ID associated with the event.
     * @param int    $groupid                The group ID associated with the event.
     * @param int    $userid                 The user ID associated with the event.
     * @param int    $repeatid               ID of an event this event is a repeat of.
     * @param string $modulename             The name of the module that created this event.
     * @param int    $moduleinstance         The instance ID of the module that created this event.
     * @param string $type                   The type of the event.
     * @param int    $timestart              Timestamp for when the event starts.
     * @param int    $timeduration           The duration of the event in seconds.
     * @param int    $timemodified           Timestamp of when the event was last modified.
     * @param int    $timesort               Timestamp by which to sort events.
     * @param bool   $visible                The event's visibility. True for visible, false for invisible.
     * @param int    $subscriptionid         The subscription ID of the event.
     * @return \core_calendar\local\interfaces\event_interface
     */
    public function create_instance(
        $id,
        $name,
        $descriptionvalue,
        $descriptionformat,
        $courseid,
        $groupid,
        $userid,
        $repeatid,
        $modulename,
        $moduleinstance,
        $type,
        $timestart,
        $timeduration,
        $timemodified,
        $timesort,
        $visible,
        $subscriptionid
    );
}
