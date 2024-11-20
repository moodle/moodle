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

namespace core\route\api;

use core\exception;
use core\param;
use core\router\route;
use core\output\mustache_template_source_loader;
use core\router\schema\response\payload_response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Template Controller.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class templates {
    use \core\router\route_controller;

    /**
     * Fetch a single template for a component in a theme.
     *
     * @param ResponseInterface $response
     * @param string $themename
     * @param string $component
     * @param null|string $identifier
     * @return payload_response
     */
    #[route(
        path: '/templates/{themename}/{component}/{identifier}',
        method: ['GET'],
        title: 'Fetch a single template',
        description: 'Fetch a single template for a component in a theme',
        security: [],
        pathtypes: [
            new \core\router\parameters\path_themename(),
            new \core\router\parameters\path_component(),
            new \core\router\schema\parameters\path_parameter(
                name: 'identifier',
                type: param::SAFEPATH,
            ),
        ],
        queryparams: [
            new \core\router\schema\parameters\query_parameter(
                name: 'includecomments',
                type: param::BOOL,
                description: 'Include comments in the template',
                default: false,
            ),
        ],
        headerparams: [
            new \core\router\parameters\header_language(),
        ],
        responses: [
            new \core\router\schema\response\response(
                statuscode: 200,
                description: 'OK',
                content: [
                    new \core\router\schema\response\content\json_media_type(
                        schema:  new \core\router\schema\objects\schema_object(
                            content: [
                                'templates' => new \core\router\schema\objects\array_of_strings(
                                    keyparamtype: param::TEXT,
                                    valueparamtype: param::RAW,
                                ),
                                'strings' => new \core\router\schema\objects\array_of_strings(
                                    keyparamtype: param::TEXT,
                                    valueparamtype: param::RAW,
                                ),
                            ],
                        ),
                        examples: [
                            new \core\router\schema\example(
                                name: 'Single template value',
                                summary: 'A json response containing the template for a single template',
                                value: [
                                    'templates' => [
                                        "mod_example/template_identifier" => "<div class=\"example\">Hello World</div>",
                                        "mod_example/other_template" => "<div class=\"example\">Hello World</div>",
                                    ],
                                    'strings' => [
                                        'core/loading' => 'Loading',
                                    ],
                                ],
                            ),
                        ]
                    ),
                ],
            ),
        ],
    )]
    public function get_templates(
        ServerRequestInterface $request,
        ResponseInterface $response,
        mustache_template_source_loader $loader,
        string $themename,
        string $component,
        string $identifier,
    ): payload_response {
        global $PAGE;

        $PAGE->set_context(\core\context\system::instance());

        $params = $request->getQueryParams();
        $comments = $params['includecomments'];

        try {
            $dependencies = $loader->load_with_dependencies(
                templatecomponent: $component,
                templatename: $identifier,
                themename: $themename,
                includecomments: $comments,
                lang: $request->getHeaderLine('language'),
            );
        } catch (\moodle_exception $e) {
            throw new exception\not_found_exception('template', "{$component}/{$identifier}");
        }

        $result = [
            'templates' => [],
            'strings' => [],
        ];

        foreach ($dependencies['templates'] as $component => $templates) {
            foreach ($templates as $template => $value) {
                $result['templates']["{$component}/{$template}"] = $value;
            }
        }
        foreach ($dependencies['strings'] as $component => $templates) {
            foreach ($templates as $template => $value) {
                $result['strings']["{$component}/{$template}"] = $value;
            }
        }

        return new payload_response(
            payload: $result,
            request: $request,
        );
    }
}
