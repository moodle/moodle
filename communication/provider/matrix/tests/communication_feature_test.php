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
 * Class communication_feature_test to test the matrix features implemented using the core interfaces.
 *
 * @package    communication_matrix
 * @category   test
 * @copyright  2023 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \communication_matrix\communication_feature
 */
class communication_feature_test extends \advanced_testcase {

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
     * @covers ::update_chat_room
     */
    public function test_create_or_update_chat_room() {
        $course = $this->getDataGenerator()->create_course();

        // Sameple test data.
        $instanceid = $course->id;
        $component = 'core_course';
        $instancetype = 'coursecommunication';
        $selectedcommunication = 'communication_matrix';
        $communicationroomname = 'communicationroom';

        $communicationprocessor = processor::create_instance(
            $selectedcommunication,
            $instanceid,
            $component,
            $instancetype,
            $communicationroomname,
        );
        $communicationprocessor->get_room_provider()->create_chat_room();

        $matrixrooms = new matrix_rooms($communicationprocessor->get_id());

        // Test the response against the stored data.
        $this->assertNotEmpty($matrixrooms->get_matrix_room_id());

        // Add api call to get room data and test against set data.
        $matrixroomdata = $this->get_matrix_room_data($matrixrooms->get_matrix_room_id());
        $this->assertEquals($matrixrooms->get_matrix_room_id(), $matrixroomdata->room_id);
        $this->assertEquals($communicationprocessor->get_room_name(), $matrixroomdata->name);

        $communicationroomname = 'communicationroomedited';
        $communicationprocessor->update_instance($selectedcommunication, $communicationroomname);
        $communicationprocessor->get_room_provider()->update_chat_room();

        // Add api call to get room data and test against set data.
        $matrixroomdata = $this->get_matrix_room_data($matrixrooms->get_matrix_room_id());
        $this->assertEquals($communicationprocessor->get_room_name(), $matrixroomdata->name);
    }

    /**
     * Test delete chat room.
     *
     * @covers ::delete_chat_room
     */
    public function test_delete_chat_room(): void {
        $course = $this->getDataGenerator()->create_course();

        // Sameple test data.
        $instanceid = $course->id;
        $component = 'core_course';
        $instancetype = 'coursecommunication';
        $selectedcommunication = 'communication_matrix';
        $communicationroomname = 'communicationroom';

        $communicationprocessor = processor::create_instance(
            $selectedcommunication,
            $instanceid,
            $component,
            $instancetype,
            $communicationroomname,
        );
        $communicationprocessor->get_room_provider()->create_chat_room();

        $matrixrooms = new matrix_rooms($communicationprocessor->get_id());

        $communicationprocessor->get_room_provider()->delete_chat_room();

        // We are not deleting any matrix room, just deleting local record.
        $matrixroomsafterdeletion = new matrix_rooms($communicationprocessor->get_id());

        $this->assertFalse($matrixroomsafterdeletion->room_record_exists());

        $matrixroomdata = $this->get_matrix_room_data($matrixrooms->get_matrix_room_id());

        $this->assertNotEmpty($matrixroomdata);
        $this->assertEquals($communicationprocessor->get_room_name(), $matrixroomdata->name);
    }

    /**
     * Test update room avatar.
     *
     * @covers ::update_room_avatar
     */
    public function test_update_room_avatar(): void {
        global $CFG;
        $course = $this->get_course('Sampleroom', 'none');

        // Sample data.
        $communicationroomname = 'Sampleroom';
        $selectedcommunication = 'communication_matrix';
        $avatarurl = $CFG->dirroot . '/communication/tests/fixtures/moodle_logo.jpg';

        $communication = \core_communication\api::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id
        );
        $communication->create_and_configure_room(
            $selectedcommunication,
            $communicationroomname,
            $avatarurl
        );

