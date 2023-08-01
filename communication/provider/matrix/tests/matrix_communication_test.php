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

use core_communication\processor;
use core_communication\communication_test_helper_trait;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/matrix_test_helper_trait.php');
require_once(__DIR__ . '/../../../tests/communication_test_helper_trait.php');

/**
 * Class matrix_provider_test to test the matrix provider scenarios using the matrix endpoints.
 *
 * @package    communication_matrix
 * @category   test
 * @copyright  2023 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class matrix_communication_test extends \advanced_testcase {

    use matrix_test_helper_trait;
    use communication_test_helper_trait;

    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        $this->setup_communication_configs();
        $this->initialise_mock_server();
    }

    /**
     * Test creating course with matrix provider creates all the associated data and matrix room.
     *
     * @covers \core_communication\api::create_and_configure_room
     * @covers \core_communication\task\create_and_configure_room_task::execute
     * @covers \core_communication\task\create_and_configure_room_task::queue
     */
    public function test_create_course_with_matrix_provider(): void {
        // Sample data.
        $roomname = 'Samplematrixroom';
        $provider = 'communication_matrix';
        $course = $this->get_course($roomname, $provider);

        // Run the task.
        $this->runAdhocTasks('\core_communication\task\create_and_configure_room_task');

        $communicationprocessor = processor::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id,
        );

        // Initialize the matrix room object.
        $matrixrooms = new matrix_rooms($communicationprocessor->get_id());

        // Test against the data.
        $matrixroomdata = $this->get_matrix_room_data($matrixrooms->get_matrix_room_id());
        $this->assertEquals($matrixrooms->get_matrix_room_id(), $matrixroomdata->room_id);
        $this->assertEquals($roomname, $matrixroomdata->name);
    }

    /**
     * Test update course with matrix provider.
     *
     * @covers \core_communication\api::update_room
     * @covers \core_communication\task\update_room_task::execute
     * @covers \core_communication\task\update_room_task::queue
     */
    public function test_update_course_with_matrix_provider(): void {
        global $CFG;
        $course = $this->get_course();

        // Run the task.
        $this->runAdhocTasks('\core_communication\task\create_and_configure_room_task');

        // Sample data.
        $communicationroomname = 'Sampleroomupdated';
        $selectedcommunication = 'communication_matrix';
        $logo = $this->create_communication_file('moodle_logo.jpg', 'logo.jpg');

        $communication = \core_communication\api::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id,
        );
        $communication->update_room($selectedcommunication, $communicationroomname, $logo);

        // Pending avatar update should indicate avatar is not in sync.
        $communicationprocessor = processor::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id
        );
        $this->assertFalse($communicationprocessor->is_avatar_synced());

        // Run the task.
        $this->runAdhocTasks('\core_communication\task\update_room_task');

        $communicationprocessor = processor::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id
        );

        // Check that the avatar is now synced with Matrix again.
        $this->assertTrue($communicationprocessor->is_avatar_synced());

        // Initialize the matrix room object.
        $matrixrooms = new matrix_rooms($communicationprocessor->get_id());

        // Test against the data.
        $matrixroomdata = $this->get_matrix_room_data($matrixrooms->get_matrix_room_id());
        $this->assertEquals($matrixrooms->get_matrix_room_id(), $matrixroomdata->room_id);
        $this->assertEquals($communicationroomname, $matrixroomdata->name);
    }

    /**
     * Test course delete with matrix provider.
     *
     * @covers \core_communication\api::delete_room
     * @covers \core_communication\task\delete_room_task::execute
     * @covers \core_communication\task\delete_room_task::queue
     */
    public function test_delete_course_with_matrix_provider(): void {
        global $DB;
        // Sample data.
        $roomname = 'Samplematrixroom';
        $provider = 'communication_matrix';
        $course = $this->get_course($roomname, $provider);

        // Run the task.
        $this->runAdhocTasks('\core_communication\task\create_and_configure_room_task');

        $communicationprocessor = processor::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id
        );
        $communicationid = $communicationprocessor->get_id();

        // Initialize the matrix room object.
        $matrixrooms = new matrix_rooms($communicationprocessor->get_id());

        // Test against the data.
        $matrixroomdata = $this->get_matrix_room_data($matrixrooms->get_matrix_room_id());
        $this->assertEquals($matrixrooms->get_matrix_room_id(), $matrixroomdata->room_id);

        // Now delete the course.
        delete_course($course, false);

        // Run the task.
        $this->runAdhocTasks('\core_communication\task\delete_room_task');

        $communicationprocessor = processor::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id
        );
        $this->assertNull($communicationprocessor);

        // Initialize the matrix room object.
        $matrixrooms = $DB->get_record('matrix_rooms', ['commid' => $communicationid]);
        $this->assertEmpty($matrixrooms);
    }

    /**
     * Test creating course with matrix provider creates all the associated data and matrix room.
     *
     * @covers \core_communication\api::add_members_to_room
     * @covers \core_communication\task\add_members_to_room_task::execute
     * @covers \core_communication\task\add_members_to_room_task::queue
     */
    public function test_create_members_with_matrix_provider(): void {
        $course = $this->get_course('Samplematrixroom', 'communication_matrix');
        $user = $this->get_user('Samplefnmatrix', 'Samplelnmatrix', 'sampleunmatrix');

        // Run room operation task.
        $this->runAdhocTasks('\core_communication\task\create_and_configure_room_task');

        // Enrol the user in the course.
        $enrol = enrol_get_plugin('manual');
        $enrolinstances = enrol_get_instances($course->id, true);
        $enrol->enrol_user(reset($enrolinstances), $user->id);

        // Run user operation task.
        $this->runAdhocTasks('\core_communication\task\add_members_to_room_task');

        $communicationprocessor = processor::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id
        );
        $matrixrooms = new matrix_rooms($communicationprocessor->get_id());
        $eventmanager = new matrix_events_manager($matrixrooms->get_matrix_room_id());

        // Get matrix user id from moodle.
        $matrixuserid = matrix_user_manager::get_matrixid_from_moodle($user->id);
        $this->assertNotNull($matrixuserid);

        // Get matrix user id from matrix.
        $matrixuserdata = $this->get_matrix_user_data($matrixrooms->get_matrix_room_id(), $matrixuserid);
        $this->assertNotEmpty($matrixuserdata);
        $this->assertEquals("Samplefnmatrix Samplelnmatrix", $matrixuserdata->displayname);
    }

    /**
     * Test enrolment adds the user to a Matrix room.
     *
     * @covers \core_communication\api::add_members_to_room
     * @covers \core_communication\task\add_members_to_room_task::execute
     * @covers \core_communication\task\add_members_to_room_task::queue
     */
    public function test_enrolling_user_adds_user_to_matrix_room(): void {
        global $CFG;
        require_once($CFG->dirroot . '/lib/enrollib.php');

        // Sample data.
        $roomname = 'Samplematrixroom';
        $provider = 'communication_matrix';
        $course = $this->get_course($roomname, $provider);
        $user = $this->get_user();

        // Run room tasks.
        $this->runAdhocTasks('\core_communication\task\create_and_configure_room_task');

        // Enrol the user in the course.
        $enrol = enrol_get_plugin('manual');
        $enrolinstances = enrol_get_instances($course->id, true);
        $instance = reset($enrolinstances);
        $enrol->enrol_user($instance, $user->id);

        // Run the user tasks.
        $this->runAdhocTasks('\core_communication\task\add_members_to_room_task');

        $communicationprocessor = processor::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id
        );

        $matrixrooms = new matrix_rooms($communicationprocessor->get_id());
        $eventmanager = new matrix_events_manager($matrixrooms->get_matrix_room_id());
        $matrixhomeserverurl = $eventmanager->matrixhomeserverurl;

        $matrixuserid = matrix_user_manager::get_matrixid_from_moodle($user->id);
        // Check our Matrix user id has room membership.
        $this->assertTrue($communicationprocessor->get_room_provider()->check_room_membership($matrixuserid));
    }

    /**
     * Test enrolment removes the user from a Matrix room.
     *
     * @covers \core_communication\api::remove_members_from_room
     * @covers \core_communication\task\remove_members_from_room::execute
     * @covers \core_communication\task\remove_members_from_room::queue
     */
    public function test_unenrolling_user_removes_user_from_matrix_room(): void {
        global $CFG;
        require_once($CFG->dirroot . '/lib/enrollib.php');

        // Sample data.
        $roomname = 'Samplematrixroom';
        $provider = 'communication_matrix';
        $course = $this->get_course($roomname, $provider);
        $user = $this->get_user();

        // Run room tasks.
        $this->runAdhocTasks('\core_communication\task\create_and_configure_room_task');

        // Enrol the user in the course.
        $enrol = enrol_get_plugin('manual');
        $enrolinstances = enrol_get_instances($course->id, true);
        $instance = reset($enrolinstances);
        $enrol->enrol_user($instance, $user->id);

        // Run the user tasks.
        $this->runAdhocTasks('\core_communication\task\add_members_to_room_task');

        $communicationprocessor = processor::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id
        );

        $matrixrooms = new matrix_rooms($communicationprocessor->get_id());
        $eventmanager = new matrix_events_manager($matrixrooms->get_matrix_room_id());
        $matrixhomeserverurl = $eventmanager->matrixhomeserverurl;

        $matrixuserid = matrix_user_manager::get_matrixid_from_moodle($user->id);
        // Check our Matrix user id has room membership.
        $this->assertTrue($communicationprocessor->get_room_provider()->check_room_membership($matrixuserid));
        // Unenrol the user from the course.
        $enrol->unenrol_user($instance, $user->id);
        // Run the user tasks.
        $this->runAdhocTasks('\core_communication\task\remove_members_from_room');
        // Check our Matrix user id no longer has membership.
        $this->assertFalse($communicationprocessor->get_room_provider()->check_room_membership($matrixuserid));
    }

    /**
     * Test enrolled users in a course lose access to a room when their enrolment is suspended.
     *
     * @covers \core_communication\api::remove_members_from_room
     * @covers \core_communication\task\remove_members_from_room::execute
     * @covers \core_communication\task\remove_members_from_room::queue
     */
    public function test_users_removed_from_room_when_suspending_enrolment(): void {
        global $CFG;
        require_once($CFG->dirroot . '/lib/enrollib.php');

        // Sample data.
        $roomname = 'Samplematrixroom';
        $provider = 'communication_matrix';
        $course = $this->get_course($roomname, $provider);
        $user = $this->get_user();

        // Run room tasks.
        $this->runAdhocTasks('\core_communication\task\create_and_configure_room_task');

        // Enrol the user in the course.
        $enrol = enrol_get_plugin('manual');
        $enrolinstances = enrol_get_instances($course->id, true);
        $instance = reset($enrolinstances);
        $enrol->enrol_user($instance, $user->id);

        // Run the user tasks.
        $this->runAdhocTasks('\core_communication\task\add_members_to_room_task');

        $communicationprocessor = processor::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id
        );
        $matrixrooms = new matrix_rooms($communicationprocessor->get_id());
        $eventmanager = new matrix_events_manager($matrixrooms->get_matrix_room_id());
        $matrixhomeserverurl = $eventmanager->matrixhomeserverurl;

        $matrixuserid = matrix_user_manager::get_matrixid_from_moodle($user->id);
        // Check our Matrix user id has room membership.
        $this->assertTrue($communicationprocessor->get_room_provider()->check_room_membership($matrixuserid));
        // Suspend user enrolment.
        $enrol->update_user_enrol($instance, $user->id, 1);
        // Run the user tasks.
        $this->runAdhocTasks('\core_communication\task\remove_members_from_room');
        // Check our Matrix user id no longer has membership.
        $this->assertFalse($communicationprocessor->get_room_provider()->check_room_membership($matrixuserid));
    }

    /**
     * Test enrolled users in a course lose access to a room when the instance is deleted.
     *
     * @covers \core_communication\api::remove_members_from_room
     * @covers \core_communication\task\remove_members_from_room::execute
     * @covers \core_communication\task\remove_members_from_room::queue
     */
    public function test_users_removed_from_room_when_deleting_instance(): void {
        global $CFG;
        require_once($CFG->dirroot . '/lib/enrollib.php');

        // Sample data.
        $roomname = 'Samplematrixroom';
        $provider = 'communication_matrix';
        $course = $this->get_course($roomname, $provider);
        $user = $this->get_user();

        // Run room tasks.
        $this->runAdhocTasks('\core_communication\task\create_and_configure_room_task');

        // Enrol the user in the course.
        $enrol = enrol_get_plugin('manual');
        $enrolinstances = enrol_get_instances($course->id, true);
        $instance = reset($enrolinstances);
        $enrol->enrol_user($instance, $user->id);

        // Run the user tasks.
        $this->runAdhocTasks('\core_communication\task\add_members_to_room_task');

        $communicationprocessor = processor::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id
        );
        $matrixrooms = new matrix_rooms($communicationprocessor->get_id());
        $eventmanager = new matrix_events_manager($matrixrooms->get_matrix_room_id());
        $matrixhomeserverurl = $eventmanager->matrixhomeserverurl;

        $matrixuserid = matrix_user_manager::get_matrixid_from_moodle($user->id);
        // Check our Matrix user id has room membership.
        $this->assertTrue($communicationprocessor->get_room_provider()->check_room_membership($matrixuserid));
        // Delete instance.
        $enrol->delete_instance($instance);
        // Run the user tasks.
        $this->runAdhocTasks('\core_communication\task\remove_members_from_room');
        // Check our Matrix user id no longer has membership.
        $this->assertFalse($communicationprocessor->get_room_provider()->check_room_membership($matrixuserid));
    }

    /**
     * Test enrolled users in a course lose access to a room when the instance is disabled.
     *
     * @covers \core_communication\api::remove_members_from_room
     * @covers \core_communication\task\remove_members_from_room::execute
     * @covers \core_communication\task\remove_members_from_room::queue
     */
    public function test_users_removed_from_room_when_disabling_instance(): void {
        global $CFG;
        require_once($CFG->dirroot . '/lib/enrollib.php');

        // Sample data.
        $roomname = 'Samplematrixroom';
        $provider = 'communication_matrix';
        $course = $this->get_course($roomname, $provider);
        $user = $this->get_user();

        // Run room tasks.
        $this->runAdhocTasks('\core_communication\task\create_and_configure_room_task');

        // Enrol the user in the course.
        $enrol = enrol_get_plugin('manual');
        $enrolinstances = enrol_get_instances($course->id, true);
        $instance = reset($enrolinstances);
        $enrol->enrol_user($instance, $user->id);

        // Run the user tasks.
        $this->runAdhocTasks('\core_communication\task\add_members_to_room_task');

        $communicationprocessor = processor::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id
        );
        $matrixrooms = new matrix_rooms($communicationprocessor->get_id());
        $eventmanager = new matrix_events_manager($matrixrooms->get_matrix_room_id());
        $matrixhomeserverurl = $eventmanager->matrixhomeserverurl;

        $matrixuserid = matrix_user_manager::get_matrixid_from_moodle($user->id);
        // Check our Matrix user id has room membership.
        $this->assertTrue($communicationprocessor->get_room_provider()->check_room_membership($matrixuserid));
        // Update enrolment communication.
        $enrol->update_communication($instance->id, 'remove', $course->id);
        // Run the user tasks.
        $this->runAdhocTasks('\core_communication\task\remove_members_from_room');
        // Check our Matrix user id no longer has membership.
        $this->assertFalse($communicationprocessor->get_room_provider()->check_room_membership($matrixuserid));
    }

    /**
     * Test enrolled users memerbship toggles correctly when an instance is disabled and reenabled again.
     *
     * @covers \core_communication\api::add_members_to_room
     * @covers \core_communication\task\add_members_to_room_task::execute
     * @covers \core_communication\task\add_members_to_room_task::queue
     * @covers \core_communication\api::remove_members_from_room
     * @covers \core_communication\task\remove_members_from_room::execute
     * @covers \core_communication\task\remove_members_from_room::queue
     */
    public function test_users_memerbship_toggles_when_disabling_and_reenabling_instance(): void {
        global $CFG;
        require_once($CFG->dirroot . '/lib/enrollib.php');

        // Sample data.
        $roomname = 'Samplematrixroom';
        $provider = 'communication_matrix';
        $course = $this->get_course($roomname, $provider);
        $user = $this->get_user();

        // Run room tasks.
        $this->runAdhocTasks('\core_communication\task\create_and_configure_room_task');

        // Enrol the user in the course.
        $enrol = enrol_get_plugin('manual');
        $enrolinstances = enrol_get_instances($course->id, true);
        $instance = reset($enrolinstances);
        $enrol->enrol_user($instance, $user->id);

        // Run the user tasks.
        $this->runAdhocTasks('\core_communication\task\add_members_to_room_task');

        $communicationprocessor = processor::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id
        );
        $matrixrooms = new matrix_rooms($communicationprocessor->get_id());
        $eventmanager = new matrix_events_manager($matrixrooms->get_matrix_room_id());
        $matrixhomeserverurl = $eventmanager->matrixhomeserverurl;

        $matrixuserid = matrix_user_manager::get_matrixid_from_moodle($user->id);
        // Check our Matrix user id has room membership.
        $this->assertTrue($communicationprocessor->get_room_provider()->check_room_membership($matrixuserid));
        // Update enrolment communication when updating instance to disabled.
        $enrol->update_communication($instance->id, 'remove', $course->id);
        // Run the user tasks.
        $this->runAdhocTasks('\core_communication\task\remove_members_from_room');
        // Check our Matrix user id no longer has membership.
        $this->assertFalse($communicationprocessor->get_room_provider()->check_room_membership($matrixuserid));
        // Update enrolment communication when updating instance to enabled.
        $enrol->update_communication($instance->id, 'add', $course->id);
        // Run the user tasks.
        $this->runAdhocTasks('\core_communication\task\add_members_to_room_task');
        // Check our Matrix user id no longer has membership.
        $this->assertTrue($communicationprocessor->get_room_provider()->check_room_membership($matrixuserid));
    }

    /**
     * Test enrolled users in a course lose access to a room when the provider is disabled.
     *
     * @covers \core_communication\api::remove_members_from_room
     * @covers \core_communication\task\remove_members_from_room::execute
     * @covers \core_communication\task\remove_members_from_room::queue
     */
    public function test_users_removed_from_room_when_disabling_provider(): void {
        global $CFG;
        require_once($CFG->dirroot . '/lib/enrollib.php');
        require_once($CFG->dirroot . '/course/lib.php');

        // Sample data.
        $roomname = 'Samplematrixroom';
        $provider = 'communication_matrix';
        $course = $this->get_course($roomname, $provider);
        $user = $this->get_user();

        // Run room tasks.
        $this->runAdhocTasks('\core_communication\task\create_and_configure_room_task');

        // Enrol the user in the course.
        $enrol = enrol_get_plugin('manual');
        $enrolinstances = enrol_get_instances($course->id, true);
        $instance = reset($enrolinstances);
        $enrol->enrol_user($instance, $user->id);

        // Run the user tasks.
        $this->runAdhocTasks('\core_communication\task\add_members_to_room_task');

        $communicationprocessor = processor::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id
        );
        $matrixrooms = new matrix_rooms($communicationprocessor->get_id());
        $eventmanager = new matrix_events_manager($matrixrooms->get_matrix_room_id());
        $matrixhomeserverurl = $eventmanager->matrixhomeserverurl;

        $matrixuserid = matrix_user_manager::get_matrixid_from_moodle($user->id);
        // Check our Matrix user id has room membership.
        $this->assertTrue($communicationprocessor->get_room_provider()->check_room_membership($matrixuserid));
        // Disable communication provider.
        $course->selectedcommunication = 'none';
        update_course($course);
        // Run the user tasks.
        $this->runAdhocTasks('\core_communication\task\remove_members_from_room');
        // Check our Matrix user id no longer has membership.
        $this->assertFalse($communicationprocessor->get_room_provider()->check_room_membership($matrixuserid));
    }

    /**
     * Test enrolled users in a course lose access to a room when their user account is suspended.
     *
     * @covers \core_communication\api::remove_members_from_room
     * @covers \core_communication\task\remove_members_from_room::execute
     * @covers \core_communication\task\remove_members_from_room::queue
     */
    public function test_users_removed_from_room_when_suspending_user(): void {
        global $CFG;
        require_once($CFG->dirroot . '/lib/enrollib.php');

        // Sample data.
        $roomname = 'Samplematrixroom';
        $provider = 'communication_matrix';
        $course = $this->get_course($roomname, $provider);
        $user = $this->get_user();

        // Run room tasks.
        $this->runAdhocTasks('\core_communication\task\create_and_configure_room_task');

        // Enrol the user in the course.
        $enrol = enrol_get_plugin('manual');
        $enrolinstances = enrol_get_instances($course->id, true);
        $instance = reset($enrolinstances);
        $enrol->enrol_user($instance, $user->id);

        // Run the user tasks.
        $this->runAdhocTasks('\core_communication\task\add_members_to_room_task');

        $communicationprocessor = processor::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id
        );
        $matrixrooms = new matrix_rooms($communicationprocessor->get_id());
        $eventmanager = new matrix_events_manager($matrixrooms->get_matrix_room_id());
        $matrixhomeserverurl = $eventmanager->matrixhomeserverurl;

        $matrixuserid = matrix_user_manager::get_matrixid_from_moodle($user->id);
        // Check our Matrix user id has room membership.
        $this->assertTrue($communicationprocessor->get_room_provider()->check_room_membership($matrixuserid));
        // Suspend user.
        $user->suspended = 1;
        user_update_user($user, false, false);
        // Run the user tasks.
        $this->runAdhocTasks('\core_communication\task\remove_members_from_room');
        // Check our Matrix user id no longer has membership.
        $this->assertFalse($communicationprocessor->get_room_provider()->check_room_membership($matrixuserid));
    }

    /**
     * Test enrolled users in a course lose access to a room when their user account is deleted.
     *
     * @covers \core_communication\api::remove_members_from_room
     * @covers \core_communication\task\remove_members_from_room::execute
     * @covers \core_communication\task\remove_members_from_room::queue
     */
    public function test_users_removed_from_room_when_deleting_user(): void {
        global $CFG;
        require_once($CFG->dirroot . '/lib/enrollib.php');

        // Sample data.
        $roomname = 'Samplematrixroom';
        $provider = 'communication_matrix';
        $course = $this->get_course($roomname, $provider);
        $user = $this->get_user();

        // Run room tasks.
        $this->runAdhocTasks('\core_communication\task\create_and_configure_room_task');

        // Enrol the user in the course.
        $enrol = enrol_get_plugin('manual');
        $enrolinstances = enrol_get_instances($course->id, true);
        $instance = reset($enrolinstances);
        $enrol->enrol_user($instance, $user->id);

        // Run the user tasks.
        $this->runAdhocTasks('\core_communication\task\add_members_to_room_task');

        $communicationprocessor = processor::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id
        );
        $matrixrooms = new matrix_rooms($communicationprocessor->get_id());
        $eventmanager = new matrix_events_manager($matrixrooms->get_matrix_room_id());
        $matrixhomeserverurl = $eventmanager->matrixhomeserverurl;

        $matrixuserid = matrix_user_manager::get_matrixid_from_moodle($user->id);
        // Check our Matrix user id has room membership.
        $this->assertTrue($communicationprocessor->get_room_provider()->check_room_membership($matrixuserid));
        // Delete user.
        delete_user($user);
        // Run the user tasks.
        // $this->runAdhocTasks('\core_communication\task\remove_members_from_room');
        // Check our Matrix user id no longer has membership.
        $this->assertFalse($communicationprocessor->get_room_provider()->check_room_membership($matrixuserid));
    }

    /**
     * Test create instance user mapping.
     *
     * @covers \core_communication\processor::create_instance_user_mapping
     * @covers \core_communication\processor::mark_users_as_synced
     * @covers \core_communication\processor::get_instance_userids
     */
    public function test_create_instance_user_mapping(): void {
        $this->resetAfterTest();

        global $DB;
        $course = $this->get_course('Sampleroom', 'none');
        $userid = $this->get_user()->id;

        // Sample data.
        $communicationroomname = 'Sampleroom';
        $selectedcommunication = 'communication_matrix';
        $component = 'core_course';
        $instancetype = 'coursecommunication';

        // First test the adding members to a room.
        $communication = \core_communication\api::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id
        );
        $communication->create_and_configure_room($selectedcommunication, $communicationroomname);
        $communication->add_members_to_room([$userid]);

        $this->runAdhocTasks('\core_communication\task\create_and_configure_room_task');
        $this->runAdhocTasks('\core_communication\task\add_members_to_room_task');

        // Test against the object.
        $communicationprocessor = processor::load_by_instance(
            $component,
            $instancetype,
            $course->id
        );

        // Test against the database.
        $communicationuserrecord = $DB->get_record('communication_user', [
            'commid' => $communicationprocessor->get_id(),
            'userid' => $userid
        ]);

        $this->assertEquals($communicationuserrecord->userid, $userid);
        $this->assertEquals($communicationuserrecord->commid, $communicationprocessor->get_id());
    }

    /**
     * Test update instance user mapping.
     *
     * @covers \core_communication\processor::create_instance_user_mapping
     * @covers \core_communication\processor::mark_users_as_synced
     * @covers \core_communication\processor::get_instance_userids
     * @covers \core_communication\processor::delete_instance_user_mapping
     */
    public function test_update_instance_user_mapping(): void {
        $this->resetAfterTest();

        global $DB;
        $course = $this->get_course();
        $userid = $this->get_user()->id;

        $this->runAdhocTasks('\core_communication\task\create_and_configure_room_task');

        // Sample data.
        $communicationroomname = 'Sampleroom';
        $selectedcommunication = 'communication_matrix';
        $component = 'core_course';
        $instancetype = 'coursecommunication';

        $communication = \core_communication\api::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id
        );
        $communication->update_room($selectedcommunication, $communicationroomname);
        $communication->add_members_to_room([$userid]);

        $this->runAdhocTasks('\core_communication\task\update_room_task');
        $this->runAdhocTasks('\core_communication\task\add_members_to_room_task');

        // Test against the object.
        $communicationprocessor = processor::load_by_instance(
            $component,
            $instancetype,
            $course->id
        );

        // Test against the database.
        $communicationuserrecord = $DB->get_record('communication_user', [
            'commid' => $communicationprocessor->get_id(),
            'userid' => $userid
        ]);

        $this->assertEquals($communicationuserrecord->userid, $userid);
        $this->assertEquals($communicationuserrecord->commid, $communicationprocessor->get_id());

        // Now add again.
        $communicationprocessor->delete_instance_user_mapping([$userid]);

        // Test against the database.
        $communicationuserrecord = $DB->get_record('communication_user', [
            'commid' => $communicationprocessor->get_id(),
            'userid' => $userid
        ]);

        $this->assertEmpty($communicationuserrecord);
    }

    /**
     * Test delete instance user mapping.
     *
     * @covers \core_communication\processor::create_instance_user_mapping
     * @covers \core_communication\processor::mark_users_as_synced
     * @covers \core_communication\processor::get_instance_userids
     * @covers \core_communication\processor::delete_instance_user_mapping
     */
    public function test_delete_instance_user_mapping(): void {
        $this->resetAfterTest();

        global $DB;
        $course = $this->get_course('Sampleroom', 'none');
        $userid = $this->get_user()->id;

        // Sample data.
        $communicationroomname = 'Sampleroom';
        $selectedcommunication = 'communication_matrix';
        $component = 'core_course';
        $instancetype = 'coursecommunication';

        // First test the adding members to a room.
        $communication = \core_communication\api::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id
        );
        $communication->create_and_configure_room($selectedcommunication, $communicationroomname);
        $communication->add_members_to_room([$userid]);

        $this->runAdhocTasks('\core_communication\task\create_and_configure_room_task');
        $this->runAdhocTasks('\core_communication\task\add_members_to_room_task');

        // Test against the object.
        $communicationprocessor = processor::load_by_instance(
            $component,
            $instancetype,
            $course->id
        );

        $this->assertEquals([$userid], $communicationprocessor->get_all_userids_for_instance());

        // Delete the user mapping.
        $communicationprocessor->delete_instance_user_mapping([$userid]);

        $this->assertEmpty($communicationprocessor->get_all_userids_for_instance());

        // Test against the database.
        $communicationuserrecord = $DB->get_record('communication_user', [
            'commid' => $communicationprocessor->get_id(),
            'userid' => $userid
        ]);

        $this->assertEmpty($communicationuserrecord);
    }

    /**
     * Test delete user mappings for instance.
     *
     * @covers \core_communication\processor::create_instance_user_mapping
     * @covers \core_communication\processor::mark_users_as_synced
     * @covers \core_communication\processor::get_instance_userids
     * @covers \core_communication\processor::delete_user_mappings_for_instance
     */
    public function test_delete_user_mappings_for_instance(): void {
        $this->resetAfterTest();

        global $DB;
        $course = $this->get_course('Sampleroom', 'none');
        $userid = $this->get_user()->id;

        // Sample data.
        $communicationroomname = 'Sampleroom';
        $selectedcommunication = 'communication_matrix';
        $component = 'core_course';
        $instancetype = 'coursecommunication';

        // First test the adding members to a room.
        $communication = \core_communication\api::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id
        );
        $communication->create_and_configure_room($selectedcommunication, $communicationroomname);
        $communication->add_members_to_room([$userid]);

        $this->runAdhocTasks('\core_communication\task\create_and_configure_room_task');
        $this->runAdhocTasks('\core_communication\task\add_members_to_room_task');

        // Test against the object.
        $communicationprocessor = processor::load_by_instance(
            $component,
            $instancetype,
            $course->id
        );

        $this->assertEquals([$userid], $communicationprocessor->get_all_userids_for_instance());

        // Delete the user mapping.
        $communicationprocessor->delete_user_mappings_for_instance();

        $this->assertEmpty($communicationprocessor->get_all_userids_for_instance());

        // Test against the database.
        $communicationuserrecord = $DB->get_record('communication_user', [
            'commid' => $communicationprocessor->get_id(),
            'userid' => $userid
        ]);

        $this->assertEmpty($communicationuserrecord);
    }

    /**
     * Test status notifications of a communication room are generated correctly.
     *
     * @covers \core_communication\api::show_communication_room_status_notification
     */
    public function test_show_communication_room_status_notification(): void {
        $course = $this->get_course();

        // Get communication api object.
        $communication = \core_communication\api::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id
        );

        // Room should be in 'pending' state before the task is run and show a notification.
        $communication->show_communication_room_status_notification();
        $notifications = \core\notification::fetch();
        $this->assertStringContainsString('Your Matrix room will be ready soon.', $notifications[0]->get_message());

        // Run the task.
        $this->runAdhocTasks('\core_communication\task\create_and_configure_room_task');

        // Get updated communication api after room configuration.
        $communication = \core_communication\api::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id
        );

        // Check the room is now in 'ready' state and show a notification.
        $communication->show_communication_room_status_notification();
        $notifications = \core\notification::fetch();
        $this->assertStringContainsString('Your Matrix room is ready!', $notifications[0]->get_message());
    }

    /**
     * Test set provider data from handler.
     *
     * @covers \core_communication\api::set_data
     * @covers \communication_matrix\communication_feature::set_form_data
     */
    public function test_set_provider_data(): void {
        $this->resetAfterTest();
        $course = $this->get_course();
        $communication = \core_communication\api::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id
        );

        // Sample data.
        $roomname = 'Sampleroom';
        $provider = 'communication_matrix';

        // Set the data.
        $communication->set_data($course);

        // Test the set data.
        $this->assertEquals($roomname, $course->communicationroomname);
        $this->assertEquals($provider, $course->selectedcommunication);
    }
}
