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
 * Ajax page.
 *
 * @package local_o365
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

namespace local_o365\page;

use core_component;
use core_plugin_manager;
use local_o365\httpclient;
use local_o365\oauth2\apptoken;
use local_o365\oauth2\clientdata;
use local_o365\rest\unified;
use local_o365\utils;
use moodle_exception;
use stdClass;
use webservice;

/**
 * Ajax page.
 */
class ajax extends base {
    /**
     * Hook function run before the main page mode.
     *
     * @return bool True.
     */
    public function header() {
        global $OUTPUT;
        echo $OUTPUT->header();

        return true;
    }

    /**
     * Run a page mode.
     *
     * @param string $mode The page mode to run.
     */
    public function run($mode) {
        try {
            $this->header();
            $methodname = (!empty($mode)) ? 'mode_'.$mode : 'mode_default';
            if (!method_exists($this, $methodname)) {
                $methodname = 'mode_default';
            }
            $this->$methodname();
        } catch (moodle_exception $e) {
            echo $this->error_response($e->getMessage());
        }
    }

    /**
     * Build an error ajax response.
     *
     * @param string $errormessage
     * @param string $errorcode
     * @return false|string
     */
    protected function error_response($errormessage, $errorcode = '') {
        $result = new stdClass;
        $result->success = false;
        $result->errorcode = $errorcode;
        $result->errormessage = $errormessage;
        return json_encode($result);
    }

    /**
     * Build a generic ajax response.
     *
     * @param mixed $data Wrapper for response data.
     * @param bool $success General success indicator.
     */
    protected function ajax_response($data, $success = true) {
        $result = new stdClass;
        $result->success = $success;
        $result->data = $data;
        return json_encode($result);
    }

    /**
     * Check if a service resource is valid.
     */
    public function mode_checkserviceresource() {
        return $this->checkserviceresource_graph();
    }

    /**
     * Check if a service resource is valid using the graph API.
     */
    protected function checkserviceresource_graph() {
        $data = new stdClass;
        $success = false;
        $setting = required_param('setting', PARAM_TEXT);
        $value = required_param('value', PARAM_TEXT);
        $tokenresource = unified::get_tokenresource();
        $clientdata = clientdata::instance_from_oidc();
        $httpclient = new httpclient();
        $token = utils::get_application_token($tokenresource, $clientdata, $httpclient);
        $apiclient = new unified($token, $httpclient);

        switch ($setting) {
            case 'entratenant':
                try {
                    $data->valid = $apiclient->test_tenant($value);
                    $success = true;
                } catch (moodle_exception $e) {
                    utils::debug('Exception: '.$e->getMessage(), __METHOD__, $e);
                    $data->valid = false;
                    $success = true;
                }
                break;

            case 'odburl':
                try {
                    $data->valid = $apiclient->validate_resource($value, $clientdata);
                    $success = true;
                } catch (moodle_exception $e) {
                    utils::debug('Exception: '.$e->getMessage(), __METHOD__, $e);
                    $data->valid = false;
                    $success = true;
                }
                break;
        }
        echo $this->ajax_response($data, $success);
    }

    /**
     * Detect the correct value for a service resource.
     */
    public function mode_detectserviceresource() {
        return $this->detectserviceresource_graph();
    }

    /**
     * Detect the correct value for a service resource using the graph API.
     */
    protected function detectserviceresource_graph() {
        $data = new stdClass;
        $success = false;
        $setting = required_param('setting', PARAM_TEXT);
        $tokenresource = unified::get_tokenresource();
        $clientdata = clientdata::instance_from_oidc();
        $httpclient = new httpclient();
        $token = utils::get_application_token($tokenresource, $clientdata, $httpclient, true);
        $apiclient = new unified($token, $httpclient);
        switch ($setting) {
            case 'entratenant':
                try {
                    $service = $apiclient->get_default_domain_name_in_tenant();
                    $data->settingval = $service;
                    $success = true;
                    echo $this->ajax_response($data, $success);
                } catch (moodle_exception $e) {
                    utils::debug($e->getMessage(), __METHOD__ . ' (detect entratenant graph)', $e);
                    echo $this->error_response($e->getMessage());
                }
                die();

            case 'odburl':
                try {
                    $service = $apiclient->get_odburl();
                    $data->settingval = $service;
                    $success = true;
                    echo $this->ajax_response($data, $success);
                } catch (moodle_exception $e) {
                    utils::debug($e->getMessage(), __METHOD__ . ' (detect entratenant graph)', $e);
                    echo $this->error_response(get_string('settings_odburl_error_graph', 'local_o365'));
                }
                die();
        }
    }

