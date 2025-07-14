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

namespace core_sms\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;
use core_sms\manager;

/**
 * Tests for sms
 *
 * @package    core_sms
 * @category   test
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \core_sms\privacy\provider
 */
final class provider_test extends \core_privacy\tests\provider_testcase {
    public function test_get_metadata(): void {
        $collection = new collection('core_sms');
        $newcollection = provider::get_metadata($collection);

        $this->assertCount(1, $newcollection->get_collection());
        $types = $newcollection->get_collection();
        $type = reset($types);
        $this->assertEquals('sms_messages', $type->get_name());
    }

    public function test_get_contexts_for_userid_no_messagse(): void {
        $user = get_admin();
        $contextlist = provider::get_contexts_for_userid($user->id);
        $this->assertInstanceOf(contextlist::class, $contextlist);

        $this->assertCount(0, $contextlist);
    }

    public function test_get_contexts_for_userid_with_messages(): void {
        $this->resetAfterTest(true);

        $user = get_admin();
        $manager = \core\di::get(manager::class);
        $message = $manager->send(
            recipientnumber: '12345',
            content: 'Hello world',
            component: 'core_sms',
            messagetype: 'example',
            recipientuserid: $user->id,
            async: false,
        );

        $contextlist = provider::get_contexts_for_userid($user->id);
        $this->assertInstanceOf(contextlist::class, $contextlist);

        $this->assertCount(1, $contextlist);
        $context = $contextlist->current();
        $this->assertInstanceOf(\core\context\user::class, $context);
        $this->assertEquals($user->id, $context->instanceid);
    }

    public function test_export_user_data(): void {
        global $DB;

        $this->resetAfterTest(true);
        $clock = $this->mock_clock_with_frozen(99999);

        $user = get_admin();
        $otheruser = $this->getDataGenerator()->create_user();
        $manager = \core\di::get(manager::class);
        $manager->send(
            recipientnumber: '12345',
            content: 'Hello world',
            component: 'core_sms',
            messagetype: 'example',
            recipientuserid: $user->id,
            async: false,
        );
        $manager->send(
            recipientnumber: '12345',
            content: 'Hello, world!',
            component: 'core_course',
            messagetype: 'example',
            recipientuserid: $user->id,
            async: false,
        );
        $manager->send(
            recipientnumber: '12345',
            content: 'Hello, world!',
            component: 'core_course',
            messagetype: 'example',
            recipientuserid: $otheruser->id,
            async: false,
        );

        $clock->set_to(10000000);

        // Get the data for the User.
        $usercontext = \core\context\user::instance($user->id);
        /** @var \core_privacy\tests\request\content_writer */
        $writer = writer::with_context($usercontext);
        $this->assertFalse($writer->has_any_data_in_any_context());

        // Export data for the user.
        $this->export_all_data_for_user($user->id, 'core_sms');
        $this->assertTrue($writer->has_any_data_in_any_context());
        $data = $writer->get_data([get_string('sms', 'core_sms')]);
        $this->assertObjectHasProperty('messages', $data);
        $this->assertCount(2, $data->messages);

        $message = reset($data->messages);
        $this->assertArrayHasKey('content', $message);
        $this->assertEquals('Hello world', $message['content']);

        $this->assertArrayHasKey('messagetype', $message);
        $this->assertEquals('example', $message['messagetype']);

        $this->assertArrayHasKey('status', $message);

        $this->assertArrayHasKey('timecreated', $message);
        $this->assertEquals(99999, $message['timecreated']);
    }

    public function test_export_course_data(): void {
        $this->resetAfterTest(true);
        $clock = $this->mock_clock_with_frozen(99999);

        $course = $this->getDataGenerator()->create_course();
        $user = get_admin();
        $otheruser = $this->getDataGenerator()->create_user();
        $manager = \core\di::get(manager::class);
        $manager->send(
            recipientnumber: '12345',
            content: 'Hello world',
            component: 'core_sms',
            messagetype: 'example',
            recipientuserid: $user->id,
            async: false,
        );
        $manager->send(
            recipientnumber: '12345',
            content: 'Hello, world!',
            component: 'core_course',
            messagetype: 'example',
            recipientuserid: $user->id,
            async: false,
        );
        $manager->send(
            recipientnumber: '12345',
            content: 'Hello, world!',
            component: 'core_course',
            messagetype: 'example',
            recipientuserid: $otheruser->id,
            async: false,
        );

        $clock->set_to(10000000);

        // Get the data for the User Context.
        $usercontext = \core\context\user::instance($user->id);
        /** @var \core_privacy\tests\request\content_writer */
        $writer = writer::with_context($usercontext);
        $this->assertFalse($writer->has_any_data_in_any_context());

        // Export data for the Course - there should be none.
        $this->export_context_data_for_user($user->id, \core\context\course::instance($course->id), 'core_sms');
        $this->assertFalse($writer->has_any_data_in_any_context());
    }

