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
 * Filter manager.
 *
 * @package    block_xp
 * @copyright  2014 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\xp;

use cache;
use moodle_database;

/**
 * Filter manager class.
 *
 * @package    block_xp
 * @copyright  2014 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_filter_manager {

    /** @var int The course ID. */
    protected $courseid;
    /** @var moodle_database The DB. */
    protected $db;
    /** @var cache The cache store. */
    protected $cache;

    /**
     * Constructor.
     *
     * @param moodle_database $db The DB.
     * @param int $courseid The course ID.
     */
    public function __construct(moodle_database $db, $courseid) {
        $this->db = $db;
        $this->courseid = $courseid;
        $this->cache = cache::make('block_xp', 'filters');
    }

    /**
     * Take the static filters and conver them.
     *
     * This should not be used any more. It is only used when converting
     * the static filters to regular filters when dealing with legacy
     * code.
     *
     * @return void
     */
    public function convert_static_filters_to_regular() {
        $filters = $this->get_static_filters();
        $this->import_filters($filters);
    }

    /**
     * Get the cache key.
     *
     * @param int $category The category constant.
     * @return string
     */
    public function get_cache_key($category) {
        return 'filters_' . $this->courseid . '_' . $category;
    }

    /**
     * Get the filters.
     *
     * @param int $category The filter category, see block_xp_filter::CATEGORY_* constants.
     * @return array of filters.
     */
    public function get_filters($category = \block_xp_filter::CATEGORY_EVENTS) {
        $key = $this->get_cache_key($category);
        if (false === ($filters = $this->cache->get($key))) {
            // TODO Caching is unsafe, we should not be serializing the object when
            // we have a mechanism for exporting them...
            $filters = $this->get_user_filters($category);
            $this->cache->set($key, $filters);
        }
        return $filters;
    }

    /**
     * Return the points filtered for this event.
     *
     * @param \core\event\base $event The event.
     * @return int Points, or null.
     */
    public function get_points_for_event(\core\event\base $event) {
        foreach ($this->get_filters() as $filter) {
            if ($filter->match($event)) {
                return $filter->get_points();
            }
        }
        return null;
    }

    /**
     * Get the static filters.
     *
     * This is a legacy method, it contains a list of static filters which were
     * used in version prior to 3.0. Those static filters were not editable and
     * ensured that events were always matched with something. From 3.0, there are
     * no longer static filters, but there are default filters, typically defined
     * by the adminstrator.
     *
     * This method should only be used whenever the static filters are converted
     * to standard filters to maintain backwards compatibility while allowing the
     * users to edit them.
     *
     * @return array Of filter objects.
     */
    public function get_static_filters() {
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
        $data = ['rule' => $ruleset, 'points' => 0, 'editable' => false];
        $list[] = \block_xp_filter::load_from_data($data);

        $data = ['rule' => $c, 'points' => 45, 'editable' => false];
        $list[] = \block_xp_filter::load_from_data($data);

        $data = ['rule' => $r, 'points' => 9, 'editable' => false];
        $list[] = \block_xp_filter::load_from_data($data);

        $data = ['rule' => $u, 'points' => 3, 'editable' => false];
        $list[] = \block_xp_filter::load_from_data($data);

        $data = ['rule' => $d, 'points' => 0, 'editable' => false];
        $list[] = \block_xp_filter::load_from_data($data);

        return $list;
    }

    /**
     * Get the filters defined by the user.
     *
     * @param int $category The filter category, see block_xp_filter::CATEGORY_* constants.
     * @return array Of filter data from the DB, though properties is already json_decoded.
     */
    public function get_user_filters($category = \block_xp_filter::CATEGORY_EVENTS) {
        $results = $this->db->get_recordset('block_xp_filters', ['courseid' => $this->courseid,
            'category' => $category, ], 'sortorder ASC, id ASC');
        $filters = [];
        foreach ($results as $key => $filter) {
            $filters[$filter->id] = \block_xp_filter::load_from_data($filter);
        }
        $results->close();
        return $filters;
    }

    /**
     * Loosely check if any filter contain any rule.
     *
     * @param string[] $ruleclasses The class names.
     * @param int $category The category constant.
     * @return bool
     */
    public function has_filters_using_rules($ruleclasses, $category = \block_xp_filter::CATEGORY_EVENTS) {
        if (empty($ruleclasses)) {
            return false;
        }

        $params = [];
        $segments = [];
        foreach ($ruleclasses as $i => $ruleclass) {
            $key = 'ruleclass' . $i;
            $searchfor = '"_class":"' . $ruleclass . '"';
            $params = array_merge($params, [
                $key => '%' . $this->db->sql_like_escape(str_replace('\\', '\\\\', $searchfor), '@') . '%',
            ]);
            $segments[] = $this->db->sql_like('ruledata', ':' . $key, false, false, false, '@');
        }

        $sql = 'courseid = :courseid AND category = :category AND (' . implode(' OR ', $segments) . ')';
        $params = array_merge($params, [
            'courseid' => $this->courseid,
            'category' => $category,
        ]);

        return $this->db->record_exists_select('block_xp_filters', $sql, $params);
    }

    /**
     * Import filters by appending them.
     *
     * @param array $filters An array of filters.
     * @return void
     */
    protected function import_filters(array $filters) {
        $sortorder = (int) $this->db->get_field('block_xp_filters', 'COALESCE(MAX(sortorder), -1) + 1',
            ['courseid' => $this->courseid]);

        foreach ($filters as $filter) {
            $record = $filter->export();
            $record->courseid = $this->courseid;
            $record->sortorder = $sortorder++;
            $newfilter = \block_xp_filter::load_from_data($record);
            $newfilter->save();
        }

        $this->invalidate_filters_cache();
    }

    /**
     * Import the default filters.
     *
     * @param int|null $category The category.
     * @return void
     */
    public function import_default_filters($category = null) {
        $fm = new admin_filter_manager($this->db);
        $filters = $category !== null ? $fm->get_filters($category) : $fm->get_all_filters();
        $this->import_filters($filters);
        $this->invalidate_filters_cache($category);
    }

    /**
     * Invalidate the filters cache.
     *
     * @param int|null $category The category to invalidate for.
     * @return void
     */
    public function invalidate_filters_cache($category = \block_xp_filter::CATEGORY_EVENTS) {
        $this->cache->delete($this->get_cache_key($category));
    }

    /**
     * Removes all filters.
     *
     * @param int|null $category The category of filters to remove.
     * @return void
     */
    public function purge($category = null) {
        if ($category === null) {
            $this->db->delete_records('block_xp_filters', ['courseid' => $this->courseid]);
            // Ideally we shouldn't be clearing all courses' cache, but that is the simplest way
            // to ensure that all the categories of filters are invalidated within this course.
            $this->cache->purge();
            return;
        }

        $this->db->delete_records('block_xp_filters', ['courseid' => $this->courseid, 'category' => $category]);
        $this->invalidate_filters_cache($category);
    }

}
