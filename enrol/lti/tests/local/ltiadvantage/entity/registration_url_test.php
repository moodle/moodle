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

namespace enrol_lti\local\ltiadvantage\entity;

/**
 * Tests for registration_url.
 *
 * @package enrol_lti
 * @copyright 2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \enrol_lti\local\ltiadvantage\entity\registration_url
 */
class registration_url_test extends \advanced_testcase {

    /**
     * Test the creation and validation of a registration_url instance.
     *
     * @dataProvider registration_url_data_provider
     * @param array $args the arguments to the constructor.
     * @param array $expectations various expectations for the test cases.
     * @covers ::__construct
     */
    public function test_registration_url(array $args, array $expectations) {
        if ($expectations['valid']) {
            $regurl = new registration_url(...array_values($args));
            $this->assertEquals($expectations['expirytime'], $regurl->get_expiry_time());
            if (!empty($expectations['token'])) {
                $this->assertEquals($expectations['token'], $regurl->param('token'));
            } else {
                $this->assertNotEmpty($regurl->param('token'));
            }
        } else {
            $this->expectException($expectations['exception']);
            $this->expectExceptionMessage($expectations['exceptionmessage']);
            new registration_url(...array_values($args));
        }
    }

    /**
     * Data provider used to test registration_url object creation.
     *
     * @return array the array of test data.
     */
    public function registration_url_data_provider(): array {
        return [
            'Valid, required args only, expiry 0' => [
                'args' => [
                    'expirytime' => 0
                ],
                'expectations' => [
                    'valid' => true,
                    'expirytime' => 0,
                ]
            ],
            'Valid, required args only, expiry positive' => [
                'args' => [
                    'expirytime' => 50
                ],
                'expectations' => [
                    'valid' => true,
                    'expirytime' => 50,
                ]
            ],
            'Invalid, required args only, expiry negative' => [
                'args' => [
                    'expirytime' => -70
                ],
                'expectations' => [
                    'valid' => false,
                    'exception' => \coding_exception::class,
                    'exceptionmessage' => 'Invalid registration_url expiry time. Must be greater than or equal to 0.'
                ]
            ],
            'Valid, all args provided' => [
                'args' => [
                    'expirytime' => 56,
                    'token' => 'token-abcde-12345'
                ],
                'expectations' => [
                    'valid' => true,
                    'expirytime' => 56,
                    'token' => 'token-abcde-12345'
                ]
            ]
        ];
    }
}
