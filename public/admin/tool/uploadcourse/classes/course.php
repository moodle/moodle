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
 * File containing the course class.
 *
 * @package    tool_uploadcourse
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use tool_uploadcourse\permissions;

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
require_once($CFG->dirroot . '/course/lib.php');

/**
 * Course class.
 *
 * @package    tool_uploadcourse
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_uploadcourse_course {

    /** Outcome of the process: creating the course */
    const DO_CREATE = 1;

    /** Outcome of the process: updating the course */
    const DO_UPDATE = 2;

    /** Outcome of the process: deleting the course */
    const DO_DELETE = 3;

    /** @var array assignable roles. */
    protected $assignableroles = [];

    /** @var array Roles context levels. */
    protected $contextlevels = [];

    /** @var array final import data. */
    protected $data = array();

    /** @var array default values. */
    protected $defaults = array();

    /** @var array enrolment data. */
    protected $enrolmentdata;

    /** @var array errors. */
    protected $errors = array();

    /** @var int the ID of the course that had been processed. */
    protected $id;

    /** @var array containing options passed from the processor. */
    protected $importoptions = array();

    /** @var int import mode. Matches tool_uploadcourse_processor::MODE_* */
    protected $mode;

    /** @var array course import options. */
    protected $options = array();

    /** @var int constant value of self::DO_*, what to do with that course */
    protected $do;

    /** @var bool set to true once we have prepared the course */
    protected $prepared = false;

    /** @var bool set to true once we have started the process of the course */
    protected $processstarted = false;

    /** @var array course import data. */
    protected $rawdata = array();

    /** @var array restore directory. */
    protected $restoredata;

    /** @var string course shortname. */
    protected $shortname;

    /** @var array errors. */
    protected $statuses = array();

    /** @var int update mode. Matches tool_uploadcourse_processor::UPDATE_* */
    protected $updatemode;

    /** @var array fields allowed as course data. */
    static protected $validfields = array('fullname', 'shortname', 'idnumber', 'category', 'visible', 'startdate', 'enddate',
        'summary', 'format', 'theme', 'lang', 'newsitems', 'showgrades', 'showreports', 'legacyfiles', 'maxbytes',
        'groupmode', 'groupmodeforce', 'enablecompletion', 'downloadcontent', 'showactivitydates');

    /** @var array fields required on course creation. */
    static protected $mandatoryfields = array('fullname', 'category');

    /** @var array fields which are considered as options. */
    static protected $optionfields = array('delete' => false, 'rename' => null, 'backupfile' => null,
        'templatecourse' => null, 'reset' => false);

    /** @var array options determining what can or cannot be done at an import level. */
    static protected $importoptionsdefaults = array('canrename' => false, 'candelete' => false, 'canreset' => false,
        'reset' => false, 'restoredir' => null, 'shortnametemplate' => null);

    /**
     * Constructor
     *
     * @param int $mode import mode, constant matching tool_uploadcourse_processor::MODE_*
     * @param int $updatemode update mode, constant matching tool_uploadcourse_processor::UPDATE_*
     * @param array $rawdata raw course data.
     * @param array $defaults default course data.
     * @param array $importoptions import options.
     */
    public function __construct($mode, $updatemode, $rawdata, $defaults = array(), $importoptions = array()) {

        if ($mode !== tool_uploadcourse_processor::MODE_CREATE_NEW &&
                $mode !== tool_uploadcourse_processor::MODE_CREATE_ALL &&
                $mode !== tool_uploadcourse_processor::MODE_CREATE_OR_UPDATE &&
                $mode !== tool_uploadcourse_processor::MODE_UPDATE_ONLY) {
            throw new coding_exception('Incorrect mode.');
        } else if ($updatemode !== tool_uploadcourse_processor::UPDATE_NOTHING &&
                $updatemode !== tool_uploadcourse_processor::UPDATE_ALL_WITH_DATA_ONLY &&
                $updatemode !== tool_uploadcourse_processor::UPDATE_ALL_WITH_DATA_OR_DEFAUTLS &&
                $updatemode !== tool_uploadcourse_processor::UPDATE_MISSING_WITH_DATA_OR_DEFAUTLS) {
            throw new coding_exception('Incorrect update mode.');
        }

        $this->mode = $mode;
        $this->updatemode = $updatemode;

        if (isset($rawdata['shortname'])) {
            $this->shortname = $rawdata['shortname'];
        }
        $this->rawdata = $rawdata;
        $this->defaults = $defaults;

        // Extract course options.
        foreach (self::$optionfields as $option => $default) {
            $this->options[$option] = isset($rawdata[$option]) ? $rawdata[$option] : $default;
        }

        // Import options.
        foreach (self::$importoptionsdefaults as $option => $default) {
            $this->importoptions[$option] = isset($importoptions[$option]) ? $importoptions[$option] : $default;
        }
    }

    /**
     * Does the mode allow for course creation?
     *
     * @return bool
     */
    public function can_create() {
        return in_array($this->mode, array(tool_uploadcourse_processor::MODE_CREATE_ALL,
            tool_uploadcourse_processor::MODE_CREATE_NEW,
            tool_uploadcourse_processor::MODE_CREATE_OR_UPDATE)
        );
    }

    /**
     * Does the mode allow for course deletion?
     *
     * @return bool
     */
    public function can_delete() {
        return $this->importoptions['candelete'];
    }

    /**
     * Does the mode only allow for course creation?
     *
     * @return bool
     */
    public function can_only_create() {
        return in_array($this->mode, array(tool_uploadcourse_processor::MODE_CREATE_ALL,
            tool_uploadcourse_processor::MODE_CREATE_NEW));
    }

    /**
     * Does the mode allow for course rename?
     *
     * @return bool
     */
    public function can_rename() {
        return $this->importoptions['canrename'];
    }

    /**
     * Does the mode allow for course reset?
     *
     * @return bool
     */
    public function can_reset() {
        return $this->importoptions['canreset'];
    }

    /**
     * Does the mode allow for course update?
     *
     * @return bool
     */
    public function can_update() {
        return in_array($this->mode,
                array(
                    tool_uploadcourse_processor::MODE_UPDATE_ONLY,
                    tool_uploadcourse_processor::MODE_CREATE_OR_UPDATE)
                ) && $this->updatemode != tool_uploadcourse_processor::UPDATE_NOTHING;
    }

    /**
     * Can we use default values?
     *
     * @return bool
     */
    public function can_use_defaults() {
        return in_array($this->updatemode, array(tool_uploadcourse_processor::UPDATE_MISSING_WITH_DATA_OR_DEFAUTLS,
            tool_uploadcourse_processor::UPDATE_ALL_WITH_DATA_OR_DEFAUTLS));
    }

    /**
     * Delete the current course.
     *
     * @return bool
     */
    protected function delete() {
        global $DB;
        $this->id = $DB->get_field_select('course', 'id', 'shortname = :shortname',
            array('shortname' => $this->shortname), MUST_EXIST);
        return delete_course($this->id, false);
    }

    /**
     * Log an error
     *
     * @param string $code error code.
     * @param string $message error message.
     * @return void
     */
    protected function error($code, string $message) {
        if (array_key_exists($code, $this->errors)) {
            throw new coding_exception('Error code already defined');
        }
        $this->errors[$code] = $message;
    }

    /**
     * Return whether the course exists or not.
     *
     * @param string $shortname the shortname to use to check if the course exists. Falls back on $this->shortname if empty.
     * @return bool
     */
    protected function exists($shortname = null) {
        global $DB;
        if (is_null($shortname)) {
            $shortname = $this->shortname;
        }
        if (!empty($shortname) || is_numeric($shortname)) {
            return $DB->record_exists('course', array('shortname' => $shortname));
        }
        return false;
    }

    /**
     * Return the data that will be used upon saving.
     *
     * @return null|array
     */
    public function get_data() {
        return $this->data;
    }

    /**
     * Return the errors found during preparation.
     *
     * @return array
     */
    public function get_errors() {
        return $this->errors;
    }

    /**
     * Return array of valid fields for default values
     *
     * @return array
     */
    protected function get_valid_fields() {
        return array_merge(self::$validfields, \tool_uploadcourse_helper::get_custom_course_field_names());
    }

    /**
     * Assemble the course data based on defaults.
     *
     * This returns the final data to be passed to create_course().
     *
     * @param array $data current data.
     * @return array
     */
    protected function get_final_create_data($data) {
        foreach ($this->get_valid_fields() as $field) {
            if (!isset($data[$field]) && isset($this->defaults[$field])) {
                $data[$field] = $this->defaults[$field];
            }
        }
        $data['shortname'] = $this->shortname;
        return $data;
    }

    /**
     * Assemble the course data based on defaults.
     *
     * This returns the final data to be passed to update_course().
     *
     * @param array $data current data.
     * @param bool $usedefaults are defaults allowed?
     * @param bool $missingonly ignore fields which are already set.
     * @return array
     */
    protected function get_final_update_data($data, $usedefaults = false, $missingonly = false) {
        global $DB;
        $newdata = array();
        $existingdata = $DB->get_record('course', array('shortname' => $this->shortname));
        foreach ($this->get_valid_fields() as $field) {
            if ($missingonly) {
                if (isset($existingdata->$field) and $existingdata->$field !== '') {
                    continue;
                }
            }
            if (isset($data[$field])) {
                $newdata[$field] = $data[$field];
            } else if ($usedefaults && isset($this->defaults[$field])) {
                $newdata[$field] = $this->defaults[$field];
            }
        }
        $newdata['id'] =  $existingdata->id;
        return $newdata;
    }

    /**
     * Return the ID of the processed course.
     *
     * @return int|null
     */
    public function get_id() {
        if (!$this->processstarted) {
            throw new coding_exception('The course has not been processed yet!');
        }
        return $this->id;
    }

    /**
     * Get the directory of the object to restore.
     *
     * @return string|false|null subdirectory in $CFG->backuptempdir/..., false when an error occured
     *                           and null when there is simply nothing.
     */
    protected function get_restore_content_dir() {
        $backupfile = null;
        $shortname = null;

        if (!empty($this->options['backupfile'])) {
            $backupfile = $this->options['backupfile'];
        } else if (!empty($this->options['templatecourse']) || is_numeric($this->options['templatecourse'])) {
            $shortname = $this->options['templatecourse'];
        }

        $errors = array();
        $dir = tool_uploadcourse_helper::get_restore_content_dir($backupfile, $shortname, $errors);
        if (!empty($errors)) {
            foreach ($errors as $key => $message) {
                $this->error($key, $message);
            }
            return false;
        } else if ($dir === false) {
            // We want to return null when nothing was wrong, but nothing was found.
            $dir = null;
        }

        if (empty($dir) && !empty($this->importoptions['restoredir'])) {
            $dir = $this->importoptions['restoredir'];
        }

        return $dir;
    }

    /**
     * Return the errors found during preparation.
     *
     * @return array
     */
    public function get_statuses() {
        return $this->statuses;
    }

    /**
     * Return whether there were errors with this course.
     *
     * @return boolean
     */
    public function has_errors() {
        return !empty($this->errors);
    }

    /**
     * Validates and prepares the data.
     *
     * @return bool false is any error occured.
     */
    public function prepare() {
        global $DB, $SITE, $CFG;

        $this->prepared = true;

        // Validate the shortname.
        if (!empty($this->shortname) || is_numeric($this->shortname)) {
            if ($this->shortname !== clean_param($this->shortname, PARAM_TEXT)) {
                $this->error('invalidshortname', new lang_string('invalidshortname', 'tool_uploadcourse'));
                return false;
            }

            // Ensure we don't overflow the maximum length of the shortname field.
            if (core_text::strlen($this->shortname) > 255) {
                $this->error('invalidshortnametoolong', new lang_string('invalidshortnametoolong', 'tool_uploadcourse', 255));
                return false;
            }
        }

        $exists = $this->exists();

        // Do we want to delete the course?
        if ($this->options['delete']) {
            if (!$exists) {
                $this->error('cannotdeletecoursenotexist', new lang_string('cannotdeletecoursenotexist', 'tool_uploadcourse'));
                return false;
            } else if (!$this->can_delete()) {
                $this->error('coursedeletionnotallowed', new lang_string('coursedeletionnotallowed', 'tool_uploadcourse'));
                return false;
            }

            if ($error = permissions::check_permission_to_delete($this->shortname)) {
                $this->error('coursedeletionpermission', $error);
                return false;
            }

            $this->do = self::DO_DELETE;
            return true;
        }

        // Can we create/update the course under those conditions?
        if ($exists) {
            if ($this->mode === tool_uploadcourse_processor::MODE_CREATE_NEW) {
                $this->error('courseexistsanduploadnotallowed',
                    new lang_string('courseexistsanduploadnotallowed', 'tool_uploadcourse'));
                return false;
            } else if ($this->can_update()) {
                // We can never allow for any front page changes!
                if ($this->shortname == $SITE->shortname) {
                    $this->error('cannotupdatefrontpage', new lang_string('cannotupdatefrontpage', 'tool_uploadcourse'));
                    return false;
                }
            }
        } else {
            if (!$this->can_create()) {
                $this->error('coursedoesnotexistandcreatenotallowed',
                    new lang_string('coursedoesnotexistandcreatenotallowed', 'tool_uploadcourse'));
                return false;
            }
        }

        // Basic data.
        $coursedata = array();
        foreach ($this->rawdata as $field => $value) {
            if (!in_array($field, self::$validfields)) {
                continue;
            } else if ($field == 'shortname') {
                // Let's leave it apart from now, use $this->shortname only.
                continue;
            }
            $coursedata[$field] = $value;
        }

        $mode = $this->mode;
        $updatemode = $this->updatemode;
        $usedefaults = $this->can_use_defaults();

        // Resolve the category, and fail if not found.
        $errors = array();
        $catid = tool_uploadcourse_helper::resolve_category($this->rawdata, $errors);
        if (empty($errors)) {
            $coursedata['category'] = $catid;
        } else {
            foreach ($errors as $key => $message) {
                $this->error($key, $message);
            }
            return false;
        }

        // Ensure we don't overflow the maximum length of the fullname field.
        if (
            !empty($coursedata['fullname']) &&
            core_text::strlen($coursedata['fullname']) > \core_course\constants::FULLNAME_MAXIMUM_LENGTH
        ) {
            $this->error('invalidfullnametoolong', new lang_string('invalidfullnametoolong', 'tool_uploadcourse',
                \core_course\constants::FULLNAME_MAXIMUM_LENGTH));
            return false;
        }

        // If the course does not exist, or will be forced created.
        if (!$exists || $mode === tool_uploadcourse_processor::MODE_CREATE_ALL) {

            // Mandatory fields upon creation.
            $errors = array();
            foreach (self::$mandatoryfields as $field) {
                if ((!isset($coursedata[$field]) || $coursedata[$field] === '') &&
                        (!isset($this->defaults[$field]) || $this->defaults[$field] === '')) {
                    $errors[] = $field;
                }
            }
            if (!empty($errors)) {
                $this->error('missingmandatoryfields', new lang_string('missingmandatoryfields', 'tool_uploadcourse',
                    implode(', ', $errors)));
                return false;
            }
        }

        // Should the course be renamed?
        if (!empty($this->options['rename']) || is_numeric($this->options['rename'])) {
            if (!$this->can_update()) {
                $this->error('canonlyrenameinupdatemode', new lang_string('canonlyrenameinupdatemode', 'tool_uploadcourse'));
                return false;
            } else if (!$exists) {
                $this->error('cannotrenamecoursenotexist', new lang_string('cannotrenamecoursenotexist', 'tool_uploadcourse'));
                return false;
            } else if (!$this->can_rename()) {
                $this->error('courserenamingnotallowed', new lang_string('courserenamingnotallowed', 'tool_uploadcourse'));
                return false;
            } else if ($this->options['rename'] !== clean_param($this->options['rename'], PARAM_TEXT)) {
                $this->error('invalidshortname', new lang_string('invalidshortname', 'tool_uploadcourse'));
                return false;
            } else if ($this->exists($this->options['rename'])) {
                $this->error('cannotrenameshortnamealreadyinuse',
                    new lang_string('cannotrenameshortnamealreadyinuse', 'tool_uploadcourse'));
                return false;
            } else if (isset($coursedata['idnumber']) &&
                    $DB->count_records_select('course', 'idnumber = :idn AND shortname != :sn',
                    array('idn' => $coursedata['idnumber'], 'sn' => $this->shortname)) > 0) {
                $this->error('cannotrenameidnumberconflict', new lang_string('cannotrenameidnumberconflict', 'tool_uploadcourse'));
                return false;
            }
            $coursedata['shortname'] = $this->options['rename'];
            $this->status('courserenamed', new lang_string('courserenamed', 'tool_uploadcourse',
                array('from' => $this->shortname, 'to' => $coursedata['shortname'])));
        }

        // Should we generate a shortname?
        if (empty($this->shortname) && !is_numeric($this->shortname)) {
            if (empty($this->importoptions['shortnametemplate'])) {
                $this->error('missingshortnamenotemplate', new lang_string('missingshortnamenotemplate', 'tool_uploadcourse'));
                return false;
            } else if (!$this->can_only_create()) {
                $this->error('cannotgenerateshortnameupdatemode',
                    new lang_string('cannotgenerateshortnameupdatemode', 'tool_uploadcourse'));
                return false;
            } else {
                $newshortname = tool_uploadcourse_helper::generate_shortname($coursedata,
                    $this->importoptions['shortnametemplate']);
                if (is_null($newshortname)) {
                    $this->error('generatedshortnameinvalid', new lang_string('generatedshortnameinvalid', 'tool_uploadcourse'));
                    return false;
                } else if ($this->exists($newshortname)) {
                    if ($mode === tool_uploadcourse_processor::MODE_CREATE_NEW) {
                        $this->error('generatedshortnamealreadyinuse',
                            new lang_string('generatedshortnamealreadyinuse', 'tool_uploadcourse'));
                        return false;
                    }
                    $exists = true;
                }
                $this->status('courseshortnamegenerated', new lang_string('courseshortnamegenerated', 'tool_uploadcourse',
                    $newshortname));
                $this->shortname = $newshortname;
            }
        }

        // If exists, but we only want to create courses, increment the shortname.
        if ($exists && $mode === tool_uploadcourse_processor::MODE_CREATE_ALL) {
            $original = $this->shortname;
            $this->shortname = tool_uploadcourse_helper::increment_shortname($this->shortname);
            $exists = false;
            if ($this->shortname != $original) {
                $this->status('courseshortnameincremented', new lang_string('courseshortnameincremented', 'tool_uploadcourse',
                    array('from' => $original, 'to' => $this->shortname)));
                if (isset($coursedata['idnumber'])) {
                    $originalidn = $coursedata['idnumber'];
                    $coursedata['idnumber'] = tool_uploadcourse_helper::increment_idnumber($coursedata['idnumber']);
                    if ($originalidn != $coursedata['idnumber']) {
                        $this->status('courseidnumberincremented', new lang_string('courseidnumberincremented', 'tool_uploadcourse',
                            array('from' => $originalidn, 'to' => $coursedata['idnumber'])));
                    }
                }
            }
        }

        // If the course does not exist, ensure that the ID number is not taken.
        if (!$exists && isset($coursedata['idnumber'])) {
            if ($DB->count_records_select('course', 'idnumber = :idn', array('idn' => $coursedata['idnumber'])) > 0) {
                $this->error('idnumberalreadyinuse', new lang_string('idnumberalreadyinuse', 'tool_uploadcourse'));
                return false;
            }
        }

        // Course start date.
        if (!empty($coursedata['startdate'])) {
            $coursedata['startdate'] = strtotime($coursedata['startdate']);
        }

        // Course end date.
        if (!empty($coursedata['enddate'])) {
            $coursedata['enddate'] = strtotime($coursedata['enddate']);
        }

        // If lang is specified, check the user is allowed to set that field.
        if (!empty($coursedata['lang'])) {
            if ($exists) {
                $courseid = $DB->get_field('course', 'id', ['shortname' => $this->shortname]);
                if (!has_capability('moodle/course:setforcedlanguage', context_course::instance($courseid))) {
                    $this->error('cannotforcelang', new lang_string('cannotforcelang', 'tool_uploadcourse'));
                    return false;
                }
            } else {
                $catcontext = context_coursecat::instance($coursedata['category']);
                if (!guess_if_creator_will_have_course_capability('moodle/course:setforcedlanguage', $catcontext)) {
                    $this->error('cannotforcelang', new lang_string('cannotforcelang', 'tool_uploadcourse'));
                    return false;
                }
            }
        }

        // Ultimate check mode vs. existence.
        switch ($mode) {
            case tool_uploadcourse_processor::MODE_CREATE_NEW:
            case tool_uploadcourse_processor::MODE_CREATE_ALL:
                if ($exists) {
                    $this->error('courseexistsanduploadnotallowed',
                        new lang_string('courseexistsanduploadnotallowed', 'tool_uploadcourse'));
                    return false;
                }
                break;
            case tool_uploadcourse_processor::MODE_UPDATE_ONLY:
                if (!$exists) {
                    $this->error('coursedoesnotexistandcreatenotallowed',
                        new lang_string('coursedoesnotexistandcreatenotallowed', 'tool_uploadcourse'));
                    return false;
                }
                // No break!
            case tool_uploadcourse_processor::MODE_CREATE_OR_UPDATE:
                if ($exists) {
                    if ($updatemode === tool_uploadcourse_processor::UPDATE_NOTHING) {
                        $this->error('updatemodedoessettonothing',
                            new lang_string('updatemodedoessettonothing', 'tool_uploadcourse'));
                        return false;
                    }
                }
                break;
            default:
                // O_o Huh?! This should really never happen here!
                $this->error('unknownimportmode', new lang_string('unknownimportmode', 'tool_uploadcourse'));
                return false;
        }

        // Get final data.
        if ($exists) {
            $missingonly = ($updatemode === tool_uploadcourse_processor::UPDATE_MISSING_WITH_DATA_OR_DEFAUTLS);
            $coursedata = $this->get_final_update_data($coursedata, $usedefaults, $missingonly);

            // Make sure we are not trying to mess with the front page, though we should never get here!
            if ($coursedata['id'] == $SITE->id) {
                $this->error('cannotupdatefrontpage', new lang_string('cannotupdatefrontpage', 'tool_uploadcourse'));
                return false;
            }

            if ($error = permissions::check_permission_to_update($coursedata)) {
                $this->error('cannotupdatepermission', $error);
                return false;
            }

            $this->do = self::DO_UPDATE;
        } else {
            $coursedata = $this->get_final_create_data($coursedata);

            if ($error = permissions::check_permission_to_create($coursedata)) {
                $this->error('courseuploadnotallowed', $error);
                return false;
            }

            $this->do = self::DO_CREATE;
        }

        // Validate course start and end dates.
        if ($exists) {
            // We also check existing start and end dates if we are updating an existing course.
            $existingdata = $DB->get_record('course', array('shortname' => $this->shortname));
            if (empty($coursedata['startdate'])) {
                $coursedata['startdate'] = $existingdata->startdate;
            }
            if (empty($coursedata['enddate'])) {
                $coursedata['enddate'] = $existingdata->enddate;
            }
        }
        if ($errorcode = course_validate_dates($coursedata)) {
            $this->error($errorcode, new lang_string($errorcode, 'error'));
            return false;
        }

        // Add role renaming.
        $errors = array();
        $rolenames = tool_uploadcourse_helper::get_role_names($this->rawdata, $errors);
        if (!empty($errors)) {
            foreach ($errors as $key => $message) {
                $this->error($key, $message);
            }
            return false;
        }
        foreach ($rolenames as $rolekey => $rolename) {
            $coursedata[$rolekey] = $rolename;
        }

        // Custom fields. If the course already exists and mode isn't set to force creation, we can use its context.
        if ($exists && $mode !== tool_uploadcourse_processor::MODE_CREATE_ALL) {
            $context = context_course::instance($coursedata['id']);
        } else {
            // The category ID is taken from the defaults if it exists, otherwise from course data.
            $context = context_coursecat::instance($this->defaults['category'] ?? $coursedata['category']);
        }
        $customfielddata = tool_uploadcourse_helper::get_custom_course_field_data($this->rawdata, $this->defaults, $context,
            $errors);
        if (!empty($errors)) {
            foreach ($errors as $key => $message) {
                $this->error($key, $message);
            }

            return false;
        }

        foreach ($customfielddata as $name => $value) {
            $coursedata[$name] = $value;
        }

        // Some validation.
        if (!empty($coursedata['format']) && !in_array($coursedata['format'], tool_uploadcourse_helper::get_course_formats())) {
            $this->error('invalidcourseformat', new lang_string('invalidcourseformat', 'tool_uploadcourse'));
            return false;
        }

        // Add data for course format options.
        if (isset($coursedata['format']) || $exists) {
            if (isset($coursedata['format'])) {
                $courseformat = course_get_format((object)['format' => $coursedata['format']]);
            } else {
                $courseformat = course_get_format($existingdata);
            }
            $coursedata += $courseformat->validate_course_format_options($this->rawdata);
        }

        // Special case, 'numsections' is not a course format option any more but still should apply from the template course,
        // if any, and otherwise from defaults.
        if (!$exists || !array_key_exists('numsections', $coursedata)) {
            if (isset($this->rawdata['numsections']) && is_numeric($this->rawdata['numsections'])) {
                $coursedata['numsections'] = (int)$this->rawdata['numsections'];
            } else if (isset($this->options['templatecourse'])) {
                $numsections = tool_uploadcourse_helper::get_coursesection_count($this->options['templatecourse']);
                if ($numsections != 0) {
                    $coursedata['numsections'] = $numsections;
                } else {
                    $coursedata['numsections'] = get_config('moodlecourse', 'numsections');
                }
            } else {
                $coursedata['numsections'] = get_config('moodlecourse', 'numsections');
            }
        }

        // Visibility can only be 0 or 1.
        if (!empty($coursedata['visible']) AND !($coursedata['visible'] == 0 OR $coursedata['visible'] == 1)) {
            $this->error('invalidvisibilitymode', new lang_string('invalidvisibilitymode', 'tool_uploadcourse'));
            return false;
        }

        // Ensure that user is allowed to configure course content download and the field contains a valid value.
        if (isset($coursedata['downloadcontent'])) {
            if (!$CFG->downloadcoursecontentallowed ||
                    !has_capability('moodle/course:configuredownloadcontent', $context)) {

                $this->error('downloadcontentnotallowed', new lang_string('downloadcontentnotallowed', 'tool_uploadcourse'));
                return false;
            }

            $downloadcontentvalues = [
                DOWNLOAD_COURSE_CONTENT_DISABLED,
                DOWNLOAD_COURSE_CONTENT_ENABLED,
                DOWNLOAD_COURSE_CONTENT_SITE_DEFAULT,
            ];
            if (!in_array($coursedata['downloadcontent'], $downloadcontentvalues)) {
                $this->error('invaliddownloadcontent', new lang_string('invaliddownloadcontent', 'tool_uploadcourse'));
                return false;
            }
        }

        // Saving data.
        $this->data = $coursedata;

        // Get enrolment data. Where the course already exists, we can also perform validation.
        // Some data is impossible to validate without the existing course, we will do it again during actual upload.
        $this->enrolmentdata = tool_uploadcourse_helper::get_enrolment_data($this->rawdata);
        $courseid = $coursedata['id'] ?? 0;
        $errors = $this->validate_enrolment_data($courseid, $this->enrolmentdata);

        if (!empty($errors)) {
            foreach ($errors as $key => $message) {
                $this->error($key, $message);
            }

            return false;
        }

        if (isset($this->rawdata['tags']) && strval($this->rawdata['tags']) !== '') {
            $this->data['tags'] = preg_split('/\s*,\s*/', trim($this->rawdata['tags']), -1, PREG_SPLIT_NO_EMPTY);
        }

        // Restore data.
        // TODO Speed up things by not really extracting the backup just yet, but checking that
        // the backup file or shortname passed are valid. Extraction should happen in proceed().
        $this->restoredata = $this->get_restore_content_dir();
        if ($this->restoredata === false) {
            return false;
        }

        if ($this->restoredata && ($error = permissions::check_permission_to_restore($this->do, $this->data))) {
            $this->error('courserestorepermission', $error);
            return false;
        }

        // We can only reset courses when allowed and we are updating the course.
        if ($this->importoptions['reset'] || $this->options['reset']) {
            if ($this->do !== self::DO_UPDATE) {
                $this->error('canonlyresetcourseinupdatemode',
                    new lang_string('canonlyresetcourseinupdatemode', 'tool_uploadcourse'));
                return false;
            } else if (!$this->can_reset()) {
                $this->error('courseresetnotallowed', new lang_string('courseresetnotallowed', 'tool_uploadcourse'));
                return false;
            }

            if ($error = permissions::check_permission_to_reset($this->data)) {
                $this->error('courseresetpermission', $error);
                return false;
            }
        }

        return true;
    }

    /**
     * Proceed with the import of the course.
     *
     * @return void
     */
    public function proceed() {
        global $CFG, $USER;

        if (!$this->prepared) {
            throw new coding_exception('The course has not been prepared.');
        } else if ($this->has_errors()) {
            throw new moodle_exception('Cannot proceed, errors were detected.');
        } else if ($this->processstarted) {
            throw new coding_exception('The process has already been started.');
        }
        $this->processstarted = true;

        if ($this->do === self::DO_DELETE) {
            if ($this->delete()) {
                $this->status('coursedeleted', new lang_string('coursedeleted', 'tool_uploadcourse'));
            } else {
                $this->error('errorwhiledeletingcourse', new lang_string('errorwhiledeletingcourse', 'tool_uploadcourse'));
            }
            return true;
        } else if ($this->do === self::DO_CREATE) {
            $course = create_course((object) $this->data);
            $this->id = $course->id;
            $this->status('coursecreated', new lang_string('coursecreated', 'tool_uploadcourse'));
        } else if ($this->do === self::DO_UPDATE) {
            $course = (object) $this->data;
            update_course($course);
            $this->id = $course->id;
            $this->status('courseupdated', new lang_string('courseupdated', 'tool_uploadcourse'));
        } else {
            // Strangely the outcome has not been defined, or is unknown!
            throw new coding_exception('Unknown outcome!');
        }

        // Restore a course.
        if (!empty($this->restoredata)) {
            $rc = new restore_controller($this->restoredata, $course->id, backup::INTERACTIVE_NO,
                backup::MODE_IMPORT, $USER->id, backup::TARGET_CURRENT_ADDING);

            // Check if the format conversion must happen first.
            if ($rc->get_status() == backup::STATUS_REQUIRE_CONV) {
                $rc->convert();
            }
            if ($rc->execute_precheck()) {
                $rc->execute_plan();
                $this->status('courserestored', new lang_string('courserestored', 'tool_uploadcourse'));
            } else {
                $this->error('errorwhilerestoringcourse', new lang_string('errorwhilerestoringcourse', 'tool_uploadcourse'));
            }
            $rc->destroy();
        }

        // Proceed with enrolment data.
        $this->process_enrolment_data($course);

        // Reset the course.
        if ($this->importoptions['reset'] || $this->options['reset']) {
            if ($this->do === self::DO_UPDATE && $this->can_reset()) {
                $this->reset($course);
                $this->status('coursereset', new lang_string('coursereset', 'tool_uploadcourse'));
            }
        }

        // Mark context as dirty.
        $context = context_course::instance($course->id);
        $context->mark_dirty();
    }

    /**
     * Validate passed enrolment data against an existing course
     *
     * @param int $courseid id of the course where enrolment methods are created/updated or 0 if it is a new course
     * @param array[] $enrolmentdata
     * @return lang_string[] Errors keyed on error code
     */
    protected function validate_enrolment_data(int $courseid, array $enrolmentdata): array {
        global $DB;

        // Nothing to validate.
        if (empty($enrolmentdata)) {
            return [];
        }

        $errors = [];

        $enrolmentplugins = tool_uploadcourse_helper::get_enrolment_plugins();
        $instances = enrol_get_instances($courseid, false);

        foreach ($enrolmentdata as $method => $options) {

            if (isset($options['role']) || isset($options['roleid'])) {
                if (isset($options['role'])) {
                    $role = $options['role'];
                    $roleid = $DB->get_field('role', 'id', ['shortname' => $role], MUST_EXIST);
                } else {
                    $roleid = $options['roleid'];
                    $role = $DB->get_field('role', 'shortname', ['id' => $roleid], MUST_EXIST);
                }
                if ($courseid) {
                    if (!$this->validate_role_context($courseid, $roleid)) {
                        $errors['contextrolenotallowed'] = new lang_string('contextrolenotallowed', 'core_role', $role);

                        break;
                    }
                } else {
                    // We can at least check that context level is correct while actual context not exist.
                    if (!$this->validate_role_context_level($roleid)) {
                        $errors['contextrolenotallowed'] = new lang_string('contextrolenotallowed', 'core_role', $role);

                        break;
                    }
                }
            }

            $plugin = $enrolmentplugins[$method];
            $errors += $plugin->validate_enrol_plugin_data($options, $courseid);
            if ($errors) {
                break;
            }

            if ($courseid) {
                // Find matching instances by enrolment method.
                $methodinstances = array_filter($instances, static function (stdClass $instance) use ($method) {
                    return (strcmp($instance->enrol, $method) == 0);
                });

                if (!empty($options['delete'])) {
                    // Ensure user is able to delete the instances.
                    foreach ($methodinstances as $methodinstance) {
                        if (!$plugin->can_delete_instance($methodinstance)) {
                            $errors['errorcannotdeleteenrolment'] = new lang_string('errorcannotdeleteenrolment',
                                'tool_uploadcourse', $plugin->get_instance_name($methodinstance));
                            break;
                        }
                    }
                } else if (!empty($options['disable'])) {
                    // Ensure user is able to toggle instance statuses.
                    foreach ($methodinstances as $methodinstance) {
                        if (!$plugin->can_hide_show_instance($methodinstance)) {
                            $errors['errorcannotdisableenrolment'] =
                                new lang_string('errorcannotdisableenrolment', 'tool_uploadcourse',
                                    $plugin->get_instance_name($methodinstance));

                            break;
                        }
                    }
                } else {
                    // Ensure user is able to create/update instance.
                    $methodinstance = empty($methodinstances) ? null : reset($methodinstances);
                    if ((empty($methodinstance) && !$plugin->can_add_instance($courseid)) ||
                        (!empty($methodinstance) && !$plugin->can_edit_instance($methodinstance))) {

                        $errors['errorcannotcreateorupdateenrolment'] =
                            new lang_string('errorcannotcreateorupdateenrolment', 'tool_uploadcourse',
                                $plugin->get_instance_name($methodinstance));

                        break;
                    }
                }
            }
        }

        return $errors;
    }

    /**
     * Add the enrolment data for the course.
     *
     * @param object $course course record.
     * @return void
     */
    protected function process_enrolment_data($course) {
        global $DB;

        $enrolmentdata = $this->enrolmentdata;
        if (empty($enrolmentdata)) {
            return;
        }

        $enrolmentplugins = tool_uploadcourse_helper::get_enrolment_plugins();
        foreach ($enrolmentdata as $enrolmethod => $method) {

            $plugin = $enrolmentplugins[$enrolmethod];
            $instance = $plugin->find_instance($method, $course->id);

            $todelete = isset($method['delete']) && $method['delete'];
            $todisable = isset($method['disable']) && $method['disable'];
            unset($method['delete']);
            unset($method['disable']);

            if ($todelete) {
                // Remove the enrolment method.
                if ($instance) {
                    $plugin = $enrolmentplugins[$instance->enrol];

                    // Ensure user is able to delete the instance.
                    if ($plugin->can_delete_instance($instance) && $plugin->is_csv_upload_supported()) {
                        $plugin->delete_instance($instance);
                    } else {
                        $this->error('errorcannotdeleteenrolment',
                            new lang_string('errorcannotdeleteenrolment', 'tool_uploadcourse',
                                $plugin->get_instance_name($instance)));
                    }
                }
            } else {
                // Create/update enrolment.
                $plugin = $enrolmentplugins[$enrolmethod];

                // In case we could not properly validate enrolment data before the course existed
                // let's repeat it again here.
                $errors = $plugin->validate_enrol_plugin_data($method, $course->id);

                if (!$errors) {
                    $status = ($todisable) ? ENROL_INSTANCE_DISABLED : ENROL_INSTANCE_ENABLED;
                    $method += ['status' => $status, 'courseid' => $course->id, 'id' => $instance->id ?? null];
                    $method = $plugin->fill_enrol_custom_fields($method, $course->id);

                    // Create a new instance if necessary.
                    if (empty($instance) && $plugin->can_add_instance($course->id)) {
                        $error = $plugin->validate_plugin_data_context($method, $course->id);
                        if ($error) {
                            $this->error('contextnotallowed', $error);
                            break;
                        }
                        $instanceid = $plugin->add_default_instance($course);
                        if (!$instanceid) {
                            // Add instance with provided fields if plugin supports it.
                            $instanceid = $plugin->add_custom_instance($course, $method);
                        }

                        $instance = $DB->get_record('enrol', ['id' => $instanceid]);
                        if ($instance) {
                            $instance->roleid = $plugin->get_config('roleid');
                            // On creation the user can decide the status.
                            $plugin->update_status($instance, $status);
                        }
                    }

                    // Check if the we need to update the instance status.
                    if ($instance && $status != $instance->status) {
                        if ($plugin->can_hide_show_instance($instance)) {
                            $plugin->update_status($instance, $status);
                        } else {
                            $this->error('errorcannotdisableenrolment',
                                new lang_string('errorcannotdisableenrolment', 'tool_uploadcourse',
                                    $plugin->get_instance_name($instance)));
                            break;
                        }
                    }

                    if (empty($instance) || !$plugin->can_edit_instance($instance)) {
                        $this->error('errorcannotcreateorupdateenrolment',
                            new lang_string('errorcannotcreateorupdateenrolment', 'tool_uploadcourse',
                                $plugin->get_instance_name($instance)));

                        break;
                    }

                    // Validate role context again since course is created.
                    if (isset($method['role']) || isset($method['roleid'])) {
                        if (isset($method['role'])) {
                            $role = $method['role'];
                            $roleid = $DB->get_field('role', 'id', ['shortname' => $role], MUST_EXIST);
                        } else {
                            $roleid = $method['roleid'];
                            $role = $DB->get_field('role', 'shortname', ['id' => $roleid], MUST_EXIST);
                        }
                        if (!$this->validate_role_context($course->id, $roleid)) {
                            $this->error('contextrolenotallowed', new lang_string('contextrolenotallowed', 'core_role', $role));
                            break;
                        }
                    }

                    // Now update values.
                    // Sort out plugin specific fields.
                    $modifiedinstance = $plugin->update_enrol_plugin_data($course->id, $method, clone $instance);
                    $plugin->update_instance($instance, $modifiedinstance);
                } else {
                    foreach ($errors as $key => $message) {
                        $this->error($key, $message);
                    }
                }
            }
        }
    }

    /**
     * Check if role is allowed in course context
     *
     * @param int $courseid course context.
     * @param int $roleid Role ID.
     * @return bool
     */
    protected function validate_role_context(int $courseid, int $roleid): bool {
        if (empty($this->assignableroles[$courseid])) {
            $coursecontext = \context_course::instance($courseid);
            $this->assignableroles[$courseid] = get_assignable_roles($coursecontext, ROLENAME_SHORT);
        }
        if (!array_key_exists($roleid, $this->assignableroles[$courseid])) {
            return false;
        }
        return true;
    }

    /**
     * Check if role is allowed at this context level.
     *
     * @param int $roleid Role ID.
     * @return bool
     */
    protected function validate_role_context_level(int $roleid): bool {
        if (empty($this->contextlevels[$roleid])) {
            $this->contextlevels[$roleid] = get_role_contextlevels($roleid);
        }

        if (!in_array(CONTEXT_COURSE, $this->contextlevels[$roleid])) {
            return false;
        }
        return true;
    }

    /**
     * Reset the current course.
     *
     * This does not reset any of the content of the activities.
     *
     * @param stdClass $course the course object of the course to reset.
     * @return array status array of array component, item, error.
     */
    protected function reset($course) {
        global $DB;

        $resetdata = new stdClass();
        $resetdata->id = $course->id;
        $resetdata->reset_start_date = time();
        $resetdata->reset_events = true;
        $resetdata->reset_notes = true;
        $resetdata->delete_blog_associations = true;
        $resetdata->reset_completion = true;
        $resetdata->reset_roles_overrides = true;
        $resetdata->reset_roles_local = true;
        $resetdata->reset_groups_members = true;
        $resetdata->reset_groups_remove = true;
        $resetdata->reset_groupings_members = true;
        $resetdata->reset_groupings_remove = true;
        $resetdata->reset_gradebook_items = true;
        $resetdata->reset_gradebook_grades = true;
        $resetdata->reset_comments = true;

        if (empty($course->startdate)) {
            $course->startdate = $DB->get_field_select('course', 'startdate', 'id = :id', array('id' => $course->id));
        }
        $resetdata->reset_start_date_old = $course->startdate;

        if (empty($course->enddate)) {
            $course->enddate = $DB->get_field_select('course', 'enddate', 'id = :id', array('id' => $course->id));
        }
        $resetdata->reset_end_date_old = $course->enddate;

        // Add roles.
        $roles = tool_uploadcourse_helper::get_role_ids();
        $resetdata->unenrol_users = array_values($roles);
        $resetdata->unenrol_users[] = 0;    // Enrolled without role.

        return reset_course_userdata($resetdata);
    }

    /**
     * Log a status
     *
     * @param string $code status code.
     * @param lang_string $message status message.
     * @return void
     */
    protected function status($code, lang_string $message) {
        if (array_key_exists($code, $this->statuses)) {
            throw new coding_exception('Status code already defined');
        }
        $this->statuses[$code] = $message;
    }

}
