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
 * Tests for deployment_repository.
 *
 * @package enrol_lti
 * @copyright 2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \enrol_lti\local\ltiadvantage\repository\deployment_repository
 */
class deployment_repository_test extends \advanced_testcase {
    /**
     * Helper to create test deployment objects for use with the repository tests.
     *
     * @param string $deploymentid the string id of the deployment.
     * @param int|null $appregistrationid the id of the application registration to which this deployment belongs.
     * @return deployment the deployment.
     */
    protected function create_test_deployment(string $deploymentid = 'DeployID123',
            ?int $appregistrationid = null): deployment {

        if (is_null($appregistrationid)) {
            $registration = application_registration::create(
                'Test',
                'a2c94a2c94',
                new \moodle_url('http://lms.example.org'),
                'clientid_123',
                new \moodle_url('https://example.org/authrequesturl'),
                new \moodle_url('https://example.org/jwksurl'),
                new \moodle_url('https://example.org/accesstokenurl')
            );
            $registrationrepo = new application_registration_repository();
            $createdregistration = $registrationrepo->save($registration);
            $appregistrationid = $createdregistration->get_id();
        }
        return deployment::create(
            $appregistrationid,
            $deploymentid,
            'Tool deployment on platform x',
        );
    }

    /**
     * Helper to assert that all the key elements of two deployments (i.e. excluding id) are equal.
     *
     * @param deployment $expected the deployment whose values are deemed correct.
     * @param deployment $check the deployment to check.
     */
    protected function assert_same_deployment_values(deployment $expected, deployment $check): void {
        $this->assertEquals($expected->get_deploymentname(), $check->get_deploymentname());
        $this->assertEquals($expected->get_deploymentid(), $check->get_deploymentid());
        $this->assertEquals($expected->get_registrationid(), $check->get_registrationid());
        $this->assertEquals($expected->get_legacy_consumer_key(), $check->get_legacy_consumer_key());
    }

    /**
     * Helper to assert that all the key elements of a deployment are present in the DB.
     *
     * @param deployment $expected the deployment whose values are deemed correct.
     */
    protected function assert_deployment_db_values(deployment $expected) {
        global $DB;
        $checkrecord = $DB->get_record('enrol_lti_deployment', ['id' => $expected->get_id()]);
        $this->assertEquals($expected->get_id(), $checkrecord->id);
        $this->assertEquals($expected->get_deploymentname(), $checkrecord->name);
        $this->assertEquals($expected->get_deploymentid(), $checkrecord->deploymentid);
        $this->assertEquals($expected->get_registrationid(), $checkrecord->platformid);
        $this->assertEquals($expected->get_legacy_consumer_key(), $checkrecord->legacyconsumerkey);
        $this->assertNotEmpty($checkrecord->timecreated);
        $this->assertNotEmpty($checkrecord->timemodified);
    }

    /**
     * Test saving a new deployment.
     *
     * @covers ::save
     */
    public function test_save_new(): void {
        $this->resetAfterTest();
        $deploymentrepo = new deployment_repository();
        $deployment = $this->create_test_deployment();
        $deployment->set_legacy_consumer_key('test-consumer-key');
        $saved = $deploymentrepo->save($deployment);

        $this->assertIsInt($saved->get_id());
        $this->assert_same_deployment_values($deployment, $saved);
        $this->assert_deployment_db_values($saved);
    }

    /**
     * Test saving an existing deployment.
     *
     * @covers ::save
     */
    public function test_save_existing(): void {
        $this->resetAfterTest();
        $deploymentrepo = new deployment_repository();
        $deployment = $this->create_test_deployment();
        $saved = $deploymentrepo->save($deployment);

        $saved->set_legacy_consumer_key('added-consumer-key');
        $saved2 = $deploymentrepo->save($saved);

        $this->assertEquals($saved->get_id(), $saved2->get_id());
        $this->assert_same_deployment_values($saved, $saved2);
        $this->assert_deployment_db_values($saved2);
    }

