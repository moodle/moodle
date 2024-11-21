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
 * Action.
 *
 * @package    block_xp
 * @copyright  2024 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\action;

/**
 * Action.
 *
 * @package    block_xp
 * @copyright  2024 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface action {

    /**
     * Get the education level of the action.
     *
     * @return \context The context.
     */
    public function get_context(): \context;

    /**
     * Get the object ID.
     *
     * @return int|null
     */
    public function get_object_id(): ?int;

    /**
     * Get the time of the action.
     *
     * @return \DateTimeImmutable
     */
    public function get_time(): \DateTimeImmutable;

    /**
     * Get the type of the action.
     *
     * @return string
     */
    public function get_type(): string;

    /**
     * Get the user ID of the person performing the action.
     *
     * @return int
     */
    public function get_user_id(): int;

}
