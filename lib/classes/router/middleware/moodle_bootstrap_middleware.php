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

use core\router\util;
use core\router\route_loader_interface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Middleware to set flags and define setup.
 *
 * @package    core
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodle_bootstrap_middleware implements MiddlewareInterface {
    #[\Override]
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        global $CFG, $PAGE;

        if (str_contains($request->getUri(), route_loader_interface::ROUTE_GROUP_API)) {
            // @codeCoverageIgnoreStart
            if (!defined('AJAX_SCRIPT')) {
                define('AJAX_SCRIPT', true);
            }
            // @codeCoverageIgnoreEnd
        }

        $routeattribute = util::get_route_instance_for_request($request);
        if ($routeattribute && !$routeattribute->cookies) {
            // @codeCoverageIgnoreStart
            // This request should not access Moodle cookies.
            if (!defined('NO_MOODLE_COOKIES')) {
                define('NO_MOODLE_COOKIES', true);
            }
            // @codeCoverageIgnoreEnd
        }

        if (!$routeattribute || !$routeattribute->abortafterconfig) {
            // Do not load the full Moodle stack. This is a lightweight request.
            $this->load_full_moodle();
        }

        // Set the URL for the page.
        // Normally in Moodle this is a largely hard-coded value with only the query string changing dynamically in the page.
        // However, in this instance, we are generating the URL dynamically because we are the request terminator
        // for a large number of requests at different endpoints.

        // In basic cases this will just work, but there are some edge cases to consider - specficially were the site
        // is behind a reverse proxy and/or an SSL terminator.
        // In these cases the URL we generate from the ServerRequestInterface may be the _terminated_ URL,
        // and not the URL that was requested by the client.

        // We need to generate the URL that the client requested, not the URL that the server received.
        $url = $request->getUri();

        if (!empty($CFG->reverseproxy)) {
            // This site is behind a reverse proxy. The requested URI may have a different:
            // - scheme
            // - host
            // - port
            // to the URL that the client requested.

            $url = $url
                // Start by setting the scheme and host to the wwwroot.
                ->withScheme(parse_url($CFG->wwwroot, PHP_URL_SCHEME))
                ->withHost(parse_url($CFG->wwwroot, PHP_URL_HOST))

                // Update the URL to match the port of the wwwroot.
                // While it is highly unlikely that a wwwroot includes an explicit port, we should still handle it.
                ->withPort(parse_url($CFG->wwwroot, PHP_URL_PORT));

        }

        if (!empty($CFG->sslproxy)) {
            // This site is behind an ssl terminating proxy. The requested URI may have a different:
            // - scheme
            // - port
            // to the URL that the client requested.
            $url = $url
                // The wwwroot must use the https scheme, but the terminating request may have been received using http.
                ->withScheme('https')

                // Update the URL to match the port of the wwwroot.
                // While it is highly unlikely that a wwwroot includes an explicit port, we should still handle it.
                ->withPort(parse_url($CFG->wwwroot, PHP_URL_PORT));
        }

        $PAGE->set_url((string) $url);

        return $handler->handle($request);
    }

    /**
     * Check whether Moodle is fully loaded.
     *
     * @return bool
     * @codeCoverageIgnore
     */
    public function is_full_moodle_loaded(): bool {
        if (defined('ABORT_AFTER_CONFIG')) {
            return defined('ABORT_AFTER_CONFIG_CANCEL');
        }

        return true;
    }

    /**
     * Load the full Moodle Framework.
     *
     * @codeCoverageIgnore
     */
    protected function load_full_moodle(): void {
        // Note: These globals should be defined even if they are not used as they are used in the require.
        global $CFG, $DB, $SESSION, $OUTPUT, $PAGE;

        if ($this->is_full_moodle_loaded()) {
            return;
        }

        // Ok, now we need to start normal moodle script, we need to load all libs and $DB.
        if (defined('ABORT_AFTER_CONFIG_CANCEL') && ABORT_AFTER_CONFIG_CANCEL) {
            return;
        }
        define('ABORT_AFTER_CONFIG_CANCEL', true);

        require("{$CFG->dirroot}/lib/setup.php");
    }
}
