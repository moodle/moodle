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
 * A path parameter describing a cachekey.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class path_cachekey extends \core\router\schema\parameters\path_parameter implements referenced_object {
    /**
     * Create a new instance of the parameter.
     *
     * @param string $name The name the parameter should match in the path
     * @param mixed ...$extra Additional arguments
     */
    public function __construct(
        string $name = 'cachekey',
        ...$extra,
    ) {
        $extra['name'] = $name;
        $extra['type'] = param::INT;
        $extra['description'] = 'A timestamp representing a cache key.';
        $extra['examples'] = [
            new example(
                name: 'A timestamp',
                value: 1737584047,
            ),
        ];

        parent::__construct(...$extra);
    }

    #[\Override]
    public function is_required(\core\router\route $route): bool {
        // The cache key is not required.
        // It may be in the URI at the start but we cannot use an optional parameter here.
        // Instead we generate multiple routes.
        return false;
    }
}
