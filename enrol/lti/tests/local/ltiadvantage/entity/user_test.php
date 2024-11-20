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
 * Tests for user.
 *
 * @package enrol_lti
 * @copyright 2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \enrol_lti\local\ltiadvantage\entity\user
 */
final class user_test extends \advanced_testcase {

    /**
     * Test creation of a user instance using the factory method.
     *
     * @dataProvider create_data_provider
     * @param array $args the arguments to the creation method.
     * @param array $expectations various expectations for the test cases.
     * @covers ::create
     */
    public function test_create(array $args, array $expectations): void {
        if ($expectations['valid']) {
            $user = user::create(...array_values($args));
            $this->assertInstanceOf(user::class, $user);
            $this->assertEquals($expectations['id'], $user->get_id());
            $this->assertEquals($expectations['localid'], $user->get_localid());
            $this->assertEquals($expectations['resourcelinkid'], $user->get_resourcelinkid());
            $this->assertEquals($expectations['resourceid'], $user->get_resourceid());
            $this->assertEquals($expectations['deploymentid'], $user->get_deploymentid());
            $this->assertEquals($expectations['sourceid'], $user->get_sourceid());
            $this->assertEquals($expectations['lang'], $user->get_lang());
            $this->assertEquals($expectations['timezone'], $user->get_timezone());
            $this->assertEquals($expectations['city'], $user->get_city());
            $this->assertEquals($expectations['country'], $user->get_country());
            $this->assertEquals($expectations['institution'], $user->get_institution());
            $this->assertEquals($expectations['maildisplay'], $user->get_maildisplay());
            $this->assertEquals($expectations['lastgrade'], $user->get_lastgrade());
            $this->assertEquals($expectations['lastaccess'], $user->get_lastaccess());
        } else {
            $this->expectException($expectations['exception']);
            $this->expectExceptionMessage($expectations['exceptionmessage']);
            user::create(...array_values($args));
        }
    }

