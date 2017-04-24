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
 * Calendar event interface.
 *
 * @package    core_calendar
 * @copyright  2017 Cameron Ball <cameron@cameron1729.xyz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_calendar\local\event\entities;

use core_calendar\local\event\proxies\proxy_interface;

defined('MOODLE_INTERNAL') || die();

/**
 * Interface for an event class.
 *
 * @copyright  2017 Cameron Ball <cameron@cameron1729.xyz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface event_interface {
    /**
     * Get the event's ID.
     *
     * @return integer
     */
    public function get_id();

    /**
     * Get the event's name.
     *
     * @return string
     */
    public function get_name();

    /**
     * Get the event's description.
     *
     * @return description_interface
     */
    public function get_description();

    /**
     * Get the course object associated with the event.
     *
     * @return proxy_interface
     */
    public function get_course();

    /**
     * Get the course module object that created the event.
     *
     * @return proxy_interface
     */
    public function get_course_module();

    /**
     * Get the group object associated with the event.
     *
     * @return proxy_interface
     */
    public function get_group();

    /**
     * Get the user object associated with the event.
     *
     * @return proxy_interface
     */
    public function get_user();

    /**
     * Get the event's type.
     *
     * @return string
     */
    public function get_type();

    /**
     * Get the times associated with the event.
     *
     * @return times_interface
     */
    public function get_times();

    /**
     * Get repeats of this event.
     *
     * @return event_collection_interface
     */
    public function get_repeats();

    /**
     * Get the event's subscription.
     *
     * @return proxy_interface
     */
    public function get_subscription();

    /**
     * Get the event's visibility.
     *
     * @return bool true if the event is visible, false otherwise
     */
    public function is_visible();
}
