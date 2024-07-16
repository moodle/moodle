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
 * Profile field API library file.
 *
 * @package core_user
 * @copyright  2007 onwards Shane Elliot {@link http://pukunui.com}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Visible to anyone who has the moodle/site:viewuseridentity permission.
 * Editable by the profile owner if they have the moodle/user:editownprofile capability
 * or any user with the moodle/user:update capability.
 */
define('PROFILE_VISIBLE_TEACHERS', '3');

/**
 * Visible to anyone who can view the user.
 * Editable by the profile owner if they have the moodle/user:editownprofile capability
 * or any user with the moodle/user:update capability.
 */
define('PROFILE_VISIBLE_ALL', '2');
/**
 * Visible to the profile owner or anyone with the moodle/user:viewalldetails capability.
 * Editable by the profile owner if they have the moodle/user:editownprofile capability
 * or any user with moodle/user:viewalldetails and moodle/user:update capabilities.
 */
define('PROFILE_VISIBLE_PRIVATE', '1');
/**
 * Only visible to users with the moodle/user:viewalldetails capability.
 * Only editable by users with the moodle/user:viewalldetails and moodle/user:update capabilities.
 */
define('PROFILE_VISIBLE_NONE', '0');

/**
 * Base class for the customisable profile fields.
 *
 * @package core_user
 * @copyright  2007 onwards Shane Elliot {@link http://pukunui.com}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class profile_field_base {

    // These 2 variables are really what we're interested in.
    // Everything else can be extracted from them.

    /** @var int */
    public $fieldid;

    /** @var int */
    public $userid;

    /** @var stdClass */
    public $field;

    /** @var string */
    public $inputname;

    /** @var mixed */
    public $data;

    /** @var string */
    public $dataformat;

    /** @var string name of the user profile category */
    protected $categoryname;

    /**
     * Constructor method.
     * @param int $fieldid id of the profile from the user_info_field table
     * @param int $userid id of the user for whom we are displaying data
     * @param stdClass $fielddata optional data for the field object plus additional fields 'hasuserdata', 'data' and 'dataformat'
     *    with user data. (If $fielddata->hasuserdata is empty, user data is not available and we should use default data).
     *    If this parameter is passed, constructor will not call load_data() at all.
     */
    public function __construct($fieldid=0, $userid=0, $fielddata=null) {
        global $CFG;

        if ($CFG->debugdeveloper) {
            // In Moodle 3.4 the new argument $fielddata was added to the constructor. Make sure that
            // plugin constructor properly passes this argument.
            $backtrace = debug_backtrace();
            if (isset($backtrace[1]['class']) && $backtrace[1]['function'] === '__construct' &&
                    in_array(self::class, class_parents($backtrace[1]['class']))) {
                // If this constructor is called from the constructor of the plugin make sure that the third argument was passed through.
                if (count($backtrace[1]['args']) >= 3 && count($backtrace[0]['args']) < 3) {
                    debugging($backtrace[1]['class'].'::__construct() must support $fielddata as the third argument ' .
                        'and pass it to the parent constructor', DEBUG_DEVELOPER);
                }
            }
        }

        $this->set_fieldid($fieldid);
        $this->set_userid($userid);
        if ($fielddata) {
            $this->set_field($fielddata);
            if ($userid > 0 && !empty($fielddata->hasuserdata)) {
                $this->set_user_data($fielddata->data, $fielddata->dataformat);
            }
        } else {
            $this->load_data();
        }
    }

    /**
     * Old syntax of class constructor. Deprecated in PHP7.
     *
     * @deprecated since Moodle 3.1
     */
    public function profile_field_base($fieldid=0, $userid=0) {
        debugging('Use of class name as constructor is deprecated', DEBUG_DEVELOPER);
        self::__construct($fieldid, $userid);
    }

    /**
     * Abstract method: Adds the profile field to the moodle form class
     * @abstract The following methods must be overwritten by child classes
     * @param MoodleQuickForm $mform instance of the moodleform class
     */
    public function edit_field_add($mform) {
        throw new \moodle_exception('mustbeoveride', 'debug', '', 'edit_field_add');
    }

    /**
     * Display the data for this field
     * @return string
     */
    public function display_data() {
        $options = new stdClass();
        $options->para = false;
        return format_text($this->data, FORMAT_MOODLE, $options);
    }

    /**
     * Display the name of the profile field.
     *
     * @param bool $escape
     * @return string
     */
    public function display_name(bool $escape = true): string {
        return format_string($this->field->name, true, [
            'context' => context_system::instance(),
            'escape' => $escape,
        ]);
    }

    /**
     * Print out the form field in the edit profile page
     * @param MoodleQuickForm $mform instance of the moodleform class
     * @return bool
     */
    public function edit_field($mform) {
        if (!$this->is_editable()) {
            return false;
        }

        $this->edit_field_add($mform);
        $this->edit_field_set_default($mform);
        $this->edit_field_set_required($mform);
        return true;
    }

    /**
     * Tweaks the edit form
     * @param MoodleQuickForm $mform instance of the moodleform class
     * @return bool
     */
    public function edit_after_data($mform) {
        if (!$this->is_editable()) {
            return false;
        }

        $this->edit_field_set_locked($mform);
        return true;
    }

    /**
     * Saves the data coming from form
     * @param stdClass $usernew data coming from the form
     */
    public function edit_save_data($usernew) {
        global $DB;

        if (!isset($usernew->{$this->inputname})) {
            // Field not present in form, probably locked and invisible - skip it.
            return;
        }

        $data = new stdClass();

        $usernew->{$this->inputname} = $this->edit_save_data_preprocess($usernew->{$this->inputname}, $data);
        if (!isset($usernew->{$this->inputname})) {
            // Field cannot be set to null, set the default value.
            $usernew->{$this->inputname} = $this->field->defaultdata;
        }

        $data->userid  = $usernew->id;
        $data->fieldid = $this->field->id;
        $data->data    = $usernew->{$this->inputname};

        if ($dataid = $DB->get_field('user_info_data', 'id', array('userid' => $data->userid, 'fieldid' => $data->fieldid))) {
            $data->id = $dataid;
            $DB->update_record('user_info_data', $data);
        } else {
            $DB->insert_record('user_info_data', $data);
        }
    }

    /**
     * Validate the form field from profile page
     *
     * @param stdClass $usernew
     * @return  array  error messages for the form validation
     */
    public function edit_validate_field($usernew) {
        global $DB;

        $errors = array();
        // Get input value.
        if (isset($usernew->{$this->inputname})) {
            if (is_array($usernew->{$this->inputname}) && isset($usernew->{$this->inputname}['text'])) {
                $value = $usernew->{$this->inputname}['text'];
            } else {
                $value = $usernew->{$this->inputname};
            }
        } else {
            $value = '';
        }

        // Check for uniqueness of data if required.
        if ($this->is_unique() && (($value !== '') || $this->is_required())) {
            $data = $DB->get_records_sql('
                    SELECT id, userid
                      FROM {user_info_data}
                     WHERE fieldid = ?
                       AND ' . $DB->sql_compare_text('data', 255) . ' = ' . $DB->sql_compare_text('?', 255),
                    array($this->field->id, $value));
            if ($data) {
                $existing = false;
                foreach ($data as $v) {
                    if ($v->userid == $usernew->id) {
                        $existing = true;
                        break;
                    }
                }
                if (!$existing) {
                    $errors[$this->inputname] = get_string('valuealreadyused');
                }
            }
        }
        return $errors;
    }

    /**
     * Sets the default data for the field in the form object
     * @param MoodleQuickForm $mform instance of the moodleform class
     */
    public function edit_field_set_default($mform) {
        if (isset($this->field->defaultdata)) {
            $mform->setDefault($this->inputname, $this->field->defaultdata);
        }
    }

    /**
     * Sets the required flag for the field in the form object
     *
     * @param MoodleQuickForm $mform instance of the moodleform class
     */
    public function edit_field_set_required($mform) {
        global $USER;
        if ($this->is_required() && ($this->userid == $USER->id || isguestuser())) {
            $mform->addRule($this->inputname, get_string('required'), 'required', null, 'client');
        }
    }

    /**
     * HardFreeze the field if locked.
     * @param MoodleQuickForm $mform instance of the moodleform class
     */
    public function edit_field_set_locked($mform) {
        if (!$mform->elementExists($this->inputname)) {
            return;
        }
        if ($this->is_locked() and !has_capability('moodle/user:update', context_system::instance())) {
            $mform->hardFreeze($this->inputname);
            $mform->setConstant($this->inputname, $this->data);
        }
    }

    /**
     * Hook for child classess to process the data before it gets saved in database
     * @param stdClass $data
     * @param stdClass $datarecord The object that will be used to save the record
     * @return  mixed
     */
    public function edit_save_data_preprocess($data, $datarecord) {
        return $data;
    }

    /**
     * Loads a user object with data for this field ready for the edit profile
     * form
     * @param stdClass $user a user object
     */
    public function edit_load_user_data($user) {
        if ($this->data !== null) {
            $user->{$this->inputname} = $this->data;
        }
    }

    /**
     * Check if the field data should be loaded into the user object
     * By default it is, but for field types where the data may be potentially
     * large, the child class should override this and return false
     * @return bool
     */
    public function is_user_object_data() {
        return true;
    }

    /**
     * Accessor method: set the userid for this instance
     * @internal This method should not generally be overwritten by child classes.
     * @param integer $userid id from the user table
     */
    public function set_userid($userid) {
        $this->userid = $userid;
    }

    /**
     * Accessor method: set the fieldid for this instance
     * @internal This method should not generally be overwritten by child classes.
     * @param integer $fieldid id from the user_info_field table
     */
    public function set_fieldid($fieldid) {
        $this->fieldid = $fieldid;
    }

    /**
     * Sets the field object and default data and format into $this->data and $this->dataformat
     *
     * This method should be called before {@link self::set_user_data}
     *
     * @param stdClass $field
     * @throws coding_exception
     */
    public function set_field($field) {
        global $CFG;
        if ($CFG->debugdeveloper) {
            $properties = ['id', 'shortname', 'name', 'datatype', 'description', 'descriptionformat', 'categoryid', 'sortorder',
                'required', 'locked', 'visible', 'forceunique', 'signup', 'defaultdata', 'defaultdataformat', 'param1', 'param2',
                'param3', 'param4', 'param5'];
            foreach ($properties as $property) {
                if (!property_exists($field, $property)) {
                    debugging('The \'' . $property . '\' property must be set.', DEBUG_DEVELOPER);
                }
            }
        }
        if ($this->fieldid && $this->fieldid != $field->id) {
            throw new coding_exception('Can not set field object after a different field id was set');
        }
        $this->fieldid = $field->id;
        $this->field = $field;
        $this->inputname = 'profile_field_' . $this->field->shortname;
        $this->data = $this->field->defaultdata;
        $this->dataformat = FORMAT_HTML;
    }

    /**
     * Sets user id and user data for the field
     *
     * @param mixed $data
     * @param int $dataformat
     */
    public function set_user_data($data, $dataformat) {
        $this->data = $data;
        $this->dataformat = $dataformat;
    }

    /**
     * Set the name for the profile category where this field is
     *
     * @param string $categoryname
     */
    public function set_category_name($categoryname) {
        $this->categoryname = $categoryname;
    }

    /**
     * Return field short name
     *
     * @return string
     */
    public function get_shortname(): string {
        return $this->field->shortname;
    }

    /**
     * Returns the name of the profile category where this field is
     *
     * @return string
     */
    public function get_category_name() {
        global $DB;
        if ($this->categoryname === null) {
            $this->categoryname = $DB->get_field('user_info_category', 'name', ['id' => $this->field->categoryid]);
        }
        return $this->categoryname;
    }

    /**
     * Accessor method: Load the field record and user data associated with the
     * object's fieldid and userid
     *
     * @internal This method should not generally be overwritten by child classes.
     */
    public function load_data() {
        global $DB;

        // Load the field object.
        if (($this->fieldid == 0) or (!($field = $DB->get_record('user_info_field', array('id' => $this->fieldid))))) {
            $this->field = null;
            $this->inputname = '';
        } else {
            $this->set_field($field);
        }

        if (!empty($this->field) && $this->userid > 0) {
            $params = array('userid' => $this->userid, 'fieldid' => $this->fieldid);
            if ($data = $DB->get_record('user_info_data', $params, 'data, dataformat')) {
                $this->set_user_data($data->data, $data->dataformat);
            }
        } else {
            $this->data = null;
        }
    }

    /**
     * Check if the field data is visible to the current user
     * @internal This method should not generally be overwritten by child classes.
     *
     * @param context|null $context
     * @return bool
     */
    public function is_visible(?context $context = null): bool {
        global $USER, $COURSE;

        if ($context === null) {
            $context = ($this->userid > 0) ? context_user::instance($this->userid) : context_system::instance();
        }

        switch ($this->field->visible) {
            case PROFILE_VISIBLE_TEACHERS:
                if ($this->is_signup_field() && (empty($this->userid) || isguestuser($this->userid))) {
                    return true;
                } else if ($this->userid == $USER->id) {
                    return true;
                } else if ($this->userid > 0) {
                    return has_capability('moodle/user:viewalldetails', $context);
                } else {
                    $coursecontext = context_course::instance($COURSE->id);
                    return has_capability('moodle/site:viewuseridentity', $coursecontext);
                }
            case PROFILE_VISIBLE_ALL:
                return true;
            case PROFILE_VISIBLE_PRIVATE:
                if ($this->is_signup_field() && (empty($this->userid) || isguestuser($this->userid))) {
                    return true;
                } else if ($this->userid == $USER->id) {
                    return true;
                } else {
                    return has_capability('moodle/user:viewalldetails', $context);
                }
            default:
                // PROFILE_VISIBLE_NONE, so let's check capabilities at system level.
                if ($this->userid > 0) {
                    $context = context_system::instance();
                }
                return has_capability('moodle/user:viewalldetails', $context);
        }
    }

    /**
     * Check if the field data is editable for the current user
     * This method should not generally be overwritten by child classes.
     * @return bool
     */
    public function is_editable() {
        global $USER;

        if (!$this->is_visible()) {
            return false;
        }

        if ($this->is_signup_field() && (empty($this->userid) || isguestuser($this->userid))) {
            // Allow editing the field on the signup page.
            return true;
        }

        $systemcontext = context_system::instance();

        if ($this->userid == $USER->id && has_capability('moodle/user:editownprofile', $systemcontext)) {
            return true;
        }

        if (has_capability('moodle/user:update', $systemcontext)) {
            return true;
        }

        // Checking for mentors have capability to edit user's profile.
        if ($this->userid > 0) {
            $usercontext = context_user::instance($this->userid);
            if ($this->userid != $USER->id && has_capability('moodle/user:editprofile', $usercontext, $USER->id)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the field data is considered empty
     * @internal This method should not generally be overwritten by child classes.
     * @return boolean
     */
    public function is_empty() {
        return ( ($this->data != '0') and empty($this->data));
    }

    /**
     * Check if the field is required on the edit profile page
     * @internal This method should not generally be overwritten by child classes.
     * @return bool
     */
    public function is_required() {
        return (boolean)$this->field->required;
    }

    /**
     * Check if the field is locked on the edit profile page
     * @internal This method should not generally be overwritten by child classes.
     * @return bool
     */
    public function is_locked() {
        return (boolean)$this->field->locked;
    }

    /**
     * Check if the field data should be unique
     * @internal This method should not generally be overwritten by child classes.
     * @return bool
     */
    public function is_unique() {
        return (boolean)$this->field->forceunique;
    }

    /**
     * Check if the field should appear on the signup page
     * @internal This method should not generally be overwritten by child classes.
     * @return bool
     */
    public function is_signup_field() {
        return (boolean)$this->field->signup;
    }

    /**
     * Return the field settings suitable to be exported via an external function.
     * By default it return all the field settings.
     *
     * @return array all the settings
     * @since Moodle 3.2
     */
    public function get_field_config_for_external() {
        return (array) $this->field;
    }

    /**
     * Return the field type and null properties.
     * This will be used for validating the data submitted by a user.
     *
     * @return array the param type and null property
     * @since Moodle 3.2
     */
    public function get_field_properties() {
        return array(PARAM_RAW, NULL_NOT_ALLOWED);
    }

    /**
     * Whether to display the field and content to the user
     *
     * @param context|null $context
     * @return bool
     */
    public function show_field_content(?context $context = null): bool {
        return $this->is_visible($context) && !$this->is_empty();
    }

    /**
     * Check if the field should convert the raw data into user-friendly data when exporting
     *
     * @return bool
     */
    public function is_transform_supported(): bool {
        return false;
    }
}

/**
 * Return profile field instance for given type
 *
 * @param string $type
 * @param int $fieldid
 * @param int $userid
 * @param stdClass|null $fielddata
 * @return profile_field_base
 */
function profile_get_user_field(string $type, int $fieldid = 0, int $userid = 0, ?stdClass $fielddata = null): profile_field_base {
    global $CFG;

    require_once("{$CFG->dirroot}/user/profile/field/{$type}/field.class.php");

    // Return instance of profile field type.
    $profilefieldtype = "profile_field_{$type}";
    return new $profilefieldtype($fieldid, $userid, $fielddata);
}

/**
 * Returns an array of all custom field records with any defined data (or empty data), for the specified user id.
 * @param int $userid
 * @return profile_field_base[]
 */
function profile_get_user_fields_with_data(int $userid): array {
    global $DB;

    // Join any user info data present with each user info field for the user object.
    $sql = 'SELECT uif.*, uic.name AS categoryname ';
    if ($userid > 0) {
        $sql .= ', uind.id AS hasuserdata, uind.data, uind.dataformat ';
    }
    $sql .= 'FROM {user_info_field} uif ';
    $sql .= 'LEFT JOIN {user_info_category} uic ON uif.categoryid = uic.id ';
    if ($userid > 0) {
        $sql .= 'LEFT JOIN {user_info_data} uind ON uif.id = uind.fieldid AND uind.userid = :userid ';
    }
    $sql .= 'ORDER BY uic.sortorder ASC, uif.sortorder ASC ';
    $fields = $DB->get_records_sql($sql, ['userid' => $userid]);
    $data = [];
    foreach ($fields as $field) {
        $field->hasuserdata = !empty($field->hasuserdata);
        $fieldobject = profile_get_user_field($field->datatype, $field->id, $userid, $field);
        $fieldobject->set_category_name($field->categoryname);
        unset($field->categoryname);
        $data[] = $fieldobject;
    }
    return $data;
}

/**
 * Returns an array of all custom field records with any defined data (or empty data), for the specified user id, by category.
 * @param int $userid
 * @return profile_field_base[][]
 */
function profile_get_user_fields_with_data_by_category(int $userid): array {
    $fields = profile_get_user_fields_with_data($userid);
    $data = [];
    foreach ($fields as $field) {
        $data[$field->field->categoryid][] = $field;
    }
    return $data;
}

/**
 * Loads user profile field data into the user object.
 * @param stdClass $user
 */
function profile_load_data(stdClass $user): void {
    $fields = profile_get_user_fields_with_data($user->id);
    foreach ($fields as $formfield) {
        $formfield->edit_load_user_data($user);
    }
}

/**
 * Print out the customisable categories and fields for a users profile
 *
 * @param MoodleQuickForm $mform instance of the moodleform class
 * @param int $userid id of user whose profile is being edited or 0 for the new user
 */
function profile_definition(MoodleQuickForm $mform, int $userid = 0): void {
    $categories = profile_get_user_fields_with_data_by_category($userid);
    foreach ($categories as $categoryid => $fields) {
        // Check first if *any* fields will be displayed.
        $fieldstodisplay = [];

        foreach ($fields as $formfield) {
            if ($formfield->is_editable()) {
                $fieldstodisplay[] = $formfield;
            }
        }

        if (empty($fieldstodisplay)) {
            continue;
        }

        // Display the header and the fields.
        $mform->addElement('header', 'category_'.$categoryid, format_string($fields[0]->get_category_name()));
        foreach ($fieldstodisplay as $formfield) {
            $formfield->edit_field($mform);
        }
    }
}

/**
 * Adds profile fields to user edit forms.
 * @param MoodleQuickForm $mform
 * @param int $userid
 */
function profile_definition_after_data(MoodleQuickForm $mform, int $userid): void {
    $userid = ($userid < 0) ? 0 : (int)$userid;

    $fields = profile_get_user_fields_with_data($userid);
    foreach ($fields as $formfield) {
        $formfield->edit_after_data($mform);
    }
}

/**
 * Validates profile data.
 * @param stdClass $usernew
 * @param array $files
 * @return array array of errors, same as in {@see moodleform::validation()}
 */
function profile_validation(stdClass $usernew, array $files): array {
    $err = array();
    $fields = profile_get_user_fields_with_data($usernew->id);
    foreach ($fields as $formfield) {
        $err += $formfield->edit_validate_field($usernew, $files);
    }
    return $err;
}

/**
 * Saves profile data for a user.
 * @param stdClass $usernew
 */
function profile_save_data(stdClass $usernew): void {
    global $CFG;

    $fields = profile_get_user_fields_with_data($usernew->id);
    foreach ($fields as $formfield) {
        $formfield->edit_save_data($usernew);
    }
}

/**
 * Retrieves a list of profile fields that must be displayed in the sign-up form.
 *
 * @return array list of profile fields info
 * @since Moodle 3.2
 */
function profile_get_signup_fields(): array {
    $profilefields = array();
    $fieldobjects = profile_get_user_fields_with_data(0);
    foreach ($fieldobjects as $fieldobject) {
        $field = (object)$fieldobject->get_field_config_for_external();
        if ($fieldobject->get_category_name() !== null && $fieldobject->is_signup_field() && $field->visible <> 0) {
            $profilefields[] = (object) array(
                'categoryid' => $field->categoryid,
                'categoryname' => $fieldobject->get_category_name(),
                'fieldid' => $field->id,
                'datatype' => $field->datatype,
                'object' => $fieldobject
            );
        }
    }
    return $profilefields;
}

/**
 * Adds code snippet to a moodle form object for custom profile fields that
 * should appear on the signup page
 * @param MoodleQuickForm $mform moodle form object
 */
function profile_signup_fields(MoodleQuickForm $mform): void {

    if ($fields = profile_get_signup_fields()) {
        foreach ($fields as $field) {
            // Check if we change the categories.
            if (!isset($currentcat) || $currentcat != $field->categoryid) {
                 $currentcat = $field->categoryid;
                 $mform->addElement('header', 'category_'.$field->categoryid, format_string($field->categoryname));
            };
            $field->object->edit_field($mform);
        }
    }
}

/**
 * Returns an object with the custom profile fields set for the given user
 * @param int $userid
 * @param bool $onlyinuserobject True if you only want the ones in $USER.
 * @return stdClass object where properties names are shortnames of custom profile fields
 */
function profile_user_record(int $userid, bool $onlyinuserobject = true): stdClass {
    $usercustomfields = new stdClass();

    $fields = profile_get_user_fields_with_data($userid);
    foreach ($fields as $formfield) {
        if (!$onlyinuserobject || $formfield->is_user_object_data()) {
            $usercustomfields->{$formfield->field->shortname} = $formfield->data;
        }
    }

    return $usercustomfields;
}

/**
 * Obtains a list of all available custom profile fields, indexed by id.
 *
 * Some profile fields are not included in the user object data (see
 * profile_user_record function above). Optionally, you can obtain only those
 * fields that are included in the user object.
 *
 * To be clear, this function returns the available fields, and does not
 * return the field values for a particular user.
 *
 * @param bool $onlyinuserobject True if you only want the ones in $USER
 * @return array Array of field objects from database (indexed by id)
 * @since Moodle 2.7.1
 */
function profile_get_custom_fields(bool $onlyinuserobject = false): array {
    $fieldobjects = profile_get_user_fields_with_data(0);
    $fields = [];
    foreach ($fieldobjects as $fieldobject) {
        if (!$onlyinuserobject || $fieldobject->is_user_object_data()) {
            $fields[$fieldobject->fieldid] = (object)$fieldobject->get_field_config_for_external();
        }
    }
    ksort($fields);
    return $fields;
}

/**
 * Load custom profile fields into user object
 *
 * @param stdClass $user user object
 */
function profile_load_custom_fields($user) {
    $user->profile = (array)profile_user_record($user->id);
}

/**
 * Save custom profile fields for a user.
 *
 * @param int $userid The user id
 * @param array $profilefields The fields to save
 */
function profile_save_custom_fields($userid, $profilefields) {
    global $DB;

    $fields = profile_get_user_fields_with_data(0);
    if ($fields) {
        foreach ($fields as $fieldobject) {
            $field = (object)$fieldobject->get_field_config_for_external();
            if (isset($profilefields[$field->shortname])) {
                $conditions = array('fieldid' => $field->id, 'userid' => $userid);
                $id = $DB->get_field('user_info_data', 'id', $conditions);
                $data = $profilefields[$field->shortname];
                if ($id) {
                    $DB->set_field('user_info_data', 'data', $data, array('id' => $id));
                } else {
                    $record = array('fieldid' => $field->id, 'userid' => $userid, 'data' => $data);
                    $DB->insert_record('user_info_data', $record);
                }
            }
        }
    }
}

/**
 * Gets basic data about custom profile fields. This is minimal data that is cached within the
 * current request for all fields so that it can be used quickly.
 *
 * @param string $shortname Shortname of custom profile field
 * @param bool $casesensitive Whether to perform case-sensitive matching of shortname. Note current limitations of custom profile
 *  fields allow the same shortname to exist differing only by it's case
 * @return stdClass|null Object with properties id, shortname, name, visible, datatype, categoryid, etc
 */
function profile_get_custom_field_data_by_shortname(string $shortname, bool $casesensitive = true): ?stdClass {
    $cache = \cache::make_from_params(cache_store::MODE_REQUEST, 'core_profile', 'customfields',
            [], ['simplekeys' => true, 'simpledata' => true]);
    $data = $cache->get($shortname);
    if ($data === false) {
        // If we don't have data, we get and cache it for all fields to avoid multiple DB requests.
        $fields = profile_get_custom_fields();
        $data = null;
        foreach ($fields as $field) {
            $cache->set($field->shortname, $field);

            // Perform comparison according to case sensitivity parameter.
            $shortnamematch = $casesensitive
                ? strcmp($field->shortname, $shortname) === 0
                : strcasecmp($field->shortname, $shortname) === 0;

            if ($shortnamematch) {
                $data = $field;
            }
        }
    }

    return $data;
}

/**
 * Trigger a user profile viewed event.
 *
 * @param stdClass  $user user  object
 * @param stdClass  $context  context object (course or user)
 * @param stdClass  $course course  object
 * @since Moodle 2.9
 */
function profile_view($user, $context, $course = null) {

    $eventdata = array(
        'objectid' => $user->id,
        'relateduserid' => $user->id,
        'context' => $context
    );

    if (!empty($course)) {
        $eventdata['courseid'] = $course->id;
        $eventdata['other'] = array(
            'courseid' => $course->id,
            'courseshortname' => $course->shortname,
            'coursefullname' => $course->fullname
        );
    }

    $event = \core\event\user_profile_viewed::create($eventdata);
    $event->add_record_snapshot('user', $user);
    $event->trigger();
}

/**
 * Does the user have all required custom fields set?
 *
 * Internal, to be exclusively used by {@link user_not_fully_set_up()} only.
 *
 * Note that if users have no way to fill a required field via editing their
 * profiles (e.g. the field is not visible or it is locked), we still return true.
 * So this is actually checking if we should redirect the user to edit their
 * profile, rather than whether there is a value in the database.
 *
 * @param int $userid
 * @return bool
 */
function profile_has_required_custom_fields_set($userid) {
    $profilefields = profile_get_user_fields_with_data($userid);
    foreach ($profilefields as $profilefield) {
        if ($profilefield->is_required() && !$profilefield->is_locked() &&
            $profilefield->is_empty() && $profilefield->get_field_config_for_external()['visible']) {
            return false;
        }
    }

    return true;
}

/**
 * Return the list of valid custom profile user fields.
 *
 * @return array array of profile field names
 */
function get_profile_field_names(): array {
    $profilefields = profile_get_user_fields_with_data(0);
    $profilefieldnames = [];
    foreach ($profilefields as $field) {
        $profilefieldnames[] = $field->inputname;
    }
    return $profilefieldnames;
}

/**
 * Return the list of profile fields
 * in a format they can be used for choices in a group select menu.
 *
 * @return array array of category name with its profile fields
 */
function get_profile_field_list(): array {
    $customfields = profile_get_user_fields_with_data_by_category(0);
    $data = [];
    foreach ($customfields as $category) {
        foreach ($category as $field) {
            $categoryname = $field->get_category_name();
            if (!isset($data[$categoryname])) {
                $data[$categoryname] = [];
            }
            $data[$categoryname][$field->inputname] = $field->display_name();
        }
    }
    return $data;
}
