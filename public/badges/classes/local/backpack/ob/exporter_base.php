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

namespace core_badges\local\backpack\ob;

use core\url;

/**
 * The base class for Open Badges exporters. It provides a common interface for exporting Open Badges data.
 *
 * @package    core_badges
 * @copyright  2025 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class exporter_base {
    /**
     * Export the data to an array.
     *
     * @return array The exported data.
     */
    abstract public function export(): array;

    /**
     * Get the URL to the JSON representation of the exporter.
     *
     * @return url The URL to the JSON representation of the exporter.
     */
    public function get_json(): string {
        return json_encode($this->export());
    }

    /**
     * Get the exporter class using the same namespace as this class.
     *
     * @param string $classname The name of the exporter class to retrieve.
     * @return string The fully qualified class name of the exporter.
     */
    protected function get_exporter_class(string $classname): string {
        return (new \ReflectionClass($this))->getNamespaceName() . '\\' . $classname;
    }

    /**
     * Get the version from the namespace of the exporter class.
     *
     * @return string The version string extracted from the namespace, or null if not found.
     */
    protected function get_version_from_namespace(): string {
        $namespace = (new \ReflectionClass($this))->getNamespaceName();
        preg_match('/v\d+p\d+/', $namespace, $matches);
        return $matches[0] ?? null;
    }
}
