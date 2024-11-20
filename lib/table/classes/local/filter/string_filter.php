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
 * String filter.
 *
 * @package    core
 * @category   table
 * @copyright  2020 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace core_table\local\filter;

use TypeError;

/**
 * Class representing a string filter.
 *
 * @package    core
 * @copyright  2020 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class string_filter extends filter {
    /**
     * Add a value to the filter.
     *
     * @param string $values
     * @return self
     */
    public function add_filter_value($value): parent {
        if (!is_string($value)) {
            $type = gettype($value);
            if ($type === 'object') {
                $type = get_class($value);
            }

            throw new TypeError("The value supplied was of type '{$type}'. A string was expected.");
        }

        if (array_search($value, $this->filtervalues) !== false) {
            // Remove duplicates.
            return $this;
        }

        $this->filtervalues[] = $value;

        return $this;
    }
}