    /**
     * Data provider for testing the user::create() method.
     *
     * @return array the data for testing.
     */
    public static function create_data_provider(): array {
        global $CFG;
        return [
            'Valid create, only required args provided' => [
                'args' => [
                    'resourceid' => 22,
                    'userid' => 2,
                    'deploymentid' => 33,
                    'sourceid' => 'user-id-123',
                    'lang' => $CFG->lang,
                    'timezone' => '99'
                ],
                'expectations' => [
                    'valid' => true,
                    'resourceid' => 22,
                    'deploymentid' => 33,
                    'sourceid' => 'user-id-123',
                    'lang' => $CFG->lang,
                    'timezone' => '99',
                    'city' => '',
                    'country' => '',
                    'institution' => '',
                    'maildisplay' => 2,
                    'lastgrade' => 0.0,
                    'lastaccess' => null,
                    'id' => null,
                    'localid' => 2,
                    'resourcelinkid' => null,
                ]
            ],
            'Valid create, all args provided explicitly' => [
                'args' => [
                    'resourceid' => 22,
                    'userid' => 2,
                    'deploymentid' => 33,
                    'sourceid' => 'user-id-123',
                    'lang' => $CFG->lang,
                    'timezone' => '99',
                    'city' => 'Melbourne',
                    'country' => 'AU',
                    'institution' => 'My institution',
                    'maildisplay' => 1,
                    'lastgrade' => 50.55,
                    'lastaccess' => 14567888,
                    'resourcelinkid' => 44,
                    'id' => 22
                ],
                'expectations' => [
                    'valid' => true,
                    'resourceid' => 22,
                    'deploymentid' => 33,
                    'sourceid' => 'user-id-123',
                    'lang' => $CFG->lang,
                    'timezone' => '99',
                    'city' => 'Melbourne',
                    'country' => 'AU',
                    'institution' => 'My institution',
                    'maildisplay' => 1,
                    'lastgrade' => 50.55,
                    'lastaccess' => 14567888,
                    'resourcelinkid' => 44,
                    'localid' => 2,
                    'id' => 22,
                ]
            ],
            'Valid create, optional args explicitly nulled for default values' => [
                'args' => [
                    'resourceid' => 22,
                    'userid' => 2,
                    'deploymentid' => 33,
                    'sourceid' => 'user-id-123',
                    'lang' => $CFG->lang,
                    'timezone' => '99',
                    'city' => 'Melbourne',
                    'country' => 'AU',
                    'institution' => 'My institution',
                    'maildisplay' => null,
                    'lastgrade' => null,
                    'lastaccess' => null,
                    'resourcelinkid' => null,
                    'id' => null

                ],
                'expectations' => [
                    'valid' => true,
                    'resourceid' => 22,
                    'deploymentid' => 33,
                    'sourceid' => 'user-id-123',
                    'lang' => $CFG->lang,
                    'timezone' => '99',
                    'city' => 'Melbourne',
                    'country' => 'AU',
                    'institution' => 'My institution',
                    'maildisplay' => 2,
                    'lastgrade' => 0.0,
                    'lastaccess' => null,
                    'resourcelinkid' => null,
                    'localid' => 2,
                    'id' => null
                ]
            ],
            'Invalid create, lang with bad value (vvvv not installed)' => [
                'args' => [
                    'resourceid' => 22,
                    'userid' => 2,
                    'deploymentid' => 33,
                    'sourceid' => 'user-id-123',
                    'lang' => 'vvvv',
                    'timezone' => '99',
                ],
                'expectations' => [
                    'valid' => false,
                    'exception' => \coding_exception::class,
                    'exceptionmessage' => "Invalid lang 'vvvv' provided."
                ]
            ],
            'Invalid create, timezone with bad value' => [
                'args' => [
                    'resourceid' => 22,
                    'userid' => 2,
                    'deploymentid' => 33,
                    'sourceid' => 'user-id-123',
                    'lang' => $CFG->lang,
                    'timezone' => 'NOT/FOUND',
                ],
                'expectations' => [
                    'valid' => false,
                    'exception' => \coding_exception::class,
                    'exceptionmessage' => "Invalid timezone 'NOT/FOUND' provided."
                ]
            ],
            'Invalid create, explicitly provided country with bad value' => [
                'args' => [
                    'resourceid' => 22,
                    'userid' => 2,
                    'deploymentid' => 33,
                    'sourceid' => 'user-id-123',
                    'lang' => $CFG->lang,
                    'timezone' => '99',
                    'city' => '',
                    'country' => 'FFF',
                ],
                'expectations' => [
                    'valid' => false,
                    'exception' => \coding_exception::class,
                    'exceptionmessage' => "Invalid country code 'FFF'."
                ]
            ],
            'Invalid create, explicit maildisplay with bad value' => [
                'args' => [
                    'resourceid' => 22,
                    'userid' => 2,
                    'deploymentid' => 33,
                    'sourceid' => 'user-id-123',
                    'lang' => $CFG->lang,
                    'timezone' => '99',
                    'city' => '',
                    'country' => '',
                    'institution' => '',
                    'maildisplay' => 3,
                ],
                'expectations' => [
                    'valid' => false,
                    'exception' => \coding_exception::class,
                    'exceptionmessage' => "Invalid maildisplay value '3'. Must be in the range {0..2}."
                ]
            ],
        ];
    }

    /**
     * Test creation of a user instance from a resource link.
     *
     * @dataProvider create_from_resource_link_data_provider
     * @param array $args the arguments to the creation method.
     * @param array $expectations various expectations for the test cases.
     * @covers ::create_from_resource_link
     */
    public function test_create_from_resource_link(array $args, array $expectations): void {
        if ($expectations['valid']) {
            $user = user::create_from_resource_link(...array_values($args));
            $this->assertInstanceOf(user::class, $user);
            $this->assertEquals($expectations['id'], $user->get_id());
            $this->assertEquals($expectations['localid'], $user->get_localid());
            $this->assertEquals($expectations['resourcelinkid'], $user->get_resourcelinkid());
            $this->assertEquals($expectations['resourceid'], $user->get_resourceid());
            $this->assertEquals($expectations['deploymentid'], $user->get_deploymentid());
            $this->assertEquals($expectations['sourceid'], $user->get_sourceid());
            $this->assertEquals($expectations['lang'], $user->get_lang());
            $this->assertEquals($expectations['city'], $user->get_city());
            $this->assertEquals($expectations['country'], $user->get_country());
            $this->assertEquals($expectations['institution'], $user->get_institution());
            $this->assertEquals($expectations['timezone'], $user->get_timezone());
            $this->assertEquals($expectations['maildisplay'], $user->get_maildisplay());
            $this->assertEquals($expectations['lastgrade'], $user->get_lastgrade());
            $this->assertEquals($expectations['lastaccess'], $user->get_lastaccess());
        } else {
            $this->expectException($expectations['exception']);
            $this->expectExceptionMessage($expectations['exceptionmessage']);
            user::create_from_resource_link(...array_values($args));
        }
    }

