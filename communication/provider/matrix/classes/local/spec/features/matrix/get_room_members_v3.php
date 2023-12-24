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

namespace communication_matrix\local\spec\features\matrix;

use communication_matrix\local\command;
use GuzzleHttp\Psr7\Response;

/**
 * Matrix API feature to fetch a list of room members.
 *
 * https://spec.matrix.org/v1.1/client-server-api/#get_matrixclientv3roomsroomidjoined_members
 *
 * @package    communication_matrix
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @codeCoverageIgnore
 * This code does not warrant being tested. Testing offers no discernible benefit given its usage is tested.
 */
trait get_room_members_v3 {
    /**
     * Get a list of room members.
     *
     * @param string $roomid The room ID
     * @return Response
     */
    public function get_room_members(string $roomid): Response {
        $params = [
            ':roomid' => $roomid,
        ];

        return $this->execute(new command(
            $this,
            method: 'GET',
            endpoint: '_matrix/client/v3/rooms/:roomid/joined_members',
            params: $params,
        ));
    }
}
