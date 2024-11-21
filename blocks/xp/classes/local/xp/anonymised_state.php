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
 * Anonymise a state.
 *
 * @package    block_xp
 * @copyright  2019 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\xp;

use moodle_url;

/**
 * Anonymise a state.
 *
 * @package    block_xp
 * @copyright  2019 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class anonymised_state implements state_with_subject {

    /** @var string The name. */
    protected $name;
    /** @var moodle_url|null The pic. */
    protected $pic;
    /** @var state_with_subject The state. */
    protected $state;

    /**
     * Constructor.
     *
     * @param state_with_subject $state The state to anonymise.
     * @param string $name The new name.
     * @param moodle_url $pic The new pic.
     */
    public function __construct(state_with_subject $state, $name, $pic = null) {
        $this->state = $state;
        $this->name = $name;
        $this->pic = $pic;
    }

    /**
     * Get the ID of the thing.
     *
     * @return int
     */
    public function get_id() {
        return $this->state->get_id();
    }

    /**
     * Get the level of the thing.
     *
     * @return level
     */
    public function get_level() {
        return $this->state->get_level();
    }

    /**
     * Get the link to the subject.
     *
     * @return moodle_url|null
     */
    public function get_link() {
        return null;
    }

    /**
     * Get the name of the subject.
     *
     * @return string
     */
    public function get_name() {
        return $this->name;
    }

    /**
     * Get the picture as a URL.
     *
     * @return moodle_url|null
     */
    public function get_picture() {
        return $this->pic;
    }

    /**
     * Get the ratio of completion in the level.
     *
     * @return float
     */
    public function get_ratio_in_level() {
        return $this->state->get_ratio_in_level();
    }

    /**
     * Get the XP to gain in the level.
     *
     * @return int
     */
    public function get_total_xp_in_level() {
        return $this->state->get_total_xp_in_level();
    }

    /**
     * Get the total XP accrued.
     *
     * @return int
     */
    public function get_xp() {
        return $this->state->get_xp();
    }

    /**
     * Get XP accrued in their level.
     *
     * @return int
     */
    public function get_xp_in_level() {
        return $this->state->get_xp_in_level();
    }

}
