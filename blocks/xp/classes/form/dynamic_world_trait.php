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

namespace block_xp\form;

use block_xp\di;
use block_xp\local\course_world;
use context;
use moodle_url;

/**
 * Dynamic world trait.
 *
 * @package    block_xp
 * @copyright  2024 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait dynamic_world_trait {

    /** @var course_world The world. */
    private $world;

    /**
     * Get the world.
     */
    protected function get_world(): course_world {
        if (!$this->world) {
            $worldfactory = di::get('context_world_factory');
            $contextid = $this->optional_param('contextid', 0, PARAM_INT);
            $this->world = $worldfactory->get_world_from_context(context::instance_by_id($contextid));
        }
        return $this->world;
    }

    /**
     * Get the context for submission.
     *
     * @return context
     */
    protected function get_context_for_dynamic_submission(): context {
        return $this->get_world()->get_context();
    }

    /**
     * Check access.
     *
     * @return void
     */
    protected function check_access_for_dynamic_submission(): void {
        $perms = $this->get_world()->get_access_permissions();
        $perms->require_manage();
    }

    /**
     * Get the page URL.
     *
     * @return moodle_url
     */
    protected function get_page_url_for_dynamic_submission(): moodle_url {
        if (!$this->routename) {
            throw new \coding_exception('routenamenotdefined');
        }
        $urlresolver = di::get('url_resolver');
        return $urlresolver->reverse($this->routename, ['courseid' => $this->world->get_courseid()]);
    }

}
