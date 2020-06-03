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
 * Table filterset.
 *
 * @package    core
 * @category   table
 * @copyright  2020 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace core_table\local\filter;

use InvalidArgumentException;
use JsonSerializable;
use UnexpectedValueException;
use moodle_exception;

/**
 * Class representing a set of filters.
 *
 * @package    core
 * @copyright  2020 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class filterset implements JsonSerializable {
    /** @var in The default filter type (ANY) */
    const JOINTYPE_DEFAULT = 1;

    /** @var int None of the following match */
    const JOINTYPE_NONE = 0;

    /** @var int Any of the following match */
    const JOINTYPE_ANY = 1;

    /** @var int All of the following match */
    const JOINTYPE_ALL = 2;

    /** @var int The join type currently in use */
    protected $jointype = self::JOINTYPE_DEFAULT;

    /** @var array The list of combined filter types */
    protected $filtertypes = null;

    /** @var array The list of active filters */
    protected $filters = [];

    /** @var int[] valid join types */
    protected $jointypes = [
        self::JOINTYPE_NONE,
        self::JOINTYPE_ANY,
        self::JOINTYPE_ALL,
    ];

    /**
     * Specify the type of join to employ for the filter.
     *
     * @param int $jointype The join type to use using one of the supplied constants
     * @return self
     */
    public function set_join_type(int $jointype): self {
        if (array_search($jointype, $this->jointypes) === false) {
            throw new InvalidArgumentException('Invalid join type specified');
        }

        $this->jointype = $jointype;

        return $this;
    }

    /**
     * Return the currently specified join type.
     *
     * @return int
     */
    public function get_join_type(): int {
        return $this->jointype;
    }

    /**
     * Add the specified filter.
     *
     * @param filter $filter
     * @return self
     */
    public function add_filter(filter $filter): self {
        $filtername = $filter->get_name();

        if (array_key_exists($filtername, $this->filters)) {
            // This filter already exists.
            if ($this->filters[$filtername] === $filter) {
                // This is the same value as already added.
                // Just ignore it.
                return $this;
            }

            // This is a different value to last time. Fail as this is not supported.
            throw new UnexpectedValueException(
                "A filter of type '{$filtername}' has already been added. Check that you have the correct filter."
            );
        }

        // Ensure that the filter is both known, and is of the correct type.
        $validtypes = $this->get_all_filtertypes();

        if (!array_key_exists($filtername, $validtypes)) {
            // Unknown filter.
            throw new InvalidArgumentException(
                "The filter '{$filtername}' was not recognised."
            );
        }

        // Check that the filter is of the correct type.
        if (!is_a($filter, $validtypes[$filtername])) {
            $actualtype = get_class($filter);
            $requiredtype = $validtypes[$filtername];

            throw new InvalidArgumentException(
                "The filter '{$filtername}' was incorrectly specified as a {$actualtype}. It must be a {$requiredtype}."
            );
        }

        // All good. Add the filter.
        $this->filters[$filtername] = $filter;

        return $this;
    }

    /**
     * Add the specified filter from the supplied params.
     *
     * @param string $filtername The name of the filter to create
     * @param mixed[] ...$args Additional arguments used to create this filter type
     * @return self
     */
    public function add_filter_from_params(string $filtername, ...$args): self {
        // Fetch the list of valid filters by name.
        $validtypes = $this->get_all_filtertypes();

        if (!array_key_exists($filtername, $validtypes)) {
            // Unknown filter.
            throw new InvalidArgumentException(
                "The filter '{$filtername}' was not recognised."
            );
        }

        $filterclass = $validtypes[$filtername];

        if (!class_exists($filterclass)) {
            // Filter class cannot be class autoloaded.
            throw new InvalidArgumentException(
                "The filter class '{$filterclass}' for filter '{$filtername}' could not be found."
            );
        }

        // Pass all supplied arguments to the constructor when adding a new filter.
        // This allows for a wider definition of the the filter in child classes.
        $this->add_filter(new $filterclass($filtername, ...$args));

        return $this;
    }

    /**
     * Return the current set of filters.
     *
     * @return filter[]
     */
    public function get_filters(): array {
        // Sort the filters by their name to ensure consistent output.
        // Note: This is not a locale-aware sort, but we don't need this.
        // It's primarily for consistency, not for actual sorting.
        asort($this->filters);

        return $this->filters;
    }

    /**
     * Check whether the filter has been added or not.
     *
     * @param string $filtername
     * @return bool
     */
    public function has_filter(string $filtername): bool {
        // We do not check if the filtername is valid, only that it exists.
        // This is an existence check and there is no benefit to doing any more.
        return array_key_exists($filtername, $this->filters);
    }

    /**
     * Get the named filter.
     *
     * @param string $filtername
     * @return filter
     */
    public function get_filter(string $filtername): filter {
        if (!array_key_exists($filtername, $this->get_all_filtertypes())) {
            throw new UnexpectedValueException("The filter specified ({$filtername}) is invalid.");
        }

        if (!array_key_exists($filtername, $this->filters)) {
            throw new UnexpectedValueException("The filter specified ({$filtername}) has not been created.");
        }

        return $this->filters[$filtername];
    }

    /**
     * Confirm whether the filter has been correctly specified.
     *
     * @throws moodle_exception
     */
    public function check_validity(): void {
        // Ensure that all required filters are present.
        $missing = [];
        foreach (array_keys($this->get_required_filters()) as $filtername) {
            if (!array_key_exists($filtername, $this->filters)) {
                $missing[] = $filtername;
            }
        }

        if (!empty($missing)) {
            throw new moodle_exception(
                'missingrequiredfields',
                'core_table',
                '',
                implode(get_string('listsep', 'langconfig') . ' ', $missing)
            );
        }
    }

    /**
     * Get the list of required filters in an array of filtername => filter class type.
     *
     * @return array
     */
    protected function get_required_filters(): array {
        return [];
    }

    /**
     * Get the list of optional filters in an array of filtername => filter class type.
     *
     * @return array
     */
    protected function get_optional_filters(): array {
        return [];
    }

    /**
     * Get all filter valid types in an array of filtername => filter class type.
     *
     * @return array
     */
    public function get_all_filtertypes(): array {
        if ($this->filtertypes === null) {
            $required = $this->get_required_filters();
            $optional = $this->get_optional_filters();

            $conflicts = array_keys(array_intersect_key($required, $optional));

            if (!empty($conflicts)) {
                throw new InvalidArgumentException(
                    "Some filter types are both required, and optional: " . implode(', ', $conflicts)
                );
            }

            $this->filtertypes = array_merge($required, $optional);
            asort($this->filtertypes);
        }

        return $this->filtertypes;
    }

    /**
     * Serialize filterset.
     *
     * @return mixed|object
     */
    public function jsonSerialize() {
        return (object) [
            'jointype' => $this->get_join_type(),
            'filters' => $this->get_filters(),
        ];
    }
}
