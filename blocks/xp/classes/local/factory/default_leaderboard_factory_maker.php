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
 * Leaderboard factory maker.
 *
 * @package    block_xp
 * @copyright  2024 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\factory;

use block_xp\local\config\config;
use block_xp\local\world;
use moodle_database;

/**
 * Leaderboard factory maker.
 *
 * @package    block_xp
 * @copyright  2024 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class default_leaderboard_factory_maker implements leaderboard_factory_maker {

    /** @var moodle_database The database. */
    protected $db;
    /** @var config The config. */
    protected $adminconfig;

    /**
     * Constructor.
     *
     * @param moodle_database $db The database.
     */
    public function __construct(moodle_database $db, config $adminconfig) {
        $this->db = $db;
        $this->adminconfig = $adminconfig;
    }

    /**
     * Get the leaderboard factory.
     *
     * @param world $world The world.
     * @param config $configoverride An optional config override.
     * @return leaderboard_factory
     */
    public function get_leaderboard_factory(world $world, config $configoverride = null): leaderboard_factory {
        return new world_leaderboard_factory($this->db, $world, $configoverride);
    }

}
