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
 * Course world config.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\config;

use moodle_database;

/**
 * Course world config.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_world_config extends proxy_config {

    /** No ranking. */
    const RANK_OFF = 0;
    /** Ranking enabled. */
    const RANK_ON = 1;
    /** Relative ranking. Difference in XP between row and point of reference. */
    const RANK_REL = 2;
    /** Hide identity. */
    const IDENTITY_OFF = 0;
    /** Identity displayed. */
    const IDENTITY_ON = 1;

    /** When there is nothing to do about the default filters. */
    const DEFAULT_FILTERS_NOOP = 0;
    /** When the default filters are static, and non-editable. This is a legacy state for spotting v2.x. */
    const DEFAULT_FILTERS_STATIC = 1;
    /** When the defaults filters have not yet been added. */
    const DEFAULT_FILTERS_MISSING = 2;

    /** When hardcoded to no badges. Legacy of 'No custom badges'. */
    const CUSTOM_BADGES_NONE = 0;
    /** When there is nothing to do. Legacy of 'Yes, use custom badges'. */
    const CUSTOM_BADGES_NOOP = 1;
    /** When the default badges are missing. */
    const CUSTOM_BADGES_MISSING = 2;

    /** @var config The proxied config object. */
    protected $store;

    /**
     * Constructor.
     *
     * @param config $adminconfig The admin config.
     * @param moodle_database $db The DB.
     * @param int $courseid The course ID.
     */
    public function __construct(config $adminconfig, moodle_database $db, $courseid) {
        // What do we do here? We create a stack of configuration which the table_row_config
        // can get the defaults from if it needs to. This works so long as we do not introduce
        // keys in both course and admin configs which do not represent the same thing.
        // Note that we set the admin config as immutable, just to make sure we don't change it.
        $defaults = new config_stack([
            new immutable_config($adminconfig),
            new default_course_world_config(),
        ]);

        $config = new \block_xp\local\config\table_row_config($db, 'block_xp_config',
            $defaults, ['courseid' => $courseid], true);
        parent::__construct($config);
    }

}
