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

use Psr\Http\Message\ServerRequestInterface;
use Slim\App;
use Slim\Handlers\ErrorHandler;

/**
 * An Eerror Handler implementation for Moodle which is aware of the REST API.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class error_handler extends ErrorHandler {
    /**
     * Construct a new Error Handler.
     *
     * @param \Slim\App $app
     */
    public function __construct(
        App $app,
    ) {
        parent::__construct(
            $app->getCallableResolver(),
            $app->getResponseFactory(),
        );

        $this->registerErrorRenderer('text/html', error_renderer::class);
    }

    #[\Override]
    protected function determineContentType(ServerRequestInterface $request): ?string {
        // For anything hitting /rest/api/v2 we will default to JSON.
        $restbase = (new \core\url('/rest/api/v2/'))->get_path();
        if (substr($request->getUri()->getPath(), 0, strlen($restbase)) === $restbase) {
            return 'application/json';
        }

        // Fall back to the default behaviour of using the Accept header.
        return parent::determineContentType($request);
    }
}
