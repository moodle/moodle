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

namespace core_course;

use cm_info;
use core_cache\cache;
use core_courseformat\sectiondelegatemodule;
use core\context\module as context_module;
use core\context_helper;
use core\exception\coding_exception;
use core\exception\moodle_exception;
use core\url;
use section_info;
use stdClass;

/**
 * Information about a course that is cached in the course table 'modinfo' field (and then in
 * memory) in order to reduce the need for other database queries.
 *
 * This includes information about the course-modules and the sections on the course. It can also
 * include dynamic data that has been updated for the current user.
 *
 * Use {@see get_fast_modinfo()} to retrieve the instance of the object for particular course
 * and particular user.
 *
 * @package    core
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  Sam Marshall
 * @property-read int $courseid Course ID
 * @property-read int $userid User ID
 * @property-read array $sections Array from section number (e.g. 0) to array of course-module IDs in that
 *     section; this only includes sections that contain at least one course-module
 * @property-read cm_info[] $cms Array from course-module instance to cm_info object within this course, in
 *     order of appearance
 * @property-read cm_info[][] $instances Array from string (modname) => int (instance id) => cm_info object
 * @property-read array $groups Groups that the current user belongs to. Calculated on the first request.
 *     Is an array of grouping id => array of group id => group id. Includes grouping id 0 for 'all groups'
 */
class modinfo {
    /**
     * @var int Summary of MAX_CACHE_SIZE
     *
     * Maximum number of modinfo items to keep in memory cache. Do not increase this to a large
     * number because:
     * a) modinfo can be big (megabyte range) for some courses
     * b) performance of cache will deteriorate if there are very many items in it.
     */
    public const MAX_CACHE_SIZE = 10;

    /** @var int Maximum time the course cache building lock can be held */
    public const COURSE_CACHE_LOCK_EXPIRY = 180;

    /** @var int Time to wait for the course cache building lock before throwing an exception */
    public const COURSE_CACHE_LOCK_WAIT = 60;

    /**
     * List of fields from DB table 'course' that are cached in MUC and are always present in modinfo::$course
     * @var array
     */
    public static $cachedfields = ['shortname', 'fullname', 'format',
            'enablecompletion', 'groupmode', 'groupmodeforce', 'cacherev'];

    /**
     * For convenience we store the course object here as it is needed in other parts of code
     * @var stdClass
     */
    private stdClass $course;

    /**
     * Array of section data from cache indexed by section number.
     * @var section_info[]
     */
    private array $sectioninfobynum;

    /**
     * Array of section data from cache indexed by id.
     * @var section_info[]
     */
    private array $sectioninfobyid;

    /** @var array Index of delegated sections (indexed by component and itemid) */
    private array $delegatedsections;

    /**
     * Index of sections delegated by course modules, indexed by course module instance.
     * @var null|section_info[]
     */
    private ?array $delegatedbycm = null;

    /**
     * Contains the course content weights so they can be sorted accordingly.
     *
     * @var array|null
     */
    private ?array $weights = null;

    /**
     * User ID
     * @var int
     */
    private $userid;

    /**
     * Array indexed by section num (e.g. 0) => array of course-module ids
     * This list only includes sections that actually contain at least one course-module
     * @var array
     */
    private $sectionmodules;

    /**
     * Array from int (cm id) => cm_info object
     * @var cm_info[]
     */
    private $cms;

    /**
     * Array from string (modname) => int (instance id) => cm_info object
     * @var cm_info[][]
     */
    private $instances;

    /**
     * Groups that the current user belongs to. This value is calculated on first
     * request to the property or function.
     * When set, it is an array of grouping id => array of group id => group id.
     * Includes grouping id 0 for 'all groups'.
     * @var int[][]
     */
    private $groups;

    /**
     * List of class read-only properties and their getter methods.
     * Used by magic functions __get(), __isset().
     * @var array
     */
    private static $standardproperties = [
        'courseid' => 'get_course_id',
        'userid' => 'get_user_id',
        'sections' => 'get_sections',
        'cms' => 'get_cms',
        'instances' => 'get_instances',
        'groups' => 'get_groups_all',
        'delegatedbycm' => 'get_sections_delegated_by_cm',
    ];

    /**
     * Magic method getter
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name) {
        if (isset(self::$standardproperties[$name])) {
            $method = self::$standardproperties[$name];
            return $this->$method();
        } else {
            debugging('Invalid modinfo property accessed: ' . $name);
            return null;
        }
    }

    /**
     * Magic method for function isset()
     *
     * @param string $name
     * @return bool
     */
    public function __isset($name) {
        if (isset(self::$standardproperties[$name])) {
            $value = $this->__get($name);
            return isset($value);
        }
        return false;
    }

    /**
     * Magic method setter
     *
     * Will display the developer warning when trying to set/overwrite existing property.
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value) {
        debugging("It is not allowed to set the property modinfo::\${$name}", DEBUG_DEVELOPER);
    }

    /**
     * Returns course object that was used in the first {@see get_fast_modinfo()} call.
     *
     * It may not contain all fields from DB table {course} but always has at least the following:
     * id,shortname,fullname,format,enablecompletion,groupmode,groupmodeforce,cacherev
     *
     * @return stdClass
     */
    public function get_course() {
        return $this->course;
    }

    /**
     * Returns the course ID for which this modinfo was generated.
     *
     * @return int Course ID
     */
    public function get_course_id() {
        return $this->course->id;
    }

