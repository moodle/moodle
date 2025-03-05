<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace core;

use stdClass, IteratorAggregate, ArrayIterator;
use coding_exception, moodle_url;

/**
 * Basic moodle context abstraction class.
 *
 * Google confirms that no other important framework is using "context" class,
 * we could use something else like mcontext or moodle_context, but we need to type
 * this very often which would be annoying and it would take too much space...
 *
 * This class is derived from stdClass for backwards compatibility with
 * odl $context record that was returned from DML $DB->get_record()
 *
 * @package   core_access
 * @category  access
 * @copyright Petr Skoda
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 4.2
 *
 * @property-read int $id context id
 * @property-read int $contextlevel CONTEXT_SYSTEM, CONTEXT_COURSE, etc.
 * @property-read int $instanceid id of related instance in each context
 * @property-read string $path path to context, starts with system context
 * @property-read int $depth
 * @property-read bool $locked true means write capabilities are ignored in this context or parents
 */
abstract class context extends stdClass implements IteratorAggregate {

    /** @var string Default sorting of capabilities in {@see get_capabilities} */
    protected const DEFAULT_CAPABILITY_SORT = 'contextlevel, component, name';

    /**
     * The context id
     * Can be accessed publicly through $context->id
     * @var int
     */
    protected $_id;

    /**
     * The context level
     * Can be accessed publicly through $context->contextlevel
     * @var int One of CONTEXT_* e.g. CONTEXT_COURSE, CONTEXT_MODULE
     */
    protected $_contextlevel;

    /**
     * Id of the item this context is related to e.g. COURSE_CONTEXT => course.id
     * Can be accessed publicly through $context->instanceid
     * @var int
     */
    protected $_instanceid;

    /**
     * The path to the context always starting from the system context
     * Can be accessed publicly through $context->path
     * @var string
     */
    protected $_path;

    /**
     * The depth of the context in relation to parent contexts
     * Can be accessed publicly through $context->depth
     * @var int
     */
    protected $_depth;

    /**
     * Whether this context is locked or not.
     *
     * Can be accessed publicly through $context->locked.
     *
     * @var int
     */
    protected $_locked;

    /**
     * @var array Context caching info
     */
    private static $cache_contextsbyid = array();

    /**
     * @var array Context caching info
     */
    private static $cache_contexts = array();

    /**
     * Context count
     * Why do we do count contexts? Because count($array) is horribly slow for large arrays
     * @var int
     */
    protected static $cache_count = 0;

    /**
     * @var array Context caching info
     */
    protected static $cache_preloaded = array();

    /**
     * @var context\system The system context once initialised
     */
    protected static $systemcontext = null;

    /**
     * Returns short context name.
     *
     * @since Moodle 4.2
     *
     * @return string
     */
    public static function get_short_name(): string {
        // NOTE: it would be more correct to make this abstract,
        // unfortunately there are tests that attempt to mock context classes.
        throw new \coding_exception('get_short_name() method must be overridden in custom context levels');
    }

    /**
     * Resets the cache to remove all data.
     */
    protected static function reset_caches() {
        self::$cache_contextsbyid = array();
        self::$cache_contexts = array();
        self::$cache_count = 0;
        self::$cache_preloaded = array();

        self::$systemcontext = null;
    }

    /**
     * Adds a context to the cache. If the cache is full, discards a batch of
     * older entries.
     *
     * @param context $context New context to add
     * @return void
     */
    protected static function cache_add(context $context) {
        if (isset(self::$cache_contextsbyid[$context->id])) {
            // Already cached, no need to do anything - this is relatively cheap, we do all this because count() is slow.
            return;
        }

        if (self::$cache_count >= CONTEXT_CACHE_MAX_SIZE) {
            $i = 0;
            foreach (self::$cache_contextsbyid as $ctx) {
                $i++;
                if ($i <= 100) {
                    // We want to keep the first contexts to be loaded on this page, hopefully they will be needed again later.
                    continue;
                }
                if ($i > (CONTEXT_CACHE_MAX_SIZE / 3)) {
                    // We remove oldest third of the contexts to make room for more contexts.
                    break;
                }
                unset(self::$cache_contextsbyid[$ctx->id]);
                unset(self::$cache_contexts[$ctx->contextlevel][$ctx->instanceid]);
                self::$cache_count--;
            }
        }

        self::$cache_contexts[$context->contextlevel][$context->instanceid] = $context;
        self::$cache_contextsbyid[$context->id] = $context;
        self::$cache_count++;
    }

