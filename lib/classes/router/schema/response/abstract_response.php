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

namespace core\router\schema\response;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * An abstract response to a request.
 *
 * This approach is inspired and based upon slim-routing https://github.com/juliangut/slim-routing
 * We only need a fraction of this functionality.
 *
 * @package    core
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class abstract_response implements response_type {
    /**
     * Create a new abstract response.
     *
     * @param ServerRequestInterface $request The request
     * @param ResponseInterface|null $response The response
     */
    public function __construct(
        /** @var ServerRequestInterface The Request */
        public readonly ServerRequestInterface $request,
        /** @var ResponseInterface|null The Response */
        public readonly ?ResponseInterface $response = null,
    ) {
    }

    #[\Override]
    public function get_request(): ServerRequestInterface {
        return $this->request;
    }

    #[\Override]
    public function get_response(
        ResponseFactoryInterface $responsefactory,
    ): ?ResponseInterface {
        return $this->response ?? $responsefactory->createResponse();
    }
}
