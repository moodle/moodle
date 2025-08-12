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

use ArrayIterator;
use IteratorAggregate;
use Traversable;
use core\context\course as context_course;
use core_courseformat\sectiondelegate;
use core_courseformat\sectiondelegatemodule;

// phpcs:disable PSR2.Classes.PropertyDeclaration.Underscore

/**
 * Data about a single section on a course.
 *
 * This contains the fields from the.course_sections table, plus additional data when required.
 *
 * @package    core_course
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  Sam Marshall
 * @property-read int $id Section ID - from course_sections table
 * @property-read int $course Course ID - from course_sections table
 * @property-read int $sectionnum Section number - from course_sections table
 * @property-read string $name Section name if specified - from course_sections table
 * @property-read int $visible Section visibility (1 = visible) - from course_sections table
 * @property-read string $summary Section summary text if specified - from course_sections table
 * @property-read int $summaryformat Section summary text format (FORMAT_xx constant) - from course_sections table
 * @property-read string $availability Availability information as JSON string - from course_sections table
 * @property-read string|null $component Optional section delegate component - from course_sections table
 * @property-read int|null $itemid Optional section delegate item id - from course_sections table
 * @property-read array $conditionscompletion Availability conditions for this section based on the completion of
 *    course-modules (array from course-module id to required completion state
 *    for that module) - from cached data in sectioncache field
 * @property-read array $conditionsgrade Availability conditions for this section based on course grades (array from
 *    grade item id to object with ->min, ->max fields) - from cached data in
 *    sectioncache field
 * @property-read array $conditionsfield Availability conditions for this section based on user fields
 * @property-read bool $available True if this section is available to the given user i.e. if all availability conditions
 *    are met - obtained dynamically
 * @property-read string $availableinfo If section is not available to some users, this string gives information about
 *    availability which can be displayed to students and/or staff (e.g. 'Available from 3 January 2010')
 *    for display on main page - obtained dynamically
 * @property-read bool $uservisible True if this section is available to the given user (for example, if current user
 *    has viewhiddensections capability, they can access the section even if it is not
 *    visible or not available, so this would be true in that case) - obtained dynamically
 * @property-read string $sequence Comma-separated list of all modules in the section. Note, this field may not exactly
 *    match course_sections.sequence if later has references to non-existing modules or not modules of not available module types.
 * @property-read course_modinfo $modinfo
 */
class section_info implements IteratorAggregate {
    /**
     * Section ID - from course_sections table
     * @var int
     */
    private $_id;

    /**
     * Section number - from course_sections table
     * @var int
     */
    private $_sectionnum;

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
     * Availability information as JSON string - from course_sections table
     * @var string
     */
    private $_availability;

    /**
     * @var string|null the delegated component if any.
     */
    private ?string $_component = null;

    /**
     * @var int|null the delegated instance item id if any.
     */
    private ?int $_itemid = null;

    /**
     * @var sectiondelegate|null Section delegate instance if any.
     */
    private ?sectiondelegate $_delegateinstance = null;

    /** @var cm_info[]|null Section cm_info activities, null when it is not loaded yet. */
    private array|null $_sequencecminfos = null;

    /**
     * @var bool|null $_isorphan True if the section is orphan for some reason.
     */
    private $_isorphan = null;

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
     * are met - obtained dynamically on request, see function {@see section_info::get_available()}
     * @var bool|null
     */
    private $_available;

    /**
     * If section is not available to some users, this string gives information about
     * availability which can be displayed to students and/or staff (e.g. 'Available from 3
     * January 2010') for display on main page - obtained dynamically on request, see
     * function {@see section_info::get_availableinfo()}
     * @var string
     */
    private $_availableinfo;

    /**
     * True if this section is available to the CURRENT user (for example, if current user
     * has viewhiddensections capability, they can access the section even if it is not
     * visible or not available, so this would be true in that case) - obtained dynamically
     * on request, see function {@see section_info::get_uservisible()}
     * @var bool|null
     */
    private $_uservisible;

