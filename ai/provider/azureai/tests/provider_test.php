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

namespace aiprovider_azureai;

use core_ai\manager;

/**
 * Test Azure AI provider methods.
 *
 * @package    aiprovider_azureai
 * @copyright  2024 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \aiprovider_azureai\provider
 */
final class provider_test extends \advanced_testcase {

    /** @var manager $manager */
    private manager $manager;

    /** @var provider $provider */
    private provider $provider;

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
            classname: '\aiprovider_azureai\provider',
            name: 'dummy',
            config: $config,
        );
    }
    /**
     * Test get_action_list
     */
    public function test_get_action_list(): void {
        $actionlist = $this->provider->get_action_list();
        $this->assertIsArray($actionlist);
        $this->assertEquals(4, count($actionlist));
        $this->assertContains(\core_ai\aiactions\generate_text::class, $actionlist);
        $this->assertContains(\core_ai\aiactions\generate_image::class, $actionlist);
        $this->assertContains(\core_ai\aiactions\summarise_text::class, $actionlist);
        $this->assertContains(\core_ai\aiactions\explain_text::class, $actionlist);
    }

    /**
     * Test generate_userid.
     */
    public function test_generate_userid(): void {
        $userid = $this->provider->generate_userid(1);

        // Assert that the generated userid is a string of proper length.
        $this->assertIsString($userid);
        $this->assertEquals(64, strlen($userid));
    }

    /**
     * Test is_request_allowed.
     */
    public function test_is_request_allowed(): void {
        // Create the provider instance.
        $config = [
            'enableuserratelimit' => true,
            'userratelimit' => 3,
            'enableglobalratelimit' => true,
            'globalratelimit' => 5,
        ];

        /** @var provider $provider */
        $provider = $this->manager->create_provider_instance(
            classname: provider::class,
            name: 'dummy',
            config: $config,
        );

        $contextid = 1;
        $userid = 1;
        $prompttext = 'This is a test prompt';
        $aspectratio = 'square';
        $quality = 'hd';
        $numimages = 1;
        $style = 'vivid';
        $action = new \core_ai\aiactions\generate_image(
            contextid: $contextid,
            userid: $userid,
            prompttext: $prompttext,
            quality: $quality,
            aspectratio: $aspectratio,
            numimages: $numimages,
            style: $style
        );

        // Make 3 requests, all should be allowed.
        for ($i = 0; $i < 3; $i++) {
            $this->assertTrue($provider->is_request_allowed($action));
        }

        // The 4th request for the same user should be denied.
        $result = $provider->is_request_allowed($action);
        $this->assertFalse($result['success']);
        $this->assertEquals('User rate limit exceeded', $result['errormessage']);

        // Change user id to make a request for a different user, should pass (4 requests for global rate).
        $action = new \core_ai\aiactions\generate_image(
            contextid: $contextid,
            userid: 2,
            prompttext: $prompttext,
            quality: $quality,
            aspectratio: $aspectratio,
            numimages: $numimages,
            style: $style);
        $this->assertTrue($provider->is_request_allowed($action));

        // Make a 5th request for the global rate limit, it should be allowed.
        $this->assertTrue($provider->is_request_allowed($action));

        // The 6th request should be denied.
        $result = $provider->is_request_allowed($action);
        $this->assertFalse($result['success']);
        $this->assertEquals('Global rate limit exceeded', $result['errormessage']);
    }

    /**
     * Test is_provider_configured.
     */
    public function test_is_provider_configured(): void {

        // No configured values.
        $this->assertFalse($this->provider->is_provider_configured());

        // Partially configured values.
        $updatedprovider = $this->manager->update_provider_instance(
            provider: $this->provider,
            config: ['apikey' => '123'],
        );
        $this->assertFalse($updatedprovider->is_provider_configured());

        // Properly configured values.
        $updatedprovider = $this->manager->update_provider_instance(
            provider: $this->provider,
            config: [
                'apikey' => '123',
                'endpoint' => 'abc',
            ],
        );
        $this->assertTrue($updatedprovider->is_provider_configured());
    }
}
