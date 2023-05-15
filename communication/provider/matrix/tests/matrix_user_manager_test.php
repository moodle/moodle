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
 * Class matrix_user_manager_test to test the matrix user manager.
 *
 * @package    communication_matrix
 * @category   test
 * @copyright  2023 Stevani Andolo <stevani.andolo@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \communication_matrix\matrix_user_manager
 */
class matrix_user_manager_test extends \advanced_testcase {

    use matrix_test_helper_trait;
    use communication_test_helper_trait;

    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        $this->setup_communication_configs();
        $this->initialise_mock_server();
    }

    /**
     * Test get matrix id from moodle.
     *
     * @covers ::get_matrixid_from_moodle
     */
    public function test_get_matrixid_from_moodle(): void {
        $course = $this->get_course();
        $userid = $this->get_user()->id;

        // Run room operation task.
        $this->runAdhocTasks('\core_communication\task\create_and_configure_room_task');

        $communication = \core_communication\api::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id
        );
        $communication->add_members_to_room([$userid]);

        $this->runAdhocTasks('\core_communication\task\add_members_to_room_task');

        $communicationprocessor = processor::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id
        );

        $matrixrooms = new matrix_rooms($communicationprocessor->get_id());
        $eventmanager = new matrix_events_manager($matrixrooms->get_matrix_room_id());
        $matrixhomeserverurl = $eventmanager->matrixhomeserverurl;

        // Get created matrixuserid from moodle.
        $elementserver = matrix_user_manager::set_matrix_home_server($matrixhomeserverurl);
        $matrixuserid = matrix_user_manager::get_matrixid_from_moodle($userid, $matrixhomeserverurl);
        $this->assertNotNull($matrixuserid);
        $this->assertEquals("@sampleun:{$elementserver}", $matrixuserid);
    }

    /**
     * Sets qualified matrix user id.
     *
     * @return void
     * @covers ::set_qualified_matrix_user_id
     */
    public function test_set_qualified_matrix_user_id(): void {

        $course = $this->get_course();
        $user = $this->get_user();

        // Run room operation task.
        $this->runAdhocTasks('\core_communication\task\create_and_configure_room_task');

        $communication = \core_communication\api::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id
        );
        $communication->add_members_to_room([$user->id]);

        $this->runAdhocTasks('\core_communication\task\add_members_to_room_task');

        $communicationprocessor = processor::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id
        );

        $matrixrooms = new matrix_rooms($communicationprocessor->get_id());
        $eventmanager = new matrix_events_manager($matrixrooms->get_matrix_room_id());
        $matrixhomeserverurl = $eventmanager->matrixhomeserverurl;
        $elementserver = matrix_user_manager::set_matrix_home_server($matrixhomeserverurl);

        // Sets qualified matrix id test1.
        list($matrixuserid, $pureusername) = matrix_user_manager::set_qualified_matrix_user_id($user->id, $matrixhomeserverurl);
        $this->assertEquals("@{$user->username}:{$elementserver}", $matrixuserid);
        $this->assertEquals("sampleun", $pureusername);

        // Sets qualified matrix id test2.
        $user = $this->get_user('moodlefn', 'moodleln', 'admin@moodle.com');
        list($matrixuserid, $pureusername) = matrix_user_manager::set_qualified_matrix_user_id($user->id, $matrixhomeserverurl);
        $this->assertEquals("@admin.moodle.com:{$elementserver}", $matrixuserid);
        $this->assertEquals("admin.moodle.com", $pureusername);

        // Sets qualified matrix id test3.
        $user = $this->get_user('moodlefn', 'moodleln', 'admin-user@moodle.com');
        list($matrixuserid, $pureusername) = matrix_user_manager::set_qualified_matrix_user_id($user->id, $matrixhomeserverurl);
        $this->assertEquals("@admin-user.moodle.com:{$elementserver}", $matrixuserid);
        $this->assertEquals("admin-user.moodle.com", $pureusername);
    }

    /**
     * Add user's matrix id to moodle.
     *
     * @covers ::add_user_matrix_id_to_moodle
     */
    public function test_add_user_matrix_id_to_moodle(): void {

        $course = $this->get_course();
        $userid = $this->get_user()->id;

        // Run room operation task.
        $this->runAdhocTasks('\core_communication\task\create_and_configure_room_task');

        $communication = \core_communication\api::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id
        );
        $communication->add_members_to_room([$userid]);

        $this->runAdhocTasks('\core_communication\task\add_members_to_room_task');

        $communicationprocessor = processor::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id
        );

        $matrixrooms = new matrix_rooms($communicationprocessor->get_id());
        $eventmanager = new matrix_events_manager($matrixrooms->get_matrix_room_id());

        // Sets qualified matrix id.
        list($qualifiedmuid, $pureusername) = matrix_user_manager::set_qualified_matrix_user_id(
            $userid,
            $eventmanager->matrixhomeserverurl
        );
        $this->assertNotNull($qualifiedmuid);
        $this->assertNotNull($pureusername);

        // Will return true on success.
        $this->assertTrue(matrix_user_manager::add_user_matrix_id_to_moodle($userid, $pureusername));

        // Get created matrixuserid from moodle.
        $elementserver = matrix_user_manager::set_matrix_home_server($eventmanager->matrixhomeserverurl);
        $matrixuserid = matrix_user_manager::get_matrixid_from_moodle($userid, $eventmanager->matrixhomeserverurl);
        $this->assertNotNull($matrixuserid);
        $this->assertEquals("@sampleun:{$elementserver}", $matrixuserid);
    }

    /**
     * Add matrix home server for qualified matrix id.
     *
     * @return void
     * @covers ::set_matrix_home_server
     */
    public function test_set_matrix_home_server(): void {

        $course = $this->get_course();
        $userid = $this->get_user()->id;

        // Run room operation task.
        $this->runAdhocTasks('\core_communication\task\create_and_configure_room_task');

        $communication = \core_communication\api::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id
        );
        $communication->add_members_to_room([$userid]);

        $this->runAdhocTasks('\core_communication\task\add_members_to_room_task');

        $communicationprocessor = processor::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id
        );

        $matrixrooms = new matrix_rooms($communicationprocessor->get_id());
        $eventmanager = new matrix_events_manager($matrixrooms->get_matrix_room_id());

        // Will generate matrix home server.
        $generatedhomeserver = matrix_user_manager::set_matrix_home_server($eventmanager->matrixhomeserverurl);
        $this->assertNotNull($generatedhomeserver);
    }

    /**
     * Test post matrix insert new user field record.
     *
     * @covers ::execute
     */
    public function test_create_matrix_user_profile_fields(): void {
        $course = $this->get_course();
        $userid = $this->get_user()->id;

        // Run room operation task.
        $this->runAdhocTasks('\core_communication\task\create_and_configure_room_task');

        $communication = \core_communication\api::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id
        );
        $communication->add_members_to_room([$userid]);

        $this->runAdhocTasks('\core_communication\task\add_members_to_room_task');

        // Check if "Communication" field has been added.
        $categoryfield = get_config('core_communication', 'communication_category_field');
        $this->assertNotNull($categoryfield);
        $this->assertEquals('Communication', $categoryfield);

        // Check if "matrixuserid" field has been added.
        $infofield = get_config('communication_matrix', 'matrixuserid_field');
        $this->assertNotNull($infofield);
        $this->assertEquals('matrixuserid', $infofield);
    }
}
