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

defined('MOODLE_INTERNAL') || die();

/**
 * A class that (statically) provides all base xml generation.
 *
 * @package    mod_webexactvity
 * @author     Eric Merrill <merrill@oakland.edu>
 * @copyright  2014 Oakland University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class xml_gen {
    /**
     * Wraps given XML in the propper authentication headers.
     *
     * @param string       $xml The PHP type is followed by the variable name.
     * @param user|bool    $user The user to use. Use admin if not provided.
     * @return string      The wrapped XML.
     */
    public static function auth_wrap($xml, $user = false) {
        return self::standard_wrap(self::get_auth_header($user).$xml);
    }

    /**
     * Wraps given XML in the propper body wrapper.
     *
     * @param string    $xml The PHP type is followed by the variable name.
     * @return string   The wrapped XML.
     */
    private static function standard_wrap($xml) {
        $outxml = '<?xml version="1.0" encoding="UTF-8"?>'.
                  '<serv:message xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"'.
                  ' xmlns:serv="http://www.webex.com/schemas/2002/06/service">';
        $outxml .= $xml;
        $outxml .= '</serv:message>';

        return $outxml;
    }

    /**
     * Wraps given XML in the propper authentication headers.
     *
     * @param user|bool   $user The user to use. Use admin if false or not provided.
     * @return string     The authentication block.
     */
    private static function get_auth_header($user = false) {
        global $CFG;

        $config = get_config('webexactivity');

        $outxml = '<header><securityContext>';

        if ($user == false) {
            $outxml .= '<webExID>'.$config->apiusername.'</webExID>';
            $outxml .= '<password>'.self::format_password($config->apipassword).'</password>';
        } else {
            $outxml .= '<webExID>'.$user->webexid.'</webExID>';
            $outxml .= '<password>'.self::format_password($user->password).'</password>';
        }

        $outxml .= '<siteName>'.$config->sitename.'</siteName>';

        $outxml .= '</securityContext></header>';

        return $outxml;
    }

    // ---------------------------------------------------
    // User Functions.
    // ---------------------------------------------------
    /**
     * Provide the xml to retrieve user information.
     *
     * @param string    $username The username of the user to lookup.
     * @return string   The XML.
     */
    public static function get_user_info($username) {
        $xml = '<body><bodyContent xsi:type="java:com.webex.service.binding.user.GetUser">'.
               '<webExId>'.$username.'</webExId>'.
               '</bodyContent></body>';

        return $xml;
    }

    /**
     * Provide the xml to retrieve a users login url.
     *
     * @param string    $username The username of the user to lookup.
     * @return string   The XML.
     */
    public static function get_user_login_url($username) {
        $xml = '<body><bodyContent xsi:type="java:com.webex.service.binding.user.GetloginurlUser">'.
               '<webExID>'.$username.'</webExID>';
        $xml .= '</bodyContent></body>';

        return $xml;
    }

    /**
     * Provide the xml to create a user.
     *
     * Required keys in $data are:
     * 1/ firstname - First name of the user
     * 2/ lastname - Last name of the user
     * 3/ webexid - WebEx userid to use
     * 4/ email - Email address of the user
     * 5/ password - Plain text password to get
     *
     * @param stdClass  $data Data object to use.
     * @return string   The XML.
     */
    public static function create_user($data) {
        $xml = '<body><bodyContent xsi:type="java:com.webex.service.binding.user.CreateUser">'.
               '<firstName>'.self::format_text($data->firstname, 64).'</firstName>'.
               '<lastName>'.self::format_text($data->lastname, 64).'</lastName>'.
               '<webExId>'.$data->webexid.'</webExId>'.
               '<email>'.$data->email.'</email>'.
               '<password>'.self::format_password($data->password).'</password>'.
               '<privilege><host>true</host></privilege>'.
               '<active>ACTIVATED</active>';

        if (isset($data->schedulingpermission)) {
            $xml .= '<schedulingPermission>'.self::format_text($data->schedulingpermission).'</schedulingPermission>';
        }

        $xml .= '</bodyContent></body>';

        return $xml;
    }

    /**
     * Provide the xml to send a new password for a user.
     *
     * @param user      $webexuser A webex user.
     * @return string   The XML.
     */
    public static function update_user_password($webexuser) {
        $xml = '<body><bodyContent xsi:type="java:com.webex.service.binding.user.SetUser">'.
               '<webExId>'.$webexuser->webexid.'</webExId>'.
               '<password>'.self::format_password($webexuser->password, 64).'</password>'.
               '<active>ACTIVATED</active>'.
               '</bodyContent></body>';

        return $xml;
    }

    /**
     * Provide the xml to update a webex User.
     *
     * @param user        $webexuser A webex user.
     * @return string     The XML.
     */
    public static function update_user($webexuser) {
        $xml = '<body><bodyContent xsi:type="java:com.webex.service.binding.user.SetUser">'.
               '<webExId>'.$webexuser->webexid.'</webExId>';

        if (isset($webexuser->newwebexid)) {
            $xml .= '<newWebExId>'.$webexuser->webexid.'</newWebExId>';
        }

        $xml .= '<firstName>'.self::format_text($webexuser->firstname, 64).'</firstName>'.
                '<lastName>'.self::format_text($webexuser->lastname, 64).'</lastName>'.
                '<email>'.$webexuser->email.'</email>'.
                '<active>ACTIVATED</active>';

        if (isset($webexuser->schedulingpermission)) {
            $xml .= '<schedulingPermission>'.self::format_text($webexuser->schedulingpermission).'</schedulingPermission>';
        }

        $xml .= '</bodyContent></body>';

        return $xml;
    }

    /**
     * Provide the xml to check the authentication of a user.
     *
     * @param user       $webexuser A webex user.
     * @return string    The XML.
     */
    public static function check_user_auth($webexuser) {
        $xml = '<body><bodyContent xsi:type="java:com.webex.service.binding.user.GetUser">'.
               '<webExId>'.$webexuser->webexid.'</webExId>'.
               '</bodyContent></body>';

        return $xml;
    }

    /**
     * Provide the xml to retrieve a user based on email.
     *
     * @param string    $username The username of the user to lookup.
     * @return string   The XML.
     */
    public static function get_user_for_email($email) {
        $xml = '<body><bodyContent xsi:type="java:com.webex.service.binding.user.LstsummaryUser">'.
               '<listControl>'.
               '<serv:startFrom>1</serv:startFrom>'.
               '<serv:maximumNum>1</serv:maximumNum>'.
               '</listControl>'.
               '<email>'.self::format_text($email).'</email>'.
               '</bodyContent></body>';

        return $xml;
    }

    /**
     * Provide the xml to retrieve the hosting url for a meeting.
     *
     * @param meeting   $meeting The meeting to get the host URL for.
     * @return string   The XML.
     */
    public static function get_host_url($meeting) {
        $xml = '<body><bodyContent xsi:type="java:com.webex.service.binding.meeting.GethosturlMeeting">'.
               '<sessionKey>'.$meeting->meetingkey.'</sessionKey>'.
               '</bodyContent></body>';

        return $xml;
    }

    /**
     * Provide the xml to retrieve the participant join url for a meeting.
     * Works with TC and MC.
     *
     * @param meeting       $meeting The meeting to get the host URL for.
     * @param object|null   $user The user object. If null, get external participant link.
     * @return string       The XML.
     */
    public static function get_join_url($meeting, $user = null) {
        $xml = '<body><bodyContent xsi:type="java:com.webex.service.binding.meeting.GetjoinurlMeeting">'.
               '<sessionKey>'.$meeting->meetingkey.'</sessionKey>';

        // User is optional.
        if (!is_null($user)) {
            $xml .= '<attendeeEmail>'.$user->email.'</attendeeEmail>'.
                    '<attendeeName>'.fullname($user).'</attendeeName>';
        }

        if (!empty($meeting->password)) {
            $xml .= '<meetingPW>'.$meeting->password.'</meetingPW>';
        }

        $xml .= '</bodyContent></body>';

        return $xml;
    }

    // ---------------------------------------------------
    // Meeting Functions.
    // ---------------------------------------------------
    /**
     * Provide the xml to get information about a meeting. Must be overridden.
     *
     * @param string    $meetingkey Meeting key to lookup.
     * @return string   The XML.
     */
    public static function get_meeting_info($meetingkey) {
        debugging('Function get_meeting_info must be implemented by child class.', DEBUG_DEVELOPER);
    }

    /**
     * Provide the xml to get information about a meeting. Must be overridden.
     *
     * @param string    $meetingkey Meeting key to lookup.
     * @return string   The XML.
     */
    public static function get_session_info($meetingkey) {
        $xml = '<body><bodyContent xsi:type="java:com.webex.service.binding.ep.GetSessionInfo">'.
               '<sessionKey>'.$meetingkey.'</sessionKey>'.
               '</bodyContent></body>';

        return $xml;
    }

    /**
     * Provide the xml to create a meeting. Must be overridden.
     *
     * Required keys in $data are:
     * 1/ startdate - Start time range.
     * 2/ duration - Duration in minutes.
     * 3/ name - Name of the meeting.
     *
     * Optional keys in $data are:
     * 1/ intro - Meeting description.
     * 2/ hostusers - Array of users to add as hosts.
     *
     * @param stdClass  $data Meeting data to make.
     * @return string   The XML.
     */
    public static function create_meeting($data) {
        debugging('Function create_meeting must be implemented by child class.', DEBUG_DEVELOPER);
    }

    /**
     * Provide the xml to update a meeting. Must be overridden.
     *
     * Required keys in $data are:
     * 1/ meetingkey - Meeting key to update.
     *
     * Optional keys in $data are:
     * 1/ startdate - Start time range.
     * 2/ duration - Duration in minutes.
     * 3/ name - Name of the meeting.
     * 4/ intro - Meeting description.
     * 5/ hostusers - Array of users to add as hosts.
     *
     * @param stdClass  $data Meeting data to make.
     * @return string   The XML.
     */
    public static function update_meeting($data) {
        debugging('Function update_meeting must be implemented by child class.', DEBUG_DEVELOPER);
    }

    /**
     * Provide the xml to delete a meeting.
     *
     * @param string    $meetingkey Meeting key to delete.
     * @return string   The XML.
     */
    public static function delete_meeting($meetingkey) {
        $xml = '<body><bodyContent xsi:type="java:com.webex.service.binding.ep.DelSession">'.
               '<sessionKey>'.$meetingkey.'</sessionKey>'.
               '</bodyContent></body>';

        return $xml;
    }

    /**
     * Provide the xml to list all open sessions on the WebEx server.
     *
     * @return string   The XML.
     */
    public static function list_open_sessions() {
        $xml = '<body><bodyContent xsi:type="java:com.webex.service.binding.ep.LstOpenSession">'.
               '</bodyContent></body>';

        return $xml;
    }

    // ---------------------------------------------------
    // Recording Functions.
    // ---------------------------------------------------
    /**
     * Provide the xml to create a user.
     *
     * Optional keys in $data are:
     * 1/ meetingkey - Meeting key to retrieve recordings for.
     * 2/ startdate - Start time range.
     * 3/ enddate - End time range.
     * 4/ start - Record number to start at.
     * 5/ count - Count of records to get, 500 max.
     *
     * @param stdClass  $data Data object to use.
     * @return string   The XML.
     */
    public static function list_recordings($data) {
        $xml = '<body><bodyContent xsi:type="java:com.webex.service.binding.ep.LstRecording">';

        if (!isset($data->start)) {
            $data->start = 0;
        }
        if (!isset($data->count)) {
            $data->count = 500;
        }

        $xml .= '<listControl><startFrom>'.$data->start.'</startFrom>';
        $xml .= '<maximumNum>'.$data->count.'</maximumNum></listControl>';

        if (isset($data->meetingkey)) {
            $xml .= '<sessionKey>'.$data->meetingkey.'</sessionKey>';
        }
        if (isset($data->startdate) && isset($data->enddate)) {
            $xml .= '<createTimeScope>';
            $xml .= '<createTimeStart>'.self::time_to_date_string($data->startdate).'</createTimeStart>';
            $xml .= '<createTimeEnd>'.self::time_to_date_string($data->enddate).'</createTimeEnd>';
            $xml .= '<timeZoneID>20</timeZoneID>'; // GMT timezone.
            $xml .= '</createTimeScope>';
        }
        $xml .= '<returnSessionDetails>true</returnSessionDetails>';
        $xml .= '</bodyContent></body>';

        return $xml;
    }

    /**
     * Provide the xml to get detailed recording info.
     *
     * @param string    $recordingid Recording ID get.
     * @return string   The XML.
     */
    public static function recording_detail($recordingid) {
        $xml = '<body><bodyContent xsi:type="java:com.webex.service.binding.ep.GetRecordingInfo">';
        $xml .= '<recordingID>'.$recordingid.'</recordingID>';
        $xml .= '</bodyContent></body>';

        return $xml;
    }

    /**
     * Provide the xml to get delete a recording.
     *
     * @param string    $recordingid Recording ID delete.
     * @return string   The XML.
     */
    public static function delete_recording($recordingid) {
        $xml = '<body><bodyContent xsi:type="java:com.webex.service.binding.ep.DelRecording">';
        $xml .= '<recordingID>'.$recordingid.'</recordingID>';
        $xml .= '</bodyContent></body>';

        return $xml;
    }

    /**
     * Provide the xml to get update recording info.
     *
     * Required keys in $data are:
     * 1/ recordingid - Meeting key to retrieve recordings for.
     *
     * Optional keys in $data are:
     * 1/ name - The name to set.
     *
     * @param stdClass   $data Data object.
     * @return string    The XML.
     */
    public static function update_recording($data) {
        $xml = '<body><bodyContent xsi:type="java:com.webex.service.binding.ep.SetRecordingInfo">';
        $xml .= '<recording><recordingID>'.$data->recordingid.'</recordingID><description>Des 1</description></recording>';

        if (isset($data->name)) {
            $xml .= '<basic>';
            $xml .= '<topic>'.self::format_text($data->name).'</topic>';
            $xml .= '<agenda>Agenda 1</agenda>';
            $xml .= '</basic>';
        }
        $xml .= '<fileAccess><attendeeDownload>false</attendeeDownload></fileAccess>';
        $xml .= '</bodyContent></body>';

        return $xml;
    }

    // ---------------------------------------------------
    // Support Functions.
    // ---------------------------------------------------
    /**
     * Format a timestamp to send to WebEx. Converts to GMT time.
     *
     * @param int       $time Timestamp to format.
     * @return string   The XML.
     */
    public static function time_to_date_string($time) {
        // Convert the time to GMT.
        $dt = new \DateTime(date("o:m:d H:i:s", $time), \core_date::get_server_timezone_object());
        $gmttime = $time - $dt->getOffset();

        return date('m/d/Y H:i:s', $gmttime);
    }

    /**
     * Format text for sending to WebEx.
     *
     * @param string   $text The text to format.
     * @param int      $limit Limit the output to this many chars. 0 for unlimited.
     * @return string  The formatted text.
     */
    public static function format_text($text, $limit = 0) {
        // No current support for HTML tags. Researching.
        $text = strip_tags($text);

        // Some characters are already encoded in the DB.
        $text = html_entity_decode($text);

        // Hack for pre-PHP 5.4.0 support. Remove when dropping Moodle 2.6 support.
        if (PHP_VERSION_ID < 50400) {
            $text = htmlspecialchars($text);
        } else {
            // Convert with XML compatability.
            $text = htmlentities($text, ENT_COMPAT | ENT_XML1);
        }

        // Need special line endings.
        $text = str_replace("\n", "&#10;\n", $text);
        $text = str_replace("\r", "&#13;\r", $text);

        if ($limit) {
            $text = substr($text, 0, $limit);
        }

        return $text;
    }

    /**
     * Format a password for sending to WebEx.
     *
     * @param string   $text The password to format.
     * @param int      $limit Limit the output to this many chars. 0 for unlimited.
     * @return string  The formatted password.
     */
    public static function format_password($text, $limit = 0) {
        // Convert with XML compatability.
        $text = htmlentities($text, ENT_COMPAT | ENT_XML1);

        if ($limit) {
            $text = substr($text, 0, $limit);
        }

        return $text;
    }

}
