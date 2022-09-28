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
class mod_plugnmeet_get_recordings extends external_api {
    /**
     * @return external_function_parameters
     */
    public static function get_recordings_parameters() {
        return new external_function_parameters([
            'instanceId' => new external_value(PARAM_INT, 'course module id', VALUE_REQUIRED),
            'room_id' => new external_value(PARAM_RAW, 'room id', VALUE_REQUIRED),
            'from' => new external_value(PARAM_INT, 'from row', VALUE_REQUIRED),
            'limit' => new external_value(PARAM_INT, 'limit of records', VALUE_REQUIRED),
            'order_by' => new external_value(PARAM_TEXT, 'order by', VALUE_REQUIRED),
        ]);
    }

    /**
     * @param $roomid
     * @param $from
     * @param $limit
     * @param $orderby
     * @return array
     * @throws dml_exception
     */
    public static function get_recordings($instanceid, $roomid, $from, $limit, $orderby) {
        $result = array(
            "status" => false,
            "result" => array (
                'total_recordings' => 0,
                'from' => 0,
                'limit' => 20,
                'order_by' => 'DESC',
                'recordings_list' => null
            )
        );
        $cm = get_coursemodule_from_instance('plugnmeet', $instanceid, 0, false, MUST_EXIST);
        $context = context_module::instance($cm->id);

        try {
            require_login();
            require_capability('mod/plugnmeet:view', $context);
        } catch (Exception $e) {
            $result['msg'] = $e->getMessage();
            return $result;
        }

        $config = get_config('mod_plugnmeet');
        $connect = new PlugNmeetConnect($config);

        try {
            $roomids = array($roomid);
            $res = $connect->getRecordings($roomids, $from, $limit, $orderby);

            $result['status'] = $res->getStatus();
            $result['msg'] = $res->getResponseMsg();
            if ($result['status']) {
                $result['result'] = array(
                    'total_recordings' => $res->getTotalRecordings(),
                    'from' => $res->getFrom(),
                    'limit' => $res->getLimit(),
                    'order_by' => $res->getOrderBy(),
                    'recordings_list' => json_encode($res->getRawResponse()->result->recordings_list)
                );
            }
        } catch (Exception $e) {
            $result['msg'] = $e->getMessage();
        }

        return $result;
    }

    /**
     * @return external_single_structure
     */
    public static function get_recordings_returns() {
        return new external_single_structure([
            'status' => new external_value(PARAM_BOOL, 'status of request'),
            'msg' => new external_value(PARAM_TEXT, 'status message', VALUE_REQUIRED),
            'result' => new external_single_structure([
                'total_recordings' => new external_value(PARAM_INT, 'status of request'),
                'from' => new external_value(PARAM_INT, 'status of request'),
                'limit' => new external_value(PARAM_INT, 'status of request'),
                'order_by' => new external_value(PARAM_TEXT, 'status of request'),
                'recordings_list' => new external_value(PARAM_RAW, 'recordings_list')
            ]),
        ]);
    }
}
