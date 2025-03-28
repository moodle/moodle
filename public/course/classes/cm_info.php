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
use core\exception\coding_exception;
use IteratorAggregate;
use stdClass;
use Traversable;
use core\url;
use core\output\core_renderer;
use core\output\renderer_base;
use core\lang_string;
use section_info;
use core\context\module as context_module;
use core\context\course as context_course;
use core_courseformat\output\activitybadge;
use core\component;
use core\output\html_writer;

/**
 * Data about a single module on a course.
 *
 * This contains most of the fields in the course_modules table, plus additional data when required.
 *
 * The object can be accessed by core or any plugin (i.e. course format, block, filter, etc.) as
 * get_fast_modinfo($courseorid)->cms[$coursemoduleid]
 * or
 * get_fast_modinfo($courseorid)->instances[$moduletype][$instanceid]
 *
 * There are three stages when activity module can add/modify data in this object:
 *
 * <b>Stage 1 - during building the cache.</b>
 * Allows to add to the course cache static user-independent information about the module.
 * Modules should try to include only absolutely necessary information that may be required
 * when displaying course view page. The information is stored in application-level cache
 * and reset when {@see rebuild_course_cache()} is called or cache is purged by admin.
 *
 * Modules can implement callback XXX_get_coursemodule_info() returning instance of object
 * {@see cached_cm_info}
 *
 * <b>Stage 2 - dynamic data.</b>
 * Dynamic data is user-dependent, it is stored in request-level cache. To reset this cache
 * {@see get_fast_modinfo()} with $reset argument may be called.
 *
 * Dynamic data is obtained when any of the following properties/methods is requested:
 * - {@see cm_info::$url}
 * - {@see cm_info::$name}
 * - {@see cm_info::$onclick}
 * - {@see cm_info::get_icon_url()}
 * - {@see cm_info::$uservisible}
 * - {@see cm_info::$available}
 * - {@see cm_info::$availableinfo}
 * - plus any of the properties listed in Stage 3.
 *
 * Modules can implement callback <b>XXX_cm_info_dynamic()</b> and inside this callback they
 * are allowed to use any of the following set methods:
 * - {@see cm_info::set_available()}
 * - {@see cm_info::set_name()}
 * - {@see cm_info::set_no_view_link()}
 * - {@see cm_info::set_user_visible()}
 * - {@see cm_info::set_on_click()}
 * - {@see cm_info::set_icon_url()}
 * - {@see cm_info::override_customdata()}
 * Any methods affecting view elements can also be set in this callback.
 *
 * <b>Stage 3 (view data).</b>
 * Also user-dependend data stored in request-level cache. Second stage is created
 * because populating the view data can be expensive as it may access much more
 * Moodle APIs such as filters, user information, output renderers and we
 * don't want to request it until necessary.
 * View data is obtained when any of the following properties/methods is requested:
 * - {@see cm_info::$afterediticons}
 * - {@see cm_info::$content}
 * - {@see cm_info::get_formatted_content()}
 * - {@see cm_info::$extraclasses}
 * - {@see cm_info::$afterlink}
 *
 * Modules can implement callback <b>XXX_cm_info_view()</b> and inside this callback they
 * are allowed to use any of the following set methods:
 * - {@see cm_info::set_after_edit_icons()}
 * - {@see cm_info::set_after_link()}
 * - {@see cm_info::set_content()}
 * - {@see cm_info::set_extra_classes()}
 *
 * @package core_course
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright Sam Marshall
 * @property-read int $id Course-module ID - from course_modules table
 * @property-read int $instance Module instance (ID within module table) - from course_modules table
 * @property-read int $course Course ID - from course_modules table
 * @property-read string $idnumber 'ID number' from course-modules table (arbitrary text set by user) - from
 *    course_modules table
 * @property-read int $added Time that this course-module was added (unix time) - from course_modules table
 * @property-read int $visible Visible setting (0 or 1; if this is 0, students cannot see/access the activity) - from
 *    course_modules table
 * @property-read int $visibleoncoursepage Visible on course page setting - from course_modules table, adjusted to
 *    whether course format allows this module to have the "stealth" mode
 * @property-read int $visibleold Old visible setting (if the entire section is hidden, the previous value for
 *    visible is stored in this field) - from course_modules table
 * @property-read int $groupmode Group mode (one of the constants NOGROUPS, SEPARATEGROUPS, or VISIBLEGROUPS) - from
 *    course_modules table. Use {@see cm_info::$effectivegroupmode} to find the actual group mode that may be forced by course.
 * @property-read int $groupingid Grouping ID (0 = all groupings)
 * @property-read bool $coursegroupmodeforce Indicates whether the course containing the module has forced the groupmode
 *    This means that cm_info::$groupmode should be ignored and cm_info::$coursegroupmode be used instead
 * @property-read int $coursegroupmode Group mode (one of the constants NOGROUPS, SEPARATEGROUPS, or VISIBLEGROUPS) - from
 *    course table - as specified for the course containing the module
 *    Effective only if {@see cm_info::$coursegroupmodeforce} is set
 * @property-read int $effectivegroupmode Effective group mode for this module (one of the constants NOGROUPS, SEPARATEGROUPS,
 *    or VISIBLEGROUPS). This can be different from groupmode set for the module if the groupmode is forced for the course.
 *    This value will always be NOGROUPS if module type does not support group mode.
 * @property-read int $indent Indent level on course page (0 = no indent) - from course_modules table
 * @property-read int $completion Activity completion setting for this activity, COMPLETION_TRACKING_xx constant - from
 *    course_modules table
 * @property-read mixed $completiongradeitemnumber Set to the item number (usually 0) if completion depends on a particular
 *    grade of this activity, or null if completion does not depend on a grade - from course_modules table
 * @property-read int $completionview 1 if 'on view' completion is enabled, 0 otherwise - from course_modules table
 * @property-read int $completionexpected Set to a unix time if completion of this activity is expected at a
 *    particular time, 0 if no time set - from course_modules table
 * @property-read string $availability Availability information as JSON string or null if none -
 *    from course_modules table
 * @property-read int $showdescription Controls whether the description of the activity displays on the course main page (in
 *    addition to anywhere it might display within the activity itself). 0 = do not show
 *    on main page, 1 = show on main page.
 * @property-read string $extra (deprecated) Extra HTML that is put in an unhelpful part of the HTML when displaying this module in
 *    course page - from cached data in modinfo field. Deprecated, replaced by ->extraclasses and ->onclick
 * @property-read string $icon Name of icon to use - from cached data in modinfo field
 * @property-read string $iconcomponent Component that contains icon - from cached data in modinfo field
 * @property-read string $modname Name of module e.g. 'forum' (this is the same name as the module's main database
 *    table) - from cached data in modinfo field
 * @property-read int $module ID of module type - from course_modules table
 * @property-read string $name Name of module instance for display on page e.g. 'General discussion forum' - from cached
 *    data in modinfo field
 * @property-read int $sectionnum Section number that this course-module is in (section 0 = above the calendar, section 1
 *    = week/topic 1, etc) - from cached data in modinfo field
 * @property-read int $sectionid Section id - from course_modules table
 * @property-read array $conditionscompletion Availability conditions for this course-module based on the completion of other
 *    course-modules (array from other course-module id to required completion state for that
 *    module) - from cached data in modinfo field
 * @property-read array $conditionsgrade Availability conditions for this course-module based on course grades (array from
 *    grade item id to object with ->min, ->max fields) - from cached data in modinfo field
 * @property-read array $conditionsfield Availability conditions for this course-module based on user fields
 * @property-read bool $available True if this course-module is available to students i.e. if all availability conditions
 *    are met - obtained dynamically
 * @property-read string $availableinfo If course-module is not available to students, this string gives information about
 *    availability which can be displayed to students and/or staff (e.g. 'Available from 3
 *    January 2010') for display on main page - obtained dynamically
 * @property-read bool $uservisible True if this course-module is available to the CURRENT user (for example, if current user
 *    has viewhiddenactivities capability, they can access the course-module even if it is not
 *    visible or not available, so this would be true in that case)
 * @property-read context_module $context Module context
 * @property-read string $modfullname Returns a localised human-readable name of the module type - calculated on request
 * @property-read string $modplural Returns a localised human-readable name of the module type in plural form
 *      Calculated on request
 * @property-read string $content Content to display on main (view) page - calculated on request
 * @property-read url|null $url URL to link to for this module, or null if it doesn't have a view page - calculated on request
 * @property-read string $extraclasses Extra CSS classes to add to html output for this activity on main page
 *      C'alculated on request
 * @property-read string $onclick Content of HTML on-click attribute already escaped - calculated on request
 * @property-read mixed $customdata Optional custom data stored in modinfo cache for this activity, or null if none
 * @property-read string $afterlink Extra HTML code to display after link - calculated on request
 * @property-read string $afterediticons Extra HTML code to display after editing icons (e.g. more icons) - calculated on request
 * @property-read bool $deletioninprogress True if this course module is scheduled for deletion, false otherwise.
 * @property-read bool $downloadcontent True if content download is enabled for this course module, false otherwise.
 * @property-read bool $lang the forced language for this activity (language pack name). Null means not forced.
 * @property-read int|null $enableaitools AI tools for course_modules table
 * @property-read string|null $enabledaiactions AI actions for course_modules table
 */
