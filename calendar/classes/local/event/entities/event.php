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
 * Calendar event class.
 *
 * @package    core_calendar
 * @copyright  2017 Cameron Ball <cameron@cameron1729.xyz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_calendar\local\event\entities;

defined('MOODLE_INTERNAL') || die();

use core_calendar\local\event\proxies\proxy_interface;
use core_calendar\local\event\value_objects\description_interface;
use core_calendar\local\event\value_objects\times_interface;

/**
 * Class representing a calendar event.
 *
 * @copyright 2017 Cameron Ball <cameron@cameron1729.xyz>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class event implements event_interface {
    /**
     * @var int $id The event's id in the database.
     */
    protected $id;

    /**
     * @var string $name The name of this event.
     */
    protected $name;

    /**
     * @var description_interface $description Description for this event.
     */
    protected $description;

    /**
     * @var string $location Location of this event.
     */
    protected $location;

    /**
     * @var proxy_interface $category Category for this event.
     */
    protected $category;

    /**
     * @var proxy_interface $course Course for this event.
     */
    protected $course;

    /**
     * @var proxy_interface $group Group for this event.
     */
    protected $group;

    /**
     * @var proxy_interface $user User for this event.
     */
    protected $user;

    /**
     * @var event_collection_interface $repeats Collection of repeat events.
     */
    protected $repeats;

    /**
     * @var proxy_interface $coursemodule The course module that created this event.
     */
    protected $coursemodule;

    /**
     * @var string type The type of this event.
     */
    protected $type;

    /**
     * @var times_interface $times The times for this event.
     */
    protected $times;

    /**
     * @var bool $visible The visibility of this event.
     */
    protected $visible;

    /**
     * @var proxy_interface $subscription Subscription for this event.
     */
    protected $subscription;

    /**
     * Constructor.
     *
     * @param int                        $id             The event's ID in the database.
     * @param string                     $name           The event's name.
     * @param description_interface      $description    The event's description.
     * @param proxy_interface            $category       The category associated with the event.
     * @param proxy_interface            $course         The course associated with the event.
     * @param proxy_interface            $group          The group associated with the event.
     * @param proxy_interface            $user           The user associated with the event.
     * @param event_collection_interface $repeats        Collection of repeat events.
     * @param proxy_interface            $coursemodule   The course module that created the event.
     * @param string                     $type           The event's type.
     * @param times_interface            $times          The times associated with the event.
     * @param bool                       $visible        The event's visibility. True for visible, false for invisible.
     * @param proxy_interface            $subscription   The event's subscription.
     * @param string                     $location       The event's location.
     */
    public function __construct(
        $id,
        $name,
        description_interface $description,
        proxy_interface $category = null,
        proxy_interface $course = null,
        proxy_interface $group = null,
        proxy_interface $user = null,
        event_collection_interface $repeats = null,
        proxy_interface $coursemodule = null,
        $type,
        times_interface $times,
        $visible,
        proxy_interface $subscription = null,
        $location = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->location = $location;
        $this->category = $category;
        $this->course = $course;
        $this->group = $group;
        $this->user = $user;
        $this->repeats = $repeats;
        $this->coursemodule = $coursemodule;
        $this->type = $type;
        $this->times = $times;
        $this->visible = $visible;
        $this->subscription = $subscription;
    }

    public function get_id() {
        return $this->id;
    }

    public function get_name() {
        return $this->name;
    }

    public function get_description() {
        return $this->description;
    }

    public function get_location() {
        return $this->location;
    }

    public function get_category() {
        return $this->category;
    }

    public function get_course() {
        return $this->course;
    }

    public function get_course_module() {
        return $this->coursemodule;
    }

    public function get_group() {
        return $this->group;
    }

    public function get_user() {
        return $this->user;
    }

    public function get_type() {
        return $this->type;
    }

    public function get_times() {
        return $this->times;
    }

    public function get_repeats() {
        return $this->repeats;
    }

    public function get_subscription() {
        return $this->subscription;
    }

    public function is_visible() {
        return $this->visible;
    }
}
