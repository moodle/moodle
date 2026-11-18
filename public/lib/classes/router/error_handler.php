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

namespace core\router;

use core\exception\response_aware_exception;
use core\router\response\access_denied_response;
use core\router\response\exception_response;
use core\router\response\method_not_allowed_response;
use core\router\response\not_found_response;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;
use Slim\Handlers\ErrorHandler;

/**
 * An Error Handler implementation for Moodle which is aware of the REST API.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class error_handler extends ErrorHandler {
    #[\Override]
    protected function respond(): ResponseInterface {
        $restbase = \core\di::get(\core\router::class)->basepath . route_loader_interface::ROUTE_GROUP_API;
        $path = $this->request->getUri()->getPath();
        if ($path !== $restbase && !str_starts_with($path, "{$restbase}/")) {
            return parent::respond();
        }

        $responseclass = match (true) {
            $this->exception instanceof response_aware_exception => $this->exception->get_response_classname(),
            $this->exception instanceof HttpNotFoundException => not_found_response::class,
            $this->exception instanceof HttpForbiddenException => access_denied_response::class,
            $this->exception instanceof HttpMethodNotAllowedException => method_not_allowed_response::class,
            default => null,
        };
        if ($responseclass === null) {
            return parent::respond();
        }

        // Routing errors occur before the middleware which finishes bootstrapping Moodle.
        util::load_full_moodle();

        return $responseclass::get_response($this->request, $this->exception)->get_response($this->responseFactory);
    }

    #[\Override]
    protected function determineStatusCode(): int {
        $exception = $this->exception;

        if ($exception instanceof response_aware_exception) {
            $responseclassname = $exception->get_response_classname();
            if (is_subclass_of($responseclassname, exception_response::class)) {
                return $responseclassname::get_exception_status_code();
            }
        }

        return parent::determineStatusCode();
    }
}
