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
 * @package    local_intellidata
 * @copyright  2021 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */

namespace local_intellidata\services;

use local_intellidata\helpers\DebugHelper;
use local_intellidata\helpers\ParamsHelper;
use local_intellidata\lti\OAuthConsumer;
use local_intellidata\lti\OAuthRequest;
use local_intellidata\lti\OAuthSignatureMethod_HMAC_SHA1;
use local_intellidata\helpers\SettingsHelper;

/**
 *
 * @package    local_intellidata
 * @copyright  2021 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */
class lti_service {
    /** @var mixed LTI endpoint */
    private $endpoint;

    /** @var mixed LTI consumer key */
    private $key;

    /** @var mixed LTI shared secret */
    private $secret;

    /** @var bool LTI debug mode */
    private $debug;

    /**
     * Set endpoint for LTI
     *
     * @param string $endpoint
     */
    public function set_endpoint($endpoint) {
        $this->endpoint = $endpoint;
    }

    /**
     * lti_service constructor.
     * @throws \dml_exception
     */
    public function __construct() {
        $this->endpoint = SettingsHelper::get_setting('ltitoolurl');
        $this->key = SettingsHelper::get_setting('lticonsumerkey');
        $this->secret = SettingsHelper::get_setting('ltisharedsecret');
        $this->debug = SettingsHelper::get_setting('ltidebug');

    }

    /**
     * Get signed parameters for LTI request
     *
     * @param array $customparameters [param_key => param_value]
     * @return array
     */
    private function lti_request_params($customparameters) {
        global $USER;

        $requestparams = [
            'user_id' => $USER->id,
            'lis_person_contact_email_primary' => $USER->email,
            'lis_person_name_given' => $USER->firstname,
            'lis_person_name_family' => $USER->lastname,
            'lis_person_name_full' => fullname($USER),
            'ext_user_username' => $USER->username,
            'lti_message_type' => 'basic-lti-launch-request',
            'lti_version' => 'LTI-1p0',
            'resource_link_id' => 0,
        ];

        $requestparams = array_merge($requestparams, $customparameters);

        return $this->lti_sign_parameters($requestparams);
    }

    /**
     * Set Lti role.
     *
     * @param array $ids
     * @param array $roles
     *
     * @return void
     */
    public function set_lti_role($ids = [], $roles = []) {
        global $DB;

        if (!$role = $DB->get_record('role', ['id' => get_config(ParamsHelper::PLUGIN, 'ibnltirole')])) {
            return;
        }

        try {
            // Removing all user assign for lti role.
            $DB->delete_records('role_assignments', ['roleid' => $role->id]);
        } catch (\Exception $e) {
            DebugHelper::error_log($e->getMessage());
        }

        if (!$ids && !$roles) {
            return;
        }

        $context = \context_system::instance();

        $sqlids = $ids ? " SELECT u.id
                FROM mdl_user u
               WHERE u.id IN (" . implode(",", $ids) . ") " : '';

        $sqlroles = $roles ? " SELECT DISTINCT u.id
                FROM {user} u
                JOIN {role_assignments} ra ON ra.userid = u.id
               WHERE ra.roleid IN ('" . implode("','", $roles) . "') " : '';

        $sql = " $sqlids " . ($sqlids && $sqlroles ? "UNION DISTINCT" : "") . " $sqlroles ";

        if (get_config(ParamsHelper::PLUGIN, 'ltiassigndefaultmethod')) {
            // Use standard method for assign user to lti role.
            if ($records = $DB->get_records_sql($sql)) {
                foreach ($records as $record) {
                    role_assign($role->id, $record->id, $context->id);
                }
            }
        } else {
            $sql = "INSERT INTO {role_assignments} (roleid, contextid, userid, timemodified)
                         SELECT
                            '" . $role->id . "' AS roleid,
                            '" . $context->id . "' AS contextid,
                            t.id,
                            '" . time() . "' AS timemodified
                           FROM ($sql) t ";

            $DB->execute($sql);
        }
    }

    /**
     * Lti sign parameters.
     *
     * @param $oldparms
     * @return array|null
     */
    public function lti_sign_parameters($oldparms) {
        $parms = $oldparms;
        $hmacmethod = new OAuthSignatureMethod_HMAC_SHA1();
        $testconsumer = new OAuthConsumer($this->key, $this->secret, null);
        $accreq = OAuthRequest::from_consumer_and_token(
            $testconsumer, '', "POST", $this->endpoint, $parms
        );
        $accreq->sign_request($hmacmethod, $testconsumer, '');
        $newparms = $accreq->get_parameters();

        return $newparms;
    }

    /**
     * Return the launch data required for opening the attendance tool.
     *
     * @param $customparams
     * @return array the endpoint URL and parameters (including the signature)
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public function lti_get_launch_data($customparams = []) {
        if (!empty($this->key) && !empty($this->secret) && !empty($this->endpoint)) {
            $parms = $this->lti_request_params($customparams);

            $endpointurl = new \moodle_url(
                SettingsHelper::get_setting('ltitoolurl')
            );
            $endpointparams = $endpointurl->params();

            // Strip querystring params in endpoint url from $parms to avoid duplication.
            if (!empty($endpointparams) && !empty($parms)) {
                foreach (array_keys($endpointparams) as $paramname) {
                    if (isset($parms[$paramname])) {
                        unset($parms[$paramname]);
                    }
                }
            }

        } else {
            echo 'Invalid LTI credentials';exit;
        }

        return [$this->endpoint, $parms, $this->debug];
    }
}