    /**
     * Data provider used in testing the user::create_from_resource_link() method.
     *
     * @return array the data for testing.
     */
    public static function create_from_resource_link_data_provider(): array {
        global $CFG;
        return [
            'Valid creation, all args provided explicitly' => [
                'args' => [
                    'resourcelinkid' => 11,
                    'resourceid' => 22,
                    'userid' => 2,
                    'deploymentid' => 33,
                    'sourceid' => 'user-id-123',
                    'lang' => $CFG->lang,
                    'timezone' => '99',
                    'city' => 'Melbourne',
                    'country' => 'AU',
                    'institution' => 'platform',
                    'maildisplay' => 1
                ],
                'expectations' => [
                    'valid' => true,
                    'id' => null,
                    'localid' => 2,
                    'resourcelinkid' => 11,
                    'resourceid' => 22,
                    'deploymentid' => 33,
                    'sourceid' => 'user-id-123',
                    'lang' => $CFG->lang,
                    'timezone' => '99',
                    'city' => 'Melbourne',
                    'country' => 'AU',
                    'institution' => 'platform',
                    'maildisplay' => 1,
                    'lastgrade' => 0.0,
                    'lastaccess' => null
                ]
            ],
            'Valid creation, only required args provided, explicit values' => [
                'args' => [
                    'resourcelinkid' => 11,
                    'resourceid' => 22,
                    'userid' => 2,
                    'deploymentid' => 33,
                    'sourceid' => 'user-id-123',
                    'lang' => $CFG->lang,
                    'timezone' => 'UTC'
                ],
                'expectations' => [
                    'valid' => true,
                    'id' => null,
                    'localid' => 2,
                    'resourcelinkid' => 11,
                    'resourceid' => 22,
                    'deploymentid' => 33,
                    'sourceid' => 'user-id-123',
                    'lang' => $CFG->lang,
                    'timezone' => 'UTC',
                    'city' => '',
                    'country' => '',
                    'institution' => '',
                    'maildisplay' => 2,
                    'lastgrade' => 0.0,
                    'lastaccess' => null
                ]
            ],
            'Invalid creation, only required args provided, empty sourceid' => [
                'args' => [
                    'resourcelinkid' => 11,
                    'resourceid' => 22,
                    'user' => 2,
                    'deploymentid' => 33,
                    'sourceid' => '',
                    'lang' => $CFG->lang,
                    'timezone' => 'UTC'
                ],
                'expectations' => [
                    'valid' => false,
                    'exception' => \coding_exception::class,
                    'exceptionmessage' => 'Invalid sourceid value. Cannot be an empty string.'
                ]
            ],
            'Invalid creation, only required args provided, empty lang' => [
                'args' => [
                    'resourcelinkid' => 11,
                    'resourceid' => 22,
                    'user' => 2,
                    'deploymentid' => 33,
                    'sourceid' => 'user-id-123',
                    'lang' => '',
                    'timezone' => 'UTC'
                ],
                'expectations' => [
                    'valid' => false,
                    'exception' => \coding_exception::class,
                    'exceptionmessage' => 'Invalid lang value. Cannot be an empty string.'
                ]
            ],
            'Invalid creation, only required args provided, empty timezone' => [
                'args' => [
                    'resourcelinkid' => 11,
                    'resourceid' => 22,
                    'userid' => 2,
                    'deploymentid' => 33,
                    'sourceid' => 'user-id-123',
                    'lang' => $CFG->lang,
                    'timezone' => ''
                ],
                'expectations' => [
                    'valid' => false,
                    'exception' => \coding_exception::class,
                    'exceptionmessage' => 'Invalid timezone value. Cannot be an empty string.'
                ]
            ],
            'Invalid creation, only required args provided, invalid lang (vvvv not installed)' => [
                'args' => [
                    'resourcelinkid' => 11,
                    'resourceid' => 22,
                    'userid' => 2,
                    'deploymentid' => 33,
                    'sourceid' => 'user-id-123',
                    'lang' => 'vvvv',
                    'timezone' => 'UTC'
                ],
                'expectations' => [
                    'valid' => false,
                    'exception' => \coding_exception::class,
                    'exceptionmessage' => "Invalid lang 'vvvv' provided."
                ]
            ],
            'Invalid creation, only required args provided, invalid timezone' => [
                'args' => [
                    'resourcelinkid' => 11,
                    'resourceid' => 22,
                    'userid' => 2,
                    'deploymentid' => 33,
                    'sourceid' => 'user-id-123',
                    'lang' => $CFG->lang,
                    'timezone' => 'NOT/FOUND'
                ],
                'expectations' => [
                    'valid' => false,
                    'exception' => \coding_exception::class,
                    'exceptionmessage' => "Invalid timezone 'NOT/FOUND' provided."
                ]
            ],
            'Invalid creation, all args provided explicitly, invalid country' => [
                'args' => [
                    'resourcelinkid' => 11,
                    'resourceid' => 22,
                    'userid' => 2,
                    'deploymentid' => 33,
                    'sourceid' => 'user-id-123',
                    'lang' => $CFG->lang,
                    'timezone' => '99',
                    'city' => 'Melbourne',
                    'country' => 'FFF',
                    'institution' => 'platform',
                    'maildisplay' => 1
                ],
                'expectations' => [
                    'valid' => false,
                    'exception' => \coding_exception::class,
                    'exceptionmessage' => "Invalid country code 'FFF'."
                ]
            ],
            'Invalid creation, all args provided explicitly, invalid maildisplay' => [
                'args' => [
                    'resourcelinkid' => 11,
                    'resourceid' => 22,
                    'userid' => 2,
                    'deploymentid' => 33,
                    'sourceid' => 'user-id-123',
                    'lang' => $CFG->lang,
                    'timezone' => '99',
                    'city' => 'Melbourne',
                    'country' => 'AU',
                    'institution' => 'platform',
                    'maildisplay' => 4
                ],
                'expectations' => [
                    'valid' => false,
                    'exception' => \coding_exception::class,
                    'exceptionmessage' => "Invalid maildisplay value '4'. Must be in the range {0..2}."
                ]
            ],
        ];
    }

