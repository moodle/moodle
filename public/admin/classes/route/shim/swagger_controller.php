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

namespace core_admin\route\shim;

use core\router\route;
use core\router\route_controller;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * A shim for the Swagger UI routes.
 *
 * @package    core_admin
 * @copyright  2026 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class swagger_controller {
    use route_controller;

    /**
     * Shim /admin/swaggerui.php to the Swagger UI controller.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    #[route(
        path: '/swaggerui.php',
    )]
    public function display(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        return self::redirect_to_callable(
            $request,
            $response,
            [\core_admin\route\controller\swagger_controller::class, 'display'],
        );
    }
}
