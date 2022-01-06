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
 * An activity to interface with WebEx.
 *
 * @package    mod_webexactvity
 * @author     Eric Merrill <merrill@oakland.edu>
 * @copyright  2014 Oakland University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_webexactivity;

use \mod_webexactivity\local\type;
use \mod_webexactivity\local\exception;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/xmlize.php');

/**
 * Static factories to build meetings of the correct types, and get other info.
 *
 * @package    mod_webexactvity
 * @author     Eric Merrill <merrill@oakland.edu>
 * @copyright  2014 Oakland University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class meeting {
    /**
     * Loads a meeting object of the propper type.
     *
     * @param stdClass|int     $meeting Meeting record, or id of record, to load.
     * @return bool|meeting    A meeting object or false on failure.
     */
    public static function load($meeting) {
        global $DB;

        if (is_numeric($meeting)) {
            $record = $DB->get_record('webexactivity', array('id' => $meeting));
        } else if (is_object($meeting)) {
            $record = $meeting;
        } else {
            debugging('Unable to load meeting', DEBUG_DEVELOPER);
            return false;
        }

        switch ($record->type) {
            case webex::WEBEXACTIVITY_TYPE_MEETING:
                $meeting = new type\meeting_center\meeting($record);
                return $meeting;
                break;
            case webex::WEBEXACTIVITY_TYPE_TRAINING:
                $meeting = new type\training_center\meeting($record);
                return $meeting;
                break;
            case webex::WEBEXACTIVITY_TYPE_SUPPORT:
                debugging('Support center not yet supported', DEBUG_DEVELOPER);
                break;
            default:
                debugging('Unknown Type', DEBUG_DEVELOPER);

        }

        return false;
    }

    /**
     * Create a meeting object of the propper type.
     *
     * @param int     $type  The type to create.
     * @return bool|meeting  A meeting object or false on failure.
     */
    public static function create_new($type) {
        switch ($type) {
            case webex::WEBEXACTIVITY_TYPE_MEETING:
                return new type\meeting_center\meeting();
                break;
            case webex::WEBEXACTIVITY_TYPE_TRAINING:
                return new type\training_center\meeting();
                break;
            case webex::WEBEXACTIVITY_TYPE_SUPPORT:
                debugging('Support center not yet supported', DEBUG_DEVELOPER);
                break;
            default:
                debugging('Unknown Type', DEBUG_DEVELOPER);
        }

        return false;
    }

    /**
     * Returns an array for available meeting types and names.
     *
     * @param context   $context A Moodle context object.
     * @return array    An array of typeconst=>lang_string.
     */
    public static function get_available_types($context = null) {
        if (is_null($context)) {
            $all = false;
        } else {
            $all = has_capability('mod/webexactivity:allavailabletypes', $context);
        }
        $out = array();

        $setting = get_config('webexactivity', 'typemeetingcenter');
        if (stripos($setting, webex::WEBEXACTIVITY_TYPE_INSTALLED) !== false) {
            if ($all || (stripos($setting, webex::WEBEXACTIVITY_TYPE_ALL) !== false)) {
                $name = self::get_meeting_type_name(webex::WEBEXACTIVITY_TYPE_MEETING);
                $out[webex::WEBEXACTIVITY_TYPE_MEETING] = $name;
            }
        }

        $setting = get_config('webexactivity', 'typetrainingcenter');
        if (stripos($setting, webex::WEBEXACTIVITY_TYPE_INSTALLED) !== false) {
            if ($all || (stripos($setting, webex::WEBEXACTIVITY_TYPE_ALL) !== false)) {
                $name = self::get_meeting_type_name(webex::WEBEXACTIVITY_TYPE_TRAINING);
                $out[webex::WEBEXACTIVITY_TYPE_TRAINING] = $name;
            }
        }

        return $out;
    }

    /**
     * Checks if the passed type is valid for the user.
     *
     * @param int       $type A meeting type constant.
     * @param context   $context A Moodle context object.
     * @return bool     True if it is valid, false if not.
     * @throws coding_exception on type error.
     */
    public static function is_valid_type($type, $context) {
        if (is_null($context)) {
            $all = false;
        } else {
            $all = has_capability('mod/webexactivity:allavailabletypes', $context);
        }

        switch ($type) {
            case webex::WEBEXACTIVITY_TYPE_MEETING:
                $setting = get_config('webexactivity', 'typemeetingcenter');
                break;
            case webex::WEBEXACTIVITY_TYPE_TRAINING:
                $setting = get_config('webexactivity', 'typetrainingcenter');
                break;
            default:
                throw new \coding_exception('Unknown meeting type passed to is_valid_type.');
                break;
        }

        if (stripos($setting, webex::WEBEXACTIVITY_TYPE_INSTALLED) !== false) {
            if ($all) {
                return true;
            }
            if (stripos($setting, webex::WEBEXACTIVITY_TYPE_ALL) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns the name of a meeting type.
     *
     * @param int           $type A meeting type constant.
     * @return lang_string  String object of meeting name.
     * @throws coding_exception on type error.
     */
    public static function get_meeting_type_name($type) {
        switch ($type) {
            case webex::WEBEXACTIVITY_TYPE_MEETING:
                return get_string('typemeetingcenter', 'mod_webexactivity', null, true);
                break;
            case webex::WEBEXACTIVITY_TYPE_TRAINING:
                return get_string('typetrainingcenter', 'mod_webexactivity', null, true);
                break;
            default:
                throw new \coding_exception('Unknown meeting type passed to get_meeting_name.');
                break;
        }
    }

    /**
     * Returns if the meeting requires a password or not.
     *
     * @param int      $type A meeting type constant.
     * @return bool    True if password is required, false if not.
     * @throws coding_exception on type error.
     */
    public static function get_meeting_type_password_required($type) {
        switch ($type) {
            case webex::WEBEXACTIVITY_TYPE_MEETING:
                $setting = get_config('webexactivity', 'typemeetingcenter');
                if (stripos($setting, webex::WEBEXACTIVITY_TYPE_PASSWORD_REQUIRED) !== false) {
                    return true;
                }
                return false;
            case webex::WEBEXACTIVITY_TYPE_TRAINING:
                $setting = get_config('webexactivity', 'typetrainingcenter');
                if (stripos($setting, webex::WEBEXACTIVITY_TYPE_PASSWORD_REQUIRED) !== false) {
                    return true;
                }
                return false;
            default:
                throw new \coding_exception('Unknown meeting type passed to get_meeting_name.');
                break;
        }
    }

    /**
     * Returns if the meeting template is setup or not
     *
     * @param int      $type A meeting type constant.
     * @return string  String with webex meeting template name
     * @throws coding_exception on type error.
     */
    public static function get_meeting_type_template($type) {
        switch ($type) {
            case webex::WEBEXACTIVITY_TYPE_MEETING:
                return get_config('webexactivity', 'meetingtemplate');
            case webex::WEBEXACTIVITY_TYPE_TRAINING:
                return get_config('webexactivity', 'trainingtemplate');
            default:
                throw new \coding_exception('Unknown meeting type passed to get_meeting_template.');
                break;
        }
    }
}
