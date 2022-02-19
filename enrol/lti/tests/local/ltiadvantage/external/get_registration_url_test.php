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

namespace enrol_lti\local\ltiadvantage\external;

use external_api;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/webservice/tests/helpers.php');


/**
 * Test class for enrol_lti\local\ltiadvantage\external\get_registration_url.
 *
 * @package    enrol_lti
 * @copyright  2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \enrol_lti\local\ltiadvantage\external\get_registration_url
 */
class get_registration_url_test extends \externallib_advanced_testcase {

    /**
     * Test the behaviour of get_registration_url() as an admin user with permissions.
     *
     * @covers ::execute
     */
    public function test_get_registration_url() {
        global $CFG;
        $this->resetAfterTest();
        $this->setAdminUser();

        // Get a registration URL before one has been created.
        $result = get_registration_url::execute();
        $result = external_api::clean_returnvalue(get_registration_url::execute_returns(), $result);
        $this->assertEquals('', $result['url']);

        // Get a registration URL, creating one in the process.
        $result = get_registration_url::execute(true);
        $result = external_api::clean_returnvalue(get_registration_url::execute_returns(), $result);
        $this->assertStringContainsString($CFG->wwwroot . '/enrol/lti/register.php?token=', $result['url']);

        // Get a registration URL again, this time confirming we get back the still-valid URL.
        $result2 = get_registration_url::execute();
        $result2 = external_api::clean_returnvalue(get_registration_url::execute_returns(), $result2);
        $this->assertEquals($result['url'], $result2['url']);

        // Get a registration URL again, this time confirming we get back the still-valid URL despite asking for
        // autocreation.
        $result3 = get_registration_url::execute(true);
        $result3 = external_api::clean_returnvalue(get_registration_url::execute_returns(), $result3);
        $this->assertEquals($result['url'], $result3['url']);
    }

    /**
     * Test the behaviour of get_registration_url() for users without permission.
     *
     * @dataProvider get_registration_url_permission_data_provider
     * @param bool $createifmissing whether to attempt to create the registration URL or not.
     * @param array $expected the array of expected values.
     * @covers ::execute
     */
    public function test_get_registration_url_permissions(bool $createifmissing, array $expected) {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $this->expectException($expected['exception']);
        get_registration_url::execute($createifmissing);
    }

    /**
     * Data provider for testing get_registration_url calls for users without permissions.
     *
     * @return array the test data.
     */
    public function get_registration_url_permission_data_provider() {
        return [
            'no auto creation' => [
                'createifmissing' => false,
                'expected' => [
                    'exception' => \moodle_exception::class
                ]
            ],
            'auto creation' => [
                'createifmissing' => true,
                'expected' => [
                    'exception' => \moodle_exception::class
                ]
            ]
        ];
    }
}