class cm_info implements IteratorAggregate {
    /**
     * State: Only basic data from modinfo cache is available.
     */
    private const STATE_BASIC = 0;

    /**
     * State: In the process of building dynamic data (to avoid recursive calls to obtain_dynamic_data())
     */
    private const STATE_BUILDING_DYNAMIC = 1;

    /**
     * State: Dynamic data is available too.
     */
    private const STATE_DYNAMIC = 2;

    /**
     * State: In the process of building view data (to avoid recursive calls to obtain_view_data())
     */
    private const STATE_BUILDING_VIEW = 3;

    /**
     * State: View data (for course page) is available.
     */
    private const STATE_VIEW = 4;

    /** @var modinfo Parent object */
    private $modinfo;

    /**
     * Level of information stored inside this object (STATE_xx constant)
     * @var int
     */
    private $state;

    /**
     * Course-module ID - from course_modules table
     * @var int
     */
    private $id;

    /**
     * Module instance (ID within module table) - from course_modules table
     * @var int
     */
    private $instance;

    /**
     * 'ID number' from course-modules table (arbitrary text set by user) - from
     * course_modules table
     * @var string
     */
    private $idnumber;

    /**
     * Time that this course-module was added (unix time) - from course_modules table
     * @var int
     */
    private $added;

    /**
     * @var int
     *
     * This variable is not used and is included here only so it can be documented.
     * Once the database entry is removed from course_modules, it should be deleted
     * here too.
     * @deprecated Do not use this variable
     */
    #[\core\attribute\deprecated(
        since: '2.0',
        mdl: 'MDL-26781',
    )]
    private $score;

    /**
     * Visible setting (0 or 1; if this is 0, students cannot see/access the activity) - from
     * course_modules table
     * @var int
     */
    private $visible;

    /**
     * Visible on course page setting - from course_modules table
     * @var int
     */
    private $visibleoncoursepage;

    /**
     * Old visible setting (if the entire section is hidden, the previous value for
     * visible is stored in this field) - from course_modules table
     * @var int
     */
    private $visibleold;

    /**
     * Group mode (one of the constants NONE, SEPARATEGROUPS, or VISIBLEGROUPS) - from
     * course_modules table
     * @var int
     */
    private $groupmode;

    /**
     * Grouping ID (0 = all groupings)
     * @var int
     */
    private $groupingid;

    /**
     * Indent level on course page (0 = no indent) - from course_modules table
     * @var int
     */
    private $indent;

    /**
     * Activity completion setting for this activity, COMPLETION_TRACKING_xx constant - from
     * course_modules table
     * @var int
     */
    private $completion;

    /**
     * Set to the item number (usually 0) if completion depends on a particular
     * grade of this activity, or null if completion does not depend on a grade - from
     * course_modules table
     * @var mixed
     */
    private $completiongradeitemnumber;

    /**
     * 1 if pass grade completion is enabled, 0 otherwise - from course_modules table
     * @var int
     */
    private $completionpassgrade;

    /**
     * 1 if 'on view' completion is enabled, 0 otherwise - from course_modules table
     * @var int
     */
    private $completionview;

    /** @var int Set to a unix time if completion of this activity is expected at a particular time, 0 if no time set - from course_modules table */
    private $completionexpected;

    /** @var string Availability information as JSON string or null if none - from course_modules table */
    private $availability;

    /**
     * @var int Whether the description of this activity is displayed on the course main page.
     *
     * Note: This would be in addition to anywhere it might display within the activity itself.
     *
     * 0 = do not show on main page, 1 = show on main page.
     */
    private $showdescription;

    /**
     * @var string Extra HTML
     *
     * Extra HTML that is put in an unhelpful part of the HTML when displaying this module in
     * course page - from cached data in modinfo field
     * @deprecated This is crazy, don't use it. Replaced by ->extraclasses and ->onclick
     */
    #[\core\attribute\deprecated(
        replacement: '->extraclasses and ->onclick',
        since: '2.0',
        mdl: 'MDL-25981',
        reason: 'This is crazy, don\'t use it.'
    )]
    private $extra;

    /**
     * Name of icon to use - from cached data in modinfo field
     * @var string
     */
    private $icon;

    /**
     * Component that contains icon - from cached data in modinfo field
     * @var string
     */
    private $iconcomponent;

    /**
     * The instance record form the module table
     * @var stdClass
     */
    private $instancerecord;

    /**
     * Name of module e.g. 'forum' (this is the same name as the module's main database
     * table) - from cached data in modinfo field
     * @var string
     */
    private $modname;

    /**
     * ID of module - from course_modules table
     * @var int
     */
    private $module;

    /**
     * Name of module instance for display on page e.g. 'General discussion forum' - from cached
     * data in modinfo field
     * @var string
     */
    private $name;

    /**
     * Section number that this course-module is in (section 0 = above the calendar, section 1
     * = week/topic 1, etc) - from cached data in modinfo field
     * @var int
     */
    private $sectionnum;

    /**
     * Section id - from course_modules table
     * @var int
     */
    private $sectionid;

    /**
     * Availability conditions for this course-module based on the completion of other
     * course-modules (array from other course-module id to required completion state for that
     * module) - from cached data in modinfo field
     * @var array
     */
    private $conditionscompletion;

    /**
     * Availability conditions for this course-module based on course grades (array from
     * grade item id to object with ->min, ->max fields) - from cached data in modinfo field
     * @var array
     */
    private $conditionsgrade;

    /**
     * Availability conditions for this course-module based on user fields
     * @var array
     */
    private $conditionsfield;

    /**
     * True if this course-module is available to students i.e. if all availability conditions
     * are met - obtained dynamically
     * @var bool
     */
    private $available;

    /**
     * If course-module is not available to students, this string gives information about
     * availability which can be displayed to students and/or staff (e.g. 'Available from 3
     * January 2010') for display on main page - obtained dynamically
     * @var string
     */
    private $availableinfo;

    /**
     * True if this course-module is available to the CURRENT user (for example, if current user
     * has viewhiddenactivities capability, they can access the course-module even if it is not
     * visible or not available, so this would be true in that case)
     * @var bool
     */
    private $uservisible;

    /**
     * True if this course-module is visible to the CURRENT user on the course page
     * @var bool
     */
    private $uservisibleoncoursepage;

    /**
     * @var url|null The activity URL, if any.
     */
    private $url;

    /**
     * @var string
     */
    private $content;

    /**
     * @var bool
     */
    private $contentisformatted;

    /**
     * @var bool True if the content has a special course item display like labels.
     */
    private $customcmlistitem;

    /**
     * @var string
     */
    private $extraclasses;

    /**
     * @var url full external url pointing to icon image for activity
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
     * @var bool representing the deletion state of the module. True if the mod is scheduled for deletion.
     */
    private $deletioninprogress;

    /**
     * @var int enable/disable download content for this course module
     */
    private $downloadcontent;

    /**
     * @var string|null the forced language for this activity (language pack name). Null means not forced.
     */
    private $lang;

    /**
     * @var int|null enable/disable AI tools for this course module
     */
    private $enableaitools;

    /**
     * @var string|null enabled AI actions for this course module
     */
    private $enabledaiactions;

    /**
     * List of class read-only properties and their getter methods.
     * Used by magic functions __get(), __isset().
     *
     * @var array
     */
    private static $standardproperties = [
        'url' => 'get_url',
        'content' => 'get_content',
        'extraclasses' => 'get_extra_classes',
        'onclick' => 'get_on_click',
        'customdata' => 'get_custom_data',
        'afterlink' => 'get_after_link',
        'afterediticons' => 'get_after_edit_icons',
        'modfullname' => 'get_module_type_name',
        'modplural' => 'get_module_type_name_plural',
        'id' => false,
        'added' => false,
        'availability' => false,
        'available' => 'get_available',
        'availableinfo' => 'get_available_info',
        'completion' => false,
        'completionexpected' => false,
        'completiongradeitemnumber' => false,
        'completionpassgrade' => false,
        'completionview' => false,
        'conditionscompletion' => false,
        'conditionsfield' => false,
        'conditionsgrade' => false,
        'context' => 'get_context',
        'course' => 'get_course_id',
        'coursegroupmode' => 'get_course_groupmode',
        'coursegroupmodeforce' => 'get_course_groupmodeforce',
        'customcmlistitem' => 'has_custom_cmlist_item',
        'effectivegroupmode' => 'get_effective_groupmode',
        'extra' => false,
        'groupingid' => false,
        'groupmembersonly' => 'get_deprecated_group_members_only',
        'groupmode' => false,
        'icon' => false,
        'iconcomponent' => false,
        'idnumber' => false,
        'indent' => false,
        'instance' => false,
        'modname' => false,
        'module' => false,
        'name' => 'get_name',
        'score' => false,
        'section' => 'get_section_id',
        'sectionid' => false,
        'sectionnum' => false,
        'showdescription' => false,
        'uservisible' => 'get_user_visible',
        'visible' => false,
        'visibleoncoursepage' => false,
        'visibleold' => false,
        'deletioninprogress' => false,
        'downloadcontent' => false,
        'lang' => false,
        'enableaitools' => false,
        'enabledaiactions' => false,
    ];

    /**
     * List of methods with no arguments that were public prior to Moodle 2.6.
     *
     * They can still be accessed publicly via magic __call() function with no warnings
     * but are not listed in the class methods list.
     * For the consistency of the code it is better to use corresponding properties.
     *
     * These methods be deprecated completely in later versions.
     *
     * @var array $standardmethods
     */
    private static $standardmethods = [
        // Following methods are not recommended to use because there have associated read-only properties.
        'get_url',
        'get_content',
        'get_extra_classes',
        'get_on_click',
        'get_custom_data',
        'get_after_link',
        'get_after_edit_icons',
        // Method obtain_dynamic_data() should not be called from outside of this class but it was public before Moodle 2.6.
        'obtain_dynamic_data',
    ];

    /**
     * Magic method to call functions that are now declared as private but were public in Moodle before 2.6.
     * These private methods can not be used anymore.
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     * @throws coding_exception
     */
    public function __call($name, $arguments) {
        if (in_array($name, self::$standardmethods)) {
            $message = "cm_info::$name() can not be used anymore.";
            if ($alternative = array_search($name, self::$standardproperties)) {
                $message .= " Please use the property cm_info->$alternative instead.";
            }
            throw new coding_exception($message);
        }
        throw new coding_exception("Method cm_info::{$name}() does not exist");
    }

    /**
     * Magic method getter
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name) {
        \core\deprecation::emit_deprecation_if_present([__CLASS__, $name]);
        if (isset(self::$standardproperties[$name])) {
            if ($method = self::$standardproperties[$name]) {
                return $this->$method();
            } else {
                return $this->$name;
            }
        } else {
            debugging('Invalid cm_info property accessed: ' . $name);
            return null;
        }
    }

    #[\Override]
    public function getIterator(): Traversable {
        // Make sure dynamic properties are retrieved prior to view properties.
        $this->obtain_dynamic_data();
        $ret = [];

        // Do not iterate over deprecated properties.
        $props = self::$standardproperties;
        unset($props['groupmembersonly']);

        foreach ($props as $key => $unused) {
            if (\core\deprecation::is_deprecated([__CLASS__, $key])) {
                continue;
            }

            $ret[$key] = $this->__get($key);
        }
        return new ArrayIterator($ret);
    }

    /**
     * Magic method for function isset()
     *
     * @param string $name
     * @return bool
     */
    public function __isset($name) {
        \core\deprecation::emit_deprecation_if_present([__CLASS__, $name]);
        if (isset(self::$standardproperties[$name])) {
            $value = $this->__get($name);
            return isset($value);
        }
        return false;
    }

    /**
     * Magic method setter
     *
     * Will display the developer warning when trying to set/overwrite property.
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value) {
        debugging("It is not allowed to set the property cm_info::\${$name}", DEBUG_DEVELOPER);
    }

    /**
     * Whether this activity has a view page.
     *
     * Note: modules may still have a view.php file, but return false if this is not intended to be linked to
     * from standard parts of the interface.
     * For an example of these, see `mod_label`.
     *
     * @return bool True if this module has a 'view' page that should be linked to in navigation etc.
     */
    public function has_view() {
        return !is_null($this->url);
    }

    /**
     * Gets the URL to link to for this module.
     *
     * This method is normally called by the property ->url, but can be called directly if
     * there is a case when it might be called recursively (you can't call property values
     * recursively).
     *
     * @return url URL to link to for this module, or null if it doesn't have a view page
     */
    public function get_url() {
        $this->obtain_dynamic_data();
        return $this->url;
    }

    /**
     * Obtains content to display on main (view) page.
     * Note: Will collect view data, if not already obtained.
     * @return string Content to display on main page below link, or empty string if none
     */
    private function get_content() {
        $this->obtain_view_data();
        return $this->content;
    }

    /**
     * Returns the content to display on course/overview page, formatted and passed through filters
     *
     * if $options['context'] is not specified, the module context is used
     *
     * @param array|stdClass $options formatting options, see {@see format_text()}
     * @return string
     */
    public function get_formatted_content($options = []) {
        $this->obtain_view_data();
        if (empty($this->content)) {
            return '';
        }
        if ($this->contentisformatted) {
            return $this->content;
        }

        // Improve filter performance by preloading filter setttings for all activities on the course.
        // This does nothing if called multiple times.
        filter_preload_activities($this->get_modinfo());

        $options = (array)$options;
        if (!isset($options['context'])) {
            $options['context'] = $this->get_context();
        }
        return format_text($this->content, FORMAT_HTML, $options);
    }

    /**
     * Return the module custom cmlist item flag.
     *
     * Activities like label uses this flag to indicate that it should be
     * displayed as a custom course item instead of a tipical activity card.
     *
     * @return bool
     */
    public function has_custom_cmlist_item(): bool {
        $this->obtain_view_data();
        return $this->customcmlistitem ?? false;
    }

    /**
     * Getter method for property $name, ensures that dynamic data is obtained.
     *
     * This method is normally called by the property ->name, but can be called directly if there
     * is a case when it might be called recursively (you can't call property values recursively).
     *
     * @return string
     */
    public function get_name() {
        $this->obtain_dynamic_data();
        return $this->name;
    }

    /**
     * Returns the name to display on course/overview page, formatted and passed through filters
     *
     * if $options['context'] is not specified, the module context is used
     *
     * @param array|stdClass $options formatting options, see {@see format_string()}
     * @return string
     */
    public function get_formatted_name($options = []) {
        global $CFG;
        $options = (array)$options;
        if (!isset($options['context'])) {
            $options['context'] = $this->get_context();
        }
        // Improve filter performance by preloading filter setttings for all
        // activities on the course (this does nothing if called multiple
        // times).
        if (!empty($CFG->filterall)) {
            filter_preload_activities($this->get_modinfo());
        }
        return format_string($this->get_name(), true, $options);
    }

    /**
     * Note: Will collect view data, if not already obtained.
     * @return string Extra CSS classes to add to html output for this activity on main page
     */
    private function get_extra_classes() {
        $this->obtain_view_data();
        return $this->extraclasses;
    }

    /**
     * Get the on-click attribute.
     *
     * Note: This string will be used literally as a string so should be pre-escaped.
     *
     * @return string Content of HTML on-click attribute.
     */
    private function get_on_click() {
        // Does not need view data; may be used by navigation.
        $this->obtain_dynamic_data();
        return $this->onclick;
    }
    /**
     * Getter method for property $customdata, ensures that dynamic data is retrieved.
     *
     * This method is normally called by the property ->customdata, but can be called directly if there
     * is a case when it might be called recursively (you can't call property values recursively).
     *
     * @return mixed Optional custom data stored in modinfo cache for this activity, or null if none
     */
    public function get_custom_data() {
        $this->obtain_dynamic_data();
        return $this->customdata;
    }

    /**
     * Note: Will collect view data, if not already obtained.
     * @return string Extra HTML code to display after link
     */
    private function get_after_link() {
        $this->obtain_view_data();
        return $this->afterlink;
    }

    /**
     * Get the activity badge data associated to this course module (if the module supports it).
     * Modules can use this method to provide additional data to be displayed in the activity badge.
     *
     * @param renderer_base|null $output Output render to use, or null for default (global)
     * @return stdClass|null The activitybadge data (badgecontent, badgestyle...) or null if the module doesn't implement it.
     */
    public function get_activitybadge(?renderer_base $output = null): ?stdClass {
        global $OUTPUT;

        $activibybadgeclass = activitybadge::create_instance($this);
        if (empty($activibybadgeclass)) {
            return null;
        }

        if (!isset($output)) {
            $output = $OUTPUT;
        }

        return $activibybadgeclass->export_for_template($output);
    }

    /**
     * Note: Will collect view data, if not already obtained.
     * @return string Extra HTML code to display after editing icons (e.g. more icons)
     */
    private function get_after_edit_icons() {
        $this->obtain_view_data();
        return $this->afterediticons;
    }

    /**
     * Fetch the module's icon URL.
     *
     * This function fetches the course module instance's icon URL.
     * This method adds a `filtericon` parameter in the URL when rendering the monologo version of the course module icon or when
     * the plugin declares, via its `filtericon` custom data, that the icon needs to be filtered.
     * This additional information can be used by plugins when rendering the module icon to determine whether to apply
     * CSS filtering to the icon.
     *
     * @param core_renderer $output Output render to use, or null for default (global)
     * @return url Icon URL for a suitable icon to put beside this cm
     */
    public function get_icon_url($output = null) {
        global $OUTPUT;
        $this->obtain_dynamic_data();
        if (!$output) {
            $output = $OUTPUT;
        }

        $ismonologo = false;
        if (!empty($this->iconurl)) {
            // Support modules setting their own, external, icon image.
            $icon = $this->iconurl;
        } else if (!empty($this->icon)) {
            // Fallback to normal local icon + component processing.
            if (substr($this->icon, 0, 4) === 'mod/') {
                [$modname, $iconname] = explode('/', substr($this->icon, 4), 2);
                $icon = $output->image_url($iconname, $modname);
            } else {
                if (!empty($this->iconcomponent)) {
                    // Icon has specified component.
                    $icon = $output->image_url($this->icon, $this->iconcomponent);
                } else {
                    // Icon does not have specified component, use default.
                    $icon = $output->image_url($this->icon);
                }
            }
        } else {
            $icon = $output->image_url('monologo', $this->modname);
            // Activity modules may only have an `icon` icon instead of a `monologo` icon.
            // So we need to determine if the module really has a `monologo` icon.
            $ismonologo = component::has_monologo_icon('mod', $this->modname);
        }

        // Determine whether the icon will be filtered in the CSS.
        // This can be controlled by the module by declaring a 'filtericon' custom data.
        // If the 'filtericon' custom data is not set, icon filtering will be determined whether the module has a `monologo` icon.
        // Additionally, we need to cast custom data to array as some modules may treat it as an object.
        $filtericon = ((array)$this->customdata)['filtericon'] ?? $ismonologo;
        if ($filtericon) {
            $icon->param('filtericon', 1);
        }
        return $icon;
    }

    /**
     * Get the grouping label
     *
     * @param string $textclasses additional classes for grouping label
     * @return string An empty string or HTML grouping label span tag
     */
    public function get_grouping_label($textclasses = '') {
        $groupinglabel = '';
        if (
            $this->effectivegroupmode != NOGROUPS
            && !empty($this->groupingid)
            && has_capability('moodle/course:managegroups', context_course::instance($this->course))
        ) {
            $groupings = groups_get_all_groupings($this->course);
            $groupinglabel = html_writer::tag(
                'span',
                '(' . format_string($groupings[$this->groupingid]->name) . ')',
                ['class' => 'groupinglabel ' . $textclasses],
            );
        }
        return $groupinglabel;
    }

    /**
     * Returns a localised human-readable name of the module type.
     *
     * @param bool $plural If true, the function returns the plural form of the name.
     * @return ?lang_string
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
     * Returns a localised human-readable name of the module type in plural form - calculated on request
     *
     * @return string
     */
    private function get_module_type_name_plural() {
        return $this->get_module_type_name(true);
    }

    /**
     * Get the modinfo object that this cm_info came from.
     *
     * @return modinfo Modinfo object that this came from
     */
    public function get_modinfo(): modinfo {
        return $this->modinfo;
    }

    /**
     * Returns the section this module belongs to
     *
     * @return section_info
     */
    public function get_section_info() {
        return $this->modinfo->get_section_info_by_id($this->sectionid);
    }

    /**
     * Getter method for property $section that returns section id.
     *
     * This method is called by the property ->section.
     *
     * @return int
     */
    private function get_section_id(): int {
        return $this->sectionid;
    }

    /**
     * Returns course object that was used in the first {@see get_fast_modinfo()} call.
     *
     * It may not contain all fields from DB table {course} but always has at least the following:
     * id,shortname,fullname,format,enablecompletion,groupmode,groupmodeforce,cacherev
     *
     * If the course object lacks the field you need you can use the global
     * function {@see get_course()} that will save extra query if you access
     * current course or frontpage course.
     *
     * @return stdClass
     */
    public function get_course() {
        return $this->modinfo->get_course();
    }

    /**
     * Returns course id for which the modinfo was generated.
     *
     * @return int
     */
    private function get_course_id() {
        return $this->modinfo->get_course_id();
    }

    /**
     * Returns group mode used for the course containing the module
     *
     * @return int one of constants NOGROUPS, SEPARATEGROUPS, VISIBLEGROUPS
     */
    private function get_course_groupmode() {
        return $this->modinfo->get_course()->groupmode;
    }

    /**
     * Returns whether group mode is forced for the course containing the module
     *
     * @return bool
     */
    private function get_course_groupmodeforce() {
        return $this->modinfo->get_course()->groupmodeforce;
    }

    /**
     * Returns effective groupmode of the module that may be overwritten by forced course groupmode.
     *
     * @return int one of constants NOGROUPS, SEPARATEGROUPS, VISIBLEGROUPS
     */
    private function get_effective_groupmode() {
        $groupmode = $this->groupmode;
        if ($this->modinfo->get_course()->groupmodeforce) {
            $groupmode = $this->modinfo->get_course()->groupmode;
            if ($groupmode != NOGROUPS && !plugin_supports('mod', $this->modname, FEATURE_GROUPS, false)) {
                $groupmode = NOGROUPS;
            }
        }
        return $groupmode;
    }

    /**
     * Get the context for that activity.
     *
     * @return context_module Current module context
     */
    private function get_context() {
        return context_module::instance($this->id);
    }

    /**
     * Returns itself in the form of stdClass.
     *
     * The object includes all fields that table course_modules has and additionally
     * fields 'name', 'modname', 'sectionnum' (if requested).
     *
     * This can be used as a faster alternative to {@see get_coursemodule_from_id()}
     *
     * @param bool $additionalfields include additional fields 'name', 'modname', 'sectionnum'
     * @return stdClass
     */
    public function get_course_module_record($additionalfields = false) {
        $cmrecord = new stdClass();

        // Standard fields from table course_modules.
        static $cmfields = ['id', 'course', 'module', 'instance', 'section', 'idnumber', 'added',
            'score', 'indent', 'visible', 'visibleoncoursepage', 'visibleold', 'groupmode', 'groupingid',
            'completion', 'completiongradeitemnumber', 'completionview', 'completionexpected', 'completionpassgrade',
            'showdescription', 'availability', 'deletioninprogress', 'downloadcontent', 'lang',
            'enableaitools', 'enabledaiactions',
        ];

        foreach ($cmfields as $key) {
            $cmrecord->$key = $this->$key;
        }

        // Additional fields that function get_coursemodule_from_id() adds.
        if ($additionalfields) {
            $cmrecord->name = $this->name;
            $cmrecord->modname = $this->modname;
            $cmrecord->sectionnum = $this->sectionnum;
        }

        return $cmrecord;
    }

    /**
     * Return the activity database table record.
     *
     * The instance record will be cached after the first call.
     *
     * @return stdClass
     */
    public function get_instance_record() {
        global $DB;
        if (!isset($this->instancerecord)) {
            $this->instancerecord = $DB->get_record(
                table: $this->modname,
                conditions: ['id' => $this->instance],
                strictness: MUST_EXIST,
            );
        }
        return $this->instancerecord;
    }

    /**
     * Returns the section delegated by this module, if any.
     *
     * @return ?section_info
     */
    public function get_delegated_section_info(): ?section_info {
        $delegatedsections = $this->modinfo->get_sections_delegated_by_cm();
        if (!array_key_exists($this->id, $delegatedsections)) {
            return null;
        }
        return $delegatedsections[$this->id];
    }

    /**
     * Sets content to display on course view page below link (if present).
     * @param string $content New content as HTML string (empty string if none)
     * @param bool $isformatted Whether user content is already passed through format_text/format_string and should not
     *    be formatted again. This can be useful when module adds interactive elements on top of formatted user text.
     * @return void
     */
    public function set_content($content, $isformatted = false) {
        $this->content = $content;
        $this->contentisformatted = $isformatted;
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
     * @param url $iconurl full external url pointing to icon image for activity
     * @return void
     */
    public function set_icon_url(url $iconurl) {
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
     * Overrides the value of an element in the customdata array.
     *
     * @param string $name The key in the customdata array
     * @param mixed $value The value
     */
    public function override_customdata($name, $value) {
        if (!is_array($this->customdata)) {
            $this->customdata = [];
        }
        $this->customdata[$name] = $value;
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
        if ($this->state < self::STATE_BUILDING_DYNAMIC) {
            $this->update_user_visible();
        }
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
     * Sets the 'customcmlistitem' flag
     *
     * This can be used (by setting true) to prevent the course from rendering the
     * activity item as a regular activity card. This is applied to activities like labels.
     *
     * @param bool $customcmlistitem if the cmlist item of that activity has a special dysplay other than a card.
     */
    public function set_custom_cmlist_item(bool $customcmlistitem) {
        $this->customcmlistitem = $customcmlistitem;
    }

    /**
     * Sets the 'available' flag and related details. This flag is normally used to make
     * course modules unavailable until a certain date or condition is met. (When a course
     * module is unavailable, it is still visible to users who have viewhiddenactivities
     * permission.)
     *
     * When this is function is called, user-visible status is recalculated automatically.
     *
     * The $showavailability flag does not really do anything any more, but is retained
     * for backward compatibility. Setting this to false will cause $availableinfo to
     * be ignored.
     *
     * Note: May not be called from _cm_info_view (only _cm_info_dynamic).
     * @param bool $available False if this item is not 'available'
     * @param int $showavailability 0 = do not show this item at all if it's not available,
     *   1 = show this item greyed out with the following message
     * @param string $availableinfo Information about why this is not available, or
     *   empty string if not displaying
     * @return void
     */
    public function set_available($available, $showavailability = 0, $availableinfo = '') {
        $this->check_not_view_only();
        $this->available = $available;
        if (!$showavailability) {
            $availableinfo = '';
        }
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
            throw new coding_exception(
                'Cannot set this data from _cm_info_view because it may ' .
                'affect other pages as well as view'
            );
        }
    }

    /**
     * Constructor should not be called directly; use {@see get_fast_modinfo()}
     *
     * @param modinfo $modinfo Parent object
     * @param mixed $notused1 Argument not used
     * @param stdClass $mod Module object from the modinfo field of course table
     * @param mixed $notused2 Argument not used
     */
    public function __construct(modinfo $modinfo, $notused1, $mod, $notused2) {
        $this->modinfo = $modinfo;

        $this->id               = $mod->cm;
        $this->instance         = $mod->id;
        $this->modname          = $mod->mod;
        $this->idnumber         = isset($mod->idnumber) ? $mod->idnumber : '';
        $this->name             = $mod->name;
        $this->visible          = $mod->visible;
        $this->visibleoncoursepage = $mod->visibleoncoursepage;
        $this->sectionnum       = $mod->section; // Note weirdness with name here. Keeping for backwards compatibility.
        $this->groupmode        = isset($mod->groupmode) ? $mod->groupmode : 0;
        $this->groupingid       = isset($mod->groupingid) ? $mod->groupingid : 0;
        $this->indent           = isset($mod->indent) ? $mod->indent : 0;
        $this->extra            = isset($mod->extra) ? $mod->extra : '';
        $this->extraclasses     = isset($mod->extraclasses) ? $mod->extraclasses : '';
        // The iconurl may be stored as either string or instance of url.
        $this->iconurl          = isset($mod->iconurl) ? new url($mod->iconurl) : '';
        $this->onclick          = isset($mod->onclick) ? $mod->onclick : '';
        $this->content          = isset($mod->content) ? $mod->content : '';
        $this->icon             = isset($mod->icon) ? $mod->icon : '';
        $this->iconcomponent    = isset($mod->iconcomponent) ? $mod->iconcomponent : '';
        $this->customdata       = isset($mod->customdata) ? $mod->customdata : '';
        $this->showdescription  = isset($mod->showdescription) ? $mod->showdescription : 0;
        $this->state = self::STATE_BASIC;

        $this->sectionid = isset($mod->sectionid) ? $mod->sectionid : 0;
        $this->module = isset($mod->module) ? $mod->module : 0;
        $this->added = isset($mod->added) ? $mod->added : 0;
        $this->score = isset($mod->score) ? $mod->score : 0;
        $this->visibleold = isset($mod->visibleold) ? $mod->visibleold : 0;
        $this->deletioninprogress = isset($mod->deletioninprogress) ? $mod->deletioninprogress : 0;
        $this->downloadcontent = $mod->downloadcontent ?? null;
        $this->lang = $mod->lang ?? null;

        // Note: it saves effort and database space to always include the
        // availability and completion fields, even if availability or completion
        // are actually disabled.
        $this->completion = isset($mod->completion) ? $mod->completion : 0;
        $this->completionpassgrade = isset($mod->completionpassgrade) ? $mod->completionpassgrade : 0;
        $this->completiongradeitemnumber = isset($mod->completiongradeitemnumber)
                ? $mod->completiongradeitemnumber : null;
        $this->completionview = isset($mod->completionview)
                ? $mod->completionview : 0;
        $this->completionexpected = isset($mod->completionexpected)
                ? $mod->completionexpected : 0;
        $this->availability = isset($mod->availability) ? $mod->availability : null;
        $this->conditionscompletion = isset($mod->conditionscompletion)
                ? $mod->conditionscompletion : [];
        $this->conditionsgrade = isset($mod->conditionsgrade)
                ? $mod->conditionsgrade : [];
        $this->conditionsfield = isset($mod->conditionsfield)
                ? $mod->conditionsfield : [];

        static $modviews = [];
        if (!isset($modviews[$this->modname])) {
            $modviews[$this->modname] = !plugin_supports(
                'mod',
                $this->modname,
                FEATURE_NO_VIEW_LINK,
            );
        }
        $this->url = $modviews[$this->modname]
                ? new url('/mod/' . $this->modname . '/view.php', ['id' => $this->id])
                : null;
    }

    /**
     * Creates a cm_info object from a database record (also accepts cm_info
     * in which case it is just returned unchanged).
     *
     * @param stdClass|cm_info|null|bool $cm Stdclass or cm_info (or null or false)
     * @param int $userid Optional userid (default to current)
     * @return cm_info|null Object as cm_info, or null if input was null/false
     */
    public static function create($cm, $userid = 0) {
        // Null, false, etc. gets passed through as null.
        if (!$cm) {
            return null;
        }
        // If it is already a cm_info object with the right user, just return it.
        if (($cm instanceof cm_info) && ($cm->get_modinfo()->userid == $userid)) {
            return $cm;
        }
        // Otherwise load modinfo.
        if (empty($cm->id) || empty($cm->course)) {
            throw new coding_exception('$cm must contain ->id and ->course');
        }
        $modinfo = get_fast_modinfo($cm->course, $userid);
        return $modinfo->get_cm($cm->id);
    }

    /**
     * If dynamic data for this course-module is not yet available, gets it.
     *
     * This function is automatically called when requesting any modinfo property
     * that can be modified by modules (have a set_xxx method).
     *
     * Dynamic data is data which does not come directly from the cache but is calculated at
     * runtime based on the current user. Primarily this concerns whether the user can access
     * the module or not.
     *
     * As part of this function, the module's _cm_info_dynamic function from its lib.php will
     * be called (if it exists). Make sure that the functions that are called here do not use
     * any getter magic method from cm_info.
     * @return void
     */
    private function obtain_dynamic_data() {
        global $CFG;
        $userid = $this->modinfo->get_user_id();
        if ($this->state >= self::STATE_BUILDING_DYNAMIC || $userid == -1) {
            return;
        }
        $this->state = self::STATE_BUILDING_DYNAMIC;

        if (!empty($CFG->enableavailability)) {
            // Get availability information.
            $ci = new \core_availability\info_module($this);

            // Note that the modinfo currently available only includes minimal details (basic data)
            // but we know that this function does not need anything more than basic data.
            $this->available = $ci->is_available(
                $this->availableinfo,
                true,
                $userid,
                $this->modinfo,
            );
        } else {
            $this->available = true;
        }

        // Check parent section.
        if ($this->available) {
            $parentsection = $this->modinfo->get_section_info($this->sectionnum);
            if (!$parentsection->get_available()) {
                // Do not store info from section here, as that is already presented from the section (if appropriate).
                // Just change the flag.
                $this->available = false;
            }
        }

        // Update visible state for current user.
        $this->update_user_visible();

        // Let module make dynamic changes at this point.
        $this->call_mod_function('cm_info_dynamic');
        $this->state = self::STATE_DYNAMIC;
    }

    /**
     * Getter method for property $uservisible, ensures that dynamic data is retrieved.
     *
     * This method is normally called by the property ->uservisible, but can be called directly if
     * there is a case when it might be called recursively (you can't call property values
     * recursively).
     *
     * @return bool
     */
    public function get_user_visible() {
        $this->obtain_dynamic_data();
        return $this->uservisible;
    }

    /**
     * Returns whether this module is visible to the current user on course page
     *
     * Activity may be visible on the course page but not available, for example
     * when it is hidden conditionally but the condition information is displayed.
     *
     * @return bool
     */
    public function is_visible_on_course_page() {
        $this->obtain_dynamic_data();
        return $this->uservisibleoncoursepage;
    }

    /**
     * Use this method if you want to check if the plugin overrides any visibility checks to block rendering to the display.
     *
     * @return bool
     */
    public function is_of_type_that_can_display(): bool {
        return modinfo::is_mod_type_visible_on_course($this->modname);
    }

    /**
     * Whether this module is available but hidden from course page
     *
     * "Stealth" modules are the ones that are not shown on course page but available by following url.
     * They are normally also displayed in grade reports and other reports.
     * Module will be stealth either if visibleoncoursepage=0 or it is a visible module inside the hidden
     * section.
     *
     * @return bool
     */
    public function is_stealth() {
        return !$this->visibleoncoursepage ||
            ($this->visible && ($section = $this->get_section_info()) && !$section->visible);
    }

    /**
     * Getter method for property $available, ensures that dynamic data is retrieved
     * @return bool
     */
    private function get_available() {
        $this->obtain_dynamic_data();
        return $this->available;
    }

    /**
     * Getter method for property $availableinfo, ensures that dynamic data is retrieved
     *
     * @return string Available info (HTML)
     */
    private function get_available_info() {
        $this->obtain_dynamic_data();
        return $this->availableinfo;
    }

    /**
     * Works out whether activity is available to the current user
     *
     * If the activity is unavailable, additional checks are required to determine if its hidden or greyed out
     *
     * @return void
     */
    private function update_user_visible() {
        $userid = $this->modinfo->get_user_id();
        if ($userid == -1) {
            return null;
        }
        $this->uservisible = true;

        // If the module is being deleted, set the uservisible state to false and return.
        if ($this->deletioninprogress) {
            $this->uservisible = false;
            return null;
        }

        // If the user cannot access the activity set the uservisible flag to false.
        // Additional checks are required to determine whether the activity is entirely hidden or just greyed out.
        if (
            !$this->visible && !has_capability('moodle/course:viewhiddenactivities', $this->get_context(), $userid)
            || (
                !$this->get_available()
                && !has_capability('moodle/course:ignoreavailabilityrestrictions', $this->get_context(), $userid)
            )
        ) {
            $this->uservisible = false;
        }

        // Check group membership.
        if ($this->is_user_access_restricted_by_capability()) {
            $this->uservisible = false;
            // Ensure activity is completely hidden from the user.
            $this->availableinfo = '';
        }

        $capabilities = [
            'moodle/course:manageactivities',
            'moodle/course:activityvisibility',
            'moodle/course:viewhiddenactivities',
        ];
        $this->uservisibleoncoursepage = $this->uservisible &&
            ($this->visibleoncoursepage || has_any_capability($capabilities, $this->get_context(), $userid));
        // Activity that is not available, not hidden from course page and has availability
        // info is actually visible on the course page (with availability info and without a link).
        if (!$this->uservisible && $this->visibleoncoursepage && $this->availableinfo) {
            $this->uservisibleoncoursepage = true;
        }
    }

    /**
     * Checks whether mod/...:view capability restricts the current user's access.
     *
     * @return bool|null True if the user access is restricted.
     */
    public function is_user_access_restricted_by_capability() {
        $userid = $this->modinfo->get_user_id();
        if ($userid == -1) {
            return null;
        }
        $capability = 'mod/' . $this->modname . ':view';
        $capabilityinfo = get_capability_info($capability);
        if (!$capabilityinfo) {
            // Capability does not exist, no one is prevented from seeing the activity.
            return false;
        }

        // You are blocked if you don't have the capability.
        return !has_capability($capability, $this->get_context(), $userid);
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
        if ($this->state >= self::STATE_BUILDING_VIEW || $this->modinfo->get_user_id() == -1) {
            return;
        }
        $this->obtain_dynamic_data();
        $this->state = self::STATE_BUILDING_VIEW;

        // Let module make changes at this point.
        $this->call_mod_function('cm_info_view');
        $this->state = self::STATE_VIEW;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(cm_info::class, \cm_info::class);
