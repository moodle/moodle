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
 * World interface.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local;

use block_xp\local\xp\levels_info;
use block_xp\local\xp\state_store;

/**
 * World interface.
 *
 * The thing in which things are collecting experience.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface world {

    /**
     * Get the access permissions.
     *
     * @return \block_xp\local\permission\access_permissions
     */
    public function get_access_permissions();

    /**
     * Get the config.
     *
     * @return \block_xp\local\config\config
     */
    public function get_config();

    /**
     * Get the context.
     *
     * @return \context
     */
    public function get_context();

    /**
     * Return the collection strategy.
     *
     * @return \block_xp\local\strategy\collection_strategy
     */
    public function get_collection_strategy();

    /**
     * Get levels info.
     *
     * @return levels_info
     */
    public function get_levels_info();

    /**
     * Get the state store.
     *
     * @return state_store
     */
    public function get_store();

}
