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
 * Event collection strategy.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\strategy;

use block_xp\local\factory\course_world_factory;
use block_xp\local\utils\user_utils;

/**
 * The global collection strategy.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class global_collection_strategy implements event_collection_strategy {

    /** @var array Contexts allowed. */
    protected $allowedcontexts = [];
    /** @var course_world_factory The course factory. */
    protected $worldfactory;

    /**
     * Constructor.
     *
     * @param course_world_factory $worldfactory The world.
     * @param int $contextmode The context mode.
     */
    public function __construct(course_world_factory $worldfactory, $contextmode) {
        $allowedcontexts = [CONTEXT_COURSE, CONTEXT_MODULE];
        if (!empty($contextmode) && $contextmode == CONTEXT_SYSTEM) {
            $allowedcontexts[] = CONTEXT_SYSTEM;
        }
        $this->allowedcontexts = $allowedcontexts;
        $this->worldfactory = $worldfactory;
    }

    /**
     * Collect an event.
     *
     * @param \core\event\base $event The event.
     * @return void
     */
    public function collect_event(\core\event\base $event) {
        $userid = $event->userid;

        if ($event->component === 'block_xp') {
            // Skip own events.
            return;
        } else if ($event->anonymous) {
            // Skip all the events marked as anonymous.
            return;
        } else if (!in_array($event->contextlevel, $this->allowedcontexts)) {
            // Ignore events that are not in the right context.
            return;
        } else if ($event->edulevel !== \core\event\base::LEVEL_PARTICIPATING) {
            // Ignore events that are not participating.
            return;
        } else if (!$event->get_context()) {
            // For some reason the context does not exist...
            return;
        }

        // Skip the events if the user does not have the right to earn XP.
        if (!user_utils::can_earn_points($event->get_context(), $userid)) {
            return;
        }

        $strategy = $this->worldfactory->get_world($event->courseid)->get_collection_strategy();
        if ($strategy instanceof event_collection_strategy) {
            $strategy->collect_event($event);
        }
    }

}
