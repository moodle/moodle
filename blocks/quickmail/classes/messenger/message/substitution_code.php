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

namespace block_quickmail\messenger\message;

defined('MOODLE_INTERNAL') || die();

use block_quickmail\persistents\message;

class substitution_code {

    // Code class => [codes].
    public static $codes = [
        'user' => [
            'firstname',
            'lastname',
            'fullname',
            'middlename',
            'email',
            'alternatename',
        ],
        'course' => [
            'coursefullname',
            'courseshortname',
            'courseidnumber',
            'coursesummary',
            'coursestartdate',
            'courseenddate',
            'courselink',
            'courselastaccess',
            'studentstartdate',
            'studentenddate',
        ],
        'activity' => [
            'activityname',
            'activityduedate',
            'activitylink',
            'activitygradelink',
        ],
    ];

    /**
     * Returns an array of codes for the given code class
     *
     * If a string is passed, will return all codes for that code class
     * If an array is passed, will return all codes for those code classes
     * If null is passed (default), will return all codes
     *
     * @param  mixed  $class  string, array, defaults to null
     * @return array
     */
    public static function get($class = null) {
        if (is_string($class)) {
            return self::$codes[$class];
        }

        $codeclasses = is_null($class)
            ? array_keys(self::$codes)
            : $class;

        return self::get_for_classes(array_unique($codeclasses));
    }

    /**
     * Returns an array of substitution code classes which are used in a given message
     *
     * @param  message $message
     * @return array
     */
    public static function get_code_classes_from_message(message $message) {
        // User class is always included.
        $codes = ['user'];

        // If this is a course-based message, add the course class.
        if ($message->get_message_scope() == 'compose') {
            $codes[] = 'course';
        }

        if ($notificationtypeinterface = $message->get_notification_type_interface()) {
            $codes[] = $notificationtypeinterface->get_notification_model()->get_object_type();
        }

        return array_unique($codes);
    }

    /**
     * Returns an array of codes for the given code classes
     *
     * @param  array  $codeclasses  user|course|activity
     * @return array
     */
    private static function get_for_classes($codeclasses) {
        $codes = [];

        foreach (array_keys(self::$codes) as $code) {
            if (in_array($code, $codeclasses)) {
                $codes = array_merge($codes, self::$codes[$code]);
            }
        }

        return $codes;
    }

    /**
     * Returns the delimiter that should be typed in front of the substution code
     * @TODO: make this configurable!!
     *
     * @return string
     */
    public static function first_delimiter() {
        return '[:';
    }

    /**
     * Returns the delimiter that should be typed behind the substution code
     * @TODO: make this configurable!!
     *
     * @return string
     */
    public static function last_delimiter() {
        return ':]';
    }

}
