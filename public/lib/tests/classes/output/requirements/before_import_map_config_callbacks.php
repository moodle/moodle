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

namespace core\tests\output\requirements;

/**
 * Callback for testing the before_import_map_config hook.
 *
 * @package    core
 * @category   test
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class before_import_map_config_callbacks {
    /**
     * Add a custom import specifier to the import map.
     *
     * @param \core\hook\output\before_import_map_config $hook
     */
    public static function add_custom_import(\core\hook\output\before_import_map_config $hook): void {
        $hook->add_import(
            'my-custom-specifier',
            loader: new \core\url('https://example.com/custom.js'),
        );
    }
}
