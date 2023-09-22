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

/**
 * Tests for the matrix_room class.
 *
 * @package    communication_matrix
 * @category   test
 * @copyright  2023 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \communication_matrix\matrix_room
 */
class matrix_room_test extends \advanced_testcase {
    /**
     * Test for load_by_processor_id with no record.
     *
     * @covers ::load_by_processor_id
     */
    public function test_load_by_processor_id_none(): void {
        $this->assertNull(matrix_room::load_by_processor_id(999999999));
    }

    /**
     * Test for load_by_processor_id with valid records.
     *
     * @covers ::create_room_record
     * @covers ::__construct
     * @covers ::load_by_processor_id
     * @covers ::get_processor_id
     * @covers ::get_room_id
     * @covers ::get_topic
     */
    public function test_create_room_record(): void {
        $this->resetAfterTest();

        $room = matrix_room::create_room_record(
            processorid: 10000,
            topic: null,
        );
        $this->assertInstanceOf(matrix_room::class, $room);
        $this->assertEquals(10000, $room->get_processor_id());
        $this->assertNotNull('', $room->get_topic());
        $this->assertEquals('', $room->get_topic());
        $this->assertNull($room->get_room_id());

        $room = matrix_room::create_room_record(
            processorid: 12345,
            topic: 'The topic of this room is thusly',
        );

        $this->assertInstanceOf(matrix_room::class, $room);
        $this->assertEquals(12345, $room->get_processor_id());
        $this->assertEquals('The topic of this room is thusly', $room->get_topic());
        $this->assertNull($room->get_room_id());

        $room = matrix_room::create_room_record(
            processorid: 54321,
            topic: 'The topic of this room is thusly',
            roomid: 'This is a roomid',
        );

        $this->assertInstanceOf(matrix_room::class, $room);
        $this->assertEquals(54321, $room->get_processor_id());
        $this->assertEquals('The topic of this room is thusly', $room->get_topic());
        $this->assertEquals('This is a roomid', $room->get_room_id());

        $reloadedroom = matrix_room::load_by_processor_id(54321);
        $this->assertEquals(54321, $reloadedroom->get_processor_id());
        $this->assertEquals('The topic of this room is thusly', $reloadedroom->get_topic());
        $this->assertEquals('This is a roomid', $reloadedroom->get_room_id());
    }

    /**
     * Test for update_room_record.
     *
     * @covers ::update_room_record
     */
    public function test_update_room_record(): void {
        $this->resetAfterTest();

        $room = matrix_room::create_room_record(
            processorid: 12345,
            topic: 'The topic of this room is that',
        );

        // Add a roomid.
        $room->update_room_record(
            roomid: 'This is a roomid',
        );

        $this->assertEquals('This is a roomid', $room->get_room_id());
        $this->assertEquals('The topic of this room is that', $room->get_topic());
        $this->assertEquals(12345, $room->get_processor_id());

        // Alter the roomid and topic.
        $room->update_room_record(
            roomid: 'updatedRoomId',
            topic: 'updatedTopic is here',
        );

        $this->assertEquals('updatedRoomId', $room->get_room_id());
        $this->assertEquals('updatedTopic is here', $room->get_topic());
        $this->assertEquals(12345, $room->get_processor_id());
    }

    /**
     * Tests for delete_room_record.
     *
     * @covers ::delete_room_record
     */
    public function test_delete_room_record(): void {
        global $DB;

        $this->resetAfterTest();

        $room = matrix_room::create_room_record(
            processorid: 12345,
            topic: 'The topic of this room is that',
        );
        $this->assertCount(1, $DB->get_records('matrix_room'));

        $room->delete_room_record();
        $this->assertCount(0, $DB->get_records('matrix_room'));
    }
}
