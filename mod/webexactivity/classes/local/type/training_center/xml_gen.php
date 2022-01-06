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

namespace mod_webexactivity\local\type\training_center;

defined('MOODLE_INTERNAL') || die();

/**
 * A class that (statically) provides training center xml.
 *
 * @package    mod_webexactvity
 * @author     Eric Merrill <merrill@oakland.edu>
 * @copyright  2014 Oakland University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class xml_gen extends \mod_webexactivity\local\type\base\xml_gen {

    // ---------------------------------------------------
    // Meeting Functions.
    // ---------------------------------------------------
    /**
     * Provide the xml to get information about a meeting.
     *
     * @param string    $meetingkey Meeting key to lookup.
     * @return string   The XML.
     */
    public static function get_meeting_info($meetingkey) {
        $xml = '<body><bodyContent xsi:type="java:com.webex.service.binding.training.GetTrainingSession">'.
               '<sessionKey>'.$meetingkey.'</sessionKey>'.
               '</bodyContent></body>';

        return $xml;
    }

    /**
     * Provide the xml to create a meeting.
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
        if (!$sessionxml = self::training_session_xml($data)) {
            return false;
        }

        $xml = '<body><bodyContent xsi:type="java:com.webex.service.binding.training.CreateTrainingSession">';
        $xml .= $sessionxml;
        $xml .= '</bodyContent></body>';

        return $xml;
    }

    /**
     * Provide the xml to update a meeting.
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
        if (!$sessionxml = self::training_session_xml($data)) {
            return false;
        }

        $xml = '<body><bodyContent xsi:type="java:com.webex.service.binding.training.SetTrainingSession">';
        $xml .= $sessionxml;
        $xml .= '</bodyContent></body>';

        return $xml;
    }

    /**
     * Provide the detailed meeting xml for update or delete.
     *
     * Optional keys in $data are:
     * 1/ meetingkey - Meeting key (required for update).
     * 2/ startdate - Start time range.
     * 3/ duration - Duration in minutes.
     * 4/ name - Name of the meeting.
     * 5/ intro - Meeting description.
     * 6/ hostusers - Array of users to add as hosts.
     *
     * @param stdClass  $data Meeting data to make.
     * @return string   The XML.
     */
    private static function training_session_xml($data) {
        $config = get_config('webexactivity');

        $xml = '';
        if (isset($data->meetingkey)) {
            $xml .= '<sessionKey>'.$data->meetingkey.'</sessionKey>';
        }

        $xml .= '<accessControl><listing>UNLISTED</listing>';

        if (isset($data->password)) {
            $xml .= '<sessionPassword>'.self::format_password($data->password, 16).'</sessionPassword>';
            $xml .= '<enforcePassword>FALSE</enforcePassword>';
        }

        $xml .= '</accessControl>';

        $xml .= '<schedule>';
        // Only include the time if it isn't in the past.
        if (isset($data->starttime) && ($data->starttime >= (time() + 10))) {
            $startstr = self::time_to_date_string($data->starttime);

            $xml .= '<startDate>'.$startstr.'</startDate>';
            $xml .= '<timeZoneID>20</timeZoneID>'; // GMT timezone.
            $xml .= '<openTime>20</openTime>';
        }
        if (isset($data->duration)) {
            $xml .= '<duration>'.$data->duration.'</duration>';
        }
        if (isset($data->hostwebexid)) {
            $xml .= '<hostWebExID>'.self::format_text($data->hostwebexid).'</hostWebExID>';
        }
        $xml .= '</schedule>';

        if (isset($data->name) || isset($data->template)) {
            $xml .= '<metaData>';
            if (isset($data->name)) {
                $xml .= '<confName>'.self::format_text($data->name, 400).'</confName>';
                if (isset($data->intro)) {
                    $xml .= '<agenda>'.self::format_text($data->intro, 2250).'</agenda>';
                }
            }
            if (isset($data->template) && $data->template != "") {
                $xml .= '<sessionTemplate><use>'.$data->template.'</use></sessionTemplate>';
            }

            $xml .= '</metaData>';
        }

        $xml .= '<enableOptions>';

        $xml .= '<attendeeSendVideo>true</attendeeSendVideo>';

        /*if (isset($data->allchat)) {
            if ($data->allchat) {
                $xml .= '<chatAllAttendees>true</chatAllAttendees>';
            } else {
                $xml .= '<chatAllAttendees>false</chatAllAttendees>';
            }
        }*/

        $xml .= '</enableOptions>';

        if (!empty($config->enablecallin)) {
            $xml .= "<telephony><telephonySupport>CALLIN</telephonySupport></telephony>";
        }

        if (isset($data->hostusers)) {
            $xml .= '<presenters><participants>';
            foreach ($data->hostusers as $huser) {
                $xml .= '<participant><person>';

                if (isset($huser->firstname) && isset($huser->lastname)) {
                    $xml .= '<name>'.self::format_text($huser->firstname.' '.$huser->lastname).'</name>';
                }
                if (isset($huser->email)) {
                    $xml .= '<email>'.$huser->email.'</email>';
                }
                if (isset($huser->webexid)) {
                    $xml .= '<webExId>'.$huser->webexid.'</webExId>';
                }
                $xml .= '<type>MEMBER</type></person>'.
                        '<role>HOST</role></participant>';
            }
            $xml .= '</participants></presenters>';
        }

        // TODO Expand.

        $xml .= '<repeat><repeatType>SINGLE</repeatType></repeat>';

        return $xml;
    }

}
