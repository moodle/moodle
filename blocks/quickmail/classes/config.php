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
 * @package    block_quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class block_quickmail_config {

    public static $courseconfigurablefields = [
        'allowstudents',
        'roleselection',
        'receipt',
        'prepend_class',
        'default_message_type',
    ];

    /**
     * Returns a transformed config array, or specific value, for the given key (block or course relative)
     *
     * @param  string  $key
     * @param  mixed  $courseorid  optional, if set, gets specific course configuration
     * @param  bool  $transformed  whether or not to transform the output values
     * @return mixed
     */
    public static function get($key = '', $courseorid = 0, $transformed = true) {
        return $courseorid ?
            self::course($courseorid, $key, $transformed) :
            self::block($key, $transformed);
    }

    /**
     * Returns a config array for the block, and specific key if given
     *
     * @param  string  $key  optional, config key to return
     * @param  bool  $transformed  whether or not to transform the output values
     * @return array|mixed
     */
    public static function block($key = '', $transformed = true) {
        $defaultmessagetype = get_config('moodle', 'block_quickmail_message_types_available');

        $blockconfigarray = [
            'allowstudents' => get_config('moodle', 'block_quickmail_allowstudents'),
            'roleselection' => get_config('moodle', 'block_quickmail_roleselection'),
            'send_as_tasks' => get_config('moodle', 'block_quickmail_send_as_tasks'),
            'receipt' => get_config('moodle', 'block_quickmail_receipt'),
            'allow_mentor_copy' => get_config('moodle', 'block_quickmail_allow_mentor_copy'),
            'email_profile_fields' => get_config('moodle', 'block_quickmail_email_profile_fields'),
            'prepend_class' => get_config('moodle', 'block_quickmail_prepend_class'),
            'ferpa' => get_config('moodle', 'block_quickmail_ferpa'),
            'downloads' => get_config('moodle', 'block_quickmail_downloads'),
            'additionalemail' => get_config('moodle', 'block_quickmail_additionalemail'),
            'notifications_enabled' => get_config('moodle', 'block_quickmail_notifications_enabled'),
            'send_now_threshold' => get_config('moodle', 'block_quickmail_send_now_threshold'),
            'message_types_available' => $defaultmessagetype,
            'default_message_type' => $defaultmessagetype == 'all'
                ? 'email'
                : $defaultmessagetype,
        ];

        if ($transformed) {
            return self::get_transformed($blockconfigarray, $key);
        }

        return $key ? $blockconfigarray[$key] : $blockconfigarray;
    }

    /**
     * Returns a config array for the given course, and specific key if given
     *
     * @param  mixed  $courseorid
     * @param  string  $key  optional, config key to return
     * @param  bool  $transformed  whether or not to transform the output values
     * @return array|mixed
     */
    public static function course($courseorid, $key = '', $transformed = true) {
        global $DB;

        $courseid = is_object($courseorid) ? $courseorid->id : $courseorid;

        // Get this course's config, if any.
        $courseconfig = $DB->get_records_menu('block_quickmail_config', ['coursesid' => $courseid], '', 'name,value');

        // Get the master block config.
        $blockconfig = self::block('', false);

        // Determine allowstudents for this course.
        if ((int) $blockconfig['allowstudents'] < 0) {
            $courseallowstudents = 0;
        } else {
            $courseallowstudents = array_key_exists('allowstudents', $courseconfig) ?
                $courseconfig['allowstudents'] :
                $blockconfig['allowstudents'];
        }

        // Determine default message_type, if any, for this course.
        // NOTE: block-level "all" will default to course-level "email".
        if ($blockconfig['message_types_available'] == 'all') {
            $coursedefaultmessagetype = array_key_exists('default_message_type', $courseconfig)
                ? $courseconfig['default_message_type']
                : 'email';
        } else {
            $coursedefaultmessagetype = $blockconfig['message_types_available'];
        }

        $courseconfigarray = [
            'allowstudents' => $courseallowstudents,
            'roleselection' => array_key_exists('roleselection', $courseconfig)
                ? $courseconfig['roleselection']
                : $blockconfig['roleselection'],
            'receipt' => array_key_exists('receipt', $courseconfig)
                ? $courseconfig['receipt']
                : $blockconfig['receipt'],
            'prepend_class' => array_key_exists('prepend_class', $courseconfig)
                ? $courseconfig['prepend_class']
                : $blockconfig['prepend_class'],
            'ferpa' => $blockconfig['ferpa'],
            'downloads' => $blockconfig['downloads'],
            'send_as_tasks' => $blockconfig['send_as_tasks'],
            'allow_mentor_copy' => $blockconfig['allow_mentor_copy'],
            'email_profile_fields' => $blockconfig['email_profile_fields'],
            'additionalemail' => $blockconfig['additionalemail'],
            'message_types_available' => $blockconfig['message_types_available'],
            'default_message_type' => $coursedefaultmessagetype,
            'notifications_enabled' => $blockconfig['notifications_enabled'],
            'send_now_threshold' => $blockconfig['send_now_threshold'],
        ];

        if ($transformed) {
            return self::get_transformed($courseconfigarray, $key);
        }

        return $key ? $courseconfigarray[$key] : $courseconfigarray;
    }

    /**
     * Returns an array of role ids configured to be selectable when composing message
     *
     * @param  object  $courseorid  optional, if not given will default to the block-level setting
     * @return array
     */
    public static function get_role_selection_array($courseorid = null) {
        // Get course if possible.
        if (empty($courseorid)) {
            $course = null;
        } else if (is_object($courseorid)) {
            $course = $courseorid;
        } else {
            try {
                $course = get_course($courseorid);
            } catch (\Exception $e) {
                $course = null;
            }
        }

        $roleselectionvalue = $course
            ? self::course($course, 'roleselection')
            : self::block('roleselection');
        return explode(',', $roleselectionvalue);
    }

    /**
     * Returns a transformed array from the given array
     *
     * @param  array  $params
     * @param  string $key  optional, config key to return
     * @return array|mixed
     */
    public static function get_transformed($params, $key = '') {
        $transformed = [
            'allowstudents' => (int) $params['allowstudents'],
            'roleselection' => (string) $params['roleselection'],
            'receipt' => (int) $params['receipt'],
            'allow_mentor_copy' => (int) $params['allow_mentor_copy'],
            'email_profile_fields' => explode(',', $params['email_profile_fields']),
            'prepend_class' => (string) $params['prepend_class'],
            'ferpa' => (string) $params['ferpa'],
            'downloads' => (int) $params['downloads'],
            'send_as_tasks' => (int) $params['send_as_tasks'],
            'additionalemail' => (int) $params['additionalemail'],
            'message_types_available' => (string) $params['message_types_available'],
            'default_message_type' => (string) $params['default_message_type'],
            'notifications_enabled' => (int) $params['notifications_enabled'],
            'send_now_threshold' => (int) $params['send_now_threshold'],
        ];

        return $key ? $transformed[$key] : $transformed;
    }

    /**
     * Returns the supported message types
     *
     * @return array
     */
    public static function get_supported_message_types() {
        global $CFG;

        $types = [
            'all',
            'email'
        ];

        if ( ! empty($CFG->messaging)) {
            $types[] = 'message';
        }

        return $types;
    }

    /**
     * Returns an array of editor options with a given context
     *
     * @param  object $context
     * @return array
     */
    public static function get_editor_options($context) {
        return [
            'trusttext' => true,
            'subdirs' => true,
            'maxfiles' => -1,
            'context' => $context
        ];
    }

    /**
     * Returns an array of filemanager options
     *
     * @return array
     */
    public static function get_filemanager_options() {
        return [
            'subdirs' => 1,
            'accepted_types' => '*'
        ];
    }

    /**
     * Updates a given course's settings to match the given params
     *
     * @param  object  $course
     * @param  array $params
     * @return void
     */
    public static function update_course_config($course, $params = []) {
        global $DB;

        // First, clear out old settings.
        self::delete_course_config($course);

        $courseconfigurablefields = self::$courseconfigurablefields;

        // Get rid of non-course-configurable fields.
        $params = \block_quickmail_plugin::array_filter_key($params, function ($key) use ($courseconfigurablefields) {
            return in_array($key, $courseconfigurablefields);
        });

        // Handle conversion of special cases….
        if (array_key_exists('roleselection', $params)) {
            if (is_array($params['roleselection'])) {
                // Convert array to comma-delimited string for single field storage.
                $params['roleselection'] = implode(',', $params['roleselection']);
            }
        }

        // Next, iterate over each given param, inserting each record for this course.
        foreach ($params as $name => $value) {
            $config = new \stdClass;
            $config->coursesid = $course->id;
            $config->name = $name;
            $config->value = $value;

            $DB->insert_record('block_quickmail_config', $config);
        }
    }

    /**
     * Deletes a given course's settings
     *
     * @param  object  $course
     * @return void
     */
    public static function delete_course_config($course) {
        global $DB;

        $DB->delete_records('block_quickmail_config', ['coursesid' => $course->id]);
    }

    /**
     * Reports whether or the given course is configured to have FERPA restrictions or not
     *
     * FERPA restrictions = if true, any user that cannot access all groups in the course
     * will have limited results when pulling groups or users. These results are limited
     * to whichever groups the user is in, or the users within those groups.
     *
     * @param  object  $course
     * @return bool
     */
    public static function be_ferpa_strict_for_course($course) {
        // Get this block's ferpa setting.
        $setting = self::block('ferpa', false);

        // If strict, be strict.
        if ($setting == 'strictferpa') {
            return true;
        }

        // If deferred to course, return what is configured by the course.
        if ($setting == 'courseferpa') {
            return (bool) $course->groupmode;
        }

        // Otherwise, do not be strict.
        return false;
    }

}
