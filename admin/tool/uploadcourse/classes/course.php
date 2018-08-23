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
        'groupmode', 'groupmodeforce', 'enablecompletion');

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
     * @param lang_string $message error message.
     * @return void
     */
    protected function error($code, lang_string $message) {
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
     * Assemble the course data based on defaults.
     *
     * This returns the final data to be passed to create_course().
     *
     * @param array $data current data.
     * @return array
     */
    protected function get_final_create_data($data) {
        foreach (self::$validfields as $field) {
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
        foreach (self::$validfields as $field) {
            if ($missingonly) {
                if (!is_null($existingdata->$field) and $existingdata->$field !== '') {
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
        global $DB, $SITE;
        $this->prepared = true;

        // Validate the shortname.
        if (!empty($this->shortname) || is_numeric($this->shortname)) {
            if ($this->shortname !== clean_param($this->shortname, PARAM_TEXT)) {
                $this->error('invalidshortname', new lang_string('invalidshortname', 'tool_uploadcourse'));
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

            $this->do = self::DO_UPDATE;
        } else {
            $coursedata = $this->get_final_create_data($coursedata);
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

        // Special case, 'numsections' is not a course format option any more but still should apply from defaults.
        if (!$exists || !array_key_exists('numsections', $coursedata)) {
            if (isset($this->rawdata['numsections']) && is_numeric($this->rawdata['numsections'])) {
                $coursedata['numsections'] = (int)$this->rawdata['numsections'];
            } else {
                $coursedata['numsections'] = get_config('moodlecourse', 'numsections');
            }
        }

        // Saving data.
        $this->data = $coursedata;
        $this->enrolmentdata = tool_uploadcourse_helper::get_enrolment_data($this->rawdata);

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
                $this->error('errorwhilerestoringcourse', new lang_string('errorwhilerestoringthecourse', 'tool_uploadcourse'));
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
        $instances = enrol_get_instances($course->id, false);
        foreach ($enrolmentdata as $enrolmethod => $method) {

            $instance = null;
            foreach ($instances as $i) {
                if ($i->enrol == $enrolmethod) {
                    $instance = $i;
                    break;
                }
            }

            $todelete = isset($method['delete']) && $method['delete'];
            $todisable = isset($method['disable']) && $method['disable'];
            unset($method['delete']);
            unset($method['disable']);

            if (!empty($instance) && $todelete) {
                // Remove the enrolment method.
                foreach ($instances as $instance) {
                    if ($instance->enrol == $enrolmethod) {
                        $plugin = $enrolmentplugins[$instance->enrol];
                        $plugin->delete_instance($instance);
                        break;
                    }
                }
            } else if (!empty($instance) && $todisable) {
                // Disable the enrolment.
                foreach ($instances as $instance) {
                    if ($instance->enrol == $enrolmethod) {
                        $plugin = $enrolmentplugins[$instance->enrol];
                        $plugin->update_status($instance, ENROL_INSTANCE_DISABLED);
                        $enrol_updated = true;
                        break;
                    }
                }
            } else {
                $plugin = null;
                if (empty($instance)) {
                    $plugin = $enrolmentplugins[$enrolmethod];
                    $instance = new stdClass();
                    $instance->id = $plugin->add_default_instance($course);
                    $instance->roleid = $plugin->get_config('roleid');
                    $instance->status = ENROL_INSTANCE_ENABLED;
                } else {
                    $plugin = $enrolmentplugins[$instance->enrol];
                    $plugin->update_status($instance, ENROL_INSTANCE_ENABLED);
                }

                // Now update values.
                foreach ($method as $k => $v) {
                    $instance->{$k} = $v;
                }

                // Sort out the start, end and date.
                $instance->enrolstartdate = (isset($method['startdate']) ? strtotime($method['startdate']) : 0);
                $instance->enrolenddate = (isset($method['enddate']) ? strtotime($method['enddate']) : 0);

                // Is the enrolment period set?
                if (isset($method['enrolperiod']) && ! empty($method['enrolperiod'])) {
                    if (preg_match('/^\d+$/', $method['enrolperiod'])) {
                        $method['enrolperiod'] = (int) $method['enrolperiod'];
                    } else {
                        // Try and convert period to seconds.
                        $method['enrolperiod'] = strtotime('1970-01-01 GMT + ' . $method['enrolperiod']);
                    }
                    $instance->enrolperiod = $method['enrolperiod'];
                }
                if ($instance->enrolstartdate > 0 && isset($method['enrolperiod'])) {
                    $instance->enrolenddate = $instance->enrolstartdate + $method['enrolperiod'];
                }
                if ($instance->enrolenddate > 0) {
                    $instance->enrolperiod = $instance->enrolenddate - $instance->enrolstartdate;
                }
                if ($instance->enrolenddate < $instance->enrolstartdate) {
                    $instance->enrolenddate = $instance->enrolstartdate;
                }

                // Sort out the given role. This does not filter the roles allowed in the course.
                if (isset($method['role'])) {
                    $roleids = tool_uploadcourse_helper::get_role_ids();
                    if (isset($roleids[$method['role']])) {
                        $instance->roleid = $roleids[$method['role']];
                    }
                }

                $instance->timemodified = time();
                $DB->update_record('enrol', $instance);
            }
        }
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
