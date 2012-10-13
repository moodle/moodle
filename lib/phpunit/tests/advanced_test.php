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
 * PHPUnit integration tests
 *
 * @package    core
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Test advanced_testcase extra features.
 *
 * @package    core
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_phpunit_advanced_testcase extends advanced_testcase {

    public function test_debugging() {
        global $CFG;
        $this->resetAfterTest();

        debugging('hokus');
        $this->assertDebuggingCalled();
        debugging('pokus');
        $this->assertDebuggingCalled('pokus');
        debugging('pokus', DEBUG_MINIMAL);
        $this->assertDebuggingCalled('pokus', DEBUG_MINIMAL);
        $this->assertDebuggingNotCalled();

        debugging('a');
        debugging('b', DEBUG_MINIMAL);
        debugging('c', DEBUG_DEVELOPER);
        $debuggings = $this->getDebuggingMessages();
        $this->assertEquals(3, count($debuggings));
        $this->assertSame('a', $debuggings[0]->message);
        $this->assertSame(DEBUG_NORMAL, $debuggings[0]->level);
        $this->assertSame('b', $debuggings[1]->message);
        $this->assertSame(DEBUG_MINIMAL, $debuggings[1]->level);
        $this->assertSame('c', $debuggings[2]->message);
        $this->assertSame(DEBUG_DEVELOPER, $debuggings[2]->level);

        $this->resetDebugging();
        $this->assertDebuggingNotCalled();
        $debuggings = $this->getDebuggingMessages();
        $this->assertEquals(0, count($debuggings));

        $CFG->debug = DEBUG_NONE;
        debugging('hokus');
        $this->assertDebuggingNotCalled();
    }

    public function test_set_user() {
        global $USER, $DB;

        $this->assertEquals(0, $USER->id);
        $this->assertSame($_SESSION['USER'], $USER);

        $user = $DB->get_record('user', array('id'=>2));
        $this->assertNotEmpty($user);
        $this->setUser($user);
        $this->assertEquals(2, $USER->id);
        $this->assertEquals(2, $_SESSION['USER']->id);
        $this->assertSame($_SESSION['USER'], $USER);

        $USER->id = 3;
        $this->assertEquals(3, $USER->id);
        $this->assertEquals(3, $_SESSION['USER']->id);
        $this->assertSame($_SESSION['USER'], $USER);

        session_set_user($user);
        $this->assertEquals(2, $USER->id);
        $this->assertEquals(2, $_SESSION['USER']->id);
        $this->assertSame($_SESSION['USER'], $USER);

        $USER = $DB->get_record('user', array('id'=>1));
        $this->assertNotEmpty($USER);
        $this->assertEquals(1, $USER->id);
        $this->assertEquals(1, $_SESSION['USER']->id);
        $this->assertSame($_SESSION['USER'], $USER);

        $this->setUser(null);
        $this->assertEquals(0, $USER->id);
        $this->assertSame($_SESSION['USER'], $USER);
    }

    public function test_set_admin_user() {
        global $USER;

        $this->resetAfterTest(true);

        $this->setAdminUser();
        $this->assertEquals($USER->id, 2);
        $this->assertTrue(is_siteadmin());
    }

    public function test_set_guest_user() {
        global $USER;

        $this->resetAfterTest(true);

        $this->setGuestUser();
        $this->assertEquals($USER->id, 1);
        $this->assertTrue(isguestuser());
    }

    public function test_database_reset() {
        global $DB;

        $this->resetAfterTest(true);

        $this->preventResetByRollback();

        $this->assertEquals(1, $DB->count_records('course')); // only frontpage in new site

        // this is weird table - id is NOT a sequence here
        $this->assertEquals(0, $DB->count_records('context_temp'));
        $DB->import_record('context_temp', array('id'=>5, 'path'=>'/1/2', 'depth'=>2));
        $record = $DB->get_record('context_temp', array());
        $this->assertEquals(5, $record->id);

        $this->assertEquals(0, $DB->count_records('user_preferences'));
        $originaldisplayid = $DB->insert_record('user_preferences', array('userid'=>2, 'name'=> 'phpunittest', 'value'=>'x'));
        $this->assertEquals(1, $originaldisplayid);

        $course = $this->getDataGenerator()->create_course();
        $this->assertEquals(2, $course->id);

        $this->assertEquals(2, $DB->count_records('user'));
        $DB->delete_records('user', array('id'=>1));
        $this->assertEquals(1, $DB->count_records('user'));

        //=========

        $this->resetAllData();

        $this->assertEquals(1, $DB->count_records('course')); // only frontpage in new site
        $this->assertEquals(0, $DB->count_records('context_temp')); // only frontpage in new site
        $course = $this->getDataGenerator()->create_course();
        $this->assertEquals(2, $course->id);

        $displayid = $DB->insert_record('user_preferences', array('userid'=>2, 'name'=> 'phpunittest', 'value'=>'x'));
        $this->assertEquals($originaldisplayid, $displayid);

        $this->assertEquals(2, $DB->count_records('user'));
        $DB->delete_records('user', array('id'=>2));
        $user = $this->getDataGenerator()->create_user();
        $this->assertEquals(3, $user->id);

        // =========

        $this->resetAllData();

        $course = $this->getDataGenerator()->create_course();
        $this->assertEquals(2, $course->id);

        $this->assertEquals(2, $DB->count_records('user'));
        $DB->delete_records('user', array('id'=>2));

        //==========

        $this->resetAllData();

        $course = $this->getDataGenerator()->create_course();
        $this->assertEquals(2, $course->id);

        $this->assertEquals(2, $DB->count_records('user'));
    }

    public function test_change_detection() {
        global $DB, $CFG, $COURSE, $SITE, $USER;

        $this->preventResetByRollback();
        phpunit_util::reset_all_data(true);

        // database change
        $this->assertEquals(1, $DB->get_field('user', 'confirmed', array('id'=>2)));
        $DB->set_field('user', 'confirmed', 0, array('id'=>2));
        try {
            phpunit_util::reset_all_data(true);
        } catch (Exception $e) {
            $this->assertInstanceOf('PHPUnit_Framework_Error_Warning', $e);
        }
        $this->assertEquals(1, $DB->get_field('user', 'confirmed', array('id'=>2)));

        // config change
        $CFG->xx = 'yy';
        unset($CFG->admin);
        $CFG->rolesactive = 0;
        try {
            phpunit_util::reset_all_data(true);
        } catch (Exception $e) {
            $this->assertInstanceOf('PHPUnit_Framework_Error_Warning', $e);
            $this->assertContains('xx', $e->getMessage());
            $this->assertContains('admin', $e->getMessage());
            $this->assertContains('rolesactive', $e->getMessage());
        }
        $this->assertFalse(isset($CFG->xx));
        $this->assertTrue(isset($CFG->admin));
        $this->assertEquals(1, $CFG->rolesactive);

        //silent changes
        $_SERVER['xx'] = 'yy';
        phpunit_util::reset_all_data(true);
        $this->assertFalse(isset($_SERVER['xx']));

        // COURSE
        $SITE->id = 10;
        $COURSE = new stdClass();
        $COURSE->id = 7;
        try {
            phpunit_util::reset_all_data(true);
        } catch (Exception $e) {
            $this->assertInstanceOf('PHPUnit_Framework_Error_Warning', $e);
            $this->assertEquals(1, $SITE->id);
            $this->assertSame($SITE, $COURSE);
            $this->assertSame($SITE, $COURSE);
        }

        // USER change
        $this->setUser(2);
        try {
            phpunit_util::reset_all_data(true);
        } catch (Exception $e) {
            $this->assertInstanceOf('PHPUnit_Framework_Error_Warning', $e);
            $this->assertEquals(0, $USER->id);
        }
    }

    public function test_getDataGenerator() {
        $generator = $this->getDataGenerator();
        $this->assertInstanceOf('phpunit_data_generator', $generator);
    }

    public function test_database_mock1() {
        global $DB;

        try {
            $DB->get_record('pokus', array());
            $this->fail('Exception expected when accessing non existent table');
        } catch (dml_exception $e) {
            $this->assertTrue(true);
        }
        $DB = $this->getMock(get_class($DB));
        $this->assertNull($DB->get_record('pokus', array()));
        // test continues after reset
    }

    public function test_database_mock2() {
        global $DB;

        // now the database should be back to normal
        $this->assertFalse($DB->get_record('user', array('id'=>9999)));
    }

    public function test_load_dataset() {
        global $DB;

        $this->resetAfterTest(true);

        $this->assertFalse($DB->record_exists('user', array('id'=>5)));
        $this->assertFalse($DB->record_exists('user', array('id'=>7)));
        $dataset = $this->createXMLDataSet(__DIR__.'/fixtures/sample_dataset.xml');
        $this->loadDataSet($dataset);
        $this->assertTrue($DB->record_exists('user', array('id'=>5)));
        $this->assertTrue($DB->record_exists('user', array('id'=>7)));
        $user5 = $DB->get_record('user', array('id'=>5));
        $user7 = $DB->get_record('user', array('id'=>7));
        $this->assertEquals('john.doe', $user5->username);
        $this->assertEquals('jane.doe', $user7->username);

        $dataset = $this->createCsvDataSet(array('user'=>__DIR__.'/fixtures/sample_dataset.csv'));
        $this->loadDataSet($dataset);
        $this->assertEquals(8, $DB->get_field('user', 'id', array('username'=>'pepa.novak')));
        $this->assertEquals(9, $DB->get_field('user', 'id', array('username'=>'bozka.novakova')));

        $data = array(
            'user' => array(
                array('username', 'email'),
                array('top.secret', 'top@example.com'),
                array('low.secret', 'low@example.com'),
            ),
        );
        $dataset = $this->createArrayDataSet($data);
        $this->loadDataSet($dataset);
        $this->assertTrue($DB->record_exists('user', array('email'=>'top@example.com')));
        $this->assertTrue($DB->record_exists('user', array('email'=>'low@example.com')));

        $data = array(
            'user' => array(
                array('username'=>'noidea', 'email'=>'noidea@example.com'),
                array('username'=>'onemore', 'email'=>'onemore@example.com'),
            ),
        );
        $dataset = $this->createArrayDataSet($data);
        $this->loadDataSet($dataset);
        $this->assertTrue($DB->record_exists('user', array('username'=>'noidea')));
        $this->assertTrue($DB->record_exists('user', array('username'=>'onemore')));
    }

    public function test_message_redirection() {
        global $DB;

        $this->preventResetByRollback(); // Messaging is not compatible with transactions...
        $this->resetAfterTest(false);

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        // Any core message will do here.
        $message1 = new stdClass();
        $message1->component         = 'moodle';
        $message1->name              = 'instantmessage';
        $message1->userfrom          = $user1;
        $message1->userto            = $user2;
        $message1->subject           = 'message subject 1';
        $message1->fullmessage       = 'message body';
        $message1->fullmessageformat = FORMAT_MARKDOWN;
        $message1->fullmessagehtml   = '<p>message body</p>';
        $message1->smallmessage      = 'small message';

        $message2 = new stdClass();
        $message2->component         = 'moodle';
        $message2->name              = 'instantmessage';
        $message2->userfrom          = $user2;
        $message2->userto            = $user1;
        $message2->subject           = 'message subject 2';
        $message2->fullmessage       = 'message body';
        $message2->fullmessageformat = FORMAT_MARKDOWN;
        $message2->fullmessagehtml   = '<p>message body</p>';
        $message2->smallmessage      = 'small message';

        // There should be debugging message without redirection.
        message_send($message1);
        $this->assertDebuggingCalled(null, null, 'message_send() must print debug message that messaging is disabled in phpunit tests.');

        // Sink should catch messages;
        $sink = $this->redirectMessages();
        $mid1 = message_send($message1);
        $mid2 = message_send($message2);

        $this->assertDebuggingNotCalled('message redirection must prevent debug messages from the message_send()');
        $this->assertEquals(2, $sink->count());
        $this->assertGreaterThanOrEqual(1, $mid1);
        $this->assertGreaterThanOrEqual($mid1, $mid2);

        $messages = $sink->get_messages();
        $this->assertTrue(is_array($messages));
        $this->assertEquals(2, count($messages));
        $this->assertEquals($mid1, $messages[0]->id);
        $this->assertEquals($message1->userto->id, $messages[0]->useridto);
        $this->assertEquals($message1->userfrom->id, $messages[0]->useridfrom);
        $this->assertEquals($message1->smallmessage, $messages[0]->smallmessage);
        $this->assertEquals($mid2, $messages[1]->id);
        $this->assertEquals($message2->userto->id, $messages[1]->useridto);
        $this->assertEquals($message2->userfrom->id, $messages[1]->useridfrom);
        $this->assertEquals($message2->smallmessage, $messages[1]->smallmessage);

        // Test resetting.
        $sink->clear();
        $messages = $sink->get_messages();
        $this->assertTrue(is_array($messages));
        $this->assertEquals(0, count($messages));

        message_send($message1);
        $messages = $sink->get_messages();
        $this->assertTrue(is_array($messages));
        $this->assertEquals(1, count($messages));

        // Test closing.
        $sink->close();
        $messages = $sink->get_messages();
        $this->assertTrue(is_array($messages));
        $this->assertEquals(1, count($messages), 'Messages in sink are supposed to stay there after close');

        // Test debugging is enabled again.
        message_send($message1);
        $this->assertDebuggingCalled(null, null, 'message_send() must print debug message that messaging is disabled in phpunit tests.');

        // Test invalid names and components.

        $sink = $this->redirectMessages();

        $message3 = new stdClass();
        $message3->component         = 'xxxx_yyyyy';
        $message3->name              = 'instantmessage';
        $message3->userfrom          = $user2;
        $message3->userto            = $user1;
        $message3->subject           = 'message subject 2';
        $message3->fullmessage       = 'message body';
        $message3->fullmessageformat = FORMAT_MARKDOWN;
        $message3->fullmessagehtml   = '<p>message body</p>';
        $message3->smallmessage      = 'small message';

        try {
            message_send($message3);
            $this->fail('coding expcetion expected if invalid component specified');
        } catch (coding_exception $e) {
            $this->assertTrue(true);
        }

        $message3->component = 'moodle';
        $message3->name      = 'yyyyyy';
        try {
            message_send($message3);
            $this->fail('coding expcetion expected if invalid name specified');
        } catch (coding_exception $e) {
            $this->assertTrue(true);
        }

        message_send($message1);
        $this->assertEquals(1, $sink->count());

        // Test if sink can be carried over to next test.
        $this->assertTrue(phpunit_util::is_redirecting_messages());
        return $sink;
    }

    /**
     * @depends test_message_redirection
     */
    public function test_message_redirection_noreset($sink) {
        $this->preventResetByRollback(); // Messaging is not compatible with transactions...
        $this->resetAfterTest(true);

        $this->assertTrue(phpunit_util::is_redirecting_messages());
        $this->assertEquals(1, $sink->count());

        $message = new stdClass();
        $message->component         = 'moodle';
        $message->name              = 'instantmessage';
        $message->userfrom          = get_admin();
        $message->userto            = get_admin();
        $message->subject           = 'message subject 1';
        $message->fullmessage       = 'message body';
        $message->fullmessageformat = FORMAT_MARKDOWN;
        $message->fullmessagehtml   = '<p>message body</p>';
        $message->smallmessage      = 'small message';

        message_send($message);
        $this->assertEquals(2, $sink->count());
    }

    /**
     * @depends test_message_redirection_noreset
     */
    public function test_message_redirection_reset() {
        $this->assertFalse(phpunit_util::is_redirecting_messages(), 'Test reset must stop message redirection.');
    }
}
