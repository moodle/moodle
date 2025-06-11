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
 * @author     Update by David Lowe
 */

class sent_messages_ctrl {
    public function remove_sent_messages($data) {
        global $USER;
        // Authentication.
        require_login();

        $success = "success";
        $failmsg = "The messages have been successfully removed";
        foreach ($data->ids as $id) {

            // Check that the message has not been deleted.
            if (!$message = \block_quickmail\persistents\message::find_or_null($id)) {
                $success = "error";
                $failmsg = "Cannot find this sent message";
            }

            // Check that the user can delete this message.
            if ($message->get('user_id') !== $USER->id) {
                $success = "error";
                $failmsg = "This user cannot delete the sent message(s)";
            }

            $message->mark_as_deleted();
        }

        return array(
            'success' => $success,
            'msg' => $failmsg
        );
    }
}
