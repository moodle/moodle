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

namespace local_ai_manager\hook;

use local_ai_manager\local\tenant;

/**
 * Hook for customizing the rights config table.
 *
 * This hook will be dispatched when it's about to show the rights config table.
 *
 * @package    local_ai_manager
 * @copyright  2024 ISB Bayern
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\core\attribute\label(
        'Allows plugins to customize the user rights management table in the tenant config.')]
#[\core\attribute\tags('local_ai_manager')]
class usertable_extend {

    /**
     * Constructor for the hook.
     *
     * @param tenant $tenant the tenant object
     * @param array $columns the columns array for the table
     * @param array $headers the headers array associated with the columns for the table
     * @param array $filterids the ids which are selected in the filter by the user
     * @param string $fields the database fields to select for the table
     * @param string $from the "from" clause for the DB query
     * @param string $where the "where" clause for the DB query
     * @param array $params the params array for the DB query
     */
    public function __construct(
        /** @var tenant $tenant the tenant object */
            private tenant $tenant,
            /** @var array $columns the columns array for the table */
            private array $columns,
            /** @var array $headers the headers array associated with the columns for the table */
            private array $headers,
            /** @var array $filterids the ids which are selected in the filter by the user */
            private array $filterids,
            /** @var string $fields the database fields to select for the table */
            private string $fields,
            /** @var string $from the "from" clause for the DB query */
            private string $from,
            /** @var string $where the "where" clause for the DB query */
            private string $where,
            /** @var array $params the params array for the DB query */
            private array $params
    ) {
    }

    /**
     * Standard getter.
     *
     * @return tenant the tenant object
     */
    public function get_tenant(): tenant {
        return $this->tenant;
    }

    /**
     * Standard getter
     *
     * @return array the columns of the table
     */
    public function get_columns(): array {
        return $this->columns;
    }

    /**
     * Standard getter
     *
     * @return array the headers of the table
     */
    public function get_headers(): array {
        return $this->headers;
    }

    /**
     * Standard getter
     *
     * @return array the ids the user selected in the filter
     */
    public function get_filterids(): array {
        return $this->filterids;
    }

    /**
     * Standard getter
     *
     * @return array $fields the fields to select for the DB query
     */
    public function get_fields(): string {
        return $this->fields;
    }

    /**
     * Standard getter
     *
     * @return string the "from" statement for the DB query
     */
    public function get_from(): string {
        return $this->from;
    }

    /**
     * Standard getter
     *
     * @return string the "where" statement for the DB query
     */
    public function get_where(): string {
        return $this->where;
    }

    /**
     * Standard getter
     *
     * @return array the params array for the DB query
     */
    public function get_params(): array {
        return $this->params;
    }

    /**
     * Standard setter to allow hook callbacks to store the manipulated data into the hook object.
     *
     * @param array $columns the columns array
     */
    public function set_columns(array $columns): void {
        $this->columns = $columns;
    }

    /**
     * Standard setter to allow hook callbacks to store the manipulated data into the hook object.
     *
     * @param array $headers the headers array
     */
    public function set_headers(array $headers): void {
        $this->headers = $headers;
    }

    /**
     * Standard setter to allow hook callbacks to store the manipulated data into the hook object.
     *
     * @param string $fields the fields string for the DB query
     */
    public function set_fields(string $fields): void {
        $this->fields = $fields;
    }

    /**
     * Standard setter to allow hook callbacks to store the manipulated data into the hook object.
     *
     * @param string $from the "from" string for the DB query
     */
    public function set_from(string $from): void {
        $this->from = $from;
    }

    /**
     * Standard setter to allow hook callbacks to store the manipulated data into the hook object.
     *
     * @param string $where the "where" string for the DB query
     */
    public function set_where(string $where): void {
        $this->where = $where;
    }

    /**
     * Standard setter to allow hook callbacks to store the manipulated data into the hook object.
     *
     * @param array $params the params array for the DB query
     */
    public function set_params(array $params): void {
        $this->params = $params;
    }
}
