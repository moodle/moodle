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
 * modinfolib.php - Functions/classes relating to cached information about module instances on
 * a course.
 * @package    core
 * @subpackage lib
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     sam marshall
 */


// Maximum number of modinfo items to keep in memory cache. Do not increase this to a large
// number because:
// a) modinfo can be big (megabyte range) for some courses
// b) performance of cache will deteriorate if there are very many items in it
if (!defined('MAX_MODINFO_CACHE_SIZE')) {
    define('MAX_MODINFO_CACHE_SIZE', 10);
}


/**
 * Information about a course that is cached in the course table 'modinfo' field (and then in
 * memory) in order to reduce the need for other database queries.
 *
 * This includes information about the course-modules and the sections on the course. It can also
 * include dynamic data that has been updated for the current user.
 */
class course_modinfo extends stdClass {
    // For convenience we store the course object here as it is needed in other parts of code
    private $course;
    // Array of section data from cache
    private $sectioninfo;

    // Existing data fields
    ///////////////////////

    // These are public for backward compatibility. Note: it is not possible to retain BC
    // using PHP magic get methods because behaviour is different with regard to empty().

    /**
     * Course ID
     * @var int
     * @deprecated For new code, use get_course_id instead.
     */
    public $courseid;

    /**
     * User ID
     * @var int
     * @deprecated For new code, use get_user_id instead.
     */
    public $userid;

    /**
     * Array from int (section num, e.g. 0) => array of int (course-module id); this list only
     * includes sections that actually contain at least one course-module
     * @var array
     * @deprecated For new code, use get_sections instead
     */
    public $sections;

    /**
     * Array from int (cm id) => cm_info object
     * @var array
     * @deprecated For new code, use get_cms or get_cm instead.
     */
    public $cms;

    /**
     * Array from string (modname) => int (instance id) => cm_info object
     * @var array
     * @deprecated For new code, use get_instances or get_instances_of instead.
     */
    public $instances;

    /**
     * Groups that the current user belongs to. This value is usually not available (set to null)
     * unless the course has activities set to groupmembersonly. When set, it is an array of
     * grouping id => array of group id => group id. Includes grouping id 0 for 'all groups'.
     * @var array
     * @deprecated Don't use this! For new code, use get_groups.
     */
    public $groups;

    // Get methods for data
    ///////////////////////

    /**
     * @return object Moodle course object that was used to construct this data
     */
    public function get_course() {
        return $this->course;
    }

    /**
     * @return int Course ID
     */
    public function get_course_id() {
        return $this->courseid;
    }

    /**
     * @return int User ID
     */
    public function get_user_id() {
        return $this->userid;
    }

    /**
     * @return array Array from section number (e.g. 0) to array of course-module IDs in that
     *   section; this only includes sections that contain at least one course-module
     */
    public function get_sections() {
        return $this->sections;
    }

    /**
     * @return array Array from course-module instance to cm_info object within this course, in
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
            throw new moodle_exception('invalidcoursemodule', 'error');
        }
        return $this->cms[$cmid];
    }

    /**
     * Obtains all module instances on this course.
     * @return array Array from module name => array from instance id => cm_info
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
        $modnamesused = array();
        foreach ($this->get_cms() as $cmid => $mod) {
            if (isset($modnames[$mod->modname]) && $mod->uservisible) {
                $modnamesused[$mod->modname] = $modnames[$mod->modname];
            }
        }
        collatorlib::asort($modnamesused);
        return $modnamesused;
    }

    /**
     * Obtains all instances of a particular module on this course.
     * @param $modname Name of module (not full frankenstyle) e.g. 'label'
     * @return array Array from instance id => cm_info for modules on this course; empty if none
     */
    public function get_instances_of($modname) {
        if (empty($this->instances[$modname])) {
            return array();
        }
        return $this->instances[$modname];
    }

    /**
     * Returns groups that the current user belongs to on the course. Note: If not already
     * available, this may make a database query.
     * @param int $groupingid Grouping ID or 0 (default) for all groups
     * @return array Array of int (group id) => int (same group id again); empty array if none
     */
    public function get_groups($groupingid=0) {
        if (is_null($this->groups)) {
            // NOTE: Performance could be improved here. The system caches user groups
            // in $USER->groupmember[$courseid] => array of groupid=>groupid. Unfortunately this
            // structure does not include grouping information. It probably could be changed to
            // do so, without a significant performance hit on login, thus saving this one query
            // each request.
            $this->groups = groups_get_user_groups($this->courseid, $this->userid);
        }
        if (!isset($this->groups[$groupingid])) {
            return array();
        }
        return $this->groups[$groupingid];
    }

    /**
     * Gets all sections as array from section number => data about section.
     * @return array Array of section_info objects organised by section number
     */
    public function get_section_info_all() {
        return $this->sectioninfo;
    }

    /**
     * Gets data about specific numbered section.
     * @param int $sectionnumber Number (not id) of section
     * @param int $strictness Use MUST_EXIST to throw exception if it doesn't
     * @return section_info Information for numbered section or null if not found
     */
    public function get_section_info($sectionnumber, $strictness = IGNORE_MISSING) {
        if (!array_key_exists($sectionnumber, $this->sectioninfo)) {
            if ($strictness === MUST_EXIST) {
                throw new moodle_exception('sectionnotexist');
            } else {
                return null;
            }
        }
        return $this->sectioninfo[$sectionnumber];
    }