    /**
     * Check setup in Azure.
     *
     * @throws moodle_exception
     */
    public function mode_checksetup() {
        $data = new stdClass;

        $entratenant = required_param('entratenant', PARAM_TEXT);
        $existingentratenantsetting = get_config('local_o365', 'entratenant');
        if ($existingentratenantsetting != $entratenant) {
            add_to_config_log('entratenant', $existingentratenantsetting, $entratenant, 'local_o365');
        }
        set_config('entratenant', $entratenant, 'local_o365');

        $odburl = required_param('odburl', PARAM_TEXT);
        $existingodburlsetting = get_config('local_o365', 'odburl');
        if ($existingodburlsetting != $odburl) {
            add_to_config_log('odburl', $existingodburlsetting, $odburl, 'local_o365');
        }
        set_config('odburl', $odburl, 'local_o365');

        // App data.
        $appdata = new stdClass;

        $unifiedapi = new stdClass;
        $unifiedapi->active = false;

        $clientdata = clientdata::instance_from_oidc();
        $httpclient = new httpclient();
        $unifiedresource = unified::get_tokenresource();
        $correctredirecturl = \auth_oidc\utils::get_redirecturl();

        // Microsoft Graph API.
        try {
            $token = utils::get_application_token($unifiedresource, $clientdata, $httpclient, true);
            if (empty($token)) {
                throw new moodle_exception('errorgetapplicationtoken', 'local_o365');
            }
            $unifiedapiclient = new unified($token, $httpclient);

            // Check app-only perms.
            $missingappperms = $unifiedapiclient->check_graph_apponly_permissions();
            $unifiedapi->missingappperms = $missingappperms;
            $unifiedapi->apptoken = ($token instanceof apptoken) ? true : false;

            // Check delegated (user) perms.
            $missingdelegatedperms = $unifiedapiclient->check_graph_delegated_permissions();
            if ($missingdelegatedperms === null) {
                $unifiedapi->active = false;
            } else {
                $unifiedapi->active = true;
                $unifiedapi->missingperms = $missingdelegatedperms;
            }
            $appinfo = $unifiedapiclient->get_application_info();
        } catch (moodle_exception $e) {
            $unifiedapi->active = false;
            utils::debug($e->getMessage(), __METHOD__ . ' (unified)', $e);
            $unifiedapi->error = $e->getMessage();
        }

        // Check reply url.
        $replyurls = [];
        if (isset($appinfo['value'][0]['web']) && isset($appinfo['value'][0]['web']['redirectUris'])) {
            $replyurls = $appinfo['value'][0]['web']['redirectUris'];
        }
        if (!empty($replyurls)) {
            $redirecturls = (array)$replyurls;
            $appdata->replyurl = new stdClass;
            $appdata->replyurl->correct = false;
            $appdata->replyurl->detected = implode(', ', $redirecturls);
            $appdata->replyurl->intended = $correctredirecturl;
            foreach ($redirecturls as $redirecturl) {
                if ($redirecturl === $correctredirecturl) {
                    $appdata->replyurl->correct = true;
                    break;
                }
            }
        }

        if (isset($appinfo['value'][0]['homepage'])) {
            $appdata->signonurl = new stdClass;
            $appdata->signonurl->correct = ($appinfo['value'][0]['homepage'] === $correctredirecturl) ? true : false;
            $appdata->signonurl->detected = $appinfo['value'][0]['homepage'];
            $appdata->signonurl->intended = $correctredirecturl;
        }

        $data->appdata = $appdata;
        $data->unifiedapi = $unifiedapi;
        $existingunifiedapiactivesetting = get_config('local_o365', 'unifiedapiactive');
        if ($existingunifiedapiactivesetting != (int)$unifiedapi->active) {
            add_to_config_log('unifiedapiactive', $existingunifiedapiactivesetting, (int)$unifiedapi->active, 'local_o365');
        }
        set_config('unifiedapiactive', (int)$unifiedapi->active, 'local_o365');

        $existingverifysetupresultsetting = get_config('local_o365', 'verifysetupresult');
        if ($existingverifysetupresultsetting != serialize($data)) {
            add_to_config_log('verifysetupresult', $existingverifysetupresultsetting, serialize($data), 'local_o365');
        }
        set_config('verifysetupresult', serialize($data), 'local_o365');

        $success = true;
        echo $this->ajax_response($data, $success);
    }

