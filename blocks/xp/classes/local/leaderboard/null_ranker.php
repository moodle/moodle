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
 * Ranker.
 *
 * @package    block_xp
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\leaderboard;

use block_xp\local\iterator\map_iterator;
use block_xp\local\xp\state;
use block_xp\local\xp\state_rank;

/**
 * Ranker.
 *
 * Ranks are always set to 0.
 *
 * @package    block_xp
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class null_ranker implements ranker {

    /**
     * Rank a state.
     *
     * @param state $state The state.
     * @return rank
     */
    public function rank_state(state $state) {
        return new state_rank(0, $state);
    }

    /**
     * Rank an ordered list of states.
     *
     * @param Traversable $states The states.
     * @return Traversable
     */
    public function rank_states($states) {
        return new map_iterator($states, [$this, 'rank_state']);
    }

}
