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
 * Contains an observer class containing methods for handling events.
 *
 * @package    message_email
 * @copyright  2019 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace message_email;

defined('MOODLE_INTERNAL') || die();

/**
 * Observer class containing methods for handling events.
 *
 * @package    message_email
 * @copyright  2019 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class event_observers {

    /**
     * Message viewed event handler.
     *
     * @param \core\event\message_viewed $event The message viewed event.
     */
    public static function message_viewed(\core\event\message_viewed $event) {
        global $DB;

        $userid = $event->userid;
        $messageid = $event->other['messageid'];

        $DB->delete_records('message_email_messages', ['useridto' => $userid, 'messageid' => $messageid]);
    }
}
