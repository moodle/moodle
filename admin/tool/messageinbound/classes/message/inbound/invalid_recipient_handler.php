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
 * A Handler to re-process messages which previously failed sender verification.
 *
 * @package    tool_messageinbound
 * @category   message
 * @copyright  2014 Andrew Nicols
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_messageinbound\message\inbound;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/repository/lib.php');

/**
 * A Handler to re-process messages which previously failed sender verification.
 *
 * This may happen if the user did not use their registerd e-mail address,
 * the verification hash used had expired, or if some erroneous content was
 * introduced into the content hash.
 *
 * @copyright  2014 Andrew Nicols
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class invalid_recipient_handler extends \core\message\inbound\handler {

    /**
     * Do not allow changes to the address validation setting.
     */
    public function can_change_validateaddress() {
        return false;
    }

    /**
     * Return a description for the current handler.
     *
     * @return string
     */
    public function get_description() {
        return get_string('invalid_recipient_handler', 'tool_messageinbound');
    }

    /**
     * Return a name for the current handler.
     * This appears in the admin pages as a human-readable name.
     *
     * @return string
     */
    public function get_name() {
        return get_string('invalid_recipient_handler_name', 'tool_messageinbound');
    }

    /**
     * Process a message received and validated by the Inbound Message processor.
     *
     * @param \stdClass $record The Inbound Message record
     * @param \stdClass $data The message data packet.
     * @return bool Whether the message was successfully processed.
     * @throws \core\message\inbound\processing_failed_exception when the message can not be found.
     */
    public function process_message(\stdClass $record, \stdClass $data) {
        global $DB;

        if (!$maildata = $DB->get_record('messageinbound_messagelist', array('id' => $record->datavalue))) {
            // The message requested couldn't be found. Failing here will alert the user that we failed.
            throw new \core\message\inbound\processing_failed_exception('oldmessagenotfound', 'tool_messageinbound');
        }

        mtrace("=== Request to re-process message {$record->datavalue} from server.");
        mtrace("=== Message-Id:\t{$maildata->messageid}");
        mtrace("=== Recipient:\t{$maildata->address}");

        $manager = new \tool_messageinbound\manager();
        return $manager->process_existing_message($maildata);
    }

}
