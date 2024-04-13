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

namespace enrol_lti\local\ltiadvantage\task;

use enrol_lti\helper;
use enrol_lti\local\ltiadvantage\entity\user;
use enrol_lti\local\ltiadvantage\repository\resource_link_repository;
use enrol_lti\local\ltiadvantage\repository\user_repository;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../lti_advantage_testcase.php');

/**
 * Tests for the enrol_lti\local\ltiadvantage\task\sync_members scheduled task.
 *
 * @package enrol_lti
 * @copyright 2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \enrol_lti\local\ltiadvantage\task\sync_members
 */
class sync_members_test extends \lti_advantage_testcase {

    /**
     * Verify the user's profile picture has been set, which is useful to verify picture syncs.
     *
     * @param int $userid the id of the Moodle user.
     * @param bool $match true to verify a match, false to verify a non-match.
     */
    protected function verify_user_profile_image(int $userid, bool $match = true): void {
        global $CFG;
        $user = \core_user::get_user($userid);
        $usercontext = \context_user::instance($user->id);
        $expected = $CFG->wwwroot . '/pluginfile.php/' . $usercontext->id . '/user/icon/boost/f2?rev='. $user->picture;

        $page = new \moodle_page();
        $page->set_url('/user/profile.php');
        $page->set_context(\context_system::instance());
        $renderer = $page->get_renderer('core');
        $userpicture = new \user_picture($user);
        if ($match) {
            $this->assertEquals($expected, $userpicture->get_url($page, $renderer)->out(false));
        } else {
            $this->assertNotEquals($expected, $userpicture->get_url($page, $renderer)->out(false));
        }

    }

    /**
     * Helper to get a list of mocked member entries for use in the mocked sync task.
     *
     * @param array $userids the array of lti user ids to use.
     * @param array|null $legacyuserids legacy user ids for the lti11_legacy_user_id property, null if not desired.
     * @param bool $names whether to include names in the user data or not.
     * @param bool $emails whether to include email in the user data or not.
     * @param bool $linklevel whether to mock the user return data at link-level (true) or context-level (false).
     * @param bool $picture whether to mock a user's picture field in the return data.
     * @param array $roles an array of IMS roles to include with each member which, if empty, defaults to just the learner role.
     * @return array the array of users.
     * @throws \Exception if the legacyuserids array doesn't contain the correct number of ids.
     */
    protected static function get_mock_members_with_ids(
        array $userids,
        ?array $legacyuserids = null,
        $names = true,
        $emails = true,
        bool $linklevel = true,
        bool $picture = false,
        array $roles = [],
    ): array {

        if (!is_null($legacyuserids) && count($legacyuserids) != count($userids)) {
            throw new \Exception('legacyuserids must contain the same number of ids as $userids.');
        }

        if (empty($roles)) {
            $roles = ['http://purl.imsglobal.org/vocab/lis/v2/membership#Learner'];
        }

        $users = [];
        foreach ($userids as $userid) {
            $user = ['user_id' => (string) $userid, 'roles' => $roles];
            if ($picture) {
                $user['picture'] = static::getExternalTestFileUrl('/test.jpg', false);
            }
            if ($names) {
                $user['given_name'] = 'Firstname' . $userid;
                $user['family_name'] = 'Surname' . $userid;
            }
            if ($emails) {
                $user['email'] = "firstname.surname{$userid}@lms.example.org";
            }
            if ($legacyuserids) {
                $user['lti11_legacy_user_id'] = array_shift($legacyuserids);
            }
            if ($linklevel) {
                // Link-level memberships also include a message property.
                $user['message'] = [
                    'https://purl.imsglobal.org/spec/lti/claim/message_type' => 'LtiResourceLinkRequest'
                ];
            }
            $users[] = $user;
        }
        return $users;
    }

    /**
     * Gets a task mocked to only support resource-link-level memberships request.
     *
     * @param array $resourcelinks array for stipulating per link users, containing list of [resourcelink, members].
     * @return sync_members|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function get_mock_task_resource_link_level(array $resourcelinks = []) {
        $mocktask = $this->getMockBuilder(sync_members::class)
            ->onlyMethods(['get_resource_link_level_members', 'get_context_level_members'])
            ->getMock();
        $mocktask->expects($this->any())
            ->method('get_context_level_members')
            ->will($this->returnCallback(function() {
                return false;
            }));
        $expectedcount = !empty($resourcelinks) ? count($resourcelinks) : 1;
        $mocktask->expects($this->exactly($expectedcount))
            ->method('get_resource_link_level_members')
            ->will($this->returnCallback(function ($nrpsinfo, $serviceconnector, $registration, $reslink) use ($resourcelinks) {
                if ($resourcelinks) {
                    foreach ($resourcelinks as $rl) {
                        if ($reslink->get_resourcelinkid() === $rl[0]->get_resourcelinkid()) {
                            return $rl[1];
                        }
                    }
                } else {
                    return self::get_mock_members_with_ids(range(1, 2));
                }
            }));
        return $mocktask;
    }

    /**
     * Gets a task mocked to only support context-level memberships request.
     *
     * @return sync_members|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function get_mock_task_context_level() {
        $mocktask = $this->getMockBuilder(sync_members::class)
            ->onlyMethods(['get_resource_link_level_members', 'get_context_level_members'])
            ->getMock();
        $mocktask->expects($this->any())
            ->method('get_resource_link_level_members')
            ->will($this->returnCallback(function() {
                // An exception is what the service code will throw if the resource link level service isn't available.
                throw new \Exception();
            }));
        $mocktask->expects($this->any())
            ->method('get_context_level_members')
            ->will($this->returnCallback(function() {
                return self::get_mock_members_with_ids(range(1, 3), null, true, true, false);
            }));;
        return $mocktask;
    }

    /**
     * Gets a sync task, with the remote calls mocked to return the supplied users.
     *
     * See get_mock_members_with_ids() for generating the users for input.
     *
     * @param array $users a list of users, the result of a call to get_mock_members_with_ids().
     * @return \PHPUnit\Framework\MockObject\MockObject the mock task.
     */
    protected function get_mock_task_with_users(array $users) {
        $mocktask = $this->getMockBuilder(sync_members::class)
            ->onlyMethods(['get_resource_link_level_members', 'get_context_level_members'])
            ->getMock();
        $mocktask->expects($this->any())
            ->method('get_context_level_members')
            ->will($this->returnCallback(function() {
                return false;
            }));
        $mocktask->expects($this->any())
            ->method('get_resource_link_level_members')
            ->will($this->returnCallback(function () use ($users) {
                return $users;
            }));
        return $mocktask;
    }

