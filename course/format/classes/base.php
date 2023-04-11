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
 * Contains the base definition class for any course format plugin.
 *
 * @package   core_courseformat
 * @copyright 2020 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_courseformat;

use navigation_node;
use moodle_page;
use core_component;
use course_modinfo;
use html_writer;
use section_info;
use context_course;
use editsection_form;
use moodle_exception;
use coding_exception;
use moodle_url;
use lang_string;
use completion_info;
use external_api;
use stdClass;
use cache;
use core_courseformat\output\legacy_renderer;

/**
 * Base class for course formats
 *
 * Each course format must declare class
 * class format_FORMATNAME extends core_courseformat\base {}
 * in file lib.php
 *
 * For each course just one instance of this class is created and it will always be returned by
 * course_get_format($courseorid). Format may store it's specific course-dependent options in
 * variables of this class.
 *
 * In rare cases instance of child class may be created just for format without course id
 * i.e. to check if format supports AJAX.
 *
 * Also course formats may extend class section_info and overwrite
 * course_format::build_section_cache() to return more information about sections.
 *
 * If you are upgrading from Moodle 2.3 start with copying the class format_legacy and renaming
 * it to format_FORMATNAME, then move the code from your callback functions into
 * appropriate functions of the class.
 *
 * @package    core_courseformat
 * @copyright  2012 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class base {
    /** @var int Id of the course in this instance (maybe 0) */
    protected $courseid;
    /** @var string format used for this course. Please note that it can be different from
     * course.format field if course referes to non-existing of disabled format */
    protected $format;
    /** @var stdClass course data for course object, please use course_format::get_course() */
    protected $course = false;
    /** @var array caches format options, please use course_format::get_format_options() */
    protected $formatoptions = array();
    /** @var int the section number in single section format, zero for multiple section formats. */
    protected $singlesection = 0;
    /** @var course_modinfo the current course modinfo, please use course_format::get_modinfo() */
    private $modinfo = null;
    /** @var array cached instances */
    private static $instances = array();
    /** @var array plugin name => class name. */
    private static $classesforformat = array('site' => 'site');

    /**
     * Creates a new instance of class
     *
     * Please use course_get_format($courseorid) to get an instance of the format class
     *
     * @param string $format
     * @param int $courseid
     * @return course_format
     */
    protected function __construct($format, $courseid) {
        $this->format = $format;
        $this->courseid = $courseid;
    }

    /**
     * Validates that course format exists and enabled and returns either itself or default format
     *
     * @param string $format
     * @return string
     */
    protected static final function get_format_or_default($format) {
        global $CFG;
        require_once($CFG->dirroot . '/course/lib.php');

        if (array_key_exists($format, self::$classesforformat)) {
            return self::$classesforformat[$format];
        }

        $plugins = get_sorted_course_formats();
        foreach ($plugins as $plugin) {
            self::$classesforformat[$plugin] = $plugin;
        }

        if (array_key_exists($format, self::$classesforformat)) {
            return self::$classesforformat[$format];
        }

        if (PHPUNIT_TEST && class_exists('format_' . $format)) {
            // Allow unittests to use non-existing course formats.
            return $format;
        }

        // Else return default format.
        $defaultformat = get_config('moodlecourse', 'format');
        if (!in_array($defaultformat, $plugins)) {
            // When default format is not set correctly, use the first available format.
            $defaultformat = reset($plugins);
        }
        debugging('Format plugin format_'.$format.' is not found. Using default format_'.$defaultformat, DEBUG_DEVELOPER);

        self::$classesforformat[$format] = $defaultformat;
        return $defaultformat;
    }

    /**
     * Get class name for the format.
     *
     * If course format xxx does not declare class format_xxx, format_legacy will be returned.
     * This function also includes lib.php file from corresponding format plugin
     *
     * @param string $format
     * @return string
     */
    protected static final function get_class_name($format) {
        global $CFG;
        static $classnames = array('site' => 'format_site');
        if (!isset($classnames[$format])) {
            $plugins = core_component::get_plugin_list('format');
            $usedformat = self::get_format_or_default($format);
            if (isset($plugins[$usedformat]) && file_exists($plugins[$usedformat].'/lib.php')) {
                require_once($plugins[$usedformat].'/lib.php');
            }
            $classnames[$format] = 'format_'. $usedformat;
            if (!class_exists($classnames[$format])) {
                require_once($CFG->dirroot.'/course/format/formatlegacy.php');
                $classnames[$format] = 'format_legacy';
            }
        }
        return $classnames[$format];
    }

    /**
     * Returns an instance of the class
     *
     * @todo MDL-35727 use MUC for caching of instances, limit the number of cached instances
     *
     * @param int|stdClass $courseorid either course id or
     *     an object that has the property 'format' and may contain property 'id'
     * @return course_format
     */
    public static final function instance($courseorid) {
        global $DB;
        if (!is_object($courseorid)) {
            $courseid = (int)$courseorid;
            if ($courseid && isset(self::$instances[$courseid]) && count(self::$instances[$courseid]) == 1) {
                $formats = array_keys(self::$instances[$courseid]);
                $format = reset($formats);
            } else {
                $format = $DB->get_field('course', 'format', array('id' => $courseid), MUST_EXIST);
            }
        } else {
            $format = $courseorid->format;
            if (isset($courseorid->id)) {
                $courseid = clean_param($courseorid->id, PARAM_INT);
            } else {
                $courseid = 0;
            }
        }
        // Validate that format exists and enabled, use default otherwise.
        $format = self::get_format_or_default($format);
        if (!isset(self::$instances[$courseid][$format])) {
            $classname = self::get_class_name($format);
            self::$instances[$courseid][$format] = new $classname($format, $courseid);
        }
        return self::$instances[$courseid][$format];
    }

    /**
     * Resets cache for the course (or all caches)
     *
     * To be called from rebuild_course_cache()
     *
     * @param int $courseid
     */
    public static final function reset_course_cache($courseid = 0) {
        if ($courseid) {
            if (isset(self::$instances[$courseid])) {
                foreach (self::$instances[$courseid] as $format => $object) {
                    // In case somebody keeps the reference to course format object.
                    self::$instances[$courseid][$format]->course = false;
                    self::$instances[$courseid][$format]->formatoptions = array();
                    self::$instances[$courseid][$format]->modinfo = null;
                }
                unset(self::$instances[$courseid]);
            }
        } else {
            self::$instances = array();
        }
    }
    /**
     * Reset the current user for all courses.
     *
     * The course format cache resets every time the course cache resets but
     * also when the user changes their language, all course editors
     *
     * @return void
     */
    public static function session_cache_reset_all(): void {
        $statecache = cache::make('core', 'courseeditorstate');
        $statecache->purge();
    }

    /**
     * Reset the current user course format cache.
     *
     * The course format cache resets every time the course cache resets but
     * also when the user changes their course format preference, complete
     * an activity...
     *
     * @param stdClass $course the course object
     * @return string the new statekey
     */
    public static function session_cache_reset(stdClass $course): string {
        $statecache = cache::make('core', 'courseeditorstate');
        $newkey = $course->cacherev . '_' . time();
        $statecache->set($course->id, $newkey);
        return $newkey;
    }

    /**
     * Return the current user course format cache key.
     *
     * The course format session cache can be used to cache the
     * user course representation. The statekey will be reset when the
     * the course state changes. For example when the course is edited,
     * the user completes an activity or simply some course preference
     * like collapsing a section happens.
     *
     * @param stdClass $course the course object
     * @return string the current statekey
     */
    public static function session_cache(stdClass $course): string {
        $statecache = cache::make('core', 'courseeditorstate');
        $statekey = $statecache->get($course->id);
        // Validate the statekey code.
        if (preg_match('/^[0-9]+_[0-9]+$/', $statekey)) {
            list($cacherev) = explode('_', $statekey);
            if ($cacherev == $course->cacherev) {
                return $statekey;
            }
        }
        return self::session_cache_reset($course);
    }

    /**
     * Returns the format name used by this course
     *
     * @return string
     */
    public final function get_format() {
        return $this->format;
    }

    /**
     * Returns id of the course (0 if course is not specified)
     *
     * @return int
     */
    public final function get_courseid() {
        return $this->courseid;
    }

    /**
     * Returns a record from course database table plus additional fields
     * that course format defines
     *
     * @return stdClass
     */
    public function get_course() {
        global $DB;
        if (!$this->courseid) {
            return null;
        }
        if ($this->course === false) {
            $this->course = get_course($this->courseid);
            $options = $this->get_format_options();
            $dbcoursecolumns = null;
            foreach ($options as $optionname => $optionvalue) {
                if (isset($this->course->$optionname)) {
                    // Course format options must not have the same names as existing columns in db table "course".
                    if (!isset($dbcoursecolumns)) {
                        $dbcoursecolumns = $DB->get_columns('course');
                    }
                    if (isset($dbcoursecolumns[$optionname])) {
                        debugging('The option name '.$optionname.' in course format '.$this->format.
                            ' is invalid because the field with the same name exists in {course} table',
                            DEBUG_DEVELOPER);
                        continue;
                    }
                }
                $this->course->$optionname = $optionvalue;
            }
        }
        return $this->course;
    }

    /**
     * Get the course display value for the current course.
     *
     * Formats extending topics or weeks will use coursedisplay as this setting name
     * so they don't need to override the method. However, if the format uses a different
     * display logic it must override this method to ensure the core renderers know
     * if a COURSE_DISPLAY_MULTIPAGE or COURSE_DISPLAY_SINGLEPAGE is being used.
     *
     * @return int The current value (COURSE_DISPLAY_MULTIPAGE or COURSE_DISPLAY_SINGLEPAGE)
     */
    public function get_course_display(): int {
        return $this->get_course()->coursedisplay ?? COURSE_DISPLAY_SINGLEPAGE;
    }

    /**
     * Return the current course modinfo.
     *
     * This method is used mainly by the output components to avoid unnecesary get_fast_modinfo calls.
     *
     * @return course_modinfo
     */
    public function get_modinfo(): course_modinfo {
        global $USER;
        if ($this->modinfo === null) {
            $this->modinfo = course_modinfo::instance($this->courseid, $USER->id);
        }
        return $this->modinfo;
    }

    /**
     * Method used in the rendered and during backup instead of legacy 'numsections'
     *
     * Default renderer will treat sections with sectionnumber greater that the value returned by this
     * method as "orphaned" and not display them on the course page unless in editing mode.
     * Backup will store this value as 'numsections'.
     *
     * This method ensures that 3rd party course format plugins that still use 'numsections' continue to
     * work but at the same time we no longer expect formats to have 'numsections' property.
     *
     * @return int
     */
    public function get_last_section_number() {
        $course = $this->get_course();
        if (isset($course->numsections)) {
            return $course->numsections;
        }
        $modinfo = get_fast_modinfo($course);
        $sections = $modinfo->get_section_info_all();
        return (int)max(array_keys($sections));
    }

    /**
     * Method used to get the maximum number of sections for this course format.
     * @return int
     */
    public function get_max_sections() {
        $maxsections = get_config('moodlecourse', 'maxsections');
        if (!isset($maxsections) || !is_numeric($maxsections)) {
            $maxsections = 52;
        }
        return $maxsections;
    }

    /**
     * Returns true if the course has a front page.
     *
     * This function is called to determine if the course has a view page, whether or not
     * it contains a listing of activities. It can be useful to set this to false when the course
     * format has only one activity and ignores the course page. Or if there are multiple
     * activities but no page to see the centralised information.
     *
     * Initially this was created to know if forms should add a button to return to the course page.
     * So if 'Return to course' does not make sense in your format your should probably return false.
     *
     * @return boolean
     * @since Moodle 2.6
     */
    public function has_view_page() {
        return true;
    }

    /**
     * Generate the title for this section page.
     *
     * @return string the page title
     */
    public function page_title(): string {
        global $PAGE;
        return $PAGE->title;
    }

    /**
     * Returns true if this course format uses sections
     *
     * This function may be called without specifying the course id
     * i.e. in course_format_uses_sections()
     *
     * Developers, note that if course format does use sections there should be defined a language
     * string with the name 'sectionname' defining what the section relates to in the format, i.e.
     * $string['sectionname'] = 'Topic';
     * or
     * $string['sectionname'] = 'Week';
     *
     * @return bool
     */
    public function uses_sections() {
        return false;
    }

    /**
     * Returns true if this course format uses course index
     *
     * This function may be called without specifying the course id
     * i.e. in course_index_drawer()
     *
     * @return bool
     */
    public function uses_course_index() {
        return false;
    }

    /**
     * Returns true if this course format uses activity indentation.
     *
     * @return bool if the course format uses indentation.
     */
    public function uses_indentation(): bool {
        return true;
    }

    /**
     * Returns a list of sections used in the course
     *
     * This is a shortcut to get_fast_modinfo()->get_section_info_all()
     * @see get_fast_modinfo()
     * @see course_modinfo::get_section_info_all()
     *
     * @return array of section_info objects
     */
    public final function get_sections() {
        if ($course = $this->get_course()) {
            $modinfo = get_fast_modinfo($course);
            return $modinfo->get_section_info_all();
        }
        return array();
    }

    /**
     * Returns information about section used in course
     *
     * @param int|stdClass $section either section number (field course_section.section) or row from course_section table
     * @param int $strictness
     * @return section_info
     */
    public final function get_section($section, $strictness = IGNORE_MISSING) {
        if (is_object($section)) {
            $sectionnum = $section->section;
        } else {
            $sectionnum = $section;
        }
        $sections = $this->get_sections();
        if (array_key_exists($sectionnum, $sections)) {
            return $sections[$sectionnum];
        }
        if ($strictness == MUST_EXIST) {
            throw new moodle_exception('sectionnotexist');
        }
        return null;
    }

    /**
     * Returns the display name of the given section that the course prefers.
     *
     * @param int|stdClass $section Section object from database or just field course_sections.section
     * @return Display name that the course format prefers, e.g. "Topic 2"
     */
    public function get_section_name($section) {
        if (is_object($section)) {
            $sectionnum = $section->section;
        } else {
            $sectionnum = $section;
        }

        if (get_string_manager()->string_exists('sectionname', 'format_' . $this->format)) {
            return get_string('sectionname', 'format_' . $this->format) . ' ' . $sectionnum;
        }

        // Return an empty string if there's no available section name string for the given format.
        return '';
    }

    /**
     * Returns the default section using course_format's implementation of get_section_name.
     *
     * @param int|stdClass $section Section object from database or just field course_sections section
     * @return string The default value for the section name based on the given course format.
     */
    public function get_default_section_name($section) {
        return self::get_section_name($section);
    }

    /**
     * Returns the name for the highlighted section.
     *
     * @return string The name for the highlighted section based on the given course format.
     */
    public function get_section_highlighted_name(): string {
        return get_string('highlighted');
    }

    /**
     * Set if the current format instance will show multiple sections or an individual one.
     *
     * Some formats has the hability to swith from one section to multiple sections per page,
     * this method replaces the old print_multiple_section_page and print_single_section_page.
     *
     * @param int $singlesection zero for all sections or a section number
     */
    public function set_section_number(int $singlesection): void {
        $this->singlesection = $singlesection;
    }

    /**
     * Set if the current format instance will show multiple sections or an individual one.
     *
     * Some formats has the hability to swith from one section to multiple sections per page,
     * output components will use this method to know if the current display is a single or
     * multiple sections.
     *
     * @return int zero for all sections or the sectin number
     */
    public function get_section_number(): int {
        return $this->singlesection;
    }

    /**
     * Return the format section preferences.
     *
     * @return array of preferences indexed by sectionid
     */
    public function get_sections_preferences(): array {
        global $USER;

        $result = [];

        $course = $this->get_course();
        $coursesectionscache = cache::make('core', 'coursesectionspreferences');

        $coursesections = $coursesectionscache->get($course->id);
        if ($coursesections) {
            return $coursesections;
        }

        $sectionpreferences = $this->get_sections_preferences_by_preference();

        foreach ($sectionpreferences as $preference => $sectionids) {
            if (!empty($sectionids) && is_array($sectionids)) {
                foreach ($sectionids as $sectionid) {
                    if (!isset($result[$sectionid])) {
                        $result[$sectionid] = new stdClass();
                    }
                    $result[$sectionid]->$preference = true;
                }
            }
        }

        $coursesectionscache->set($course->id, $result);
        return $result;
    }

    /**
     * Return the format section preferences.
     *
     * @return array of preferences indexed by preference name
     */
    public function get_sections_preferences_by_preference(): array {
        global $USER;
        $course = $this->get_course();
        try {
            $sectionpreferences = (array) json_decode(
                get_user_preferences('coursesectionspreferences_' . $course->id, null, $USER->id)
            );
            if (empty($sectionpreferences)) {
                $sectionpreferences = [];
            }
        } catch (\Throwable $e) {
            $sectionpreferences = [];
        }
        return $sectionpreferences;
    }

    /**
     * Return the format section preferences.
     *
     * @param string $preferencename preference name
     * @param int[] $sectionids affected section ids
     *
     */
    public function set_sections_preference(string $preferencename, array $sectionids) {
        global $USER;
        $course = $this->get_course();
        $sectionpreferences = $this->get_sections_preferences_by_preference();
        $sectionpreferences[$preferencename] = $sectionids;
        set_user_preference('coursesectionspreferences_' . $course->id, json_encode($sectionpreferences), $USER->id);
        // Invalidate section preferences cache.
        $coursesectionscache = cache::make('core', 'coursesectionspreferences');
        $coursesectionscache->delete($course->id);
    }

    /**
     * Returns the information about the ajax support in the given source format
     *
     * The returned object's property (boolean)capable indicates that
     * the course format supports Moodle course ajax features.
     *
     * @return stdClass
     */
    public function supports_ajax() {
        // No support by default.
        $ajaxsupport = new stdClass();
        $ajaxsupport->capable = false;
        return $ajaxsupport;
    }

    /**
     * Returns true if this course format is compatible with content components.
     *
     * Using components means the content elements can watch the frontend course state and
     * react to the changes. Formats with component compatibility can have more interactions
     * without refreshing the page, like having drag and drop from the course index to reorder
     * sections and activities.
     *
     * @return bool if the format is compatible with components.
     */
    public function supports_components() {
        return false;
    }


    /**
     * Custom action after section has been moved in AJAX mode
     *
     * Used in course/rest.php
     *
     * @return array This will be passed in ajax respose
     */
    public function ajax_section_move() {
        return null;
    }

    /**
     * The URL to use for the specified course (with section)
     *
     * Please note that course view page /course/view.php?id=COURSEID is hardcoded in many
     * places in core and contributed modules. If course format wants to change the location
     * of the view script, it is not enough to change just this function. Do not forget
     * to add proper redirection.
     *
     * @param int|stdClass $section Section object from database or just field course_sections.section
     *     if null the course view page is returned
     * @param array $options options for view URL. At the moment core uses:
     *     'navigation' (bool) if true and section has no separate page, the function returns null
     *     'sr' (int) used by multipage formats to specify to which section to return
     * @return null|moodle_url
     */
    public function get_view_url($section, $options = array()) {
        global $CFG;
        $course = $this->get_course();
        $url = new moodle_url('/course/view.php', array('id' => $course->id));

        if (array_key_exists('sr', $options)) {
            $sectionno = $options['sr'];
        } else if (is_object($section)) {
            $sectionno = $section->section;
        } else {
            $sectionno = $section;
        }
        if (empty($CFG->linkcoursesections) && !empty($options['navigation']) && $sectionno !== null) {
            // By default assume that sections are never displayed on separate pages.
            return null;
        }
        if ($this->uses_sections() && $sectionno !== null) {
            $url->set_anchor('section-'.$sectionno);
        }
        return $url;
    }

    /**
     * Loads all of the course sections into the navigation
     *
     * This method is called from global_navigation::load_course_sections()
     *
     * By default the method global_navigation::load_generic_course_sections() is called
     *
     * When overwriting please note that navigationlib relies on using the correct values for
     * arguments $type and $key in navigation_node::add()
     *
     * Example of code creating a section node:
     * $sectionnode = $node->add($sectionname, $url, navigation_node::TYPE_SECTION, null, $section->id);
     * $sectionnode->nodetype = navigation_node::NODETYPE_BRANCH;
     *
     * Example of code creating an activity node:
     * $activitynode = $sectionnode->add($activityname, $action, navigation_node::TYPE_ACTIVITY, null, $activity->id, $icon);
     * if (global_navigation::module_extends_navigation($activity->modname)) {
     *     $activitynode->nodetype = navigation_node::NODETYPE_BRANCH;
     * } else {
     *     $activitynode->nodetype = navigation_node::NODETYPE_LEAF;
     * }
     *
     * Also note that if $navigation->includesectionnum is not null, the section with this relative
     * number needs is expected to be loaded
     *
     * @param global_navigation $navigation
     * @param navigation_node $node The course node within the navigation
     */
    public function extend_course_navigation($navigation, navigation_node $node) {
        if ($course = $this->get_course()) {
            $navigation->load_generic_course_sections($course, $node);
        }
        return array();
    }

    /**
     * Returns the list of blocks to be automatically added for the newly created course
     *
     * @see blocks_add_default_course_blocks()
     *
     * @return array of default blocks, must contain two keys BLOCK_POS_LEFT and BLOCK_POS_RIGHT
     *     each of values is an array of block names (for left and right side columns)
     */
    public function get_default_blocks() {
        global $CFG;
        if (isset($CFG->defaultblocks)) {
            return blocks_parse_default_blocks_list($CFG->defaultblocks);
        }
        $blocknames = array(
            BLOCK_POS_LEFT => array(),
            BLOCK_POS_RIGHT => array()
        );
        return $blocknames;
    }

    /**
     * Returns the localised name of this course format plugin
     *
     * @return lang_string
     */
    public final function get_format_name() {
        return new lang_string('pluginname', 'format_'.$this->get_format());
    }

    /**
     * Definitions of the additional options that this course format uses for course
     *
     * This function may be called often, it should be as fast as possible.
     * Avoid using get_string() method, use "new lang_string()" instead
     * It is not recommended to use dynamic or course-dependant expressions here
     * This function may be also called when course does not exist yet.
     *
     * Option names must be different from fields in the {course} talbe or any form elements on
     * course edit form, it may even make sence to use special prefix for them.
     *
     * Each option must have the option name as a key and the array of properties as a value:
     * 'default' - default value for this option (assumed null if not specified)
     * 'type' - type of the option value (PARAM_INT, PARAM_RAW, etc.)
     *
     * Additional properties used by default implementation of
     * course_format::create_edit_form_elements() (calls this method with $foreditform = true)
     * 'label' - localised human-readable label for the edit form
     * 'element_type' - type of the form element, default 'text'
     * 'element_attributes' - additional attributes for the form element, these are 4th and further
     *    arguments in the moodleform::addElement() method
     * 'help' - string for help button. Note that if 'help' value is 'myoption' then the string with
     *    the name 'myoption_help' must exist in the language file
     * 'help_component' - language component to look for help string, by default this the component
     *    for this course format
     *
     * This is an interface for creating simple form elements. If format plugin wants to use other
     * methods such as disableIf, it can be done by overriding create_edit_form_elements().
     *
     * Course format options can be accessed as:
     * $this->get_course()->OPTIONNAME (inside the format class)
     * course_get_format($course)->get_course()->OPTIONNAME (outside of format class)
     *
     * All course options are returned by calling:
     * $this->get_format_options();
     *
     * @param bool $foreditform
     * @return array of options
     */
    public function course_format_options($foreditform = false) {
        return array();
    }

    /**
     * Definitions of the additional options that this course format uses for section
     *
     * See course_format::course_format_options() for return array definition.
     *
     * Additionally section format options may have property 'cache' set to true
     * if this option needs to be cached in get_fast_modinfo(). The 'cache' property
     * is recommended to be set only for fields used in course_format::get_section_name(),
     * course_format::extend_course_navigation() and course_format::get_view_url()
     *
     * For better performance cached options are recommended to have 'cachedefault' property
     * Unlike 'default', 'cachedefault' should be static and not access get_config().
     *
     * Regardless of value of 'cache' all options are accessed in the code as
     * $sectioninfo->OPTIONNAME
     * where $sectioninfo is instance of section_info, returned by
     * get_fast_modinfo($course)->get_section_info($sectionnum)
     * or get_fast_modinfo($course)->get_section_info_all()
     *
     * All format options for particular section are returned by calling:
     * $this->get_format_options($section);
     *
     * @param bool $foreditform
     * @return array
     */
    public function section_format_options($foreditform = false) {
        return array();
    }

    /**
     * Returns the format options stored for this course or course section
     *
     * When overriding please note that this function is called from rebuild_course_cache()
     * and section_info object, therefore using of get_fast_modinfo() and/or any function that
     * accesses it may lead to recursion.
     *
     * @param null|int|stdClass|section_info $section if null the course format options will be returned
     *     otherwise options for specified section will be returned. This can be either
     *     section object or relative section number (field course_sections.section)
     * @return array
     */
    public function get_format_options($section = null) {
        global $DB;
        if ($section === null) {
            $options = $this->course_format_options();
        } else {
            $options = $this->section_format_options();
        }
        if (empty($options)) {
            // There are no option for course/sections anyway, no need to go further.
            return array();
        }
        if ($section === null) {
            // Course format options will be returned.
            $sectionid = 0;
        } else if ($this->courseid && isset($section->id)) {
            // Course section format options will be returned.
            $sectionid = $section->id;
        } else if ($this->courseid && is_int($section) &&
                ($sectionobj = $DB->get_record('course_sections',
                        array('section' => $section, 'course' => $this->courseid), 'id'))) {
            // Course section format options will be returned.
            $sectionid = $sectionobj->id;
        } else {
            // Non-existing (yet) section was passed as an argument
            // default format options for course section will be returned.
            $sectionid = -1;
        }
        if (!array_key_exists($sectionid, $this->formatoptions)) {
            $this->formatoptions[$sectionid] = array();
            // First fill with default values.
            foreach ($options as $optionname => $optionparams) {
                $this->formatoptions[$sectionid][$optionname] = null;
                if (array_key_exists('default', $optionparams)) {
                    $this->formatoptions[$sectionid][$optionname] = $optionparams['default'];
                }
            }
            if ($this->courseid && $sectionid !== -1) {
                // Overwrite the default options values with those stored in course_format_options table
                // nothing can be stored if we are interested in generic course ($this->courseid == 0)
                // or generic section ($sectionid === 0).
                $records = $DB->get_records('course_format_options',
                        array('courseid' => $this->courseid,
                              'format' => $this->format,
                              'sectionid' => $sectionid
                            ), '', 'id,name,value');
                $indexedrecords = [];
                foreach ($records as $record) {
                    $indexedrecords[$record->name] = $record->value;
                }
                foreach ($options as $optionname => $option) {
                    contract_value($this->formatoptions[$sectionid], $indexedrecords, $option, $optionname);
                }
            }
        }
        return $this->formatoptions[$sectionid];
    }

    /**
     * Adds format options elements to the course/section edit form
     *
     * This function is called from course_edit_form::definition_after_data()
     *
     * @param MoodleQuickForm $mform form the elements are added to
     * @param bool $forsection 'true' if this is a section edit form, 'false' if this is course edit form
     * @return array array of references to the added form elements
     */
    public function create_edit_form_elements(&$mform, $forsection = false) {
        $elements = array();
        if ($forsection) {
            $options = $this->section_format_options(true);
        } else {
            $options = $this->course_format_options(true);
        }
        foreach ($options as $optionname => $option) {
            if (!isset($option['element_type'])) {
                $option['element_type'] = 'text';
            }
            $args = array($option['element_type'], $optionname, $option['label']);
            if (!empty($option['element_attributes'])) {
                $args = array_merge($args, $option['element_attributes']);
            }
            $elements[] = call_user_func_array(array($mform, 'addElement'), $args);
            if (isset($option['help'])) {
                $helpcomponent = 'format_'. $this->get_format();
                if (isset($option['help_component'])) {
                    $helpcomponent = $option['help_component'];
                }
                $mform->addHelpButton($optionname, $option['help'], $helpcomponent);
            }
            if (isset($option['type'])) {
                $mform->setType($optionname, $option['type']);
            }
            if (isset($option['default']) && !array_key_exists($optionname, $mform->_defaultValues)) {
                // Set defaults for the elements in the form.
                // Since we call this method after set_data() make sure that we don't override what was already set.
                $mform->setDefault($optionname, $option['default']);
            }
        }

        if (!$forsection && empty($this->courseid)) {
            // Check if course end date form field should be enabled by default.
            // If a default date is provided to the form element, it is magically enabled by default in the
            // MoodleQuickForm_date_time_selector class, otherwise it's disabled by default.
            if (get_config('moodlecourse', 'courseenddateenabled')) {
                // At this stage (this is called from definition_after_data) course data is already set as default.
                // We can not overwrite what is in the database.
                $mform->setDefault('enddate', $this->get_default_course_enddate($mform));
            }
        }

        return $elements;
    }

    /**
     * Override if you need to perform some extra validation of the format options
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @param array $errors errors already discovered in edit form validation
     * @return array of "element_name"=>"error_description" if there are errors,
     *         or an empty array if everything is OK.
     *         Do not repeat errors from $errors param here
     */
    public function edit_form_validation($data, $files, $errors) {
        return array();
    }

    /**
     * Prepares values of course or section format options before storing them in DB
     *
     * If an option has invalid value it is not returned
     *
     * @param array $rawdata associative array of the proposed course/section format options
     * @param int|null $sectionid null if it is course format option
     * @return array array of options that have valid values
     */
    protected function validate_format_options(array $rawdata, int $sectionid = null) : array {
        if (!$sectionid) {
            $allformatoptions = $this->course_format_options(true);
        } else {
            $allformatoptions = $this->section_format_options(true);
        }
        $data = array_intersect_key($rawdata, $allformatoptions);
        foreach ($data as $key => $value) {
            $option = $allformatoptions[$key] + ['type' => PARAM_RAW, 'element_type' => null, 'element_attributes' => [[]]];
            expand_value($data, $data, $option, $key);
            if ($option['element_type'] === 'select' && !array_key_exists($data[$key], $option['element_attributes'][0])) {
                // Value invalid for select element, skip.
                unset($data[$key]);
            }
        }
        return $data;
    }

    /**
     * Validates format options for the course
     *
     * @param array $data data to insert/update
     * @return array array of options that have valid values
     */
    public function validate_course_format_options(array $data) : array {
        return $this->validate_format_options($data);
    }

    /**
     * Updates format options for a course or section
     *
     * If $data does not contain property with the option name, the option will not be updated
     *
     * @param stdClass|array $data return value from moodleform::get_data() or array with data
     * @param null|int $sectionid null if these are options for course or section id (course_sections.id)
     *     if these are options for section
     * @return bool whether there were any changes to the options values
     */
    protected function update_format_options($data, $sectionid = null) {
        global $DB;
        $data = $this->validate_format_options((array)$data, $sectionid);
        if (!$sectionid) {
            $allformatoptions = $this->course_format_options();
            $sectionid = 0;
        } else {
            $allformatoptions = $this->section_format_options();
        }
        if (empty($allformatoptions)) {
            // Nothing to update anyway.
            return false;
        }
        $defaultoptions = array();
        $cached = array();
        foreach ($allformatoptions as $key => $option) {
            $defaultoptions[$key] = null;
            if (array_key_exists('default', $option)) {
                $defaultoptions[$key] = $option['default'];
            }
            expand_value($defaultoptions, $defaultoptions, $option, $key);
            $cached[$key] = ($sectionid === 0 || !empty($option['cache']));
        }
        $records = $DB->get_records('course_format_options',
                array('courseid' => $this->courseid,
                      'format' => $this->format,
                      'sectionid' => $sectionid
                    ), '', 'name,id,value');
        $changed = $needrebuild = false;
        foreach ($defaultoptions as $key => $value) {
            if (isset($records[$key])) {
                if (array_key_exists($key, $data) && $records[$key]->value !== $data[$key]) {
                    $DB->set_field('course_format_options', 'value',
                            $data[$key], array('id' => $records[$key]->id));
                    $changed = true;
                    $needrebuild = $needrebuild || $cached[$key];
                }
            } else {
                if (array_key_exists($key, $data) && $data[$key] !== $value) {
                    $newvalue = $data[$key];
                    $changed = true;
                    $needrebuild = $needrebuild || $cached[$key];
                } else {
                    $newvalue = $value;
                    // We still insert entry in DB but there are no changes from user point of
                    // view and no need to call rebuild_course_cache().
                }
                $DB->insert_record('course_format_options', array(
                    'courseid' => $this->courseid,
                    'format' => $this->format,
                    'sectionid' => $sectionid,
                    'name' => $key,
                    'value' => $newvalue
                ));
            }
        }
        if ($needrebuild) {
            if ($sectionid) {
                // Invalidate the section cache by given section id.
                course_modinfo::purge_course_section_cache_by_id($this->courseid, $sectionid);
                // Partial rebuild sections that have been invalidated.
                rebuild_course_cache($this->courseid, true, true);
            } else {
                // Full rebuild if sectionid is null.
                rebuild_course_cache($this->courseid);
            }
        }
        if ($changed) {
            // Reset internal caches.
            if (!$sectionid) {
                $this->course = false;
            }
            unset($this->formatoptions[$sectionid]);
        }
        return $changed;
    }

    /**
     * Updates format options for a course
     *
     * If $data does not contain property with the option name, the option will not be updated
     *
     * @param stdClass|array $data return value from moodleform::get_data() or array with data
     * @param stdClass $oldcourse if this function is called from update_course()
     *     this object contains information about the course before update
     * @return bool whether there were any changes to the options values
     */
    public function update_course_format_options($data, $oldcourse = null) {
        return $this->update_format_options($data);
    }

    /**
     * Updates format options for a section
     *
     * Section id is expected in $data->id (or $data['id'])
     * If $data does not contain property with the option name, the option will not be updated
     *
     * @param stdClass|array $data return value from moodleform::get_data() or array with data
     * @return bool whether there were any changes to the options values
     */
    public function update_section_format_options($data) {
        $data = (array)$data;
        return $this->update_format_options($data, $data['id']);
    }

    /**
     * Return an instance of moodleform to edit a specified section
     *
     * Default implementation returns instance of editsection_form that automatically adds
     * additional fields defined in course_format::section_format_options()
     *
     * Format plugins may extend editsection_form if they want to have custom edit section form.
     *
     * @param mixed $action the action attribute for the form. If empty defaults to auto detect the
     *              current url. If a moodle_url object then outputs params as hidden variables.
     * @param array $customdata the array with custom data to be passed to the form
     *     /course/editsection.php passes section_info object in 'cs' field
     *     for filling availability fields
     * @return moodleform
     */
    public function editsection_form($action, $customdata = array()) {
        global $CFG;
        require_once($CFG->dirroot. '/course/editsection_form.php');
        if (!array_key_exists('course', $customdata)) {
            $customdata['course'] = $this->get_course();
        }
        return new editsection_form($action, $customdata);
    }

    /**
     * Allows course format to execute code on moodle_page::set_course()
     *
     * @param moodle_page $page instance of page calling set_course
     */
    public function page_set_course(moodle_page $page) {
    }

    /**
     * Allows course format to execute code on moodle_page::set_cm()
     *
     * Current module can be accessed as $page->cm (returns instance of cm_info)
     *
     * @param moodle_page $page instance of page calling set_cm
     */
    public function page_set_cm(moodle_page $page) {
    }

    /**
     * Course-specific information to be output on any course page (usually above navigation bar)
     *
     * Example of usage:
     * define
     * class format_FORMATNAME_XXX implements renderable {}
     *
     * create format renderer in course/format/FORMATNAME/renderer.php, define rendering function:
     * class format_FORMATNAME_renderer extends plugin_renderer_base {
     *     protected function render_format_FORMATNAME_XXX(format_FORMATNAME_XXX $xxx) {
     *         return html_writer::tag('div', 'This is my header/footer');
     *     }
     * }
     *
     * Return instance of format_FORMATNAME_XXX in this function, the appropriate method from
     * plugin renderer will be called
     *
     * @return null|renderable null for no output or object with data for plugin renderer
     */
    public function course_header() {
        return null;
    }

    /**
     * Course-specific information to be output on any course page (usually in the beginning of
     * standard footer)
     *
     * See course_format::course_header() for usage
     *
     * @return null|renderable null for no output or object with data for plugin renderer
     */
    public function course_footer() {
        return null;
    }

    /**
     * Course-specific information to be output immediately above content on any course page
     *
     * See course_format::course_header() for usage
     *
     * @return null|renderable null for no output or object with data for plugin renderer
     */
    public function course_content_header() {
        return null;
    }

    /**
     * Course-specific information to be output immediately below content on any course page
     *
     * See course_format::course_header() for usage
     *
     * @return null|renderable null for no output or object with data for plugin renderer
     */
    public function course_content_footer() {
        return null;
    }

    /**
     * Returns instance of page renderer used by this plugin
     *
     * @param moodle_page $page
     * @return renderer_base
     */
    public function get_renderer(moodle_page $page) {
        try {
            $renderer = $page->get_renderer('format_'. $this->get_format());
        } catch (moodle_exception $e) {
            $formatname = $this->get_format();
            $expectedrenderername = 'format_'. $this->get_format() . '\output\renderer';
            debugging(
                "The '{$formatname}' course format does not define the {$expectedrenderername} renderer class. This is required since Moodle 4.0.",
                 DEBUG_DEVELOPER
            );
            $renderer = new legacy_renderer($page, null);
        }

        return $renderer;
    }

    /**
     * Returns instance of output component used by this plugin
     *
     * @throws coding_exception if the format class does not extends the original core one.
     * @param string $outputname the element to render (section, activity...)
     * @return string the output component classname
     */
    public function get_output_classname(string $outputname): string {
        // The core output class.
        $baseclass = "core_courseformat\\output\\local\\$outputname";

        // Look in this format and any parent formats before we get to the base one.
        $classes = array_merge([get_class($this)], class_parents($this));
        foreach ($classes as $component) {
            if ($component === self::class) {
                break;
            }

            // Because course formats are in the root namespace, there is no need to process the
            // class name - it is already a Frankenstyle component name beginning 'format_'.

            // Check if there is a specific class in this format.
            $outputclass = "$component\\output\\courseformat\\$outputname";
            if (class_exists($outputclass)) {
                // Check that the outputclass is a subclass of the base class.
                if (!is_subclass_of($outputclass, $baseclass)) {
                    throw new coding_exception("The \"$outputclass\" must extend \"$baseclass\"");
                }
                return $outputclass;
            }
        }

        return $baseclass;
    }

    /**
     * Returns true if the specified section is current
     *
     * By default we analyze $course->marker
     *
     * @param int|stdClass|section_info $section
     * @return bool
     */
    public function is_section_current($section) {
        if (is_object($section)) {
            $sectionnum = $section->section;
        } else {
            $sectionnum = $section;
        }
        return ($sectionnum && ($course = $this->get_course()) && $course->marker == $sectionnum);
    }

    /**
     * Returns if an specific section is visible to the current user.
     *
     * Formats can overrride this method to implement any special section logic.
     *
     * @param section_info $section the section modinfo
     * @return bool;
     */
    public function is_section_visible(section_info $section): bool {
        // Previous to Moodle 4.0 thas logic was hardcoded. To prevent errors in the contrib plugins
        // the default logic is the same required for topics and weeks format and still uses
        // a "hiddensections" format setting.
        $course = $this->get_course();
        $hidesections = $course->hiddensections ?? true;
        // Show the section if the user is permitted to access it, OR if it's not available
        // but there is some available info text which explains the reason & should display,
        // OR it is hidden but the course has a setting to display hidden sections as unavailable.
        return $section->uservisible ||
            ($section->visible && !$section->available && !empty($section->availableinfo)) ||
            (!$section->visible && !$hidesections);
    }

    /**
     * return true if the course editor must be displayed.
     *
     * @param array|null $capabilities array of capabilities a user needs to have to see edit controls in general.
     *  If null or not specified, the user needs to have 'moodle/course:manageactivities'
     * @return bool true if edit controls must be displayed
     */
    public function show_editor(?array $capabilities = ['moodle/course:manageactivities']): bool {
        global $PAGE;
        $course = $this->get_course();
        $coursecontext = context_course::instance($course->id);
        if ($capabilities === null) {
            $capabilities = ['moodle/course:manageactivities'];
        }
        return $PAGE->user_is_editing() && has_all_capabilities($capabilities, $coursecontext);
    }

    /**
     * Allows to specify for modinfo that section is not available even when it is visible and conditionally available.
     *
     * Note: affected user can be retrieved as: $section->modinfo->userid
     *
     * Course format plugins can override the method to change the properties $available and $availableinfo that were
     * calculated by conditional availability.
     * To make section unavailable set:
     *     $available = false;
     * To make unavailable section completely hidden set:
     *     $availableinfo = '';
     * To make unavailable section visible with availability message set:
     *     $availableinfo = get_string('sectionhidden', 'format_xxx');
     *
     * @param section_info $section
     * @param bool $available the 'available' propery of the section_info as it was evaluated by conditional availability.
     *     Can be changed by the method but 'false' can not be overridden by 'true'.
     * @param string $availableinfo the 'availableinfo' propery of the section_info as it was evaluated by conditional availability.
     *     Can be changed by the method
     */
    public function section_get_available_hook(section_info $section, &$available, &$availableinfo) {
    }

    /**
     * Whether this format allows to delete sections
     *
     * If format supports deleting sections it is also recommended to define language string
     * 'deletesection' inside the format.
     *
     * Do not call this function directly, instead use course_can_delete_section()
     *
     * @param int|stdClass|section_info $section
     * @return bool
     */
    public function can_delete_section($section) {
        return false;
    }

    /**
     * Deletes a section
     *
     * Do not call this function directly, instead call course_delete_section()
     *
     * @param int|stdClass|section_info $section
     * @param bool $forcedeleteifnotempty if set to false section will not be deleted if it has modules in it.
     * @return bool whether section was deleted
     */
    public function delete_section($section, $forcedeleteifnotempty = false) {
        global $DB;
        if (!$this->uses_sections()) {
            // Not possible to delete section if sections are not used.
            return false;
        }
        if (!is_object($section)) {
            $section = $DB->get_record('course_sections', array('course' => $this->get_courseid(), 'section' => $section),
                'id,section,sequence,summary');
        }
        if (!$section || !$section->section) {
            // Not possible to delete 0-section.
            return false;
        }

        if (!$forcedeleteifnotempty && (!empty($section->sequence) || !empty($section->summary))) {
            return false;
        }

        $course = $this->get_course();

        // Remove the marker if it points to this section.
        if ($section->section == $course->marker) {
            course_set_marker($course->id, 0);
        }

        $lastsection = $DB->get_field_sql('SELECT max(section) from {course_sections}
                            WHERE course = ?', array($course->id));

        // Find out if we need to descrease the 'numsections' property later.
        $courseformathasnumsections = array_key_exists('numsections',
            $this->get_format_options());
        $decreasenumsections = $courseformathasnumsections && ($section->section <= $course->numsections);

        // Move the section to the end.
        move_section_to($course, $section->section, $lastsection, true);

        // Delete all modules from the section.
        foreach (preg_split('/,/', $section->sequence, -1, PREG_SPLIT_NO_EMPTY) as $cmid) {
            course_delete_module($cmid);
        }

        // Delete section and it's format options.
        $DB->delete_records('course_format_options', array('sectionid' => $section->id));
        $DB->delete_records('course_sections', array('id' => $section->id));
        // Invalidate the section cache by given section id.
        course_modinfo::purge_course_section_cache_by_id($course->id, $section->id);
        // Partial rebuild section cache that has been purged.
        rebuild_course_cache($course->id, true, true);

        // Delete section summary files.
        $context = \context_course::instance($course->id);
        $fs = get_file_storage();
        $fs->delete_area_files($context->id, 'course', 'section', $section->id);

        // Descrease 'numsections' if needed.
        if ($decreasenumsections) {
            $this->update_course_format_options(array('numsections' => $course->numsections - 1));
        }

        return true;
    }

    /**
     * Prepares the templateable object to display section name
     *
     * @param \section_info|\stdClass $section
     * @param bool $linkifneeded
     * @param bool $editable
     * @param null|lang_string|string $edithint
     * @param null|lang_string|string $editlabel
     * @return \core\output\inplace_editable
     */
    public function inplace_editable_render_section_name($section, $linkifneeded = true,
                                                         $editable = null, $edithint = null, $editlabel = null) {
        global $USER, $CFG;
        require_once($CFG->dirroot.'/course/lib.php');

        if ($editable === null) {
            $editable = !empty($USER->editing) && has_capability('moodle/course:update',
                    context_course::instance($section->course));
        }

        $displayvalue = $title = get_section_name($section->course, $section);
        if ($linkifneeded) {
            // Display link under the section name if the course format setting is to display one section per page.
            $url = course_get_url($section->course, $section->section, array('navigation' => true));
            if ($url) {
                $displayvalue = html_writer::link($url, $title);
            }
            $itemtype = 'sectionname';
        } else {
            // If $linkifneeded==false, we never display the link (this is used when rendering the section header).
            // Itemtype 'sectionnamenl' (nl=no link) will tell the callback that link should not be rendered -
            // there is no other way callback can know where we display the section name.
            $itemtype = 'sectionnamenl';
        }
        if (empty($edithint)) {
            $edithint = new lang_string('editsectionname');
        }
        if (empty($editlabel)) {
            $editlabel = new lang_string('newsectionname', '', $title);
        }

        return new \core\output\inplace_editable('format_' . $this->format, $itemtype, $section->id, $editable,
            $displayvalue, $section->name, $edithint, $editlabel);
    }

    /**
     * Updates the value in the database and modifies this object respectively.
     *
     * ALWAYS check user permissions before performing an update! Throw exceptions if permissions are not sufficient
     * or value is not legit.
     *
     * @param stdClass $section
     * @param string $itemtype
     * @param mixed $newvalue
     * @return \core\output\inplace_editable
     */
    public function inplace_editable_update_section_name($section, $itemtype, $newvalue) {
        if ($itemtype === 'sectionname' || $itemtype === 'sectionnamenl') {
            $context = context_course::instance($section->course);
            external_api::validate_context($context);
            require_capability('moodle/course:update', $context);

            $newtitle = clean_param($newvalue, PARAM_TEXT);
            if (strval($section->name) !== strval($newtitle)) {
                course_update_section($section->course, $section, array('name' => $newtitle));
            }
            return $this->inplace_editable_render_section_name($section, ($itemtype === 'sectionname'), true);
        }
    }


    /**
     * Returns the default end date value based on the start date.
     *
     * This is the default implementation for course formats, it is based on
     * moodlecourse/courseduration setting. Course formats like format_weeks for
     * example can overwrite this method and return a value based on their internal options.
     *
     * @param moodleform $mform
     * @param array $fieldnames The form - field names mapping.
     * @return int
     */
    public function get_default_course_enddate($mform, $fieldnames = array()) {

        if (empty($fieldnames)) {
            $fieldnames = array('startdate' => 'startdate');
        }

        $startdate = $this->get_form_start_date($mform, $fieldnames);
        $courseduration = intval(get_config('moodlecourse', 'courseduration'));
        if (!$courseduration) {
            // Default, it should be already set during upgrade though.
            $courseduration = YEARSECS;
        }

        return $startdate + $courseduration;
    }

    /**
     * Indicates whether the course format supports the creation of the Announcements forum.
     *
     * For course format plugin developers, please override this to return true if you want the Announcements forum
     * to be created upon course creation.
     *
     * @return bool
     */
    public function supports_news() {
        // For backwards compatibility, check if default blocks include the news_items block.
        $defaultblocks = $this->get_default_blocks();
        foreach ($defaultblocks as $blocks) {
            if (in_array('news_items', $blocks)) {
                return true;
            }
        }
        // Return false by default.
        return false;
    }

    /**
     * Get the start date value from the course settings page form.
     *
     * @param moodleform $mform
     * @param array $fieldnames The form - field names mapping.
     * @return int
     */
    protected function get_form_start_date($mform, $fieldnames) {
        $startdate = $mform->getElementValue($fieldnames['startdate']);
        return $mform->getElement($fieldnames['startdate'])->exportValue($startdate);
    }

    /**
     * Returns whether this course format allows the activity to
     * have "triple visibility state" - visible always, hidden on course page but available, hidden.
     *
     * @param stdClass|cm_info $cm course module (may be null if we are displaying a form for adding a module)
     * @param stdClass|section_info $section section where this module is located or will be added to
     * @return bool
     */
    public function allow_stealth_module_visibility($cm, $section) {
        return false;
    }

    /**
     * Callback used in WS core_course_edit_section when teacher performs an AJAX action on a section (show/hide)
     *
     * Access to the course is already validated in the WS but the callback has to make sure
     * that particular action is allowed by checking capabilities
     *
     * Course formats should register
     *
     * @param stdClass|section_info $section
     * @param string $action
     * @param int $sr the section return
     * @return null|array|stdClass any data for the Javascript post-processor (must be json-encodeable)
     */
    public function section_action($section, $action, $sr) {
        global $PAGE;
        if (!$this->uses_sections() || !$section->section) {
            // No section actions are allowed if course format does not support sections.
            // No actions are allowed on the 0-section by default (overwrite in course format if needed).
            throw new moodle_exception('sectionactionnotsupported', 'core', null, s($action));
        }

        $course = $this->get_course();
        $coursecontext = context_course::instance($course->id);
        $modinfo = $this->get_modinfo();
        $renderer = $this->get_renderer($PAGE);

        if (!($section instanceof section_info)) {
            $section = $modinfo->get_section_info($section->section);
        }

        if ($sr) {
            $this->set_section_number($sr);
        }

        switch($action) {
            case 'hide':
            case 'show':
                require_capability('moodle/course:sectionvisibility', $coursecontext);
                $visible = ($action === 'hide') ? 0 : 1;
                course_update_section($course, $section, array('visible' => $visible));
                break;
            case 'refresh':
                return [
                    'content' => $renderer->course_section_updated($this, $section),
                ];
            default:
                throw new moodle_exception('sectionactionnotsupported', 'core', null, s($action));
        }

        return ['modules' => $this->get_section_modules_updated($section)];
    }

    /**
     * Return an array with all section modules content.
     *
     * This method is used in section_action method to generate the updated modules content
     * after a modinfo change.
     *
     * @param section_info $section the section
     * @return string[] the full modules content.
     */
    protected function get_section_modules_updated(section_info $section): array {
        global $PAGE;

        $modules = [];

        if (!$this->uses_sections() || !$section->section) {
            return $modules;
        }

        // Load the cmlist output from the updated modinfo.
        $renderer = $this->get_renderer($PAGE);
        $modinfo = $this->get_modinfo();
        $coursesections = $modinfo->sections;
        if (array_key_exists($section->section, $coursesections)) {
            foreach ($coursesections[$section->section] as $cmid) {
                $cm = $modinfo->get_cm($cmid);
                $modules[] = $renderer->course_section_updated_cm_item($this, $section, $cm);
            }
        }
        return $modules;
    }

    /**
     * Return the plugin config settings for external functions,
     * in some cases the configs will need formatting or be returned only if the current user has some capabilities enabled.
     *
     * @return array the list of configs
     * @since Moodle 3.5
     */
    public function get_config_for_external() {
        return array();
    }

    /**
     * Course deletion hook.
     *
     * Format plugins can override this method to clean any format specific data and dependencies.
     *
     */
    public function delete_format_data() {
        global $DB;
        $course = $this->get_course();
        // By default, formats store some most display specifics in a user preference.
        $DB->delete_records('user_preferences', ['name' => 'coursesectionspreferences_' . $course->id]);
    }
}
