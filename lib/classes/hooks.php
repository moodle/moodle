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

namespace core;

/**
 * Hook discovery agent for core.
 *
 * @package   core
 * @copyright Andrew Lyons <andrew@nicols.co.uk>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hooks implements \core\hook\discovery_agent {
    public static function discover_hooks(): array {
        // Describe any hard-coded hooks which can't be easily discovered by namespace.
        $hooks = [];

        $hooks = array_merge($hooks, self::discover_hooks_in_namespace('core', 'hook'));

        return $hooks;
    }

    public static function discover_hooks_in_namespace(string $component, string $namespace): array {
        $classes = \core_component::get_component_classes_in_namespace($component, $namespace);

        $hooks = [];
        foreach (array_keys($classes) as $classname) {
            $rc = new \ReflectionClass($classname);
            if ($rc->isAbstract()) {
                // Skip abstract classes.
                continue;
            }

            if (is_a($classname, \core\hook\manager::class, true)) {
                // Skip the manager.
                continue;
            }

            $hooks[$classname] = [
                'class' => $classname,
                'description' => '',
                'tags' => [],
            ];

            if ($rc->implementsInterface(\core\hook\described_hook::class)) {
                $hooks[$classname]['description'] = $classname::get_hook_description();
            }


        }

        return $hooks;
    }
}