    /**
     * Check that all the given ltiusers are enrolled in the course.
     *
     * @param \stdClass $course the course instance.
     * @param user[] $ltiusers array of lti user instances.
     */
    protected function verify_course_enrolments(\stdClass $course, array $ltiusers) {
        global $CFG;
        require_once($CFG->libdir . '/enrollib.php');
        $enrolledusers = get_enrolled_users(\context_course::instance($course->id));
        $this->assertCount(count($ltiusers), $enrolledusers);
        $enrolleduserids = array_map(function($stringid) {
            return (int) $stringid;
        }, array_column($enrolledusers, 'id'));
        foreach ($ltiusers as $ltiuser) {
            $this->assertContains($ltiuser->get_localid(), $enrolleduserids);
        }
    }

    /**
     * Test confirming task name.
     *
     * @covers ::get_name
     */
    public function test_get_name(): void {
        $this->assertEquals(get_string('tasksyncmembers', 'enrol_lti'), (new sync_members())->get_name());
    }

    /**
     * Test a resource-link-level membership sync, confirming that all relevant domain objects are updated properly.
     *
     * @covers ::execute
     */
    public function test_resource_link_level_sync(): void {
        $this->resetAfterTest();
        [$course, $resource] = $this->create_test_environment();

        // Launch the tool for a user.
        $mocklaunch = $this->get_mock_launch($resource, self::get_mock_launch_users_with_ids(['1'])[0]);
        $instructoruser = $this->lti_advantage_user_authenticates('1');
        $launchservice = $this->get_tool_launch_service();
        $launchservice->user_launches_tool($instructoruser, $mocklaunch);

        // Sync members.
        $task = $this->get_mock_task_resource_link_level();
        $task->execute();

        // Verify 2 users and their corresponding course enrolments exist.
        $this->expectOutputRegex(
            "/Completed - Synced members for tool '$resource->id' in the course '$course->id'. ".
            "Processed 2 users; enrolled 2 members; unenrolled 0 members./"
        );
        $userrepo = new user_repository();
        $ltiusers = $userrepo->find_by_resource($resource->id);
        $this->assertCount(2, $ltiusers);
        $this->verify_course_enrolments($course, $ltiusers);
    }

    /**
     * Test a resource-link-level membership sync when there are more than one resource links for the resource.
     *
     * @covers ::execute
     */
    public function test_resource_link_level_sync_multiple_resource_links(): void {
        $this->resetAfterTest();
        [$course, $resource] = $this->create_test_environment();

        // Launch twice - once from each resource link in the platform.
        $launchservice = $this->get_tool_launch_service();
        $instructoruser = $this->lti_advantage_user_authenticates('1');
        $mocklaunch = $this->get_mock_launch($resource, self::get_mock_launch_users_with_ids(['1'])[0], '123');
        $launchservice->user_launches_tool($instructoruser, $mocklaunch);
        $mocklaunch = $this->get_mock_launch($resource, self::get_mock_launch_users_with_ids(['1'])[0], '456');
        $launchservice->user_launches_tool($instructoruser, $mocklaunch);

        // Now, grab the resource links.
        $rlrepo = new resource_link_repository();
        $reslinks = $rlrepo->find_by_resource($resource->id);
        $mockmembers = self::get_mock_members_with_ids(range(1, 10));
        $mockusers1 = array_slice($mockmembers, 0, 6);
        $mockusers2 = array_slice($mockmembers, 6);
        $resourcelinks = [
            [$reslinks[0], $mockusers1],
            [$reslinks[1], $mockusers2]
        ];

        // Sync the members, using the mock task set up to sync different sets of users for each resource link.
        $task = $this->get_mock_task_resource_link_level($resourcelinks);
        ob_start();
        $task->execute();
        $output = ob_get_contents();
        ob_end_clean();

        // Verify 10 users and their corresponding course enrolments exist.
        $userrepo = new user_repository();
        $ltiusers = $userrepo->find_by_resource($resource->id);
        $this->assertCount(10, $ltiusers);
        $this->assertStringContainsString("Completed - Synced 6 members for the resource link", $output);
        $this->assertStringContainsString("Completed - Synced 4 members for the resource link", $output);
        $this->assertStringContainsString("Completed - Synced members for tool '$resource->id' in the course '".
            "$resource->courseid'. Processed 10 users; enrolled 10 members; unenrolled 0 members.\n", $output);
        $this->verify_course_enrolments($course, $ltiusers);
    }

    /**
     * Verify the task will update users' profile pictures if the 'picture' member field is provided.
     *
     * @covers ::execute
     */
    public function test_user_profile_image_sync(): void {
        $this->resetAfterTest();
        [$course, $resource] = $this->create_test_environment();

        // Launch the tool for a user.
        $mocklaunch = $this->get_mock_launch($resource, self::get_mock_launch_users_with_ids(['1'])[0]);
        $launchservice = $this->get_tool_launch_service();
        $instructoruser = $this->lti_advantage_user_authenticates('1');
        $launchservice->user_launches_tool($instructoruser, $mocklaunch);

        // Sync members.
        $task = $this->get_mock_task_with_users(self::get_mock_members_with_ids(['1'], null, true, true, true, true));
        ob_start();
        $task->execute();
        ob_end_clean();

        // Verify 1 users and their corresponding course enrolments exist.
        $userrepo = new user_repository();
        $ltiusers = $userrepo->find_by_resource($resource->id);
        $this->assertCount(1, $ltiusers);
        $this->verify_course_enrolments($course, $ltiusers);

        // Verify user profile image has been updated.
        $this->verify_user_profile_image($ltiusers[0]->get_localid());
    }

