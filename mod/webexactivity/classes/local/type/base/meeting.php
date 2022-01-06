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

namespace mod_webexactivity\local\type\base;

use \mod_webexactivity\local\exception;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/calendar/lib.php');

/**
 * Class that represents and controls a meeting instance.
 *
 * This should be extended by classes that represent specific meeting types.
 *
 * @package    mod_webexactvity
 * @author     Eric Merrill <merrill@oakland.edu>
 * @copyright  2014 Oakland University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class meeting {
    /** @var stdClass Record object containing the information about the meeting. */
    protected $meetingrecord = null;

    /** @var array An array of expected keys. */
    protected $keys = array(
            'id' => null,
            'course' => 0,
            'name' => '',
            'intro' => null,
            'introformat' => 0,
            'creatorwebexid' => null,
            'hostwebexid' => null,
            'type' => null,
            'meetingkey' => null,
            'guestkey' => null, // Unused.
            'eventid' => null, // Unused.
            'hostkey' => null, // Unused?
            'password' => null,
            'meetinglink' => null,
            'starttime' => null,
            'endtime' => null,
            'duration' => null,
            'calpublish' => 1,
            'allchat' => 1, // Used for MC.
            'studentdownload' => 1,
            'laststatuscheck' => 0,
            'status' => \mod_webexactivity\webex::WEBEXACTIVITY_STATUS_NEVER_STARTED,
            'template' => null,
            'timemodified' => 0);

    /** @var webex A webex object to do network connections and other support services. */
    protected $webex;

    /** @var bool Track if there is a change that needs to go to WebEx. */
    protected $webexchange;

    public $cmid = null;

    /**
     * The XML generator class name to use. Can be redefined by child classes.
     */
    const GENERATOR = '\mod_webexactivity\local\type\base\xml_gen';

    /**
     * Prefix for retrieved XML fields.
     */
    const XML_PREFIX = '';

    /**
     * The default open time for this meeting type.
     */
    const OPEN_TIME = 20;

    /**
     * The meetings type code.
     */
    const TYPE_CODE = '';

    /**
     * The meetings type.
     */
    const TYPE = \mod_webexactivity\webex::WEBEXACTIVITY_TYPE_BASE;

    /**
     * Builds the meeting object.
     *
     * @param stdClass|int    $meeting Object of meeting record, or id of record to load.
     */
    public function __construct($meeting = false) {
        global $DB;

        $this->webex = new \mod_webexactivity\webex();

        if ($meeting === false) {
            $this->meetingrecord = new \stdClass();
            return;
        }

        if (is_numeric($meeting)) {
            $this->meetingrecord = $DB->get_record('webexactivity', array('id' => $meeting));
        } else if (is_object($meeting)) {
            $this->meetingrecord = $meeting;
        } else {
            debugging('meeting\base constructor passed unknown type.', DEBUG_DEVELOPER);
        }

        $this->load_webex_record($meeting);
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
            case 'starttime':
                // If the current time is not set, new meeting.
                if (!isset($this->starttime)) {
                    // If the time is past, or near past, set it to the near future.
                    if ($val < (time() + 60)) {
                        $val = time() + 60;
                    }
                } else if ($val < (time() + 60)) {
                    $curr = $this->starttime;
                    // If the current time is already set, and the time is past or near past.
                    if ($curr > time()) {
                        // If the current time is in the future, assume they want to start it now.
                        $val = time() + 60;
                    } else {
                        // If they are both in the past, leave it as the old setting. Can't change it.
                        $val = $curr;
                    }
                }
                break;
            case 'xml':
            case 'guestuserid':
                debugging('Meeting property "'.$name.'" removed.', DEBUG_DEVELOPER);
                return;
                break;
            case 'status':
                if (isset($this->status) && ($val != $this->status)) {
                    if ($val === \mod_webexactivity\webex::WEBEXACTIVITY_STATUS_IN_PROGRESS) {
                        $cm = get_coursemodule_from_instance('webexactivity', $this->id);
                        $context = \context_module::instance($cm->id);
                        $params = array(
                            'context' => $context,
                            'objectid' => $this->id
                        );
                        $event = \mod_webexactivity\event\meeting_started::create($params);
                        $event->add_record_snapshot('webexactivity', $this->meetingrecord);
                        $event->trigger();
                    } else if ($val === \mod_webexactivity\webex::WEBEXACTIVITY_STATUS_STOPPED) {
                        $cm = get_coursemodule_from_instance('webexactivity', $this->id);
                        $context = \context_module::instance($cm->id);
                        $params = array(
                            'context' => $context,
                            'objectid' => $this->id
                        );
                        $event = \mod_webexactivity\event\meeting_ended::create($params);
                        $event->add_record_snapshot('webexactivity', $this->meetingrecord);
                        $event->trigger();
                    }
                }
                break;
        }

        switch ($name) {
            case 'duration':
                // Need to change type to match type from db.
                $val = (string)$val;
            case 'starttime':
            case 'name':
            case 'intro':
            case 'password':
            case 'creatorwebexid':
            case 'hostwebexid':
                if ((!isset($this->$name) && !is_null($val)) || ($this->$name !== $val)) {
                    $this->webexchange = true;
                }
                break;
        }

        $this->meetingrecord->$name = $val;
        if (!array_key_exists($name, $this->keys)) {
            debugging('Unknown meeting value set "'.$name.'"', DEBUG_DEVELOPER);
        }
        return;
    }

    /**
     * Magic getter method for object.
     *
     * @param string    $name The name of the value to be retrieved.
     */
    public function __get($name) {
        if (!isset($this->meetingrecord->$name)) {
            return null;
        }

        return $this->meetingrecord->$name;
    }

    /**
     * Magic isset method for object.
     *
     * @param string    $name The name of the value to be checked.
     */
    public function __isset($name) {
        return isset($this->meetingrecord->$name);
    }

    /**
     * Magic unset method for object.
     *
     * @param string    $name The name of the value to be unset.
     */
    public function __unset($name) {
        unset($this->meetingrecord->$name);
    }

    // ---------------------------------------------------
    // Meeting Functions.
    // ---------------------------------------------------
    /**
     * Save this meeting object into WebEx.
     *
     * @return bool    True on success, false on failure.
     */
    public function save_to_webex() {
        $gen = static::GENERATOR;

        if (!isset($this->password) && static::is_password_required()) {
            $this->password = self::generate_password();
        }

        $hostuser = $this->get_host_webex_user();
        $creator = $this->get_creator_webex_user();

        if (isset($this->meetingkey)) {
            // Updating meeting.
            $xml = $gen::update_meeting($this);
        } else {
            // Creating meeting.
            $this->hostwebexid = $hostuser->webexid;
            $this->creatorwebexid = $creator->webexid;
            $this->template = static::get_template_name();

            $xml = $gen::create_meeting($this);
        }

        try {
            $response = $this->webex->get_response($xml, $creator);
        } catch (exception\host_scheduling $e) {
            // If the user doesn't have the scheduing permission set, then update it.
            $update = $hostuser->set_scheduling_permission();

            if ($update) {
                // Try again.
                $response = $this->webex->get_response($xml, $creator);
            } else {
                return false;
            }
        }

        $status = $this->process_response($response);

        if ($status) {
            $this->webexchange = false;
            return true;
        } else {
            return false;
        }
    }

    /**
     * Delete this meeting from WebEx.
     *
     * @return bool    True on success, false on failure.
     */
    public function delete_from_webex() {
        // If we have no key, then we don't exist in WebEx, so we can say we are deleted.
        if (!isset($this->meetingkey)) {
            return true;
        }

        $gen = static::GENERATOR;

        $xml = $gen::delete_meeting($this->meetingkey);
        $creator = $this->get_creator_webex_user();

        try {
            $response = $this->webex->get_response($xml, $creator);
        } catch (exception\webex_xml_exception $e) {
            if (strpos($e->getMessage(), '060001') !== false) {
                // If the code is 060001, meeting was not found in WebEx.
                return true;
            }
            throw $e;
        }

        return true;
    }

    /**
     * Fetch meeting information from WebEx.
     *
     * @param bool     $save True to save to the db.
     * @return bool    True on success, false on failure.
     */
    public function get_info($save = false) {
        if (!isset($this->meetingkey)) {
            return false;
        }

        $gen = static::GENERATOR;

        $xml = $gen::get_meeting_info($this->meetingkey);
        $creator = $this->get_creator_webex_user();

        if (!$response = $this->webex->get_response($xml, $creator)) {
            return false;
        }

        if (!$this->process_response($response)) {
            return false;
        }

        if ($save) {
            $this->save();
        }

        return $response;
    }

    /**
     * Fetch meeting information from WebEx.
     *
     * @param bool     $save True to save to the db.
     * @return bool    True on success, false on failure.
     */
    public function get_session_info($save = false) {
        if (!isset($this->meetingkey)) {
            return false;
        }

        $gen = static::GENERATOR;

        $xml = $gen::get_session_info($this->meetingkey);
        $creator = $this->get_creator_webex_user();

        if (!$response = $this->webex->get_response($xml, $creator)) {
            return false;
        }

        if (!$this->process_session_info_response($response)) {
            return false;
        }

        if ($save) {
            $this->save();
        }

        return $response;
    }

    /**
     * Return the time status (upcoming, in progress, past, long past, available).
     *
     * @return int   Constant represents status.
     */
    public function get_time_status() {
        $time = time();
        $grace = get_config('webexactivity', 'meetingclosegrace');

        if (isset($this->endtime)) {
            $endtime = $this->endtime;
        } else {
            $endtime = $this->starttime + ($this->duration * 60) + ($grace * 60);
        }

        $starttime = $this->starttime - (static::OPEN_TIME * 60);

        if ($this->status == \mod_webexactivity\webex::WEBEXACTIVITY_STATUS_IN_PROGRESS) {
            return \mod_webexactivity\webex::WEBEXACTIVITY_TIME_IN_PROGRESS;
        }

        if ($time < $starttime) {
            return \mod_webexactivity\webex::WEBEXACTIVITY_TIME_UPCOMING;
        }

        if ($time > $endtime) {
            if ($time > ($endtime + (24 * 3600))) {
                return \mod_webexactivity\webex::WEBEXACTIVITY_TIME_LONG_PAST;
            } else {
                return \mod_webexactivity\webex::WEBEXACTIVITY_TIME_PAST;
            }
        }

        return \mod_webexactivity\webex::WEBEXACTIVITY_TIME_AVAILABLE;
    }

    /**
     * Return if the meeting is available to join/host.
     *
     * @param bool    $host Set to true if host, false if not.
     * @return bool   True if available, false if not.
     */
    public function is_available($host = false) {
        $status = $this->get_time_status();

        if ($host) {
            if (($status === \mod_webexactivity\webex::WEBEXACTIVITY_TIME_AVAILABLE) ||
                    ($status === \mod_webexactivity\webex::WEBEXACTIVITY_TIME_IN_PROGRESS) ||
                    ($status === \mod_webexactivity\webex::WEBEXACTIVITY_TIME_UPCOMING)) {
                return true;
            }
        } else {
            if (($status === \mod_webexactivity\webex::WEBEXACTIVITY_TIME_AVAILABLE) ||
                    ($status === \mod_webexactivity\webex::WEBEXACTIVITY_TIME_IN_PROGRESS)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Process a response from WebEx into the meeting. Must be overridden.
     *
     * @param array    $response XML array of the response from WebEx for meeting information.
     * @return bool    True on success, false on failure/error.
     */
    protected function process_response($response) {
        $prefix = static::XML_PREFIX;

        if (empty($prefix)) {
            debugging('Function process_response must be implemented by child class.', DEBUG_DEVELOPER);
        }

        if ($response === false) {
            return false;
        }

        if (empty($response)) {
            return true;
        }

        if (isset($response[$prefix.':additionalInfo']['0']['#'][$prefix.':guestToken']['0']['#'])) {
            $this->guestkey = $response[$prefix.':additionalInfo']['0']['#'][$prefix.':guestToken']['0']['#'];
        }

        if (isset($response[$prefix.':eventID']['0']['#'])) {
            $this->eventid = $response[$prefix.':eventID']['0']['#'];
        }

        if (isset($response[$prefix.':hostKey']['0']['#'])) {
            $this->hostkey = $response[$prefix.':hostKey']['0']['#'];
        }

        return true;

    }

    /**
     * Process a info (GetSessionInfo) response from WebEx into the DB.
     *
     * @param array    $response XML array of the response from WebEx for meeting information.
     * @return bool    True on success, false on failure/error.
     */
    protected function process_session_info_response($response) {
        if ($response === false) {
            return false;
        }

        if (empty($response)) {
            return true;
        }

        if (isset($response['ep:hostKey']['0']['#'])) {
            $this->hostkey = $response['ep:hostKey']['0']['#'];
        }

        $passreq = null;
        if (isset($response['ep:accessControl']['0']['#']['ep:passwordReq']['0']['#'])) {
            $passreq = $response['ep:accessControl']['0']['#']['ep:passwordReq']['0']['#'];

            if (strcasecmp($passreq, 'true') === 0) {
                $passreq = true;
            } else if (strcasecmp($passreq, 'false') === 0) {
                $passreq = false;
            } else {
                $passreq = null;
            }
        }

        $password = null;
        if (isset($response['ep:accessControl']['0']['#']['ep:sessionPassword']['0']['#'])) {
            $password = $response['ep:accessControl']['0']['#']['ep:sessionPassword']['0']['#'];

            if (trim($password) === '') {
                $password = null;
            }
        }

        if (is_null($password)) {
            if ($passreq === false) {
                // If we didn't get a password, and no password is required, clear it from the DB.
                $this->password = null;
            }
        } else {
            // Update the password, incase WebEx changed it.
            $this->password = $password;
        }

        if (isset($response['ep:meetingLink']['0']['#'])) {
            $this->meetinglink = $response['ep:meetingLink']['0']['#'];
        }

        return true;
    }

    /**
     * Add a webex user as a host to the meeting.
     *
     * @param user    $webexuser The user object to add.
     * @return bool   True on success, false on failure/error.
     */
    public function add_webexuser_host($webexuser) {
        global $DB;

        if ($webexuser->webexid === $this->hostwebexid) {
            return true;
        }

        $moodleuser = $DB->get_record('user', array('id' => $webexuser->moodleuserid));
        $user = new \stdClass();
        $user->webexid = $webexuser->webexid;
        $user->email = $moodleuser->email;
        $user->firstname = $moodleuser->firstname;
        $user->lastname = $moodleuser->lastname;

        $data = new \stdClass();
        $data->meetingkey = $this->meetingkey;
        $data->hostusers = array($user);

        $gen = static::GENERATOR;
        $xml = $gen::update_meeting($data);

        $creator = $this->get_creator_webex_user();
        if (!($response = $this->webex->get_response($xml, $creator))) {
            return false;
        }

        return true;
    }

    /**
     * Change the webex host to a different user.
     *
     * @param user    $webexuser The user object to add.
     * @return bool   True on success, false on failure/error.
     */
    public function change_webexuser_host($webexuser) {
        $this->hostwebexid = $webexuser->webexid;

        return $this->save();
    }

    // ---------------------------------------------------
    // Recording Functions.
    // ---------------------------------------------------
    /**
     * Delete all meetings connected to this meeting.
     *
     * @return bool    True on success, false on failure/error.
     */
    public function delete_recordings() {
        $recordings = $this->get_recordings();

        if (!is_array($recordings)) {
            return true;
        }

        foreach ($recordings as $recording) {
            $recording->delete();
        }

        return true;
    }

    /**
     * Get all the recording objects for this meeting.
     *
     * @return array    Array of recording objects.
     */
    public function get_recordings() {
        global $DB;

        $params = array('webexid' => $this->id, 'deleted' => 0);
        $recordingrecords = $DB->get_records('webexactivity_recording', $params, 'timecreated ASC');

        if (!$recordingrecords) {
            return array();
        }

        $out = array();

        foreach ($recordingrecords as $record) {
            $out[] = new \mod_webexactivity\recording($record);
        }

        return $out;
    }

    // ---------------------------------------------------
    // URL Functions.
    // ---------------------------------------------------
    /**
     * Get the link for hosting this meeting.
     *
     * @param string     $returnurl The url to return the use to.
     * @return string    The host url.
     */
    public function get_host_url($returnurl = false) {
        $baseurl = \mod_webexactivity\webex::get_base_url();
        $url = $baseurl.'/m.php?AT=HM&MK='.$this->meetingkey;

        if ($returnurl) {
            $url .= '&BU='.urlencode($returnurl);
        }

        return $url;
    }

    /**
     * Get an authenticated host url for a meeting.
     *
     * @param string     $authfailed Failure url.
     * @param string     $hostsuccess Post host url.
     * @return string    The host url.
     */
    public function get_authed_host_url($authfail = false, $hostsuccess = false) {
        $gen = static::GENERATOR;

        $xml = $gen::get_host_url($this);
        $creator = $this->get_creator_webex_user();

        if (!$response = $this->webex->get_response($xml, $creator)) {
            throw new \coding_exception('error');
        }

        if (!isset($response['meet:hostMeetingURL']['0']['#'])) {
            throw new \coding_exception('error');
        }

        $url = $response['meet:hostMeetingURL']['0']['#'];

        $parts = explode('&', $url);

        if ($hostsuccess) {
            foreach ($parts as $key => $part) {
                $subs = explode('=', $part);
                if (strcasecmp('MU', $subs[0]) === 0) {
                    $mu = urldecode($subs[1]);
                    $mu .= '&BU='.urlencode($hostsuccess);
                    $parts[$key] = 'MU='.urlencode($mu);
                }
            }
        }

        if ($authfail) {
            $parts[] = 'BU='.urlencode($authfail);
        }

        $url = implode('&', $parts);

        return $url;
    }

    /**
     * Get the link for a moodle user to join the meeting.
     *
     * @param object|null   $user Moodle user record.
     * @param string        $returnurl The url to return the use to.
     * @return string       The moodle join url.
     */
    public function get_moodle_join_url($user = null, $returnurl = false) {
        $gen = static::GENERATOR;

        $xml = $gen::get_join_url($this, $user);
        $creator = $this->get_creator_webex_user();

        if (!$response = $this->webex->get_response($xml, $creator)) {
            throw new \coding_exception('error');
        }

        if (!isset($response['meet:joinMeetingURL']['0']['#'])) {
            throw new \coding_exception('error');
        }

        $url = $response['meet:joinMeetingURL']['0']['#'];

        if ($returnurl) {
            $url .= '&BU='.urlencode($returnurl);
        }

        return $url;
    }

    /**
     * Get the link for external users to join the meeting.
     *
     * @return string    The external join url.
     */
    public function get_external_join_url() {
        // See if the link appears to be the new style already. If not, create.
        if (stristr($this->meetinglink, 'e.php') === false) {
            $this->meetinglink = $this->get_moodle_join_url();
            $this->save_to_db();
        }

        return $this->meetinglink;
    }

    /**
     * Get the link for external users to join the meeting.
     *
     * @return string    The external join url.
     */
    public function get_old_external_join_url() {
        $baseurl = \mod_webexactivity\webex::get_base_url();

        if (!isset($this->eventid)) {
            $this->get_info(true);
        }

        $url = $baseurl.'/k2/j.php?ED='.$this->eventid.'&UID=1';

        return $url;
    }

    /**
     * Get the link to switch meeting type for further URL API requests
     *
     * @param string     $mtype Meeting type to switch to
     * @param string     $returnurl The url to return the use to.
     * @return string    The url for switching meeting type
     */
    public function get_switch_meeting_type_ulr($mtype = 'MC', $returnurl = false) {
        $baseurl = \mod_webexactivity\webex::get_base_url();
        $url = $baseurl."/o.php?AT=ST&SP=".$mtype;
        if ($returnurl) {
            $url .= '&BU='.urlencode($returnurl);
        }
        return $url;
    }

    // ---------------------------------------------------
    // Support Functions.
    // ---------------------------------------------------
    /**
     * Returns the webex user that created this meeting.
     *
     * @return bool|user    The WebEx user. False on failure.
     */
    public function get_creator_webex_user() {
        $webexuser = false;
        if (isset($this->creatorwebexid)) {
            try {
                // Try and load the user for this meetings user.
                $webexuser = \mod_webexactivity\user::load_webex_id($this->creatorwebexid);
            } catch (\coding_exception $e) {
                $webexuser = false;
            }
        }

        // If we haven't set it, try and set it to the admin user.
        if (!$webexuser) {
            $webexuser = \mod_webexactivity\user::load_admin_user();
        }

        return $webexuser;
    }

    /**
     * Check if this meeting type requires a password.
     *
     * @return bool  True if a meeting password is required, false if not.
     */
    public static function is_password_required() {
        return \mod_webexactivity\meeting::get_meeting_type_password_required(static::TYPE);
    }

    /**
     * Generate password.
     *
     * @return string  A meeting password.
     */
    public static function generate_password() {
        $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        $pass = array();
        $length = strlen($alphabet) - 1;
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $length);
            $pass[] = $alphabet[$n];
        }

        return implode($pass);
    }

    /**
     * Check if this meeting type has a template
     *
     * @return string  Meeting template name
     */
    public static function get_template_name() {
        return \mod_webexactivity\meeting::get_meeting_type_template(static::TYPE);
    }

    /**
     * Check if this meeting was created by an admin.
     *
     * @return bool  True if admin created this, false if otherwise.
     */
    public function is_admin_created() {
        if (strcasecmp(get_config('webexactivity', 'apiusername'), $this->creatorwebexid) === 0) {
            return true;
        }

        return false;
    }

    /**
     * Returns the webex user that hosts this meeting.
     *
     * @return bool|user    The WebEx user. False on failure.
     */
    public function get_host_webex_user() {
        global $USER;

        $webexuser = false;
        if (isset($this->hostwebexid)) {
            try {
                // Try and load the user for this meetings user.
                $webexuser = \mod_webexactivity\user::load_webex_id($this->hostwebexid);
            } catch (\coding_exception $e) {
                $webexuser = false;
            }
        }

        // If we haven't set it, try and set it to the current user.
        if (!$webexuser) {
            $webexuser = \mod_webexactivity\user::load_for_user($USER);
        }

        return $webexuser;
    }

    /**
     * Load a database object into the meeting.
     *
     * @param stdClass   $meeting The record to load.
     */
    protected function load_webex_record($meeting) {
        $this->meetingrecord = $meeting;

        $meetingarray = (array) $meeting;
    }

    /**
     * Save the meeting to WebEx and Moodle as needed.
     *
     * @return bool    True on success, false on failure/error.
     */
    public function save() {
        if ($this->webexchange) {
            if (!$this->save_to_webex()) {
                return false;
            }
        }

        if (!$this->save_to_db()) {
            return false;
        }

        $this->save_calendar_event();

        return true;
    }

    /**
     * Save the meeting to the Moodle database.
     *
     * @return bool    True on success, false on failure/error.
     */
    public function save_to_db() {
        global $DB;

        $this->timemodified = time();

        if (isset($this->id)) {
            if ($DB->update_record('webexactivity', $this->meetingrecord)) {
                return true;
            }
            return false;
        } else {
            if ($id = $DB->insert_record('webexactivity', $this->meetingrecord)) {
                $this->id = $id;
                return true;
            }
            return false;
        }
    }

    /**
     * Delete the meeting (and all it's recordings) from the DB and WebEx.
     *
     * @return bool    True on success, false on failure/error.
     */
    public function delete() {
        if (!$this->delete_recordings()) {
            return false;
        }

        if (!$this->delete_from_webex()) {
            return false;
        }

        if (!$this->delete_from_db()) {
            return false;
        }

        return true;
    }

    /**
     * Delete the meeting from the database.
     *
     * @return bool    True on success, false on failure/error.
     */
    public function delete_from_db() {
        global $DB;

        if (!isset($this->id)) {
            return true;
        }

        if (!$DB->delete_records('webexactivity', array('id' => $this->id))) {
            return false;
        }

        unset($this->id);
        unset($this->meetingrecord);
        return true;
    }

    public function save_calendar_event() {
        global $DB, $USER;

        $event = new \stdClass();
        $params = ['modulename' => 'webexactivity', 'instance' => $this->id, 'eventtype' => 'meetingtime'];
        $event->id = $DB->get_field('event', 'id', $params);

        if (!$this->calpublish || !is_null($this->endtime)) {
            // This means there should not be an event.
            if ($event->id) {
                // But there is one, so remove it.
                $calendarevent = \calendar_event::load($event->id);
                $calendarevent->delete();
            }
        } else {
            // This means there should be an event.

            if (empty($this->cmid)) {
                $cm = get_coursemodule_from_instance('webexactivity', $this->id, $this->course);
                if (empty($cm)) {
                    return false;
                }
                $this->cmid = $cm->id;
            }
            if ($event->id) {
                // Update an existing event.
                $event->type         = CALENDAR_EVENT_TYPE_ACTION;
                $event->name         = $this->name;
                $event->description  = format_module_intro('webexactivity', $this, $this->cmid);
                $event->timestart    = $this->starttime;
                $event->timesort     = $this->starttime;
                $event->timeduration = $this->duration * 60;

                $calendarevent = \calendar_event::load($event->id);
                $calendarevent->update($event, false);
            } else {
                // Create a new event.
                $event = new \stdClass();
                $event->type         = CALENDAR_EVENT_TYPE_ACTION;
                $event->name         = $this->name;
                $event->description  = format_module_intro('webexactivity', $this, $this->cmid);
                $event->courseid     = $this->course;
                $event->groupid      = 0;
                $event->userid       = 0;
                $event->modulename   = 'webexactivity';
                $event->instance     = $this->id;
                $event->eventtype    = 'meetingtime';
                $event->timestart    = $this->starttime;
                $event->timesort     = $this->starttime;
                $event->timeduration = $this->duration * 60;

                \calendar_event::create($event, false);
            }
        }
    }
}
