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

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Middleware to normalise the URI path.
 *
 * This middleware will:
 * - remove duplicate /
 * - remove any trailing /
 * - ensure that there is a leading /
 *
 * @package    core
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class uri_normalisation_middleware implements MiddlewareInterface {
    #[\Override]
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        $uri = $request->getUri();
        $path = $uri->getPath();

        // Remove duplicate slashes.
        $path = preg_replace('@/+@', '/', $path);

        // Remove trailing slashes.
        $path = rtrim($path, '/');

        // Ensure that there is always a path.
        // Note: This must be performed after handling removal of duplicate and trailing slashes.
        if ($path === '') {
            $path = '/';
        }

        if ($uri->getPath() !== $path) {
            // Path has changed. Update it.
            $request = $request->withUri($uri->withPath($path));
        }

        return $handler->handle($request);
    }
}
