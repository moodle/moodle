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

namespace core_reportbuilder\local\aggregation;

use lang_string;
use core_reportbuilder\local\report\column;

/**
 * Base class for column aggregation types
 *
 * @package     core_reportbuilder
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class base {

    /**
     * Return the class name of the aggregation type
     *
     * @return string
     */
    final public static function get_class_name(): string {
        $namespacedclass = explode('\\', get_called_class());

        return end($namespacedclass);
    }

    /**
     * Return the display name of the aggregation
     *
     * @return lang_string
     */
    abstract public static function get_name(): lang_string;

    /**
     * Whether the aggregation is compatible with the given column type
     *
     * @param int $columntype The type as defined by the {@see column::set_type} method
     * @return bool
     */
    abstract public static function compatible(int $columntype): bool;

    /**
     * Whether the aggregation is sortable, by default return the sortable status of the column itself
     *
     * @param bool $columnsortable
     * @return bool
     */
    public static function sortable(bool $columnsortable): bool {
        return $columnsortable;
    }

    /**
     * Return SQL suitable for using within {@see get_field_sql} for column fields, by default just the first one
     *
     * @param string[] $sqlfields
     * @return string
     */
    public static function get_column_field_sql(array $sqlfields): string {
        return reset($sqlfields);
    }

    /**
     * Helper method for concatenating given fields for a column, so they are suitable for aggregation
     *
     * @param string[] $sqlfields
     * @param string $delimeter
     * @return string
     */
    final protected static function get_column_fields_concat(array $sqlfields, string $delimeter = ','): string {
        global $DB;

        $concatfields = [];
        foreach ($sqlfields as $sqlfield) {

            // We need to ensure all values are char (this ought to be done in the DML drivers, see MDL-72184).
            switch ($DB->get_dbfamily()) {
                case 'postgres' :
                    $sqlfield = "CAST({$sqlfield} AS VARCHAR)";
                    break;
                case 'oracle' :
                    $sqlfield = "TO_CHAR({$sqlfield})";
                    break;
            }

            // Coalesce all the SQL fields, to remove all nulls.
            $concatfields[] = "COALESCE({$sqlfield}, ' ')";
            $concatfields[] = "'{$delimeter}'";
        }

        // Slice off the last delimeter.
        return $DB->sql_concat(...array_slice($concatfields, 0, -1));
    }

    /**
     * Return the aggregated field SQL
     *
     * @param string $field
     * @param int $columntype
     * @return string
     */
    abstract public static function get_field_sql(string $field, int $columntype): string;

    /**
     * Return formatted value for column when applying aggregation, by default executing all callbacks on the value
     *
     * Should be overridden in child classes that need to format the column value differently (e.g. 'sum' would just show
     * a numeric count value)
     *
     * @param mixed $value
     * @param array $values
     * @param array $callbacks Array of column callbacks, {@see column::add_callback} for definition
     * @return mixed
     */
    public static function format_value($value, array $values, array $callbacks) {
        foreach ($callbacks as $callback) {
            [$callable, $arguments] = $callback;
            $value = ($callable)($value, (object) $values, $arguments);
        }

        return $value;
    }
}