    /**
     * Default values for sectioncache fields; if a field has this value, it won't
     * be stored in the sectioncache cache, to save space. Checks are done by ===
     * which means values must all be strings.
     * @var array
     */
    private static $sectioncachedefaults = [
        'name' => null,
        'summary' => '',
        'summaryformat' => '1', // FORMAT_HTML, but must be a string.
        'visible' => '1',
        'availability' => null,
        'component' => null,
        'itemid' => null,
    ];

    /**
     * Stores format options that have been cached when building 'coursecache'
     * When the format option is requested we look first if it has been cached
     * @var array
     */
    private $cachedformatoptions = [];

    /**
     * Stores the list of all possible section options defined in each used course format.
     * @var array
     */
    private static $sectionformatoptions = [];

    /**
     * Stores the modinfo object passed in constructor, may be used when requesting
     * dynamically obtained attributes such as available, availableinfo, uservisible.
     * Also used to retrun information about current course or user.
     * @var course_modinfo
     */
    private $modinfo;

    /**
     * True if has activities, otherwise false.
     * @var bool
     */
    public $hasactivites;

    /**
     * List of class read-only properties' getter methods.
     * Used by magic functions __get(), __isset().
     * @var array
     */
    private static $standardproperties = [
        'section' => 'get_section_number',
    ];

    /**
     * Constructs object from database information plus extra required data.
     * @param object $data Array entry from cached sectioncache
     * @param int $number Section number (array key)
     * @param mixed $notused1 argument not used (informaion is available in $modinfo)
     * @param mixed $notused2 argument not used (informaion is available in $modinfo)
     * @param modinfo $modinfo Owner (needed for checking availability)
     * @param mixed $notused3 argument not used (informaion is available in $modinfo)
     */
    public function __construct($data, $number, $notused1, $notused2, $modinfo, $notused3) {
        global $CFG;
        require_once($CFG->dirroot . '/course/lib.php');

        // Data that is always present.
        $this->_id = $data->id;

        $defaults = self::$sectioncachedefaults + [
            'conditionscompletion' => [],
            'conditionsgrade' => [],
            'conditionsfield' => [],
        ];

        // Data that may use default values to save cache size.
        foreach ($defaults as $field => $value) {
            if (isset($data->{$field})) {
                $this->{'_' . $field} = $data->{$field};
            } else {
                $this->{'_' . $field} = $value;
            }
        }

        // Other data from constructor arguments.
        $this->_sectionnum = $number;
        $this->modinfo = $modinfo;

        // Cached course format data.
        $course = $modinfo->get_course();
        if (!isset(self::$sectionformatoptions[$course->format])) {
            // Store list of section format options defined in each used course format.
            // They do not depend on particular course but only on its format.
            self::$sectionformatoptions[$course->format] =
                    course_get_format($course)->section_format_options();
        }
        foreach (self::$sectionformatoptions[$course->format] as $field => $option) {
            if (!empty($option['cache'])) {
                if (isset($data->{$field})) {
                    $this->cachedformatoptions[$field] = $data->{$field};
                } else if (array_key_exists('cachedefault', $option)) {
                    $this->cachedformatoptions[$field] = $option['cachedefault'];
                }
            }
        }
    }

    /**
     * Magic method to check if the property is set
     *
     * @param string $name name of the property
     * @return bool
     */
    public function __isset($name) {
        if (isset(self::$standardproperties[$name])) {
            $value = $this->__get($name);
            return isset($value);
        }
        if (
            method_exists($this, 'get_' . $name)
            || property_exists($this, '_' . $name)
            || array_key_exists($name, self::$sectionformatoptions[$this->modinfo->get_course()->format])
        ) {
            $value = $this->__get($name);
            return isset($value);
        }
        return false;
    }

