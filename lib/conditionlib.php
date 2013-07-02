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
 * Used for tracking conditions that apply before activities are displayed
 * to students ('conditional availability').
 *
 * @package    core_condition
 * @category   condition
 * @copyright  1999 onwards Martin Dougiamas  http://dougiamas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * CONDITION_STUDENTVIEW_HIDE - The activity is not displayed to students at all when conditions aren't met.
 */
define('CONDITION_STUDENTVIEW_HIDE', 0);
/**
 * CONDITION_STUDENTVIEW_SHOW - The activity is displayed to students as a greyed-out name, with
 * informational text that explains the conditions under which it will be available.
 */
define('CONDITION_STUDENTVIEW_SHOW', 1);

/**
 * CONDITION_MISSING_NOTHING - The $item variable is expected to contain all completion-related data
 */
define('CONDITION_MISSING_NOTHING', 0);
/**
 * CONDITION_MISSING_EXTRATABLE - The $item variable is expected to contain the fields from
 * the relevant table (course_modules or course_sections) but not the _availability data
 */
define('CONDITION_MISSING_EXTRATABLE', 1);
/**
 * CONDITION_MISSING_EVERYTHING - The $item variable is expected to contain nothing except the ID
 */
define('CONDITION_MISSING_EVERYTHING', 2);

/**
 * OP_CONTAINS - comparison operator that determines whether a specified user field contains
 * a provided variable
 */
define('OP_CONTAINS', 'contains');
/**
 * OP_DOES_NOT_CONTAIN - comparison operator that determines whether a specified user field does not
 * contain a provided variable
 */
define('OP_DOES_NOT_CONTAIN', 'doesnotcontain');
/**
 * OP_IS_EQUAL_TO - comparison operator that determines whether a specified user field is equal to
 * a provided variable
 */
define('OP_IS_EQUAL_TO', 'isequalto');
/**
 * OP_STARTS_WITH - comparison operator that determines whether a specified user field starts with
 * a provided variable
 */
define('OP_STARTS_WITH', 'startswith');
/**
 * OP_ENDS_WITH - comparison operator that determines whether a specified user field ends with
 * a provided variable
 */
define('OP_ENDS_WITH', 'endswith');
/**
 * OP_IS_EMPTY - comparison operator that determines whether a specified user field is empty
 */
define('OP_IS_EMPTY', 'isempty');
/**
 * OP_IS_NOT_EMPTY - comparison operator that determines whether a specified user field is not empty
 */
define('OP_IS_NOT_EMPTY', 'isnotempty');

require_once($CFG->libdir.'/completionlib.php');

/**
 * Core class to handle conditional activites
 *
 * @package   core_condition
 * @category  condition
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class condition_info extends condition_info_base {
    /**
     * Constructs with course-module details.
     *
     * @global moodle_database $DB
     * @uses CONDITION_MISSING_NOTHING
     * @param object $cm Moodle course-module object. May have extra fields
     *   ->conditionsgrade, ->conditionscompletion which should come from
     *   get_fast_modinfo. Should have ->availablefrom, ->availableuntil,
     *   and ->showavailability, ->course, ->visible; but the only required
     *   thing is ->id.
     * @param int $expectingmissing Used to control whether or not a developer
     *   debugging message (performance warning) will be displayed if some of
     *   the above data is missing and needs to be retrieved; a
     *   CONDITION_MISSING_xx constant
     * @param bool $loaddata If you need a 'write-only' object, set this value
     *   to false to prevent database access from constructor
     */
    public function __construct($cm, $expectingmissing = CONDITION_MISSING_NOTHING,
        $loaddata=true) {
        parent::__construct($cm, 'course_modules', 'coursemoduleid',
                $expectingmissing, $loaddata);
    }

    /**
     * Adds the extra availability conditions (if any) into the given
     * course-module (or section) object.
     *
     * This function may be called statically (for the editing form) or
     * dynamically.
     *
     * @param object $cm Moodle course-module data object
     */
    public static function fill_availability_conditions($cm) {
        parent::fill_availability_conditions_inner($cm, 'course_modules', 'coursemoduleid');
    }

    /**
     * Gets the course-module object with full necessary data to determine availability.
     * @return object Course-module object with full data
     * @throws coding_exception If data was not supplied when constructing object
     */
    public function get_full_course_module() {
        return $this->get_full_item();
    }

    /**
     * Utility function called by modedit.php; updates the
     * course_modules_availability table based on the module form data.
     *
     * @param object $cm Course-module with as much data as necessary, min id
     * @param object $fromform Data from form
     * @param bool $wipefirst If true, wipes existing conditions
     */
    public static function update_cm_from_form($cm, $fromform, $wipefirst=true) {
        $ci = new condition_info($cm, CONDITION_MISSING_EVERYTHING, false);
        parent::update_from_form($ci, $fromform, $wipefirst);
    }

    /**
     * Used in course/lib.php because we need to disable the completion JS if
     * a completion value affects a conditional activity.
     *
     * @global stdClass $CONDITIONLIB_PRIVATE
     * @param object $course Moodle course object
     * @param object $item Moodle course-module
     * @return bool True if this is used in a condition, false otherwise
     */
    public static function completion_value_used_as_condition($course, $cm) {
        // Have we already worked out a list of required completion values
        // for this course? If so just use that
        global $CONDITIONLIB_PRIVATE, $DB;
        if (!array_key_exists($course->id, $CONDITIONLIB_PRIVATE->usedincondition)) {
            // We don't have data for this course, build it
            $modinfo = get_fast_modinfo($course);
            $CONDITIONLIB_PRIVATE->usedincondition[$course->id] = array();

            // Activities
            foreach ($modinfo->cms as $othercm) {
                foreach ($othercm->conditionscompletion as $cmid => $expectedcompletion) {
                    $CONDITIONLIB_PRIVATE->usedincondition[$course->id][$cmid] = true;
                }
            }

            // Sections
            foreach ($modinfo->get_section_info_all() as $section) {
                foreach ($section->conditionscompletion as $cmid => $expectedcompletion) {
                    $CONDITIONLIB_PRIVATE->usedincondition[$course->id][$cmid] = true;
                }
            }
        }
        return array_key_exists($cm->id, $CONDITIONLIB_PRIVATE->usedincondition[$course->id]);
    }

    protected function get_context() {
        return context_module::instance($this->item->id);
    }
}


