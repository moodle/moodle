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
 * Edwiser Bridge - WordPress and Moodle integration.
 * Observer file used as the callback for all the events.
 *
 * @package     local_edwiserbridge
 * @copyright   2021 WisdmLabs (https://wisdmlabs.com/) <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author      Wisdmlabs
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/local/edwiserbridge/lib.php');
require_once($CFG->dirroot . '/user/lib.php');

/**
 * Handles callbacks for all in built Moodle events.
 *
 * @package     local_edwiserbridge
 * @copyright   2021 WisdmLabs (https://wisdmlabs.com/) <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_edwiserbridge_observer {

    /**
     * Functionality to handle user enrollment event.
     *
     * @param core\event\user_enrolment_created $event event.
     */
    public static function user_enrolment_created(core\event\user_enrolment_created $event) {
        global $CFG;
        $userdata = user_get_users_by_id(array($event->relateduserid));

        $requestdata = array(
            'action'     => 'course_enrollment',
            'user_id'    => $event->relateduserid,
            'course_id'  => $event->courseid,
            'user_name'  => $userdata[$event->relateduserid]->username,
            'first_name' => $userdata[$event->relateduserid]->firstname,
            'last_name'  => $userdata[$event->relateduserid]->lastname,
            'email'      => $userdata[$event->relateduserid]->email
        );

        if (check_if_request_is_from_wp()) {
            return;
        }

        $apihandler = api_handler_instance();
        if (isset($CFG->eb_connection_settings)) {
            $sites = unserialize($CFG->eb_connection_settings);
            $synchconditions = unserialize($CFG->eb_synch_settings);
            foreach ($sites as $value) {
                if ($synchconditions[$value['wp_name']]['course_enrollment'] && $value['wp_token']) {
                    // Adding Token for verification in WP from Moodle.
                    $requestdata['secret_key'] = $value['wp_token'];

                    $apihandler->connect_to_wp_with_args($value["wp_url"], $requestdata);
                }
            }
        }
    }

    /**
     * Functionality to handle user un enrollment event.
     *
     * @param core\event\user_enrolment_deleted $event event.
     */
    public static function user_enrolment_deleted(core\event\user_enrolment_deleted $event) {
        global $CFG;
        $userdata = user_get_users_by_id(array($event->relateduserid));
        $requestdata = array(
            'action'     => 'course_un_enrollment',
            'user_id'    => $event->relateduserid,
            'course_id'  => $event->courseid,
            'user_name'  => $userdata[$event->relateduserid]->username,
            'first_name' => $userdata[$event->relateduserid]->firstname,
            'last_name'  => $userdata[$event->relateduserid]->lastname,
            'email'      => $userdata[$event->relateduserid]->email
        );

        // Checks if the request is from the wordpress site or from te Moodle site itself.
        if (check_if_request_is_from_wp()) {
            return;
        }

        $apihandler = api_handler_instance();
        if (isset($CFG->eb_connection_settings)) {
            $sites = unserialize($CFG->eb_connection_settings);
            $synchconditions = unserialize($CFG->eb_synch_settings);

            foreach ($sites as $value) {
                if ($synchconditions[$value['wp_name']]['course_un_enrollment'] && $value['wp_token']) {
                    // Adding Token for verification in WP from Moodle.
                    $requestdata['secret_key'] = $value['wp_token'];
                    $apihandler->connect_to_wp_with_args($value["wp_url"], $requestdata);
                }
            }
        }
    }

    /**
     * Functionality to handle user creation event.
     *
     * @param core\event\user_created $event event.
     */
    public static function user_created(core\event\user_created $event) {

        global $CFG;
        $userdata = user_get_users_by_id(array($event->relateduserid));

        // User password should be encrypted. Using Openssl for it.
        // We will use token as the key as it is present on both sites.
        // Open SSL encryption initialization.
        $encmethod = 'AES-128-CTR';

        $apihandler = api_handler_instance();
        if (isset($CFG->eb_connection_settings)) {
            $sites = unserialize($CFG->eb_connection_settings);
            $synchconditions = unserialize($CFG->eb_synch_settings);

            foreach ($sites as $value) {
                if ($synchconditions[$value["wp_name"]]["user_creation"] && $value['wp_token']) {
                    $password    = '';
                    $enciv       = '';
                    $newpassword = optional_param('newpassword', '', PARAM_TEXT);

                    // If new password in not empty.
                    if ($newpassword && !empty($newpassword)) {
                        // if (isset($_POST['newpassword']) && $_POST['newpassword']) {
                        $enckey   = openssl_digest($value["wp_token"], 'SHA256', true);
                        $enciv    = substr(hash('sha256', $value["wp_token"]), 0, 16);
                        $password = openssl_encrypt($newpassword, $encmethod, $enckey, 0, $enciv);
                    }

                    require_once("$CFG->dirroot/user/profile/lib.php");

                    $requestdata = array(
                        'action' => 'user_creation',
                        'user_id'     => $event->relateduserid,
                        'user_name'   => $userdata[$event->relateduserid]->username,
                        'first_name'  => $userdata[$event->relateduserid]->firstname,
                        'last_name'   => $userdata[$event->relateduserid]->lastname,
                        'email'       => $userdata[$event->relateduserid]->email,
                        'password'    => $password,
                        'enc_iv'      => $enciv,
                        'secret_key' => $value['wp_token'], // Adding Token for verification in WP from Moodle.
                        'custom_fields' => json_encode(profile_user_record($event->relateduserid, false)), // adding custom fields data.
                    );

                    $apihandler->connect_to_wp_with_args($value["wp_url"], $requestdata);
                }
            }
        }
    }

    /**
     * Functionality to handle user update event.
     *
     * @param core\event\user_updated $event event.
     */
    public static function user_updated(core\event\user_updated $event) {
        global $CFG;
        $userdata = user_get_users_by_id(array($event->relateduserid));

        // User password should be encrypted. Using Openssl for it.
        // We will use token as the key as it is present on both sites.
        // Open SSL encryption initialization.
        $encmethod = 'AES-128-CTR';

        $apihandler = api_handler_instance();
        if (isset($CFG->eb_connection_settings)) {

            $sites = unserialize($CFG->eb_connection_settings);
            $synchconditions = unserialize($CFG->eb_synch_settings);

            foreach ($sites as $value) {
                if (
                    isset($synchconditions[$value["wp_name"]]["user_updation"]) &&
                    $synchconditions[$value["wp_name"]]["user_updation"] &&
                    $value['wp_token']
                ) {
                    $password    = '';
                    $enciv       = '';
                    $newpassword = optional_param('newpassword', '', PARAM_TEXT);

                    // If new password in not empty.
                    if ($newpassword && !empty($newpassword)) {
                        // if (isset($_POST['newpassword']) && $_POST['newpassword']) {
                        $enckey   = openssl_digest($value["wp_token"], 'SHA256', true);
                        $enciv = substr(hash('sha256', $value["wp_token"]), 0, 16);
                        $password = openssl_encrypt($newpassword, $encmethod, $enckey, 0, $enciv);
                    }

                    require_once("$CFG->dirroot/user/profile/lib.php");

                    $requestdata = array(
                        'action'        => 'user_updated',
                        'user_id'       => $event->relateduserid,
                        'first_name'    => $userdata[$event->relateduserid]->firstname,
                        'last_name'     => $userdata[$event->relateduserid]->lastname,
                        'email'         => $userdata[$event->relateduserid]->email,
                        'country'       => $userdata[$event->relateduserid]->country,
                        'city'          => $userdata[$event->relateduserid]->city,
                        'phone'         => $userdata[$event->relateduserid]->phone1,
                        'password'      => $password,
                        'enc_iv'        => $enciv,
                        'secret_key'    => $value['wp_token'], // Adding Token for verification in WP from Moodle.
                        'custom_fields' => json_encode(profile_user_record($event->relateduserid, false)), // adding custom fields data.
                    );

                    $apihandler->connect_to_wp_with_args($value["wp_url"], $requestdata);
                }
            }
        }
    }

    /**
     * Functionality to handle user update event.
     *
     * @param core\event\user_password_updated $event event.
     */
    public static function user_password_updated(core\event\user_password_updated $event) {
        global $CFG;

        $user_id = $event->userid;
        if($event->relateduserid) {
            $user_id = $event->relateduserid;
        }

        $userdata = user_get_users_by_id(array($user_id));

        // User password should be encrypted. Using Openssl for it.
        // We will use token as the key as it is present on both sites.
        // Open SSL encryption initialization.
        $encmethod = 'AES-128-CTR';
        $apihandler  = api_handler_instance();
        if (isset($CFG->eb_connection_settings)) {

            $sites = unserialize($CFG->eb_connection_settings);
            $synchconditions = unserialize($CFG->eb_synch_settings);

            foreach ($sites as $value) {
                if (
                    isset($synchconditions[$value["wp_name"]]["user_updation"]) &&
                    $synchconditions[$value["wp_name"]]["user_updation"] &&
                    $value['wp_token']
                ) {

                    $password    = '';
                    $enciv       = '';
                    $newpassword = optional_param('newpassword1', '', PARAM_TEXT);
                    if(empty($newpassword)) {
                        $newpassword = optional_param('password', '', PARAM_TEXT);
                    }

                    // If new password in not empty.
                    if ($newpassword && !empty($newpassword)) {
                        // if (isset($_POST['newpassword1']) && $_POST['newpassword1']) {
                        $enckey   = openssl_digest($value["wp_token"], 'SHA256', true);
                        $enciv    = substr(hash('sha256', $value["wp_token"]), 0, 16);
                        $password = openssl_encrypt($newpassword, $encmethod, $enckey, 0, $enciv);
                    }

                    $requestdata = array(
                        'action'     => 'user_updated',
                        'user_id'    => $user_id,
                        'email'      => $userdata[$user_id]->email,
                        'password'   => $password,
                        'enc_iv'     => $enciv,
                        'secret_key' => $value['wp_token'], // Adding Token for verification in WP from Moodle.
                    );

                    $apihandler->connect_to_wp_with_args($value["wp_url"], $requestdata);
                }
            }
        }
    }

    /**
     * Functionality to handle user deletion event.
     *
     * @param core\event\user_deleted $event event.
     */
    public static function user_deleted(core\event\user_deleted $event) {
        global $CFG;
        $requestdata = array(
            'action'  => 'user_deletion',
            'user_id' => $event->relateduserid
        );

        $apihandler = api_handler_instance();
        if (isset($CFG->eb_connection_settings)) {
            $sites = unserialize($CFG->eb_connection_settings);
            $synchconditions = unserialize($CFG->eb_synch_settings);

            foreach ($sites as $value) {
                if (isset( $synchconditions[$value["wp_name"]]["user_deletion"] ) && $synchconditions[$value["wp_name"]]["user_deletion"] && $value['wp_token']) {
                    // Adding Token for verification in WP from Moodle.
                    $requestdata['secret_key'] = $value['wp_token'];

                    $apihandler->connect_to_wp_with_args($value["wp_url"], $requestdata);
                }
            }
        }
    }

    /**
     * Functionality to handle Course deletion event.
     *
     * @param core\event\course_created $event event.
     */
    public static function course_created(core\event\course_created $event) {
        global $CFG;
        // Get course info.
        $course = get_course($event->courseid);

        $apihandler = api_handler_instance();
        if (isset($CFG->eb_connection_settings)) {
            $sites = unserialize($CFG->eb_connection_settings);
            $synchconditions = unserialize($CFG->eb_synch_settings);

            foreach ($sites as $value) {
                if (
                    isset($synchconditions[$value["wp_name"]]["course_creation"]) &&
                    $synchconditions[$value["wp_name"]]["course_creation"] &&
                    $value['wp_token']
                ) {
                    $requestdata = array(
                        'action'      => 'course_created',
                        'course_id'   => $event->courseid,
                        'fullname'    => $course->fullname,
                        'summary'     => $course->summary,
                        'cat'         => $course->category,
                        'secret_key'  => $value['wp_token'], // Adding Token for verification in WP from Moodle.
                    );

                    $apihandler->connect_to_wp_with_args($value["wp_url"], $requestdata);
                }
            }
        }
    }

    /**
     * Functionality to handle Course deletion event.
     *
     * @param core\event\course_deleted $event event.
     */
    public static function course_deleted(core\event\course_deleted $event) {
        global $CFG;

        $requestdata = array(
            'action'    => 'course_deleted',
            'course_id' => $event->objectid,
        );

        $apihandler = api_handler_instance();
        if (isset($CFG->eb_connection_settings)) {
            $sites = unserialize($CFG->eb_connection_settings);
            $synchconditions = unserialize($CFG->eb_synch_settings);

            foreach ($sites as $value) {
                if (
                    isset($synchconditions[$value["wp_name"]]["course_deletion"]) &&
                    $synchconditions[$value["wp_name"]]["course_deletion"] &&
                    $value['wp_token']
                ) {
                    // Adding Token for verification in WP from Moodle.
                    $requestdata['secret_key'] = $value['wp_token'];

                    $apihandler->connect_to_wp_with_args($value["wp_url"], $requestdata);
                }
            }
        }
    }
}
