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
use enrol_lti\local\ltiadvantage\entity\user;

/**
 * Tests for user_repository objects.
 *
 * @package enrol_lti
 * @copyright 2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \enrol_lti\local\ltiadvantage\repository\user_repository
 */
class user_repository_test extends \advanced_testcase {
    /**
     * Helper to generate a new user instance.
     *
     * @param int $mockresourceid used to spoof a published resource, to which this user is associated.
     * @return user a user instance
     */
    protected function generate_user(int $mockresourceid = 1): user {
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

        $resourcelinkrepo = new resource_link_repository();
        $resourcelink = $saveddeployment->add_resource_link('resourcelinkid_123', $mockresourceid,
            $savedcontext->get_id());
        $savedresourcelink = $resourcelinkrepo->save($resourcelink);

        $user = $this->getDataGenerator()->create_user();
        $ltiuser = $savedresourcelink->add_user(
            $user->id,
            'source-id-123',
            'en',
            'Perth',
            'AU',
            'An Example Institution',
            '99',
            2,
        );

        $ltiuser->set_lastgrade(67.33333333);

        return $ltiuser;
    }

    /**
     * Helper to assert that all the key elements of two users (i.e. excluding id) are equal.
     *
     * @param user $expected the user whose values are deemed correct.
     * @param user $check the user to check.
     * @param bool $checkresourcelink whether or not to confirm the resource link value matches too.
     */
    protected function assert_same_user_values(user $expected, user $check, bool $checkresourcelink = false): void {
        $this->assertEquals($expected->get_deploymentid(), $check->get_deploymentid());
        $this->assertEquals($expected->get_city(), $check->get_city());
        $this->assertEquals($expected->get_country(), $check->get_country());
        $this->assertEquals($expected->get_institution(), $check->get_institution());
        $this->assertEquals($expected->get_timezone(), $check->get_timezone());
        $this->assertEquals($expected->get_maildisplay(), $check->get_maildisplay());
        $this->assertEquals($expected->get_lang(), $check->get_lang());
        if ($checkresourcelink) {
            $this->assertEquals($expected->get_resourcelinkid(), $check->get_resourcelinkid());
        }
    }

    /**
     * Helper to assert that all the key elements of a user are present in the DB.
     *
     * @param user $expected the user whose values are deemed correct.
     */
    protected function assert_user_db_values(user $expected) {
        global $DB;
        $sql = "SELECT u.username, u.firstname, u.lastname, u.email, u.city, u.country, u.institution, u.timezone,
                       u.maildisplay, u.mnethostid, u.confirmed, u.lang, u.auth
                  FROM {enrol_lti_users} lu
                  JOIN {user} u
                    ON (lu.userid = u.id)
                 WHERE lu.id = :id";
        $userrecord = $DB->get_record_sql($sql, ['id' => $expected->get_id()]);
        $this->assertEquals($expected->get_city(), $userrecord->city);
        $this->assertEquals($expected->get_country(), $userrecord->country);
        $this->assertEquals($expected->get_institution(), $userrecord->institution);
        $this->assertEquals($expected->get_timezone(), $userrecord->timezone);
        $this->assertEquals($expected->get_maildisplay(), $userrecord->maildisplay);
        $this->assertEquals($expected->get_lang(), $userrecord->lang);

        $ltiuserrecord = $DB->get_record('enrol_lti_users', ['id' => $expected->get_id()]);
        $this->assertEquals($expected->get_id(), $ltiuserrecord->id);
        $this->assertEquals($expected->get_sourceid(), $ltiuserrecord->sourceid);
        $this->assertEquals($expected->get_resourceid(), $ltiuserrecord->toolid);
        $this->assertEquals($expected->get_lastgrade(), $ltiuserrecord->lastgrade);

        if ($expected->get_resourcelinkid()) {
            $sql = "SELECT rl.id
                      FROM {enrol_lti_users} lu
                      JOIN {enrol_lti_user_resource_link} rlj
                        ON (lu.id = rlj.ltiuserid)
                      JOIN {enrol_lti_resource_link} rl
                        ON (rl.id = rlj.resourcelinkid)
                     WHERE lu.id = :id";
            $resourcelinkrecord = $DB->get_record_sql($sql, ['id' => $expected->get_id()]);
            $this->assertEquals($expected->get_resourcelinkid(), $resourcelinkrecord->id);
        }
    }

    /**
     * Tests adding a user to the store.
     *
     * @covers ::save
     */
    public function test_save_new() {
        $this->resetAfterTest();
        $user = $this->generate_user();
        $userrepo = new user_repository();
        $saveduser = $userrepo->save($user);

        $this->assertIsInt($saveduser->get_id());
        $this->assert_same_user_values($user, $saveduser, true);
        $this->assert_user_db_values($saveduser);
    }

