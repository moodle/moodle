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
 * Base class for a single availability condition.
 *
 * All condition types must extend this class.
 *
 * @package core_availability
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_availability;

defined('MOODLE_INTERNAL') || die();

/**
 * Base class for a single availability condition.
 *
 * All condition types must extend this class.
 *
 * The structure of a condition in JSON input data is:
 *
 * { type:'date', ... }
 *
 * where 'date' is the name of the plugin (availability_date in this case) and
 * ... is arbitrary extra data to be used by the plugin.
 *
 * Conditions require a constructor with one parameter: $structure. This will
 * contain all the JSON data for the condition. If the structure of the data
 * is incorrect (e.g. missing fields) then the constructor may throw a
 * coding_exception. However, the constructor should cope with all data that
 * was previously valid (e.g. if the format changes, old data may still be
 * present in a restore, so there should be a default value for any new fields
 * and old ones should be handled correctly).
 *
 * @package core_availability
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class condition extends tree_node {

    /**
     * Determines whether a particular item is currently available
     * according to this availability condition.
     *
     * If implementations require a course or modinfo, they should use
     * the get methods in $info.
     *
     * The $not option is potentially confusing. This option always indicates
     * the 'real' value of NOT. For example, a condition inside a 'NOT AND'
     * group will get this called with $not = true, but if you put another
     * 'NOT OR' group inside the first group, then a condition inside that will
     * be called with $not = false. We need to use the real values, rather than
     * the more natural use of the current value at this point inside the tree,
     * so that the information displayed to users makes sense.
     *
     * @param bool $not Set true if we are inverting the condition
     * @param info $info Item we're checking
     * @param bool $grabthelot Performance hint: if true, caches information
     *   required for all course-modules, to make the front page and similar
     *   pages work more quickly (works only for current user)
     * @param int $userid User ID to check availability for
     * @return bool True if available
     */
    abstract public function is_available($not, info $info, $grabthelot, $userid);

    public function check_available($not, info $info, $grabthelot, $userid) {
        // Use is_available, and we always display (at this stage).
        $allow = $this->is_available($not, $info, $grabthelot, $userid);
        return new result($allow, $this);
    }

    public function is_available_for_all($not = false) {
        // Default is that all conditions may make something unavailable.
        return false;
    }

    /**
     * Display a representation of this condition (used for debugging).
     *
     * @return string Text representation of condition
     */
    public function __toString() {
        return '{' . $this->get_type() . ':' . $this->get_debug_string() . '}';
    }

    /**
     * Gets the type name (e.g. 'date' for availability_date) of plugin.
     *
     * @return string The type name for this plugin
     */
    protected function get_type() {
        return preg_replace('~^availability_(.*?)\\\\condition$~', '$1', get_class($this));
    }

    /**
     * Returns a marker indicating that an activity name should be placed in a description.
     *
     * Gets placeholder text which will be decoded by info::format_info later when we can safely
     * display names.
     *
     * @param int $cmid Course-module id
     * @return string Placeholder text
     * @since Moodle 4.0
     */
    public static function description_cm_name(int $cmid): string {
        return '<AVAILABILITY_CMNAME_' . $cmid . '/>';
    }

    /**
     * Returns a marker indicating that formatted text should be placed in a description.
     *
     * Gets placeholder text which will be decoded by info::format_info later when we can safely
     * call format_string.
     *
     * @param string $str Text to be processed with format_string
     * @return string Placeholder text
     * @since Moodle 4.0
     */
    public static function description_format_string(string $str): string {
        return '<AVAILABILITY_FORMAT_STRING>' . htmlspecialchars($str, ENT_NOQUOTES) .
                '</AVAILABILITY_FORMAT_STRING>';
    }

    /**
     * Returns a marker indicating that some of the description text should be computed at display
     * time.
     *
     * This will result in a call to the get_description_callback_value static function within
     * the condition class.
     *
     * Gets placeholder text which will be decoded by info::format_info later when we can safely
     * call most Moodle functions.
     *
     * @param string[] $params Array of arbitrary parameters
     * @return string Placeholder text
     * @since Moodle 4.0
     */
    public function description_callback(array $params): string {
        $out = '<AVAILABILITY_CALLBACK type="' . $this->get_type() . '">';
        $first = true;
        foreach ($params as $param) {
            if ($first) {
                $first = false;
            } else {
                $out .= '<P/>';
            }
            $out .= htmlspecialchars($param, ENT_NOQUOTES);
        }
        $out .= '</AVAILABILITY_CALLBACK>';
        return $out;
    }

    /**
     * Obtains a string describing this restriction (whether or not
     * it actually applies). Used to obtain information that is displayed to
     * students if the activity is not available to them, and for staff to see
     * what conditions are.
     *
     * The $full parameter can be used to distinguish between 'staff' cases
     * (when displaying all information about the activity) and 'student' cases
     * (when displaying only conditions they don't meet).
     *
     * If implementations require a course or modinfo, they should use
     * the get methods in $info. They should not use any other functions that
     * might rely on modinfo, such as format_string.
     *
     * To work around this limitation, use the functions:
     *
     * description_cm_name()
     * description_format_string()
     * description_callback()
     *
     * These return special markers which will be added to the string and processed
     * later after modinfo is complete.
     *
     * @param bool $full Set true if this is the 'full information' view
     * @param bool $not Set true if we are inverting the condition
     * @param info $info Item we're checking
     * @return string Information string (for admin) about all restrictions on
     *   this item
     */
    abstract public function get_description($full, $not, info $info);

    /**
     * Obtains a string describing this restriction, used when there is only
     * a single restriction to display. (I.e. this provides a 'short form'
     * rather than showing in a list.)
     *
     * Default behaviour sticks the prefix text, normally displayed above
     * the list, in front of the standard get_description call.
     *
     * If implementations require a course or modinfo, they should use
     * the get methods in $info. They should not use any other functions that
     * might rely on modinfo, such as format_string.
     *
     * To work around this limitation, use the functions:
     *
     * description_cm_name()
     * description_format_string()
     * description_callback()
     *
     * These return special markers which will be added to the string and processed
     * later after modinfo is complete.
     *
     * @param bool $full Set true if this is the 'full information' view
     * @param bool $not Set true if we are inverting the condition
     * @param info $info Item we're checking
     * @return string Information string (for admin) about all restrictions on
     *   this item
     */
    public function get_standalone_description($full, $not, info $info) {
        return get_string('list_root_and', 'availability') . ' ' .
                $this->get_description($full, $not, $info);
    }

    /**
     * Obtains a representation of the options of this condition as a string,
     * for debugging.
     *
     * @return string Text representation of parameters
     */
    abstract protected function get_debug_string();

    public function update_dependency_id($table, $oldid, $newid) {
        // By default, assumes there are no dependent ids.
        return false;
    }

    /**
     * If the plugin has been configured to rely on a particular activity's
     * completion value, it should return true here. (This is necessary so that
     * we know the course page needs to update when that activity becomes
     * complete.)
     *
     * Default implementation returns false.
     *
     * @param \stdClass $course Moodle course object
     * @param int $cmid ID of activity whose completion value is considered
     * @return boolean True if the availability of something else may rely on it
     */
    public static function completion_value_used($course, $cmid) {
        return false;
    }
}
