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

namespace core\exception;

use core\router\response\unauthorized_api_response;

/**
 * Tests for the API token exception classes.
 *
 * Note: Revoked and Invalid tokens are treated equally. They are not identified as different, and both
 * therefore map to the same response class as the expired token exception.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(expired_api_token_exception::class)]
#[\PHPUnit\Framework\Attributes\CoversClass(invalid_api_token_exception::class)]
#[\PHPUnit\Framework\Attributes\CoversClass(revoked_api_token_exception::class)]
final class api_token_exception_test extends \advanced_testcase {
    /**
     * All API token exceptions must implement the response_aware_exception interface.
     *
     * @param string $classname
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('api_token_exception_provider')]
    public function test_implements_response_aware_exception(string $classname): void {
        $exception = new $classname();
        $this->assertInstanceOf(response_aware_exception::class, $exception);
    }

    /**
     * All API token exceptions must map to the unauthorized_api_response class, since they are all treated equally.
     *
     * @param string $classname
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('api_token_exception_provider')]
    public function test_get_response_classname(string $classname): void {
        $exception = new $classname();
        $this->assertEquals(unauthorized_api_response::class, $exception->get_response_classname());
    }

    /**
     * All API token exceptions must be a moodle_exception, and use a language string.
     *
     * @param string $classname
     * @param string $expectedmessage
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('api_token_exception_provider')]
    public function test_message(string $classname, string $expectedmessage): void {
        $exception = new $classname();
        $this->assertInstanceOf(moodle_exception::class, $exception);
        $this->assertEquals($expectedmessage, $exception->getMessage());
    }

    /**
     * Data provider for the API token exception tests.
     *
     * @return array[]
     */
    public static function api_token_exception_provider(): array {
        return [
            'expired' => [
                expired_api_token_exception::class,
                get_string('expiredapitoken', 'error'),
            ],
            'invalid' => [
                invalid_api_token_exception::class,
                get_string('invalidapitoken', 'error'),
            ],
            'revoked' => [
                revoked_api_token_exception::class,
                get_string('invalidapitoken', 'error'),
            ],
        ];
    }
}
