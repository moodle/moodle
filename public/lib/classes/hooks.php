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
 * Standard hook discovery agent for Moodle which lists
 * all non-abstract classes in hooks namespace of core and all plugins
 * unless there is a hook discovery agent in a plugin.
 *
 * @package   core
 * @copyright Andrew Lyons <andrew@nicols.co.uk>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class hooks implements \core\hook\discovery_agent {
    /**
     * Returns all Moodle hooks in standard hook namespace.
     *
     * @return array list of hook classes
     */
    public static function discover_hooks(): array {
        // Look for hooks in hook namespace in core and all components.
        $hooks = [];

        $hooks = array_merge($hooks, self::discover_hooks_in_namespace('core', 'hook'));

        foreach (\core_component::get_component_names() as $component) {
            $agent = "$component\\hooks";
            if (class_exists($agent) && is_subclass_of($agent, hook\discovery_agent::class)) {
                // Let the plugin supply the list of hooks instead.
                continue;
            }
            $hooks = array_merge($hooks, self::discover_hooks_in_namespace($component, 'hook'));
        }

        return $hooks;
    }

    /**
     * Look up all non-abstract classes in "$component\$namespace" namespace.
     *
     * @param string $component
     * @param string $namespace
     * @return array list of hook classes
     */
    public static function discover_hooks_in_namespace(string $component, string $namespace): array {
        $classes = \core_component::get_component_classes_in_namespace($component, $namespace);

        $hooks = [];
        foreach (array_keys($classes) as $classname) {
            $rc = new \ReflectionClass($classname);
            if ($rc->isAbstract()) {
                // Skip abstract classes.
                continue;
            }

            if ($classname === \core\hook\manager::class) {
                // Skip the manager in core.
                continue;
            }

            $hooks[$classname] = [
                'class' => $classname,
                'description' => '',
                'tags' => [],
            ];

            if (is_subclass_of($classname, \core\hook\described_hook::class)) {
                $hooks[$classname]['description'] = $classname::get_hook_description();
                $hooks[$classname]['tags'] = $classname::get_hook_tags();
            } else {
                if ($description = attribute_helper::instance($classname, \core\attribute\label::class)) {
                    $hooks[$classname]['description'] = (string) $description->label;
                }

                if ($tags = attribute_helper::instance($classname, \core\attribute\tags::class)) {
                    $hooks[$classname]['tags'] = $tags->tags;
                }
            }
        }

        return $hooks;
    }
}
