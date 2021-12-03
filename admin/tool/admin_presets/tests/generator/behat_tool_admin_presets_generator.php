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
 * Admin presets entity generators.
 *
 * @package    tool_admin_presets
 * @category   test
 * @copyright  2021 Amaia Anabitarte (amaia@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_tool_admin_presets_generator extends behat_generator_base {

    /**
     * Get a list of the entities that can be created.

     * @return array entity name => information about how to generate.
     */
    protected function get_creatable_entities(): array {
        return [
            'presets' => [
                'singular' => 'preset',
                'datagenerator' => 'preset',
                'required' => [],
            ],
        ];
    }
}
