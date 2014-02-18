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
 * User profile field condition.
 *
 * @package availability_profile
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace availability_profile;

defined('MOODLE_INTERNAL') || die();

/**
 * User profile field condition.
 *
 * @package availability_profile
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class condition extends \core_availability\condition {
    /** @var string Operator: field contains value */
    const OP_CONTAINS = 'contains';

    /** @var string Operator: field does not contain value */
    const OP_DOES_NOT_CONTAIN = 'doesnotcontain';

    /** @var string Operator: field equals value */
    const OP_IS_EQUAL_TO = 'isequalto';

    /** @var string Operator: field starts with value */
    const OP_STARTS_WITH = 'startswith';

    /** @var string Operator: field ends with value */
    const OP_ENDS_WITH = 'endswith';

    /** @var string Operator: field is empty */
    const OP_IS_EMPTY = 'isempty';

    /** @var string Operator: field is not empty */
    const OP_IS_NOT_EMPTY = 'isnotempty';

    /** @var array|null Array of custom profile fields (static cache within request) */
    protected static $customprofilefields = null;

    /** @var string Field name (for standard fields) or '' if custom field */
    protected $standardfield = '';

    /** @var int Field name (for custom fields) or '' if standard field */
    protected $customfield = '';

    /** @var string Operator type (OP_xx constant) */
    protected $operator;

    /** @var string Expected value for field */
    protected $value = '';

    /**
     * Constructor.
     *
     * @param stdClass $structure Data structure from JSON decode
     * @throws coding_exception If invalid data structure.
     */
    public function __construct($structure) {
        // Get operator.
        if (isset($structure->op) && in_array($structure->op, array(self::OP_CONTAINS,
                self::OP_DOES_NOT_CONTAIN, self::OP_IS_EQUAL_TO, self::OP_STARTS_WITH,
                self::OP_ENDS_WITH, self::OP_IS_EMPTY, self::OP_IS_NOT_EMPTY), true)) {
            $this->operator = $structure->op;
        } else {
            throw new \coding_exception('Missing or invalid ->op for profile condition');
        }

        // For operators other than the empty/not empty ones, require value.
        switch($this->operator) {
            case self::OP_IS_EMPTY:
            case self::OP_IS_NOT_EMPTY:
                if (isset($structure->v)) {
                    throw new \coding_exception('Unexpected ->v for non-value operator');
                }
                break;
            default:
                if (isset($structure->v) && is_string($structure->v)) {
                    $this->value = $structure->v;
                } else {
                    throw new \coding_exception('Missing or invalid ->v for profile condition');
                }
                break;
        }

        // Get field type.
        if (property_exists($structure, 'sf')) {
            if (property_exists($structure, 'cf')) {
                throw new \coding_exception('Both ->sf and ->cf for profile condition');
            }
            if (is_string($structure->sf)) {
                $this->standardfield = $structure->sf;
            } else {
                throw new \coding_exception('Invalid ->sf for profile condition');
            }
        } else if (property_exists($structure, 'cf')) {
            if (is_string($structure->cf)) {
                $this->customfield = $structure->cf;
            } else {
                throw new \coding_exception('Invalid ->cf for profile condition');
            }
        } else {
            throw new \coding_exception('Missing ->sf or ->cf for profile condition');
        }
    }

    public function save() {
        $result = (object)array('type' => 'profile', 'op' => $this->operator);
        if ($this->customfield) {
            $result->cf = $this->customfield;
        } else {
            $result->sf = $this->standardfield;
        }
        switch($this->operator) {
            case self::OP_IS_EMPTY:
            case self::OP_IS_NOT_EMPTY:
                break;
            default:
                $result->v = $this->value;
                break;
        }
        return $result;
    }

    public function is_available($not, \core_availability\info $info, $grabthelot, $userid) {
        $uservalue = $this->get_cached_user_profile_field($userid);
        $allow = self::is_field_condition_met($this->operator, $uservalue, $this->value);
        if ($not) {
            $allow = !$allow;
        }
        return $allow;
    }

    public function get_description($full, $not, \core_availability\info $info) {
        $course = $info->get_course();
        // Display the fieldname into current lang.
        if ($this->customfield) {
            // Is a custom profile field (will use multilang).
            $customfields = self::get_custom_profile_fields();
            if (array_key_exists($this->customfield, $customfields)) {
                $translatedfieldname = $customfields[$this->customfield]->name;
            } else {
                $translatedfieldname = get_string('missing', 'availability_profile',
                        $this->customfield);
            }
        } else {
            $translatedfieldname = get_user_field_name($this->standardfield);
        }
        $context = \context_course::instance($course->id);
        $a = new \stdClass();
        $a->field = format_string($translatedfieldname, true, array('context' => $context));
        $a->value = s($this->value);
        if ($not) {
            // When doing NOT strings, we replace the operator with its inverse.
            // Some of them don't have inverses, so for those we use a new
            // identifier which is only used for this lang string.
            switch($this->operator) {
                case self::OP_CONTAINS:
                    $opname = self::OP_DOES_NOT_CONTAIN;
                    break;
                case self::OP_DOES_NOT_CONTAIN:
                    $opname = self::OP_CONTAINS;
                    break;
                case self::OP_ENDS_WITH:
                    $opname = 'notendswith';
                    break;
                case self::OP_IS_EMPTY:
                    $opname = self::OP_IS_NOT_EMPTY;
                    break;
                case self::OP_IS_EQUAL_TO:
                    $opname = 'notisequalto';
                    break;
                case self::OP_IS_NOT_EMPTY:
                    $opname = self::OP_IS_EMPTY;
                    break;
                case self::OP_STARTS_WITH:
                    $opname = 'notstartswith';
                    break;
                default:
                    throw new \coding_exception('Unexpected operator: ' . $this->operator);
            }
        } else {
            $opname = $this->operator;
        }
        return get_string('requires_' . $opname, 'availability_profile', $a);
    }

    protected function get_debug_string() {
        if ($this->customfield) {
            $out = '*' . $this->customfield;
        } else {
            $out = $this->standardfield;
        }
        $out .= ' ' . $this->operator;
        switch($this->operator) {
            case self::OP_IS_EMPTY:
            case self::OP_IS_NOT_EMPTY:
                break;
            default:
                $out .= ' ' . $this->value;
                break;
        }
        return $out;
    }

    /**
     * Returns true if a field meets the required conditions, false otherwise.
     *
     * @param string $operator the requirement/condition
     * @param string $uservalue the user's value
     * @param string $value the value required
     * @return boolean True if conditions are met
     */
    protected static function is_field_condition_met($operator, $uservalue, $value) {
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
            case self::OP_CONTAINS:
                $pos = strpos($uservalue, $value);
                if ($pos === false) {
                    $fieldconditionmet = false;
                }
                break;
            case self::OP_DOES_NOT_CONTAIN:
                if (!empty($value)) {
                    $pos = strpos($uservalue, $value);
                    if ($pos !== false) {
                        $fieldconditionmet = false;
                    }
                }
                break;
            case self::OP_IS_EQUAL_TO:
                if ($value !== $uservalue) {
                    $fieldconditionmet = false;
                }
                break;
            case self::OP_STARTS_WITH:
                $length = strlen($value);
                if ((substr($uservalue, 0, $length) !== $value)) {
                    $fieldconditionmet = false;
                }
                break;
            case self::OP_ENDS_WITH:
                $length = strlen($value);
                $start = $length * -1;
                if (substr($uservalue, $start) !== $value) {
                    $fieldconditionmet = false;
                }
                break;
            case self::OP_IS_EMPTY:
                if (!empty($uservalue)) {
                    $fieldconditionmet = false;
                }
                break;
            case self::OP_IS_NOT_EMPTY:
                if (empty($uservalue)) {
                    $fieldconditionmet = false;
                }
                break;
        }
        return $fieldconditionmet;
    }

    /**
     * Gets data about custom profile fields. Cached statically in current
     * request.
     *
     * @return array Array of records indexed by shortname
     */
    public static function get_custom_profile_fields() {
        global $DB;

        if (self::$customprofilefields === null) {
            self::$customprofilefields = $DB->get_records('user_info_field', null,
                    'id ASC', 'shortname, id, name, defaultdata');
        }
        return self::$customprofilefields;
    }

    /**
     * Wipes the static cache (for use in unit tests).
     */
    public static function wipe_static_cache() {
        self::$customprofilefields = null;
    }

    /**
     * Return the value for a user's profile field
     *
     * @param int $userid User ID
     * @return string|bool Value, or false if user does not have a value for this field
     */
    protected function get_cached_user_profile_field($userid) {
        global $USER, $DB, $CFG;
        $iscurrentuser = $USER->id == $userid;
        if (isguestuser($userid) || ($iscurrentuser && !isloggedin())) {
            // Must be logged in and can't be the guest.
            return false;
        }

        // Custom profile fields will be numeric, there are no numeric standard profile fields so this is not a problem.
        $iscustomprofilefield = $this->customfield ? true : false;
        if ($iscustomprofilefield) {
            // As its a custom profile field we need to map the id back to the actual field.
            // We'll also preload all of the other custom profile fields just in case and ensure we have the
            // default value available as well.
            if (!array_key_exists($this->customfield, self::get_custom_profile_fields())) {
                // No such field exists.
                // This shouldn't normally happen but occur if things go wrong when deleting a custom profile field
                // or when restoring a backup of a course with user profile field conditions.
                return false;
            }
            $field = $this->customfield;
        } else {
            $field = $this->standardfield;
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
                    throw new \coding_exception('Requested user profile field does not exist');
                }
            }
            // Checking if the custom profile fields are already available.
            if (!isset($USER->profile)) {
                // Drat! they're not. We need to use a temp object and load them.
                // We don't use $USER as the profile fields are loaded into the object.
                $user = new \stdClass;
                $user->id = $USER->id;
                // This should ALWAYS be set, but just in case we check.
                require_once($CFG->dirroot . '/user/profile/lib.php');
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
                $data = $DB->get_field('user_info_data', 'data', array('userid' => $userid,
                        'fieldid' => self::$customprofilefields[$field]->id), IGNORE_MISSING);
                // If we have data return that, otherwise return the default.
                if ($data !== false) {
                    return $data;
                } else {
                    return self::$customprofilefields[$field]->defaultdata;
                }
            } else {
                // Its a standard field, retrieve it from the user.
                return $DB->get_field('user', $field, array('id' => $userid), MUST_EXIST);
            }
        }
        return false;
    }

    public function is_applied_to_user_lists() {
        // Profile conditions are assumed to be 'permanent', so they affect the
        // display of user lists for activities.
        return true;
    }

    public function filter_user_list(array $users, $not, \core_availability\info $info,
            \core_availability\capability_checker $checker) {
        global $CFG, $DB;

        // Get all users from the list who match the condition.
        list ($sql, $params) = $DB->get_in_or_equal(array_keys($users));

        if ($this->customfield) {
            $customfields = self::get_custom_profile_fields();
            if (!array_key_exists($this->customfield, $customfields)) {
                // If the field isn't found, nobody matches.
                return array();
            }
            $customfield = $customfields[$this->customfield];

            // Fetch custom field value for all users.
            $values = $DB->get_records_select('user_info_data', 'fieldid = ? AND userid ' . $sql,
                    array_merge(array($customfield->id), $params),
                    '', 'userid, data');
            $valuefield = 'data';
            $default = $customfield->defaultdata;
        } else {
            $values = $DB->get_records_select('user', 'id ' . $sql, $params,
                    '', 'id, '. $this->standardfield);
            $valuefield = $this->standardfield;
            $default = '';
        }

        // Filter the user list.
        $result = array();
        foreach ($users as $id => $user) {
            // Get value for user.
            if (array_key_exists($id, $values)) {
                $value = $values[$id]->{$valuefield};
            } else {
                $value = $default;
            }

            // Check value.
            $allow = $this->is_field_condition_met($this->operator, $value, $this->value);
            if ($not) {
                $allow = !$allow;
            }
            if ($allow) {
                $result[$id] = $user;
            }
        }
        return $result;
    }
}
