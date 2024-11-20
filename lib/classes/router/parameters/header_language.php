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

namespace core\router\parameters;

use core\param;
use core\router\schema\example;
use core\router\schema\referenced_object;

/**
 * A header to accept an optional language for the requested content.
 *
 * @package    core
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class header_language extends \core\router\schema\parameters\header_object implements referenced_object {
    /**
     * Create a new path_component parameter.
     *
     * @param string $name The name of the parameter to use for the component name
     * @param mixed ...$extra Additional arguments
     */
    public function __construct(
        string $name = 'language',
        ...$extra,
    ) {
        global $CFG;

        $extra['name'] = $name;
        $extra['type'] = param::LANG;
        $extra['description'] = 'The language of the requested response.';

        // Generally speaking, the default language should be the site default.
        // This is a value which is usually stored in DB, so we have a fallback for when the full
        // Moodle configuration has not been loaded.
        $extra['default'] = $CFG->lang ?? 'en';

        $extra['examples'] = [
            new example(
                name: 'Site default',
                value: null,
            ),
            new example(
                name: 'English',
                value: 'en',
            ),
            new example(
                name: 'Deutsch (kids)',
                value: 'de_kids',
            ),
        ];

        parent::__construct(...$extra);
    }
}
