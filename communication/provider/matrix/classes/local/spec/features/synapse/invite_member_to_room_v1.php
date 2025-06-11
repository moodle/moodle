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

namespace communication_matrix\local\spec\features\synapse;

use communication_matrix\local\command;
use GuzzleHttp\Psr7\Response;

/**
 * Synapse API feature to invite a user into a room.
 *
 * https://matrix-org.github.io/synapse/latest/admin_api/room_membership.html#edit-room-membership-api
 *
 * @package    communication_matrix
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @codeCoverageIgnore
 * This code does not warrant being tested. Testing offers no discernible benefit given its usage is tested.
 */
trait invite_member_to_room_v1 {
    /**
     * Join a user to a room.
     *
     * Note: This joins the user, and does not invite them.
     *
     * @param string $roomid
     * @param string $userid
     * @return Response
     */
    public function invite_member_to_room(string $roomid, string $userid): Response {
        $params = [
            ':roomid' => $roomid,
            'user_id' => $userid,
        ];

        return $this->execute(new command(
            $this,
            method: 'POST',
            endpoint: '_synapse/admin/v1/join/:roomid',
            params: $params,
        ));
    }
}
