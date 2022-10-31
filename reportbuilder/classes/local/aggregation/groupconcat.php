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
use core_reportbuilder\local\helpers\database;
use core_reportbuilder\local\report\column;

/**
 * Column group concatenation aggregation type
 *
 * @package     core_reportbuilder
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class groupconcat extends base {

    /** @var string Character to use as a delimeter between column fields */
    protected const COLUMN_FIELD_DELIMETER = '<|>';

    /** @var string Character to use a null coalesce value */
    protected const COLUMN_NULL_COALESCE = '<^>';

    /** @var string Character to use as a delimeter between field values */
    protected const FIELD_VALUE_DELIMETER = '<,>';

    /**
     * Return aggregation name
     *
     * @return lang_string
     */
    public static function get_name(): lang_string {
        return new lang_string('aggregationgroupconcat', 'core_reportbuilder');
    }

    /**
     * This aggregation can be performed on all non-timestamp columns
     *
     * @param int $columntype
     * @return bool
     */
    public static function compatible(int $columntype): bool {
        return !in_array($columntype, [
            column::TYPE_TIMESTAMP,
        ]);
    }

    /**
     * We cannot sort this aggregation type
     *
     * @param bool $columnsortable
     * @return bool
     */
    public static function sortable(bool $columnsortable): bool {
        return false;
    }

    /**
     * Override base method to ensure all SQL fields are concatenated together if there are multiple
     *
     * @param array $sqlfields
     * @return string
     */
    public static function get_column_field_sql(array $sqlfields): string {
        if (count($sqlfields) === 1) {
            return parent::get_column_field_sql($sqlfields);
        }

        return self::get_column_fields_concat($sqlfields, self::COLUMN_FIELD_DELIMETER, self::COLUMN_NULL_COALESCE);
    }

    /**
     * Return the aggregated field SQL
     *
     * @param string $field
     * @param int $columntype
     * @return string
     */
    public static function get_field_sql(string $field, int $columntype): string {
        global $DB;

        $fieldsort = database::sql_group_concat_sort($field);

        return $DB->sql_group_concat($field, self::FIELD_VALUE_DELIMETER, $fieldsort);
    }

    /**
     * Return formatted value for column when applying aggregation, note we need to split apart the concatenated string
     * and apply callbacks to each concatenated value separately
     *
     * @param mixed $value
     * @param array $values
     * @param array $callbacks
     * @return mixed
     */
    public static function format_value($value, array $values, array $callbacks) {
        $firstvalue = reset($values);
        if ($firstvalue === null) {
            return '';
        }
        $formattedvalues = [];

        // Store original names of all values that would be present without aggregation.
        $valuenames = array_keys($values);
        $valuenamescount = count($valuenames);

        // Loop over each extracted value from the concatenated string.
        $values = explode(self::FIELD_VALUE_DELIMETER, (string)$firstvalue);
        foreach ($values as $value) {

            // Ensure we have equal number of value names/data, account for truncation by DB.
            $valuedata = explode(self::COLUMN_FIELD_DELIMETER, $value);
            if ($valuenamescount !== count($valuedata)) {
                continue;
            }

            // Re-construct original values, also ensuring any nulls contained within are restored.
            $originalvalue = array_map(static function(string $value): ?string {
                return $value === self::COLUMN_NULL_COALESCE ? null : $value;
            }, array_combine($valuenames, $valuedata));

            $originalfirstvalue = reset($originalvalue);

            // Once we've re-constructed each value, we can apply callbacks to it.
            $formattedvalues[] = parent::format_value($originalfirstvalue, $originalvalue, $callbacks);
        }

        $listseparator = get_string('listsep', 'langconfig') . ' ';
        return implode($listseparator, $formattedvalues);
    }
}