    /**
     * Returns the user ID for which this modinfo was generated.
     *
     * @return int User ID
     */
    public function get_user_id() {
        return $this->userid;
    }

    /**
     * Obtains all sections as array from section number (e.g. 0) to array of course-module IDs in that
     *
     * @return array Array from section number (e.g. 0) to array of course-module IDs in that
     *   section; this only includes sections that contain at least one course-module
     */
    public function get_sections() {
        return $this->sectionmodules;
    }

    /**
     * Obtains all course-module objects (for course-modules that are on this course).
     *
     * @return cm_info[] Array from course-module instance to cm_info object within this course, in
     *   order of appearance
     */
    public function get_cms() {
        return $this->cms;
    }

    /**
     * Obtains a single course-module object (for a course-module that is on this course).
     * @param int $cmid Course-module ID
     * @return cm_info Information about that course-module
     * @throws moodle_exception If the course-module does not exist
     */
    public function get_cm($cmid) {
        if (empty($this->cms[$cmid])) {
            throw new moodle_exception('invalidcoursemoduleid', 'error', '', $cmid);
        }
        return $this->cms[$cmid];
    }

    /**
     * Obtains all module instances on this course.
     * @return cm_info[][] Array from module name => array from instance id => cm_info
     */
    public function get_instances() {
        return $this->instances;
    }

    /**
     * Returns array of localised human-readable module names used in this course
     *
     * @param bool $plural if true returns the plural form of modules names
     * @return array
     */
    public function get_used_module_names($plural = false) {
        $modnames = get_module_types_names($plural);
        $modnamesused = [];
        foreach ($this->get_cms() as $cmid => $mod) {
            if (!isset($modnamesused[$mod->modname]) && isset($modnames[$mod->modname]) && $mod->uservisible) {
                $modnamesused[$mod->modname] = $modnames[$mod->modname];
            }
        }
        return $modnamesused;
    }

    /**
     * Obtains all instances of a particular module on this course.
     * @param string $modname Name of module (not full frankenstyle) e.g. 'label'
     * @return cm_info[] Array from instance id => cm_info for modules on this course; empty if none
     */
    public function get_instances_of($modname) {
        if (empty($this->instances[$modname])) {
            return [];
        }
        return $this->instances[$modname];
    }

    /**
     * Obtains a single instance of a particular module on this course.
     *
     * @param string $modname Name of module (not full frankenstyle) e.g. 'label'
     * @param int $instanceid Instance id
     * @param int $strictness Use IGNORE_MISSING to return null if not found, or MUST_EXIST to throw exception
     * @return cm_info|null cm_info for the instance on this course or null if not found
     * @throws moodle_exception If the instance is not found
     */
    public function get_instance_of(string $modname, int $instanceid, int $strictness = IGNORE_MISSING): ?cm_info {
        if (empty($this->instances[$modname]) || empty($this->instances[$modname][$instanceid])) {
            if ($strictness === IGNORE_MISSING) {
                return null;
            }
            throw new moodle_exception('invalidmoduleid', 'error', '', $instanceid);
        }
        return $this->instances[$modname][$instanceid];
    }

    /**
     * Sorts the given array of course modules according to the order they appear on the course page.
     *
     * @param cm_info[] $cms Array of cm_info objects to sort by reference
     * @return void
     */
    public function sort_cm_array(array &$cms): void {
        $weights = $this->get_content_weights();
        uasort($cms, function ($a, $b) use ($weights) {
            $weighta = $weights['cm' . $a->id] ?? PHP_INT_MAX;
            $weightb = $weights['cm' . $b->id] ?? PHP_INT_MAX;
            return $weighta <=> $weightb;
        });
    }

    /**
     * Groups that the current user belongs to organised by grouping id. Calculated on the first request.
     * @return int[][] array of grouping id => array of group id => group id. Includes grouping id 0 for 'all groups'
     */
    private function get_groups_all() {
        if (is_null($this->groups)) {
            $this->groups = groups_get_user_groups($this->course->id, $this->userid);
        }
        return $this->groups;
    }

    /**
     * Returns groups that the current user belongs to on the course. Note: If not already
     * available, this may make a database query.
     * @param int $groupingid Grouping ID or 0 (default) for all groups
     * @return int[] Array of int (group id) => int (same group id again); empty array if none
     */
    public function get_groups($groupingid = 0) {
        $allgroups = $this->get_groups_all();
        if (!isset($allgroups[$groupingid])) {
            return [];
        }
        return $allgroups[$groupingid];
    }

    /**
     * Gets all sections as array from section number => data about section.
     *
     * The method will return all sections of the course, including the ones
     * delegated to a component.
     *
     * @return section_info[] Array of section_info objects organised by section number
     */
    public function get_section_info_all() {
        return $this->sectioninfobynum;
    }

    /**
     * Gets all sections listed in course page as array from section number => data about section.
     *
     * The method is similar to get_section_info_all but filtering all sections delegated to components.
     *
     * @return section_info[] Array of section_info objects organised by section number
     */
    public function get_listed_section_info_all() {
        if (empty($this->delegatedsections)) {
            return $this->sectioninfobynum;
        }
        $sections = [];
        foreach ($this->sectioninfobynum as $section) {
            if (!$section->get_component_instance()) {
                $sections[$section->section] = $section;
            }
        }
        return $sections;
    }

