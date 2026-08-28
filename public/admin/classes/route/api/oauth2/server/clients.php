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

namespace core_admin\route\api\oauth2\server;

use core\router\require_login;
use core\router\route;
use core\router\schema\response\payload_response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * REST API routes for OAuth2 server clients.
 *
 * @package    core_admin
 * @copyright  2026 Mihail Gehoski <mihailgesoski@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class clients {
    /**
     * Revoke a client.
     *
     * @param ServerRequestInterface $request The request object
     * @param ResponseInterface $response The response object
     * @param \core\oauth2\server\entity\client_entity $cliententity The client entity
     * @return payload_response The response object with the success status
     */
    #[route(
        path: '/oauth2/server/clients/{client}/revoke',
        method: ['POST'],
        pathtypes: [
            new \core_admin\route\parameters\oauth2\server\path_client(),
        ],
        requirelogin: new require_login(
            requirelogin: true,
            autologinguest: false,
        ),
    )]
    public function revoke_client(
        ServerRequestInterface $request,
        ResponseInterface $response,
        \core\oauth2\server\entity\client_entity $cliententity,
    ): payload_response {
        require_capability('moodle/site:manageoauth2clients', \core\context\system::instance());

        $manager = \core\di::get(\core\oauth2\server\client_manager::class);
        $manager->revoke_client($cliententity->get_id());

        return new payload_response(
            payload: [
                'success' => true,
            ],
            request: $request,
            response: $response,
        );
    }

    /**
     * Reactivate a revoked client.
     *
     * @param ServerRequestInterface $request The request object
     * @param ResponseInterface $response The response object
     * @param \core\oauth2\server\entity\client_entity $cliententity The client entity
     * @return payload_response The response payload indicating success
     */
    #[route(
        path: '/oauth2/server/clients/{client}/reactivate',
        method: ['POST'],
        pathtypes: [
            new \core_admin\route\parameters\oauth2\server\path_client(),
        ],
        requirelogin: new require_login(
            requirelogin: true,
            autologinguest: false,
        ),
    )]
    public function reactivate_client(
        ServerRequestInterface $request,
        ResponseInterface $response,
        \core\oauth2\server\entity\client_entity $cliententity,
    ): payload_response {
        require_capability('moodle/site:manageoauth2clients', \core\context\system::instance());

        $manager = \core\di::get(\core\oauth2\server\client_manager::class);
        $manager->reactivate_client($cliententity->get_id());

        return new payload_response(
            payload: [
                'success' => true,
            ],
            request: $request,
            response: $response,
        );
    }

    /**
     * Delete a client.
     *
     * @param ServerRequestInterface $request The request object
     * @param ResponseInterface $response The response object
     * @param \core\oauth2\server\entity\client_entity $cliententity The client entity
     * @return payload_response The response payload indicating success
     */
    #[route(
        path: '/oauth2/server/clients/{client}/delete',
        method: ['POST'],
        pathtypes: [
            new \core_admin\route\parameters\oauth2\server\path_client(),
        ],
        requirelogin: new require_login(
            requirelogin: true,
            autologinguest: false,
        ),
    )]
    public function delete_client(
        ServerRequestInterface $request,
        ResponseInterface $response,
        \core\oauth2\server\entity\client_entity $cliententity,
    ): payload_response {
        require_capability('moodle/site:manageoauth2clients', \core\context\system::instance());

        $manager = \core\di::get(\core\oauth2\server\client_manager::class);
        $manager->delete_client($cliententity->get_id());

        return new payload_response(
            payload: [
                'success' => true,
            ],
            request: $request,
            response: $response,
        );
    }
}