    /**
     * Constructs based on course.
     * Note: This constructor should not usually be called directly.
     * Use get_fast_modinfo($course) instead as this maintains a cache.
     * @param object $course Moodle course object, which may include modinfo
     * @param int $userid User ID
     */
    public function __construct($course, $userid) {
        global $CFG, $DB;

        // Check modinfo field is set. If not, build and load it.
        if (empty($course->modinfo) || empty($course->sectioncache)) {
            rebuild_course_cache($course->id);
            $course = $DB->get_record('course', array('id'=>$course->id), '*', MUST_EXIST);
        }

        // Set initial values
        $this->courseid = $course->id;
        $this->userid = $userid;
        $this->sections = array();
        $this->cms = array();
        $this->instances = array();
        $this->groups = null;
        $this->course = $course;

        // Load modinfo field into memory as PHP object and check it's valid
        $info = unserialize($course->modinfo);
        if (!is_array($info)) {
            // hmm, something is wrong - lets try to fix it
            rebuild_course_cache($course->id);
            $course->modinfo = $DB->get_field('course', 'modinfo', array('id'=>$course->id));
            $info = unserialize($course->modinfo);
            if (!is_array($info)) {
                // If it still fails, abort
                debugging('Problem with "modinfo" data for this course');
                return;
            }
        }

        // Load sectioncache field into memory as PHP object and check it's valid
        $sectioncache = unserialize($course->sectioncache);
        if (!is_array($sectioncache) || empty($sectioncache)) {
            // hmm, something is wrong - let's fix it
            rebuild_course_cache($course->id);
            $course->sectioncache = $DB->get_field('course', 'sectioncache', array('id'=>$course->id));
            $sectioncache = unserialize($course->sectioncache);
            if (!is_array($sectioncache)) {
                // If it still fails, abort
                debugging('Problem with "sectioncache" data for this course');
                return;
            }
        }

        // If we haven't already preloaded contexts for the course, do it now
        preload_course_contexts($course->id);

        // Loop through each piece of module data, constructing it
        $modexists = array();
        foreach ($info as $mod) {
            if (empty($mod->name)) {
                // something is wrong here
                continue;
            }

            // Skip modules which don't exist
            if (empty($modexists[$mod->mod])) {
                if (!file_exists("$CFG->dirroot/mod/$mod->mod/lib.php")) {
                    continue;
                }
                $modexists[$mod->mod] = true;
            }

            // Construct info for this module
            $cm = new cm_info($this, $course, $mod, $info);

            // Store module in instances and cms array
            if (!isset($this->instances[$cm->modname])) {
                $this->instances[$cm->modname] = array();
            }
            $this->instances[$cm->modname][$cm->instance] = $cm;
            $this->cms[$cm->id] = $cm;

            // Reconstruct sections. This works because modules are stored in order
            if (!isset($this->sections[$cm->sectionnum])) {
                $this->sections[$cm->sectionnum] = array();
            }
            $this->sections[$cm->sectionnum][] = $cm->id;
        }

        // Expand section objects
        $this->sectioninfo = array();
        foreach ($sectioncache as $number => $data) {
            // Calculate sequence
            if (isset($this->sections[$number])) {
                $sequence = implode(',', $this->sections[$number]);
            } else {
                $sequence = '';
            }
            // Expand
            $this->sectioninfo[$number] = new section_info($data, $number, $course->id, $sequence,
                    $this, $userid);
        }

        // We need at least 'dynamic' data from each course-module (this is basically the remaining
        // data which was always present in previous version of get_fast_modinfo, so it's required
        // for BC). Creating it in a second pass is necessary because obtain_dynamic_data sometimes
        // needs to be able to refer to a 'complete' (with basic data) modinfo.
        foreach ($this->cms as $cm) {
            $cm->obtain_dynamic_data();
        }
    }

    /**
     * Builds a list of information about sections on a course to be stored in
     * the course cache. (Does not include information that is already cached
     * in some other way.)
     *
     * Used internally by rebuild_course_cache function; do not use otherwise.
     * @param int $courseid Course ID
     * @return array Information about sections, indexed by section number (not id)
     */
    public static function build_section_cache($courseid) {
        global $DB;

        // Get section data
        $sections = $DB->get_records('course_sections', array('course' => $courseid), 'section',
                'section, id, course, name, summary, summaryformat, sequence, visible, ' .
                'availablefrom, availableuntil, showavailability, groupingid');
        $compressedsections = array();

        $formatoptionsdef = course_get_format($courseid)->section_format_options();
        // Remove unnecessary data and add availability
        foreach ($sections as $number => $section) {
            // Add cached options from course format to $section object
            foreach ($formatoptionsdef as $key => $option) {
                if (!empty($option['cache'])) {
                    $formatoptions = course_get_format($courseid)->get_format_options($section);
                    if (!array_key_exists('cachedefault', $option) || $option['cachedefault'] !== $formatoptions[$key]) {
                        $section->$key = $formatoptions[$key];
                    }
                }
            }
            // Clone just in case it is reused elsewhere
            $compressedsections[$number] = clone($section);
            section_info::convert_for_section_cache($compressedsections[$number]);
        }

        return $compressedsections;
    }
}


/**
 * Data about a single module on a course. This contains most of the fields in the course_modules
 * table, plus additional data when required.
 *
 * This object has many public fields; code should treat all these fields as read-only and set
 * data only using the supplied set functions. Setting the fields directly is not supported
 * and may cause problems later.
 */
class cm_info extends stdClass {
    /**
     * State: Only basic data from modinfo cache is available.
     */
    const STATE_BASIC = 0;

    /**
     * State: Dynamic data is available too.
     */
    const STATE_DYNAMIC = 1;

    /**
     * State: View data (for course page) is available.
     */
    const STATE_VIEW = 2;

    /**
     * Parent object
     * @var course_modinfo
     */
    private $modinfo;

    /**
     * Level of information stored inside this object (STATE_xx constant)
     * @var int
     */
    private $state;

    // Existing data fields
    ///////////////////////

    /**
     * Course-module ID - from course_modules table
     * @var int
     */
    public $id;

    /**
     * Module instance (ID within module table) - from course_modules table
     * @var int
     */
    public $instance;

    /**
     * Course ID - from course_modules table
     * @var int
     */
    public $course;

    /**
     * 'ID number' from course-modules table (arbitrary text set by user) - from
     * course_modules table
     * @var string
     */
    public $idnumber;

    /**
     * Time that this course-module was added (unix time) - from course_modules table
     * @var int
     */
    public $added;

    /**
     * This variable is not used and is included here only so it can be documented.
     * Once the database entry is removed from course_modules, it should be deleted
     * here too.
     * @var int
     * @deprecated Do not use this variable
     */
    public $score;

    /**
     * Visible setting (0 or 1; if this is 0, students cannot see/access the activity) - from
     * course_modules table
     * @var int
     */
    public $visible;

    /**
     * Old visible setting (if the entire section is hidden, the previous value for
     * visible is stored in this field) - from course_modules table
     * @var int
     */
    public $visibleold;

    /**
     * Group mode (one of the constants NONE, SEPARATEGROUPS, or VISIBLEGROUPS) - from
     * course_modules table
     * @var int
     */
    public $groupmode;

    /**
     * Grouping ID (0 = all groupings)
     * @var int
     */
    public $groupingid;

    /**
     * Group members only (if set to 1, only members of a suitable group see this link on the
     * course page; 0 = everyone sees it even if they don't belong to a suitable group)  - from
     * course_modules table
     * @var int
     */
    public $groupmembersonly;

