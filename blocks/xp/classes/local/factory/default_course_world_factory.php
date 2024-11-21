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
 * Course world factory.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\factory;

use moodle_database;
use block_xp\local\config\config;
use block_xp\local\config\config_stack;
use block_xp\local\config\course_world_config;
use block_xp\local\config\filtered_config;
use block_xp\local\config\immutable_config;

/**
 * Course world factory.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class default_course_world_factory implements course_world_factory {

    /** @var config The admin config. */
    protected $adminconfig;
    /** @var config The config overrides. */
    protected $configoverrides;
    /** @var moodle_database The DB. */
    protected $db;
    /** @var badge_url_resolver_course_world_factory The badge URL resolver factory. */
    protected $urlresolverfactory;
    /** @var course_world[] World cache. */
    protected $worlds = [];
    /** @var levels_info_factory The levels info factory. */
    protected $levelsinfofactory;

    /**
     * Constructor.
     *
     * @param config $adminconfig The admin config.
     * @param moodle_database $db The DB.
     * @param badge_url_resolver_course_world_factory $urlresolverfactory The badge URL resolver factory.
     * @param config $adminconfiglocked The locked config.
     * @param levels_info_factory $levelsinfofactory The levels info factory.
     */
    public function __construct(config $adminconfig, moodle_database $db,
            badge_url_resolver_course_world_factory $urlresolverfactory,
            config $adminconfiglocked,
            levels_info_factory $levelsinfofactory) {

        $this->adminconfig = $adminconfig;
        $this->db = $db;
        $this->urlresolverfactory = $urlresolverfactory;
        $this->levelsinfofactory = $levelsinfofactory;

        // The overrides for a course config are based on the admin settings, for those admin settings that have
        // had their locked status set to true. The whole is immutable to prevent writes on the admin settings.
        $this->configoverrides = new immutable_config(
            new filtered_config($this->adminconfig, array_keys(array_filter($adminconfiglocked->get_all())))
        );
    }

    /**
     * Get the world.
     *
     * @param int $courseid Course ID.
     * @return block_xp\local\course_world
     */
    public function get_world($courseid) {

        // When the block was set up for the whole site we attach it to the site course.
        $sitewide = $this->adminconfig->get('context') == CONTEXT_SYSTEM;
        if ($sitewide) {
            $courseid = SITEID;
        }

        $courseid = intval($courseid);
        if (!isset($this->worlds[$courseid])) {
            $courseconfig = new course_world_config($this->adminconfig, $this->db, $courseid);
            $config = new config_stack([$this->configoverrides, $courseconfig]);

            $this->worlds[$courseid] = new \block_xp\local\course_world($config, $this->db, $courseid, $this->urlresolverfactory,
                $this->levelsinfofactory);
        }
        return $this->worlds[$courseid];
    }

}
