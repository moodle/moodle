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

namespace block_quickmail\persistents\concerns;

defined('MOODLE_INTERNAL') || die();

use block_quickmail\persistents\message;
use block_quickmail_string;

trait belongs_to_a_message {
    // Relationships.
    /**
     * Returns the parent message object of this message recipient.
     *
     * @return stdClass
     */
    public function get_message() {
        return message::find_or_null($this->get('message_id'));
    }

    // Validators.
    /**
     * Validate the message ID.
     *
     * @param int $value The value.
     * @return true|string
     */
    protected function validate_message_id($value) {
        if (!$message = message::find_or_null($value)) {
            return block_quickmail_string::get('message_no_record');
        }

        return true;
    }

    // Custom Static Methods.
    /**
     * Creates a new persistent record for the given message with the given array of attributes
     *
     * @param  block_quickmail\persistents\message  $message
     * @param  array  $params  [attr => value]
     * @return object (persistent)
     * @throws dml_missing_record_exception
     */
    public static function create_for_message(message $message, $params = []) {
        // Merge the message id into the creation parameters.
        $params = array_merge(['message_id' => $message->get('id')], $params);

        return self::create_new($params);
    }

}
