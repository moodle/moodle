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
use enrol_lti\local\ltiadvantage\entity\resource_link;
use enrol_lti\local\ltiadvantage\entity\application_registration;

/**
 * Tests for resource_link_repository objects.
 *
 * @package enrol_lti
 * @copyright 2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \enrol_lti\local\ltiadvantage\repository\resource_link_repository
 */
final class resource_link_repository_test extends \advanced_testcase {
    /**
     * Helper to generate a new resource_link instance.
     *
     * @param string $id the id to use for this the resource link.
     * @return resource_link the resource_link instance.
     */
    protected function generate_resource_link($id = 'res-link-1'): resource_link {
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

        $deployment = $createdregistration->add_tool_deployment('Deployment 1', 'DeployID123');
        $deploymentrepo = new deployment_repository();
        $saveddeployment = $deploymentrepo->save($deployment);

        $contextrepo = new context_repository();
        $context = $saveddeployment->add_context(
            'CTX123',
            ['http://purl.imsglobal.org/vocab/lis/v2/course#CourseSection']
        );
        $savedcontext = $contextrepo->save($context);

        $resourcelink = $saveddeployment->add_resource_link($id, $savedcontext->get_id());
        $resourcelink->add_grade_service(
            new \moodle_url('https://lms.example.com/context/24/lineitems'),
            new \moodle_url('https://lms.example.com/context/24/lineitem/3'),
            [
                'https://purl.imsglobal.org/spec/lti-ags/scope/lineitem',
                'https://purl.imsglobal.org/spec/lti-ags/scope/lineitem.readonly',
                'https://purl.imsglobal.org/spec/lti-ags/scope/result.readonly',
                'https://purl.imsglobal.org/spec/lti-ags/scope/score'
            ]
        );
        $resourcelink->add_names_and_roles_service(
            new \moodle_url('https://lms.example.com/context/24/memberships'),
            [1.0, 2.0]
        );

        return $resourcelink;
    }

    /**
     * Helper to assert that all the key elements of two resource_links (i.e. excluding id) are equal.
     *
     * @param resource_link $expected the resource_link whose values are deemed correct.
     * @param resource_link $check the resource_link to check.
     */
    protected function assert_same_resourcelink_values(resource_link $expected, resource_link $check): void {
        $this->assertEquals($expected->get_resourcelinkid(), $check->get_resourcelinkid());
        $this->assertEquals($expected->get_deploymentid(), $check->get_deploymentid());
        $this->assertEquals($expected->get_contextid(), $check->get_contextid());
        $this->assertEquals($expected->get_grade_service(), $check->get_grade_service());
        $this->assertEquals($expected->get_names_and_roles_service(), $check->get_names_and_roles_service());
    }

    /**
     * Helper to assert that all the key elements of a resource_link are present in the DB.
     *
     * @param resource_link $expected the resource_link whose values are deemed correct.
     */
    protected function assert_resourcelink_db_values(resource_link $expected) {
        global $DB;
        $checkrecord = $DB->get_record('enrol_lti_resource_link', ['id' => $expected->get_id()]);
        $gradeservice = $expected->get_grade_service();
        $this->assertEquals($expected->get_id(), $checkrecord->id);
        $this->assertEquals($expected->get_deploymentid(), $checkrecord->ltideploymentid);
        $this->assertEquals($expected->get_resourcelinkid(), $checkrecord->resourcelinkid);
        $this->assertEquals($expected->get_contextid(), $checkrecord->lticontextid);
        $this->assertEquals($gradeservice ? $gradeservice->get_lineitemsurl() : null, $checkrecord->lineitemsservice);
        $this->assertEquals($gradeservice ? $gradeservice->get_lineitemurl() : null, $checkrecord->lineitemservice);
        $this->assertEquals($gradeservice ? json_encode($gradeservice->get_lineitemscope()) : null,
            $checkrecord->lineitemscope);
        $this->assertEquals($gradeservice ? $gradeservice->get_resultscope() : null, $checkrecord->resultscope);
        $this->assertEquals($gradeservice ? $gradeservice->get_scorescope() : null, $checkrecord->scorescope);
        $this->assertNotEmpty($checkrecord->timecreated);
        $this->assertNotEmpty($checkrecord->timemodified);
    }

    /**
     * Tests adding a resource_link to the store.
     *
     * @covers ::save
     */
    public function test_save_new(): void {
        $this->resetAfterTest();
        $resourcelink = $this->generate_resource_link();
        $repository = new resource_link_repository();
        $savedresourcelink = $repository->save($resourcelink);

        $this->assertIsInt($savedresourcelink->get_id());
        $this->assert_same_resourcelink_values($resourcelink, $savedresourcelink);
        $this->assert_resourcelink_db_values($savedresourcelink);
    }

    /**
     * Test that we cannot add two resource_links with the same resourcelinkid for a given deploymentid.
     *
     * @covers ::save
     */
    public function test_add_uniqueness_constraints(): void {
        $this->resetAfterTest();
        $reslink1 = $this->generate_resource_link();
        $reslink2 = clone $reslink1;
        $repository = new resource_link_repository();
        $createdresource1 = $repository->save($reslink1);
        $this->assertIsInt($createdresource1->get_id());
        $this->expectException(\dml_exception::class);
        $repository->save($reslink2);
    }

