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

/**
 * One Roster Client.
 *
 * This plugin synchronises enrolment and roles with a One Roster endpoint.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_oneroster\local;

use coding_exception;
use enrol_oneroster\local\interfaces\filter as filter_interface;

/**
 * A One Roster Filter.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class filter implements filter_interface {
    /** @var string[] The current search filters */
    protected $filters = [];

    /** @var string The current logical operator */
    protected $logicaloperator = 'AND';

    /**
     * Constructor for a new filter.
     *
     * @param   string|null $field The field to filter on
     * @param   string|null $value The value to filter on
     * @param   string $predicate The filter search type
     */
    public function __construct(?string $field = null, ?string $value = null, string $predicate = '=') {
        if ($field !== null && $value !== null) {
            $this->add_filter($field, $value, $predicate);
        }
    }

    /**
     * Set the join operator when dealing with multiple filters.
     *
     * Note: One Roster recommends that no more than two filters are applied.
     * Complex joins are not supported by the One Roster APIs.
     *
     * @param   string $logicaloperator A valid logical join operator, such as AND, or OR.
     * @return  filter_interface
     */
    public function set_operator(string $logicaloperator): filter_interface {
        $logicaloperator = strtoupper($logicaloperator);
        switch ($logicaloperator) {
            case filter_interface::AND:
            case filter_interface::OR:
                break;
            default:
                throw new \InvalidArgumentException("The '{$logicaloperator}' Logical Operator is not supported");
        }

        $this->logicaloperator = $logicaloperator;

        return $this;
    }

    /**
     * Add a filter.
     *
     * @param   string|null $field The field to filter on
     * @param   string|null $value The value to filter on
     * @param   string $predicate The filter search type
     * @return  filter_interface
     */
    abstract public function add_filter(string $field, string $value, string $predicate = '='): filter_interface;

    /**
     * Cast the complete filter to a string for searching.
     *
     * @return  string
     */
    public function __toString(): string {
        return implode(" {$this->logicaloperator} ", $this->filters);
    }
}
