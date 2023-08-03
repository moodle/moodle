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
 * Class api_test to test the communication public api and its associated methods.
 *
 * @package    core_communication
 * @category   test
 * @copyright  2023 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core_communication\api
 */
class api_test extends \advanced_testcase {

    use communication_test_helper_trait;

    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        $this->setup_communication_configs();
    }

    /**
     * Test the communication plugin list for the form element returns the correct number of plugins.
     *
     * @covers ::get_communication_plugin_list_for_form
     */
    public function test_get_communication_plugin_list_for_form(): void {
        $communicationplugins = \core_communication\api::get_communication_plugin_list_for_form();
        // Get the communication plugins.
        $plugins = \core_component::get_plugin_list('communication');
        // Check the number of plugins matches plus 1 as we have none in the selection.
        $this->assertCount(count($plugins) + 1, $communicationplugins);
    }

    /**
     * Test set data to the instance.
     *
     * @covers ::set_data
     */
    public function test_set_data(): void {
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

    /**
     * Test get_current_communication_provider method.
     *
     * @covers ::get_provider
     */
    public function test_get_provider(): void {
        $course = $this->get_course();

        $communication = \core_communication\api::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id
        );

        $this->assertEquals('communication_matrix', $communication->get_provider());
    }

    /**
     * Test get_avatar_filerecord method.
     *
     * @covers ::get_avatar_filerecord
     */
    public function test_get_avatar_filerecord(): void {
        $course = $this->get_course();

        $communication = \core_communication\api::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id
        );
        $filerecord = $communication->get_avatar_filerecord('avatar.svg');

        $this->assertEquals('avatar.svg', $filerecord->filename);
        $this->assertEquals('core_communication', $filerecord->component);
        $this->assertEquals('avatar', $filerecord->filearea);
    }

    /**
     * Test set_avatar method.
     *
     * @covers ::set_avatar
     * @covers ::get_avatar_filerecord
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

        $communication = \core_communication\api::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id
        );
        $communication->create_and_configure_room($selectedcommunication, $communicationroomname, $avatar);

        $communicationprocessor = processor::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id
        );

        $this->assertNotNull($communicationprocessor->get_avatar());
    }

    /**
     * Test the create_and_configure_room method to add/create tasks.
     *
     * @covers ::create_and_configure_room
     */
    public function test_create_and_configure_room(): void {
        // Get the course by disabling communication so that we can create it manually calling the api.
        $course = $this->get_course('Sampleroom', 'none');

        // Sample data.
        $communicationroomname = 'Sampleroom';
        $selectedcommunication = 'communication_matrix';

        $communication = \core_communication\api::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id
        );
        $communication->create_and_configure_room($selectedcommunication, $communicationroomname);

        // Test the tasks added.
        $adhoctask = \core\task\manager::get_adhoc_tasks('\\core_communication\\task\\create_and_configure_room_task');
        $this->assertCount(1, $adhoctask);

        $adhoctask = reset($adhoctask);
        $this->assertInstanceOf('\\core_communication\\task\\create_and_configure_room_task', $adhoctask);

        // Test the communication record exists.
        $communicationprocessor = processor::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id
        );

        $this->assertEquals($communicationroomname, $communicationprocessor->get_room_name());
        $this->assertEquals($selectedcommunication, $communicationprocessor->get_provider());
    }

    /**
     * Test the create_and_configure_room method to add/create tasks when no communication provider selected.
     *
     * @covers ::create_and_configure_room
     */
    public function test_create_and_configure_room_without_communication_provider_selected(): void {
        // Get the course by disabling communication so that we can create it manually calling the api.
        $course = $this->get_course('Sampleroom', 'none');

        // Test the tasks added.
        $adhoctask = \core\task\manager::get_adhoc_tasks('\\core_communication\\task\\create_and_configure_room_task');
        $this->assertCount(0, $adhoctask);

        // Test the communication record exists.
        $communicationprocessor = processor::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id
        );

        $this->assertNull($communicationprocessor);
    }

    /**
     * Test update operation.
     *
     * @covers ::update_room
     */
    public function test_update_room(): void {
        $course = $this->get_course();

        // Sample data.
        $communicationroomname = 'Sampleroomupdated';
        $selectedcommunication = 'communication_matrix';

        $communication = \core_communication\api::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id
        );
        $communication->update_room($selectedcommunication, $communicationroomname);

        // Test the tasks added.
        $adhoctask = \core\task\manager::get_adhoc_tasks('\\core_communication\\task\\update_room_task');
        // Should be 2 as one for create, another for update.
        $this->assertCount(1, $adhoctask);

        $adhoctask = reset($adhoctask);
        $this->assertInstanceOf('\\core_communication\\task\\update_room_task', $adhoctask);

        // Test the communication record exists.
        $communicationprocessor = processor::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id
        );

        $this->assertEquals($communicationroomname, $communicationprocessor->get_room_name());
        $this->assertEquals($selectedcommunication, $communicationprocessor->get_provider());
    }

    /**
     * Test delete operation.
     *
     * @covers ::delete_room
     */
    public function test_delete_room(): void {
        $course = $this->get_course();

        // Sample data.
        $communicationroomname = 'Sampleroom';
        $selectedcommunication = 'communication_matrix';

        // Test the communication record exists.
        $communicationprocessor = processor::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id
        );

        $this->assertEquals($communicationroomname, $communicationprocessor->get_room_name());
        $this->assertEquals($selectedcommunication, $communicationprocessor->get_provider());

        $communication = \core_communication\api::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id
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
     * Test the update_room_membership for adding adn removing members.
     *
     * @covers ::add_members_to_room
     * @covers ::remove_members_from_room
     */
    public function test_update_room_membership(): void {
        $course = $this->get_course();
        $userid = $this->get_user()->id;

        // First test the adding members to a room.
        $communication = \core_communication\api::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id
        );
        $communication->add_members_to_room([$userid]);

        // Test the tasks added.
        $adhoctask = \core\task\manager::get_adhoc_tasks('\\core_communication\\task\\add_members_to_room_task');
        $this->assertCount(1, $adhoctask);

        // Now test the removing members from a room.
        $communication->remove_members_from_room([$userid]);

        // Test the tasks added.
        $adhoctask = \core\task\manager::get_adhoc_tasks('\\core_communication\\task\\remove_members_from_room');
        $this->assertCount(1, $adhoctask);
    }

    /**
     * Test the enabled communication plugin list and default.
     *
     * @covers ::get_enabled_providers_and_default
     */
    public function test_get_enabled_providers_and_default(): void {
        list($communicationproviders, $defaulprovider) = \core_communication\api::get_enabled_providers_and_default();
        // Get the communication plugins.
        $plugins = \core_component::get_plugin_list('communication');
        // Check the number of plugins matches plus 1 as we have none in the selection.
        $this->assertCount(count($plugins) + 1, $communicationproviders);
        $this->assertEquals(processor::PROVIDER_NONE, $defaulprovider);
    }
}