    /**
     * Indent level on course page (0 = no indent) - from course_modules table
     * @var int
     */
    public $indent;

    /**
     * Activity completion setting for this activity, COMPLETION_TRACKING_xx constant - from
     * course_modules table
     * @var int
     */
    public $completion;

    /**
     * Set to the item number (usually 0) if completion depends on a particular
     * grade of this activity, or null if completion does not depend on a grade - from
     * course_modules table
     * @var mixed
     */
    public $completiongradeitemnumber;

    /**
     * 1 if 'on view' completion is enabled, 0 otherwise - from course_modules table
     * @var int
     */
    public $completionview;

    /**
     * Set to a unix time if completion of this activity is expected at a
     * particular time, 0 if no time set - from course_modules table
     * @var int
     */
    public $completionexpected;

    /**
     * Available date for this activity (0 if not set, or set to seconds since epoch; before this
     * date, activity does not display to students) - from course_modules table
     * @var int
     */
    public $availablefrom;

    /**
     * Available until date for this activity (0 if not set, or set to seconds since epoch; from
     * this date, activity does not display to students) - from course_modules table
     * @var int
     */
    public $availableuntil;

    /**
     * When activity is unavailable, this field controls whether it is shown to students (0 =
     * hide completely, 1 = show greyed out with information about when it will be available) -
     * from course_modules table
     * @var int
     */
    public $showavailability;

    /**
     * Controls whether the description of the activity displays on the course main page (in
     * addition to anywhere it might display within the activity itself). 0 = do not show
     * on main page, 1 = show on main page.
     * @var int
     */
    public $showdescription;

    /**
     * Extra HTML that is put in an unhelpful part of the HTML when displaying this module in
     * course page - from cached data in modinfo field
     * @deprecated This is crazy, don't use it. Replaced by ->extraclasses and ->onclick
     * @var string
     */
    public $extra;

    /**
     * Name of icon to use - from cached data in modinfo field
     * @var string
     */
    public $icon;

    /**
     * Component that contains icon - from cached data in modinfo field
     * @var string
     */
    public $iconcomponent;

    /**
     * Name of module e.g. 'forum' (this is the same name as the module's main database
     * table) - from cached data in modinfo field
     * @var string
     */
    public $modname;

    /**
     * ID of module - from course_modules table
     * @var int
     */
    public $module;

    /**
     * Name of module instance for display on page e.g. 'General discussion forum' - from cached
     * data in modinfo field
     * @var string
     */
    public $name;

    /**
     * Section number that this course-module is in (section 0 = above the calendar, section 1
     * = week/topic 1, etc) - from cached data in modinfo field
     * @var string
     */
    public $sectionnum;

    /**
     * Section id - from course_modules table
     * @var int
     */
    public $section;

    /**
     * Availability conditions for this course-module based on the completion of other
     * course-modules (array from other course-module id to required completion state for that
     * module) - from cached data in modinfo field
     * @var array
     */
    public $conditionscompletion;

    /**
     * Availability conditions for this course-module based on course grades (array from
     * grade item id to object with ->min, ->max fields) - from cached data in modinfo field
     * @var array
     */
    public $conditionsgrade;

    /**
     * Availability conditions for this course-module based on user fields
     * @var array
     */
    public $conditionsfield;

    /**
     * True if this course-module is available to students i.e. if all availability conditions
     * are met - obtained dynamically
     * @var bool
     */
    public $available;

    /**
     * If course-module is not available to students, this string gives information about
     * availability which can be displayed to students and/or staff (e.g. 'Available from 3
     * January 2010') for display on main page - obtained dynamically
     * @var string
     */
    public $availableinfo;

    /**
     * True if this course-module is available to the CURRENT user (for example, if current user
     * has viewhiddenactivities capability, they can access the course-module even if it is not
     * visible or not available, so this would be true in that case)
     * @var bool
     */
    public $uservisible;

    /**
     * Module context - hacky shortcut
     * @deprecated
     * @var stdClass
     */
    public $context;


    // New data available only via functions
    ////////////////////////////////////////

    /**
     * @var moodle_url
     */
    private $url;

    /**
     * @var string
     */
    private $content;

    /**
     * @var string
     */
    private $extraclasses;

    /**
     * @var moodle_url full external url pointing to icon image for activity
     */
    private $iconurl;

    /**
     * @var string
     */
    private $onclick;

    /**
     * @var mixed
     */
    private $customdata;

    /**
     * @var string
     */
    private $afterlink;

    /**
     * @var string
     */
    private $afterediticons;

    /**
     * Magic method getter
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name) {
        switch ($name) {
            case 'modplural':
                return $this->get_module_type_name(true);
            case 'modfullname':
                return $this->get_module_type_name();
            default:
                debugging('Invalid cm_info property accessed: '.$name);
                return null;
        }
    }

    /**
     * @return bool True if this module has a 'view' page that should be linked to in navigation
     *   etc (note: modules may still have a view.php file, but return false if this is not
     *   intended to be linked to from 'normal' parts of the interface; this is what label does).
     */
    public function has_view() {
        return !is_null($this->url);
    }

    /**
     * @return moodle_url URL to link to for this module, or null if it doesn't have a view page
     */
    public function get_url() {
        return $this->url;
    }

    /**
     * Obtains content to display on main (view) page.
     * Note: Will collect view data, if not already obtained.
     * @return string Content to display on main page below link, or empty string if none
     */
    public function get_content() {
        $this->obtain_view_data();
        return $this->content;
    }

    /**
     * Note: Will collect view data, if not already obtained.
     * @return string Extra CSS classes to add to html output for this activity on main page
     */
    public function get_extra_classes() {
        $this->obtain_view_data();
        return $this->extraclasses;
    }

    /**
     * @return string Content of HTML on-click attribute. This string will be used literally
     * as a string so should be pre-escaped.
     */
    public function get_on_click() {
        // Does not need view data; may be used by navigation
        return $this->onclick;
    }
    /**
     * @return mixed Optional custom data stored in modinfo cache for this activity, or null if none
     */
    public function get_custom_data() {
        return $this->customdata;
    }

    /**
     * Note: Will collect view data, if not already obtained.
     * @return string Extra HTML code to display after link
     */
    public function get_after_link() {
        $this->obtain_view_data();
        return $this->afterlink;
    }

    /**
     * Note: Will collect view data, if not already obtained.
     * @return string Extra HTML code to display after editing icons (e.g. more icons)
     */
    public function get_after_edit_icons() {
        $this->obtain_view_data();
        return $this->afterediticons;
    }

