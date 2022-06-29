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

namespace enrol_lti\local\ltiadvantage\repository;
use enrol_lti\local\ltiadvantage\entity\application_registration;
use enrol_lti\local\ltiadvantage\entity\deployment;

/**
 * Tests for the application_registration_repository.
 *
 * @package enrol_lti
 * @copyright 2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \enrol_lti\local\ltiadvantage\repository\application_registration_repository
 */
class application_registration_repository_test extends \advanced_testcase {
    /**
     * Helper to generate a new application_registration object.
     *
     * @param string|null $issuer the issuer of the application, or null to use a default.
     * @param string|null $uniqueid unique id for the tool registration, or null to use a default.
     * @param string|null $clientid the clientid of the platform's tool registration, or null to use a default.
     * @return application_registration the application_registration instance.
     */
    protected function generate_application_registration(string $issuer = null, string $uniqueid = null,
            string $clientid = null): application_registration {

        $issuer = $issuer ?? 'https://lms.example.org';
        $uniqueid = $uniqueid ?? 'a2c94a2c94';
        $clientid = $clientid ?? 'clientid_123';
        return application_registration::create(
            'Example LMS application',
            $uniqueid,
            new \moodle_url($issuer),
            $clientid,
            new \moodle_url('https://example.org/authrequesturl'),
            new \moodle_url('https://example.org/jwksurl'),
            new \moodle_url('https://example.org/accesstokenurl')
        );
    }

    /**
     * Helper to assert that all the key elements of two registrations (i.e. excluding id) are equal.
     *
     * @param application_registration $expected the registration whose values are deemed correct.
     * @param application_registration $check the registration to check.
     */
    protected function assert_same_registration_values(application_registration $expected,
            application_registration $check): void {
        $this->assertEquals($expected->get_name(), $check->get_name());
        $this->assertEquals($expected->get_platformid(), $check->get_platformid());
        $this->assertEquals($expected->get_clientid(), $check->get_clientid());
        $this->assertEquals($expected->get_authenticationrequesturl(),
            $check->get_authenticationrequesturl());
        $this->assertEquals($expected->get_jwksurl(), $check->get_jwksurl());
        $this->assertEquals($expected->get_accesstokenurl(), $check->get_accesstokenurl());
        $this->assertEquals($expected->is_complete(), $check->is_complete());
        $this->assertEquals($expected->get_uniqueid(), $check->get_uniqueid());
    }

    /**
     * Helper to assert that all the key elements of an application_registration are present in the DB.
     *
     * @param application_registration $registration
     */
    protected function assert_registration_db_values(application_registration $registration) {
        global $DB;
        $record = $DB->get_record('enrol_lti_app_registration', ['id' => $registration->get_id()]);
        $this->assertEquals($registration->get_id(), $record->id);
        $this->assertEquals($registration->get_name(), $record->name);
        $this->assertEquals($registration->get_platformid(), $record->platformid);
        $this->assertEquals($registration->get_clientid(), $record->clientid);
        $this->assertEquals($registration->get_authenticationrequesturl(), $record->authenticationrequesturl);
        $this->assertEquals($registration->get_jwksurl(), $record->jwksurl);
        $this->assertEquals($registration->get_accesstokenurl(), $record->accesstokenurl);
        $expectedstatus = $registration->is_complete() ? application_registration::REGISTRATION_STATUS_COMPLETE
            : application_registration::REGISTRATION_STATUS_INCOMPLETE;
        $this->assertEquals($expectedstatus, $record->status);
        $this->assertNotEmpty($record->timecreated);
        $this->assertNotEmpty($record->timemodified);
    }

