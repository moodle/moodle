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

namespace core\route\controller;

use Psr\Http\Message\ResponseInterface;

/**
 * Test controller to provide a route for testing purposes.
 *
 * This set of routes is primarily intended for use with the Environment Checks
 * to help administrators ensure that the Routing system is correctly configured.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class test_controller {
    /**
     * Test controller route.
     *
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    #[\core\router\route(
        path: '/check/controller/test',
    )]
    public function test_action(ResponseInterface $response): ResponseInterface {
        $response->getBody()->write('Test controller response');
        return $response;
    }
}
