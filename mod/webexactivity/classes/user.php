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

use \mod_webexactivity\local\exception;
use \mod_webexactivity\local\type\base\xml_gen;

defined('MOODLE_INTERNAL') || die();

/**
 * A class that represents a WebEx user.
 *
 * @package    mod_webexactvity
 * @author     Eric Merrill <merrill@oakland.edu>
 * @copyright  2014 Oakland University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user {
    // TODO Cleaner handling of user changes to webex. Support for userid changes.
    /** @var stdClass The DB record that represents this user. */
    protected $user = null;

    /** @var string A string to hold scheduling permission temporarily. */
    protected $_schedulingpermission = null;

    /** @var bool Is this an admin user. */
    private $_isadmin = false;

    /**
     * Builds the user object.
     *
     * @param stdClass|int|string  $user Object of user record, id of record to load.
     * @throws coding_exception when bad parameter received.
     */
    private function __construct($user = null) {
        if (is_null($user)) {
            $this->user = new \stdClass();
        } else if (is_object($user)) {
            $this->user = $user;
        }

        if ($this->user) {
            return;
        }

        throw new \coding_exception('Unexpected parameter type passed to user constructor.');
    }

    // ---------------------------------------------------
    // Static Factories.
    // ---------------------------------------------------
    /**
     * Load the webex user for a given moodle user.
     *
     * @param stdClass|int   $muser Object of user record, id of record to load.
     * @param bool           $create If true, try to create user if it doesn't exist.
     * @return user|bool     The user object, false on failure.
     * @throws coding_exception when Moodle user not found, or unknown parameter type.
     */
    public static function load_for_user($muser, $create = true) {
        global $DB;

        if (is_object($muser)) {
            $moodleuser = $muser;
        } else if (is_numeric($muser)) {
            $moodleuser = $DB->get_record('user', array('id' => $muser));
            if (!$moodleuser) {
                throw new \coding_exception('Moodle user not found.');
            }
        } else {
            throw new \coding_exception('Unexpected paramater passed to load_for_user.');
        }

        $record = $DB->get_record('webexactivity_user', array('moodleuserid' => $moodleuser->id));

        if ($record) {
            // Record found, just load it up.
            $webexuser = new user($record);
        } else {
            if (!$create) {
                return false;
            }
            // Creating a new user.
            $webexuser = self::create();

            $prefix = get_config('webexactivity', 'prefix');
            $webexuser->firstname = $moodleuser->firstname;
            $webexuser->lastname = $moodleuser->lastname;
            $webexuser->webexid = $prefix.$moodleuser->username;
            $webexuser->email = $moodleuser->email;
            $webexuser->password = webex::generate_password();
            $webexuser->moodleuserid = $moodleuser->id;

            $status = $webexuser->save_to_webex();
            if (!$status) {
                // Some error creating the user.
                return false;
            }
            // We needed to send a password for creation, but we don't really want it.
            $webexuser->password = null;
            $webexuser->save_to_db();
        }

        return $webexuser;
    }

    /**
     * Load the webex user for a given webExId or webex userId.
     *
     * @param string|int   $webexid WebEx username (webExId) or user number (userId).
     * @return user|bool  The user object, false on failure.
     * @throws coding_exception for unknown parameter type.
     */
    public static function load_webex_id($webexid) {
        global $DB;

        if (strcasecmp(get_config('webexactivity', 'apiusername'), $webexid) === 0) {
            return self::load_admin_user();
        }

        if (is_numeric($webexid)) {
            $params = array('webexuserid' => $webexid);
        } else if (is_string($webexid)) {
            $params = array('webexid' => $webexid);
        } else {
            throw new \coding_exception('Unexpected paramater passed to load_webex_id.');
        }

        $record = $DB->get_record('webexactivity_user', $params);
        if ($record) {
            // User found - load and return.
            return new user($record);
        } else {
            // Not ours.
            return false;
            // TODO - expand to load from WebEx if it makes sense.
        }
    }

    /**
     * Load the webex user for a given moodle user.
     *
     * @param stdClass|int   $webexid WebEx record or id.
     * @return user|bool     The user object, false on failure.
     * @throws coding_exception for unknown parameter type.
     */
    public static function load_record($rec) {
        global $DB;

        if (is_object($rec)) {
            $record = $rec;
        } else if (is_numeric($rec)) {
            $record = $DB->get_record('webexactivity_user', array('id' => $rec));
            if (!$record) {
                throw new \coding_exception('WebEx user not found.');
            }
        } else {
            throw new \coding_exception('Unexpected paramater passed to load_record.');
        }

        return new user($record);
    }

    /**
     * Load the webex user for a given moodle user.
     *
     * @return user|bool     The user object, false on failure.
     */
    public static function load_admin_user() {
        $record = new \stdClass();
        $record->webexid = get_config('webexactivity', 'apiusername');
        $record->password = self::encrypt_password(get_config('webexactivity', 'apipassword'));
        $record->manual = 1;

        return new user_admin($record);
    }

    /**
     * Create a new WebEx user.
     *
     * @return user  The user object, false on failure.
     */
    public static function create() {
        return new user();
    }

    // ---------------------------------------------------
    // User Methods.
    // ---------------------------------------------------
    /**
     * Set the password for the user.
     *
     * @param string   $password The plaintext password to set.
     * @return bool    True on success, false on failure.
     */
    public function update_password($password) {
        if ($this->manual) {
            return false;
        }

        $this->user->password = self::encrypt_password($password);

        $webex = new webex();

        $xml = xml_gen::update_user_password($this);

        $response = $webex->get_response($xml);

        if ($response !== false) {
            $this->save_to_db();
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get a login URL for the user.
     *
     * @param string   $backurl The URL to go to on failure.
     * @param string   $fronturl The URL to go to on success.
     * @return string|bool    The url, false on failure.
     */
    public function get_login_url($backurl = false, $forwardurl = false) {
        $xml = xml_gen::get_user_login_url($this->webexid);

        $webex = new \mod_webexactivity\webex();

        if (!($response = $webex->get_response($xml, $this))) {
            return false;
        }

        $returnurl = $response['use:userLoginURL']['0']['#'];

        $returnurl = str_replace('+', '%2B', $returnurl);

        if ($backurl) {
            $encoded = urlencode($backurl);
            $returnurl = str_replace('&BU=', '&BU='.$encoded, $returnurl);
        }

        if ($forwardurl) {
            $encoded = urlencode($forwardurl);
            $returnurl = str_replace('&MU=GoBack', '&MU='.$encoded, $returnurl);
        }

        return $returnurl;
    }

    /**
     * Get a logout URL for the user.
     *
     * @param string   $backurl The URL to go to on failure or success.
     * @return string    The url.
     */
    public static function get_logout_url($backurl = false) {
        $url = webex::get_base_url();

        $url .= '/p.php?AT=LO';
        if ($backurl) {
            $encoded = urlencode($backurl);
            $url .= '&BU='.$encoded;
        }

        return $url;
    }

    /**
     * Check if the auth credentials of the WebEx user are good.
     *
     * @return bool    True if auth succeeded, false if failed.
     */
    public function check_user_auth() {
        if (!isset($this->password)) {
            return false;
        }
        $xml = xml_gen::check_user_auth($this);

        $webex = new \mod_webexactivity\webex();

        try {
            $response = $webex->get_response($xml, $this);
        } catch (exception\bad_password $e) {
            return false;
        }

        if ($response) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Update the schedulingPermission to let the admin user schedule meetings for the user.
     *
     * @return bool    True if auth succeeded, false if failed.
     */
    public function set_scheduling_permission() {
        $info = self::get_webex_info($this->webexid);

        $adminusername = get_config('webexactivity', 'apiusername');

        if (isset($info['use:schedulingPermission']['0']['#'])) {
            $perm = $info['use:schedulingPermission']['0']['#'];

            $ids = explode(';', $perm);

            foreach ($ids as $id) {
                if (strcasecmp($adminusername, $id) === 0) {
                    // The user already has the permission set, don't set again.
                    return true;
                }
            }

            $ids[] = $adminusername;
            $perm = implode(';', $ids);
        } else {
            $perm = $adminusername;
        }

        $this->schedulingpermission = $perm;

        return $this->save_to_webex();
    }

    /**
     * Encrypt the password for storage.
     *
     * @param string     $password The plain text password.
     * @return string    The encrypted password.
     */
    public static function encrypt_password($password) {
        // BOOOOOO Weak!!
        return base64_encode($password);
    }

    /**
     * Decrypt the password for use.
     *
     * @param string     $encrypted The encrypted password.
     * @return string    The plain text password.
     */
    public static function decrypt_password($encrypted) {
        // BOOOOOO Weak!!
        return base64_decode($encrypted);
    }

    // ---------------------------------------------------
    // Support Methods.
    // ---------------------------------------------------
    /**
     * Save this user to the database.
     *
     * @return bool    True if auth succeeded, false if failed.
     */
    public function save_to_db() {
        global $DB;

        $this->user->timemodified = time();

        if (isset($this->user->id)) {
            if ($DB->update_record('webexactivity_user', $this->user)) {
                return true;
            }
            return false;
        } else {
            if ($id = $DB->insert_record('webexactivity_user', $this->user)) {
                $this->user->id = $id;
                return true;
            }
            return false;
        }
    }

    /**
     * Save this user to WebEx.
     *
     * @return bool    True if auth succeeded, false if failed.
     * @throws invalid_response_exception for unexpected WebEx response.
     * @throws coding_exception.
     */
    public function save_to_webex() {
        $webex = new webex();

        if (isset($this->webexuserid)) {
            // The user has already been saved to WebEx, update.
            $xml = xml_gen::update_user($this);

            $response = $webex->get_response($xml);

            if ($response !== false) {
                return true;
            } else {
                return false;
            }
        } else {
            // Creating a new user.
            $this->schedulingpermission = get_config('webexactivity', 'apiusername');

            $xml = xml_gen::create_user($this);
            try {
                $response = $webex->get_response($xml);
            } catch (exception\webex_xml_exception $e) {
                $response = false;
            } catch (exception\webex_user_collision $e) {
                // Expection for username or email already exists.
                if ($this->update_from_webex()) {
                    return true;
                }

                // Can't use this user.
                throw $e;
            }

            if ($response) {
                if (isset($response['use:userId']['0']['#'])) {
                    $this->webexuserid = $response['use:userId']['0']['#'];
                    return true;
                } else {
                    throw new \invalid_response_exception('Unexpected WebEx response when creating user');
                }
            } else {
                $errors = $webex->get_latest_errors();

                // Failure creating user.
                if (!isset($errors['exception'])) {
                    throw new \invalid_response_exception('No exception found when creating users. Exception code expected.');
                }

                $exception = $errors['exception'];
                $message = 'WebEx exception '.$exception.' when creating new user.';
                if (isset($errors['message'])) {
                    $message .= ' Reason given: "' . $message . '"';
                }

                $message .= "\n".get_string('user_create_exception', 'mod_webexactivity');

                throw new \coding_exception($message);
            }

            throw new \coding_exception('Unknown error when creating new user.');
        }
    }

    /**
     * Load user info from WebEx.
     *
     * @return bool    True if auth succeeded, false if failed.
     */
    public function update_from_webex() {
        $info = false;

        if (isset($this->email)) {
            $info = self::search_webex_for_email($this->email);
        }

        if (!$info && isset($this->webexid)) {
            // WebEx email lookup failed, try WebEx ID.
            $info = self::search_webex_for_webexid($this->webexid);
        }

        if (!$info) {
            // Info not found.
            return false;
        }

        if (!isset($info->webexid)) {
            // No webExId found.
            return false;
        }

        $prefix = get_config('webexactivity', 'prefix');
        if (!empty($prefix) && strpos($info->webexid, $prefix) !== 0) {
            // Not the same username prefix.
            $this->manual = 1;
        }

        $this->webexid = $info->webexid;
        $this->webexuserid = $info->userid;

        if (isset($info->firstname)) {
            $this->firstname = $info->firstname;
        }

        if (isset($info->lastname)) {
            $this->lastname = $info->lastname;
        }

        if (isset($info->email)) {
            $this->email = $info->email;
        }

        return true;
    }

    // ---------------------------------------------------
    // WebEx Methods.
    // ---------------------------------------------------
    /**
     * Load user info from WebEx. Guaranteed to return webexid and userid if exists.
     *
     * @param string          $webexid WebEx ID (username) to search for.
     * @return stdClass|bool  object of user info, false if failed.
     */
    public static function search_webex_for_webexid($webexid) {
        $response = self::get_webex_info($webexid);

        if (!$response) {
            // Not found (or maybe another error).
            return false;
        }

        if (!isset($response['use:userId']['0']['#']) || !isset($response['use:webExId']['0']['#'])) {
            // Not found (or maybe another error).
            return false;
        }

        $user = new \stdClass();
        $user->userid = $response['use:userId']['0']['#'];
        $user->webexid = $response['use:webExId']['0']['#'];

        if (isset($response['use:firstName']['0']['#'])) {
            $user->firstname = $response['use:firstName']['0']['#'];
        }
        if (isset($response['use:lastName']['0']['#'])) {
            $user->lastname = $response['use:lastName']['0']['#'];
        }
        if (isset($response['use:email']['0']['#'])) {
            $user->email = $response['use:email']['0']['#'];
        }

        return $user;
    }

    /**
     * Load user info from WebEx based on email address.
     *
     * @param string          $email Email address to search for.
     * @return stdClass|bool  object of user info, false if failed.
     */
    public static function search_webex_for_email($email) {
        $webex = new webex();

        $xml = xml_gen::get_user_for_email($email);
        $response = $webex->get_response($xml);

        if (!$response) {
            // Not found (or maybe another error).
            return false;
        }

        if (!isset($response['use:user']['0']['#']['use:webExId']['0']['#'])) {
            // Not found.
            return false;
        }

        $webexid = $response['use:user']['0']['#']['use:webExId']['0']['#'];

        return self::search_webex_for_webexid($webexid);
    }

    /**
     * Load user info from WebEx.
     *
     * @param string       $webexid WebEx ID of the user to get info for.
     * @return array|bool  array of user info, false if failed.
     */
    public static function get_webex_info($webexid) {
        $webex = new webex();

        $xml = xml_gen::get_user_info($webexid);
        $response = $webex->get_response($xml);

        if (!$response) {
            // Not found (or maybe another error).
            return false;
        }

        return $response;
    }

    // ---------------------------------------------------
    // Magic Methods.
    // ---------------------------------------------------
    /**
     * Magic setter method for object.
     *
     * @param string    $name The name of the value to be set.
     * @param mixed     $val  The value to be set.
     */
    public function __set($name, $val) {
        switch ($name) {
            case 'password':
                if ($val) {
                    $this->user->password = self::encrypt_password($val);
                } else {
                    $this->user->password = null;
                }
                break;
            case 'schedulingpermission':
                $this->_schedulingpermission = $val;
                break;
            case 'isadmin':
                throw new \coding_exception('Can\'t change isadmin variable.');
                break;
            default:
                $this->user->$name = $val;
        }
    }

    /**
     * Magic getter method for object.
     *
     * @param string    $name The name of the value to be retrieved.
     */
    public function __get($name) {
        switch ($name) {
            case 'password':
                if (isset($this->user->password)) {
                    $pass = self::decrypt_password($this->user->password);
                    return $pass;
                } else {
                    return '';
                }
                break;
            case 'record':
                return $this->user;
                break;
            case 'schedulingpermission':
                return $this->_schedulingpermission;
                break;
            case 'isadmin':
                return $this->_isadmin;
                break;
        }

        return $this->user->$name;
    }

    /**
     * Magic isset method for object.
     *
     * @param string    $name The name of the value to be checked.
     */
    public function __isset($name) {
        switch ($name) {
            case 'schedulingpermission':
                return isset($this->_schedulingpermission);
                break;
            case 'isadmin':
                return true;
                break;
        }

        return isset($this->user->$name);
    }

    /**
     * Magic unset method for object.
     *
     * @param string    $name The name of the value to be unset.
     */
    public function __unset($name) {
        switch ($name) {
            case 'schedulingpermission':
                unset($this->_schedulingpermission);
                return;
                break;
            case 'isadmin':
                throw new \coding_exception('Can\'t change isadmin variable.');
                break;
        }

        unset($this->user->$name);
    }
}
