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

use block_quickmail_plugin;
use block_quickmail_string;
use block_quickmail\persistents\event_notification;

class notification_model_helper {

    /**
     * Returns a fully namespaced notification_model class name from a notification type and a model key
     *
     * @param  string  $notificationtype  reminder|event
     * @param  string  $modelkey  ex: 'course-non-participation'
     * @return string
     */
    public static function get_full_model_class_name($notificationtype, $modelkey) {
        return 'block_quickmail\notifier\models\\' . $notificationtype . '\\' . self::get_model_class_name($modelkey);
    }

    /**
     * Returns a notification model class name given a notification_type_interface's model key
     *
     * @param  string  $modelkey
     * @return string
     */
    public static function get_model_class_name($modelkey) {
        return str_replace('-', '_', $modelkey) . '_model';
    }

    /**
     * Returns an array of "notification model" keys available for the given notification type
     *
     * @param  string  $notificationtype  reminder|event
     * @return array
     */
    public static function get_available_model_keys_by_type($notificationtype) {
        return block_quickmail_plugin::get_model_notification_types($notificationtype);
    }

    /**
     * Returns a model's 'object type' given a notification type and key
     *
     * @param  string  $notificationtype  reminder|event
     * @param  string  $modelkey
     * @return string
     */
    public static function get_object_type_for_model($notificationtype, $modelkey) {
        $modelclass = self::get_full_model_class_name($notificationtype, $modelkey);

        return $modelclass::$objecttype;
    }

    /**
     * Returns a model's required "condition keys" given a notification type and key
     *
     * @param  string  $notificationtype  reminder|event
     * @param  string  $modelkey
     * @return array
     */
    public static function get_condition_keys_for_model($notificationtype, $modelkey) {
        $modelclass = self::get_full_model_class_name($notificationtype, $modelkey);

        return $modelclass::$conditionkeys;
    }

    /**
     * Reports whether or not a model requires an object selection other that 'course' or 'user', given a notification type and key
     *
     * @param  string  $notificationtype  reminder|event
     * @param  string  $modelkey
     * @return bool
     */
    public static function model_requires_object($notificationtype, $modelkey) {
        $objecttype = self::get_object_type_for_model($notificationtype, $modelkey);

        return ! in_array($objecttype, ['user', 'course']);
    }

    /**
     * Reports whether or not a model requires condition selections, given a notification type and key
     *
     * @param  string  $notificationtype  reminder|event
     * @param  string  $modelkey
     * @return bool
     */
    public static function model_requires_conditions($notificationtype, $modelkey) {
        $conditionkeys = self::get_condition_keys_for_model($notificationtype, $modelkey);

        return (bool) count($conditionkeys);
    }

    /**
     * Returns a lang string key for a model's condition summary, given a notification type and key
     *
     * @param  string  $notificationtype  reminder|event
     * @param  string  $modelkey
     * @return string
     */
    public static function get_condition_summary_lang_string($notificationtype, $modelkey) {
        return 'condition_summary_' . $notificationtype . '_' . $modelkey;
    }

    /**
     * Reports whether or not the given model key is a "one time event"
     *
     * @param  string  $modelname  ex: course_entered
     * @return bool
     */
    public static function model_is_one_time_event($modelname) {
        return in_array(str_replace('_', '-', $modelname), event_notification::$onetimeevents);
    }

}
