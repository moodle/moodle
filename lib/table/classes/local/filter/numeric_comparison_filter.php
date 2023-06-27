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
 * Integer comparison filter to allow a comparison such as "> 42".
 *
 * @package    core
 * @category   table
 * @copyright  2020 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace core_table\local\filter;

use InvalidArgumentException;
use TypeError;

/**
 * Class representing an integer filter.
 *
 * @package    core
 * @copyright  2020 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class numeric_comparison_filter extends filter {
    /**
     * Get the authoritative direction.
     *
     * @param string $direction The supplied direction
     * @return string The authoritative direction
     */
    protected function get_direction(string $direction): string {
        $validdirections = [
            '=' => '==',
            '==' => '==',
            '===' => '===',

            '>' => '>',
            '=>' => '=>',
            '<' => '<',
            '<=' => '<=',
        ];

        if (!array_key_exists($direction, $validdirections)) {
            throw new InvalidArgumentException("Invalid direction specified '{$direction}'.");
        }

        return $validdirections[$direction];
    }

    /**
     * Add a value to the filter.
     *
     * @param string $value A json-encoded array containing a direction, and comparison value
     * @return self
     */
    public function add_filter_value($value): parent {
        if (!is_string($value)) {
            $type = gettype($value);
            if ($type === 'object') {
                $type = get_class($value);
            }

            throw new TypeError(
                "The value supplied was of type '{$type}'. A string representing a json-encoded value was expected."
            );
        }

        $data = json_decode($value);

        if ($data === null) {
            throw new InvalidArgumentException(
                "A json-encoded object containing both a direction, and comparison value was expected."
            );
        }

        if (!is_object($data)) {
            $type = gettype($value);
            throw new InvalidArgumentException(
                "The value supplied was a json encoded '{$type}'. " .
                "An object containing both a direction, and comparison value was expected."
            );
        }

        if (!property_exists($data, 'direction')) {
            throw new InvalidArgumentException("A 'direction' must be provided.");
        }
        $direction = $this->get_direction($data->direction);

        if (!property_exists($data, 'value')) {
            throw new InvalidArgumentException("A 'value' must be provided.");
        }
        $value = $data->value;

        if (!is_numeric($value)) {
            $type = gettype($value);
            if ($type === 'object') {
                $type = get_class($value);
            }

            throw new TypeError("The value supplied was of type '{$type}'. A numeric value was expected.");
        }

        $fullvalue = (object) [
            'direction' => $direction,
            'value' => $value,
        ];

        if (array_search($fullvalue, $this->filtervalues) !== false) {
            // Remove duplicates.
            return $this;
        }

        $this->filtervalues[] = $fullvalue;

        return $this;
    }
}
