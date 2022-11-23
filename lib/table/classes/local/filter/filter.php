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

use Countable;
use JsonSerializable;
use InvalidArgumentException;
use Iterator;

/**
 * Class representing a generic filter of any type.
 *
 * @package    core
 * @copyright  2020 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filter implements Countable, Iterator, JsonSerializable {

    /** @var int The default filter type (ANY) */
    const JOINTYPE_DEFAULT = 1;

    /** @var int None of the following match */
    const JOINTYPE_NONE = 0;

    /** @var int Any of the following match */
    const JOINTYPE_ANY = 1;

    /** @var int All of the following match */
    const JOINTYPE_ALL = 2;

    /** @var string The name of this filter */
    protected $name = null;

    /** @var int The join type currently in use */
    protected $jointype = self::JOINTYPE_DEFAULT;

    /** @var array The list of active filter values */
    protected $filtervalues = [];

    /** @var int[] valid join types */
    protected $jointypes = [
        self::JOINTYPE_NONE,
        self::JOINTYPE_ANY,
        self::JOINTYPE_ALL,
    ];

    /** @var int The current iterator position */
    protected $iteratorposition = null;

    /**
     * Constructor for the generic filter class.
     *
     * @param string $name The name of the current filter.
     * @param int $jointype The join to use when combining the filters.
     *                      See the JOINTYPE_ constants for further information on the field.
     * @param mixed[] $values An array of filter objects to be applied.
     */
    public function __construct(string $name, ?int $jointype = null, ?array $values = null) {
        $this->name = $name;

        if ($jointype !== null) {
            $this->set_join_type($jointype);
        }

        if (!empty($values)) {
            foreach ($values as $value) {
                $this->add_filter_value($value);
            }
        }
    }

    /**
     * Reset the iterator position.
     */
    public function reset_iterator(): void {
        $this->iteratorposition = null;
    }

    /**
     * Return the current filter value.
     */
    #[\ReturnTypeWillChange]
    public function current() {
        if ($this->iteratorposition === null) {
            $this->rewind();
        }

        if ($this->iteratorposition === null) {
            return null;
        }

        return $this->filtervalues[$this->iteratorposition];
    }

    /**
     * Returns the current position of the iterator.
     *
     * @return int
     */
    #[\ReturnTypeWillChange]
    public function key() {
        if ($this->iteratorposition === null) {
            $this->rewind();
        }

        return $this->iteratorposition;
    }

    /**
     * Rewind the Iterator position to the start.
     */
    public function rewind(): void {
        if ($this->iteratorposition === null) {
            $this->sort_filter_values();
        }

        if (count($this->filtervalues)) {
            $this->iteratorposition = 0;
        }
    }

    /**
     * Move to the next value in the list.
     */
    public function next(): void {
        ++$this->iteratorposition;
    }

    /**
     * Check if the current position is valid.
     *
     * @return bool
     */
    public function valid(): bool {
        return isset($this->filtervalues[$this->iteratorposition]);
    }

    /**
     * Return the number of contexts.
     *
     * @return int
     */
    public function count(): int {
        return count($this->filtervalues);
    }

    /**
     * Return the name of the filter.
     *
     * @return string
     */
    public function get_name(): string {
        return $this->name;
    }

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
     * Add a value to the filter.
     *
     * @param mixed $value
     * @return self
     */
    public function add_filter_value($value): self {
        if ($value === null) {
            // Null values are usually invalid.
            return $this;
        }

        if ($value === '') {
            // Empty strings are invalid.
            return $this;
        }

        if (array_search($value, $this->filtervalues) !== false) {
            // Remove duplicates.
            return $this;
        }

        $this->filtervalues[] = $value;

        // Reset the iterator position.
        $this->reset_iterator();

        return $this;
    }

    /**
     * Sort the filter values to ensure reliable, and consistent output.
     */
    protected function sort_filter_values(): void {
        // Sort the filter values to ensure consistent output.
        // Note: This is not a locale-aware sort, but we don't need this.
        // It's primarily for consistency, not for actual sorting.
        sort($this->filtervalues);

        $this->reset_iterator();
    }

    /**
     * Return the current filter values.
     *
     * @return mixed[]
     */
    public function get_filter_values(): array {
        $this->sort_filter_values();
        return $this->filtervalues;
    }

    /**
     * Serialize filter.
     *
     * @return mixed|object
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize() {
        return (object) [
            'name' => $this->get_name(),
            'jointype' => $this->get_join_type(),
            'values' => $this->get_filter_values(),
        ];
    }
}