    /**
     * Test a context-level membership sync, confirming that all relevant domain objects are updated properly.
     *
     * @covers ::execute
     */
    public function test_context_level_sync(): void {
        $this->resetAfterTest();
        [$course, $resource] = $this->create_test_environment();

        // Launch the tool for a user.
        $mocklaunch = $this->get_mock_launch($resource, self::get_mock_launch_users_with_ids(['1'])[0]);
        $launchservice = $this->get_tool_launch_service();
        $instructoruser = $this->lti_advantage_user_authenticates('1');
        $launchservice->user_launches_tool($instructoruser, $mocklaunch);

        // Sync members.
        $task = $this->get_mock_task_context_level();
        ob_start();
        $task->execute();
        ob_end_clean();

        // Verify 3 users and their corresponding course enrolments exist.
        $userrepo = new user_repository();
        $ltiusers = $userrepo->find_by_resource($resource->id);
        $this->assertCount(3, $ltiusers);
        $this->verify_course_enrolments($course, $ltiusers);
    }

    /**
     * Test verifying the sync task handles the omission/inclusion of PII information for users.
     *
     * @covers ::execute
     */
    public function test_sync_user_data(): void {
        $this->resetAfterTest();
        [$course, $resource, $resource2, $resource3, $appreg] = $this->create_test_environment();
        $userrepo = new user_repository();

        // Launch the tool for a user.
        $mocklaunch = $this->get_mock_launch($resource, self::get_mock_launch_users_with_ids(['1'])[0]);
        $launchservice = $this->get_tool_launch_service();
        $instructoruser = $this->lti_advantage_user_authenticates('1');
        $launchservice->user_launches_tool($instructoruser, $mocklaunch);

        // Sync members.
        $task = $this->get_mock_task_with_users(self::get_mock_members_with_ids(range(1, 5), null, false, false));

        ob_start();
        $task->execute();
        ob_end_clean();

        // Verify 5 users and their corresponding course enrolments exist.
        $ltiusers = $userrepo->find_by_resource($resource->id);
        $this->assertCount(5, $ltiusers);
        $this->verify_course_enrolments($course, $ltiusers);

        // Since user data wasn't included in the response, the users will have been synced using fallbacks,
        // so verify these.
        foreach ($ltiusers as $ltiuser) {
            $user = \core_user::get_user($ltiuser->get_localid());
            // Firstname falls back to sourceid.
            $this->assertEquals($ltiuser->get_sourceid(), $user->firstname);

            // Lastname falls back to resource context id.
            $this->assertEquals($appreg->get_platformid(), $user->lastname);

            // Email falls back to example.com.
            $issuersubhash = sha1($appreg->get_platformid() . '_' . $ltiuser->get_sourceid());
            $this->assertEquals("enrol_lti_13_{$issuersubhash}@example.com", $user->email);
        }

        // Sync again, this time with user data included.
        $mockmembers = self::get_mock_members_with_ids(range(1, 5));
        $task = $this->get_mock_task_with_users($mockmembers);

        ob_start();
        $task->execute();
        ob_end_clean();

        // User data was included in the response and should have been updated.
        $ltiusers = $userrepo->find_by_resource($resource->id);
        $this->assertCount(5, $ltiusers);
        $this->verify_course_enrolments($course, $ltiusers);
        foreach ($ltiusers as $ltiuser) {
            $user = \core_user::get_user($ltiuser->get_localid());
            $mockmemberindex = array_search($ltiuser->get_sourceid(), array_column($mockmembers, 'user_id'));
            $mockmember = $mockmembers[$mockmemberindex];
            $this->assertEquals($mockmember['given_name'], $user->firstname);
            $this->assertEquals($mockmember['family_name'], $user->lastname);
            $this->assertEquals($mockmember['email'], $user->email);
        }
    }

    /**
     * Test verifying the task won't sync members for shared resources having member sync disabled.
     *
     * @covers ::execute
     */
    public function test_membership_sync_disabled(): void {
        $this->resetAfterTest();
        [$course, $resource] = $this->create_test_environment(true, true, false);

        // Launch the tool for a user.
        $mockuser = self::get_mock_launch_users_with_ids(['1'])[0];
        $mocklaunch = $this->get_mock_launch($resource, $mockuser);
        $launchservice = $this->get_tool_launch_service();
        $instructoruser = $this->lti_advantage_user_authenticates('1');
        $launchservice->user_launches_tool($instructoruser, $mocklaunch);

        // Sync members.
        $task = $this->get_mock_task_with_users(self::get_mock_launch_users_with_ids(range(1, 4)));
        ob_start();
        $task->execute();
        ob_end_clean();

        // Verify no users were added or removed.
        // A single user (the user who launched the resource link) is expected.
        $userrepo = new user_repository();
        $ltiusers = $userrepo->find_by_resource($resource->id);
        $this->assertCount(1, $ltiusers);
        $this->assertEquals($mockuser['user_id'], $ltiusers[0]->get_sourceid());
        $this->verify_course_enrolments($course, $ltiusers);
    }

    /**
     * Test verifying the sync task for resources configured as 'helper::MEMBER_SYNC_ENROL_AND_UNENROL'.
     *
     * @covers ::execute
     */
    public function test_sync_mode_enrol_and_unenrol(): void {
        $this->resetAfterTest();
        [$course, $resource] = $this->create_test_environment();
        $userrepo = new user_repository();

        // Launch the tool for a user.
        $mockuser = self::get_mock_launch_users_with_ids(['1'])[0];
        $mocklaunch = $this->get_mock_launch($resource, $mockuser);
        $launchservice = $this->get_tool_launch_service();
        $instructoruser = $this->lti_advantage_user_authenticates('1');
        $launchservice->user_launches_tool($instructoruser, $mocklaunch);

        // Sync members.
        $task = $this->get_mock_task_with_users(self::get_mock_members_with_ids(range(1, 3)));

        ob_start();
        $task->execute();
        ob_end_clean();

        // Verify 3 users and their corresponding course enrolments exist.
        $ltiusers = $userrepo->find_by_resource($resource->id);
        $this->assertCount(3, $ltiusers);
        $this->verify_course_enrolments($course, $ltiusers);

        // Now, simulate a subsequent sync in which 1 existing user maintains access,
        // 2 existing users are unenrolled and 3 new users are enrolled.
        $task2 = $this->get_mock_task_with_users(self::get_mock_members_with_ids(['1', '4', '5', '6']));
        ob_start();
        $task2->execute();
        ob_end_clean();

        // Verify the missing users have been unenrolled and new users enrolled.
        $ltiusers = $userrepo->find_by_resource($resource->id);
        $this->assertCount(4, $ltiusers);
        $unenrolleduserids = ['2', '3'];
        $enrolleduserids = ['1', '4', '5', '6'];
        foreach ($ltiusers as $ltiuser) {
            $this->assertNotContains($ltiuser->get_sourceid(), $unenrolleduserids);
            $this->assertContains($ltiuser->get_sourceid(), $enrolleduserids);
        }
        $this->verify_course_enrolments($course, $ltiusers);
    }