    /**
     * Removes a context from the cache.
     *
     * @param context $context Context object to remove
     * @return void
     */
    protected static function cache_remove(context $context) {
        if (!isset(self::$cache_contextsbyid[$context->id])) {
            // Not cached, no need to do anything - this is relatively cheap, we do all this because count() is slow.
            return;
        }
        unset(self::$cache_contexts[$context->contextlevel][$context->instanceid]);
        unset(self::$cache_contextsbyid[$context->id]);

        self::$cache_count--;

        if (self::$cache_count < 0) {
            self::$cache_count = 0;
        }
    }

    /**
     * Gets a context from the cache.
     *
     * @param int $contextlevel Context level
     * @param int $instance Instance ID
     * @return context|bool Context or false if not in cache
     */
    protected static function cache_get($contextlevel, $instance) {
        if (isset(self::$cache_contexts[$contextlevel][$instance])) {
            return self::$cache_contexts[$contextlevel][$instance];
        }
        return false;
    }

    /**
     * Gets a context from the cache based on its id.
     *
     * @param int $id Context ID
     * @return context|bool Context or false if not in cache
     */
    protected static function cache_get_by_id($id) {
        if (isset(self::$cache_contextsbyid[$id])) {
            return self::$cache_contextsbyid[$id];
        }
        return false;
    }

    /**
     * Preloads context information from db record and strips the cached info.
     *
     * @param stdClass $rec
     * @return context|null (modifies $rec)
     */
    protected static function preload_from_record(stdClass $rec) {
        $notenoughdata = false;
        $notenoughdata = $notenoughdata || empty($rec->ctxid);
        $notenoughdata = $notenoughdata || empty($rec->ctxlevel);
        $notenoughdata = $notenoughdata || !isset($rec->ctxinstance);
        $notenoughdata = $notenoughdata || empty($rec->ctxpath);
        $notenoughdata = $notenoughdata || empty($rec->ctxdepth);
        $notenoughdata = $notenoughdata || !isset($rec->ctxlocked);
        if ($notenoughdata) {
            // The record does not have enough data, passed here repeatedly or context does not exist yet.
            if (isset($rec->ctxid) && !isset($rec->ctxlocked)) {
                debugging('Locked value missing. Code is possibly not usings the getter properly.', DEBUG_DEVELOPER);
            }
            return null;
        }

        $record = (object) [
            'id' => $rec->ctxid,
            'contextlevel' => $rec->ctxlevel,
            'instanceid' => $rec->ctxinstance,
            'path' => $rec->ctxpath,
            'depth' => $rec->ctxdepth,
            'locked' => $rec->ctxlocked,
        ];

        unset($rec->ctxid);
        unset($rec->ctxlevel);
        unset($rec->ctxinstance);
        unset($rec->ctxpath);
        unset($rec->ctxdepth);
        unset($rec->ctxlocked);

        return self::create_instance_from_record($record);
    }


    /* ====== magic methods ======= */

    /**
     * Magic setter method, we do not want anybody to modify properties from the outside
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value) {
        debugging('Can not change context instance properties!');
    }

    /**
     * Magic method getter, redirects to read only values.
     * @param string $name
     * @return mixed
     */
    public function __get($name) {
        switch ($name) {
            case 'id':
                return $this->_id;
            case 'contextlevel':
                return $this->_contextlevel;
            case 'instanceid':
                return $this->_instanceid;
            case 'path':
                return $this->_path;
            case 'depth':
                return $this->_depth;
            case 'locked':
                return $this->is_locked();

            default:
                debugging('Invalid context property accessed! '.$name);
                return null;
        }
    }

    /**
     * Full support for isset on our magic read only properties.
     * @param string $name
     * @return bool
     */
    public function __isset($name) {
        switch ($name) {
            case 'id':
                return isset($this->_id);
            case 'contextlevel':
                return isset($this->_contextlevel);
            case 'instanceid':
                return isset($this->_instanceid);
            case 'path':
                return isset($this->_path);
            case 'depth':
                return isset($this->_depth);
            case 'locked':
                // Locked is always set.
                return true;
            default:
                return false;
        }
    }

    /**
     * All properties are read only, sorry.
     * @param string $name
     */
    public function __unset($name) {
        debugging('Can not unset context instance properties!');
    }

