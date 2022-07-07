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
 * Box.net client.
 *
 * @package core
 * @author James Levy <james@box.net>
 * @link http://enabled.box.net
 * @access public
 * @version 1.0
 * @copyright copyright Box.net 2007
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/oauthlib.php');

/**
 * Box.net client class.
 *
 * @package    core
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class boxnet_client extends oauth2_client {

    /** @const API URL */
    const API = 'https://api.box.com/2.0';

    /** @const UPLOAD_API URL */
    const UPLOAD_API = 'https://upload.box.com/api/2.0';

    /**
     * Return authorize URL.
     *
     * @return string
     */
    protected function auth_url() {
        return 'https://www.box.com/api/oauth2/authorize';
    }

    /**
     * Create a folder.
     *
     * @param string $foldername The folder name.
     * @param int $parentid The ID of the parent folder.
     * @return array Information about the new folder.
     */
    public function create_folder($foldername, $parentid = 0) {
        $params = array('name' => $foldername, 'parent' => array('id' => (string) $parentid));
        $this->reset_state();
        $result = $this->post($this->make_url("/folders"), json_encode($params));
        $result = json_decode($result);
        return $result;
    }

    /**
     * Download the file.
     *
     * @param int $fileid File ID.
     * @param string $path Path to download the file to.
     * @return bool Success or not.
     */
    public function download_file($fileid, $path) {
        $this->reset_state();
        $result = $this->download_one($this->make_url("/files/$fileid/content"), array(),
            array('filepath' => $path, 'CURLOPT_FOLLOWLOCATION' => true));
        return ($result === true && $this->info['http_code'] === 200);
    }

    /**
     * Get info of a file.
     *
     * @param int $fileid File ID.
     * @return object
     */
    public function get_file_info($fileid) {
        $this->reset_state();
        $result = $this->request($this->make_url("/files/$fileid"));
        return json_decode($result);
    }

    /**
     * Get a folder content.
     *
     * @param int $folderid Folder ID.
     * @return object
     */
    public function get_folder_items($folderid = 0) {
        $this->reset_state();
        $result = $this->request($this->make_url("/folders/$folderid/items",
            array('fields' => 'id,name,type,modified_at,size,owned_by')));
        return json_decode($result);
    }

    /**
     * Log out.
     *
     * @return void
     */
    public function log_out() {
        if ($accesstoken = $this->get_accesstoken()) {
            $params = array(
                'client_id' => $this->get_clientid(),
                'client_secret' => $this->get_clientsecret(),
                'token' => $accesstoken->token
            );
            $this->reset_state();
            $this->post($this->revoke_url(), $params);
        }
        parent::log_out();
    }

    /**
     * Build a request URL.
     *
     * @param string $uri The URI to request.
     * @param array $params Query string parameters.
     * @param bool $uploadapi Whether this works with the upload API or not.
     * @return string
     */
    protected function make_url($uri, $params = array(), $uploadapi = false) {
        $api = $uploadapi ? self::UPLOAD_API : self::API;
        $url = new moodle_url($api . '/' . ltrim($uri, '/'), $params);
        return $url->out(false);
    }

    /**
     * Rename a file.
     *
     * @param int $fileid The file ID.
     * @param string $newname The new file name.
     * @return object Box.net file object.
     */
    public function rename_file($fileid, $newname) {
        // This requires a PUT request with data within it. We cannot use
        // the standard PUT request 'CURLOPT_PUT' because it expects a file.
        $data = array('name' => $newname);
        $options = array(
            'CURLOPT_CUSTOMREQUEST' => 'PUT',
            'CURLOPT_POSTFIELDS' => json_encode($data)
        );
        $url = $this->make_url("/files/$fileid");
        $this->reset_state();
        $result = $this->request($url, $options);
        $result = json_decode($result);
        return $result;
    }

    /**
     * Resets curl for multiple requests.
     *
     * @return void
     */
    public function reset_state() {
        $this->cleanopt();
        $this->resetHeader();
    }

    /**
     * Return the revoke URL.
     *
     * @return string
     */
    protected function revoke_url() {
        return 'https://www.box.com/api/oauth2/revoke';
    }

    /**
     * Share a file and return the link to it.
     *
     * @param string $fileid The file ID.
     * @param bool $businesscheck Whether or not to check if the user can share files, has a business account.
     * @return object
     */
    public function share_file($fileid, $businesscheck = true) {
        // Sharing the file, this requires a PUT request with data within it. We cannot use
        // the standard PUT request 'CURLOPT_PUT' because it expects a file.
        $data = array('shared_link' => array('access' => 'open', 'permissions' =>
            array('can_download' => true, 'can_preview' => true)));
        $options = array(
            'CURLOPT_CUSTOMREQUEST' => 'PUT',
            'CURLOPT_POSTFIELDS' => json_encode($data)
        );
        $this->reset_state();
        $result = $this->request($this->make_url("/files/$fileid"), $options);
        $result = json_decode($result);

        if ($businesscheck) {
            // Checks that the user has the right to share the file. If not, throw an exception.
            $this->reset_state();
            $this->head($result->shared_link->download_url);
            $info = $this->get_info();
            if ($info['http_code'] == 403) {
                throw new moodle_exception('No permission to share the file');
            }
        }

        return $result->shared_link;
    }

    /**
     * Search.
     *
     * @return object
     */
    public function search($query) {
        $this->reset_state();
        $result = $this->request($this->make_url('/search', array('query' => $query, 'limit' => 50, 'offset' => 0)));
        return json_decode($result);
    }

    /**
     * Return token URL.
     *
     * @return string
     */
    protected function token_url() {
        return 'https://www.box.com/api/oauth2/token';
    }

    /**
     * Upload a file.
     *
     * Please note that the file is named on Box.net using the path we are providing, and so
     * the file has the name of the stored_file hash.
     *
     * @param stored_file $storedfile A stored_file.
     * @param integer $parentid The ID of the parent folder.
     * @return object Box.net file object.
     */
    public function upload_file(stored_file $storedfile, $parentid = 0) {
        $url = $this->make_url('/files/content', array(), true);
        $options = array(
            'filename' => $storedfile,
            'parent_id' => $parentid
        );
        $this->reset_state();
        $result = $this->post($url, $options);
        $result = json_decode($result);
        return $result;
    }

}

/**
 * @deprecated since 2.6, 2.5.3, 2.4.7
 */
class boxclient {
    public function __construct() {
        throw new coding_exception(__CLASS__ . ' has been removed. Please update your code to use boxnet_client.',
            DEBUG_DEVELOPER);
    }
}
