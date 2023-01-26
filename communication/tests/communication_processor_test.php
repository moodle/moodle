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

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/communication_test_helper_trait.php');

/**
 * Class communication_processor_test to test the communication internal api and its associated methods.
 *
 * @package    core_communication
 * @category   test
 * @copyright  2023 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core_communication\communication_processor
 */
class communication_processor_test extends \advanced_testcase {

    use communication_test_helper_trait;

    /**
     * Test create instance.
     *
     * @covers ::create_instance
     */
    public function test_create_instance(): void {
        global $DB;
        $this->resetAfterTest();

        // Sameple test data.
        $instanceid = 10;
        $component = 'core_course';
        $instancetype = 'coursecommunication';
        $selectedcommunication = 'communication_matrix';
        $communicationroomname = 'communicationroom';

        $communicationprocessor = communication_processor::create_instance(
            $selectedcommunication,
            $instanceid,
            $component,
            $instancetype,
            $communicationroomname,
        );

        // Now test the record against the database.
        $communicationrecord = $DB->get_record('communication',
            ['instanceid' => $instanceid, 'component' => $component, 'instancetype' => $instancetype]);

        // Test against the set data.
        $this->assertNotEmpty($communicationrecord);
        $this->assertEquals($instanceid, $communicationrecord->instanceid);
        $this->assertEquals($component, $communicationrecord->component);
        $this->assertEquals($selectedcommunication, $communicationrecord->provider);
        $this->assertEquals($communicationroomname, $communicationrecord->roomname);
        $this->assertEquals($instancetype, $communicationrecord->instancetype);

        // Test against the object.
        $this->assertEquals($communicationprocessor->get_id(), $communicationrecord->id);
        $this->assertEquals($communicationprocessor->get_provider(), $communicationrecord->provider);
        $this->assertEquals($communicationprocessor->get_room_name(), $communicationrecord->roomname);
    }

    /**
     * Test update instance.
     *
     * @covers ::update_instance
     */
    public function test_update_instance(): void {
        global $DB;
        $this->resetAfterTest();

        // Sameple test data.
        $instanceid = 10;
        $component = 'core_course';
        $instancetype = 'coursecommunication';
        $selectedcommunication = 'communication_matrix';
        $communicationroomname = 'communicationroom';

        $communicationprocessor = communication_processor::create_instance(
            $selectedcommunication,
            $instanceid,
            $component,
            $instancetype,
            $communicationroomname,
        );

        $selectedcommunication = 'none';
        $communicationroomname = 'communicationroomedited';

        $communicationprocessor->update_instance($selectedcommunication, $communicationroomname);

        // Now test the record against the database.
        $communicationrecord = $DB->get_record('communication', [
            'instanceid' => $instanceid,
            'component' => $component,
            'instancetype' => $instancetype
        ]);

        // Test against the set data.
        $this->assertNotEmpty($communicationrecord);
        $this->assertEquals($instanceid, $communicationrecord->instanceid);
        $this->assertEquals($component, $communicationrecord->component);
        $this->assertEquals($selectedcommunication, $communicationrecord->provider);
        $this->assertEquals($communicationroomname, $communicationrecord->roomname);
        $this->assertEquals($instancetype, $communicationrecord->instancetype);

        // Test against the object.
        $this->assertEquals($communicationprocessor->get_id(), $communicationrecord->id);
        $this->assertEquals($communicationprocessor->get_provider(), $communicationrecord->provider);
        $this->assertEquals($communicationprocessor->get_room_name(), $communicationrecord->roomname);
    }

    /**
     * Test delete instance.
     *
     * @covers ::delete_instance
     */
    public function test_delete_instance(): void {
        global $DB;
        $this->resetAfterTest();

        // Sameple test data.
        $instanceid = 10;
        $component = 'core_course';
        $instancetype = 'coursecommunication';
        $selectedcommunication = 'communication_matrix';
        $communicationroomname = 'communicationroom';

        $communicationprocessor = communication_processor::create_instance(
            $selectedcommunication,
            $instanceid,
            $component,
            $instancetype,
            $communicationroomname,
        );

        $communicationprocessor->delete_instance();

        // Now test the record against the database.
        $communicationrecord = $DB->get_record('communication', [
            'instanceid' => $instanceid,
            'component' => $component,
            'instancetype' => $instancetype
        ]);

        // Test against the set data.
        $this->assertEmpty($communicationrecord);

        // Test against the object.
        $communicationprocessor = communication_processor::load_by_instance(
            $component,
            $instancetype,
            $instanceid);
        $this->assertNull($communicationprocessor);
    }

