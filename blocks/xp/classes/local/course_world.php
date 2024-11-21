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
 * Course World.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local;

use context;
use context_course;
use context_system;
use moodle_database;
use block_xp\local\config\config;
use block_xp\local\config\course_world_config;
use block_xp\local\factory\badge_url_resolver_course_world_factory;
use block_xp\local\factory\levels_info_factory;

/**
 * Course World.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_world implements world {

    /** @var config The config. */
    protected $config;
    /** @var context The context. */
    protected $context;
    /** @var int The course ID. */
    protected $courseid;
    /** @var moodle_database The DB. */
    protected $db;
    /** @var levels_info The levels info. */
    protected $levelsinfo;
    /** @var course_user_event_collection_logger The logger. */
    protected $logger;
    /** @var access_permissions The access permissions. */
    protected $perms;
    /** @var course_user_state_store The store. */
    protected $store;
    /** @var collection_strategy The collection strategy. */
    protected $strategy;
    /** @var course_filter_manager The filter manager. */
    protected $filtermanager;
    /** @var badge_url_resolver_course_world_factory The badge URL resolver factory. */
    protected $urlresolverfactory;
    /** @var object Observer object cache. */
    protected $statestoreobserver;
    /** @var levels_info_factory|null The levels info factory. */
    protected $levelsinfofactory;

    /**
     * Constructor.
     *
     * @param config $config The course config.
     * @param moodle_database $db The DB.
     * @param int $courseid The course ID.
     * @param badge_url_resolver_course_world_factory $urlresolverfactory The badge URL resolver factory.
     * @param levels_info_factory|null $levelsinfofactory The levels info factory.
     */
    public function __construct(config $config, moodle_database $db, $courseid,
            badge_url_resolver_course_world_factory $urlresolverfactory,
            levels_info_factory $levelsinfofactory = null) {
        $this->config = $config;
        $this->courseid = $courseid;
        $this->db = $db;
        $this->levelsinfofactory = $levelsinfofactory;
        $this->urlresolverfactory = $urlresolverfactory;

        // TODO We should move the context out of here, and inject the permissions instead.
        if ($courseid == SITEID) {
            $this->context = context_system::instance();
        } else {
            $this->context = context_course::instance($courseid);
        }

        $this->perms = new \block_xp\local\permission\context_permissions($this->context);
    }

    public function get_access_permissions() {
        return $this->perms;
    }

    public function get_config() {
        return $this->config;
    }

    public function get_collection_strategy() {
        if (!$this->strategy) {
            $this->strategy = new \block_xp\local\strategy\course_world_collection_strategy(
                $this->get_context(),
                $this->get_config(),
                $this->get_store(),
                $this->get_filter_manager(),
                $this->get_collection_logger(),
                $this->get_level_up_notification_service()
            );
        }
        return $this->strategy;
    }

    /**
     * Get context.
     *
     * @return context
     */
    public function get_context() {
        return $this->context;
    }

    /**
     * Get course ID.
     *
     * @return int
     */
    public function get_courseid() {
        return $this->courseid;
    }

    /**
     * Get filter manager.
     *
     * @return xp\course_filter_manager
     */
    public function get_filter_manager() {
        if (!$this->filtermanager) {
            $this->filtermanager = new \block_xp\local\xp\course_filter_manager($this->db, $this->courseid);

            $config = $this->get_config();
            $state = $config->get('defaultfilters');
            if ($state == course_world_config::DEFAULT_FILTERS_NOOP) {
                // Early bail.
                return $this->filtermanager;

            } else if ($state == course_world_config::DEFAULT_FILTERS_MISSING) {
                // The default filters were not applied yet.
                $this->filtermanager->import_default_filters();
                $config->set('defaultfilters', course_world_config::DEFAULT_FILTERS_NOOP);

            } else if ($state == course_world_config::DEFAULT_FILTERS_STATIC) {
                // We are in a legacy state, convert.
                $this->filtermanager->convert_static_filters_to_regular();
                $config->set('defaultfilters', course_world_config::DEFAULT_FILTERS_NOOP);
            }

        }
        return $this->filtermanager;
    }

    public function get_levels_info() {
        if (!$this->levelsinfo) {

            // We must apply this check in case an older version of XP+ is used with this.
            if ($this->levelsinfofactory) {
                $this->levelsinfo = $this->levelsinfofactory->get_world_levels_info($this);

            } else {
                $resolver = $this->urlresolverfactory->get_url_resolver($this);
                $config = $this->get_config();
                $data = json_decode($config->get('levelsdata'), true);
                if (!$data) {
                    $this->levelsinfo = \block_xp\local\xp\algo_levels_info::make_from_defaults($resolver);
                } else {
                    $this->levelsinfo = new \block_xp\local\xp\algo_levels_info($data, $resolver);
                }
            }

        }
        return $this->levelsinfo;
    }

    /**
     * Get level up notification service.
     *
     * @return notification\course_level_up_notification_service
     */
    public function get_level_up_notification_service() {
        // TODO We could put that somewhere else.
        return new \block_xp\local\notification\course_level_up_notification_service($this->courseid);
    }

    /**
     * Get the level up state store observer.
     *
     * @return \block_xp\local\observer\level_up_state_store_observer
     */
    protected function get_level_up_state_store_observer() {
        return $this->get_state_store_observer();
    }

    /**
     * Get the points increase state store observer.
     *
     * @return \block_xp\local\observer\points_increased_state_store_observer
     */
    protected function get_points_increased_state_store_observer() {
        return $this->get_state_store_observer();
    }

    /**
     * Get the state store observer.
     *
     * @return object
     */
    protected function get_state_store_observer() {
        if (!$this->statestoreobserver) {
            $this->statestoreobserver = new \block_xp\local\observer\default_state_store_observer($this->context, $this->config,
                $this->get_level_up_notification_service());
        }
        return $this->statestoreobserver;
    }

    public function get_store() {
        if (!$this->store) {
            $this->store = new \block_xp\local\xp\course_user_state_store(
                $this->db,
                $this->get_levels_info(),
                $this->get_courseid(),
                $this->get_collection_logger(),
                $this->get_level_up_state_store_observer(),
                $this->get_points_increased_state_store_observer()
            );
        }
        return $this->store;
    }

    /**
     * Get logger.
     *
     * @return logger\course_user_event_collection_logger
     */
    private function get_collection_logger() {
        if (!$this->logger) {
            $this->logger = new \block_xp\local\logger\course_user_event_collection_logger($this->db, $this->courseid);
        }
        return $this->logger;
    }

    /**
     * Get the user recent activity repository.
     *
     * @return user_recent_activity_repository
     */
    public function get_user_recent_activity_repository() {
        return new \block_xp\local\activity\course_log_recent_activity_repository($this->db, $this->courseid);
    }

    /**
     * Reset the filters to defaults.
     *
     * This API should not be public, but due to the inconsistency in the implementation
     * of filters and how the config has an impact on it, it is cleaner to declare this
     * method as it can act upon both the config and the filters at once.
     *
     * This method should only be used temporarily, it can be removed at any time!
     *
     * @param int|null $category The filters category.
     * @return void
     */
    public function reset_filters_to_defaults($category = null) {

        // This guarantees a fresh and migrated list of filters.
        $this->filtermanager = null;
        $filtermanager = $this->get_filter_manager();

        // Reset everything if there is no category.
        if ($category === null) {
            $filtermanager->purge();
            $this->get_config()->set('defaultfilters', course_world_config::DEFAULT_FILTERS_MISSING);
            $this->filtermanager = null;
            return;
        }

        // Reset the filters for the given category.
        $filtermanager->purge($category);
        $filtermanager->import_default_filters($category);
    }

}
