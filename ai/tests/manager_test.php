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

namespace core_ai;

use core_ai\aiactions\generate_image;
use core_ai\aiactions\generate_text;
use core_ai\aiactions\summarise_text;
use core_ai\aiactions\responses\response_generate_image;

/**
 * Test ai subsystem manager methods.
 *
 * @package    core_ai
 * @copyright  2024 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core_ai\manager
 */
final class manager_test extends \advanced_testcase {
    /**
     * Test get_ai_plugin_classname.
     */
    public function test_get_ai_plugin_classname(): void {
        $manager = \core\di::get(manager::class);

        // We're working with a private method here, so we need to use reflection.
        $method = new \ReflectionMethod($manager, 'get_ai_plugin_classname');

        // Test a provider plugin.
        $classname = $method->invoke($manager, 'aiprovider_fooai');
        $this->assertEquals('aiprovider_fooai\\provider', $classname);

        // Test a placement plugin.
        $classname = $method->invoke($manager, 'aiplacement_fooplacement');
        $this->assertEquals('aiplacement_fooplacement\\placement', $classname);

        // Test an invalid plugin.
        $this->expectException(\coding_exception::class);
        $this->expectExceptionMessage('Plugin name does not start with \'aiprovider_\' or \'aiplacement_\': bar');
        $method->invoke($manager, 'bar');
    }

    /**
     * Test get_supported_actions.
     */
    public function test_get_supported_actions(): void {
        $manager = \core\di::get(manager::class);
        $actions = $manager->get_supported_actions('aiprovider_openai');

        // Assert array keys match the expected actions.
        $this->assertEquals([
            generate_text::class,
            generate_image::class,
            summarise_text::class,
        ], $actions);
    }

    /**
     * Test get_providers_for_actions.
     */
    public function test_get_providers_for_actions(): void {
        $this->resetAfterTest();
        set_config('enabled', 1, 'aiprovider_openai');
        set_config('apikey', '123', 'aiprovider_openai');

        $manager = \core\di::get(manager::class);
        $actions = [
            generate_text::class,
            summarise_text::class,
        ];

        // Get the providers for the actions.
        $providers = $manager->get_providers_for_actions($actions);

        // Assert that the providers array is indexed by action name.
        $this->assertEquals($actions, array_keys($providers));

        // Assert that there is only one provider for each action.
        $this->assertCount(2, $providers[generate_text::class]);
        $this->assertCount(2, $providers[summarise_text::class]);

        // Disable the generate text action for the Open AI provider.
        manager::set_action_state('aiprovider_openai', generate_text::class::get_basename(), 0);
        $providers = $manager->get_providers_for_actions($actions, true);

        // Assert that there is no provider for the generate text action.
        $this->assertCount(0, $providers[generate_text::class]);
        $this->assertCount(1, $providers[summarise_text::class]);
    }

    /**
     * Test process_action fail.
     */
    public function test_process_action_fail(): void {
        $this->resetAfterTest();
        $managermock = $this->getMockBuilder(manager::class)
            ->onlyMethods(['call_action_provider'])
            ->getMock();

        $expectedresult = new aiactions\responses\response_generate_image(
            success: true,
        );

        // Set up the expectation for call_action_provider to return the defined result.
        $managermock->expects($this->any())
            ->method('call_action_provider')
            ->willReturn($expectedresult);

        $action = new generate_image(
            contextid: 1,
            userid: 1,
            prompttext: 'This is a test prompt',
            quality: 'hd',
            aspectratio: 'square',
            numimages: 1,
            style: 'vivid',
        );

        // Success should be false as there are no enabled providers.
        $result = $managermock->process_action($action);
        $this->assertFalse($result->get_success());
    }

