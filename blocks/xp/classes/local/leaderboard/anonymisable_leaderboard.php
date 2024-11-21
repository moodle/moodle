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
 * Anonymised leaderboard.
 *
 * @package    block_xp
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\leaderboard;

use block_xp\local\iterator\map_iterator;
use block_xp\local\sql\limit;
use block_xp\local\xp\rank;
use block_xp\local\xp\state_anonymiser;
use block_xp\local\xp\state_rank;

/**
 * Anonymisable leaderboard.
 *
 * @package    block_xp
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class anonymisable_leaderboard implements leaderboard {

    /** @var leaderboard The leaderboard to anonymise. */
    protected $leaderboard;
    /** @var state_anonymiser The anonymiser. */
    protected $anonymiser;

    /**
     * Constructor.
     *
     * @param leaderboard $leaderboard The leaderboard.
     * @param state_anonymiser $anonymiser The anonymiser.
     */
    public function __construct(leaderboard $leaderboard, state_anonymiser $anonymiser) {
        $this->leaderboard = $leaderboard;
        $this->anonymiser = $anonymiser;
    }

    /**
     * Anonymise the state rank.
     *
     * @param rank $rank The state rank.
     * @return rank
     */
    protected function anonymise_rank(rank $rank) {
        return new state_rank($rank->get_rank(), $this->anonymiser->anonymise_state($rank->get_state()));
    }

    /**
     * Get the leaderboard columns.
     *
     * @return array Where keys are column identifiers and values are lang_string objects.
     */
    public function get_columns() {
        return $this->leaderboard->get_columns();
    }

    /**
     * Get the number of rows in the leaderboard.
     *
     * @return int
     */
    public function get_count() {
        return $this->leaderboard->get_count();
    }

    /**
     * Get the number of rows in the leaderboard.
     *
     * @param int $id The object ID.
     * @return int
     */
    public function get_position($id) {
        return $this->leaderboard->get_position($id);
    }


    /**
     * Get the rank of an object.
     *
     * @param int $id The object ID.
     * @return rank|null
     */
    public function get_rank($id) {
        $staterank = $this->leaderboard->get_rank($id);
        return $staterank === null ? null : $this->anonymise_rank($staterank);
    }


    /**
     * Get the ranking.
     *
     * @param limit $limit The limit.
     * @return Traversable
     */
    public function get_ranking(limit $limit) {
        $ranking = $this->leaderboard->get_ranking($limit);
        return new map_iterator($ranking, function($state) {
            return $this->anonymise_rank($state);
        });
    }

}