    /**
     * Test saving an existing user instance.
     *
     * @covers ::save
     */
    public function test_save_existing() {
        $this->resetAfterTest();
        $user = $this->generate_user();
        $userrepo = new user_repository();
        $saveduser = $userrepo->save($user);

        $saveduser->set_city('New City');
        $saveduser->set_country('NZ');
        $saveduser->set_lastgrade(99.99999999);
        $saveduser2 = $userrepo->save($saveduser);

        $this->assertEquals($saveduser->get_id(), $saveduser2->get_id());
        $this->assert_same_user_values($saveduser, $saveduser2, true);
        $this->assert_user_db_values($saveduser2);
    }

    /**
     * Test saving an instance which exists by id, but has a different localid to the data in the store.
     *
     * @covers ::save
     */
    public function test_save_existing_localid_mismatch() {
        $this->resetAfterTest();
        $user = $this->generate_user();
        $userrepo = new user_repository();
        $saveduser = $userrepo->save($user);

        $user2 = user::create(
            $saveduser->get_resourceid(),
            999999,
            $saveduser->get_deploymentid(),
            $saveduser->get_sourceid(),
            $saveduser->get_lang(),
            $saveduser->get_timezone(),
            '',
            '',
            '',
            null,
            null,
            null,
            null,
            $saveduser->get_id()
        );
        $this->expectException(\coding_exception::class);
        $this->expectExceptionMessage("Cannot update user mapping. LTI user '{$saveduser->get_id()}' is already mapped " .
            "to user '{$saveduser->get_localid()}' and can't be associated with another user '999999'.");
        $userrepo->save($user2);
    }

    /**
     * Test trying to save a user with an id that is invalid.
     *
     * @covers ::save
     */
    public function test_save_stale_id() {
        $this->resetAfterTest();
        $instructoruser = $this->getDataGenerator()->create_user();
        $userrepo = new user_repository();
        $user = user::create(
            4,
            $instructoruser->id,
            5,
            'source-id-123',
            'en',
            '99',
            '',
            '',
            '',
            null,
            null,
            null,
            null,
            999999
        );

        $this->expectException(\coding_exception::class);
        $this->expectExceptionMessage("Cannot save lti user with id '999999'. The record does not exist.");
        $userrepo->save($user);
    }

    /**
     * Verify that trying to save a stale object results in an exception referring to unique constraint violation.
     *
     * @covers ::save
     */
    public function test_save_uniqueness_constraint() {
        $this->resetAfterTest();
        $user = $this->generate_user();
        $userrepo = new user_repository();
        $userrepo->save($user);

        $this->expectException(\coding_exception::class);
        $this->expectExceptionMessageMatches("/Cannot create duplicate LTI user '[a-z0-9_]*' for resource '[0-9]*'/");
        $userrepo->save($user);
    }

    /**
     * Test finding a user instance by id.
     *
     * @covers ::find
     */
    public function test_find() {
        $this->resetAfterTest();
        $user = $this->generate_user();
        $userrepo = new user_repository();
        $saveduser = $userrepo->save($user);

        $founduser = $userrepo->find($saveduser->get_id());
        $this->assertIsInt($founduser->get_id());
        $this->assert_same_user_values($saveduser, $founduser, false);

        $this->assertNull($userrepo->find(0));
    }

    /**
     * Test finding all of users associated with a given published resource.
     *
     * @covers ::find_by_resource
     */
    public function test_find_by_resource() {
        $this->resetAfterTest();
        $user = $this->generate_user();
        $userrepo = new user_repository();
        $saveduser = $userrepo->save($user);
        $instructoruser = $this->getDataGenerator()->create_user();

        $user2 = user::create(
            $saveduser->get_resourceid(),
            $instructoruser->id,
            $saveduser->get_deploymentid(),
            'another-user-123',
            'en',
            '99',
            'Perth',
            'AU',
            'An Example Institution',
            2
        );
        $saveduser2 = $userrepo->save($user2);
        $savedusers = [$saveduser->get_id() => $saveduser, $saveduser2->get_id() => $saveduser2];

        $foundusers = $userrepo->find_by_resource($saveduser->get_resourceid());
        $this->assertCount(2, $foundusers);
        foreach ($foundusers as $founduser) {
            $this->assert_same_user_values($savedusers[$founduser->get_id()], $founduser);
        }
    }

