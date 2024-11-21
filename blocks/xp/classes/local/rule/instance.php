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
 * Instance.
 *
 * @package    block_xp
 * @copyright  2024 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\rule;

/**
 * Instance.
 *
 * @package    block_xp
 * @copyright  2024 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface instance {

    /**
     * Get the ID.
     *
     * @return int
     */
    public function get_id(): int;

    /**
     * Get the context.
     *
     * @return \context
     */
    public function get_context(): \context;

    /**
     * Get the child context, if any.
     *
     * @return \context|null
     */
    public function get_child_context(): ?\context;

    /**
     * Get the points.
     *
     * @return int
     */
    public function get_points(): int;

    /**
     * Get the type name.
     *
     * @return string
     */
    public function get_type_name(): string;

    /**
     * Get the filter name.
     *
     * @return string
     */
    public function get_filter_name(): string;

    /**
     * Get the filter config.
     *
     * @return object
     */
    public function get_filter_config(): object;

}
