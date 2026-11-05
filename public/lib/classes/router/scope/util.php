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

namespace core\router\scope;

/**
 * Class util
 *
 * @package    core
 * @copyright  2026 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class util {
    /**
     * Get all available scope entities.
     *
     * @return scope_entity_interface[] The array of scope entities keyed by identifier.
     */
    public function get_all_scopes(): array {
        $scopes = [];

        foreach ($this->get_scope_map() as $identifier => $classname) {
            $scopes[$identifier] = new $classname();
        }

        return $scopes;
    }

    /**
     * Get the map of scope identifiers to scope classes.
     *
     * @return array The map of scope identifiers to scope classes.
     */
    public function get_scope_map(): array {
        // Look for a cached version of the scope map first.
        $cache = \cache::make('core', 'oauth2_server');
        $scopemapcache = $cache->get('scope_map');

        if ($scopemapcache !== false) {
            // Use the cache if it exists.
            return $scopemapcache;
        }

        $scopemap = [];

        $isabstract = fn (string $classname): bool => (new \ReflectionClass($classname))->isAbstract();

        foreach (\core\component::get_component_names(true) as $componentname) {
            $scopes = \core\component::get_component_classes_in_namespace(
                $componentname,
                'route\scope',
            );
            foreach (array_keys($scopes) as $classname) {
                if (
                    is_subclass_of($classname, abstract_scope::class) &&
                    !$isabstract($classname)
                ) {
                    try {
                        $scopemap[$classname::get_identifier()] = $classname;
                    } catch (\Throwable $e) {
                        debugging("Skipping scope class '{$classname}' due to error: {$e->getMessage()}", DEBUG_DEVELOPER);
                    }
                }
            }
        }

        $cache->set('scope_map', $scopemap);

        return $scopemap;
    }
}