    public function test_delete_data_for_all_users_in_context(): void {
        global $DB;

        $this->resetAfterTest(true);

        $user = get_admin();
        $otheruser = $this->getDataGenerator()->create_user();
        $manager = \core\di::get(manager::class);
        $manager->send(
            recipientnumber: '12345',
            content: 'Hello world',
            component: 'core_sms',
            messagetype: 'example',
            recipientuserid: $user->id,
            async: false,
        );
        $manager->send(
            recipientnumber: '12345',
            content: 'Hello, world!',
            component: 'core_course',
            messagetype: 'example',
            recipientuserid: $user->id,
            async: false,
        );
        $manager->send(
            recipientnumber: '12345',
            content: 'Hello, world!',
            component: 'core_course',
            messagetype: 'example',
            recipientuserid: $otheruser->id,
            async: false,
        );
        $this->assertCount(2, $DB->get_records('sms_messages', ['recipientuserid' => $user->id]));
        $this->assertEquals(3, $DB->count_records('sms_messages'));

        // No data is store under the system context. Nothing should be deleted.
        provider::delete_data_for_all_users_in_context(\core\context\system::instance());
        $this->assertEquals(3, $DB->count_records('sms_messages'));

        $usercontext = \core\context\user::instance($user->id);
        provider::delete_data_for_all_users_in_context($usercontext);
        $this->assertEmpty($DB->get_records('sms_messages', ['recipientuserid' => $user->id]));
        $this->assertEquals(1, $DB->count_records('sms_messages'));
    }

    public function test_delete_data_for_user(): void {
        global $DB;

        $this->resetAfterTest(true);

        $user = get_admin();
        $otheruser = $this->getDataGenerator()->create_user();
        $manager = \core\di::get(manager::class);
        $manager->send(
            recipientnumber: '12345',
            content: 'Hello world',
            component: 'core_sms',
            messagetype: 'example',
            recipientuserid: $user->id,
            async: false,
        );
        $manager->send(
            recipientnumber: '12345',
            content: 'Hello, world!',
            component: 'core_course',
            messagetype: 'example',
            recipientuserid: $user->id,
            async: false,
        );
        $manager->send(
            recipientnumber: '12345',
            content: 'Hello, world!',
            component: 'core_course',
            messagetype: 'example',
            recipientuserid: $otheruser->id,
            async: false,
        );
        $this->assertCount(2, $DB->get_records('sms_messages', ['recipientuserid' => $user->id]));
        $this->assertEquals(3, $DB->count_records('sms_messages'));

        $usercontext = \core\context\user::instance($user->id);
        provider::delete_data_for_user(new approved_contextlist($user, 'core_sms', [$usercontext->id]));
        $this->assertEmpty($DB->get_records('sms_messages', ['recipientuserid' => $user->id]));
        $this->assertEquals(1, $DB->count_records('sms_messages'));
    }

    public function test_delete_data_for_users_incorrect_userid(): void {
        global $DB;

        $this->resetAfterTest(true);

        $user = get_admin();
        $otheruser = $this->getDataGenerator()->create_user();
        $manager = \core\di::get(manager::class);
        $manager->send(
            recipientnumber: '12345',
            content: 'Hello world',
            component: 'core_sms',
            messagetype: 'example',
            recipientuserid: $user->id,
            async: false,
        );
        $manager->send(
            recipientnumber: '12345',
            content: 'Hello, world!',
            component: 'core_course',
            messagetype: 'example',
            recipientuserid: $user->id,
            async: false,
        );
        $manager->send(
            recipientnumber: '12345',
            content: 'Hello, world!',
            component: 'core_course',
            messagetype: 'example',
            recipientuserid: $otheruser->id,
            async: false,
        );
        $this->assertEquals(2, $DB->count_records('sms_messages', ['recipientuserid' => $user->id]));
        $this->assertEquals(1, $DB->count_records('sms_messages', ['recipientuserid' => $otheruser->id]));
        $this->assertEquals(3, $DB->count_records('sms_messages'));

        $usercontext = \core\context\user::instance($user->id);

        // Data is all stored under the users own context.
        provider::delete_data_for_users(new approved_userlist(
            $usercontext,
            'core_sms',
            [$otheruser->id]
        ));
        $this->assertEquals(0, $DB->count_records('sms_messages', ['recipientuserid' => $user->id]));
        $this->assertEquals(1, $DB->count_records('sms_messages', ['recipientuserid' => $otheruser->id]));
        $this->assertEquals(1, $DB->count_records('sms_messages'));
    }

