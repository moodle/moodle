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
 * Availability password - Condition (Check whether the user has entered the required password)
 *
 * @package    availability_password
 * @copyright  2016 Davo Smith, Synergy Learning UK on behalf of Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace availability_password;

use cm_info;
use core_availability\info;
use core_availability\info_module;

/**
 * Condition main class
 * @package    availability_password
 * @copyright  2016 Davo Smith, Synergy Learning UK on behalf of Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class condition extends \core_availability\condition {

    /**
     * The configured password for this condition.
     *
     * @var string
     */
    protected $password = '';
    /**
     * Stores arrays of accepted passwords, indexed by cmid, for the current user.
     * Loaded from database table availability_password_grant.
     *
     * @var null|string[][]
     */
    protected static $passwordsaccepted = null;

    /**
     * condition constructor.
     * @param object $structure the configured settings for this instance
     */
    public function __construct($structure) {
        if (!empty($structure->password)) {
            $this->password = $structure->password;
        }
    }

    /**
     * Should correctly entered passwords be stored in the user session
     * or in the DB
     * @return bool true if passwords are stored in the session
     */
    private function remember_session() {
        static $remember = null;
        if ($remember === null) {
            // Note: global settings currently unsupported for availability conditions.
            // See https://tracker.moodle.org/browse/MDL-49620.
            // For now, this will always return false, unless manually inserted into the database.
            $remember = get_config('availability_password', 'remember');
        }
        return ($remember === 'session');
    }

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
    public function is_available($not, info $info, $grabthelot, $userid) {
        if (!$info instanceof info_module) {
            return true; // This should only ever be set against activities, not sections.
        }

        $cm = $info->get_course_module();
        $ret = $this->is_available_internal($cm, $userid);

        return ($not xor $ret);
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
     * the get methods in $info.
     *
     * The special string <AVAILABILITY_CMNAME_123/> can be returned, where
     * 123 is any number. It will be replaced with the correctly-formatted
     * name for that activity.
     *
     * @param bool $full Set true if this is the 'full information' view
     * @param bool $not Set true if we are inverting the condition
     * @param info $info Item we're checking
     * @return string Information string (for admin) about all restrictions on
     *   this item
     */
    public function get_description($full, $not, info $info) {
        global $USER, $PAGE;
        static $jsadded = false;

        if (!$info instanceof info_module) {
            return ''; // Should only be possible against activities, not sections.
        }
        $cm = $info->get_course_module();

        if ($not) {
            $str = get_string('requires_nopassword', 'availability_password');
        } else {
            $str = get_string('requires_password', 'availability_password');
        }
        if (!$full || !$this->is_available($not, $info, false, $USER->id)) {
            $url = new \moodle_url('/availability/condition/password/index.php', ['id' => $cm->id]);
            $str = \html_writer::link($url, $str, ['class' => 'availability_password-popup']);

            if (!$jsadded) {
                $PAGE->requires->strings_for_js(['enterpassword', 'wrongpassword', 'passwordintro', 'passwordprotection'],
                    'availability_password');
                $PAGE->requires->strings_for_js(['submit', 'cancel'], 'core');

                $jsadded = true;
                $PAGE->requires->yui_module('moodle-availability_password-popup', 'M.availability_password.popup.init');
            }
        }
        return $str;
    }

    /**
     * Obtains a representation of the options of this condition as a string,
     * for debugging.
     *
     * @return string Text representation of parameters
     */
    protected function get_debug_string() {
        return 'Password = '.$this->password;
    }

    /**
     * Saves tree data back to a structure object.
     *
     * @return \stdClass Structure object (ready to be made into JSON format)
     */
    public function save() {
        return (object) ['type' => 'password', 'password' => $this->password];
    }

    /**
     * Check the given password against the condition's password and return true
     * if they match.
     *
     * @param string $password
     * @return bool
     */
    private function check_password($password) {
        return ($password == $this->password);
    }

    /**
     * Record the fact that the given user has correctly entered the password.
     *
     * @param cm_info $cm
     * @param int $userid
     */
    private function save_available(cm_info $cm, $userid) {
        global $USER, $DB;

        if ($this->is_available_internal($cm, $userid)) {
            return; // Password already marked as accepted.
        }

        if ($this->remember_session()) {
            // Store in the session.
            if ($userid != $USER->id) {
                return; // With per-session remembering, only the current user can save passwords.
            }
            if (!isset($USER->availability_password)) {
                $USER->availability_password = [];
            }
            if (!isset($USER->availability_password[$cm->id])) {
                $USER->availability_password[$cm->id] = [];
            }
            $USER->availability_password[$cm->id][] = $this->password;

        } else {
            // Save the entered password in the DB, in case the password is changed or there are multiple passwords
            // on an activity (no idea why that would be done, but, in theory, it is supported by the code).
            $ins = (object) [
                'courseid' => $cm->course,
                'cmid' => $cm->id,
                'userid' => $userid,
                'password' => $this->password,
            ];
            $DB->insert_record('availability_password_grant', $ins, false);
        }

        self::$passwordsaccepted = null; // Clear the static cache (just in case).
    }

    /**
     * Internal check to see if the password has been entered for this condition.
     *
     * @param cm_info $cm
     * @param int $userid
     * @return bool
     */
    private function is_available_internal(cm_info $cm, $userid) {
        global $USER, $DB;

        if ($userid != $USER->id) {
            if ($this->remember_session()) {
                return false; // Only remember whilst the user is logged in.
            }
            // Not the current user - just load a single record.
            $cond = ['cmid' => $cm->id, 'userid' => $userid, 'password' => $this->password];
            return $DB->record_exists('availability_password_grant', $cond);
        }

        if (self::$passwordsaccepted === null) {
            // Current user, load the results for the whole course.
            if ($this->remember_session()) {
                // Retrieve from the user session.
                if (isset($USER->availability_password)) {
                    self::$passwordsaccepted = $USER->availability_password;
                } else {
                    self::$passwordsaccepted = [];
                }
            } else {
                // Retrieve from the database.
                $cond = ['courseid' => $cm->course, 'userid' => $userid];
                $recs = $DB->get_records('availability_password_grant', $cond);
                self::$passwordsaccepted = [];
                foreach ($recs as $rec) {
                    if (!isset(self::$passwordsaccepted[$rec->cmid])) {
                        self::$passwordsaccepted[$rec->cmid] = [];
                    }
                    self::$passwordsaccepted[$rec->cmid][] = $rec->password;
                }
            }
        }

        // Check to see if the user has entered the correct password.
        if (!isset(self::$passwordsaccepted[$cm->id])) {
            return false;
        }
        return in_array($this->password, self::$passwordsaccepted[$cm->id]);
    }

    /**
     * Tidy up the password accepted records when the activity is deleted.
     * @param \core\event\course_module_deleted $event
     */
    public static function course_module_deleted(\core\event\course_module_deleted $event) {
        global $DB;
        $cmid = $event->contextinstanceid;
        $DB->delete_records('availability_password_grant', ['cmid' => $cmid]);
    }

    /**
     * Tidy up the password accepted records when the course is deleted.
     * @param \core\event\course_deleted $event
     */
    public static function course_deleted(\core\event\course_deleted $event) {
        global $DB;
        $courseid = $event->contextinstanceid;
        $DB->delete_records('availability_password_grant', ['courseid' => $courseid]);
    }

    /**
     * Tidy up the password accepted records when the user is deleted.
     * @param \core\event\user_deleted $event
     */
    public static function user_deleted(\core\event\user_deleted $event) {
        global $DB;
        $userid = $event->contextinstanceid;
        $DB->delete_records('availability_password_grant', ['userid' => $userid]);
    }

    /**
     * Checks the given password against the restrictions set for the given cm.
     * If the password matches, then that restriction is marked as being met.
     *
     * @param cm_info $cm
     * @param string $password
     * @param int $userid (optional)
     * @return bool
     */
    public static function submit_password_for_cm(cm_info $cm, $password, $userid = null) {
        global $USER;

        if ($userid === null) {
            $userid = $USER->id;
        }
        $correct = false;
        $pconds = self::get_password_conditions($cm);
        foreach ($pconds as $pcond) {
            if ($pcond->check_password($password)) {
                $pcond->save_available($cm, $userid);
                $correct = true;
            }
        }
        return $correct;
    }

    /**
     * Does this cm have any password restrictions configured?
     *
     * @param cm_info $cm
     * @return bool
     */
    public static function has_password_condition(cm_info $cm) {
        $pconds = self::get_password_conditions($cm);
        return (bool)$pconds;
    }

    /**
     * Internal method to retrieve any and all password conditions for the given cm.
     *
     * @param cm_info $cm
     * @return condition[]
     */
    private static function get_password_conditions(cm_info $cm) {
        $info = new info_module($cm);
        $tree = $info->get_availability_tree();
        return $tree->get_all_children('\\availability_password\\condition');
    }
}
