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

use communication_matrix\matrix_test_helper_trait;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../provider/matrix/tests/matrix_test_helper_trait.php');
require_once(__DIR__ . '/communication_test_helper_trait.php');

/**
 * Class processor_test to test the communication internal api and its associated methods.
 *
 * @package    core_communication
 * @category   test
 * @copyright  2023 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core_communication\processor
 */
final class processor_test extends \advanced_testcase {
    use matrix_test_helper_trait;
    use communication_test_helper_trait;

    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        $this->setup_communication_configs();
        $this->initialise_mock_server();
    }

    /**
     * Test create instance.
     *
     * @covers ::create_instance
     * @covers ::get_id
     * @covers ::get_context
     * @covers ::get_context_id
     * @covers ::get_provider
     * @covers ::get_room_name
     */
    public function test_create_instance(): void {
        global $DB;
        $this->resetAfterTest();

        // Sample test data.
        $instanceid = 10;
        $context = \core\context\system::instance();
        $component = 'core';
        $instancetype = 'mycommunication';
        $selectedcommunication = 'communication_matrix';
        $communicationroomname = 'communicationroom';

        $communicationprocessor = processor::create_instance(
            $context,
            $selectedcommunication,
            $instanceid,
            $component,
            $instancetype,
            $communicationroomname,
        );

        // Now test the record against the database.
        $communicationrecord = $DB->get_record(
            'communication',
            ['instanceid' => $instanceid, 'component' => $component, 'instancetype' => $instancetype]
        );

        // Test against the set data.
        $this->assertNotEmpty($communicationrecord);
        $this->assertEquals($context->id, $communicationrecord->contextid);
        $this->assertEquals($instanceid, $communicationrecord->instanceid);
        $this->assertEquals($component, $communicationrecord->component);
        $this->assertEquals($selectedcommunication, $communicationrecord->provider);
        $this->assertEquals($communicationroomname, $communicationrecord->roomname);
        $this->assertEquals($instancetype, $communicationrecord->instancetype);

        // Test against the object.
        $this->assertEquals($context->id, $communicationprocessor->get_context_id());
        $this->assertEquals($context, $communicationprocessor->get_context());
        $this->assertEquals($communicationprocessor->get_id(), $communicationrecord->id);
        $this->assertEquals($communicationprocessor->get_provider(), $communicationrecord->provider);
        $this->assertEquals($communicationprocessor->get_room_name(), $communicationrecord->roomname);
    }

    /**
     * Test update instance.
     *
     * @covers ::update_instance
     * @covers ::is_instance_active
     * @covers ::get_id
     * @covers ::get_room_name
     */
    public function test_update_instance(): void {
        global $DB;
        $this->resetAfterTest();

        // Sameple test data.
        $instanceid = 10;
        $context = \core\context\system::instance();
        $component = 'core';
        $instancetype = 'mycommunication';
        $selectedcommunication = 'communication_matrix';
        $communicationroomname = 'communicationroom';

        $communicationprocessor = processor::create_instance(
            $context,
            $selectedcommunication,
            $instanceid,
            $component,
            $instancetype,
            $communicationroomname,
        );

        $selectedcommunication = 'none';
        $communicationroomname = 'communicationroomedited';

        $communicationprocessor->update_instance(processor::PROVIDER_INACTIVE, $communicationroomname);

        // Now test the record against the database.
        $communicationrecord = $DB->get_record('communication', [
            'instanceid' => $instanceid,
            'component' => $component,
            'instancetype' => $instancetype,
        ]);

        // Test against the set data.
        $this->assertNotEmpty($communicationrecord);
        $this->assertEquals($context->id, $communicationrecord->contextid);
        $this->assertEquals($instanceid, $communicationrecord->instanceid);
        $this->assertEquals($component, $communicationrecord->component);
        $this->assertEquals(processor::PROVIDER_INACTIVE, $communicationrecord->active);
        $this->assertEquals($communicationroomname, $communicationrecord->roomname);
        $this->assertEquals($instancetype, $communicationrecord->instancetype);

        // Test against the object.
        $this->assertEquals($context->id, $communicationprocessor->get_context_id());
        $this->assertEquals($context, $communicationprocessor->get_context());
        $this->assertEquals($communicationprocessor->get_id(), $communicationrecord->id);
        $this->assertEquals($communicationprocessor->is_instance_active(), $communicationrecord->active);
        $this->assertEquals($communicationprocessor->get_room_name(), $communicationrecord->roomname);
    }

    /**
     * Test delete instance.
     *
     * @covers ::delete_instance
     * @covers ::create_instance
     * @covers ::load_by_instance
     */
    public function test_delete_instance(): void {
        global $DB;
        $this->resetAfterTest();

        // Sameple test data.
        $instanceid = 10;
        $context = \core\context\system::instance();
        $component = 'core';
        $instancetype = 'mycommunication';
        $selectedcommunication = 'communication_matrix';
        $communicationroomname = 'communicationroom';

        $communicationprocessor = processor::create_instance(
            $context,
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
            'instancetype' => $instancetype,
        ]);

        // Test against the set data.
        $this->assertEmpty($communicationrecord);

        // Test against the object.
        $communicationprocessor = processor::load_by_instance(
            context: $context,
            component: $component,
            instancetype: $instancetype,
            instanceid: $instanceid,
        );
        $this->assertNull($communicationprocessor);
    }

    /**
     * Test load by id.
     *
     * @covers ::load_by_instance
     * @covers ::get_room_provider
     */
    public function test_load_by_instance(): void {
        $this->resetAfterTest();
        $course = $this->get_course();
        $context = \core\context\course::instance($course->id);

        // Test the communication record exists.
        $communicationprocessor = processor::load_by_instance(
            context: $context,
            component: 'core_course',
            instancetype: 'coursecommunication',
            instanceid: $course->id,
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
     * @covers ::get_room_provider
     * @covers ::load_by_instance
     */
    public function test_load_by_id(): void {
        $this->resetAfterTest();
        $course = $this->get_course();
        $context = \core\context\course::instance($course->id);

        // Test the communication record exists.
        $communicationprocessor = processor::load_by_instance(
            context: $context,
            component: 'core_course',
            instancetype: 'coursecommunication',
            instanceid: $course->id,
        );

        $communicationprocessorbyid = processor::load_by_id($communicationprocessor->get_id());

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
     * @covers ::load_by_instance
     */
    public function test_get_component(): void {
        $this->resetAfterTest();
        $course = $this->get_course();
        $context = \core\context\course::instance($course->id);

        // Test the communication record exists.
        $communicationprocessor = processor::load_by_instance(
            context: $context,
            component: 'core_course',
            instancetype: 'coursecommunication',
            instanceid: $course->id,
        );

        $this->assertEquals('core_course', $communicationprocessor->get_component());
    }

    /**
     * Test get provider.
     *
     * @covers ::get_provider
     * @covers ::load_by_instance
     */
    public function test_get_provider(): void {
        $this->resetAfterTest();
        $course = $this->get_course();
        $context = \core\context\course::instance($course->id);

        // Test the communication record exists when fetching the active provider.
        $communicationprocessor = processor::load_by_instance(
            context: $context,
            component: 'core_course',
            instancetype: 'coursecommunication',
            instanceid: $course->id,
        );

        $this->assertEquals('communication_matrix', $communicationprocessor->get_provider());

        // Test the communication record exists when specifying the provider.
        $communicationprocessor = processor::load_by_instance(
            context: $context,
            component: 'core_course',
            instancetype: 'coursecommunication',
            instanceid: $course->id,
            provider: 'communication_matrix',
        );

        $this->assertEquals('communication_matrix', $communicationprocessor->get_provider());

        // Test the communication record exists when the provider is not active.
        $communicationprocessor->update_instance(processor::PROVIDER_INACTIVE);
        $communicationprocessor = processor::load_by_instance(
            context: $context,
            component: 'core_course',
            instancetype: 'coursecommunication',
            instanceid: $course->id,
            provider: 'communication_matrix',
        );

        $this->assertEquals('communication_matrix', $communicationprocessor->get_provider());
    }

    /**
     * Test get room name.
     *
     * @covers ::get_room_name
     * @covers ::load_by_instance
     */
    public function test_get_room_name(): void {
        $this->resetAfterTest();
        $course = $this->get_course();
        $context = \core\context\course::instance($course->id);

        // Test the communication record exists.
        $communicationprocessor = processor::load_by_instance(
            context: $context,
            component: 'core_course',
            instancetype: 'coursecommunication',
            instanceid: $course->id,
        );

        $this->assertEquals('Sampleroom', $communicationprocessor->get_room_name());
    }

    /**
     * Test get room provider.
     *
     * @covers ::get_room_provider
     * @covers ::require_room_features
     * @covers ::supports_room_features
     * @covers ::load_by_instance
     */
    public function test_get_room_provider(): void {
        $this->resetAfterTest();
        $course = $this->get_course();
        $context = \core\context\course::instance($course->id);

        // Test the communication record exists.
        $communicationprocessor = processor::load_by_instance(
            context: $context,
            component: 'core_course',
            instancetype: 'coursecommunication',
            instanceid: $course->id,
        );

        $this->assertInstanceOf(room_chat_provider::class, $communicationprocessor->get_room_provider());
    }

    /**
     * Test get user provider.
     *
     * @covers ::get_user_provider
     * @covers ::require_user_features
     * @covers ::supports_user_features
     * @covers ::load_by_instance
     */
    public function test_get_user_provider(): void {
        $this->resetAfterTest();
        $course = $this->get_course();
        $context = \core\context\course::instance($course->id);

        // Test the communication record exists.
        $communicationprocessor = processor::load_by_instance(
            context: $context,
            component: 'core_course',
            instancetype: 'coursecommunication',
            instanceid: $course->id,
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
     * @covers ::load_by_instance
     */
    public function test_get_room_user_provider(): void {
        $this->resetAfterTest();
        $course = $this->get_course();
        $context = \core\context\course::instance($course->id);

        // Test the communication record exists.
        $communicationprocessor = processor::load_by_instance(
            context: $context,
            component: 'core_course',
            instancetype: 'coursecommunication',
            instanceid: $course->id,
        );

        $this->assertInstanceOf(room_user_provider::class, $communicationprocessor->get_room_user_provider());
    }

    /**
     * Test get avatar.
     *
     * @covers ::get_avatar
     * @covers ::load_by_instance
     * @covers ::get_avatar_filename
     * @covers ::set_avatar_filename
     * @covers ::set_avatar_synced_flag
     */
    public function test_get_avatar(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        global $CFG;
        $course = $this->get_course('Sampleroom', 'none');

        // Sample data.
        $communicationroomname = 'Sampleroom';
        $selectedcommunication = 'communication_matrix';
        $avatar = $this->create_communication_file(
            'moodle_logo.jpg',
            'moodle_logo.jpg',
        );

        $communication = \core_communication\api::load_by_instance(
            context: \core\context\course::instance($course->id),
            component: 'core_course',
            instancetype: 'coursecommunication',
            instanceid: $course->id,
            provider: $selectedcommunication,
        );
        $communication->create_and_configure_room($communicationroomname, $avatar);

        $communicationprocessor = processor::load_by_instance(
            context: \core\context\course::instance($course->id),
            component: 'core_course',
            instancetype: 'coursecommunication',
            instanceid: $course->id,
        );

        $avatar = $communicationprocessor->get_avatar();

        $this->assertNotNull($avatar);
        $this->assertEquals($avatar->get_component(), 'core_communication');
        $this->assertEquals($avatar->get_filearea(), 'avatar');
        $this->assertEquals($avatar->get_itemid(), $communicationprocessor->get_id());
        $this->assertEquals($avatar->get_filepath(), '/');
        $this->assertEquals($avatar->get_filearea(), 'avatar');
        $this->assertEquals($avatar->get_filename(), $communicationprocessor->get_avatar_filename());

        // Change the avatar file name to something else and check it was set.
        $communicationprocessor->set_avatar_filename('newname.svg');

        $communicationprocessor = processor::load_by_instance(
            context: \core\context\course::instance($course->id),
            component: 'core_course',
            instancetype: 'coursecommunication',
            instanceid: $course->id,
        );
        $this->assertEquals($communicationprocessor->get_avatar_filename(), 'newname.svg');
    }

    /**
     * Test if the provider is enabled and configured, or disabled.
     *
     * @covers ::is_provider_available
     */
    public function test_is_provider_available(): void {
        $this->resetAfterTest();
        $communicationprovider = 'communication_matrix';
        $this->assertTrue(processor::is_provider_available($communicationprovider));

        // Now test is disabling the plugin returns false.
        set_config('disabled', 1, $communicationprovider);
        $this->assertFalse(processor::is_provider_available($communicationprovider));
    }

    /**
     * Test delete flagged user id's return correct users.
     *
     * @covers ::get_all_delete_flagged_userids
     */
    public function test_get_all_delete_flagged_userids(): void {
        $this->resetAfterTest();

        $course = $this->get_course('Sampleroom', 'none');
        $user1 = $this->getDataGenerator()->create_user()->id;
        $user2 = $this->getDataGenerator()->create_user()->id;

        // Sample data.
        $communicationroomname = 'Sampleroom';
        $selectedcommunication = 'communication_matrix';
        $component = 'core_course';
        $instancetype = 'coursecommunication';

        // Load the communication api.
        $communication = \core_communication\api::load_by_instance(
            context: \core\context\course::instance($course->id),
            component: $component,
            instancetype: $instancetype,
            instanceid: $course->id,
            provider: $selectedcommunication,
        );
        $communication->create_and_configure_room($communicationroomname);
        $communication->add_members_to_room([$user1, $user2]);

        // Now remove user1 from the room.
        $communication->remove_members_from_room([$user1]);

        // Test against the object.
        $communicationprocessor = processor::load_by_instance(
            context: \core\context\course::instance($course->id),
            component: $component,
            instancetype: $instancetype,
            instanceid: $course->id,
        );

        $this->assertEquals([$user1], $communicationprocessor->get_all_delete_flagged_userids());
    }
}