    /**
     * Test fetching an object from the store.
     *
     * @covers ::find
     */
    public function test_find(): void {
        $this->resetAfterTest();
        $resourcelink = $this->generate_resource_link();
        $repository = new resource_link_repository();
        $newreslink = $repository->save($resourcelink);

        $locatedreslink = $repository->find($newreslink->get_id());
        $this->assertEquals($newreslink, $locatedreslink);
        $repository->delete($locatedreslink->get_id());
        $this->assertEmpty($repository->find($locatedreslink->get_id()));
    }

    /**
     * Test finding a collection of resource links by resource.
     *
     * @covers ::find_by_resource
     */
    public function test_find_by_resource(): void {
        $this->resetAfterTest();
        $resourcelink = $this->generate_resource_link();
        $repository = new resource_link_repository();
        $newreslink = $repository->save($resourcelink);
        $resourcelink2 = resource_link::create('another-res-link-1', $newreslink->get_deploymentid(),
            $newreslink->get_resourceid());
        $newreslink2 = $repository->save($resourcelink2);
        $resourcelink3 = resource_link::create('another-res-link-2', $newreslink->get_deploymentid(),
            $newreslink->get_resourceid() + 1);
        $newreslink3 = $repository->save($resourcelink3);

        $locatedreslinks = $repository->find_by_resource($newreslink->get_resourceid());
        $this->assertCount(2, $locatedreslinks);
        usort($locatedreslinks, function($a, $b) {
            return strcmp($b->get_resourcelinkid(), $a->get_resourcelinkid());
        });
        $this->assertEquals([$newreslink, $newreslink2], $locatedreslinks);
        $locatedreslinks = $repository->find_by_resource($newreslink->get_resourceid() + 1);
        $this->assertCount(1, $locatedreslinks);
        $this->assertEquals([$newreslink3], $locatedreslinks);
        $this->assertEmpty($repository->find_by_resource(0));
    }

    /**
     * Test finding a collection of resource links by resource and user.
     *
     * @covers ::find_by_resource_and_user
     */
    public function test_find_by_resource_and_user(): void {
        global $CFG;
        $this->resetAfterTest();
        $resourcelink = $this->generate_resource_link();
        $repository = new resource_link_repository();
        $newreslink = $repository->save($resourcelink);
        $resourcelink2 = resource_link::create('another-res-link-1', $newreslink->get_deploymentid(),
            $newreslink->get_resourceid());
        $newreslink2 = $repository->save($resourcelink2);
        $resourcelink3 = resource_link::create('another-res-link-2', $newreslink->get_deploymentid(),
            $newreslink->get_resourceid());
        $newreslink3 = $repository->save($resourcelink3);
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $userrepo = new user_repository();
        $user = $newreslink->add_user(
            $user1->id,
            'platform-user-id-123',
            $CFG->lang,
            'Sydney',
            'AU',
            'Test university',
            '99'
        );
        $createduser = $userrepo->save($user);
        $createduser->set_resourcelinkid($newreslink2->get_id());
        $userrepo->save($createduser);

        $user2 = $newreslink3->add_user(
            $user2->id,
            'platform-user-id-777',
            $CFG->lang,
            'Melbourne',
            'AU',
            'Test university',
            '99'
        );
        $createduser2 = $userrepo->save($user2);

        $locatedreslinks = $repository->find_by_resource_and_user($newreslink->get_resourceid(),
            $createduser->get_id());
        $this->assertCount(2, $locatedreslinks);
        usort($locatedreslinks, function($a, $b) {
            return strcmp($b->get_resourcelinkid(), $a->get_resourcelinkid());
        });
        $this->assertEquals([$newreslink, $newreslink2], $locatedreslinks);
        $locatedreslinks = $repository->find_by_resource_and_user($newreslink->get_resourceid(),
            $createduser2->get_id());
        $this->assertCount(1, $locatedreslinks);
        $this->assertEquals([$newreslink3], $locatedreslinks);
        $this->assertEmpty($repository->find_by_resource_and_user($newreslink->get_resourceid(), 0));
    }

    /**
     * Test deletion from the store.
     *
     * @covers ::delete
     */
    public function test_delete(): void {
        global $CFG;
        $this->resetAfterTest();
        $resourcelink = $this->generate_resource_link();
        $repository = new resource_link_repository();
        $newreslink = $repository->save($resourcelink);
        $this->assertTrue($repository->exists($newreslink->get_id()));

        // Also create a user from this resource link so we get some test user_resource_link mappings.
        $user = $newreslink->add_user(
            2,
            'source-id-123',
            $CFG->lang,
            'Perth',
            'AU',
            'An Example Institution',
            '99',
            2,
        );
        $userrepo = new user_repository();
        $userrepo->save($user);
        global $DB;
        $this->assertTrue($DB->record_exists('enrol_lti_user_resource_link',
            ['resourcelinkid' => $newreslink->get_id()]));

        $repository->delete($newreslink->get_id());
        $this->assertFalse($repository->exists($newreslink->get_id()));
        $this->assertEmpty($repository->find($newreslink->get_id()));
        $this->assertFalse($DB->record_exists('enrol_lti_user_resource_link',
            ['resourcelinkid' => $newreslink->get_id()]));

        $this->assertNull($repository->delete($newreslink->get_id()));
    }