    /**
     * Confirm the sync task operation for resources configured as 'helper::MEMBER_SYNC_UNENROL_MISSING'.
     *
     * @covers ::execute
     */
    public function test_sync_mode_unenrol_missing(): void {
        $this->resetAfterTest();
        [$course, $resource] = $this->create_test_environment(true, true, true, helper::MEMBER_SYNC_UNENROL_MISSING);
        $userrepo = new user_repository();

        // Launch the tool for a user.
        $mocklaunch = $this->get_mock_launch($resource, self::get_mock_launch_users_with_ids([1])[0]);
        $launchservice = $this->get_tool_launch_service();
        $instructoruser = $this->lti_advantage_user_authenticates('1');
        $launchservice->user_launches_tool($instructoruser, $mocklaunch);
        $this->assertCount(1, $userrepo->find_by_resource($resource->id));

        // Sync members using a payload which doesn't include the original launch user (User id = 1).
        $task = $this->get_mock_task_with_users(self::get_mock_members_with_ids(range(2, 3)));

        ob_start();
        $task->execute();
        ob_end_clean();

        // Verify the original user (launching user) has been unenrolled and that no new members have been enrolled.
        $ltiusers = $userrepo->find_by_resource($resource->id);
        $this->assertCount(0, $ltiusers);
    }

    /**
     * Confirm the sync task operation for resources configured as 'helper::MEMBER_SYNC_ENROL_NEW'.
     *
     * @covers ::execute
     */
    public function test_sync_mode_enrol_new(): void {
        $this->resetAfterTest();
        [$course, $resource] = $this->create_test_environment(true, true, true, helper::MEMBER_SYNC_ENROL_NEW);
        $userrepo = new user_repository();

        // Launch the tool for a user.
        $mocklaunch = $this->get_mock_launch($resource, self::get_mock_launch_users_with_ids([1])[0]);
        $launchservice = $this->get_tool_launch_service();
        $instructoruser = $this->lti_advantage_user_authenticates('1');
        $launchservice->user_launches_tool($instructoruser, $mocklaunch);
        $this->assertCount(1, $userrepo->find_by_resource($resource->id));

        // Sync members using a payload which includes two new members only (i.e. not the original launching user).
        $task = $this->get_mock_task_with_users(self::get_mock_members_with_ids(range(2, 3)));

        ob_start();
        $task->execute();
        ob_end_clean();

        // Verify we now have 3 enrolments. The original user (who was not unenrolled) and the 2 new users.
        $ltiusers = $userrepo->find_by_resource($resource->id);
        $this->assertCount(3, $ltiusers);
        $this->verify_course_enrolments($course, $ltiusers);
    }

    /**
     * Test confirming that no changes take place if the auth_lti plugin is not enabled.
     *
     * @covers ::execute
     */
    public function test_sync_auth_disabled(): void {
        $this->resetAfterTest();
        [$course, $resource] = $this->create_test_environment(false);
        $userrepo = new user_repository();

        // Launch the tool for a user.
        $mocklaunch = $this->get_mock_launch($resource, self::get_mock_launch_users_with_ids([1])[0]);
        $launchservice = $this->get_tool_launch_service();
        $instructoruser = $this->lti_advantage_user_authenticates('1');
        $launchservice->user_launches_tool($instructoruser, $mocklaunch);
        $this->assertCount(1, $userrepo->find_by_resource($resource->id));

        // If the task were to run, this would trigger 1 unenrolment (the launching user) and 3 enrolments.
        $task = $this->get_mock_task_with_users(self::get_mock_members_with_ids(range(2, 2)));
        $task->execute();

        // Verify that the sync didn't take place.
        $this->expectOutputRegex("/Skipping task - Authentication plugin 'LTI' is not enabled/");
        $this->assertCount(1, $userrepo->find_by_resource($resource->id));
    }

    /**
     * Test confirming that no sync takes place when the enrol_lti plugin is not enabled.
     *
     * @covers ::execute
     */
    public function test_sync_enrol_disabled(): void {
        $this->resetAfterTest();
        [$course, $resource] = $this->create_test_environment(true, false);
        $userrepo = new user_repository();

        // Launch the tool for a user.
        $mocklaunch = $this->get_mock_launch($resource, self::get_mock_launch_users_with_ids([1])[0]);
        $launchservice = $this->get_tool_launch_service();
        $instructoruser = $this->lti_advantage_user_authenticates('1');
        $launchservice->user_launches_tool($instructoruser, $mocklaunch);
        $this->assertCount(1, $userrepo->find_by_resource($resource->id));

        // If the task were to run, this would trigger 1 unenrolment of the launching user and enrolment of 3 users.
        $task = $this->get_mock_task_with_users(self::get_mock_members_with_ids(range(2, 2)));
        $task->execute();

        // Verify that the sync didn't take place.
        $this->expectOutputRegex("/Skipping task - The 'Publish as LTI tool' plugin is disabled/");
        $this->assertCount(1, $userrepo->find_by_resource($resource->id));
    }