    /**
     * @param moodle_core_renderer $output Output render to use, or null for default (global)
     * @return moodle_url Icon URL for a suitable icon to put beside this cm
     */
    public function get_icon_url($output = null) {
        global $OUTPUT;
        if (!$output) {
            $output = $OUTPUT;
        }
        // Support modules setting their own, external, icon image
        if (!empty($this->iconurl)) {
            $icon = $this->iconurl;

        // Fallback to normal local icon + component procesing
        } else if (!empty($this->icon)) {
            if (substr($this->icon, 0, 4) === 'mod/') {
                list($modname, $iconname) = explode('/', substr($this->icon, 4), 2);
                $icon = $output->pix_url($iconname, $modname);
            } else {
                if (!empty($this->iconcomponent)) {
                    // Icon  has specified component
                    $icon = $output->pix_url($this->icon, $this->iconcomponent);
                } else {
                    // Icon does not have specified component, use default
                    $icon = $output->pix_url($this->icon);
                }
            }
        } else {
            $icon = $output->pix_url('icon', $this->modname);
        }
        return $icon;
    }

    /**
     * Returns a localised human-readable name of the module type
     *
     * @param bool $plural return plural form
     * @return string
     */
    public function get_module_type_name($plural = false) {
        $modnames = get_module_types_names($plural);
        if (isset($modnames[$this->modname])) {
            return $modnames[$this->modname];
        } else {
            return null;
        }
    }

    /**
     * @return course_modinfo Modinfo object that this came from
     */
    public function get_modinfo() {
        return $this->modinfo;
    }

    /**
     * @return object Moodle course object that was used to construct this data
     */
    public function get_course() {
        return $this->modinfo->get_course();
    }

    // Set functions
    ////////////////

    /**
     * Sets content to display on course view page below link (if present).
     * @param string $content New content as HTML string (empty string if none)
     * @return void
     */
    public function set_content($content) {
        $this->content = $content;
    }

    /**
     * Sets extra classes to include in CSS.
     * @param string $extraclasses Extra classes (empty string if none)
     * @return void
     */
    public function set_extra_classes($extraclasses) {
        $this->extraclasses = $extraclasses;
    }

    /**
     * Sets the external full url that points to the icon being used
     * by the activity. Useful for external-tool modules (lti...)
     * If set, takes precedence over $icon and $iconcomponent
     *
     * @param moodle_url $iconurl full external url pointing to icon image for activity
     * @return void
     */
    public function set_icon_url(moodle_url $iconurl) {
        $this->iconurl = $iconurl;
    }

    /**
     * Sets value of on-click attribute for JavaScript.
     * Note: May not be called from _cm_info_view (only _cm_info_dynamic).
     * @param string $onclick New onclick attribute which should be HTML-escaped
     *   (empty string if none)
     * @return void
     */
    public function set_on_click($onclick) {
        $this->check_not_view_only();
        $this->onclick = $onclick;
    }

    /**
     * Sets HTML that displays after link on course view page.
     * @param string $afterlink HTML string (empty string if none)
     * @return void
     */
    public function set_after_link($afterlink) {
        $this->afterlink = $afterlink;
    }

    /**
     * Sets HTML that displays after edit icons on course view page.
     * @param string $afterediticons HTML string (empty string if none)
     * @return void
     */
    public function set_after_edit_icons($afterediticons) {
        $this->afterediticons = $afterediticons;
    }

    /**
     * Changes the name (text of link) for this module instance.
     * Note: May not be called from _cm_info_view (only _cm_info_dynamic).
     * @param string $name Name of activity / link text
     * @return void
     */
    public function set_name($name) {
        $this->update_user_visible();
        $this->name = $name;
    }

    /**
     * Turns off the view link for this module instance.
     * Note: May not be called from _cm_info_view (only _cm_info_dynamic).
     * @return void
     */
    public function set_no_view_link() {
        $this->check_not_view_only();
        $this->url = null;
    }

    /**
     * Sets the 'uservisible' flag. This can be used (by setting false) to prevent access and
     * display of this module link for the current user.
     * Note: May not be called from _cm_info_view (only _cm_info_dynamic).
     * @param bool $uservisible
     * @return void
     */
    public function set_user_visible($uservisible) {
        $this->check_not_view_only();
        $this->uservisible = $uservisible;
    }

    /**
     * Sets the 'available' flag and related details. This flag is normally used to make
     * course modules unavailable until a certain date or condition is met. (When a course
     * module is unavailable, it is still visible to users who have viewhiddenactivities
     * permission.)
     *
     * When this is function is called, user-visible status is recalculated automatically.
     *
     * Note: May not be called from _cm_info_view (only _cm_info_dynamic).
     * @param bool $available False if this item is not 'available'
     * @param int $showavailability 0 = do not show this item at all if it's not available,
     *   1 = show this item greyed out with the following message
     * @param string $availableinfo Information about why this is not available which displays
     *   to those who have viewhiddenactivities, and to everyone if showavailability is set;
     *   note that this function replaces the existing data (if any)
     * @return void
     */
    public function set_available($available, $showavailability=0, $availableinfo='') {
        $this->check_not_view_only();
        $this->available = $available;
        $this->showavailability = $showavailability;
        $this->availableinfo = $availableinfo;
        $this->update_user_visible();
    }

    /**
     * Some set functions can only be called from _cm_info_dynamic and not _cm_info_view.
     * This is because they may affect parts of this object which are used on pages other
     * than the view page (e.g. in the navigation block, or when checking access on
     * module pages).
     * @return void
     */
    private function check_not_view_only() {
        if ($this->state >= self::STATE_DYNAMIC) {
            throw new coding_exception('Cannot set this data from _cm_info_view because it may ' .
                    'affect other pages as well as view');
        }
    }

