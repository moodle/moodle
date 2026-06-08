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

namespace auth_oauth2;

use advanced_testcase;
use core\clock;
use core\di;
use dml_exception;
use Generator;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Unit tests for the class \auth_oauth2\linked_login
 *
 * @package   auth_oauth2
 * @copyright 2026 eDaktik GmbH {@link https://www.edaktik.at/}
 * @author    Christian Abila <christian.abila@edaktik.at>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \auth_oauth2\linked_login
 */
#[CoversMethod(linked_login::class, 'delete_expired_confirmation_tokens')]
#[CoversMethod(linked_login::class, 'delete_expired_pending')]
final class linked_login_test extends advanced_testcase {
    /**
     * Expired confirmation tokens are deleted
     *
     * @param int $offset Seconds relative to now (negative = past, positive = future)
     * @param int $expected
     * @return void
     * @throws dml_exception
     */
    #[DataProvider('expirydate_provider')]
    public function test_delete_expired_confirmation_tokens(int $offset, int $expected): void {
        $this->resetAfterTest();
        global $DB, $USER;

        $confirmed = 0;
        $expirydate = $offset === $confirmed ? $confirmed : di::get(clock::class)->now()->getTimestamp() + $offset;

        $DB->insert_record(
            linked_login::TABLE,
            [
                'timecreated' => time(),
                'timemodified' => 0,
                'usermodified' => 0,
                'userid' => $USER->id,
                'issuerid' => 2,
                'email' => 'email@example.com',
                'confirmtokenexpires' => $expirydate,
            ],
        );

        linked_login::delete_expired_confirmation_tokens();

        $this->assertEquals($expected, $DB->count_records(linked_login::TABLE));
    }

    /**
     * Expiry dates provider
     *
     * @return Generator
     */
    public static function expirydate_provider(): Generator {
        yield 'expired' => [
            'offset' => -60, // 1 minute in the past.
            'expected' => 0,
        ];
        yield 'not yet expired' => [
            'offset' => 1740, // 29 minutes in the future.
            'expected' => 1,
        ];
        yield 'confirmed' => [
            'offset' => 0,
            'expected' => 1,
        ];
    }

    /**
     * delete_expired_pending removes only the expired record for the given user/issuer/username.
     *
     * @param int $offset Seconds relative to now (negative = past, positive = future, 0 = confirmed)
     * @param int $expected Expected record count after deletion
     * @return void
     * @throws dml_exception
     */
    #[DataProvider('delete_expired_pending_provider')]
    public function test_delete_expired_pending(int $offset, int $expected): void {
        $this->resetAfterTest();
        global $DB;

        $this->setAdminUser();
        $issuer = \core\oauth2\api::create_standard_issuer('google');
        $user = $this->getDataGenerator()->create_user();

        $confirmed = 0;
        $expirydate = $offset === $confirmed ? $confirmed : di::get(clock::class)->now()->getTimestamp() + $offset;

        $DB->insert_record(linked_login::TABLE, [
            'timecreated' => time(),
            'timemodified' => 0,
            'usermodified' => 0,
            'userid' => $user->id,
            'issuerid' => $issuer->get('id'),
            'username' => 'banana',
            'email' => 'banana@example.com',
            'confirmtoken' => random_string(32),
            'confirmtokenexpires' => $expirydate,
        ]);

        linked_login::delete_expired_pending($issuer, 'banana', $user->id);

        $this->assertEquals($expected, $DB->count_records(linked_login::TABLE));
    }

    /**
     * Data provider for test_delete_expired_pending.
     *
     * @return Generator
     */
    public static function delete_expired_pending_provider(): Generator {
        yield 'expired record is deleted' => [
            'offset' => -60, // 1 minute in the past.
            'expected' => 0,
        ];
        yield 'not yet expired record is kept' => [
            'offset' => 1740, // 29 minutes in the future.
            'expected' => 1,
        ];
        yield 'confirmed record (expires = 0) is kept' => [
            'offset' => 0,
            'expected' => 1,
        ];
    }
}