    /**
     * Test syncing members when the enrolment instance is disabled.
     *
     * @covers ::execute
     */
    public function test_sync_members_disabled_instance(): void {
        $this->resetAfterTest();
        global $DB;

        [$course, $resource, $resource2, $resource3] = $this->create_test_environment();
        $userrepo = new user_repository();

        // Disable resource 1.
        $enrol = (object) ['id' => $resource->enrolid, 'status' => ENROL_INSTANCE_DISABLED];
        $DB->update_record('enrol', $enrol);

        // Delete the activity being shared by resource2, leaving resource 2 disabled as a result.
        $modcontext = \context::instance_by_id($resource2->contextid);
        course_delete_module($modcontext->instanceid);

        // Only the enabled resource 3 should sync members.
        $task = $this->get_mock_task_with_users(self::get_mock_members_with_ids(range(1, 1)));
        $task->execute();

        $this->expectOutputRegex(
            "/^Starting - Member sync for published resource '$resource3->id' for course '$course->id'.\n".
            "Completed - Synced members for tool '$resource3->id' in the course '$course->id'. Processed 0 users; ".
            "enrolled 0 members; unenrolled 0 members.\n$/"
        );
        $this->assertCount(0, $userrepo->find_by_resource($resource->id));
    }

    /**
     * Test syncing members for a membersync-enabled resource when the launch omits the NRPS service endpoints.
     *
     * @covers ::execute
     */
    public function test_sync_no_nrps_support(): void {
        $this->resetAfterTest();
        [$course, $resource] = $this->create_test_environment();
        $userrepo = new user_repository();

        // Launch the tool for a user.
        $mockinstructor = self::get_mock_launch_users_with_ids([1])[0];
        $mocklaunch = $this->get_mock_launch($resource, $mockinstructor, null, null, false);
        $launchservice = $this->get_tool_launch_service();
        $instructoruser = $this->lti_advantage_user_authenticates('1');
        $launchservice->user_launches_tool($instructoruser, $mocklaunch);
        $this->assertCount(1, $userrepo->find_by_resource($resource->id));

        // The task would sync an additional 2 users if the link had NRPS service support.
        $task = $this->get_mock_task_with_users(self::get_mock_members_with_ids(range(2, 2)));

        // We expect the task to report that it is skipping the resource due to a lack of NRPS support.
        $task->execute();

        // Verify no enrolments or unenrolments.
        $this->expectOutputRegex(
            "/Skipping - No names and roles service found.\n".
            "Completed - Synced members for tool '{$resource->id}' in the course '{$course->id}'. ".
            "Processed 0 users; enrolled 0 members; unenrolled 0 members./"
        );
        $this->assertCount(1, $userrepo->find_by_resource($resource->id));
    }

    /**
     * Test confirming that preexisting, non-lti user accounts do not have their profiles or pictures updated during sync.
     *
     * @covers ::execute
     */
    public function test_sync_non_lti_linked_user(): void {
        $this->resetAfterTest();

        // Set up the environment.
        [$course, $resource] = $this->create_test_environment();

        // Fake an auth - making sure it's a manual account.
        $authenticateduser = $this->lti_advantage_user_authenticates('123');
        $authenticateduser->auth = 'manual';
        $authenticateduser->password = '1234abcD*';
        user_update_user($authenticateduser);
        $authenticateduser = \core_user::get_user($authenticateduser->id);

        // Mock the launch for the specified user.
        $mocklaunchuser = self::get_mock_launch_users_with_ids([$authenticateduser->id])[0];
        $mocklaunch = $this->get_mock_launch($resource, $mocklaunchuser);
        $this->get_tool_launch_service()->user_launches_tool($authenticateduser, $mocklaunch);

        // Prepare the sync task, with a stubbed list of members.
        $task = $this->get_mock_task_with_users(self::get_mock_members_with_ids(['123'], null, true, true, true, true));

        // Run the member sync.
        $this->expectOutputRegex(
            "/Skipped profile sync for user '$authenticateduser->id'. The user does not belong to the LTI auth method.\n" .
            "Skipped picture sync for user '$authenticateduser->id'. The user does not belong to the LTI auth method/"
        );
        $task->execute();

        $updateduser = \core_user::get_user($authenticateduser->id);
        $this->assertEquals($authenticateduser->firstname, $updateduser->firstname);
        $this->assertEquals($authenticateduser->lastname, $updateduser->lastname);
        $this->assertEquals($authenticateduser->email, $updateduser->email);
        $this->verify_user_profile_image($authenticateduser->id, false);
    }

    /**
     * Test the member sync for a range of scenarios including migrated tools, unlaunched tools, provisioning methods.
     *
     * @dataProvider member_sync_data_provider
     * @param array|null $legacydata array detailing what legacy information to create, or null if not required.
     * @param array|null $resourceconfig array detailing config values to be used when creating the test enrol_lti instances.
     * @param array $launchdata array containing details of the launch, including user and migration claim.
     * @param array|null $syncmembers the members to use in the mock sync.
     * @param array $expected the array detailing expectations.
     * @covers ::execute
     */
    public function test_sync_enrolments_and_migration(?array $legacydata, ?array $resourceconfig, array $launchdata,
            ?array $syncmembers, array $expected): void {

        $this->resetAfterTest();

        // Set up the environment.
        [$course, $resource] = $this->create_test_environment(true, true, true, helper::MEMBER_SYNC_ENROL_AND_UNENROL, true, false,
            0, $resourceconfig['provisioningmodeinstructor'] ?? 0, $resourceconfig['provisioningmodelearner'] ?? 0);

        // Set up legacy tool and user data.
        if ($legacydata) {
            [$legacytools, $legacyconsumerrecord, $legacyusers] = $this->setup_legacy_data($course, $legacydata);
        }

        // Mock the launch for the specified user.
        $mocklaunch = $this->get_mock_launch($resource, $launchdata['user'], null, [], true,
            $launchdata['launch_migration_claim']);

        // Perform the launch.
        $instructoruser = $this->lti_advantage_user_authenticates(
            $launchdata['user']['user_id'],
            $launchdata['launch_migration_claim'] ?? []
        );
        $this->get_tool_launch_service()->user_launches_tool($instructoruser, $mocklaunch);

        // Prepare the sync task, with a stubbed list of members.
        $task = $this->get_mock_task_with_users($syncmembers);

        // Run the member sync.
        ob_start();
        $task->execute();
        ob_end_clean();

        // Verify enrolments.
        $ltiusers = (new user_repository())->find_by_resource($resource->id);
        $enrolled = array_filter($expected['enrolments'], function($user) {
            return $user['is_enrolled'];
        });
        $this->assertCount(count($enrolled), $ltiusers);
        $this->verify_course_enrolments($course, $ltiusers);

        // Verify migration, if expected.
        if ($legacydata) {
            $legacyuserids = array_column($legacyusers, 'id');
            foreach ($ltiusers as $ltiuser) {
                $this->assertArrayHasKey($ltiuser->get_sourceid(), $expected['enrolments']);
                if (!$expected['enrolments'][$ltiuser->get_sourceid()]['is_migrated']) {
                    // Those members who hadn't launched over 1p1 prior will have new lti user records created.
                    $this->assertNotContains((string)$ltiuser->get_localid(), $legacyuserids);
                } else {
                    // Those members who were either already migrated during launch, or were migrated during the sync,
                    // will be mapped to their legacy user accounts.
                    $this->assertContains((string)$ltiuser->get_localid(), $legacyuserids);
                }
            }
        }
    }

