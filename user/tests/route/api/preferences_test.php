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

namespace core_user\route\api;

use core\tests\route_testcase;
use GuzzleHttp\Psr7\Utils;

/**
 * Tests for user preference API handler.
 *
 * @package    core_user
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \core_user\route\api\preferences
 * @covers \core_user\route\responses\user_preferences_response
 */
final class preferences_test extends route_testcase {
    /**
     * Ensure that preferences returned for a user without login are empty.
     */
    public function test_preferences_no_login(): void {
        $this->add_class_routes_to_route_loader(preferences::class);
        $response = $this->process_api_request('GET', '/current/preferences');

        $this->assert_valid_response($response);
        $payload = $this->decode_response($response);

        $this->assertEmpty((array) $payload);
    }

    /**
     * Test that the preferences are returned when logged in.
     */
    public function test_preferences_returned(): void {
        $this->resetAfterTest();

        $this->add_class_routes_to_route_loader(preferences::class);

        $this->setAdminUser();
        set_user_preference('filemanager_recentviewmode', 1);

        $response = $this->process_api_request('GET', '/current/preferences');

        $this->assert_valid_response($response);

        $payload = $this->decode_response($response);

        $this->assertObjectHasProperty('filemanager_recentviewmode', $payload);
        $this->assertEquals(1, $payload->filemanager_recentviewmode);
    }

    public function test_preference_returned(): void {
        $this->resetAfterTest();

        $this->add_class_routes_to_route_loader(preferences::class);

        $this->setAdminUser();
        set_user_preference('filemanager_recentviewmode', 1);

        $response = $this->process_api_request('GET', '/current/preferences/filemanager_recentviewmode');

        $this->assert_valid_response($response);

        $payload = $this->decode_response($response);

        $this->assertObjectHasProperty('filemanager_recentviewmode', $payload);
        $this->assertEquals(1, $payload->filemanager_recentviewmode);
    }

    public function test_preferences_set(): void {
        $this->resetAfterTest();

        $this->add_class_routes_to_route_loader(preferences::class);

        $this->setAdminUser();

        $response = $this->process_api_request(
            'POST',
            '/current/preferences',
            body: Utils::streamFor(json_encode([
                'preferences' => [
                    'filemanager_recentviewmode' => 2,
                    'drawer-open-index' => 1,
                ],
            ])),
        );

        $this->assert_valid_response($response);

        // Check that the response contained the updtaed parameter.
        $payload = (object) $this->decode_response($response);
        $this->assertObjectHasProperty('filemanager_recentviewmode', $payload);
        $this->assertObjectHasProperty('drawer-open-index', $payload);
        $this->assertEquals(2, $payload->filemanager_recentviewmode);

        // Check that the preference was updated.
        $this->assertEquals(2, get_user_preferences('filemanager_recentviewmode'));
        $this->assertEquals(1, get_user_preferences('drawer-open-index'));
    }

    /**
     * Test that an invalid preference is rejected.
     */
    public function test_preferences_set_invalid_value(): void {
        $this->resetAfterTest();

        $this->add_class_routes_to_route_loader(preferences::class);

        $this->setAdminUser();

        $response = $this->process_api_request(
            'POST',
            '/current/preferences',
            body: Utils::streamFor(json_encode([
                'preferences' => [
                    'filemanager_recentviewmode' => 4,
                ],
            ])),
        );

        $this->assert_invalid_parameter_response($response);
        $payload = $this->decode_response($response);
        $this->assertStringContainsString('filemanager_recentviewmode', $payload->message);
    }

    /**
     * Test that a preference the user does not have permission to is rejected.
     */
    public function test_preferences_set_not_permitted_valid_login(): void {
        $this->resetAfterTest();

        $this->add_class_routes_to_route_loader(preferences::class);

        $this->setAdminUser();

        $response = $this->process_api_request(
            'POST',
            '/current/preferences',
            body: Utils::streamFor(json_encode([
                'preferences' => [
                    'auth_forcepasswordchange' => 4,
                ],
            ])),
        );

        $this->assert_access_denied_response($response);
    }

    public function test_preference_set(): void {
        $this->resetAfterTest();

        $this->add_class_routes_to_route_loader(preferences::class);

        $this->setAdminUser();

        $response = $this->process_api_request(
            'POST',
            '/current/preferences/filemanager_recentviewmode',
            body: Utils::streamFor(json_encode([
                'value' => 2,
            ])),
        );

        $this->assert_valid_response($response);

        // Check that the response contained the updtaed parameter.
        $payload = $this->decode_response($response);
        $this->assertObjectHasProperty('filemanager_recentviewmode', $payload);
        $this->assertEquals(2, $payload->filemanager_recentviewmode);

        // Check that the preference was updated.
        $this->assertEquals(2, get_user_preferences('filemanager_recentviewmode'));
    }

    /**
     * Test that an invalid preference is rejected.
     */
    public function test_preference_set_invalid_value(): void {
        $this->resetAfterTest();

        $this->add_class_routes_to_route_loader(preferences::class);

        $this->setAdminUser();

        $response = $this->process_api_request(
            'POST',
            '/current/preferences/filemanager_recentviewmode',
            body: Utils::streamFor(json_encode([
                'value' => 4,
            ])),
        );

        $this->assert_invalid_parameter_response($response);
        $payload = $this->decode_response($response);
        $this->assertStringContainsString('filemanager_recentviewmode', $payload->message);
    }

    /**
     * Test that an invalid preference inentifier is rejected.
     */
    public function test_preference_set_invalid_preference(): void {
        $this->resetAfterTest();

        $this->add_class_routes_to_route_loader(preferences::class);

        $this->setAdminUser();

        $response = $this->process_api_request(
            'POST',
            '/current/preferences/what_a_fake',
            body: Utils::streamFor(json_encode([
                'value' => 4,
            ])),
        );

        $this->assert_invalid_parameter_response($response);
        $payload = $this->decode_response($response);
        $this->assertStringContainsString('what_a_fake', $payload->message);
    }

    /**
     * Test that a preference the user does not have permission to is rejected.
     */
    public function test_preference_set_not_permitted_valid_login(): void {
        $this->resetAfterTest();

        $this->add_class_routes_to_route_loader(preferences::class);

        $this->setAdminUser();

        $response = $this->process_api_request(
            'POST',
            '/current/preferences/auth_forcepasswordchange',
            body: Utils::streamFor(json_encode([
                'value' => 4,
            ])),
        );

        $this->assert_access_denied_response($response);
    }

    /**
     * A user cannot get or set preferences for anothe ruser.
     */
    public function test_preference_get_other_user(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $user = $this->getDataGenerator()->create_user();

        $this->add_class_routes_to_route_loader(preferences::class);

        // Get all preferences.
        $response = $this->process_api_request('GET', "/{$user->id}/preferences");
        $this->assert_access_denied_response($response);

        // Get one preference.
        $response = $this->process_api_request('GET', "/{$user->id}/preferences/example");
        $this->assert_access_denied_response($response);

        // Set all preferences.
        $response = $this->process_api_request(
            'POST',
            "/{$user->id}/preferences/filemanager_recentviewmode",
            body: Utils::streamFor(json_encode([
                'value' => 4,
            ])),
        );
        $this->assert_access_denied_response($response);

        // Get all preferences.
        $response = $this->process_api_request(
            'POST',
            "/{$user->id}/preferences",
            body: Utils::streamFor(json_encode([
                'preferences' => [
                    'filemanager_recentviewmode' => 2,
                ],
            ])),
        );
        $this->assert_access_denied_response($response);
    }
}
