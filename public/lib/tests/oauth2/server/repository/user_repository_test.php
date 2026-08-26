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

namespace core\oauth2\server\repository;

use PHPUnit\Framework\Attributes\CoversClass;
use core\oauth2\server\entity\client_entity;
use core\oauth2\server\entity\user_entity;

/**
 * Tests for {@see user_repository}.
 *
 * @package    core
 * @copyright  2026 Mihail Geshoski <mihailgesoski@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(user_repository::class)]
final class user_repository_test extends \advanced_testcase {
    /**
     * Test getting a user entity by user credentials.
     *
     * @todo MDL-84281: Update this test to use the WithoutErrorHandler attribute when PHPUnit 12+ is available.
     */
    public function test_get_user_entity_by_user_credentials(): void {
        global $CFG;

        $this->resetAfterTest();

        $repository = new user_repository();
        $client = new client_entity();
        $client->setIdentifier('client-id');

        // Prevent standard error logging during invalid login attempts in tests.
        $oldlog = ini_get('error_log');
        ini_set('error_log', "{$CFG->dataroot}/testlog.log");

        $_SERVER['HTTP_USER_AGENT'] = 'no browser'; // Hack around missing user agent in CLI scripts.

        // Create a real Moodle user.
        $user = $this->getDataGenerator()->create_user([
            'username' => 'oauth2testuser',
            'password' => 'OAuth2_Pass_123!',
            'email' => 'oauth2test@example.com',
        ]);

        // Invalid username.
        $this->assertNull(
            $repository->getUserEntityByUserCredentials(
                'non-existent',
                'OAuth2_Pass_123!',
                'password',
                $client
            )
        );

        // Invalid password.
        $this->assertNull($repository->getUserEntityByUserCredentials('oauth2testuser', 'WrongPassword!', 'password', $client));

        // Valid credentials.
        $userentity = $repository->getUserEntityByUserCredentials('oauth2testuser', 'OAuth2_Pass_123!', 'password', $client);
        $this->assertInstanceOf(user_entity::class, $userentity);
        $this->assertSame((string) $user->id, $userentity->getIdentifier());

        ini_set('error_log', $oldlog);
    }

    /**
     * Test authenticating a user, returning the full Moodle user record.
     *
     * @todo MDL-84281: Update this test to use the WithoutErrorHandler attribute when PHPUnit 12+ is available.
     */
    public function test_authenticate_user(): void {
        global $CFG;

        $this->resetAfterTest();

        $repository = new user_repository();

        // Prevent standard error logging during invalid login attempts in tests.
        $oldlog = ini_get('error_log');
        ini_set('error_log', "{$CFG->dataroot}/testlog.log");

        $_SERVER['HTTP_USER_AGENT'] = 'no browser'; // Hack around missing user agent in CLI scripts.

        $user = $this->getDataGenerator()->create_user([
            'username' => 'oauth2testuser2',
            'password' => 'OAuth2_Pass_123!',
            'email' => 'oauth2test2@example.com',
        ]);

        // Invalid credentials return false.
        $this->assertFalse($repository->authenticate_user('oauth2testuser2', 'WrongPassword!'));

        // Valid credentials return the full Moodle user record.
        $authenticateduser = $repository->authenticate_user('oauth2testuser2', 'OAuth2_Pass_123!');
        $this->assertInstanceOf(\stdClass::class, $authenticateduser);
        $this->assertEquals($user->id, $authenticateduser->id);
        $this->assertEquals($user->username, $authenticateduser->username);

        ini_set('error_log', $oldlog);
    }

    /**
     * Test getting the current logged-in user as a user entity.
     */
    public function test_get_current_user(): void {
        $this->resetAfterTest();

        $repository = new user_repository();

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $userentity = $repository->get_current_user();

        $this->assertInstanceOf(user_entity::class, $userentity);
        $this->assertSame((string) $user->id, $userentity->getIdentifier());
    }
}
