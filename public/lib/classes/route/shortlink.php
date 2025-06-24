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

namespace core\route;

use core\exception\coding_exception;
use core\router\route;
use core\router\util;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Shortlink route handler.
 *
 * Shortlinks are shorter URLs that redirect to longer Moodle URLs.
 * E.g. http://mymoodle.com/s/AbCd => http://mymoodle.com/course/view.php?id=11.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class shortlink {
    /**
     * Handle a user-specific Moodle shortlink.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param string $shortcode
     * @param \moodle_database $db
     * @param \core\shortlink $manager
     * @return ResponseInterface
     */
    #[route(
        path: '/s/{shortcode}',
        pathtypes: [
            new \core\router\schema\parameters\path_parameter(
                name: 'shortcode',
                type: \core\param::ALPHANUMEXT,
            ),
        ],
        requirelogin: new \core\router\require_login(
            requirelogin: true,
            autologinguest: false,
        ),
    )]
    public function handle_shortlink(
        ServerRequestInterface $request,
        ResponseInterface $response,
        string $shortcode,
        \moodle_database $db,
        \core\shortlink $manager,
    ): ResponseInterface {
        try {
            $url = $manager->fetch_url_for_shortcode(true, $shortcode);
            return util::redirect($response, $url);
        } catch (coding_exception $e) {
            // Shortlink not found.
            return util::throw_page_not_found($request, $response);
        }
    }

    /**
     * Global shortlinks do not require login, but the point that they redirect to may.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param string $shortcode
     * @param \moodle_database $db
     * @param \core\shortlink $manager
     * @return ResponseInterface
     */
    #[route(
        path: '/p/{shortcode}',
        pathtypes: [
            new \core\router\schema\parameters\path_parameter(
                name: 'shortcode',
                type: \core\param::ALPHANUMEXT,
            ),
        ],
    )]
    public function handle_public_shortlink(
        ServerRequestInterface $request,
        ResponseInterface $response,
        string $shortcode,
        \moodle_database $db,
        \core\shortlink $manager,
    ): ResponseInterface {
        try {
            $url = $manager->fetch_url_for_shortcode(false, $shortcode);
            return util::redirect($response, $url);
        } catch (coding_exception $e) {
            // Shortlink not found.
            return util::throw_page_not_found($request, $response, $e->getMessage(), $e);
        }
    }
}
