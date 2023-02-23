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

namespace core_reportbuilder\local\report;

use lang_string;
use moodle_exception;
use core_reportbuilder\local\filters\base;
use core_reportbuilder\local\models\filter as filter_model;

/**
 * Class to represent a report filter
 *
 * @package     core_reportbuilder
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class filter {

    /** @var string $filterclass */
    private $filterclass;

    /** @var string $name */
    private $name;

    /** @var lang_string $header */
    private $header;

    /** @var string $entity */
    private $entityname;

    /** @var string $fieldsql */
    private $fieldsql = '';

    /** @var array $fieldparams */
    private $fieldparams = [];

    /** @var string[] $joins */
    protected $joins = [];

    /** @var bool $available */
    protected $available = true;

    /** @var mixed $options */
    protected $options;

    /** @var array $limitoperators */
    protected $limitoperators = [];

    /** @var filter_model $persistent */
    protected $persistent;

    /**
     * Filter constructor
     *
     * @param string $filterclass Filter type class to use, must extend {@see base} filter class
     * @param string $name Internal name of the filter
     * @param lang_string $header Title of the filter used in reports
     * @param string $entityname Name of the entity this filter belongs to. Typically when creating filters within entities
     *      this value should be the result of calling {@see get_entity_name}, however if creating filters inside reports directly
     *      it should be the name of the entity as passed to {@see \core_reportbuilder\local\report\base::annotate_entity}
     * @param string $fieldsql SQL clause to use for filtering, {@see set_field_sql}
     * @param array $fieldparams
     * @throws moodle_exception For invalid filter class
     */
    public function __construct(
        string $filterclass,
        string $name,
        lang_string $header,
        string $entityname,
        string $fieldsql = '',
        array $fieldparams = []
    ) {
        if (!class_exists($filterclass) || !is_subclass_of($filterclass, base::class)) {
            throw new moodle_exception('filterinvalid', 'reportbuilder', '', null, $filterclass);
        }

        $this->filterclass = $filterclass;
        $this->name = $name;
        $this->header = $header;
        $this->entityname = $entityname;

        if ($fieldsql !== '') {
            $this->set_field_sql($fieldsql, $fieldparams);
        }
    }

    /**
     * Get filter class path
     *
     * @return string
     */
    public function get_filter_class(): string {
        return $this->filterclass;
    }

    /**
     * Get filter name
     *
     * @return string
     */
    public function get_name(): string {
        return $this->name;
    }

    /**
     * Return header
     *
     * @return string
     */
    public function get_header(): string {
        return $this->header->out();
    }

    /**
     * Set header
     *
     * @param lang_string $header
     * @return self
     */
    public function set_header(lang_string $header): self {
        $this->header = $header;
        return $this;
    }

    /**
     * Return filter entity name
     *
     * @return string
     */
    public function get_entity_name(): string {
        return $this->entityname;
    }

    /**
     * Return unique identifier for this filter
     *
     * @return string
     */
    public function get_unique_identifier(): string {
        return $this->get_entity_name() . ':' . $this->get_name();
    }

    /**
     * Return joins
     *
     * @return string[]
     */
    public function get_joins(): array {
        return array_values($this->joins);
    }

    /**
     * Add join clause required for this filter to join to existing tables/entities
     *
     * This is necessary in the case where {@see set_field_sql} is selecting data from a table that isn't otherwise queried
     *
     * @param string $join
     * @return self
     */
    public function add_join(string $join): self {
        $this->joins[trim($join)] = trim($join);
        return $this;
    }

    /**
     * Add multiple join clauses required for this filter, passing each to {@see add_join}
     *
     * Typically when defining filters in entities, you should pass {@see \core_reportbuilder\local\report\base::get_joins} to
     * this method, so that all entity joins are included in the report when your filter is used in it
     *
     * @param string[] $joins
     * @return self
     */
    public function add_joins(array $joins): self {
        foreach ($joins as $join) {
            $this->add_join($join);
        }
        return $this;
    }

    /**
     * Get SQL expression for the field
     *
     * @return string
     */
    public function get_field_sql(): string {
        return $this->fieldsql;
    }

    /**
     * Get the SQL params for the field being filtered
     *
     * @return array
     */
    public function get_field_params(): array {
        return $this->fieldparams;
    }

    /**
     * Set the SQL expression for the field that is being filtered. It will be passed to the filter class
     *
     * @param string $sql
     * @param array $params
     * @return self
     */
    public function set_field_sql(string $sql, array $params = []): self {
        $this->fieldsql = $sql;
        $this->fieldparams = $params;
        return $this;
    }

    /**
     * Return available state of the filter for the current user
     *
     * @return bool
     */
    public function get_is_available(): bool {
        return $this->available;
    }

    /**
     * Conditionally set whether the filter is available. For instance the filter may be added to a report with the
     * expectation that only some users are able to see it
     *
     * @param bool $available
     * @return self
     */
    public function set_is_available(bool $available): self {
        $this->available = $available;
        return $this;
    }

    /**
     * Set the options for the filter in the format that the filter class expected (e.g. the "select" filter expects an array)
     *
     * This method should only be used if the options do not require any calculations/queries, in which
     * case {@see set_options_callback} should be used. For performance, {@see get_string} shouldn't be used either, use of
     * {@see lang_string} is instead encouraged
     *
     * @param mixed $options
     * @return self
     */
    public function set_options($options): self {
        $this->options = $options;
        return $this;
    }

    /**
     * Set the options for the filter to be returned by a callback (that receives no arguments) in the format that the filter
     * class expects
     *
     * @param callable $callback
     * @return self
     */
    public function set_options_callback(callable $callback): self {
        $this->options = $callback;
        return $this;
    }

    /**
     * Get the options for the filter, returning via the the previously set options or generated via defined options callback
     *
     * @return mixed
     */
    public function get_options() {
        if (is_callable($this->options)) {
            $callable = $this->options;
            $this->options = ($callable)();
        }
        return $this->options;
    }

    /**
     * Set a limited subset of operators that should be used for the filter, refer to each filter class to find defined
     * operator constants
     *
     * @param array $limitoperators Simple array of operator values
     * @return self
     */
    public function set_limited_operators(array $limitoperators): self {
        $this->limitoperators = $limitoperators;
        return $this;
    }

    /**
     * Filter given operators to include only those previously defined by {@see set_limited_operators}
     *
     * @param array $operators All operators as defined by the filter class
     * @return array
     */
    public function restrict_limited_operators(array $operators): array {
        if (empty($this->limitoperators)) {
            return $operators;
        }

        return array_intersect_key($operators, array_flip($this->limitoperators));
    }

    /**
     * Set filter persistent
     *
     * @param filter_model $persistent
     * @return self
     */
    public function set_persistent(filter_model $persistent): self {
        $this->persistent = $persistent;
        return $this;
    }

    /**
     * Return filter persistent
     *
     * @return filter_model|null
     */
    public function get_persistent(): ?filter_model {
        return $this->persistent ?? null;
    }
}