    /* ====== implementing method from interface IteratorAggregate ====== */

    /**
     * Create an iterator because magic vars can't be seen by 'foreach'.
     *
     * Now we can convert context object to array using convert_to_array(),
     * and feed it properly to json_encode().
     */
    public function getIterator(): \Traversable {
        $ret = array(
            'id' => $this->id,
            'contextlevel' => $this->contextlevel,
            'instanceid' => $this->instanceid,
            'path' => $this->path,
            'depth' => $this->depth,
            'locked' => $this->locked,
        );
        return new ArrayIterator($ret);
    }

    /* ====== general context methods ====== */

    /**
     * Constructor is protected so that devs are forced to
     * use context_xxx::instance() or context::instance_by_id().
     *
     * @param stdClass $record
     */
    protected function __construct(stdClass $record) {
        $this->_id = (int)$record->id;
        $this->_contextlevel = (int)$record->contextlevel;
        $this->_instanceid = $record->instanceid;
        $this->_path = $record->path;
        $this->_depth = $record->depth;

        if (isset($record->locked)) {
            $this->_locked = $record->locked;
        } else if (!during_initial_install() && !moodle_needs_upgrading()) {
            debugging('Locked value missing. Code is possibly not usings the getter properly.', DEBUG_DEVELOPER);
        }
    }

    /**
     * This function is also used to work around 'protected' keyword problems in context_helper.
     *
     * @param stdClass $record
     * @return context instance
     */
    protected static function create_instance_from_record(stdClass $record) {
        $classname = context_helper::get_class_for_level($record->contextlevel);

        if ($context = self::cache_get_by_id($record->id)) {
            return $context;
        }

        $context = new $classname($record);
        self::cache_add($context);

        return $context;
    }

    /**
     * Copy prepared new contexts from temp table to context table,
     * we do this in db specific way for perf reasons only.
     */
    protected static function merge_context_temp_table() {
        global $DB;

        /* MDL-11347:
         *  - mysql does not allow to use FROM in UPDATE statements
         *  - using two tables after UPDATE works in mysql, but might give unexpected
         *    results in pg 8 (depends on configuration)
         *  - using table alias in UPDATE does not work in pg < 8.2
         *
         * Different code for each database - mostly for performance reasons
         */

        $dbfamily = $DB->get_dbfamily();
        if ($dbfamily == 'mysql') {
            $updatesql = "UPDATE {context} ct, {context_temp} temp
                             SET ct.path = temp.path,
                                 ct.depth = temp.depth,
                                 ct.locked = temp.locked
                           WHERE ct.id = temp.id";
        } else if ($dbfamily == 'postgres' || $dbfamily == 'mssql') {
            $updatesql = "UPDATE {context}
                             SET path = temp.path,
                                 depth = temp.depth,
                                 locked = temp.locked
                            FROM {context_temp} temp
                           WHERE temp.id={context}.id";
        } else {
            throw new \core\exception\coding_exception("Unsupported database family: {$dbfamily}");
        }

        $DB->execute($updatesql);
    }

    /**
     * Get a context instance as an object, from a given context id.
     *
     * @param int $id context id
     * @param int $strictness IGNORE_MISSING means compatible mode, false returned if record not found, debug message if more found;
     *                        MUST_EXIST means throw exception if no record found
     * @return context|bool the context object or false if not found
     */
    public static function instance_by_id($id, $strictness = MUST_EXIST) {
        global $DB;

        if (get_called_class() !== 'core\context' && get_called_class() !== 'core\context_helper') {
            // Some devs might confuse context->id and instanceid, better prevent these mistakes completely.
            throw new coding_exception('use only context::instance_by_id() for real context levels use ::instance() methods');
        }

        if ($id == SYSCONTEXTID) {
            return context\system::instance(0, $strictness);
        }

        if (is_array($id) || is_object($id) || empty($id)) {
            throw new coding_exception('Invalid context id specified context::instance_by_id()');
        }

        if ($context = self::cache_get_by_id($id)) {
            return $context;
        }

        if ($record = $DB->get_record('context', array('id' => $id), '*', $strictness)) {
            return self::create_instance_from_record($record);
        }

        return false;
    }

