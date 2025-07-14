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
 * Middleware to check Moodle authentication.
 *
 * @package    core
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodle_authentication_middleware implements MiddlewareInterface {
    #[\Override]
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        // Get the Moodle Route from the request. We need this to determine if login is required for this page.
        $moodleroute = $request->getAttribute(route::class);

        if ($moodleroute && $moodleroute->requirelogin) {
            $requirements = $moodleroute->requirelogin;
            if ($courseattributename = $requirements->get_course_attribute_name()) {
                $courseorid = $request->getAttribute($courseattributename, null);
            }

            if ($requirements->should_require_course_login()) {
                require_course_login(
                    $courseorid,
                    $requirements->should_autologin_guest(),
                );
            } else if ($requirements->should_require_login()) {
                require_login(
                    $courseorid,
                    $requirements->should_autologin_guest(),
                );
            }
        }

        return $handler->handle($request);
    }
}