    /**
     * Gets data about specific numbered section.
     * @param int $sectionnumber Number (not id) of section
     * @param int $strictness Use MUST_EXIST to throw exception if it doesn't
     * @return ?section_info Information for numbered section or null if not found
     */
    public function get_section_info($sectionnumber, $strictness = IGNORE_MISSING) {
        if (!array_key_exists($sectionnumber, $this->sectioninfobynum)) {
            if ($strictness === MUST_EXIST) {
                throw new moodle_exception('sectionnotexist');
            } else {
                return null;
            }
        }
        return $this->sectioninfobynum[$sectionnumber];
    }

    /**
     * Gets data about specific section ID.
     * @param int $sectionid ID (not number) of section
     * @param int $strictness Use MUST_EXIST to throw exception if it doesn't
     * @return section_info|null Information for numbered section or null if not found
     */
    public function get_section_info_by_id(int $sectionid, int $strictness = IGNORE_MISSING): ?section_info {
        if (!array_key_exists($sectionid, $this->sectioninfobyid)) {
            if ($strictness === MUST_EXIST) {
                throw new moodle_exception('sectionnotexist');
            } else {
                return null;
            }
        }
        return $this->sectioninfobyid[$sectionid];
    }

    /**
     * Gets data about specific delegated section.
     * @param string $component Component name
     * @param int $itemid Item id
     * @param int $strictness Use MUST_EXIST to throw exception if it doesn't
     * @return section_info|null Information for numbered section or null if not found
     */
    public function get_section_info_by_component(
        string $component,
        int $itemid,
        int $strictness = IGNORE_MISSING
    ): ?section_info {
        if (!isset($this->delegatedsections[$component][$itemid])) {
            if ($strictness === MUST_EXIST) {
                throw new moodle_exception('sectionnotexist');
            } else {
                return null;
            }
        }
        return $this->delegatedsections[$component][$itemid];
    }

    /**
     * Check if the course has delegated sections.
     * @return bool
     */
    public function has_delegated_sections(): bool {
        return !empty($this->delegatedsections);
    }

    /**
     * Gets data about section delegated by course modules.
     *
     * @return section_info[] sections array indexed by course module ID
     */
    public function get_sections_delegated_by_cm(): array {
        if (!is_null($this->delegatedbycm)) {
            return $this->delegatedbycm;
        }
        $this->delegatedbycm = [];
        foreach ($this->delegatedsections as $componentsections) {
            foreach ($componentsections as $section) {
                $delegateinstance = $section->get_component_instance();
                // We only return sections delegated by course modules. Sections delegated to other
                // types of components must implement their own methods to get the section.
                if (!$delegateinstance || !($delegateinstance instanceof sectiondelegatemodule)) {
                    continue;
                }
                if (!$cm = $delegateinstance->get_cm()) {
                    continue;
                }
                $this->delegatedbycm[$cm->id] = $section;
            }
        }
        return $this->delegatedbycm;
    }

    /**
     * @var static[] Static cache for generated modinfo instances
     *
     * @see modinfo::instance()
     * @see modinfo::clear_instance_cache()
     */
    protected static $instancecache = [];

    /**
     * Timestamps (microtime) when the modinfo instances were last accessed
     *
     * It is used to remove the least recent accessed instances when static cache is full
     *
     * @var float[]
     */
    protected static $cacheaccessed = [];

    /**
     * Store a list of known course cacherev values. This is in case people reuse a course object
     * (with an old cacherev value) within the same request when calling things like
     * get_fast_modinfo, after rebuild_course_cache.
     *
     * @var int[]
     */
    protected static $mincacherevs = [];

    /**
     * Clears the cache used in modinfo::instance()
     *
     * Used in {@see get_fast_modinfo()} when called with argument $reset = true
     * and in {@see rebuild_course_cache()}
     *
     * If the cacherev for the course is known to have updated (i.e. when doing
     * rebuild_course_cache), it should be specified here.
     *
     * @param null|int|stdClass $courseorid if specified removes only cached value for this course
     * @param int $newcacherev If specified, the known cache rev for this course id will be updated
     */
    public static function clear_instance_cache($courseorid = null, int $newcacherev = 0) {
        if (empty($courseorid)) {
            self::$instancecache = [];
            self::$cacheaccessed = [];
            // This is called e.g. in phpunit when we just want to reset the caches, so also
            // reset the mincacherevs static cache.
            self::$mincacherevs = [];
            return;
        }
        if (is_object($courseorid)) {
            $courseorid = $courseorid->id;
        }
        if (isset(self::$instancecache[$courseorid])) {
            // Unsetting static variable in PHP is peculiar, it removes the reference,
            // but data remain in memory. Prior to unsetting, the varable needs to be
            // set to empty to remove its remains from memory.
            self::$instancecache[$courseorid] = '';
            unset(self::$instancecache[$courseorid]);
            unset(self::$cacheaccessed[$courseorid]);
        }
        // When clearing cache for a course, we record the new cacherev version, to make
        // sure that any future requests for the cache use at least this version.
        if ($newcacherev) {
            self::$mincacherevs[(int)$courseorid] = $newcacherev;
        }
    }

