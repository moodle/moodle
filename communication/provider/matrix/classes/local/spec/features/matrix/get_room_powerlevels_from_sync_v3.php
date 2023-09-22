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
 * Matrix API feature to fetch room power levels using the sync API.
 *
 * https://spec.matrix.org/v1.1/client-server-api/#get_matrixclientv3sync
 *
 * @package    communication_matrix
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @codeCoverageIgnore
 * This code does not warrant being tested. Testing offers no discernible benefit given its usage is tested.
 */
trait get_room_powerlevels_from_sync_v3 {
    /**
     * Get a list of room members.
     *
     * @param string $roomid The room ID
     * @return Response
     */
    public function get_room_power_levels(string $roomid): Response {
        // Filter the event data according to the API:
        // https://spec.matrix.org/v1.1/client-server-api/#filtering
        // We have to filter out all of the object data that we do not want,
        // and set a filter to only fetch the one room that we do want.
        $filter = (object) [
            "account_data" => (object) [
                // We don't want any account info for this call.
                "not_types" => ['*'],
            ],
            "event_fields" => [
                // We only care about type, and content. Not sender.
                "type",
                "content",
            ],
            "event_format" => "client",
            "presence" => (object) [
                // We don't need any presence data.
                "not_types" => ['*'],
            ],
            "room" => (object) [
                // We only want state information for power levels, not timeline and ephemeral data.
                "rooms" => [
                    $roomid,
                ],
                "state" => (object) [
                    "types" => [
                        "m.room.power_levels",
                    ],
                ],
                "ephemeral" => (object) [
                    "not_types" => ['*'],
                ],
                "timeline" => (object) [
                    "not_types" => ['*'],
                ],
            ],
        ];

        $query = [
            'filter' => json_encode($filter),
        ];

        return $this->execute(new command(
            $this,
            method: 'GET',
            endpoint: '_matrix/client/v3/sync',
            query: $query,
            sendasjson: false,
        ));
    }
}
