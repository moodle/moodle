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
 * Base class for course format plugins
 *
 * @package    core_course
 * @copyright  2012 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Returns an instance of format class (extending format_base) for given course
 *
 * @param int|stdClass $courseorid either course id or
 *     an object that has the property 'format' and may contain property 'id'
 * @return format_base
 */
function course_get_format($courseorid) {
    return format_base::instance($courseorid);
}

/**
 * Base class for course formats
 *
 * Each course format must declare class
 * class format_FORMATNAME extends format_base {}
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
 * format_base::build_section_cache() to return more information about sections.
 *
 * If you are upgrading from Moodle 2.3 start with copying the class format_legacy and renaming
 * it to format_FORMATNAME, then move the code from your callback functions into
 * appropriate functions of the class.
 *
 * @package    core_course
 * @copyright  2012 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class format_base {
    /** @var int Id of the course in this instance (maybe 0) */
    protected $courseid;
    /** @var string format used for this course. Please note that it can be different from
     * course.format field if course referes to non-existing of disabled format */
    protected $format;
    /** @var stdClass data for course object, please use {@link format_base::get_course()} */
    protected $course = false;
    /** @var array caches format options, please use {@link format_base::get_format_options()} */
    protected $formatoptions = array();
    /** @var array cached instances */
    private static $instances = array();
    /** @var array plugin name => class name. */
    private static $classesforformat = array('site' => 'site');

    /**
     * Creates a new instance of class
     *
     * Please use {@link course_get_format($courseorid)} to get an instance of the format class
     *
     * @param string $format
     * @param int $courseid
     * @return format_base
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

        // Else return default format
        $defaultformat = get_config('moodlecourse', 'format');
        if (!in_array($defaultformat, $plugins)) {
            // when default format is not set correctly, use the first available format
            $defaultformat = reset($plugins);
        }
        debugging('Format plugin format_'.$format.' is not found. Using default format_'.$defaultformat, DEBUG_DEVELOPER);

        self::$classesforformat[$format] = $defaultformat;
        return $defaultformat;
    }

    /**
     * Get class name for the format
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
     * @return format_base
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
        // validate that format exists and enabled, use default otherwise
        $format = self::get_format_or_default($format);
        if (!isset(self::$instances[$courseid][$format])) {
            $classname = self::get_class_name($format);
            self::$instances[$courseid][$format] = new $classname($format, $courseid);
        }
        return self::$instances[$courseid][$format];
    }

    /**
     * Resets cache for the course (or all caches)
     * To be called from {@link rebuild_course_cache()}
     *
     * @param int $courseid
     */
    public static final function reset_course_cache($courseid = 0) {
        if ($courseid) {
            if (isset(self::$instances[$courseid])) {
                foreach (self::$instances[$courseid] as $format => $object) {
                    // in case somebody keeps the reference to course format object
                    self::$instances[$courseid][$format]->course = false;
                    self::$instances[$courseid][$format]->formatoptions = array();
                }
                unset(self::$instances[$courseid]);
            }
        } else {
            self::$instances = array();
        }
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
     * Returns true if this course format uses sections
     *
     * This function may be called without specifying the course id
     * i.e. in {@link course_format_uses_sections()}
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
     * Returns the default section using format_base's implementation of get_section_name.
     *
     * @param int|stdClass $section Section object from database or just field course_sections section
     * @return string The default value for the section name based on the given course format.
     */
    public function get_default_section_name($section) {
        return self::get_section_name($section);
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
        // no support by default
        $ajaxsupport = new stdClass();
        $ajaxsupport->capable = false;
        return $ajaxsupport;
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
            // by default assume that sections are never displayed on separate pages
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
     * This method is called from {@link global_navigation::load_course_sections()}
     *
     * By default the method {@link global_navigation::load_generic_course_sections()} is called
     *
     * When overwriting please note that navigationlib relies on using the correct values for
     * arguments $type and $key in {@link navigation_node::add()}
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
            BLOCK_POS_RIGHT => array('search_forums', 'news_items', 'calendar_upcoming', 'recent_activity')
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
     * {@link format_base::create_edit_form_elements()} (calls this method with $foreditform = true)
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
     * See {@link format_base::course_format_options()} for return array definition.
     *
     * Additionally section format options may have property 'cache' set to true
     * if this option needs to be cached in {@link get_fast_modinfo()}. The 'cache' property
     * is recommended to be set only for fields used in {@link format_base::get_section_name()},
     * {@link format_base::extend_course_navigation()} and {@link format_base::get_view_url()}
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
            // there are no option for course/sections anyway, no need to go further
            return array();
        }
        if ($section === null) {
            // course format options will be returned
            $sectionid = 0;
        } else if ($this->courseid && isset($section->id)) {
            // course section format options will be returned
            $sectionid = $section->id;
        } else if ($this->courseid && is_int($section) &&
                ($sectionobj = $DB->get_record('course_sections',
                        array('section' => $section, 'course' => $this->courseid), 'id'))) {
            // course section format options will be returned
            $sectionid = $sectionobj->id;
        } else {
            // non-existing (yet) section was passed as an argument
            // default format options for course section will be returned
            $sectionid = -1;
        }
        if (!array_key_exists($sectionid, $this->formatoptions)) {
            $this->formatoptions[$sectionid] = array();
            // first fill with default values
            foreach ($options as $optionname => $optionparams) {
                $this->formatoptions[$sectionid][$optionname] = null;
                if (array_key_exists('default', $optionparams)) {
                    $this->formatoptions[$sectionid][$optionname] = $optionparams['default'];
                }
            }
            if ($this->courseid && $sectionid !== -1) {
                // overwrite the default options values with those stored in course_format_options table
                // nothing can be stored if we are interested in generic course ($this->courseid == 0)
                // or generic section ($sectionid === 0)
                $records = $DB->get_records('course_format_options',
                        array('courseid' => $this->courseid,
                              'format' => $this->format,
                              'sectionid' => $sectionid
                            ), '', 'id,name,value');
                foreach ($records as $record) {
                    if (array_key_exists($record->name, $this->formatoptions[$sectionid])) {
                        $value = $record->value;
                        if ($value !== null && isset($options[$record->name]['type'])) {
                            // this will convert string value to number if needed
                            $value = clean_param($value, $options[$record->name]['type']);
                        }
                        $this->formatoptions[$sectionid][$record->name] = $value;
                    }
                }
            }
        }
        return $this->formatoptions[$sectionid];
    }

    /**
     * Adds format options elements to the course/section edit form
     *
     * This function is called from {@link course_edit_form::definition_after_data()}
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
            if (is_null($mform->getElementValue($optionname)) && isset($option['default'])) {
                $mform->setDefault($optionname, $option['default']);
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
     * Updates format options for a course or section
     *
     * If $data does not contain property with the option name, the option will not be updated
     *
     * @param stdClass|array $data return value from {@link moodleform::get_data()} or array with data
     * @param null|int null if these are options for course or section id (course_sections.id)
     *     if these are options for section
     * @return bool whether there were any changes to the options values
     */
    protected function update_format_options($data, $sectionid = null) {
        global $DB;
        if (!$sectionid) {
            $allformatoptions = $this->course_format_options();
            $sectionid = 0;
        } else {
            $allformatoptions = $this->section_format_options();
        }
        if (empty($allformatoptions)) {
            // nothing to update anyway
            return false;
        }
        $defaultoptions = array();
        $cached = array();
        foreach ($allformatoptions as $key => $option) {
            $defaultoptions[$key] = null;
            if (array_key_exists('default', $option)) {
                $defaultoptions[$key] = $option['default'];
            }
            $cached[$key] = ($sectionid === 0 || !empty($option['cache']));
        }
        $records = $DB->get_records('course_format_options',
                array('courseid' => $this->courseid,
                      'format' => $this->format,
                      'sectionid' => $sectionid
                    ), '', 'name,id,value');
        $changed = $needrebuild = false;
        $data = (array)$data;
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
                    // we still insert entry in DB but there are no changes from user point of
                    // view and no need to call rebuild_course_cache()
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
            rebuild_course_cache($this->courseid, true);
        }
        if ($changed) {
            // reset internal caches
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
     * @param stdClass|array $data return value from {@link moodleform::get_data()} or array with data
     * @param stdClass $oldcourse if this function is called from {@link update_course()}
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
     * @param stdClass|array $data return value from {@link moodleform::get_data()} or array with data
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
     * additional fields defined in {@link format_base::section_format_options()}
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
        $context = context_course::instance($this->courseid);
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
     * See {@link format_base::course_header()} for usage
     *
     * @return null|renderable null for no output or object with data for plugin renderer
     */
    public function course_footer() {
        return null;
    }

    /**
     * Course-specific information to be output immediately above content on any course page
     *
     * See {@link format_base::course_header()} for usage
     *
     * @return null|renderable null for no output or object with data for plugin renderer
     */
    public function course_content_header() {
        return null;
    }

    /**
     * Course-specific information to be output immediately below content on any course page
     *
     * See {@link format_base::course_header()} for usage
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
        return $page->get_renderer('format_'. $this->get_format());
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
     * Do not call this function directly, instead use {@link course_can_delete_section()}
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
     * Do not call this function directly, instead call {@link course_delete_section()}
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
        rebuild_course_cache($course->id, true);

        // Descrease 'numsections' if needed.
        if ($decreasenumsections) {
            $this->update_course_format_options(array('numsections' => $course->numsections - 1));
        }

        return true;
    }
}

/**
 * Pseudo course format used for the site main page
 *
 * @package    core_course
 * @copyright  2012 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class format_site extends format_base {

    /**
     * Returns the display name of the given section that the course prefers.
     *
     * @param int|stdClass $section Section object from database or just field section.section
     * @return Display name that the course format prefers, e.g. "Topic 2"
     */
    function get_section_name($section) {
        return get_string('site');
    }

    /**
     * For this fake course referring to the whole site, the site homepage is always returned
     * regardless of arguments
     *
     * @param int|stdClass $section
     * @param array $options
     * @return null|moodle_url
     */
    public function get_view_url($section, $options = array()) {
        return new moodle_url('/', array('redirect' => 0));
    }

    /**
     * Returns the list of blocks to be automatically added on the site frontpage when moodle is installed
     *
     * @return array of default blocks, must contain two keys BLOCK_POS_LEFT and BLOCK_POS_RIGHT
     *     each of values is an array of block names (for left and right side columns)
     */
    public function get_default_blocks() {
        return blocks_get_default_site_course_blocks();
    }

    /**
     * Definitions of the additional options that site uses
     *
     * @param bool $foreditform
     * @return array of options
     */
    public function course_format_options($foreditform = false) {
        static $courseformatoptions = false;
        if ($courseformatoptions === false) {
            $courseformatoptions = array(
                'numsections' => array(
                    'default' => 1,
                    'type' => PARAM_INT,
                ),
            );
        }
        return $courseformatoptions;
    }
}
