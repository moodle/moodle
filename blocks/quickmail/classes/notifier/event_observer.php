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

namespace block_quickmail\notifier;

defined('MOODLE_INTERNAL') || die();

use block_quickmail\notifier\event_notification_handler;
use block_quickmail_emailer;

/*
 * This class is responsible for observing native moodle events, and then
 * calling the corresponding event notification handler method using
 * those event details
 */
class event_observer {

    public static function course_viewed(\core\event\course_viewed $event) {
        // User who viewed course.
        $userid = $event->userid;

        // Course that was viewed.
        $courseid = $event->courseid;

        event_notification_handler::course_entered($userid, $courseid);
    }

    // For testing….
    /**
     * Send a test email from an arbitrary user to the given user_id
     *
     * @param  int  $userid
     * @return void
     */
    private static function send_test_email($userid) {
        global $DB;
        $touser = $DB->get_record('user', ['id' => $userid]);
        $fromuser = $DB->get_record('user', ['id' => '25']);
        $emailer = new block_quickmail_emailer($fromuser, 'subject', 'one fine body');
        $emailer->to_user($touser);
        $emailer->reply_to($fromuser->email, fullname($fromuser));
        $emailer->send();
    }

}