    /**
     * Tests saving application_registration instances using the repository.
     *
     * @dataProvider save_data_provider
     * @covers ::save
     * @param array $regdata the registration data
     */
    public function test_save_new(array $regdata) {
        $this->resetAfterTest();

        $reg = application_registration::create_draft($regdata['name'], $regdata['uniqueid']);
        if (isset($regdata['platformid'])) {
            $reg->set_platformid($regdata['platformid']);
        }
        if (isset($regdata['clientid'])) {
            $reg->set_clientid($regdata['clientid']);
        }
        if (isset($regdata['authenticationrequesturl'])) {
            $reg->set_authenticationrequesturl($regdata['authenticationrequesturl']);
        }
        if (isset($regdata['jwksurl'])) {
            $reg->set_jwksurl($regdata['jwksurl']);
        }
        if (isset($regdata['accesstokenurl'])) {
            $reg->set_accesstokenurl($regdata['accesstokenurl']);
        }
        if (!empty($regdata['setcomplete'])) {
            $reg->complete_registration();
        }
        $repository = new application_registration_repository();
        $createdregistration = $repository->save($reg);

        $this->assert_same_registration_values($reg, $createdregistration);
        $this->assert_registration_db_values($createdregistration);

    }

    /**
     * Provides registrations in different states for use in test_save_new.
     *
     * @return array the array of test data.
     */
    public function save_data_provider(): array {
        return [
            'minimal draft' => [
                'registrationdata' => [
                    'name' => 'My test platform',
                    'uniqueid' => 'acbhd4355',
                ]
            ],
            'draft with only some properties completed' => [
                'registrationdata' => [
                    'name' => 'My test platform',
                    'uniqueid' => 'acbhd4355',
                    'platformid' => new \moodle_url('https://lms.example.com'),
                    'clientid' => 'abc345',
                ]
            ],
            'draft with all fields completed, not marked complete' => [
                'registrationdata' => [
                    'name' => 'My test platform',
                    'uniqueid' => 'acbhd4355',
                    'platformid' => new \moodle_url('https://lms.example.com'),
                    'clientid' => 'abc345',
                    'authenticationrequesturl' => new \moodle_url('https://lms.example.com/auth'),
                    'jwksurl' => new \moodle_url('https://lms.example.com/jwks'),
                    'accesstokenurl' => new \moodle_url('https://lms.example.com/token'),
                ]
            ],
            'draft with all fields completed, marked complete' => [
                'registrationdata' => [
                    'name' => 'My test platform',
                    'uniqueid' => 'acbhd4355',
                    'platformid' => new \moodle_url('https://lms.example.com'),
                    'clientid' => 'abc345',
                    'authenticationrequesturl' => new \moodle_url('https://lms.example.com/auth'),
                    'jwksurl' => new \moodle_url('https://lms.example.com/jwks'),
                    'accesstokenurl' => new \moodle_url('https://lms.example.com/token'),
                    'setcomplete' => true,
                ]
            ],
        ];
    }

    /**
     * Test saving an application_registration that is already present in the store.
     *
     * @covers ::save
     */
    public function test_save_existing() {
        $this->resetAfterTest();
        $repository = new application_registration_repository();

        // Modifying a draft registration.
        $draftreg = application_registration::create_draft('My test platform', 'bcvd34gs');
        $createddraft = $repository->save($draftreg);
        $createddraft->set_platformid(new \moodle_url('https://lms.example.com'));
        $createddraft->set_clientid(new \moodle_url('clientid_test_33333'));
        $createddraft->set_name('Something else');
        $createddraft->set_jwksurl(new \moodle_url('https://lms.example.com/jwks'));
        $createddraft->set_authenticationrequesturl(new \moodle_url('https://lms.example.com/auth'));
        $createddraft->set_accesstokenurl(new \moodle_url('https://lms.example.com/token'));
        $createddraft->complete_registration();
        $updateddraft = $repository->save($createddraft);

        $this->assertEquals($createddraft->get_id(), $updateddraft->get_id());
        $this->assert_same_registration_values($createddraft, $updateddraft);
        $this->assert_registration_db_values($updateddraft);

        // Modifying a complete registration.
        $registration = application_registration::create(
            'My platform name',
            'a2c94a2c94',
            new \moodle_url('https://updated-lms.example.org/'),
            'Updated-client-id',
            new \moodle_url('https://updated-lms.example.org/auth'),
            new \moodle_url('https://updated-lms.example.org/jwks'),
            new \moodle_url('https://updated-lms.example.org/token'),
        );
        $createdregistration = $repository->save($registration);
        $createdregistration->set_name('Something else');
        $createdregistration->set_clientid('hhh444');
        $updatedregistration = $repository->save($createdregistration);

        $this->assertEquals($createdregistration->get_id(), $updatedregistration->get_id());
        $this->assert_same_registration_values($createdregistration, $updatedregistration);
        $this->assert_registration_db_values($updatedregistration);
    }

