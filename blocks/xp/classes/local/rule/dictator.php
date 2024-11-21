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
 * Dictator.
 *
 * @package    block_xp
 * @copyright  2024 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\rule;

use block_xp\local\rule\instance;

/**
 * Dictator.
 *
 * @package    block_xp
 * @copyright  2024 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface dictator {

    /**
     * Count rules in context.
     *
     * @param \context $storecontext The context.
     * @param \context|null $childcontext The child context.
     * @param array $options Some options (expected to support type, and filter).
     * @return instance[]
     */
    public function count_rules_in_context(\context $storecontext, \context $childcontext = null, array $options = []);

    /**
     * Get the effective rules.
     *
     * @param \context $storecontext The context.
     * @param \context $actioncontext The child context.
     * @return instance[]
     */
    public function get_effective_rules(\context $storecontext, \context $actioncontext);

    /**
     * Get the effective rules grouped by type.
     *
     * @param \context $storecontext The context.
     * @param \context $actioncontext The child context.
     * @return instance[]
     */
    public function get_effective_rules_grouped_by_type(\context $storecontext, \context $actioncontext);

    /**
     * Get rules of particular type in context.
     *
     * @param \context $storecontext The context.
     * @param \context|null $childcontext The child context.
     * @return instance[]
     */
    public function get_rules_in_context(\context $storecontext, \context $childcontext = null);

    /**
     * Get rules of particular types in context.
     *
     * @param \context $storecontext The context.
     * @param string[] $types The type names.
     * @param \context|null $childcontext The child context.
     * @return instance[]
     */
    public function get_rules_of_types_in_context(\context $storecontext, array $types, \context $childcontext = null);

    /**
     * Sort the rules by priority.
     *
     * @param instance[] $rules The rules.
     * @return instance[] The most important rules first.
     */
    public function sort_rules_by_priority($rules): array;

}
