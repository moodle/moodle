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
    /** @var array cached instances */
    private static $instances = array();

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
     * Validates course format and returns either itself or default format name
     *
     * @param string $format
     * @return string
     */
    protected static final function get_used_format($format) {
        if ($format === 'site') {
            return $format;
        }
        $plugins = get_plugin_list('format'); // TODO filter only enabled
        if (isset($plugins[$format])) {
            return $format;
        }
        // Else return default format
        $defaultformat = reset($plugins); // TODO get default format from config
        debugging('Format plugin format_'.$format.' is not found or is not enabled. Using default format_'.$defaultformat);
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
            $plugins = get_plugin_list('format');
            $usedformat = self::get_used_format($format);
            if (file_exists($plugins[$usedformat].'/lib.php')) {
                require_once $plugins[$usedformat].'/lib.php';
            }
            $classnames[$format] = 'format_'. $usedformat;
            if (!class_exists($classnames[$format])) {
                require_once $CFG->dirroot.'/course/format/formatlegacy.php';
                $classnames[$format] = 'format_legacy';
            }
        }
        return $classnames[$format];
    }

    /**
     * Returns an instance of the class
     *
     * @todo use MUC for caching of instances, limit the number of cached instances
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
                $format = reset(array_keys(self::$instances[$courseid]));
            } else {
                $format = $DB->get_field('course', 'format', array('id' => $courseid), MUST_EXIST);
            }
        } else {
            $format = $courseorid->format;
            if (isset($courseorid->id)) {
                $courseid = (int)$courseorid->id;
            } else {
                $courseid = 0;
            }
        }
        // validate that format exists and enabled, use default otherwise
        $format = self::get_used_format($format);
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
            $this->course = $DB->get_record('course', array('id' => $this->courseid));
        }
        return $this->course;
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
        return get_string('sectionname', 'format_'.$this->format) . ' ' . $sectionnum;
    }
    
    /**
     * Returns the information about the ajax support in the given source format
     *
     * The returned object's property (boolean)capable indicates that
     * the course format supports Moodle course ajax features.
     * The property (array)testedbrowsers can be used as a parameter for {@see ajaxenabled()}.
     *
     * @return stdClass
     */
    public function supports_ajax() {
        // no support by default
        $ajaxsupport = new stdClass();
        $ajaxsupport->capable = false;
        $ajaxsupport->testedbrowsers = array();
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
        $course = $this->get_course();
        $url = new moodle_url('/course/view.php', array('id' => $course->id));

        $sr = null;
        if (array_key_exists('sr', $options)) {
            $sr = $options['sr'];
        }
        if (is_object($section)) {
            $sectionno = $section->section;
        } else {
            $sectionno = $section;
        }
        if ($sectionno !== null) {
            if ($sr !== null) {
                if ($sr) {
                    $usercoursedisplay = COURSE_DISPLAY_MULTIPAGE;
                    $sectionno = $sr;
                } else {
                    $usercoursedisplay = COURSE_DISPLAY_SINGLEPAGE;
                }
            } else {
                $usercoursedisplay = $course->coursedisplay;
            }
            if ($sectionno != 0 && $usercoursedisplay == COURSE_DISPLAY_MULTIPAGE) {
                $url->param('section', $sectionno);
            } else {
                if (!empty($options['navigation'])) {
                    return null;
                }
                $url->set_anchor('section-'.$sectionno);
            }
        }
        return $url;
    }

    /**
     * Loads all of the course sections into the navigation
     *
     * By default the method {@link global_navigation::load_generic_course_sections()} is called
     *
     * @param global_navigation $navigation
     * @param navigation_node $node The course node within the navigation
     * @return array Array of sections where each element also contains the element 'sectionnode'
     *     referring to the corresponding section node
     */
    public function extend_course_navigation(&$navigation, navigation_node $node) {
        if ($course = $this->get_course()) {
            return $navigation->load_generic_course_sections($course, $node);
        }
        return array();
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
        return new moodle_url('/');
    }
}