/**
 * Handles conditional access to sections.
 *
 * @package   core_condition
 * @category  condition
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class condition_info_section extends condition_info_base {
    /**
     * Constructs with course-module details.
     *
     * @global moodle_database $DB
     * @uses CONDITION_MISSING_NOTHING
     * @param object $section Moodle section object. May have extra fields
     *   ->conditionsgrade, ->conditionscompletion. Should have ->availablefrom,
     *   ->availableuntil, and ->showavailability, ->course; but the only
     *   required thing is ->id.
     * @param int $expectingmissing Used to control whether or not a developer
     *   debugging message (performance warning) will be displayed if some of
     *   the above data is missing and needs to be retrieved; a
     *   CONDITION_MISSING_xx constant
     * @param bool $loaddata If you need a 'write-only' object, set this value
     *   to false to prevent database access from constructor
     */
    public function __construct($section, $expectingmissing = CONDITION_MISSING_NOTHING,
        $loaddata=true) {
        parent::__construct($section, 'course_sections', 'coursesectionid',
                $expectingmissing, $loaddata);
    }

    /**
     * Adds the extra availability conditions (if any) into the given
     * course-module (or section) object.
     *
     * This function may be called statically (for the editing form) or
     * dynamically.
     *
     * @param object $section Moodle section data object
     */
    public static function fill_availability_conditions($section) {
        parent::fill_availability_conditions_inner($section, 'course_sections', 'coursesectionid');
    }

    /**
     * Gets the section object with full necessary data to determine availability.
     * @return object Section object with full data
     * @throws coding_exception If data was not supplied when constructing object
     */
    public function get_full_section() {
        return $this->get_full_item();
    }

    /**
     * Gets list of required fields from main table.
     * @return array Array of field names
     */
    protected function get_main_table_fields() {
        return array_merge(parent::get_main_table_fields(), array('groupingid'));
    }

    /**
     * Determines whether this particular section is currently available
     * according to these criteria.
     *
     * - This does not include the 'visible' setting (i.e. this might return
     *   true even if visible is false); visible is handled independently.
     * - This does not take account of the viewhiddenactivities capability.
     *   That should apply later.
     *
     * @global moodle_database $DB
     * @global stdclass $USER
     * @param string $information If the item has availability restrictions,
     *   a string that describes the conditions will be stored in this variable;
     *   if this variable is set blank, that means don't display anything
     * @param bool $grabthelot Performance hint: if true, caches information
     *   required for all course-modules, to make the front page and similar
     *   pages work more quickly (works only for current user)
     * @param int $userid If set, specifies a different user ID to check availability for
     * @param object $modinfo Usually leave as null for default. Specify when
     *   calling recursively from inside get_fast_modinfo. The value supplied
     *   here must include list of all CMs with 'id' and 'name'
     * @return bool True if this item is available to the user, false otherwise
     */
    public function is_available(&$information, $grabthelot=false, $userid=0, $modinfo=null) {
        global $DB, $USER, $CONDITIONLIB_PRIVATE;

        $available = parent::is_available($information, $grabthelot, $userid, $modinfo);

        // test if user is enrolled to a grouping which has access to the section
        if (!empty($this->item->groupingid)) {
            // Get real user id
            if (!$userid) {
                $userid = $USER->id;
            }
            $context = $this->get_context();

            if ($userid != $USER->id) {
                // We are requesting for a non-current user so check it individually
                // (no cache). Do grouping check first, it's probably faster
                // than the capability check
                $gotit = $DB->record_exists_sql('
                        SELECT
                            1
                        FROM
                            {groupings} g
                            JOIN {groupings_groups} gg ON g.id = gg.groupingid
                            JOIN {groups_members} gm ON gg.groupid = gm.groupid
                        WHERE
                            g.id = ? AND gm.userid = ?',
                        array($this->item->groupingid, $userid));
                if (!$gotit && !has_capability('moodle/site:accessallgroups', $context, $userid)) {
                    $available = false;
                    $information .= get_string('groupingnoaccess', 'condition');
                }
            } else {
                // Request is for current user - use cache
                if( !array_key_exists($this->item->course, $CONDITIONLIB_PRIVATE->groupingscache)) {
                    if (has_capability('moodle/site:accessallgroups', $context)) {
                        $CONDITIONLIB_PRIVATE->groupingscache[$this->item->course] = true;
                    } else {
                        $groupings = $DB->get_records_sql('
                                SELECT
                                    g.id as gid
                                FROM
                                    {groupings} g
                                    JOIN {groupings_groups} gg ON g.id = gg.groupingid
                                    JOIN {groups_members} gm ON gg.groupid = gm.groupid
                                WHERE
                                    g.courseid = ? AND gm.userid = ?',
                                array($this->item->course, $userid));
                        $list = array();
                        foreach ($groupings as $grouping) {
                            $list[$grouping->gid] = true;
                        }
                        $CONDITIONLIB_PRIVATE->groupingscache[$this->item->course] = $list;
                    }
                }

                $usergroupings = $CONDITIONLIB_PRIVATE->groupingscache[$this->item->course];
                if ($usergroupings !== true && !array_key_exists($this->item->groupingid, $usergroupings)) {
                    $available = false;
                    $information .= get_string('groupingnoaccess', 'condition');
                }
            }
        }

        $information = trim($information);
        return $available;
    }

    /**
     * Utility function called by modedit.php; updates the
     * course_modules_availability table based on the module form data.
     *
     * @param object $section Section object, must at minimum contain id
     * @param object $fromform Data from form
     * @param bool $wipefirst If true, wipes existing conditions
     */
    public static function update_section_from_form($section, $fromform, $wipefirst=true) {
        $ci = new condition_info_section($section, CONDITION_MISSING_EVERYTHING);
        parent::update_from_form($ci, $fromform, $wipefirst);
    }

    protected function get_context() {
        return context_course::instance($this->item->course);
    }
}