    /**
     * Magic method to retrieve the property, this is either basic section property
     * or availability information or additional properties added by course format
     *
     * @param string $name name of the property
     * @return mixed
     */
    public function __get($name) {
        if (isset(self::$standardproperties[$name])) {
            if ($method = self::$standardproperties[$name]) {
                return $this->$method();
            }
        }
        if (method_exists($this, 'get_' . $name)) {
            return $this->{'get_' . $name}();
        }
        if (property_exists($this, '_' . $name)) {
            return $this->{'_' . $name};
        }
        if (array_key_exists($name, $this->cachedformatoptions)) {
            return $this->cachedformatoptions[$name];
        }
        // Precheck if the option is defined in format to avoid unnecessary DB queries in get_format_options().
        if (array_key_exists($name, self::$sectionformatoptions[$this->modinfo->get_course()->format])) {
            $formatoptions = course_get_format($this->modinfo->get_course())->get_format_options($this);
            return $formatoptions[$name];
        }
        debugging('Invalid section_info property accessed! ' . $name);
        return null;
    }

    /**
     * Finds whether this section is available at the moment for the current user.
     *
     * The value can be accessed publicly as $sectioninfo->available, but can be called directly if there
     * is a case when it might be called recursively (you can't call property values recursively).
     *
     * @return bool
     */
    public function get_available() {
        global $CFG;
        $userid = $this->modinfo->get_user_id();
        if ($this->_available !== null || $userid == -1) {
            // Has already been calculated or does not need calculation.
            return $this->_available;
        }
        $this->_available = true;
        $this->_availableinfo = '';
        if (!empty($CFG->enableavailability)) {
            // Get availability information.
            $ci = new \core_availability\info_section($this);
            $this->_available = $ci->is_available(
                $this->_availableinfo,
                true,
                $userid,
                $this->modinfo,
            );
        }

        if ($this->_available) {
            $this->_available = $this->check_delegated_available();
        }
        // Execute the hook from the course format that may override the available/availableinfo properties.
        $currentavailable = $this->_available;

        course_get_format($this->modinfo->get_course())
            ->section_get_available_hook($this, $this->_available, $this->_availableinfo);

        if (!$currentavailable && $this->_available) {
            debugging('section_get_available_hook() can not make unavailable section available', DEBUG_DEVELOPER);
            $this->_available = $currentavailable;
        }
        return $this->_available;
    }

    /**
     * Check if the delegated component is available.
     *
     * @return bool
     */
    private function check_delegated_available(): bool {
        /** @var sectiondelegatemodule $sectiondelegate */
        $sectiondelegate = $this->get_component_instance();
        if (!$sectiondelegate) {
            return true;
        }

        if ($sectiondelegate instanceof sectiondelegatemodule) {
            $parentcm = $sectiondelegate->get_cm();
            if (!$parentcm->available) {
                return false;
            }
            return $parentcm->get_section_info()->available;
        }

        return true;
    }

    /**
     * Returns the availability text shown next to the section on course page.
     *
     * @return string
     */
    private function get_availableinfo() {
        // Calling get_available() will also fill the availableinfo property
        // (or leave it null if there is no userid).
        $this->get_available();
        return $this->_availableinfo;
    }

    #[\Override]
    public function getIterator(): Traversable {
        $ret = [];
        foreach (get_object_vars($this) as $key => $value) {
            if (substr($key, 0, 1) == '_') {
                if (method_exists($this, 'get' . $key)) {
                    $ret[substr($key, 1)] = $this->{'get' . $key}();
                } else {
                    $ret[substr($key, 1)] = $this->$key;
                }
            }
        }
        $ret['sequence'] = $this->get_sequence();
        $ret['course'] = $this->get_course();
        $ret = array_merge($ret, course_get_format($this->modinfo->get_course())->get_format_options($this));
        return new ArrayIterator($ret);
    }

    /**
     * Works out whether activity is visible *for current user* - if this is false, they
     * aren't allowed to access it.
     *
     * @return bool
     */
    private function get_uservisible() {
        $userid = $this->modinfo->get_user_id();
        if ($this->_uservisible !== null || $userid == -1) {
            // Has already been calculated or does not need calculation.
            return $this->_uservisible;
        }

        if (!$this->check_delegated_uservisible()) {
            $this->_uservisible = false;
            return $this->_uservisible;
        }

        $this->_uservisible = true;
        if ($this->is_orphan() || !$this->_visible || !$this->get_available()) {
            $coursecontext = context_course::instance($this->get_course());
            if (
                ($this->_isorphan || !$this->_visible)
                && !has_capability('moodle/course:viewhiddensections', $coursecontext, $userid)
            ) {
                $this->_uservisible = false;
            }
            if (
                $this->_uservisible
                && !$this->get_available()
                && !has_capability('moodle/course:ignoreavailabilityrestrictions', $coursecontext, $userid)
            ) {
                $this->_uservisible = false;
            }
        }
        return $this->_uservisible;
    }

