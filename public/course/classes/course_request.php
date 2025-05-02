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

use core\context;
use core\context\course as context_course;
use core\context\coursecat as context_coursecat;
use core\context\system as context_system;
use core\exception\coding_exception;
use core\exception\moodle_exception;
use core_course_category;
use restore_dbops;
use stdClass;

/**
 * This class pertains to course requests and contains methods associated with
 * create, approving, and removing course requests.
 *
 * Please note we do not allow embedded images here because there is no context
 * to store them with proper access control.
 *
 * @package   core_course
 * @copyright 2009 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 *
 * @property-read int $id
 * @property-read string $fullname
 * @property-read string $shortname
 * @property-read string $summary
 * @property-read int $summaryformat
 * @property-read int $summarytrust
 * @property-read string $reason
 * @property-read int $requester
 */
class course_request {
    /**
     * This is the stdClass that stores the properties for the course request
     * and is externally accessed through the __get magic method
     * @var stdClass
     */
    protected $properties;

    /**
     * An array of options for the summary editor used by course request forms.
     *
     * This is initially set by {@see summary_editor_options()}
     * @var array
     */
    protected static $summaryeditoroptions;

    /**
     * Static function to prepare the summary editor for working with a course request.
     *
     * @param null|stdClass $data Optional, an object containing the default values
     *                       for the form, these may be modified when preparing the
     *                       editor so this should be called before creating the form
     * @return stdClass An object that can be used to set the default values for
     *                   an mforms form
     */
    public static function prepare($data = null) {
        if ($data === null) {
            $data = new stdClass();
        }
        $data = file_prepare_standard_editor($data, 'summary', self::summary_editor_options());
        return $data;
    }

    /**
     * Static function to create a new course request when passed an array of properties
     * for it.
     *
     * This function also handles saving any files that may have been used in the editor
     *
     * @param stdClass $data
     * @return course_request The newly created course request
     */
    public static function create($data) {
        global $USER, $DB, $CFG;
        $data->requester = $USER->id;

        // Setting the default category if none set.
        if (empty($data->category) || !empty($CFG->lockrequestcategory)) {
            $data->category = $CFG->defaultrequestcategory;
        }

        // Summary is a required field so copy the text over.
        $data->summary       = $data->summary_editor['text'];
        $data->summaryformat = $data->summary_editor['format'];

        $data->id = $DB->insert_record('course_request', $data);

        // Create a new course_request object and return it.
        $request = new course_request($data);

        // Notify the admin if required.
        if ($users = get_users_from_config($CFG->courserequestnotify, 'moodle/site:approvecourse')) {
            $category = \core_course_category::get($data->category);

            $a = new stdClass();
            $a->link = "$CFG->wwwroot/course/pending.php";
            $a->user = fullname($USER);
            $a->shortname = format_string($data->shortname, true, ['context' => $category->get_context()]);
            $a->fullname = format_string($data->fullname, true, ['context' => $category->get_context()]);
            $a->category = $category->get_formatted_name();
            $a->reason = format_text($data->reason, FORMAT_PLAIN);
            $subject = get_string('courserequest');
            $message = get_string('courserequestnotifyemail', 'admin', $a);
            foreach ($users as $user) {
                $request->notify($user, $USER, 'courserequested', $subject, $message);
            }
        }

        return $request;
    }

    /**
     * Returns an array of options to use with a summary editor
     *
     * @uses course_request::$summaryeditoroptions
     * @return array An array of options to use with the editor
     */
    public static function summary_editor_options() {
        global $CFG;
        if (self::$summaryeditoroptions === null) {
            self::$summaryeditoroptions = ['maxfiles' => 0, 'maxbytes' => 0];
        }
        return self::$summaryeditoroptions;
    }

    /**
     * Loads the properties for this course request object. Id is required and if
     * only id is provided then we load the rest of the properties from the database
     *
     * @param stdClass|int $properties Either an object containing properties
     *                      or the course_request id to load
     */
    public function __construct($properties) {
        global $DB;
        if (empty($properties->id)) {
            if (empty($properties)) {
                throw new coding_exception('You must provide a course request id when creating a course_request object');
            }
            $id = $properties;
            $properties = new stdClass();
            $properties->id = (int)$id;
            unset($id);
        }
        if (empty($properties->requester)) {
            if (!($this->properties = $DB->get_record('course_request', ['id' => $properties->id]))) {
                throw new moodle_exception('unknowncourserequest');
            }
        } else {
            $this->properties = $properties;
        }
        $this->properties->collision = null;
    }

