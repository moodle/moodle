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

namespace communication_matrix;

/**
 * class matrix_constants to have one location to store all constants related to matrix.
 *
 * @package    communication_matrix
 * @copyright  2023 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class matrix_constants {
    /**
     * User default power level for matrix.
     */
    public const POWER_LEVEL_DEFAULT = 0;

    /**
     * User moderator power level for matrix.
     */
    public const POWER_LEVEL_MODERATOR = 50;

    /**
     * User moderator power level for matrix.
     */
    public const POWER_LEVEL_MOODLE_MODERATOR = 51;

    /**
     * User power level for matrix associated to moodle site admins. It is a custom power level for site admins.
     */
    public const POWER_LEVEL_MOODLE_SITE_ADMIN = 90;

    /**
     * User maximum power level for matrix. This is only associated to the token user to allow god mode actions.
     */
    public const POWER_LEVEL_MAXIMUM = 100;
}
