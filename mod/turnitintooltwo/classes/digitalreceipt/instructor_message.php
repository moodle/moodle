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

defined('MOODLE_INTERNAL') || die();

class instructor_message {

    /**
     * Build a modified receipt to send to instructors upon a submission being made.
     * This message must preserve the anonymity of a submission.
     *
     * @param array $input - used to build message
     * @return string
     */
    public function build_instructor_message($input) {
        $message = new stdClass();
        $message->submission_title = $input['submission_title'];
        $message->assignment_name = $input['assignment_name'];
        if ( isset($input['assignment_part']) ) {
            $message->assignment_part = ": " . $input['assignment_part'];
        } else {
            $message->assignment_part = "";
        }
        $message->course_fullname = $input['course_fullname'];
        $message->submission_date = $input['submission_date'];
        $message->submission_id = $input['submission_id'];

        return format_string(get_string('receipt_instructor_copy', 'turnitintooltwo', $message));
    }

    /**
     * Send instructor message to instructors on course.
     *
     * @param array $instructors
     * @param string $message
     * @return void
     */
    public function send_instructor_message($instructors, $message, $courseid) {
        global $CFG;

        $subject = get_string('receipt_instructor_copy_subject', 'turnitintooltwo');

        $eventdata = new \core\message\message();

        $eventdata->component         = 'mod_turnitintooltwo';
        $eventdata->name              = 'notify_instructor_of_submission'; // This is the message name from messages.php.
        $eventdata->userfrom          = \core_user::get_noreply_user();
        $eventdata->subject           = $subject;
        $eventdata->fullmessage       = $message;
        $eventdata->fullmessageformat = FORMAT_HTML;
        $eventdata->fullmessagehtml   = $message;
        $eventdata->smallmessage      = '';
        $eventdata->notification      = 1; // This is only set to 0 for personal messages between users.

        if ($CFG->branch >= 32) {
            $eventdata->courseid = $courseid;
        }

        foreach ($instructors as $instructor) {

            $eventdata->userto = $instructor->id;
            message_send($eventdata);
        }
        unset($instructor);
    }
}