    /**
     * Test trying to save two deployments of identical nature in sequence.
     *
     * @covers ::save
     */
    public function test_save_unique_constraints_not_met(): void {
        $this->resetAfterTest();
        $deployment1 = $this->create_test_deployment('Deploy_ID_123');
        $deployment2 = $this->create_test_deployment('Deploy_ID_123', $deployment1->get_registrationid());
        $deploymentrepo = new deployment_repository();

        $this->assertInstanceOf(deployment::class, $deploymentrepo->save($deployment1));
        $this->expectException(\dml_exception::class);
        $deploymentrepo->save($deployment2);
    }

    /**
     * Test existence of a deployment within the repository.
     *
     * @covers ::exists
     */
    public function test_exists(): void {
        $this->resetAfterTest();
        $deploymentrepo = new deployment_repository();
        $deployment = $this->create_test_deployment();
        $saveddeployment = $deploymentrepo->save($deployment);

        $this->assertTrue($deploymentrepo->exists($saveddeployment->get_id()));
        $this->assertFalse($deploymentrepo->exists(0));
    }

    /**
     * Test finding a deployment in the repository.
     *
     * @covers ::find
     */
    public function test_find(): void {
        $this->resetAfterTest();
        $deployment = $this->create_test_deployment();
        $deploymentrepo = new deployment_repository();
        $saveddeployment = $deploymentrepo->save($deployment);

        $founddeployment = $deploymentrepo->find($saveddeployment->get_id());
        $this->assertEquals($saveddeployment->get_id(), $founddeployment->get_id());
        $this->assert_same_deployment_values($saveddeployment, $founddeployment);
        $this->assertNull($deploymentrepo->find(0));
    }

    /**
     * Test deleting a deployment object from the repository.
     *
     * @covers ::delete
     */
    public function test_delete(): void {
        $this->resetAfterTest();
        $deployment = $this->create_test_deployment();
        $deploymentrepo = new deployment_repository();
        $saveddeployment = $deploymentrepo->save($deployment);
        $this->assertTrue($deploymentrepo->exists($saveddeployment->get_id()));

        $deploymentrepo->delete($saveddeployment->get_id());
        $this->assertFalse($deploymentrepo->exists($saveddeployment->get_id()));

        $this->assertNull($deploymentrepo->delete($saveddeployment->get_id()));
    }

    /**
     * Test deleting a deployment by registration.
     *
     * @covers ::delete_by_registration
     */
    public function test_delete_by_registration(): void {
        $this->resetAfterTest();
        $deployment = $this->create_test_deployment();
        $deploymentrepo = new deployment_repository();
        $saveddeployment = $deploymentrepo->save($deployment);
        $deployment2 = deployment::create($saveddeployment->get_registrationid(), 'another-deployment-id-1',
            'another deployment 1');
        $saveddeployment2 = $deploymentrepo->save($deployment2);
        $deployment3 = deployment::create($saveddeployment->get_registrationid() + 1, 'another-deployment-id-2',
            'another deployment 2');
        $saveddeployment3 = $deploymentrepo->save($deployment3);
        $this->assertTrue($deploymentrepo->exists($saveddeployment->get_id()));
        $this->assertTrue($deploymentrepo->exists($saveddeployment2->get_id()));
        $this->assertTrue($deploymentrepo->exists($saveddeployment3->get_id()));

        $deploymentrepo->delete_by_registration($saveddeployment->get_registrationid());
        $this->assertFalse($deploymentrepo->exists($saveddeployment->get_id()));
        $this->assertFalse($deploymentrepo->exists($saveddeployment2->get_id()));
        $this->assertTrue($deploymentrepo->exists($saveddeployment3->get_id()));

        $this->assertNull($deploymentrepo->delete($saveddeployment->get_id()));
    }

    /**
     * Test counting the number of deployments for a given registration.
     *
     * @covers ::count_by_registration
     */
    public function test_count_by_registration(): void {
        $this->resetAfterTest();
        $deployment = $this->create_test_deployment();
        $deploymentrepo = new deployment_repository();
        $saveddeployment = $deploymentrepo->save($deployment);
        $deployment2 = deployment::create($saveddeployment->get_registrationid(), 'another-deployment-id-1',
            'another deployment 1');
        $saveddeployment2 = $deploymentrepo->save($deployment2);
        $deployment3 = deployment::create($saveddeployment->get_registrationid() + 1, 'another-deployment-id-2',
            'another deployment 2');
        $saveddeployment3 = $deploymentrepo->save($deployment3);

        $this->assertEquals(2, $deploymentrepo->count_by_registration($saveddeployment->get_registrationid()));
        $this->assertEquals(1, $deploymentrepo->count_by_registration($saveddeployment3->get_registrationid()));
        $this->assertEquals(0, $deploymentrepo->count_by_registration(0));
    }

