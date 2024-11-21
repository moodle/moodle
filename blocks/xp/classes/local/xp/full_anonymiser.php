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
 * Full anonymiser.
 *
 * @package    block_xp
 * @copyright  2021 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\xp;

use moodle_url;

/**
 * Full anonymiser.
 *
 * @package    block_xp
 * @copyright  2021 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class full_anonymiser implements state_anonymiser {

    /** @var string The name to use. */
    protected $altname;
    /** @var moodle_url The name to use. */
    protected $altpic;
    /** @var \stdClass The user to replace with. */
    protected $anonuser;
    /** @var int[] The object IDs not to anonymise. */
    protected $exceptids;

    /**
     * Constructor.
     *
     * @param object $anonuser The user to use.
     * @param int[] $exceptids The object IDS to skip.
     * @param string $altname The name to use.
     * @param moodle_url $altpic The pic to use.
     */
    public function __construct($anonuser, $exceptids = [], $altname = '?', $altpic = null) {
        $this->exceptids = $exceptids;
        $this->anonuser = $anonuser;
        $this->altname = $altname;
        $this->altpic = $altpic;
    }

    /**
     * Return an anonymised state.
     *
     * @param state $state The state.
     * @return state
     */
    public function anonymise_state(state $state) {
        $keepasis = in_array($state->get_id(), $this->exceptids);
        if ($keepasis) {
            return $state;
        }

        if ($state instanceof user_state) {
            return new anonymised_user_state($state, $this->anonuser);
        }

        return new anonymised_state($state, $this->altname, $this->altpic);
    }

}
