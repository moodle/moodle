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

use Slim\App;
use Slim\Interfaces\RouteGroupInterface;
use Slim\Interfaces\RouteInterface;

/**
 * A route loader.
 *
 * @package    core
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface route_loader_interface {
    /** @var string The route path prefix to use for API calls */
    public const ROUTE_GROUP_API = '/api/rest/v2';

    /** @var string The route path prefix to use for API calls */
    public const ROUTE_GROUP_SHIM = 'shim';

    /** @var string The route path prefix to use for API calls */
    public const ROUTE_GROUP_PAGE = '/';

    /**
     * Configure all routes for the Application.
     *
     * This method returns a set of RouteGroupInterface instances for each route prefix.
     *
     * @param App $app The application to configure routes for
     * @return RouteInterface[]|RouteGroupInterface
     */
    public function configure_routes(App $app): array;
}
