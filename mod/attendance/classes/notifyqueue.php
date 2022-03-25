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
 * Notify queue
 *
 * @package   mod_attendance
 * @copyright 2015 Antonio Carlos Mariani <antonio.c.mariani@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Notify Queue class
 *
 * @copyright 2015 Antonio Carlos Mariani <antonio.c.mariani@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_attendance_notifyqueue {

    /**
     * Show (print) the pending messages and clear them
     */
    public static function show() {
        global $SESSION, $OUTPUT;

        if (isset($SESSION->mod_attendance_notifyqueue)) {
            foreach ($SESSION->mod_attendance_notifyqueue as $message) {
                echo $OUTPUT->notification($message->message, 'notify'.$message->type);
            }
            unset($SESSION->mod_attendance_notifyqueue);
        }
    }

    /**
     * Queue a text as a problem message to be shown latter by show() method
     *
     * @param string $message a text with a message
     */
    public static function notify_problem($message) {
        self::queue_message($message, \core\output\notification::NOTIFY_ERROR);
    }

    /**
     * Queue a text as a simple message to be shown latter by show() method
     *
     * @param string $message a text with a message
     */
    public static function notify_message($message) {
        self::queue_message($message, \core\output\notification::NOTIFY_INFO);
    }

    /**
     * queue a text as a suceess message to be shown latter by show() method
     *
     * @param string $message a text with a message
     */
    public static function notify_success($message) {
        self::queue_message($message, \core\output\notification::NOTIFY_SUCCESS);
    }

    /**
     * queue a text as a message of some type to be shown latter by show() method
     *
     * @param string $message a text with a message
     * @param string $messagetype one of the \core\output\notification messages ('message', 'suceess' or 'problem')
     */
    private static function queue_message($message, $messagetype=\core\output\notification::NOTIFY_INFO) {
        global $SESSION;

        if (!isset($SESSION->mod_attendance_notifyqueue)) {
            $SESSION->mod_attendance_notifyqueue = array();
        }
        $m = new stdclass();
        $m->type = $messagetype;
        $m->message = $message;
        $SESSION->mod_attendance_notifyqueue[] = $m;
    }
}
