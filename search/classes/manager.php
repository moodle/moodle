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
 * Search subsystem manager.
 *
 * @package   core_search
 * @copyright Prateek Sachan {@link http://prateeksachan.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_search;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/lib/accesslib.php');

/**
 * Search subsystem manager.
 *
 * @package   core_search
 * @copyright Prateek Sachan {@link http://prateeksachan.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manager {

    /**
     * @var int Text contents.
     */
    const TYPE_TEXT = 1;

    /**
     * @var int File contents.
     */
    const TYPE_FILE = 2;

    /**
     * @var int User can not access the document.
     */
    const ACCESS_DENIED = 0;

    /**
     * @var int User can access the document.
     */
    const ACCESS_GRANTED = 1;

    /**
     * @var int The document was deleted.
     */
    const ACCESS_DELETED = 2;

    /**
     * @var int Maximum number of results that will be retrieved from the search engine.
     */
    const MAX_RESULTS = 100;

    /**
     * @var int Number of results per page.
     */
    const DISPLAY_RESULTS_PER_PAGE = 10;

    /**
     * @var int The id to be placed in owneruserid when there is no owner.
     */
    const NO_OWNER_ID = 0;

    /**
     * @var float If initial query takes longer than N seconds, this will be shown in cron log.
     */
    const DISPLAY_LONG_QUERY_TIME = 5.0;

    /**
     * @var float Adds indexing progress within one search area to cron log every N seconds.
     */
    const DISPLAY_INDEXING_PROGRESS_EVERY = 30.0;

    /**
     * @var int Context indexing: normal priority.
     */
    const INDEX_PRIORITY_NORMAL = 100;

    /**
     * @var int Context indexing: low priority for reindexing.
     */
    const INDEX_PRIORITY_REINDEXING = 50;

    /**
     * @var string Core search area category for all results.
     */
    const SEARCH_AREA_CATEGORY_ALL = 'core-all';

    /**
     * @var string Core search area category for course content.
     */
    const SEARCH_AREA_CATEGORY_COURSE_CONTENT = 'core-course-content';

    /**
     * @var string Core search area category for courses.
     */
    const SEARCH_AREA_CATEGORY_COURSES = 'core-courses';

    /**
     * @var string Core search area category for users.
     */
    const SEARCH_AREA_CATEGORY_USERS = 'core-users';

    /**
     * @var string Core search area category for results that do not fit into any of existing categories.
     */
    const SEARCH_AREA_CATEGORY_OTHER = 'core-other';

    /**
     * @var \core_search\base[] Enabled search areas.
     */
    protected static $enabledsearchareas = null;

    /**
     * @var \core_search\base[] All system search areas.
     */
    protected static $allsearchareas = null;

    /**
     * @var \core_search\area_category[] A list of search area categories.
     */
    protected static $searchareacategories = null;

    /**
     * @var \core_search\manager
     */
    protected static $instance = null;

    /**
     * @var array IDs (as keys) of course deletions in progress in this requuest, if any.
     */
    protected static $coursedeleting = [];

    /**
     * @var \core_search\engine
     */
    protected $engine = null;

    /**
     * Note: This should be removed once possible (see MDL-60644).
     *
     * @var float Fake current time for use in PHPunit tests
     */
    protected static $phpunitfaketime = 0;

    /**
     * @var int Result count when used with mock results for Behat tests.
     */
    protected $behatresultcount = 0;

    /**
     * Constructor, use \core_search\manager::instance instead to get a class instance.
     *
     * @param \core_search\base The search engine to use
     */
    public function __construct($engine) {
        $this->engine = $engine;
    }

    /**
     * @var int Record time of each successful schema check, but not more than once per 10 minutes.
     */
    const SCHEMA_CHECK_TRACKING_DELAY = 10 * 60;

    /**
     * @var int Require a new schema check at least every 4 hours.
     */
    const SCHEMA_CHECK_REQUIRED_EVERY = 4 * 3600;

    /**
     * Returns an initialised \core_search instance.
     *
     * While constructing the instance, checks on the search schema may be carried out. The $fast
     * parameter provides a way to skip those checks on pages which are used frequently. It has
     * no effect if an instance has already been constructed in this request.
     *
     * The $query parameter indicates that the page is used for queries rather than indexing. If
     * configured, this will cause the query-only search engine to be used instead of the 'normal'
     * one.
     *
     * @see \core_search\engine::is_installed
     * @see \core_search\engine::is_server_ready
     * @param bool $fast Set to true when calling on a page that requires high performance
     * @param bool $query Set true on a page that is used for querying
     * @throws \core_search\engine_exception
     * @return \core_search\manager
     */
    public static function instance(bool $fast = false, bool $query = false) {
        global $CFG;

        // One per request, this should be purged during testing.
        if (static::$instance !== null) {
            return static::$instance;
        }

        if (empty($CFG->searchengine)) {
            throw new \core_search\engine_exception('enginenotselected', 'search');
        }

        if (!$engine = static::search_engine_instance($query)) {
            throw new \core_search\engine_exception('enginenotfound', 'search', '', $CFG->searchengine);
        }

        // Get time now and at last schema check.
        $now = (int)self::get_current_time();
        $lastschemacheck = get_config($engine->get_plugin_name(), 'lastschemacheck');

        // On pages where performance matters, tell the engine to skip schema checks.
        $skipcheck = false;
        if ($fast && $now < $lastschemacheck + self::SCHEMA_CHECK_REQUIRED_EVERY) {
            $skipcheck = true;
            $engine->skip_schema_check();
        }

        if (!$engine->is_installed()) {
            throw new \core_search\engine_exception('enginenotinstalled', 'search', '', $CFG->searchengine);
        }

        $serverstatus = $engine->is_server_ready();
        if ($serverstatus !== true) {
            // Skip this error in Behat when faking seach results.
            if (!defined('BEHAT_SITE_RUNNING') || !get_config('core_search', 'behat_fakeresult')) {
                // Clear the record of successful schema checks since it might have failed.
                unset_config('lastschemacheck', $engine->get_plugin_name());
                // Error message with no details as this is an exception that any user may find if the server crashes.
                throw new \core_search\engine_exception('engineserverstatus', 'search');
            }
        }

        // If we did a successful schema check, record this, but not more than once per 10 minutes
        // (to avoid updating the config db table/cache too often in case it gets called frequently).
        if (!$skipcheck && $now >= $lastschemacheck + self::SCHEMA_CHECK_TRACKING_DELAY) {
            set_config('lastschemacheck', $now, $engine->get_plugin_name());
        }

        static::$instance = new \core_search\manager($engine);
        return static::$instance;
    }

    /**
     * Returns whether global search is enabled or not.
     *
     * @return bool
     */
    public static function is_global_search_enabled() {
        global $CFG;
        return !empty($CFG->enableglobalsearch);
    }

    /**
     * Tests if global search is configured to be equivalent to the front page course search.
     *
     * @return bool
     */
    public static function can_replace_course_search(): bool {
        global $CFG;

        // Assume we can replace front page search.
        $canreplace = true;

        // Global search must be enabled.
        if (!static::is_global_search_enabled()) {
            $canreplace = false;
        }

        // Users must be able to search the details of all courses that they can see,
        // even if they do not have access to them.
        if (empty($CFG->searchincludeallcourses)) {
            $canreplace = false;
        }

        // Course search must be enabled.
        if ($canreplace) {
            $areaid = static::generate_areaid('core_course', 'course');
            $enabledareas = static::get_search_areas_list(true);
            $canreplace = isset($enabledareas[$areaid]);
        }

        return $canreplace;
    }

    /**
     * Returns the search URL for course search
     *
     * @return moodle_url
     */
    public static function get_course_search_url() {
        if (self::can_replace_course_search()) {
            $searchurl = '/search/index.php';
        } else {
            $searchurl = '/course/search.php';
        }

        return new \moodle_url($searchurl);
    }

    /**
     * Returns whether indexing is enabled or not (you can enable indexing even when search is not
     * enabled at the moment, so as to have it ready for students).
     *
     * @return bool True if indexing is enabled.
     */
    public static function is_indexing_enabled() {
        global $CFG;
        return !empty($CFG->enableglobalsearch) || !empty($CFG->searchindexwhendisabled);
    }

    /**
     * Returns an instance of the search engine.
     *
     * @param bool $query If true, gets the query-only search engine (where configured)
     * @return \core_search\engine
     */
    public static function search_engine_instance(bool $query = false) {
        global $CFG;

        if ($query && $CFG->searchenginequeryonly) {
            return self::search_engine_instance_from_setting($CFG->searchenginequeryonly);
        } else {
            return self::search_engine_instance_from_setting($CFG->searchengine);
        }
    }

    /**
     * Loads a search engine based on the name given in settings, which can optionally
     * include '-alternate' to indicate that an alternate version should be used.
     *
     * @param string $setting
     * @return engine|null
     */
    protected static function search_engine_instance_from_setting(string $setting): ?engine {
        if (preg_match('~^(.*)-alternate$~', $setting, $matches)) {
            $enginename = $matches[1];
            $alternate = true;
        } else {
            $enginename = $setting;
            $alternate = false;
        }

        $classname = '\\search_' . $enginename . '\\engine';
        if (!class_exists($classname)) {
            return null;
        }

        if ($alternate) {
            return new $classname(true);
        } else {
            // Use the constructor with no parameters for compatibility.
            return new $classname();
        }
    }

    /**
     * Returns the search engine.
     *
     * @return \core_search\engine
     */
    public function get_engine() {
        return $this->engine;
    }

    /**
     * Returns a search area class name.
     *
     * @param string $areaid
     * @return string
     */
    protected static function get_area_classname($areaid) {
        list($componentname, $areaname) = static::extract_areaid_parts($areaid);
        return '\\' . $componentname . '\\search\\' . $areaname;
    }

    /**
     * Returns a new area search indexer instance.
     *
     * @param string $areaid
     * @return \core_search\base|bool False if the area is not available.
     */
    public static function get_search_area($areaid) {

        // We have them all here.
        if (!empty(static::$allsearchareas[$areaid])) {
            return static::$allsearchareas[$areaid];
        }

        $classname = static::get_area_classname($areaid);

        if (class_exists($classname) && static::is_search_area($classname)) {
            return new $classname();
        }

        return false;
    }

    /**
     * Return the list of available search areas.
     *
     * @param bool $enabled Return only the enabled ones.
     * @return \core_search\base[]
     */
    public static function get_search_areas_list($enabled = false) {

        // Two different arrays, we don't expect these arrays to be big.
        if (static::$allsearchareas !== null) {
            if (!$enabled) {
                return static::$allsearchareas;
            } else {
                return static::$enabledsearchareas;
            }
        }

        static::$allsearchareas = array();
        static::$enabledsearchareas = array();
        $searchclasses = \core_component::get_component_classes_in_namespace(null, 'search');

        foreach ($searchclasses as $classname => $classpath) {
            $areaname = substr(strrchr($classname, '\\'), 1);
            $componentname = strstr($classname, '\\', 1);
            if (!static::is_search_area($classname)) {
                continue;
            }

            $areaid = static::generate_areaid($componentname, $areaname);
            $searchclass = new $classname();
            static::$allsearchareas[$areaid] = $searchclass;
            if ($searchclass->is_enabled()) {
                static::$enabledsearchareas[$areaid] = $searchclass;
            }
        }

        if ($enabled) {
            return static::$enabledsearchareas;
        }
        return static::$allsearchareas;
    }

    /**
     * Return search area category instance by category name.
     *
     * @param string $name Category name. If name is not valid will return default category.
     *
     * @return \core_search\area_category
     */
    public static function get_search_area_category_by_name($name) {
        if (key_exists($name, self::get_search_area_categories())) {
            return self::get_search_area_categories()[$name];
        } else {
            return self::get_search_area_categories()[self::get_default_area_category_name()];
        }
    }

    /**
     * Return a list of existing search area categories.
     *
     * @return \core_search\area_category[]
     */
    public static function get_search_area_categories() {
        if (!isset(static::$searchareacategories)) {
            $categories = self::get_core_search_area_categories();

            // Go through all existing search areas and get categories they are assigned to.
            $areacategories = [];
            foreach (self::get_search_areas_list() as $searcharea) {
                foreach ($searcharea->get_category_names() as $categoryname) {
                    if (!key_exists($categoryname, $areacategories)) {
                        $areacategories[$categoryname] = [];
                    }

                    $areacategories[$categoryname][] = $searcharea;
                }
            }

            // Populate core categories by areas.
            foreach ($areacategories as $name => $searchareas) {
                if (key_exists($name, $categories)) {
                    $categories[$name]->set_areas($searchareas);
                } else {
                    throw new \coding_exception('Unknown core search area category ' . $name);
                }
            }

            // Get additional categories.
            $additionalcategories = self::get_additional_search_area_categories();
            foreach ($additionalcategories as $additionalcategory) {
                if (!key_exists($additionalcategory->get_name(), $categories)) {
                    $categories[$additionalcategory->get_name()] = $additionalcategory;
                }
            }

            // Remove categories without areas.
            foreach ($categories as $key => $category) {
                if (empty($category->get_areas())) {
                    unset($categories[$key]);
                }
            }

            // Sort categories by order.
            uasort($categories, function($category1, $category2) {
                return $category1->get_order() <=> $category2->get_order();
            });

            static::$searchareacategories = $categories;
        }

        return static::$searchareacategories;
    }

    /**
     * Get list of core search area categories.
     *
     * @return \core_search\area_category[]
     */
    protected static function get_core_search_area_categories() {
        $categories = [];

        $categories[self::SEARCH_AREA_CATEGORY_ALL] = new area_category(
            self::SEARCH_AREA_CATEGORY_ALL,
            get_string('core-all', 'search'),
            0,
            self::get_search_areas_list(true)
        );

        $categories[self::SEARCH_AREA_CATEGORY_COURSE_CONTENT] = new area_category(
            self::SEARCH_AREA_CATEGORY_COURSE_CONTENT,
            get_string('core-course-content', 'search'),
            1
        );

        $categories[self::SEARCH_AREA_CATEGORY_COURSES] = new area_category(
            self::SEARCH_AREA_CATEGORY_COURSES,
            get_string('core-courses', 'search'),
            2
        );

        $categories[self::SEARCH_AREA_CATEGORY_USERS] = new area_category(
            self::SEARCH_AREA_CATEGORY_USERS,
            get_string('core-users', 'search'),
            3
        );

        $categories[self::SEARCH_AREA_CATEGORY_OTHER] = new area_category(
            self::SEARCH_AREA_CATEGORY_OTHER,
            get_string('core-other', 'search'),
            4
        );

        return $categories;
    }

    /**
     * Gets a list of additional search area categories.
     *
     * @return \core_search\area_category[]
     */
    protected static function get_additional_search_area_categories() {
        $additionalcategories = [];

        // Allow plugins to add custom search area categories.
        if ($pluginsfunction = get_plugins_with_function('search_area_categories')) {
            foreach ($pluginsfunction as $plugintype => $plugins) {
                foreach ($plugins as $pluginfunction) {
                    $plugincategories = $pluginfunction();
                    // We're expecting a list of valid area categories.
                    if (is_array($plugincategories)) {
                        foreach ($plugincategories as $plugincategory) {
                            if (self::is_valid_area_category($plugincategory)) {
                                $additionalcategories[] = $plugincategory;
                            } else {
                                throw  new \coding_exception('Invalid search area category!');
                            }
                        }
                    } else {
                        throw  new \coding_exception($pluginfunction . ' should return a list of search area categories!');
                    }
                }
            }
        }

        return $additionalcategories;
    }

    /**
     * Check if provided instance of area category is valid.
     *
     * @param mixed $areacategory Area category instance. Potentially could be anything.
     *
     * @return bool
     */
    protected static function is_valid_area_category($areacategory) {
        return $areacategory instanceof area_category;
    }

    /**
     * Clears all static caches.
     *
     * @return void
     */
    public static function clear_static() {

        static::$enabledsearchareas = null;
        static::$allsearchareas = null;
        static::$instance = null;
        static::$searchareacategories = null;
        static::$coursedeleting = [];
        static::$phpunitfaketime = null;

        base_block::clear_static();
        engine::clear_users_cache();
    }

    /**
     * Generates an area id from the componentname and the area name.
     *
     * There should not be any naming conflict as the area name is the
     * class name in component/classes/search/.
     *
     * @param string $componentname
     * @param string $areaname
     * @return void
     */
    public static function generate_areaid($componentname, $areaname) {
        return $componentname . '-' . $areaname;
    }

    /**
     * Returns all areaid string components (component name and area name).
     *
     * @param string $areaid
     * @return array Component name (Frankenstyle) and area name (search area class name)
     */
    public static function extract_areaid_parts($areaid) {
        return explode('-', $areaid);
    }

    /**
     * Parse a search area id and get plugin name and config name prefix from it.
     *
     * @param string $areaid Search area id.
     * @return array Where the first element is a plugin name and the second is config names prefix.
     */
    public static function parse_areaid($areaid) {
        $parts = self::extract_areaid_parts($areaid);

        if (empty($parts[1])) {
            throw new \coding_exception('Trying to parse invalid search area id ' . $areaid);
        }

        $component = $parts[0];
        $area = $parts[1];

        if (strpos($component, 'core') === 0) {
            $plugin = 'core_search';
            $configprefix = str_replace('-', '_', $areaid);
        } else {
            $plugin = $component;
            $configprefix = 'search_' . $area;
        }

        return [$plugin, $configprefix];
    }

    /**
     * Returns information about the areas which the user can access.
     *
     * The returned value is a stdClass object with the following fields:
     * - everything (bool, true for admin only)
     * - usercontexts (indexed by area identifier then context
     * - separategroupscontexts (contexts within which group restrictions apply)
     * - visiblegroupscontextsareas (overrides to the above when the same contexts also have
     *   'visible groups' for certain search area ids - hopefully rare)
     * - usergroups (groups which the current user belongs to)
     *
     * The areas can be limited by course id and context id. If specifying context ids, results
     * are limited to the exact context ids specified and not their children (for example, giving
     * the course context id would result in including search items with the course context id, and
     * not anything from a context inside the course). For performance, you should also specify
     * course id(s) when using context ids.
     *
     * @param array|false $limitcourseids An array of course ids to limit the search to. False for no limiting.
     * @param array|false $limitcontextids An array of context ids to limit the search to. False for no limiting.
     * @return \stdClass Object as described above
     */
    protected function get_areas_user_accesses($limitcourseids = false, $limitcontextids = false) {
        global $DB, $USER;

        // All results for admins (unless they have chosen to limit results). Eventually we could
        // add a new capability for managers.
        if (is_siteadmin() && !$limitcourseids && !$limitcontextids) {
            return (object)array('everything' => true);
        }

        $areasbylevel = array();

        // Split areas by context level so we only iterate only once through courses and cms.
        $searchareas = static::get_search_areas_list(true);
        foreach ($searchareas as $areaid => $unused) {
            $classname = static::get_area_classname($areaid);
            $searcharea = new $classname();
            foreach ($classname::get_levels() as $level) {
                $areasbylevel[$level][$areaid] = $searcharea;
            }
        }

        // This will store area - allowed contexts relations.
        $areascontexts = array();

        // Initialise two special-case arrays for storing other information related to the contexts.
        $separategroupscontexts = array();
        $visiblegroupscontextsareas = array();
        $usergroups = array();

        if (empty($limitcourseids) && !empty($areasbylevel[CONTEXT_SYSTEM])) {
            // We add system context to all search areas working at this level. Here each area is fully responsible of
            // the access control as we can not automate much, we can not even check guest access as some areas might
            // want to allow guests to retrieve data from them.

            $systemcontextid = \context_system::instance()->id;
            if (!$limitcontextids || in_array($systemcontextid, $limitcontextids)) {
                foreach ($areasbylevel[CONTEXT_SYSTEM] as $areaid => $searchclass) {
                    $areascontexts[$areaid][$systemcontextid] = $systemcontextid;
                }
            }
        }

        if (!empty($areasbylevel[CONTEXT_USER])) {
            if ($usercontext = \context_user::instance($USER->id, IGNORE_MISSING)) {
                if (!$limitcontextids || in_array($usercontext->id, $limitcontextids)) {
                    // Extra checking although only logged users should reach this point, guest users have a valid context id.
                    foreach ($areasbylevel[CONTEXT_USER] as $areaid => $searchclass) {
                        $areascontexts[$areaid][$usercontext->id] = $usercontext->id;
                    }
                }
            }
        }

        if (is_siteadmin()) {
            $allcourses = $this->get_all_courses($limitcourseids);
        } else {
            $allcourses = $mycourses = $this->get_my_courses((bool)get_config('core', 'searchallavailablecourses'));

            if (self::include_all_courses()) {
                $allcourses = $this->get_all_courses($limitcourseids);
            }
        }

        if (empty($limitcourseids) || in_array(SITEID, $limitcourseids)) {
            $allcourses[SITEID] = get_course(SITEID);
            if (isset($mycourses)) {
                $mycourses[SITEID] = get_course(SITEID);
            }
        }

        // Keep a list of included course context ids (needed for the block calculation below).
        $coursecontextids = [];
        $modulecms = [];

        foreach ($allcourses as $course) {
            if (!empty($limitcourseids) && !in_array($course->id, $limitcourseids)) {
                // Skip non-included courses.
                continue;
            }

            $coursecontext = \context_course::instance($course->id);
            $hasgrouprestrictions = false;

            if (!empty($areasbylevel[CONTEXT_COURSE]) &&
                    (!$limitcontextids || in_array($coursecontext->id, $limitcontextids))) {
                // Add the course contexts the user can view.
                foreach ($areasbylevel[CONTEXT_COURSE] as $areaid => $searchclass) {
                    if (!empty($mycourses[$course->id]) || \core_course_category::can_view_course_info($course)) {
                        $areascontexts[$areaid][$coursecontext->id] = $coursecontext->id;
                    }
                }
            }

            // Skip module context if a user can't access related course.
            if (isset($mycourses) && !key_exists($course->id, $mycourses)) {
                continue;
            }

            $coursecontextids[] = $coursecontext->id;

            // Info about the course modules.
            $modinfo = get_fast_modinfo($course);

            if (!empty($areasbylevel[CONTEXT_MODULE])) {
                // Add the module contexts the user can view (cm_info->uservisible).

                foreach ($areasbylevel[CONTEXT_MODULE] as $areaid => $searchclass) {

                    // Removing the plugintype 'mod_' prefix.
                    $modulename = substr($searchclass->get_component_name(), 4);

                    $modinstances = $modinfo->get_instances_of($modulename);
                    foreach ($modinstances as $modinstance) {
                        // Skip module context if not included in list of context ids.
                        if ($limitcontextids && !in_array($modinstance->context->id, $limitcontextids)) {
                            continue;
                        }
                        if ($modinstance->uservisible) {
                            $contextid = $modinstance->context->id;
                            $areascontexts[$areaid][$contextid] = $contextid;
                            $modulecms[$modinstance->id] = $modinstance;

                            if (!has_capability('moodle/site:accessallgroups', $modinstance->context) &&
                                    ($searchclass instanceof base_mod) &&
                                    $searchclass->supports_group_restriction()) {
                                if ($searchclass->restrict_cm_access_by_group($modinstance)) {
                                    $separategroupscontexts[$contextid] = $contextid;
                                    $hasgrouprestrictions = true;
                                } else {
                                    // Track a list of anything that has a group id (so might get
                                    // filtered) and doesn't want to be, in this context.
                                    if (!array_key_exists($contextid, $visiblegroupscontextsareas)) {
                                        $visiblegroupscontextsareas[$contextid] = array();
                                    }
                                    $visiblegroupscontextsareas[$contextid][$areaid] = $areaid;
                                }
                            }
                        }
                    }
                }
            }

            // Insert group information for course (unless there aren't any modules restricted by
            // group for this user in this course, in which case don't bother).
            if ($hasgrouprestrictions) {
                $groups = groups_get_all_groups($course->id, $USER->id, 0, 'g.id');
                foreach ($groups as $group) {
                    $usergroups[$group->id] = $group->id;
                }
            }
        }

        // Chuck away all the 'visible groups contexts' data unless there is actually something
        // that does use separate groups in the same context (this data is only used as an
        // 'override' in cases where the search is restricting to separate groups).
        foreach ($visiblegroupscontextsareas as $contextid => $areas) {
            if (!array_key_exists($contextid, $separategroupscontexts)) {
                unset($visiblegroupscontextsareas[$contextid]);
            }
        }

        // Add all supported block contexts for course contexts that user can access, in a single query for performance.
        if (!empty($areasbylevel[CONTEXT_BLOCK]) && !empty($coursecontextids)) {
            // Get list of all block types we care about.
            $blocklist = [];
            foreach ($areasbylevel[CONTEXT_BLOCK] as $areaid => $searchclass) {
                $blocklist[$searchclass->get_block_name()] = true;
            }
            list ($blocknamesql, $blocknameparams) = $DB->get_in_or_equal(array_keys($blocklist));

            // Get list of course contexts.
            list ($contextsql, $contextparams) = $DB->get_in_or_equal($coursecontextids);

            // Get list of block context (if limited).
            $blockcontextwhere = '';
            $blockcontextparams = [];
            if ($limitcontextids) {
                list ($blockcontextsql, $blockcontextparams) = $DB->get_in_or_equal($limitcontextids);
                $blockcontextwhere = 'AND x.id ' . $blockcontextsql;
            }

            // Query all blocks that are within an included course, and are set to be visible, and
            // in a supported page type (basically just course view). This query could be
            // extended (or a second query added) to support blocks that are within a module
            // context as well, and we could add more page types if required.
            $blockrecs = $DB->get_records_sql("
                        SELECT x.*, bi.blockname AS blockname, bi.id AS blockinstanceid
                          FROM {block_instances} bi
                          JOIN {context} x ON x.instanceid = bi.id AND x.contextlevel = ?
                     LEFT JOIN {block_positions} bp ON bp.blockinstanceid = bi.id
                               AND bp.contextid = bi.parentcontextid
                               AND bp.pagetype LIKE 'course-view-%'
                               AND bp.subpage = ''
                               AND bp.visible = 0
                         WHERE bi.parentcontextid $contextsql
                               $blockcontextwhere
                               AND bi.blockname $blocknamesql
                               AND bi.subpagepattern IS NULL
                               AND (bi.pagetypepattern = 'site-index'
                                   OR bi.pagetypepattern LIKE 'course-view-%'
                                   OR bi.pagetypepattern = 'course-*'
                                   OR bi.pagetypepattern = '*')
                               AND bp.id IS NULL",
                    array_merge([CONTEXT_BLOCK], $contextparams, $blockcontextparams, $blocknameparams));
            $blockcontextsbyname = [];
            foreach ($blockrecs as $blockrec) {
                if (empty($blockcontextsbyname[$blockrec->blockname])) {
                    $blockcontextsbyname[$blockrec->blockname] = [];
                }
                \context_helper::preload_from_record($blockrec);
                $blockcontextsbyname[$blockrec->blockname][] = \context_block::instance(
                        $blockrec->blockinstanceid);
            }

            // Add the block contexts the user can view.
            foreach ($areasbylevel[CONTEXT_BLOCK] as $areaid => $searchclass) {
                if (empty($blockcontextsbyname[$searchclass->get_block_name()])) {
                    continue;
                }
                foreach ($blockcontextsbyname[$searchclass->get_block_name()] as $context) {
                    if (has_capability('moodle/block:view', $context)) {
                        $areascontexts[$areaid][$context->id] = $context->id;
                    }
                }
            }
        }

        // Return all the data.
        return (object)array('everything' => false, 'usercontexts' => $areascontexts,
                'separategroupscontexts' => $separategroupscontexts, 'usergroups' => $usergroups,
                'visiblegroupscontextsareas' => $visiblegroupscontextsareas);
    }

    /**
     * Returns requested page of documents plus additional information for paging.
     *
     * This function does not perform any kind of security checking for access, the caller code
     * should check that the current user have moodle/search:query capability.
     *
     * If a page is requested that is beyond the last result, the last valid page is returned in
     * results, and actualpage indicates which page was returned.
     *
     * @param stdClass $formdata
     * @param int $pagenum The 0 based page number.
     * @return object An object with 3 properties:
     *                    results    => An array of \core_search\documents for the actual page.
     *                    totalcount => Number of records that are possibly available, to base paging on.
     *                    actualpage => The actual page returned.
     */
    public function paged_search(\stdClass $formdata, $pagenum) {
        $out = new \stdClass();

        if (self::is_search_area_categories_enabled() && !empty($formdata->cat)) {
            $cat = self::get_search_area_category_by_name($formdata->cat);
            if (empty($formdata->areaids)) {
                $formdata->areaids = array_keys($cat->get_areas());
            } else {
                foreach ($formdata->areaids as $key => $areaid) {
                    if (!key_exists($areaid, $cat->get_areas())) {
                        unset($formdata->areaids[$key]);
                    }
                }
            }
        }

        $perpage = static::DISPLAY_RESULTS_PER_PAGE;

        // Make sure we only allow request up to max page.
        $pagenum = min($pagenum, (static::MAX_RESULTS / $perpage) - 1);

        // Calculate the first and last document number for the current page, 1 based.
        $mindoc = ($pagenum * $perpage) + 1;
        $maxdoc = ($pagenum + 1) * $perpage;

        // Get engine documents, up to max.
        $docs = $this->search($formdata, $maxdoc);

        $resultcount = count($docs);
        if ($resultcount < $maxdoc) {
            // This means it couldn't give us results to max, so the count must be the max.
            $out->totalcount = $resultcount;
        } else {
            // Get the possible count reported by engine, and limit to our max.
            $out->totalcount = $this->engine->get_query_total_count();
            if (defined('BEHAT_SITE_RUNNING') && $this->behatresultcount) {
                // Override results when using Behat mock results.
                $out->totalcount = $this->behatresultcount;
            }
            $out->totalcount = min($out->totalcount, static::MAX_RESULTS);
        }

        // Determine the actual page.
        if ($resultcount < $mindoc) {
            // We couldn't get the min docs for this page, so determine what page we can get.
            $out->actualpage = floor(($resultcount - 1) / $perpage);
        } else {
            $out->actualpage = $pagenum;
        }

        // Split the results to only return the page.
        $out->results = array_slice($docs, $out->actualpage * $perpage, $perpage, true);

        return $out;
    }

    /**
     * Returns documents from the engine based on the data provided.
     *
     * This function does not perform any kind of security checking, the caller code
     * should check that the current user have moodle/search:query capability.
     *
     * It might return the results from the cache instead.
     *
     * Valid formdata options include:
     * - q (query text)
     * - courseids (optional list of course ids to restrict)
     * - contextids (optional list of context ids to restrict)
     * - context (Moodle context object for location user searched from)
     * - order (optional ordering, one of the types supported by the search engine e.g. 'relevance')
     * - userids (optional list of user ids to restrict)
     *
     * @param \stdClass $formdata Query input data (usually from search form)
     * @param int $limit The maximum number of documents to return
     * @return \core_search\document[]
     */
    public function search(\stdClass $formdata, $limit = 0) {
        // For Behat testing, the search results can be faked using a special step.
        if (defined('BEHAT_SITE_RUNNING')) {
            $fakeresult = get_config('core_search', 'behat_fakeresult');
            if ($fakeresult) {
                // Clear config setting.
                unset_config('core_search', 'behat_fakeresult');

                // Check query matches expected value.
                $details = json_decode($fakeresult);
                if ($formdata->q !== $details->query) {
                    throw new \coding_exception('Unexpected search query: ' . $formdata->q);
                }

                // Create search documents from the JSON data.
                $docs = [];
                foreach ($details->results as $result) {
                    $doc = new \core_search\document($result->itemid, $result->componentname,
                            $result->areaname);
                    foreach ((array)$result->fields as $field => $value) {
                        $doc->set($field, $value);
                    }
                    foreach ((array)$result->extrafields as $field => $value) {
                        $doc->set_extra($field, $value);
                    }
                    $area = $this->get_search_area($doc->get('areaid'));
                    $doc->set_doc_url($area->get_doc_url($doc));
                    $doc->set_context_url($area->get_context_url($doc));
                    $docs[] = $doc;
                }

                // Store the mock count, and apply the limit to the returned results.
                $this->behatresultcount = count($docs);
                if ($this->behatresultcount > $limit) {
                    $docs = array_slice($docs, 0, $limit);
                }

                return $docs;
            }
        }

        $limitcourseids = $this->build_limitcourseids($formdata);

        $limitcontextids = false;
        if (!empty($formdata->contextids)) {
            $limitcontextids = $formdata->contextids;
        }

        // Clears previous query errors.
        $this->engine->clear_query_error();

        $contextinfo = $this->get_areas_user_accesses($limitcourseids, $limitcontextids);
        if (!$contextinfo->everything && !$contextinfo->usercontexts) {
            // User can not access any context.
            $docs = array();
        } else {
            // If engine does not support groups, remove group information from the context info -
            // use the old format instead (true = admin, array = user contexts).
            if (!$this->engine->supports_group_filtering()) {
                $contextinfo = $contextinfo->everything ? true : $contextinfo->usercontexts;
            }

            // Execute the actual query.
            $docs = $this->engine->execute_query($formdata, $contextinfo, $limit);
        }

        return $docs;
    }

    /**
     * Search for top ranked result.
     * @param \stdClass $formdata search query data
     * @return array|document[]
     */
    public function search_top(\stdClass $formdata): array {
        global $USER;

        // Return if the config value is set to 0.
        $maxtopresult = get_config('core', 'searchmaxtopresults');
        if (empty($maxtopresult)) {
            return [];
        }

        // Only process if 'searchenablecategories' is set.
        if (self::is_search_area_categories_enabled() && !empty($formdata->cat)) {
            $cat = self::get_search_area_category_by_name($formdata->cat);
            $formdata->areaids = array_keys($cat->get_areas());
        } else {
            return [];
        }
        $docs = $this->search($formdata);

        // Look for course, teacher and course content.
        $coursedocs = [];
        $courseteacherdocs = [];
        $coursecontentdocs = [];
        $otherdocs = [];
        foreach ($docs as $doc) {
            if ($doc->get('areaid') === 'core_course-course' && stripos($doc->get('title'), $formdata->q) !== false) {
                $coursedocs[] = $doc;
            } else if (strpos($doc->get('areaid'), 'course_teacher') !== false
                && stripos($doc->get('content'), $formdata->q) !== false) {
                $courseteacherdocs[] = $doc;
            } else if (strpos($doc->get('areaid'), 'mod_') !== false) {
                $coursecontentdocs[] = $doc;
            } else {
                $otherdocs[] = $doc;
            }
        }

        // Swap current courses to top.
        $enroledcourses = $this->get_my_courses(false);
        // Move current courses of the user to top.
        foreach ($enroledcourses as $course) {
            $completion = new \completion_info($course);
            if (!$completion->is_course_complete($USER->id)) {
                foreach ($coursedocs as $index => $doc) {
                    $areaid = $doc->get('areaid');
                    if ($areaid == 'core_course-course' && $course->id == $doc->get('courseid')) {
                        unset($coursedocs[$index]);
                        array_unshift($coursedocs, $doc);
                    }
                }
            }
        }

        $maxtopresult = get_config('core', 'searchmaxtopresults');
        $result = array_merge($coursedocs, $courseteacherdocs, $coursecontentdocs, $otherdocs);
        return array_slice($result, 0, $maxtopresult);
    }

    /**
     * Build a list of course ids to limit the search based on submitted form data.
     *
     * @param \stdClass $formdata Submitted search form data.
     *
     * @return array|bool
     */
    protected function build_limitcourseids(\stdClass $formdata) {
        $limitcourseids = false;

        if (!empty($formdata->mycoursesonly)) {
            $limitcourseids = array_keys($this->get_my_courses(false));
        }

        if (!empty($formdata->courseids)) {
            if (empty($limitcourseids)) {
                $limitcourseids = $formdata->courseids;
            } else {
                $limitcourseids = array_intersect($limitcourseids, $formdata->courseids);
            }
        }

        return $limitcourseids;
    }

    /**
     * Merge separate index segments into one.
     */
    public function optimize_index() {
        $this->engine->optimize();
    }

    /**
     * Index all documents.
     *
     * @param bool $fullindex Whether we should reindex everything or not.
     * @param float $timelimit Time limit in seconds (0 = no time limit)
     * @param \progress_trace|null $progress Optional class for tracking progress
     * @throws \moodle_exception
     * @return bool Whether there was any updated document or not.
     */
    public function index($fullindex = false, $timelimit = 0, \progress_trace $progress = null) {
        global $DB;

        // Cannot combine time limit with reindex.
        if ($timelimit && $fullindex) {
            throw new \coding_exception('Cannot apply time limit when reindexing');
        }
        if (!$progress) {
            $progress = new \null_progress_trace();
        }

        // Unlimited time.
        \core_php_time_limit::raise();

        // Notify the engine that an index starting.
        $this->engine->index_starting($fullindex);

        $sumdocs = 0;

        $searchareas = $this->get_search_areas_list(true);

        if ($timelimit) {
            // If time is limited (and therefore we're not just indexing everything anyway), select
            // an order for search areas. The intention here is to avoid a situation where a new
            // large search area is enabled, and this means all our other search areas go out of
            // date while that one is being indexed. To do this, we order by the time we spent
            // indexing them last time we ran, meaning anything that took a very long time will be
            // done last.
            uasort($searchareas, function(\core_search\base $area1, \core_search\base $area2) {
                return (int)$area1->get_last_indexing_duration() - (int)$area2->get_last_indexing_duration();
            });

            // Decide time to stop.
            $stopat = self::get_current_time() + $timelimit;
        }

        foreach ($searchareas as $areaid => $searcharea) {

            $progress->output('Processing area: ' . $searcharea->get_visible_name());

            // Notify the engine that an area is starting.
            $this->engine->area_index_starting($searcharea, $fullindex);

            $indexingstart = (int)self::get_current_time();
            $elapsed = self::get_current_time();

            // This is used to store this component config.
            list($componentconfigname, $varname) = $searcharea->get_config_var_name();

            $prevtimestart = intval(get_config($componentconfigname, $varname . '_indexingstart'));

            if ($fullindex === true) {
                $referencestarttime = 0;

                // For full index, we delete any queued context index requests, as those will
                // obviously be met by the full index.
                $DB->delete_records('search_index_requests');
            } else {
                $partial = get_config($componentconfigname, $varname . '_partial');
                if ($partial) {
                    // When the previous index did not complete all data, we start from the time of the
                    // last document that was successfully indexed. (Note this will result in
                    // re-indexing that one document, but we can't avoid that because there may be
                    // other documents in the same second.)
                    $referencestarttime = intval(get_config($componentconfigname, $varname . '_lastindexrun'));
                } else {
                    $referencestarttime = $prevtimestart;
                }
            }

            // Getting the recordset from the area.
            $recordset = $searcharea->get_recordset_by_timestamp($referencestarttime);
            $initialquerytime = self::get_current_time() - $elapsed;
            if ($initialquerytime > self::DISPLAY_LONG_QUERY_TIME) {
                $progress->output('Initial query took ' . round($initialquerytime, 1) .
                        ' seconds.', 1);
            }

            // Pass get_document as callback.
            $fileindexing = $this->engine->file_indexing_enabled() && $searcharea->uses_file_indexing();
            $options = array('indexfiles' => $fileindexing, 'lastindexedtime' => $prevtimestart);
            if ($timelimit) {
                $options['stopat'] = $stopat;
            }
            $options['progress'] = $progress;
            $iterator = new skip_future_documents_iterator(new \core\dml\recordset_walk(
                    $recordset, array($searcharea, 'get_document'), $options));
            $result = $this->engine->add_documents($iterator, $searcharea, $options);
            $recordset->close();
            $batchinfo = '';
            if (count($result) === 6) {
                [$numrecords, $numdocs, $numdocsignored, $lastindexeddoc, $partial, $batches] = $result;
                // Only show the batch count if we actually batched any requests.
                if ($batches !== $numdocs + $numdocsignored) {
                    $batchinfo = ' (' . $batches . ' batch' . ($batches === 1 ? '' : 'es') . ')';
                }
            } else if (count($result) === 5) {
                // Backward compatibility for engines that don't return a batch count.
                [$numrecords, $numdocs, $numdocsignored, $lastindexeddoc, $partial] = $result;
                // Deprecated since Moodle 3.10 MDL-68690.
                // TODO: MDL-68776 This will be deleted in Moodle 4.2.
                debugging('engine::add_documents() should return $batches (5-value return is deprecated)',
                        DEBUG_DEVELOPER);
            } else {
                throw new coding_exception('engine::add_documents() should return $partial (4-value return is deprecated)');
            }

            if ($numdocs > 0) {
                $elapsed = round((self::get_current_time() - $elapsed), 1);

                $partialtext = '';
                if ($partial) {
                    $partialtext = ' (not complete; done to ' . userdate($lastindexeddoc,
                            get_string('strftimedatetimeshort', 'langconfig')) . ')';
                }

                $progress->output('Processed ' . $numrecords . ' records containing ' . $numdocs .
                        ' documents' . $batchinfo . ', in ' . $elapsed . ' seconds' . $partialtext . '.', 1);
            } else {
                $progress->output('No new documents to index.', 1);
            }

            // Notify the engine this area is complete, and only mark times if true.
            if ($this->engine->area_index_complete($searcharea, $numdocs, $fullindex)) {
                $sumdocs += $numdocs;

                // Store last index run once documents have been committed to the search engine.
                set_config($varname . '_indexingstart', $indexingstart, $componentconfigname);
                set_config($varname . '_indexingend', (int)self::get_current_time(), $componentconfigname);
                set_config($varname . '_docsignored', $numdocsignored, $componentconfigname);
                set_config($varname . '_docsprocessed', $numdocs, $componentconfigname);
                set_config($varname . '_recordsprocessed', $numrecords, $componentconfigname);
                if ($lastindexeddoc > 0) {
                    set_config($varname . '_lastindexrun', $lastindexeddoc, $componentconfigname);
                }
                if ($partial) {
                    set_config($varname . '_partial', 1, $componentconfigname);
                } else {
                    unset_config($varname . '_partial', $componentconfigname);
                }
            } else {
                $progress->output('Engine reported error.');
            }

            if ($timelimit && (self::get_current_time() >= $stopat)) {
                $progress->output('Stopping indexing due to time limit.');
                break;
            }
        }

        if ($sumdocs > 0) {
            $event = \core\event\search_indexed::create(
                    array('context' => \context_system::instance()));
            $event->trigger();
        }

        $this->engine->index_complete($sumdocs, $fullindex);

        return (bool)$sumdocs;
    }

    /**
     * Indexes or reindexes a specific context of the system, e.g. one course.
     *
     * The function returns an object with field 'complete' (true or false).
     *
     * This function supports partial indexing via the time limit parameter. If the time limit
     * expires, it will return values for $startfromarea and $startfromtime which can be passed
     * next time to continue indexing.
     *
     * @param \context $context Context to restrict index.
     * @param string $singleareaid If specified, indexes only the given area.
     * @param float $timelimit Time limit in seconds (0 = no time limit)
     * @param \progress_trace|null $progress Optional class for tracking progress
     * @param string $startfromarea Area to start from
     * @param int $startfromtime Timestamp to start from
     * @return \stdClass Object indicating success
     */
    public function index_context($context, $singleareaid = '', $timelimit = 0,
            \progress_trace $progress = null, $startfromarea = '', $startfromtime = 0) {
        if (!$progress) {
            $progress = new \null_progress_trace();
        }

        // Work out time to stop, if limited.
        if ($timelimit) {
            // Decide time to stop.
            $stopat = self::get_current_time() + $timelimit;
        }

        // No PHP time limit.
        \core_php_time_limit::raise();

        // Notify the engine that an index starting.
        $this->engine->index_starting(false);

        $sumdocs = 0;

        // Get all search areas, in consistent order.
        $searchareas = $this->get_search_areas_list(true);
        ksort($searchareas);

        // Are we skipping past some that were handled previously?
        $skipping = $startfromarea ? true : false;

        foreach ($searchareas as $areaid => $searcharea) {
            // If we're only processing one area id, skip all the others.
            if ($singleareaid && $singleareaid !== $areaid) {
                continue;
            }

            // If we're skipping to a later area, continue through the loop.
            $referencestarttime = 0;
            if ($skipping) {
                if ($areaid !== $startfromarea) {
                    continue;
                }
                // Stop skipping and note the reference start time.
                $skipping = false;
                $referencestarttime = $startfromtime;
            }

            $progress->output('Processing area: ' . $searcharea->get_visible_name());

            $elapsed = self::get_current_time();

            // Get the recordset of all documents from the area for this context.
            $recordset = $searcharea->get_document_recordset($referencestarttime, $context);
            if (!$recordset) {
                if ($recordset === null) {
                    $progress->output('Skipping (not relevant to context).', 1);
                } else {
                    $progress->output('Skipping (does not support context indexing).', 1);
                }
                continue;
            }

            // Notify the engine that an area is starting.
            $this->engine->area_index_starting($searcharea, false);

            // Work out search options.
            $options = [];
            $options['indexfiles'] = $this->engine->file_indexing_enabled() &&
                    $searcharea->uses_file_indexing();
            if ($timelimit) {
                $options['stopat'] = $stopat;
            }

            // Construct iterator which will use get_document on the recordset results.
            $iterator = new \core\dml\recordset_walk($recordset,
                    array($searcharea, 'get_document'), $options);

            // Use this iterator to add documents.
            $result = $this->engine->add_documents($iterator, $searcharea, $options);
            $batchinfo = '';
            if (count($result) === 6) {
                [$numrecords, $numdocs, $numdocsignored, $lastindexeddoc, $partial, $batches] = $result;
                // Only show the batch count if we actually batched any requests.
                if ($batches !== $numdocs + $numdocsignored) {
                    $batchinfo = ' (' . $batches . ' batch' . ($batches === 1 ? '' : 'es') . ')';
                }
            } else if (count($result) === 5) {
                // Backward compatibility for engines that don't return a batch count.
                [$numrecords, $numdocs, $numdocsignored, $lastindexeddoc, $partial] = $result;
                // Deprecated since Moodle 3.10 MDL-68690.
                // TODO: MDL-68776 This will be deleted in Moodle 4.2 (as should the below bit).
                debugging('engine::add_documents() should return $batches (5-value return is deprecated)',
                        DEBUG_DEVELOPER);
            } else {
                // Backward compatibility for engines that don't support partial adding.
                list($numrecords, $numdocs, $numdocsignored, $lastindexeddoc) = $result;
                debugging('engine::add_documents() should return $partial (4-value return is deprecated)',
                        DEBUG_DEVELOPER);
                $partial = false;
            }

            if ($numdocs > 0) {
                $elapsed = round((self::get_current_time() - $elapsed), 3);
                $progress->output('Processed ' . $numrecords . ' records containing ' . $numdocs .
                        ' documents' . $batchinfo . ', in ' . $elapsed . ' seconds' .
                        ($partial ? ' (not complete)' : '') . '.', 1);
            } else {
                $progress->output('No documents to index.', 1);
            }

            // Notify the engine this area is complete, but don't store any times as this is not
            // part of the 'normal' search index.
            if (!$this->engine->area_index_complete($searcharea, $numdocs, false)) {
                $progress->output('Engine reported error.', 1);
            }

            if ($partial && $timelimit && (self::get_current_time() >= $stopat)) {
                $progress->output('Stopping indexing due to time limit.');
                break;
            }
        }

        if ($sumdocs > 0) {
            $event = \core\event\search_indexed::create(
                    array('context' => $context));
            $event->trigger();
        }

        $this->engine->index_complete($sumdocs, false);

        // Indicate in result whether we completed indexing, or only part of it.
        $result = new \stdClass();
        if ($partial) {
            $result->complete = false;
            $result->startfromarea = $areaid;
            $result->startfromtime = $lastindexeddoc;
        } else {
            $result->complete = true;
        }
        return $result;
    }

    /**
     * Resets areas config.
     *
     * @throws \moodle_exception
     * @param string $areaid
     * @return void
     */
    public function reset_config($areaid = false) {

        if (!empty($areaid)) {
            $searchareas = array();
            if (!$searchareas[$areaid] = static::get_search_area($areaid)) {
                throw new \moodle_exception('errorareanotavailable', 'search', '', $areaid);
            }
        } else {
            // Only the enabled ones.
            $searchareas = static::get_search_areas_list(true);
        }

        foreach ($searchareas as $searcharea) {
            list($componentname, $varname) = $searcharea->get_config_var_name();
            $config = $searcharea->get_config();

            foreach ($config as $key => $value) {
                // We reset them all but the enable/disabled one.
                if ($key !== $varname . '_enabled') {
                    set_config($key, 0, $componentname);
                }
            }
        }
    }

    /**
     * Deletes an area's documents or all areas documents.
     *
     * @param string $areaid The area id or false for all
     * @return void
     */
    public function delete_index($areaid = false) {
        if (!empty($areaid)) {
            $this->engine->delete($areaid);
            $this->reset_config($areaid);
        } else {
            $this->engine->delete();
            $this->reset_config();
        }
    }

    /**
     * Deletes index by id.
     *
     * @param int Solr Document string $id
     */
    public function delete_index_by_id($id) {
        $this->engine->delete_by_id($id);
    }

    /**
     * Returns search areas configuration.
     *
     * @param \core_search\base[] $searchareas
     * @return \stdClass[] $configsettings
     */
    public function get_areas_config($searchareas) {

        $vars = array('indexingstart', 'indexingend', 'lastindexrun', 'docsignored',
                'docsprocessed', 'recordsprocessed', 'partial');

        $configsettings = [];
        foreach ($searchareas as $searcharea) {

            $areaid = $searcharea->get_area_id();

            $configsettings[$areaid] = new \stdClass();
            list($componentname, $varname) = $searcharea->get_config_var_name();

            if (!$searcharea->is_enabled()) {
                // We delete all indexed data on disable so no info.
                foreach ($vars as $var) {
                    $configsettings[$areaid]->{$var} = 0;
                }
            } else {
                foreach ($vars as $var) {
                    $configsettings[$areaid]->{$var} = get_config($componentname, $varname .'_' . $var);
                }
            }

            // Formatting the time.
            if (!empty($configsettings[$areaid]->lastindexrun)) {
                $configsettings[$areaid]->lastindexrun = userdate($configsettings[$areaid]->lastindexrun);
            } else {
                $configsettings[$areaid]->lastindexrun = get_string('never');
            }
        }
        return $configsettings;
    }

    /**
     * Triggers search_results_viewed event
     *
     * Other data required:
     * - q: The query string
     * - page: The page number
     * - title: Title filter
     * - areaids: Search areas filter
     * - courseids: Courses filter
     * - timestart: Time start filter
     * - timeend: Time end filter
     *
     * @since Moodle 3.2
     * @param array $other Other info for the event.
     * @return \core\event\search_results_viewed
     */
    public static function trigger_search_results_viewed($other) {
        $event = \core\event\search_results_viewed::create([
            'context' => \context_system::instance(),
            'other' => $other
        ]);
        $event->trigger();

        return $event;
    }

    /**
     * Checks whether a classname is of an actual search area.
     *
     * @param string $classname
     * @return bool
     */
    protected static function is_search_area($classname) {
        if (is_subclass_of($classname, 'core_search\base')) {
            return (new \ReflectionClass($classname))->isInstantiable();
        }

        return false;
    }

    /**
     * Requests that a specific context is indexed by the scheduled task. The context will be
     * added to a queue which is processed by the task.
     *
     * This is used after a restore to ensure that restored items are indexed, even though their
     * modified time will be older than the latest indexed. It is also used by the 'Gradual reindex'
     * admin feature from the search areas screen.
     *
     * @param \context $context Context to index within
     * @param string $areaid Area to index, '' = all areas
     * @param int $priority Priority (INDEX_PRIORITY_xx constant)
     */
    public static function request_index(\context $context, $areaid = '',
            $priority = self::INDEX_PRIORITY_NORMAL) {
        global $DB;

        // Check through existing requests for this context or any parent context.
        list ($contextsql, $contextparams) = $DB->get_in_or_equal(
                $context->get_parent_context_ids(true));
        $existing = $DB->get_records_select('search_index_requests',
                'contextid ' . $contextsql, $contextparams, '',
                'id, searcharea, partialarea, indexpriority');
        foreach ($existing as $rec) {
            // If we haven't started processing the existing request yet, and it covers the same
            // area (or all areas) then that will be sufficient so don't add anything else.
            if ($rec->partialarea === '' && ($rec->searcharea === $areaid || $rec->searcharea === '')) {
                // If the existing request has the same (or higher) priority, no need to add anything.
                if ($rec->indexpriority >= $priority) {
                    return;
                }
                // The existing request has lower priority. If it is exactly the same, then just
                // adjust the priority of the existing request.
                if ($rec->searcharea === $areaid) {
                    $DB->set_field('search_index_requests', 'indexpriority', $priority,
                            ['id' => $rec->id]);
                    return;
                }
                // The existing request would cover this area but is a lower priority. We need to
                // add the new request even though that means we will index part of it twice.
            }
        }

        // No suitable existing request, so add a new one.
        $newrecord = [ 'contextid' => $context->id, 'searcharea' => $areaid,
                'timerequested' => (int)self::get_current_time(),
                'partialarea' => '', 'partialtime' => 0,
                'indexpriority' => $priority ];
        $DB->insert_record('search_index_requests', $newrecord);
    }

    /**
     * Processes outstanding index requests. This will take the first item from the queue (taking
     * account the indexing priority) and process it, continuing until an optional time limit is
     * reached.
     *
     * If there are no index requests, the function will do nothing.
     *
     * @param float $timelimit Time limit (0 = none)
     * @param \progress_trace|null $progress Optional progress indicator
     */
    public function process_index_requests($timelimit = 0.0, \progress_trace $progress = null) {
        global $DB;

        if (!$progress) {
            $progress = new \null_progress_trace();
        }

        $before = self::get_current_time();
        if ($timelimit) {
            $stopat = $before + $timelimit;
        }
        while (true) {
            // Retrieve first request, using fully defined ordering.
            $requests = $DB->get_records('search_index_requests', null,
                    'indexpriority DESC, timerequested, contextid, searcharea',
                    'id, contextid, searcharea, partialarea, partialtime', 0, 1);
            if (!$requests) {
                // If there are no more requests, stop.
                break;
            }
            $request = reset($requests);

            // Calculate remaining time.
            $remainingtime = 0;
            $beforeindex = self::get_current_time();
            if ($timelimit) {
                $remainingtime = $stopat - $beforeindex;

                // If the time limit expired already, stop now. (Otherwise we might accidentally
                // index with no time limit or a negative time limit.)
                if ($remainingtime <= 0) {
                    break;
                }
            }

            // Show a message before each request, indicating what will be indexed.
            $context = \context::instance_by_id($request->contextid, IGNORE_MISSING);
            if (!$context) {
                $DB->delete_records('search_index_requests', ['id' => $request->id]);
                $progress->output('Skipped deleted context: ' . $request->contextid);
                continue;
            }
            $contextname = $context->get_context_name();
            if ($request->searcharea) {
                $contextname .= ' (search area: ' . $request->searcharea . ')';
            }
            $progress->output('Indexing requested context: ' . $contextname);

            // Actually index the context.
            $result = $this->index_context($context, $request->searcharea, $remainingtime,
                    $progress, $request->partialarea, $request->partialtime);

            // Work out shared part of message.
            $endmessage = $contextname . ' (' . round(self::get_current_time() - $beforeindex, 1) . 's)';

            // Update database table and continue/stop as appropriate.
            if ($result->complete) {
                // If we completed the request, remove it from the table.
                $DB->delete_records('search_index_requests', ['id' => $request->id]);
                $progress->output('Completed requested context: ' . $endmessage);
            } else {
                // If we didn't complete the request, store the partial details (how far it got).
                $DB->update_record('search_index_requests', ['id' => $request->id,
                        'partialarea' => $result->startfromarea,
                        'partialtime' => $result->startfromtime]);
                $progress->output('Ending requested context: ' . $endmessage);

                // The time limit must have expired, so stop looping.
                break;
            }
        }
    }

    /**
     * Gets information about the request queue, in the form of a plain object suitable for passing
     * to a template for rendering.
     *
     * @return \stdClass Information about queued index requests
     */
    public function get_index_requests_info() {
        global $DB;

        $result = new \stdClass();

        $result->total = $DB->count_records('search_index_requests');
        $result->topten = $DB->get_records('search_index_requests', null,
                'indexpriority DESC, timerequested, contextid, searcharea',
                'id, contextid, timerequested, searcharea, partialarea, partialtime, indexpriority',
                0, 10);
        foreach ($result->topten as $item) {
            $context = \context::instance_by_id($item->contextid);
            $item->contextlink = \html_writer::link($context->get_url(),
                    s($context->get_context_name()));
            if ($item->searcharea) {
                $item->areaname = $this->get_search_area($item->searcharea)->get_visible_name();
            }
            if ($item->partialarea) {
                $item->partialareaname = $this->get_search_area($item->partialarea)->get_visible_name();
            }
            switch ($item->indexpriority) {
                case self::INDEX_PRIORITY_REINDEXING :
                    $item->priorityname = get_string('priority_reindexing', 'search');
                    break;
                case self::INDEX_PRIORITY_NORMAL :
                    $item->priorityname = get_string('priority_normal', 'search');
                    break;
            }
        }

        // Normalise array indices.
        $result->topten = array_values($result->topten);

        if ($result->total > 10) {
            $result->ellipsis = true;
        }

        return $result;
    }

    /**
     * Gets current time for use in search system.
     *
     * Note: This should be replaced with generic core functionality once possible (see MDL-60644).
     *
     * @return float Current time in seconds (with decimals)
     */
    public static function get_current_time() {
        if (PHPUNIT_TEST && self::$phpunitfaketime) {
            return self::$phpunitfaketime;
        }
        return microtime(true);
    }

    /**
     * Check if search area categories functionality is enabled.
     *
     * @return bool
     */
    public static function is_search_area_categories_enabled() {
        return !empty(get_config('core', 'searchenablecategories'));
    }

    /**
     * Check if all results category should be hidden.
     *
     * @return bool
     */
    public static function should_hide_all_results_category() {
        return get_config('core', 'searchhideallcategory');
    }

    /**
     * Returns default search area category name.
     *
     * @return string
     */
    public static function get_default_area_category_name() {
        $default = get_config('core', 'searchdefaultcategory');

        if (empty($default)) {
            $default = self::SEARCH_AREA_CATEGORY_ALL;
        }

        if ($default == self::SEARCH_AREA_CATEGORY_ALL && self::should_hide_all_results_category()) {
            $default = self::SEARCH_AREA_CATEGORY_COURSE_CONTENT;
        }

        return $default;
    }

    /**
     * Get a list of all courses limited by ids if required.
     *
     * @param array|false $limitcourseids An array of course ids to limit the search to. False for no limiting.
     * @return array
     */
    protected function get_all_courses($limitcourseids) {
        global $DB;

        if ($limitcourseids) {
            list ($coursesql, $courseparams) = $DB->get_in_or_equal($limitcourseids);
            $coursesql = 'id ' . $coursesql;
        } else {
            $coursesql = '';
            $courseparams = [];
        }

        // Get courses using the same list of fields from enrol_get_my_courses.
        return $DB->get_records_select('course', $coursesql, $courseparams, '',
            'id, category, sortorder, shortname, fullname, idnumber, startdate, visible, ' .
            'groupmode, groupmodeforce, cacherev');
    }

    /**
     * Get a list of courses as user can access.
     *
     * @param bool $allaccessible Include courses user is not enrolled in, but can access.
     * @return array
     */
    protected function get_my_courses($allaccessible) {
        return enrol_get_my_courses(array('id', 'cacherev'), 'id', 0, [], $allaccessible);
    }

    /**
     * Check if search all courses setting is enabled.
     *
     * @return bool
     */
    public static function include_all_courses() {
        return !empty(get_config('core', 'searchincludeallcourses'));
    }

    /**
     * Cleans up non existing search area.
     *
     * 1. Remove all configs from {config_plugins} table.
     * 2. Delete all related indexed documents.
     *
     * @param string $areaid Search area id.
     */
    public static function clean_up_non_existing_area($areaid) {
        global $DB;

        if (!empty(self::get_search_area($areaid))) {
            throw new \coding_exception("Area $areaid exists. Please use appropriate search area class to manipulate the data.");
        }

        $parts = self::parse_areaid($areaid);

        $plugin = $parts[0];
        $configprefix = $parts[1];

        foreach (base::get_settingnames() as $settingname) {
            $name = $configprefix. $settingname;
            $DB->delete_records('config_plugins', ['name' => $name, 'plugin' => $plugin]);
        }

        $engine = self::instance()->get_engine();
        $engine->delete($areaid);
    }

    /**
     * Informs the search system that a context has been deleted.
     *
     * This will clear the data from the search index, where the search engine supports that.
     *
     * This function does not usually throw an exception (so as not to get in the way of the
     * context deletion finishing).
     *
     * This is called for all types of context deletion.
     *
     * @param \context $context Context object that has just been deleted
     */
    public static function context_deleted(\context $context) {
        if (self::is_indexing_enabled()) {
            try {
                // Hold on, are we deleting a course? If so, and this context is part of the course,
                // then don't bother to send a delete because we delete the whole course at once
                // later.
                if (!empty(self::$coursedeleting)) {
                    $coursecontext = $context->get_course_context(false);
                    if ($coursecontext && array_key_exists($coursecontext->instanceid, self::$coursedeleting)) {
                        // Skip further processing.
                        return;
                    }
                }

                $engine = self::instance()->get_engine();
                $engine->delete_index_for_context($context->id);
            } catch (\moodle_exception $e) {
                debugging('Error deleting search index data for context ' . $context->id . ': ' . $e->getMessage());
            }
        }
    }

    /**
     * Informs the search system that a course is about to be deleted.
     *
     * This prevents it from sending hundreds of 'delete context' updates for all the individual
     * contexts that are deleted.
     *
     * If you call this, you must call course_deleting_finish().
     *
     * @param int $courseid Course id that is being deleted
     */
    public static function course_deleting_start(int $courseid) {
        self::$coursedeleting[$courseid] = true;
    }

    /**
     * Informs the search engine that a course has now been deleted.
     *
     * This causes the search engine to actually delete the index for the whole course.
     *
     * @param int $courseid Course id that no longer exists
     */
    public static function course_deleting_finish(int $courseid) {
        if (!array_key_exists($courseid, self::$coursedeleting)) {
            // Show a debug warning. It doesn't actually matter very much, as we will now delete
            // the course data anyhow.
            debugging('course_deleting_start not called before deletion of ' . $courseid, DEBUG_DEVELOPER);
        }
        unset(self::$coursedeleting[$courseid]);

        if (self::is_indexing_enabled()) {
            try {
                $engine = self::instance()->get_engine();
                $engine->delete_index_for_course($courseid);
            } catch (\moodle_exception $e) {
                debugging('Error deleting search index data for course ' . $courseid . ': ' . $e->getMessage());
            }
        }
    }
}