    /**
     * Returns the instance of modinfo for the specified course and specified user
     *
     * This function uses static cache for the retrieved instances. The cache
     * size is limited by self::MAX_CACHE_SIZE. If instance is not found in
     * the static cache or it was created for another user or the cacherev validation
     * failed - a new instance is constructed and returned.
     *
     * Used in {@see get_fast_modinfo()}
     *
     * @param int|stdClass $courseorid object from DB table 'course' (must have field 'id'
     *     and recommended to have field 'cacherev') or just a course id
     * @param int $userid User id to populate 'availble' and 'uservisible' attributes of modules and sections.
     *     Set to 0 for current user (default). Set to -1 to avoid calculation of dynamic user-depended data.
     * @return modinfo
     */
    public static function instance($courseorid, $userid = 0) {
        global $USER;
        if (is_object($courseorid)) {
            $course = $courseorid;
        } else {
            $course = (object)['id' => $courseorid];
        }
        if (empty($userid)) {
            $userid = $USER->id;
        }

        if (!empty(self::$instancecache[$course->id])) {
            if (
                self::$instancecache[$course->id]->userid == $userid
                && (
                    !isset($course->cacherev)
                    || $course->cacherev == self::$instancecache[$course->id]->get_course()->cacherev
                )
            ) {
                // This course's modinfo for the same user was recently retrieved, return cached.
                self::$cacheaccessed[$course->id] = microtime(true);
                return self::$instancecache[$course->id];
            } else {
                // Prevent potential reference problems when switching users.
                self::clear_instance_cache($course->id);
            }
        }
        $modinfo = new static($course, $userid);

        // We have a limit of self::MAX_CACHE_SIZE entries to store in static variable.
        if (count(self::$instancecache) >= self::MAX_CACHE_SIZE) {
            // Find the course that was the least recently accessed.
            asort(self::$cacheaccessed, SORT_NUMERIC);
            $courseidtoremove = key(array_reverse(self::$cacheaccessed, true));
            self::clear_instance_cache($courseidtoremove);
        }

        // Add modinfo to the static cache.
        self::$instancecache[$course->id] = $modinfo;
        self::$cacheaccessed[$course->id] = microtime(true);

        return $modinfo;
    }

    /**
     * Constructs based on course.
     * Note: This constructor should not usually be called directly.
     * Use get_fast_modinfo($course) instead as this maintains a cache.
     * @param stdClass $course course object, only property id is required.
     * @param int $userid User ID
     * @throws moodle_exception if course is not found
     */
    public function __construct($course, $userid) {
        global $CFG, $COURSE, $SITE, $DB;

        if (!isset($course->cacherev)) {
            // We require presence of property cacherev to validate the course cache.
            // No need to clone the $COURSE or $SITE object here because we clone it below anyway.
            $course = get_course($course->id, false);
        }

        // If we have rebuilt the course cache in this request, ensure that requested cacherev is
        // at least that value. This ensures that we're not reusing a course object with old
        // cacherev, which could result in using old cached data.
        if (
            array_key_exists($course->id, self::$mincacherevs)
            && $course->cacherev < self::$mincacherevs[$course->id]
        ) {
            $course->cacherev = self::$mincacherevs[$course->id];
        }

        $cachecoursemodinfo = cache::make('core', 'coursemodinfo');

        // Retrieve modinfo from cache. If not present or cacherev mismatches, call rebuild and retrieve again.
        $coursemodinfo = $cachecoursemodinfo->get_versioned($course->id, $course->cacherev);
        // Note the version comparison using the data in the cache should not be necessary, but the
        // partial rebuild logic sometimes sets the $coursemodinfo->cacherev to -1 which is an
        // indicator that it needs rebuilding.
        if ($coursemodinfo === false || ($course->cacherev > $coursemodinfo->cacherev)) {
            $coursemodinfo = self::build_course_cache($course);
        }

        // Set initial values.
        $this->userid = $userid;
        $this->sectionmodules = [];
        $this->cms = [];
        $this->instances = [];
        $this->groups = null;

        // If we haven't already preloaded contexts for the course, do it now
        // Modules are also cached here as long as it's the first time this course has been preloaded.
        context_helper::preload_course($course->id);

        // Quick integrity check: as a result of race conditions modinfo may not be regenerated after the change.
        // It is especially dangerous if modinfo contains the deleted course module, as it results in fatal error.
        // We can check it very cheap by validating the existence of module context.
        if ($course->id == $COURSE->id || $course->id == $SITE->id) {
            // Only verify current course (or frontpage) as pages with many courses may not have module contexts cached.
            // (Uncached modules will result in a very slow verification).
            foreach ($coursemodinfo->modinfo as $mod) {
                if (!context_module::instance($mod->cm, IGNORE_MISSING)) {
                    debugging(
                        'Course cache integrity check failed: course module with id ' . $mod->cm .
                        ' does not have context. Rebuilding cache for course ' . $course->id
                    );
                    // Re-request the course record from DB as well, don't use get_course() here.
                    $course = $DB->get_record('course', ['id' => $course->id], '*', MUST_EXIST);
                    $coursemodinfo = self::build_course_cache($course, true);
                    break;
                }
            }
        }

        // Overwrite unset fields in $course object with cached values, store the course object.
        $this->course = fullclone($course);
        foreach ($coursemodinfo as $key => $value) {
            if (
                $key !== 'modinfo'
                && $key !== 'sectioncache'
                && (!isset($this->course->$key) || $key === 'cacherev')
            ) {
                $this->course->$key = $value;
            }
        }

        // Loop through each piece of module data, constructing it.
        static $modexists = [];
        foreach ($coursemodinfo->modinfo as $mod) {
            if (!isset($mod->name) || strval($mod->name) === '') {
                // Something is wrong here.
                continue;
            }

            // Skip modules which don't exist.
            if (!array_key_exists($mod->mod, $modexists)) {
                $modexists[$mod->mod] = file_exists("$CFG->dirroot/mod/$mod->mod/lib.php");
            }
            if (!$modexists[$mod->mod]) {
                continue;
            }

            // Construct info for this module.
            $cm = new cm_info($this, null, $mod, null);

            // Store module in instances and cms array.
            if (!isset($this->instances[$cm->modname])) {
                $this->instances[$cm->modname] = [];
            }
            $this->instances[$cm->modname][$cm->instance] = $cm;
            $this->cms[$cm->id] = $cm;

            // Reconstruct sections. This works because modules are stored in order.
            if (!isset($this->sectionmodules[$cm->sectionnum])) {
                $this->sectionmodules[$cm->sectionnum] = [];
            }
            $this->sectionmodules[$cm->sectionnum][] = $cm->id;
        }

        // Expand section objects.
        $this->sectioninfobynum = [];
        $this->sectioninfobyid = [];
        $this->delegatedsections = [];
        foreach ($coursemodinfo->sectioncache as $data) {
            $sectioninfo = new section_info(
                $data,
                $data->section,
                null,
                null,
                $this,
                null,
            );
            $this->sectioninfobynum[$data->section] = $sectioninfo;
            $this->sectioninfobyid[$data->id] = $sectioninfo;
            if (!empty($sectioninfo->component)) {
                if (!isset($this->delegatedsections[$sectioninfo->component])) {
                    $this->delegatedsections[$sectioninfo->component] = [];
                }
                $this->delegatedsections[$sectioninfo->component][$sectioninfo->itemid] = $sectioninfo;
            }
        }
        ksort($this->sectioninfobynum);
    }

