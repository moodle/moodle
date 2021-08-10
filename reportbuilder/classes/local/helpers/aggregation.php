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

declare(strict_types=1);

namespace core_reportbuilder\local\helpers;

use core_collator;
use core_component;
use core_reportbuilder\local\aggregation\base;

/**
 * Helper class for column aggregation related methods
 *
 * @package     core_reportbuilder
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class aggregation {

    /**
     * Helper method to convert aggregation class name into fully qualified namespaced class
     *
     * @param string $aggregation
     * @return string
     */
    public static function get_full_classpath(string $aggregation): string {
        return "\\core_reportbuilder\\local\\aggregation\\{$aggregation}";
    }

    /**
     * Validate whether given class is a valid aggregation type
     *
     * @param string $aggregationclass Fully qualified namespaced class, see {@see get_full_classpath} for converting value
     *      stored in column persistent to full path
     * @return bool
     */
    public static function valid(string $aggregationclass): bool {
        return class_exists($aggregationclass) && is_subclass_of($aggregationclass, base::class);
    }

    /**
     * Get available aggregation types for given column type
     *
     * @param int $columntype
     * @param array $exclude List of types to exclude, if omitted then include all available types
     * @return string[] Aggregation types indexed by [shortname => name]
     */
    public static function get_column_aggregations(int $columntype, array $exclude = []): array {
        $types = [];

        $classes = core_component::get_component_classes_in_namespace('core_reportbuilder', 'local\\aggregation');
        foreach ($classes as $class => $path) {
            /** @var base $aggregationclass */
            $aggregationclass = $class;
            if (static::valid($aggregationclass) && $aggregationclass::compatible($columntype) &&
                    !in_array($aggregationclass::get_class_name(), $exclude)) {

                $types[$aggregationclass::get_class_name()] = (string) $aggregationclass::get_name();
            }
        }

        core_collator::asort($types, core_collator::SORT_STRING);

        return $types;
    }
}
