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

namespace communication_matrix;

use core\context;
use core_communication\api;
use core_communication\communication_test_helper_trait;
use core_communication\processor;
use stored_file;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/matrix_test_helper_trait.php');
require_once(__DIR__ . '/../../../tests/communication_test_helper_trait.php');

/**
 * Class communication_feature_test to test the matrix features implemented using the core interfaces.
 *
 * @package    communication_matrix
 * @category   test
 * @copyright  2023 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \communication_matrix\communication_feature
 * @coversDefaultClass \communication_matrix\communication_feature
 */
final class communication_feature_test extends \advanced_testcase {
    use matrix_test_helper_trait;
    use communication_test_helper_trait;

    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        $this->setup_communication_configs();
        $this->initialise_mock_server();
    }

    /**
     * Test create or update chat room.
     *
     * @covers ::create_chat_room
     */
    public function test_create_chat_room(): void {
        // Set up the test data first.
        $communication = \core_communication\api::load_by_instance(
            context: \core\context\system::instance(),
            component: 'communication_matrix',
            instancetype: 'example',
            instanceid: 1,
            provider: 'communication_matrix',
        );

        $communication->create_and_configure_room(
            communicationroomname: 'Room name',
            instance: (object) [
                'matrixroomtopic' => 'A fun topic',
            ],
        );

        // phpcs:ignore moodle.Commenting.InlineComment.DocBlock
        /** @var communication_feature */
        $provider = $communication->get_room_provider();
        $this->assertInstanceOf(
            communication_feature::class,
            $provider,
        );

        // Run the create_chat_room task.
        $result = $provider->create_chat_room();
        $this->assertTrue($result);

        // Ensure that a room_id was set.
        $this->assertNotEmpty($provider->get_room_id());

        // Fetch the back office room data.
        $remoteroom = $this->backoffice_get_room();

        // The roomid set in the database must match the one set on the remote server.
        $this->assertEquals(
            $remoteroom->room_id,
            $provider->get_room_id(),
        );

        // The name is a feature of the communication API itself.
        $this->assertEquals(
            'Room name',
            $communication->get_room_name(),
        );
        $this->assertEquals(
            $communication->get_room_name(),
            $remoteroom->name,
        );

        // The topic is a Matrix feature.
        $roomconfig = $provider->get_room_configuration();
        $this->assertEquals(
            'A fun topic',
            $roomconfig->get_topic(),
        );
        $this->assertEquals(
            $remoteroom->topic,
            $roomconfig->get_topic(),
        );

        // The avatar features are checked in a separate test.
    }

    /**
     * Test update of a chat room.
     *
     * @covers ::update_chat_room
     */
    public function test_update_chat_room(): void {
        $communication = $this->create_room(
            roomname: 'Our room name',
            roomtopic: 'Our room topic',
        );

        // phpcs:ignore moodle.Commenting.InlineComment.DocBlock
        /** @var communication_feature */
        $provider = $communication->get_room_provider();
        $this->assertInstanceOf(
            communication_feature::class,
            $provider,
        );

        // Update the room name.
        // Note: We have to update the record via the API, and then call the provider update method.
        // That's because the update is performed asynchronously.
        $communication->update_room(
            communicationroomname: 'Our updated room name',
        );
        $provider->reload();

        // Now call the provider's update method.
        $provider->update_chat_room();

        // And assert that it was updated remotely.
        $remoteroom = $this->backoffice_get_room();

        $this->assertEquals(
            'Our updated room name',
            $communication->get_room_name(),
        );
        $this->assertEquals(
            $communication->get_room_name(),
            $remoteroom->name,
        );
        // The remote topic should not have changed.
        $this->assertEquals(
            'Our room topic',
            $remoteroom->topic,
        );

        // Now update just the topic.
        // First in the local API.
        $communication->update_room(
            instance: (object) [
                'matrixroomtopic' => 'Our updated room topic',
            ],
        );

        $provider->reload();

        // Then call the provider's update method to actually perform the change.
        $provider->update_chat_room();

        // And assert that it was updated remotely.
        $remoteroom = $this->backoffice_get_room();

        $this->assertEquals(
            'Our updated room topic',
            $provider->get_room_configuration()->get_topic(),
        );

        // The remote topic should have been updated.
        $this->assertEquals(
            'Our updated room topic',
            $remoteroom->topic,
        );

        // The name should not have changed.
        $this->assertEquals(
            'Our updated room name',
            $communication->get_room_name(),
        );
    }

    /**
     * Test delete chat room.
     *
     * @covers ::delete_chat_room
     */
    public function test_delete_chat_room(): void {
        $communication = $this->create_room();

        $processor = $communication->get_processor();
        $provider = $communication->get_room_provider();
        $room = matrix_room::load_by_processor_id($processor->get_id());

        // Run the delete method.
        $this->assertTrue($provider->delete_chat_room());

        // The record of the room should have been removed.
        $this->assertNull(matrix_room::load_by_processor_id($processor->get_id()));

        // But the room itself shoudl exist.
        $matrixroomdata = $this->get_matrix_room_data($room->get_room_id());

        $this->assertNotEmpty($matrixroomdata);
        $this->assertEquals($processor->get_room_name(), $matrixroomdata->name);
        $this->assertEquals($room->get_topic(), $matrixroomdata->topic);
    }

    /**
     * Test update room avatar.
     *
     * @covers ::update_room_avatar
     * @dataProvider avatar_provider
     */
    public function test_update_room_avatar(
        ?string $before,
        ?string $after,
    ): void {
        $this->setAdminUser();

        // Create a new draft file.
        $logo = $this->create_communication_file('moodle_logo.jpg', 'logo.jpg');
        $circle = $this->create_communication_file('circle.png', 'circle.png');

        if ($before === 'logo') {
            $before = $logo;
        } else if ($before === 'circle') {
            $before = $circle;
        }

        if ($after === 'logo') {
            $after = $logo;
        } else if ($after === 'circle') {
            $after = $circle;
        }

        $communication = $this->create_matrix_room(
            component: 'communication_matrix',
            itemtype: 'example_room',
            itemid: 1,
            roomname: 'Example room name',
            roomavatar: $before,
        );

        // Confirm that the avatar was set remotely.
        $remoteroom = $this->backoffice_get_room();

        if ($before) {
            $this->assertStringEndsWith($before->get_filename(), $remoteroom->avatar);
            $avatarcontent = download_file_content($remoteroom->avatar);
            $this->assertEquals($before->get_content(), $avatarcontent);
        } else {
            $this->assertEmpty($remoteroom->avatar);
        }

        // Reload the API instance as the information stored has changed.
        $communication->reload();

        // Update the avatar with the 'after' avatar.
        $communication->update_room(
            avatar: $after,
        );
        $this->run_all_adhoc_tasks();

        // Confirm that the avatar was updated remotely.
        $remoteroom = $this->backoffice_get_room();

        if ($after) {
            $this->assertStringEndsWith($after->get_filename(), $remoteroom->avatar);
            $avatarcontent = download_file_content($remoteroom->avatar);
            $this->assertEquals($after->get_content(), $avatarcontent);
        } else {
            $this->assertEmpty($remoteroom->avatar);
        }
    }

    /**
     * Tests for setting and updating the room avatar.
     *
     * @return array
     */
    public static function avatar_provider(): array {
        return [
            'Empty to avatar' => [
                null,
                'circle',
            ],
            'Avatar to empty' => [
                'circle',
                null,
            ],
            'Avatar to new avatar' => [
                'circle',
                'logo',
            ],
        ];
    }

    /**
     * Test get chat room url.
     *
     * @covers ::get_chat_room_url
     */
    public function test_get_chat_room_url(): void {
        $communication = $this->create_room();

        $provider = $communication->get_room_provider();

        $url = $provider->get_chat_room_url();
        $this->assertNotNull($url);

        // Fetch the room information from the server.
        $remoteroom = $this->backoffice_get_room();

        $this->assertStringEndsWith(
            $remoteroom->room_id,
            $url,
        );
    }

    /**
     * Test create members.
     *
     * @covers ::create_members
     * @covers ::add_registered_matrix_user_to_room
     */
    public function test_create_members(): void {
        $user = $this->getDataGenerator()->create_user();

        $communication = $this->create_room(
            members: [
                $user->id,
            ],
        );

        $remoteroom = $this->backoffice_get_room();
        $this->assertCount(1, $remoteroom->members);
        $member = reset($remoteroom->members);
        $this->assertStringStartsWith("@{$user->username}", $member->userid);
    }

    /**
     * Test add/remove members from room.
     *
     * @covers ::remove_members_from_room
     * @covers ::add_members_to_room
     * @covers ::add_registered_matrix_user_to_room
     * @covers ::check_room_membership
     * @covers ::set_matrix_power_levels
     */
    public function test_add_and_remove_members_from_room(): void {
        $user = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $communication = $this->create_room();
        $provider = $communication->get_room_user_provider();

        $remoteroom = $this->backoffice_get_room();
        $this->assertCount(0, $remoteroom->members);

        // Add the members to the room.
        $provider->add_members_to_room([$user->id, $user2->id]);

        // Ensure that they have been created.
        $remoteroom = $this->backoffice_get_room();
        $this->assertCount(2, $remoteroom->members);

        $userids = array_map(fn($member) => $member->userid, $remoteroom->members);
        $userids = array_map(fn($userid) => substr($userid, 0, strpos($userid, ':')), $userids);
        $this->assertContains("@{$user->username}", $userids);
        $this->assertContains("@{$user2->username}", $userids);

        // Remove member from matrix room.
        $provider->remove_members_from_room([$user->id]);

        // Ensure that they have been removed.
        $remoteroom = $this->backoffice_get_room();
        $members = (array) $remoteroom->members;
        $this->assertCount(1, $members);
        $userids = array_map(fn ($member) => $member->userid, $members);
        $userids = array_map(fn ($userid) => substr($userid, 0, strpos($userid, ':')), $userids);
        $this->assertNotContains("@{$user->username}", $userids);
        $this->assertContains("@{$user2->username}", $userids);
    }

    /**
     * Test update of room membership.
     *
     * @covers ::update_room_membership
     * @covers ::set_matrix_power_levels
     * @covers ::is_power_levels_update_required
     * @covers ::get_user_allowed_power_level
     */
    public function test_update_room_membership(): void {
        $this->resetAfterTest();

        global $DB;

        // Create a new room.
        $course = $this->get_course('Sampleroom', 'none');
        $coursecontext = \context_course::instance($course->id);
        $user = $this->get_user();

        $communication = $this->create_room(
            component: 'core_course',
            itemtype: 'coursecommunication',
            itemid: $course->id,
            roomname: 'sampleroom',
            roomtopic: 'sampltopic',
            roomavatar: null,
            members: [$user->id],
            context: $coursecontext,
        );

        $provider = $communication->get_room_user_provider();

        // Add the members to the room.
        $provider->add_members_to_room([$user->id]);

        // Assign teacher role to the user.
        $teacherrole = $DB->get_record('role', ['shortname' => 'teacher']);
        $this->getDataGenerator()->enrol_user($user->id, $course->id);
        role_assign($teacherrole->id, $user->id, $coursecontext->id);

        // Test the tasks added as the role is a teacher.
        $provider->update_room_membership([$user->id]);

        $processor = \core_communication\processor::load_by_instance(
            context: $coursecontext,
            component: 'core_course',
            instancetype: 'coursecommunication',
            instanceid: $course->id,
        );
        $synceduser = $processor->get_instance_userids(
            synced: true,
        );
        $synceduser = reset($synceduser);

        // Test if the communication user record is synced.
        $this->assertEquals($user->id, $synceduser);
    }

    /**
     * Test the user power level allocation according to context.
     *
     * @covers ::get_user_allowed_power_level
     */
    public function test_get_user_allowed_power_level(): void {
        $this->resetAfterTest();
        global $DB;

        // Create users.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $course = $this->get_course();
        $coursecontext = \context_course::instance($course->id);
        $teacherrole = $DB->get_record('role', ['shortname' => 'editingteacher']);
        $studentrole = $DB->get_record('role', ['shortname' => 'student']);
        $this->getDataGenerator()->enrol_user($user1->id, $course->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course->id);
        // Assign roles.
        role_assign($teacherrole->id, $user1->id, $coursecontext->id);
        role_assign($studentrole->id, $user2->id, $coursecontext->id);

        $communicationprocessor = processor::load_by_instance(
            context: \core\context\course::instance($course->id),
            component: 'core_course',
            instancetype: 'coursecommunication',
            instanceid: $course->id
        );

        // Test if the power level is set according to the context.
        $this->assertEquals(
            matrix_constants::POWER_LEVEL_MOODLE_MODERATOR,
            $communicationprocessor->get_room_provider()->get_user_allowed_power_level($user1->id)
        );
        $this->assertEquals(
            matrix_constants::POWER_LEVEL_DEFAULT,
            $communicationprocessor->get_room_provider()->get_user_allowed_power_level($user2->id)
        );
    }

    /**
     * Helper to create a room.
     *
     * @param null|string $component
     * @param null|string $itemtype
     * @param null|int $itemid
     * @param null|string $roomname
     * @param null|string $roomtopic
     * @param null|stored_file $roomavatar
     * @param array $members
     * @return api
     */
    protected function create_room(
        ?string $component = 'communication_matrix',
        ?string $itemtype = 'example',
        ?int $itemid = 1,
        ?string $roomname = null,
        ?string $roomtopic = null,
        ?\stored_file $roomavatar = null,
        array $members = [],
        ?context $context = null,
    ): \core_communication\api {
        // Create a new room.
        $communication = \core_communication\api::load_by_instance(
            context: $context ?? \core\context\system::instance(),
            component: $component,
            instancetype: $itemtype,
            instanceid: $itemid,
            provider: 'communication_matrix',
        );

        $communication->create_and_configure_room(
            communicationroomname: $roomname ?? 'Room name',
            avatar: $roomavatar,
            instance: (object) [
                'matrixroomtopic' => $roomtopic ?? 'A fun topic',
            ],
        );

        $communication->add_members_to_room($members);

        // Run the adhoc task.
        $this->run_all_adhoc_tasks();

        $communication->reload();
        return $communication;
    }

    /**
     * Test if the selected provider is configured.
     *
     * @covers ::is_configured
     */
    public function test_is_configured(): void {
        $course = $this->get_course();
        $communicationprocessor = processor::load_by_instance(
            context: \core\context\course::instance($course->id),
            component: 'core_course',
            instancetype: 'coursecommunication',
            instanceid: $course->id
        );
        $this->assertTrue($communicationprocessor->get_room_provider()->is_configured());

        // Unset communication_matrix settings.
        unset_config('matrixhomeserverurl', 'communication_matrix');
        unset_config('matrixaccesstoken', 'communication_matrix');
        $this->assertFalse($communicationprocessor->get_room_provider()->is_configured());
    }
}
