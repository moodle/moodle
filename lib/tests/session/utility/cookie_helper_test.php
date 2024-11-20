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

namespace core\session\utility;

/**
 * Tests for the cookie_helper utility class.
 *
 * @package    core
 * @copyright  2024 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \core\session\utility\cookie_helper
 */
class cookie_helper_test extends \advanced_testcase {

    /**
     * Testing cookie_response_headers_add_attributes().
     *
     * @dataProvider cookie_response_headers_provider
     *
     * @param array $headers the headers to search
     * @param array $cookienames the cookienames to match
     * @param array $attributes the attributes to add
     * @param bool $casesensitive whether to do a case-sensitive lookup for the attribute
     * @param array $expectedheaders the expected, updated headers
     * @return void
     */
    public function test_cookie_response_headers_add_attributes(array $headers, array $cookienames, array $attributes,
            bool $casesensitive, array $expectedheaders): void {

        $updated = cookie_helper::cookie_response_headers_add_attributes($headers, $cookienames, $attributes, $casesensitive);
        $this->assertEquals($expectedheaders, $updated);
    }

    /**
     * Data provider for testing cookie_response_headers_add_attributes().
     *
     * @return array the inputs and expected outputs.
     */
    public static function cookie_response_headers_provider(): array {
        return [
            'Only one matching cookie header, without any of the attributes' => [
                'headers' => [
                    'Set-Cookie: testcookie=value; path=/test/; HttpOnly;',
                ],
                'cookienames' => [
                    'testcookie',
                ],
                'attributes' => [
                    'Partitioned',
                    'SameSite=None',
                    'Secure',
                ],
                'casesensitive' => false,
                'output' => [
                    'Set-Cookie: testcookie=value; path=/test/; HttpOnly; Partitioned; SameSite=None; Secure;',
                ],
            ],
            'Several matching cookie headers, without attributes' => [
                'headers' => [
                    'Set-Cookie: testcookie=value; path=/test/; HttpOnly;',
                    'Set-Cookie: mytestcookie=value; path=/test/; HttpOnly;',
                ],
                'cookienames' => [
                    'testcookie',
                    'mytestcookie',
                ],
                'attributes' => [
                    'Partitioned',
                    'SameSite=None',
                    'Secure',
                ],
                'casesensitive' => false,
                'output' => [
                    'Set-Cookie: testcookie=value; path=/test/; HttpOnly; Partitioned; SameSite=None; Secure;',
                    'Set-Cookie: mytestcookie=value; path=/test/; HttpOnly; Partitioned; SameSite=None; Secure;',
                ],
            ],
            'Several matching cookie headers, several non-matching, all missing all attributes' => [
                'headers' => [
                    'Set-Cookie: testcookie=value; path=/test/; HttpOnly;',
                    'Set-Cookie: mytestcookie=value; path=/test/; HttpOnly;',
                    'Set-Cookie: anothertestcookie=value; path=/test/; HttpOnly;',
                ],
                'cookienames' => [
                    'testcookie',
                    'mytestcookie',
                    'blah',
                    'etc',
                ],
                'attributes' => [
                    'Partitioned',
                    'SameSite=None',
                    'Secure',
                ],
                'casesensitive' => false,
                'output' => [
                    'Set-Cookie: testcookie=value; path=/test/; HttpOnly; Partitioned; SameSite=None; Secure;',
                    'Set-Cookie: mytestcookie=value; path=/test/; HttpOnly; Partitioned; SameSite=None; Secure;',
                    'Set-Cookie: anothertestcookie=value; path=/test/; HttpOnly;',
                ],
            ],
            'Matching cookie headers, some with existing attributes' => [
                'headers' => [
                    'Set-Cookie: testcookie=value; path=/test/; secure; HttpOnly; Partitioned; SameSite=None',
                    'Set-Cookie: mytestcookie=value; path=/test/; secure; HttpOnly; SameSite=None',
                ],
                'cookienames' => [
                    'testcookie',
                    'mytestcookie',
                    'etc',
                ],
                'attributes' => [
                    'Partitioned',
                    'SameSite=None',
                    'Secure',
                ],
                'casesensitive' => false,
                'output' => [
                    'Set-Cookie: testcookie=value; path=/test/; secure; HttpOnly; Partitioned; SameSite=None',
                    'Set-Cookie: mytestcookie=value; path=/test/; secure; HttpOnly; SameSite=None; Partitioned;',
                ],
            ],
            'Matching headers, some with existing attributes, case sensitive' => [
                'headers' => [
                    'Set-Cookie: testcookie=value; path=/test/; secure; HttpOnly; SameSite=None; partitioned',
                    'Set-Cookie: mytestcookie=value; path=/test/; secure; HttpOnly; SameSite=None',
                ],
                'cookienames' => [
                    'testcookie',
                    'mytestcookie',
                    'etc',
                ],
                'attributes' => [
                    'Partitioned',
                    'SameSite=None',
                    'Secure',
                ],
                'casesensitive' => true,
                'output' => [
                    'Set-Cookie: testcookie=value; path=/test/; secure; HttpOnly; SameSite=None; partitioned; Partitioned; Secure;',
                    'Set-Cookie: mytestcookie=value; path=/test/; secure; HttpOnly; SameSite=None; Partitioned; Secure;',
                ],
            ],
            'Empty list of cookie names to match, so unmodified inputs' => [
                'headers' => [
                    'Set-Cookie: testcookie=value; path=/test/; secure; HttpOnly; SameSite=None; partitioned',
                    'Set-Cookie: mytestcookie=value; path=/test/; secure; HttpOnly; SameSite=None',
                ],
                'cookienames' => [],
                'attributes' => [
                    'Partitioned',
                    'SameSite=None',
                    'Secure',
                ],
                'casesensitive' => false,
                'output' => [
                    'Set-Cookie: testcookie=value; path=/test/; secure; HttpOnly; SameSite=None; partitioned',
                    'Set-Cookie: mytestcookie=value; path=/test/; secure; HttpOnly; SameSite=None',
                ],
            ],
            'Empty list of attributes to set, so unmodified inputs' => [
                'headers' => [
                    'Set-Cookie: testcookie=value; path=/test/; secure; HttpOnly; SameSite=None; partitioned',
                    'Set-Cookie: mytestcookie=value; path=/test/; secure; HttpOnly; SameSite=None',
                ],
                'cookienames' => [
                    'testcookie',
                    'mycookie',
                ],
                'attributes' => [],
                'casesensitive' => false,
                'output' => [
                    'Set-Cookie: testcookie=value; path=/test/; secure; HttpOnly; SameSite=None; partitioned',
                    'Set-Cookie: mytestcookie=value; path=/test/; secure; HttpOnly; SameSite=None',
                ],
            ],
            'Other HTTP headers, some matching Set-Cookie, some not' => [
                'headers' => [
                    'Authorization: blah',
                    'Set-Cookie: testcookie=value; path=/test/; secure; HttpOnly; SameSite=None; Partitioned',
                    'Set-Cookie: mytestcookie=value; path=/test/; secure; HttpOnly; SameSite=None',
                ],
                'cookienames' => [
                    'testcookie',
                    'mytestcookie',
                ],
                'attributes' => [
                    'Partitioned',
                    'SameSite=None',
                    'Secure',
                ],
                'casesensitive' => false,
                'output' => [
                    'Authorization: blah',
                    'Set-Cookie: testcookie=value; path=/test/; secure; HttpOnly; SameSite=None; Partitioned',
                    'Set-Cookie: mytestcookie=value; path=/test/; secure; HttpOnly; SameSite=None; Partitioned;',
                ],
            ],
        ];
    }
}
