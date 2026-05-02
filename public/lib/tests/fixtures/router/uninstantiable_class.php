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

/**
 * A fixture which is not instantiable.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class uninstantiable_class {
    /**
     * This constructor is private to prevent instantiation.
     *
     * This would not normally happen but we want to ensure that calling
     * \core\router\util::get_route_name_for_callable() does not attempt to instantiate the class.
     */
    private function __construct() {
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