    /**
     * Test process_action.
     */
    public function test_process_action(): void {
        $this->resetAfterTest();

        // Enable the providers.
        set_config('enabled', 1, 'aiprovider_openai');
        set_config('apikey', '123', 'aiprovider_openai');
        set_config('enabled', 1, 'aiprovider_azureai');
        set_config('apikey', '123', 'aiprovider_azureai');
        set_config('endpoint', 'abc', 'aiprovider_azureai');

        $managermock = $this->getMockBuilder(manager::class)
            ->onlyMethods(['call_action_provider'])
            ->getMock();

        $expectedresult = new aiactions\responses\response_generate_image(
            success: true,
        );

        // Set up the expectation for call_action_provider to return the defined result.
        $managermock->expects($this->any())
            ->method('call_action_provider')
            ->willReturn($expectedresult);

        $action = new generate_image(
            contextid: 1,
            userid: 1,
            prompttext: 'This is a test prompt',
            quality: 'hd',
            aspectratio: 'square',
            numimages: 1,
            style: 'vivid',
        );

        // Should now return the expected result.
        $result = $managermock->process_action($action);
        $this->assertEquals($expectedresult, $result);
    }

    /**
     * Test set_user_policy.
     */
    public function test_set_user_policy(): void {
        $this->resetAfterTest();
        global $DB;

        $result = manager::user_policy_accepted(1, 1);
        $this->assertTrue($result);

        // Check record exists.
        $record = $DB->record_exists('ai_policy_register', ['userid' => 1, 'contextid' => 1]);
        $this->assertTrue($record);
    }

    /**
     * Test get_user_policy.
     */
    public function test_get_user_policy(): void {
        $this->resetAfterTest();
        global $DB;

        // Should be false for user initially.
        $result = manager::get_user_policy_status(1);
        $this->assertFalse($result);

        // Manually add record to the database.
        $record = new \stdClass();
        $record->userid = 1;
        $record->contextid = 1;
        $record->timeaccepted = time();

        $DB->insert_record('ai_policy_register', $record);

        // Should be true for user now.
        $result = manager::get_user_policy_status(1);
        $this->assertTrue($result);
    }

    /**
     * Test policy cache data source.
     */
    public function test_user_policy_caching(): void {
        global $DB;
        $this->resetAfterTest();

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();

        // Manually add records to the database.
        $record1 = new \stdClass();
        $record1->userid = $user1->id;
        $record1->contextid = 1;
        $record1->timeaccepted = time();

        $record2 = new \stdClass();
        $record2->userid = $user2->id;
        $record2->contextid = 1;
        $record2->timeaccepted = time();

        $DB->insert_records('ai_policy_register', [
            $record1,
            $record2,
        ]);

        $policycache = \cache::make('core', 'ai_policy');

        // Test single user.
        $this->assertFalse($policycache->has($user1->id));
        $this->assertFalse($policycache->has($user2->id));
        $this->assertFalse($policycache->has($user3->id));

        $result = $policycache->get($user1->id);
        $this->assertTrue($result);
        $result = $policycache->get($user2->id);
        $this->assertTrue($result);
        $result = $policycache->get($user3->id);
        $this->assertFalse($result);

        $this->assertTrue($policycache->has($user1->id));
        $this->assertTrue($policycache->has($user2->id));

        // Purge the cache.
        $policycache->purge();

        // Test multiple users.
        $this->assertFalse($policycache->has($user1->id));
        $this->assertFalse($policycache->has($user2->id));
        $this->assertFalse($policycache->has($user3->id));

        $result = $policycache->get_many([$user1->id, $user2->id, $user3->id]);
        $this->assertNotEmpty($policycache->get_many($result));
        $this->assertTrue($result[$user1->id]);
        $this->assertTrue($result[$user2->id]);
        $this->assertFalse($result[$user3->id]);

        $this->assertTrue($policycache->has($user1->id));
        $this->assertTrue($policycache->has($user2->id));
    }

