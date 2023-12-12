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
 * Contains class core_course_category responsible for course category operations
 *
 * @package    core
 * @subpackage course
 * @copyright  2013 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Class to store, cache, render and manage course category
 *
 * @property-read int $id
 * @property-read string $name
 * @property-read string $idnumber
 * @property-read string $description
 * @property-read int $descriptionformat
 * @property-read int $parent
 * @property-read int $sortorder
 * @property-read int $coursecount
 * @property-read int $visible
 * @property-read int $visibleold
 * @property-read int $timemodified
 * @property-read int $depth
 * @property-read string $path
 * @property-read string $theme
 *
 * @package    core
 * @subpackage course
 * @copyright  2013 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_course_category implements renderable, cacheable_object, IteratorAggregate {
    /** @var core_course_category stores pseudo category with id=0. Use core_course_category::get(0) to retrieve */
    protected static $coursecat0;

    /** @var array list of all fields and their short name and default value for caching */
    protected static $coursecatfields = array(
        'id' => array('id', 0),
        'name' => array('na', ''),
        'idnumber' => array('in', null),
        'description' => null, // Not cached.
        'descriptionformat' => null, // Not cached.
        'parent' => array('pa', 0),
        'sortorder' => array('so', 0),
        'coursecount' => array('cc', 0),
        'visible' => array('vi', 1),
        'visibleold' => null, // Not cached.
        'timemodified' => null, // Not cached.
        'depth' => array('dh', 1),
        'path' => array('ph', null),
        'theme' => null, // Not cached.
    );

    /** @var int */
    protected $id;

    /** @var string */
    protected $name = '';

    /** @var string */
    protected $idnumber = null;

    /** @var string */
    protected $description = false;

    /** @var int */
    protected $descriptionformat = false;

    /** @var int */
    protected $parent = 0;

    /** @var int */
    protected $sortorder = 0;

    /** @var int */
    protected $coursecount = false;

    /** @var int */
    protected $visible = 1;

    /** @var int */
    protected $visibleold = false;

    /** @var int */
    protected $timemodified = false;

    /** @var int */
    protected $depth = 0;

    /** @var string */
    protected $path = '';

    /** @var string */
    protected $theme = false;

    /** @var bool */
    protected $fromcache = false;

    /**
     * Magic setter method, we do not want anybody to modify properties from the outside
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value) {
        debugging('Can not change core_course_category instance properties!', DEBUG_DEVELOPER);
    }

    /**
     * Magic method getter, redirects to read only values. Queries from DB the fields that were not cached
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name) {
        global $DB;
        if (array_key_exists($name, self::$coursecatfields)) {
            if ($this->$name === false) {
                // Property was not retrieved from DB, retrieve all not retrieved fields.
                $notretrievedfields = array_diff_key(self::$coursecatfields, array_filter(self::$coursecatfields));
                $record = $DB->get_record('course_categories', array('id' => $this->id),
                        join(',', array_keys($notretrievedfields)), MUST_EXIST);
                foreach ($record as $key => $value) {
                    $this->$key = $value;
                }
            }
            return $this->$name;
        }
        debugging('Invalid core_course_category property accessed! '.$name, DEBUG_DEVELOPER);
        return null;
    }

    /**
     * Full support for isset on our magic read only properties.
     *
     * @param string $name
     * @return bool
     */
    public function __isset($name) {
        if (array_key_exists($name, self::$coursecatfields)) {
            return isset($this->$name);
        }
        return false;
    }

    /**
     * All properties are read only, sorry.
     *
     * @param string $name
     */
    public function __unset($name) {
        debugging('Can not unset core_course_category instance properties!', DEBUG_DEVELOPER);
    }

    /**
     * Get list of plugin callback functions.
     *
     * @param string $name Callback function name.
     * @return [callable] $pluginfunctions
     */
    public function get_plugins_callback_function(string $name) : array {
        $pluginfunctions = [];
        if ($pluginsfunction = get_plugins_with_function($name)) {
            foreach ($pluginsfunction as $plugintype => $plugins) {
                foreach ($plugins as $pluginfunction) {
                    $pluginfunctions[] = $pluginfunction;
                }
            }
        }
        return $pluginfunctions;
    }

    /**
     * Create an iterator because magic vars can't be seen by 'foreach'.
     *
     * implementing method from interface IteratorAggregate
     *
     * @return ArrayIterator
     */
    public function getIterator(): Traversable {
        $ret = array();
        foreach (self::$coursecatfields as $property => $unused) {
            if ($this->$property !== false) {
                $ret[$property] = $this->$property;
            }
        }
        return new ArrayIterator($ret);
    }

    /**
     * Constructor
     *
     * Constructor is protected, use core_course_category::get($id) to retrieve category
     *
     * @param stdClass $record record from DB (may not contain all fields)
     * @param bool $fromcache whether it is being restored from cache
     */
    protected function __construct(stdClass $record, $fromcache = false) {
        context_helper::preload_from_record($record);
        foreach ($record as $key => $val) {
            if (array_key_exists($key, self::$coursecatfields)) {
                $this->$key = $val;
            }
        }
        $this->fromcache = $fromcache;
    }

    /**
     * Returns coursecat object for requested category
     *
     * If category is not visible to the given user, it is treated as non existing
     * unless $alwaysreturnhidden is set to true
     *
     * If id is 0, the pseudo object for root category is returned (convenient
     * for calling other functions such as get_children())
     *
     * @param int $id category id
     * @param int $strictness whether to throw an exception (MUST_EXIST) or
     *     return null (IGNORE_MISSING) in case the category is not found or
     *     not visible to current user
     * @param bool $alwaysreturnhidden set to true if you want an object to be
     *     returned even if this category is not visible to the current user
     *     (category is hidden and user does not have
     *     'moodle/category:viewhiddencategories' capability). Use with care!
     * @param int|stdClass $user The user id or object. By default (null) checks the visibility to the current user.
     * @return null|self
     * @throws moodle_exception
     */
    public static function get($id, $strictness = MUST_EXIST, $alwaysreturnhidden = false, $user = null) {
        if (!$id) {
            // Top-level category.
            if ($alwaysreturnhidden || self::top()->is_uservisible()) {
                return self::top();
            }
            if ($strictness == MUST_EXIST) {
                throw new moodle_exception('cannotviewcategory');
            }
            return null;
        }

        // Try to get category from cache or retrieve from the DB.
        $coursecatrecordcache = cache::make('core', 'coursecatrecords');
        $coursecat = $coursecatrecordcache->get($id);
        if ($coursecat === false) {
            if ($records = self::get_records('cc.id = :id', array('id' => $id))) {
                $record = reset($records);
                $coursecat = new self($record);
                // Store in cache.
                $coursecatrecordcache->set($id, $coursecat);
            }
        }

        if (!$coursecat) {
            // Course category not found.
            if ($strictness == MUST_EXIST) {
                throw new moodle_exception('unknowncategory');
            }
            $coursecat = null;
        } else if (!$alwaysreturnhidden && !$coursecat->is_uservisible($user)) {
            // Course category is found but user can not access it.
            if ($strictness == MUST_EXIST) {
                throw new moodle_exception('cannotviewcategory');
            }
            $coursecat = null;
        }
        return $coursecat;
    }

    /**
     * Returns the pseudo-category representing the whole system (id=0, context_system)
     *
     * @return core_course_category
     */
    public static function top() {
        if (!isset(self::$coursecat0)) {
            $record = new stdClass();
            $record->id = 0;
            $record->visible = 1;
            $record->depth = 0;
            $record->path = '';
            $record->locked = 0;
            self::$coursecat0 = new self($record);
        }
        return self::$coursecat0;
    }

    /**
     * Returns the top-most category for the current user
     *
     * Examples:
     * 1. User can browse courses everywhere - return self::top() - pseudo-category with id=0
     * 2. User does not have capability to browse courses on the system level but
     *    has it in ONE course category - return this course category
     * 3. User has capability to browse courses in two course categories - return self::top()
     *
     * @return core_course_category|null
     */
    public static function user_top() {
        $children = self::top()->get_children();
        if (count($children) == 1) {
            // User has access to only one category on the top level. Return this category as "user top category".
            return reset($children);
        }
        if (count($children) > 1) {
            // User has access to more than one category on the top level. Return the top as "user top category".
            // In this case user actually may not have capability 'moodle/category:viewcourselist' on the top level.
            return self::top();
        }
        // User can not access any categories on the top level.
        // TODO MDL-10965 find ANY/ALL categories in the tree where user has access to.
        return self::get(0, IGNORE_MISSING);
    }

    /**
     * Load many core_course_category objects.
     *
     * @param array $ids An array of category ID's to load.
     * @return core_course_category[]
     */
    public static function get_many(array $ids) {
        global $DB;
        $coursecatrecordcache = cache::make('core', 'coursecatrecords');
        $categories = $coursecatrecordcache->get_many($ids);
        $toload = array();
        foreach ($categories as $id => $result) {
            if ($result === false) {
                $toload[] = $id;
            }
        }
        if (!empty($toload)) {
            list($where, $params) = $DB->get_in_or_equal($toload, SQL_PARAMS_NAMED);
            $records = self::get_records('cc.id '.$where, $params);
            $toset = array();
            foreach ($records as $record) {
                $categories[$record->id] = new self($record);
                $toset[$record->id] = $categories[$record->id];
            }
            $coursecatrecordcache->set_many($toset);
        }
        return $categories;
    }

    /**
     * Load all core_course_category objects.
     *
     * @param array $options Options:
     *              - returnhidden Return categories even if they are hidden
     * @return  core_course_category[]
     */
    public static function get_all($options = []) {
        global $DB;

        $coursecatrecordcache = cache::make('core', 'coursecatrecords');

        $catcontextsql = \context_helper::get_preload_record_columns_sql('ctx');
        $catsql = "SELECT cc.*, {$catcontextsql}
                     FROM {course_categories} cc
                     JOIN {context} ctx ON cc.id = ctx.instanceid";
        $catsqlwhere = "WHERE ctx.contextlevel = :contextlevel";
        $catsqlorder = "ORDER BY cc.depth ASC, cc.sortorder ASC";

        $catrs = $DB->get_recordset_sql("{$catsql} {$catsqlwhere} {$catsqlorder}", [
            'contextlevel' => CONTEXT_COURSECAT,
        ]);

        $types['categories'] = [];
        $categories = [];
        $toset = [];
        foreach ($catrs as $record) {
            $category = new self($record);
            $toset[$category->id] = $category;

            if (!empty($options['returnhidden']) || $category->is_uservisible()) {
                $categories[$record->id] = $category;
            }
        }
        $catrs->close();

        $coursecatrecordcache->set_many($toset);

        return $categories;

    }

    /**
     * Returns the first found category
     *
     * Note that if there are no categories visible to the current user on the first level,
     * the invisible category may be returned
     *
     * @return core_course_category
     */
    public static function get_default() {
        if ($visiblechildren = self::top()->get_children()) {
            $defcategory = reset($visiblechildren);
        } else {
            $toplevelcategories = self::get_tree(0);
            $defcategoryid = $toplevelcategories[0];
            $defcategory = self::get($defcategoryid, MUST_EXIST, true);
        }
        return $defcategory;
    }

    /**
     * Restores the object after it has been externally modified in DB for example
     * during {@link fix_course_sortorder()}
     */
    protected function restore() {
        if (!$this->id) {
            return;
        }
        // Update all fields in the current object.
        $newrecord = self::get($this->id, MUST_EXIST, true);
        foreach (self::$coursecatfields as $key => $unused) {
            $this->$key = $newrecord->$key;
        }
    }

    /**
     * Creates a new category either from form data or from raw data
     *
     * Please note that this function does not verify access control.
     *
     * Exception is thrown if name is missing or idnumber is duplicating another one in the system.
     *
     * Category visibility is inherited from parent unless $data->visible = 0 is specified
     *
     * @param array|stdClass $data
     * @param array $editoroptions if specified, the data is considered to be
     *    form data and file_postupdate_standard_editor() is being called to
     *    process images in description.
     * @return core_course_category
     * @throws moodle_exception
     */
    public static function create($data, $editoroptions = null) {
        global $DB, $CFG;
        $data = (object)$data;
        $newcategory = new stdClass();

        $newcategory->descriptionformat = FORMAT_MOODLE;
        $newcategory->description = '';
        // Copy all description* fields regardless of whether this is form data or direct field update.
        foreach ($data as $key => $value) {
            if (preg_match("/^description/", $key)) {
                $newcategory->$key = $value;
            }
        }

        if (empty($data->name)) {
            throw new moodle_exception('categorynamerequired');
        }
        if (core_text::strlen($data->name) > 255) {
            throw new moodle_exception('categorytoolong');
        }
        $newcategory->name = $data->name;

        // Validate and set idnumber.
        if (isset($data->idnumber)) {
            if (core_text::strlen($data->idnumber) > 100) {
                throw new moodle_exception('idnumbertoolong');
            }
            if (strval($data->idnumber) !== '' && $DB->record_exists('course_categories', array('idnumber' => $data->idnumber))) {
                throw new moodle_exception('categoryidnumbertaken');
            }
            $newcategory->idnumber = $data->idnumber;
        }

        if (isset($data->theme) && !empty($CFG->allowcategorythemes)) {
            $newcategory->theme = $data->theme;
        }

        if (empty($data->parent)) {
            $parent = self::top();
        } else {
            $parent = self::get($data->parent, MUST_EXIST, true);
        }
        $newcategory->parent = $parent->id;
        $newcategory->depth = $parent->depth + 1;

        // By default category is visible, unless visible = 0 is specified or parent category is hidden.
        if (isset($data->visible) && !$data->visible) {
            // Create a hidden category.
            $newcategory->visible = $newcategory->visibleold = 0;
        } else {
            // Create a category that inherits visibility from parent.
            $newcategory->visible = $parent->visible;
            // In case parent is hidden, when it changes visibility this new subcategory will automatically become visible too.
            $newcategory->visibleold = 1;
        }

        $newcategory->sortorder = 0;
        $newcategory->timemodified = time();

        $newcategory->id = $DB->insert_record('course_categories', $newcategory);

        // Update path (only possible after we know the category id.
        $path = $parent->path . '/' . $newcategory->id;
        $DB->set_field('course_categories', 'path', $path, array('id' => $newcategory->id));

        fix_course_sortorder();

        // If this is data from form results, save embedded files and update description.
        $categorycontext = context_coursecat::instance($newcategory->id);
        if ($editoroptions) {
            $newcategory = file_postupdate_standard_editor($newcategory, 'description', $editoroptions, $categorycontext,
                                                           'coursecat', 'description', 0);

            // Update only fields description and descriptionformat.
            $updatedata = new stdClass();
            $updatedata->id = $newcategory->id;
            $updatedata->description = $newcategory->description;
            $updatedata->descriptionformat = $newcategory->descriptionformat;
            $DB->update_record('course_categories', $updatedata);
        }

        $event = \core\event\course_category_created::create(array(
            'objectid' => $newcategory->id,
            'context' => $categorycontext
        ));
        $event->trigger();

        cache_helper::purge_by_event('changesincoursecat');

        return self::get($newcategory->id, MUST_EXIST, true);
    }

    /**
     * Updates the record with either form data or raw data
     *
     * Please note that this function does not verify access control.
     *
     * This function calls core_course_category::change_parent_raw if field 'parent' is updated.
     * It also calls core_course_category::hide_raw or core_course_category::show_raw if 'visible' is updated.
     * Visibility is changed first and then parent is changed. This means that
     * if parent category is hidden, the current category will become hidden
     * too and it may overwrite whatever was set in field 'visible'.
     *
     * Note that fields 'path' and 'depth' can not be updated manually
     * Also core_course_category::update() can not directly update the field 'sortoder'
     *
     * @param array|stdClass $data
     * @param array $editoroptions if specified, the data is considered to be
     *    form data and file_postupdate_standard_editor() is being called to
     *    process images in description.
     * @throws moodle_exception
     */
    public function update($data, $editoroptions = null) {
        global $DB, $CFG;
        if (!$this->id) {
            // There is no actual DB record associated with root category.
            return;
        }

        $data = (object)$data;
        $newcategory = new stdClass();
        $newcategory->id = $this->id;

        // Copy all description* fields regardless of whether this is form data or direct field update.
        foreach ($data as $key => $value) {
            if (preg_match("/^description/", $key)) {
                $newcategory->$key = $value;
            }
        }

        if (isset($data->name) && empty($data->name)) {
            throw new moodle_exception('categorynamerequired');
        }

        if (!empty($data->name) && $data->name !== $this->name) {
            if (core_text::strlen($data->name) > 255) {
                throw new moodle_exception('categorytoolong');
            }
            $newcategory->name = $data->name;
        }

        if (isset($data->idnumber) && $data->idnumber !== $this->idnumber) {
            if (core_text::strlen($data->idnumber) > 100) {
                throw new moodle_exception('idnumbertoolong');
            }

            // Ensure there are no other categories with the same idnumber.
            if (strval($data->idnumber) !== '' &&
                    $DB->record_exists_select('course_categories', 'idnumber = ? AND id != ?', [$data->idnumber, $this->id])) {

                throw new moodle_exception('categoryidnumbertaken');
            }
            $newcategory->idnumber = $data->idnumber;
        }

        if (isset($data->theme) && !empty($CFG->allowcategorythemes)) {
            $newcategory->theme = $data->theme;
        }

        $changes = false;
        if (isset($data->visible)) {
            if ($data->visible) {
                $changes = $this->show_raw();
            } else {
                $changes = $this->hide_raw(0);
            }
        }

        if (isset($data->parent) && $data->parent != $this->parent) {
            if ($changes) {
                cache_helper::purge_by_event('changesincoursecat');
            }
            $parentcat = self::get($data->parent, MUST_EXIST, true);
            $this->change_parent_raw($parentcat);
            fix_course_sortorder();
        }

        $newcategory->timemodified = time();

        $categorycontext = $this->get_context();
        if ($editoroptions) {
            $newcategory = file_postupdate_standard_editor($newcategory, 'description', $editoroptions, $categorycontext,
                                                           'coursecat', 'description', 0);
        }
        $DB->update_record('course_categories', $newcategory);

        $event = \core\event\course_category_updated::create(array(
            'objectid' => $newcategory->id,
            'context' => $categorycontext
        ));
        $event->trigger();

        fix_course_sortorder();
        // Purge cache even if fix_course_sortorder() did not do it.
        cache_helper::purge_by_event('changesincoursecat');

        // Update all fields in the current object.
        $this->restore();
    }


    /**
     * Checks if this course category is visible to a user.
     *
     * Please note that methods core_course_category::get (without 3rd argumet),
     * core_course_category::get_children(), etc. return only visible categories so it is
     * usually not needed to call this function outside of this class
     *
     * @param int|stdClass $user The user id or object. By default (null) checks the visibility to the current user.
     * @return bool
     */
    public function is_uservisible($user = null) {
        return self::can_view_category($this, $user);
    }

    /**
     * Checks if current user has access to the category
     *
     * @param stdClass|core_course_category $category
     * @param int|stdClass $user The user id or object. By default (null) checks access for the current user.
     * @return bool
     */
    public static function can_view_category($category, $user = null) {
        if (!$category->id) {
            return has_capability('moodle/category:viewcourselist', context_system::instance(), $user);
        }
        $context = context_coursecat::instance($category->id);
        if (!$category->visible && !has_capability('moodle/category:viewhiddencategories', $context, $user)) {
            return false;
        }
        return has_capability('moodle/category:viewcourselist', $context, $user);
    }

    /**
     * Checks if current user can view course information or enrolment page.
     *
     * This method does not check if user is already enrolled in the course
     *
     * @param stdClass $course course object (must have 'id', 'visible' and 'category' fields)
     * @param null|stdClass $user The user id or object. By default (null) checks access for the current user.
     */
    public static function can_view_course_info($course, $user = null) {
        if ($course->id == SITEID) {
            return true;
        }
        if (!$course->visible) {
            $coursecontext = context_course::instance($course->id);
            if (!has_capability('moodle/course:viewhiddencourses', $coursecontext, $user)) {
                return false;
            }
        }
        $categorycontext = isset($course->category) ? context_coursecat::instance($course->category) :
            context_course::instance($course->id)->get_parent_context();
        return has_capability('moodle/category:viewcourselist', $categorycontext, $user);
    }

    /**
     * Returns the complete corresponding record from DB table course_categories
     *
     * Mostly used in deprecated functions
     *
     * @return stdClass
     */
    public function get_db_record() {
        global $DB;
        if ($record = $DB->get_record('course_categories', array('id' => $this->id))) {
            return $record;
        } else {
            return (object)convert_to_array($this);
        }
    }

    /**
     * Returns the entry from categories tree and makes sure the application-level tree cache is built
     *
     * The following keys can be requested:
     *
     * 'countall' - total number of categories in the system (always present)
     * 0 - array of ids of top-level categories (always present)
     * '0i' - array of ids of top-level categories that have visible=0 (always present but may be empty array)
     * $id (int) - array of ids of categories that are direct children of category with id $id. If
     *   category with id $id does not exist, or category has no children, returns empty array
     * $id.'i' - array of ids of children categories that have visible=0
     *
     * @param int|string $id
     * @return mixed
     */
    protected static function get_tree($id) {
        $all = self::get_cached_cat_tree();
        if (is_null($all) || !isset($all[$id])) {
            // Could not get or rebuild the tree, or requested a non-existant ID.
            return [];
        } else {
            return $all[$id];
        }
    }

    /**
     * Return the course category tree.
     *
     * Returns the category tree array, from the cache if available or rebuilding the cache
     * if required. Uses locking to prevent the cache being rebuilt by multiple requests at once.
     *
     * @return array|null The tree as an array, or null if rebuilding the tree failed due to a lock timeout.
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    private static function get_cached_cat_tree() : ?array {
        $coursecattreecache = cache::make('core', 'coursecattree');
        $all = $coursecattreecache->get('all');
        if ($all !== false) {
            return $all;
        }
        // Might need to rebuild the tree. Put a lock in place to ensure other requests don't try and do this in parallel.
        $lockfactory = \core\lock\lock_config::get_lock_factory('core_coursecattree');
        $lock = $lockfactory->get_lock('core_coursecattree_cache',
                course_modinfo::COURSE_CACHE_LOCK_WAIT, course_modinfo::COURSE_CACHE_LOCK_EXPIRY);
        if ($lock === false) {
            // Couldn't get a lock to rebuild the tree.
            return null;
        }
        $all = $coursecattreecache->get('all');
        if ($all !== false) {
            // Tree was built while we were waiting for the lock.
            $lock->release();
            return $all;
        }
        // Re-build the tree.
        try {
            $all = self::rebuild_coursecattree_cache_contents();
            $coursecattreecache->set('all', $all);
        } finally {
            $lock->release();
        }
        return $all;
    }

    /**
     * Rebuild the course category tree as an array, including an extra "countall" field.
     *
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    private static function rebuild_coursecattree_cache_contents() : array {
        global $DB;
        $sql = "SELECT cc.id, cc.parent, cc.visible
                FROM {course_categories} cc
                ORDER BY cc.sortorder";
        $rs = $DB->get_recordset_sql($sql, array());
        $all = array(0 => array(), '0i' => array());
        $count = 0;
        foreach ($rs as $record) {
            $all[$record->id] = array();
            $all[$record->id. 'i'] = array();
            if (array_key_exists($record->parent, $all)) {
                $all[$record->parent][] = $record->id;
                if (!$record->visible) {
                    $all[$record->parent. 'i'][] = $record->id;
                }
            } else {
                // Parent not found. This is data consistency error but next fix_course_sortorder() should fix it.
                $all[0][] = $record->id;
                if (!$record->visible) {
                    $all['0i'][] = $record->id;
                }
            }
            $count++;
        }
        $rs->close();
        if (!$count) {
            // No categories found.
            // This may happen after upgrade of a very old moodle version.
            // In new versions the default category is created on install.
            $defcoursecat = self::create(array('name' => get_string('defaultcategoryname')));
            set_config('defaultrequestcategory', $defcoursecat->id);
            $all[0] = array($defcoursecat->id);
            $all[$defcoursecat->id] = array();
            $count++;
        }
        // We must add countall to all in case it was the requested ID.
        $all['countall'] = $count;
        return $all;
    }

    /**
     * Returns number of ALL categories in the system regardless if
     * they are visible to current user or not
     *
     * @deprecated since Moodle 3.7
     * @return int
     */
    public static function count_all() {
        debugging('Method core_course_category::count_all() is deprecated. Please use ' .
            'core_course_category::is_simple_site()', DEBUG_DEVELOPER);
        return self::get_tree('countall');
    }

    /**
     * Checks if the site has only one category and it is visible and available.
     *
     * In many situations we won't show this category at all
     * @return bool
     */
    public static function is_simple_site() {
        if (self::get_tree('countall') != 1) {
            return false;
        }
        $default = self::get_default();
        return $default->visible && $default->is_uservisible();
    }

    /**
     * Retrieves number of records from course_categories table
     *
     * Only cached fields are retrieved. Records are ready for preloading context
     *
     * @param string $whereclause
     * @param array $params
     * @return array array of stdClass objects
     */
    protected static function get_records($whereclause, $params) {
        global $DB;
        // Retrieve from DB only the fields that need to be stored in cache.
        $fields = array_keys(array_filter(self::$coursecatfields));
        $ctxselect = context_helper::get_preload_record_columns_sql('ctx');
        $sql = "SELECT cc.". join(',cc.', $fields). ", $ctxselect
                FROM {course_categories} cc
                JOIN {context} ctx ON cc.id = ctx.instanceid AND ctx.contextlevel = :contextcoursecat
                WHERE ". $whereclause." ORDER BY cc.sortorder";
        return $DB->get_records_sql($sql,
                array('contextcoursecat' => CONTEXT_COURSECAT) + $params);
    }

    /**
     * Resets course contact caches when role assignments were changed
     *
     * @param int $roleid role id that was given or taken away
     * @param context $context context where role assignment has been changed
     */
    public static function role_assignment_changed($roleid, $context) {
        global $CFG, $DB;

        if ($context->contextlevel > CONTEXT_COURSE) {
            // No changes to course contacts if role was assigned on the module/block level.
            return;
        }

        // Trigger a purge for all caches listening for changes to category enrolment.
        cache_helper::purge_by_event('changesincategoryenrolment');

        if (empty($CFG->coursecontact) || !in_array($roleid, explode(',', $CFG->coursecontact))) {
            // The role is not one of course contact roles.
            return;
        }

        // Remove from cache course contacts of all affected courses.
        $cache = cache::make('core', 'coursecontacts');
        if ($context->contextlevel == CONTEXT_COURSE) {
            $cache->delete($context->instanceid);
        } else if ($context->contextlevel == CONTEXT_SYSTEM) {
            $cache->purge();
        } else {
            $sql = "SELECT ctx.instanceid
                    FROM {context} ctx
                    WHERE ctx.path LIKE ? AND ctx.contextlevel = ?";
            $params = array($context->path . '/%', CONTEXT_COURSE);
            if ($courses = $DB->get_fieldset_sql($sql, $params)) {
                $cache->delete_many($courses);
            }
        }
    }

    /**
     * Executed when user enrolment was changed to check if course
     * contacts cache needs to be cleared
     *
     * @param int $courseid course id
     * @param int $userid user id
     * @param int $status new enrolment status (0 - active, 1 - suspended)
     * @param int $timestart new enrolment time start
     * @param int $timeend new enrolment time end
     */
    public static function user_enrolment_changed($courseid, $userid,
            $status, $timestart = null, $timeend = null) {
        $cache = cache::make('core', 'coursecontacts');
        $contacts = $cache->get($courseid);
        if ($contacts === false) {
            // The contacts for the affected course were not cached anyway.
            return;
        }
        $enrolmentactive = ($status == 0) &&
                (!$timestart || $timestart < time()) &&
                (!$timeend || $timeend > time());
        if (!$enrolmentactive) {
            $isincontacts = false;
            foreach ($contacts as $contact) {
                if ($contact->id == $userid) {
                    $isincontacts = true;
                }
            }
            if (!$isincontacts) {
                // Changed user's enrolment does not exist or is not active,
                // and he is not in cached course contacts, no changes to be made.
                return;
            }
        }
        // Either enrolment of manager was deleted/suspended
        // or user enrolment was added or activated.
        // In order to see if the course contacts for this course need
        // changing we would need to make additional queries, they will
        // slow down bulk enrolment changes. It is better just to remove
        // course contacts cache for this course.
        $cache->delete($courseid);
    }

    /**
     * Given list of DB records from table course populates each record with list of users with course contact roles
     *
     * This function fills the courses with raw information as {@link get_role_users()} would do.
     * See also {@link core_course_list_element::get_course_contacts()} for more readable return
     *
     * $courses[$i]->managers = array(
     *   $roleassignmentid => $roleuser,
     *   ...
     * );
     *
     * where $roleuser is an stdClass with the following properties:
     *
     * $roleuser->raid - role assignment id
     * $roleuser->id - user id
     * $roleuser->username
     * $roleuser->firstname
     * $roleuser->lastname
     * $roleuser->rolecoursealias
     * $roleuser->rolename
     * $roleuser->sortorder - role sortorder
     * $roleuser->roleid
     * $roleuser->roleshortname
     *
     * @todo MDL-38596 minimize number of queries to preload contacts for the list of courses
     *
     * @param array $courses
     */
    public static function preload_course_contacts(&$courses) {
        global $CFG, $DB;
        if (empty($courses) || empty($CFG->coursecontact)) {
            return;
        }
        $managerroles = explode(',', $CFG->coursecontact);
        $cache = cache::make('core', 'coursecontacts');
        $cacheddata = $cache->get_many(array_keys($courses));
        $courseids = array();
        foreach (array_keys($courses) as $id) {
            if ($cacheddata[$id] !== false) {
                $courses[$id]->managers = $cacheddata[$id];
            } else {
                $courseids[] = $id;
            }
        }

        // Array $courseids now stores list of ids of courses for which we still need to retrieve contacts.
        if (empty($courseids)) {
            return;
        }

        // First build the array of all context ids of the courses and their categories.
        $allcontexts = array();
        foreach ($courseids as $id) {
            $context = context_course::instance($id);
            $courses[$id]->managers = array();
            foreach (preg_split('|/|', $context->path, 0, PREG_SPLIT_NO_EMPTY) as $ctxid) {
                if (!isset($allcontexts[$ctxid])) {
                    $allcontexts[$ctxid] = array();
                }
                $allcontexts[$ctxid][] = $id;
            }
        }

        // Fetch list of all users with course contact roles in any of the courses contexts or parent contexts.
        list($sql1, $params1) = $DB->get_in_or_equal(array_keys($allcontexts), SQL_PARAMS_NAMED, 'ctxid');
        list($sql2, $params2) = $DB->get_in_or_equal($managerroles, SQL_PARAMS_NAMED, 'rid');
        list($sort, $sortparams) = users_order_by_sql('u');
        $notdeleted = array('notdeleted' => 0);
        $userfieldsapi = \core_user\fields::for_name();
        $allnames = $userfieldsapi->get_sql('u', false, '', '', false)->selects;
        $sql = "SELECT ra.contextid, ra.id AS raid,
                       r.id AS roleid, r.name AS rolename, r.shortname AS roleshortname,
                       rn.name AS rolecoursealias, u.id, u.username, $allnames
                  FROM {role_assignments} ra
                  JOIN {user} u ON ra.userid = u.id
                  JOIN {role} r ON ra.roleid = r.id
             LEFT JOIN {role_names} rn ON (rn.contextid = ra.contextid AND rn.roleid = r.id)
                WHERE  ra.contextid ". $sql1." AND ra.roleid ". $sql2." AND u.deleted = :notdeleted
             ORDER BY r.sortorder, $sort";
        $rs = $DB->get_recordset_sql($sql, $params1 + $params2 + $notdeleted + $sortparams);
        $checkenrolments = array();
        foreach ($rs as $ra) {
            foreach ($allcontexts[$ra->contextid] as $id) {
                $courses[$id]->managers[$ra->raid] = $ra;
                if (!isset($checkenrolments[$id])) {
                    $checkenrolments[$id] = array();
                }
                $checkenrolments[$id][] = $ra->id;
            }
        }
        $rs->close();

        // Remove from course contacts users who are not enrolled in the course.
        $enrolleduserids = self::ensure_users_enrolled($checkenrolments);
        foreach ($checkenrolments as $id => $userids) {
            if (empty($enrolleduserids[$id])) {
                $courses[$id]->managers = array();
            } else if ($notenrolled = array_diff($userids, $enrolleduserids[$id])) {
                foreach ($courses[$id]->managers as $raid => $ra) {
                    if (in_array($ra->id, $notenrolled)) {
                        unset($courses[$id]->managers[$raid]);
                    }
                }
            }
        }

        // Set the cache.
        $values = array();
        foreach ($courseids as $id) {
            $values[$id] = $courses[$id]->managers;
        }
        $cache->set_many($values);
    }

    /**
     * Preloads the custom fields values in bulk
     *
     * @param array $records
     */
    public static function preload_custom_fields(array &$records) {
        $customfields = \core_course\customfield\course_handler::create()->get_instances_data(array_keys($records));
        foreach ($customfields as $courseid => $data) {
            $records[$courseid]->customfields = $data;
        }
    }

    /**
     * Verify user enrollments for multiple course-user combinations
     *
     * @param array $courseusers array where keys are course ids and values are array
     *     of users in this course whose enrolment we wish to verify
     * @return array same structure as input array but values list only users from input
     *     who are enrolled in the course
     */
    protected static function ensure_users_enrolled($courseusers) {
        global $DB;
        // If the input array is too big, split it into chunks.
        $maxcoursesinquery = 20;
        if (count($courseusers) > $maxcoursesinquery) {
            $rv = array();
            for ($offset = 0; $offset < count($courseusers); $offset += $maxcoursesinquery) {
                $chunk = array_slice($courseusers, $offset, $maxcoursesinquery, true);
                $rv = $rv + self::ensure_users_enrolled($chunk);
            }
            return $rv;
        }

        // Create a query verifying valid user enrolments for the number of courses.
        $sql = "SELECT DISTINCT e.courseid, ue.userid
          FROM {user_enrolments} ue
          JOIN {enrol} e ON e.id = ue.enrolid
          WHERE ue.status = :active
            AND e.status = :enabled
            AND ue.timestart < :now1 AND (ue.timeend = 0 OR ue.timeend > :now2)";
        $now = round(time(), -2); // Rounding helps caching in DB.
        $params = array('enabled' => ENROL_INSTANCE_ENABLED,
            'active' => ENROL_USER_ACTIVE,
            'now1' => $now, 'now2' => $now);
        $cnt = 0;
        $subsqls = array();
        $enrolled = array();
        foreach ($courseusers as $id => $userids) {
            $enrolled[$id] = array();
            if (count($userids)) {
                list($sql2, $params2) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED, 'userid'.$cnt.'_');
                $subsqls[] = "(e.courseid = :courseid$cnt AND ue.userid ".$sql2.")";
                $params = $params + array('courseid'.$cnt => $id) + $params2;
                $cnt++;
            }
        }
        if (count($subsqls)) {
            $sql .= "AND (". join(' OR ', $subsqls).")";
            $rs = $DB->get_recordset_sql($sql, $params);
            foreach ($rs as $record) {
                $enrolled[$record->courseid][] = $record->userid;
            }
            $rs->close();
        }
        return $enrolled;
    }

    /**
     * Retrieves number of records from course table
     *
     * Not all fields are retrieved. Records are ready for preloading context
     *
     * @param string $whereclause
     * @param array $params
     * @param array $options may indicate that summary needs to be retrieved
     * @param bool $checkvisibility if true, capability 'moodle/course:viewhiddencourses' will be checked
     *     on not visible courses and 'moodle/category:viewcourselist' on all courses
     * @return array array of stdClass objects
     */
    protected static function get_course_records($whereclause, $params, $options, $checkvisibility = false) {
        global $DB;
        $ctxselect = context_helper::get_preload_record_columns_sql('ctx');
        $fields = array('c.id', 'c.category', 'c.sortorder',
                        'c.shortname', 'c.fullname', 'c.idnumber',
                        'c.startdate', 'c.enddate', 'c.visible', 'c.cacherev');
        if (!empty($options['summary'])) {
            $fields[] = 'c.summary';
            $fields[] = 'c.summaryformat';
        } else {
            $fields[] = $DB->sql_substr('c.summary', 1, 1). ' as hassummary';
        }
        $sql = "SELECT ". join(',', $fields). ", $ctxselect
                FROM {course} c
                JOIN {context} ctx ON c.id = ctx.instanceid AND ctx.contextlevel = :contextcourse
                WHERE ". $whereclause." ORDER BY c.sortorder";
        $list = $DB->get_records_sql($sql,
                array('contextcourse' => CONTEXT_COURSE) + $params);

        if ($checkvisibility) {
            $mycourses = enrol_get_my_courses();
            // Loop through all records and make sure we only return the courses accessible by user.
            foreach ($list as $course) {
                if (isset($list[$course->id]->hassummary)) {
                    $list[$course->id]->hassummary = strlen($list[$course->id]->hassummary) > 0;
                }
                context_helper::preload_from_record($course);
                $context = context_course::instance($course->id);
                // Check that course is accessible by user.
                if (!array_key_exists($course->id, $mycourses) && !self::can_view_course_info($course)) {
                    unset($list[$course->id]);
                }
            }
        }

        return $list;
    }

    /**
     * Returns array of ids of children categories that current user can not see
     *
     * This data is cached in user session cache
     *
     * @return array
     */
    protected function get_not_visible_children_ids() {
        global $DB;
        $coursecatcache = cache::make('core', 'coursecat');
        if (($invisibleids = $coursecatcache->get('ic'. $this->id)) === false) {
            // We never checked visible children before.
            $hidden = self::get_tree($this->id.'i');
            $catids = self::get_tree($this->id);
            $invisibleids = array();
            if ($catids) {
                // Preload categories contexts.
                list($sql, $params) = $DB->get_in_or_equal($catids, SQL_PARAMS_NAMED, 'id');
                $ctxselect = context_helper::get_preload_record_columns_sql('ctx');
                $contexts = $DB->get_records_sql("SELECT $ctxselect FROM {context} ctx
                    WHERE ctx.contextlevel = :contextcoursecat AND ctx.instanceid ".$sql,
                        array('contextcoursecat' => CONTEXT_COURSECAT) + $params);
                foreach ($contexts as $record) {
                    context_helper::preload_from_record($record);
                }
                // Check access for each category.
                foreach ($catids as $id) {
                    $cat = (object)['id' => $id, 'visible' => in_array($id, $hidden) ? 0 : 1];
                    if (!self::can_view_category($cat)) {
                        $invisibleids[] = $id;
                    }
                }
            }
            $coursecatcache->set('ic'. $this->id, $invisibleids);
        }
        return $invisibleids;
    }

    /**
     * Sorts list of records by several fields
     *
     * @param array $records array of stdClass objects
     * @param array $sortfields assoc array where key is the field to sort and value is 1 for asc or -1 for desc
     * @return int
     */
    protected static function sort_records(&$records, $sortfields) {
        if (empty($records)) {
            return;
        }
        // If sorting by course display name, calculate it (it may be fullname or shortname+fullname).
        if (array_key_exists('displayname', $sortfields)) {
            foreach ($records as $key => $record) {
                if (!isset($record->displayname)) {
                    $records[$key]->displayname = get_course_display_name_for_list($record);
                }
            }
        }
        // Sorting by one field - use core_collator.
        if (count($sortfields) == 1) {
            $property = key($sortfields);
            if (in_array($property, array('sortorder', 'id', 'visible', 'parent', 'depth'))) {
                $sortflag = core_collator::SORT_NUMERIC;
            } else if (in_array($property, array('idnumber', 'displayname', 'name', 'shortname', 'fullname'))) {
                $sortflag = core_collator::SORT_STRING;
            } else {
                $sortflag = core_collator::SORT_REGULAR;
            }
            core_collator::asort_objects_by_property($records, $property, $sortflag);
            if ($sortfields[$property] < 0) {
                $records = array_reverse($records, true);
            }
            return;
        }

        // Sort by multiple fields - use custom sorting.
        uasort($records, function($a, $b) use ($sortfields) {
            foreach ($sortfields as $field => $mult) {
                // Nulls first.
                if (is_null($a->$field) && !is_null($b->$field)) {
                    return -$mult;
                }
                if (is_null($b->$field) && !is_null($a->$field)) {
                    return $mult;
                }

                if (is_string($a->$field) || is_string($b->$field)) {
                    // String fields.
                    if ($cmp = strcoll($a->$field, $b->$field)) {
                        return $mult * $cmp;
                    }
                } else {
                    // Int fields.
                    if ($a->$field > $b->$field) {
                        return $mult;
                    }
                    if ($a->$field < $b->$field) {
                        return -$mult;
                    }
                }
            }
            return 0;
        });
    }

    /**
     * Returns array of children categories visible to the current user
     *
     * @param array $options options for retrieving children
     *    - sort - list of fields to sort. Example
     *             array('idnumber' => 1, 'name' => 1, 'id' => -1)
     *             will sort by idnumber asc, name asc and id desc.
     *             Default: array('sortorder' => 1)
     *             Only cached fields may be used for sorting!
     *    - offset
     *    - limit - maximum number of children to return, 0 or null for no limit
     * @return core_course_category[] Array of core_course_category objects indexed by category id
     */
    public function get_children($options = array()) {
        global $DB;
        $coursecatcache = cache::make('core', 'coursecat');

        // Get default values for options.
        if (!empty($options['sort']) && is_array($options['sort'])) {
            $sortfields = $options['sort'];
        } else {
            $sortfields = array('sortorder' => 1);
        }
        $limit = null;
        if (!empty($options['limit']) && (int)$options['limit']) {
            $limit = (int)$options['limit'];
        }
        $offset = 0;
        if (!empty($options['offset']) && (int)$options['offset']) {
            $offset = (int)$options['offset'];
        }

        // First retrieve list of user-visible and sorted children ids from cache.
        $sortedids = $coursecatcache->get('c'. $this->id. ':'.  serialize($sortfields));
        if ($sortedids === false) {
            $sortfieldskeys = array_keys($sortfields);
            if ($sortfieldskeys[0] === 'sortorder') {
                // No DB requests required to build the list of ids sorted by sortorder.
                // We can easily ignore other sort fields because sortorder is always different.
                $sortedids = self::get_tree($this->id);
                if ($sortedids && ($invisibleids = $this->get_not_visible_children_ids())) {
                    $sortedids = array_diff($sortedids, $invisibleids);
                    if ($sortfields['sortorder'] == -1) {
                        $sortedids = array_reverse($sortedids, true);
                    }
                }
            } else {
                // We need to retrieve and sort all children. Good thing that it is done only on first request.
                if ($invisibleids = $this->get_not_visible_children_ids()) {
                    list($sql, $params) = $DB->get_in_or_equal($invisibleids, SQL_PARAMS_NAMED, 'id', false);
                    $records = self::get_records('cc.parent = :parent AND cc.id '. $sql,
                            array('parent' => $this->id) + $params);
                } else {
                    $records = self::get_records('cc.parent = :parent', array('parent' => $this->id));
                }
                self::sort_records($records, $sortfields);
                $sortedids = array_keys($records);
            }
            $coursecatcache->set('c'. $this->id. ':'.serialize($sortfields), $sortedids);
        }

        if (empty($sortedids)) {
            return array();
        }

        // Now retrieive and return categories.
        if ($offset || $limit) {
            $sortedids = array_slice($sortedids, $offset, $limit);
        }
        if (isset($records)) {
            // Easy, we have already retrieved records.
            if ($offset || $limit) {
                $records = array_slice($records, $offset, $limit, true);
            }
        } else {
            list($sql, $params) = $DB->get_in_or_equal($sortedids, SQL_PARAMS_NAMED, 'id');
            $records = self::get_records('cc.id '. $sql, array('parent' => $this->id) + $params);
        }

        $rv = array();
        foreach ($sortedids as $id) {
            if (isset($records[$id])) {
                $rv[$id] = new self($records[$id]);
            }
        }
        return $rv;
    }

    /**
     * Returns an array of ids of categories that are (direct and indirect) children
     * of this category.
     *
     * @return int[]
     */
    public function get_all_children_ids() {
        $children = [];
        $walk = [$this->id];
        while (count($walk) > 0) {
            $catid = array_pop($walk);
            $directchildren = self::get_tree($catid);
            if (count($directchildren) > 0) {
                $walk = array_merge($walk, $directchildren);
                $children = array_merge($children, $directchildren);
            }
        }

        return $children;
    }

    /**
     * Returns true if the user has the manage capability on any category.
     *
     * This method uses the coursecat cache and an entry `has_manage_capability` to speed up
     * calls to this method.
     *
     * @return bool
     */
    public static function has_manage_capability_on_any() {
        return self::has_capability_on_any('moodle/category:manage');
    }

    /**
     * Checks if the user has at least one of the given capabilities on any category.
     *
     * @param array|string $capabilities One or more capabilities to check. Check made is an OR.
     * @return bool
     */
    public static function has_capability_on_any($capabilities) {
        global $DB;
        if (!isloggedin() || isguestuser()) {
            return false;
        }

        if (!is_array($capabilities)) {
            $capabilities = array($capabilities);
        }
        $keys = array();
        foreach ($capabilities as $capability) {
            $keys[$capability] = sha1($capability);
        }

        /** @var cache_session $cache */
        $cache = cache::make('core', 'coursecat');
        $hascapability = $cache->get_many($keys);
        $needtoload = false;
        foreach ($hascapability as $capability) {
            if ($capability === '1') {
                return true;
            } else if ($capability === false) {
                $needtoload = true;
            }
        }
        if ($needtoload === false) {
            // All capabilities were retrieved and the user didn't have any.
            return false;
        }

        $haskey = null;
        $fields = context_helper::get_preload_record_columns_sql('ctx');
        $sql = "SELECT ctx.instanceid AS categoryid, $fields
                      FROM {context} ctx
                     WHERE contextlevel = :contextlevel
                  ORDER BY depth ASC";
        $params = array('contextlevel' => CONTEXT_COURSECAT);
        $recordset = $DB->get_recordset_sql($sql, $params);
        foreach ($recordset as $context) {
            context_helper::preload_from_record($context);
            $context = context_coursecat::instance($context->categoryid);
            foreach ($capabilities as $capability) {
                if (has_capability($capability, $context)) {
                    $haskey = $capability;
                    break 2;
                }
            }
        }
        $recordset->close();
        if ($haskey === null) {
            $data = array();
            foreach ($keys as $key) {
                $data[$key] = '0';
            }
            $cache->set_many($data);
            return false;
        } else {
            $cache->set($haskey, '1');
            return true;
        }
    }

    /**
     * Returns true if the user can resort any category.
     * @return bool
     */
    public static function can_resort_any() {
        return self::has_manage_capability_on_any();
    }

    /**
     * Returns true if the user can change the parent of any category.
     * @return bool
     */
    public static function can_change_parent_any() {
        return self::has_manage_capability_on_any();
    }

    /**
     * Returns number of subcategories visible to the current user
     *
     * @return int
     */
    public function get_children_count() {
        $sortedids = self::get_tree($this->id);
        $invisibleids = $this->get_not_visible_children_ids();
        return count($sortedids) - count($invisibleids);
    }

    /**
     * Returns true if the category has ANY children, including those not visible to the user
     *
     * @return boolean
     */
    public function has_children() {
        $allchildren = self::get_tree($this->id);
        return !empty($allchildren);
    }

    /**
     * Returns true if the category has courses in it (count does not include courses
     * in child categories)
     *
     * @return bool
     */
    public function has_courses() {
        global $DB;
        return $DB->record_exists_sql("select 1 from {course} where category = ?",
                array($this->id));
    }

    /**
     * Get the link used to view this course category.
     *
     * @return  \moodle_url
     */
    public function get_view_link() {
        return new \moodle_url('/course/index.php', [
            'categoryid' => $this->id,
        ]);
    }

    /**
     * Searches courses
     *
     * List of found course ids is cached for 10 minutes. Cache may be purged prior
     * to this when somebody edits courses or categories, however it is very
     * difficult to keep track of all possible changes that may affect list of courses.
     *
     * @param array $search contains search criterias, such as:
     *     - search - search string
     *     - blocklist - id of block (if we are searching for courses containing specific block0
     *     - modulelist - name of module (if we are searching for courses containing specific module
     *     - tagid - id of tag
     *     - onlywithcompletion - set to true if we only need courses with completion enabled
     * @param array $options display options, same as in get_courses() except 'recursive' is ignored -
     *                       search is always category-independent
     * @param array $requiredcapabilities List of capabilities required to see return course.
     * @return core_course_list_element[]
     */
    public static function search_courses($search, $options = array(), $requiredcapabilities = array()) {
        global $DB;
        $offset = !empty($options['offset']) ? $options['offset'] : 0;
        $limit = !empty($options['limit']) ? $options['limit'] : null;
        $sortfields = !empty($options['sort']) ? $options['sort'] : array('sortorder' => 1);

        $coursecatcache = cache::make('core', 'coursecat');
        $cachekey = 's-'. serialize(
            $search + array('sort' => $sortfields) + array('requiredcapabilities' => $requiredcapabilities)
        );
        $cntcachekey = 'scnt-'. serialize($search);

        $ids = $coursecatcache->get($cachekey);
        if ($ids !== false) {
            // We already cached last search result.
            $ids = array_slice($ids, $offset, $limit);
            $courses = array();
            if (!empty($ids)) {
                list($sql, $params) = $DB->get_in_or_equal($ids, SQL_PARAMS_NAMED, 'id');
                $records = self::get_course_records("c.id ". $sql, $params, $options);
                // Preload course contacts if necessary - saves DB queries later to do it for each course separately.
                if (!empty($options['coursecontacts'])) {
                    self::preload_course_contacts($records);
                }
                // Preload custom fields if necessary - saves DB queries later to do it for each course separately.
                if (!empty($options['customfields'])) {
                    self::preload_custom_fields($records);
                }
                // If option 'idonly' is specified no further action is needed, just return list of ids.
                if (!empty($options['idonly'])) {
                    return array_keys($records);
                }
                // Prepare the list of core_course_list_element objects.
                foreach ($ids as $id) {
                    // If a course is deleted after we got the cache entry it may not exist in the database anymore.
                    if (!empty($records[$id])) {
                        $courses[$id] = new core_course_list_element($records[$id]);
                    }
                }
            }
            return $courses;
        }

        $preloadcoursecontacts = !empty($options['coursecontacts']);
        unset($options['coursecontacts']);

        // Empty search string will return all results.
        if (!isset($search['search'])) {
            $search['search'] = '';
        }

        if (empty($search['blocklist']) && empty($search['modulelist']) && empty($search['tagid'])) {
            // Search courses that have specified words in their names/summaries.
            $searchterms = preg_split('|\s+|', trim($search['search']), 0, PREG_SPLIT_NO_EMPTY);
            $searchcond = $searchcondparams = [];
            if (!empty($search['onlywithcompletion'])) {
                $searchcond = ['c.enablecompletion = :p1'];
                $searchcondparams = ['p1' => 1];
            }
            $courselist = get_courses_search($searchterms, 'c.sortorder ASC', 0, 9999999, $totalcount,
                $requiredcapabilities, $searchcond, $searchcondparams);
            self::sort_records($courselist, $sortfields);
            $coursecatcache->set($cachekey, array_keys($courselist));
            $coursecatcache->set($cntcachekey, $totalcount);
            $records = array_slice($courselist, $offset, $limit, true);
        } else {
            if (!empty($search['blocklist'])) {
                // Search courses that have block with specified id.
                $blockname = $DB->get_field('block', 'name', array('id' => $search['blocklist']));
                $where = 'ctx.id in (SELECT distinct bi.parentcontextid FROM {block_instances} bi
                    WHERE bi.blockname = :blockname)';
                $params = array('blockname' => $blockname);
            } else if (!empty($search['modulelist'])) {
                // Search courses that have module with specified name.
                $where = "c.id IN (SELECT DISTINCT module.course ".
                        "FROM {".$search['modulelist']."} module)";
                $params = array();
            } else if (!empty($search['tagid'])) {
                // Search courses that are tagged with the specified tag.
                $where = "c.id IN (SELECT t.itemid ".
                        "FROM {tag_instance} t WHERE t.tagid = :tagid AND t.itemtype = :itemtype AND t.component = :component)";
                $params = array('tagid' => $search['tagid'], 'itemtype' => 'course', 'component' => 'core');
                if (!empty($search['ctx'])) {
                    $rec = isset($search['rec']) ? $search['rec'] : true;
                    $parentcontext = context::instance_by_id($search['ctx']);
                    if ($parentcontext->contextlevel == CONTEXT_SYSTEM && $rec) {
                        // Parent context is system context and recursive is set to yes.
                        // Nothing to filter - all courses fall into this condition.
                    } else if ($rec) {
                        // Filter all courses in the parent context at any level.
                        $where .= ' AND ctx.path LIKE :contextpath';
                        $params['contextpath'] = $parentcontext->path . '%';
                    } else if ($parentcontext->contextlevel == CONTEXT_COURSECAT) {
                        // All courses in the given course category.
                        $where .= ' AND c.category = :category';
                        $params['category'] = $parentcontext->instanceid;
                    } else {
                        // No courses will satisfy the context criterion, do not bother searching.
                        $where = '1=0';
                    }
                }
            } else {
                debugging('No criteria is specified while searching courses', DEBUG_DEVELOPER);
                return array();
            }
            $courselist = self::get_course_records($where, $params, $options, true);
            if (!empty($requiredcapabilities)) {
                foreach ($courselist as $key => $course) {
                    context_helper::preload_from_record($course);
                    $coursecontext = context_course::instance($course->id);
                    if (!has_all_capabilities($requiredcapabilities, $coursecontext)) {
                        unset($courselist[$key]);
                    }
                }
            }
            self::sort_records($courselist, $sortfields);
            $coursecatcache->set($cachekey, array_keys($courselist));
            $coursecatcache->set($cntcachekey, count($courselist));
            $records = array_slice($courselist, $offset, $limit, true);
        }

        // Preload course contacts if necessary - saves DB queries later to do it for each course separately.
        if (!empty($preloadcoursecontacts)) {
            self::preload_course_contacts($records);
        }
        // Preload custom fields if necessary - saves DB queries later to do it for each course separately.
        if (!empty($options['customfields'])) {
            self::preload_custom_fields($records);
        }
        // If option 'idonly' is specified no further action is needed, just return list of ids.
        if (!empty($options['idonly'])) {
            return array_keys($records);
        }
        // Prepare the list of core_course_list_element objects.
        $courses = array();
        foreach ($records as $record) {
            $courses[$record->id] = new core_course_list_element($record);
        }
        return $courses;
    }

    /**
     * Returns number of courses in the search results
     *
     * It is recommended to call this function after {@link core_course_category::search_courses()}
     * and not before because only course ids are cached. Otherwise search_courses() may
     * perform extra DB queries.
     *
     * @param array $search search criteria, see method search_courses() for more details
     * @param array $options display options. They do not affect the result but
     *     the 'sort' property is used in cache key for storing list of course ids
     * @param array $requiredcapabilities List of capabilities required to see return course.
     * @return int
     */
    public static function search_courses_count($search, $options = array(), $requiredcapabilities = array()) {
        $coursecatcache = cache::make('core', 'coursecat');
        $cntcachekey = 'scnt-'. serialize($search) . serialize($requiredcapabilities);
        if (($cnt = $coursecatcache->get($cntcachekey)) === false) {
            // Cached value not found. Retrieve ALL courses and return their count.
            unset($options['offset']);
            unset($options['limit']);
            unset($options['summary']);
            unset($options['coursecontacts']);
            $options['idonly'] = true;
            $courses = self::search_courses($search, $options, $requiredcapabilities);
            $cnt = count($courses);
        }
        return $cnt;
    }

    /**
     * Retrieves the list of courses accessible by user
     *
     * Not all information is cached, try to avoid calling this method
     * twice in the same request.
     *
     * The following fields are always retrieved:
     * - id, visible, fullname, shortname, idnumber, category, sortorder
     *
     * If you plan to use properties/methods core_course_list_element::$summary and/or
     * core_course_list_element::get_course_contacts()
     * you can preload this information using appropriate 'options'. Otherwise
     * they will be retrieved from DB on demand and it may end with bigger DB load.
     *
     * Note that method core_course_list_element::has_summary() will not perform additional
     * DB queries even if $options['summary'] is not specified
     *
     * List of found course ids is cached for 10 minutes. Cache may be purged prior
     * to this when somebody edits courses or categories, however it is very
     * difficult to keep track of all possible changes that may affect list of courses.
     *
     * @param array $options options for retrieving children
     *    - recursive - return courses from subcategories as well. Use with care,
     *      this may be a huge list!
     *    - summary - preloads fields 'summary' and 'summaryformat'
     *    - coursecontacts - preloads course contacts
     *    - sort - list of fields to sort. Example
     *             array('idnumber' => 1, 'shortname' => 1, 'id' => -1)
     *             will sort by idnumber asc, shortname asc and id desc.
     *             Default: array('sortorder' => 1)
     *             Only cached fields may be used for sorting!
     *    - offset
     *    - limit - maximum number of children to return, 0 or null for no limit
     *    - idonly - returns the array or course ids instead of array of objects
     *               used only in get_courses_count()
     * @return core_course_list_element[]
     */
    public function get_courses($options = array()) {
        global $DB;
        $recursive = !empty($options['recursive']);
        $offset = !empty($options['offset']) ? $options['offset'] : 0;
        $limit = !empty($options['limit']) ? $options['limit'] : null;
        $sortfields = !empty($options['sort']) ? $options['sort'] : array('sortorder' => 1);

        if (!$this->id && !$recursive) {
            // There are no courses on system level unless we need recursive list.
            return [];
        }

        $coursecatcache = cache::make('core', 'coursecat');
        $cachekey = 'l-'. $this->id. '-'. (!empty($options['recursive']) ? 'r' : '').
                 '-'. serialize($sortfields);
        $cntcachekey = 'lcnt-'. $this->id. '-'. (!empty($options['recursive']) ? 'r' : '');

        // Check if we have already cached results.
        $ids = $coursecatcache->get($cachekey);
        if ($ids !== false) {
            // We already cached last search result and it did not expire yet.
            $ids = array_slice($ids, $offset, $limit);
            $courses = array();
            if (!empty($ids)) {
                list($sql, $params) = $DB->get_in_or_equal($ids, SQL_PARAMS_NAMED, 'id');
                $records = self::get_course_records("c.id ". $sql, $params, $options);
                // Preload course contacts if necessary - saves DB queries later to do it for each course separately.
                if (!empty($options['coursecontacts'])) {
                    self::preload_course_contacts($records);
                }
                // If option 'idonly' is specified no further action is needed, just return list of ids.
                if (!empty($options['idonly'])) {
                    return array_keys($records);
                }
                // Preload custom fields if necessary - saves DB queries later to do it for each course separately.
                if (!empty($options['customfields'])) {
                    self::preload_custom_fields($records);
                }
                // Prepare the list of core_course_list_element objects.
                foreach ($ids as $id) {
                    // If a course is deleted after we got the cache entry it may not exist in the database anymore.
                    if (!empty($records[$id])) {
                        $courses[$id] = new core_course_list_element($records[$id]);
                    }
                }
            }
            return $courses;
        }

        // Retrieve list of courses in category.
        $where = 'c.id <> :siteid';
        $params = array('siteid' => SITEID);
        if ($recursive) {
            if ($this->id) {
                $context = context_coursecat::instance($this->id);
                $where .= ' AND ctx.path like :path';
                $params['path'] = $context->path. '/%';
            }
        } else {
            $where .= ' AND c.category = :categoryid';
            $params['categoryid'] = $this->id;
        }
        // Get list of courses without preloaded coursecontacts because we don't need them for every course.
        $list = $this->get_course_records($where, $params, array_diff_key($options, array('coursecontacts' => 1)), true);

        // Sort and cache list.
        self::sort_records($list, $sortfields);
        $coursecatcache->set($cachekey, array_keys($list));
        $coursecatcache->set($cntcachekey, count($list));

        // Apply offset/limit, convert to core_course_list_element and return.
        $courses = array();
        if (isset($list)) {
            if ($offset || $limit) {
                $list = array_slice($list, $offset, $limit, true);
            }
            // Preload course contacts if necessary - saves DB queries later to do it for each course separately.
            if (!empty($options['coursecontacts'])) {
                self::preload_course_contacts($list);
            }
            // Preload custom fields if necessary - saves DB queries later to do it for each course separately.
            if (!empty($options['customfields'])) {
                self::preload_custom_fields($list);
            }
            // If option 'idonly' is specified no further action is needed, just return list of ids.
            if (!empty($options['idonly'])) {
                return array_keys($list);
            }
            // Prepare the list of core_course_list_element objects.
            foreach ($list as $record) {
                $courses[$record->id] = new core_course_list_element($record);
            }
        }
        return $courses;
    }

    /**
     * Returns number of courses visible to the user
     *
     * @param array $options similar to get_courses() except some options do not affect
     *     number of courses (i.e. sort, summary, offset, limit etc.)
     * @return int
     */
    public function get_courses_count($options = array()) {
        $cntcachekey = 'lcnt-'. $this->id. '-'. (!empty($options['recursive']) ? 'r' : '');
        $coursecatcache = cache::make('core', 'coursecat');
        if (($cnt = $coursecatcache->get($cntcachekey)) === false) {
            // Cached value not found. Retrieve ALL courses and return their count.
            unset($options['offset']);
            unset($options['limit']);
            unset($options['summary']);
            unset($options['coursecontacts']);
            $options['idonly'] = true;
            $courses = $this->get_courses($options);
            $cnt = count($courses);
        }
        return $cnt;
    }

    /**
     * Returns true if the user is able to delete this category.
     *
     * Note if this category contains any courses this isn't a full check, it will need to be accompanied by a call to either
     * {@link core_course_category::can_delete_full()} or {@link core_course_category::can_move_content_to()}
     * depending upon what the user wished to do.
     *
     * @return boolean
     */
    public function can_delete() {
        if (!$this->has_manage_capability()) {
            return false;
        }
        return $this->parent_has_manage_capability();
    }

    /**
     * Returns true if user can delete current category and all its contents
     *
     * To be able to delete course category the user must have permission
     * 'moodle/category:manage' in ALL child course categories AND
     * be able to delete all courses
     *
     * @return bool
     */
    public function can_delete_full() {
        global $DB;
        if (!$this->id) {
            // Fool-proof.
            return false;
        }

        if (!$this->has_manage_capability()) {
            return false;
        }

        // Check all child categories (not only direct children).
        $context = $this->get_context();
        $sql = context_helper::get_preload_record_columns_sql('ctx');
        $childcategories = $DB->get_records_sql('SELECT c.id, c.visible, '. $sql.
            ' FROM {context} ctx '.
            ' JOIN {course_categories} c ON c.id = ctx.instanceid'.
            ' WHERE ctx.path like ? AND ctx.contextlevel = ?',
                array($context->path. '/%', CONTEXT_COURSECAT));
        foreach ($childcategories as $childcat) {
            context_helper::preload_from_record($childcat);
            $childcontext = context_coursecat::instance($childcat->id);
            if ((!$childcat->visible && !has_capability('moodle/category:viewhiddencategories', $childcontext)) ||
                    !has_capability('moodle/category:manage', $childcontext)) {
                return false;
            }
        }

        // Check courses.
        $sql = context_helper::get_preload_record_columns_sql('ctx');
        $coursescontexts = $DB->get_records_sql('SELECT ctx.instanceid AS courseid, '.
                    $sql. ' FROM {context} ctx '.
                    'WHERE ctx.path like :pathmask and ctx.contextlevel = :courselevel',
                array('pathmask' => $context->path. '/%',
                    'courselevel' => CONTEXT_COURSE));
        foreach ($coursescontexts as $ctxrecord) {
            context_helper::preload_from_record($ctxrecord);
            if (!can_delete_course($ctxrecord->courseid)) {
                return false;
            }
        }

        // Check if plugins permit deletion of category content.
        $pluginfunctions = $this->get_plugins_callback_function('can_course_category_delete');
        foreach ($pluginfunctions as $pluginfunction) {
            // If at least one plugin does not permit deletion, stop and return false.
            if (!$pluginfunction($this)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Recursively delete category including all subcategories and courses
     *
     * Function {@link core_course_category::can_delete_full()} MUST be called prior
     * to calling this function because there is no capability check
     * inside this function
     *
     * @param boolean $showfeedback display some notices
     * @return array return deleted courses
     * @throws moodle_exception
     */
    public function delete_full($showfeedback = true) {
        global $CFG, $DB;

        require_once($CFG->libdir.'/gradelib.php');
        require_once($CFG->libdir.'/questionlib.php');
        require_once($CFG->dirroot.'/cohort/lib.php');

        // Make sure we won't timeout when deleting a lot of courses.
        $settimeout = core_php_time_limit::raise();

        // Allow plugins to use this category before we completely delete it.
        $pluginfunctions = $this->get_plugins_callback_function('pre_course_category_delete');
        foreach ($pluginfunctions as $pluginfunction) {
            $pluginfunction($this->get_db_record());
        }

        $deletedcourses = array();

        // Get children. Note, we don't want to use cache here because it would be rebuilt too often.
        $children = $DB->get_records('course_categories', array('parent' => $this->id), 'sortorder ASC');
        foreach ($children as $record) {
            $coursecat = new self($record);
            $deletedcourses += $coursecat->delete_full($showfeedback);
        }

        if ($courses = $DB->get_records('course', array('category' => $this->id), 'sortorder ASC')) {
            foreach ($courses as $course) {
                if (!delete_course($course, false)) {
                    throw new moodle_exception('cannotdeletecategorycourse', '', '', $course->shortname);
                }
                $deletedcourses[] = $course;
            }
        }

        // Move or delete cohorts in this context.
        cohort_delete_category($this);

        // Now delete anything that may depend on course category context.
        grade_course_category_delete($this->id, 0, $showfeedback);
        $cb = new \core_contentbank\contentbank();
        if (!$cb->delete_contents($this->get_context())) {
            throw new moodle_exception('errordeletingcontentfromcategory', 'contentbank', '', $this->get_formatted_name());
        }
        if (!question_delete_course_category($this, null)) {
            throw new moodle_exception('cannotdeletecategoryquestions', '', '', $this->get_formatted_name());
        }

        // Delete all events in the category.
        $DB->delete_records('event', array('categoryid' => $this->id));

        // Finally delete the category and it's context.
        $categoryrecord = $this->get_db_record();
        $DB->delete_records('course_categories', array('id' => $this->id));

        $coursecatcontext = context_coursecat::instance($this->id);
        $coursecatcontext->delete();

        cache_helper::purge_by_event('changesincoursecat');

        // Trigger a course category deleted event.
        /** @var \core\event\course_category_deleted $event */
        $event = \core\event\course_category_deleted::create(array(
            'objectid' => $this->id,
            'context' => $coursecatcontext,
            'other' => array('name' => $this->name)
        ));
        $event->add_record_snapshot($event->objecttable, $categoryrecord);
        $event->set_coursecat($this);
        $event->trigger();

        // If we deleted $CFG->defaultrequestcategory, make it point somewhere else.
        if ($this->id == $CFG->defaultrequestcategory) {
            set_config('defaultrequestcategory', $DB->get_field('course_categories', 'MIN(id)', array('parent' => 0)));
        }
        return $deletedcourses;
    }

    /**
     * Checks if user can delete this category and move content (courses, subcategories and questions)
     * to another category. If yes returns the array of possible target categories names
     *
     * If user can not manage this category or it is completely empty - empty array will be returned
     *
     * @return array
     */
    public function move_content_targets_list() {
        global $CFG;
        require_once($CFG->libdir . '/questionlib.php');
        $context = $this->get_context();
        if (!$this->is_uservisible() ||
                !has_capability('moodle/category:manage', $context)) {
            // User is not able to manage current category, he is not able to delete it.
            // No possible target categories.
            return array();
        }

        $testcaps = array();
        // If this category has courses in it, user must have 'course:create' capability in target category.
        if ($this->has_courses()) {
            $testcaps[] = 'moodle/course:create';
        }
        // If this category has subcategories or questions, user must have 'category:manage' capability in target category.
        if ($this->has_children() || question_context_has_any_questions($context)) {
            $testcaps[] = 'moodle/category:manage';
        }
        if (!empty($testcaps)) {
            // Return list of categories excluding this one and it's children.
            return self::make_categories_list($testcaps, $this->id);
        }

        // Category is completely empty, no need in target for contents.
        return array();
    }

    /**
     * Checks if user has capability to move all category content to the new parent before
     * removing this category
     *
     * @param int $newcatid
     * @return bool
     */
    public function can_move_content_to($newcatid) {
        global $CFG;
        require_once($CFG->libdir . '/questionlib.php');

        if (!$this->has_manage_capability()) {
            return false;
        }

        $testcaps = array();
        // If this category has courses in it, user must have 'course:create' capability in target category.
        if ($this->has_courses()) {
            $testcaps[] = 'moodle/course:create';
        }
        // If this category has subcategories or questions, user must have 'category:manage' capability in target category.
        if ($this->has_children() || question_context_has_any_questions($this->get_context())) {
            $testcaps[] = 'moodle/category:manage';
        }
        if (!empty($testcaps) && !has_all_capabilities($testcaps, context_coursecat::instance($newcatid))) {
            // No sufficient capabilities to perform this task.
            return false;
        }

        // Check if plugins permit moving category content.
        $pluginfunctions = $this->get_plugins_callback_function('can_course_category_delete_move');
        $newparentcat = self::get($newcatid, MUST_EXIST, true);
        foreach ($pluginfunctions as $pluginfunction) {
            // If at least one plugin does not permit move on deletion, stop and return false.
            if (!$pluginfunction($this, $newparentcat)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Deletes a category and moves all content (children, courses and questions) to the new parent
     *
     * Note that this function does not check capabilities, {@link core_course_category::can_move_content_to()}
     * must be called prior
     *
     * @param int $newparentid
     * @param bool $showfeedback
     * @return bool
     */
    public function delete_move($newparentid, $showfeedback = false) {
        global $CFG, $DB, $OUTPUT;

        require_once($CFG->libdir.'/gradelib.php');
        require_once($CFG->libdir.'/questionlib.php');
        require_once($CFG->dirroot.'/cohort/lib.php');

        // Get all objects and lists because later the caches will be reset so.
        // We don't need to make extra queries.
        $newparentcat = self::get($newparentid, MUST_EXIST, true);
        $catname = $this->get_formatted_name();
        $children = $this->get_children();
        $params = array('category' => $this->id);
        $coursesids = $DB->get_fieldset_select('course', 'id', 'category = :category ORDER BY sortorder ASC', $params);
        $context = $this->get_context();

        // Allow plugins to make necessary changes before we move the category content.
        $pluginfunctions = $this->get_plugins_callback_function('pre_course_category_delete_move');
        foreach ($pluginfunctions as $pluginfunction) {
            $pluginfunction($this, $newparentcat);
        }

        if ($children) {
            foreach ($children as $childcat) {
                $childcat->change_parent_raw($newparentcat);
                // Log action.
                $event = \core\event\course_category_updated::create(array(
                    'objectid' => $childcat->id,
                    'context' => $childcat->get_context()
                ));
                $event->trigger();
            }
            fix_course_sortorder();
        }

        if ($coursesids) {
            require_once($CFG->dirroot.'/course/lib.php');
            if (!move_courses($coursesids, $newparentid)) {
                if ($showfeedback) {
                    echo $OUTPUT->notification("Error moving courses");
                }
                return false;
            }
            if ($showfeedback) {
                echo $OUTPUT->notification(get_string('coursesmovedout', '', $catname), 'notifysuccess');
            }
        }

        // Move or delete cohorts in this context.
        cohort_delete_category($this);

        // Now delete anything that may depend on course category context.
        grade_course_category_delete($this->id, $newparentid, $showfeedback);
        $cb = new \core_contentbank\contentbank();
        $newparentcontext = context_coursecat::instance($newparentid);
        $result = $cb->move_contents($context, $newparentcontext);
        if ($showfeedback) {
            if ($result) {
                echo $OUTPUT->notification(get_string('contentsmoved', 'contentbank', $catname), 'notifysuccess');
            } else {
                echo $OUTPUT->notification(
                        get_string('errordeletingcontentbankfromcategory', 'contentbank', $catname),
                        'notifysuccess'
                );
            }
        }
        if (!question_delete_course_category($this, $newparentcat)) {
            if ($showfeedback) {
                echo $OUTPUT->notification(get_string('errordeletingquestionsfromcategory', 'question', $catname), 'notifysuccess');
            }
            return false;
        }

        // Finally delete the category and it's context.
        $categoryrecord = $this->get_db_record();
        $DB->delete_records('course_categories', array('id' => $this->id));
        $context->delete();

        // Trigger a course category deleted event.
        /** @var \core\event\course_category_deleted $event */
        $event = \core\event\course_category_deleted::create(array(
            'objectid' => $this->id,
            'context' => $context,
            'other' => array('name' => $this->name, 'contentmovedcategoryid' => $newparentid)
        ));
        $event->add_record_snapshot($event->objecttable, $categoryrecord);
        $event->set_coursecat($this);
        $event->trigger();

        cache_helper::purge_by_event('changesincoursecat');

        if ($showfeedback) {
            echo $OUTPUT->notification(get_string('coursecategorydeleted', '', $catname), 'notifysuccess');
        }

        // If we deleted $CFG->defaultrequestcategory, make it point somewhere else.
        if ($this->id == $CFG->defaultrequestcategory) {
            set_config('defaultrequestcategory', $DB->get_field('course_categories', 'MIN(id)', array('parent' => 0)));
        }
        return true;
    }

    /**
     * Checks if user can move current category to the new parent
     *
     * This checks if new parent category exists, user has manage cap there
     * and new parent is not a child of this category
     *
     * @param int|stdClass|core_course_category $newparentcat
     * @return bool
     */
    public function can_change_parent($newparentcat) {
        if (!has_capability('moodle/category:manage', $this->get_context())) {
            return false;
        }
        if (is_object($newparentcat)) {
            $newparentcat = self::get($newparentcat->id, IGNORE_MISSING);
        } else {
            $newparentcat = self::get((int)$newparentcat, IGNORE_MISSING);
        }
        if (!$newparentcat) {
            return false;
        }
        if ($newparentcat->id == $this->id || in_array($this->id, $newparentcat->get_parents())) {
            // Can not move to itself or it's own child.
            return false;
        }
        if ($newparentcat->id) {
            return has_capability('moodle/category:manage', context_coursecat::instance($newparentcat->id));
        } else {
            return has_capability('moodle/category:manage', context_system::instance());
        }
    }

    /**
     * Moves the category under another parent category. All associated contexts are moved as well
     *
     * This is protected function, use change_parent() or update() from outside of this class
     *
     * @see core_course_category::change_parent()
     * @see core_course_category::update()
     *
     * @param core_course_category $newparentcat
     * @throws moodle_exception
     */
    protected function change_parent_raw(core_course_category $newparentcat) {
        global $DB;

        $context = $this->get_context();

        $hidecat = false;
        if (empty($newparentcat->id)) {
            $DB->set_field('course_categories', 'parent', 0, array('id' => $this->id));
            $newparent = context_system::instance();
        } else {
            if ($newparentcat->id == $this->id || in_array($this->id, $newparentcat->get_parents())) {
                // Can not move to itself or it's own child.
                throw new moodle_exception('cannotmovecategory');
            }
            $DB->set_field('course_categories', 'parent', $newparentcat->id, array('id' => $this->id));
            $newparent = context_coursecat::instance($newparentcat->id);

            if (!$newparentcat->visible and $this->visible) {
                // Better hide category when moving into hidden category, teachers may unhide afterwards and the hidden children
                // will be restored properly.
                $hidecat = true;
            }
        }
        $this->parent = $newparentcat->id;

        $context->update_moved($newparent);

        // Now make it last in new category.
        $DB->set_field('course_categories', 'sortorder',
            get_max_courses_in_category() * MAX_COURSE_CATEGORIES, ['id' => $this->id]);

        if ($hidecat) {
            fix_course_sortorder();
            $this->restore();
            // Hide object but store 1 in visibleold, because when parent category visibility changes this category must
            // become visible again.
            $this->hide_raw(1);
        }
    }

    /**
     * Efficiently moves a category - NOTE that this can have
     * a huge impact access-control-wise...
     *
     * Note that this function does not check capabilities.
     *
     * Example of usage:
     * $coursecat = core_course_category::get($categoryid);
     * if ($coursecat->can_change_parent($newparentcatid)) {
     *     $coursecat->change_parent($newparentcatid);
     * }
     *
     * This function does not update field course_categories.timemodified
     * If you want to update timemodified, use
     * $coursecat->update(array('parent' => $newparentcat));
     *
     * @param int|stdClass|core_course_category $newparentcat
     */
    public function change_parent($newparentcat) {
        // Make sure parent category exists but do not check capabilities here that it is visible to current user.
        if (is_object($newparentcat)) {
            $newparentcat = self::get($newparentcat->id, MUST_EXIST, true);
        } else {
            $newparentcat = self::get((int)$newparentcat, MUST_EXIST, true);
        }
        if ($newparentcat->id != $this->parent) {
            $this->change_parent_raw($newparentcat);
            fix_course_sortorder();
            cache_helper::purge_by_event('changesincoursecat');
            $this->restore();

            $event = \core\event\course_category_updated::create(array(
                'objectid' => $this->id,
                'context' => $this->get_context()
            ));
            $event->trigger();
        }
    }

    /**
     * Hide course category and child course and subcategories
     *
     * If this category has changed the parent and is moved under hidden
     * category we will want to store it's current visibility state in
     * the field 'visibleold'. If admin clicked 'hide' for this particular
     * category, the field 'visibleold' should become 0.
     *
     * All subcategories and courses will have their current visibility in the field visibleold
     *
     * This is protected function, use hide() or update() from outside of this class
     *
     * @see core_course_category::hide()
     * @see core_course_category::update()
     *
     * @param int $visibleold value to set in field $visibleold for this category
     * @return bool whether changes have been made and caches need to be purged afterwards
     */
    protected function hide_raw($visibleold = 0) {
        global $DB;
        $changes = false;

        // Note that field 'visibleold' is not cached so we must retrieve it from DB if it is missing.
        if ($this->id && $this->__get('visibleold') != $visibleold) {
            $this->visibleold = $visibleold;
            $DB->set_field('course_categories', 'visibleold', $visibleold, array('id' => $this->id));
            $changes = true;
        }
        if (!$this->visible || !$this->id) {
            // Already hidden or can not be hidden.
            return $changes;
        }

        $this->visible = 0;
        $DB->set_field('course_categories', 'visible', 0, array('id' => $this->id));
        // Store visible flag so that we can return to it if we immediately unhide.
        $DB->execute("UPDATE {course} SET visibleold = visible WHERE category = ?", array($this->id));
        $DB->set_field('course', 'visible', 0, array('category' => $this->id));
        // Get all child categories and hide too.
        if ($subcats = $DB->get_records_select('course_categories', "path LIKE ?", array("$this->path/%"), 'id, visible')) {
            foreach ($subcats as $cat) {
                $DB->set_field('course_categories', 'visibleold', $cat->visible, array('id' => $cat->id));
                $DB->set_field('course_categories', 'visible', 0, array('id' => $cat->id));
                $DB->execute("UPDATE {course} SET visibleold = visible WHERE category = ?", array($cat->id));
                $DB->set_field('course', 'visible', 0, array('category' => $cat->id));
            }
        }
        return true;
    }

    /**
     * Hide course category and child course and subcategories
     *
     * Note that there is no capability check inside this function
     *
     * This function does not update field course_categories.timemodified
     * If you want to update timemodified, use
     * $coursecat->update(array('visible' => 0));
     */
    public function hide() {
        if ($this->hide_raw(0)) {
            cache_helper::purge_by_event('changesincoursecat');

            $event = \core\event\course_category_updated::create(array(
                'objectid' => $this->id,
                'context' => $this->get_context()
            ));
            $event->trigger();
        }
    }

    /**
     * Show course category and restores visibility for child course and subcategories
     *
     * Note that there is no capability check inside this function
     *
     * This is protected function, use show() or update() from outside of this class
     *
     * @see core_course_category::show()
     * @see core_course_category::update()
     *
     * @return bool whether changes have been made and caches need to be purged afterwards
     */
    protected function show_raw() {
        global $DB;

        if ($this->visible) {
            // Already visible.
            return false;
        }

        $this->visible = 1;
        $this->visibleold = 1;
        $DB->set_field('course_categories', 'visible', 1, array('id' => $this->id));
        $DB->set_field('course_categories', 'visibleold', 1, array('id' => $this->id));
        $DB->execute("UPDATE {course} SET visible = visibleold WHERE category = ?", array($this->id));
        // Get all child categories and unhide too.
        if ($subcats = $DB->get_records_select('course_categories', "path LIKE ?", array("$this->path/%"), 'id, visibleold')) {
            foreach ($subcats as $cat) {
                if ($cat->visibleold) {
                    $DB->set_field('course_categories', 'visible', 1, array('id' => $cat->id));
                }
                $DB->execute("UPDATE {course} SET visible = visibleold WHERE category = ?", array($cat->id));
            }
        }
        return true;
    }

    /**
     * Show course category and restores visibility for child course and subcategories
     *
     * Note that there is no capability check inside this function
     *
     * This function does not update field course_categories.timemodified
     * If you want to update timemodified, use
     * $coursecat->update(array('visible' => 1));
     */
    public function show() {
        if ($this->show_raw()) {
            cache_helper::purge_by_event('changesincoursecat');

            $event = \core\event\course_category_updated::create(array(
                'objectid' => $this->id,
                'context' => $this->get_context()
            ));
            $event->trigger();
        }
    }

    /**
     * Returns name of the category formatted as a string
     *
     * @param array $options formatting options other than context
     * @return string
     */
    public function get_formatted_name($options = array()) {
        if ($this->id) {
            $context = $this->get_context();
            return format_string($this->name, true, array('context' => $context) + $options);
        } else {
            return get_string('top');
        }
    }

    /**
     * Get the nested name of this category, with all of it's parents.
     *
     * @param   bool    $includelinks Whether to wrap each name in the view link for that category.
     * @param   string  $separator The string between each name.
     * @param   array   $options Formatting options.
     * @return  string
     */
    public function get_nested_name($includelinks = true, $separator = ' / ', $options = []) {
        // Get the name of hierarchical name of this category.
        $parents = $this->get_parents();
        $categories = static::get_many($parents);
        $categories[] = $this;

        $names = array_map(function($category) use ($options, $includelinks) {
            if ($includelinks) {
                return html_writer::link($category->get_view_link(), $category->get_formatted_name($options));
            } else {
                return $category->get_formatted_name($options);
            }

        }, $categories);

        return implode($separator, $names);
    }

    /**
     * Returns ids of all parents of the category. Last element in the return array is the direct parent
     *
     * For example, if you have a tree of categories like:
     *   Category (id = 1)
     *      Subcategory (id = 2)
     *         Sub-subcategory (id = 4)
     *   Other category (id = 3)
     *
     * core_course_category::get(1)->get_parents() == array()
     * core_course_category::get(2)->get_parents() == array(1)
     * core_course_category::get(4)->get_parents() == array(1, 2);
     *
     * Note that this method does not check if all parents are accessible by current user
     *
     * @return array of category ids
     */
    public function get_parents() {
        $parents = preg_split('|/|', $this->path, 0, PREG_SPLIT_NO_EMPTY);
        array_pop($parents);
        return $parents;
    }

    /**
     * This function returns a nice list representing category tree
     * for display or to use in a form <select> element
     *
     * List is cached for 10 minutes
     *
     * For example, if you have a tree of categories like:
     *   Category (id = 1)
     *      Subcategory (id = 2)
     *         Sub-subcategory (id = 4)
     *   Other category (id = 3)
     * Then after calling this function you will have
     * array(1 => 'Category',
     *       2 => 'Category / Subcategory',
     *       4 => 'Category / Subcategory / Sub-subcategory',
     *       3 => 'Other category');
     *
     * If you specify $requiredcapability, then only categories where the current
     * user has that capability will be added to $list.
     * If you only have $requiredcapability in a child category, not the parent,
     * then the child catgegory will still be included.
     *
     * If you specify the option $excludeid, then that category, and all its children,
     * are omitted from the tree. This is useful when you are doing something like
     * moving categories, where you do not want to allow people to move a category
     * to be the child of itself.
     *
     * @param string/array $requiredcapability if given, only categories where the current
     *      user has this capability will be returned. Can also be an array of capabilities,
     *      in which case they are all required.
     * @param integer $excludeid Exclude this category and its children from the lists built.
     * @param string $separator string to use as a separator between parent and child category. Default ' / '
     * @return array of strings
     */
    public static function make_categories_list($requiredcapability = '', $excludeid = 0, $separator = ' / ') {
        global $DB;
        $coursecatcache = cache::make('core', 'coursecat');

        // Check if we cached the complete list of user-accessible category names ($baselist) or list of ids
        // with requried cap ($thislist).
        $currentlang = current_language();
        $basecachekey = $currentlang . '_catlist';
        $baselist = $coursecatcache->get($basecachekey);
        $thislist = false;
        $thiscachekey = null;
        if (!empty($requiredcapability)) {
            $requiredcapability = (array)$requiredcapability;
            $thiscachekey = 'catlist:'. serialize($requiredcapability);
            if ($baselist !== false && ($thislist = $coursecatcache->get($thiscachekey)) !== false) {
                $thislist = preg_split('|,|', $thislist, -1, PREG_SPLIT_NO_EMPTY);
            }
        } else if ($baselist !== false) {
            $thislist = array_keys(array_filter($baselist, function($el) {
                return $el['name'] !== false;
            }));
        }

        if ($baselist === false) {
            // We don't have $baselist cached, retrieve it. Retrieve $thislist again in any case.
            $ctxselect = context_helper::get_preload_record_columns_sql('ctx');
            $sql = "SELECT cc.id, cc.sortorder, cc.name, cc.visible, cc.parent, cc.path, $ctxselect
                    FROM {course_categories} cc
                    JOIN {context} ctx ON cc.id = ctx.instanceid AND ctx.contextlevel = :contextcoursecat
                    ORDER BY cc.sortorder";
            $rs = $DB->get_recordset_sql($sql, array('contextcoursecat' => CONTEXT_COURSECAT));
            $baselist = array();
            $thislist = array();
            foreach ($rs as $record) {
                context_helper::preload_from_record($record);
                $canview = self::can_view_category($record);
                $context = context_coursecat::instance($record->id);
                $filtercontext = \context_helper::get_navigation_filter_context($context);
                $baselist[$record->id] = array(
                    'name' => $canview ? format_string($record->name, true, array('context' => $filtercontext)) : false,
                    'path' => $record->path
                );
                if (!$canview || (!empty($requiredcapability) && !has_all_capabilities($requiredcapability, $context))) {
                    // No required capability, added to $baselist but not to $thislist.
                    continue;
                }
                $thislist[] = $record->id;
            }
            $rs->close();
            $coursecatcache->set($basecachekey, $baselist);
            if (!empty($requiredcapability)) {
                $coursecatcache->set($thiscachekey, join(',', $thislist));
            }
        } else if ($thislist === false) {
            // We have $baselist cached but not $thislist. Simplier query is used to retrieve.
            $ctxselect = context_helper::get_preload_record_columns_sql('ctx');
            $sql = "SELECT ctx.instanceid AS id, $ctxselect
                    FROM {context} ctx WHERE ctx.contextlevel = :contextcoursecat";
            $contexts = $DB->get_records_sql($sql, array('contextcoursecat' => CONTEXT_COURSECAT));
            $thislist = array();
            foreach (array_keys($baselist) as $id) {
                if ($baselist[$id]['name'] !== false) {
                    context_helper::preload_from_record($contexts[$id]);
                    if (has_all_capabilities($requiredcapability, context_coursecat::instance($id))) {
                        $thislist[] = $id;
                    }
                }
            }
            $coursecatcache->set($thiscachekey, join(',', $thislist));
        }

        // Now build the array of strings to return, mind $separator and $excludeid.
        $names = array();
        foreach ($thislist as $id) {
            $path = preg_split('|/|', $baselist[$id]['path'], -1, PREG_SPLIT_NO_EMPTY);
            if (!$excludeid || !in_array($excludeid, $path)) {
                $namechunks = array();
                foreach ($path as $parentid) {
                    if (array_key_exists($parentid, $baselist) && $baselist[$parentid]['name'] !== false) {
                        $namechunks[] = $baselist[$parentid]['name'];
                    }
                }
                $names[$id] = join($separator, $namechunks);
            }
        }
        return $names;
    }

    /**
     * Prepares the object for caching. Works like the __sleep method.
     *
     * implementing method from interface cacheable_object
     *
     * @return array ready to be cached
     */
    public function prepare_to_cache() {
        $a = array();
        foreach (self::$coursecatfields as $property => $cachedirectives) {
            if ($cachedirectives !== null) {
                list($shortname, $defaultvalue) = $cachedirectives;
                if ($this->$property !== $defaultvalue) {
                    $a[$shortname] = $this->$property;
                }
            }
        }
        $context = $this->get_context();
        $a['xi'] = $context->id;
        $a['xp'] = $context->path;
        $a['xl'] = $context->locked;
        return $a;
    }

    /**
     * Takes the data provided by prepare_to_cache and reinitialises an instance of the associated from it.
     *
     * implementing method from interface cacheable_object
     *
     * @param array $a
     * @return core_course_category
     */
    public static function wake_from_cache($a) {
        $record = new stdClass;
        foreach (self::$coursecatfields as $property => $cachedirectives) {
            if ($cachedirectives !== null) {
                list($shortname, $defaultvalue) = $cachedirectives;
                if (array_key_exists($shortname, $a)) {
                    $record->$property = $a[$shortname];
                } else {
                    $record->$property = $defaultvalue;
                }
            }
        }
        $record->ctxid = $a['xi'];
        $record->ctxpath = $a['xp'];
        $record->ctxdepth = $record->depth + 1;
        $record->ctxlevel = CONTEXT_COURSECAT;
        $record->ctxinstance = $record->id;
        $record->ctxlocked = $a['xl'];
        return new self($record, true);
    }

    /**
     * Returns true if the user is able to create a top level category.
     * @return bool
     */
    public static function can_create_top_level_category() {
        return self::top()->has_manage_capability();
    }

    /**
     * Returns the category context.
     * @return context_coursecat
     */
    public function get_context() {
        if ($this->id === 0) {
            // This is the special top level category object.
            return context_system::instance();
        } else {
            return context_coursecat::instance($this->id);
        }
    }

    /**
     * Returns true if the user is able to manage this category.
     * @return bool
     */
    public function has_manage_capability() {
        if (!$this->is_uservisible()) {
            return false;
        }
        return has_capability('moodle/category:manage', $this->get_context());
    }

    /**
     * Checks whether the category has access to content bank
     *
     * @return bool
     */
    public function has_contentbank() {
        $cb = new \core_contentbank\contentbank();
        return ($cb->is_context_allowed($this->get_context()) &&
            has_capability('moodle/contentbank:access', $this->get_context()));
    }

    /**
     * Returns true if the user has the manage capability on the parent category.
     * @return bool
     */
    public function parent_has_manage_capability() {
        return ($parent = $this->get_parent_coursecat()) && $parent->has_manage_capability();
    }

    /**
     * Returns true if the current user can create subcategories of this category.
     * @return bool
     */
    public function can_create_subcategory() {
        return $this->has_manage_capability();
    }

    /**
     * Returns true if the user can resort this categories sub categories and courses.
     * Must have manage capability and be able to see all subcategories.
     * @return bool
     */
    public function can_resort_subcategories() {
        return $this->has_manage_capability() && !$this->get_not_visible_children_ids();
    }

    /**
     * Returns true if the user can resort the courses within this category.
     * Must have manage capability and be able to see all courses.
     * @return bool
     */
    public function can_resort_courses() {
        return $this->has_manage_capability() && $this->coursecount == $this->get_courses_count();
    }

    /**
     * Returns true of the user can change the sortorder of this category (resort in the parent category)
     * @return bool
     */
    public function can_change_sortorder() {
        return ($parent = $this->get_parent_coursecat()) && $parent->can_resort_subcategories();
    }

    /**
     * Returns true if the current user can create a course within this category.
     * @return bool
     */
    public function can_create_course() {
        return $this->is_uservisible() && has_capability('moodle/course:create', $this->get_context());
    }

    /**
     * Returns true if the current user can edit this categories settings.
     * @return bool
     */
    public function can_edit() {
        return $this->has_manage_capability();
    }

    /**
     * Returns true if the current user can review role assignments for this category.
     * @return bool
     */
    public function can_review_roles() {
        return $this->is_uservisible() && has_capability('moodle/role:assign', $this->get_context());
    }

    /**
     * Returns true if the current user can review permissions for this category.
     * @return bool
     */
    public function can_review_permissions() {
        return $this->is_uservisible() &&
        has_any_capability(array(
            'moodle/role:assign',
            'moodle/role:safeoverride',
            'moodle/role:override',
            'moodle/role:assign'
        ), $this->get_context());
    }

    /**
     * Returns true if the current user can review cohorts for this category.
     * @return bool
     */
    public function can_review_cohorts() {
        return $this->is_uservisible() &&
            has_any_capability(array('moodle/cohort:view', 'moodle/cohort:manage'), $this->get_context());
    }

    /**
     * Returns true if the current user can review filter settings for this category.
     * @return bool
     */
    public function can_review_filters() {
        return $this->is_uservisible() &&
                has_capability('moodle/filter:manage', $this->get_context()) &&
                count(filter_get_available_in_context($this->get_context())) > 0;
    }

    /**
     * Returns true if the current user is able to change the visbility of this category.
     * @return bool
     */
    public function can_change_visibility() {
        return $this->parent_has_manage_capability();
    }

    /**
     * Returns true if the user can move courses out of this category.
     * @return bool
     */
    public function can_move_courses_out_of() {
        return $this->has_manage_capability();
    }

    /**
     * Returns true if the user can move courses into this category.
     * @return bool
     */
    public function can_move_courses_into() {
        return $this->has_manage_capability();
    }

    /**
     * Returns true if the user is able to restore a course into this category as a new course.
     * @return bool
     */
    public function can_restore_courses_into() {
        return $this->is_uservisible() && has_capability('moodle/restore:restorecourse', $this->get_context());
    }

    /**
     * Resorts the sub categories of this category by the given field.
     *
     * @param string $field One of name, idnumber or descending values of each (appended desc)
     * @param bool $cleanup If true cleanup will be done, if false you will need to do it manually later.
     * @return bool True on success.
     * @throws coding_exception
     */
    public function resort_subcategories($field, $cleanup = true) {
        global $DB;
        $desc = false;
        if (substr($field, -4) === "desc") {
            $desc = true;
            $field = substr($field, 0, -4);  // Remove "desc" from field name.
        }
        if ($field !== 'name' && $field !== 'idnumber') {
            throw new coding_exception('Invalid field requested');
        }
        $children = $this->get_children();
        core_collator::asort_objects_by_property($children, $field, core_collator::SORT_NATURAL);
        if (!empty($desc)) {
            $children = array_reverse($children);
        }
        $i = 1;
        foreach ($children as $cat) {
            $i++;
            $DB->set_field('course_categories', 'sortorder', $i, array('id' => $cat->id));
            $i += $cat->coursecount;
        }
        if ($cleanup) {
            self::resort_categories_cleanup();
        }
        return true;
    }

    /**
     * Cleans things up after categories have been resorted.
     * @param bool $includecourses If set to true we know courses have been resorted as well.
     */
    public static function resort_categories_cleanup($includecourses = false) {
        // This should not be needed but we do it just to be safe.
        fix_course_sortorder();
        cache_helper::purge_by_event('changesincoursecat');
        if ($includecourses) {
            cache_helper::purge_by_event('changesincourse');
        }
    }

    /**
     * Resort the courses within this category by the given field.
     *
     * @param string $field One of fullname, shortname, idnumber or descending values of each (appended desc)
     * @param bool $cleanup
     * @return bool True for success.
     * @throws coding_exception
     */
    public function resort_courses($field, $cleanup = true) {
        global $DB;
        $desc = false;
        if (substr($field, -4) === "desc") {
            $desc = true;
            $field = substr($field, 0, -4);  // Remove "desc" from field name.
        }
        if ($field !== 'fullname' && $field !== 'shortname' && $field !== 'idnumber' && $field !== 'timecreated') {
            // This is ultra important as we use $field in an SQL statement below this.
            throw new coding_exception('Invalid field requested');
        }
        $ctxfields = context_helper::get_preload_record_columns_sql('ctx');
        $sql = "SELECT c.id, c.sortorder, c.{$field}, $ctxfields
                  FROM {course} c
             LEFT JOIN {context} ctx ON ctx.instanceid = c.id
                 WHERE ctx.contextlevel = :ctxlevel AND
                       c.category = :categoryid";
        $params = array(
            'ctxlevel' => CONTEXT_COURSE,
            'categoryid' => $this->id
        );
        $courses = $DB->get_records_sql($sql, $params);
        if (count($courses) > 0) {
            foreach ($courses as $courseid => $course) {
                context_helper::preload_from_record($course);
                if ($field === 'idnumber') {
                    $course->sortby = $course->idnumber;
                } else {
                    // It'll require formatting.
                    $options = array(
                        'context' => context_course::instance($course->id)
                    );
                    // We format the string first so that it appears as the user would see it.
                    // This ensures the sorting makes sense to them. However it won't necessarily make
                    // sense to everyone if things like multilang filters are enabled.
                    // We then strip any tags as we don't want things such as image tags skewing the
                    // sort results.
                    $course->sortby = strip_tags(format_string($course->$field, true, $options));
                }
                // We set it back here rather than using references as there is a bug with using
                // references in a foreach before passing as an arg by reference.
                $courses[$courseid] = $course;
            }
            // Sort the courses.
            core_collator::asort_objects_by_property($courses, 'sortby', core_collator::SORT_NATURAL);
            if (!empty($desc)) {
                $courses = array_reverse($courses);
            }
            $i = 1;
            foreach ($courses as $course) {
                $DB->set_field('course', 'sortorder', $this->sortorder + $i, array('id' => $course->id));
                $i++;
            }
            if ($cleanup) {
                // This should not be needed but we do it just to be safe.
                fix_course_sortorder();
                cache_helper::purge_by_event('changesincourse');
            }
        }
        return true;
    }

    /**
     * Changes the sort order of this categories parent shifting this category up or down one.
     *
     * @param bool $up If set to true the category is shifted up one spot, else its moved down.
     * @return bool True on success, false otherwise.
     */
    public function change_sortorder_by_one($up) {
        global $DB;
        $params = array($this->sortorder, $this->parent);
        if ($up) {
            $select = 'sortorder < ? AND parent = ?';
            $sort = 'sortorder DESC';
        } else {
            $select = 'sortorder > ? AND parent = ?';
            $sort = 'sortorder ASC';
        }
        fix_course_sortorder();
        $swapcategory = $DB->get_records_select('course_categories', $select, $params, $sort, '*', 0, 1);
        $swapcategory = reset($swapcategory);
        if ($swapcategory) {
            $DB->set_field('course_categories', 'sortorder', $swapcategory->sortorder, array('id' => $this->id));
            $DB->set_field('course_categories', 'sortorder', $this->sortorder, array('id' => $swapcategory->id));
            $this->sortorder = $swapcategory->sortorder;

            $event = \core\event\course_category_updated::create(array(
                'objectid' => $this->id,
                'context' => $this->get_context()
            ));
            $event->trigger();

            // Finally reorder courses.
            fix_course_sortorder();
            cache_helper::purge_by_event('changesincoursecat');
            return true;
        }
        return false;
    }

    /**
     * Returns the parent core_course_category object for this category.
     *
     * Only returns parent if it exists and is visible to the current user
     *
     * @return core_course_category|null
     */
    public function get_parent_coursecat() {
        if (!$this->id) {
            return null;
        }
        return self::get($this->parent, IGNORE_MISSING);
    }


    /**
     * Returns true if the user is able to request a new course be created.
     * @return bool
     */
    public function can_request_course() {
        global $CFG;
        require_once($CFG->dirroot . '/course/lib.php');

        return course_request::can_request($this->get_context());
    }

    /**
     * Returns true if the user has all the given permissions.
     *
     * @param array $permissionstocheck The value can be create, manage or any specific capability.
     * @return bool
     */
    private function has_capabilities(array $permissionstocheck): bool {
        if (empty($permissionstocheck)) {
            throw new coding_exception('Invalid permissionstocheck parameter');
        }
        foreach ($permissionstocheck as $permission) {
            if ($permission == 'create') {
                if (!$this->can_create_course()) {
                    return false;
                }
            } else if ($permission == 'manage') {
                if (!$this->has_manage_capability()) {
                    return false;
                }
            } else {
                // Specific capability.
                if (!$this->is_uservisible() || !has_capability($permission, $this->get_context())) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Returns true if the user can approve course requests.
     * @return bool
     */
    public static function can_approve_course_requests() {
        global $CFG, $DB;
        if (empty($CFG->enablecourserequests)) {
            return false;
        }
        $context = context_system::instance();
        if (!has_capability('moodle/site:approvecourse', $context)) {
            return false;
        }
        if (!$DB->record_exists('course_request', array())) {
            return false;
        }
        return true;
    }

    /**
     * General page setup for the course category pages.
     *
     * This method sets up things which are common for the course category pages such as page heading,
     * the active nodes in the page navigation block, the active item in the primary navigation (when applicable).
     *
     * @return void
     */
    public static function page_setup() {
        global $PAGE;

        if ($PAGE->context->contextlevel != CONTEXT_COURSECAT) {
            return;
        }
        $categoryid = $PAGE->context->instanceid;
        // Highlight the 'Home' primary navigation item (when applicable).
        $PAGE->set_primary_active_tab('home');
        // Set the page heading to display the category name.
        $coursecategory = self::get($categoryid, MUST_EXIST, true);
        $PAGE->set_heading($coursecategory->get_formatted_name());
        // Set the category node active in the navigation block.
        if ($coursesnode = $PAGE->navigation->find('courses', navigation_node::COURSE_OTHER)) {
            if ($categorynode = $coursesnode->find($categoryid, navigation_node::TYPE_CATEGORY)) {
                $categorynode->make_active();
            }
        }
    }

    /**
     * Returns the core_course_category object for the first category that the current user have the permission for the course.
     *
     * Only returns if it exists and is creatable/manageable to the current user
     *
     * @param core_course_category $parentcat Parent category to check.
     * @param array $permissionstocheck The value can be create, manage or any specific capability.
     * @return core_course_category|null
     */
    public static function get_nearest_editable_subcategory(core_course_category $parentcat,
        array $permissionstocheck): ?core_course_category {
        global $USER, $DB;

        // First, check the parent category.
        if ($parentcat->has_capabilities($permissionstocheck)) {
            return $parentcat;
        }

        // Get all course category contexts that are children of the parent category's context where
        // a) there is a role assignment for the current user or
        // b) there are role capability overrides for a role that the user has in this context.
        // We never need to return the system context because it cannot be a child of another context.
        $fields = array_keys(array_filter(self::$coursecatfields));
        $ctxselect = context_helper::get_preload_record_columns_sql('ctx');
        $rs = $DB->get_recordset_sql("
                SELECT cc.". join(',cc.', $fields). ", $ctxselect
                  FROM {course_categories} cc
                  JOIN {context} ctx ON cc.id = ctx.instanceid AND ctx.contextlevel = :contextcoursecat1
                  JOIN {role_assignments} ra ON ra.contextid = ctx.id
                 WHERE ctx.path LIKE :parentpath1
                       AND ra.userid = :userid1
            UNION
                SELECT cc.". join(',cc.', $fields). ", $ctxselect
                  FROM {course_categories} cc
                  JOIN {context} ctx ON cc.id = ctx.instanceid AND ctx.contextlevel = :contextcoursecat2
                  JOIN {role_capabilities} rc ON rc.contextid = ctx.id
                  JOIN {role_assignments} rc_ra ON rc_ra.roleid = rc.roleid
                  JOIN {context} rc_ra_ctx ON rc_ra_ctx.id = rc_ra.contextid
                 WHERE ctx.path LIKE :parentpath2
                       AND rc_ra.userid = :userid2
                       AND (ctx.path = rc_ra_ctx.path OR ctx.path LIKE " . $DB->sql_concat("rc_ra_ctx.path", "'/%'") . ")
        ", [
            'contextcoursecat1' => CONTEXT_COURSECAT,
            'contextcoursecat2' => CONTEXT_COURSECAT,
            'parentpath1' => $parentcat->get_context()->path . '/%',
            'parentpath2' => $parentcat->get_context()->path . '/%',
            'userid1' => $USER->id,
            'userid2' => $USER->id
        ]);

        // Check if user has required capabilities in any of the contexts.
        $tocache = [];
        $result = null;
        foreach ($rs as $record) {
            $subcategory = new self($record);
            $tocache[$subcategory->id] = $subcategory;
            if ($subcategory->has_capabilities($permissionstocheck)) {
                $result = $subcategory;
                break;
            }
        }
        $rs->close();

        $coursecatrecordcache = cache::make('core', 'coursecatrecords');
        $coursecatrecordcache->set_many($tocache);

        return $result;
    }
}
