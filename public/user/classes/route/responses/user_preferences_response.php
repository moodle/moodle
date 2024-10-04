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

namespace core_user\route\responses;

use core\param;
use core\router\schema\response\content\payload_response_type;

/**
 * A standard response for user preferences.
 *
 * @package    core_user
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_preferences_response extends \core\router\schema\response\response {
    /**
     * Constructor for a standard user preference response.
     */
    public function __construct() {
        parent::__construct(
            content: new payload_response_type(
                schema: new \core\router\schema\objects\array_of_strings(
                    keyparamtype: param::TEXT,
                    valueparamtype: param::RAW,
                ),
                examples: [
                    new \core\router\schema\example(
                        name: 'A single preference value',
                        summary: 'A json response containing a single preference',
                        value: [
                            "drawers-open-index" => "1",
                        ],
                    ),
                    new \core\router\schema\example(
                        name: 'A set of preference values',
                        summary: 'A json response containing a set of preferences',
                        value: [
                            "drawers-open-index" => "1",
                            "login_failed_count_since_success" => "1",
                            "coursesectionspreferences_2" => "{\"contentcollapsed\":[]}",
                        ],
                    ),
                ]
            ),
        );
    }
}
