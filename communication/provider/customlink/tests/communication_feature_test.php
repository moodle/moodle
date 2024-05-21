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

namespace communication_customlink;

use core_communication\processor;
use core_communication\communication_test_helper_trait;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../../../tests/communication_test_helper_trait.php');

/**
 * Class communication_feature_test to test the custom link features implemented using the core interfaces.
 *
 * @package    communication_customlink
 * @category   test
 * @copyright  2023 Michael Hawkins <michaelh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \communication_customlink\communication_feature
 */
class communication_feature_test extends \advanced_testcase {
    use communication_test_helper_trait;

    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        $this->setup_communication_configs();
    }

    /**
     * Test create, update and delete chat room.
     *
     * @covers ::load_for_instance
     */
    public function test_load_for_instance(): void {
        $communicationprocessor = $this->get_test_communication_processor();

        $instance = communication_feature::load_for_instance($communicationprocessor);
        $this->assertInstanceOf('communication_customlink\communication_feature', $instance);
    }

    /**
     * Test create, update and delete chat room.
     *
     * @covers ::create_chat_room
     * @covers ::update_chat_room
     * @covers ::delete_chat_room
     */
    public function test_create_update_delete_chat_room(): void {
        $communicationprocessor = $this->get_test_communication_processor();

        // Create, update and delete room should always return true because this provider contains
        // a link to a room, but does not manage the existence of the room.
        $createroomresult = $communicationprocessor->get_room_provider()->create_chat_room();
        $updateroomresult = $communicationprocessor->get_room_provider()->update_chat_room();
        $deleteroomresult = $communicationprocessor->get_room_provider()->delete_chat_room();
        $this->assertTrue($createroomresult);
        $this->assertTrue($updateroomresult);
        $this->assertTrue($deleteroomresult);
    }

    /**
     * Test save form data with provider's custom field and fetching with get_chat_room_url().
     *
     * @covers ::save_form_data
     * @covers ::get_chat_room_url
     */
    public function test_save_form_data(): void {
        $communicationprocessor = $this->get_test_communication_processor();
        $customlinkurl = 'https://moodle.org/message/index.php';
        $formdatainstance = (object) ['customlinkurl' => $customlinkurl];

        // Test the custom link URL is saved and can be retrieved as expected.
        $communicationprocessor->get_form_provider()->save_form_data($formdatainstance);
        $fetchedurl = $communicationprocessor->get_room_provider()->get_chat_room_url();
        $this->assertEquals($customlinkurl, $fetchedurl);

        // Test with empty customlinkurl.
        $customlinkurlempty = '';
        $formdatainstance = (object) ['customlinkurl' => $customlinkurlempty];
        $communicationprocessor->get_form_provider()->save_form_data($formdatainstance);
        $fetchedurl = $communicationprocessor->get_room_provider()->get_chat_room_url();
        // It should not update the url to an empty one.
        $this->assertEquals($customlinkurl, $fetchedurl);

        // Test with null customlinkurl.
        $customlinkurlempty = null;
        $formdatainstance = (object) ['customlinkurl' => $customlinkurlempty];
        $communicationprocessor->get_form_provider()->save_form_data($formdatainstance);
        $fetchedurl = $communicationprocessor->get_room_provider()->get_chat_room_url();
        // It should not update the url to a null one.
        $this->assertEquals($customlinkurl, $fetchedurl);
    }

    /**
     * Create a test custom link communication processor object.
     *
     * @return processor
     */
    protected function get_test_communication_processor(): processor {
        $course = $this->getDataGenerator()->create_course();
        $instanceid = $course->id;
        $context = \core\context\system::instance();
        $component = 'core_course';
        $instancetype = 'coursecommunication';
        $selectedcommunication = 'communication_customlink';
        $communicationroomname = 'communicationroom';

        $communicationprocessor = processor::create_instance(
            $context,
            $selectedcommunication,
            $instanceid,
            $component,
            $instancetype,
            $communicationroomname,
        );

        return $communicationprocessor;
    }

    /**
     * Test if the selected provider is configured.
     *
     * @covers ::is_configured
     */
    public function test_is_configured(): void {
        $communicationprocessor = $this->get_test_communication_processor();
        $this->assertTrue($communicationprocessor->get_form_provider()->is_configured());
    }
}
