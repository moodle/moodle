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

namespace core\route\responses;

use core\param;
use core\router\schema\response\content\json_media_type;
use core\router\schema\{
    objects\schema_object,
    objects\array_of_strings,
    example
};

/**
 * A standard response for templates
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class template_response extends \core\router\schema\response\response {
    /**
     * Constructor for a template response.
     */
    public function __construct() {
        parent::__construct(
            statuscode: 200,
            description: 'OK',
            content: [
                new json_media_type(
                    schema:  new schema_object(
                        content: [
                            'templates' => new array_of_strings(
                                keyparamtype: param::TEXT,
                                valueparamtype: param::RAW,
                            ),
                            'strings' => new array_of_strings(
                                keyparamtype: param::TEXT,
                                valueparamtype: param::RAW,
                            ),
                        ],
                    ),
                    examples: [
                        new example(
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
        );
    }
}
