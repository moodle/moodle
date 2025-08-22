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

namespace core;

/**
 * Tests for the short link manager.
 *
 * @package    core
 * @category   test
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core\shortlink
 */
final class shortlink_test extends \advanced_testcase {
    public function test_create_public_shortlink(): void {
        $this->resetAfterTest();

        // Mock the handler.
        $this->mock_handler('mod_example', 'submit', '123');

        // Create a public short link.
        $manager = \core\di::get(\core\shortlink::class);
        $link = $manager->create_public_shortlink('mod_example', 'submit', 123);

        $this->assertNotEmpty($link);
        $this->assertInstanceOf(\core\url::class, $link);

        // Extract the shortcode.
        $parts = explode('/', (string) $link);
        $shortcode = end($parts);

        // Fetch the URL.
        $url = $manager->fetch_url_for_shortcode(false, $shortcode);
        $this->assertEquals('https://example.com', (string) $url);
    }

    public function test_create_user_shortlink(): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();

        // Mock the handler.
        $this->mock_handler('mod_example', 'submit', '123');

        // Create a public short link.
        $manager = \core\di::get(\core\shortlink::class);
        $link = $manager->create_shortlink('mod_example', 'submit', 123, $user->id);

        $this->assertNotEmpty($link);
        $this->assertInstanceOf(\core\url::class, $link);

        // Extract the shortcode.
        $parts = explode('/', (string) $link);
        $shortcode = end($parts);

        // Fetching the URL as a user link should return the URL.
        $this->setUser($user);
        $url = $manager->fetch_url_for_shortcode(true, $shortcode);
        $this->assertEquals('https://example.com', (string) $url);