    /**
     * Builds a list of information about sections on a course to be stored in
     * the course cache. (Does not include information that is already cached
     * in some other way.)
     *
     * @param stdClass $course Course object (must contain fields id and cacherev)
     * @param boolean $usecache use cached section info if exists, use true for partial course rebuild
     * @return array Information about sections, indexed by section id (not number)
     */
    protected static function build_course_section_cache(stdClass $course, bool $usecache = false): array {
        global $DB;

        // Get section data.
        $sections = $DB->get_records(
            'course_sections',
            ['course' => $course->id],
            'section',
            'id, section, course, name, summary, summaryformat, sequence, visible, availability, component, itemid',
        );
        $compressedsections = [];
        $courseformat = course_get_format($course);

        if ($usecache) {
            $cachecoursemodinfo = cache::make('core', 'coursemodinfo');
            $coursemodinfo = $cachecoursemodinfo->get_versioned($course->id, $course->cacherev);
            if ($coursemodinfo !== false) {
                $compressedsections = $coursemodinfo->sectioncache;
            }
        }

        $formatoptionsdef = course_get_format($course)->section_format_options();
        // Remove unnecessary data and add availability.
        foreach ($sections as $section) {
            $sectionid = $section->id;
            $sectioninfocached = isset($compressedsections[$sectionid]);
            if ($sectioninfocached) {
                continue;
            }
            // Add cached options from course format to $section object.
            foreach ($formatoptionsdef as $key => $option) {
                if (!empty($option['cache'])) {
                    $formatoptions = $courseformat->get_format_options($section);
                    if (!array_key_exists('cachedefault', $option) || $option['cachedefault'] !== $formatoptions[$key]) {
                        $section->$key = $formatoptions[$key];
                    }
                }
            }
            // Clone just in case it is reused elsewhere.
            $compressedsections[$sectionid] = clone($section);
            section_info::convert_for_section_cache($compressedsections[$sectionid]);
        }
        return $compressedsections;
    }

    /**
     * Builds and stores in MUC object containing information about course
     * modules and sections together with cached fields from table course.
     *
     * @param stdClass $course object from DB table course. Must have property 'id'
     *     but preferably should have all cached fields.
     * @param boolean $partialrebuild Indicate if it's partial course cache rebuild or not
     * @return stdClass object with all cached keys of the course plus fields modinfo and sectioncache.
     *     The same object is stored in MUC
     * @throws moodle_exception if course is not found (if $course object misses some of the
     *     necessary fields it is re-requested from database)
     */
    public static function build_course_cache(stdClass $course, bool $partialrebuild = false): stdClass {
        if (empty($course->id)) {
            throw new coding_exception('Object $course is missing required property \id\'');
        }

        $cachecoursemodinfo = cache::make('core', 'coursemodinfo');
        $cachekey = $course->id;
        $cachecoursemodinfo->acquire_lock($cachekey);
        try {
            // Only actually do the build if it's still needed after getting the lock (not if
            // somebody else, who might have been holding the lock, built it already).
            $coursemodinfo = $cachecoursemodinfo->get_versioned($course->id, $course->cacherev);
            if ($coursemodinfo === false || ($course->cacherev > $coursemodinfo->cacherev)) {
                $coursemodinfo = self::inner_build_course_cache($course);
            }
        } finally {
            $cachecoursemodinfo->release_lock($cachekey);
        }
        return $coursemodinfo;
    }

