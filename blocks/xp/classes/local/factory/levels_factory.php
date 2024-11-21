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
 * Levels factory.
 *
 * @package    block_xp
 * @copyright  2023 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\factory;

use block_xp\local\config\config;
use block_xp\local\course_world;
use block_xp\local\world;
use block_xp\local\xp\algo_levels_info;
use block_xp\local\xp\badge_url_resolver;
use block_xp\local\xp\levels_info;
use block_xp\local\xp\static_level;
use coding_exception;

/**
 * Levels factory.
 *
 * @package    block_xp
 * @copyright  2023 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class levels_factory implements levels_info_factory, level_factory {

    /** @var config The admin config. */
    protected $config;
    /** @var badge_url_resolver The admin badge URL resolver. */
    protected $badgeurlresolver;
    /** @var badge_url_resolver_course_world_factory */
    protected $badgeurlresolverfactory;

    /**
     * Constructor.
     *
     * @param config $config The admin config.
     * @param badge_url_resolver $badgeurlresolver The admin badge URL resolver.
     * @param badge_url_resolver_course_world_factory $badgeurlresolverfactory The badge URL resolver factory.
     */
    public function __construct(config $config, badge_url_resolver $badgeurlresolver,
            badge_url_resolver_course_world_factory $badgeurlresolverfactory) {
        $this->config = $config;
        $this->badgeurlresolver = $badgeurlresolver;
        $this->badgeurlresolverfactory = $badgeurlresolverfactory;
    }

    /**
     * Get the default levels info.
     *
     * @return levels_info
     */
    public function get_default_levels_info() {
        return $this->make_default_levels_info($this->badgeurlresolver);
    }

    /**
     * Get the world's level's info.
     *
     * @param world $world The world.
     * @return levels_info
     */
    public function get_world_levels_info(world $world) {
        if (!$world instanceof course_world) {
            throw new coding_exception('World not supported.');
        }

        $badgeurlresolver = $this->badgeurlresolverfactory->get_url_resolver($world);
        $data = json_decode($world->get_config()->get('levelsdata'), true);
        if (!$data) {
            return $this->make_default_levels_info($badgeurlresolver);
        }

        return new algo_levels_info($data, $badgeurlresolver, $this);
    }

    /**
     * Make the default resolver.
     *
     * The badge URL resolver depends on the context in which the default levels
     * will be used, so we pass it as parameter.
     *
     * @param badge_url_resolver $badgeurlresolver The badge URL resolver.
     * @return levels_info
     */
    protected function make_default_levels_info(badge_url_resolver $badgeurlresolver) {
        $data = json_decode($this->config->get('levelsdata'), true);
        if (!$data) {
            return algo_levels_info::make_from_defaults($badgeurlresolver, $this);
        }
        return new algo_levels_info($data, $badgeurlresolver, $this);
    }

    /**
     * Make a level.
     *
     * @param int $level The level.
     * @param int $xp The points.
     * @param array $metadata The metadata.
     * @param badge_url_resolver $badgeurlresolver The badge URL resolver.
     * @return \block_xp\local\xp\level
     */
    public function make_level($level, $xp, array $metadata = [], badge_url_resolver $badgeurlresolver = null) {
        return new static_level($level, $xp, $badgeurlresolver, $metadata);
    }

}
