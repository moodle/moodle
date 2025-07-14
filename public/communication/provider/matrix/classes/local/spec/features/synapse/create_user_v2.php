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
 * Synapse API feature for creating a user.
 *
 * https://matrix-org.github.io/synapse/latest/admin_api/user_admin_api.html#create-or-modify-account
 *
 * @package    communication_matrix
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @codeCoverageIgnore
 * This code does not warrant being tested. Testing offers no discernible benefit given its usage is tested.
 */
trait create_user_v2 {
    /**
     * Create a new user.
     *
     * @param string $userid The Matrix user id.
     * @param string $displayname The visible name of the user
     * @param array $threepids The third-party identifiers of the user.
     * @param null|array $externalids
     */
    public function create_user(
        string $userid,
        string $displayname,
        array $threepids,
        ?array $externalids = null,
    ): Response {
        $params = [
            ':userid' => $userid,
            'displayname' => $displayname,
            'threepids' => $threepids,
        ];

        if ($externalids !== null) {
            $params['externalids'] = $externalids;
        }

        return $this->execute(new command(
            $this,
            method: 'PUT',
            endpoint: '_synapse/admin/v2/users/:userid',
            params: $params,
        ));
    }
}
