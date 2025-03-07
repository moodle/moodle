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

namespace qbank_viewcreator;

use core\output\datafilter;
use core_question\local\bank\condition;

/**
 * Abstract class for conditions filtering by user.
 *
 * @package   qbank_viewcreator
 * @copyright 2025 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class user_condition extends condition {

    /**
     * Return the alias for the instance of the user table to filter on.
     *
     * @return string
     */
    abstract protected static function get_table_alias(): string;

    #[\Override]
    public function get_filter_class() {
        return 'core/datafilter/filtertypes/keyword';
    }

    #[\Override]
    public static function build_query_from_filter(array $filter): array {
        global $DB;

        $conditions = [];
        $params = [];
        $notlike = $filter['jointype'] === datafilter::JOINTYPE_NONE;
        $tablealias = static::get_table_alias();
        $allnames = array_map(fn($field) => "{$tablealias}.{$field}", \core_user\fields::get_name_fields());
        $allnames = $DB->sql_concat(...$allnames);
        $conditionkey = static::get_condition_key();
        foreach ($filter['values'] as $key => $value) {
            $params["{$conditionkey}{$key}"] = "%$value%";
            $conditions[] = $DB->sql_like($allnames, ":{$conditionkey}{$key}", casesensitive: false, notlike: $notlike);
        }
        $delimiter = $filter['jointype'] === datafilter::JOINTYPE_ANY ? ' OR ' : ' AND ';
        return [
            implode($delimiter, $conditions),
            $params,
        ];
    }
}