    /**
     * Helper to create a simple, working user for testing.
     *
     * @return user a user instance.
     */
    protected function create_test_user(): user {
        global $CFG;
        $args = [
            'resourcelinkid' => 11,
            'resourceid' => 22,
            'userid' => 2,
            'deploymentid' => 33,
            'sourceid' => 'user-id-123',
            'lang' => $CFG->lang,
            'timezone' => 'UTC'
        ];
        return user::create_from_resource_link(...array_values($args));
    }


    /**
     * Test the behaviour of the user setters and getters.
     *
     * @dataProvider setters_getters_data_provider
     * @param string $methodname the name of the setter
     * @param mixed $arg the argument to the setter
     * @param array $expectations the array of expectations
     * @covers ::__construct
     */
    public function test_setters_and_getters(string $methodname, $arg, array $expectations): void {
        $user = $this->create_test_user();
        $setter = 'set_'.$methodname;
        $getter = 'get_'.$methodname;
        if ($expectations['valid']) {
            $user->$setter($arg);
            if (isset($expectations['expectedvalue'])) {
                $this->assertEquals($expectations['expectedvalue'], $user->$getter());
            } else {
                $this->assertEquals($arg, $user->$getter());
            }

        } else {
            $this->expectException($expectations['exception']);
            $this->expectExceptionMessage($expectations['exceptionmessage']);
            $user->$setter($arg);
        }
    }

