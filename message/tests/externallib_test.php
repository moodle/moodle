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
 * External message functions unit tests
 *
 * @package    core_message
 * @category   external
 * @copyright  2012 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');
require_once($CFG->dirroot . '/message/externallib.php');

class core_message_external_testcase extends externallib_advanced_testcase {

    /**
     * Test send_instant_messages
     */
    public function test_send_instant_messages() {

        global $DB, $USER, $CFG;

        $this->resetAfterTest(true);

        // Turn off all message processors (so nothing is really sent)
        require_once($CFG->dirroot . '/message/lib.php');
        $messageprocessors = get_message_processors();
        foreach($messageprocessors as $messageprocessor) {
            $messageprocessor->enabled = 0;
            $DB->update_record('message_processors', $messageprocessor);
        }

        // Set the required capabilities by the external function
        $contextid = context_system::instance()->id;
        $roleid = $this->assignUserCapability('moodle/site:sendmessage', $contextid);

        $user1 = self::getDataGenerator()->create_user();

        // Create test message data.
        $message1 = array();
        $message1['touserid'] = $user1->id;
        $message1['text'] = 'the message.';
        $message1['clientmsgid'] = 4;
        $messages = array($message1);

        $sentmessages = core_message_external::send_instant_messages($messages);

        $themessage = $DB->get_record('message', array('id' => $sentmessages[0]['msgid']));

        // Confirm that the message was inserted correctly.
        $this->assertEquals($themessage->useridfrom, $USER->id);
        $this->assertEquals($themessage->useridto, $message1['touserid']);
        $this->assertEquals($themessage->smallmessage, $message1['text']);
        $this->assertEquals($sentmessages[0]['clientmsgid'], $message1['clientmsgid']);
    }
}