/**
 * Base class to handle conditional items of some kind (currently either
 * course_modules or sections; they must have a corresponding _availability
 * table).
 *
 * @package   core_condition
 * @category  condition
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class condition_info_base {
    protected $item;
    /** @var bool */
    protected $gotdata;
    /** @var string */
    protected $availtable;
    /** @var string */
    protected $availfieldtable;
    /** @var string */
    protected $idfieldname;
    /** @var array */
    protected $usergroupings;
    /** @var array An array of custom profile field ids => to their shortname */
    protected $customprofilefields = null;
    /**
     * Constructs with item details.
     *
     * @global moodle_database $DB
     * @uses CONDITION_MISSING_NOTHING
     * @uses CONDITION_MISSING_EVERYTHING
     * @uses CONDITION_MISSING_EXTRATABLE
     * @uses DEBUG_DEVELOPER
     * @param object $item Object representing some kind of item (cm or section).
     *   May have extra fields ->conditionsgrade, ->conditionscompletion.
     *   Should have ->availablefrom, ->availableuntil, and ->showavailability,
     *   ->course; but the only required thing is ->id.
     * @param string $tableprefix Prefix for table used to store availability
     *   data, e.g. 'course_modules' if we are going to look at
     *   course_modules_availability.
     * @param string $idfield Within this table, name of field used as item id
     *   (e.g. 'coursemoduleid')
     * @param int $expectingmissing Used to control whether or not a developer
     *   debugging message (performance warning) will be displayed if some of
     *   the above data is missing and needs to be retrieved; a
     *   CONDITION_MISSING_xx constant
     * @param bool $loaddata If you need a 'write-only' object, set this value
     *   to false to prevent database access from constructor
     * @return condition_info Object which can retrieve information about the
     *   activity
     */
    public function __construct($item, $tableprefix, $idfield, $expectingmissing, $loaddata) {
        global $DB;

        // Check ID as otherwise we can't do the other queries
        if (empty($item->id)) {
            throw new coding_exception('Invalid parameters; item ID not included');
        }

        // DB table to store availability conditions
        $this->availtable = $tableprefix . '_availability';

        // DB table to store availability conditions for user fields
        $this->availfieldtable = $tableprefix . '_avail_fields';

        // Name of module/section ID field in DB
        $this->idfieldname = $idfield;

        // If not loading data, don't do anything else
        if (!$loaddata) {
            $this->item = (object)array('id' => $item->id);
            $this->gotdata = false;
            return;
        }

        // Missing basic data from course_modules
        $basicfields = $this->get_main_table_fields();
        $missingbasicfields = false;
        foreach ($basicfields as $field) {
            if (!isset($item->{$field})) {
                $missingbasicfields = true;
                break;
            }
        }
        if ($missingbasicfields) {
            if ($expectingmissing<CONDITION_MISSING_EVERYTHING) {
                debugging('Performance warning: condition_info constructor is ' .
                        'faster if you pass in $item with at least basic fields ' .
                        'from its table. '.
                        '[This warning can be disabled, see phpdoc.]',
                        DEBUG_DEVELOPER);
            }
            $item = $DB->get_record($tableprefix, array('id' => $item->id),
                    implode(',', $basicfields), MUST_EXIST);
        }

        $this->item = clone($item);
        $this->gotdata = true;

        // Missing extra data
        if (!isset($item->conditionsgrade) || !isset($item->conditionscompletion) || !isset($item->conditionsfield)) {
            if ($expectingmissing<CONDITION_MISSING_EXTRATABLE) {
                debugging('Performance warning: condition_info constructor is ' .
                        'faster if you pass in a $item from get_fast_modinfo or ' .
                        'the equivalent for sections. ' .
                        '[This warning can be disabled, see phpdoc.]',
                        DEBUG_DEVELOPER);
            }

            $this->fill_availability_conditions($this->item);
        }
    }

    /**
     * Gets list of required fields from main table.
     *
     * @return array Array of field names
     */
    protected function get_main_table_fields() {
        return array('id', 'course', 'visible',
                'availablefrom', 'availableuntil', 'showavailability');
    }

    /**
     * Fills availability conditions into the item object, if they are missing,
     * otherwise does nothing. Called by subclass fill_availability_conditions.
     * @param object $item Item object
     * @param string $tableprefix Prefix of name for _availability table e.g. 'course_modules'
     * @param string $idfield Name of field that contains id e.g. 'coursemoduleid'
     * @throws coding_exception If item object doesn't have id field
     */
    protected static function fill_availability_conditions_inner($item, $tableprefix, $idfield) {
        global $DB, $CFG;
        if (empty($item->id)) {
            throw new coding_exception('Invalid parameters; item ID not included');
        }

        // Does nothing if the variables are already present
        if (!isset($item->conditionsgrade) || !isset($item->conditionscompletion) || !isset($item->conditionsfield)) {
            $item->conditionsgrade = array();
            $item->conditionscompletion = array();
            $item->conditionsfield = array();

            $conditions = $DB->get_records_sql('
                    SELECT
                        a.id AS aid, gi.*, a.sourcecmid, a.requiredcompletion, a.gradeitemid,
                        a.grademin as conditiongrademin, a.grademax as conditiongrademax
                    FROM
                        {' . $tableprefix . '_availability} a
                        LEFT JOIN {grade_items} gi ON gi.id = a.gradeitemid
                    WHERE ' . $idfield . ' = ?', array($item->id));
            foreach ($conditions as $condition) {
                if (!is_null($condition->sourcecmid)) {
                    $item->conditionscompletion[$condition->sourcecmid] =
                        $condition->requiredcompletion;
                } else {
                    $minmax = new stdClass;
                    $minmax->min = $condition->conditiongrademin;
                    $minmax->max = $condition->conditiongrademax;
                    $minmax->name = self::get_grade_name($condition);
                    $item->conditionsgrade[$condition->gradeitemid] = $minmax;
                }
            }

            // For user fields
            $sql = "SELECT a.id as cmaid, a.*, uf.*
                      FROM {" . $tableprefix . "_avail_fields} a
                 LEFT JOIN {user_info_field} uf ON a.customfieldid =  uf.id
                     WHERE " . $idfield . " = :itemid";
            $conditions = $DB->get_records_sql($sql, array('itemid' => $item->id));
            foreach ($conditions as $condition) {
                // If the custom field is not empty, then we have a custom profile field
                if (!empty($condition->customfieldid)) {
                    $field = $condition->customfieldid;
                    // Check if the profile field name is not empty, if it is
                    // then the custom profile field no longer exists, so
                    // display !missing instead.
                    if (!empty($condition->name)) {
                        $fieldname = $condition->name;
                    } else {
                        $fieldname = '!missing';
                    }
                } else {
                    $field = $condition->userfield;
                    $fieldname = $condition->userfield;
                }
                $details = new stdClass;
                $details->fieldname = $fieldname;
                $details->operator = $condition->operator;
                $details->value = $condition->value;
                $item->conditionsfield[$field] = $details;
            }
        }
    }

    /**
     * Obtains the name of a grade item.
     *
     * @global object $CFG
     * @param object $gradeitemobj Object from get_record on grade_items table,
     *     (can be empty if you want to just get !missing)
     * @return string Name of item of !missing if it didn't exist
     */
    private static function get_grade_name($gradeitemobj) {
        global $CFG;
        if (isset($gradeitemobj->id)) {
            require_once($CFG->libdir . '/gradelib.php');
            $item = new grade_item;
            grade_object::set_properties($item, $gradeitemobj);
            return $item->get_name();
        } else {
            return '!missing'; // Ooops, missing grade
        }
    }

    /**
     * Gets the item object with full necessary data to determine availability.
     * @return object Item object with full data
     * @throws coding_exception If data was not supplied when constructing object
     */
    protected function get_full_item() {
        $this->require_data();
        return $this->item;
    }

    /**
     * The operators that provide the relationship
     * between a field and a value.
     *
     * @return array Associative array from operator constant to display name
     */
    public static function get_condition_user_field_operators() {
        return array(
            OP_CONTAINS => get_string('contains', 'condition'),
            OP_DOES_NOT_CONTAIN => get_string('doesnotcontain', 'condition'),
            OP_IS_EQUAL_TO => get_string('isequalto', 'condition'),
            OP_STARTS_WITH => get_string('startswith', 'condition'),
            OP_ENDS_WITH => get_string('endswith', 'condition'),
            OP_IS_EMPTY => get_string('isempty', 'condition'),
            OP_IS_NOT_EMPTY => get_string('isnotempty', 'condition'),
        );
    }

    /**
     * Returns list of user fields that can be compared.
     *
     * If you specify $formatoptions, then format_string will be called on the
     * custom field names. This is necessary for multilang support to work so
     * you should include this parameter unless you are going to format the
     * text later.
     *
     * @param array $formatoptions Passed to format_string if provided
     * @return array Associative array from user field constants to display name
     */
    public static function get_condition_user_fields($formatoptions = null) {
        global $DB;

        $userfields = array(
            'firstname' => get_user_field_name('firstname'),
            'lastname' => get_user_field_name('lastname'),
            'email' => get_user_field_name('email'),
            'city' => get_user_field_name('city'),
            'country' => get_user_field_name('country'),
            'url' => get_user_field_name('url'),
            'icq' => get_user_field_name('icq'),
            'skype' => get_user_field_name('skype'),
            'aim' => get_user_field_name('aim'),
            'yahoo' => get_user_field_name('yahoo'),
            'msn' => get_user_field_name('msn'),
            'idnumber' => get_user_field_name('idnumber'),
            'institution' => get_user_field_name('institution'),
            'department' => get_user_field_name('department'),
            'phone1' => get_user_field_name('phone1'),
            'phone2' => get_user_field_name('phone2'),
            'address' => get_user_field_name('address')
        );

        // Go through the custom profile fields now
        if ($user_info_fields = $DB->get_records('user_info_field')) {
            foreach ($user_info_fields as $field) {
                if ($formatoptions) {
                    $userfields[$field->id] = format_string($field->name, true, $formatoptions);
                } else {
                    $userfields[$field->id] = $field->name;
                }
            }
        }

        return $userfields;
    }

    /**
     * Adds to the database a condition based on completion of another module.
     *
     * @global moodle_database $DB
     * @param int $cmid ID of other module
     * @param int $requiredcompletion COMPLETION_xx constant
     */
    public function add_completion_condition($cmid, $requiredcompletion) {
        global $DB;
        // Add to DB
        $DB->insert_record($this->availtable, (object)array(
                $this->idfieldname => $this->item->id,
                'sourcecmid' => $cmid, 'requiredcompletion' => $requiredcompletion),
                false);

        // Store in memory too
        $this->item->conditionscompletion[$cmid] = $requiredcompletion;
    }

    /**
     * Adds user fields condition
     *
     * @param mixed $field numeric if it is a user profile field, character
     *                     if it is a column in the user table
     * @param int $operator specifies the relationship between field and value
     * @param char $value the value of the field
     */
    public function add_user_field_condition($field, $operator, $value) {
        global $DB;

        // Get the field name
        $idfieldname = $this->idfieldname;

        $objavailfield = new stdClass;
        $objavailfield->$idfieldname = $this->item->id;
        if (is_numeric($field)) { // If the condition field is numeric then it is a custom profile field
            // Need to get the field name so we can add it to the cache
            $ufield = $DB->get_field('user_info_field', 'name', array('id' => $field));
            $objavailfield->fieldname = $ufield;
            $objavailfield->customfieldid = $field;
        } else {
            $objavailfield->fieldname = $field;
            $objavailfield->userfield = $field;
        }
        $objavailfield->operator = $operator;
        $objavailfield->value = $value;
        $DB->insert_record($this->availfieldtable, $objavailfield, false);

        // Store in memory too
        $this->item->conditionsfield[$field] = $objavailfield;
    }

    /**
     * Adds to the database a condition based on the value of a grade item.
     *
     * @global moodle_database $DB
     * @param int $gradeitemid ID of grade item
     * @param float $min Minimum grade (>=), up to 5 decimal points, or null if none
     * @param float $max Maximum grade (<), up to 5 decimal points, or null if none
     * @param bool $updateinmemory If true, updates data in memory; otherwise,
     *   memory version may be out of date (this has performance consequences,
     *   so don't do it unless it really needs updating)
     */
    public function add_grade_condition($gradeitemid, $min, $max, $updateinmemory=false) {
        global $DB;
        // Normalise nulls
        if ($min==='') {
            $min = null;
        }
        if ($max==='') {
            $max = null;
        }
        // Add to DB
        $DB->insert_record($this->availtable, (object)array(
                $this->idfieldname => $this->item->id,
                'gradeitemid' => $gradeitemid, 'grademin' => $min, 'grademax' => $max),
                false);

        // Store in memory too
        if ($updateinmemory) {
            $this->item->conditionsgrade[$gradeitemid] = (object) array(
                    'min' => $min, 'max' => $max);
            $this->item->conditionsgrade[$gradeitemid]->name = self::get_grade_name(
                    $DB->get_record('grade_items', array('id'=>$gradeitemid)));
        }
    }

     /**
     * Erases from the database all conditions for this activity.
     *
     * @global moodle_database $DB
     */
    public function wipe_conditions() {
        // Wipe from DB
        global $DB;

        $DB->delete_records($this->availtable, array($this->idfieldname => $this->item->id));
        $DB->delete_records($this->availfieldtable, array($this->idfieldname => $this->item->id));

        // And from memory
        $this->item->conditionsgrade = array();
        $this->item->conditionscompletion = array();
        $this->item->conditionsfield = array();
    }

    /**
     * Obtains a string describing all availability restrictions (even if
     * they do not apply any more).
     *
     * @global stdClass $COURSE
     * @global moodle_database $DB
     * @param object $modinfo Usually leave as null for default. Specify when
     *   calling recursively from inside get_fast_modinfo. The value supplied
     *   here must include list of all CMs with 'id' and 'name'
     * @return string Information string (for admin) about all restrictions on
     *   this item
     */
    public function get_full_information($modinfo=null) {
        global $COURSE, $DB;
        $this->require_data();

        $information = '';


        // Completion conditions
        if (count($this->item->conditionscompletion) > 0) {
            if ($this->item->course == $COURSE->id) {
                $course = $COURSE;
            } else {
                $course = $DB->get_record('course', array('id' => $this->item->course),
                        'id, enablecompletion, modinfo, sectioncache', MUST_EXIST);
            }
            foreach ($this->item->conditionscompletion as $cmid => $expectedcompletion) {
                if (!$modinfo) {
                    $modinfo = get_fast_modinfo($course);
                }
                if (empty($modinfo->cms[$cmid])) {
                    continue;
                }
                $information .= html_writer::start_tag('li');
                $information .= get_string(
                        'requires_completion_' . $expectedcompletion,
                        'condition', $modinfo->cms[$cmid]->name) . ' ';
                $information .= html_writer::end_tag('li');
            }
        }

        // Grade conditions
        if (count($this->item->conditionsgrade) > 0) {
            foreach ($this->item->conditionsgrade as $gradeitemid => $minmax) {
                // String depends on type of requirement. We are coy about
                // the actual numbers, in case grades aren't released to
                // students.
                if (is_null($minmax->min) && is_null($minmax->max)) {
                    $string = 'any';
                } else if (is_null($minmax->max)) {
                    $string = 'min';
                } else if (is_null($minmax->min)) {
                    $string = 'max';
                } else {
                    $string = 'range';
                }
                $information .= html_writer::start_tag('li');
                $information .= get_string('requires_grade_'.$string, 'condition', $minmax->name).' ';
                $information .= html_writer::end_tag('li');
            }
        }

        // User field conditions
        if (count($this->item->conditionsfield) > 0) {
            $context = $this->get_context();
            // Need the array of operators
            foreach ($this->item->conditionsfield as $field => $details) {
                $a = new stdclass;
                // Display the fieldname into current lang.
                $translatedfieldname = get_user_field_name($details->fieldname);
                $a->field = format_string($translatedfieldname, true, array('context' => $context));
                $a->value = s($details->value);
                $information .= html_writer::start_tag('li');
                $information .= get_string('requires_user_field_'.$details->operator, 'condition', $a) . ' ';
                $information .= html_writer::end_tag('li');
            }
        }

        // The date logic is complicated. The intention of this logic is:
        // 1) display date without time where possible (whenever the date is
        //    midnight)
        // 2) when the 'until' date is e.g. 00:00 on the 14th, we display it as
        //    'until the 13th' (experience at the OU showed that students are
        //    likely to interpret 'until <date>' as 'until the end of <date>').
        // 3) This behaviour becomes confusing for 'same-day' dates where there
        //    are some exceptions.
        // Users in different time zones will typically not get the 'abbreviated'
        // behaviour but it should work OK for them aside from that.

        // The following cases are possible:
        // a) From 13:05 on 14 Oct until 12:10 on 17 Oct (exact, exact)
        // b) From 14 Oct until 12:11 on 17 Oct (midnight, exact)
        // c) From 13:05 on 14 Oct until 17 Oct (exact, midnight 18 Oct)
        // d) From 14 Oct until 17 Oct (midnight 14 Oct, midnight 18 Oct)
        // e) On 14 Oct (midnight 14 Oct, midnight 15 Oct)
        // f) From 13:05 on 14 Oct until 0:00 on 15 Oct (exact, midnight, same day)
        // g) From 0:00 on 14 Oct until 12:05 on 14 Oct (midnight, exact, same day)
        // h) From 13:05 on 14 Oct (exact)
        // i) From 14 Oct (midnight)
        // j) Until 13:05 on 14 Oct (exact)
        // k) Until 14 Oct (midnight 15 Oct)

        // Check if start and end dates are 'midnights', if so we show in short form
        $shortfrom = self::is_midnight($this->item->availablefrom);
        $shortuntil = self::is_midnight($this->item->availableuntil);

        // For some checks and for display, we need the previous day for the 'until'
        // value, if we are going to display it in short form
        if ($this->item->availableuntil) {
            $daybeforeuntil = strtotime('-1 day', usergetmidnight($this->item->availableuntil));
        }

        // Special case for if one but not both are exact and they are within a day
        if ($this->item->availablefrom && $this->item->availableuntil &&
                $shortfrom != $shortuntil && $daybeforeuntil < $this->item->availablefrom) {
            // Don't use abbreviated version (see examples f, g above)
            $shortfrom = false;
            $shortuntil = false;
        }

        // When showing short end date, the display time is the 'day before' one
        $displayuntil = $shortuntil ? $daybeforeuntil : $this->item->availableuntil;

        if ($this->item->availablefrom && $this->item->availableuntil) {
            if ($shortfrom && $shortuntil && $daybeforeuntil == $this->item->availablefrom) {
                $information .= html_writer::start_tag('li');
                $information .= get_string('requires_date_both_single_day', 'condition',
                        self::show_time($this->item->availablefrom, true));
                $information .= html_writer::end_tag('li');
            } else {
                $information .= html_writer::start_tag('li');
                $information .= get_string('requires_date_both', 'condition', (object)array(
                         'from' => self::show_time($this->item->availablefrom, $shortfrom),
                         'until' => self::show_time($displayuntil, $shortuntil)));
                $information .= html_writer::end_tag('li');
            }
        } else if ($this->item->availablefrom) {
            $information .= html_writer::start_tag('li');
            $information .= get_string('requires_date', 'condition',
                self::show_time($this->item->availablefrom, $shortfrom));
            $information .= html_writer::end_tag('li');
        } else if ($this->item->availableuntil) {
            $information .= html_writer::start_tag('li');
            $information .= get_string('requires_date_before', 'condition',
                self::show_time($displayuntil, $shortuntil));
            $information .= html_writer::end_tag('li');
        }

        // The information is in <li> tags, but to avoid taking up more space
        // if there is only a single item, we strip out the list tags so that it
        // is plain text in that case.
        if (!empty($information)) {
            $li = strpos($information, '<li>', 4);
            if ($li === false) {
                $information = preg_replace('~^<li>(.*)</li>$~', '$1', $information);
            } else {
                $information = html_writer::tag('ul', $information);
            }
            $information = trim($information);
        }
        return $information;
    }

    /**
     * Checks whether a given time refers exactly to midnight (in current user
     * timezone).
     *
     * @param int $time Time
     * @return bool True if time refers to midnight, false if it's some other
     *   time or if it is set to zero
     */
    private static function is_midnight($time) {
        return $time && usergetmidnight($time) == $time;
    }

    /**
     * Determines whether this particular item is currently available
     * according to these criteria.
     *
     * - This does not include the 'visible' setting (i.e. this might return
     *   true even if visible is false); visible is handled independently.
     * - This does not take account of the viewhiddenactivities capability.
     *   That should apply later.
     *
     * @global stdClass $COURSE
     * @global moodle_database $DB
     * @uses COMPLETION_COMPLETE
     * @uses COMPLETION_COMPLETE_FAIL
     * @uses COMPLETION_COMPLETE_PASS
     * @param string $information If the item has availability restrictions,
     *   a string that describes the conditions will be stored in this variable;
     *   if this variable is set blank, that means don't display anything
     * @param bool $grabthelot Performance hint: if true, caches information
     *   required for all course-modules, to make the front page and similar
     *   pages work more quickly (works only for current user)
     * @param int $userid If set, specifies a different user ID to check availability for
     * @param object $modinfo Usually leave as null for default. Specify when
     *   calling recursively from inside get_fast_modinfo. The value supplied
     *   here must include list of all CMs with 'id' and 'name'
     * @return bool True if this item is available to the user, false otherwise
     */
    public function is_available(&$information, $grabthelot=false, $userid=0, $modinfo=null) {
        global $COURSE, $DB;
        $this->require_data();

        $available = true;
        $information = '';

        // Check each completion condition
        if (count($this->item->conditionscompletion) > 0) {
            if ($this->item->course == $COURSE->id) {
                $course = $COURSE;
            } else {
                $course = $DB->get_record('course', array('id' => $this->item->course),
                        'id, enablecompletion, modinfo, sectioncache', MUST_EXIST);
            }

            $completion = new completion_info($course);
            foreach ($this->item->conditionscompletion as $cmid => $expectedcompletion) {
                // If this depends on a deleted module, handle that situation
                // gracefully.
                if (!$modinfo) {
                    $modinfo = get_fast_modinfo($course);
                }
                if (empty($modinfo->cms[$cmid])) {
                    global $PAGE;
                    if (isset($PAGE) && strpos($PAGE->pagetype, 'course-view-')===0) {
                        debugging("Warning: activity {$this->item->id} '{$this->item->name}' has condition " .
                                "on deleted activity $cmid (to get rid of this message, edit the named activity)");
                    }
                    continue;
                }

                // The completion system caches its own data
                $completiondata = $completion->get_data((object)array('id' => $cmid),
                        $grabthelot, $userid, $modinfo);

                $thisisok = true;
                if ($expectedcompletion==COMPLETION_COMPLETE) {
                    // 'Complete' also allows the pass, fail states
                    switch ($completiondata->completionstate) {
                        case COMPLETION_COMPLETE:
                        case COMPLETION_COMPLETE_FAIL:
                        case COMPLETION_COMPLETE_PASS:
                            break;
                        default:
                            $thisisok = false;
                    }
                } else {
                    // Other values require exact match
                    if ($completiondata->completionstate!=$expectedcompletion) {
                        $thisisok = false;
                    }
                }
                if (!$thisisok) {
                    $available = false;
                    $information .= html_writer::start_tag('li');
                    $information .= get_string(
                        'requires_completion_' . $expectedcompletion,
                        'condition', $modinfo->cms[$cmid]->name) . ' ';
                    $information .= html_writer::end_tag('li');
                }
            }
        }

        // Check each grade condition
        if (count($this->item->conditionsgrade)>0) {
            foreach ($this->item->conditionsgrade as $gradeitemid => $minmax) {
                $score = $this->get_cached_grade_score($gradeitemid, $grabthelot, $userid);
                if ($score===false ||
                        (!is_null($minmax->min) && $score<$minmax->min) ||
                        (!is_null($minmax->max) && $score>=$minmax->max)) {
                    // Grade fail
                    $available = false;
                    // String depends on type of requirement. We are coy about
                    // the actual numbers, in case grades aren't released to
                    // students.
                    if (is_null($minmax->min) && is_null($minmax->max)) {
                        $string = 'any';
                    } else if (is_null($minmax->max)) {
                        $string = 'min';
                    } else if (is_null($minmax->min)) {
                        $string = 'max';
                    } else {
                        $string = 'range';
                    }
                    $information .= html_writer::start_tag('li');
                    $information .= get_string('requires_grade_' . $string, 'condition', $minmax->name) . ' ';
                    $information .= html_writer::end_tag('li');
                }
            }
        }

        // Check if user field condition
        if (count($this->item->conditionsfield) > 0) {
            $context = $this->get_context();
            foreach ($this->item->conditionsfield as $field => $details) {
                $uservalue = $this->get_cached_user_profile_field($userid, $field);
                if (!$this->is_field_condition_met($details->operator, $uservalue, $details->value)) {
                    // Set available to false
                    $available = false;
                    $a = new stdClass();
                    $a->field = format_string($details->fieldname, true, array('context' => $context));
                    $a->value = s($details->value);
                    $information .= html_writer::start_tag('li');
                    $information .= get_string('requires_user_field_'.$details->operator, 'condition', $a) . ' ';
                    $information .= html_writer::end_tag('li');
                }
            }
        }

        // Test dates
        if ($this->item->availablefrom) {
            if (time() < $this->item->availablefrom) {
                $available = false;

                $information .= html_writer::start_tag('li');
                $information .= get_string('requires_date', 'condition',
                        self::show_time($this->item->availablefrom,
                            self::is_midnight($this->item->availablefrom)));
                $information .= html_writer::end_tag('li');
            }
        }

        if ($this->item->availableuntil) {
            if (time() >= $this->item->availableuntil) {
                $available = false;
                // But we don't display any information about this case. This is
                // because the only reason to set a 'disappear' date is usually
                // to get rid of outdated information/clutter in which case there
                // is no point in showing it...

                // Note it would be nice if we could make it so that the 'until'
                // date appears below the item while the item is still accessible,
                // unfortunately this is not possible in the current system. Maybe
                // later, or if somebody else wants to add it.
            }
        }

        // If the item is marked as 'not visible' then we don't change the available
        // flag (visible/available are treated distinctly), but we remove any
        // availability info. If the item is hidden with the eye icon, it doesn't
        // make sense to show 'Available from <date>' or similar, because even
        // when that date arrives it will still not be available unless somebody
        // toggles the eye icon.
        if (!$this->item->visible) {
            $information = '';
        }

        // The information is in <li> tags, but to avoid taking up more space
        // if there is only a single item, we strip out the list tags so that it
        // is plain text in that case.
        if (!empty($information)) {
            $li = strpos($information, '<li>', 4);
            if ($li === false) {
                $information = preg_replace('~^<li>(.*)</li>$~', '$1', $information);
            } else {
                $information = html_writer::tag('ul', $information);
            }
            $information = trim($information);
        }
        return $available;
    }

    /**
     * Shows a time either as a date or a full date and time, according to
     * user's timezone.
     *
     * @param int $time Time
     * @param bool $dateonly If true, uses date only
     * @return string Date
     */
    private function show_time($time, $dateonly) {
        return userdate($time,
                get_string($dateonly ? 'strftimedate' : 'strftimedatetime', 'langconfig'));
    }

    /**
     * Checks whether availability information should be shown to normal users.
     *
     * @return bool True if information about availability should be shown to
     *   normal users
     * @throws coding_exception If data wasn't loaded
     */
    public function show_availability() {
        $this->require_data();
        return $this->item->showavailability;
    }

    /**
     * Internal function cheks that data was loaded.
     *
     * @throws coding_exception If data wasn't loaded
     */
    private function require_data() {
        if (!$this->gotdata) {
            throw new coding_exception('Error: cannot call when info was ' .
                'constructed without data');
        }
    }

    /**
     * Obtains a grade score. Note that this score should not be displayed to
     * the user, because gradebook rules might prohibit that. It may be a
     * non-final score subject to adjustment later.
     *
     * @global stdClass $USER
     * @global moodle_database $DB
     * @global stdClass $SESSION
     * @param int $gradeitemid Grade item ID we're interested in
     * @param bool $grabthelot If true, grabs all scores for current user on
     *   this course, so that later ones come from cache
     * @param int $userid Set if requesting grade for a different user (does
     *   not use cache)
     * @return float Grade score as a percentage in range 0-100 (e.g. 100.0
     *   or 37.21), or false if user does not have a grade yet
     */
    private function get_cached_grade_score($gradeitemid, $grabthelot=false, $userid=0) {
        global $USER, $DB, $SESSION;
        if ($userid==0 || $userid==$USER->id) {
            // For current user, go via cache in session
            if (empty($SESSION->gradescorecache) || $SESSION->gradescorecacheuserid!=$USER->id) {
                $SESSION->gradescorecache = array();
                $SESSION->gradescorecacheuserid = $USER->id;
            }
            if (!array_key_exists($gradeitemid, $SESSION->gradescorecache)) {
                if ($grabthelot) {
                    // Get all grades for the current course
                    $rs = $DB->get_recordset_sql('
                            SELECT
                                gi.id,gg.finalgrade,gg.rawgrademin,gg.rawgrademax
                            FROM
                                {grade_items} gi
                                LEFT JOIN {grade_grades} gg ON gi.id=gg.itemid AND gg.userid=?
                            WHERE
                                gi.courseid = ?', array($USER->id, $this->item->course));
                    foreach ($rs as $record) {
                        $SESSION->gradescorecache[$record->id] =
                            is_null($record->finalgrade)
                                // No grade = false
                                ? false
                                // Otherwise convert grade to percentage
                                : (($record->finalgrade - $record->rawgrademin) * 100) /
                                    ($record->rawgrademax - $record->rawgrademin);

                    }
                    $rs->close();
                    // And if it's still not set, well it doesn't exist (eg
                    // maybe the user set it as a condition, then deleted the
                    // grade item) so we call it false
                    if (!array_key_exists($gradeitemid, $SESSION->gradescorecache)) {
                        $SESSION->gradescorecache[$gradeitemid] = false;
                    }
                } else {
                    // Just get current grade
                    $record = $DB->get_record('grade_grades', array(
                        'userid'=>$USER->id, 'itemid'=>$gradeitemid));
                    if ($record && !is_null($record->finalgrade)) {
                        $score = (($record->finalgrade - $record->rawgrademin) * 100) /
                            ($record->rawgrademax - $record->rawgrademin);
                    } else {
                        // Treat the case where row exists but is null, same as
                        // case where row doesn't exist
                        $score = false;
                    }
                    $SESSION->gradescorecache[$gradeitemid]=$score;
                }
            }
            return $SESSION->gradescorecache[$gradeitemid];
        } else {
            // Not the current user, so request the score individually
            $record = $DB->get_record('grade_grades', array(
                'userid'=>$userid, 'itemid'=>$gradeitemid));
            if ($record && !is_null($record->finalgrade)) {
                $score = (($record->finalgrade - $record->rawgrademin) * 100) /
                    ($record->rawgrademax - $record->rawgrademin);
            } else {
                // Treat the case where row exists but is null, same as
                // case where row doesn't exist
                $score = false;
            }
            return $score;
        }
    }

    /**
     * Returns true if a field meets the required conditions, false otherwise.
     *
     * @param string $operator the requirement/condition
     * @param string $uservalue the user's value
     * @param string $value the value required
     * @return boolean
     */
    private function is_field_condition_met($operator, $uservalue, $value) {
        if ($uservalue === false) {
            // If the user value is false this is an instant fail.
            // All user values come from the database as either data or the default.
            // They will always be a string.
            return false;
        }
        $fieldconditionmet = true;
        // Just to be doubly sure it is a string.
        $uservalue = (string)$uservalue;
        switch($operator) {
            case OP_CONTAINS: // contains
                $pos = strpos($uservalue, $value);
                if ($pos === false) {
                    $fieldconditionmet = false;
                }
                break;
            case OP_DOES_NOT_CONTAIN: // does not contain
                if (!empty($value)) {
                    $pos = strpos($uservalue, $value);
                    if ($pos !== false) {
                        $fieldconditionmet = false;
                    }
                }
                break;
            case OP_IS_EQUAL_TO: // equal to
                if ($value !== $uservalue) {
                    $fieldconditionmet = false;
                }
                break;
            case OP_STARTS_WITH: // starts with
                $length = strlen($value);
                if ((substr($uservalue, 0, $length) !== $value)) {
                    $fieldconditionmet = false;
                }
                break;
            case OP_ENDS_WITH: // ends with
                $length = strlen($value);
                $start  = $length * -1; // negative
                if (substr($uservalue, $start) !== $value) {
                    $fieldconditionmet = false;
                }
                break;
            case OP_IS_EMPTY: // is empty
                if (!empty($uservalue)) {
                    $fieldconditionmet = false;
                }
                break;
            case OP_IS_NOT_EMPTY: // is not empty
                if (empty($uservalue)) {
                    $fieldconditionmet = false;
                }
                break;
        }
        return $fieldconditionmet;
    }

    /**
     * Return the value for a user's profile field
     *
     * @param int $userid set if requesting grade for a different user (does not use cache)
     * @param int $fieldid the user profile field id
     * @return string the user value, or false if user does not have a user field value yet
     */
    protected function get_cached_user_profile_field($userid, $fieldid) {
        global $USER, $DB, $CFG;

        if ($userid === 0) {
            // Map out userid = 0 to the current user
            $userid = $USER->id;
        }
        $iscurrentuser = $USER->id == $userid;

        if (isguestuser($userid) || ($iscurrentuser && !isloggedin())) {
            // Must be logged in and can't be the guest. (e.g. front page)
            return false;
        }

        // Custom profile fields will be numeric, there are no numeric standard profile fields so this is not a problem.
        $iscustomprofilefield = is_numeric($fieldid);
        if ($iscustomprofilefield) {
            // As its a custom profile field we need to map the id back to the actual field.
            // We'll also preload all of the other custom profile fields just in case and ensure we have the
            // default value available as well.
            if ($this->customprofilefields === null) {
                $this->customprofilefields = $DB->get_records('user_info_field', null, 'sortorder ASC, id ASC', 'id, shortname, defaultdata');
            }
            if (!array_key_exists($fieldid, $this->customprofilefields)) {
                // No such field exists.
                // This shouldn't normally happen but occur if things go wrong when deleting a custom profile field
                // or when restoring a backup of a course with user profile field conditions.
                return false;
            }
            $field = $this->customprofilefields[$fieldid]->shortname;
        } else {
            $field = $fieldid;
        }

        // If its the current user than most likely we will be able to get this information from $USER.
        // If its a regular profile field then it should already be available, if not then we have a mega problem.
        // If its a custom profile field then it should be available but may not be. If it is then we use the value
        // available, otherwise we load all custom profile fields into a temp object and refer to that.
        // Noting its not going be great for performance if we have to use the temp object as it involves loading the
        // custom profile field API and classes.
        if ($iscurrentuser) {
            if (!$iscustomprofilefield) {
                if (property_exists($USER, $field)) {
                    return $USER->{$field};
                } else {
                    // Unknown user field. This should not happen.
                    throw new coding_exception('Requested user profile field does not exist');
                }
            }
            // Checking if the custom profile fields are already available.
            if (!isset($USER->profile)) {
                // Drat! they're not. We need to use a temp object and load them.
                // We don't use $USER as the profile fields are loaded into the object.
                $user = new stdClass;
                $user->id = $USER->id;
                // This should ALWAYS be set, but just in case we check.
                require_once($CFG->dirroot.'/user/profile/lib.php');
                profile_load_custom_fields($user);
                if (array_key_exists($field, $user->profile)) {
                    return $user->profile[$field];
                }
            } else if (array_key_exists($field, $USER->profile)) {
                // Hurrah they're available, this is easy.
                return $USER->profile[$field];
            }
            // The profile field doesn't exist.
            return false;
        } else {
            // Loading for another user.
            if ($iscustomprofilefield) {
                // Fetch the data for the field. Noting we keep this query simple so that Database caching takes care of performance
                // for us (this will likely be hit again).
                // We are able to do this because we've already pre-loaded the custom fields.
                $data = $DB->get_field('user_info_data', 'data', array('userid' => $userid, 'fieldid' => $fieldid), IGNORE_MISSING);
                // If we have data return that, otherwise return the default.
                if ($data !== false) {
                    return $data;
                } else {
                    return $this->customprofilefields[$field]->defaultdata;
                }
            } else {
                // Its a standard field, retrieve it from the user.
                return $DB->get_field('user', $field, array('id' => $userid), MUST_EXIST);
            }
        }
        return false;
    }

    /**
     * For testing only. Wipes information cached in user session.
     *
     * @global stdClass $SESSION
     */
    static function wipe_session_cache() {
        global $SESSION;
        unset($SESSION->gradescorecache);
        unset($SESSION->gradescorecacheuserid);
        unset($SESSION->userfieldcache);
        unset($SESSION->userfieldcacheuserid);
    }

    /**
     * Initialises the global cache
     * @global stdClass $CONDITIONLIB_PRIVATE
     */
    public static function init_global_cache() {
        global $CONDITIONLIB_PRIVATE;
        $CONDITIONLIB_PRIVATE = new stdClass;
        $CONDITIONLIB_PRIVATE->usedincondition = array();
        $CONDITIONLIB_PRIVATE->groupingscache = array();
    }

    /**
     * Utility function that resets grade/completion conditions in table based
     * in data from editing form.
     *
     * @param condition_info_base $ci Condition info
     * @param object $fromform Data from form
     * @param bool $wipefirst If true, wipes existing conditions
     */
    protected static function update_from_form(condition_info_base $ci, $fromform, $wipefirst) {
        if ($wipefirst) {
            $ci->wipe_conditions();
        }
        foreach ($fromform->conditiongradegroup as $record) {
            if($record['conditiongradeitemid']) {
                $ci->add_grade_condition($record['conditiongradeitemid'],
                    unformat_float($record['conditiongrademin']), unformat_float($record['conditiongrademax']));
            }
        }
        foreach ($fromform->conditionfieldgroup as $record) {
            if($record['conditionfield']) {
                $ci->add_user_field_condition($record['conditionfield'],
                        $record['conditionfieldoperator'],
                        $record['conditionfieldvalue']);
            }
        }
        if(isset ($fromform->conditioncompletiongroup)) {
            foreach($fromform->conditioncompletiongroup as $record) {
                if($record['conditionsourcecmid']) {
                    $ci->add_completion_condition($record['conditionsourcecmid'],
                        $record['conditionrequiredcompletion']);
                }
            }
        }
    }

    /**
     * Obtains context for any necessary checks.
     *
     * @return context Suitable context for the item
     */
    protected abstract function get_context();
}

condition_info::init_global_cache();
