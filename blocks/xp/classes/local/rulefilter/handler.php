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
 * Handler.
 *
 * @package    block_xp
 * @copyright  2024 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\rulefilter;

use block_xp\local\rulefilter\rulefilter;

/**
 * Handler.
 *
 * @package    block_xp
 * @copyright  2024 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface handler {

    /**
     * Get filter.
     *
     * @param string $name The name.
     * @return rulefilter|null
     */
    public function get_filter(string $name): ?rulefilter;

    /**
     * Get filter name.
     *
     * @param rulefilter $filter The filter.
     * @return string
     */
    public function get_filter_name(rulefilter $filter): string;

    /**
     * Get filter priority.
     *
     * @param rulefilter $filter The filter.
     * @return int
     */
    public function get_filter_priority(rulefilter $filter): int;

    /**
     * Get filter priority from name.
     *
     * @param string $name The name.
     * @return int
     */
    public function get_filter_priority_from_name(string $name): int;

    /**
     * Get all filters.
     *
     * @return rulefilter[] Indexed by name.
     */
    public function get_filters(): array;
}
