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

namespace core_ai\provider;

use core_ai\aiactions\generate_image;
use core_ai\aiactions\generate_text;
use core_ai\aiactions\responses\response_generate_image;
use core_ai\aiactions\responses\response_generate_text;
use core_ai\aiactions\responses\response_summarise_text;
use core_ai\aiactions\summarise_text;
use core_ai\manager;
use core_ai\privacy\provider;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\writer;
use core_privacy\tests\request\content_writer;

/**
 * Unit tests for \core_ai\provider
 *
 * @package    core_ai
 * @copyright  2024 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core_ai\provider
 */
final class provider_test extends \advanced_testcase {

    /** @var \core_ai\manager */
    private $manager;

    /** @var \core_ai\provider */
    private $provider;

    /**
     * Overriding setUp() function to always reset after tests.
     */
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();

        // Create the provider instance.
        $this->manager = \core\di::get(\core_ai\manager::class);
        $config = ['data' => 'goeshere'];
        $this->provider = $this->manager->create_provider_instance(
                classname: '\aiprovider_openai\provider',
                name: 'dummy',
                config: $config,
        );
    }

    /**
     * Test get_contexts_for_userid().
     */
    public function test_get_contexts_for_userid(): void {
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $course1context = \context_course::instance($course1->id);
        $course2context = \context_course::instance($course2->id);
        $syscontext = \context_system::instance();

        // AI policy.
        // Set the user policy.
        manager::user_policy_accepted($user1->id, $course1context->id);

        // Retrieve the user1's context ids.
        $contextids = provider::get_contexts_for_userid($user1->id);
        $this->assertNotEmpty($contextids->get_contextids());
        $this->assertCount(1, $contextids->get_contextids());
        $this->assertTrue(in_array($course1context->id, $contextids->get_contextids()));
        // Retrieve the user2's context ids.
        $contextids = provider::get_contexts_for_userid($user2->id);
        $this->assertEmpty($contextids->get_contextids());

        // AI generate text.
        $action = new generate_text(
            contextid: $course2context->id,
            userid: $user1->id,
            prompttext: 'This is a test prompt',
        );

        $body = [
            'id' => 'chatcmpl-123',
            'fingerprint' => 'fp_44709d6fcb',
            'generatedcontent' => 'This is the generated content',
            'finishreason' => 'stop',
            'prompttokens' => 9,
            'completiontokens' => 12,
            'model' => 'gpt-4o',
        ];
        $actionresponse = new response_generate_text(
            success: true,
        );
        $actionresponse->set_response_data($body);

        $method = new \ReflectionMethod($this->manager, 'store_action_result');
        $method->invoke($this->manager, $this->provider, $action, $actionresponse);

        // Retrieve the user1's context ids.
        $contextids = provider::get_contexts_for_userid($user1->id);
        $this->assertNotEmpty($contextids->get_contextids());
        $this->assertCount(2, $contextids->get_contextids());
        $this->assertTrue(in_array($course1context->id, $contextids->get_contextids()));
        $this->assertTrue(in_array($course2context->id, $contextids->get_contextids()));

        // AI generate image.
        $action = new generate_image(
            contextid: $syscontext->id,
            userid: $user2->id,
            prompttext: 'This is a test prompt',
            quality: 'hd',
            aspectratio: 'square',
            numimages: 1,
            style: 'vivid',
        );

        $body = [
            'revisedprompt' => 'This is a revised prompt',
            'sourceurl' => 'https://example.com/image.png',
            'model' => 'dall-e-3',
        ];
        $actionresponse = new response_generate_image(
            success: true,
        );
        $actionresponse->set_response_data($body);

        $method = new \ReflectionMethod($this->manager, 'store_action_result');
        $method->invoke($this->manager, $this->provider, $action, $actionresponse);

        // Retrieve the user1's context ids.
        $contextids = provider::get_contexts_for_userid($user1->id);
        $this->assertNotEmpty($contextids->get_contextids());
        $this->assertCount(2, $contextids->get_contextids());
        $this->assertTrue(in_array($course1context->id, $contextids->get_contextids()));
        $this->assertTrue(in_array($course2context->id, $contextids->get_contextids()));

        // Retrieve the user2's context ids.
        $contextids = provider::get_contexts_for_userid($user2->id);
        $this->assertNotEmpty($contextids->get_contextids());
        $this->assertCount(1, $contextids->get_contextids());
        $this->assertFalse(in_array($course2context->id, $contextids->get_contextids()));
        $this->assertTrue(in_array($syscontext->id, $contextids->get_contextids()));

        // AI summarise text.
        $action = new summarise_text(
            contextid: $course2context->id,
            userid: $user2->id,
            prompttext: 'This is a test prompt',
        );

        $body = [
            'id' => 'chatcmpl-123',
            'fingerprint' => 'fp_44709d6fcb',
            'generatedcontent' => 'This is the generated content',
            'finishreason' => 'stop',
            'prompttokens' => 9,
            'completiontokens' => 12,
            'model' => 'gpt-4o',
        ];
        $actionresponse = new response_generate_text(
            success: true,
        );
        $actionresponse->set_response_data($body);

        $method = new \ReflectionMethod($this->manager, 'store_action_result');
        $method->invoke($this->manager, $this->provider, $action, $actionresponse);

        // Retrieve the user2's context ids.
        $contextids = provider::get_contexts_for_userid($user2->id);
        $this->assertNotEmpty($contextids->get_contextids());
        $this->assertCount(2, $contextids->get_contextids());
        $this->assertTrue(in_array($course2context->id, $contextids->get_contextids()));
        $this->assertTrue(in_array($syscontext->id, $contextids->get_contextids()));
    }

    /**
     * Test export_user_data() for AI policy.
     */
    public function test_export_user_data_for_policy(): void {
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $coursecontext = \context_course::instance($course->id);

        // Set the user policy.
        manager::user_policy_accepted($user->id, $coursecontext->id);

        // Retrieve the user's context ids.
        $contextlist = provider::get_contexts_for_userid($user->id);
        $approvedcontextlist = new approved_contextlist($user, 'core_ai', $contextlist->get_contextids());
        provider::export_user_data($approvedcontextlist);

        foreach ($contextlist as $context) {
            /** @var content_writer $writer */
            $writer = writer::with_context($context);
            $this->assertTrue($writer->has_any_data());
            $subcontexts = [
                get_string('ai', 'core_ai'),
            ];
            $name = 'policy';
            $data = $writer->get_related_data($subcontexts, $name);
            $this->assertEquals($coursecontext->id, $data->contextid);
        }
    }

    /**
     * Test export_user_data() for generate text.
     */
    public function test_export_user_data_for_generate_text(): void {
        $user = $this->getDataGenerator()->create_user();
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $course1context = \context_course::instance($course1->id);
        $course2context = \context_course::instance($course2->id);
        $clock = $this->mock_clock_with_frozen();

        $timecreated1 = $clock->time();
        $action = new generate_text(
            contextid: $course1context->id,
            userid: $user->id,
            prompttext: 'This is a test prompt 1',
        );

        $body = [
            'id' => 'chatcmpl-123',
            'fingerprint' => 'fp_44709d6fcb',
            'generatedcontent' => 'This is the generated content 1',
            'finishreason' => 'stop',
            'prompttokens' => 9,
            'completiontokens' => 12,
            'model' => 'gpt-4o',
        ];
        $actionresponse = new response_generate_text(
            success: true,
        );
        $actionresponse->set_response_data($body);

        $method = new \ReflectionMethod($this->manager, 'store_action_result');
        $method->invoke($this->manager, $this->provider, $action, $actionresponse);

        $clock->bump(2);
        $timecreated2 = $clock->time();
        $action = new generate_text(
            contextid: $course2context->id,
            userid: $user->id,
            prompttext: 'This is a test prompt 2',
        );

        $body = [
            'id' => 'chatcmpl-123',
            'fingerprint' => 'fp_44709d6fcb',
            'generatedcontent' => 'This is the generated content 2',
            'finishreason' => 'stop',
            'prompttokens' => 10,
            'completiontokens' => 15,
            'model' => 'gpt-4o',
        ];
        $actionresponse = new response_generate_text(
            success: true,
        );
        $actionresponse->set_response_data($body);
        $method->invoke($this->manager, $this->provider, $action, $actionresponse);

        // Retrieve the user's context ids.
        $contextlist = provider::get_contexts_for_userid($user->id);
        $approvedcontextlist = new approved_contextlist($user, 'core_ai', $contextlist->get_contextids());
        provider::export_user_data($approvedcontextlist);

        foreach ($contextlist as $context) {
            /** @var content_writer $writer */
            $writer = writer::with_context($context);
            $this->assertTrue($writer->has_any_data());

            if ($context->instanceid == $course1context->instanceid) {
                $subcontexts = [
                    get_string('ai', 'core_ai'),
                    get_string('action_generate_text', 'core_ai'),
                    date('c', $timecreated1),
                ];
                $name = 'action_generate_text';
                $data = $writer->get_related_data($subcontexts, $name);
                $this->assertEquals('generate_text', $data->actionname);
                $this->assertEquals($course1context->id, $data->contextid);
                $this->assertEquals('This is a test prompt 1', $data->prompt);
                $this->assertEquals('This is the generated content 1', $data->generatedcontent);
                $this->assertEquals('9', $data->prompttokens);
                $this->assertEquals('12', $data->completiontoken);
                $this->assertEquals(get_string('yes'), $data->success);
                $this->assertEquals('gpt-4o', $data->model);
            }

            if ($context->instanceid == $course2context->instanceid) {
                $subcontexts = [
                    get_string('ai', 'core_ai'),
                    get_string('action_generate_text', 'core_ai'),
                    date('c', $timecreated2),
                ];
                $name = 'action_generate_text';
                $data = $writer->get_related_data($subcontexts, $name);
                $this->assertEquals('generate_text', $data->actionname);
                $this->assertEquals($course2context->id, $data->contextid);
                $this->assertEquals('This is a test prompt 2', $data->prompt);
                $this->assertEquals('This is the generated content 2', $data->generatedcontent);
                $this->assertEquals('10', $data->prompttokens);
                $this->assertEquals('15', $data->completiontoken);
                $this->assertEquals(get_string('yes'), $data->success);
                $this->assertEquals('gpt-4o', $data->model);
            }
        }
    }

    /**
     * Test export_user_data() for generate image.
     */
    public function test_export_user_data_for_generate_image(): void {
        $user = $this->getDataGenerator()->create_user();
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $course1context = \context_course::instance($course1->id);
        $course2context = \context_course::instance($course2->id);
        $clock = $this->mock_clock_with_frozen();

        $timecreated1 = $clock->time();
        $action = new generate_image(
            contextid: $course1context->id,
            userid: $user->id,
            prompttext: 'This is a test prompt 1',
            quality: 'hd',
            aspectratio: 'square',
            numimages: 1,
            style: 'vivid',
        );

        $body = [
            'revisedprompt' => 'This is a revised prompt',
            'sourceurl' => 'https://example.com/image1.png',
            'model' => 'dall-e-3',
        ];
        $actionresponse = new response_generate_image(
            success: true,
        );
        $actionresponse->set_response_data($body);

        $method = new \ReflectionMethod($this->manager, 'store_action_result');
        $method->invoke($this->manager, $this->provider, $action, $actionresponse);

        $clock->bump(2);
        $timecreated2 = $clock->time();
        $action = new generate_image(
            contextid: $course2context->id,
            userid: $user->id,
            prompttext: 'This is a test prompt 2',
            quality: 'hd',
            aspectratio: 'portrait',
            numimages: 2,
            style: 'vivid',
        );

        $body = [
            'revisedprompt' => 'This is a revised prompt',
            'sourceurl' => 'https://example.com/image2.png',
            'model' => 'dall-e-3',
        ];
        $actionresponse = new response_generate_image(
            success: true,
        );
        $actionresponse->set_response_data($body);
        $method->invoke($this->manager, $this->provider, $action, $actionresponse);

        // Retrieve the user's context ids.
        $contextlist = provider::get_contexts_for_userid($user->id);
        $approvedcontextlist = new approved_contextlist($user, 'core_ai', $contextlist->get_contextids());
        provider::export_user_data($approvedcontextlist);

        foreach ($contextlist as $context) {
            /** @var content_writer $writer */
            $writer = writer::with_context($context);
            $this->assertTrue($writer->has_any_data());

            if ($context->instanceid == $course1context->instanceid) {
                $subcontexts = [
                    get_string('ai', 'core_ai'),
                    get_string('action_generate_image', 'core_ai'),
                    date('c', $timecreated1),
                ];
                $name = 'action_generate_image';
                $data = $writer->get_related_data($subcontexts, $name);
                $this->assertEquals('generate_image', $data->actionname);
                $this->assertEquals($course1context->id, $data->contextid);
                $this->assertEquals('This is a test prompt 1', $data->prompt);
                $this->assertEquals('1', $data->numberimages);
                $this->assertEquals('hd', $data->quality);
                $this->assertEquals('square', $data->aspectratio);
                $this->assertEquals('vivid', $data->style);
                $this->assertEquals('https://example.com/image1.png', $data->sourceurl);
                $this->assertEquals(get_string('yes'), $data->success);
                $this->assertEquals('dall-e-3', $data->model);
            }

            if ($context->instanceid == $course2context->instanceid) {
                $subcontexts = [
                    get_string('ai', 'core_ai'),
                    get_string('action_generate_image', 'core_ai'),
                    date('c', $timecreated2),
                ];
                $name = 'action_generate_image';
                $data = $writer->get_related_data($subcontexts, $name);
                $this->assertEquals('generate_image', $data->actionname);
                $this->assertEquals($course2context->id, $data->contextid);
                $this->assertEquals('This is a test prompt 2', $data->prompt);
                $this->assertEquals('2', $data->numberimages);
                $this->assertEquals('hd', $data->quality);
                $this->assertEquals('portrait', $data->aspectratio);
                $this->assertEquals('vivid', $data->style);
                $this->assertEquals('https://example.com/image2.png', $data->sourceurl);
                $this->assertEquals(get_string('yes'), $data->success);
                $this->assertEquals('dall-e-3', $data->model);
            }
        }
    }

    /**
     * Test export_user_data() for summarise text.
     */
    public function test_export_user_data_for_summarise_text(): void {
        $user = $this->getDataGenerator()->create_user();
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $course1context = \context_course::instance($course1->id);
        $course2context = \context_course::instance($course2->id);
        $clock = $this->mock_clock_with_frozen();

        $timecreated1 = $clock->time();
        $action = new summarise_text(
            contextid: $course1context->id,
            userid: $user->id,
            prompttext: 'This is a test prompt 1',
        );

        $body = [
            'id' => 'chatcmpl-123',
            'fingerprint' => 'fp_44709d6fcb',
            'generatedcontent' => 'This is the generated content 1',
            'finishreason' => 'stop',
            'prompttokens' => 9,
            'completiontokens' => 12,
            'model' => 'gpt-4o',
        ];
        $actionresponse = new response_summarise_text(
            success: true,
        );
        $actionresponse->set_response_data($body);

        $method = new \ReflectionMethod($this->manager, 'store_action_result');
        $method->invoke($this->manager, $this->provider, $action, $actionresponse);

        $clock->bump(2);
        $timecreated2 = $clock->time();
        $action = new summarise_text(
            contextid: $course2context->id,
            userid: $user->id,
            prompttext: 'This is a test prompt 2',
        );

        $body = [
            'id' => 'chatcmpl-123',
            'fingerprint' => 'fp_44709d6fcb',
            'generatedcontent' => 'This is the generated content 2',
            'finishreason' => 'stop',
            'prompttokens' => 10,
            'completiontokens' => 15,
            'model' => 'gpt-4o',
        ];
        $actionresponse = new response_summarise_text(
            success: true,
        );
        $actionresponse->set_response_data($body);
        $method->invoke($this->manager, $this->provider, $action, $actionresponse);

        // Retrieve the user's context ids.
        $contextlist = provider::get_contexts_for_userid($user->id);
        $approvedcontextlist = new approved_contextlist($user, 'core_ai', $contextlist->get_contextids());
        provider::export_user_data($approvedcontextlist);

        foreach ($contextlist as $context) {
            /** @var content_writer $writer */
            $writer = writer::with_context($context);
            $this->assertTrue($writer->has_any_data());

            if ($context->instanceid == $course1context->instanceid) {
                $subcontexts = [
                    get_string('ai', 'core_ai'),
                    get_string('action_summarise_text', 'core_ai'),
                    date('c', $timecreated1),
                ];
                $name = 'action_summarise_text';
                $data = $writer->get_related_data($subcontexts, $name);
                $this->assertEquals('summarise_text', $data->actionname);
                $this->assertEquals($course1context->id, $data->contextid);
                $this->assertEquals('This is a test prompt 1', $data->prompt);
                $this->assertEquals('This is the generated content 1', $data->generatedcontent);
                $this->assertEquals('9', $data->prompttokens);
                $this->assertEquals('12', $data->completiontoken);
                $this->assertEquals(get_string('yes'), $data->success);
                $this->assertEquals('gpt-4o', $data->model);
            }

            if ($context->instanceid == $course2context->instanceid) {
                $subcontexts = [
                    get_string('ai', 'core_ai'),
                    get_string('action_summarise_text', 'core_ai'),
                    date('c', $timecreated2),
                ];
                $name = 'action_summarise_text';
                $data = $writer->get_related_data($subcontexts, $name);
                $this->assertEquals('summarise_text', $data->actionname);
                $this->assertEquals($course2context->id, $data->contextid);
                $this->assertEquals('This is a test prompt 2', $data->prompt);
                $this->assertEquals('This is the generated content 2', $data->generatedcontent);
                $this->assertEquals('10', $data->prompttokens);
                $this->assertEquals('15', $data->completiontoken);
                $this->assertEquals(get_string('yes'), $data->success);
                $this->assertEquals('gpt-4o', $data->model);
            }
        }
    }

    /**
     * Test delete_data_for_all_users_in_context() for AI policy.
     */
    public function test_delete_data_for_all_users_in_context_for_policy(): void {
        global $DB;
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $course1context = \context_course::instance($course1->id);
        $course2context = \context_course::instance($course2->id);

        // Set the user policy.
        manager::user_policy_accepted($user1->id, $course1context->id);
        manager::user_policy_accepted($user2->id, $course1context->id);
        manager::user_policy_accepted($user3->id, $course2context->id);

        provider::delete_data_for_all_users_in_context($course1context);

        // Verify all policy data for Course 1 has been deleted.
        $datas = $DB->get_records('ai_policy_register', ['contextid' => $course1context->id]);
        $this->assertCount(0, $datas);

        // Verify policy data for Course 2 are still present.
        $datas = $DB->get_records('ai_policy_register', ['contextid' => $course2context->id]);
        $this->assertCount(1, $datas);
    }

    /**
     * Test delete_data_for_all_users_in_context() for generate text.
     */
    public function test_delete_data_for_all_users_in_context_for_generate_text(): void {
        global $DB;
        $user = $this->getDataGenerator()->create_user();
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $course1context = \context_course::instance($course1->id);
        $course2context = \context_course::instance($course2->id);
        $clock = $this->mock_clock_with_frozen();

        $action = new generate_text(
            contextid: $course1context->id,
            userid: $user->id,
            prompttext: 'This is a test prompt 1',
        );

        $body = [
            'id' => 'chatcmpl-123',
            'fingerprint' => 'fp_44709d6fcb',
            'generatedcontent' => 'This is the generated content 1',
            'finishreason' => 'stop',
            'prompttokens' => 9,
            'completiontokens' => 12,
            'model' => 'gpt-4o',
        ];
        $actionresponse = new response_generate_text(
            success: true,
        );
        $actionresponse->set_response_data($body);

        $method = new \ReflectionMethod($this->manager, 'store_action_result');
        $storeresult1 = $method->invoke($this->manager, $this->provider, $action, $actionresponse);

        $clock->bump(2);
        $action = new generate_text(
            contextid: $course2context->id,
            userid: $user->id,
            prompttext: 'This is a test prompt 2',
        );

        $body = [
            'id' => 'chatcmpl-123',
            'fingerprint' => 'fp_44709d6fcb',
            'generatedcontent' => 'This is the generated content 2',
            'finishreason' => 'stop',
            'prompttokens' => 10,
            'completiontokens' => 15,
            'model' => 'gpt-4o',
        ];
        $actionresponse = new response_generate_text(
            success: true,
        );
        $actionresponse->set_response_data($body);
        $method = new \ReflectionMethod($this->manager, 'store_action_result');
        $storeresult2 = $method->invoke($this->manager, $this->provider, $action, $actionresponse);

        provider::delete_data_for_all_users_in_context($course1context);

        $actionid1 = $DB->get_field('ai_action_register', 'actionid', ['id' => $storeresult1]);
        $actionid2 = $DB->get_field('ai_action_register', 'actionid', ['id' => $storeresult2]);

        $record = $DB->get_record('ai_action_generate_text', ['id' => $actionid1]);
        $this->assertEquals('', $record->prompt);
        $this->assertEquals('', $record->responseid);
        $this->assertEquals('', $record->fingerprint);
        $this->assertEquals('', $record->generatedcontent);

        $record = $DB->get_record('ai_action_generate_text', ['id' => $actionid2]);
        $this->assertNotEquals('', $record->prompt);
        $this->assertNotEquals('', $record->responseid);
        $this->assertNotEquals('', $record->fingerprint);
        $this->assertNotEquals('', $record->generatedcontent);
    }

    /**
     * Test delete_data_for_all_users_in_context() for generate image.
     */
    public function test_delete_data_for_all_users_in_context_for_generate_image(): void {
        global $DB;
        $user = $this->getDataGenerator()->create_user();
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $course1context = \context_course::instance($course1->id);
        $course2context = \context_course::instance($course2->id);
        $clock = $this->mock_clock_with_frozen();

        $action = new generate_image(
            contextid: $course1context->id,
            userid: $user->id,
            prompttext: 'This is a test prompt 1',
            quality: 'hd',
            aspectratio: 'square',
            numimages: 1,
            style: 'vivid',
        );

        $body = [
            'revisedprompt' => 'This is a revised prompt',
            'sourceurl' => 'https://example.com/image1.png',
            'model' => 'dall-e-3',
        ];
        $actionresponse = new response_generate_image(
            success: true,
        );
        $actionresponse->set_response_data($body);

        $method = new \ReflectionMethod($this->manager, 'store_action_result');
        $storeresult1 = $method->invoke($this->manager, $this->provider, $action, $actionresponse);

        $clock->bump(2);
        $action = new generate_image(
            contextid: $course2context->id,
            userid: $user->id,
            prompttext: 'This is a test prompt 2',
            quality: 'hd',
            aspectratio: 'square',
            numimages: 1,
            style: 'vivid',
        );

        $body = [
            'revisedprompt' => 'This is a revised prompt',
            'sourceurl' => 'https://example.com/image2.png',
            'model' => 'dall-e-3',
        ];
        $actionresponse = new response_generate_image(
            success: true,
        );
        $actionresponse->set_response_data($body);
        $method = new \ReflectionMethod($this->manager, 'store_action_result');
        $storeresult2 = $method->invoke($this->manager, $this->provider, $action, $actionresponse);

        provider::delete_data_for_all_users_in_context($course1context);

        $actionid1 = $DB->get_field('ai_action_register', 'actionid', ['id' => $storeresult1]);
        $actionid2 = $DB->get_field('ai_action_register', 'actionid', ['id' => $storeresult2]);

        $record = $DB->get_record('ai_action_generate_image', ['id' => $actionid1]);
        $this->assertEquals('', $record->prompt);
        $this->assertEquals('', $record->sourceurl);
        $this->assertEquals('', $record->revisedprompt);

        $record = $DB->get_record('ai_action_generate_image', ['id' => $actionid2]);
        $this->assertNotEquals('', $record->prompt);
        $this->assertNotEquals('', $record->sourceurl);
        $this->assertNotEquals('', $record->revisedprompt);
    }

    /**
     * Test delete_data_for_all_users_in_context() for summarise text.
     */
    public function test_delete_data_for_all_users_in_context_for_summarise_text(): void {
        global $DB;
        $user = $this->getDataGenerator()->create_user();
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $course1context = \context_course::instance($course1->id);
        $course2context = \context_course::instance($course2->id);
        $clock = $this->mock_clock_with_frozen();

        $action = new summarise_text(
            contextid: $course1context->id,
            userid: $user->id,
            prompttext: 'This is a test prompt 1',
        );

        $body = [
            'id' => 'chatcmpl-123',
            'fingerprint' => 'fp_44709d6fcb',
            'generatedcontent' => 'This is the generated content 1',
            'finishreason' => 'stop',
            'prompttokens' => 9,
            'completiontokens' => 12,
            'model' => 'gpt-4o',
        ];
        $actionresponse = new response_summarise_text(
            success: true,
        );
        $actionresponse->set_response_data($body);

        $method = new \ReflectionMethod($this->manager, 'store_action_result');
        $storeresult1 = $method->invoke($this->manager, $this->provider, $action, $actionresponse);

        $clock->bump(2);
        $action = new summarise_text(
            contextid: $course2context->id,
            userid: $user->id,
            prompttext: 'This is a test prompt 2',
        );

        $body = [
            'id' => 'chatcmpl-123',
            'fingerprint' => 'fp_44709d6fcb',
            'generatedcontent' => 'This is the generated content 2',
            'finishreason' => 'stop',
            'prompttokens' => 10,
            'completiontokens' => 15,
            'model' => 'gpt-4o',
        ];
        $actionresponse = new response_summarise_text(
            success: true,
        );
        $actionresponse->set_response_data($body);
        $method = new \ReflectionMethod($this->manager, 'store_action_result');
        $storeresult2 = $method->invoke($this->manager, $this->provider, $action, $actionresponse);

        provider::delete_data_for_all_users_in_context($course1context);

        $actionid1 = $DB->get_field('ai_action_register', 'actionid', ['id' => $storeresult1]);
        $actionid2 = $DB->get_field('ai_action_register', 'actionid', ['id' => $storeresult2]);

        $record = $DB->get_record('ai_action_summarise_text', ['id' => $actionid1]);
        $this->assertEquals('', $record->prompt);
        $this->assertEquals('', $record->responseid);
        $this->assertEquals('', $record->fingerprint);
        $this->assertEquals('', $record->generatedcontent);

        $record = $DB->get_record('ai_action_summarise_text', ['id' => $actionid2]);
        $this->assertNotEquals('', $record->prompt);
        $this->assertNotEquals('', $record->responseid);
        $this->assertNotEquals('', $record->fingerprint);
        $this->assertNotEquals('', $record->generatedcontent);
    }

    /**
     * Test delete_data_for_all_users_in_context() for generate text.
     */
    public function test_delete_data_for_user_for_generate_text(): void {
        global $DB;
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $coursecontext = \context_course::instance($course->id);
        $clock = $this->mock_clock_with_frozen();

        $action = new generate_text(
            contextid: $coursecontext->id,
            userid: $user1->id,
            prompttext: 'This is a test prompt 1',
        );

        $body = [
            'id' => 'chatcmpl-123',
            'fingerprint' => 'fp_44709d6fcb',
            'generatedcontent' => 'This is the generated content 1',
            'finishreason' => 'stop',
            'prompttokens' => 9,
            'completiontokens' => 12,
            'model' => 'gpt-4o',
        ];
        $actionresponse = new response_generate_text(
            success: true,
        );
        $actionresponse->set_response_data($body);

        $method = new \ReflectionMethod($this->manager, 'store_action_result');
        $storeresult1 = $method->invoke($this->manager, $this->provider, $action, $actionresponse);

        $clock->bump(2);
        $action = new generate_text(
            contextid: $coursecontext->id,
            userid: $user2->id,
            prompttext: 'This is a test prompt 2',
        );

        $body = [
            'id' => 'chatcmpl-123',
            'fingerprint' => 'fp_44709d6fcb',
            'generatedcontent' => 'This is the generated content 2',
            'finishreason' => 'stop',
            'prompttokens' => 10,
            'completiontokens' => 15,
            'model' => 'gpt-4o',
        ];
        $actionresponse = new response_generate_text(
            success: true,
        );
        $actionresponse->set_response_data($body);
        $method = new \ReflectionMethod($this->manager, 'store_action_result');
        $storeresult2 = $method->invoke($this->manager, $this->provider, $action, $actionresponse);

        $contextlist = provider::get_contexts_for_userid($user1->id);
        $approvedcontextlist = new approved_contextlist($user1, 'core_ai', $contextlist->get_contextids());
        provider::delete_data_for_user($approvedcontextlist);

        $actionid1 = $DB->get_field('ai_action_register', 'actionid', ['id' => $storeresult1]);
        $actionid2 = $DB->get_field('ai_action_register', 'actionid', ['id' => $storeresult2]);

        $record = $DB->get_record('ai_action_generate_text', ['id' => $actionid1]);
        $this->assertEquals('', $record->prompt);
        $this->assertEquals('', $record->responseid);
        $this->assertEquals('', $record->fingerprint);
        $this->assertEquals('', $record->generatedcontent);

        $record = $DB->get_record('ai_action_generate_text', ['id' => $actionid2]);
        $this->assertNotEquals('', $record->prompt);
        $this->assertNotEquals('', $record->responseid);
        $this->assertNotEquals('', $record->fingerprint);
        $this->assertNotEquals('', $record->generatedcontent);
    }

    /**
     * Test delete_data_for_all_users_in_context() for generate image.
     */
    public function test_delete_data_for_user_for_generate_image(): void {
        global $DB;
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $coursecontext = \context_course::instance($course->id);
        $clock = $this->mock_clock_with_frozen();

        $action = new generate_image(
            contextid: $coursecontext->id,
            userid: $user1->id,
            prompttext: 'This is a test prompt 1',
            quality: 'hd',
            aspectratio: 'square',
            numimages: 1,
            style: 'vivid',
        );

        $body = [
            'revisedprompt' => 'This is a revised prompt',
            'sourceurl' => 'https://example.com/image1.png',
            'model' => 'dall-e-3',
        ];
        $actionresponse = new response_generate_image(
            success: true,
        );
        $actionresponse->set_response_data($body);

        $method = new \ReflectionMethod($this->manager, 'store_action_result');
        $storeresult1 = $method->invoke($this->manager, $this->provider, $action, $actionresponse);

        $clock->bump(2);
        $action = new generate_image(
            contextid: $coursecontext->id,
            userid: $user2->id,
            prompttext: 'This is a test prompt 2',
            quality: 'hd',
            aspectratio: 'square',
            numimages: 1,
            style: 'vivid',
        );

        $body = [
            'revisedprompt' => 'This is a revised prompt',
            'sourceurl' => 'https://example.com/image2.png',
            'model' => 'dall-e-3',
        ];
        $actionresponse = new response_generate_image(
            success: true,
        );
        $actionresponse->set_response_data($body);
        $method = new \ReflectionMethod($this->manager, 'store_action_result');
        $storeresult2 = $method->invoke($this->manager, $this->provider, $action, $actionresponse);

        $contextlist = provider::get_contexts_for_userid($user1->id);
        $approvedcontextlist = new approved_contextlist($user1, 'core_ai', $contextlist->get_contextids());
        provider::delete_data_for_user($approvedcontextlist);

        $actionid1 = $DB->get_field('ai_action_register', 'actionid', ['id' => $storeresult1]);
        $actionid2 = $DB->get_field('ai_action_register', 'actionid', ['id' => $storeresult2]);

        $record = $DB->get_record('ai_action_generate_image', ['id' => $actionid1]);
        $this->assertEquals('', $record->prompt);
        $this->assertEquals('', $record->sourceurl);
        $this->assertEquals('', $record->revisedprompt);

        $record = $DB->get_record('ai_action_generate_image', ['id' => $actionid2]);
        $this->assertNotEquals('', $record->prompt);
        $this->assertNotEquals('', $record->sourceurl);
        $this->assertNotEquals('', $record->revisedprompt);
    }

    /**
     * Test delete_data_for_all_users_in_context() for summarise text.
     */
    public function test_delete_data_for_user_for_summarise_text(): void {
        global $DB;
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $coursecontext = \context_course::instance($course->id);
        $clock = $this->mock_clock_with_frozen();

        $action = new summarise_text(
            contextid: $coursecontext->id,
            userid: $user1->id,
            prompttext: 'This is a test prompt 1',
        );

        $body = [
            'id' => 'chatcmpl-123',
            'fingerprint' => 'fp_44709d6fcb',
            'generatedcontent' => 'This is the generated content 1',
            'finishreason' => 'stop',
            'prompttokens' => 9,
            'completiontokens' => 12,
            'model' => 'gpt-4o',
        ];
        $actionresponse = new response_summarise_text(
            success: true,
        );
        $actionresponse->set_response_data($body);

        $method = new \ReflectionMethod($this->manager, 'store_action_result');
        $storeresult1 = $method->invoke($this->manager, $this->provider, $action, $actionresponse);

        $clock->bump(2);
        $action = new summarise_text(
            contextid: $coursecontext->id,
            userid: $user2->id,
            prompttext: 'This is a test prompt 2',
        );

        $body = [
            'id' => 'chatcmpl-123',
            'fingerprint' => 'fp_44709d6fcb',
            'generatedcontent' => 'This is the generated content 2',
            'finishreason' => 'stop',
            'prompttokens' => 10,
            'completiontokens' => 15,
            'model' => 'gpt-4o',
        ];
        $actionresponse = new response_summarise_text(
            success: true,
        );
        $actionresponse->set_response_data($body);
        $method = new \ReflectionMethod($this->manager, 'store_action_result');
        $storeresult2 = $method->invoke($this->manager, $this->provider, $action, $actionresponse);

        $contextlist = provider::get_contexts_for_userid($user1->id);
        $approvedcontextlist = new approved_contextlist($user1, 'core_ai', $contextlist->get_contextids());
        provider::delete_data_for_user($approvedcontextlist);

        $actionid1 = $DB->get_field('ai_action_register', 'actionid', ['id' => $storeresult1]);
        $actionid2 = $DB->get_field('ai_action_register', 'actionid', ['id' => $storeresult2]);

        $record = $DB->get_record('ai_action_summarise_text', ['id' => $actionid1]);
        $this->assertEquals('', $record->prompt);
        $this->assertEquals('', $record->responseid);
        $this->assertEquals('', $record->fingerprint);
        $this->assertEquals('', $record->generatedcontent);

        $record = $DB->get_record('ai_action_summarise_text', ['id' => $actionid2]);
        $this->assertNotEquals('', $record->prompt);
        $this->assertNotEquals('', $record->responseid);
        $this->assertNotEquals('', $record->fingerprint);
        $this->assertNotEquals('', $record->generatedcontent);
    }

    /**
     * Test get_users_in_context() for policy.
     */
    public function test_get_users_in_context_for_policy(): void {
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $course1context = \context_course::instance($course1->id);
        $course2context = \context_course::instance($course2->id);

        manager::user_policy_accepted($user1->id, $course1context->id);
        manager::user_policy_accepted($user2->id, $course2context->id);

        // The user list for course1context should return user1.
        $userlist = new \core_privacy\local\request\userlist($course1context, 'core_ai');
        provider::get_users_in_context($userlist);
        $this->assertCount(1, $userlist);
        $this->assertTrue(in_array($user1->id, $userlist->get_userids()));
        $this->assertFalse(in_array($user2->id, $userlist->get_userids()));

        // The user list for course2context should return user2.
        $userlist = new \core_privacy\local\request\userlist($course2context, 'core_ai');
        provider::get_users_in_context($userlist);
        $this->assertCount(1, $userlist);
        $this->assertTrue(in_array($user2->id, $userlist->get_userids()));
        $this->assertFalse(in_array($user1->id, $userlist->get_userids()));
    }

    /**
     * Test get_users_in_context() for generate text.
     */
    public function test_get_users_in_context_for_generate_text(): void {
        global $DB;
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $course1context = \context_course::instance($course1->id);
        $course2context = \context_course::instance($course2->id);
        $clock = $this->mock_clock_with_frozen();

        $action = new generate_text(
            contextid: $course1context->id,
            userid: $user1->id,
            prompttext: 'This is a test prompt 1',
        );

        $body = [
            'id' => 'chatcmpl-123',
            'fingerprint' => 'fp_44709d6fcb',
            'generatedcontent' => 'This is the generated content 1',
            'finishreason' => 'stop',
            'prompttokens' => 9,
            'completiontokens' => 12,
            'model' => 'gpt-4o',
        ];
        $actionresponse = new response_generate_text(
            success: true,
        );
        $actionresponse->set_response_data($body);

        $method = new \ReflectionMethod($this->manager, 'store_action_result');
        $method->invoke($this->manager, $this->provider, $action, $actionresponse);

        $clock->bump(2);
        $action = new generate_text(
            contextid: $course2context->id,
            userid: $user2->id,
            prompttext: 'This is a test prompt 2',
        );

        $body = [
            'id' => 'chatcmpl-123',
            'fingerprint' => 'fp_44709d6fcb',
            'generatedcontent' => 'This is the generated content 2',
            'finishreason' => 'stop',
            'prompttokens' => 10,
            'completiontokens' => 15,
            'model' => 'gpt-4o',
        ];
        $actionresponse = new response_generate_text(
            success: true,
        );
        $actionresponse->set_response_data($body);
        $method = new \ReflectionMethod($this->manager, 'store_action_result');
        $method->invoke($this->manager, $this->provider, $action, $actionresponse);

        // The user list for course1context should return user1.
        $userlist = new \core_privacy\local\request\userlist($course1context, 'core_ai');
        provider::get_users_in_context($userlist);
        $this->assertCount(1, $userlist);
        $this->assertTrue(in_array($user1->id, $userlist->get_userids()));
        $this->assertFalse(in_array($user2->id, $userlist->get_userids()));

        // The user list for course2context should return user2.
        $userlist = new \core_privacy\local\request\userlist($course2context, 'core_ai');
        provider::get_users_in_context($userlist);
        $this->assertCount(1, $userlist);
        $this->assertTrue(in_array($user2->id, $userlist->get_userids()));
        $this->assertFalse(in_array($user1->id, $userlist->get_userids()));
    }

    /**
     * Test get_users_in_context() for generate image.
     */
    public function test_get_users_in_context_for_generate_image(): void {
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $course1context = \context_course::instance($course1->id);
        $course2context = \context_course::instance($course2->id);
        $clock = $this->mock_clock_with_frozen();

        $action = new generate_image(
            contextid: $course1context->id,
            userid: $user1->id,
            prompttext: 'This is a test prompt 1',
            quality: 'hd',
            aspectratio: 'square',
            numimages: 1,
            style: 'vivid',
        );

        $body = [
            'revisedprompt' => 'This is a revised prompt',
            'sourceurl' => 'https://example.com/image1.png',
            'model' => 'dall-e-3',
        ];
        $actionresponse = new response_generate_image(
            success: true,
        );
        $actionresponse->set_response_data($body);

        $method = new \ReflectionMethod($this->manager, 'store_action_result');
        $method->invoke($this->manager, $this->provider, $action, $actionresponse);

        $clock->bump(2);
        $action = new generate_image(
            contextid: $course2context->id,
            userid: $user2->id,
            prompttext: 'This is a test prompt 2',
            quality: 'hd',
            aspectratio: 'square',
            numimages: 1,
            style: 'vivid',
        );

        $body = [
            'revisedprompt' => 'This is a revised prompt',
            'sourceurl' => 'https://example.com/image2.png',
            'model' => 'dall-e-3',
        ];
        $actionresponse = new response_generate_image(
            success: true,
        );
        $actionresponse->set_response_data($body);
        $method = new \ReflectionMethod($this->manager, 'store_action_result');
        $method->invoke($this->manager, $this->provider, $action, $actionresponse);

        // The user list for course1context should return user1.
        $userlist = new \core_privacy\local\request\userlist($course1context, 'core_ai');
        provider::get_users_in_context($userlist);
        $this->assertCount(1, $userlist);
        $this->assertTrue(in_array($user1->id, $userlist->get_userids()));
        $this->assertFalse(in_array($user2->id, $userlist->get_userids()));

        // The user list for course2context should return user2.
        $userlist = new \core_privacy\local\request\userlist($course2context, 'core_ai');
        provider::get_users_in_context($userlist);
        $this->assertCount(1, $userlist);
        $this->assertTrue(in_array($user2->id, $userlist->get_userids()));
        $this->assertFalse(in_array($user1->id, $userlist->get_userids()));
    }

    /**
     * Test get_users_in_context() for summarise text.
     */
    public function test_get_users_in_context_for_summarise_text(): void {
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $course1context = \context_course::instance($course1->id);
        $course2context = \context_course::instance($course2->id);
        $clock = $this->mock_clock_with_frozen();

        $action = new summarise_text(
            contextid: $course1context->id,
            userid: $user1->id,
            prompttext: 'This is a test prompt 1',
        );

        $body = [
            'id' => 'chatcmpl-123',
            'fingerprint' => 'fp_44709d6fcb',
            'generatedcontent' => 'This is the generated content 1',
            'finishreason' => 'stop',
            'prompttokens' => 9,
            'completiontokens' => 12,
            'model' => 'gpt-4o',
        ];
        $actionresponse = new response_summarise_text(
            success: true,
        );
        $actionresponse->set_response_data($body);

        $method = new \ReflectionMethod($this->manager, 'store_action_result');
        $method->invoke($this->manager, $this->provider, $action, $actionresponse);

        $clock->bump(2);
        $action = new summarise_text(
            contextid: $course2context->id,
            userid: $user2->id,
            prompttext: 'This is a test prompt 2',
        );

        $body = [
            'id' => 'chatcmpl-123',
            'fingerprint' => 'fp_44709d6fcb',
            'generatedcontent' => 'This is the generated content 2',
            'finishreason' => 'stop',
            'prompttokens' => 10,
            'completiontokens' => 15,
            'model' => 'gpt-4o',
        ];
        $actionresponse = new response_summarise_text(
            success: true,
        );
        $actionresponse->set_response_data($body);
        $method = new \ReflectionMethod($this->manager, 'store_action_result');
        $method->invoke($this->manager, $this->provider, $action, $actionresponse);

        // The user list for course1context should return user1.
        $userlist = new \core_privacy\local\request\userlist($course1context, 'core_ai');
        provider::get_users_in_context($userlist);
        $this->assertCount(1, $userlist);
        $this->assertTrue(in_array($user1->id, $userlist->get_userids()));
        $this->assertFalse(in_array($user2->id, $userlist->get_userids()));

        // The user list for course2context should return user2.
        $userlist = new \core_privacy\local\request\userlist($course2context, 'core_ai');
        provider::get_users_in_context($userlist);
        $this->assertCount(1, $userlist);
        $this->assertTrue(in_array($user2->id, $userlist->get_userids()));
        $this->assertFalse(in_array($user1->id, $userlist->get_userids()));
    }

    /**
     * Test delete_data_for_users() for policy.
     */
    public function test_delete_data_for_users_for_policy(): void {
        global $DB;
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $course1context = \context_course::instance($course1->id);
        $course2context = \context_course::instance($course2->id);

        // Set the user policy.
        manager::user_policy_accepted($user1->id, $course1context->id);
        manager::user_policy_accepted($user2->id, $course1context->id);
        manager::user_policy_accepted($user3->id, $course2context->id);

        $userlist = new \core_privacy\local\request\userlist($course1context, 'core_ai');
        provider::get_users_in_context($userlist);
        $approvedlist = new approved_userlist($course1context, 'core_ai', $userlist->get_userids());
        provider::delete_data_for_users($approvedlist);

        // Verify all policy data for user1 and user2 have been deleted.
        $this->assertFalse($DB->record_exists('ai_policy_register', ['userid' => $user1->id]));
        $this->assertFalse($DB->record_exists('ai_policy_register', ['userid' => $user2->id]));

        // Verify policy data for user3 is still present.
        $this->assertTrue($DB->record_exists('ai_policy_register', ['userid' => $user3->id]));
    }

    /**
     * Test delete_data_for_users() for generate text.
     */
    public function test_delete_data_for_users_for_generate_text(): void {
        global $DB;
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $course1context = \context_course::instance($course1->id);
        $course2context = \context_course::instance($course2->id);
        $clock = $this->mock_clock_with_frozen();

        $action = new generate_text(
            contextid: $course1context->id,
            userid: $user1->id,
            prompttext: 'This is a test prompt 1',
        );

        $body = [
            'id' => 'chatcmpl-123',
            'fingerprint' => 'fp_44709d6fcb',
            'generatedcontent' => 'This is the generated content 1',
            'finishreason' => 'stop',
            'prompttokens' => 9,
            'completiontokens' => 12,
            'model' => 'gpt-4o',
        ];
        $actionresponse = new response_generate_text(
            success: true,
        );
        $actionresponse->set_response_data($body);

        $method = new \ReflectionMethod($this->manager, 'store_action_result');
        $storeresult1 = $method->invoke($this->manager, $this->provider, $action, $actionresponse);

        $clock->bump(2);
        $action = new generate_text(
            contextid: $course2context->id,
            userid: $user2->id,
            prompttext: 'This is a test prompt 2',
        );

        $body = [
            'id' => 'chatcmpl-123',
            'fingerprint' => 'fp_44709d6fcb',
            'generatedcontent' => 'This is the generated content 2',
            'finishreason' => 'stop',
            'prompttokens' => 10,
            'completiontokens' => 15,
            'model' => 'gpt-4o',
        ];
        $actionresponse = new response_generate_text(
            success: true,
        );
        $actionresponse->set_response_data($body);
        $method = new \ReflectionMethod($this->manager, 'store_action_result');
        $storeresult2 = $method->invoke($this->manager, $this->provider, $action, $actionresponse);

        $userlist = new \core_privacy\local\request\userlist($course1context, 'core_ai');
        provider::get_users_in_context($userlist);
        $approvedlist = new approved_userlist($course1context, 'core_ai', $userlist->get_userids());
        provider::delete_data_for_users($approvedlist);

        $actionid1 = $DB->get_field('ai_action_register', 'actionid', ['id' => $storeresult1]);
        $actionid2 = $DB->get_field('ai_action_register', 'actionid', ['id' => $storeresult2]);

        $record = $DB->get_record('ai_action_generate_text', ['id' => $actionid1]);
        $this->assertEquals('', $record->prompt);
        $this->assertEquals('', $record->responseid);
        $this->assertEquals('', $record->fingerprint);
        $this->assertEquals('', $record->generatedcontent);

        $record = $DB->get_record('ai_action_generate_text', ['id' => $actionid2]);
        $this->assertNotEquals('', $record->prompt);
        $this->assertNotEquals('', $record->responseid);
        $this->assertNotEquals('', $record->fingerprint);
        $this->assertNotEquals('', $record->generatedcontent);
    }

    /**
     * Test delete_data_for_users() for generate image.
     */
    public function test_delete_data_for_users_for_generate_image(): void {
        global $DB;
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $course1context = \context_course::instance($course1->id);
        $course2context = \context_course::instance($course2->id);
        $clock = $this->mock_clock_with_frozen();

        $action = new generate_image(
            contextid: $course1context->id,
            userid: $user1->id,
            prompttext: 'This is a test prompt 1',
            quality: 'hd',
            aspectratio: 'square',
            numimages: 1,
            style: 'vivid',
        );

        $body = [
            'revisedprompt' => 'This is a revised prompt',
            'sourceurl' => 'https://example.com/image1.png',
            'model' => 'dall-e-3',
        ];
        $actionresponse = new response_generate_image(
            success: true,
        );
        $actionresponse->set_response_data($body);

        $method = new \ReflectionMethod($this->manager, 'store_action_result');
        $storeresult1 = $method->invoke($this->manager, $this->provider, $action, $actionresponse);

        $clock->bump(2);
        $action = new generate_image(
            contextid: $course2context->id,
            userid: $user2->id,
            prompttext: 'This is a test prompt 2',
            quality: 'hd',
            aspectratio: 'square',
            numimages: 1,
            style: 'vivid',
        );

        $body = [
            'revisedprompt' => 'This is a revised prompt',
            'sourceurl' => 'https://example.com/image2.png',
            'model' => 'dall-e-3',
        ];
        $actionresponse = new response_generate_image(
            success: true,
        );
        $actionresponse->set_response_data($body);
        $method = new \ReflectionMethod($this->manager, 'store_action_result');
        $storeresult2 = $method->invoke($this->manager, $this->provider, $action, $actionresponse);

        $userlist = new \core_privacy\local\request\userlist($course1context, 'core_ai');
        provider::get_users_in_context($userlist);
        $approvedlist = new approved_userlist($course1context, 'core_ai', $userlist->get_userids());
        provider::delete_data_for_users($approvedlist);

        $actionid1 = $DB->get_field('ai_action_register', 'actionid', ['id' => $storeresult1]);
        $actionid2 = $DB->get_field('ai_action_register', 'actionid', ['id' => $storeresult2]);

        $record = $DB->get_record('ai_action_generate_image', ['id' => $actionid1]);
        $this->assertEquals('', $record->prompt);
        $this->assertEquals('', $record->sourceurl);
        $this->assertEquals('', $record->revisedprompt);

        $record = $DB->get_record('ai_action_generate_image', ['id' => $actionid2]);
        $this->assertNotEquals('', $record->prompt);
        $this->assertNotEquals('', $record->sourceurl);
        $this->assertNotEquals('', $record->revisedprompt);
    }

    /**
     * Test delete_data_for_users() for generate image.
     */
    public function test_delete_data_for_users_for_summarise_text(): void {
        global $DB;
        $user = $this->getDataGenerator()->create_user();
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $course1context = \context_course::instance($course1->id);
        $course2context = \context_course::instance($course2->id);
        $clock = $this->mock_clock_with_frozen();

        $action = new summarise_text(
            contextid: $course1context->id,
            userid: $user->id,
            prompttext: 'This is a test prompt 1',
        );

        $body = [
            'id' => 'chatcmpl-123',
            'fingerprint' => 'fp_44709d6fcb',
            'generatedcontent' => 'This is the generated content 1',
            'finishreason' => 'stop',
            'prompttokens' => 9,
            'completiontokens' => 12,
            'model' => 'gpt-4o',
        ];
        $actionresponse = new response_summarise_text(
            success: true,
        );
        $actionresponse->set_response_data($body);

        $method = new \ReflectionMethod($this->manager, 'store_action_result');
        $storeresult1 = $method->invoke($this->manager, $this->provider, $action, $actionresponse);

        $clock->bump(2);
        $action = new summarise_text(
            contextid: $course2context->id,
            userid: $user->id,
            prompttext: 'This is a test prompt 2',
        );

        $body = [
            'id' => 'chatcmpl-123',
            'fingerprint' => 'fp_44709d6fcb',
            'generatedcontent' => 'This is the generated content 2',
            'finishreason' => 'stop',
            'prompttokens' => 10,
            'completiontokens' => 15,
            'model' => 'gpt-4o',
        ];
        $actionresponse = new response_summarise_text(
            success: true,
        );
        $actionresponse->set_response_data($body);
        $method = new \ReflectionMethod($this->manager, 'store_action_result');
        $storeresult2 = $method->invoke($this->manager, $this->provider, $action, $actionresponse);

        $userlist = new \core_privacy\local\request\userlist($course1context, 'core_ai');
        provider::get_users_in_context($userlist);
        $approvedlist = new approved_userlist($course1context, 'core_ai', $userlist->get_userids());
        provider::delete_data_for_users($approvedlist);

        $actionid1 = $DB->get_field('ai_action_register', 'actionid', ['id' => $storeresult1]);
        $actionid2 = $DB->get_field('ai_action_register', 'actionid', ['id' => $storeresult2]);

        $record = $DB->get_record('ai_action_summarise_text', ['id' => $actionid1]);
        $this->assertEquals('', $record->prompt);
        $this->assertEquals('', $record->responseid);
        $this->assertEquals('', $record->fingerprint);
        $this->assertEquals('', $record->generatedcontent);

        $record = $DB->get_record('ai_action_summarise_text', ['id' => $actionid2]);
        $this->assertNotEquals('', $record->prompt);
        $this->assertNotEquals('', $record->responseid);
        $this->assertNotEquals('', $record->fingerprint);
        $this->assertNotEquals('', $record->generatedcontent);
    }

    /**
     * Test get_name.
     */
    public function test_get_name(): void {
        $this->assertEquals('aiprovider_openai', $this->provider->get_name());
    }

    /**
     * Test the is_request_allowed method of the provider abstract class.
     */
    public function test_is_request_allowed(): void {
        // Create action.
        $action1 = new summarise_text(
            contextid: 1,
            userid: 1,
            prompttext: 'This is a test prompt 1',
        );
        $action2 = new summarise_text(
            contextid: 1,
            userid: 2,
            prompttext: 'This is a test prompt 1',
        );

        // Create the provider instance.
        $config = [
            'enableuserratelimit' => true,
            'userratelimit' => 3,
            'enableglobalratelimit' => true,
            'globalratelimit' => 5,
        ];
        $provider = $this->manager->create_provider_instance(
                classname: '\aiprovider_openai\provider',
                name: 'dummy',
                config: $config,
        );
        // Make 3 requests for the first user, all should be allowed.
        for ($i = 0; $i < 3; $i++) {
            $this->assertTrue($provider->is_request_allowed($action1));
        }

        // The 4th request should be denied.
        $this->assertFalse($provider->is_request_allowed($action1)['success']);

        // Make 2 requests for the second user, all should be allowed.
        for ($i = 0; $i < 2; $i++) {
            $this->assertTrue($provider->is_request_allowed($action2));
        }

        // THe final request should be denied.
        $this->assertFalse($provider->is_request_allowed($action2)['success']);
    }
}
