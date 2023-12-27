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
use communication_matrix\matrix_constants;
use GuzzleHttp\Psr7\Response;

/**
 * Matrix API feature to update a room power levels.
 *
 * Matrix rooms have a concept of power levels, which are used to determine what actions a user can perform in a room.
 *
 * https://spec.matrix.org/v1.1/client-server-api/#mroompower_levels
 *
 * @package    communication_matrix
 * @copyright  2023 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @codeCoverageIgnore
 * This code does not warrant being tested. Testing offers no discernible benefit given its usage is tested.
 */
trait update_room_power_levels_v3 {
    /**
     * Set the avatar for a room to the specified URL.
     *
     * @param string $roomid The roomid to set for
     * @param array $users The users to set power levels for
     * @param int $ban The level required to ban a user
     * @param int $invite The level required to invite a user
     * @param int $kick The level required to kick a user
     * @param array $notifications The level required to send notifications
     * @param int $redact The level required to redact events
     * @return Response
     */
    public function update_room_power_levels(
        string $roomid,
        array $users,
        int $ban = matrix_constants::POWER_LEVEL_MAXIMUM,
        int $invite = matrix_constants::POWER_LEVEL_MODERATOR,
        int $kick = matrix_constants::POWER_LEVEL_MODERATOR,
        array $notifications = [
            'room' => matrix_constants::POWER_LEVEL_MODERATOR,
        ],
        int $redact = matrix_constants::POWER_LEVEL_MODERATOR,
    ): Response {
        $params = [
            ':roomid' => $roomid,
            'ban' => $ban,
            'invite' => $invite,
            'kick' => $kick,
            'notifications' => $notifications,
            'redact' => $redact,
            'users' => $users,
        ];

        return $this->execute(new command(
            $this,
            method: 'PUT',
            endpoint: '_matrix/client/v3/rooms/:roomid/state/m.room.power_levels',
            params: $params,
        ));
    }
}