    /**
     * Tests trying to persist two as-yet-unpersisted objects having identical makeup.
     *
     * @covers ::save
     */
    public function test_save_duplicate_unique_constraints() {
        $this->resetAfterTest();
        $testregistration = $this->generate_application_registration();
        $testregistration2 = $this->generate_application_registration();
        $repository = new application_registration_repository();

        $this->assertInstanceOf(application_registration::class, $repository->save($testregistration));
        $this->expectException(\dml_exception::class);
        $repository->save($testregistration2);
    }

    /**
     * Test finding an application_registration in the repository.
     *
     * @covers ::find
     */
    public function test_find() {
        $this->resetAfterTest();
        $testregistration = $this->generate_application_registration();
        $repository = new application_registration_repository();
        $createdregistration = $repository->save($testregistration);
        $foundregistration = $repository->find($createdregistration->get_id());

        $this->assertEquals($createdregistration->get_id(), $foundregistration->get_id());
        $this->assert_same_registration_values($testregistration, $foundregistration);
        $this->assertNull($repository->find(0));
    }

    /**
     * Test finding an application registration by its unique id.
     *
     * @covers ::find_by_uniqueid
     */
    public function test_find_by_uniqueid() {
        $this->resetAfterTest();
        $testregistration = $this->generate_application_registration('https://lms.example.org', 'abc12345');
        $repository = new application_registration_repository();
        $createdregistration = $repository->save($testregistration);
        $foundregistration = $repository->find_by_uniqueid($createdregistration->get_uniqueid());

        $this->assertEquals($createdregistration->get_id(), $foundregistration->get_id());
        $this->assert_same_registration_values($testregistration, $foundregistration);
        $this->assertNull($repository->find_by_uniqueid('cccc'));
    }

    /**
     * Test finding an application registration by its platform and unique id combination.
     *
     * @covers ::find_by_platform_uniqueid
     */
    public function test_find_by_platform_uniqueid() {
        $this->resetAfterTest();
        $repository = new application_registration_repository();

        $testregistration = $this->generate_application_registration('https://lms.example.org', 'abc12345');
        $createdregistration = $repository->save($testregistration);
        $foundregistration = $repository->find_by_platform_uniqueid($createdregistration->get_platformid(),
            $createdregistration->get_uniqueid());

        $this->assertEquals($createdregistration->get_id(), $foundregistration->get_id());
        $this->assert_same_registration_values($testregistration, $foundregistration);

        // Same platformid, empty uniqueid.
        $this->assertNull($repository->find_by_platform_uniqueid('https://lms.example.org', ''));

        // Same platformid, different uniqueid.
        $this->assertNull($repository->find_by_platform_uniqueid('https://lms.example.org', 'bbbbb'));

        // Different platformid, empty uniqueid.
        $this->assertNull($repository->find_by_platform_uniqueid('https://lms-two.example.org', ''));

        // Different platformid, same uniqueid.
        $this->assertNull($repository->find_by_platform_uniqueid('https://lms-two.example.org', 'abc12345'));
    }

