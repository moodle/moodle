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
 * Contains class core_course_list_element
 *
 * @package    core
 * @subpackage course
 * @copyright  2018 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Class to store information about one course in a list of courses
 *
 * Not all information may be retrieved when object is created but
 * it will be retrieved on demand when appropriate property or method is
 * called.
 *
 * Instances of this class are usually returned by functions
 * {@link core_course_category::search_courses()}
 * and
 * {@link core_course_category::get_courses()}
 *
 * @property-read int $id
 * @property-read int $category Category ID
 * @property-read int $sortorder
 * @property-read string $fullname
 * @property-read string $shortname
 * @property-read string $idnumber
 * @property-read string $summary Course summary. Field is present if core_course_category::get_courses()
 *     was called with option 'summary'. Otherwise will be retrieved from DB on first request
 * @property-read int $summaryformat Summary format. Field is present if core_course_category::get_courses()
 *     was called with option 'summary'. Otherwise will be retrieved from DB on first request
 * @property-read string $format Course format. Retrieved from DB on first request
 * @property-read int $showgrades Retrieved from DB on first request
 * @property-read int $newsitems Retrieved from DB on first request
 * @property-read int $startdate
 * @property-read int $enddate
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
class core_course_list_element implements IteratorAggregate {

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
            core_course_category::preload_course_contacts($courses);
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

        if (!$this->has_course_contacts()) {
            // No course contacts exist.
            return array();
        }

        if ($this->coursecontacts === null) {
            $this->coursecontacts = array();

            $context = context_course::instance($this->id);

            $canviewfullnames = has_capability('moodle/site:viewfullnames', $context);

            $displayall = get_config('core', 'coursecontactduplicates');

            foreach ($this->record->managers as $ruser) {
                $processed = array_key_exists($ruser->id, $this->coursecontacts);
                if (!$displayall && $processed) {
                    continue;
                }

                $role = (object)[
                        'id'          => $ruser->roleid,
                        'name'        => $ruser->rolename,
                        'shortname'   => $ruser->roleshortname,
                        'coursealias' => $ruser->rolecoursealias,
                ];
                $role->displayname = role_get_name($role, $context, ROLENAME_ALIAS);

                if (!$processed) {
                    $user = username_load_fields_from_object((object)[], $ruser, null, ['id', 'username']);
                    $this->coursecontacts[$ruser->id] = [
                            'user'     => $user,
                            'username' => fullname($user, $canviewfullnames),

                            // List of all roles.
                            'roles'    => [],

                            // Primary role of this user.
                            'role'     => $role,
                            'rolename' => $role->displayname,
                    ];
                }
                $this->coursecontacts[$ruser->id]['roles'][$ruser->roleid] = $role;
            }
        }
        return $this->coursecontacts;
    }

    /**
     * Returns custom fields data for this course
     *
     * @return \core_customfield\data_controller[]
     */
    public function get_custom_fields() : array {
        if (!isset($this->record->customfields)) {
            $this->record->customfields = \core_course\customfield\course_handler::create()->get_instance_data($this->id);
        }
        return $this->record->customfields;
    }

    /**
     * Does this course have custom fields
     *
     * @return bool
     */
    public function has_custom_fields() : bool {
        $customfields = $this->get_custom_fields();
        return !empty($customfields);
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
     * To do that please call require_login with the course, or if not possible call
     * {@link core_course_list_element::can_access()}
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
     * To do that please call require_login with the course, or if not possible call
     * {@link core_course_list_element::can_access()}
     *
     * @return bool
     */
    public function can_change_visibility() {
        // You must be able to both hide a course and view the hidden course.
        return has_all_capabilities(array('moodle/course:visibility', 'moodle/course:viewhiddencourses'),
            $this->get_context());
    }

    /**
     * Returns the context for this course.
     * @return context_course
     */
    public function get_context() {
        return context_course::instance($this->__get('id'));
    }

    /**
     * Returns true if the current user can review enrolments for this course.
     *
     * Note: this function does not check that the current user can access the course.
     * To do that please call require_login with the course, or if not possible call
     * {@link core_course_list_element::can_access()}
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
     * To do that please call require_login with the course, or if not possible call
     * {@link core_course_list_element::can_access()}
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
     * To do that please call require_login with the course, or if not possible call
     * {@link core_course_list_element::can_access()}
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
     * To do that please call require_login with the course, or if not possible call
     * {@link core_course_list_element::can_access()}
     *
     * @return bool
     */
    public function can_restore() {
        return has_capability('moodle/restore:restorecourse', $this->get_context());
    }
}
