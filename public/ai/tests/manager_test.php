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
use core_ai\aiactions\explain_text;
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
            explain_text::class,
        ], $actions);
    }

    /**
     * Test create_provider_instance method.
     */
    public function test_create_provider_instance(): void {
        $this->resetAfterTest();
        global $DB;

        // Create the provider instance.
        $manager = \core\di::get(\core_ai\manager::class);
        $config = ['data' => 'goeshere'];
        $provider = $manager->create_provider_instance(
            classname: '\aiprovider_openai\provider',
            name: 'dummy',
            config: $config,
        );

        $this->assertIsInt($provider->id);
        $this->assertFalse($provider->enabled);
        $this->assertEquals('goeshere', $provider->config['data']);

        // Check the record was written to the DB.
        $record = $DB->get_record('ai_providers', ['id' => $provider->id], '*', MUST_EXIST);
        $this->assertEquals($provider->id, $record->id);
    }

    /**
     * Test create_provider_instance non provider class.
     */
    public function test_create_provider_instance_non_provider_class(): void {
        $this->resetAfterTest();

        // Should throw an exception as the class is not a provider.
        $this->expectException(\coding_exception::class);
        $this->expectExceptionMessage(' Provider class not valid: ' . $this::class);

        // Create the provider instance.
        $manager = \core\di::get(\core_ai\manager::class);
        $manager->create_provider_instance(
            classname: $this::class,
            name: 'dummy',
        );
    }

    /**
     * Test get_provider_record method
     */
    public function test_get_provider_record(): void {
        global $DB;

        $this->resetAfterTest();

        // Create a dummy provider record directly in the database.
        $config = ['data' => 'goeshere'];
        $record = new \stdClass();
        $record->name = 'dummy1';
        $record->provider = 'dummy';
        $record->enabled = 1;
        $record->config = json_encode($config);
        $record->actionconfig = json_encode(['generate_text' => 1]);
        $record->id = $DB->insert_record('ai_providers', $record);

        $manager = \core\di::get(\core_ai\manager::class);
        $provider = $manager->get_provider_record(['provider' => 'dummy']);

        $this->assertEquals($record->id, $provider->id);
    }

    /**
     * Test get_provider_records method.
     */
    public function test_get_provider_records(): void {
        $this->resetAfterTest();
        global $DB;

        // Create some dummy provider records directly in the database.
        $config = ['data' => 'goeshere'];
        $record1 = new \stdClass();
        $record1->name = 'dummy1';
        $record1->provider = 'dummy';
        $record1->enabled = 1;
        $record1->config = json_encode($config);
        $record1->actionconfig = json_encode(['generate_text' => 1]);

        $record2 = new \stdClass();
        $record2->name = 'dummy2';
        $record2->provider = 'dummy';
        $record2->enabled = 1;
        $record2->config = json_encode($config);
        $record2->actionconfig = json_encode(['generate_text' => 1]);

        $DB->insert_records('ai_providers', [
                $record1,
                $record2,
        ]);

        // Get the provider records.
        $manager = \core\di::get(\core_ai\manager::class);
        $providers = $manager->get_provider_records(['provider' => 'dummy']);

        // Assert that the records were returned.
        $this->assertCount(2, $providers);
    }

    /**
     * Test get_provider_instances method.
     */
    public function test_get_provider_instances(): void {
        $this->resetAfterTest();
        global $DB;

        $manager = \core\di::get(\core_ai\manager::class);
        $config = ['data' => 'goeshere'];

        $provider = $manager->create_provider_instance(
                classname: '\aiprovider_openai\provider',
                name: 'dummy',
                config: $config,
        );

        // Create an instance record for a non provider class.
        $record = new \stdClass();
        $record->name = 'dummy';
        $record->provider = 'dummy';
        $record->enabled = 1;
        $record->config = json_encode($config);

        $DB->insert_record('ai_providers', $record);

        // Get the provider instances.
        $instances = $manager->get_provider_instances();
        $this->assertDebuggingCalled('Unable to find a provider class for dummy');
        $this->assertCount(1, $instances);
        $this->assertEquals($provider->id, $instances[$provider->id]->id);
    }

    /**
     * Test update_provider_instance method.
     */
    public function test_update_provider_instance(): void {
        $this->resetAfterTest();
        global $DB;

        // Create the provider instance.
        $manager = \core\di::get(\core_ai\manager::class);
        $config = ['data' => 'goeshere'];
        $provider = $manager->create_provider_instance(
                classname: '\aiprovider_openai\provider',
                name: 'dummy',
                config: $config,
        );

        // Update the provider instance.
        $config['data'] = 'updateddata';
        $manager->update_provider_instance($provider, $config);

        // Check the record was updated in the DB.
        $record = $DB->get_record('ai_providers', ['id' => $provider->id], '*', MUST_EXIST);
        $this->assertEquals($provider->id, $record->id);
        $this->assertEquals('updateddata', json_decode($record->config)->data);
    }

    /**
     * Test delete_provider_instance method.
     */
    public function test_delete_provider_instance(): void {
        $this->resetAfterTest();
        global $DB;

        // Create the provider instance.
        $manager = \core\di::get(\core_ai\manager::class);
        $config = ['data' => 'goeshere'];
        $provider = $manager->create_provider_instance(
                classname: '\aiprovider_openai\provider',
                name: 'dummy',
                config: $config,
        );

        // Delete the provider instance.
        $manager->delete_provider_instance($provider);

        // Check the record was deleted from the DB.
        $record = $DB->record_exists('ai_providers', ['id' => $provider->id]);
        $this->assertFalse($record);
    }

    /**
     * Test get_providers_for_actions.
     */
    public function test_get_providers_for_actions(): void {
        $this->resetAfterTest();
        $manager = \core\di::get(manager::class);
        $actions = [
            generate_text::class,
            summarise_text::class,
            explain_text::class,
        ];

        // Create two provider instances.
        $config = [
            'apikey' => 'goeshere',
            'endpoint' => 'https://example.com',
        ];
        $provider1 = $manager->create_provider_instance(
            classname: '\aiprovider_openai\provider',
            name: 'dummy1',
            enabled: true,
            config: $config,
        );

        $config['apiendpoint'] = 'https://example.com';
        $provider2 = $manager->create_provider_instance(
            classname: '\aiprovider_azureai\provider',
            name: 'dummy2',
            enabled: true,
            config: $config,
        );

        // Get the providers for the actions.
        $providers = $manager->get_providers_for_actions($actions);

        // Assert that the providers array is indexed by action name.
        $this->assertEquals($actions, array_keys($providers));

        // Assert that there is only one provider for each action.
        $this->assertCount(2, $providers[generate_text::class]);
        $this->assertCount(2, $providers[summarise_text::class]);
        $this->assertCount(2, $providers[explain_text::class]);

        // Disable the generate text action for the Open AI provider.
        $setresult = $manager->set_action_state(
                plugin: $provider1->provider,
                actionbasename: generate_text::class::get_basename(),
                enabled: 0,
                instanceid: $provider1->id);
        // Assert that the action was disabled.
        $this->assertFalse($setresult);

        $providers = $manager->get_providers_for_actions($actions, true);

        // Assert that there is no provider for the generate text action.
        $this->assertCount(1, $providers[generate_text::class]);
        $this->assertCount(2, $providers[summarise_text::class]);

        // Ordering the provider instances.
        // Re-enable the generate text action for the Openai provider.
        $manager->set_action_state(
            plugin: $provider1->provider,
            actionbasename: generate_text::class::get_basename(),
            enabled: 1,
            instanceid: $provider1->id,
        );

        // Move the $provider2 to the first provider for the generate text action.
        $manager->change_provider_order($provider2->id, \core\plugininfo\aiprovider::MOVE_UP);
        // Get the new providers for the actions.
        $providers = $manager->get_providers_for_actions($actions);
        // Assert whether provider2 is the first provider and provider1 is the last provider for the generate text action.
        $this->assertEquals($providers[generate_text::class][0], $provider2);
        $this->assertEquals($providers[generate_text::class][1], $provider1);

        // Move the $provider2 to the last provider for the generate text action.
        $manager->change_provider_order($provider2->id, \core\plugininfo\aiprovider::MOVE_DOWN);
        // Get the new providers for the actions.
        $providers = $manager->get_providers_for_actions($actions);
        // Assert whether provider1 is the first provider and provider2 is the last provider for the generate text action.
        $this->assertEquals($providers[generate_text::class][0], $provider1);
        $this->assertEquals($providers[generate_text::class][1], $provider2);
    }

    /**
     * Test process_action fail.
     */
    public function test_process_action_fail(): void {
        $this->resetAfterTest();
        global $DB;
        $managermock = $this->getMockBuilder(manager::class)
            ->setConstructorArgs([$DB])
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
        global $DB;

        // Create two provider instances.
        $manager = \core\di::get(manager::class);
        $config = ['apikey' => 'goeshere'];
        $provider1 = $manager->create_provider_instance(
                classname: '\aiprovider_openai\provider',
                name: 'dummy1',
                enabled: true,
                config: $config,
        );

        $config['apiendpoint'] = 'https://example.com';
        $provider2 = $manager->create_provider_instance(
                classname: '\aiprovider_azureai\provider',
                name: 'dummy2',
                enabled: true,
                config: $config,
        );

        $managermock = $this->getMockBuilder(manager::class)
            ->setConstructorArgs([$DB])
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
            'model' => 'dall-e-3',
        ];
        $actionresponse = new response_generate_image(
            success: true,
        );
        $actionresponse->set_response_data($body);

        $manager = \core\di::get(manager::class);
        $config = ['data' => 'goeshere'];
        $provider = $manager->create_provider_instance(
                classname: '\aiprovider_openai\provider',
                name: 'dummy',
                config: $config,
        );

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
        $this->assertEquals($actionresponse->get_model_used(), $record->model);
    }

    /**
     * Test call_action_provider.
     */
    public function test_call_action_provider(): void {
        $this->resetAfterTest();
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

        $manager = \core\di::get(manager::class);
        $config = ['apikey' => 'goeshere'];
        $provider = $manager->create_provider_instance(
                classname: '\aiprovider_openai\provider',
                name: 'dummy',
                config: $config,
        );

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
        $action = generate_image::class;
        $manager = \core\di::get(manager::class);

        $config = ['apikey' => 'goeshere'];
        $provider = $manager->create_provider_instance(
            classname: '\aiprovider_openai\provider',
            name: 'dummy',
            config: $config,
        );

        // Should be enabled by default.
        $result = $manager->is_action_enabled(
            plugin: $provider->provider,
            actionclass: $action,
            instanceid: $provider->id
        );
        $this->assertTrue($result);

        // Disable the action.
        $manager->set_action_state(
            plugin: $provider->provider,
            actionbasename: $action::get_basename(),
            enabled: 0,
            instanceid: $provider->id
        );

        // Should now be disabled.
        $result = $manager->is_action_enabled(
            plugin: $provider->provider,
            actionclass: $action,
            instanceid: $provider->id
        );
        $this->assertFalse($result);
    }

    /**
     * Test enable_action.
     */
    public function test_enable_action(): void {
        $this->resetAfterTest();
        $action = generate_image::class;

        $manager = \core\di::get(manager::class);

        $config = ['apikey' => 'goeshere'];
        $provider = $manager->create_provider_instance(
                classname: '\aiprovider_openai\provider',
                name: 'dummy',
                config: $config,
        );

        // Disable the action.
        $setresult = $manager->set_action_state(
                plugin: $provider->provider,
                actionbasename: $action::get_basename(),
                enabled: 0,
                instanceid: $provider->id);

        // Should now be disabled.
        $this->assertFalse($setresult);
        $result = $manager->is_action_enabled(
                plugin: $provider->provider,
                actionclass: $action,
                instanceid: $provider->id
        );
        $this->assertFalse($result);

        // Enable the action.
        $manager = \core\di::get(manager::class);
        $result = $manager->set_action_state(
                plugin: $provider->provider,
                actionbasename: generate_text::class::get_basename(),
                enabled: 1,
                instanceid: $provider->id);
        $this->assertTrue($result);
    }

    /**
     * Test is_action_available method.
     */
    public function test_is_action_available(): void {
        $this->resetAfterTest();
        $action = generate_image::class;

        // Plugin is disabled by default, action state should not matter. Everything should be false.
        $manager = \core\di::get(manager::class);
        $result = $manager->is_action_available($action);
        $this->assertFalse($result);

        // Create the provider instance, actions will be enabled by default when the plugin is enabled.
        $config = ['apikey' => 'goeshere'];
        $provider = $manager->create_provider_instance(
            classname: '\aiprovider_openai\provider',
            name: 'dummy',
            enabled: true,
            config: $config,
        );

        // Should now be available.
        $result = $manager->is_action_available($action);
        $this->assertTrue($result);

        // Disable the action.
        $manager->set_action_state(
                plugin: $provider->provider,
                actionbasename: $action::get_basename(),
                enabled: 0,
                instanceid: $provider->id);

        // Should now be unavailable.
        $result = $manager->is_action_available($action);
        $this->assertFalse($result);
    }
}