    /**
     * Test that users can be found based on their resource_link association.
     *
     * @covers ::find_by_resource_link
     */
    public function test_find_by_resource_link() {
        $this->resetAfterTest();
        $user = $this->generate_user();
        $user->set_resourcelinkid(33);
        $userrepo = new user_repository();
        $saveduser = $userrepo->save($user);

        $instructoruser = $this->getDataGenerator()->create_user();
        $user2 = user::create(
            $saveduser->get_resourceid(),
            $instructoruser->id,
            $saveduser->get_deploymentid(),
            'another-user-123',
            'en',
            '99',
            'Perth',
            'AU',
            'An Example Institution',
            2,
            null,
            null,
            33
        );
        $saveduser2 = $userrepo->save($user2);
        $savedusers = [$saveduser->get_id() => $saveduser, $saveduser2->get_id() => $saveduser2];

        $foundusers = $userrepo->find_by_resource_link(33);
        $this->assertCount(2, $foundusers);
        foreach ($foundusers as $founduser) {
            $this->assert_same_user_values($savedusers[$founduser->get_id()], $founduser);
        }
    }

    /**
     * Test checking existence of a user instance, based on id.
     *
     * @covers ::exists
     */
    public function test_exists() {
        $this->resetAfterTest();
        $user = $this->generate_user();
        $userrepo = new user_repository();
        $saveduser = $userrepo->save($user);

        $this->assertTrue($userrepo->exists($saveduser->get_id()));
        $this->assertFalse($userrepo->exists(-50));
    }

    /**
     * Test deleting a user instance, based on id.
     *
     * @covers ::delete
     */
    public function test_delete() {
        $this->resetAfterTest();
        $user = $this->generate_user();
        $userrepo = new user_repository();
        $saveduser = $userrepo->save($user);
        $this->assertTrue($userrepo->exists($saveduser->get_id()));

        $userrepo->delete($saveduser->get_id());
        $this->assertFalse($userrepo->exists($saveduser->get_id()));

        global $DB;
        $this->assertFalse($DB->record_exists('enrol_lti_users', ['id' => $saveduser->get_id()]));
        $this->assertFalse($DB->record_exists('enrol_lti_user_resource_link', ['ltiuserid' => $saveduser->get_id()]));
        $this->assertTrue($DB->record_exists('user', ['id' => $saveduser->get_localid()]));

        $this->assertNull($userrepo->delete($saveduser->get_id()));
    }

    /**
     * Test deleting a collection of lti user instances by deployment.
     *
     * @covers ::delete_by_deployment
     */
    public function test_delete_by_deployment() {
        $this->resetAfterTest();
        $user = $this->generate_user();
        $userrepo = new user_repository();
        $saveduser = $userrepo->save($user);
        $instructoruser = $this->getDataGenerator()->create_user();
        $instructor2user = $this->getDataGenerator()->create_user();

        $user2 = user::create(
            $saveduser->get_resourceid(),
            $instructoruser->id,
            $saveduser->get_deploymentid(),
            'another-user-123',
            'en',
            '99',
            'Perth',
            'AU',
            'An Example Institution',
        );
        $saveduser2 = $userrepo->save($user2);

        $user3 = user::create(
            $saveduser->get_resourceid(),
            $instructor2user->id,
            $saveduser->get_deploymentid() + 1,
            'another-user-678',
            'en',
            '99',
            'Melbourne',
            'AU',
            'An Example Institution',
        );
        $saveduser3 = $userrepo->save($user3);
        $this->assertTrue($userrepo->exists($saveduser->get_id()));
        $this->assertTrue($userrepo->exists($saveduser2->get_id()));
        $this->assertTrue($userrepo->exists($saveduser3->get_id()));

        $userrepo->delete_by_deployment($saveduser->get_deploymentid());
        $this->assertFalse($userrepo->exists($saveduser->get_id()));
        $this->assertFalse($userrepo->exists($saveduser2->get_id()));
        $this->assertTrue($userrepo->exists($saveduser3->get_id()));
    }

    /**
     * Verify a user who has been deleted can be re-saved to the repository and matched to an existing local user.
     *
     * @covers ::save
     */
    public function test_save_deleted() {
        $this->resetAfterTest();
        $user = $this->generate_user();
        $userrepo = new user_repository();
        $saveduser = $userrepo->save($user);

        $userrepo->delete($saveduser->get_id());
        $this->assertFalse($userrepo->exists($saveduser->get_id()));

        $saveduser2 = $userrepo->save($user);
        $this->assertEquals($saveduser->get_localid(), $saveduser2->get_localid());
        $this->assertNotEquals($saveduser->get_id(), $saveduser2->get_id());
    }
}
