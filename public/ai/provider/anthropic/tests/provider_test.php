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

namespace aiprovider_anthropic;

use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Tests for the Anthropic Claude provider class.
 *
 * @package    aiprovider_anthropic
 * @copyright  2026 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(\aiprovider_anthropic\provider::class)]
final class provider_test extends \advanced_testcase {
    /** @var \core_ai\manager */
    private $manager;

    /** @var \core_ai\provider */
    private $provider;

    #[\Override]
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();

        $this->manager = \core\di::get(\core_ai\manager::class);
        $this->provider = $this->manager->create_provider_instance(
            classname: '\aiprovider_anthropic\provider',
            name: 'dummy',
        );
    }

    /**
     * Test get_action_list returns the three supported actions and excludes generate_image.
     */
    public function test_get_action_list(): void {
        $actionlist = $this->provider->get_action_list();
        $this->assertIsArray($actionlist);
        $this->assertCount(3, $actionlist);
        $this->assertContains(\core_ai\aiactions\generate_text::class, $actionlist);
        $this->assertContains(\core_ai\aiactions\summarise_text::class, $actionlist);
        $this->assertContains(\core_ai\aiactions\explain_text::class, $actionlist);
        $this->assertNotContains(\core_ai\aiactions\generate_image::class, $actionlist);
    }

    /**
     * Test generate_userid produces a 64-character hash string.
     */
    public function test_generate_userid(): void {
        $userid = $this->provider->generate_userid(1);
        $this->assertIsString($userid);
        $this->assertEquals(64, strlen($userid));
    }

    /**
     * Test is_request_allowed enforces user and global rate limits.
     */
    public function test_is_request_allowed(): void {
        $config = [
            'enableuserratelimit' => true,
            'userratelimit' => 3,
            'enableglobalratelimit' => true,
            'globalratelimit' => 5,
        ];
        $provider = $this->manager->create_provider_instance(
            classname: '\aiprovider_anthropic\provider',
            name: 'dummy',
            config: $config,
        );

        $action = new \core_ai\aiactions\generate_text(
            contextid: 1,
            userid: 1,
            prompttext: 'This is a test prompt',
        );

        // First three requests should be allowed.
        for ($i = 0; $i < 3; $i++) {
            $this->assertTrue($provider->is_request_allowed($action));
        }

        // Fourth request for the same user should be denied.
        $result = $provider->is_request_allowed($action);
        $this->assertFalse($result['success']);
        $this->assertEquals(
            'You have reached the maximum number of AI requests you can make in an hour. Try again later.',
            $result['errormessage']
        );

        // Different user — should pass (4th of 5 global).
        $action2 = new \core_ai\aiactions\generate_text(
            contextid: 1,
            userid: 2,
            prompttext: 'Another prompt',
        );
        $this->assertTrue($provider->is_request_allowed($action2));
        $this->assertTrue($provider->is_request_allowed($action2));

        // 6th global request should be denied.
        $result = $provider->is_request_allowed($action2);
        $this->assertFalse($result['success']);
        $this->assertEquals(
            'The AI service has reached the maximum number of site-wide requests per hour. Try again later.',
            $result['errormessage'],
        );
    }

    /**
     * Test is_provider_configured requires the apikey to be set.
     */
    public function test_is_provider_configured(): void {
        $this->assertFalse($this->provider->is_provider_configured());

        $updatedprovider = $this->manager->update_provider_instance(
            provider: $this->provider,
            config: ['apikey' => 'test-api-key'],
        );
        $this->assertTrue($updatedprovider->is_provider_configured());
    }

    /**
     * Test add_authentication_headers sets x-api-key and anthropic-version.
     */
    public function test_add_authentication_headers(): void {
        $provider = $this->manager->create_provider_instance(
            classname: '\aiprovider_anthropic\provider',
            name: 'dummy',
            config: ['apikey' => 'my-test-key'],
        );

        $request = new \GuzzleHttp\Psr7\Request('POST', 'https://api.anthropic.com/v1/messages');
        $request = $provider->add_authentication_headers($request);

        $this->assertEquals('my-test-key', $request->getHeaderLine('x-api-key'));
        $this->assertEquals('2023-06-01', $request->getHeaderLine('anthropic-version'));
    }
}
