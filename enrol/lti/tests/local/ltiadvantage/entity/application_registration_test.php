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
 * Tests for application_registration.
 *
 * @package enrol_lti
 * @copyright 2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \enrol_lti\local\ltiadvantage\entity\application_registration
 */
class application_registration_test extends \advanced_testcase {

    /**
     * Test the creation of an application_registration instance.
     *
     * @dataProvider creation_data_provider
     * @param array $args the arguments to the creation method.
     * @param array $expectations various expectations for the test cases.
     * @covers ::create
     */
    public function test_creation(array $args, array $expectations) {
        if ($expectations['valid']) {
            $reg = application_registration::create(...array_values($args));
            $this->assertEquals($args['name'], $reg->get_name());
            $this->assertEquals($args['platformid'], $reg->get_platformid());
            $this->assertEquals($args['clientid'], $reg->get_clientid());
            $this->assertEquals($args['authrequesturl'], $reg->get_authenticationrequesturl());
            $this->assertEquals($args['jwksurl'], $reg->get_jwksurl());
            $this->assertEquals($args['accesstokenurl'], $reg->get_accesstokenurl());
            $expectedid = $args['id'] ?? null;
            $this->assertEquals($expectedid, $reg->get_id());
        } else {
            $this->expectException($expectations['exception']);
            $this->expectExceptionMessage($expectations['exceptionmessage']);
            application_registration::create(...array_values($args));
        }
    }

    /**
     * Data provider for testing the creation of application_registration instances.
     *
     * @return array the data for testing.
     */
    public function creation_data_provider(): array {
        return [
            'Valid, only required args provided' => [
                'args' => [
                    'name' => 'Platform X',
                    'platformid' => new \moodle_url('https://lms.example.com'),
                    'clientid' => 'client-id-12345',
                    'authrequesturl' => new \moodle_url('https://lms.example.com/auth'),
                    'jwksurl' => new \moodle_url('https://lms.example.com/jwks'),
                    'accesstokenurl' => new \moodle_url('https://lms.example.com/token'),
                ],
                'expectations' => [
                    'valid' => true
                ]
            ],
            'Valid, all args provided' => [
                'args' => [
                    'name' => 'Platform X',
                    'platformid' => new \moodle_url('https://lms.example.com'),
                    'clientid' => 'client-id-12345',
                    'authrequesturl' => new \moodle_url('https://lms.example.com/auth'),
                    'jwksurl' => new \moodle_url('https://lms.example.com/jwks'),
                    'accesstokenurl' => new \moodle_url('https://lms.example.com/token'),
                    'id' => 24
                ],
                'expectations' => [
                    'valid' => true
                ]
            ],
            'Invalid, empty name provided' => [
                'args' => [
                    'name' => '',
                    'platformid' => new \moodle_url('https://lms.example.com'),
                    'clientid' => 'client-id-12345',
                    'authrequesturl' => new \moodle_url('https://lms.example.com/auth'),
                    'jwksurl' => new \moodle_url('https://lms.example.com/jwks'),
                    'accesstokenurl' => new \moodle_url('https://lms.example.com/token'),
                ],
                'expectations' => [
                    'valid' => false,
                    'exception' => \coding_exception::class,
                    'exceptionmessage' => "Invalid 'name' arg. Cannot be an empty string."
                ]
            ],
            'Invalid, empty clientid provided' => [
                'args' => [
                    'name' => 'Platform X',
                    'platformid' => new \moodle_url('https://lms.example.com'),
                    'clientid' => '',
                    'authrequesturl' => new \moodle_url('https://lms.example.com/auth'),
                    'jwksurl' => new \moodle_url('https://lms.example.com/jwks'),
                    'accesstokenurl' => new \moodle_url('https://lms.example.com/token'),
                ],
                'expectations' => [
                    'valid' => false,
                    'exception' => \coding_exception::class,
                    'exceptionmessage' => "Invalid 'clientid' arg. Cannot be an empty string."
                ]
            ]
        ];
    }

