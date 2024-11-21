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
 * Main factory.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\factory;

use block_xp\local\course_world;
use block_xp\local\config\course_world_config;
use block_xp\local\xp\badge_url_resolver;

/**
 * Main factory.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class default_badge_url_resolver_course_world_factory
        implements badge_url_resolver_course_world_factory {

    /** @var badge_url_resolver Resolver. */
    protected $adminresolver;

    /**
     * Constructor.
     *
     * @param badge_url_resolver $adminresolver The admin URL resolver.
     */
    public function __construct(badge_url_resolver $adminresolver) {
        $this->adminresolver = $adminresolver;
    }

    /**
     * Get the URL resolver.
     *
     * @param course_world $world The world.
     * @return block_xp\local\xp\badge_url_resolver
     */
    public function get_url_resolver(course_world $world) {
        $resolver = null;
        $config = $world->get_config();
        $custombadges = $config->get('enablecustomlevelbadges');

        if ($custombadges == course_world_config::CUSTOM_BADGES_NOOP) {
            // We're all set, use the badges present.
            $resolver = new \block_xp\local\xp\file_storage_badge_url_resolver($world->get_context(), 'block_xp', 'badges', 0);

        } else if ($custombadges == course_world_config::CUSTOM_BADGES_MISSING) {
            // The scenario here is that we are in a new course (not a legacy one),
            // and the badges have not been customised, so we will use the admin
            // ones. We will exit the 'missing' state when the teacher will
            // effectively custommise the levels.
            $resolver = $this->adminresolver;

        } else {
            // Probably the legacy state of course_world_config::CUSTOM_BADGES_NONE.
            // We use the standard look of the levels.
            $resolver = new \block_xp\local\xp\dummy_badge_url_resolver();
        }

        return $resolver;
    }

}
