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

namespace core_reportbuilder\local\entities;

use coding_exception;
use lang_string;
use core_reportbuilder\local\report\column;
use core_reportbuilder\local\report\filter;

/**
 * Base class for all report entities
 *
 * @package     core_reportbuilder
 * @copyright   2019 Marina Glancy <marina@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class base {

    /** @var string $entityname Internal reference to name of entity */
    private $entityname = null;

    /** @var lang_string $entitytitle Used as a title for the entity in reports */
    private $entitytitle = null;

    /** @var array $tablealiases Database tables that this entity uses and their aliases */
    private $tablealiases = [];

    /** @var array $tablejoinaliases Database tables that have already been joined to the report and their aliases */
    private $tablejoinaliases = [];

    /** @var string[] $joins List of SQL joins for the entity */
    private $joins = [];

    /** @var column[] $columns List of columns for the entity */
    private $columns = [];

    /** @var filter[] $filters List of filters for the entity */
    private $filters = [];

    /** @var filter[] $conditions List of conditions for the entity */
    private $conditions = [];

    /**
     * Database tables that this entity uses and their default aliases
     *
     * Must be overridden by the entity to list all database tables that it expects to be present in the main
     * SQL or in JOINs added to this entity
     *
     * @return string[] Array of $tablename => $alias
     */
    abstract protected function get_default_table_aliases(): array;

    /**
     * The default title for this entity
     *
     * @return lang_string
     */
    abstract protected function get_default_entity_title(): lang_string;

    /**
     * Initialise the entity, called automatically when it is added to a report
     *
     * This is where entity defines all its columns and filters by calling:
     * - {@see add_column}
     * - {@see add_filter}
     * - etc
     *
     * @return self
     */
    abstract public function initialise(): self;

    /**
     * The default machine-readable name for this entity that will be used in the internal names of the columns/filters
     *
     * @return string
     */
    private function get_default_entity_name(): string {
        $namespace = explode('\\', get_called_class());

        return end($namespace);
    }

    /**
     * Set entity name
     *
     * @param string $entityname
     * @return self
     */
    final public function set_entity_name(string $entityname): self {
        $this->entityname = $entityname;
        return $this;
    }

    /**
     * Return entity name
     *
     * @return string
     */
    final public function get_entity_name(): string {
        return $this->entityname ?? $this->get_default_entity_name();
    }

    /**
     * Set entity title
     *
     * @param lang_string $title
     * @return self
     */
    final public function set_entity_title(lang_string $title): self {
        $this->entitytitle = $title;
        return $this;
    }

    /**
     * Get entity title
     *
     * @return lang_string
     */
    final public function get_entity_title(): lang_string {
        return $this->entitytitle ?? $this->get_default_entity_title();
    }

    /**
     * Override the default alias for given database table used in entity queries, to avoid table alias clashes that may occur
     * if multiple entities of a report each define the same default alias for one of their tables
     *
     * @param string $tablename
     * @param string $alias
     * @return self
     * @throws coding_exception
     */
    final public function set_table_alias(string $tablename, string $alias): self {
        if (!array_key_exists($tablename, $this->get_default_table_aliases())) {
            throw new coding_exception('Invalid table name', $tablename);
        }

        $this->tablealiases[$tablename] = $alias;
        return $this;
    }

    /**
     * Override multiple default database table aliases used in entity queries as per {@see set_table_alias}, typically when
     * you're adding an entity multiple times to a report you'd want to override the table aliases in the second instance to
     * avoid clashes with the first
     *
     * @param array $aliases Array of tablename => alias values
     * @return self
     */
    final public function set_table_aliases(array $aliases): self {
        foreach ($aliases as $tablename => $alias) {
            $this->set_table_alias($tablename, $alias);
        }
        return $this;
    }

    /**
     * Returns an alias used in the queries for a given table
     *
     * @param string $tablename
     * @return string
     * @throws coding_exception
     */
    final public function get_table_alias(string $tablename): string {
        $defaulttablealiases = $this->get_default_table_aliases();
        if (!array_key_exists($tablename, $defaulttablealiases)) {
            throw new coding_exception('Invalid table name', $tablename);
        }

        return $this->tablealiases[$tablename] ?? $defaulttablealiases[$tablename];
    }

    /**
     * Set the alias for given database table that has already been added to the report. Enables entities to avoid additional
     * joins on the same table by allowing re-use of existing table aliases in their own queries, {@see has_table_join_alias}
     *
     * @param string $tablename
     * @param string $alias
     * @return self
     */
    final public function set_table_join_alias(string $tablename, string $alias): self {
        $this->tablejoinaliases[$tablename] = $alias;

        // Internally set the same table alias for the entity.
        return $this->set_table_alias($tablename, $alias);
    }

    /**
     * Determine whether defined table join alias was specified. Call {@see get_table_alias} to retrieve said value
     *
     * @param string $tablename
     * @return bool
     */
    final public function has_table_join_alias(string $tablename): bool {
        return array_key_exists($tablename, $this->tablejoinaliases);
    }

    /**
     * Add join clause required for this entity to join to existing tables/entities
     *
     * @param string $join
     * @return self
     */
    final public function add_join(string $join): self {
        $this->joins[trim($join)] = trim($join);
        return $this;
    }

    /**
     * Add multiple join clauses required for this entity {@see add_join}
     *
     * @param string[] $joins
     * @return self
     */
    final public function add_joins(array $joins): self {
        foreach ($joins as $join) {
            $this->add_join($join);
        }
        return $this;
    }

    /**
     * Return entity joins
     *
     * @return string[]
     */
    final public function get_joins(): array {
        return array_values($this->joins);
    }

    /**
     * Helper method for returning joins necessary for retrieving tags related to the current entity
     *
     * Both 'tag' and 'tag_instance' aliases must be returned by the entity {@see get_default_table_aliases} method
     *
     * @param string $component
     * @param string $itemtype
     * @param string $itemidfield
     * @return string[]
     */
    final protected function get_tag_joins_for_entity(string $component, string $itemtype, string $itemidfield): array {
        $taginstancealias = $this->get_table_alias('tag_instance');
        $tagalias = $this->get_table_alias('tag');

        return [
            "LEFT JOIN {tag_instance} {$taginstancealias}
                    ON {$taginstancealias}.component = '{$component}'
                   AND {$taginstancealias}.itemtype = '{$itemtype}'
                   AND {$taginstancealias}.itemid = {$itemidfield}",
            "LEFT JOIN {tag} {$tagalias}
                    ON {$tagalias}.id = {$taginstancealias}.tagid",
        ];
    }

    /**
     * Add a column to the entity
     *
     * @param column $column
     * @return self
     */
    final protected function add_column(column $column): self {
        $this->columns[$column->get_name()] = $column;
        return $this;
    }

    /**
     * Returns entity columns
     *
     * @return column[]
     */
    final public function get_columns(): array {
        return $this->columns;
    }

    /**
     * Returns an entity column
     *
     * @param string $name
     * @return column
     * @throws coding_exception For invalid column name
     */
    final public function get_column(string $name): column {
        if (!array_key_exists($name, $this->columns)) {
            throw new coding_exception('Invalid column name', $name);
        }

        return $this->columns[$name];
    }

    /**
     * Add a filter to the entity
     *
     * @param filter $filter
     * @return self
     */
    final protected function add_filter(filter $filter): self {
        $this->filters[$filter->get_name()] = $filter;
        return $this;
    }

    /**
     * Returns entity filters
     *
     * @return filter[]
     */
    final public function get_filters(): array {
        return $this->filters;
    }

    /**
     * Returns an entity filter
     *
     * @param string $name
     * @return filter
     * @throws coding_exception For invalid filter name
     */
    final public function get_filter(string $name): filter {
        if (!array_key_exists($name, $this->filters)) {
            throw new coding_exception('Invalid filter name', $name);
        }

        return $this->filters[$name];
    }

    /**
     * Add a condition to the entity
     *
     * @param filter $condition
     * @return $this
     */
    final protected function add_condition(filter $condition): self {
        $this->conditions[$condition->get_name()] = $condition;
        return $this;
    }

    /**
     * Returns entity conditions
     *
     * @return filter[]
     */
    final public function get_conditions(): array {
        return $this->conditions;
    }

    /**
     * Returns an entity condition
     *
     * @param string $name
     * @return filter
     * @throws coding_exception For invalid condition name
     */
    final public function get_condition(string $name): filter {
        if (!array_key_exists($name, $this->conditions)) {
            throw new coding_exception('Invalid condition name', $name);
        }

        return $this->conditions[$name];
    }
}
