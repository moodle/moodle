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
final class application_registration_service_test extends \lti_advantage_testcase {
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
     * @covers ::create_draft_application_registration
     */
    public function test_create_draft_application(): void {
        $this->resetAfterTest();
        $service = $this->get_application_registration_service();

        // Create a draft, passing the required name.
        $draftreg = $service->create_draft_application_registration((object) ['name' => 'My test platform']);
        $this->assertEquals('My test platform', $draftreg->get_name());
        $this->assertNull($draftreg->get_authenticationrequesturl());
        $this->assertNull($draftreg->get_jwksurl());
        $this->assertNull($draftreg->get_accesstokenurl());
        $this->assertNull($draftreg->get_platformid());
        $this->assertNull($draftreg->get_clientid());
        $this->assertFalse($draftreg->is_complete());

        // Try to create a draft omitting name.
        $this->expectException(\coding_exception::class);
        $service->create_draft_application_registration((object) []);
    }

    /**
     * Test the update_application_registration method.
     *
     * @covers ::update_application_registration
     */
    public function test_update_application_registration(): void {
        $this->resetAfterTest();

        // Create a registration in the draft state.
        $service = $this->get_application_registration_service();
        $draftreg = $service->create_draft_application_registration((object) ['name' => 'My test platform']);

        // Update the draft.
        $updatedto = (object) [
            'id' => $draftreg->get_id(),
            'name' => 'My test platform name edit 1',
            'platformid' => 'https://lms.example.org',
            'clientid' => '123',
            'authenticationrequesturl' => 'https://lms.example.org/authrequesturl',
            'jwksurl' => 'https://lms.example.org/jwksurl',
            'accesstokenurl' => 'https://lms.example.org/accesstokenurl'
        ];
        $reg = $service->update_application_registration($updatedto);

        // Verify details saved and complete status.
        $this->assertEquals($updatedto->id, $reg->get_id());
        $this->assertEquals($updatedto->name, $reg->get_name());
        $this->assertEquals(new \moodle_url($updatedto->platformid), $reg->get_platformid());
        $this->assertEquals($updatedto->clientid, $reg->get_clientid());
        $this->assertEquals(new \moodle_url($updatedto->authenticationrequesturl), $reg->get_authenticationrequesturl());
        $this->assertEquals(new \moodle_url($updatedto->jwksurl), $reg->get_jwksurl());
        $this->assertEquals(new \moodle_url($updatedto->accesstokenurl), $reg->get_accesstokenurl());
        $this->assertTrue($reg->is_complete());

        // Update again.
        $updatedto = (object) [
            'id' => $draftreg->get_id(),
            'name' => 'My test platform name edit 2',
            'platformid' => 'https://different.example.org',
            'clientid' => 'abcd1234',
            'authenticationrequesturl' => 'https://lms.example.org/authrequesturl2',
            'jwksurl' => 'https://lms.example.org/jwksurl2',
            'accesstokenurl' => 'https://lms.example.org/accesstokenurl2'
        ];
        $reg = $service->update_application_registration($updatedto);

        // Verify again.
        $this->assertEquals($updatedto->id, $reg->get_id());
        $this->assertEquals($updatedto->name, $reg->get_name());
        $this->assertEquals(new \moodle_url($updatedto->platformid), $reg->get_platformid());
        $this->assertEquals($updatedto->clientid, $reg->get_clientid());
        $this->assertEquals(new \moodle_url($updatedto->authenticationrequesturl), $reg->get_authenticationrequesturl());
        $this->assertEquals(new \moodle_url($updatedto->jwksurl), $reg->get_jwksurl());
        $this->assertEquals(new \moodle_url($updatedto->accesstokenurl), $reg->get_accesstokenurl());
        $this->assertTrue($reg->is_complete());

        // Update missing id.
        unset($updatedto->id);
        $this->expectException(\coding_exception::class);
        $service->update_application_registration($updatedto);
    }

    /**
     * Test that removing an application registration also removes all associated data.
     *
     * @covers ::delete_application_registration
     */
    public function test_delete_application_registration(): void {
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
}