    /**
     * Returns the requested property
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key) {
        return $this->properties->$key;
    }

    /**
     * Override this to ensure empty($request->blah) calls return a reliable answer...
     *
     * This is required because we define the __get method
     *
     * @param mixed $key
     * @return bool True is it not empty, false otherwise
     */
    public function __isset($key) {
        return (!empty($this->properties->$key));
    }

    /**
     * Returns the user who requested this course.
     *
     * Uses a static var to cache the results and cut down the number of db queries
     *
     * @return stdClass The user who requested the course
     */
    public function get_requester() {
        global $DB;
        static $requesters = [];
        if (!array_key_exists($this->properties->requester, $requesters)) {
            $requesters[$this->properties->requester] = $DB->get_record('user', ['id' => $this->properties->requester]);
        }
        return $requesters[$this->properties->requester];
    }

    /**
     * Checks that the shortname used by the course does not conflict with any other
     * courses that exist
     *
     * @param string|null $shortnamemark The string to append to the requests shortname
     *                     should a conflict be found
     * @return bool true is there is a conflict, false otherwise
     */
    public function check_shortname_collision($shortnamemark = '[*]') {
        global $DB;

        if ($this->properties->collision !== null) {
            return $this->properties->collision;
        }

        if (empty($this->properties->shortname)) {
            debugging('Attempting to check a course request shortname before it has been set', DEBUG_DEVELOPER);
            $this->properties->collision = false;
        } else if ($DB->record_exists('course', ['shortname' => $this->properties->shortname])) {
            if (!empty($shortnamemark)) {
                $this->properties->shortname .= ' ' . $shortnamemark;
            }
            $this->properties->collision = true;
        } else {
            $this->properties->collision = false;
        }
        return $this->properties->collision;
    }

    /**
     * Checks user capability to approve a requested course
     *
     * If course was requested without category for some reason (might happen if $CFG->defaultrequestcategory is
     * misconfigured), we check capabilities 'moodle/site:approvecourse' and 'moodle/course:changecategory'.
     *
     * @return bool
     */
    public function can_approve() {
        global $CFG;
        $category = null;
        if ($this->properties->category) {
            $category = core_course_category::get($this->properties->category, IGNORE_MISSING);
        } else if ($CFG->defaultrequestcategory) {
            $category = core_course_category::get($CFG->defaultrequestcategory, IGNORE_MISSING);
        }
        if ($category) {
            return has_capability('moodle/site:approvecourse', $category->get_context());
        }

        // We can not determine the context where the course should be created. The approver should have
        // both capabilities to approve courses and change course category in the system context.
        return has_all_capabilities(['moodle/site:approvecourse', 'moodle/course:changecategory'], context_system::instance());
    }

    /**
     * Returns the category where this course request should be created
     *
     * Note that we don't check here that user has a capability to view
     * hidden categories if he has capabilities 'moodle/site:approvecourse' and
     * 'moodle/course:changecategory'
     *
     * @return core_course_category
     */
    public function get_category() {
        global $CFG;
        if (
            $this->properties->category
            && ($category = core_course_category::get($this->properties->category, IGNORE_MISSING))
        ) {
            return $category;
        } else if (
            $CFG->defaultrequestcategory
            && ($category = core_course_category::get($CFG->defaultrequestcategory, IGNORE_MISSING))
        ) {
            return $category;
        } else {
            return core_course_category::get_default();
        }
    }

