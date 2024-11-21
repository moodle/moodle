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
 * State store.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\xp;

use block_xp\local\reason\reason;

/**
 * State store.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface state_store_with_reason extends state_store {

    /**
     * Add a certain amount of experience points.
     *
     * @param int $id The receiver.
     * @param int $amount The amount.
     * @param reason $reason A reason.
     */
    public function increase_with_reason($id, $amount, reason $reason);

    /**
     * Set the amount of experience points.
     *
     * @param int $id The receiver.
     * @param int $amount The amount.
     * @param reason $reason A reason.
     */
    public function set_with_reason($id, $amount, reason $reason);

}
