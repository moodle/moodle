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

namespace enrol_lti\local\ltiadvantage\utility;

/**
 * Test cases for the enrol_lti\local\ltiadvantage\utility\message_helper utility class.
 *
 * @package    enrol_lti
 * @copyright  2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \enrol_lti\local\ltiadvantage\utility\message_helper
 */
class message_helper_test extends \base_testcase {

    /**
     * Test the static helper is_instructor_launch.
     *
     * @dataProvider message_roles_provider
     * @param array $jwtdata the mock JWT data from a launch.
     * @param bool $expected the expected return of is_instructor_launch() given the JWT data.
     * @covers ::is_instructor_launch
     */
    public function test_is_instructor_launch(array $jwtdata, bool $expected): void {
        $this->assertEquals($expected, message_helper::is_instructor_launch($jwtdata));
    }

    /**
     * Data provider for testing role helpers.
     *
     * @return array the array of test JWT data.
     */
    public static function message_roles_provider(): array {
        return [
            'Roles claim present, includes learner role only' => [
                'jwtdata' => [
                    'https://purl.imsglobal.org/spec/lti/claim/roles' => [
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner'
                    ]
                ],
                'expected' => false
            ],
            'Roles claim present, includes teacher role only' => [
                'jwtdata' => [
                    'https://purl.imsglobal.org/spec/lti/claim/roles' => [
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Instructor'
                    ]
                ],
                'expected' => true
            ],
            'Roles claim present, includes admin role only' => [
                'jwtdata' => [
                    'https://purl.imsglobal.org/spec/lti/claim/roles' => [
                        'http://purl.imsglobal.org/vocab/lis/v2/institution/person#Administrator'
                    ]
                ],
                'expected' => true
            ],
            'Roles claim present, includes learner and teacher roles' => [
                'jwtdata' => [
                    'https://purl.imsglobal.org/spec/lti/claim/roles' => [
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Instructor',
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner'
                    ]
                ],
                'expected' => true
            ],
            'Roles claim present, includes instructor role using legacy short name and learner role' => [
                'jwtdata' => [
                    'https://purl.imsglobal.org/spec/lti/claim/roles' => [
                        'Instructor',
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner'
                    ]
                ],
                'expected' => true
            ],
            'Roles claim empty' => [
                'jwtdata' => [
                    'https://purl.imsglobal.org/spec/lti/claim/roles' => [],
                ],
                'expected' => false
            ],
            'Roles claim not present' => [
                'jwtdata' => [],
                'expected' => false
            ],
        ];
    }
}
