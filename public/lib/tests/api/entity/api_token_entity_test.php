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

namespace core\api\entity;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Tests for {@see api_token_entity}.
 *
 * @package    core
 * @copyright  2026 Mihail Geshoski <mihailgesoski@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(api_token_entity::class)]
final class api_token_entity_test extends \advanced_testcase {
    /**
     * Test create_from_record() and getters.
     */
    public function test_create_from_record_and_getters(): void {
        $record = new \stdClass();
        $record->id = 123;
        $record->name = 'Test token';
        $record->description = 'A token for testing';
        $record->token = 'token';
        $record->userid = 456;
        $record->scopes = 'scope';
        $record->expirytime = 1700000000;
        $record->revoked = api_token_entity::REVOKED_YES;
        $record->timecreated = 1600000000;
        $record->lastaccessed = 1650000000;

        $entity = api_token_entity::create_from_record($record);

        $this->assertEquals(123, $entity->get_id());
        $this->assertEquals('Test token', $entity->get_name());
        $this->assertEquals('A token for testing', $entity->get_description());
        $this->assertEquals('token', $entity->get_token());
        $this->assertEquals(456, $entity->get_userid());
        $this->assertEquals('scope', $entity->get_scopes());
        $this->assertEquals(1700000000, $entity->get_expirytime());
        $this->assertEquals(api_token_entity::REVOKED_YES, $entity->get_revoked());
        $this->assertEquals(1600000000, $entity->get_timecreated());
        $this->assertEquals(1650000000, $entity->get_lastaccessed());
    }

    /**
     * Test is_revoked().
     *
     * @param int $revokedstatus The revoked status of the token.
     * @param bool $expected The expected result.
     */
    #[DataProvider('is_revoked_provider')]
    public function test_is_revoked(int $revokedstatus, bool $expected): void {
        $record = new \stdClass();
        $record->id = 1;
        $record->name = 'test';
        $record->token = 'token';
        $record->userid = 2;
        $record->scopes = 'scope';
        $record->timecreated = time();
        $record->revoked = $revokedstatus;

        $entity = api_token_entity::create_from_record($record);
        $this->assertSame($expected, $entity->is_revoked());
    }

    /**
     * Data provider for is_revoked.
     */
    public static function is_revoked_provider(): array {
        return [
            'not revoked' => [
                api_token_entity::REVOKED_NO,
                false,
            ],
            'revoked' => [
                api_token_entity::REVOKED_YES,
                true,
            ],
        ];
    }

    /**
     * Test has_expired().
     *
     * @param int|null $expirytime The expiry time of the token, or null if no expiry.
     * @param bool $expected The expected result.
     */
    #[DataProvider('has_expired_provider')]
    public function test_has_expired(?int $expirytime, bool $expected): void {
        $record = new \stdClass();
        $record->id = 1;
        $record->name = 'test';
        $record->token = 'token';
        $record->userid = 2;
        $record->scopes = 'scope';
        $record->timecreated = time();
        $record->revoked = api_token_entity::REVOKED_NO;
        $record->expirytime = $expirytime;

        $entity = api_token_entity::create_from_record($record);
        $this->assertSame($expected, $entity->has_expired());
    }

    /**
     * Data provider for has_expired.
     */
    public static function has_expired_provider(): array {
        return [
            'no expiry' => [
                null,
                false,
            ],
            'future expiry' => [
                time() + 3600,
                false,
            ],
            'past expiry' => [
                time() - 3600,
                true,
            ],
        ];
    }

    /**
     * Test is_active().
     *
     * @param int $revokedstatus The revoked status of the token.
     * @param int|null $expirytime The expiry time of the token, or null if no expiry.
     * @param bool $expected The expected result.
     */
    #[DataProvider('is_active_provider')]
    public function test_is_active(int $revokedstatus, ?int $expirytime, bool $expected): void {
        $record = new \stdClass();
        $record->id = 1;
        $record->name = 'test';
        $record->token = 'token';
        $record->userid = 2;
        $record->scopes = 'scope';
        $record->timecreated = time();
        $record->revoked = $revokedstatus;
        $record->expirytime = $expirytime;

        $entity = api_token_entity::create_from_record($record);
        $this->assertSame($expected, $entity->is_active());
    }

    /**
     * Data provider for is_active.
     */
    public static function is_active_provider(): array {
        return [
            'active: not revoked, no expiry' => [
                api_token_entity::REVOKED_NO,
                null,
                true,
            ],
            'active: not revoked, future expiry' => [
                api_token_entity::REVOKED_NO,
                time() + 3600,
                true,
            ],
            'inactive: revoked, no expiry' => [
                api_token_entity::REVOKED_YES,
                null,
                false,
            ],
            'inactive: not revoked, past expiry' => [
                api_token_entity::REVOKED_NO,
                time() - 3600,
                false,
            ],
            'inactive: revoked, past expiry' => [
                api_token_entity::REVOKED_YES,
                time() - 3600,
                false,
            ],
        ];
    }
}