    /**
     * Called to build course cache when there is already a lock obtained.
     *
     * @param stdClass $course object from DB table course
     * @param bool $partialrebuild Indicate if it's partial course cache rebuild or not
     * @return stdClass Course object that has been stored in MUC
     */
    protected static function inner_build_course_cache(stdClass $course, bool $partialrebuild = false): stdClass {
        global $DB, $CFG;
        require_once("{$CFG->dirroot}/course/lib.php");

        $cachekey = $course->id;
        $cachecoursemodinfo = cache::make('core', 'coursemodinfo');
        if (!$cachecoursemodinfo->check_lock_state($cachekey)) {
            throw new coding_exception('You must acquire a lock on the course ID before calling inner_build_course_cache');
        }

        // Always reload the course object from database to ensure we have the latest possible
        // value for cacherev.
        $course = $DB->get_record(
            'course',
            ['id' => $course->id],
            implode(',', array_merge(['id'], self::$cachedfields)),
            MUST_EXIST,
        );
        // Retrieve all information about activities and sections.
        $coursemodinfo = new stdClass();
        $coursemodinfo->modinfo = self::get_array_of_activities($course, $partialrebuild);
        $coursemodinfo->sectioncache = self::build_course_section_cache($course, $partialrebuild);
        foreach (self::$cachedfields as $key) {
            $coursemodinfo->$key = $course->$key;
        }
        // Set the accumulated activities and sections information in cache, together with cacherev.
        $cachecoursemodinfo->set_versioned($cachekey, $course->cacherev, $coursemodinfo);
        return $coursemodinfo;
    }

    /**
     * Purge the cache of a course section by its id.
     *
     * @param int $courseid The course to purge cache in
     * @param int $sectionid The section _id_ to purge
     */
    public static function purge_course_section_cache_by_id(int $courseid, int $sectionid): void {
        $course = get_course($courseid);
        $cache = cache::make('core', 'coursemodinfo');
        $cachekey = $course->id;
        $cache->acquire_lock($cachekey);
        try {
            $coursemodinfo = $cache->get_versioned($cachekey, $course->cacherev);
            if ($coursemodinfo !== false && array_key_exists($sectionid, $coursemodinfo->sectioncache)) {
                $coursemodinfo->cacherev = -1;
                unset($coursemodinfo->sectioncache[$sectionid]);
                $cache->set_versioned($cachekey, $course->cacherev, $coursemodinfo);
            }
        } finally {
            $cache->release_lock($cachekey);
        }
    }

    /**
     * Purge the cache of a course section by its number.
     *
     * @param int $courseid The course to purge cache in
     * @param int $sectionno The section number to purge
     */
    public static function purge_course_section_cache_by_number(int $courseid, int $sectionno): void {
        $course = get_course($courseid);
        $cache = cache::make('core', 'coursemodinfo');
        $cachekey = $course->id;
        $cache->acquire_lock($cachekey);
        try {
            $coursemodinfo = $cache->get_versioned($cachekey, $course->cacherev);
            if ($coursemodinfo !== false) {
                foreach ($coursemodinfo->sectioncache as $sectionid => $sectioncache) {
                    if ($sectioncache->section == $sectionno) {
                        $coursemodinfo->cacherev = -1;
                        unset($coursemodinfo->sectioncache[$sectionid]);
                        $cache->set_versioned($cachekey, $course->cacherev, $coursemodinfo);
                        break;
                    }
                }
            }
        } finally {
            $cache->release_lock($cachekey);
        }
    }

    /**
     * Purge the cache of a course module.
     *
     * @param int $courseid Course id
     * @param int $cmid Course module id
     */
    public static function purge_course_module_cache(int $courseid, int $cmid): void {
        self::purge_course_modules_cache($courseid, [$cmid]);
    }

    /**
     * Purges the coursemodinfo caches stored in MUC.
     *
     * @param int[] $courseids Array of course ids to purge the course caches
     * for (or all courses if empty array).
     *
     */
    public static function purge_course_caches(array $courseids = []): void {
        global $DB;

        // Purging might purge all course caches, so use a recordset and close it.
        $select = '';
        $params = null;
        if (!empty($courseids)) {
            [$sql, $params] = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED);
            $select = 'id ' . $sql;
        }

        $courses = $DB->get_recordset_select(
            table: 'course',
            select: $select,
            params: $params,
            fields: 'id',
        );

