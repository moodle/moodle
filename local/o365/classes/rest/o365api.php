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
 * Abstract base class for all o365 REST api classes.
 *
 * @package local_o365
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

namespace local_o365\rest;

use moodle_exception;

defined('MOODLE_INTERNAL') || die();

/**
 * Abstract base class for all o365 REST api classes.
 */
abstract class o365api {
    /**
     * @var null The general API area of the class.
     */
    public $apiarea = null;

    /** @var \local_o365\oauth2\token A token object representing all token information to be used for this client. */
    protected $token;

    /** @var \local_o365\httpclientinterface An HTTP client to use for communication. */
    protected $httpclient;

    /**
     * Constructor.
     *
     * @param \local_o365\oauth2\token $token A token object representing all token information to be used for this client.
     * @param \local_o365\httpclientinterface $httpclient An HTTP client to use for communication.
     */
    public function __construct(\local_o365\oauth2\token $token, \local_o365\httpclientinterface $httpclient) {
        $this->token = $token;
        $this->httpclient = $httpclient;
    }

    /**
     * Determine if the API client is configured.
     *
     * @return bool Whether the API client is configured.
     */
    public static function is_configured() {
        return true;
    }

    /**
     * Determine whether the plugins are configured to use the chinese API.
     *
     * @return bool Whether we should use the chinese API (true), or not (false).
     */
    public static function use_chinese_api() {
        $chineseapi = get_config('local_o365', 'chineseapi');
        return (!empty($chineseapi)) ? true : false;
    }

    /**
     * Automatically construct an instance of the API class for a given user.
     * NOTE: Useful for one-offs, not efficient for bulk operations.
     *
     * @param int $userid The Moodle user ID to construct the API for.
     * @return \local_o365\rest\o365api An instance of the requested API class with dependencies met for a given user.
     */
    public static function instance_for_user($userid = null) {
        $httpclient = new \local_o365\httpclient();
        $clientdata = \local_o365\oauth2\clientdata::instance_from_oidc();
        $tokenresource = static::get_tokenresource();
        if (!empty($userid)) {
            $token = \local_o365\oauth2\token::instance($userid, $tokenresource, $clientdata, $httpclient);
        } else {
            $token = \local_o365\utils::get_app_or_system_token($tokenresource, $clientdata, $httpclient);
        }
        if (!empty($token)) {
            return new static($token, $httpclient);
        } else {
            throw new moodle_exception('erroro365apinotoken', 'local_o365');
        }
    }

    /**
     * Get the API client's oauth2 resource.
     *
     * @return string The resource for oauth2 tokens.
     */
    public static function get_tokenresource() {
        throw new moodle_exception('erroro365apinotimplemented', 'local_o365');
    }

    /**
     * Get the base URI that API calls should be sent to.
     *
     * @return string|bool The URI to send API calls to, or false if a precondition failed.
     */
    public function get_apiuri() {
        throw new moodle_exception('erroro365apinotimplemented', 'local_o365');
    }

    /**
     * Determine whether the supplied token is valid, and refresh if necessary.
     */
    protected function checktoken() {
        if ($this->token->is_expired() === true) {
            return $this->token->refresh();
        } else {
            return true;
        }
    }

    /**
     * Transform the full request URL.
     *
     * @param string $requesturi The full request URI, includes the API uri and called endpoint.
     * @return string The transformed full request URI.
     */
    protected function transform_full_request_uri($requesturi) {
        return $requesturi;
    }

