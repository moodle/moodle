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

namespace core\fixtures;

use core\router\route;
use GuzzleHttp\Psr7\Response;

/**
 * Fixture for tests of the router and route classes.
 *
 * @package    core
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class route_on_method_only {
    /**
     * A method without a route.
     *
     * @return Response
     */
    public function method_without_route(): Response {
        return new Response(200, [], 'test');
    }

    /**
     * A method with a route.
     *
     * @return Response
     */
    #[route(
        path: '/method/path',
    )]
    public function method_with_route(): Response {
        return new Response(200, [], 'test2');
    }
}
