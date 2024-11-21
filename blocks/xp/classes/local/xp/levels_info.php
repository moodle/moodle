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
 * Levels info interface.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\xp;

/**
 * Levels info interface.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface levels_info extends \JsonSerializable {

    /**
     * Get the number of levels.
     *
     * @return int
     */
    public function get_count();

    /**
     * Get the level.
     *
     * @param int $level The level as a number.
     * @return level
     */
    public function get_level($level);

    /**
     * Get the level for a certain amount of experience points.
     *
     * @param int $xp The experience points.
     * @return level
     */
    public function get_level_from_xp($xp);

    /**
     * Get the levels.
     *
     * @return level[]
     */
    public function get_levels();

}
