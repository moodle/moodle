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
 * Context world factory.
 *
 * @package    block_xp
 * @copyright  2024 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\factory;

use block_xp\local\config\config;
use block_xp\local\world;

/**
 * Context world factory.
 *
 * @package    block_xp
 * @copyright  2024 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class default_context_world_factory implements context_world_factory {

    /** @var config The admin config. */
    protected $adminconfig;
    /** @var course_world_factory The course world factory. */
    protected $courseworldfactory;

    /**
     * Constructor.
     *
     * @param config $adminconfig The admin config.
     */
    public function __construct(config $adminconfig) {
        $this->adminconfig = $adminconfig;
    }

    /**
     * Get the world.
     *
     * @param \context $context The context.
     * @return world
     */
    public function get_world_from_context(\context $context): world {
        if ($this->adminconfig->get('context') == CONTEXT_SYSTEM) {
            $courseid = SITEID;
        } else {
            $context = $this->normalise_context($context);
            $courseid = $context instanceof \context_course ? $context->instanceid : SITEID;
        }

        if (!$this->courseworldfactory) {
            throw new \coding_exception('The context world factory requires the course world factory, for now.');
        }

        return $this->courseworldfactory->get_world($courseid);
    }

    /**
     * Normalise the context.
     *
     * @param \context $context The context.
     * @return \context The normalised context.
     */
    protected function normalise_context(\context $context): \context {
        $finalcontext = $context->get_course_context(false);
        if (!$finalcontext) {
            $finalcontext = \context_system::instance();
        }
        if ($finalcontext instanceof \context_course && $finalcontext->instanceid == SITEID) {
            $finalcontext = \context_system::instance();
        }
        // System, or course context, but not frontpage.
        return $finalcontext;
    }

    /**
     * Set the course world factory.
     *
     * @param course_world_factory $factory The factory.
     */
    public function set_course_world_factory(course_world_factory $factory) {
        $this->courseworldfactory = $factory;
    }

}