    public function test_delete_data_for_users_correct_user(): void {
        global $DB;

        $this->resetAfterTest(true);

        $user = get_admin();
        $otheruser = $this->getDataGenerator()->create_user();
        $manager = \core\di::get(manager::class);
        $manager->send(
            recipientnumber: '12345',
            content: 'Hello world',
            component: 'core_sms',
            messagetype: 'example',
            recipientuserid: $user->id,
            async: false,
        );
        $manager->send(
            recipientnumber: '12345',
            content: 'Hello, world!',
            component: 'core_course',
            messagetype: 'example',
            recipientuserid: $user->id,
            async: false,
        );
        $manager->send(
            recipientnumber: '12345',
            content: 'Hello, world!',
            component: 'core_course',
            messagetype: 'example',
            recipientuserid: $otheruser->id,
            async: false,
        );
        $this->assertEquals(2, $DB->count_records('sms_messages', ['recipientuserid' => $user->id]));
        $this->assertEquals(1, $DB->count_records('sms_messages', ['recipientuserid' => $otheruser->id]));
        $this->assertEquals(3, $DB->count_records('sms_messages'));

        $usercontext = \core\context\user::instance($user->id);

        // Deleting data for both users only deletes that user's content.
        provider::delete_data_for_users(new approved_userlist(
            $usercontext,
            'core_sms',
            [$user->id, $otheruser->id],
        ));
        $this->assertEquals(0, $DB->count_records('sms_messages', ['recipientuserid' => $user->id]));
        $this->assertEquals(1, $DB->count_records('sms_messages', ['recipientuserid' => $otheruser->id]));
        $this->assertEquals(1, $DB->count_records('sms_messages'));
    }

    public function test_delete_data_for_users_wrong_context(): void {
        global $DB;

        $this->resetAfterTest(true);

        $user = get_admin();
        $otheruser = $this->getDataGenerator()->create_user();
        $manager = \core\di::get(manager::class);
        $manager->send(
            recipientnumber: '12345',
            content: 'Hello world',
            component: 'core_sms',
            messagetype: 'example',
            recipientuserid: $user->id,
            async: false,
        );
        $manager->send(
            recipientnumber: '12345',
            content: 'Hello, world!',
            component: 'core_course',
            messagetype: 'example',
            recipientuserid: $user->id,
            async: false,
        );
        $manager->send(
            recipientnumber: '12345',
            content: 'Hello, world!',
            component: 'core_course',
            messagetype: 'example',
            recipientuserid: $otheruser->id,
            async: false,
        );
        $this->assertEquals(2, $DB->count_records('sms_messages', ['recipientuserid' => $user->id]));
        $this->assertEquals(1, $DB->count_records('sms_messages', ['recipientuserid' => $otheruser->id]));
        $this->assertEquals(3, $DB->count_records('sms_messages'));

        // Incorrect contexts are ignored.
        provider::delete_data_for_users(new approved_userlist(
            \core\context\system::instance(),
            'core_sms',
            [$user->id, $otheruser->id],
        ));

        $this->assertEquals(2, $DB->count_records('sms_messages', ['recipientuserid' => $user->id]));
        $this->assertEquals(1, $DB->count_records('sms_messages', ['recipientuserid' => $otheruser->id]));
        $this->assertEquals(3, $DB->count_records('sms_messages'));
    }

    public function test_get_users_in_context(): void {
        global $DB;

        $this->resetAfterTest(true);

        $user = get_admin();
        $otheruser = $this->getDataGenerator()->create_user();
        $manager = \core\di::get(manager::class);
        $manager->send(
            recipientnumber: '12345',
            content: 'Hello world',
            component: 'core_sms',
            messagetype: 'example',
            recipientuserid: $user->id,
            async: false,
        );
        $manager->send(
            recipientnumber: '12345',
            content: 'Hello, world!',
            component: 'core_course',
            messagetype: 'example',
            recipientuserid: $user->id,
            async: false,
        );
        $manager->send(
            recipientnumber: '12345',
            content: 'Hello, world!',
            component: 'core_course',
            messagetype: 'example',
            recipientuserid: $otheruser->id,
            async: false,
        );
        $this->assertCount(2, $DB->get_records('sms_messages', ['recipientuserid' => $user->id]));
        $this->assertEquals(3, $DB->count_records('sms_messages'));

        // Get the users in the user context - should just be the user of that user context.
        $userlist = new userlist(\core\context\user::instance($user->id), 'core_sms');
        provider::get_users_in_context($userlist);
        $this->assertEquals([$user->id], $userlist->get_userids());

        // No users in the system context.
        $userlist = new userlist(\core\context\system::instance(), 'core_sms');
        provider::get_users_in_context($userlist);
        $this->assertEquals([], $userlist->get_userids());
    }
}
