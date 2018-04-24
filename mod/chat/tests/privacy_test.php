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
 * Data provider tests.
 *
 * @package    mod_chat
 * @category   test
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $CFG;

use core_privacy\tests\provider_testcase;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\writer;
use mod_chat\privacy\provider;

require_once($CFG->dirroot . '/mod/chat/lib.php');

/**
 * Data provider testcase class.
 *
 * @package    mod_chat
 * @category   test
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_chat_privacy_testcase extends provider_testcase {

    public function setUp() {
        global $PAGE;
        $this->resetAfterTest();
        $PAGE->get_renderer('core');
    }

    public function test_get_contexts_for_userid() {
        global $DB;
        $dg = $this->getDataGenerator();
        $c1 = $dg->create_course();
        $c2 = $dg->create_course();

        $u1 = $dg->create_user();
        $u2 = $dg->create_user();

        $chat1a = $dg->create_module('chat', ['course' => $c1]);
        $chat1b = $dg->create_module('chat', ['course' => $c1]);
        $chat2a = $dg->create_module('chat', ['course' => $c2]);

        // Logins but no message.
        $chatuser = $this->login_user_in_course_chat($u1, $c1, $chat1a);

        // Logins and messages.
        $chatuser = $this->login_user_in_course_chat($u1, $c1, $chat1b);
        chat_send_chatmessage($chatuser, 'Hello world!');

        // Silent login (no system message).
        $chatuser = $this->login_user_in_course_chat($u1, $c2, $chat2a, 0, true);

        // Silent login and messages.
        $chatuser = $this->login_user_in_course_chat($u2, $c1, $chat1b, 0, true);
        chat_send_chatmessage($chatuser, 'Ça va ?');
        chat_send_chatmessage($chatuser, 'Moi, ça va.');

        // Silent login and messages.
        $chatuser = $this->login_user_in_course_chat($u2, $c2, $chat2a);
        chat_send_chatmessage($chatuser, 'What\'s happening here?');

        // Check contexts for user 1.
        $contextids = provider::get_contexts_for_userid($u1->id)->get_contextids();
        $this->assertCount(2, $contextids);
        $this->assertTrue(in_array(context_module::instance($chat1a->cmid)->id, $contextids));
        $this->assertTrue(in_array(context_module::instance($chat1b->cmid)->id, $contextids));

        $contextids = provider::get_contexts_for_userid($u2->id)->get_contextids();
        $this->assertCount(2, $contextids);
        $this->assertTrue(in_array(context_module::instance($chat1b->cmid)->id, $contextids));
        $this->assertTrue(in_array(context_module::instance($chat2a->cmid)->id, $contextids));
    }

    public function test_delete_data_for_all_users_in_context() {
        global $DB;
        $dg = $this->getDataGenerator();
        $c1 = $dg->create_course();

        $u1 = $dg->create_user();
        $u2 = $dg->create_user();

        $chat1a = $dg->create_module('chat', ['course' => $c1]);
        $chat1b = $dg->create_module('chat', ['course' => $c1]);
        $chat1actx = context_module::instance($chat1a->cmid);
        $chat1bctx = context_module::instance($chat1b->cmid);

        $u1chat1a = $this->login_user_in_course_chat($u1, $c1, $chat1a);
        $u2chat1a = $this->login_user_in_course_chat($u2, $c1, $chat1a);
        chat_send_chatmessage($u1chat1a, 'Ça va ?');
        chat_send_chatmessage($u2chat1a, 'Oui, et toi ?');
        chat_send_chatmessage($u1chat1a, 'Bien merci.');
        chat_send_chatmessage($u2chat1a, 'Pourquoi ils disent omelette "du" fromage ?!');
        chat_send_chatmessage($u1chat1a, 'Aucune idée');
        $this->assert_has_data_in_chat($u1, $chat1a);
        $this->assert_has_data_in_chat($u2, $chat1a);

        $u1chat1b = $this->login_user_in_course_chat($u1, $c1, $chat1b);
        $u2chat1b = $this->login_user_in_course_chat($u2, $c1, $chat1b);
        chat_send_chatmessage($u1chat1b, 'How are you going?');
        chat_send_chatmessage($u2chat1b, 'Alright, you?');
        chat_send_chatmessage($u1chat1b, 'Good, thanks.');
        chat_send_chatmessage($u2chat1b, 'Sacre bleu!');
        chat_send_chatmessage($u1chat1b, '\ö/');
        $this->assert_has_data_in_chat($u1, $chat1b);
        $this->assert_has_data_in_chat($u2, $chat1b);

        // No change.
        provider::delete_data_for_all_users_in_context(context_course::instance($c1->id));
        $this->assert_has_data_in_chat($u1, $chat1a);
        $this->assert_has_data_in_chat($u2, $chat1a);
        $this->assert_has_data_in_chat($u1, $chat1b);
        $this->assert_has_data_in_chat($u2, $chat1b);

        // Deletinge first chat does not affect other chat.
        provider::delete_data_for_all_users_in_context($chat1actx);
        $this->assert_has_no_data_in_chat($u1, $chat1a);
        $this->assert_has_no_data_in_chat($u2, $chat1a);
        $this->assert_has_data_in_chat($u1, $chat1b);
        $this->assert_has_data_in_chat($u2, $chat1b);
    }

    public function test_delete_data_for_user() {
        global $DB;
        $dg = $this->getDataGenerator();
        $c1 = $dg->create_course();

        $u1 = $dg->create_user();
        $u2 = $dg->create_user();

        $chat1a = $dg->create_module('chat', ['course' => $c1]);
        $chat1b = $dg->create_module('chat', ['course' => $c1]);
        $chat1actx = context_module::instance($chat1a->cmid);
        $chat1bctx = context_module::instance($chat1b->cmid);

        $u1chat1a = $this->login_user_in_course_chat($u1, $c1, $chat1a);
        $u2chat1a = $this->login_user_in_course_chat($u2, $c1, $chat1a);
        chat_send_chatmessage($u1chat1a, 'Ça va ?');
        chat_send_chatmessage($u2chat1a, 'Oui, et toi ?');
        chat_send_chatmessage($u1chat1a, 'Bien merci.');
        chat_send_chatmessage($u2chat1a, 'Pourquoi ils disent omelette "du" fromage ?!');
        chat_send_chatmessage($u1chat1a, 'Aucune idée');
        $this->assert_has_data_in_chat($u1, $chat1a);
        $this->assert_has_data_in_chat($u2, $chat1a);

        $u1chat1b = $this->login_user_in_course_chat($u1, $c1, $chat1b);
        $u2chat1b = $this->login_user_in_course_chat($u2, $c1, $chat1b);
        chat_send_chatmessage($u1chat1b, 'How are you going?');
        chat_send_chatmessage($u2chat1b, 'Alright, you?');
        chat_send_chatmessage($u1chat1b, 'Good, thanks.');
        chat_send_chatmessage($u2chat1b, 'Sacre bleu!');
        chat_send_chatmessage($u1chat1b, '\ö/');
        $this->assert_has_data_in_chat($u1, $chat1b);
        $this->assert_has_data_in_chat($u2, $chat1b);

        provider::delete_data_for_user(new approved_contextlist($u1, 'mod_chat', [$chat1actx->id]));
        $this->assert_has_no_data_in_chat($u1, $chat1a);
        $this->assert_has_data_in_chat($u2, $chat1a);
        $this->assert_has_data_in_chat($u1, $chat1b);
        $this->assert_has_data_in_chat($u2, $chat1b);

        provider::delete_data_for_user(new approved_contextlist($u2, 'mod_chat', [$chat1actx->id, $chat1bctx->id]));
        $this->assert_has_no_data_in_chat($u1, $chat1a);
        $this->assert_has_no_data_in_chat($u2, $chat1a);
        $this->assert_has_data_in_chat($u1, $chat1b);
        $this->assert_has_no_data_in_chat($u2, $chat1b);
    }

    public function test_export_data_for_user() {
        global $DB;
        $dg = $this->getDataGenerator();
        $c1 = $dg->create_course();

        $u1 = $dg->create_user();
        $u2 = $dg->create_user();

        $chat1a = $dg->create_module('chat', ['course' => $c1]);
        $chat1b = $dg->create_module('chat', ['course' => $c1]);
        $chat1actx = context_module::instance($chat1a->cmid);
        $chat1bctx = context_module::instance($chat1b->cmid);

        $u1chat1a = $this->login_user_in_course_chat($u1, $c1, $chat1a);
        $u2chat1a = $this->login_user_in_course_chat($u2, $c1, $chat1a);
        chat_send_chatmessage($u1chat1a, 'Ça va ?');
        chat_send_chatmessage($u2chat1a, 'Oui, et toi ?');
        chat_send_chatmessage($u1chat1a, 'Bien merci.');
        chat_send_chatmessage($u2chat1a, 'Pourquoi ils disent omelette "du" fromage ?!');
        chat_send_chatmessage($u1chat1a, 'Aucune idée');
        chat_send_chatmessage($u1chat1a, 'exit', true);

        $u1chat1b = $this->login_user_in_course_chat($u1, $c1, $chat1b);
        $u2chat1b = $this->login_user_in_course_chat($u2, $c1, $chat1b);
        chat_send_chatmessage($u1chat1b, 'How are you going?');
        chat_send_chatmessage($u2chat1b, 'Alright, you?');
        chat_send_chatmessage($u1chat1b, 'Good, thanks.');
        chat_send_chatmessage($u2chat1b, 'Sacre bleu!');
        chat_send_chatmessage($u1chat1b, '\ö/');

        // Export for user 1 in chat 1.
        provider::export_user_data(new approved_contextlist($u1, 'mod_chat', [$chat1actx->id]));
        $data = writer::with_context($chat1actx)->get_data([]);
        $this->assertNotEmpty($data);
        $this->assertCount(5, $data->messages);
        $this->assertEquals(get_string('messageenter', 'mod_chat', fullname($u1)), $data->messages[0]['message']);
        $this->assertEquals(transform::yesno(true), $data->messages[0]['is_system_generated']);
        $this->assertEquals('Ça va ?', $data->messages[1]['message']);
        $this->assertEquals(transform::yesno(false), $data->messages[1]['is_system_generated']);
        $this->assertEquals('Bien merci.', $data->messages[2]['message']);
        $this->assertEquals(transform::yesno(false), $data->messages[2]['is_system_generated']);
        $this->assertEquals('Aucune idée', $data->messages[3]['message']);
        $this->assertEquals(transform::yesno(false), $data->messages[3]['is_system_generated']);
        $this->assertEquals(get_string('messageexit', 'mod_chat', fullname($u1)), $data->messages[4]['message']);
        $this->assertEquals(transform::yesno(true), $data->messages[4]['is_system_generated']);
        $data = writer::with_context($chat1bctx)->get_data([]);
        $this->assertEmpty($data);

        // Export for user2 in chat 1 and 2.
        writer::reset();
        provider::export_user_data(new approved_contextlist($u2, 'mod_chat', [$chat1actx->id, $chat1bctx->id]));
        $data = writer::with_context($chat1actx)->get_data([]);
        $this->assertNotEmpty($data);
        $this->assertCount(3, $data->messages);
        $this->assertEquals(get_string('messageenter', 'mod_chat', fullname($u2)), $data->messages[0]['message']);
        $this->assertEquals('Oui, et toi ?', $data->messages[1]['message']);
        $this->assertEquals('Pourquoi ils disent omelette "du" fromage ?!', $data->messages[2]['message']);
        $data = writer::with_context($chat1bctx)->get_data([]);
        $this->assertNotEmpty($data);
        $this->assertCount(3, $data->messages);
        $this->assertEquals(get_string('messageenter', 'mod_chat', fullname($u2)), $data->messages[0]['message']);
        $this->assertEquals('Alright, you?', $data->messages[1]['message']);
        $this->assertEquals('Sacre bleu!', $data->messages[2]['message']);
    }

    /**
     * Assert that there is data for a user in a chat.
     *
     * @param object $user The user.
     * @param object $chat The chat.
     * @return void
     */
    protected function assert_has_data_in_chat($user, $chat) {
        $this->assertTrue($this->has_data_in_chat($user, $chat));
    }

    /**
     * Assert that there isn't any data for a user in a chat.
     *
     * @param object $user The user.
     * @param object $chat The chat.
     * @return void
     */
    protected function assert_has_no_data_in_chat($user, $chat) {
        $this->assertFalse($this->has_data_in_chat($user, $chat));
    }

    /**
     * Check whether a user has data in a chat.
     *
     * @param object $user The user.
     * @param object $chat The chat.
     * @return bool
     */
    protected function has_data_in_chat($user, $chat) {
        global $DB;
        return $DB->record_exists('chat_messages', ['chatid' => $chat->id, 'userid' => $user->id]);
    }

    /**
     * Login a user in a chat.
     *
     * @param object $user The user.
     * @param object $course The course.
     * @param object $chat The chat.
     * @param int $group The group number.
     * @param bool $silent Whether we should advertise that the user logs in.
     * @return object The chat user.
     */
    protected function login_user_in_course_chat($user, $course, $chat, $group = 0, $silent = false) {
        global $DB, $USER;
        $origuser = $USER;
        $this->setUser($user);
        chat_login_user($chat->id, $silent ? 'sockets' : 'basic', 0, $course);
        $chatuser = $DB->get_record('chat_users', ['userid' => $user->id, 'chatid' => $chat->id, 'groupid' => 0]);
        $this->setUser($origuser);
        return $chatuser;
    }
}