    /**
     * Make an API call.
     *
     * @param string $httpmethod The HTTP method to use. get/post/patch/merge/delete.
     * @param string $apimethod The API endpoint/method to call.
     * @param string $params Additional parameters to include.
     * @param array $options Additional options for the request.
     * @return string|array The result of the API call.
     */
    public function apicall($httpmethod, $apimethod, $params = '', $options = []) {
        // Used if we have to retry due to rate limiting.
        $origparam = [
            'httpmethod' => $httpmethod,
            'apimethod' => $apimethod,
            'params' => $params,
            'options' => $options,
        ];

        $tokenvalid = $this->checktoken();
        if ($tokenvalid !== true) {
            throw new moodle_exception('erroro365apiinvalidtoken', 'local_o365');
        }

        $apiurl = $this->get_apiuri();

        $httpmethod = strtolower($httpmethod);
        if (!in_array($httpmethod, ['get', 'post', 'put', 'patch', 'merge', 'delete'], true)) {
            throw new moodle_exception('erroro365apiinvalidmethod', 'local_o365');
        }

        $requesturi = $this->transform_full_request_uri($apiurl . $apimethod);

        $contenttype = 'application/json;odata.metadata=full';
        if (isset($options['contenttype'])) {
            $contenttype = $options['contenttype'];
            unset($options['contenttype']);
        }

        // Generate the user agent string.
        $useragent = 'Moodle';

        // Add API area if available.
        $apiarea = null;
        if (!empty($this->apiarea)) {
            $apiarea = $this->apiarea;
        }
        if (!empty($options['apiarea'])) {
            $apiarea = $options['apiarea'];
            unset($options['apiarea']);
        }
        if (!empty($apiarea)) {
            $useragent .= '-' . $apiarea;
        }

        // Add plugin version.
        $pluginversion = get_config('local_o365', 'version');
        if (!empty($pluginversion)) {
            $useragent .= '-' . $pluginversion;
        }
        $options['CURLOPT_USERAGENT'] = $useragent;

        // Add headers.
        $header = [
            'Accept: application/json',
            'Content-Type: ' . $contenttype,
            'Authorization: Bearer ' . $this->token->get_token(),
        ];

        if ($httpmethod !== 'put' && !empty($params) && is_string($params)) {
            $header[] = 'Content-length: ' . strlen($params);
        }

        $this->httpclient->resetheader();
        $this->httpclient->setheader($header);

        // Check if we were rate limited in the last 10 minutes.
        $ratelimitlevel = 0;
        $ratelimittime = 0;
        $ratelimit = get_config('local_o365', 'ratelimit');
        $ratelimitdisabled = get_config('local_o365', 'ratelimitdisabled');
        if (empty($ratelimitdisabled)) {
            if (!empty($ratelimit)) {
                $ratelimit = explode(':', $ratelimit, 2);
                if ($ratelimit[1] > (time() - (10 * MINSECS))) {
                    // Rate limiting enabled.
                    $ratelimittime = $ratelimit[1];
                    $ratelimitlevel = $ratelimit[0];
                    if ($ratelimitlevel >= 4) {
                        $ratelimitlevel = 4;
                    }
                }
            }
        }

        // Throttle if enabled.
        if (!empty($ratelimitlevel)) {
            usleep((260000 * $ratelimitlevel));
        } else {
            // Small sleep to help prevent throttling in the first place.
            usleep(100000);
        }

        $result = $this->httpclient->$httpmethod($requesturi, $params, $options);
        if (isset($this->httpclient) && isset($this->httpclient->info) && isset($this->httpclient->info['http_code'])) {
            if ($this->httpclient->info['http_code'] == 429) {
                // We are being throttled.
                $ratelimitlevel++;
                set_config('ratelimit', $ratelimitlevel . ':' . time(), 'local_o365');

                return $this->apicall($origparam['httpmethod'], $origparam['apimethod'], $origparam['params'],
                    $origparam['options']);
            } else if ($this->httpclient->info['http_code'] == 202) {
                // If response is 202 Accepted, return response.
                return $this->httpclient->response;
            }
        }
        return $result;
    }