        // Fetching the URL as someone else should not return the URL.
        $otheruser = $this->getDataGenerator()->create_user();
        $this->setUser($otheruser);
        $this->expectException(\core\exception\coding_exception::class);
        $manager->fetch_url_for_shortcode(true, $shortcode);
    }

    public function test_create_users_shortlink(): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $otheruser = $this->getDataGenerator()->create_user();

        // Mock the handler.
        $this->mock_handler('mod_example', 'submit', '123');

        // Create a public short link.
        $manager = \core\di::get(\core\shortlink::class);
        $link = $manager->create_shortlink_for_users('mod_example', 'submit', 123, [$user->id, $otheruser->id]);

        $this->assertNotEmpty($link);
        $this->assertInstanceOf(\core\url::class, $link);

        // Extract the shortcode.
        $parts = explode('/', (string) $link);
        $shortcode = end($parts);

        // Fetching the URL as a user link should return the URL.
        $this->setUser($user);
        $url = $manager->fetch_url_for_shortcode(true, $shortcode);
        $this->assertEquals('https://example.com', (string) $url);

        $this->setUser($otheruser);
        $url = $manager->fetch_url_for_shortcode(true, $shortcode);
        $this->assertEquals('https://example.com', (string) $url);

        $yetanotheruser = $this->getDataGenerator()->create_user();
        $this->setUser($yetanotheruser);
        $this->expectException(\core\exception\coding_exception::class);
        $manager->fetch_url_for_shortcode(true, $shortcode);
    }

    public function test_handler_deleted_after_creation(): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $otheruser = $this->getDataGenerator()->create_user();

        // Mock the handler.
        $this->mock_handler('mod_example', 'submit', '123');

        // Create a public short link.
        $manager = \core\di::get(\core\shortlink::class);
        $link = $manager->create_shortlink_for_users('mod_example', 'submit', 123, [$user->id, $otheruser->id]);

        $this->assertNotEmpty($link);
        $this->assertInstanceOf(\core\url::class, $link);

        // Extract the shortcode.
        $parts = explode('/', (string) $link);
        $shortcode = end($parts);

        // Delete the handler.
        \core\di::set(\mod_example\shortlink_handler::class, null);

        // Fetching the URL as someone else should not return the URL.
        $this->setUser($user);
        $this->expectException(\core\exception\coding_exception::class);
        $manager->fetch_url_for_shortcode(true, $shortcode);
    }

    /**
     * Ensure that a link type deleted after creation is not accessible.
     */
    public function test_linktype_deleted_after_creation(): void {
        global $DB;

        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $otheruser = $this->getDataGenerator()->create_user();

        // Mock the handler.
        $this->mock_handler('mod_example', 'submit', '123');

        // Create a public short link.
        $manager = \core\di::get(\core\shortlink::class);
        $link = $manager->create_shortlink_for_users('mod_example', 'submit', 123, [$user->id, $otheruser->id]);

        $this->assertNotEmpty($link);
        $this->assertInstanceOf(\core\url::class, $link);

        // Extract the shortcode.
        $parts = explode('/', (string) $link);
        $shortcode = end($parts);

        // Simulate the link type being deleted by changing it in the DB.
        $DB->set_field('shortlink', 'linktype', 'invalid');

        $this->setUser($user);
        $this->expectException(\core\exception\coding_exception::class);
        $manager->fetch_url_for_shortcode(true, $shortcode);
    }

    public function test_identifier_changed_after_creation(): void {
        global $DB;

        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $otheruser = $this->getDataGenerator()->create_user();

        // Mock the handler but have it return null for this URL
        $this->mock_handler('mod_example', 'submit', '123', null);

        // Create a public short link.
        $manager = \core\di::get(\core\shortlink::class);
        $link = $manager->create_shortlink_for_users('mod_example', 'submit', 123, [$user->id, $otheruser->id]);

        $this->assertNotEmpty($link);
        $this->assertInstanceOf(\core\url::class, $link);

        // Extract the shortcode.
        $parts = explode('/', (string) $link);
        $shortcode = end($parts);

        $this->setUser($user);
        $this->expectException(\core\exception\coding_exception::class);
        $manager->fetch_url_for_shortcode(true, $shortcode);
    }

    public function test_unknown_handler(): void {
        $manager = \core\di::get(\core\shortlink::class);

        $this->expectException(\core\exception\coding_exception::class);
        $this->expectExceptionMessageMatches('/No shortlink handler found for component mod_example$/');

        $manager->create_shortlink('mod_example', 'invalid', 123, 0);
    }

    public function test_invalid_handler(): void {
        \core\di::set(\mod_example\shortlink_handler::class, new class {});
        $manager = \core\di::get(\core\shortlink::class);

        $this->expectException(\core\exception\coding_exception::class);
        $this->expectExceptionMessageMatches('/Shortlink handler for component mod_example must implement shortlink_handler_interface/');

        $manager->create_shortlink('mod_example', 'invalid', 123, 0);
    }

    public function test_invalid_linktype(): void {
        // Mock the handler.
        $handler = $this->createMock(\core\shortlink_handler_interface::class);
        $handler->method('get_valid_linktypes')
            ->willReturn([]);

        \core\di::set(\mod_example\shortlink_handler::class, $handler);

        $manager = \core\di::get(\core\shortlink::class);

        $this->expectException(\core\exception\coding_exception::class);
        $this->expectExceptionMessageMatches('/Invalid link type submit for component mod_example/');

        $manager->create_shortlink('mod_example', 'submit', 123, 0);
    }

    public function test_creation_for_users_including_public(): void {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();

        // Create a public short link.
        $manager = \core\di::get(\core\shortlink::class);
        $this->expectException(\core\exception\coding_exception::class);
        $manager->create_shortlink_for_users('mod_example', 'submit', 123, [$user->id, 0]);
    }

    /**
     * @dataProvider valid_min_max_length_provider
     */
    public function test_creation_min_max_length(
        int $minlength,
        int $maxlength,
        int $actualminlengthvalue,
    ): void {
        $this->resetAfterTest();

        // Mock the handler.
        $this->mock_handler('mod_example', 'submit', '123');

        $manager = \core\di::get(\core\shortlink::class);

        $link = $manager->create_shortlink('mod_example', 'submit', 123, 0, 1, 1);
        $parts = explode('/', (string) $link);
        $shortcode = end($parts);

        $this->assertLessThanOrEqual($actualminlengthvalue, strlen($shortcode));
    }

    public static function valid_min_max_length_provider(): \Iterator {
        // One char.
        yield [1, 1, 1];
        yield [1, 2, 1];
        yield [2, 2, 2];
        yield [4, 4, 4];
        yield [4, 40, 4];
    }

    /**
     * @dataProvider invalid_min_max_length_provider
     */
    public function test_creation_invalid_min_max_length(
        int $minlength,
        int $maxlength,
    ): void {
        $this->resetAfterTest();
        $this->mock_handler('mod_example', 'submit', '123');

        $manager = \core\di::get(\core\shortlink::class);
        $this->expectException(\core\exception\coding_exception::class);
        $manager->create_shortlink('mod_example', 'submit', 123, 0, $minlength, $maxlength);
    }

    public static function invalid_min_max_length_provider(): \Iterator {
        // Empty.
        yield [0, 0];
        // Min greater than max.
        yield [1, 0];
        // Min greater than max.
        yield [2, 1];
    }

    private function mock_handler(
        string $component,
        string $linktype,
        string $identifier,
        ?string $response = 'https://example.com',
    ): void {
        // Mock the handler.
        $handler = $this->createMock(\core\shortlink_handler_interface::class);
        $handler->method('get_valid_linktypes')
            ->willReturn(['submit']);
        $handler->method('process_shortlink')
            ->with(
                $this->equalTo($linktype),
                $this->equalTo($identifier),
            )
            ->willReturn($response ? new \core\url($response) : null);
        \core\di::set("{$component}\\shortlink_handler", $handler);
    }

    public function test_creation_run_out_of_characters(): void {
        $this->resetAfterTest();

        // Mock the handler.
        $this->mock_handler('mod_example', 'submit', '123');

        $manager = \core\di::get(\core\shortlink::class);

        // We know that there are at least 25 + 25 + 10 + 2 possible character combinations for the default URL.
        // Generate 100 short links and check that they are all unique.
        $shortcodes = [];
        for ($i = 0; $i < 100; $i++) {
            $link = $manager->create_public_shortlink('mod_example', 'submit', 123, 1, 1);
            $parts = explode('/', (string) $link);
            $shortcode = end($parts);
            $this->assertGreaterThanOrEqual(1, strlen($shortcode));

            $shortcodes[$shortcode] = $shortcode;
        }

        $this->assertCount(100, $shortcodes);
    }
}
