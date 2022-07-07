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
 * REST interface to Nextcloud's implementation of Open Collaboration Services.
 *
 * @package    repository_nextcloud
 * @copyright  2017 Jan Dageförde (Learnweb, University of Münster)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace repository_nextcloud;

use core\oauth2\client;
use core\oauth2\rest;

defined('MOODLE_INTERNAL') || die();

/**
 * REST interface to Nextcloud's implementation of Open Collaboration Services.
 *
 * @package    repository_nextcloud
 * @copyright  2017 Jan Dageförde (Learnweb, University of Münster)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ocs_client extends rest {

    /**
     * shareType=0 creates a private user share.
     */
    const SHARE_TYPE_USER = 0;

    /**
     * shareType=3 creates a public share.
     */
    const SHARE_TYPE_PUBLIC = 3;

    /**
     * permissions=1 gives read permission for a share.
     */
    const SHARE_PERMISSION_READ = 1;

    /**
     * permissions=1 gives read permission for a share.
     */
    const SHARE_PERMISSION_ALL = 31;

    /**
     * OCS endpoint as configured for the used issuer.
     * @var \moodle_url
     */
    private $ocsendpoint;


    /**
     * Get endpoint URLs from the used issuer to use them in get_api_functions().
     * @param client $oauthclient OAuth-authenticated Nextcloud client
     * @throws configuration_exception Exception if critical endpoints are missing.
     * @throws \moodle_exception when trying to construct a moodleurl
     */
    public function __construct(client $oauthclient) {
        parent::__construct($oauthclient);

        $issuer = $oauthclient->get_issuer();
        $ocsendpoint = $issuer->get_endpoint_url('ocs');
        if ($ocsendpoint === false) {
            throw new configuration_exception('Endpoint ocs_endpoint not defined.');
        }
        $this->ocsendpoint = new \moodle_url($ocsendpoint);
        if (empty($this->ocsendpoint->get_param('format'))) {
            $this->ocsendpoint->params(array('format' => 'xml'));
        }
    }

    /**
     * Define relevant functions of the OCS API.
     *
     * Previously, the instruction to create a oauthclient recommended the user to enter the return format (format=xml).
     * However, in this case the shareid is appended at the wrong place. Therefore, a new url is build which inserts the
     * shareid at the suitable place for delete_share and get_information_of_share.
     * create_share docs: https://docs.nextcloud.com/server/13/developer_manual/core/ocs-share-api.html#create-a-new-share
     *
     */
    public function get_api_functions() {
        return [
            'create_share' => [
                'endpoint' => $this->ocsendpoint->out(false),
                'method' => 'post',
                'args' => [
                    'path' => PARAM_TEXT, // Could be PARAM_PATH, we really don't want to enforce a Moodle understanding of paths.
                    'shareType' => PARAM_INT,
                    'shareWith' => PARAM_TEXT, // Name of receiving user/group. Required if SHARE_TYPE_USER.
                    'publicUpload' => PARAM_RAW, // Actually Boolean, but neither String-Boolean ('false') nor PARAM_BOOL (0/1).
                    'permissions' => PARAM_INT,
                    'shareWith' => PARAM_TEXT,
                    'expireDate' => PARAM_TEXT
                ],
                'response' => 'text/xml'
            ],
            'delete_share' => [
                'endpoint' => $this->build_share_url(),
                'method' => 'delete',
                'args' => [
                    'share_id' => PARAM_INT
                ],
                'response' => 'text/xml'
            ],
            'get_shares' => [
                'endpoint' => $this->ocsendpoint->out(false),
                'method' => 'get',
                'args' => [
                    'path' => PARAM_TEXT,
                    'reshares' => PARAM_RAW, // Returns not only the shares from the current user but all of the given file.
                    'subfiles' => PARAM_RAW, // Returns all shares within a folder, given that path defines a folder.
                ],
                'response' => 'text/xml'
            ],
            'get_information_of_share' => [
                'endpoint' => $this->build_share_url(),
                'method' => 'get',
                'args' => [
                    'share_id' => PARAM_INT
                ],
                'response' => 'text/xml'
            ],
        ];
    }

    /**
     * Private Function to return a url with the shareid in the path.
     * @return string
     */
    private function build_share_url() {
        // Out_omit_querystring() in combination with ocsendpoint->get_path() is not used since both function include
        // /ocs/v1.php.
        $shareurl = $this->ocsendpoint->get_scheme() . '://' . $this->ocsendpoint->get_host() . ':' .
            $this->ocsendpoint->get_port() . $this->ocsendpoint->get_path() . '/{share_id}?' .
            $this->ocsendpoint->get_query_string(false);
        return $shareurl;
    }

    /**
     * In POST requests, Moodle's REST API assumes that params are
     * - transmitted as part of the URL or
     * - expressed in JSON.
     * Neither is true; we are passing an array to $functionargs which is then put into CURLOPT_POSTFIELDS.
     * Curl assumes the content type to be `multipart/form-data` then, but the Moodle REST API tries to put
     * a JSON content type. As a result, clients would fail.
     * To make this less tedious to use, we assume that the params-as-array-in-$functionargs is the default for us.
     *
     * @param string $functionname Name of a function from get_api_functions()
     * @param array $functionargs Request parameters
     * @param bool|string $rawpost Optional param to include in the body of a post
     * @param bool|string $contenttype Content type of the request body. Default: multipart/form-data if !$rawpost, JSON otherwise
     * @return object|string
     * @throws \coding_exception
     * @throws \core\oauth2\rest_exception
     */
    public function call($functionname, $functionargs, $rawpost = false, $contenttype = false) {
        if ($rawpost === false && $contenttype === false) {
            return parent::call($functionname, $functionargs, false, 'multipart/form-data');
        } else {
            return parent::call($functionname, $functionargs, $rawpost, $contenttype);
        }
    }

}