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

use core\router\route;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Middleware to add standard cache headers to a response.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cache_control_middleware implements MiddlewareInterface {
    #[\Override]
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        // Handle the response.
        $response = $handler->handle($request);

        if ($request->getMethod() !== 'GET') {
            // Do not add any caching headers to non-GET requests.
            return $response;
        }

        if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 300) {
            // Do not add any caching headers to non-OK responses.
            return $response;
        }

        $moodleroute = $request->getAttribute(route::class);
        if (!$moodleroute || !$moodleroute->cachelifetime) {
            // No Moodle route found, or the moodle route has no cachelifetime.
            return $response;
        }

        // Prevent cache poisoning.
        $route = $request->getAttribute('__route__');
        $cachekey = $route->getArgument('cachekey');

        if (!$cachekey || !is_numeric($cachekey)) {
            // The cache key was not provided, or is not in the correct format. Do not cache the response.
            return $response;
        }

        $now = new \DateTimeImmutable(timezone: new \DateTimeZone('UTC'));
        if ($cachekey > $now->getTimestamp()) {
            // The cache key is in the future, so we should not cache the response.
            return $response;
        }

        // Base the cache headers on the current time and not the cache key.
        // We do not want cache degredation to occur based on the date that the cache key was generated.
        $lifetime = \DateInterval::createFromDateString($moodleroute->cachelifetime);
        $expiry = $now->add($lifetime);
        $expiryseconds = $expiry->getTimestamp() - $now->getTimestamp();

        if ($expiryseconds <= 0) {
            // The cache lifetime is zero or negative, so we should not cache the response.
            return $response;
        }

        return $response
            ->withHeader('Expires', $expiry->format('D, d M Y H:i:s \G\M\T'))
            ->withHeader('Pragma', '')
            ->withHeader('Cache-Control', 'public, max-age=' . $expiryseconds . ', immutable')
            ->withHeader('Accept-Ranges', 'none');
    }
}
