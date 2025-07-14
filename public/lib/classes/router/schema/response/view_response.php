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

namespace core\router\schema\response;

use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * A response which will render the specified template.
 *
 * This approach is inspired and based upon slim-routing https://github.com/juliangut/slim-routing
 * We only need a fraction of this functionality.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class view_response extends abstract_response {
    /**
     * Create a new view response.
     *
     * @param string $template The template name
     * @param array $parameters The parameters to pass
     * @param ServerRequestInterface $request The request
     * @param ResponseInterface|null $response The response
     */
    public function __construct(
        /** @var string The template name */
        private readonly string $template,
        /** @var array The parameters to pass */
        private readonly array $parameters,
        ServerRequestInterface $request,
        ?ResponseInterface $response = null,
    ) {
        parent::__construct($request, $response);
    }

    /**
     * Get the template name.
     *
     * @return string
     */
    public function get_template_name(): string {
        return $this->template;
    }

    /**
     * Get the parameters.
     *
     * @return array
     */
    public function get_parameters(): array {
        return $this->parameters;
    }

    #[\Override]
    public function get_response(
        ResponseFactoryInterface $responsefactory,
    ): ?ResponseInterface {
        global $OUTPUT;

        $response = parent::get_response($responsefactory);
        return $response
            ->withHeader('Content-Type', 'text/html; charset=utf-8')
            ->withBody(Utils::streamFor(
                $OUTPUT->render_from_template(
                    $this->get_template_name(),
                    $this->get_parameters(),
                ),
            ));
    }
}
