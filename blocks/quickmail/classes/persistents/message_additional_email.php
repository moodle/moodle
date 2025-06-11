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

namespace block_quickmail\persistents;

defined('MOODLE_INTERNAL') || die();

use core_user;
use lang_string;
use block_quickmail\persistents\concerns\enhanced_persistent;
use block_quickmail\persistents\concerns\belongs_to_a_message;
use block_quickmail\persistents\message;

class message_additional_email extends \block_quickmail\persistents\persistent {

    use enhanced_persistent,
        belongs_to_a_message;

    /** Table name for the persistent. */
    const TABLE = 'block_quickmail_msg_ad_email';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'message_id' => [
                'type' => PARAM_INT,
            ],
            'email' => [
                'type' => PARAM_EMAIL,
            ],
            'sent_at' => [
                'type' => PARAM_INT,
                'default' => 0
            ],
        ];
    }

    // Getters.
    /**
     * Reports whether or not this additional email has been messaged
     *
     * @return bool
     */
    public function has_been_sent_to() {
        return (bool) $this->get('sent_at');
    }

    // Custom Methods.
    /**
     * Mark this additional email as have being sent to successfully
     *
     * @return void
     */
    public function mark_as_sent() {
        $this->set('sent_at', time());
        $this->update();
    }

    // Custom Static Methods.
    /**
     * Deletes all additional emails for this message
     *
     * @param  message $message
     * @return void
     */
    public static function clear_all_for_message(message $message) {
        global $DB;

        // Delete all recipients belonging to this message.
        $DB->delete_records('block_quickmail_msg_ad_email', ['message_id' => $message->get('id')]);
    }

}
