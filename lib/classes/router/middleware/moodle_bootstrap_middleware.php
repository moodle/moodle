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
        global $PAGE;

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

        $PAGE->set_url((string) $request->getUri());

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
