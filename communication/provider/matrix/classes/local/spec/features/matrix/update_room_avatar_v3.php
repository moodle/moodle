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
 * Matrix API feature to update a room avatar.
 *
 * https://spec.matrix.org/v1.1/client-server-api/#put_matrixclientv3roomsroomidstateeventtypestatekey
 *
 * @package    communication_matrix
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @codeCoverageIgnore
 * This code does not warrant being tested. Testing offers no discernible benefit given its usage is tested.
 */
trait update_room_avatar_v3 {
    /**
     * Set the avatar for a room to the specified URL.
     *
     * @param string $roomid The roomid to set for
     * @param null|string $avatarurl The mxc URL to use
     * @return Response
     */
    public function update_room_avatar(
        string $roomid,
        ?string $avatarurl,
    ): Response {
        $params = [
            ':roomid' => $roomid,
            'url' => $avatarurl,
        ];

        return $this->execute(new command(
            $this,
            method: 'PUT',
            endpoint: '_matrix/client/v3/rooms/:roomid/state/m.room.avatar',
            ignorehttperrors: true,
            params: $params,
        ));
    }
}