    /**
     * Update context info after moving context in the tree structure.
     *
     * @param context $newparent
     * @return void
     */
    public function update_moved(context $newparent) {
        global $DB;

        $frompath = $this->_path;
        $newpath = $newparent->path . '/' . $this->_id;

        $trans = $DB->start_delegated_transaction();

        $setdepth = '';
        if (($newparent->depth + 1) != $this->_depth) {
            $diff = $newparent->depth - $this->_depth + 1;
            $setdepth = ", depth = depth + $diff";
        }
        $sql = "UPDATE {context}
                   SET path = ?
                       $setdepth
                 WHERE id = ?";
        $params = array($newpath, $this->_id);
        $DB->execute($sql, $params);

        $this->_path = $newpath;
        $this->_depth = $newparent->depth + 1;

        $sql = "UPDATE {context}
                   SET path = ".$DB->sql_concat("?", $DB->sql_substr("path", strlen($frompath) + 1))."
                       $setdepth
                 WHERE path LIKE ?";
        $params = array($newpath, "{$frompath}/%");
        $DB->execute($sql, $params);

        $this->mark_dirty();

        self::reset_caches();

        $trans->allow_commit();
    }

    /**
     * Set whether this context has been locked or not.
     *
     * @param   bool    $locked
     * @return  $this
     */
    public function set_locked(bool $locked) {
        global $DB;

        if ($this->_locked == $locked) {
            return $this;
        }

        $this->_locked = $locked;
        $DB->set_field('context', 'locked', (int) $locked, ['id' => $this->id]);
        $this->mark_dirty();

        if ($locked) {
            $eventname = '\\core\\event\\context_locked';
        } else {
            $eventname = '\\core\\event\\context_unlocked';
        }
        $event = $eventname::create(['context' => $this, 'objectid' => $this->id]);
        $event->trigger();

        self::reset_caches();

        return $this;
    }

    /**
     * Remove all context path info and optionally rebuild it.
     *
     * @param bool $rebuild
     * @return void
     */
    public function reset_paths($rebuild = true) {
        global $DB;

        if ($this->_path) {
            $this->mark_dirty();
        }
        $DB->set_field_select('context', 'depth', 0, "path LIKE '%/$this->_id/%'");
        $DB->set_field_select('context', 'path', null, "path LIKE '%/$this->_id/%'");
        if ($this->_contextlevel != CONTEXT_SYSTEM) {
            $DB->set_field('context', 'depth', 0, array('id' => $this->_id));
            $DB->set_field('context', 'path', null, array('id' => $this->_id));
            $this->_depth = 0;
            $this->_path = null;
        }

        if ($rebuild) {
            context_helper::build_all_paths(false);
        }

        self::reset_caches();
    }

    /**
     * Delete all data linked to content, do not delete the context record itself
     */
    public function delete_content() {
        global $CFG, $DB;

        blocks_delete_all_for_context($this->_id);
        filter_delete_all_for_context($this->_id);

        require_once($CFG->dirroot . '/comment/lib.php');
        \comment::delete_comments(array('contextid' => $this->_id));

        require_once($CFG->dirroot.'/rating/lib.php');
        $delopt = new stdclass();
        $delopt->contextid = $this->_id;
        $rm = new \rating_manager();
        $rm->delete_ratings($delopt);

        // Delete all files attached to this context.
        $fs = get_file_storage();
        $fs->delete_area_files($this->_id);

        // Delete all repository instances attached to this context.
        require_once($CFG->dirroot . '/repository/lib.php');
        \repository::delete_all_for_context($this->_id);

        // Delete all advanced grading data attached to this context.
        require_once($CFG->dirroot.'/grade/grading/lib.php');
        \grading_manager::delete_all_for_context($this->_id);

        // Now delete stuff from role related tables, role_unassign_all
        // and unenrol should be called earlier to do proper cleanup.
        $DB->delete_records('role_assignments', array('contextid' => $this->_id));
        $DB->delete_records('role_names', array('contextid' => $this->_id));
        $this->delete_capabilities();
    }

    /**
     * Unassign all capabilities from a context.
     */
    public function delete_capabilities() {
        global $DB;

        $ids = $DB->get_fieldset_select('role_capabilities', 'DISTINCT roleid', 'contextid = ?', array($this->_id));
        if ($ids) {
            $DB->delete_records('role_capabilities', array('contextid' => $this->_id));

            // Reset any cache of these roles, including MUC.
            accesslib_clear_role_cache($ids);
        }
    }

