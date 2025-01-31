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

namespace aiprovider_deepseek;

/**
 * Test Deepseek provider methods.
 *
 * @package    aiprovider_deepseek
 * @copyright  2025 Yusuf Wibisono <yusuf.wibisono@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers     \aiprovider_deepseek\provider
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
            classname: '\aiprovider_deepseek\provider',
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
        $this->assertCount(3, $actionlist);
        $this->assertContains(\core_ai\aiactions\generate_text::class, $actionlist);
        $this->assertContains(\core_ai\aiactions\summarise_text::class, $actionlist);
        $this->assertContains(\core_ai\aiactions\explain_text::class, $actionlist);
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

        $provider = $this->manager->create_provider_instance(
            classname: '\aiprovider_deepseek\provider',
            name: 'dummy',
            config: $config,
        );

        $contextid = 1;
        $userid = 1;
        $prompttext = 'This is a test prompt';
        $action = new \core_ai\aiactions\generate_text(
            contextid: $contextid,
            userid: $userid,
            prompttext: $prompttext,
        );

        // Make 3 requests, all should be allowed.
        for ($i = 0; $i < 3; $i++) {
            $this->assertTrue($provider->is_request_allowed($action));
        }

        // The 4th request for the same user should be denied.
        $result = $provider->is_request_allowed($action);
        $this->assertFalse($result['success']);
        $this->assertEquals(
            'You have reached the maximum number of AI requests you can make in an hour. Try again later.',
            $result['errormessage'],
        );

        // Change user id to make a request for a different user, should pass (4 requests for global rate).
        $action = new \core_ai\aiactions\generate_text(
            contextid: $contextid,
            userid: 2,
            prompttext: $prompttext,
        );
        $this->assertTrue($provider->is_request_allowed($action));

        // Make a 5th request for the global rate limit, it should be allowed.
        $this->assertTrue($provider->is_request_allowed($action));

        // The 6th request should be denied.
        $result = $provider->is_request_allowed($action);
        $this->assertFalse($result['success']);
        $this->assertEquals(
            expected: 'The AI service has reached the maximum number of site-wide requests per hour. Try again later.',
            actual: $result['errormessage'],
        );
    }
}
