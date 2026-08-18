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

use core\oauth2\server\entity\client_entity;
use core\router\require_login;
use core\router\route;
use core\router\schema\response\payload_response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * REST API routes for OAuth2 secrets.
 *
 * @package    core_admin
 * @copyright  2026 Mihail Gehoski <mihailgesoski@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class client_secrets {
    /**
     * Create a new client secret.
     *
     * @param ServerRequestInterface $request The request object
     * @param ResponseInterface $response The response object
     * @param client_entity $cliententity The client entity
     * @return payload_response The client secret
     */
    #[route(
        path: '/oauth2/server/clients/{client}/secrets/create',
        method: ['POST'],
        pathtypes: [
            new \core_admin\route\parameters\oauth2\server\path_client(),
        ],
        requirelogin: new require_login(
            requirelogin: true,
            autologinguest: false,
        ),
    )]
    public function create_secret(
        ServerRequestInterface $request,
        ResponseInterface $response,
        client_entity $cliententity,
    ): payload_response {
        require_capability('moodle/site:manageoauth2clients', \core\context\system::instance());

        $manager = \core\di::get(\core\oauth2\server\client_manager::class);
        $secret = $manager->create_secret($cliententity->get_id());

        return new payload_response(
            payload: [
                'secret' => $secret,
            ],
            request: $request,
            response: $response,
        );
    }

    /**
     * Get client secrets.
     *
     * @param ServerRequestInterface $request The request object
     * @param ResponseInterface $response The response object
     * @param client_entity $cliententity The client entity
     * @return payload_response The list of client secret records
     */
    #[route(
        path: '/oauth2/server/clients/{client}/secrets',
        method: ['GET'],
        pathtypes: [
            new \core_admin\route\parameters\oauth2\server\path_client(),
        ],
        queryparams: [
            new \core\router\schema\parameters\query_parameter(
                name: 'includeinactive',
                type: \core\param::BOOL,
                description: 'Whether to include inactive secrets',
                default: false,
            ),
        ],
        requirelogin: new require_login(
            requirelogin: true,
            autologinguest: false,
        ),
    )]
    public function get_client_secrets(
        ServerRequestInterface $request,
        ResponseInterface $response,
        \core\oauth2\server\entity\client_entity $cliententity,
    ): payload_response {
        require_capability('moodle/site:manageoauth2clients', \core\context\system::instance());

        $manager = \core\di::get(\core\oauth2\server\client_manager::class);
        $secrets = $manager->get_secrets($cliententity->get_id(), $request->getQueryParams()['includeinactive'] ?? false);

        return new payload_response(
            payload: [
                'secrets' => $secrets,
            ],
            request: $request,
            response: $response,
        );
    }

    /**
     * Revoke client secret.
     *
     * @param ServerRequestInterface $request The request object
     * @param ResponseInterface $response The response object
     * @return payload_response The revocation status
     */
    #[route(
        path: '/oauth2/server/secrets/revoke',
        method: ['POST'],
        requestbody: new \core\router\schema\request_body(
            content: [
                new \core\router\schema\response\content\json_media_type(
                    schema: new \core\router\schema\objects\schema_object(
                        content: [
                            'secretid' => new \core\router\schema\objects\scalar_type(
                                type: \core\param::INT,
                                required: true,
                            ),
                        ],
                    ),
                ),
            ],
            description: 'Revocation parameters payload',
            required: true,
        ),
        requirelogin: new require_login(
            requirelogin: true,
            autologinguest: false,
        ),
    )]
    public function revoke_client_secret(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): payload_response {
        require_capability('moodle/site:manageoauth2clients', \core\context\system::instance());

        $body = $request->getParsedBody();
        $secretid = (int) $body['secretid'];

        $manager = \core\di::get(\core\oauth2\server\client_manager::class);
        $manager->revoke_secret($secretid);

        return new payload_response(
            payload: [
                'success' => true,
            ],
            request: $request,
            response: $response,
        );
    }
}
