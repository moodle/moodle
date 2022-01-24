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

namespace enrol_lti\local\ltiadvantage\service;

use enrol_lti\helper;
use enrol_lti\local\ltiadvantage\entity\registration_url;
use enrol_lti\local\ltiadvantage\repository\application_registration_repository;
use enrol_lti\local\ltiadvantage\repository\context_repository;
use enrol_lti\local\ltiadvantage\repository\deployment_repository;
use enrol_lti\local\ltiadvantage\repository\resource_link_repository;
use enrol_lti\local\ltiadvantage\repository\user_repository;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../lti_advantage_testcase.php');

/**
 * Tests for the application_registration_service.
 *
 * @package enrol_lti
 * @copyright 2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \enrol_lti\local\ltiadvantage\service\application_registration_service
 */
class application_registration_service_test extends \lti_advantage_testcase {
    /**
     * Helper to get an application_registration_service instance.
     * @return application_registration_service
     */
    protected function get_application_registration_service(): application_registration_service {
        return new application_registration_service(
            new application_registration_repository(),
            new deployment_repository(),
            new resource_link_repository(),
            new context_repository(),
            new user_repository()
        );
    }

    /**
     * Test the use case "As an admin, I can register an application as an LTI consumer (platform)".
     *
     * @covers ::create_application_registration
     */
    public function test_register_application() {
        $this->resetAfterTest();
        $reg = (object) [
            'name' => 'Example LMS application',
            'platformid' => 'https://lms.example.org',
            'clientid' => '123',
            'authenticationrequesturl' => new \moodle_url('https://example.org/authrequesturl'),
            'jwksurl' => new \moodle_url('https://example.org/jwksurl'),
            'accesstokenurl' => new \moodle_url('https://example.org/accesstokenurl')
        ];

        $service = $this->get_application_registration_service();
        $createdreg = $service->create_application_registration($reg);

        $regrepo = new application_registration_repository();
        $this->assertTrue($regrepo->exists($createdreg->get_id()));
    }

    /**
     * Test verifying that the service cannot save two identical (same issuer and clientid) application registrations.
     *
     * @covers ::create_application_registration
     */
    public function test_register_application_unique_constraints() {
        $this->resetAfterTest();
        $reg = (object) [
            'name' => 'Example LMS application',
            'platformid' => 'https://lms.example.org',
            'clientid' => '123',
            'authenticationrequesturl' => new \moodle_url('https://example.org/authrequesturl'),
            'jwksurl' => new \moodle_url('https://example.org/jwksurl'),
            'accesstokenurl' => new \moodle_url('https://example.org/accesstokenurl')
        ];

        $service = $this->get_application_registration_service();
        $service->create_application_registration($reg);

        $this->expectException(\moodle_exception::class);
        $service->create_application_registration($reg);
    }

    /**
     * Test the use case "As an admin, I can update an application registered as an LTI consumer (platform)".
     *
     * @covers ::update_application_registration
     */
    public function test_update_application_registration() {
        $this->resetAfterTest();
        $reg = (object) [
            'name' => 'Example LMS application',
            'platformid' => 'https://lms.example.org',
            'clientid' => '123',
            'authenticationrequesturl' => new \moodle_url('https://example.org/authrequesturl'),
            'jwksurl' => new \moodle_url('https://example.org/jwksurl'),
            'accesstokenurl' => new \moodle_url('https://example.org/accesstokenurl')
        ];

        $service = $this->get_application_registration_service();
        $createdreg = $service->create_application_registration($reg);

        $reg->id = $createdreg->get_id();
        $reg->jwksurl = new \moodle_url('https://example.org/updated_jwksurl');

        $updatedreg = $service->update_application_registration($reg);
        $this->assertEquals($reg->name, $updatedreg->get_name());
        $this->assertEquals($reg->jwksurl, $updatedreg->get_jwksurl());
    }

    /**
     * Test verifying that the service requires an object id.
     *
     * @covers ::update_application_registration
     */
    public function test_update_application_registration_missing_id() {
        $this->resetAfterTest();
        $reg = (object) [
            'name' => 'Example LMS application',
            'platformid' => 'https://lms.example.org',
            'clientid' => '123',
            'authenticationrequesturl' => new \moodle_url('https://example.org/authrequesturl'),
            'jwksurl' => new \moodle_url('https://example.org/jwksurl'),
            'accesstokenurl' => new \moodle_url('https://example.org/accesstokenurl')
        ];

        $service = $this->get_application_registration_service();
        $service->create_application_registration($reg);

        $reg->jwksurl = new \moodle_url('https://example.org/updated_jwksurl');

        $this->expectException(\coding_exception::class);
        $service->update_application_registration($reg);
    }

