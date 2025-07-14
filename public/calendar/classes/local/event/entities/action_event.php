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
 * Calendar action event class.
 *
 * @package    core_calendar
 * @copyright  2017 Cameron Ball <cameron@cameron1729.xyz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_calendar\local\event\entities;

defined('MOODLE_INTERNAL') || die();

use core_calendar\local\event\factories\action_factory_interface;

/**
 * Class representing an actionable event.
 *
 * An actionable event can be thought of as an embellished event. That is,
 * it does everything a regular event does, but has some extra information
 * attached to it. For example, the URL a user needs to visit to complete
 * an action, the number of actionable items, etc.
 *
 * @copyright 2017 Cameron Ball <cameron@cameron1729.xyz>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class action_event implements action_event_interface {
    /**
     * @var event_interface $event The event to delegate to.
     */
    protected $event;

    /**
     * @var action_interface $action The action associated with this event.
     */
    protected $action;

    /**
     * @var proxy_interface $category Category for this event.
     */
    protected $category;

    /**
     * Constructor.
     *
     * @param event_interface  $event  The event to delegate to.
     * @param action_interface $action The action associated with this event.
     */
    public function __construct(event_interface $event, action_interface $action) {
        $this->event = $event;
        $this->action = $action;
    }

    public function get_id() {
        return $this->event->get_id();
    }

    public function get_name() {
        return $this->event->get_name();
    }

    public function get_description() {
        return $this->event->get_description();
    }

    public function get_location() {
        return $this->event->get_location();
    }

    public function get_category() {
        return $this->event->get_category();
    }

    public function get_course() {
        return $this->event->get_course();
    }

    public function get_course_module() {
        return $this->event->get_course_module();
    }

    public function get_group() {
        return $this->event->get_group();
    }

    public function get_user() {
        return $this->event->get_user();
    }

    public function get_type() {
        return $this->event->get_type();
    }

    public function get_times() {
        return $this->event->get_times();
    }

    public function get_repeats() {
        return $this->event->get_repeats();
    }

    public function get_subscription() {
        return $this->event->get_subscription();
    }

    public function is_visible() {
        return $this->event->is_visible();
    }

    public function get_action() {
        return $this->action;
    }

    /**
     * Event component
     * @return string
     */
    public function get_component() {
        return $this->event->get_component();
    }
}