    /**
     * This function approves the request turning it into a course
     *
     * This function converts the course request into a course, at the same time
     * transferring any files used in the summary to the new course and then removing
     * the course request and the files associated with it.
     *
     * @return int The id of the course that was created from this request
     */
    public function approve() {
        global $CFG, $DB, $USER;

        require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');

        $user = $DB->get_record('user', ['id' => $this->properties->requester, 'deleted' => 0], '*', MUST_EXIST);

        $courseconfig = get_config('moodlecourse');

        // Transfer appropriate settings.
        $data = clone($this->properties);
        unset($data->id);
        unset($data->reason);
        unset($data->requester);

        // Set category.
        $category = $this->get_category();
        $data->category = $category->id;

        // Set misc settings.
        $data->requested = 1;

        // Apply course default settings.
        $data->format             = $courseconfig->format;
        $data->newsitems          = $courseconfig->newsitems;
        $data->showgrades         = $courseconfig->showgrades;
        $data->showreports        = $courseconfig->showreports;
        $data->maxbytes           = $courseconfig->maxbytes;
        $data->groupmode          = $courseconfig->groupmode;
        $data->groupmodeforce     = $courseconfig->groupmodeforce;
        $data->visible            = $courseconfig->visible;
        $data->visibleold         = $data->visible;
        $data->lang               = $courseconfig->lang;
        $data->enablecompletion   = $courseconfig->enablecompletion;
        $data->numsections        = $courseconfig->numsections;
        $data->startdate          = usergetmidnight(time());
        if ($courseconfig->courseenddateenabled) {
            $data->enddate        = usergetmidnight(time()) + $courseconfig->courseduration;
        }

        [$data->fullname, $data->shortname] = restore_dbops::calculate_course_names(0, $data->fullname, $data->shortname);

        $course = create_course($data);
        $context = context_course::instance($course->id, MUST_EXIST);

        // Add enrol instances.
        if (!$DB->record_exists('enrol', ['courseid' => $course->id, 'enrol' => 'manual'])) {
            if ($manual = enrol_get_plugin('manual')) {
                $manual->add_default_instance($course);
            }
        }

        // Enrol the requester as teacher if necessary.
        if (
            !empty($CFG->creatornewroleid)
            && !is_viewing($context, $user, 'moodle/role:assign')
            && !is_enrolled($context, $user, 'moodle/role:assign')
        ) {
            enrol_try_internal_enrol($course->id, $user->id, $CFG->creatornewroleid);
        }

        $this->delete();

        $a = new stdClass();
        $a->name = format_string($course->fullname, true, ['context' => $context]);
        $a->url = course_get_url($course);

        $usernameplaceholders = \core\user::get_name_placeholders($user);
        foreach ($usernameplaceholders as $field => $value) {
            $a->{$field} = $value;
        }

        $this->notify(
            touser: $user,
            fromuser: $USER,
            name: 'courserequestapproved',
            subject: get_string('courseapprovedsubject'),
            message: get_string('courseapprovedemail2', 'moodle', $a),
            courseid: $course->id,
        );

        return $course->id;
    }

    /**
     * Reject a course request
     *
     * This function rejects a course request, emailing the requesting user the
     * provided notice and then removing the request from the database
     *
     * @param string $notice The message to display to the user
     */
    public function reject($notice) {
        global $USER, $DB;
        $user = $DB->get_record('user', ['id' => $this->properties->requester], '*', MUST_EXIST);
        $this->notify(
            touser: $user,
            fromuser: $USER,
            name: 'courserequestrejected',
            subject: get_string('courserejectsubject'),
            message: get_string('courserejectemail', 'moodle', $notice),
        );
        $this->delete();
    }

    /**
     * Deletes the course request and any associated files
     */
    public function delete() {
        global $DB;
        $DB->delete_records('course_request', ['id' => $this->properties->id]);
    }

    /**
     * Send a message from one user to another using events_trigger
     *
     * @param object $touser
     * @param object $fromuser
     * @param string $name
     * @param string $subject
     * @param string $message
     * @param int|null $courseid
     */
    protected function notify($touser, $fromuser, $name, $subject, $message, $courseid = null) {
        $eventdata = new \core\message\message();
        $eventdata->courseid          = empty($courseid) ? SITEID : $courseid;
        $eventdata->component         = 'moodle';
        $eventdata->name              = $name;
        $eventdata->userfrom          = $fromuser;
        $eventdata->userto            = $touser;
        $eventdata->subject           = $subject;
        $eventdata->fullmessage       = $message;
        $eventdata->fullmessageformat = FORMAT_PLAIN;
        $eventdata->fullmessagehtml   = '';
        $eventdata->smallmessage      = '';
        $eventdata->notification      = 1;
        message_send($eventdata);
    }

    /**
     * Checks if current user can request a course in this context
     *
     * @param context $context
     * @return bool
     */
    public static function can_request(context $context) {
        global $CFG;
        if (empty($CFG->enablecourserequests)) {
            return false;
        }
        if (has_capability('moodle/course:create', $context)) {
            return false;
        }

        if ($context instanceof context_system) {
            $defaultcontext = context_coursecat::instance($CFG->defaultrequestcategory, IGNORE_MISSING);
            return $defaultcontext && has_capability('moodle/course:request', $defaultcontext);
        } else if ($context instanceof context_coursecat) {
            if (!$CFG->lockrequestcategory || $CFG->defaultrequestcategory == $context->instanceid) {
                return has_capability('moodle/course:request', $context);
            }
        }
        return false;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(course_request::class, \course_request::class);
