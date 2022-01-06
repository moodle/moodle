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
 * Profile manager class
 *
 * @package    tool_moodlenet
 * @copyright  2020 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_moodlenet;

/**
 * Class for handling interaction with the moodlenet profile.
 *
 * @package    tool_moodlenet
 * @copyright  2020 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class profile_manager {

    /**
     * Get the mnet profile for a user.
     *
     * @param  int $userid The ID for the user to get the profile form
     * @return moodlenet_user_profile or null.
     */
    public static function get_moodlenet_user_profile(int $userid): ?moodlenet_user_profile {
        global $CFG;
        // Check for official profile.
        if (self::official_profile_exists()) {
            $user = \core_user::get_user($userid, 'moodlenetprofile');
            try {
                $userprofile = $user->moodlenetprofile ? $user->moodlenetprofile : '';
                return (isset($user)) ? new moodlenet_user_profile(s($userprofile), $userid) : null;
            } catch (\moodle_exception $e) {
                // If an exception is thrown, means there isn't a valid profile set. No need to log exception.
                return null;
            }
        }
        // Otherwise get hacked in user profile field.
        require_once($CFG->dirroot . '/user/profile/lib.php');
        $profilefields = profile_get_user_fields_with_data($userid);
        foreach ($profilefields as $key => $field) {
            if ($field->get_category_name() == self::get_category_name()
                    && $field->inputname == 'profile_field_mnetprofile') {
                try {
                    return new moodlenet_user_profile(s($field->display_data()), $userid);
                } catch (\moodle_exception $e) {
                    // If an exception is thrown, means there isn't a valid profile set. No need to log exception.
                    return null;
                }
            }
        }
        return null;
    }

    /**
     * Save the moodlenet profile.
     *
     * @param moodlenet_user_profile $moodlenetprofile The moodlenet profile to save.
     */
    public static function save_moodlenet_user_profile(moodlenet_user_profile $moodlenetprofile): void {
        global $CFG, $DB;
        // Do some cursory checks first to see if saving is possible.
        if (self::official_profile_exists()) {
            // All good. Let's save.
            $user = \core_user::get_user($moodlenetprofile->get_userid());
            $user->moodlenetprofile = $moodlenetprofile->get_profile_name();

            require_once($CFG->dirroot . '/user/lib.php');

            \user_update_user($user, false, true);
            return;
        }
        $fielddata = self::get_user_profile_field();
        $fielddata = self::validate_and_fix_missing_profile_items($fielddata);
        // Everything should be back to normal. Let's save.
        require_once($CFG->dirroot . '/user/profile/lib.php');
        \profile_save_custom_fields($moodlenetprofile->get_userid(),
                [$fielddata->shortname => $moodlenetprofile->get_profile_name()]);
    }

    /**
     * Checks to see if the required user profile fields and categories are in place. If not it regenerates them.
     *
     * @param  stdClass $fielddata The moodlenet profile field.
     * @return stdClass The same moodlenet profile field, with any necessary updates made.
     */
    private static function validate_and_fix_missing_profile_items(\stdClass $fielddata): \stdClass {
        global $DB;

        if (empty((array) $fielddata)) {
            // We need to regenerate the category and field to store this data.
            if (!self::check_profile_category()) {
                $categoryid = self::create_user_profile_category();
                self::create_user_profile_text_field($categoryid);
            } else {
                // We need the category id.
                $category = $DB->get_record('user_info_category', ['name' => self::get_category_name()]);
                self::create_user_profile_text_field($category->id);
            }
            $fielddata = self::get_user_profile_field();
        } else {
            if (!self::check_profile_category($fielddata->categoryid)) {
                $categoryid = self::create_user_profile_category();
                // Update the field to put it back into this category.
                $fielddata->categoryid = $categoryid;
                $DB->update_record('user_info_field', $fielddata);
            }
        }
        return $fielddata;
    }

    /**
     * Returns the user profile field table object.
     *
     * @return stdClass the moodlenet profile table object. False if no record found.
     */
    private static function get_user_profile_field(): \stdClass {
        global $DB;
        $fieldname = self::get_profile_field_name();
        $record = $DB->get_record('user_info_field', ['shortname' => $fieldname]);
        return ($record) ? $record : (object) [];
    }

    /**
     * This reports back if the category has been deleted or the config value is different.
     *
     * @param  int $categoryid The category id to check against.
     * @return bool True is the category checks out, otherwise false.
     */
    private static function check_profile_category(int $categoryid = null): bool {
        global $DB;
        $categoryname = self::get_category_name();
        $categorydata = $DB->get_record('user_info_category', ['name' => $categoryname]);
        if (empty($categorydata)) {
            return false;
        }
        if (isset($categoryid) && $categorydata->id != $categoryid) {
            return false;
        }
        return true;
    }

    /**
     * Are we using the proper user profile field to hold the mnet profile?
     *
     * @return bool True if we are using a user table field for the mnet profile. False means we are using costom profile fields.
     */
    public static function official_profile_exists(): bool {
        global $DB;

        $usertablecolumns = $DB->get_columns('user', false);
        if (isset($usertablecolumns['moodlenetprofile'])) {
            return true;
        }
        return false;
    }

    /**
     * Gets the category name that is set for this site.
     *
     * @return string The category used to hold the moodle net profile field.
     */
    public static function get_category_name(): string {
        return get_config('tool_moodlenet', 'profile_category');
    }

    /**
     * Sets the a unique category to hold the moodle net user profile.
     *
     * @param string $categoryname The base category name to use.
     * @return string The actual name of the category to use.
     */
    private static function set_category_name(string $categoryname): string {
        global $DB;

        $attemptname = $categoryname;

        // Check if this category already exists.
        $foundcategoryname = false;
        $i = 0;
        do {
            $category = $DB->count_records('user_info_category', ['name' => $attemptname]);
            if ($category > 0) {
                $i++;
                $attemptname = $categoryname . $i;
            } else {
                set_config('profile_category', $attemptname, 'tool_moodlenet');
                $foundcategoryname = true;
            }
        } while (!$foundcategoryname);
        return $attemptname;
    }

    /**
     * Create a custom user profile category to hold our custom field.
     *
     * @return int The id of the created category.
     */
    public static function create_user_profile_category(): int {
        global $DB;
        // No nice API to do this, so direct DB calls it is.
        $data = new \stdClass();
        $data->sortorder = $DB->count_records('user_info_category') + 1;
        $data->name = self::set_category_name(get_string('pluginname', 'tool_moodlenet'));
        $data->id = $DB->insert_record('user_info_category', $data, true);

        $createdcategory = $DB->get_record('user_info_category', array('id' => $data->id));
        \core\event\user_info_category_created::create_from_category($createdcategory)->trigger();
        return $createdcategory->id;
    }

    /**
     * Sets a unique name to be used for the moodle net profile.
     *
     * @param string $fieldname The base fieldname to use.
     * @return string The actual profile field name.
     */
    private static function set_profile_field_name(string $fieldname): string {
        global $DB;

        $attemptname = $fieldname;

        // Check if this profilefield already exists.
        $foundfieldname = false;
        $i = 0;
        do {
            $profilefield = $DB->count_records('user_info_field', ['shortname' => $attemptname]);
            if ($profilefield > 0) {
                $i++;
                $attemptname = $fieldname . $i;
            } else {
                set_config('profile_field_name', $attemptname, 'tool_moodlenet');
                $foundfieldname = true;
            }
        } while (!$foundfieldname);
        return $attemptname;
    }

    /**
     * Gets the unique profile field used to hold the moodle net profile.
     *
     * @return string The profile field name being used on this site.
     */
    public static function get_profile_field_name(): string {
        return get_config('tool_moodlenet', 'profile_field_name');
    }


    /**
     * Create a user profile field to hold the moodlenet profile information.
     *
     * @param  int $categoryid The category to put this field into.
     */
    public static function create_user_profile_text_field(int $categoryid): void {
        global $CFG;

        require_once($CFG->dirroot . '/user/profile/definelib.php');
        require_once($CFG->dirroot . '/user/profile/field/text/define.class.php');

        // Add our moodlenet profile field.
        $profileclass = new \profile_define_text();
        $data = (object) [
            'shortname' => self::set_profile_field_name('mnetprofile'),
            'name' => get_string('mnetprofile', 'tool_moodlenet'),
            'datatype' => 'text',
            'description' => get_string('mnetprofiledesc', 'tool_moodlenet'),
            'descriptionformat' => 1,
            'categoryid' => $categoryid,
            'signup' => 1,
            'forceunique' => 1,
            'visible' => 2,
            'param1' => 30,
            'param2' => 2048
        ];
        $profileclass->define_save($data);
    }

    /**
     * Given our $moodlenetprofile let's cURL the domains' WebFinger endpoint
     *
     * @param moodlenet_user_profile $moodlenetprofile The moodlenet profile to get info from.
     * @return array [bool, text, raw]
     */
    public static function get_moodlenet_profile_link(moodlenet_user_profile $moodlenetprofile): array {
        $domain = $moodlenetprofile->get_domain();
        $username = $moodlenetprofile->get_username();

        // Assumption: All MoodleNet instance's will contain a WebFinger validation script.
        $url = "https://".$domain."/.well-known/webfinger?resource=acct:".$username."@".$domain;

        $curl = new \curl();
        $options = [
            'CURLOPT_HEADER' => 0,
        ];
        $content = $curl->get($url, null, $options);
        $info = $curl->get_info();

        // The base cURL seems fine, let's press on.
        if (!$curl->get_errno() && !$curl->error) {
            // WebFinger gave us a 404 back so the user has no droids here.
            if ($info['http_code'] >= 400) {
                if ($info['http_code'] === 404) {
                    // User not found.
                    return [
                        'result' => false,
                        'message' => get_string('profilevalidationfail', 'tool_moodlenet'),
                    ];
                } else {
                    // There was some other error that was not a missing account.
                    return [
                        'result' => false,
                        'message' => get_string('profilevalidationerror', 'tool_moodlenet'),
                    ];
                }
            }

            // We must have a valid link so give it back to the user.
            $data = json_decode($content);
            return [
                'result' => true,
                'message' => get_string('profilevalidationpass', 'tool_moodlenet'),
                'domain' => $data->aliases[0]
            ];
        } else {
            // There was some failure in curl so report it back.
            return [
                'result' => false,
                'message' => get_string('profilevalidationerror', 'tool_moodlenet'),
            ];
        }
    }
}
