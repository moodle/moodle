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

namespace report_lsusql;

/**
 * Static utility methods to support the report_lsusql module.
 *
 * @package report_lsusql
 * @copyright 2021 The Open University
 * @copyright 2022 Louisiana State University
 * @copyright 2022 Robert Russo
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class utils {


    /**
     * Return the current timestamp, or a fixed timestamp specified by an automated test.
     *
     * @return int The timestamp
     */
    public static function time(): int {
        if ((defined('BEHAT_SITE_RUNNING') || PHPUNIT_TEST) &&
                $time = get_config('report_lsusql', 'behat_fixed_time')) {
            return $time;
        } else {
            return time();
        }
    }

    /**
     * Group the queries by category Id.
     *
     * @param array $queries Queries need to be grouped.
     * @return array Pre-loaded Categories.
     */
    public static function group_queries_by_category($queries) {
        $grouppedqueries = [];
        foreach ($queries as $query) {
            if (isset($grouppedqueries[$query->categoryid])) {
                $grouppedqueries[$query->categoryid][] = $query;
            } else {
                $grouppedqueries[$query->categoryid] = [$query];
            }
        }

        return $grouppedqueries;
    }

    public function get_queries_data($queries) {

    }

    /**
     * Get queries for each type.
     *
     * @param array $queries Array of queries.
     * @param string $type Type to filter.
     * @return array All queries of type.
     */
    public static function get_number_of_report_by_type(array $queries, string $type) {
        return array_filter($queries, function($query) use ($type) {
            return $query->runable == $type;
        }, ARRAY_FILTER_USE_BOTH);
    }

}
