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
 * Class core_tag_index_builder
 *
 * @package   core_tag
 * @copyright 2016 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Helper to build tag index
 *
 * This can be used by components to implement tag area callbacks. This is especially
 * useful for in-course content when we need to check and cache user's access to
 * multiple courses. Course access and accessible items are stored in session cache
 * with 15 minutes expiry time.
 *
 * Example of usage:
 *
 * $builder = new core_tag_index_builder($component, $itemtype, $sql, $params, $from, $limit);
 * while ($item = $builder->has_item_that_needs_access_check()) {
 *     if (!$builder->can_access_course($item->courseid)) {
 *         $builder->set_accessible($item, false);
 *     } else {
 *         $accessible = true; // Check access and set $accessible respectively.
 *         $builder->set_accessible($item, $accessible);
 *     }
 * }
 * $items = $builder->get_items();
 *
 * @package   core_tag
 * @copyright 2016 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_tag_index_builder {

    /** @var string component specified in the constructor */
    protected $component;

    /** @var string itemtype specified in the constructor */
    protected $itemtype;

    /** @var string SQL statement */
    protected $sql;

    /** @var array parameters for SQL statement */
    protected $params;

    /** @var int index from which to return records */
    protected $from;

    /** @var int maximum number of records to return */
    protected $limit;

    /** @var array result of SQL query */
    protected $items;

    /** @var array list of item ids ( array_keys($this->items) ) */
    protected $itemkeys;

    /** @var string alias of the item id in the SQL result */
    protected $idfield = 'id';

    /** @var array cache of items accessibility (id => bool) */
    protected $accessibleitems;

    /** @var array cache of courses accessibility (courseid => bool) */
    protected $courseaccess;

    /** @var bool indicates that items cache was changed in this class and needs pushing to MUC */
    protected $cachechangedaccessible = false;

    /** @var bool indicates that course accessibiity cache was changed in this class and needs pushing to MUC */
    protected $cachechangedcourse = false;

    /** @var array cached courses (not pushed to MUC) */
    protected $courses;

    /**
     * Constructor.
     *
     * Specify the SQL query for retrieving the tagged items, SQL query must:
     * - return the item id as the first field and make sure that it is unique in the result
     * - provide ORDER BY that exclude any possibility of random results, if $fromctx was specified when searching
     *   for tagged items it is the best practice to make sure that items from this context are returned first.
     *
     * This query may also contain placeholders %COURSEFILTER% or %ITEMFILTER% that will be substituted with
     * expressions excluding courses and/or filters that are already known as inaccessible.
     *
     * Example: "WHERE c.id %COURSEFILTER% AND cm.id %ITEMFILTER%"
     *
     * This query may contain fields to preload context if context is needed for formatting values.
     *
     * It is recommended to sort by course sortorder first, this way the items from the same course will be next to
     * each other and the sequence of courses will the same in different tag areas.
     *
     * @param string $component component responsible for tagging
     * @param string $itemtype type of item that is being tagged
     * @param string $sql SQL query that would retrieve all relevant items without permission check
     * @param array $params parameters for the query (must be named)
     * @param int $from return a subset of records, starting at this point
     * @param int $limit return a subset comprising this many records in total (this field is NOT optional)
     */
    public function __construct($component, $itemtype, $sql, $params, $from, $limit) {
        $this->component = preg_replace('/[^A-Za-z0-9_]/i', '', $component);
        $this->itemtype = preg_replace('/[^A-Za-z0-9_]/i', '', $itemtype);
        $this->sql = $sql;
        $this->params = $params;
        $this->from = $from;
        $this->limit = $limit;
        $this->courses = array();
    }

    /**
     * Substitute %COURSEFILTER% with an expression filtering out courses where current user does not have access
     */
    protected function prepare_sql_courses() {
        global $DB;
        if (!preg_match('/\\%COURSEFILTER\\%/', $this->sql)) {
            return;
        }
        $this->init_course_access();
        $unaccessiblecourses = array_filter($this->courseaccess, function($item) {
            return !$item;
        });
        $idx = 0;
        while (preg_match('/^([^\\0]*?)\\%COURSEFILTER\\%([^\\0]*)$/', $this->sql, $matches)) {
            list($sql, $params) = $DB->get_in_or_equal(array_keys($unaccessiblecourses),
                    SQL_PARAMS_NAMED, 'ca_'.($idx++).'_', false, 0);
            $this->sql = $matches[1].' '.$sql.' '.$matches[2];
            $this->params += $params;
        }
    }

    /**
     * Substitute %ITEMFILTER% with an expression filtering out items where current user does not have access
     */
    protected function prepare_sql_items() {
        global $DB;
        if (!preg_match('/\\%ITEMFILTER\\%/', $this->sql)) {
            return;
        }
        $this->init_items_access();
        $unaccessibleitems = array_filter($this->accessibleitems, function($item) {
            return !$item;
        });
        $idx = 0;
        while (preg_match('/^([^\\0]*?)\\%ITEMFILTER\\%([^\\0]*)$/', $this->sql, $matches)) {
            list($sql, $params) = $DB->get_in_or_equal(array_keys($unaccessibleitems),
                    SQL_PARAMS_NAMED, 'ia_'.($idx++).'_', false, 0);
            $this->sql = $matches[1].' '.$sql.' '.$matches[2];
            $this->params += $params;
        }
    }

    /**
     * Ensures that SQL query was executed and $this->items is filled
     */
    protected function retrieve_items() {
        global $DB;
        if ($this->items !== null) {
            return;
        }
        $this->prepare_sql_courses();
        $this->prepare_sql_items();
        $this->items = $DB->get_records_sql($this->sql, $this->params);
        $this->itemkeys = array_keys($this->items);
        if ($this->items) {
            // Find the name of the first key of the item - usually 'id' but can be something different.
            // This must be a unique identifier of the item.
            $firstitem = reset($this->items);
            $firstitemarray = (array)$firstitem;
            $this->idfield = key($firstitemarray);
        }
    }

    /**
     * Returns the filtered records from SQL query result.
     *
     * This function can only be executed after $builder->has_item_that_needs_access_check() returns null
     *
     *
     * @return array
     */
    public function get_items() {
        global $DB, $CFG;
        if (is_siteadmin()) {
            $this->sql = preg_replace('/\\%COURSEFILTER\\%/', '<>0', $this->sql);
            $this->sql = preg_replace('/\\%ITEMFILTER\\%/', '<>0', $this->sql);
            return $DB->get_records_sql($this->sql, $this->params, $this->from, $this->limit);
        }
        if ($CFG->debugdeveloper && $this->has_item_that_needs_access_check()) {
            debugging('Caller must ensure that has_item_that_needs_access_check() does not return anything '
                    . 'before calling get_items(). The item list may be incomplete', DEBUG_DEVELOPER);
        }
        $this->retrieve_items();
        $this->save_caches();
        $idx = 0;
        $items = array();
        foreach ($this->itemkeys as $id) {
            if (!array_key_exists($id, $this->accessibleitems) || !$this->accessibleitems[$id]) {
                continue;
            }
            if ($idx >= $this->from) {
                $items[$id] = $this->items[$id];
            }
            $idx++;
            if ($idx >= $this->from + $this->limit) {
                break;
            }
        }
        return $items;
    }

    /**
     * Returns the first row from the SQL result that we don't know whether it is accessible by user or not.
     *
     * This will return null when we have necessary number of accessible items to return in {@link get_items()}
     *
     * After analyzing you may decide to mark not only this record but all similar as accessible or not accessible.
     * For example, if you already call get_fast_modinfo() to check this item's accessibility, why not mark all
     * items in the same course as accessible or not accessible.
     *
     * Helpful methods: {@link set_accessible()} and {@link walk()}
     *
     * @return null|object
     */
    public function has_item_that_needs_access_check() {
        if (is_siteadmin()) {
            return null;
        }
        $this->retrieve_items();
        $counter = 0; // Counter for accessible items.
        foreach ($this->itemkeys as $id) {
            if (!array_key_exists($id, $this->accessibleitems)) {
                return (object)(array)$this->items[$id];
            }
            $counter += $this->accessibleitems[$id] ? 1 : 0;
            if ($counter >= $this->from + $this->limit) {
                // We found enough accessible items fot get_items() method, do not look any further.
                return null;
            }
        }
        return null;
    }

    /**
     * Walk through the array of items and call $callable for each of them
     * @param callable $callable
     */
    public function walk($callable) {
        $this->retrieve_items();
        array_walk($this->items, $callable);
    }

    /**
     * Marks record or group of records as accessible (or not accessible)
     *
     * @param int|std_Class $identifier either record id of the item that needs to be set accessible
     * @param bool $accessible whether to mark as accessible or not accessible (default true)
     */
    public function set_accessible($identifier, $accessible = true) {
        if (is_object($identifier)) {
            $identifier = (int)($identifier->{$this->idfield});
        }
        $this->init_items_access();
        if (is_int($identifier)) {
            $accessible = (int)(bool)$accessible;
            if (!array_key_exists($identifier, $this->accessibleitems) ||
                    $this->accessibleitems[$identifier] != $accessible) {
                $this->accessibleitems[$identifier] = $accessible;
                $this->cachechangedaccessible;
            }
        } else {
            throw new coding_exception('Argument $identifier must be either int or object');
        }
    }

    /**
     * Retrieves a course record (only fields id,visible,fullname,shortname,cacherev).
     *
     * This method is useful because it also caches results and preloads course context.
     *
     * @param int $courseid
     */
    public function get_course($courseid) {
        global $DB;
        if (!array_key_exists($courseid, $this->courses)) {
            $ctxquery = context_helper::get_preload_record_columns_sql('ctx');
            $sql = "SELECT c.id,c.visible,c.fullname,c.shortname,c.cacherev, $ctxquery
            FROM {course} c JOIN {context} ctx ON ctx.contextlevel = ? AND ctx.instanceid=c.id
            WHERE c.id = ?";
            $params = array(CONTEXT_COURSE, $courseid);
            $this->courses[$courseid] = $DB->get_record_sql($sql, $params);
            context_helper::preload_from_record($this->courses[$courseid]);
        }
        return $this->courses[$courseid];
    }

    /**
     * Ensures that we read the course access from the cache.
     */
    protected function init_course_access() {
        if ($this->courseaccess === null) {
            $this->courseaccess = cache::make('core', 'tagindexbuilder')->get('courseaccess') ?: [];
        }
    }

    /**
     * Ensures that we read the items access from the cache.
     */
    protected function init_items_access() {
        if ($this->accessibleitems === null) {
            $this->accessibleitems = cache::make('core', 'tagindexbuilder')->get($this->component.'__'.$this->itemtype) ?: [];
        }
    }

    /**
     * Checks if current user has access to the course
     *
     * This method calls global function {@link can_access_course} and caches results
     *
     * @param int $courseid
     * @return bool
     */
    public function can_access_course($courseid) {
        $this->init_course_access();
        if (!array_key_exists($courseid, $this->courseaccess)) {
            $this->courseaccess[$courseid] = can_access_course($this->get_course($courseid)) ? 1 : 0;
            $this->cachechangedcourse = true;
        }
        return $this->courseaccess[$courseid];
    }

    /**
     * Saves course/items caches if needed
     */
    protected function save_caches() {
        if ($this->cachechangedcourse) {
            cache::make('core', 'tagindexbuilder')->set('courseaccess', $this->courseaccess);
            $this->cachechangedcourse = false;
        }
        if ($this->cachechangedaccessible) {
            cache::make('core', 'tagindexbuilder')->set($this->component.'__'.$this->itemtype,
                    $this->accessibleitems);
            $this->cachechangedaccessible = false;
        }
    }

    /**
     * Resets all course/items session caches - useful in unittests when we change users and enrolments.
     */
    public static function reset_caches() {
        cache_helper::purge_by_event('resettagindexbuilder');
    }
}
