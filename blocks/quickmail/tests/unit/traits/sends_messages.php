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

// Message helpers.
trait sends_messages {

    public function open_message_sink() {
        $this->preventResetByRollback();

        $sink = $this->redirectMessages();

        return $sink;
    }

    public function close_message_sink($sink) {
        $sink->close();
    }

    public function message_sink_message_count($sink) {
        return count($sink->get_messages());
    }

    public function dispatch_queued_messages() {
        // 2020-10-30, Segun Babalola
        // For some reason, messages created from notifications remain sat in the DB
        // this method flushes those messages so they get captured by the sink in tests
        $messages = \block_quickmail\repos\queued_repo::get_all_messages_to_send();

        foreach ($messages as $msg) {
            $msg->send();
        }
    }
}
