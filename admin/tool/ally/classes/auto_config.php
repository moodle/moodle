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
 * Web service auto configuration tool.
 * @author    Guy Thomas <citricity@gmail.com>
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_ally;

use tool_ally\logging\logger;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . "/../../../../webservice/lib.php");
require_once(__DIR__ . "/../../../../user/lib.php");


class auto_config {

    /**
     * @var \stdClass - web user
     */
    public $user;

    /**
     * @var \stdClass - role
     */
    public $role;

    /**
     * @var string - token
     */
    public $token;

    /**
     * Main configuration.
     */
    public function configure() {
        $this->create_user();
        $this->create_role();
        $this->enable_web_service();
    }

    /**
     * Re configure a web service user.
     * @throws \moodle_exception.
     */
    public function configure_user($webuserpwd) {
        $user = local::get_ally_web_user();

        if ($user) {
            $user->password = $webuserpwd;
            $user->policyagreed = 1;
            $user = $this->load_user_profile($user);
            user_update_user($user);
            profile_save_data($user);
            $this->user = $user;
            return $user;
        }
    }

    /**
     * Create web service user.
     * @throws \moodle_exception.
     */
    private function create_user() {
        $webuserpwd = strval(new password());

        if ($user = $this->configure_user($webuserpwd)) {
            return;
        }

        $user = create_user_record('ally_webuser', $webuserpwd);
        $user->policyagreed = 1;
        $user->password = $webuserpwd;
        $user->firstname = 'Ally';
        $user->lastname = 'Webservice';
        $user->email = 'allywebservice@test.local'; // Fake email address.
        $user = $this->load_user_profile($user);
        user_update_user($user);
        profile_save_data($user);
        $this->user = $user;
    }

    private function load_user_profile($user) {
        if ($user->id) {
            $profilefilds = profile_get_user_fields_with_data($user->id);
        } else {
            $profilefilds = profile_get_user_fields_with_data(0);
        }
        foreach ($profilefilds as $profilefield) {
            if ($profilefield->field->required) {
                $fieldname = $profilefield->inputname;
                $user->$fieldname = '';
            }
        }
        return $user;
    }

    /**
     * Create web service role.
     * @throws \coding_exception
     * @throws \dml_exception
     */
    private function create_role() {
        global $DB;

        $role = $DB->get_record('role', ['shortname' => 'ally_webservice']);
        if ($role) {
            $roleid = $role->id;
            $this->role = $role;
        } else {
            $roleid = create_role('Ally webservice', 'ally_webservice', 'Role for Ally web service', 'teacher');
            $this->role = $DB->get_record('role', ['id' => $roleid]);
        }

        $contextid = \context_system::instance()->id;

        // Add capabilities to role.
        $caps = [
            "moodle/course:ignorefilesizelimits",
            "moodle/course:managefiles",
            "moodle/course:update",
            "moodle/course:useremail",
            "moodle/course:view",
            "moodle/course:viewhiddencourses",
            "moodle/course:viewhiddenactivities",
            "moodle/course:viewparticipants",
            "moodle/question:useall",
            "moodle/site:configview",
            "moodle/site:viewparticipants",
            "moodle/site:accessallgroups",
            "moodle/user:update",
            "moodle/user:viewdetails",
            "moodle/user:viewhiddendetails",
            "webservice/rest:use",
            "mod/resource:view",
            "moodle/category:viewhiddencategories",
            "tool/ally:viewlogs"
        ];
        foreach ($caps as $cap) {
            assign_capability($cap, CAP_ALLOW, $roleid, $contextid);
        }

        // Add teacher archetype caps to role.
        $caps = get_default_capabilities('teacher');
        foreach ($caps as $cap => $permission) {
            try {
                assign_capability($cap, $permission, $roleid, $contextid);
            } catch (\moodle_exception $mex) {
                $logstr = 'logger:autoconfigfailureteachercap';
                $msg = get_string($logstr . '_exp', 'tool_ally', (object) [
                    'cap' => $cap,
                    'permission' => $permission
                ]);
                logger::get()->error($logstr, [
                    '_explanation' => $msg,
                    '_exception' => $mex
                ]);
            }
        }

        // Allow role to be allocated at system level.
        set_role_contextlevels($roleid, [CONTEXT_SYSTEM]);

        // Assign user to role.
        role_assign($roleid, $this->user->id, $contextid);
    }

    /**
     * Enable web service.
     */
    private function enable_web_service() {
        global $CFG;

        set_config('enablewebservices', 1);

        // Enable REST protocol.
        $webservice = 'rest'; // We want to enable the rest web service protocol.
        $availablewebservices = \core_component::get_plugin_list('webservice');
        $activewebservices = empty($CFG->webserviceprotocols) ? array() : explode(',', $CFG->webserviceprotocols);
        foreach ($activewebservices as $key => $active) {
            if (empty($availablewebservices[$active])) {
                unset($activewebservices[$key]);
            }
        }
        if (!in_array($webservice, $activewebservices)) {
            $activewebservices[] = $webservice;
            $activewebservices = array_unique($activewebservices);
        }
        set_config('webserviceprotocols', implode(',', $activewebservices));

        $this->enable_ally_web_service();
        $this->create_ws_token();
    }

    /**
     * Enable ally web service.
     * @throws \coding_exception
     */
    private function enable_ally_web_service() {
        global $DB;

        $webservicemanager = new \webservice;

        $servicedata = (object) [
            'name' => 'Ally integration services',
            'component' => 'tool_ally',
            'timecreated' => time(),
            'timemodified' => time(),
            'shortname' => 'tool_ally',
            'restrictedusers' => 0,
            'enabled' => 1,
            'downloadfiles' => 1,
            'uploadfiles' => 1
        ];

        $row = $DB->get_record('external_services', ['component' => 'tool_ally']);
        if (!$row) {
            $servicedata->id = $webservicemanager->add_external_service($servicedata);
            $servicedata->timecreated = $row->timecreated;
            $params = array(
                'objectid' => $servicedata->id
            );
            $event = \core\event\webservice_service_created::create($params);
            $event->trigger();
        } else {
            $servicedata->id = $row->id;
            $webservicemanager->update_external_service($servicedata);
            $params = array(
                'objectid' => $servicedata->id
            );
            $event = \core\event\webservice_service_updated::create($params);
            $event->trigger();
        }
    }

    /**
     * Create web service token.
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    private function create_ws_token() {
        global $DB;

        // Create token for Ally.
        $webservicemanager = new \webservice();
        $service = $webservicemanager->get_external_service_by_shortname('tool_ally');
        $context = \context_system::instance();
        $existing = $DB->get_record('external_tokens', [
                'userid' => $this->user->id,
                'externalserviceid' => $service->id,
                'contextid' => $context->id
            ]
        );
        if ($existing) {
            $this->token = $existing->token;
        } else {
            $this->token = external_generate_token(EXTERNAL_TOKEN_PERMANENT, $service->id,
                $this->user->id, $context);
        }
    }
}