    /**
     * Data provider for testing the user object setters.
     *
     * @return array the array of test data.
     */
    public static function setters_getters_data_provider(): array {
        global $CFG;
        return [
            'Testing set_resourcelinkid with valid id' => [
                'methodname' => 'resourcelinkid',
                'arg' => 8,
                'expectations' => [
                    'valid' => true,
                ]
            ],
            'Testing set_resourcelinkid with invalid id' => [
                'methodname' => 'resourcelinkid',
                'arg' => -1,
                'expectations' => [
                    'valid' => false,
                    'exception' => \coding_exception::class,
                    'exceptionmessage' => "Invalid resourcelinkid '-1' provided. Must be > 0."
                ]
            ],
            'Testing set_city with a non-empty string' => [
                'methodname' => 'city',
                'arg' => 'Melbourne',
                'expectations' => [
                    'valid' => true,
                ]
            ],
            'Testing set_city with an empty string' => [
                'methodname' => 'city',
                'arg' => '',
                'expectations' => [
                    'valid' => true,
                ]
            ],
            'Testing set_country with a valid country code' => [
                'methodname' => 'country',
                'arg' => 'AU',
                'expectations' => [
                    'valid' => true,
                ]
            ],
            'Testing set_country with an empty string' => [
                'methodname' => 'country',
                'arg' => '',
                'expectations' => [
                    'valid' => true,
                ]
            ],
            'Testing set_country with an invalid country code' => [
                'methodname' => 'country',
                'arg' => 'FFF',
                'expectations' => [
                    'valid' => false,
                    'exception' => \coding_exception::class,
                    'exceptionmessage' => "Invalid country code 'FFF'."
                ]
            ],
            'Testing set_institution with a non-empty string' => [
                'methodname' => 'institution',
                'arg' => 'Some institution',
                'expectations' => [
                    'valid' => true,
                ]
            ],
            'Testing set_institution with an empty string' => [
                'methodname' => 'institution',
                'arg' => '',
                'expectations' => [
                    'valid' => true,
                ]
            ],
            'Testing set_timezone with a valid real timezone' => [
                'methodname' => 'timezone',
                'arg' => 'Pacific/Wallis',
                'expectations' => [
                    'valid' => true,
                ]
            ],
            'Testing set_timezone with a valid server timezone value' => [
                'methodname' => 'timezone',
                'arg' => '99',
                'expectations' => [
                    'valid' => true,
                ]
            ],
            'Testing set_timezone with an invalid timezone value' => [
                'methodname' => 'timezone',
                'arg' => 'NOT/FOUND',
                'expectations' => [
                    'valid' => false,
                    'exception' => \coding_exception::class,
                    'exceptionmessage' => "Invalid timezone 'NOT/FOUND' provided."
                ]
            ],
            'Testing set_maildisplay with a valid int: 0' => [
                'methodname' => 'maildisplay',
                'arg' => '0',
                'expectations' => [
                    'valid' => true,
                ]
            ],
            'Testing set_maildisplay with a valid int: 1' => [
                'methodname' => 'maildisplay',
                'arg' => '1',
                'expectations' => [
                    'valid' => true,
                ]
            ],
            'Testing set_maildisplay with a valid int: 2' => [
                'methodname' => 'maildisplay',
                'arg' => '2',
                'expectations' => [
                    'valid' => true,
                ]
            ],
            'Testing set_maildisplay with invalid int: -1' => [
                'methodname' => 'maildisplay',
                'arg' => '-1',
                'expectations' => [
                    'valid' => false,
                    'exception' => \coding_exception::class,
                    'exceptionmessage' => "Invalid maildisplay value '-1'. Must be in the range {0..2}."
                ]
            ],
            'Testing set_maildisplay with invalid int: 3' => [
                'methodname' => 'maildisplay',
                'arg' => '3',
                'expectations' => [
                    'valid' => false,
                    'exception' => \coding_exception::class,
                    'exceptionmessage' => "Invalid maildisplay value '3'. Must be in the range {0..2}."
                ]
            ],
            'Testing set_lang with valid lang code' => [
                'methodname' => 'lang',
                'arg' => $CFG->lang,
                'expectations' => [
                    'valid' => true,
                ]
            ],
            'Testing set_lang with an empty string' => [
                'methodname' => 'lang',
                'arg' => '',
                'expectations' => [
                    'valid' => false,
                    'exception' => \coding_exception::class,
                    'exceptionmessage' => 'Invalid lang value. Cannot be an empty string.'
                ]
            ],
            'Testing set_lang with invalid lang code' => [
                'methodname' => 'lang',
                'arg' => 'ff',
                'expectations' => [
                    'valid' => false,
                    'exception' => \coding_exception::class,
                    'exceptionmessage' => "Invalid lang 'ff' provided."
                ]
            ],
            'Testing set_lastgrade with valid grade' => [
                'methodname' => 'lastgrade',
                'arg' => 0.0,
                'expectations' => [
                    'valid' => true
                ]
            ],
            'Testing set_lastgrade with valid non zero grade' => [
                'methodname' => 'lastgrade',
                'arg' => 150.0,
                'expectations' => [
                    'valid' => true
                ]
            ],
            'Testing set_lastgrade with valid non zero long decimal grade' => [
                'methodname' => 'lastgrade',
                'arg' => 150.777779,
                'expectations' => [
                    'valid' => true,
                    'expectedvalue' => 150.77778
                ]
            ],
            'Testing set_lastaccess with valid time' => [
                'methodname' => 'lastaccess',
                'arg' => 4,
                'expectations' => [
                    'valid' => true
                ]
            ],
            'Testing set_lastaccess with invalid time' => [
                'methodname' => 'lastaccess',
                'arg' => -1,
                'expectations' => [
                    'valid' => false,
                    'exception' => \coding_exception::class,
                    'exceptionmessage' => 'Cannot set negative access time'
                ]
            ],
        ];
    }
}
