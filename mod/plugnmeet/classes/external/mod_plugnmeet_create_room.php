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
 * @package    mod_plugnmeet
 * @author     Jibon L. Costa <jibon@mynaparrot.com>
 * @copyright  2022 MynaParrot
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->libdir . "/externallib.php");

if (!class_exists("plugNmeetConnect")) {
    require($CFG->dirroot . '/mod/plugnmeet/helpers/plugNmeetConnect.php');
}

/**
 *
 */
class mod_plugnmeet_create_room extends external_api {
    /**
     * @return external_function_parameters
     */
    public static function create_room_parameters() {
        return new external_function_parameters([
            'instanceId' => new external_value(PARAM_INT, 'course module id', VALUE_REQUIRED),
            'join' => new external_value(PARAM_BOOL, 'should join after create', VALUE_REQUIRED),
            'isAdmin' => new external_value(PARAM_BOOL, 'is admin or not', VALUE_REQUIRED),
        ]);
    }

    /**
     * @param $instanceid
     * @param $join
     * @param $isadmin
     * @return string[]
     * @throws dml_exception
     */
    public static function create_room($instanceid, $join, $isadmin) {
        global $DB, $USER;

        $result = [
            'status' => false,
            'join_token' => ''
        ];

        $cm = get_coursemodule_from_instance('plugnmeet', $instanceid, 0, false, MUST_EXIST);
        $context = context_module::instance($cm->id);

        try {
            require_login();
            require_capability('mod/plugnmeet:view', $context);
        } catch (Exception $e) {
            $result['msg'] = $e->getMessage();
            return $result;
        }

        $instance = $DB->get_record('plugnmeet', array('id' => $instanceid), '*', MUST_EXIST);
        $config = get_config('mod_plugnmeet');
        $roommetadata = json_decode($instance->roommetadata, true);

        $connect = new PlugNmeetConnect($config);
        try {
            $res = $connect->createRoom($instance->roomid,
                $instance->name,
                $instance->welcomemessage,
                $instance->maxparticipants,
                "",
                $roommetadata);

            $result['status'] = $res->getStatus();
            $result['msg'] = $res->getResponseMsg();
            if ($result['status']) {
                $result['roomInfo'] = json_encode($res->getRawResponse());
            }
        } catch (Exception $e) {
            $result['msg'] = $e->getMessage();
        }

        if ($join && $result['status']) {
            $name = $USER->firstname . " " . $USER->lastname;
            try {
                $res = $connect->getJoinToken($instance->roomid, $name, $USER->id, $isadmin);
                $result['status'] = $res->getStatus();
                $result['msg'] = $res->getResponseMsg();
                if ($result['status']) {
                    $result['access_token'] = $res->getToken();
                }
            } catch (Exception $e) {
                $result['msg'] = $e->getMessage();
            }
        }

        return $result;
    }

    /**
     * @return external_single_structure
     */
    public static function create_room_returns() {
        return new external_single_structure([
            'status' => new external_value(PARAM_BOOL, 'status of request'),
            'msg' => new external_value(PARAM_TEXT, 'status message', VALUE_REQUIRED),
            'roomInfo' => new external_value(PARAM_RAW, 'room info'),
            'access_token' => new external_value(PARAM_RAW, 'join token'),
        ]);
    }
}