    /**
     * Test store_action_result.
     */
    public function test_store_action_result(): void {
        $this->resetAfterTest();
        global $DB;

        $contextid = 1;
        $userid = 1;
        $prompttext = 'This is a test prompt';
        $aspectratio = 'square';
        $quality = 'hd';
        $numimages = 1;
        $style = 'vivid';

        $action = new generate_image(
            contextid: 1,
            userid: $userid,
            prompttext: $prompttext,
            quality: $quality,
            aspectratio: $aspectratio,
            numimages: $numimages,
            style: $style
        );

        $body = [
            'revisedprompt' => 'This is a revised prompt',
            'imageurl' => 'https://example.com/image.png',
        ];
        $actionresponse = new response_generate_image(
            success: true,
        );
        $actionresponse->set_response_data($body);

        $provider = new \aiprovider_openai\provider();

        $manager = \core\di::get(manager::class);
        // We're working with a private method here, so we need to use reflection.
        $method = new \ReflectionMethod($manager, 'store_action_result');
        $storeresult = $method->invoke($manager, $provider, $action, $actionresponse);

        // Check the record was written to the DB with expected values.
        $record = $DB->get_record('ai_action_register', ['id' => $storeresult], '*', MUST_EXIST);
        $this->assertEquals($action->get_basename(), $record->actionname);
        $this->assertEquals($userid, $record->userid);
        $this->assertEquals($contextid, $record->contextid);
        $this->assertEquals($provider->get_name(), $record->provider);
        $this->assertEquals($actionresponse->get_errorcode(), $record->errorcode);
        $this->assertEquals($actionresponse->get_errormessage(), $record->errormessage);
        $this->assertEquals($action->get_configuration('timecreated'), $record->timecreated);
        $this->assertEquals($actionresponse->get_timecreated(), $record->timecompleted);
    }

    /**
     * Test call_action_provider.
     */
    public function test_call_action_provider(): void {
        $contextid = 1;
        $userid = 1;
        $prompttext = 'This is a test prompt';
        $aspectratio = 'square';
        $quality = 'hd';
        $numimages = 1;
        $style = 'vivid';

        $action = new generate_image(
            contextid: $contextid,
            userid: $userid,
            prompttext: $prompttext,
            quality: $quality,
            aspectratio: $aspectratio,
            numimages: $numimages,
            style: $style
        );

        $provider = new \aiprovider_openai\provider();

        $manager = \core\di::get(manager::class);

        // We're working with a private method here, so we need to use reflection.
        $method = new \ReflectionMethod($manager, 'call_action_provider');
        $actionresult = $method->invoke($manager, $provider, $action);

        // Assert the result was of the correct type.
        $this->assertInstanceOf(response_generate_image::class, $actionresult);
    }

    /**
     * Test is_action enabled.
     */
    public function test_is_action_enabled(): void {
        $this->resetAfterTest();
        $plugin = 'aiprovider_openai';
        $action = generate_image::class;

        // Should be enabled by default.
        $result = manager::is_action_enabled($plugin, $action);
        $this->assertTrue($result);

        // Disable the action.
        manager::set_action_state($plugin, $action::get_basename(), 0);

        // Should now be disabled.
        $result = manager::is_action_enabled($plugin, $action);
        $this->assertFalse($result);
    }

    /**
     * Test enable_action.
     */
    public function test_enable_action(): void {
        $this->resetAfterTest();
        $plugin = 'aiprovider_openai';
        $action = generate_image::class;

        // Disable the action.
        set_config('generate_image', 0, $plugin);

        // Should now be disabled.
        $result = manager::is_action_enabled($plugin, $action);
        $this->assertFalse($result);

        // Enable the action.
        $result = manager::set_action_state($plugin, $action::get_basename(), 1);
        $this->assertTrue($result);
    }

    /**
     * Test is_action_available method.
     */
    public function test_is_action_available(): void {
        $this->resetAfterTest();
        $action = generate_image::class;

        // Plugin is disabled by default, action state should not matter. Everything should be false.
        $result = manager::is_action_available($action);
        $this->assertFalse($result);

        // Enable the plugin, actions will be enabled by default when the plugin is enabled.
        $manager = \core_plugin_manager::resolve_plugininfo_class('aiprovider');
        $manager::enable_plugin('openai', 1);
        set_config('apikey', '123', 'aiprovider_openai');

        // Should now be available.
        $result = manager::is_action_available($action);
        $this->assertTrue($result);

        // Disable the action.
        set_config('generate_image', 0, 'aiprovider_openai');

        // Should now be unavailable.
        $result = manager::is_action_available($action);
        $this->assertFalse($result);
    }
}