        $communicationprocessor = processor::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id
        );
        $communicationprocessor->get_room_provider()->create_chat_room();

        $matrixrooms = new matrix_rooms($communicationprocessor->get_id());

        // Add api call to get room data and test against set data.
        $matrixroomdata = $this->get_matrix_room_data($matrixrooms->get_matrix_room_id());
        $this->assertNotEmpty($matrixroomdata->avatar);
    }

    /**
     * Test get chat room url.
     *
     * @covers ::get_chat_room_url
     */
    public function test_get_chat_room_url(): void {
        global $CFG;
        $course = $this->get_course('Sampleroom', 'none');

        // Sample data.
        $communicationroomname = 'Sampleroom';
        $selectedcommunication = 'communication_matrix';
        $avatarurl = $CFG->dirroot . '/communication/tests/fixtures/moodle_logo.jpg';

        $communication = \core_communication\api::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id
        );

        $communication->create_and_configure_room(
            $selectedcommunication,
            $communicationroomname,
            $avatarurl
        );

        $communicationprocessor = processor::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id
        );

        $communicationprocessor->get_room_provider()->create_chat_room();

        $matrixrooms = new matrix_rooms($communicationprocessor->get_id());

        $this->assertNotNull($communicationprocessor->get_room_provider()->get_chat_room_url());
        $this->assertStringContainsString(
            $matrixrooms->get_matrix_room_id(),
            $communicationprocessor->get_room_provider()->get_chat_room_url()
        );
    }

    /**
     * Test create members.
     *
     * @covers ::create_members
     * @covers ::add_registered_matrix_user_to_room
     */
    public function test_create_members(): void {
        global $CFG;
        $course = $this->get_course('Sampleroom', 'none');
        $userid = $this->get_user()->id;

        // Sample data.
        $communicationroomname = 'Sampleroom';
        $selectedcommunication = 'communication_matrix';
        $avatarurl = $CFG->dirroot . '/communication/tests/fixtures/moodle_logo.jpg';

        $communication = \core_communication\api::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id
        );

        $communication->create_and_configure_room(
            $selectedcommunication,
            $communicationroomname,
            $avatarurl
        );
        $communication->add_members_to_room([$userid]);

        $communicationprocessor = processor::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id
        );

        $communicationprocessor->get_room_provider()->create_chat_room();
        $communicationprocessor->get_room_provider()->add_members_to_room([$userid]);

        $matrixrooms = new matrix_rooms($communicationprocessor->get_id());
        $eventmanager = new matrix_events_manager($matrixrooms->get_matrix_room_id());

        // Get created matrixuserid from moodle.
        $elementserver = matrix_user_manager::set_matrix_home_server($eventmanager->matrixhomeserverurl);
        $matrixuserid = matrix_user_manager::get_matrixid_from_moodle($userid, $eventmanager->matrixhomeserverurl);

        $this->assertNotNull($matrixuserid);
        $this->assertEquals("@sampleun:{$elementserver}", $matrixuserid);

        // Add api call to get user data and test against set data.
        $matrixuserdata = $this->get_matrix_user_data($matrixrooms->get_matrix_room_id(), $matrixuserid);

        $this->assertNotEmpty($matrixuserdata);
        $this->assertEquals("Samplefn Sampleln", $matrixuserdata->displayname);
    }

    /**
     * Test add/remove members from room.
     *
     * @covers ::remove_members_from_room
     * @covers ::add_members_to_room
     * @covers ::add_registered_matrix_user_to_room
     * @covers ::check_room_membership
     */
    public function test_add_and_remove_members_from_room(): void {
        global $CFG;
        $course = $this->get_course('Sampleroom', 'none');
        $userid = $this->get_user()->id;

        // Sample data.
        $communicationroomname = 'Sampleroom';
        $selectedcommunication = 'communication_matrix';
        $avatarurl = $CFG->dirroot . '/communication/tests/fixtures/moodle_logo.jpg';

        $communication = \core_communication\api::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id
        );

        $communication->create_and_configure_room(
            $selectedcommunication,
            $communicationroomname,
            $avatarurl
        );
        $communication->add_members_to_room([$userid]);

        $communicationprocessor = processor::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id
        );

        $communicationprocessor->get_room_provider()->create_chat_room();
        $communicationprocessor->get_room_provider()->add_members_to_room([$userid]);

        $matrixrooms = new matrix_rooms($communicationprocessor->get_id());
        $eventmanager = new matrix_events_manager($matrixrooms->get_matrix_room_id());

        // Get created matrixuserid from moodle.
        $matrixuserid = matrix_user_manager::get_matrixid_from_moodle($userid, $eventmanager->matrixhomeserverurl);

        // Test user is a member of the room.
        $this->assertTrue($communicationprocessor->get_room_provider()->check_room_membership($matrixuserid));

        // Remove member from matrix room.
        $communicationprocessor->get_room_provider()->remove_members_from_room([$userid]);

        // Test user is no longer a member of the room.
        $this->assertFalse($communicationprocessor->get_room_provider()->check_room_membership($matrixuserid));
    }

    /**
     * Test save form data options.
     *
     * @covers ::save_form_data
     */
    public function test_save_form_data(): void {
        $this->resetAfterTest();
        $course = $this->get_course();

        $communicationprocessor = processor::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id
        );

        $course->matrixroomtopic = 'Sampletopicupdated';
        $communicationprocessor->get_form_provider()->save_form_data($course);

        // Test the updated topic.
        $matrixroomdata = new matrix_rooms($communicationprocessor->get_id());
        $this->assertEquals('Sampletopicupdated', $matrixroomdata->get_matrix_room_topic());
    }

}
