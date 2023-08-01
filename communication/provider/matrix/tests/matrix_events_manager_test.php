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

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/matrix_test_helper_trait.php');

/**
 * Class matrix_events_manager_test to test the matrix events endpoint.
 *
 * @package    communication_matrix
 * @category   test
 * @copyright  2023 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \communication_matrix\matrix_events_manager
 */
class matrix_events_manager_test extends \advanced_testcase {

    use matrix_test_helper_trait;

    public function setUp(): void {
        parent::setUp();
        $this->initialise_mock_server();
    }

    /**
     * Test the api endpoints url's for matrix.
     *
     * @return void
     * @covers ::get_token
     * @covers ::get_update_avatar_endpoint
     * @covers ::get_update_room_topic_endpoint
     * @covers ::get_update_room_name_endpoint
     * @covers ::get_create_room_endpoint
     * @covers ::get_delete_room_endpoint
     * @covers ::get_upload_content_endpoint
     */
    public function test_matrix_api_endpoints(): void {
        $this->resetAfterTest();
        $mockroomid = 'sampleroomid';
        $mockuserid = 'sampleuserid';

        $matrixeventsmanager = new matrix_events_manager($mockroomid);

        // Test the endpoints and information.
        $this->assertEquals($this->get_matrix_access_token(), $matrixeventsmanager->get_token());

        $this->assertEquals($this->get_matrix_server_url() . '/' . '_matrix/client/r0/createRoom',
            $matrixeventsmanager->get_create_room_endpoint());

        $this->assertEquals($this->get_matrix_server_url() . '/' . '_matrix/client/r0/rooms' .
            '/' . urlencode($mockroomid) . '/' . 'state/m.room.topic/',
            $matrixeventsmanager->get_update_room_topic_endpoint());

        $this->assertEquals($this->get_matrix_server_url(). '/' . '_matrix/client/r0/rooms' .
            '/' . urlencode($mockroomid) . '/' . 'state/m.room.name/',
            $matrixeventsmanager->get_update_room_name_endpoint());

        $this->assertEquals($this->get_matrix_server_url(). '/' . '_synapse/admin/v1/rooms' .
            '/' . urlencode($mockroomid), $matrixeventsmanager->get_room_info_endpoint());

        $this->assertEquals($this->get_matrix_server_url() . '/' . '_synapse/admin/v1/rooms/' . urlencode($mockroomid),
            $matrixeventsmanager->get_delete_room_endpoint());

        $this->assertEquals($this->get_matrix_server_url() . '/' . '_matrix/media/r0/upload/',
            $matrixeventsmanager->get_upload_content_endpoint());

        $this->assertEquals($this->get_matrix_server_url() . '/' . '_matrix/client/r0/rooms' .
            '/' . urlencode($mockroomid) . '/' . 'state/m.room.avatar/',
            $matrixeventsmanager->get_update_avatar_endpoint());

        $this->assertEquals($this->get_matrix_server_url() . '/' . '_matrix/client/r0/rooms' .
            '/' . urlencode($mockroomid) . '/' . 'joined_members',
            $matrixeventsmanager->get_room_membership_joined_endpoint());

        $this->assertEquals($this->get_matrix_server_url() . '/' . '_synapse/admin/v1/join' .
            '/' . urlencode($mockroomid),
            $matrixeventsmanager->get_room_membership_join_endpoint());

        $this->assertEquals($this->get_matrix_server_url() . '/' . '_matrix/client/r0/rooms' .
            '/' . urlencode($mockroomid) . '/' . 'kick',
            $matrixeventsmanager->get_room_membership_kick_endpoint());

        $this->assertEquals($this->get_matrix_server_url() . '/' . '_synapse/admin/v2/users/' . urlencode($mockuserid),
            $matrixeventsmanager->get_create_user_endpoint($mockuserid));

        $this->assertEquals($this->get_matrix_server_url() . '/' . '_synapse/admin/v2/users/' . urlencode($mockuserid),
            $matrixeventsmanager->get_user_info_endpoint($mockuserid));
    }
}
