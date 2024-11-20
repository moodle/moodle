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

use core\router\response_handler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Middleware to handle errors in a route callable.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class error_handling_middleware implements MiddlewareInterface {
    /**
     * Create a new instance of the error handling middleware.
     *
     * @param response_handler $responsehandler A handler to standardise a response
     */
    public function __construct(
        /** @var response_handler A handler to standardise a response */
        protected response_handler $responsehandler,
    ) {
    }

    #[\Override]
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        try {
            $response = $handler->handle($request);
        } catch (\Exception $e) {
            // @codeCoverageIgnoreStart
            if (defined('ABORT_AFTER_CONFIG') && !defined('ABORT_AFTER_CONFIG_CANCEL')) {
                define('ABORT_AFTER_CONFIG_CANCEL', true);
                require(__DIR__ . '/../../../setup.php');
            }
            // @codeCoverageIgnoreEnd

            $response = $this->responsehandler->get_response_from_exception($request, $e);
        }

        return $response;
    }
}