    /**
     * Delete the context content and the context record itself
     */
    public function delete() {
        global $DB;

        if ($this->_contextlevel <= CONTEXT_SYSTEM) {
            throw new coding_exception('Cannot delete system context');
        }

        // Double check the context still exists.
        if (!$DB->record_exists('context', array('id' => $this->_id))) {
            self::cache_remove($this);
            return;
        }

        $this->delete_content();
        $DB->delete_records('context', array('id' => $this->_id));
        // Purge static context cache if entry present.
        self::cache_remove($this);

        // Inform search engine to delete data related to this context.
        \core_search\manager::context_deleted($this);
    }

    /* ====== context level related methods ====== */

    /**
     * Utility method for context creation
     *
     * @param int $contextlevel
     * @param int $instanceid
     * @param string $parentpath
     * @return stdClass context record
     */
    protected static function insert_context_record($contextlevel, $instanceid, $parentpath) {
        global $DB;

        $record = new stdClass();
        $record->contextlevel = $contextlevel;
        $record->instanceid = $instanceid;
        $record->depth = 0;
        $record->path = null; // Not known before insert.
        $record->locked = 0;

        $record->id = $DB->insert_record('context', $record);

        // Now add path if known - it can be added later.
        if (!is_null($parentpath)) {
            $record->path = $parentpath.'/'.$record->id;
            $record->depth = substr_count($record->path, '/');
            $DB->update_record('context', $record);
        }

        return $record;
    }

    /**
     * Returns human readable context identifier.
     *
     * @param boolean $withprefix whether to prefix the name of the context with the
     *      type of context, e.g. User, Course, Forum, etc.
     * @param boolean $short whether to use the short name of the thing. Only applies
     *      to course contexts
     * @param boolean $escape Whether the returned name of the thing is to be
     *      HTML escaped or not.
     * @return string the human readable context name.
     */
    public function get_context_name($withprefix = true, $short = false, $escape = true) {
        // Must be implemented in all context levels.
        throw new coding_exception('can not get name of abstract context');
    }

    /**
     * Whether the current context is locked.
     *
     * @return  bool
     */
    public function is_locked() {
        if ($this->_locked) {
            return true;
        }

        if ($parent = $this->get_parent_context()) {
            return $parent->is_locked();
        }

        return false;
    }

    /**
     * Returns the most relevant URL for this context.
     *
     * @return moodle_url
     */
    abstract public function get_url();

    /**
     * Returns context instance database name.
     *
     * @return string|null table name for all levels except system.
     */
    protected static function get_instance_table(): ?string {
        return null;
    }

    /**
     * Returns list of columns that can be used from behat
     * to look up context by reference.
     *
     * @return array list of column names from instance table
     */
    protected static function get_behat_reference_columns(): array {
        return [];
    }

    /**
     * Returns list of all role archetypes that are compatible
     * with role assignments in context level.
     * @since Moodle 4.2
     *
     * @return string[]
     */
    protected static function get_compatible_role_archetypes(): array {
        // Override if archetype roles should be allowed to be assigned in context level.
        return [];
    }

    /**
     * Returns list of all possible parent context levels,
     * it may include itself if nesting is allowed.
     * @since Moodle 4.2
     *
     * @return int[]
     */
    public static function get_possible_parent_levels(): array {
        // Override if other type of parents are expected.
        return [context\system::LEVEL];
    }

    /**
     * Returns array of relevant context capability records.
     *
     * @param string $sort SQL order by snippet for sorting returned capabilities sensibly for display
     * @return array
     */
    abstract public function get_capabilities(string $sort = self::DEFAULT_CAPABILITY_SORT);

    /**
     * Recursive function which, given a context, find all its children context ids.
     *
     * For course category contexts it will return immediate children and all subcategory contexts.
     * It will NOT recurse into courses or subcategories categories.
     * If you want to do that, call it on the returned courses/categories.
     *
     * When called for a course context, it will return the modules and blocks
     * displayed in the course page and blocks displayed on the module pages.
     *
     * If called on a user/course/module context it _will_ populate the cache with the appropriate
     * contexts ;-)
     *
     * @return array Array of child records
     */
    public function get_child_contexts() {
        global $DB;

        if (empty($this->_path) || empty($this->_depth)) {
            debugging('Can not find child contexts of context '.$this->_id.' try rebuilding of context paths');
            return array();
        }

        $sql = "SELECT ctx.*
                  FROM {context} ctx
                 WHERE ctx.path LIKE ?";
        $params = array($this->_path.'/%');
        $records = $DB->get_records_sql($sql, $params);

        $result = array();
        foreach ($records as $record) {
            $result[$record->id] = self::create_instance_from_record($record);
        }

        return $result;
    }

