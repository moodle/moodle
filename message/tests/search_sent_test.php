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
 * Sent message global search unit tests.
 *
 * @package     core
 * @copyright   2016 Devang Gaur
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/search/tests/fixtures/testable_core_search.php');

/**
 * Provides the unit tests for sent message global search.
 *
 * @package     core
 * @copyright   2016 Devang Gaur
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class message_sent_search_testcase extends advanced_testcase {

    /**
     * @var string Area id
     */
    protected $messagesentareaid = null;

    /**
     * Setting up the test environment
     * @return void
     */
    public function setUp() {
        $this->resetAfterTest(true);
        set_config('enableglobalsearch', true);

        $this->messagesentareaid = \core_search\manager::generate_areaid('core_message', 'message_sent');

        // Set \core_search::instance to the mock_search_engine as we don't require the search engine to be working to test this.
        $search = testable_core_search::instance();
    }

    /**
     * Indexing messages contents.
     *
     * @return void
     */
    public function test_message_sent_indexing() {

        // Returns the instance as long as the area is supported.
        $searcharea = \core_search\manager::get_search_area($this->messagesentareaid);
        $this->assertInstanceOf('\core_message\search\message_sent', $searcharea);

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        $this->preventResetByRollback();
        $sink = $this->redirectMessages();

        $message = new \core\message\message();
        $message->courseid = SITEID;
        $message->userfrom = $user1;
        $message->userto = $user2;
        $message->subject = "Test Subject";
        $message->smallmessage = "Test small messsage";
        $message->fullmessage = "Test full messsage";
        $message->fullmessageformat = 0;
        $message->fullmessagehtml = null;
        $message->notification = 0;
        $message->component = 'moodle';
        $message->name = "instantmessage";

        message_send($message);

        $messages = $sink->get_messages();

        $this->assertEquals(1, count($messages));

        // All records.
        $recordset = $searcharea->get_recordset_by_timestamp(0);
        $this->assertTrue($recordset->valid());
        $nrecords = 0;
        foreach ($recordset as $record) {
            $this->assertInstanceOf('stdClass', $record);
            $doc = $searcharea->get_document($record);
            $this->assertInstanceOf('\core_search\document', $doc);
            $nrecords++;
        }
        // If there would be an error/failure in the foreach above the recordset would be closed on shutdown.
        $recordset->close();
        $this->assertEquals(1, $nrecords);

        // The +2 is to prevent race conditions.
        $recordset = $searcharea->get_recordset_by_timestamp(time() + 2);

        // No new records.
        $this->assertFalse($recordset->valid());
        $recordset->close();
    }

    /**
     * Indexing messages, with restricted contexts.
     */
    public function test_message_sent_indexing_contexts() {
        global $SITE;

        $searcharea = \core_search\manager::get_search_area($this->messagesentareaid);

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        $this->preventResetByRollback();
        $sink = $this->redirectMessages();

        // Send first message.
        $message = new \core\message\message();
        $message->courseid = SITEID;
        $message->userfrom = $user1;
        $message->userto = $user2;
        $message->subject = 'Test1';
        $message->smallmessage = 'Test small messsage';
        $message->fullmessage = 'Test full messsage';
        $message->fullmessageformat = 0;
        $message->fullmessagehtml = null;
        $message->notification = 0;
        $message->component = 'moodle';
        $message->name = 'instantmessage';
        message_send($message);

        // Ensure that ordering by timestamp will return in consistent order.
        $this->waitForSecond();

        // Send second message in opposite direction.
        $message = new \core\message\message();
        $message->courseid = SITEID;
        $message->userfrom = $user2;
        $message->userto = $user1;
        $message->subject = 'Test2';
        $message->smallmessage = 'Test small messsage';
        $message->fullmessage = 'Test full messsage';
        $message->fullmessageformat = 0;
        $message->fullmessagehtml = null;
        $message->notification = 0;
        $message->component = 'moodle';
        $message->name = 'instantmessage';
        message_send($message);

        // Test function with null context and system context (same).
        $rs = $searcharea->get_document_recordset(0, null);
        $this->assertEquals(['Test1', 'Test2'], self::recordset_to_subjects($rs));
        $rs = $searcharea->get_document_recordset(0, context_system::instance());
        $this->assertEquals(['Test1', 'Test2'], self::recordset_to_subjects($rs));

        // Test with user context for each user.
        $rs = $searcharea->get_document_recordset(0, \context_user::instance($user1->id));
        $this->assertEquals(['Test1'], self::recordset_to_subjects($rs));
        $rs = $searcharea->get_document_recordset(0, \context_user::instance($user2->id));
        $this->assertEquals(['Test2'], self::recordset_to_subjects($rs));

        // Test with a course context (should return null).
        $this->assertNull($searcharea->get_document_recordset(0,
                context_course::instance($SITE->id)));
    }

    /**
     * Utility function to convert recordset to array of message subjects for testing.
     *
     * @param moodle_recordset $rs Recordset to convert (and close)
     * @return array Array of IDs from records indexed by number (0, 1, 2, ...)
     */
    public static function recordset_to_subjects(moodle_recordset $rs) {
        $results = [];
        foreach ($rs as $rec) {
            $results[] = $rec->subject;
        }
        $rs->close();
        return $results;
    }

    /**
     * Document contents.
     *
     * @return void
     */
    public function test_message_sent_document() {

        // Returns the instance as long as the area is supported.
        $searcharea = \core_search\manager::get_search_area($this->messagesentareaid);
        $this->assertInstanceOf('\core_message\search\message_sent', $searcharea);

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        $this->preventResetByRollback();
        $sink = $this->redirectMessages();

        $message = new \core\message\message();
        $message->courseid = SITEID;
        $message->userfrom = $user1;
        $message->userto = $user2;
        $message->subject = "Test Subject";
        $message->smallmessage = "Test small messsage";
        $message->fullmessage = "Test full messsage";
        $message->fullmessageformat = 0;
        $message->fullmessagehtml = null;
        $message->notification = 0;
        $message->component = "moodle";
        $message->name = "instantmessage";

        message_send($message);

        $messages = $sink->get_messages();
        $message = $messages[0];

        $doc = $searcharea->get_document($message);
        $this->assertInstanceOf('\core_search\document', $doc);
        $this->assertEquals($message->id, $doc->get('itemid'));
        $this->assertEquals($this->messagesentareaid . '-' . $message->id, $doc->get('id'));
        $this->assertEquals(SITEID, $doc->get('courseid'));
        $this->assertEquals($message->useridfrom, $doc->get('owneruserid'));
        $this->assertEquals($message->useridto, $doc->get('userid'));
        $this->assertEquals(content_to_text($message->subject, false), $doc->get('title'));
        $this->assertEquals(content_to_text($message->smallmessage, false), $doc->get('content'));
    }

    /**
     * Document accesses.
     *
     * @return void
     */
    public function test_message_sent_access() {
        global $CFG;

        // Returns the instance as long as the area is supported.
        $searcharea = \core_search\manager::get_search_area($this->messagesentareaid);

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();

        $this->preventResetByRollback();
        $sink = $this->redirectMessages();

        $message = new \core\message\message();
        $message->courseid = SITEID;
        $message->userfrom = $user1;
        $message->userto = $user2;
        $message->subject = "Test Subject";
        $message->smallmessage = "Test small messsage";
        $message->fullmessage = "Test full messsage";
        $message->fullmessageformat = 0;
        $message->fullmessagehtml = null;
        $message->notification = 0;
        $message->component = "moodle";
        $message->name = "instantmessage";

        $messageid = message_send($message);

        $messages = $sink->get_messages();
        $message = $messages[0];

        $this->setUser($user1);

        if ($CFG->messaging) {
            $this->assertEquals(\core_search\manager::ACCESS_GRANTED, $searcharea->check_access($messageid));
        } else {
            $this->assertEquals(\core_search\manager::ACCESS_DENIED, $searcharea->check_access($messageid));
        }

        $this->assertEquals(\core_search\manager::ACCESS_DELETED, $searcharea->check_access(-123));

        \core_message\api::delete_message($user1->id, $message->id);
        $this->assertEquals(\core_search\manager::ACCESS_DELETED, $searcharea->check_access($messageid));

        $this->setUser($user2);
        $this->assertEquals(\core_search\manager::ACCESS_DENIED, $searcharea->check_access($messageid));

        $this->setUser($user3);
        $this->assertEquals(\core_search\manager::ACCESS_DENIED, $searcharea->check_access($messageid));

        $this->setGuestUser();
        $this->assertEquals(\core_search\manager::ACCESS_DENIED, $searcharea->check_access($messageid));

        $this->setAdminUser();
        $this->assertEquals(\core_search\manager::ACCESS_DENIED, $searcharea->check_access($messageid));

        delete_user($user2);

        $this->setUser($user1);
        $this->assertEquals(\core_search\manager::ACCESS_DELETED, $searcharea->check_access($messageid));

    }

    /**
     * Test sent deleted user.
     * Tests the case where a sent message for a deleted user
     * is attempted to be added to the index.
     *
     * @return void
     */
    public function test_message_sent_deleted_user() {

        // Returns the instance as long as the area is supported.
        $searcharea = \core_search\manager::get_search_area($this->messagesentareaid);
        $this->assertInstanceOf('\core_message\search\message_sent', $searcharea);

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        $this->preventResetByRollback();
        $sink = $this->redirectMessages();

        $message = new \core\message\message();
        $message->courseid = SITEID;
        $message->userfrom = $user1;
        $message->userto = $user2;
        $message->subject = "Test Subject";
        $message->smallmessage = "Test small messsage";
        $message->fullmessage = "Test full messsage";
        $message->fullmessageformat = 0;
        $message->fullmessagehtml = null;
        $message->notification = 0;
        $message->component = "moodle";
        $message->name = "instantmessage";

        message_send($message);

        $messages = $sink->get_messages();
        $message = $messages[0];

        // Delete user.
        delete_user($user1);

        $doc = $searcharea->get_document($message);

        $this->assertFalse($doc);

    }

    /**
     * Test document icon.
     */
    public function test_get_doc_icon() {
        $searcharea = \core_search\manager::get_search_area($this->messagesentareaid);

        $document = $this->getMockBuilder('\core_search\document')
            ->disableOriginalConstructor()
            ->getMock();

        $result = $searcharea->get_doc_icon($document);

        $this->assertEquals('t/message', $result->get_name());
        $this->assertEquals('moodle', $result->get_component());
    }

    /**
     * Test assigned search categories.
     */
    public function test_get_category_names() {
        $searcharea = \core_search\manager::get_search_area($this->messagesentareaid);

        $expected = ['core-users'];
        $this->assertEquals($expected, $searcharea->get_category_names());
    }
}
