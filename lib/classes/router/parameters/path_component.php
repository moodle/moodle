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
 * A component path parameter.
 *
 * @package    core
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class path_component extends \core\router\schema\parameters\path_parameter implements referenced_object {
    /**
     * Create a new path_component parameter.
     *
     * @param string $name The name of the parameter to use for the component name
     * @param mixed ...$extra Additional arguments
     */
    public function __construct(
        string $name = 'component',
        ...$extra,
    ) {
        $extra['name'] = $name;
        $extra['type'] = param::COMPONENT;
        $extra['description'] = 'The name of a Moodle component, in frankenstyle format.';
        $extra['examples'] = [
            new example(
                name: 'The core subsystem',
                value: 'core',
            ),
            new example(
                name: 'The Course subsystem',
                value: 'core_course',
            ),
            new example(
                name: 'An activity module',
                value: 'mod_assign',
            ),
            new example(
                name: 'An assignment subplugin',
                value: 'assignsubmission_file',
            ),
        ];

        parent::__construct(...$extra);
    }
}
