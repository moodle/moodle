<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace core\hook;

/**
 * This interface describes a class which can discover all hook
 * classes of a plugin.
 *
 * To add new discovery agent in your plugin you need to add your_plugin\hooks
 * class that implements this interface.
 *
 * @package   core
 * @copyright Andrew Lyons <andrew@nicols.co.uk>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface discovery_agent {
    /**
     * Returns a list of hooks for component.
     *
     * @return array
     */
    public static function discover_hooks(): array;
}