    /**
     * Constructor should not be called directly; use get_fast_modinfo.
     * @param course_modinfo $modinfo Parent object
     * @param object $course Course row
     * @param object $mod Module object from the modinfo field of course table
     * @param object $info Entire object from modinfo field of course table
     */
    public function __construct(course_modinfo $modinfo, $course, $mod, $info) {
        global $CFG;
        $this->modinfo = $modinfo;

        $this->id               = $mod->cm;
        $this->instance         = $mod->id;
        $this->course           = $course->id;
        $this->modname          = $mod->mod;
        $this->idnumber         = isset($mod->idnumber) ? $mod->idnumber : '';
        $this->name             = $mod->name;
        $this->visible          = $mod->visible;
        $this->sectionnum       = $mod->section; // Note weirdness with name here
        $this->groupmode        = isset($mod->groupmode) ? $mod->groupmode : 0;
        $this->groupingid       = isset($mod->groupingid) ? $mod->groupingid : 0;
        $this->groupmembersonly = isset($mod->groupmembersonly) ? $mod->groupmembersonly : 0;
        $this->indent           = isset($mod->indent) ? $mod->indent : 0;
        $this->extra            = isset($mod->extra) ? $mod->extra : '';
        $this->extraclasses     = isset($mod->extraclasses) ? $mod->extraclasses : '';
        $this->iconurl          = isset($mod->iconurl) ? $mod->iconurl : '';
        $this->onclick          = isset($mod->onclick) ? $mod->onclick : '';
        $this->content          = isset($mod->content) ? $mod->content : '';
        $this->icon             = isset($mod->icon) ? $mod->icon : '';
        $this->iconcomponent    = isset($mod->iconcomponent) ? $mod->iconcomponent : '';
        $this->customdata       = isset($mod->customdata) ? $mod->customdata : '';
        $this->context          = context_module::instance($mod->cm);
        $this->showdescription  = isset($mod->showdescription) ? $mod->showdescription : 0;
        $this->state = self::STATE_BASIC;

        // This special case handles old label data. Labels used to use the 'name' field for
        // content
        if ($this->modname === 'label' && $this->content === '') {
            $this->content = $this->extra;
            $this->extra = '';
        }

        // Note: These fields from $cm were not present in cm_info in Moodle
        // 2.0.2 and prior. They may not be available if course cache hasn't
        // been rebuilt since then.
        $this->section = isset($mod->sectionid) ? $mod->sectionid : 0;
        $this->module = isset($mod->module) ? $mod->module : 0;
        $this->added = isset($mod->added) ? $mod->added : 0;
        $this->score = isset($mod->score) ? $mod->score : 0;
        $this->visibleold = isset($mod->visibleold) ? $mod->visibleold : 0;

        // Note: it saves effort and database space to always include the
        // availability and completion fields, even if availability or completion
        // are actually disabled
        $this->completion = isset($mod->completion) ? $mod->completion : 0;
        $this->completiongradeitemnumber = isset($mod->completiongradeitemnumber)
                ? $mod->completiongradeitemnumber : null;
        $this->completionview = isset($mod->completionview)
                ? $mod->completionview : 0;
        $this->completionexpected = isset($mod->completionexpected)
                ? $mod->completionexpected : 0;
        $this->showavailability = isset($mod->showavailability) ? $mod->showavailability : 0;
        $this->availablefrom = isset($mod->availablefrom) ? $mod->availablefrom : 0;
        $this->availableuntil = isset($mod->availableuntil) ? $mod->availableuntil : 0;
        $this->conditionscompletion = isset($mod->conditionscompletion)
                ? $mod->conditionscompletion : array();
        $this->conditionsgrade = isset($mod->conditionsgrade)
                ? $mod->conditionsgrade : array();
        $this->conditionsfield = isset($mod->conditionsfield)
                ? $mod->conditionsfield : array();

        static $modviews;
        if (!isset($modviews[$this->modname])) {
            $modviews[$this->modname] = !plugin_supports('mod', $this->modname,
                    FEATURE_NO_VIEW_LINK);
        }
        $this->url = $modviews[$this->modname]
                ? new moodle_url('/mod/' . $this->modname . '/view.php', array('id'=>$this->id))
                : null;
    }

    /**
     * If dynamic data for this course-module is not yet available, gets it.
     *
     * This function is automatically called when constructing course_modinfo, so users don't
     * need to call it.
     *
     * Dynamic data is data which does not come directly from the cache but is calculated at
     * runtime based on the current user. Primarily this concerns whether the user can access
     * the module or not.
     *
     * As part of this function, the module's _cm_info_dynamic function from its lib.php will
     * be called (if it exists).
     * @return void
     */
    public function obtain_dynamic_data() {
        global $CFG;
        if ($this->state >= self::STATE_DYNAMIC) {
            return;
        }
        $userid = $this->modinfo->get_user_id();

        if (!empty($CFG->enableavailability)) {
            // Get availability information
            $ci = new condition_info($this);
            // Note that the modinfo currently available only includes minimal details (basic data)
            // so passing it to this function is a bit dangerous as it would cause infinite
            // recursion if it tried to get dynamic data, however we know that this function only
            // uses basic data.
            $this->available = $ci->is_available($this->availableinfo, true,
                    $userid, $this->modinfo);

            // Check parent section
            $parentsection = $this->modinfo->get_section_info($this->sectionnum);
            if (!$parentsection->available) {
                // Do not store info from section here, as that is already
                // presented from the section (if appropriate) - just change
                // the flag
                $this->available = false;
            }
        } else {
            $this->available = true;
        }

        // Update visible state for current user
        $this->update_user_visible();

        // Let module make dynamic changes at this point
        $this->call_mod_function('cm_info_dynamic');
        $this->state = self::STATE_DYNAMIC;
    }

    /**
     * Works out whether activity is available to the current user
     *
     * If the activity is unavailable, additional checks are required to determine if its hidden or greyed out
     *
     * @see is_user_access_restricted_by_group()
     * @see is_user_access_restricted_by_conditional_access()
     * @return void
     */
    private function update_user_visible() {
        global $CFG;
        $modcontext = context_module::instance($this->id);
        $userid = $this->modinfo->get_user_id();
        $this->uservisible = true;

        // If the user cannot access the activity set the uservisible flag to false.
        // Additional checks are required to determine whether the activity is entirely hidden or just greyed out.
        if ((!$this->visible or !$this->available) and
                !has_capability('moodle/course:viewhiddenactivities', $modcontext, $userid)) {

            $this->uservisible = false;
        }

        // Check group membership.
        if ($this->is_user_access_restricted_by_group()) {

             $this->uservisible = false;
            // Ensure activity is completely hidden from the user.
            $this->showavailability = 0;
        }
    }

