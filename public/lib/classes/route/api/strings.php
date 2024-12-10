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

use core\param;
use core\router\route;
use core\router\schema\response\payload_response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Language string Controller.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class strings {
    use \core\router\route_controller;

    /**
     * Fetch a string or all strings for a component in a language.
     *
     * @param ServerRequestInterface $request
     * @param string $language
     * @param string $component
     * @param null|string $identifier
     * @return payload_response
     */
    #[route(
        path: '/strings/{language}/{component}[/{identifier:.*}]',
        method: ['GET'],
        title: 'Fetch Moodle Language Strings',
        description: 'Fetch a single string, or all strings for a component',
        security: [],
        cookies: false,
        cachelifetime: '90 days',
        pathtypes: [
            new \core\router\parameters\path_language(),
            new \core\router\parameters\path_component(),
            new \core\router\schema\parameters\path_parameter(
                name: 'identifier',
                type: param::STRINGID,
            ),
        ],
        responses: [
            new \core\router\schema\response\response(
                statuscode: 200,
                description: 'OK',
                content: [
                    new \core\router\schema\response\content\json_media_type(
                        schema:  new \core\router\schema\objects\schema_object(
                            content: [
                                'strings' => new \core\router\schema\objects\array_of_strings(
                                    keyparamtype: param::TEXT,
                                    valueparamtype: param::RAW,
                                ),
                            ],
                        ),
                        examples: [
                            new \core\router\schema\example(
                                name: 'Single String',
                                summary: 'A json response containing the translation for a single string',
                                value: [
                                    'strings' => [
                                        'core/actionsmenu' => 'Actions menu',
                                    ],
                                ],
                            ),
                        ]
                    ),
                ],
            ),
        ],
    )]
    #[\core\router\scope\unscoped_resource]
    public function get_strings(
        ServerRequestInterface $request,
        string $language,
        string $component,
        ?string $identifier = null,
    ): payload_response {
        global $PAGE;

        $PAGE->set_context(\core\context\system::instance());

        $stringmanager = get_string_manager();

        if ($identifier) {
            $strings = [
                "{$component}/{$identifier}" => $stringmanager->get_string($identifier, $component, [], $language),
            ];
        } else {
            $strings = [];
            foreach ($stringmanager->load_component_strings($component, $language) as $identifier => $value) {
                $strings["{$component}/{$identifier}"] = $value;
            }
        }

        return new payload_response(
            payload: [
                'strings' => $strings,
            ],
            request: $request,
        );
    }
}
