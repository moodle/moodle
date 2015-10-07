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
 * Contains class coursecat reponsible for course category operations
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
class coursecat implements renderable, cacheable_object, IteratorAggregate {
    /** @var coursecat stores pseudo category with id=0. Use coursecat::get(0) to retrieve */
    protected static $coursecat0;

    /** Do not fetch course contacts more often than once per hour. */
    const CACHE_COURSE_CONTACTS_TTL = 3600;

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
    protected $fromcache;

    /** @var bool */
    protected $hasmanagecapability = null;

    /**
     * Magic setter method, we do not want anybody to modify properties from the outside
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value) {
        debugging('Can not change coursecat instance properties!', DEBUG_DEVELOPER);
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
        debugging('Invalid coursecat property accessed! '.$name, DEBUG_DEVELOPER);
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
        debugging('Can not unset coursecat instance properties!', DEBUG_DEVELOPER);
    }

    /**
     * Create an iterator because magic vars can't be seen by 'foreach'.
     *
     * implementing method from interface IteratorAggregate
     *
     * @return ArrayIterator
     */
    public function getIterator() {
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
     * Constructor is protected, use coursecat::get($id) to retrieve category
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
     * If category is not visible to user it is treated as non existing
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
     * @return null|coursecat
     * @throws moodle_exception
     */
    public static function get($id, $strictness = MUST_EXIST, $alwaysreturnhidden = false) {
        if (!$id) {
            if (!isset(self::$coursecat0)) {
                $record = new stdClass();
                $record->id = 0;
                $record->visible = 1;
                $record->depth = 0;
                $record->path = '';
                self::$coursecat0 = new coursecat($record);
            }
            return self::$coursecat0;
        }
        $coursecatrecordcache = cache::make('core', 'coursecatrecords');
        $coursecat = $coursecatrecordcache->get($id);
        if ($coursecat === false) {
            if ($records = self::get_records('cc.id = :id', array('id' => $id))) {
                $record = reset($records);
                $coursecat = new coursecat($record);
                // Store in cache.
                $coursecatrecordcache->set($id, $coursecat);
            }
        }
        if ($coursecat && ($alwaysreturnhidden || $coursecat->is_uservisible())) {
            return $coursecat;
        } else {
            if ($strictness == MUST_EXIST) {
                throw new moodle_exception('unknowncategory');
            }
        }
        return null;
    }

    /**
     * Load many coursecat objects.
     *
     * @global moodle_database $DB
     * @param array $ids An array of category ID's to load.
     * @return coursecat[]
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
                $categories[$record->id] = new coursecat($record);
                $toset[$record->id] = $categories[$record->id];
            }
            $coursecatrecordcache->set_many($toset);
        }
        return $categories;
    }

    /**
     * Returns the first found category
     *
     * Note that if there are no categories visible to the current user on the first level,
     * the invisible category may be returned
     *
     * @return coursecat
     */
    public static function get_default() {
        if ($visiblechildren = self::get(0)->get_children()) {
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
     * @return coursecat
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
        if (!empty($data->idnumber)) {
            if (core_text::strlen($data->idnumber) > 100) {
                throw new moodle_exception('idnumbertoolong');
            }
            if ($DB->record_exists('course_categories', array('idnumber' => $data->idnumber))) {
                throw new moodle_exception('categoryidnumbertaken');
            }
        }
        if (isset($data->idnumber)) {
            $newcategory->idnumber = $data->idnumber;
        }

        if (isset($data->theme) && !empty($CFG->allowcategorythemes)) {
            $newcategory->theme = $data->theme;
        }

        if (empty($data->parent)) {
            $parent = self::get(0);
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

        // We should mark the context as dirty.
        context_coursecat::instance($newcategory->id)->mark_dirty();

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
     * This function calls coursecat::change_parent_raw if field 'parent' is updated.
     * It also calls coursecat::hide_raw or coursecat::show_raw if 'visible' is updated.
     * Visibility is changed first and then parent is changed. This means that
     * if parent category is hidden, the current category will become hidden
     * too and it may overwrite whatever was set in field 'visible'.
     *
     * Note that fields 'path' and 'depth' can not be updated manually
     * Also coursecat::update() can not directly update the field 'sortoder'
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

        if (isset($data->idnumber) && $data->idnumber != $this->idnumber) {
            if (core_text::strlen($data->idnumber) > 100) {
                throw new moodle_exception('idnumbertoolong');
            }
            if ($DB->record_exists('course_categories', array('idnumber' => $data->idnumber))) {
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
     * Checks if this course category is visible to current user
     *
     * Please note that methods coursecat::get (without 3rd argumet),
     * coursecat::get_children(), etc. return only visible categories so it is
     * usually not needed to call this function outside of this class
     *
     * @return bool
     */
    public function is_uservisible() {
        return !$this->id || $this->visible ||
                has_capability('moodle/category:viewhiddencategories', $this->get_context());
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
     *   category with id $id does not exist returns false. If category has no children returns empty array
     * $id.'i' - array of ids of children categories that have visible=0
     *
     * @param int|string $id
     * @return mixed
     */
    protected static function get_tree($id) {
        global $DB;
        $coursecattreecache = cache::make('core', 'coursecattree');
        $rv = $coursecattreecache->get($id);
        if ($rv !== false) {
            return $rv;
        }
        // Re-build the tree.
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
            $defcoursecat = self::create(array('name' => get_string('miscellaneous')));
            set_config('defaultrequestcategory', $defcoursecat->id);
            $all[0] = array($defcoursecat->id);
            $all[$defcoursecat->id] = array();
            $count++;
        }
        // We must add countall to all in case it was the requested ID.
        $all['countall'] = $count;
        foreach ($all as $key => $children) {
            $coursecattreecache->set($key, $children);
        }
        if (array_key_exists($id, $all)) {
            return $all[$id];
        }
        // Requested non-existing category.
        return array();
    }

    /**
     * Returns number of ALL categories in the system regardless if
     * they are visible to current user or not
     *
     * @return int
     */
    public static function count_all() {
        return self::get_tree('countall');
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
     * Given list of DB records from table course populates each record with list of users with course contact roles
     *
     * This function fills the courses with raw information as {@link get_role_users()} would do.
     * See also {@link course_in_list::get_course_contacts()} for more readable return
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
        $notdeleted = array('notdeleted'=>0);
        $allnames = get_all_user_name_fields(true, 'u');
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
     * @param array $options may indicate that summary and/or coursecontacts need to be retrieved
     * @param bool $checkvisibility if true, capability 'moodle/course:viewhiddencourses' will be checked
     *     on not visible courses
     * @return array array of stdClass objects
     */
    protected static function get_course_records($whereclause, $params, $options, $checkvisibility = false) {
        global $DB;
        $ctxselect = context_helper::get_preload_record_columns_sql('ctx');
        $fields = array('c.id', 'c.category', 'c.sortorder',
                        'c.shortname', 'c.fullname', 'c.idnumber',
                        'c.startdate', 'c.visible', 'c.cacherev');
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
            // Loop through all records and make sure we only return the courses accessible by user.
            foreach ($list as $course) {
                if (isset($list[$course->id]->hassummary)) {
                    $list[$course->id]->hassummary = strlen($list[$course->id]->hassummary) > 0;
                }
                if (empty($course->visible)) {
                    // Load context only if we need to check capability.
                    context_helper::preload_from_record($course);
                    if (!has_capability('moodle/course:viewhiddencourses', context_course::instance($course->id))) {
                        unset($list[$course->id]);
                    }
                }
            }
        }

        // Preload course contacts if necessary.
        if (!empty($options['coursecontacts'])) {
            self::preload_course_contacts($list);
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
            $invisibleids = array();
            if ($hidden) {
                // Preload categories contexts.
                list($sql, $params) = $DB->get_in_or_equal($hidden, SQL_PARAMS_NAMED, 'id');
                $ctxselect = context_helper::get_preload_record_columns_sql('ctx');
                $contexts = $DB->get_records_sql("SELECT $ctxselect FROM {context} ctx
                    WHERE ctx.contextlevel = :contextcoursecat AND ctx.instanceid ".$sql,
                        array('contextcoursecat' => CONTEXT_COURSECAT) + $params);
                foreach ($contexts as $record) {
                    context_helper::preload_from_record($record);
                }
                // Check that user has 'viewhiddencategories' capability for each hidden category.
                foreach ($hidden as $id) {
                    if (!has_capability('moodle/category:viewhiddencategories', context_coursecat::instance($id))) {
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
        $records = coursecat_sortable_records::sort($records, $sortfields);
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
     * @return coursecat[] Array of coursecat objects indexed by category id
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
                $rv[$id] = new coursecat($records[$id]);
            }
        }
        return $rv;
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

        /* @var cache_session $cache */
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
     * @param array $options display options, same as in get_courses() except 'recursive' is ignored -
     *                       search is always category-independent
     * @return course_in_list[]
     */
    public static function search_courses($search, $options = array()) {
        global $DB;
        $offset = !empty($options['offset']) ? $options['offset'] : 0;
        $limit = !empty($options['limit']) ? $options['limit'] : null;
        $sortfields = !empty($options['sort']) ? $options['sort'] : array('sortorder' => 1);

        $coursecatcache = cache::make('core', 'coursecat');
        $cachekey = 's-'. serialize($search + array('sort' => $sortfields));
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
                // If option 'idonly' is specified no further action is needed, just return list of ids.
                if (!empty($options['idonly'])) {
                    return array_keys($records);
                }
                // Prepare the list of course_in_list objects.
                foreach ($ids as $id) {
                    $courses[$id] = new course_in_list($records[$id]);
                }
            }
            return $courses;
        }

        $preloadcoursecontacts = !empty($options['coursecontacts']);
        unset($options['coursecontacts']);

        if (!empty($search['search'])) {
            // Search courses that have specified words in their names/summaries.
            $searchterms = preg_split('|\s+|', trim($search['search']), 0, PREG_SPLIT_NO_EMPTY);
            $searchterms = array_filter($searchterms, create_function('$v', 'return strlen($v) > 1;'));
            $courselist = get_courses_search($searchterms, 'c.sortorder ASC', 0, 9999999, $totalcount);
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
                        "FROM {tag_instance} t WHERE t.tagid = :tagid AND t.itemtype = :itemtype)";
                $params = array('tagid' => $search['tagid'], 'itemtype' => 'course');
            } else {
                debugging('No criteria is specified while searching courses', DEBUG_DEVELOPER);
                return array();
            }
            $courselist = self::get_course_records($where, $params, $options, true);
            self::sort_records($courselist, $sortfields);
            $coursecatcache->set($cachekey, array_keys($courselist));
            $coursecatcache->set($cntcachekey, count($courselist));
            $records = array_slice($courselist, $offset, $limit, true);
        }

        // Preload course contacts if necessary - saves DB queries later to do it for each course separately.
        if (!empty($preloadcoursecontacts)) {
            self::preload_course_contacts($records);
        }
        // If option 'idonly' is specified no further action is needed, just return list of ids.
        if (!empty($options['idonly'])) {
            return array_keys($records);
        }
        // Prepare the list of course_in_list objects.
        $courses = array();
        foreach ($records as $record) {
            $courses[$record->id] = new course_in_list($record);
        }
        return $courses;
    }

    /**
     * Returns number of courses in the search results
     *
     * It is recommended to call this function after {@link coursecat::search_courses()}
     * and not before because only course ids are cached. Otherwise search_courses() may
     * perform extra DB queries.
     *
     * @param array $search search criteria, see method search_courses() for more details
     * @param array $options display options. They do not affect the result but
     *     the 'sort' property is used in cache key for storing list of course ids
     * @return int
     */
    public static function search_courses_count($search, $options = array()) {
        $coursecatcache = cache::make('core', 'coursecat');
        $cntcachekey = 'scnt-'. serialize($search);
        if (($cnt = $coursecatcache->get($cntcachekey)) === false) {
            // Cached value not found. Retrieve ALL courses and return their count.
            unset($options['offset']);
            unset($options['limit']);
            unset($options['summary']);
            unset($options['coursecontacts']);
            $options['idonly'] = true;
            $courses = self::search_courses($search, $options);
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
     * If you plan to use properties/methods course_in_list::$summary and/or
     * course_in_list::get_course_contacts()
     * you can preload this information using appropriate 'options'. Otherwise
     * they will be retrieved from DB on demand and it may end with bigger DB load.
     *
     * Note that method course_in_list::has_summary() will not perform additional
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
     * @return course_in_list[]
     */
    public function get_courses($options = array()) {
        global $DB;
        $recursive = !empty($options['recursive']);
        $offset = !empty($options['offset']) ? $options['offset'] : 0;
        $limit = !empty($options['limit']) ? $options['limit'] : null;
        $sortfields = !empty($options['sort']) ? $options['sort'] : array('sortorder' => 1);

        // Check if this category is hidden.
        // Also 0-category never has courses unless this is recursive call.
        if (!$this->is_uservisible() || (!$this->id && !$recursive)) {
            return array();
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
                // Prepare the list of course_in_list objects.
                foreach ($ids as $id) {
                    $courses[$id] = new course_in_list($records[$id]);
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

        // Apply offset/limit, convert to course_in_list and return.
        $courses = array();
        if (isset($list)) {
            if ($offset || $limit) {
                $list = array_slice($list, $offset, $limit, true);
            }
            // Preload course contacts if necessary - saves DB queries later to do it for each course separately.
            if (!empty($options['coursecontacts'])) {
                self::preload_course_contacts($list);
            }
            // If option 'idonly' is specified no further action is needed, just return list of ids.
            if (!empty($options['idonly'])) {
                return array_keys($list);
            }
            // Prepare the list of course_in_list objects.
            foreach ($list as $record) {
                $courses[$record->id] = new course_in_list($record);
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
     * {@link coursecat::can_delete_full()} or {@link coursecat::can_move_content_to()} depending upon what the user wished to do.
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

        $context = $this->get_context();
        if (!$this->is_uservisible() ||
                !has_capability('moodle/category:manage', $context)) {
            return false;
        }

        // Check all child categories (not only direct children).
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

        return true;
    }

    /**
     * Recursively delete category including all subcategories and courses
     *
     * Function {@link coursecat::can_delete_full()} MUST be called prior
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

        $deletedcourses = array();

        // Get children. Note, we don't want to use cache here because it would be rebuilt too often.
        $children = $DB->get_records('course_categories', array('parent' => $this->id), 'sortorder ASC');
        foreach ($children as $record) {
            $coursecat = new coursecat($record);
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
        if (!question_delete_course_category($this, 0, $showfeedback)) {
            throw new moodle_exception('cannotdeletecategoryquestions', '', '', $this->get_formatted_name());
        }

        // Finally delete the category and it's context.
        $DB->delete_records('course_categories', array('id' => $this->id));

        $coursecatcontext = context_coursecat::instance($this->id);
        $coursecatcontext->delete();

        cache_helper::purge_by_event('changesincoursecat');

        // Trigger a course category deleted event.
        /* @var \core\event\course_category_deleted $event */
        $event = \core\event\course_category_deleted::create(array(
            'objectid' => $this->id,
            'context' => $coursecatcontext,
            'other' => array('name' => $this->name)
        ));
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
        $context = $this->get_context();
        if (!$this->is_uservisible() ||
                !has_capability('moodle/category:manage', $context)) {
            return false;
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
            return has_all_capabilities($testcaps, context_coursecat::instance($newcatid));
        }

        // There is no content but still return true.
        return true;
    }

    /**
     * Deletes a category and moves all content (children, courses and questions) to the new parent
     *
     * Note that this function does not check capabilities, {@link coursecat::can_move_content_to()}
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

        if ($children) {
            foreach ($children as $childcat) {
                $childcat->change_parent_raw($newparentcat);
                // Log action.
                $event = \core\event\course_category_updated::create(array(
                    'objectid' => $childcat->id,
                    'context' => $childcat->get_context()
                ));
                $event->set_legacy_logdata(array(SITEID, 'category', 'move', 'editcategory.php?id=' . $childcat->id,
                    $childcat->id));
                $event->trigger();
            }
            fix_course_sortorder();
        }

        if ($coursesids) {
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
        if (!question_delete_course_category($this, $newparentcat, $showfeedback)) {
            if ($showfeedback) {
                echo $OUTPUT->notification(get_string('errordeletingquestionsfromcategory', 'question', $catname), 'notifysuccess');
            }
            return false;
        }

        // Finally delete the category and it's context.
        $DB->delete_records('course_categories', array('id' => $this->id));
        $context->delete();

        // Trigger a course category deleted event.
        /* @var \core\event\course_category_deleted $event */
        $event = \core\event\course_category_deleted::create(array(
            'objectid' => $this->id,
            'context' => $context,
            'other' => array('name' => $this->name)
        ));
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
     * @param int|stdClass|coursecat $newparentcat
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
     * @see coursecat::change_parent()
     * @see coursecat::update()
     *
     * @param coursecat $newparentcat
     * @throws moodle_exception
     */
    protected function change_parent_raw(coursecat $newparentcat) {
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
        $DB->set_field('course_categories', 'sortorder', MAX_COURSES_IN_CATEGORY*MAX_COURSE_CATEGORIES, array('id' => $this->id));

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
     * $coursecat = coursecat::get($categoryid);
     * if ($coursecat->can_change_parent($newparentcatid)) {
     *     $coursecat->change_parent($newparentcatid);
     * }
     *
     * This function does not update field course_categories.timemodified
     * If you want to update timemodified, use
     * $coursecat->update(array('parent' => $newparentcat));
     *
     * @param int|stdClass|coursecat $newparentcat
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
            $event->set_legacy_logdata(array(SITEID, 'category', 'move', 'editcategory.php?id=' . $this->id, $this->id));
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
     * @see coursecat::hide()
     * @see coursecat::update()
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
        $DB->set_field('course_categories', 'visible', 0, array('id'=>$this->id));
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
            $event->set_legacy_logdata(array(SITEID, 'category', 'hide', 'editcategory.php?id=' . $this->id, $this->id));
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
     * @see coursecat::show()
     * @see coursecat::update()
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
            $event->set_legacy_logdata(array(SITEID, 'category', 'show', 'editcategory.php?id=' . $this->id, $this->id));
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
     * Returns ids of all parents of the category. Last element in the return array is the direct parent
     *
     * For example, if you have a tree of categories like:
     *   Miscellaneous (id = 1)
     *      Subcategory (id = 2)
     *         Sub-subcategory (id = 4)
     *   Other category (id = 3)
     *
     * coursecat::get(1)->get_parents() == array()
     * coursecat::get(2)->get_parents() == array(1)
     * coursecat::get(4)->get_parents() == array(1, 2);
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
     *   Miscellaneous (id = 1)
     *      Subcategory (id = 2)
     *         Sub-subcategory (id = 4)
     *   Other category (id = 3)
     * Then after calling this function you will have
     * array(1 => 'Miscellaneous',
     *       2 => 'Miscellaneous / Subcategory',
     *       4 => 'Miscellaneous / Subcategory / Sub-subcategory',
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
     * See also {@link make_categories_options()}
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
            $thislist = array_keys($baselist);
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
                // If the category's parent is not visible to the user, it is not visible as well.
                if (!$record->parent || isset($baselist[$record->parent])) {
                    context_helper::preload_from_record($record);
                    $context = context_coursecat::instance($record->id);
                    if (!$record->visible && !has_capability('moodle/category:viewhiddencategories', $context)) {
                        // No cap to view category, added to neither $baselist nor $thislist.
                        continue;
                    }
                    $baselist[$record->id] = array(
                        'name' => format_string($record->name, true, array('context' => $context)),
                        'path' => $record->path
                    );
                    if (!empty($requiredcapability) && !has_all_capabilities($requiredcapability, $context)) {
                        // No required capability, added to $baselist but not to $thislist.
                        continue;
                    }
                    $thislist[] = $record->id;
                }
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
                context_helper::preload_from_record($contexts[$id]);
                if (has_all_capabilities($requiredcapability, context_coursecat::instance($id))) {
                    $thislist[] = $id;
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
                    $namechunks[] = $baselist[$parentid]['name'];
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
        return $a;
    }

    /**
     * Takes the data provided by prepare_to_cache and reinitialises an instance of the associated from it.
     *
     * implementing method from interface cacheable_object
     *
     * @param array $a
     * @return coursecat
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
        return new coursecat($record, true);
    }

    /**
     * Returns true if the user is able to create a top level category.
     * @return bool
     */
    public static function can_create_top_level_category() {
        return has_capability('moodle/category:manage', context_system::instance());
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
        if ($this->hasmanagecapability === null) {
            $this->hasmanagecapability = has_capability('moodle/category:manage', $this->get_context());
        }
        return $this->hasmanagecapability;
    }

    /**
     * Returns true if the user has the manage capability on the parent category.
     * @return bool
     */
    public function parent_has_manage_capability() {
        return has_capability('moodle/category:manage', get_category_or_system_context($this->parent));
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
        return $this->id && $this->get_parent_coursecat()->can_resort_subcategories();
    }

    /**
     * Returns true if the current user can create a course within this category.
     * @return bool
     */
    public function can_create_course() {
        return has_capability('moodle/course:create', $this->get_context());
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
        return has_capability('moodle/role:assign', $this->get_context());
    }

    /**
     * Returns true if the current user can review permissions for this category.
     * @return bool
     */
    public function can_review_permissions() {
        return has_any_capability(array(
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
        return has_any_capability(array('moodle/cohort:view', 'moodle/cohort:manage'), $this->get_context());
    }

    /**
     * Returns true if the current user can review filter settings for this category.
     * @return bool
     */
    public function can_review_filters() {
        return has_capability('moodle/filter:manage', $this->get_context()) &&
               count(filter_get_available_in_context($this->get_context()))>0;
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
        return has_capability('moodle/restore:restorecourse', $this->get_context());
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
     * @global \moodle_database $DB
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
            $event->set_legacy_logdata(array(SITEID, 'category', 'move', 'management.php?categoryid=' . $this->id,
                $this->id));
            $event->trigger();

            // Finally reorder courses.
            fix_course_sortorder();
            cache_helper::purge_by_event('changesincoursecat');
            return true;
        }
        return false;
    }

    /**
     * Returns the parent coursecat object for this category.
     *
     * @return coursecat
     */
    public function get_parent_coursecat() {
        return self::get($this->parent);
    }


    /**
     * Returns true if the user is able to request a new course be created.
     * @return bool
     */
    public function can_request_course() {
        global $CFG;
        if (empty($CFG->enablecourserequests) || $this->id != $CFG->defaultrequestcategory) {
            return false;
        }
        return !$this->can_create_course() && has_capability('moodle/course:request', $this->get_context());
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
}

/**
 * Class to store information about one course in a list of courses
 *
 * Not all information may be retrieved when object is created but
 * it will be retrieved on demand when appropriate property or method is
 * called.
 *
 * Instances of this class are usually returned by functions
 * {@link coursecat::search_courses()}
 * and
 * {@link coursecat::get_courses()}
 *
 * @property-read int $id
 * @property-read int $category Category ID
 * @property-read int $sortorder
 * @property-read string $fullname
 * @property-read string $shortname
 * @property-read string $idnumber
 * @property-read string $summary Course summary. Field is present if coursecat::get_courses()
 *     was called with option 'summary'. Otherwise will be retrieved from DB on first request
 * @property-read int $summaryformat Summary format. Field is present if coursecat::get_courses()
 *     was called with option 'summary'. Otherwise will be retrieved from DB on first request
 * @property-read string $format Course format. Retrieved from DB on first request
 * @property-read int $showgrades Retrieved from DB on first request
 * @property-read int $newsitems Retrieved from DB on first request
 * @property-read int $startdate
 * @property-read int $marker Retrieved from DB on first request
 * @property-read int $maxbytes Retrieved from DB on first request
 * @property-read int $legacyfiles Retrieved from DB on first request
 * @property-read int $showreports Retrieved from DB on first request
 * @property-read int $visible
 * @property-read int $visibleold Retrieved from DB on first request
 * @property-read int $groupmode Retrieved from DB on first request
 * @property-read int $groupmodeforce Retrieved from DB on first request
 * @property-read int $defaultgroupingid Retrieved from DB on first request
 * @property-read string $lang Retrieved from DB on first request
 * @property-read string $theme Retrieved from DB on first request
 * @property-read int $timecreated Retrieved from DB on first request
 * @property-read int $timemodified Retrieved from DB on first request
 * @property-read int $requested Retrieved from DB on first request
 * @property-read int $enablecompletion Retrieved from DB on first request
 * @property-read int $completionnotify Retrieved from DB on first request
 * @property-read int $cacherev
 *
 * @package    core
 * @subpackage course
 * @copyright  2013 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_in_list implements IteratorAggregate {

    /** @var stdClass record retrieved from DB, may have additional calculated property such as managers and hassummary */
    protected $record;

    /** @var array array of course contacts - stores result of call to get_course_contacts() */
    protected $coursecontacts;

    /** @var bool true if the current user can access the course, false otherwise. */
    protected $canaccess = null;

    /**
     * Creates an instance of the class from record
     *
     * @param stdClass $record except fields from course table it may contain
     *     field hassummary indicating that summary field is not empty.
     *     Also it is recommended to have context fields here ready for
     *     context preloading
     */
    public function __construct(stdClass $record) {
        context_helper::preload_from_record($record);
        $this->record = new stdClass();
        foreach ($record as $key => $value) {
            $this->record->$key = $value;
        }
    }

    /**
     * Indicates if the course has non-empty summary field
     *
     * @return bool
     */
    public function has_summary() {
        if (isset($this->record->hassummary)) {
            return !empty($this->record->hassummary);
        }
        if (!isset($this->record->summary)) {
            // We need to retrieve summary.
            $this->__get('summary');
        }
        return !empty($this->record->summary);
    }

    /**
     * Indicates if the course have course contacts to display
     *
     * @return bool
     */
    public function has_course_contacts() {
        if (!isset($this->record->managers)) {
            $courses = array($this->id => &$this->record);
            coursecat::preload_course_contacts($courses);
        }
        return !empty($this->record->managers);
    }

    /**
     * Returns list of course contacts (usually teachers) to display in course link
     *
     * Roles to display are set up in $CFG->coursecontact
     *
     * The result is the list of users where user id is the key and the value
     * is an array with elements:
     *  - 'user' - object containing basic user information
     *  - 'role' - object containing basic role information (id, name, shortname, coursealias)
     *  - 'rolename' => role_get_name($role, $context, ROLENAME_ALIAS)
     *  - 'username' => fullname($user, $canviewfullnames)
     *
     * @return array
     */
    public function get_course_contacts() {
        global $CFG;
        if (empty($CFG->coursecontact)) {
            // No roles are configured to be displayed as course contacts.
            return array();
        }
        if ($this->coursecontacts === null) {
            $this->coursecontacts = array();
            $context = context_course::instance($this->id);

            if (!isset($this->record->managers)) {
                // Preload course contacts from DB.
                $courses = array($this->id => &$this->record);
                coursecat::preload_course_contacts($courses);
            }

            // Build return array with full roles names (for this course context) and users names.
            $canviewfullnames = has_capability('moodle/site:viewfullnames', $context);
            foreach ($this->record->managers as $ruser) {
                if (isset($this->coursecontacts[$ruser->id])) {
                    // Only display a user once with the highest sortorder role.
                    continue;
                }
                $user = new stdClass();
                $user = username_load_fields_from_object($user, $ruser, null, array('id', 'username'));
                $role = new stdClass();
                $role->id = $ruser->roleid;
                $role->name = $ruser->rolename;
                $role->shortname = $ruser->roleshortname;
                $role->coursealias = $ruser->rolecoursealias;

                $this->coursecontacts[$user->id] = array(
                    'user' => $user,
                    'role' => $role,
                    'rolename' => role_get_name($role, $context, ROLENAME_ALIAS),
                    'username' => fullname($user, $canviewfullnames)
                );
            }
        }
        return $this->coursecontacts;
    }

    /**
     * Checks if course has any associated overview files
     *
     * @return bool
     */
    public function has_course_overviewfiles() {
        global $CFG;
        if (empty($CFG->courseoverviewfileslimit)) {
            return false;
        }
        $fs = get_file_storage();
        $context = context_course::instance($this->id);
        return !$fs->is_area_empty($context->id, 'course', 'overviewfiles');
    }

    /**
     * Returns all course overview files
     *
     * @return array array of stored_file objects
     */
    public function get_course_overviewfiles() {
        global $CFG;
        if (empty($CFG->courseoverviewfileslimit)) {
            return array();
        }
        require_once($CFG->libdir. '/filestorage/file_storage.php');
        require_once($CFG->dirroot. '/course/lib.php');
        $fs = get_file_storage();
        $context = context_course::instance($this->id);
        $files = $fs->get_area_files($context->id, 'course', 'overviewfiles', false, 'filename', false);
        if (count($files)) {
            $overviewfilesoptions = course_overviewfiles_options($this->id);
            $acceptedtypes = $overviewfilesoptions['accepted_types'];
            if ($acceptedtypes !== '*') {
                // Filter only files with allowed extensions.
                require_once($CFG->libdir. '/filelib.php');
                foreach ($files as $key => $file) {
                    if (!file_extension_in_typegroup($file->get_filename(), $acceptedtypes)) {
                        unset($files[$key]);
                    }
                }
            }
            if (count($files) > $CFG->courseoverviewfileslimit) {
                // Return no more than $CFG->courseoverviewfileslimit files.
                $files = array_slice($files, 0, $CFG->courseoverviewfileslimit, true);
            }
        }
        return $files;
    }

    /**
     * Magic method to check if property is set
     *
     * @param string $name
     * @return bool
     */
    public function __isset($name) {
        return isset($this->record->$name);
    }

    /**
     * Magic method to get a course property
     *
     * Returns any field from table course (retrieves it from DB if it was not retrieved before)
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name) {
        global $DB;
        if (property_exists($this->record, $name)) {
            return $this->record->$name;
        } else if ($name === 'summary' || $name === 'summaryformat') {
            // Retrieve fields summary and summaryformat together because they are most likely to be used together.
            $record = $DB->get_record('course', array('id' => $this->record->id), 'summary, summaryformat', MUST_EXIST);
            $this->record->summary = $record->summary;
            $this->record->summaryformat = $record->summaryformat;
            return $this->record->$name;
        } else if (array_key_exists($name, $DB->get_columns('course'))) {
            // Another field from table 'course' that was not retrieved.
            $this->record->$name = $DB->get_field('course', $name, array('id' => $this->record->id), MUST_EXIST);
            return $this->record->$name;
        }
        debugging('Invalid course property accessed! '.$name);
        return null;
    }

    /**
     * All properties are read only, sorry.
     *
     * @param string $name
     */
    public function __unset($name) {
        debugging('Can not unset '.get_class($this).' instance properties!');
    }

    /**
     * Magic setter method, we do not want anybody to modify properties from the outside
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value) {
        debugging('Can not change '.get_class($this).' instance properties!');
    }

    /**
     * Create an iterator because magic vars can't be seen by 'foreach'.
     * Exclude context fields
     *
     * Implementing method from interface IteratorAggregate
     *
     * @return ArrayIterator
     */
    public function getIterator() {
        $ret = array('id' => $this->record->id);
        foreach ($this->record as $property => $value) {
            $ret[$property] = $value;
        }
        return new ArrayIterator($ret);
    }

    /**
     * Returns the name of this course as it should be displayed within a list.
     * @return string
     */
    public function get_formatted_name() {
        return format_string(get_course_display_name_for_list($this), true, $this->get_context());
    }

    /**
     * Returns the formatted fullname for this course.
     * @return string
     */
    public function get_formatted_fullname() {
        return format_string($this->__get('fullname'), true, $this->get_context());
    }

    /**
     * Returns the formatted shortname for this course.
     * @return string
     */
    public function get_formatted_shortname() {
        return format_string($this->__get('shortname'), true, $this->get_context());
    }

    /**
     * Returns true if the current user can access this course.
     * @return bool
     */
    public function can_access() {
        if ($this->canaccess === null) {
            $this->canaccess = can_access_course($this->record);
        }
        return $this->canaccess;
    }

    /**
     * Returns true if the user can edit this courses settings.
     *
     * Note: this function does not check that the current user can access the course.
     * To do that please call require_login with the course, or if not possible call {@see course_in_list::can_access()}
     *
     * @return bool
     */
    public function can_edit() {
        return has_capability('moodle/course:update', $this->get_context());
    }

    /**
     * Returns true if the user can change the visibility of this course.
     *
     * Note: this function does not check that the current user can access the course.
     * To do that please call require_login with the course, or if not possible call {@see course_in_list::can_access()}
     *
     * @return bool
     */
    public function can_change_visibility() {
        // You must be able to both hide a course and view the hidden course.
        return has_all_capabilities(array('moodle/course:visibility', 'moodle/course:viewhiddencourses'), $this->get_context());
    }

    /**
     * Returns the context for this course.
     * @return context_course
     */
    public function get_context() {
        return context_course::instance($this->__get('id'));
    }

    /**
     * Returns true if this course is visible to the current user.
     * @return bool
     */
    public function is_uservisible() {
        return $this->visible || has_capability('moodle/course:viewhiddencourses', $this->get_context());
    }

    /**
     * Returns true if the current user can review enrolments for this course.
     *
     * Note: this function does not check that the current user can access the course.
     * To do that please call require_login with the course, or if not possible call {@see course_in_list::can_access()}
     *
     * @return bool
     */
    public function can_review_enrolments() {
        return has_capability('moodle/course:enrolreview', $this->get_context());
    }

    /**
     * Returns true if the current user can delete this course.
     *
     * Note: this function does not check that the current user can access the course.
     * To do that please call require_login with the course, or if not possible call {@see course_in_list::can_access()}
     *
     * @return bool
     */
    public function can_delete() {
        return can_delete_course($this->id);
    }

    /**
     * Returns true if the current user can backup this course.
     *
     * Note: this function does not check that the current user can access the course.
     * To do that please call require_login with the course, or if not possible call {@see course_in_list::can_access()}
     *
     * @return bool
     */
    public function can_backup() {
        return has_capability('moodle/backup:backupcourse', $this->get_context());
    }

    /**
     * Returns true if the current user can restore this course.
     *
     * Note: this function does not check that the current user can access the course.
     * To do that please call require_login with the course, or if not possible call {@see course_in_list::can_access()}
     *
     * @return bool
     */
    public function can_restore() {
        return has_capability('moodle/restore:restorecourse', $this->get_context());
    }
}

/**
 * An array of records that is sortable by many fields.
 *
 * For more info on the ArrayObject class have a look at php.net.
 *
 * @package    core
 * @subpackage course
 * @copyright  2013 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class coursecat_sortable_records extends ArrayObject {

    /**
     * An array of sortable fields.
     * Gets set temporarily when sort is called.
     * @var array
     */
    protected $sortfields = array();

    /**
     * Sorts this array using the given fields.
     *
     * @param array $records
     * @param array $fields
     * @return array
     */
    public static function sort(array $records, array $fields) {
        $records = new coursecat_sortable_records($records);
        $records->sortfields = $fields;
        $records->uasort(array($records, 'sort_by_many_fields'));
        return $records->getArrayCopy();
    }

    /**
     * Sorts the two records based upon many fields.
     *
     * This method should not be called itself, please call $sort instead.
     * It has been marked as access private as such.
     *
     * @access private
     * @param stdClass $a
     * @param stdClass $b
     * @return int
     */
    public function sort_by_many_fields($a, $b) {
        foreach ($this->sortfields as $field => $mult) {
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
    }
}