    /**
     * Determine if the current context is a parent of the possible child.
     *
     * @param   context $possiblechild
     * @param   bool $includeself Whether to check the current context
     * @return  bool
     */
    public function is_parent_of(context $possiblechild, bool $includeself): bool {
        // A simple substring check is used on the context path.
        // The possible child's path is used as a haystack, with the current context as the needle.
        // The path is prefixed with '+' to ensure that the parent always starts at the top.
        // It is suffixed with '+' to ensure that parents are not included.
        // The needle always suffixes with a '/' to ensure that the contextid uses a complete match (i.e. 142/ instead of 14).
        // The haystack is suffixed with '/+' if $includeself is true to allow the current context to match.
        // The haystack is suffixed with '+' if $includeself is false to prevent the current context from matching.
        $haystacksuffix = $includeself ? '/+' : '+';

        $strpos = strpos(
            "+{$possiblechild->path}{$haystacksuffix}",
            "+{$this->path}/"
        );
        return $strpos === 0;
    }

    /**
     * Returns parent contexts of this context in reversed order, i.e. parent first,
     * then grand parent, etc.
     *
     * @param bool $includeself true means include self too
     * @return array of context instances
     */
    public function get_parent_contexts($includeself = false) {
        if (!$contextids = $this->get_parent_context_ids($includeself)) {
            return array();
        }

        // Preload the contexts to reduce DB calls.
        context_helper::preload_contexts_by_id($contextids);

        $result = array();
        foreach ($contextids as $contextid) {
            // Do NOT change this to self!
            $parent = context_helper::instance_by_id($contextid, MUST_EXIST);
            $result[$parent->id] = $parent;
        }

        return $result;
    }

    /**
     * Determine if the current context is a child of the possible parent.
     *
     * @param   context $possibleparent
     * @param   bool $includeself Whether to check the current context
     * @return  bool
     */
    public function is_child_of(context $possibleparent, bool $includeself): bool {
        // A simple substring check is used on the context path.
        // The current context is used as a haystack, with the possible parent as the needle.
        // The path is prefixed with '+' to ensure that the parent always starts at the top.
        // It is suffixed with '+' to ensure that children are not included.
        // The needle always suffixes with a '/' to ensure that the contextid uses a complete match (i.e. 142/ instead of 14).
        // The haystack is suffixed with '/+' if $includeself is true to allow the current context to match.
        // The haystack is suffixed with '+' if $includeself is false to prevent the current context from matching.
        $haystacksuffix = $includeself ? '/+' : '+';

        $strpos = strpos(
            "+{$this->path}{$haystacksuffix}",
            "+{$possibleparent->path}/"
        );
        return $strpos === 0;
    }

    /**
     * Returns parent context ids of this context in reversed order, i.e. parent first,
     * then grand parent, etc.
     *
     * @param bool $includeself true means include self too
     * @return array of context ids
     */
    public function get_parent_context_ids($includeself = false) {
        if (empty($this->_path)) {
            return array();
        }

        $parentcontexts = trim($this->_path, '/'); // Kill leading slash.
        $parentcontexts = explode('/', $parentcontexts);
        if (!$includeself) {
            array_pop($parentcontexts); // And remove its own id.
        }

        return array_reverse($parentcontexts);
    }

    /**
     * Returns parent context paths of this context.
     *
     * @param bool $includeself true means include self too
     * @return array of context paths
     */
    public function get_parent_context_paths($includeself = false) {
        if (empty($this->_path)) {
            return array();
        }

        $contextids = explode('/', $this->_path);

        $path = '';
        $paths = array();
        foreach ($contextids as $contextid) {
            if ($contextid) {
                $path .= '/' . $contextid;
                $paths[$contextid] = $path;
            }
        }

        if (!$includeself) {
            unset($paths[$this->_id]);
        }

        return $paths;
    }

    /**
     * Returns parent context
     *
     * @return context|false
     */
    public function get_parent_context() {
        if (empty($this->_path) || $this->_id == SYSCONTEXTID) {
            return false;
        }

        $parentcontexts = trim($this->_path, '/'); // Kill leading slash.
        $parentcontexts = explode('/', $parentcontexts);
        array_pop($parentcontexts); // Self.
        $contextid = array_pop($parentcontexts); // Immediate parent.

        // Do NOT change this to self!
        return context_helper::instance_by_id($contextid, MUST_EXIST);
    }

