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
 * State rank.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\xp;

/**
 * State rank.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class state_rank implements rank {

    /** @var int The rank. */
    protected $rank;
    /** @var state The state. */
    protected $state;

    /**
     * Constructor.
     *
     * @param int $rank The rank.
     * @param state $state The state.
     */
    public function __construct($rank, state $state) {
        $this->rank = $rank;
        $this->state = $state;
    }

    /**
     * Get the rank of the state.
     *
     * @return int
     */
    public function get_rank() {
        return $this->rank;
    }

    /**
     * The state.
     *
     * @return state
     */
    public function get_state() {
        return $this->state;
    }

}