    /**
     * Checks whether the module's group settings restrict the current user's access
     *
     * @return bool True if the user access is restricted
     */
    public function is_user_access_restricted_by_group() {
        global $CFG;

        if (!empty($CFG->enablegroupmembersonly) and !empty($this->groupmembersonly)) {
            $modcontext = context_module::instance($this->id);
            $userid = $this->modinfo->get_user_id();
            if (!has_capability('moodle/site:accessallgroups', $modcontext, $userid)) {
                // If the activity has 'group members only' and you don't have accessallgroups...
                $groups = $this->modinfo->get_groups($this->groupingid);
                if (empty($groups)) {
                    // ...and you don't belong to a group, then set it so you can't see/access it
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Checks whether the module's conditional access settings mean that the user cannot see the activity at all
     *
     * @return bool True if the user cannot see the module. False if the activity is either available or should be greyed out.
     */
    public function is_user_access_restricted_by_conditional_access() {
        global $CFG, $USER;

        if (empty($CFG->enableavailability)) {
            return false;
        }

        // If module will always be visible anyway (but greyed out), don't bother checking anything else
        if ($this->showavailability == CONDITION_STUDENTVIEW_SHOW) {
            return false;
        }

        // Can the user see hidden modules?
        $modcontext = context_module::instance($this->id);
        $userid = $this->modinfo->get_user_id();
        if (has_capability('moodle/course:viewhiddenactivities', $modcontext, $userid)) {
            return false;
        }

        // Is the module hidden due to unmet conditions?
        if (!$this->available) {
            return true;
        }

        return false;
    }

    /**
     * Calls a module function (if exists), passing in one parameter: this object.
     * @param string $type Name of function e.g. if this is 'grooblezorb' and the modname is
     *   'forum' then it will try to call 'mod_forum_grooblezorb' or 'forum_grooblezorb'
     * @return void
     */
    private function call_mod_function($type) {
        global $CFG;
        $libfile = $CFG->dirroot . '/mod/' . $this->modname . '/lib.php';
        if (file_exists($libfile)) {
            include_once($libfile);
            $function = 'mod_' . $this->modname . '_' . $type;
            if (function_exists($function)) {
                $function($this);
            } else {
                $function = $this->modname . '_' . $type;
                if (function_exists($function)) {
                    $function($this);
                }
            }
        }
    }

    /**
     * If view data for this course-module is not yet available, obtains it.
     *
     * This function is automatically called if any of the functions (marked) which require
     * view data are called.
     *
     * View data is data which is needed only for displaying the course main page (& any similar
     * functionality on other pages) but is not needed in general. Obtaining view data may have
     * a performance cost.
     *
     * As part of this function, the module's _cm_info_view function from its lib.php will
     * be called (if it exists).
     * @return void
     */
    private function obtain_view_data() {
        if ($this->state >= self::STATE_VIEW) {
            return;
        }

        // Let module make changes at this point
        $this->call_mod_function('cm_info_view');
        $this->state = self::STATE_VIEW;
    }
}


/**
 * Returns reference to full info about modules in course (including visibility).
 * Cached and as fast as possible (0 or 1 db query).
 *
 * use get_fast_modinfo($courseid, 0, true) to reset the static cache for particular course
 * use get_fast_modinfo(0, 0, true) to reset the static cache for all courses
 *
 * @uses MAX_MODINFO_CACHE_SIZE
 * @param int|stdClass $courseorid object from DB table 'course' or just a course id
 * @param int $userid User id to populate 'uservisible' attributes of modules and sections.
 *     Set to 0 for current user (default)
 * @param bool $resetonly whether we want to get modinfo or just reset the cache
 * @return course_modinfo|null Module information for course, or null if resetting
 */
function get_fast_modinfo($courseorid, $userid = 0, $resetonly = false) {
    global $CFG, $USER;
    require_once($CFG->dirroot.'/course/lib.php');

    if (!empty($CFG->enableavailability)) {
        require_once($CFG->libdir.'/conditionlib.php');
    }

    static $cache = array();

    // compartibility with syntax prior to 2.4:
    if ($courseorid === 'reset') {
        debugging("Using the string 'reset' as the first argument of get_fast_modinfo() is deprecated. Use get_fast_modinfo(0,0,true) instead.", DEBUG_DEVELOPER);
        $courseorid = 0;
        $resetonly = true;
    }

    if (is_object($courseorid)) {
        $course = $courseorid;
    } else {
        $course = (object)array('id' => $courseorid, 'modinfo' => null, 'sectioncache' => null);
    }

    // Function is called with $reset = true
    if ($resetonly) {
        if (isset($course->id) && $course->id > 0) {
            $cache[$course->id] = false;
        } else {
            foreach (array_keys($cache) as $key) {
                $cache[$key] = false;
            }
        }
        return null;
    }

    // Function is called with $reset = false, retrieve modinfo
    if (empty($userid)) {
        $userid = $USER->id;
    }

    if (array_key_exists($course->id, $cache)) {
        if ($cache[$course->id] === false) {
            // this course has been recently reset, do not rely on modinfo and sectioncache in $course
            $course->modinfo = null;
            $course->sectioncache = null;
        } else if ($cache[$course->id]->userid == $userid) {
            // this course's modinfo for the same user was recently retrieved, return cached
            return $cache[$course->id];
        }
    }

    if (!property_exists($course, 'modinfo')) {
        debugging('Coding problem - missing course modinfo property in get_fast_modinfo() call');
    }

    if (!property_exists($course, 'sectioncache')) {
        debugging('Coding problem - missing course sectioncache property in get_fast_modinfo() call');
    }

    unset($cache[$course->id]); // prevent potential reference problems when switching users

    $cache[$course->id] = new course_modinfo($course, $userid);

    // Ensure cache does not use too much RAM
    if (count($cache) > MAX_MODINFO_CACHE_SIZE) {
        reset($cache);
        $key = key($cache);
        unset($cache[$key]->instances);
        unset($cache[$key]->cms);
        unset($cache[$key]);
    }

    return $cache[$course->id];
}

/**
 * Rebuilds the cached list of course activities stored in the database
 * @param int $courseid - id of course to rebuild, empty means all
 * @param boolean $clearonly - only clear the modinfo fields, gets rebuild automatically on the fly
 */
function rebuild_course_cache($courseid=0, $clearonly=false) {
    global $COURSE, $SITE, $DB, $CFG;

    // Destroy navigation caches
    navigation_cache::destroy_volatile_caches();

    if (class_exists('format_base')) {
        // if file containing class is not loaded, there is no cache there anyway
        format_base::reset_course_cache($courseid);
    }

    if ($clearonly) {
        if (empty($courseid)) {
            $DB->set_field('course', 'modinfo', null);
            $DB->set_field('course', 'sectioncache', null);
        } else {
            // Clear both fields in one update
            $resetobj = (object)array('id' => $courseid, 'modinfo' => null, 'sectioncache' => null);
            $DB->update_record('course', $resetobj);
        }
        // update cached global COURSE too ;-)
        if ($courseid == $COURSE->id or empty($courseid)) {
            $COURSE->modinfo = null;
            $COURSE->sectioncache = null;
        }
        if ($courseid == $SITE->id) {
            $SITE->modinfo = null;
            $SITE->sectioncache = null;
        }
        // reset the fast modinfo cache
        get_fast_modinfo($courseid, 0, true);
        return;
    }

    require_once("$CFG->dirroot/course/lib.php");

    if ($courseid) {
        $select = array('id'=>$courseid);
    } else {
        $select = array();
        @set_time_limit(0);  // this could take a while!   MDL-10954
    }

    $rs = $DB->get_recordset("course", $select,'','id,fullname');
    foreach ($rs as $course) {
        $modinfo = serialize(get_array_of_activities($course->id));
        $sectioncache = serialize(course_modinfo::build_section_cache($course->id));
        $updateobj = (object)array('id' => $course->id,
                'modinfo' => $modinfo, 'sectioncache' => $sectioncache);
        $DB->update_record("course", $updateobj);
        // update cached global COURSE too ;-)
        if ($course->id == $COURSE->id) {
            $COURSE->modinfo = $modinfo;
            $COURSE->sectioncache = $sectioncache;
        }
        if ($course->id == $SITE->id) {
            $SITE->modinfo = $modinfo;
            $SITE->sectioncache = $sectioncache;
        }
    }
    $rs->close();
    // reset the fast modinfo cache
    get_fast_modinfo($courseid, 0, true);
}


/**
 * Class that is the return value for the _get_coursemodule_info module API function.
 *
 * Note: For backward compatibility, you can also return a stdclass object from that function.
 * The difference is that the stdclass object may contain an 'extra' field (deprecated because
 * it was crazy, except for label which uses it differently). The stdclass object may not contain
 * the new fields defined here (content, extraclasses, customdata).
 */
class cached_cm_info {
    /**
     * Name (text of link) for this activity; Leave unset to accept default name
     * @var string
     */
    public $name;

    /**
     * Name of icon for this activity. Normally, this should be used together with $iconcomponent
     * to define the icon, as per pix_url function.
     * For backward compatibility, if this value is of the form 'mod/forum/icon' then an icon
     * within that module will be used.
     * @see cm_info::get_icon_url()
     * @see renderer_base::pix_url()
     * @var string
     */
    public $icon;

    /**
     * Component for icon for this activity, as per pix_url; leave blank to use default 'moodle'
     * component
     * @see renderer_base::pix_url()
     * @var string
     */
    public $iconcomponent;

    /**
     * HTML content to be displayed on the main page below the link (if any) for this course-module
     * @var string
     */
    public $content;

    /**
     * Custom data to be stored in modinfo for this activity; useful if there are cases when
     * internal information for this activity type needs to be accessible from elsewhere on the
     * course without making database queries. May be of any type but should be short.
     * @var mixed
     */
    public $customdata;

    /**
     * Extra CSS class or classes to be added when this activity is displayed on the main page;
     * space-separated string
     * @var string
     */
    public $extraclasses;

    /**
     * External URL image to be used by activity as icon, useful for some external-tool modules
     * like lti. If set, takes precedence over $icon and $iconcomponent
     * @var $moodle_url
     */
    public $iconurl;

    /**
     * Content of onclick JavaScript; escaped HTML to be inserted as attribute value
     * @var string
     */
    public $onclick;
}


/**
 * Data about a single section on a course. This contains the fields from the
 * course_sections table, plus additional data when required.
 */
class section_info implements IteratorAggregate {
    /**
     * Section ID - from course_sections table
     * @var int
     */
    private $_id;

    /**
     * Course ID - from course_sections table
     * @var int
     */
    private $_course;

    /**
     * Section number - from course_sections table
     * @var int
     */
    private $_section;

    /**
     * Section name if specified - from course_sections table
     * @var string
     */
    private $_name;

    /**
     * Section visibility (1 = visible) - from course_sections table
     * @var int
     */
    private $_visible;

    /**
     * Section summary text if specified - from course_sections table
     * @var string
     */
    private $_summary;

    /**
     * Section summary text format (FORMAT_xx constant) - from course_sections table
     * @var int
     */
    private $_summaryformat;

    /**
     * When section is unavailable, this field controls whether it is shown to students (0 =
     * hide completely, 1 = show greyed out with information about when it will be available) -
     * from course_sections table
     * @var int
     */
    private $_showavailability;

    /**
     * Available date for this section (0 if not set, or set to seconds since epoch; before this
     * date, section does not display to students) - from course_sections table
     * @var int
     */
    private $_availablefrom;

    /**
     * Available until date for this section  (0 if not set, or set to seconds since epoch; from
     * this date, section does not display to students) - from course_sections table
     * @var int
     */
    private $_availableuntil;

    /**
     * If section is restricted to users of a particular grouping, this is its id
     * (0 if not set) - from course_sections table
     * @var int
     */
    private $_groupingid;

    /**
     * Availability conditions for this section based on the completion of
     * course-modules (array from course-module id to required completion state
     * for that module) - from cached data in sectioncache field
     * @var array
     */
    private $_conditionscompletion;

    /**
     * Availability conditions for this section based on course grades (array from
     * grade item id to object with ->min, ->max fields) - from cached data in
     * sectioncache field
     * @var array
     */
    private $_conditionsgrade;

    /**
     * Availability conditions for this section based on user fields
     * @var array
     */
    private $_conditionsfield;

    /**
     * True if this section is available to students i.e. if all availability conditions
     * are met - obtained dynamically
     * @var bool
     */
    private $_available;

    /**
     * If section is not available to students, this string gives information about
     * availability which can be displayed to students and/or staff (e.g. 'Available from 3
     * January 2010') for display on main page - obtained dynamically
     * @var string
     */
    private $_availableinfo;

    /**
     * True if this section is available to the CURRENT user (for example, if current user
     * has viewhiddensections capability, they can access the section even if it is not
     * visible or not available, so this would be true in that case)
     * @var bool
     */
    private $_uservisible;

    /**
     * Default values for sectioncache fields; if a field has this value, it won't
     * be stored in the sectioncache cache, to save space. Checks are done by ===
     * which means values must all be strings.
     * @var array
     */
    private static $sectioncachedefaults = array(
        'name' => null,
        'summary' => '',
        'summaryformat' => '1', // FORMAT_HTML, but must be a string
        'visible' => '1',
        'showavailability' => '0',
        'availablefrom' => '0',
        'availableuntil' => '0',
        'groupingid' => '0',
    );

    /**
     * Stores format options that have been cached when building 'coursecache'
     * When the format option is requested we look first if it has been cached
     * @var array
     */
    private $cachedformatoptions = array();

    /**
     * Constructs object from database information plus extra required data.
     * @param object $data Array entry from cached sectioncache
     * @param int $number Section number (array key)
     * @param int $courseid Course ID
     * @param int $sequence Sequence of course-module ids contained within
     * @param course_modinfo $modinfo Owner (needed for checking availability)
     * @param int $userid User ID
     */
    public function __construct($data, $number, $courseid, $sequence, $modinfo, $userid) {
        global $CFG;

        // Data that is always present
        $this->_id = $data->id;

        $defaults = self::$sectioncachedefaults +
                array('conditionscompletion' => array(),
                    'conditionsgrade' => array(),
                    'conditionsfield' => array());

        // Data that may use default values to save cache size
        foreach ($defaults as $field => $value) {
            if (isset($data->{$field})) {
                $this->{'_'.$field} = $data->{$field};
            } else {
                $this->{'_'.$field} = $value;
            }
        }

        // cached course format data
        $formatoptionsdef = course_get_format($courseid)->section_format_options();
        foreach ($formatoptionsdef as $field => $option) {
            if (!empty($option['cache'])) {
                if (isset($data->{$field})) {
                    $this->cachedformatoptions[$field] = $data->{$field};
                } else if (array_key_exists('cachedefault', $option)) {
                    $this->cachedformatoptions[$field] = $option['cachedefault'];
                }
            }
        }

        // Other data from other places
        $this->_course = $courseid;
        $this->_section = $number;
        $this->_sequence = $sequence;

        // Availability data
        if (!empty($CFG->enableavailability)) {
            // Get availability information
            $ci = new condition_info_section($this);
            $this->_available = $ci->is_available($this->_availableinfo, true,
                    $userid, $modinfo);
            // Display grouping info if available & not already displaying
            // (it would already display if current user doesn't have access)
            // for people with managegroups - same logic/class as grouping label
            // on individual activities.
            $context = context_course::instance($courseid);
            if ($this->_availableinfo === '' && $this->_groupingid &&
                    has_capability('moodle/course:managegroups', $context)) {
                $groupings = groups_get_all_groupings($courseid);
                $this->_availableinfo = html_writer::tag('span', '(' . format_string(
                        $groupings[$this->_groupingid]->name, true, array('context' => $context)) .
                        ')', array('class' => 'groupinglabel'));
            }
        } else {
            $this->_available = true;
        }

        // Update visibility for current user
        $this->update_user_visible($userid);
    }

    /**
     * Magic method to check if the property is set
     *
     * @param string $name name of the property
     * @return bool
     */
    public function __isset($name) {
        if (property_exists($this, '_'.$name)) {
            return isset($this->{'_'.$name});
        }
        $defaultformatoptions = course_get_format($this->_course)->section_format_options();
        if (array_key_exists($name, $defaultformatoptions)) {
            $value = $this->__get($name);
            return isset($value);
        }
        return false;
    }

    /**
     * Magic method to check if the property is empty
     *
     * @param string $name name of the property
     * @return bool
     */
    public function __empty($name) {
        if (property_exists($this, '_'.$name)) {
            return empty($this->{'_'.$name});
        }
        $defaultformatoptions = course_get_format($this->_course)->section_format_options();
        if (array_key_exists($name, $defaultformatoptions)) {
            $value = $this->__get($name);
            return empty($value);
        }
        return true;
    }

    /**
     * Magic method to retrieve the property, this is either basic section property
     * or availability information or additional properties added by course format
     *
     * @param string $name name of the property
     * @return bool
     */
    public function __get($name) {
        if (property_exists($this, '_'.$name)) {
            return $this->{'_'.$name};
        }
        if (array_key_exists($name, $this->cachedformatoptions)) {
            return $this->cachedformatoptions[$name];
        }
        $defaultformatoptions = course_get_format($this->_course)->section_format_options();
        // precheck if the option is defined in format to avoid unnecessary DB queries in get_format_options()
        if (array_key_exists($name, $defaultformatoptions)) {
            $formatoptions = course_get_format($this->_course)->get_format_options($this);
            return $formatoptions[$name];
        }
        debugging('Invalid section_info property accessed! '.$name);
        return null;
    }

    /**
     * Implementation of IteratorAggregate::getIterator(), allows to cycle through properties
     * and use {@link convert_to_array()}
     *
     * @return ArrayIterator
     */
    public function getIterator() {
        $ret = array();
        foreach (get_object_vars($this) as $key => $value) {
            if (substr($key, 0, 1) == '_') {
                $ret[substr($key, 1)] = $this->$key;
            }
        }
        $ret = array_merge($ret, course_get_format($this->_course)->get_format_options($this));
        return new ArrayIterator($ret);
    }

    /**
     * Works out whether activity is visible *for current user* - if this is false, they
     * aren't allowed to access it.
     * @param int $userid User ID
     * @return void
     */
    private function update_user_visible($userid) {
        global $CFG;
        $coursecontext = context_course::instance($this->_course);
        $this->_uservisible = true;
        if ((!$this->_visible || !$this->_available) &&
                !has_capability('moodle/course:viewhiddensections', $coursecontext, $userid)) {
            $this->_uservisible = false;
        }
    }

    /**
     * Prepares section data for inclusion in sectioncache cache, removing items
     * that are set to defaults, and adding availability data if required.
     *
     * Called by build_section_cache in course_modinfo only; do not use otherwise.
     * @param object $section Raw section data object
     */
    public static function convert_for_section_cache($section) {
        global $CFG;

        // Course id stored in course table
        unset($section->course);
        // Section number stored in array key
        unset($section->section);
        // Sequence stored implicity in modinfo $sections array
        unset($section->sequence);

        // Add availability data if turned on
        if ($CFG->enableavailability) {
            require_once($CFG->dirroot . '/lib/conditionlib.php');
            condition_info_section::fill_availability_conditions($section);
            if (count($section->conditionscompletion) == 0) {
                unset($section->conditionscompletion);
            }
            if (count($section->conditionsgrade) == 0) {
                unset($section->conditionsgrade);
            }
        }

        // Remove default data
        foreach (self::$sectioncachedefaults as $field => $value) {
            // Exact compare as strings to avoid problems if some strings are set
            // to "0" etc.
            if (isset($section->{$field}) && $section->{$field} === $value) {
                unset($section->{$field});
            }
        }
    }
}
