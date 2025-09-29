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

namespace core\router\middleware;

use core\router\request_validator_interface;
use core\router\response_handler;
use core\router\response_validator_interface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Middleware to handle validation of request and response based on the route data.
 *
 * @package    core
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class validation_middleware implements MiddlewareInterface {
    /**
     * Create a new instance of the validation middleware.
     *
     * @param response_handler $responsehandler A handler to standardise a response
     * @param request_validator_interface $requestvalidator A request validator
     * @param response_validator_interface $responsevalidator A response validator
     */
    public function __construct(
        /** @var response_handler A handler to standardise a response */
        protected response_handler $responsehandler,

        /** @var request_validator_interface The request validator used to validate incoming data */
        protected request_validator_interface $requestvalidator,

        /** @var response_validator_interface The response validator used to validate incoming data */
        protected response_validator_interface $responsevalidator,
    ) {
    }

    #[\Override]
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        global $USER;

        try {
            $request = $this->requestvalidator->validate_request($request);
        } catch (\Exception $e) {
            $response = $this->responsehandler->get_response_from_exception($request, $e);
            // Throw 'page not found' exception for non-admins.
            // This hides stacktrace and errorcodes in detailed payload responses.
            if (!is_siteadmin($USER->id) && $response->getStatusCode() == 404) {
                return \core\router\util::throw_page_not_found($request, $response, $response->getReasonPhrase());
            }
            return $response;
        }

        $response = $handler->handle($request);

        try {
            $this->responsevalidator->validate_response($request, $response);
        } catch (\Exception $e) {
            return $this->responsehandler->get_response_from_exception($request, $e);
        }

        return $response;
    }
}
