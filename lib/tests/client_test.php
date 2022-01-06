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

/**
 * Unit test client_test.
 *
 * Unit test for testable functions in core/oauth2/client.php
 *
 * @copyright  2021 Peter Dias
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package    core
 */
class client_test extends advanced_testcase {
    /**
     * Uses the static dataset as feed-in
     *
     * @return array
     */
    public function map_response_provider(): array {
        return [
            "Nested objects syntax a-b-c syntax " => [
                [
                    "name-firstname" => "firstname",
                    "contact-phone-home" => "homenumber",
                ], [
                    "firstname" => "John",
                    "homenumber" => "020000000",
                ]
            ],
            "Nested objects syntax with array support a-b[0]-c syntax " => [
                [
                    "name-firstname" => "firstname",
                    "contact-phone-home" => "homenumber",
                    "picture[0]-url" => "urltest",
                ], [
                    "firstname" => "John",
                    "homenumber" => "020000000",
                    "urltest" => "www.google.com",
                ]
            ],
            "Nested objects syntax with array support a-b-0-c syntax " => [
                [
                    "name-firstname" => "firstname",
                    "contact-phone-home" => "homenumber",
                    "picture-0-url" => "urltest",
                ], [
                    "firstname" => "John",
                    "homenumber" => "020000000",
                    "urltest" => "www.google.com",
                ]
            ],
            "Nested objects syntax with array support a-b-0-c syntax with non-existent nodes" => [
                [
                    "name-firstname" => "firstname",
                    "contact-phone-home" => "homenumber",
                    "picture-0-url-url" => "urltest",
                ], [
                    "firstname" => "John",
                    "homenumber" => "020000000",
                ]
            ],
        ];
    }

    /**
     * Test the map_userinfo_to_fields function
     *
     * @dataProvider map_response_provider
     * @param array $mapping
     * @param array $expected
     * @throws ReflectionException
     */
    public function test_map_userinfo_to_fields(array $mapping, array $expected) {
        $dataset = [
            "name" => (object) [
                "firstname" => "John",
                "lastname" => "Doe",
            ],
            "contact" => (object) [
                "email" => "john@example.com",
                "phone" => (object) [
                    "mobile" => "010000000",
                    "home" => "020000000"
                ],
            ],
            "picture" => [
                [
                    "url" => "www.google.com",
                    "description" => "This is a URL",
                ],
                [
                    "url" => "www.facebook.com",
                    "description" => "This is another URL",
                ]
            ]
        ];

        $method = new ReflectionMethod("core\oauth2\client", "map_userinfo_to_fields");
        $method->setAccessible(true);

        $issuer = new \core\oauth2\issuer(0);
        $mockbuilder = $this->getMockBuilder('core\oauth2\client');
        $mockbuilder->onlyMethods(['get_userinfo_mapping']);
        $mockbuilder->setConstructorArgs([$issuer, "", ""]);

        $mock = $mockbuilder->getMock();
        $mock->expects($this->once())
            ->method('get_userinfo_mapping')
            ->will($this->returnValue($mapping));
        $this->assertSame($expected, $method->invoke($mock, (object) $dataset));
    }
}
