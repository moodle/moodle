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

namespace core_reportbuilder\local\filters;

use MoodleQuickForm;
use core_reportbuilder\local\report\filter;
use core_reportbuilder\local\models\filter as filter_model;

/**
 * Base class for all report filters
 *
 * Filters provide a form for collecting user input, and then return appropriate SQL fragments based on these values
 *
 * @package     core_reportbuilder
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class base {

    /** @var filter $filter */
    protected $filter;

    /** @var string $name */
    protected $name;

    /**
     * Do not allow the constructor to be called directly or overridden
     *
     * @param filter $filter
     */
    private function __construct(filter $filter) {
        $this->filter = $filter;
        $this->name = $filter->get_unique_identifier();
    }

    /**
     * Creates an instance of a filter type, based on supplied report filter instance
     *
     * The report filter instance is used by reports/entities to define what should be filtered against, e.g. a SQL fragment
     *
     * @param filter $filter The report filter instance
     * @return static
     */
    final public static function create(filter $filter): self {
        $filterclass = $filter->get_filter_class();

        return new $filterclass($filter);
    }

    /**
     * Returns the filter header
     *
     * @return string
     */
    final public function get_header(): string {
        return $this->filter->get_header();
    }

    /**
     * Returns the filter's entity name
     *
     * @return string
     */
    final public function get_entity_name(): string {
        return $this->filter->get_entity_name();
    }

    /**
     * Returns the filter persistent
     *
     * Note that filters for system reports don't store a persistent and will return null.
     *
     * @return filter_model|null
     */
    final public function get_filter_persistent(): ?filter_model {
        return $this->filter->get_persistent();
    }

    /**
     * Adds filter-specific form elements
     *
     * @param MoodleQuickForm $mform
     */
    abstract public function setup_form(MoodleQuickForm $mform): void;

    /**
     * Returns the filter clauses to be used with SQL where
     *
     * Ideally the field SQL should be included only once in the returned expression, however if that is unavoidable then
     * use the {@see filter::get_field_sql_and_params} helper to ensure uniqueness of any parameters included within
     *
     * @param array $values
     * @return array [$sql, [...$params]]
     */
    abstract public function get_sql_filter(array $values): array;

    /**
     * Given an array of current filter values for the report, determine whether the filter would apply to the report (i.e. user
     * has configured it from it's initial "Any value" state). A filter would typically be considered applied if it returns SQL
     * filter clauses, but child classes may override this method if they use different logic
     *
     * @param array $values
     * @return bool
     */
    public function applies_to_values(array $values): bool {
        [$filtersql] = $this->get_sql_filter($values);

        return $filtersql !== '';
    }

    /**
     * Return sample filter values, that when applied to a report would activate the filter - that is, cause the filter to return
     * SQL snippet. Should be overridden in child classes, to ensure compatibility with stress tests of reports
     *
     * @return array
     */
    public function get_sample_values(): array {
        return [];
    }
}