    /**
     * Test confirming a deployment can be found by registration and deploymentid.
     *
     * @covers ::find_by_registration
     */
    public function test_find_by_registration(): void {
        $this->resetAfterTest();
        $deployment = $this->create_test_deployment();
        $deploymentrepo = new deployment_repository();
        $saveddeployment = $deploymentrepo->save($deployment);
        $regid = $saveddeployment->get_registrationid();

        // Existing registration.
        $this->assertInstanceOf(deployment::class,
            $deploymentrepo->find_by_registration($regid, $saveddeployment->get_deploymentid()));

        // A non-existent registration.
        $this->assertNull($deploymentrepo->find_by_registration($regid, 'NonExistentDeploymentId'));
    }

    /**
     * Testing that all deployments for a given registration can be fetched.
     *
     * @covers ::find_all_by_registration
     */
    public function test_find_all_by_registration(): void {
        $this->resetAfterTest();
        $registration1 = application_registration::create(
            'Test',
            'a2c94a2c94',
            new \moodle_url('http://lms.example.org'),
            'clientid_123',
            new \moodle_url('https://example.org/authrequesturl'),
            new \moodle_url('https://example.org/jwksurl'),
            new \moodle_url('https://example.org/accesstokenurl')
        );
        $registration2 = application_registration::create(
            'Test 2',
            'c56bcdc56bcd',
            new \moodle_url('http://lms2.example.org'),
            'clientid_345',
            new \moodle_url('https://example.org/authrequesturl'),
            new \moodle_url('https://example.org/jwksurl'),
            new \moodle_url('https://example.org/accesstokenurl')
        );
        $registrationrepo = new application_registration_repository();
        $deploymentrepo = new deployment_repository();
        $createdregistration1 = $registrationrepo->save($registration1);
        $createdregistration2 = $registrationrepo->save($registration2);
        $deployment1 = $createdregistration1->add_tool_deployment('Deployment 1', 'reg1_deploy1');
        $deployment2 = $createdregistration1->add_tool_deployment('Deployment 2', 'reg1_deploy2');
        $deployment3 = $createdregistration2->add_tool_deployment('Deployment 3', 'reg2_deploy1');
        $saveddeployment1 = $deploymentrepo->save($deployment1);
        $saveddeployment2 = $deploymentrepo->save($deployment2);
        $saveddeployment3 = $deploymentrepo->save($deployment3);
        $reg1saveddeployments = [
            $saveddeployment1->get_id() => $saveddeployment1,
            $saveddeployment2->get_id() => $saveddeployment2
        ];
        $reg2saveddeployments = [
            $saveddeployment3->get_id() => $saveddeployment3
        ];

        // Registration 1.
        $reg1founddeployments = $deploymentrepo->find_all_by_registration($createdregistration1->get_id());
        $this->assertCount(2, $reg1founddeployments);
        foreach ($reg1founddeployments as $reg1founddeployment) {
            $this->assertEquals($reg1saveddeployments[$reg1founddeployment->get_id()], $reg1founddeployment);
        }

        // Registration 2.
        $reg2founddeployments = $deploymentrepo->find_all_by_registration($createdregistration2->get_id());
        $this->assertCount(1, $deploymentrepo->find_all_by_registration($createdregistration2->get_id()));
        foreach ($reg2founddeployments as $reg2founddeployment) {
            $this->assertEquals($reg2saveddeployments[$reg2founddeployment->get_id()], $reg2founddeployment);
        }

        // A non-existent registration.
        $nonexistentregdeployments = $deploymentrepo->find_all_by_registration(0);
        $this->assertEmpty($nonexistentregdeployments);
    }
}
