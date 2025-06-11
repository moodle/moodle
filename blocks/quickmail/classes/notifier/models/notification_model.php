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
 * @package    block_quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_quickmail\notifier\models;

defined('MOODLE_INTERNAL') || die();

use block_quickmail\notifier\models\interfaces\notification_model_interface;
use block_quickmail\persistents\interfaces\notification_type_interface;
use block_quickmail\notifier\notification_condition;
use block_quickmail\notifier\models\notification_model_helper;

abstract class notification_model implements notification_model_interface {

    public static $objecttype = '';
    public static $conditionkeys = [];
    public $notificationtypeinterface;
    public $notification;
    public $condition;

    public function __construct(notification_type_interface $notificationtypeinterface) {
        $this->notification_type_interface = $notificationtypeinterface;
        $this->notification = $notificationtypeinterface->get_notification();
        $this->condition = notification_condition::from_condition_string($this->notification->get('conditions'));
    }

    /**
     * Instantiates and returns a notification_model_interface given a notification_type_interface
     *
     * @param  notification_type_interface  $notificationtypeinterface
     * @return reminder_notification_model_interface
     */
    public static function make(notification_type_interface $notificationtypeinterface) {
        $class = static::get_notification_type_interface_model_class_name($notificationtypeinterface);

        return new $class($notificationtypeinterface);
    }

    /**
     * Returns a fully namespaced notification_model class name from the given notification_type_interface
     *
     * @param  notification_type_interface  $notificationtypeinterface
     * @return string
     */
    public static function get_notification_type_interface_model_class_name($notificationtypeinterface) {
        return notification_model_helper::get_full_model_class_name(
            $notificationtypeinterface::$notificationtypekey, $notificationtypeinterface->get('model'));
    }

    /**
     * Returns the type of object in which this notification model uses
     *
     * @return string
     */
    public function get_object_type() {
        return static::$objecttype;
    }

    // Getters.
    /**
     * Returns this notification_model's notification's course id
     *
     * @return int
     */
    public function get_course_id() {
        return (int) $this->notification->get('course_id');
    }

}