    /**
     * Is this context part of any course? If yes return course context.
     *
     * @param bool $strict true means throw exception if not found, false means return false if not found
     * @return context\course|false context of the enclosing course, null if not found or exception
     */
    public function get_course_context($strict = true) {
        if ($strict) {
            throw new coding_exception('Context does not belong to any course.');
        } else {
            return false;
        }
    }

    /**
     * Returns sql necessary for purging of stale context instances.
     *
     * @return string cleanup SQL
     */
    protected static function get_cleanup_sql() {
        throw new coding_exception('get_cleanup_sql() method must be implemented in all context levels');
    }

    /**
     * Rebuild context paths and depths at context level.
     *
     * @param bool $force
     * @return void
     */
    protected static function build_paths($force) {
        throw new coding_exception('build_paths() method must be implemented in all context levels');
    }

    /**
     * Create missing context instances at given level
     *
     * @return void
     */
    protected static function create_level_instances() {
        throw new coding_exception('create_level_instances() method must be implemented in all context levels');
    }

    /**
     * Reset all cached permissions and definitions if the necessary.
     * @return void
     */
    public function reload_if_dirty() {
        global $ACCESSLIB_PRIVATE, $USER;

        // Load dirty contexts list if needed.
        if (CLI_SCRIPT) {
            if (!isset($ACCESSLIB_PRIVATE->dirtycontexts)) {
                // We do not load dirty flags in CLI and cron.
                $ACCESSLIB_PRIVATE->dirtycontexts = array();
            }
        } else {
            if (!isset($USER->access['time'])) {
                // Nothing has been loaded yet, so we do not need to check dirty flags now.
                return;
            }

            // From skodak: No idea why -2 is there, server cluster time difference maybe...
            $changedsince = $USER->access['time'] - 2;

            if (!isset($ACCESSLIB_PRIVATE->dirtycontexts)) {
                $ACCESSLIB_PRIVATE->dirtycontexts = get_cache_flags('accesslib/dirtycontexts', $changedsince);
            }

            if (!isset($ACCESSLIB_PRIVATE->dirtyusers[$USER->id])) {
                $ACCESSLIB_PRIVATE->dirtyusers[$USER->id] = get_cache_flag('accesslib/dirtyusers', $USER->id, $changedsince);
            }
        }

        $dirty = false;

        if (!empty($ACCESSLIB_PRIVATE->dirtyusers[$USER->id])) {
            $dirty = true;
        } else if (!empty($ACCESSLIB_PRIVATE->dirtycontexts)) {
            $paths = $this->get_parent_context_paths(true);

            foreach ($paths as $path) {
                if (isset($ACCESSLIB_PRIVATE->dirtycontexts[$path])) {
                    $dirty = true;
                    break;
                }
            }
        }

        if ($dirty) {
            // Reload all capabilities of USER and others - preserving loginas, roleswitches, etc.
            // Then cleanup any marks of dirtyness... at least from our short term memory!
            reload_all_capabilities();
        }
    }

    /**
     * Mark a context as dirty (with timestamp) so as to force reloading of the context.
     */
    public function mark_dirty() {
        global $CFG, $USER, $ACCESSLIB_PRIVATE;

        if (during_initial_install()) {
            return;
        }

        // Only if it is a non-empty string.
        if (is_string($this->_path) && $this->_path !== '') {
            set_cache_flag('accesslib/dirtycontexts', $this->_path, 1, time() + $CFG->sessiontimeout);
            if (isset($ACCESSLIB_PRIVATE->dirtycontexts)) {
                $ACCESSLIB_PRIVATE->dirtycontexts[$this->_path] = 1;
            } else {
                if (CLI_SCRIPT) {
                    $ACCESSLIB_PRIVATE->dirtycontexts = array($this->_path => 1);
                } else {
                    if (isset($USER->access['time'])) {
                        $ACCESSLIB_PRIVATE->dirtycontexts = get_cache_flags('accesslib/dirtycontexts', $USER->access['time'] - 2);
                    } else {
                        $ACCESSLIB_PRIVATE->dirtycontexts = array($this->_path => 1);
                    }
                    // Flags not loaded yet, it will be done later in $context->reload_if_dirty().
                }
            }
        }
    }
}
