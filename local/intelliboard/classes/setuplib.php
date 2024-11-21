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
 * This plugin provides access to Moodle data.
 *
 * @package    local_intelliboard
 * @copyright  2020 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    http://intelliboard.net/
 */

use local_intelliboard\attendance_api;

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/externallib.php");

class local_intelliboard_setuplib extends external_api {

    /** ----------------------- SAVE BASE PARAMS ----------------------- */

    /**
     * @return external_function_parameters
     */
    public static function save_base_settings_parameters() {
        return new external_function_parameters(
            ["params" => new external_single_structure([
                "webservice" => new external_value(PARAM_BOOL, "Enable web services"),
                "rest_protocol" => new external_value(PARAM_BOOL, "Enable REST services"),
                "soap_protocol" => new external_value(PARAM_BOOL, "Enable SOAP services"),
                "user_identifier" => new external_value(PARAM_TEXT, "User identifier"),
                "enable_tracking" => new external_value(PARAM_BOOL, "Enable tracking"),
                "enable_sso_link" => new external_value(PARAM_BOOL, "Enable SSO link"),
                "email" => new external_value(PARAM_EMAIL, "Subscription Email"),
            ])]
        );
    }

    /**
     * @param $params
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     * @throws required_capability_exception
     * @throws restricted_context_exception
     */
    public static function save_base_settings($params)
    {
        global $DB;

        $inputdata = self::validate_parameters(self::save_base_settings_parameters(), [
            "params" => [
                "webservice" => $params["webservice"],
                "rest_protocol" => $params["rest_protocol"],
                "soap_protocol" => $params["soap_protocol"],
                "user_identifier" => $params["user_identifier"],
                "enable_tracking" => $params["enable_tracking"],
                "enable_sso_link" => $params["enable_sso_link"],
                "email" => $params["email"],
            ]
        ]);

        $context = context_system::instance();
        self::validate_context($context);
        require_capability("moodle/site:config", $context);

        $inputdata = $inputdata["params"];
        $externalservicedata = $DB->get_record("external_services", ["component" => "local_intelliboard"], "*", MUST_EXIST);

        $errors = self::validate_basic_params($inputdata);

        if ($errors) {
            return [
                "status" => "error",
                "data" => $errors
            ];
        }

        $setupservice = new \local_intelliboard\services\setup();
        $setupservice->protocol_handler($inputdata["rest_protocol"], $inputdata["soap_protocol"]);
        $setupservice->webservices_handler($inputdata["webservice"]);
        $userid = $setupservice->webservice_users_handler($inputdata["user_identifier"], $externalservicedata->id);

        if (!$userid) {
            return [
                "status" => "error",
                "data" => "<p>" . get_string("invalid_user", "local_intelliboard") . "</p>"
            ];
        }

        $setupservice->intelliboard_plugin_handler([
            "email" => $inputdata["email"],
            "enable_tracking" => $inputdata["enable_tracking"],
            "enable_sso_link" => $inputdata["enable_sso_link"],
        ]);
        $token = $setupservice->webservice_token_handler($externalservicedata->id, $userid);

        return [
            "status" => "success",
            "data" => json_encode([
                "token" => $token
            ])
        ];
    }

    /**
     * @return external_single_structure
     */
    public static function save_base_settings_returns()
    {
        return new external_single_structure(
            [
                "status" => new \external_value(PARAM_TEXT),
                "data" => new \external_value(PARAM_RAW),
            ]
        );
    }
    /** ----------------------- SAVE BASE PARAMS ----------------------- */


    /** ----------------------- LOGIN ----------------------- */

    /**
     * @return external_function_parameters
     */
    public static function login_parameters() {
        return new external_function_parameters([
            "email" => new external_value(PARAM_EMAIL, "IntelliBoard email"),
            "password" => new external_value(PARAM_TEXT, "IntelliBoard password"),
            "moodle_service_token" => new external_value(PARAM_RAW, "Token of IntellBoard web service"),
            "restProtocol" => new external_value(PARAM_BOOL, "Use REST protocol"),
        ]);
    }

    /**
     * @param $email
     * @param $password
     * @param $token
     * @param $restProtocol
     * @return array|mixed
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     * @throws required_capability_exception
     * @throws restricted_context_exception
     */
    public static function login($email, $password, $token, $restProtocol)
    {
        global $CFG;

        require_once($CFG->dirroot . "/local/intelliboard/locallib.php");

        $inputdata = self::validate_parameters(self::login_parameters(), [
            "email" => $email,
            "password" => $password,
            "moodle_service_token" => $token,
            "restProtocol" => $restProtocol,
        ]);
        $url = new \moodle_url("/");

        $context = context_system::instance();
        self::validate_context($context);
        require_capability("moodle/site:config", $context);


        $response = intelliboard_auth([
            "task" => "moodle_login",
            "email" => $inputdata["email"],
            "password" => $inputdata["password"],
            "web_service_token" => $inputdata["moodle_service_token"],
            "rest_protocol" => $inputdata["restProtocol"],
            "lms_url" => $url->out(),
        ], "moodleLogin");

        if (isset($response["status"]) && isset($response["message"])) {
            if ($response["status"] == "success") {
                set_config("te1", $inputdata["email"], "local_intelliboard");
            }

            return $response;
        } else {
            return [
                "status" => "error",
                "message" => get_string("server_error", "local_intelliboard")
            ];
        }
    }