    /**
     * Processes API responses.
     *
     * @param string $response The raw response from an API call.
     * @param array $expectedstructure A structure to validate.
     * @return array|null Array if successful, null if not.
     */
    public function process_apicall_response($response, array $expectedstructure = []) {
        $backtrace = debug_backtrace(0);
        $callingline = (isset($backtrace[0]['line'])) ? $backtrace[0]['line'] : '?';
        $caller = __METHOD__ . ':' . $callingline;

        $result = @json_decode($response, true);
        if (empty($result) || !is_array($result)) {
            \local_o365\utils::debug('Bad response received', $caller, $response);
            throw new moodle_exception('erroro365apibadcall', 'local_o365');
        }
        if (isset($result['odata.error'])) {
            $errmsg = 'Error response received.';
            \local_o365\utils::debug($errmsg, $caller, $result['odata.error']);
            if (isset($result['odata.error']['message']) && isset($result['odata.error']['message']['value'])) {
                $apierrormessage = $result['odata.error']['message']['value'];
                throw new moodle_exception('erroro365apibadcall_message', 'local_o365', '', htmlentities($apierrormessage));
            } else {
                throw new moodle_exception('erroro365apibadcall', 'local_o365');
            }
        }
        if (isset($result['error'])) {
            $errmsg = 'Error response received.';
            \local_o365\utils::debug($errmsg, $caller, $result['error']);
            if (isset($result['error']['message'])) {
                $apierrormessage = 'Unknown error, check logs for more information.';
                if (is_string($result['error']['message'])) {
                    $apierrormessage = $result['error']['message'];
                } else if (is_array($result['error']['message']) && isset($result['error']['message']['value'])) {
                    $apierrormessage = $result['error']['message']['value'];
                }
                throw new moodle_exception('erroro365apibadcall_message', 'local_o365', '', htmlentities($apierrormessage));
            } else {
                throw new moodle_exception('erroro365apibadcall', 'local_o365');
            }
        }

        foreach ($expectedstructure as $key => $val) {
            if (!isset($result[$key])) {
                $errmsg = 'Invalid structure received. No "' . $key . '"';
                \local_o365\utils::debug($errmsg, $caller, $result);
                throw new moodle_exception('erroro365apibadcall_message', 'local_o365', '', $errmsg);
            }

            if ($val !== null && $result[$key] !== $val) {
                $strreceivedval = \local_o365\utils::tostring($result[$key]);
                $strval = \local_o365\utils::tostring($val);
                $errmsg =
                    'Invalid structure received. Invalid "' . $key . '". Received "' . $strreceivedval . '", expected "' . $strval .
                    '"';
                \local_o365\utils::debug($errmsg, $caller, $result);
                throw new moodle_exception('erroro365apibadcall_message', 'local_o365', '', $errmsg);
            }
        }
        return $result;
    }

    /**
     * Get a full URL and include auth token. This is useful for associated resources: attached images, etc.
     *
     * @param string $url A full URL to get.
     * @param array $options
     * @return string The result of the request.
     */
    public function geturl($url, $options = []) {
        $tokenvalid = $this->checktoken();
        if ($tokenvalid !== true) {
            throw new moodle_exception('erroro365apiinvalidtoken', 'local_o365');
        }
        $header = ['Authorization: Bearer ' . $this->token->get_token(),];
        $this->httpclient->resetheader();
        $this->httpclient->setheader($header);
        return $this->httpclient->get($url, '', $options);
    }

    /**
     * Get an array of the current required permissions.
     *
     * @param string $api An API to get information on, or empty for all.
     * @return array Array of required Azure AD application permissions.
     */
    public function get_required_permissions($api = null) {
        $apis = [
            'graph' => [
                'appId' => '00000003-0000-0000-c000-000000000000',
                'displayName' => '',
                'requiredAppPermissions' => [
                    'AppCatalog.Read.All' => ['AppCatalog.ReadWrite.All'],
                    'AppRoleAssignment.ReadWrite.All' => [],
                    'Calendars.ReadWrite' => [],
                    'Channel.ReadBasic.All' => ['ChannelSettings.Read.All', 'ChannelSettings.ReadWrite.All'],
                    'Directory.ReadWrite.All' => [],
                    'Directory.Read.All' => [],
                    'EduRoster.ReadWrite.All' => [],
                    'Files.ReadWrite.All' => [],
                    'Group.ReadWrite.All' => [],
                    'MailboxSettings.Read' => ['MailboxSettings.ReadWrite'],
                    'Member.Read.Hidden' => [],
                    'Notes.ReadWrite.All' => [],
                    'Sites.Read.All' => [],
                    'Team.Create' => [],
                    'TeamMember.ReadWrite.All' => [],
                    'TeamsAppInstallation.ReadWriteForTeam.All' => [],
                    'TeamSettings.ReadWrite.All' => [],
                    'TeamsTab.Create' => [],
                    'User.Read.All' => ['User.ReadWrite.All'],
                ],
                'requiredDelegatedPermissionsUsingAppPermissions' => [
                    'Files.ReadWrite.All' => [],
                    'Notes.ReadWrite.All' => [],
                    'Group.ReadWrite.All' => [],
                    'Calendars.ReadWrite' => [],
                    'Domain.Read.All' => ['Domain.ReadWrite.All', 'Directory.Read.All'],
                    'User.Read' => [],
                    'openid' => [],
                    'offline_access' => [],
                    'email' => [],
                    'profile' => [],
                ],
            ],
        ];
        if (!empty($api)) {
            if (!isset($apis[$api])) {
                throw new \Exception('No API with identifier ' . $api . ' found.');
            }
            return $apis[$api];
        } else {
            return $apis;
        }
    }
}
