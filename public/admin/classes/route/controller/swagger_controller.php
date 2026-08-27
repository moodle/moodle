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

namespace core_admin\route\controller;

use core\output\html_writer;
use core\router\route;
use core\router\util;
use core\url;
use Psr\Http\Message\ResponseInterface;

/**
 * Generate and handle the swagger UI page.
 *
 * @package    core_admin
 * @copyright  2026 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class swagger_controller {
    /** @var string The Swagger UI version. */
    public const SWAGGER_UI_VERSION = '5.32.14';

    /** @var string The Swagger UI Hierarchical Tags Plugin version. */
    public const SWAGGER_UI_HIERARCHICAL_TAGS_PLUGIN_VERSION = '1.0.4';

    /**
     * Construct a new instance of the SwaggerUI page.
     */
    public function __construct() {
        global $CFG;

        require_once($CFG->libdir . '/adminlib.php');
    }

    /**
     * Display the Swagger UI Page.
     *
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    #[route(
        path: '/swagger',
        method: ['GET'],
    )]
    public function display(
        ResponseInterface $response,
    ): ResponseInterface {
        global $OUTPUT, $PAGE;

        $PAGE->set_url(util::get_path_for_callable(__METHOD__));

        admin_externalpage_setup('swaggerui');

        $PAGE->requires->css($this->get_css_url());

        $response->getBody()->write($OUTPUT->header());

        // These have to be manually added for now because they must be made cross-origin. The `js` method does not yet support this.
        $response->getBody()->write(html_writer::tag(
            tagname: 'script',
            contents: '',
            attributes: [
                'src' => $this->get_bundle_url(),
                'crossorigin' => 'crossorigin',
            ],
        ));
        $response->getBody()->write(html_writer::tag(
            tagname: 'script',
            contents: '',
            attributes: [
                'src' => $this->get_hierarchical_tags_plugin_url(),
                'crossorigin' => 'crossorigin',
            ],
        ));

        $openapipath = util::get_path_for_callable([\core\router\apidocs::class, 'openapi_docs'])->out();
        $oauth2redirect = util::get_path_for_callable([self::class, 'oauth2_redirect'])->out();
        $oauthtitle = get_string('swaggerui', 'admin');
        $swaggerinit = <<<JS
            window.ui = SwaggerUIBundle({
                url: "{$openapipath}",
                dom_id: '#swagger-ui',

                // Enable the "Try it out" button by default.
                tryItOutEnabled: true,

                // Show snippets different OS options.
                requestSnippetsEnabled: true,

                deepLinking: true,

                plugins: [
                    HierarchicalTagsPlugin,
                ],

                hierarchicalTagSeparator: /[_]/,

                oauth2RedirectUrl: "{$oauth2redirect}",
            });

            window.ui.initOAuth({
                clientId: "openapi",
                appName: "{$oauthtitle}",
            });
        JS;

        $PAGE->requires->js_init_code(
            jscode: $swaggerinit,
            ondomready: true,
        );

        $response->getBody()->write(html_writer::div('', '', [
            'id' => 'swagger-ui',
        ]));

        $response->getBody()->write($OUTPUT->footer());

        return $response;
    }

    /**
     * Handle Swagger UI OAuth2 Redirect.
     *
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    #[route(
        path: '/swagger/oauth2-redirect',
        method: ['GET'],
    )]
    public function oauth2_redirect(
        ResponseInterface $response,
    ): ResponseInterface {
        global $OUTPUT, $PAGE;

        $PAGE->set_url(util::get_path_for_callable(__METHOD__));

        admin_externalpage_setup('swaggerui');

        $response->getBody()->write($OUTPUT->header());

        // These have to be manually added for now because they must be made cross-origin. The `js` method does not yet support this.
        $response->getBody()->write(html_writer::tag(
            tagname: 'script',
            contents: '',
            attributes: [
                'src' => $this->get_cdn_url('swagger-ui-dist', 'oauth2-redirect.js'),
                'crossorigin' => 'crossorigin',
            ],
        ));

        $response->getBody()->write($OUTPUT->footer());

        return $response;
    }

    /**
     * Get a CDN Url.
     *
     * @param string $package
     * @param string $subpath
     * @param string $version
     * @return url
     */
    private function get_cdn_url(
        string $package,
        string $subpath,
        string $version = self::SWAGGER_UI_VERSION,
    ): url {
        return new url(sprintf(
            "https://unpkg.com/%s@%s/%s",
            $package,
            $version,
            $subpath,
        ));
    }

    /**
     * Get a plugin URL.
     *
     * @param string $plugin
     * @param string $version
     * @param string $subpath
     * @return url
     */
    private function get_plugin_url(
        string $plugin,
        string $version,
        string $subpath,
    ): url {
        return $this->get_cdn_url($plugin, $subpath, $version);
    }

    /**
     * Get the Bundle URL.
     *
     * @return url
     */
    private function get_bundle_url(): url {
        return $this->get_cdn_url('swagger-ui-dist', 'swagger-ui-bundle.js');
    }

    /**
     * Get the CSS URL.
     *
     * @return url
     */
    private function get_css_url(): url {
        return $this->get_cdn_url('swagger-ui-dist', 'swagger-ui.css');
    }

    /**
     * Get the Hierarchical Tags Plugin URL.
     *
     * @return url
     */
    private function get_hierarchical_tags_plugin_url(): url {
        return $this->get_plugin_url(
            'swagger-ui-plugin-hierarchical-tags',
            self::SWAGGER_UI_HIERARCHICAL_TAGS_PLUGIN_VERSION,
            'build/index.js',
        );
    }
}
