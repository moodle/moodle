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

use enrol_lti\local\ltiadvantage\repository\application_registration_repository;
use enrol_lti\local\ltiadvantage\repository\context_repository;
use enrol_lti\local\ltiadvantage\repository\deployment_repository;
use enrol_lti\local\ltiadvantage\repository\resource_link_repository;
use enrol_lti\local\ltiadvantage\repository\user_repository;
use enrol_lti\local\ltiadvantage\service\application_registration_service;
use external_api;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/webservice/tests/helpers.php');

/**
 * Test class for enrol_lti\local\ltiadvantage\external\delete_registration_url.
 *
 * @package    enrol_lti
 * @copyright  2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \enrol_lti\local\ltiadvantage\external\delete_registration_url
 */
class delete_registration_url_test extends \externallib_advanced_testcase {

    /**
     * Setup for the test cases.
     */
    protected function setUp(): void {
        $this->resetAfterTest();
    }

    /**
     * Test registration URL deletion.
     *
     * @dataProvider delete_registration_data_provider
     * @param bool $existing whether the test is dealing with deleting an existing registration URL or not.
     * @param bool $permissions whether the user has permissions to delete or not.
     * @covers ::execute
     */
    public function test_delete_registration(bool $existing, bool $permissions) {
        $appregservice = new application_registration_service(
            new application_registration_repository(),
            new deployment_repository(),
            new resource_link_repository(),
            new context_repository(),
            new user_repository()
        );
        if ($existing) {
            $appregservice->create_registration_url();
        }

        if ($permissions) {
            $this->setAdminUser();
            $result = delete_registration_url::execute();
            $result = external_api::clean_returnvalue(delete_registration_url::execute_returns(), $result);
            $this->assertTrue($result['status']);
            $this->assertNull($appregservice->get_registration_url());
        } else {
            $user = $this->getDataGenerator()->create_user();
            $this->setUser($user);
            $this->expectException(\moodle_exception::class);
            delete_registration_url::execute();
        }

    }

    /**
     * Data provider for testing delete_registration_url().
     * @return array the test data.
     */
    public function delete_registration_data_provider() {
        return [
            'No prior registration' => [
                'existing' => false,
                'permissions' => true,
            ],
            'Existing registration' => [
                'existing' => true,
                'permissions' => true,
            ],
            'Existing, no permissions' => [
                'existing' => true,
                'permissions' => false
            ],
            'No prior registration, no permissions' => [
                'existing' => false,
                'permissions' => false
            ]
        ];
    }
}
