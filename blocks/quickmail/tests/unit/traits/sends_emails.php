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

defined('MOODLE_INTERNAL') || die();

// Email helpers.
trait sends_emails {

    public function open_email_sink() {
        unset_config('noemailever');

        $sink = $this->redirectEmails();

        return $sink;
    }

    public function close_email_sink($sink) {
        $sink->close();
    }

    public function email_sink_email_count($sink) {
        return count($sink->get_messages());
    }

    // Subject.
    // From.
    // To.
    public function email_in_sink_attr($sink, $index, $attr) {
        $messages = $sink->get_messages();

        $message = $messages[$index - 1];

        return $message->$attr;
    }

    public function email_in_sink_body_contains($sink, $index, $bodytext) {
        $messages = $sink->get_messages();

        $message = $messages[$index - 1];

        $body = $message->body;

        return (bool) strpos($body, format_text_email($bodytext, 1));
    }


}
