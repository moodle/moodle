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

namespace core_ai;

/**
 * Class provider.
 *
 * @package    core_ai
 * @copyright  2024 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class provider {
    /**
     * Get the actions that this provider supports.
     *
     * Returns an array of action class names.
     *
     * @return array An array of action class names.
     */
    abstract public function get_action_list(): array;

    /**
     * Given an action class name, return an array of sub actions
     * that this provider supports.
     *
     * @param string $classname The action class name.
     * @return array An array of supported sub actions.
     */
    public function get_sub_actions(string $classname): array {
        return [];
    }

    /**
     * Get the name of the provider.
     *
     * @return string The name of the provider.
     */
    public function get_name(): string {
        $name = explode('\\', get_class($this))[0];
        return get_string('pluginname', $name);
    }

}
