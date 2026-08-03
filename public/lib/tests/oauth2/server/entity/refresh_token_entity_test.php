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

namespace core\oauth2\server\entity;

use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Tests for {@see refresh_token_entity}.
 *
 * @package    core
 * @copyright  2026 Mihail Geshoski <mihailgesoski@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(refresh_token_entity::class)]
final class refresh_token_entity_test extends \advanced_testcase {
    /**
     * Test the identifier getter and setter.
     *
     * @return void
     */
    public function test_identifier_setter_and_getter(): void {
        $token = new refresh_token_entity();
        $token->setIdentifier('refresh-token-id');

        $this->assertSame('refresh-token-id', $token->getIdentifier());
    }

    /**
     * Test the expiry date getter and setter.
     *
     * @return void
     */
    public function test_expiry_date_time_setter_and_getter(): void {
        $token = new refresh_token_entity();
        $expiry = new \DateTimeImmutable('+1 day');
        $token->setExpiryDateTime($expiry);

        $this->assertEquals($expiry, $token->getExpiryDateTime());
    }

    /**
     * Test the access token getter and setter.
     *
     * @return void
     */
    public function test_access_token_setter_and_getter(): void {
        $token = new refresh_token_entity();
        $accesstoken = new access_token_entity();
        $accesstoken->setIdentifier('access-token-id');

        $token->setAccessToken($accesstoken);

        $this->assertSame($accesstoken, $token->getAccessToken());
    }
}