        // Purge each course's cache to make sure cache is recalculated next time
        // the course is viewed.
        foreach ($courses as $course) {
            self::purge_course_cache($course->id);
        }
        $courses->close();
    }

    /**
     * Purge the cache of multiple course modules.
     *
     * @param int $courseid Course id
     * @param int[] $cmids List of course module ids
     * @return void
     */
    public static function purge_course_modules_cache(int $courseid, array $cmids): void {
        $course = get_course($courseid);
        $cache = cache::make('core', 'coursemodinfo');
        $cachekey = $course->id;
        $cache->acquire_lock($cachekey);
        try {
            $coursemodinfo = $cache->get_versioned($cachekey, $course->cacherev);
            $hascache = ($coursemodinfo !== false);
            $updatedcache = false;
            if ($hascache) {
                foreach ($cmids as $cmid) {
                    if (array_key_exists($cmid, $coursemodinfo->modinfo)) {
                        unset($coursemodinfo->modinfo[$cmid]);
                        $updatedcache = true;
                    }
                }
                if ($updatedcache) {
                    $coursemodinfo->cacherev = -1;
                    $cache->set_versioned($cachekey, $course->cacherev, $coursemodinfo);
                    $cache->get_versioned($cachekey, $course->cacherev);
                }
            }
        } finally {
            $cache->release_lock($cachekey);
        }
    }

    /**
     * For a given course, returns an array of course activity objects
     *
     * @param stdClass $course Course object
     * @param bool $usecache get activities from cache if modinfo exists when $usecache is true
     * @return array list of activities
     */
    public static function get_array_of_activities(stdClass $course, bool $usecache = false): array {
        global $CFG, $DB;

        if (empty($course)) {
            throw new moodle_exception('courseidnotfound');
        }

        $rawmods = get_course_mods($course->id);
        if (empty($rawmods)) {
            return [];
        }

        $mods = [];
        if ($usecache) {
            // Get existing cache.
            $cachecoursemodinfo = cache::make('core', 'coursemodinfo');
            $coursemodinfo = $cachecoursemodinfo->get_versioned($course->id, $course->cacherev);
            if ($coursemodinfo !== false) {
                $mods = $coursemodinfo->modinfo;
            }
        }

        $courseformat = course_get_format($course);

        if (
            $sections = $DB->get_records(
                'course_sections',
                ['course' => $course->id],
                'section ASC',
                'id,section,sequence,visible',
            )
        ) {
            // First check and correct obvious mismatches between course_sections.sequence and course_modules.section.
            if ($errormessages = course_integrity_check($course->id, $rawmods, $sections)) {
                debugging(join('<br>', $errormessages));
                $rawmods = get_course_mods($course->id);
                $sections = $DB->get_records(
                    'course_sections',
                    ['course' => $course->id],
                    'section ASC',
                    'id,section,sequence,visible',
                );
            }
            // Build array of activities.
            foreach ($sections as $section) {
                if (!empty($section->sequence)) {
                    $cmids = explode(",", $section->sequence);
                    $numberofmods = count($cmids);
                    foreach ($cmids as $cmid) {
                        // Activity does not exist in the database.
                        $notexistindb = empty($rawmods[$cmid]);
                        $activitycached = isset($mods[$cmid]);
                        if ($activitycached || $notexistindb) {
                            continue;
                        }

                        // Adjust visibleoncoursepage, value in DB may not respect format availability.
                        $rawmods[$cmid]->visibleoncoursepage = (!$rawmods[$cmid]->visible
                            || $rawmods[$cmid]->visibleoncoursepage
                            || empty($CFG->allowstealth)
                            || !$courseformat->allow_stealth_module_visibility($rawmods[$cmid], $section)) ? 1 : 0;

                        $mods[$cmid] = new stdClass();
                        $mods[$cmid]->id = $rawmods[$cmid]->instance;
                        $mods[$cmid]->cm = $rawmods[$cmid]->id;
                        $mods[$cmid]->mod = $rawmods[$cmid]->modname;

                        // Oh dear. Inconsistent names left 'section' here for backward compatibility,
                        // but also save sectionid and sectionnumber.
                        $mods[$cmid]->section = $section->section;
                        $mods[$cmid]->sectionnumber = $section->section;
                        $mods[$cmid]->sectionid = $rawmods[$cmid]->section;

                        $mods[$cmid]->module = $rawmods[$cmid]->module;
                        $mods[$cmid]->added = $rawmods[$cmid]->added;
                        $mods[$cmid]->score = $rawmods[$cmid]->score;
                        $mods[$cmid]->idnumber = $rawmods[$cmid]->idnumber;
                        $mods[$cmid]->visible = $rawmods[$cmid]->visible;
                        $mods[$cmid]->visibleoncoursepage = $rawmods[$cmid]->visibleoncoursepage;
                        $mods[$cmid]->visibleold = $rawmods[$cmid]->visibleold;
                        $mods[$cmid]->groupmode = $rawmods[$cmid]->groupmode;
                        $mods[$cmid]->groupingid = $rawmods[$cmid]->groupingid;
                        $mods[$cmid]->indent = $rawmods[$cmid]->indent;
                        $mods[$cmid]->completion = $rawmods[$cmid]->completion;
                        $mods[$cmid]->extra = "";
                        $mods[$cmid]->completiongradeitemnumber =
                            $rawmods[$cmid]->completiongradeitemnumber;
                        $mods[$cmid]->completionpassgrade = $rawmods[$cmid]->completionpassgrade;
                        $mods[$cmid]->completionview = $rawmods[$cmid]->completionview;
                        $mods[$cmid]->completionexpected = $rawmods[$cmid]->completionexpected;
                        $mods[$cmid]->showdescription = $rawmods[$cmid]->showdescription;
                        $mods[$cmid]->availability = $rawmods[$cmid]->availability;
                        $mods[$cmid]->deletioninprogress = $rawmods[$cmid]->deletioninprogress;
                        $mods[$cmid]->downloadcontent = $rawmods[$cmid]->downloadcontent;
                        $mods[$cmid]->lang = $rawmods[$cmid]->lang;
                        $mods[$cmid]->enableaitools = $rawmods[$cmid]->enableaitools;
                        $mods[$cmid]->enabledaiactions = $rawmods[$cmid]->enabledaiactions;

                        $modname = $mods[$cmid]->mod;
                        $functionname = $modname . "_get_coursemodule_info";

                        if (!file_exists("$CFG->dirroot/mod/$modname/lib.php")) {
                            continue;
                        }

                        include_once("$CFG->dirroot/mod/$modname/lib.php");

                        if ($hasfunction = function_exists($functionname)) {
                            if ($info = $functionname($rawmods[$cmid])) {
                                if (!empty($info->icon)) {
                                    $mods[$cmid]->icon = $info->icon;
                                }
                                if (!empty($info->iconcomponent)) {
                                    $mods[$cmid]->iconcomponent = $info->iconcomponent;
                                }
                                if (!empty($info->name)) {
                                    $mods[$cmid]->name = $info->name;
                                }
                                if ($info instanceof cached_cm_info) {
                                    // When using cached_cm_info you can include three new fields.
                                    // That aren't available for legacy code.
                                    if (!empty($info->content)) {
                                        $mods[$cmid]->content = $info->content;
                                    }
                                    if (!empty($info->extraclasses)) {
                                        $mods[$cmid]->extraclasses = $info->extraclasses;
                                    }
                                    if (!empty($info->iconurl)) {
                                        // Convert URL to string as it's easier to store.
                                        // Also serialized object contains \0 byte,
                                        // ... and can not be written to Postgres DB.
                                        $url = new url($info->iconurl);
                                        $mods[$cmid]->iconurl = $url->out(false);
                                    }
                                    if (!empty($info->onclick)) {
                                        $mods[$cmid]->onclick = $info->onclick;
                                    }
                                    if (!empty($info->customdata)) {
                                        $mods[$cmid]->customdata = $info->customdata;
                                    }
                                } else {
                                    // When using a stdclass, the (horrible) deprecated ->extra field,
                                    // ... that is available for BC.
                                    if (!empty($info->extra)) {
                                        $mods[$cmid]->extra = $info->extra;
                                    }
                                }
                            }
                        }
                        // When there is no modname_get_coursemodule_info function,
                        // ... but showdescriptions is enabled, then we use the 'intro',
                        // ... and 'introformat' fields in the module table.
                        if (!$hasfunction && $rawmods[$cmid]->showdescription) {
                            if (
                                $modvalues = $DB->get_record(
                                    $rawmods[$cmid]->modname,
                                    ['id' => $rawmods[$cmid]->instance],
                                    'name, intro, introformat',
                                )
                            ) {
                                // Set content from intro and introformat. Filters are disabled.
                                // Because we filter it with format_text at display time.
                                $mods[$cmid]->content = format_module_intro(
                                    $rawmods[$cmid]->modname,
                                    $modvalues,
                                    $rawmods[$cmid]->id,
                                    false,
                                );

                                // To save making another query just below, put name in here.
                                $mods[$cmid]->name = $modvalues->name;
                            }
                        }
                        if (!isset($mods[$cmid]->name)) {
                            $mods[$cmid]->name = $DB->get_field(
                                $rawmods[$cmid]->modname,
                                "name",
                                ["id" => $rawmods[$cmid]->instance],
                            );
                        }

                        // Minimise the database size by unsetting default options when they are 'empty'.
                        // This list corresponds to code in the cm_info constructor.
                        foreach (
                            ['idnumber', 'groupmode', 'groupingid',
                            'indent', 'completion', 'extra', 'extraclasses', 'iconurl', 'onclick', 'content',
                            'icon', 'iconcomponent', 'customdata', 'availability', 'completionview',
                            'completionexpected', 'score', 'showdescription', 'deletioninprogress'] as $property
                        ) {
                            if (
                                property_exists($mods[$cmid], $property)
                                && empty($mods[$cmid]->{$property})
                            ) {
                                unset($mods[$cmid]->{$property});
                            }
                        }
                        // Special case: this value is usually set to null, but may be 0.
                        if (
                            property_exists($mods[$cmid], 'completiongradeitemnumber')
                            && is_null($mods[$cmid]->completiongradeitemnumber)
                        ) {
                            unset($mods[$cmid]->completiongradeitemnumber);
                        }
                    }
                }
            }
        }
        return $mods;
    }

    /**
     * Purge the cache of a given course
     *
     * @param int $courseid Course id
     */
    public static function purge_course_cache(int $courseid): void {
        increment_revision_number('course', 'cacherev', 'id = :id', ['id' => $courseid]);
        // Because this is a versioned cache, there is no need to actually delete the cache item,
        // only increase the required version number.
    }

    /**
     * Can this module type be displayed on a course page or selected from the activity types when adding an activity to a course?
     *
     * @param string $modname The module type name
     * @return bool
     */
    public static function is_mod_type_visible_on_course(string $modname): bool {
        return plugin_supports('mod', $modname, FEATURE_CAN_DISPLAY, true);
    }

    /**
     * Get content weights for all sections and modules in the course.
     *
     * The weights are calculated based on the order of sections and modules
     * as they appear on the course page, including delegated sections.
     *
     * @return array Associative array with keys 'section{sectionid}' and 'cm{cmid}' and integer weights as values.
     */
    private function get_content_weights(): array {
        if ($this->weights !== null) {
            return $this->weights;
        }
        $result = [];
        foreach ($this->sectioninfobynum as $section) {
            // Delegated sections are always at the end of the course and they will
            // be added only if they are part of any section sequence.
            if ($section->is_delegated()) {
                continue;
            }
            $sortedelements = $this->calculate_section_weights($section, count($result));
            $result += $sortedelements;
        }
        $this->weights = $result;
        return $result;
    }

    /**
     * Calculate weights for a section and its modules, including delegated sections.
     *
     * @param section_info $section The section to calculate weights for.
     * @param int $currentweight The starting weight to use for this section.
     * @return section_info[] Associative array of section_info objects, indexed by the cmid of the delegating module.
     */
    private function calculate_section_weights(section_info $section, int $currentweight = 0): array {
        $delegatedcms = $this->get_sections_delegated_by_cm();

        $weights = [
            'section' . $section->id => $currentweight++,
        ];

        foreach ($section->get_sequence_cm_infos() as $cm) {
            $weights['cm' . $cm->id] = $currentweight++;

            if (array_key_exists($cm->id, $delegatedcms)) {
                $subweights = $this->calculate_section_weights($delegatedcms[$cm->id], $currentweight);
                $weights += $subweights;
                $currentweight += count($subweights);
            }
        }
        return $weights;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(modinfo::class, \course_modinfo::class);