    /**
     * Test deleting a group of resource links by resource.
     *
     * @covers ::delete_by_resource
     */
    public function test_delete_by_resource(): void {
        global $CFG;
        $this->resetAfterTest();
        $resourcelink = $this->generate_resource_link();
        $repository = new resource_link_repository();
        $newreslink = $repository->save($resourcelink);

        // Also create a user from this resource link so we get some test user_resource_link mappings.
        $user = $newreslink->add_user(
            2,
            'source-id-123',
            $CFG->lang,
            'Perth',
            'AU',
            'An Example Institution',
            '99',
            2,
        );
        $userrepo = new user_repository();
        $userrepo->save($user);
        global $DB;
        $this->assertTrue($DB->record_exists('enrol_lti_user_resource_link',
            ['resourcelinkid' => $newreslink->get_id()]));

        // Create a resource link under the same deployment for another resource.
        $resourcelink2 = resource_link::create('another-res-link-1', $newreslink->get_deploymentid(),
            $newreslink->get_resourceid() + 1);
        $newreslink2 = $repository->save($resourcelink2);

        $repository->delete_by_resource($newreslink->get_resourceid());
        $this->assertFalse($repository->exists($newreslink->get_id()));
        $this->assertEmpty($repository->find($newreslink->get_id()));
        $this->assertFalse($DB->record_exists('enrol_lti_user_resource_link',
            ['resourcelinkid' => $newreslink->get_id()]));
        $this->assertTrue($repository->exists($newreslink2->get_id()));
        $this->assertInstanceOf(resource_link::class, $repository->find($newreslink2->get_id()));

        $this->assertNull($repository->delete_by_deployment($newreslink->get_deploymentid()));
    }

    /**
     * Test deleting a resource links by their deployment container.
     *
     * @covers ::delete_by_deployment
     */
    public function test_delete_by_deployment(): void {
        global $CFG;
        $this->resetAfterTest();
        $resourcelink = $this->generate_resource_link();
        $repository = new resource_link_repository();
        $newreslink = $repository->save($resourcelink);
        $this->assertTrue($repository->exists($newreslink->get_id()));

        // Also create a user from this resource link so we get some test user_resource_link mappings.
        $user = $newreslink->add_user(
            2,
            'source-id-123',
            $CFG->lang,
            'Perth',
            'AU',
            'An Example Institution',
            '99',
            2,
        );
        $userrepo = new user_repository();
        $userrepo->save($user);
        global $DB;
        $this->assertTrue($DB->record_exists('enrol_lti_user_resource_link',
            ['resourcelinkid' => $newreslink->get_id()]));

        $repository->delete_by_deployment($newreslink->get_deploymentid());
        $this->assertFalse($repository->exists($newreslink->get_id()));
        $this->assertEmpty($repository->find($newreslink->get_id()));
        $this->assertFalse($DB->record_exists('enrol_lti_user_resource_link',
            ['resourcelinkid' => $newreslink->get_id()]));

        $this->assertNull($repository->delete_by_deployment($newreslink->get_deploymentid()));
    }

    /**
     * Test checking existence in the store.
     *
     * @covers ::exists
     */
    public function test_exists(): void {
        $this->resetAfterTest();
        $resourcelink = $this->generate_resource_link();
        $repository = new resource_link_repository();
        $newreslink = $repository->save($resourcelink);
        $this->assertTrue($repository->exists($newreslink->get_id()));
        $repository->delete($newreslink->get_id());
        $this->assertFalse($repository->exists($newreslink->get_id()));
    }

    /**
     * Test update of an existing resource_link.
     *
     * @covers ::save
     */
    public function test_save_existing(): void {
        $this->resetAfterTest();
        $resourcelink = $this->generate_resource_link();
        $repository = new resource_link_repository();
        $newreslink = $repository->save($resourcelink);
        $newreslink->add_grade_service(
            new \moodle_url('https://lms.example.org/context/lineitems')
        );

        $updatedreslink = $repository->save($newreslink);
        $this->assertEquals($newreslink, $updatedreslink);
    }

    /**
     * Test update with a stale object which is no longer present in the store.
     *
     * @covers ::save
     */
    public function test_update_stale(): void {
        $this->resetAfterTest();
        $resourcelink = $this->generate_resource_link();
        $repository = new resource_link_repository();
        $newreslink = $repository->save($resourcelink);
        $repository->delete($newreslink->get_id());

        $newreslink->add_grade_service(
            new \moodle_url('https://lms.example.org/context/lineitems')
        );
        $this->expectException(\coding_exception::class);
        $repository->save($newreslink);
    }
}
