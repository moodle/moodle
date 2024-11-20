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

/**
 * Swagger UI for Moodle
 *
 * @package   core_admin
 * @copyright Andrew Lyons <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../config.php');
require_once($CFG->libdir . '/adminlib.php');

$swaggerversion = '5.17.14';

$PAGE->set_url('/admin/swaggerui.php');

admin_externalpage_setup('swaggerui');

$PAGE->requires->css(new moodle_url("https://unpkg.com/swagger-ui-dist@{$swaggerversion}/swagger-ui.css"));

echo $OUTPUT->header();

// These have to be manually added for now because they must be made cross-origin. The `js` method does not yet support this.
echo html_writer::tag(
    tagname: 'script',
    contents: '',
    attributes: [
        'src' => new moodle_url("https://unpkg.com/swagger-ui-dist@{$swaggerversion}/swagger-ui-bundle.js"),
        'crossorigin' => 'crossorigin',
    ],
);
echo html_writer::tag(
    tagname: 'script',
    contents: '',
    attributes: [
        'src' => new moodle_url("https://unpkg.com/swagger-ui-plugin-hierarchical-tags"),
        'crossorigin' => 'crossorigin',
    ],
);

$openapipath = moodle_url::routed_path('/api/rest/v2/openapi.json')->out();
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

        hierarchicalTagSeparator: /[_]/
    });
JS;

$PAGE->requires->js_init_code(
    jscode: $swaggerinit,
    ondomready: true,
);

echo html_writer::div('', '', [
    'id' => 'swagger-ui',
]);

echo $OUTPUT->footer();
