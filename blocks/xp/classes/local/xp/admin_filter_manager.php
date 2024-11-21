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
 * Admin filter manager.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\xp;

use moodle_database;
use block_xp\di;
use block_xp\local\config\course_world_config;
use coding_exception;

/**
 * Admin filter manager class.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_filter_manager {

    /** Key under which we check if the filters were customised. */
    const CUSTOMISED_CONFIG_KEY = 'admin_filter_manager:customised';

    /** @var moodle_database The DB. */
    protected $db;

    /**
     * Constructor.
     *
     * @param moodle_database $db The DB.
     */
    public function __construct(moodle_database $db) {
        $this->db = $db;
    }

    /**
     * Get all the filters.
     *
     * @return block_xp_filter[]
     */
    public function get_all_filters() {
        if (!$this->is_customised()) {
            // Early bail. We assume that we do not have any default filters for other categories.
            return $this->get_default_filters(\block_xp_filter::CATEGORY_EVENTS);
        }

        $results = $this->db->get_recordset('block_xp_filters', ['courseid' => 0], 'sortorder ASC, id ASC');
        $filters = [];
        foreach ($results as $key => $filter) {
            $filters[$filter->id] = \block_xp_filter::load_from_data($filter);
        }
        $results->close();
        return $filters;
    }

    /**
     * Default admin filters.
     *
     * @param int $category The category.
     * @return block_xp_filter[]
     */
    protected function get_default_filters($category = \block_xp_filter::CATEGORY_EVENTS) {
        if ($category != \block_xp_filter::CATEGORY_EVENTS) {
            return [];
        }

        $d = new \block_xp_rule_property(\block_xp_rule_base::EQ, 'd', 'crud');
        $c = new \block_xp_rule_property(\block_xp_rule_base::EQ, 'c', 'crud');
        $r = new \block_xp_rule_property(\block_xp_rule_base::EQ, 'r', 'crud');
        $u = new \block_xp_rule_property(\block_xp_rule_base::EQ, 'u', 'crud');

        // Skip those as they duplicate other more low level actions.
        $bcmv = new \block_xp_rule_event('\mod_book\event\course_module_viewed');
        $dsc = new \block_xp_rule_event('\mod_forum\event\discussion_subscription_created');
        $sc = new \block_xp_rule_event('\mod_forum\event\subscription_created');
        $as = new \block_xp_rule_property(\block_xp_rule_base::CT, 'assessable_submitted', 'eventname');
        $au = new \block_xp_rule_property(\block_xp_rule_base::CT, 'assessable_uploaded', 'eventname');

        $list = [];

        $ruleset = new \block_xp_ruleset([$bcmv, $dsc, $sc, $as, $au], \block_xp_ruleset::ANY);
        $data = ['rule' => $ruleset, 'points' => 0];
        $list[] = \block_xp_filter::load_from_data($data);

        $data = ['rule' => $c, 'points' => 45];
        $list[] = \block_xp_filter::load_from_data($data);

        $data = ['rule' => $r, 'points' => 9];
        $list[] = \block_xp_filter::load_from_data($data);

        $data = ['rule' => $u, 'points' => 3];
        $list[] = \block_xp_filter::load_from_data($data);

        $data = ['rule' => $d, 'points' => 0];
        $list[] = \block_xp_filter::load_from_data($data);
        return $list;
    }

    /**
     * Get the filters defined by the admin.
     *
     * @param int $category The matching category.
     * @return block_xp_filter[]
     */
    public function get_filters($category = \block_xp_filter::CATEGORY_EVENTS) {
        if (!$this->is_customised()) {
            // Early bail, saving one query.
            return $this->get_default_filters($category);
        }

        $results = $this->db->get_recordset('block_xp_filters', ['courseid' => 0, 'category' => $category],
            'sortorder ASC, id ASC');
        $filters = [];
        foreach ($results as $key => $filter) {
            $filters[$filter->id] = \block_xp_filter::load_from_data($filter);
        }
        $results->close();
        return $filters;
    }

    /**
     * Whether the admin filters were customised.
     *
     * @return bool
     */
    public function is_customised() {
        return get_config('block_xp', self::CUSTOMISED_CONFIG_KEY);
    }

    /**
     * Mark the filters as customised.
     *
     * @return void
     */
    public function mark_as_customised() {
        set_config(self::CUSTOMISED_CONFIG_KEY, 1, 'block_xp');
    }

    /**
     * Reset the admin rules to their defaults.
     *
     * @return void
     */
    public function reset() {
        $this->db->delete_records('block_xp_filters', ['courseid' => 0]);
        unset_config(self::CUSTOMISED_CONFIG_KEY, 'block_xp');
    }

    /**
     * Reset all courses to defaults.
     *
     * Note: This only works if the plugin is set to be used per course.
     *
     * This current implementation is not safe, not efficient and is due to be
     * changed once the filters handling has been rewritten.
     *
     * @return void
     */
    public function reset_all_courses_to_defaults() {
        $config = di::get('config');
        if ($config->get('context') != CONTEXT_COURSE) {
            throw new coding_exception('Cannot reset filters for all courses in current mode.');
        }
        $courseworldfactory = di::get('course_world_factory');

        // This is dangerously hardcoded, byt let's use this for now to detect all instances to work on.
        $sql = 'courseid > 0 AND defaultfilters != :defaultfilters';
        $courseids = $this->db->get_fieldset_select('block_xp_config', 'courseid', $sql, [
            'defaultfilters' => course_world_config::DEFAULT_FILTERS_MISSING,
        ]);

        // This is slow, but that's sort of the cleanest way.
        foreach ($courseids as $courseid) {
            $world = $courseworldfactory->get_world($courseid);
            $world->reset_filters_to_defaults();
        }
    }
}
