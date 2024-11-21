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
 *
 * @package    local_intelliboard
 * @copyright  2020 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    http://intelliboard.net/
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/externallib.php");

class local_intelliboard_apikeylib extends external_api {

  /**
   * @return external_function_parameters
   */
    public static function save_apikey_parameters() {
        return new external_function_parameters(
            ['apikey' => new external_value(PARAM_RAW, 'Api key to configure Intelliboard accounts')]
        );
    }

    /**
     * @param $apikey
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     * @throws restricted_context_exception
     */
    public static function save_apikey($apikey)
    {
      if (isset($CFG->intelliboardapikey) and $CFG->intelliboardapikey == false) {
          throw new moodle_exception('invalidaccess', 'error');
      }
        $params = array(
            'apikey' => $apikey
        );
        $params = self::validate_parameters(self::save_apikey_parameters(), $params);

        set_config("apikey", clean_param($params['apikey'], PARAM_ALPHANUMEXT), "local_intelliboard");

        $newapikey =  get_config('local_intelliboard', 'apikey');

        return [
            "status" => "success",
            "data" => substr($newapikey , -4),
        ];
    }

    /**
     * @return external_single_structure
     */
    public static function save_apikey_returns() {
        return new external_single_structure(
            [
                "status" => new \external_value(PARAM_TEXT),
                "data" => new \external_value(PARAM_RAW),
            ]
        );
    }
}