    /**
     * Test create instance user mapping.
     *
     * @covers ::create_instance_user_mapping
     * @covers ::get_all_userids_for_instance
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

        // Test against the object.
        $communicationprocessor = communication_processor::load_by_instance(
            $component,
            $instancetype,
            $course->id
        );

        $this->assertEquals([$userid], $communicationprocessor->get_all_userids_for_instance());

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
     * @covers ::get_all_userids_for_instance
     */
    public function test_update_instance_user_mapping(): void {
        $this->resetAfterTest();

        global $DB;
        $course = $this->get_course();
        $userid = $this->get_user()->id;

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

        // Test against the object.
        $communicationprocessor = communication_processor::load_by_instance(
            $component,
            $instancetype,
            $course->id
        );

        $this->assertnotEmpty($communicationprocessor->get_instance_userids_by_synced());

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
     * @covers ::delete_instance_user_mapping
     * @covers ::get_all_userids_for_instance
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

        // Test against the object.
        $communicationprocessor = communication_processor::load_by_instance(
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
     * @covers ::delete_user_mappings_for_instance
     * @covers ::get_all_userids_for_instance
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

        // Test against the object.
        $communicationprocessor = communication_processor::load_by_instance(
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
     * Test load by id.
     *
     * @covers ::load_by_instance
     */
    public function test_load_by_instance(): void {
        $this->resetAfterTest();
        $course = $this->get_course();

        // Test the communication record exists.
        $communicationprocessor = communication_processor::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id
        );

        $this->assertNotNull($communicationprocessor);
        $this->assertInstanceOf(communication_provider::class, $communicationprocessor->get_room_provider());
        $this->assertInstanceOf(room_chat_provider::class, $communicationprocessor->get_room_provider());
        $this->assertInstanceOf(room_user_provider::class, $communicationprocessor->get_room_provider());
        $this->assertInstanceOf(user_provider::class, $communicationprocessor->get_room_provider());
    }

    /**
     * Test load by id.
     *
     * @covers ::load_by_id
     */
    public function test_load_by_id(): void {
        $this->resetAfterTest();
        $course = $this->get_course();

        // Test the communication record exists.
        $communicationprocessor = communication_processor::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id
        );

        $communicationprocessorbyid = communication_processor::load_by_id($communicationprocessor->get_id());

        $this->assertNotNull($communicationprocessorbyid);
        $this->assertInstanceOf(communication_provider::class, $communicationprocessorbyid->get_room_provider());
        $this->assertInstanceOf(room_chat_provider::class, $communicationprocessorbyid->get_room_provider());
        $this->assertInstanceOf(room_user_provider::class, $communicationprocessorbyid->get_room_provider());
        $this->assertInstanceOf(user_provider::class, $communicationprocessorbyid->get_room_provider());
    }

    /**
     * Test get component.
     *
     * @covers ::get_component
     */
    public function test_get_component(): void {
        $this->resetAfterTest();
        $course = $this->get_course();

        // Test the communication record exists.
        $communicationprocessor = communication_processor::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id
        );

        $this->assertEquals('core_course', $communicationprocessor->get_component());
    }

    /**
     * Test get provider.
     *
     * @covers ::get_provider
     */
    public function test_get_provider(): void {
        $this->resetAfterTest();
        $course = $this->get_course();

        // Test the communication record exists.
        $communicationprocessor = communication_processor::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id
        );

        $this->assertEquals('communication_matrix', $communicationprocessor->get_provider());
    }

    /**
     * Test get room name.
     *
     * @covers ::get_room_name
     */
    public function test_get_room_name(): void {
        $this->resetAfterTest();
        $course = $this->get_course();

        // Test the communication record exists.
        $communicationprocessor = communication_processor::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id
        );

        $this->assertEquals('Sampleroom', $communicationprocessor->get_room_name());
    }

    /**
     * Test get room provider.
     *
     * @covers ::get_room_provider
     * @covers ::require_room_features
     * @covers ::supports_room_features
     */
    public function test_get_room_provider(): void {
        $this->resetAfterTest();
        $course = $this->get_course();

        // Test the communication record exists.
        $communicationprocessor = communication_processor::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id
        );

        $this->assertInstanceOf(room_chat_provider::class, $communicationprocessor->get_room_provider());
    }

    /**
     * Test get user provider.
     *
     * @covers ::get_user_provider
     * @covers ::require_user_features
     * @covers ::supports_user_features
     */
    public function test_get_user_provider(): void {
        $this->resetAfterTest();
        $course = $this->get_course();

        // Test the communication record exists.
        $communicationprocessor = communication_processor::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id
        );

        $this->assertInstanceOf(user_provider::class, $communicationprocessor->get_room_provider());
    }

    /**
     * Test get room user provider.
     *
     * @covers ::get_room_user_provider
     * @covers ::require_room_features
     * @covers ::require_room_user_features
     * @covers ::supports_room_user_features
     * @covers ::supports_room_features
     */
    public function test_get_room_user_provider(): void {
        $this->resetAfterTest();
        $course = $this->get_course();

        // Test the communication record exists.
        $communicationprocessor = communication_processor::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id
        );

        $this->assertInstanceOf(room_user_provider::class, $communicationprocessor->get_room_user_provider());
    }

    /**
     * Test get avatar.
     *
     * @covers ::get_avatar
     */
    public function test_get_avatar(): void {
        $this->resetAfterTest();

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
        $communication->create_and_configure_room($selectedcommunication, $communicationroomname, $avatarurl);

        $communicationprocessor = communication_processor::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id
        );

        $avatar = $communicationprocessor->get_avatar();

        $this->assertNotNull($avatar);
        $this->assertEquals($avatar->get_component(), 'core_communication');
        $this->assertEquals($avatar->get_filearea(), 'avatar');
        $this->assertEquals($avatar->get_itemid(), $communicationprocessor->get_id());
        $this->assertEquals($avatar->get_filepath(), '/');
        $this->assertEquals($avatar->get_filearea(), 'avatar');
    }
}
