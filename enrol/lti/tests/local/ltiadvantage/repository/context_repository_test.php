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
use enrol_lti\local\ltiadvantage\entity\context;
use enrol_lti\local\ltiadvantage\entity\application_registration;

/**
 * Tests for context_repository.
 *
 * @package enrol_lti
 * @copyright 2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \enrol_lti\local\ltiadvantage\repository\context_repository
 */
class context_repository_test extends \advanced_testcase {
    /**
     * Helper to create test context objects for use with the repository tests.
     *
     * @return context the context.
     */
    protected function create_test_context(): context {
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

        return $saveddeployment->add_context('CTX123', ['http://purl.imsglobal.org/vocab/lis/v2/course#CourseSection']);
    }

    /**
     * Helper to assert that all the key elements of two contexts (i.e. excluding id) are equal.
     *
     * @param context $expected the context whose values are deemed correct.
     * @param context $check the context to check.
     */
    protected function assert_same_context_values(context $expected, context $check): void {
        $this->assertEquals($expected->get_deploymentid(), $check->get_deploymentid());
        $this->assertEquals($expected->get_contextid(), $check->get_contextid());
        $this->assertEquals($expected->get_types(), $check->get_types());
    }

    /**
     * Helper to assert that all the key elements of a context are present in the DB.
     *
     * @param context $expected the context whose values are deemed correct.
     */
    protected function assert_context_db_values(context $expected) {
        global $DB;
        $checkrecord = $DB->get_record('enrol_lti_context', ['id' => $expected->get_id()]);
        $this->assertEquals($expected->get_id(), $checkrecord->id);
        $this->assertEquals($expected->get_deploymentid(), $checkrecord->ltideploymentid);
        $this->assertEquals($expected->get_contextid(), $checkrecord->contextid);
        $this->assertEquals(json_encode($expected->get_types()), $checkrecord->type);
        $this->assertNotEmpty($checkrecord->timecreated);
        $this->assertNotEmpty($checkrecord->timemodified);
    }

    /**
     * Test saving a new context.
     *
     * @covers ::save
     */
    public function test_save_new() {
        $this->resetAfterTest();
        $context = $this->create_test_context();
        $contextrepo = new context_repository();
        $saved = $contextrepo->save($context);

        $this->assertIsInt($saved->get_id());
        $this->assert_same_context_values($context, $saved);
        $this->assert_context_db_values($saved);
    }

    /**
     * Test saving an existing context.
     *
     * @covers ::save
     */
    public function test_save_existing() {
        $this->resetAfterTest();
        $context = $this->create_test_context();
        $contextrepo = new context_repository();
        $saved = $contextrepo->save($context);

        $context2 = $context::create(
            $saved->get_deploymentid(),
            $saved->get_contextid(),
            $saved->get_types(),
            $saved->get_id()
        );
        $saved2 = $contextrepo->save($saved);

        $this->assertEquals($saved->get_id(), $saved2->get_id());
        $this->assert_same_context_values($saved, $saved2);
        $this->assert_context_db_values($saved2);
    }

    /**
     * Test trying to save two contexts with the same id for the same deployment.
     *
     * @covers ::save
     */
    public function test_save_unique_constraints_not_met() {
        $this->resetAfterTest();
        $context = $this->create_test_context();
        $context2 = clone $context;

        $contextrepo = new context_repository();
        $saved = $contextrepo->save($context);
        $this->assertInstanceOf(context::class, $saved);

        $this->expectException(\dml_exception::class);
        $contextrepo->save($context2);
    }

    /**
     * Test existence of a context within the repository.
     *
     * @covers ::exists
     */
    public function test_exists() {
        $this->resetAfterTest();
        $contextrepo = new context_repository();
        $context = $this->create_test_context();
        $savedcontext = $contextrepo->save($context);

        $this->assertTrue($contextrepo->exists($savedcontext->get_id()));
        $this->assertFalse($contextrepo->exists(0));
    }

    /**
     * Test finding a context in the repository.
     *
     * @covers ::find
     */
    public function test_find() {
        $this->resetAfterTest();
        $context = $this->create_test_context();
        $contextrepo = new context_repository();
        $savedcontext = $contextrepo->save($context);

        $foundcontext = $contextrepo->find($savedcontext->get_id());
        $this->assertEquals($savedcontext->get_id(), $foundcontext->get_id());
        $this->assert_same_context_values($savedcontext, $foundcontext);
        $this->assertNull($contextrepo->find(0));
    }

    /**
     * Test finding a context by contextid within the deployment.
     *
     * @covers ::find_by_contextid
     */
    public function test_find_by_contextid() {
        $this->resetAfterTest();
        $context = $this->create_test_context();
        $contextrepo = new context_repository();
        $savedcontext = $contextrepo->save($context);

        $foundcontext = $contextrepo->find_by_contextid($savedcontext->get_contextid(),
            $savedcontext->get_deploymentid());
        $this->assertEquals($savedcontext->get_id(), $foundcontext->get_id());
        $this->assert_same_context_values($savedcontext, $foundcontext);
        $this->assertNull($contextrepo->find_by_contextid(0, $savedcontext->get_deploymentid()));
    }

    /**
     * Test deleting a context from the repository.
     *
     * @covers ::delete
     */
    public function test_delete() {
        $this->resetAfterTest();
        $context = $this->create_test_context();
        $contextrepo = new context_repository();
        $savedcontext = $contextrepo->save($context);
        $this->assertTrue($contextrepo->exists($savedcontext->get_id()));

        $contextrepo->delete($savedcontext->get_id());
        $this->assertFalse($contextrepo->exists($savedcontext->get_id()));

        $this->assertNull($contextrepo->delete($savedcontext->get_id()));
    }

    /**
     * Test deleting a context from the repository, by deployment.
     *
     * @covers ::delete_by_deployment
     */
    public function test_delete_by_deployment() {
        $this->resetAfterTest();
        $context = $this->create_test_context();
        $contextrepo = new context_repository();
        $savedcontext = $contextrepo->save($context);
        $context2 = context::create($savedcontext->get_deploymentid(), 'new-context-345', ['CourseSection']);
        $savedcontext2 = $contextrepo->save($context2);
        $context3 = context::create($savedcontext->get_deploymentid() + 1, 'new-context-567', ['CourseSection']);
        $savedcontext3 = $contextrepo->save($context3);
        $this->assertTrue($contextrepo->exists($savedcontext->get_id()));
        $this->assertTrue($contextrepo->exists($savedcontext2->get_id()));
        $this->assertTrue($contextrepo->exists($savedcontext3->get_id()));

        $contextrepo->delete_by_deployment($savedcontext->get_deploymentid());
        $this->assertFalse($contextrepo->exists($savedcontext->get_id()));
        $this->assertFalse($contextrepo->exists($savedcontext2->get_id()));
        $this->assertTrue($contextrepo->exists($savedcontext3->get_id()));

        $this->assertNull($contextrepo->delete_by_deployment($savedcontext->get_id()));
    }
}
