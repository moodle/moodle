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

namespace core\hook\output;

/**
 * A hook to allow plugins to modify parts of the RequireJS configuration before it is applied.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\core\attribute\tags('output')]
#[\core\attribute\label('Allows plugins to make changes before RequireJS configuration is applied')]
class before_requirejs_config {
    /** @var array The RequireJS map entries added by plugins */
    private array $requirejsmap = [];

    /**
     * Add an entry to the RequireJS map.
     *
     * This allows requirejs to replace one module with another when a module is requested.
     * This is particularly useful for replacing modules with ESM versions.
     *
     * @param string $key The source module to be replaced
     * @param string $value The module that will replace the source module
     * @param string $from The filter from which this map should apply.
     *      Use '*' to apply to all modules
     */
    public function add_requirejs_esm_map_entry(
        string $key,
        string $value,
        string $from = '*',
    ): void {
        if (!array_key_exists($from, $this->requirejsmap)) {
            $this->requirejsmap[$from] = [];
        }
        $this->requirejsmap[$from][$key] = $value;
    }

    /**
     * Add multiple entries to the RequireJS map.
     *
     * This allows requirejs to replace one module with another when a module is requested.
     * This is particularly useful for replacing modules with ESM versions.
     *
     * @param array $entries An associative array of source module => replacement module
     * @param string $from The filter from which this map should apply.
     *      Use '*' to apply to all modules
     */
    public function add_requirejs_esm_map_entries(
        array $entries,
        string $from = '*',
    ): void {
        foreach ($entries as $key => $value) {
            $this->add_requirejs_esm_map_entry($key, $value, $from);
        }
    }

    /**
     * Get the RequireJS Map.
     *
     * @return array
     */
    public function get_requirejs_map(): array {
        return $this->requirejsmap;
    }
}
