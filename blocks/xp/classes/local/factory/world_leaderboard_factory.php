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
 * Leaderboard factory.
 *
 * @package    block_xp
 * @copyright  2024 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\factory;

use block_xp\local\config\config;
use block_xp\local\config\config_stack;
use block_xp\local\config\course_world_config;
use block_xp\local\config\immutable_config;
use block_xp\local\config\static_config;
use block_xp\local\course_world;
use block_xp\local\division\all_division;
use block_xp\local\division\division;
use block_xp\local\division\group_division;
use block_xp\local\leaderboard\anonymisable_leaderboard;
use block_xp\local\leaderboard\course_user_leaderboard;
use block_xp\local\leaderboard\leaderboard;
use block_xp\local\leaderboard\neighboured_leaderboard;
use block_xp\local\leaderboard\null_ranker;
use block_xp\local\leaderboard\ranker;
use block_xp\local\leaderboard\relative_ranker;
use block_xp\local\userfilter\user_filter;
use block_xp\local\utils\user_utils;
use block_xp\local\world;
use block_xp\local\xp\full_anonymiser;
use block_xp\local\xp\state_anonymiser;
use lang_string;
use moodle_database;

/**
 * Leaderboard factory.
 *
 * @package    block_xp
 * @copyright  2024 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class world_leaderboard_factory implements leaderboard_factory {

    /** @var config The config to refer to. */
    protected $config;
    /** @var \moodle_database The database. */
    protected $db;
    /** @var world The world. */
    protected $world;

    /**
     * Constructor.
     *
     * @param moodle_database $db The database.
     * @param world $world The world.
     * @param config|null $configoverride The configure override.
     */
    public function __construct(moodle_database $db, world $world, config $configoverride = null) {
        $this->db = $db;
        $this->world = $world;
        $this->config = new immutable_config(new config_stack([
            $configoverride ?? new static_config(),
            $world->get_config(),
        ]));
    }

    /**
     * Get the leaderboard.
     *
     * This is the primary method for obtaining a leaderboard. It will check which
     * leaderboard to present based on the logged-in user.
     *
     * @return leaderboard
     */
    public function get_leaderboard(): leaderboard {
        global $USER;
        $division = $this->get_default_division($USER->id);
        return $this->get_leaderboard_for_division($division);
    }

    /**
     * Get the leaderboard for a division.
     *
     * This does not validate that the logged-in user can access the given division.
     *
     * @param division $division The division.
     */
    public function get_leaderboard_for_division(division $division): leaderboard {
        global $USER;
        return $this->assemble_leaderboard($USER->id, $division->get_user_filter());
    }

    /**
     * Get the leaderboard
     *
     * @param int $targetuserid The target user ID.
     * @param user_filter|null $userfilter The user filter.
     * @return leaderboard
     */
    protected function assemble_leaderboard(int $targetuserid, user_filter $userfilter = null): leaderboard {

        // How is the rank computed?
        $ranker = $this->get_ranker($targetuserid);

        // Find out what columns to use.
        $columns = $this->get_columns();

        // Get the leaderboard.
        $leaderboard = $this->get_leaderboard_instance($columns, $ranker);

        // We future proof with a method check, but in reality we probably need an interface.
        // The problem with the interface is that we wrap the leaderboard so much that it would be
        // needed in each. It's a bit of a mess that will eventually require a big clean.
        if ($userfilter && method_exists($leaderboard, 'set_user_filter')) {
            $leaderboard->set_user_filter($userfilter);
        }

        // Wrap?
        $leaderboard = $this->wrap_leaderboard($leaderboard, $targetuserid);

        // Is the leaderboard anonymised?
        $anonymiser = $this->get_anonymiser($targetuserid);
        if ($anonymiser) {
            $leaderboard = new anonymisable_leaderboard($leaderboard, $anonymiser);
        }

        return $leaderboard;
    }

    /**
     * Get the anonymiser.
     *
     * @param int $targetuserid The target user ID.
     * @return state_anonymiser|null
     */
    protected function get_anonymiser(int $targetuserid): ?state_anonymiser {
        $anonymiser = null;
        if ($this->config->get('identitymode') != course_world_config::IDENTITY_ON) {
            $anonymiser = new full_anonymiser(guest_user(), [$targetuserid], '?', user_utils::default_picture());
        }
        return $anonymiser;
    }

    /**
     * Get the columns
     *
     * @return array
     */
    protected function get_columns(): array {
        $config = $this->config;
        $columns = [];

        if ($config->get('rankmode') != course_world_config::RANK_OFF) {
            if ($config->get('rankmode') == course_world_config::RANK_REL) {
                $columns['level'] = new lang_string('level', 'block_xp');
                $columns['rank'] = new lang_string('difference', 'block_xp');
            } else {
                $columns['rank'] = new lang_string('rank', 'block_xp');
                $columns['level'] = new lang_string('level', 'block_xp');
            }
        } else {
            $columns['level'] = new lang_string('level', 'block_xp');
        }
        $columns['fullname'] = new lang_string('participant', 'block_xp');

        $additionalcols = explode(',', $config->get('laddercols'));
        if (in_array('xp', $additionalcols)) {
            $columns['xp'] = new lang_string('total', 'block_xp');
        }
        if (in_array('progress', $additionalcols)) {
            $columns['progress'] = new lang_string('progress', 'block_xp');
        }

        return $columns;
    }

    /**
     * Get the default division.
     *
     * @param int $targetuserid The target user ID.
     * @return division
     */
    protected function get_default_division(int $targetuserid): division {
        if ($this->world instanceof course_world) {
            $groupid = user_utils::get_primary_group_id($this->world->get_courseid(), $targetuserid);
            if ($groupid) {
                return new group_division($groupid);
            }
        }
        return new all_division();
    }

    /**
     * Get the leaderboard instance
     *
     * @param array $columns The columns.
     * @param ranker|null $ranker The ranker.
     * @return leaderboard
     */
    protected function get_leaderboard_instance(array $columns, ranker $ranker = null): leaderboard {
        if (!$this->world instanceof course_world) {
            throw new \coding_exception('This factory only supports course_world instances.');
        }
        return new course_user_leaderboard(
            $this->db,
            $this->world->get_levels_info(),
            $this->world->get_courseid(),
            $columns,
            $ranker
        );
    }

    /**
     * Get the ranker.
     *
     * @param int $targetuserid The target user ID.
     * @return ranker|null
     */
    protected function get_ranker(int $targetuserid): ?ranker {
        $ranker = null;
        $config = $this->config;
        if ($config->get('rankmode') == course_world_config::RANK_OFF) {
            $ranker = new null_ranker();
        } else if ($config->get('rankmode') == course_world_config::RANK_REL) {
            $ranker = new relative_ranker($this->world->get_store()->get_state($targetuserid));
        }
        return $ranker;
    }

    /**
     * Wrap the leaderboard.
     *
     * @param leaderboard $leaderboard The leaderboard.
     * @param int $targetuserid The target user ID.
     * @return leaderboard
     */
    protected function wrap_leaderboard(leaderboard $leaderboard, int $targetuserid): leaderboard {

        // Do we only display the neighbours?
        $config = $this->config;
        if ($config->get('neighbours')) {
            $leaderboard = new neighboured_leaderboard($leaderboard, $targetuserid, $config->get('neighbours'),
                $this->world->get_access_permissions()->can_manage($targetuserid));
        }

        return $leaderboard;
    }

}