    public static function login_returns()
    {
        return new external_single_structure(
            [
                "status" => new \external_value(PARAM_TEXT),
                "message" => new \external_value(PARAM_RAW),
            ]
        );
    }

    /** ----------------------- LOGIN (END) ----------------------- */

    /** ----------------------- RGISTER ----------------------- */

    /**
     * @return external_function_parameters
     */
    public static function register_parameters() {
        return new external_function_parameters([
            "name" => new external_value(PARAM_TEXT, "User name"),
            "email" => new external_value(PARAM_EMAIL, "User email"),
            "password" => new external_value(PARAM_TEXT, "User password"),
            "country" => new external_value(PARAM_TEXT, "User country"),
            "moodle_service_token" => new external_value(PARAM_RAW, "Token of IntellBoard web service"),
            "restProtocol" => new external_value(PARAM_BOOL, "Use REST protocol"),
        ]);
    }

    /**
     * @param $email
     * @param $password
     * @param $token
     * @param $restProtocol
     * @return array|mixed
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     * @throws required_capability_exception
     * @throws restricted_context_exception
     */
    public static function register($name, $email, $password, $country, $token, $restProtocol)
    {
        global $CFG;

        require_once($CFG->dirroot . "/local/intelliboard/locallib.php");

        $inputdata = self::validate_parameters(self::register_parameters(), [
            "name" => $name,
            "email" => $email,
            "password" => $password,
            "country" => $country,
            "moodle_service_token" => $token,
            "restProtocol" => $restProtocol,
        ]);
        $url = new \moodle_url("/");

        $context = context_system::instance();
        self::validate_context($context);
        require_capability("moodle/site:config", $context);


        $response = intelliboard_auth([
            "task" => "moodle_register",
            "name" => $inputdata["name"],
            "email" => $inputdata["email"],
            "password" => $inputdata["password"],
            "country" => $inputdata["country"],
            "web_service_token" => $inputdata["moodle_service_token"],
            "rest_protocol" => $inputdata["restProtocol"],
            "lms_url" => $url->out(),
        ], "moodleRegister");

        if (isset($response["status"]) && isset($response["message"])) {
            if ($response["status"] == "success") {
                set_config("te1", $inputdata["email"], "local_intelliboard");
            }

            return $response;
        } else {
            return [
                "status" => "error",
                "message" => get_string("server_error", "local_intelliboard")
            ];
        }
    }

    public static function register_returns()
    {
        return new external_single_structure(
            [
                "status" => new \external_value(PARAM_TEXT),
                "message" => new \external_value(PARAM_RAW),
            ]
        );
    }
    /** ----------------------- REGISTER (END) ----------------------- */

    /** ----------------------- CHECK EMAIL ----------------------- */

    /**
     * @return external_function_parameters
     */
    public static function check_email_parameters() {
        return new external_function_parameters([
            "email" => new external_value(PARAM_EMAIL, "User email"),
        ]);
    }

    /**
     * @param $email
     * @param $password
     * @param $token
     * @param $restProtocol
     * @return array|mixed
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     * @throws required_capability_exception
     * @throws restricted_context_exception
     */
    public static function check_email($email)
    {
        global $CFG;

        require_once($CFG->dirroot . "/local/intelliboard/locallib.php");

        $inputdata = self::validate_parameters(self::check_email_parameters(), [
            "email" => $email,
        ]);

        $context = context_system::instance();
        self::validate_context($context);
        require_capability("moodle/site:config", $context);


        $response = intelliboard_auth([
            "task" => "moodle_check_email",
            "email" => $inputdata["email"],
        ], "moodleCheckEmail");

        if (isset($response["status"]) && isset($response["email_exists"]) && $response["status"] == "ok") {
            return [
                "email_exists" => $response["email_exists"]
            ];
        } else {
            return [
                "email_exists" => false
            ];
        }
    }

    public static function check_email_returns()
    {
        return new external_single_structure(
            [
                "email_exists" => new \external_value(PARAM_BOOL),
            ]
        );
    }
    /** ----------------------- CHECK EMAIL (END) ----------------------- */

    public static function validate_basic_params($data)
    {
        $errors = "";

        if (!$data["webservice"]) {
            $errors .= "<p>" . get_string("enable_webservice", "local_intelliboard") . "</p>";
        }

        if (!$data["rest_protocol"] && !$data["soap_protocol"]) {
            $errors .= "<p>" . get_string("you_need_to_enable_at_least_one_protocol", "local_intelliboard") . "</p>";
        }

        if (!$data["email"]) {
            $errors .= "<p>" . get_string("email_is_required", "local_intelliboard") . "</p>";
        }

        return $errors;
    }
}