    /**
     * Test that removing an application registration also removes all associated data.
     *
     * @covers ::delete_application_registration
     */
    public function test_delete_application_registration() {
        $this->resetAfterTest();
        // Setup.
        $registrationrepo = new application_registration_repository();
        $deploymentrepo = new deployment_repository();
        $contextrepo = new context_repository();
        $resourcelinkrepo = new resource_link_repository();
        $userrepo = new user_repository();
        [$course, $resource] = $this->create_test_environment();

        // Launch the tool for a user.
        $mocklaunch = $this->get_mock_launch($resource, $this->get_mock_launch_users_with_ids(['1'])[0]);
        $instructoruser = $this->getDataGenerator()->create_user();
        $launchservice = $this->get_tool_launch_service();
        $launchservice->user_launches_tool($instructoruser, $mocklaunch);

        // Check all the expected data exists for the deployment after setup.
        $registrations = $registrationrepo->find_all();
        $this->assertCount(1, $registrations);
        $registration = array_pop($registrations);

        $deployments = $deploymentrepo->find_all_by_registration($registration->get_id());
        $this->assertCount(1, $deployments);
        $deployment = array_pop($deployments);

        $resourcelinks = $resourcelinkrepo->find_by_resource($resource->id);
        $this->assertCount(1, $resourcelinks);
        $resourcelink = array_pop($resourcelinks);

        $context = $contextrepo->find($resourcelink->get_contextid());
        $this->assertNotNull($context);

        $users = $userrepo->find_by_resource($resource->id);
        $this->assertCount(1, $users);
        $user = array_pop($users);

        $enrolledusers = get_enrolled_users(\context_course::instance($course->id));
        $this->assertCount(1, $enrolledusers);

        // Now delete the application_registration using the service.
        $service = $this->get_application_registration_service();
        $service->delete_application_registration($registration->get_id());

        // Verify that the context, resourcelink, user, deployment and registration instances are all deleted.
        $this->assertFalse($registrationrepo->exists($registration->get_id()));
        $this->assertFalse($deploymentrepo->exists($deployment->get_id()));
        $this->assertFalse($contextrepo->exists($context->get_id()));
        $this->assertFalse($resourcelinkrepo->exists($resourcelink->get_id()));
        $this->assertFalse($userrepo->exists($user->get_id()));

        // Verify that all users are unenrolled.
        $enrolledusers = get_enrolled_users(\context_course::instance($course->id));
        $this->assertCount(0, $enrolledusers);

        // Verify the tool record stays in place (I.e. the published resource is still available).
        $this->assertNotEmpty(helper::get_lti_tool($resource->id));
    }

    /**
     * Test creation of a dynamic registration url.
     * @dataProvider registration_url_data_provider
     * @param int|null $duration how long the URL is valid for, in seconds.
     * @param array $expected the array of expected values/results.
     * @covers ::create_registration_url
     */
    public function test_create_registration_url(?int $duration, array $expected) {
        $this->resetAfterTest();
        $appregservice = $this->get_application_registration_service();
        if ($expected['exception']) {
            $this->expectException($expected['exception']);
        }
        if (!is_null($duration)) {
            $regurl = $appregservice->create_registration_url($duration);
        } else {
            $regurl = $appregservice->create_registration_url();
        }
        $this->assertInstanceOf(registration_url::class, $regurl);
        $this->assertGreaterThanOrEqual($expected['expirytime'], $regurl->get_expiry_time());
    }

    /**
     * Data provider for testing registration url creation.
     *
     * @return array the test data.
     */
    public function registration_url_data_provider() {
        return [
            'no params' => [
                'duration' => null,
                'expected' => [
                    'expirytime' => time() + 86400,
                    'exception' => false,
                ]
            ],
            'expiry specified' => [
                'duration' => 3600,
                'expected' => [
                    'expirytime' => time() + 3600,
                    'exception' => false,
                ]
            ],
            'invalid expiry specified' => [
                'duration' => -5,
                'expected' => [
                    'expirytime' => null,
                    'exception' => \coding_exception::class,
                ]
            ]
        ];
    }

    /**
     * Test getting the current registration url.
     *
     * @covers ::get_registration_url
     */
    public function test_get_registration_url() {
        $this->resetAfterTest();
        $appregservice = $this->get_application_registration_service();

        // Check when not existing.
        $this->assertNull($appregservice->get_registration_url());

        // Check after creation.
        $appregservice->create_registration_url();
        $regurl = $appregservice->get_registration_url();
        $this->assertInstanceOf(registration_url::class, $regurl);
    }

    /**
     * Test getting a registration URL by its token.
     *
     * @covers ::get_registration_url
     */
    public function test_get_registration_url_using_token() {
        $this->resetAfterTest();
        $appregservice = $this->get_application_registration_service();
        $createdregurl = $appregservice->create_registration_url();

        // Check valid token.
        $token = $createdregurl->param('token');
        $regurl = $appregservice->get_registration_url($token);
        $this->assertInstanceOf(registration_url::class, $regurl);

        // Check invalid token.
        $this->assertNull($appregservice->get_registration_url('invalid_token'));
    }

    /**
     * Test deletion of the current registration URL.
     *
     * @covers ::delete_registration_url
     */
    public function test_delete_registration_url() {
        $this->resetAfterTest();
        $appregservice = $this->get_application_registration_service();

        // Deletion when no URL exists.
        $this->assertNull($appregservice->delete_registration_url());

        // Deletion of a URL.
        $appregservice->create_registration_url();
        $regurl = $appregservice->get_registration_url();
        $this->assertInstanceOf(registration_url::class, $regurl);
        $appregservice->delete_registration_url();
        $this->assertNull($appregservice->get_registration_url());
    }
}