    /**
     * Test the factory method for creating a tool deployment associated with the registration instance.
     *
     * @dataProvider add_tool_deployment_data_provider
     * @param array $args the arguments to the creation method.
     * @param array $expectations various expectations for the test cases.
     * @covers ::add_tool_deployment
     */
    public function test_add_tool_deployment(array $args, array $expectations) {

        if ($expectations['valid']) {
            $reg = application_registration::create(...array_values($args['registration']));
            $deployment = $reg->add_tool_deployment(...array_values($args['deployment']));
            $this->assertInstanceOf(deployment::class, $deployment);
            $this->assertEquals($args['deployment']['name'], $deployment->get_deploymentname());
            $this->assertEquals($args['deployment']['deploymentid'], $deployment->get_deploymentid());
            $this->assertEquals($reg->get_id(), $deployment->get_registrationid());
        } else {
            $reg = application_registration::create(...array_values($args['registration']));
            $this->expectException($expectations['exception']);
            $this->expectExceptionMessage($expectations['exceptionmessage']);
            $reg->add_tool_deployment(...array_values($args['deployment']));
        }
    }

    /**
     * Data provider for testing the add_tool_deployment method.
     *
     * @return array the array of test data.
     */
    public function add_tool_deployment_data_provider(): array {
        return [
            'Valid, contains id on registration and valid deployment data provided' => [
                'args' => [
                    'registration' => [
                        'name' => 'Platform X',
                        'platformid' => new \moodle_url('https://lms.example.com'),
                        'clientid' => 'client-id-12345',
                        'authrequesturl' => new \moodle_url('https://lms.example.com/auth'),
                        'jwksurl' => new \moodle_url('https://lms.example.com/jwks'),
                        'accesstokenurl' => new \moodle_url('https://lms.example.com/token'),
                        'id' => 24
                    ],
                    'deployment' => [
                        'name' => 'Deployment at site level',
                        'deploymentid' => 'id-123abc'
                    ]
                ],
                'expectations' => [
                    'valid' => true,
                ]
            ],
            'Invalid, missing id on registration' => [
                'args' => [
                    'registration' => [
                        'name' => 'Platform X',
                        'platformid' => new \moodle_url('https://lms.example.com'),
                        'clientid' => 'client-id-12345',
                        'authrequesturl' => new \moodle_url('https://lms.example.com/auth'),
                        'jwksurl' => new \moodle_url('https://lms.example.com/jwks'),
                        'accesstokenurl' => new \moodle_url('https://lms.example.com/token'),
                    ],
                    'deployment' => [
                        'name' => 'Deployment at site level',
                        'deploymentid' => 'id-123abc'
                    ]
                ],
                'expectations' => [
                    'valid' => false,
                    'exception' => \coding_exception::class,
                    'exceptionmessage' => "Can't add deployment to a resource_link that hasn't first been saved."
                ]
            ],
            'Invalid, id present on registration but empty deploymentname' => [
                'args' => [
                    'registration' => [
                        'name' => 'Platform X',
                        'platformid' => new \moodle_url('https://lms.example.com'),
                        'clientid' => 'client-id-12345',
                        'authrequesturl' => new \moodle_url('https://lms.example.com/auth'),
                        'jwksurl' => new \moodle_url('https://lms.example.com/jwks'),
                        'accesstokenurl' => new \moodle_url('https://lms.example.com/token'),
                        'id' => 24
                    ],
                    'deployment' => [
                        'name' => '',
                        'deploymentid' => 'id-123abc'
                    ]
                ],
                'expectations' => [
                    'valid' => false,
                    'exception' => \coding_exception::class,
                    'exceptionmessage' => "Invalid 'deploymentname' arg. Cannot be an empty string."
                ]
            ],
            'Invalid, id present on registration but empty deploymentid' => [
                'args' => [
                    'registration' => [
                        'name' => 'Platform X',
                        'platformid' => new \moodle_url('https://lms.example.com'),
                        'clientid' => 'client-id-12345',
                        'authrequesturl' => new \moodle_url('https://lms.example.com/auth'),
                        'jwksurl' => new \moodle_url('https://lms.example.com/jwks'),
                        'accesstokenurl' => new \moodle_url('https://lms.example.com/token'),
                        'id' => 24
                    ],
                    'deployment' => [
                        'name' => 'Site deployment',
                        'deploymentid' => ''
                    ]
                ],
                'expectations' => [
                    'valid' => false,
                    'exception' => \coding_exception::class,
                    'exceptionmessage' => "Invalid 'deploymentid' arg. Cannot be an empty string."
                ]
            ]
        ];
    }
}