    /**
     * Check setup in Moodle.
     */
    public function mode_checkteamsmoodlesetup() {
        global $CFG;
        require_once($CFG->libdir.'/adminlib.php');
        require_once($CFG->dirroot . '/webservice/lib.php');
        require_once($CFG->dirroot . '/lib/classes/component.php');
        require_once($CFG->libdir . '/sessionlib.php');

        $systemcontext = \context_system::instance();

        $data = new stdClass;
        $data->success = [];
        $data->errormessages = [];
        $data->info = [];
        $success = true;

        // Check and enable Secure cookies only.

        if (is_moodle_cookie_secure()) {
            // Secure cookies only is enabled.
            $data->info[] = get_string('settings_notice_cookiesecurealreadyenabled', 'local_o365');
        } else {
            // Secure cookies only is not enabled.
            $existingcookiesecuresetting = get_config('moodle', 'cookiesecure');
            if ($existingcookiesecuresetting != 1) {
                add_to_config_log('cookiesecure', $existingcookiesecuresetting, 1, 'moodle');
            }
            set_config('cookiesecure', 1);
            $data->success[] = get_string('settings_notice_cookiesecureenabled', 'local_o365');
        }

        // Check and enable Open ID authentication.
        $auth = 'oidc';
        $enabledauths = get_enabled_auth_plugins(true);
        if (!in_array($auth, $enabledauths)) {
            $enabledauths[] = $auth;
            $enabledauths = array_unique($enabledauths);
            if (set_config('auth', implode(',', $enabledauths))) {
                $data->success[] = get_string('settings_notice_oidcenabled', 'local_o365');
            } else {
                $data->errormessages[] = get_string('settings_notice_oidcnotenabled', 'local_o365');
                $success = false;
            }
        } else {
            $data->info[] = get_string('settings_notice_oidcalreadyenabled', 'local_o365');
        }

        // Enabling admin settings.
        $formdata = new stdClass();
        $formdata->s__allowframembedding = 1;
        $formdata->s__enablewebservices = 1;
        $count = admin_write_settings($formdata);
        if ($count == 0) {
            $data->info[] = get_string('settings_notice_webservicesframealreadyenabled', 'local_o365');
        } else {
            $data->success[] = get_string('settings_notice_webservicesframeenabled', 'local_o365');
        }

        // Enable REST protocol.
        $webservice = 'rest';
        $availablewebservices = core_component::get_plugin_list('webservice');
        $activewebservices = empty($CFG->webserviceprotocols) ? [] : explode(',', $CFG->webserviceprotocols);
        foreach ($activewebservices as $key => $active) {
            if (empty($availablewebservices[$active])) {
                unset($activewebservices[$key]);
            }
        }
        if (!in_array($webservice, $activewebservices)) {
            $activewebservices[] = $webservice;
            $activewebservices = array_unique($activewebservices);
            if (set_config('webserviceprotocols', implode(',', $activewebservices))) {
                $data->success[] = get_string('settings_notice_restenabled', 'local_o365');
            } else {
                $data->errormessages[] = get_string('settings_notice_restnotenabled', 'local_o365');
                $success = false;
            }
        } else {
            $data->info[] = get_string('settings_notice_restalreadyenabled', 'local_o365');
        }

        // Enable Microsoft 365 Webservices.
        $webservicemanager = new webservice();
        $o365service = $webservicemanager->get_external_service_by_shortname('o365_webservices');
        if (!$o365service->enabled) {
            $o365service->enabled = 1;
            $webservicemanager->update_external_service($o365service);
            $params = ['objectid' => $o365service->id];
            $event = \core\event\webservice_service_updated::create($params);
            $event->trigger();
            $data->success[] = get_string('settings_notice_o365serviceenabled', 'local_o365');
        } else {
            $data->info[] = get_string('settings_notice_o365servicealreadyenabled', 'local_o365');
        }

        // Enable permission to create Webservice token.
        $caproles = array_keys(get_roles_with_capability('moodle/webservice:createtoken', CAP_ALLOW, $systemcontext));
        if (in_array($CFG->defaultuserroleid, $caproles)) {
            $data->info[] = get_string('settings_notice_createtokenalreadyallowed', 'local_o365');
        } else {
            if (assign_capability('moodle/webservice:createtoken', CAP_ALLOW, $CFG->defaultuserroleid, $systemcontext->id, true)) {
                $data->success[] = get_string('settings_notice_createtokenallowed', 'local_o365');
            } else {
                $data->error[] = get_string('settings_notice_createtokennotallowed', 'local_o365');
            }
        }

        // Enable permission to use REST Protocol.
        $caproles = array_keys(get_roles_with_capability('webservice/rest:use', CAP_ALLOW, $systemcontext));
        if (in_array($CFG->defaultuserroleid, $caproles)) {
            $data->info[] = get_string('settings_notice_restusagealreadyallowed', 'local_o365');
        } else {
            if (assign_capability('webservice/rest:use', CAP_ALLOW, $CFG->defaultuserroleid, $systemcontext->id, true)) {
                $data->success[] = get_string('settings_notice_restusageallowed', 'local_o365');
            } else {
                $data->error[] = get_string('settings_notice_restusagenotallowed', 'local_o365');
            }
        }

        \core\session\manager::gc(); // Remove stale sessions.
        core_plugin_manager::reset_caches();

        echo $this->ajax_response($data, $success);
    }
}