    /**
     * Data provider for member syncs.
     *
     * @return array[] the array of test data.
     */
    public static function member_sync_data_provider(): array {
        global $CFG;
        require_once($CFG->dirroot . '/auth/lti/auth.php');
        return [
            'Migrated tool, user ids changed, new and existing users present in sync' => [
                'legacy_data' => [
                    'users' => [
                        ['user_id' => '1'],
                        ['user_id' => '2'],
                    ],
                    'consumer_key' => 'CONSUMER_1',
                    'tools' => [
                        ['secret' => 'toolsecret1'],
                        ['secret' => 'toolsecret2'],
                    ]
                ],
                'resource_config' => null,
                'launch_data' => [
                    'user' => self::get_mock_launch_users_with_ids(['1p3_1'])[0],
                    'launch_migration_claim' => [
                        'consumer_key' => 'CONSUMER_1',
                        'signing_secret' => 'toolsecret1',
                        'user_id' => '1',
                        'context_id' => 'd345b',
                        'tool_consumer_instance_guid' => '12345-123',
                        'resource_link_id' => '4b6fa'
                    ],
                ],
                'sync_members_data' => [
                    self::get_mock_members_with_ids(['1p3_1'], ['1'])[0],
                    self::get_mock_members_with_ids(['1p3_2'], ['2'])[0],
                    self::get_mock_members_with_ids(['1p3_3'], ['3'])[0],
                    self::get_mock_members_with_ids(['1p3_4'], ['4'])[0],
                ],
                'expected' => [
                    'enrolments' => [
                        '1p3_1' => [
                            'is_enrolled' => true,
                            'is_migrated' => true,
                        ],
                        '1p3_2' => [
                            'is_enrolled' => true,
                            'is_migrated' => true,
                        ],
                        '1p3_3' => [
                            'is_enrolled' => true,
                            'is_migrated' => false,
                        ],
                        '1p3_4' => [
                            'is_enrolled' => true,
                            'is_migrated' => false,
                        ]
                    ]
                ]
            ],
            'Migrated tool, no change in user ids, new and existing users present in sync' => [
                'legacy_data' => [
                    'users' => [
                        ['user_id' => '1'],
                        ['user_id' => '2'],
                    ],
                    'consumer_key' => 'CONSUMER_1',
                    'tools' => [
                        ['secret' => 'toolsecret1'],
                        ['secret' => 'toolsecret2'],
                    ]
                ],
                'resource_config' => null,
                'launch_data' => [
                    'user' => self::get_mock_launch_users_with_ids(['1'])[0],
                    'launch_migration_claim' => [
                        'consumer_key' => 'CONSUMER_1',
                        'signing_secret' => 'toolsecret1',
                        'context_id' => 'd345b',
                        'tool_consumer_instance_guid' => '12345-123',
                        'resource_link_id' => '4b6fa'
                    ],
                ],
                'sync_members_data' => [
                    self::get_mock_members_with_ids(['1'], null)[0],
                    self::get_mock_members_with_ids(['2'], null)[0],
                    self::get_mock_members_with_ids(['3'], null)[0],
                    self::get_mock_members_with_ids(['4'], null)[0],
                ],
                'expected' => [
                    'enrolments' => [
                        '1' => [
                            'is_enrolled' => true,
                            'is_migrated' => true,
                        ],
                        '2' => [
                            'is_enrolled' => true,
                            'is_migrated' => true,
                        ],
                        '3' => [
                            'is_enrolled' => true,
                            'is_migrated' => false,
                        ],
                        '4' => [
                            'is_enrolled' => true,
                            'is_migrated' => false,
                        ]
                    ]
                ]
            ],
            'New tool, no launch migration claim, change in user ids, new and existing users present in sync' => [
                'legacy_data' => [
                    'users' => [
                        ['user_id' => '1'],
                        ['user_id' => '2'],
                    ],
                    'consumer_key' => 'CONSUMER_1',
                    'tools' => [
                        ['secret' => 'toolsecret1'],
                        ['secret' => 'toolsecret2'],
                    ]
                ],
                'resource_config' => null,
                'launch_data' => [
                    'user' => self::get_mock_launch_users_with_ids(['1p3_1'])[0],
                    'launch_migration_claim' => null,
                ],
                'sync_members_data' => [
                    self::get_mock_members_with_ids(['1p3_1'], null)[0],
                    self::get_mock_members_with_ids(['1p3_2'], null)[0],
                    self::get_mock_members_with_ids(['1p3_3'], null)[0],
                    self::get_mock_members_with_ids(['1p3_4'], null)[0],
                ],
                'expected' => [
                    'enrolments' => [
                        '1p3_1' => [
                            'is_enrolled' => true,
                            'is_migrated' => false,
                        ],
                        '1p3_2' => [
                            'is_enrolled' => true,
                            'is_migrated' => false,
                        ],
                        '1p3_3' => [
                            'is_enrolled' => true,
                            'is_migrated' => false,
                        ],
                        '1p3_4' => [
                            'is_enrolled' => true,
                            'is_migrated' => false,
                        ]
                    ]
                ]
            ],
            'New tool, no launch migration claim, no change in user ids, new and existing users present in sync' => [
                'legacy_data' => [
                    'users' => [
                        ['user_id' => '1'],
                        ['user_id' => '2'],
                    ],
                    'consumer_key' => 'CONSUMER_1',
                    'tools' => [
                        ['secret' => 'toolsecret1'],
                        ['secret' => 'toolsecret2'],
                    ]
                ],
                'resource_config' => null,
                'launch_data' => [
                    'user' => self::get_mock_launch_users_with_ids(['1'])[0],
                    'launch_migration_claim' => null,
                ],
                'sync_members_data' => [
                    self::get_mock_members_with_ids(['1'], null)[0],
                    self::get_mock_members_with_ids(['2'], null)[0],
                    self::get_mock_members_with_ids(['3'], null)[0],
                    self::get_mock_members_with_ids(['4'], null)[0],
                ],
                'expected' => [
                    'enrolments' => [
                        '1' => [
                            'is_enrolled' => true,
                            'is_migrated' => false,
                        ],
                        '2' => [
                            'is_enrolled' => true,
                            'is_migrated' => false,
                        ],
                        '3' => [
                            'is_enrolled' => true,
                            'is_migrated' => false,
                        ],
                        '4' => [
                            'is_enrolled' => true,
                            'is_migrated' => false,
                        ]
                    ]
                ]
            ],
            'New tool, migration only via member sync, no launch claim, new and existing users present in sync' => [
                'legacy_data' => [
                    'users' => [
                        ['user_id' => '1'],
                        ['user_id' => '2'],
                    ],
                    'consumer_key' => 'CONSUMER_1',
                    'tools' => [
                        ['secret' => 'toolsecret1'],
                        ['secret' => 'toolsecret2'],
                    ]
                ],
                'resource_config' => null,
                'launch_data' => [
                    'user' => self::get_mock_launch_users_with_ids(['1p3_1'])[0],
                    'launch_migration_claim' => null,
                ],
                'sync_members_data' => [
                    self::get_mock_members_with_ids(['1p3_1'], ['1'])[0],
                    self::get_mock_members_with_ids(['1p3_2'], ['2'])[0],
                    self::get_mock_members_with_ids(['1p3_3'], ['3'])[0],
                    self::get_mock_members_with_ids(['1p3_4'], ['4'])[0],
                ],
                'expected' => [
                    'enrolments' => [
                        '1p3_1' => [
                            'is_enrolled' => true,
                            'is_migrated' => false,
                        ],
                        '1p3_2' => [
                            'is_enrolled' => true,
                            'is_migrated' => false,
                        ],
                        '1p3_3' => [
                            'is_enrolled' => true,
                            'is_migrated' => false,
                        ],
                        '1p3_4' => [
                            'is_enrolled' => true,
                            'is_migrated' => false,
                        ]
                    ]
                ]
            ],
            'Default provisioning modes, mixed bag of users and roles' => [
                'legacy_data' => null,
                'resource_config' => [
                    'provisioningmodelearner' => \auth_plugin_lti::PROVISIONING_MODE_AUTO_ONLY,
                    'provisioningmodeinstructor' => \auth_plugin_lti::PROVISIONING_MODE_PROMPT_NEW_EXISTING
                ],
                'launch_data' => [
                    'user' => self::get_mock_launch_users_with_ids(['1p3_1'])[0],
                    'launch_migration_claim' => null,
                ],
                'sync_members_data' => [
                    // This user is just an instructor but is also the user who is already linked, via the launch above.
                    self::get_mock_members_with_ids(['1p3_1'], null, true, true, true, false, [
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Instructor',
                    ])[0],
                    // This user is just a learner.
                    self::get_mock_members_with_ids(['1p3_2'], null, true, true, true, false, [
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner'
                    ])[0],
                    // This user is also a learner.
                    self::get_mock_members_with_ids(['1p3_3'], null, true, true, true, false, [
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner'
                    ])[0],
                    // This user is both an instructor and a learner.
                    self::get_mock_members_with_ids(['1p3_4'], null, true, true, true, false, [
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Instructor',
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner'
                    ])[0],
                ],
                'expected' => [
                    'enrolments' => [
                        '1p3_1' => [
                            'is_enrolled' => true, // Instructor - enrolled because they are also the launch user (already linked).
                            'is_migrated' => false,
                        ],
                        '1p3_2' => [
                            'is_enrolled' => true, // Learner - enrolled due to 'auto' provisioning mode.
                            'is_migrated' => false,
                        ],
                        '1p3_3' => [
                            'is_enrolled' => true, // Learner - enrolled due to 'auto' provisioning mode.
                            'is_migrated' => false,
                        ],
                        '1p3_4' => [
                            'is_enrolled' => false,  // Both roles - not enrolled due to instructor's 'prompt' provisioning mode.
                            'is_migrated' => false,
                        ]
                    ]
                ]
            ],
            'All automatic provisioning, mixed bag of users and roles' => [
                'legacy_data' => null,
                'resource_config' => [
                    'provisioningmodelearner' => \auth_plugin_lti::PROVISIONING_MODE_AUTO_ONLY,
                    'provisioningmodeinstructor' => \auth_plugin_lti::PROVISIONING_MODE_AUTO_ONLY
                ],
                'launch_data' => [
                    'user' => self::get_mock_launch_users_with_ids(['1p3_1'])[0],
                    'launch_migration_claim' => null,
                ],
                'sync_members_data' => [
                    // This user is just an instructor but is also the user who is already linked, via the launch above.
                    self::get_mock_members_with_ids(['1p3_1'], null, true, true, true, false, [
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Instructor',
                    ])[0],
                    // This user is just a learner.
                    self::get_mock_members_with_ids(['1p3_2'], null, true, true, true, false, [
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner'
                    ])[0],
                    // This user is also a learner.
                    self::get_mock_members_with_ids(['1p3_3'], null, true, true, true, false, [
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner'
                    ])[0],
                    // This user is both an instructor and a learner.
                    self::get_mock_members_with_ids(['1p3_4'], null, true, true, true, false, [
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Instructor',
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner'
                    ])[0],
                ],
                'expected' => [
                    'enrolments' => [
                        '1p3_1' => [
                            'is_enrolled' => true, // Instructor - enrolled because they are also the launch user (already linked).
                            'is_migrated' => false,
                        ],
                        '1p3_2' => [
                            'is_enrolled' => true, // Learner - enrolled due to 'auto' provisioning mode.
                            'is_migrated' => false,
                        ],
                        '1p3_3' => [
                            'is_enrolled' => true, // Learner - enrolled due to 'auto' provisioning mode.
                            'is_migrated' => false,
                        ],
                        '1p3_4' => [
                            'is_enrolled' => true, // Both roles - enrolled due to instructor's 'auto' provisioning mode.
                            'is_migrated' => false,
                        ]
                    ]
                ]
            ],
            'All prompt provisioning, mixed bag of users and roles' => [
                'legacy_data' => null,
                'resource_config' => [
                    'provisioningmodelearner' => \auth_plugin_lti::PROVISIONING_MODE_PROMPT_NEW_EXISTING,
                    'provisioningmodeinstructor' => \auth_plugin_lti::PROVISIONING_MODE_PROMPT_NEW_EXISTING
                ],
                'launch_data' => [
                    'user' => self::get_mock_launch_users_with_ids(['1p3_1'])[0],
                    'launch_migration_claim' => null,
                ],
                'sync_members_data' => [
                    // This user is just an instructor but is also the user who is already linked, via the launch above.
                    self::get_mock_members_with_ids(['1p3_1'], null, true, true, true, false, [
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Instructor',
                    ])[0],
                    // This user is just a learner.
                    self::get_mock_members_with_ids(['1p3_2'], null, true, true, true, false, [
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner'
                    ])[0],
                    // This user is also a learner.
                    self::get_mock_members_with_ids(['1p3_3'], null, true, true, true, false, [
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner'
                    ])[0],
                    // This user is both an instructor and a learner.
                    self::get_mock_members_with_ids(['1p3_4'], null, true, true, true, false, [
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Instructor',
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner'
                    ])[0],
                ],
                'expected' => [
                    'enrolments' => [
                        '1p3_1' => [
                            'is_enrolled' => true, // Instructor - enrolled because they are also the launch user (already linked).
                            'is_migrated' => false,
                        ],
                        '1p3_2' => [
                            'is_enrolled' => false, // Learner - not enrolled due to 'prompt' provisioning mode.
                            'is_migrated' => false,
                        ],
                        '1p3_3' => [
                            'is_enrolled' => false, // Learner - not enrolled due to 'prompt' provisioning mode.
                            'is_migrated' => false,
                        ],
                        '1p3_4' => [
                            'is_enrolled' => false, // Both roles - not enrolled due to instructor's 'prompt' provisioning mode.
                            'is_migrated' => false,
                        ]
                    ]
                ]
            ],
            'All automatic provisioning, with legacy data and migration claim, mixed bag of users and roles' => [
                'legacy_data' => [
                    'users' => [
                        ['user_id' => '2'],
                        ['user_id' => '3'],
                        ['user_id' => '4'],
                        ['user_id' => '5']
                    ],
                    'consumer_key' => 'CONSUMER_1',
                    'tools' => [
                        ['secret' => 'toolsecret1'],
                        ['secret' => 'toolsecret2'],
                    ]
                ],
                'resource_config' => [
                    'provisioningmodelearner' => \auth_plugin_lti::PROVISIONING_MODE_AUTO_ONLY,
                    'provisioningmodeinstructor' => \auth_plugin_lti::PROVISIONING_MODE_AUTO_ONLY
                ],
                'launch_data' => [
                    'user' => self::get_mock_launch_users_with_ids(['1p3_1'])[0],
                    'launch_migration_claim' => [
                        'consumer_key' => 'CONSUMER_1',
                        'signing_secret' => 'toolsecret1',
                        'context_id' => 'd345b',
                        'tool_consumer_instance_guid' => '12345-123',
                        'resource_link_id' => '4b6fa'
                    ],
                ],
                'sync_members_data' => [
                    // This user is just an instructor but is also the user who is already linked, via the launch above.
                    self::get_mock_members_with_ids(['1p3_1'], null, true, true, true, false, [
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Instructor',
                    ])[0],
                    // This user is just a learner.
                    self::get_mock_members_with_ids(['1p3_2'], ['2'], true, true, true, false, [
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner'
                    ])[0],
                    // This user is also a learner.
                    self::get_mock_members_with_ids(['1p3_3'], ['3'], true, true, true, false, [
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner'
                    ])[0],
                    // This user is both an instructor and a learner.
                    self::get_mock_members_with_ids(['1p3_4'], ['4'], true, true, true, false, [
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Instructor',
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner'
                    ])[0],
                    // This user is just an instructor who hasn't launched before (unlike the first user here).
                    self::get_mock_members_with_ids(['1p3_5'], ['5'], true, true, true, false, [
                        'http://purl.imsglobal.org/vocab/lis/v2/membership#Instructor',
                    ])[0],
                ],
                'expected' => [
                    'enrolments' => [
                        '1p3_1' => [
                            'is_enrolled' => true, // Instructor - enrolled because they are also the launch user (already linked).
                            'is_migrated' => false,
                        ],
                        '1p3_2' => [
                            'is_enrolled' => true, // Learner - enrolled due to 'auto' provisioning mode.
                            'is_migrated' => true,
                        ],
                        '1p3_3' => [
                            'is_enrolled' => true, // Learner - enrolled due to 'auto' provisioning mode.
                            'is_migrated' => true,
                        ],
                        '1p3_4' => [
                            'is_enrolled' => true, // Both roles - enrolled due to instructor's 'auto' provisioning mode.
                            'is_migrated' => true
                        ],
                        '1p3_5' => [
                            'is_enrolled' => true, // Instructor role only - enrolled due to instructor's 'auto' provisioning mode.
                            'is_migrated' => true
                        ]
                    ]
                ]
            ],
        ];
    }
}
