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
use core\route\responses\template_response;
use core\router\schema\response\payload_response;
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
     * Fetch a single template for a component in a theme with the language specified.
     *
     * @param ServerRequestInterface $request
     * @param string $themename
     * @param string $component
     * @param null|string $identifier
     * @return payload_response
     */
    #[route(
        path: '/templates/{themename}:{language}/{component}/{identifier:.*}',
        method: ['GET'],
        title: 'Fetch a single template',
        description: 'Fetch a single template for a component in a theme specifying the language',
        security: [],
        cookies: false,
        cachelifetime: '90 days',
        pathtypes: [
            new \core\router\parameters\path_language(),
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
        responses: [
            new template_response(),
        ],
    )]
    #[\core\router\scope\unscoped_resource]
    public function get_templates_with_language(
        ServerRequestInterface $request,
        mustache_template_source_loader $loader,
        string $language,
        string $themename,
        string $component,
        string $identifier,
    ): payload_response {
        return $this->template_provider(
            $request,
            $loader,
            $language,
            $themename,
            $component,
            $identifier,
        );
    }

    /**
     * Fetch a single template for a component in a theme.
     *
     * @param ServerRequestInterface $request
     * @param string $themename
     * @param string $component
     * @param null|string $identifier
     * @return payload_response
     */
    #[route(
        path: '/templates/{themename}/{component}/{identifier:.*}',
        method: ['GET'],
        title: 'Fetch a single template',
        description: 'Fetch a single template for a component in a theme',
        security: [],
        cookies: false,
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
            new template_response(),
        ],
    )]
    #[\core\router\scope\unscoped_resource]
    #[\core\attribute\deprecated(
        'Use get_templates_with_language instead',
        '6.0',
        'Language should be included in the path to prevent cache poisoning',
        'MDL-82652',
    )]
    public function get_templates(
        ServerRequestInterface $request,
        mustache_template_source_loader $loader,
        string $themename,
        string $component,
        string $identifier,
    ): payload_response {
        return $this->template_provider(
            $request,
            $loader,
            $request->getHeaderLine('language'),
            $themename,
            $component,
            $identifier,
        );
    }

    /**
     * Provides the template and its dependencies for a given component and identifier within a specific theme and language.
     *
     * @param ServerRequestInterface $request
     * @param mustache_template_source_loader $loader
     * @param string $language
     * @param string $themename
     * @param string $component
     * @param string $identifier
     * @throws exception\not_found_exception
     * @return payload_response
     */
    protected function template_provider(
        ServerRequestInterface $request,
        mustache_template_source_loader $loader,
        string $language,
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
                lang: $language,
            );
        } catch (\moodle_exception $e) {
            throw new exception\not_found_exception('template', "{$component}/{$identifier}");
        }

        $result = [
            'templates' => [],
            'strings' => [],
        ];

        foreach ($dependencies['templates'] as $templatecomponent => $templates) {
            foreach ($templates as $template => $value) {
                $result['templates']["{$templatecomponent}/{$template}"] = $value;
            }
        }
        foreach ($dependencies['strings'] as $stringcomponent => $templates) {
            foreach ($templates as $template => $value) {
                $result['strings']["{$stringcomponent}/{$template}"] = $value;
            }
        }

        return new payload_response(
            payload: $result,
            request: $request,
        );
    }
}