    /**
     * Test verifying that find_all() returns all registrations.
     *
     * @covers ::find_all
     */
    public function test_find_all() {
        $this->resetAfterTest();
        // None to begin with.
        $repository = new application_registration_repository();
        $this->assertEquals([], $repository->find_all());

        // Add two registrations.
        $reg1 = $this->generate_application_registration('https://some.platform.org', '123');
        $reg2 = $this->generate_application_registration('https://another.platform.org', '456');
        $reg1 = $repository->save($reg1);
        $regns[$reg1->get_id()] = $reg1;
        $reg2 = $repository->save($reg2);
        $regns[$reg2->get_id()] = $reg2;

        // Verify 2 found.
        $found = $repository->find_all();
        $this->assertCount(2, $found);
        foreach ($found as $reg) {
            $check = $regns[$reg->get_id()];
            $this->assertEquals($check, $reg);
        }
    }

    /**
     * Test confirming that registrations can be found by their platform string.
     *
     * @covers ::find_by_platform
     */
    public function test_find_by_platform() {
        $this->resetAfterTest();
        // None to begin with.
        $repository = new application_registration_repository();
        $this->assertNull($repository->find_by_platform('https://some.platform.org', 'abc'));

        // Create 2 registrations.
        $reg1 = $this->generate_application_registration('https://some.platform.org', '123', 'abc');
        $reg2 = $this->generate_application_registration('https://another.platform.org', '456', 'def');
        $reg1 = $repository->save($reg1);
        $reg2 = $repository->save($reg2);

        // Verify that we can find the registrations by their platform string.
        $found = $repository->find_by_platform('https://some.platform.org', 'abc');
        $this->assertEquals($reg1, $found);
        $found2 = $repository->find_by_platform('https://another.platform.org', 'def');
        $this->assertEquals($reg2, $found2);
    }

    /**
     * Test checking existence of an application_registration within the repository.
     *
     * @covers ::exists
     */
    public function test_exists() {
        $this->resetAfterTest();
        $testregistration = $this->generate_application_registration();
        $repository = new application_registration_repository();
        $createdregistration = $repository->save($testregistration);

        $this->assertTrue($repository->exists($createdregistration->get_id()));
        $this->assertFalse($repository->exists(0));
    }

    /**
     * Test confirming that delete removes items from the repository.
     *
     * @covers ::delete
     */
    public function test_delete() {
        $this->resetAfterTest();
        global $DB;
        $reg = $this->generate_application_registration();
        $repository = new application_registration_repository();
        $reg = $repository->save($reg);

        $repository->delete($reg->get_id());
        $this->assertEquals(0, $DB->count_records('enrol_lti_app_registration'));
        $this->assertFalse($repository->exists($reg->get_id()));

        // Deletion of nonexistent registration will not result in errors.
        $this->assertNull($repository->delete('000000'));
    }

    /**
     * Verify that application registrations can be found through their linked deployments.
     *
     * @covers ::find_by_deployment
     */
    public function test_find_by_deployment() {
        $this->resetAfterTest();
        $appregrepo = new application_registration_repository();
        $deploymentrepo = new deployment_repository();

        // Deployment linked to a registration.
        $testregistration = $this->generate_application_registration();
        $createdregistration = $appregrepo->save($testregistration);
        $deployment1 = $createdregistration->add_tool_deployment('Deployment 1', '12345');
        $createddeployment = $deploymentrepo->save($deployment1);

        // Deployment not linked to a registration.
        $deployment2 = deployment::create('000', '56789', 'unlinked deployment');
        $createddeployment2 = $deploymentrepo->save($deployment2);

        // Should be able to find the registration from the linked deployment.
        $foundreg = $appregrepo->find_by_deployment($createddeployment->get_id());
        $this->assertEquals($createdregistration, $foundreg);

        // But not for the deployment which isn't linked.
        $this->assertNull($appregrepo->find_by_deployment($createddeployment2->get_id()));
    }
}
