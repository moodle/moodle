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

namespace core_communication;

use core_communication\task\add_members_to_room_task;
use core_communication\task\create_and_configure_room_task;
use communication_matrix\matrix_test_helper_trait;
use core_communication\task\synchronise_provider_task;
use core_communication\task\synchronise_providers_task;
use core_communication\task\remove_members_from_room;
use core_communication\task\update_room_task;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../provider/matrix/tests/matrix_test_helper_trait.php');
require_once(__DIR__ . '/communication_test_helper_trait.php');

/**
 * Class api_test to test the communication public api and its associated methods.
 *
 * @package    core_communication
 * @category   test
 * @copyright  2023 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \core_communication\api
 */
final class api_test extends \advanced_testcase {
    use matrix_test_helper_trait;
    use communication_test_helper_trait;

    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        $this->setup_communication_configs();
        $this->initialise_mock_server();
    }

    /**
     * Test set data to the instance.
     */
    public function test_set_data(): void {
        $course = $this->get_course();

        $communication = \core_communication\api::load_by_instance(
            context: \core\context\course::instance($course->id),
            component: 'core_course',
            instancetype: 'coursecommunication',
            instanceid: $course->id,
        );

        // Sample data.
        $roomname = 'Sampleroom';
        $provider = 'communication_matrix';

        // Set the data.
        $communication->set_data($course);

        $roomnameidentifier = $communication->get_provider() . 'roomname';

        // Test the set data.
        $this->assertEquals($roomname, $course->$roomnameidentifier);
        $this->assertEquals($provider, $course->selectedcommunication);
    }

    /**
     * Test get_current_communication_provider method.
     */
    public function test_get_provider(): void {
        $course = $this->get_course();

        $communication = \core_communication\api::load_by_instance(
            context: \core\context\course::instance($course->id),
            component: 'core_course',
            instancetype: 'coursecommunication',
            instanceid: $course->id,
        );

        $this->assertEquals('communication_matrix', $communication->get_provider());
    }

    /**
     * Test set_avatar method.
     */
    public function test_set_avatar(): void {
        global $CFG;
        $this->setAdminUser();
        $course = $this->get_course('Sampleroom', 'none');

        // Sample data.
        $communicationroomname = 'Sampleroom';
        $selectedcommunication = 'communication_matrix';

        $avatar = $this->create_communication_file(
            'moodle_logo.jpg',
            'moodle_logo.jpg',
        );

        // Create the room, settingthe avatar.
        $communication = \core_communication\api::load_by_instance(
            context: \core\context\course::instance($course->id),
            component: 'core_course',
            instancetype: 'coursecommunication',
            instanceid: $course->id,
            provider: $selectedcommunication,
        );

        $communication->create_and_configure_room($communicationroomname, $avatar);

        // Reload the communication processor.
        $communicationprocessor = processor::load_by_instance(
            context: \core\context\course::instance($course->id),
            component: 'core_course',
            instancetype: 'coursecommunication',
            instanceid: $course->id,
        );

        // Compare result.
        $this->assertEquals(
            $avatar->get_contenthash(),
            $communicationprocessor->get_avatar()->get_contenthash(),
        );
    }

    /**
     * Test the create_and_configure_room method to add/create tasks.
     */
    public function test_create_and_configure_room(): void {
        // Get the course by disabling communication so that we can create it manually calling the api.
        $course = $this->get_course('Sampleroom', 'none');

        // Sample data.
        $communicationroomname = 'Sampleroom';
        $selectedcommunication = 'communication_matrix';

        $communication = \core_communication\api::load_by_instance(
            context: \core\context\course::instance($course->id),
            component: 'core_course',
            instancetype: 'coursecommunication',
            instanceid: $course->id,
            provider: $selectedcommunication,
        );
        $communication->create_and_configure_room($communicationroomname);

        // Test the tasks added.
        $adhoctask = \core\task\manager::get_adhoc_tasks('\\core_communication\\task\\create_and_configure_room_task');
        $this->assertCount(1, $adhoctask);

        $adhoctask = reset($adhoctask);
        $this->assertInstanceOf('\\core_communication\\task\\create_and_configure_room_task', $adhoctask);

        // Test the communication record exists.
        $communicationprocessor = processor::load_by_instance(
            context: \core\context\course::instance($course->id),
            component: 'core_course',
            instancetype: 'coursecommunication',
            instanceid: $course->id,
        );

        $this->assertEquals($communicationroomname, $communicationprocessor->get_room_name());
        $this->assertEquals($selectedcommunication, $communicationprocessor->get_provider());
    }

    /**
     * Test the create_and_configure_room method to add/create tasks when no communication provider selected.
     */
    public function test_create_and_configure_room_without_communication_provider_selected(): void {
        // Get the course by disabling communication so that we can create it manually calling the api.
        $course = $this->get_course('Sampleroom', 'none');

        // Test the tasks added.
        $adhoctask = \core\task\manager::get_adhoc_tasks('\\core_communication\\task\\create_and_configure_room_task');
        $this->assertCount(0, $adhoctask);

        // Test the communication record exists.
        $communicationprocessor = processor::load_by_instance(
            context: \core\context\course::instance($course->id),
            component: 'core_course',
            instancetype: 'coursecommunication',
            instanceid: $course->id,
        );

        $this->assertNull($communicationprocessor);
    }

    /**
     * Test update operation.
     */
    public function test_update_room(): void {
        $course = $this->get_course();

        // Sample data.
        $communicationroomname = 'Sampleroomupdated';
        $selectedcommunication = 'communication_matrix';

        $communication = \core_communication\api::load_by_instance(
            context: \core\context\course::instance($course->id),
            component: 'core_course',
            instancetype: 'coursecommunication',
            instanceid: $course->id,
        );
        $communication->update_room(processor::PROVIDER_ACTIVE, $communicationroomname);

        // Test the communication record exists.
        $communicationprocessor = processor::load_by_instance(
            context: \core\context\course::instance($course->id),
            component: 'core_course',
            instancetype: 'coursecommunication',
            instanceid: $course->id,
        );

        $this->assertEquals($communicationroomname, $communicationprocessor->get_room_name());
        $this->assertEquals($selectedcommunication, $communicationprocessor->get_provider());
        $this->assertTrue($communicationprocessor->is_instance_active());

        $communication->update_room(processor::PROVIDER_INACTIVE, $communicationroomname);

        // Test updating active state.
        $communicationprocessor = processor::load_by_instance(
            context: \core\context\course::instance($course->id),
            component: 'core_course',
            instancetype: 'coursecommunication',
            instanceid: $course->id,
            provider: $selectedcommunication,
        );

        $this->assertEquals($communicationroomname, $communicationprocessor->get_room_name());
        $this->assertEquals($selectedcommunication, $communicationprocessor->get_provider());
        $this->assertFalse($communicationprocessor->is_instance_active());
    }

    /**
     * Test delete operation.
     */
    public function test_delete_room(): void {
        $course = $this->get_course();

        // Sample data.
        $communicationroomname = 'Sampleroom';
        $selectedcommunication = 'communication_matrix';

        // Test the communication record exists.
        $communicationprocessor = processor::load_by_instance(
            context: \core\context\course::instance($course->id),
            component: 'core_course',
            instancetype: 'coursecommunication',
            instanceid: $course->id,
        );

        $this->assertEquals($communicationroomname, $communicationprocessor->get_room_name());
        $this->assertEquals($selectedcommunication, $communicationprocessor->get_provider());

        $communication = \core_communication\api::load_by_instance(
            context: \core\context\course::instance($course->id),
            component: 'core_course',
            instancetype: 'coursecommunication',
            instanceid: $course->id,
        );
        $communication->delete_room();

        // Test the tasks added.
        $adhoctask = \core\task\manager::get_adhoc_tasks('\\core_communication\\task\\delete_room_task');
        // Should be 2 as one for create, another for update.
        $this->assertCount(1, $adhoctask);

        $adhoctask = reset($adhoctask);
        $this->assertInstanceOf('\\core_communication\\task\\delete_room_task', $adhoctask);
    }

    /**
     * Test the adding and removing of members from room.
     */
    public function test_adding_and_removing_of_room_membership(): void {
        $course = $this->get_course();
        $userids = [];
        for ($i = 0; $i < 40; $i++) {
            $userids[] = $this->get_user('Samplefn' . $i, 'Sampleln' . $i, 'sampleun' . $i)->id;
        }
        // First test the adding members to a room.
        $communication = \core_communication\api::load_by_instance(
            context: \core\context\course::instance($course->id),
            component: 'core_course',
            instancetype: 'coursecommunication',
            instanceid: $course->id,
        );
        $communication->add_members_to_room($userids);

        // Test the tasks added.
        $adhoctask = \core\task\manager::get_adhoc_tasks('\\core_communication\\task\\add_members_to_room_task');
        $this->assertCount(2, $adhoctask);

        // Now test the removing members from a room.
        $communication->remove_members_from_room($userids);

        // Test the tasks added.
        $adhoctask = \core\task\manager::get_adhoc_tasks('\\core_communication\\task\\remove_members_from_room');
        $this->assertCount(2, $adhoctask);
    }

    /**
     * Test the update of room membership with the change user role.
     */
    public function test_update_room_membership_on_user_role_change(): void {
        global $DB;

        // Generate the data.
        $user = $this->getDataGenerator()->create_user();
        $course = $this->get_course();
        $coursecontext = \context_course::instance($course->id);
        $teacherrole = $DB->get_record('role', ['shortname' => 'teacher']);
        $this->getDataGenerator()->enrol_user($user->id, $course->id);

        $adhoctask = \core\task\manager::get_adhoc_tasks('\\core_communication\\task\\add_members_to_room_task');
        $this->assertCount(1, $adhoctask);

        $adhoctask = reset($adhoctask);
        $this->assertInstanceOf('\\core_communication\\task\\add_members_to_room_task', $adhoctask);

        // Test the tasks added as the role is a teacher.
        $adhoctask = \core\task\manager::get_adhoc_tasks('\\core_communication\\task\\update_room_membership_task');
        $this->assertCount(1, $adhoctask);

        $adhoctask = reset($adhoctask);
        $this->assertInstanceOf('\\core_communication\\task\\update_room_membership_task', $adhoctask);
    }

    /**
     * Test sync_provider method for the sync of available provider.
     */
    public function test_sync_provider(): void {
        // Generate the data.
        $user = $this->getDataGenerator()->create_user();
        $course1 = $this->get_course();
        $this->getDataGenerator()->enrol_user($user->id, $course1->id);
        $course2 = $this->get_course();
        $this->getDataGenerator()->enrol_user($user->id, $course2->id);
        // Now run the task to add sync providers.
        $this->execute_task(synchronise_providers_task::class);
        $adhoctask = \core\task\manager::get_adhoc_tasks(synchronise_provider_task::class);
        $this->assertCount(2, $adhoctask);
    }

    /**
     * Test the removal of all members from the room.
     */
    public function test_remove_all_members_from_room(): void {
        $course = $this->get_course();
        $userid = $this->get_user()->id;
        $communication = \core_communication\api::load_by_instance(
            context: \core\context\course::instance($course->id),
            component: 'core_course',
            instancetype: 'coursecommunication',
            instanceid: $course->id,
        );
        $communication->add_members_to_room([$userid]);

        // Now test the removing members from a room.
        $communication->remove_all_members_from_room();

        // Test the remove members tasks added.
        $adhoctask = \core\task\manager::get_adhoc_tasks(remove_members_from_room::class);
        $this->assertCount(1, $adhoctask);
    }

    /**
     * Test the configuration of room changes as well as the membership with the change of provider.
     */
    public function test_configure_room_and_membership_by_provider(): void {
        global $DB;

        $course = $this->get_course('Sampleroom', 'none');
        $userid = $this->get_user()->id;
        $provider = 'communication_matrix';

        $communication = \core_communication\api::load_by_instance(
            context: \core\context\course::instance($course->id),
            component: 'core_course',
            instancetype: 'coursecommunication',
            instanceid: $course->id,
        );

        $communication->configure_room_and_membership_by_provider(
            provider: $provider,
            instance: $course,
            communicationroomname: $course->fullname,
            users: [$userid],
        );
        $communication->reload();

        // Test that the task to create a room is added.
        $adhoctask = \core\task\manager::get_adhoc_tasks(create_and_configure_room_task::class);
        $this->assertCount(1, $adhoctask);

        // Test that no update tasks are added.
        $adhoctask = \core\task\manager::get_adhoc_tasks(update_room_task::class);
        $this->assertCount(0, $adhoctask);

        // Test that the task to add members to room is not added, as we are adding the user mapping not the task.
        $adhoctask = \core\task\manager::get_adhoc_tasks(add_members_to_room_task::class);
        $this->assertCount(0, $adhoctask);

        // Now delete all the ad-hoc tasks.
        $DB->delete_records('task_adhoc');

        // Now disable the provider by setting none.
        $communication->configure_room_and_membership_by_provider(
            provider: processor::PROVIDER_NONE,
            instance: $course,
            communicationroomname: $course->fullname,
            users: [$userid],
        );
        $communication->reload();

        // Test that the task to delete a room is added.
        $adhoctask = \core\task\manager::get_adhoc_tasks(update_room_task::class);
        $this->assertCount(1, $adhoctask);

        // Test that the task to remove members from room is added.
        $adhoctask = \core\task\manager::get_adhoc_tasks(remove_members_from_room::class);
        $this->assertCount(1, $adhoctask);

        // Now delete all the ad-hoc tasks.
        $DB->delete_records('task_adhoc');

        // Now try to set the same none provider again.
        $communication->configure_room_and_membership_by_provider(
            provider: processor::PROVIDER_NONE,
            instance: $course,
            communicationroomname: $course->fullname,
            users: [$userid],
        );

        // Test that no communication task is added.
        $adhoctask = \core\task\manager::get_adhoc_tasks(create_and_configure_room_task::class);
        $this->assertCount(0, $adhoctask);

        $adhoctask = \core\task\manager::get_adhoc_tasks(update_room_task::class);
        $this->assertCount(0, $adhoctask);

        $adhoctask = \core\task\manager::get_adhoc_tasks(add_members_to_room_task::class);
        $this->assertCount(0, $adhoctask);

        $adhoctask = \core\task\manager::get_adhoc_tasks(remove_members_from_room::class);
        $this->assertCount(0, $adhoctask);

        // Now let's change it back to matrix and test the update task is added.
        $communication->configure_room_and_membership_by_provider(
            provider: $provider,
            instance: $course,
            communicationroomname: $course->fullname,
            users: [$userid],
        );
        $communication->reload();

        // Test create task is not added because communication has been created in the past.
        $adhoctask = \core\task\manager::get_adhoc_tasks(create_and_configure_room_task::class);
        $this->assertCount(0, $adhoctask);

        // Test an update task added.
        $adhoctask = \core\task\manager::get_adhoc_tasks(update_room_task::class);
        $this->assertCount(1, $adhoctask);

        // Test add membership task is added.
        $adhoctask = \core\task\manager::get_adhoc_tasks(add_members_to_room_task::class);
        $this->assertCount(1, $adhoctask);

        // Now delete all the ad-hoc tasks.
        $DB->delete_records('task_adhoc');

        $course->customlinkurl = $course->customlinkurl ?? 'https://moodle.org';

        // Now change the provider to another one.
        $communication->configure_room_and_membership_by_provider(
            provider: 'communication_customlink',
            instance: $course,
            communicationroomname: $course->fullname,
            users: [$userid],
        );
        $communication->reload();

        // Remove membership and update room task for the previous provider.
        // Create room task for new one.
        $adhoctask = \core\task\manager::get_adhoc_tasks(update_room_task::class);
        $this->assertCount(1, $adhoctask);

        $adhoctask = \core\task\manager::get_adhoc_tasks(remove_members_from_room::class);
        $this->assertCount(1, $adhoctask);

        $adhoctask = \core\task\manager::get_adhoc_tasks(create_and_configure_room_task::class);
        $this->assertCount(1, $adhoctask);

        // Now delete all the ad-hoc tasks.
        $DB->delete_records('task_adhoc');

        // Now disable the provider.
        $communication->configure_room_and_membership_by_provider(
            provider: processor::PROVIDER_NONE,
            instance: $course,
            communicationroomname: $course->fullname,
            users: [$userid],
        );
        $communication->reload();

        // Should have one update and one remove task.
        $adhoctask = \core\task\manager::get_adhoc_tasks(update_room_task::class);
        $this->assertCount(1, $adhoctask);

        // This provider doesn't have any membership, so no remove task.
        $adhoctask = \core\task\manager::get_adhoc_tasks(remove_members_from_room::class);
        $this->assertCount(0, $adhoctask);

        // Now delete all the ad-hoc tasks.
        $DB->delete_records('task_adhoc');

        // Now enable the same provider again.
        $communication->configure_room_and_membership_by_provider(
            provider: $provider,
            instance: $course,
            communicationroomname: $course->fullname,
            users: [$userid],
        );

        // Now it should have one update and one add task.
        $adhoctask = \core\task\manager::get_adhoc_tasks(update_room_task::class);
        $this->assertCount(1, $adhoctask);

        $adhoctask = \core\task\manager::get_adhoc_tasks(add_members_to_room_task::class);
        $this->assertCount(1, $adhoctask);
    }
}