    /**
     * Check if the delegated component is user visible.
     *
     * @return bool
     */
    private function check_delegated_uservisible(): bool {
        /** @var sectiondelegatemodule $sectiondelegate */
        $sectiondelegate = $this->get_component_instance();
        if (!$sectiondelegate) {
            return true;
        }

        if ($sectiondelegate instanceof sectiondelegatemodule) {
            $parentcm = $sectiondelegate->get_cm();
            if (!$parentcm->uservisible) {
                return false;
            }
            $result = $parentcm->get_section_info()->uservisible;
            return $result;
        }

        return true;
    }

    /**
     * Restores the course_sections.sequence value
     *
     * @return string
     */
    private function get_sequence() {
        if (!empty($this->modinfo->sections[$this->_sectionnum])) {
            return implode(',', $this->modinfo->sections[$this->_sectionnum]);
        } else {
            return '';
        }
    }

    /**
     * Returns the course modules in this section.
     *
     * @return cm_info[]
     */
    public function get_sequence_cm_infos(): array {
        if ($this->_sequencecminfos !== null) {
            return $this->_sequencecminfos;
        }
        $sequence = $this->modinfo->sections[$this->_sectionnum] ?? [];
        $cms = $this->modinfo->get_cms();
        $result = [];
        foreach ($sequence as $cmid) {
            if (isset($cms[$cmid])) {
                $result[] = $cms[$cmid];
            }
        }
        $this->_sequencecminfos = $result;
        return $result;
    }

    /**
     * Returns course ID - from course_sections table
     *
     * @return int
     */
    private function get_course() {
        return $this->modinfo->get_course_id();
    }

    /**
     * Modinfo object
     *
     * @return course_modinfo
     */
    private function get_modinfo() {
        return $this->modinfo;
    }

    /**
     * Returns section number.
     *
     * This method is called by the property ->section.
     *
     * @return int
     */
    private function get_section_number(): int {
        return $this->sectionnum;
    }

    /**
     * Get the delegate component instance.
     */
    public function get_component_instance(): ?sectiondelegate {
        if (!$this->is_delegated()) {
            return null;
        }
        if ($this->_delegateinstance !== null) {
            return $this->_delegateinstance;
        }
        $this->_delegateinstance = sectiondelegate::instance($this);
        return $this->_delegateinstance;
    }

    /**
     * Returns true if this section is a delegate to a component.
     * @return bool
     */
    public function is_delegated(): bool {
        return !empty($this->_component);
    }

    /**
     * Returns true if this section is orphan.
     *
     * @return bool
     */
    public function is_orphan(): bool {
        if ($this->_isorphan !== null) {
            return $this->_isorphan;
        }

        $courseformat = course_get_format($this->modinfo->get_course());
        // There are some cases where a restored course using third-party formats can
        // have orphaned sections due to a fixed section number.
        if ($this->_sectionnum > $courseformat->get_last_section_number()) {
            $this->_isorphan = true;
            return $this->_isorphan;
        }
        // Some delegated sections can belong to a plugin that is disabled or not present.
        if ($this->is_delegated() && !$this->get_component_instance()) {
            $this->_isorphan = true;
            return $this->_isorphan;
        }

        $this->_isorphan = false;
        return $this->_isorphan;
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

        // Course id stored in course table.
        unset($section->course);
        // Sequence stored implicity in modinfo $sections array.
        unset($section->sequence);

        // Remove default data.
        foreach (self::$sectioncachedefaults as $field => $value) {
            // Exact compare as strings to avoid problems if some strings are set to "0" etc.
            if (isset($section->{$field}) && $section->{$field} === $value) {
                unset($section->{$field});
            }
        }
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(section_info::class, \section_info::class);
