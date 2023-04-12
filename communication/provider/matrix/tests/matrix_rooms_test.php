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
 * Class matrix_rooms_test to test the matrix room data in db.
 *
 * @package    communication_matrix
 * @category   test
 * @copyright  2023 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \communication_matrix\matrix_rooms
 */
class matrix_rooms_test extends \advanced_testcase {

    use matrix_test_helper_trait;
    use communication_test_helper_trait;

    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        $this->setup_communication_configs();
    }

    /**
     * Test the matrix room creation in database.
     *
     * @covers ::create_matrix_room_record
     */
    public function test_create_matrix_room_record(): void {
        global $DB;
        $course = $this->get_course();

        $sampleroomid = 'samplematrixroomid';
        $sampleroomtopic = 'samplematrixroomtopic';

        // Communication internal api call.
        $communicationprocessor = processor::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id
        );

        // Call matrix room object to create the matrix data.
        $matrixroom = new \communication_matrix\matrix_rooms($communicationprocessor->get_id());
        $matrixroom->update_matrix_room_record(
            $sampleroomid,
            $sampleroomtopic
        );

        // Test the object.
        $this->assertEquals($matrixroom->get_matrix_room_id(), $sampleroomid);

        // Get the record from db.
        $matrixrecord = $DB->get_record('matrix_rooms',
            ['commid' => $communicationprocessor->get_id()]);

        // Check the record against sample data.
        $this->assertNotEmpty($matrixrecord);
        $this->assertEquals($sampleroomid, $matrixrecord->roomid);
        $this->assertEquals($communicationprocessor->get_id(), $matrixrecord->commid);
    }

    /**
     * Test matrix room record updates.
     *
     * @covers ::update_matrix_room_record
     */
    public function test_update_matrix_room_record(): void {
        global $DB;
        $course = $this->get_course();

        $sampleroomid = 'samplematrixroomid';
        $sampleroomtopic = 'samplematrixroomtopic';

        // Communication internal api call.
        $communicationprocessor = processor::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id
        );

        // Call matrix room object to create the matrix data.
        $matrixroom = new \communication_matrix\matrix_rooms($communicationprocessor->get_id());
        $matrixroom->update_matrix_room_record(
            $sampleroomid,
            $sampleroomtopic
        );

        // Get the record from db.
        $matrixrecord = $DB->get_record('matrix_rooms',
            ['commid' => $communicationprocessor->get_id()]);

        // Check the record against sample data.
        $this->assertNotEmpty($matrixrecord);

        $sampleroomidupdated = 'samplematrixroomidupdated';

        $matrixroom->update_matrix_room_record(
            $sampleroomidupdated,
            $sampleroomtopic
        );

        // Test the object.
        $this->assertEquals($matrixroom->get_matrix_room_id(), $sampleroomidupdated);

        // Get the record from db.
        $matrixrecord = $DB->get_record('matrix_rooms',
            ['commid' => $communicationprocessor->get_id()]);

        // Check the record against sample data.
        $this->assertNotEmpty($matrixrecord);
        $this->assertEquals($sampleroomidupdated, $matrixrecord->roomid);
        $this->assertEquals($communicationprocessor->get_id(), $matrixrecord->commid);
    }

    /**
     * Test matrix room deletion.
     *
     * @covers ::delete_matrix_room_record
     * @covers ::get_matrix_room_id
     */
    public function test_delete_matrix_room_record(): void {
        global $DB;
        $course = $this->get_course();

        $sampleroomid = 'samplematrixroomid';
        $sampleroomtopic = 'samplematrixroomtopic';

        // Communication internal api call.
        $communicationprocessor = processor::load_by_instance(
            'core_course',
            'coursecommunication',
            $course->id
        );

        // Call matrix room object to create the matrix data.
        $matrixroom = new \communication_matrix\matrix_rooms($communicationprocessor->get_id());
        $matrixroom->update_matrix_room_record(
            $sampleroomid,
            $sampleroomtopic
        );

        // Get the record from db.
        $matrixrecord = $DB->get_record('matrix_rooms',
            ['commid' => $communicationprocessor->get_id()]);

        // Check the record against sample data.
        $this->assertNotEmpty($matrixrecord);

        // Now delete the record.
        $matrixroom->delete_matrix_room_record();

        // Get the record from db.
        $matrixrecord = $DB->get_record('matrix_rooms',
            ['commid' => $communicationprocessor->get_id()]);

        // Check the record against sample data.
        $this->assertEmpty($matrixrecord);
    }
}
