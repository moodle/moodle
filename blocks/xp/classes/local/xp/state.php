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
 * State interface.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\xp;

/**
 * State interface.
 *
 * The XP state of a thing.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface state {

    /**
     * Get the ID of the thing.
     *
     * @return int
     */
    public function get_id();

    /**
     * Get the level of the thing.
     *
     * @return level
     */
    public function get_level();

    /**
     * Get the ratio of completion in the level.
     *
     * @return float
     */
    public function get_ratio_in_level();

    /**
     * Get the XP to gain in the level.
     *
     * @return int
     */
    public function get_total_xp_in_level();

    /**
     * Get the total XP accrued.
     *
     * @return int
     */
    public function get_xp();

    /**
     * Get XP accrued in their level.
     *
     * @return int
     */
    public function get_xp_in_level();

}
