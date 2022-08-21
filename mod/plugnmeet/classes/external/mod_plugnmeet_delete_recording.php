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
class mod_plugnmeet_delete_recording extends external_api {
    /**
     * @return external_function_parameters
     */
    public static function delete_recording_parameters() {
        return new external_function_parameters([
            'instanceId' => new external_value(PARAM_INT, 'course module id', VALUE_REQUIRED),
            'recordId' => new external_value(PARAM_RAW, 'record id', VALUE_REQUIRED),
        ]);
    }

    /**
     * @param $recordid
     * @return array
     * @throws dml_exception
     */
    public static function delete_recording($instanceid, $recordid) {
        $result = array(
            "status" => false,
        );
        $cm = get_coursemodule_from_instance('plugnmeet', $instanceid, 0, false, MUST_EXIST);
        $context = context_module::instance($cm->id);

        try {
            require_login();
            require_capability('mod/plugnmeet:edit', $context);
        } catch (Exception $e) {
            $result['msg'] = $e->getMessage();
            return $result;
        }

        $config = get_config('mod_plugnmeet');
        $connect = new PlugNmeetConnect($config);
        try {
            $res = $connect->deleteRecording($recordid);
            $result['status'] = $res->getStatus();
            $result['msg'] = $res->getResponseMsg();
        } catch (Exception $e) {
            $result['msg'] = $e->getMessage();
        }

        return $result;
    }

    /**
     * @return external_single_structure
     */
    public static function delete_recording_returns() {
        return new external_single_structure([
            'status' => new external_value(PARAM_BOOL, 'status of request'),
            'msg' => new external_value(PARAM_TEXT, 'status message', VALUE_REQUIRED),
        ]);
    }
}
