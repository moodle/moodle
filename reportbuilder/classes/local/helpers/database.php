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

use coding_exception;
use core_text;

/**
 * Helper functions for DB manipulations
 *
 * @package     core_reportbuilder
 * @copyright   2019 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class database {

    /** @var string Prefix for generated aliases */
    private const GENERATE_ALIAS_PREFIX = 'rbalias';

    /** @var string Prefix for generated param names names */
    private const GENERATE_PARAM_PREFIX = 'rbparam';

    /**
     * Generates unique table/column alias that must be used in generated SQL
     *
     * @return string
     */
    public static function generate_alias(): string {
        static $aliascount = 0;

        return static::GENERATE_ALIAS_PREFIX . ($aliascount++);
    }
    /**
     * Generates unique parameter name that must be used in generated SQL
     *
     * @return string
     */
    public static function generate_param_name(): string {
        static $paramcount = 0;

        return static::GENERATE_PARAM_PREFIX . ($paramcount++);
    }

    /**
     * Validate that parameter names were generated using {@see generate_param_name}.
     *
     * @param array $params
     * @return bool
     * @throws coding_exception For invalid params.
     */
    public static function validate_params(array $params): bool {
        $nonmatchingkeys = array_filter($params, static function($key): bool {
            return !preg_match('/^' . static::GENERATE_PARAM_PREFIX . '[\d]+/', $key);
        }, ARRAY_FILTER_USE_KEY);

        if (!empty($nonmatchingkeys)) {
            throw new coding_exception('Invalid parameter names', implode(', ', array_keys($nonmatchingkeys)));
        }

        return true;
    }

    /**
     * Generate SQL expression for sorting group concatenated fields
     *
     * @param string $field The original field or SQL expression
     * @param string|null $sort A valid SQL ORDER BY to sort the concatenated fields, if omitted then $field will be used
     * @return string
     */
    public static function sql_group_concat_sort(string $field, string $sort = null): string {
        global $DB;

        // Fallback to sorting by the specified field, unless it contains parameters which would be duplicated.
        if ($sort === null && !preg_match('/[:?$]/', $field)) {
            $fieldsort = $field;
        } else {
            $fieldsort = $sort;
        }

        // Nothing to sort by.
        if ($fieldsort === null) {
            return '';
        }

        // If the sort specifies a direction, we need to handle that differently in Postgres.
        if ($DB->get_dbfamily() === 'postgres') {
            $fieldsortdirection = '';

            preg_match('/(?<direction>ASC|DESC)?$/i', $fieldsort, $matches);
            if (array_key_exists('direction', $matches)) {
                $fieldsortdirection = $matches['direction'];
                $fieldsort = core_text::substr($fieldsort, 0, -(core_text::strlen($fieldsortdirection)));
            }

            // Cast sort, stick the direction on the end.
            $fieldsort = "CAST({$fieldsort} AS VARCHAR) {$fieldsortdirection}";
        }

        return $fieldsort;
    }
}
